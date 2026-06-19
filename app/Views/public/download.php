<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?>Pusat Unduhan - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= asset('css/public-page.css') ?>">
<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }
    .file-icon-box {
        width: 80px;
        height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: var(--bs-light);
        border-radius: 1rem;
        transition: background-color 0.2s ease;
    }
    .hover-lift:hover .file-icon-box {
        background-color: var(--bs-primary-bg-subtle);
    }
    
    /* Warna icon search hitam saat mode gelap */
    [data-bs-theme="dark"] .input-group-text .bi-search {
        color: #000000 !important;
    }

    /* Override border primary agar sinkron dengan theme-tokens */
    .border-primary-theme {
        border-color: var(--color-primary-600) !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="public-page-wrapper">
    <?= $this->include('public/partials/hero_header') ?>

    <section class="public-page-content">
        <div class="container px-sm-5 px-lg-0">
            <div class="content-card border-0 shadow-sm rounded-4 p-4 p-md-5">
                
                <!-- Search Bar -->
                <div class="bg-light rounded-4 p-4 mb-5">
                    <form action="" method="get" class="row g-3">
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="cari" class="form-control border-start-0 rounded-end-3 py-2" 
                                    placeholder="Cari nama dokumen..." value="<?= esc($searchQuery ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                Cari Dokumen
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Documents List -->
                <?php if (!empty($downloads)) : ?>
                    <div class="row g-3">
                        <?php foreach ($downloads as $item) : ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-4 hover-lift border-start border-4 border-primary-theme">
                                    <div class="card-body p-4">
                                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
                                            <!-- File Icon -->
                                            <div class="flex-shrink-0">
                                                <div class="file-icon-box">
                                                    <i class="bi bi-file-earmark-arrow-down fs-2 text-primary"></i>
                                                    <span class="small fw-bold text-uppercase text-secondary" style="font-size: 10px;">
                                                        <?= esc($item['file_type']) ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-grow-1">
                                                <h5 class="fw-bold mb-2 text-dark"><?= esc($item['title']) ?></h5>
                                                <div class="d-flex flex-wrap gap-3 small text-muted">
                                                    <span class="d-flex align-items-center"><i class="bi bi-hdd me-1"></i><?= round($item['file_size'] / (1024 * 1024), 2) ?> MB</span>
                                                    <span class="d-flex align-items-center"><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($item['created_at'])) ?></span>
                                                    <span class="d-flex align-items-center"><i class="bi bi-download me-1"></i>Diunduh <?= number_format($item['download_count']) ?> kali</span>
                                                </div>
                                            </div>

                                            <!-- Action -->
                                            <div class="flex-shrink-0">
                                                <a href="<?= base_url('download/do/' . $item['id']) ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center shadow-sm">
                                                    <i class="bi bi-download me-2"></i> Unduh
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pager) : ?>
                        <div class="mt-5 d-flex justify-content-center">
                            <?= $pager->links('public', 'bootstrap_pagination') ?>
                        </div>
                    <?php endif; ?>

                <?php else : ?>
                    <div class="text-center py-5">
                        <div class="display-1 text-muted opacity-25 mb-4">
                            <i class="bi bi-folder-x"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Dokumen tidak ditemukan</h4>
                        <p class="text-secondary">Maaf, kami tidak menemukan dokumen yang sesuai dengan pencarian Anda.</p>
                        <a href="<?= base_url('download') ?>" class="btn btn-primary rounded-pill px-4 mt-3">Tampilkan Semua</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
