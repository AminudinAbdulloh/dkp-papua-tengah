<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?>Kelola Download<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Download</li>
        </ol>
    </nav>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="h3 fw-bold text-body mb-1">Kelola Dokumen Download</h1>
            <p class="text-secondary mb-0">Daftar file dan dokumen yang dapat diunduh oleh publik.</p>
        </div>
        <a href="<?= base_url('admin/konten/download/tambah') ?>" class="btn btn-primary rounded-3 px-3">
            <i class="bi bi-plus-lg me-1"></i>Tambah Dokumen
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <!-- Search -->
        <div class="mb-4">
            <form action="" method="get" class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 rounded-start-3 text-secondary">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="q" class="form-control border-start-0 rounded-end-3" 
                            placeholder="Cari dokumen..." value="<?= esc($search ?? '') ?>">
                        <?php if ($search) : ?>
                            <a href="<?= base_url('admin/konten/download') ?>" class="btn btn-outline-secondary rounded-0">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                        <button class="btn btn-primary rounded-end-3" type="submit">Cari</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle border-top">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Informasi Dokumen</th>
                        <th>Ukuran & Tipe</th>
                        <th class="text-center">Download</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($downloads)) : ?>
                        <?php $no = 1 + (10 * (($pager->getCurrentPage('downloads') ?? 1) - 1)); ?>
                        <?php foreach ($downloads as $item) : ?>
                            <tr>
                                <td class="ps-3 text-secondary"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-body"><?= esc($item['title']) ?></div>
                                    <div class="mt-1 small text-muted">
                                        <i class="bi bi-calendar3 me-1"></i><?= date('d M Y, H:i', strtotime($item['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal px-2 py-1">
                                        <i class="bi bi-file-earmark-text me-1"></i><?= strtoupper($item['file_type']) ?>
                                    </span>
                                    <div class="small text-secondary mt-1">
                                        <?= round($item['file_size'] / (1024 * 1024), 2) ?> MB
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold"><?= number_format($item['download_count']) ?></div>
                                    <div class="small text-muted">kali</div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?= base_url('uploads/downloads/' . $item['file_path']) ?>" 
                                           class="btn btn-sm btn-light rounded-3" target="_blank" title="Lihat File">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/konten/download/' . $item['id'] . '/edit') ?>" 
                                           class="btn btn-sm btn-light rounded-3" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= base_url('admin/konten/download/' . $item['id'] . '/hapus') ?>" 
                                              method="post" class="d-inline" data-confirm="Hapus dokumen ini?">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-light text-danger rounded-3" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="bi bi-folder-x fs-1 d-block mb-3 opacity-25"></i>
                                Belum ada dokumen yang diunggah.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pager) : ?>
            <div class="mt-4">
                <?= $pager->links('downloads', 'bootstrap_pagination') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
