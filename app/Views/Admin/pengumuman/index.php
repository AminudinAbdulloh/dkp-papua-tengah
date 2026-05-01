<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?>Pengumuman<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4 d-flex flex-wrap align-items-start justify-content-between gap-3">
    <div>
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengumuman</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-body mb-1">Pengumuman</h1>
        <p class="text-secondary mb-0">
            Kelola data pengumuman yang ada di website.
        </p>
    </div>
    <a class="btn btn-primary rounded-3" href="<?= base_url('admin/pengumuman/tambah') ?>">
        <i class="bi bi-plus-lg me-1"></i>Tambah Pengumuman
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 60px;">#</th>
                        <th>Judul</th>
                        <th>Berkas</th>
                        <th class="d-none d-md-table-cell" style="width: 140px;">Tanggal</th>
                        <th class="pe-4 text-end" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($pengumuman ?? []) === []) : ?>
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-secondary">
                                Belum ada Pengumuman. <a href="<?= base_url('admin/pengumuman/tambah') ?>">Tambah Pengumuman pertama</a>.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php 
                        $currentPage = isset($pager) ? $pager->getCurrentPage('admin') : 1;
                        $perPage = isset($pager) ? $pager->getPerPage('admin') : 10;
                        $startNo = ($currentPage - 1) * $perPage + 1;
                        foreach ($pengumuman as $index => $row) : 
                        ?>
                            <?php
                            $createdAt = (string) ($row['created_at'] ?? '');
                            $dateLabel = '';
                            if ($createdAt !== '' && preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $createdAt, $m)) {
                                $dateLabel = $m[3] . '/' . $m[2] . '/' . $m[1];
                            }
                            ?>
                            <tr>
                                <td class="ps-4 text-center">
                                    <span class="text-secondary"><?= $startNo + $index ?></span>
                                </td>
                                <td>
                                    <span class="fw-medium"><?= esc((string) ($row['judul'] ?? '')) ?></span>
                                    <div class="small text-secondary text-truncate" style="max-width: 400px;">
                                        <?= esc(mb_substr(strip_tags((string) ($row['deskripsi'] ?? '')), 0, 80)) ?>…
                                    </div>
                                </td>
                                <td>
                                    <?php if (! empty($row['berkas'])) : ?>
                                        <a href="<?= base_url('uploads/pengumuman/' . $row['berkas']) ?>" target="_blank" class="badge bg-info-subtle text-info text-decoration-none rounded-pill">Lihat Berkas</a>
                                    <?php else : ?>
                                        <span class="text-secondary small">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="d-none d-md-table-cell text-secondary small"><?= esc($dateLabel) ?></td>
                                <td class="pe-4 text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary rounded-3"
                                        href="<?= base_url('admin/pengumuman/' . (int) $row['id'] . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= base_url('admin/pengumuman/' . (int) $row['id'] . '/hapus') ?>"
                                        class="d-inline" data-confirm="Hapus pengumuman ini?">
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
