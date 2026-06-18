<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?>Kelola Berita<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4 d-flex flex-wrap align-items-start justify-content-between gap-3">
    <div>
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Berita</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-body mb-1">Kelola Berita</h1>
        <p class="text-secondary mb-0">
            Tambah, ubah, atau hapus artikel yang tampil di halaman beranda dan berita publik.
        </p>
    </div>
    <a class="btn btn-primary rounded-3" href="<?= base_url('admin/konten/berita/tambah') ?>">
        <i class="bi bi-plus-lg me-1"></i>Tambah berita
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= base_url('admin/konten/berita/pengaturan-eksklusif') ?>" method="post" class="row g-3 align-items-center">
            <?= csrf_field() ?>
            <div class="col-12 col-md-auto d-flex align-items-center gap-2">
                <i class="bi bi-gear-fill text-primary"></i>
                <label for="exclusive_limit" class="form-label small fw-semibold text-secondary mb-0">Limit Tampilan Berita Eksklusif:</label>
            </div>
            <div class="col-12 col-md-2">
                <input type="number" id="exclusive_limit" name="exclusive_limit" class="form-control rounded-3" min="1" max="20" value="<?= esc($exclusiveLimit ?? 5) ?>" required>
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-primary rounded-3">Simpan Limit</button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12 col-md-6 col-lg-4">
        <form action="" method="get" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-secondary">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0 ps-0 rounded-end-3" 
                    placeholder="Cari judul atau penulis..." value="<?= esc($searchQuery ?? '') ?>">
            </div>
            <?php if (($searchQuery ?? '') !== ''): ?>
                <a href="<?= base_url('admin/konten/berita') ?>" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
            <button type="submit" class="btn btn-light border rounded-3 px-3">Cari</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Judul</th>
                        <th class="d-none d-md-table-cell">Dibuat</th>
                        <th class="d-none d-lg-table-cell text-end">Tayangan</th>
                        <th class="d-none d-lg-table-cell">Penulis</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($articles ?? []) === []) : ?>
                        <tr>
                            <td colspan="6" class="px-4 py-5 text-center text-secondary">
                                Belum ada berita. <a href="<?= base_url('admin/konten/berita/tambah') ?>">Tambah berita</a>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($articles as $row) : ?>
                            <?php
                            $pub = (int) ($row['is_published'] ?? 0) === 1;
                            $dateLabel = \App\Models\NewsArticleModel::displayDateFromRow($row);
                            $viewsN = (int) ($row['views'] ?? 0);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-medium"><?= esc((string) ($row['title'] ?? '')) ?></span>
                                    <div class="small text-secondary d-md-none"><?= esc($dateLabel) ?></div>
                                </td>
                                <td class="text-secondary small d-none d-md-table-cell"><?= esc($dateLabel) ?></td>
                                <td class="d-none d-lg-table-cell text-end small"><?= esc(number_format($viewsN, 0, ',', '.')) ?></td>
                                <td class="d-none d-lg-table-cell"><?= esc((string) ($row['author'] ?? '—')) ?></td>
                                <td>
                                    <div class="d-flex flex-column gap-1 align-items-start">
                                        <?php if ($pub) : ?>
                                            <span class="badge text-bg-success rounded-pill">Terbit</span>
                                        <?php else : ?>
                                            <span class="badge text-bg-secondary rounded-pill">Draft</span>
                                        <?php endif; ?>
                                        <?php if ((int)($row['is_exclusive'] ?? 0) === 1) : ?>
                                            <span class="badge text-bg-warning rounded-pill text-dark" style="font-size: 0.75rem;"><i class="bi bi-star-fill me-1"></i>Eksklusif</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <?php if ($pub) : ?>
                                        <a class="btn btn-sm btn-light border rounded-3"
                                            href="<?= base_url('berita/' . (int) $row['id']) ?>" target="_blank" rel="noopener noreferrer"
                                            title="Lihat di situs">Lihat</a>
                                    <?php endif; ?>
                                    <a class="btn btn-sm btn-outline-success rounded-3"
                                        href="<?= base_url('admin/konten/komentar/' . (int) $row['id']) ?>"
                                        title="Kelola komentar berita ini">Komentar</a>
                                    <a class="btn btn-sm btn-outline-primary rounded-3"
                                        href="<?= base_url('admin/konten/berita/' . (int) $row['id'] . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= base_url('admin/konten/berita/' . (int) $row['id'] . '/hapus') ?>"
                                        class="d-inline" data-confirm="Hapus berita ini?">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (isset($pager) && $pager !== null): ?>
            <div class="mt-4 pb-3 d-flex justify-content-center">
                <?= $pager->links('admin', 'bootstrap_pagination') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
