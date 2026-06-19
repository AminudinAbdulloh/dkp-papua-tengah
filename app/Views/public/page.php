<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?><?= esc($pageData['title'] ?? 'Halaman') ?> - Dinas Kelautan dan Perikanan Papua
Tengah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= asset('css/public-page.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="public-page-wrapper">
    <?= $this->include('public/partials/hero_header') ?>

    <section class="public-page-content">
        <div class="container px-sm-5 px-lg-0">
            <div class="content-card">
                <?php if (($pageData['path'] ?? '') === 'layanan/form-permohonan-informasi'): ?>
                    <?= $this->include('public/partials/form_permohonan_informasi') ?>
                <?php elseif (($pageData['path'] ?? '') === 'layanan/form-keberatan-informasi'): ?>
                    <?= $this->include('public/partials/form_keberatan_informasi') ?>
                <?php else: ?>
                    <?php helper('content') ?>
                    <?php
                    $contentText = trim((string) ($pageData['content'] ?? ''));
                    $pagePath = (string) ($pageData['path'] ?? '');
                    ?>

                    <?php if ($pagePath === 'profil/kontak') : ?>
                        <?php
                        $contact = json_decode($contentText, true);
                        $mapEmbed = 'https://www.google.com/maps?q=-3.3676,135.4972&z=15&output=embed';
                        $address = 'Sanoba, Distrik Nabire, Kabupaten Nabire, Papua Tengah 98816';
                        $email = 'dislautkan@papua.go.id';
                        $phone = '(0123) 456789';
                        $socials = [
                            ['label' => 'Instagram', 'url' => 'https://instagram.com/'],
                            ['label' => 'YouTube', 'url' => 'https://youtube.com/'],
                        ];

                        if (is_array($contact)) {
                            $mapEmbed = trim((string) ($contact['map_embed'] ?? ''));
                            $address = trim((string) ($contact['address'] ?? ''));
                            $email = trim((string) ($contact['email'] ?? ''));
                            $phone = trim((string) ($contact['phone'] ?? ''));
                            $socials = is_array($contact['socials'] ?? null) ? $contact['socials'] : [];
                        }

                        $socialIconMap = [
                            'instagram' => 'bi-instagram',
                            'youtube' => 'bi-youtube',
                            'facebook' => 'bi-facebook',
                            'twitter' => 'bi-twitter-x',
                            'x' => 'bi-twitter-x',
                            'tiktok' => 'bi-tiktok',
                            'linkedin' => 'bi-linkedin',
                            'whatsapp' => 'bi-whatsapp',
                            'telegram' => 'bi-telegram',
                        ];
                        ?>
                        <section class="public-contact-layout">
                            <div class="public-contact-map">
                                <?php if ($mapEmbed !== '') : ?>
                                    <iframe
                                        src="<?= esc($mapEmbed, 'attr') ?>"
                                        title="Lokasi kantor"
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                    ></iframe>
                                <?php else : ?>
                                    <div class="public-contact-empty">Peta lokasi belum tersedia.</div>
                                <?php endif; ?>
                            </div>
                            <div class="public-contact-details">
                                <h2>Alamat dan Kontak</h2>
                                <?php if ($address !== '') : ?>
                                    <div class="public-contact-item">
                                        <span class="public-contact-icon"><i class="bi bi-geo-alt"></i></span>
                                        <div>
                                            <h3>Alamat</h3>
                                            <p><?= nl2br(esc($address)) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($email !== '') : ?>
                                    <div class="public-contact-item">
                                        <span class="public-contact-icon"><i class="bi bi-envelope"></i></span>
                                        <div>
                                            <h3>Email</h3>
                                            <p><a href="mailto:<?= esc($email, 'attr') ?>"><?= esc($email) ?></a></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($phone !== '') : ?>
                                    <div class="public-contact-item">
                                        <span class="public-contact-icon"><i class="bi bi-telephone"></i></span>
                                        <div>
                                            <h3>Telepon</h3>
                                            <p><?= nl2br(esc($phone)) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($socials !== []) : ?>
                                    <div class="public-contact-social">
                                        <h3>Sosial Media</h3>
                                        <ul>
                                        <?php foreach ($socials as $social) : ?>
                                            <?php
                                            $label = trim((string) ($social['label'] ?? ''));
                                            $url = trim((string) ($social['url'] ?? ''));
                                            if ($label === '' || $url === '') {
                                                continue;
                                            }
                                            $key = strtolower($label);
                                            $iconClass = $socialIconMap[$key] ?? 'bi-link-45deg';
                                            ?>
                                            <li>
                                                <a href="<?= esc($url, 'attr') ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= esc($label) ?>">
                                                    <i class="bi <?= esc($iconClass, 'attr') ?>"></i>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>
                    <?php elseif (in_array($pagePath, ['profil/sejarah', 'profil/visi-misi', 'profil/tupoksi', 'profil/struktur', 'profil/pejabat', 'layanan/alur-permohonan-informasi'], true) && $contentText !== '' && is_html_string($contentText)) : ?>
                        <article class="content-section public-page-prose">
                            <?= safe_admin_html($contentText) ?>
                        </article>
                    <?php else : ?>
                        <?php $paragraphs = preg_split("/\R{2,}/", $contentText) ?: []; ?>

                        <?php foreach ($paragraphs as $paragraph) : ?>
                            <article class="content-section">
                                <p><?= esc($paragraph) ?></p>
                            </article>
                        <?php endforeach ?>
                    <?php endif ?>
                <?php endif ?>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>