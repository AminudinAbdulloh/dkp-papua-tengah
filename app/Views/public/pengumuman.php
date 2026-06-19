<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?><?= esc($pageData['title'] ?? 'Pengumuman') ?> - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= asset('css/public-page.css') ?>">
<link rel="stylesheet" href="<?= asset('css/informasi-publik.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="public-page-wrapper">
    <?= $this->include('public/partials/hero_header') ?>

    <section class="public-page-content pb-5">
        <div class="container px-sm-5 px-lg-0">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <?php if (($pengumuman ?? []) === []) : ?>
                        <div class="info-publik-empty text-center py-5 bg-body rounded-4 border shadow-sm">
                            <i class="bi bi-megaphone fs-1 text-secondary mb-3 d-inline-block"></i>
                            <h3 class="h4">Belum Ada Pengumuman</h3>
                            <p class="text-secondary">Saat ini tidak ada pengumuman terbaru yang dipublikasikan.</p>
                        </div>
                    <?php else : ?>
                        <div class="pub-document-list">
                            <?php foreach ($pengumuman as $p) : ?>
                                <?php
                                $createdAt = (string) ($p['created_at'] ?? '');
                                $dateLabel = '';
                                if ($createdAt !== '' && preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $createdAt, $m)) {
                                    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    $dateLabel = (int)$m[3] . ' ' . $months[(int)$m[2]] . ' ' . $m[1];
                                }
                                ?>
                                <a href="<?= base_url('pengumuman/' . esc($p['id'] ?? '')) ?>" class="pub-doc-card">
                                    <div class="pub-doc-card-body">
                                        <h3 class="pub-doc-title"><?= esc((string) ($p['judul'] ?? '')) ?></h3>
                                        <?php if (trim((string) ($p['deskripsi'] ?? '')) !== '') : ?>
                                            <p class="pub-doc-desc"><?= esc((string) $p['deskripsi']) ?></p>
                                        <?php endif; ?>
                                        <div class="pub-doc-meta mt-2">
                                            <i class="bi bi-calendar3"></i>
                                            <span><?= esc($dateLabel) ?></span>
                                        </div>
                                    </div>
                                    <div class="pub-doc-arrow">
                                        <i class="bi bi-chevron-right"></i>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php if (isset($pager) && $pager !== null): ?>
                            <div class="mt-5 d-flex justify-content-center">
                                <?= $pager->links('public', 'bootstrap_pagination') ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
