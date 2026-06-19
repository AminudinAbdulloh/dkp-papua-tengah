<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?><?= esc($pageData['title'] ?? 'Detail Dokumen') ?> - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= asset('css/public-page.css') ?>">
<link rel="stylesheet" href="<?= asset('css/informasi-publik.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="public-page-wrapper">
    <?= $this->include('public/partials/hero_header') ?>

    <section class="public-page-content">
        <div class="container px-sm-5 px-lg-0">

            <!-- Breadcrumb and search -->
            <div class="info-publik-topbar">
                <nav class="info-publik-breadcrumb" aria-label="breadcrumb">
                    <?php foreach ($breadcrumbs ?? [] as $idx => $crumb) : ?>
                        <?php if ($idx > 0) : ?>
                            <span class="separator">›</span>
                        <?php endif; ?>
                        <?php if (($crumb['href'] ?? null) !== null && $idx < count($breadcrumbs) - 1) : ?>
                            <a href="<?= esc($crumb['href']) ?>"><?= esc(strtoupper($crumb['label'])) ?></a>
                        <?php else : ?>
                            <span class="current"><?= esc(strtoupper($crumb['label'])) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
                <form class="info-publik-search" method="get" action="">
                    <input type="text" name="cari" placeholder="Cari" value="" class="info-search-input">
                    <button type="submit" class="info-search-btn" aria-label="Cari">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>

            <div class="info-publik-layout">
                <!-- Main Content: Document Detail -->
                <div class="info-publik-main">
                    <div class="info-item-detail-card">
                        <h2 class="info-item-detail-title"><?= esc((string) ($document['title'] ?? '')) ?></h2>

                        <?php if (trim((string) ($document['description'] ?? '')) !== '') : ?>
                            <p class="info-item-detail-desc"><?= esc((string) $document['description']) ?></p>
                        <?php endif; ?>

                        <?php if (trim((string) ($document['file_path'] ?? '')) !== '') : ?>
                            <div class="info-item-detail-section">
                                <h3>Berkas</h3>
                                <table class="info-file-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Berkas</th>
                                            <th>Tanggal Unggah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url($document['file_path']) ?>" target="_blank" rel="noopener noreferrer" class="info-file-link">
                                                    <i class="bi bi-file-earmark-arrow-down"></i>
                                                    <?= esc((string) ($document['file_name'] ?? basename((string) $document['file_path']))) ?>
                                                </a>
                                            </td>
                                            <td><?= esc(\App\Models\PublicInformationModel::displayDateFromRow($document)) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php
                        $meta = [];
                        if (trim((string) ($document['responsible_party'] ?? '')) !== '') {
                            $meta['Penanggung Jawab'] = (string) $document['responsible_party'];
                        }
                        if (trim((string) ($document['time_period'] ?? '')) !== '') {
                            $meta['Jangka Waktu'] = (string) $document['time_period'];
                        }
                        if (trim((string) ($document['information_format'] ?? '')) !== '') {
                            $meta['Bentuk Informasi'] = (string) $document['information_format'];
                        }
                        if (($document['year'] ?? null) !== null) {
                            $meta['Tahun'] = (string) $document['year'];
                        }
                        ?>
                        <?php if ($meta !== []) : ?>
                            <div class="info-item-detail-meta">
                                <?php foreach ($meta as $label => $value) : ?>
                                    <div class="meta-item">
                                        <span class="meta-label"><?= esc($label) ?></span>
                                        <span class="meta-value"><?= esc($value) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar: Sub-Kategori Publikasi -->
                <aside class="info-publik-sidebar">
                    <div class="sidebar-card">
                        <h3 class="sidebar-title">Kategori Publikasi</h3>
                        <nav class="sidebar-nav">
                            <?php if (($allPubCategories ?? []) !== []) : ?>
                                <?php foreach ($allPubCategories as $pc) : ?>
                                    <?php $isActive = (isset($currentPubCategoryId) && $currentPubCategoryId > 0 && $currentPubCategoryId === (int) ($pc['id'] ?? 0)); ?>
                                    <a href="<?= base_url('publikasi/' . esc($currentTypeSlug ?? '') . '?sub=' . esc((string) ($pc['slug'] ?? ''))) ?>"
                                        class="sidebar-link <?= $isActive ? 'active' : '' ?>">
                                        <i class="bi bi-folder<?= $isActive ? '-fill' : '' ?>"></i>
                                        <?= esc((string) ($pc['name'] ?? '')) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="sidebar-empty-note">Belum ada sub-kategori.</p>
                            <?php endif; ?>
                        </nav>
                    </div>
                </aside>
            </div>

        </div>
    </section>
</div>
<?= $this->endSection() ?>
