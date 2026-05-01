<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?>Kelola Informasi Publik<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4 d-flex flex-wrap align-items-start justify-content-between gap-3">
    <div>
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Informasi Publik</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-body mb-1">Kelola Informasi Publik</h1>
        <p class="text-secondary mb-0">
            Tambah, ubah, atau hapus informasi publik yang tampil di halaman PPID.
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-primary rounded-3" href="<?= base_url('admin/konten/kategori-publikasi') ?>">
            <i class="bi bi-folder2 me-1"></i>Kelola Kategori
        </a>
        <a class="btn btn-primary rounded-3" href="<?= base_url('admin/konten/informasi-publik/tambah') ?>">
            <i class="bi bi-plus-lg me-1"></i>Tambah informasi
        </a>
    </div>
</div>

<!-- Category filter tabs -->
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body py-2 px-3">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="small text-secondary me-1">Filter:</span>
            <a href="<?= base_url('admin/konten/informasi-publik') ?>"
                class="btn btn-sm rounded-pill <?= ($activeCategory ?? null) === null ? 'btn-primary' : 'btn-outline-secondary' ?>">
                Semua
            </a>
            <?php foreach ($categories as $slug => $label) : ?>
                <a href="<?= base_url('admin/konten/informasi-publik?kategori=' . $slug) ?>"
                    class="btn btn-sm rounded-pill <?= ($activeCategory ?? '') === $slug ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <?= esc($label) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 40px;">#</th>
                        <th>Judul</th>
                        <th class="d-none d-md-table-cell">Kategori</th>
                        <th class="d-none d-lg-table-cell">Sub-Kategori Publikasi</th>
                        <th class="d-none d-lg-table-cell">Tahun</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($items ?? []) === []) : ?>
                        <tr>
                            <td colspan="7" class="px-4 py-5 text-center text-secondary">
                                Belum ada informasi publik.
                                <a href="<?= base_url('admin/konten/informasi-publik/tambah') ?>">Tambah informasi pertama</a>.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php 
                        $currentPage = isset($pager) ? $pager->getCurrentPage('admin') : 1;
                        $perPage = isset($pager) ? $pager->getPerPage('admin') : 10;
                        $startNo = ($currentPage - 1) * $perPage + 1;
                        foreach ($items as $idx => $row) : 
                        ?>
                            <?php
                            $pub = (int) ($row['is_published'] ?? 0) === 1;
                            $catLabel = \App\Models\PublicInformationModel::categoryLabel((string) ($row['category'] ?? ''));
                            $pubCatName = (string) ($row['pub_cat_name'] ?? '');
                            ?>
                            <tr>
                                <td class="ps-4 text-secondary small"><?= $startNo + $idx ?></td>
                                <td>
                                    <span class="fw-medium"><?= esc((string) ($row['title'] ?? '')) ?></span>
                                    <?php if (trim((string) ($row['description'] ?? '')) !== '') : ?>
                                        <div class="small text-secondary text-truncate" style="max-width: 350px;">
                                            <?= esc((string) $row['description']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge rounded-pill text-bg-info"><?= esc($catLabel) ?></span>
                                </td>
                                <td class="d-none d-lg-table-cell small text-secondary">
                                    <?= $pubCatName !== '' ? esc($pubCatName) : '—' ?>
                                </td>
                                <td class="d-none d-lg-table-cell small text-secondary">
                                    <?= esc((string) ($row['year'] ?? '—')) ?>
                                </td>
                                <td>
                                    <?php if ($pub) : ?>
                                        <span class="badge text-bg-success rounded-pill">Terbit</span>
                                    <?php else : ?>
                                        <span class="badge text-bg-secondary rounded-pill">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary rounded-3"
                                        href="<?= base_url('admin/konten/informasi-publik/' . (int) $row['id'] . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= base_url('admin/konten/informasi-publik/' . (int) $row['id'] . '/hapus') ?>"
                                        class="d-inline" data-confirm="Hapus informasi ini?">
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
