<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?>Kelola Komentar Berita<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4 d-flex flex-wrap align-items-start justify-content-between gap-3">
    <div>
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/konten/berita') ?>">Berita</a></li>
                <li class="breadcrumb-item active" aria-current="page">Komentar</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-body mb-1">Kelola Komentar Berita</h1>
        <p class="text-secondary mb-0">
            Moderasikan komentar pengunjung (setujui, tolak, atau hapus) agar tampil pada halaman detail berita.
        </p>
    </div>
    <a class="btn btn-outline-secondary rounded-3" href="<?= base_url('admin/konten/berita') ?>">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Berita
    </a>
</div>

<div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-2 p-3">
    <i class="bi bi-info-circle-fill text-info fs-5"></i>
    <div>
        <span class="small text-secondary d-block">Menampilkan komentar untuk berita:</span>
        <strong class="text-dark"><?= esc($newsTitle) ?></strong>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="" method="get" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-secondary">Cari Komentar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-secondary">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0 rounded-end-3" 
                        placeholder="Nama atau isi komentar..." value="<?= esc($searchQuery ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-secondary">Status</label>
                <select name="status" class="form-select rounded-3">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                    <option value="approved" <?= ($statusFilter ?? '') === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-3 w-100">Filter</button>
                <?php if (($searchQuery ?? '') !== '' || ($statusFilter ?? '') !== ''): ?>
                    <a href="<?= base_url('admin/konten/komentar/' . (int)$newsId) ?>" class="btn btn-outline-secondary rounded-3">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 180px;">Penulis</th>
                        <th>Komentar</th>
                        <th style="width: 150px;">Status</th>
                        <th style="width: 150px;">Dibuat</th>
                        <th class="pe-4 text-end" style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($comments ?? []) === []) : ?>
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-secondary">
                                Tidak ada komentar yang ditemukan.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($comments as $row) : ?>
                            <?php
                            $status = $row['status'] ?? 'pending';
                            $dateLabel = '';
                            if (!empty($row['created_at'])) {
                                $dateKey = explode(' ', $row['created_at'])[0];
                                $dateLabel = \App\Models\NewsArticleModel::formatIndonesianDate($dateKey);
                            }
                            ?>
                            <tr>
                                <td class="ps-4 fw-medium">
                                    <?= esc((string) ($row['name'] ?? '')) ?>
                                </td>
                                <td class="text-secondary small">
                                    <?= nl2br(esc((string) ($row['comment'] ?? ''))) ?>
                                </td>
                                <td>
                                    <?php if ($status === 'approved') : ?>
                                        <span class="badge text-bg-success rounded-pill">Disetujui</span>
                                    <?php elseif ($status === 'rejected') : ?>
                                        <span class="badge text-bg-danger rounded-pill">Ditolak</span>
                                    <?php else : ?>
                                        <span class="badge text-bg-warning rounded-pill text-dark">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary small">
                                    <?= esc($dateLabel) ?>
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <?php if ($status !== 'approved') : ?>
                                        <form method="post" action="<?= base_url('admin/konten/komentar/' . (int) $row['id'] . '/setujui') ?>"
                                            class="d-inline" data-confirm="Setujui komentar ini agar tampil di situs publik?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="news_id" value="<?= (int)$newsId ?>">
                                            <button type="submit" class="btn btn-sm btn-success rounded-3">Setujui</button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($status !== 'rejected') : ?>
                                        <form method="post" action="<?= base_url('admin/konten/komentar/' . (int) $row['id'] . '/tolak') ?>"
                                            class="d-inline" data-confirm="Tolak komentar ini? Komentar tidak akan ditampilkan di publik.">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="news_id" value="<?= (int)$newsId ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-3">Tolak</button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="post" action="<?= base_url('admin/konten/komentar/' . (int) $row['id'] . '/hapus') ?>"
                                        class="d-inline" data-confirm="Hapus komentar ini secara permanen dari database?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="news_id" value="<?= (int)$newsId ?>">
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
