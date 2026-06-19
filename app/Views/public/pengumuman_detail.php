<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?><?= esc($pageData['title'] ?? 'Detail Pengumuman') ?> - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= asset('css/public-page.css') ?>">
<link rel="stylesheet" href="<?= asset('css/informasi-publik.css') ?>">
<style>
.pengumuman-detail-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

.pengumuman-detail-date {
    font-size: 0.95rem;
    color: var(--bs-primary);
    font-weight: 600;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.pengumuman-detail-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--bs-dark);
    margin-bottom: 24px;
    line-height: 1.4;
}

.pengumuman-detail-desc {
    color: #4a5568;
    line-height: 1.8;
    margin-bottom: 32px;
    font-size: 1.05rem;
}

.pengumuman-detail-section {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.pengumuman-detail-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--bs-dark);
    margin-bottom: 16px;
}

@media (max-width: 768px) {
    .pengumuman-detail-card {
        padding: 24px;
    }
    
    .pengumuman-detail-title {
        font-size: 1.5rem;
    }
}

/* Dark Mode Styles */
[data-bs-theme="dark"] .pengumuman-detail-card {
    background: var(--bs-gray-800);
    border-color: var(--bs-gray-700);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

[data-bs-theme="dark"] .pengumuman-detail-title,
[data-bs-theme="dark"] .pengumuman-detail-section h3 {
    color: var(--bs-light);
}

[data-bs-theme="dark"] .pengumuman-detail-desc {
    color: var(--bs-gray-300);
}

[data-bs-theme="dark"] .pengumuman-detail-section,
[data-bs-theme="dark"] .pengumuman-detail-card .border-top {
    border-color: var(--bs-gray-700) !important;
}

[data-bs-theme="dark"] .table-attachment th {
    background-color: var(--bs-gray-700);
    color: var(--bs-light);
    border-color: var(--bs-gray-600);
}

[data-bs-theme="dark"] .table-attachment td {
    background-color: var(--bs-gray-800);
    color: var(--bs-gray-300);
    border-color: var(--bs-gray-600);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="public-page-wrapper">
    <?= $this->include('public/partials/hero_header') ?>

    <section class="public-page-content pb-5">
        <div class="container px-sm-5 px-lg-0">
            <!-- Breadcrumb -->
            <div class="info-publik-topbar mb-4">
                <nav class="info-publik-breadcrumb" aria-label="breadcrumb">
                    <?php foreach ($pageData['breadcrumbs'] ?? [] as $idx => $crumb) : ?>
                        <?php if ($idx > 0) : ?>
                            <span class="separator">›</span>
                        <?php endif; ?>
                        <?php if (($crumb['href'] ?? null) !== null && $idx < count($pageData['breadcrumbs']) - 1) : ?>
                            <a href="<?= esc($crumb['href']) ?>"><?= esc(strtoupper($crumb['label'])) ?></a>
                        <?php else : ?>
                            <span class="current"><?= esc(strtoupper($crumb['label'])) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="pengumuman-detail-card">
                        <?php
                        $createdAt = (string) ($pengumuman['created_at'] ?? '');
                        $dateLabel = '';
                        if ($createdAt !== '' && preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $createdAt, $m)) {
                            $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $dateLabel = (int)$m[3] . ' ' . $months[(int)$m[2]] . ' ' . $m[1];
                        }
                        ?>
                        
                        <div class="pengumuman-detail-date">
                            <i class="bi bi-calendar3"></i> Dipublikasikan pada <?= esc($dateLabel) ?>
                        </div>
                        
                        <h2 class="pengumuman-detail-title"><?= esc((string) ($pengumuman['judul'] ?? '')) ?></h2>
                        
                        <div class="pengumuman-detail-desc">
                            <?= nl2br(esc((string) ($pengumuman['deskripsi'] ?? ''))) ?>
                        </div>

                        <?php if (!empty($pengumuman['berkas'])) : ?>
                            <div class="pengumuman-detail-section">
                                <h3>Berkas Lampiran</h3>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered table-attachment align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Dokumen</th>
                                                <th style="width: 250px;">Waktu Unggah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="<?= base_url('uploads/pengumuman/' . $pengumuman['berkas']) ?>" target="_blank" class="text-decoration-none fw-medium text-primary d-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-pdf fs-4"></i>
                                                        <span class="text-break"><?= esc($pengumuman['berkas']) ?></span>
                                                    </a>
                                                </td>
                                                <td class="text-secondary">
                                                    <?php
                                                        $fullDate = (string) ($pengumuman['created_at'] ?? '');
                                                        if ($fullDate !== '') {
                                                            echo date('d/m/Y H:i', strtotime($fullDate)) . ' WIT';
                                                        } else {
                                                            echo '-';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-5 pt-4 border-top">
                            <a href="<?= base_url('pengumuman') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Pengumuman
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
