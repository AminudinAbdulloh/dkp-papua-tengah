<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?>Galeri Video<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4 d-flex flex-wrap align-items-start justify-content-between gap-3">
    <div>
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Galeri Video</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-body mb-1">Galeri Video</h1>
        <p class="text-secondary mb-0">
            Kelola daftar video YouTube yang tampil di beranda dan halaman galeri video.
        </p>
    </div>
    <a class="btn btn-primary rounded-3" href="<?= base_url('admin/konten/galeri-video/tambah') ?>">
        <i class="bi bi-plus-lg me-1"></i>Tambah video
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 120px;">Pratinjau</th>
                        <th>Judul</th>
                        <th class="d-none d-md-table-cell">ID YouTube</th>
                        <th class="d-none d-md-table-cell">Tanggal</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($videos ?? []) === []) : ?>
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-secondary">
                                Belum ada video. <a href="<?= base_url('admin/konten/galeri-video/tambah') ?>">Tambah video pertama</a>
                                atau jalankan <code class="small">php spark db:seed GalleryVideoSeeder</code>.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($videos as $row) : ?>
                            <?php
                            $yid = (string) ($row['youtube_id'] ?? '');
                            $thumb = $yid !== '' ? 'https://img.youtube.com/vi/' . rawurlencode($yid) . '/mqdefault.jpg' : '';
                            $dateLabel = \App\Models\GalleryVideoModel::displayDateFromRow($row);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if ($thumb !== '') : ?>
                                        <img src="<?= esc($thumb, 'attr') ?>" alt="" class="rounded-2 border" style="width: 96px; height: 54px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-medium"><?= esc((string) ($row['title'] ?? '')) ?></span>
                                    <div class="small text-secondary d-md-none font-monospace"><?= esc($yid) ?></div>
                                </td>
                                <td class="d-none d-md-table-cell small font-monospace"><?= esc($yid) ?></td>
                                <td class="d-none d-md-table-cell text-secondary small"><?= esc($dateLabel) ?></td>
                                <td class="pe-4 text-end text-nowrap">
                                    <a class="btn btn-sm btn-light border rounded-3" href="<?= esc((string) ($row['youtube_url'] ?? '#'), 'attr') ?>"
                                        target="_blank" rel="noopener noreferrer">YouTube</a>
                                    <a class="btn btn-sm btn-outline-primary rounded-3"
                                        href="<?= base_url('admin/konten/galeri-video/' . (int) $row['id'] . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= base_url('admin/konten/galeri-video/' . (int) $row['id'] . '/hapus') ?>"
                                        class="d-inline" data-confirm="Hapus video ini dari daftar?">
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
