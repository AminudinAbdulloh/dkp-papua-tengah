<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?><?= esc($pageData['title'] ?? 'Informasi Publik') ?> - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

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
                    <?php
                    $crumbs = [
                        ['label' => 'BERANDA', 'href' => base_url('/')],
                        ['label' => 'INFORMASI PUBLIK', 'href' => base_url('publikasi/pelaporan?sub=daftar-informasi-publik')],
                    ];
                    if (isset($currentCategory) && $currentCategory !== null) {
                        $crumbs[] = ['label' => strtoupper(\App\Models\PublicInformationModel::categoryLabel($currentCategory)), 'href' => null];
                    }
                    ?>
                    <?php foreach ($crumbs as $idx => $crumb) : ?>
                        <?php if ($idx > 0) : ?>
                            <span class="separator">›</span>
                        <?php endif; ?>
                        <?php if ($crumb['href'] !== null && $idx < count($crumbs) - 1) : ?>
                            <a href="<?= esc($crumb['href']) ?>"><?= esc($crumb['label']) ?></a>
                        <?php else : ?>
                            <span class="current"><?= esc($crumb['label']) ?></span>
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
                <!-- Main Content -->
                <div class="info-publik-main">
                    <?php if (($infoItems ?? []) === []) : ?>
                        <div class="info-publik-empty">
                            <i class="bi bi-folder2-open"></i>
                            <h3>Belum ada informasi</h3>
                            <p>Informasi publik untuk kategori ini belum tersedia.</p>
                        </div>
                    <?php else : ?>
                        <!-- Table View -->
                        <div class="info-table-card">
                            <div class="table-responsive">
                                <table class="info-publik-table">
                                    <thead>
                                        <tr>
                                            <th class="col-no">No</th>
                                            <th class="col-detail">Detail</th>
                                            <th class="col-title">Ringkasan Isi Informasi</th>
                                            <th class="col-responsible">Penanggung Jawab</th>
                                            <th class="col-period">Jangka Waktu</th>
                                            <th class="col-format">Bentuk Informasi</th>
                                            <th class="col-year">Tahun</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($infoItems as $idx => $row) : ?>
                                            <tr>
                                                <td class="col-no"><?= (isset($startNo) ? $startNo : 1) + $idx ?></td>
                                                <td class="col-detail">
                                                     <?php
                                                    // Detail link handling
                                                    $customUrl   = (string) ($row['custom_url'] ?? '');
                                                    $pubType     = (string) ($row['publication_type'] ?? '');
                                                    $itemId      = (int) ($row['id'] ?? 0);

                                                    if ($customUrl !== '') {
                                                        // Item profil/statis → gunakan custom_url langsung
                                                        $detailUrl  = $customUrl;
                                                        $hasDetail  = true;
                                                    } elseif (($pubType !== '' || ($row['category'] ?? '') === 'dikecualikan') && $itemId > 0) {
                                                        // Item database → arahkan ke detail dokumen
                                                        $slugToUse = $pubType !== '' ? $pubType : 'informasi-dikecualikan';
                                                        $detailUrl  = base_url('publikasi/' . $slugToUse . '/' . $itemId);
                                                        $hasDetail  = true;
                                                    } else {
                                                        $detailUrl  = '#';
                                                        $hasDetail  = false;
                                                    }
                                                    ?>
                                                    <?php if ($hasDetail) : ?>
                                                        <a href="<?= esc($detailUrl) ?>" class="detail-link">Detail</a>
                                                    <?php else : ?>
                                                        <span class="detail-link-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="col-title">
                                                    <div class="info-title-text"><?= esc((string) ($row['title'] ?? '')) ?></div>
                                                    <?php if (trim((string) ($row['description'] ?? '')) !== '') : ?>
                                                        <div class="info-desc-text"><?= esc(mb_strimwidth((string) $row['description'], 0, 120, '...')) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="col-responsible"><?= esc((string) ($row['responsible_party'] ?? '—')) ?></td>
                                                <td class="col-period"><?= esc((string) ($row['time_period'] ?? '—')) ?></td>
                                                <td class="col-format"><?= esc((string) ($row['information_format'] ?? '—')) ?></td>
                                                <td class="col-year"><?= esc((string) ($row['year'] ?? '—')) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (isset($pagerLinks) && $pagerLinks !== ''): ?>
                            <div class="mt-4 d-flex justify-content-center">
                                <?= $pagerLinks ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="info-publik-sidebar">
                    <div class="sidebar-card">
                        <h3 class="sidebar-title">Informasi Publik</h3>
                        <nav class="sidebar-nav">
                            <?php
                            $allCategories = \App\Models\PublicInformationModel::categoryLabels();
                            $routeMap = [
                                'berkala' => 'informasi-berkala',
                                'serta-merta' => 'informasi-serta-merta',
                                'setiap-saat' => 'informasi-setiap-saat',
                                'dikecualikan' => 'informasi-dikecualikan',
                            ];
                            ?>
                            <a href="<?= base_url('publikasi/pelaporan?sub=daftar-informasi-publik') ?>"
                                class="sidebar-link">
                                <i class="bi bi-folder2-open"></i>
                                Daftar Informasi Publik
                            </a>
                            <?php foreach ($allCategories as $slug => $label) : ?>
                                <a href="<?= base_url('informasi/' . ($routeMap[$slug] ?? $slug)) ?>"
                                    class="sidebar-link <?= (isset($currentCategory) && $currentCategory === $slug) ? 'active' : '' ?>">
                                    <i class="bi <?= match ($slug) {
                                        'berkala' => 'bi-calendar-event',
                                        'serta-merta' => 'bi-megaphone',
                                        'setiap-saat' => 'bi-clock-history',
                                        'dikecualikan' => 'bi-lock',
                                        default => 'bi-file-text',
                                    } ?>"></i>
                                    <?= esc($label) ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                </aside>
            </div>

        </div>
    </section>
</div>
<?= $this->endSection() ?>
