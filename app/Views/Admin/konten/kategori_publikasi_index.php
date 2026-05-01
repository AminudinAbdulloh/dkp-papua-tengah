<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?>Kelola Kategori Publikasi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4 d-flex flex-wrap align-items-start justify-content-between gap-3">
    <div>
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kategori Publikasi</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-body mb-1">Kelola Kategori Publikasi</h1>
        <p class="text-secondary mb-0">
            Sub-kategori publikasi yang tampil di sidebar halaman publik (misal: Laporan Kinerja, LHKPN, dll).
        </p>
    </div>
    <a class="btn btn-primary rounded-3" href="<?= base_url('admin/konten/kategori-publikasi/tambah') ?>">
        <i class="bi bi-plus-lg me-1"></i>Tambah kategori
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 50px;">Urutan</th>
                        <th>Kategori Utama</th>
                        <th>Sub-Kategori</th>
                        <th class="d-none d-md-table-cell">Slug</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($items ?? []) === []) : ?>
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-secondary small">
                                Belum ada sub-kategori publikasi.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($items as $row) : ?>
                            <?php $typeSlug = (string) ($row['publication_type'] ?? ''); ?>
                            <tr>
                                <td class="ps-4 text-secondary"><?= (int) ($row['sort_order'] ?? 0) ?></td>
                                <td>
                                    <span class="badge text-bg-light border text-secondary"><?= esc($typeLabels[$typeSlug] ?? $typeSlug) ?></span>
                                </td>
                                <td>
                                    <span class="fw-medium"><?= esc((string) ($row['name'] ?? '')) ?></span>
                                </td>
                                <td class="d-none d-md-table-cell small font-monospace text-secondary">
                                    <?= esc((string) ($row['slug'] ?? '')) ?>
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary rounded-3"
                                        href="<?= base_url('admin/konten/kategori-publikasi/' . (int) $row['id'] . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= base_url('admin/konten/kategori-publikasi/' . (int) $row['id'] . '/hapus') ?>"
                                        class="d-inline" data-confirm="Hapus kategori ini? Dokumen yang terhubung tidak akan dihapus.">
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
