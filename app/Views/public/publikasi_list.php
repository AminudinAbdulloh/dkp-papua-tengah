<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?><?= esc($pageData['title'] ?? 'Publikasi') ?> - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

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
                    <input type="text" name="cari" placeholder="Cari" value="<?= esc($searchQuery ?? '') ?>" class="info-search-input">
                    <button type="submit" class="info-search-btn" aria-label="Cari">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>

            <div class="info-publik-layout">
                <!-- Main Content: Card list of documents -->
                <div class="info-publik-main">
                    <?php if (($documents ?? []) === []) : ?>
                        <div class="info-publik-empty">
                            <i class="bi bi-folder2-open"></i>
                            <h3>Belum ada dokumen</h3>
                            <p>Belum ada dokumen yang tersedia untuk kategori ini.</p>
                        </div>
                    <?php else : ?>
                        <div class="pub-document-list">
                            <?php foreach ($documents as $doc) : ?>
                                <a href="<?= base_url('publikasi/' . esc($currentTypeSlug ?? '') . '/' . (int) $doc['id']) ?>"
                                    class="pub-doc-card">
                                    <div class="pub-doc-card-body">
                                        <h3 class="pub-doc-title"><?= esc((string) ($doc['title'] ?? '')) ?></h3>
                                        <?php if (trim((string) ($doc['description'] ?? '')) !== '') : ?>
                                            <p class="pub-doc-desc"><?= esc((string) $doc['description']) ?></p>
                                        <?php endif; ?>
                                        <div class="pub-doc-meta">
                                            <i class="bi bi-calendar3"></i>
                                            <span><?= esc(\App\Models\PublicInformationModel::displayDateFromRow($doc)) ?></span>
                                        </div>
                                    </div>
                                    <div class="pub-doc-arrow">
                                        <i class="bi bi-chevron-right"></i>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php if (isset($pagerLinks) && $pagerLinks !== ''): ?>
                            <div class="mt-4 d-flex justify-content-center">
                                <?= $pagerLinks ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Sidebar: Sub-Kategori Publikasi -->
                <aside class="info-publik-sidebar">
                    <div class="sidebar-card">
                        <h3 class="sidebar-title">Kategori Publikasi</h3>
                        <nav class="sidebar-nav">
                            <?php if (($allPubCategories ?? []) !== []) : ?>
                                <?php foreach ($allPubCategories as $pc) : ?>
                                    <a href="<?= base_url('publikasi/' . esc($currentTypeSlug ?? '') . '?sub=' . esc((string) ($pc['slug'] ?? ''))) ?>"
                                        class="sidebar-link <?= (isset($currentSubSlug) && $currentSubSlug === (string) ($pc['slug'] ?? '')) ? 'active' : '' ?>">
                                        <i class="bi bi-folder"></i>
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
