<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?>Kelola Keberatan Informasi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4 d-flex flex-wrap align-items-start justify-content-between gap-3">
    <div>
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Keberatan Informasi</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-body mb-1">Kelola Keberatan Informasi</h1>
        <p class="text-secondary mb-0">
            Daftar pengajuan keberatan atas permohonan informasi yang diajukan masyarakat.
        </p>
    </div>
</div>

<!-- Status filter tabs -->
<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body py-2 px-3">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="small text-secondary me-1">Filter:</span>
            <a href="<?= base_url('admin/konten/keberatan-informasi') ?>"
                class="btn btn-sm rounded-pill <?= ($activeStatus ?? null) === null ? 'btn-primary' : 'btn-outline-secondary' ?>">
                Semua
            </a>
            <?php foreach ($statuses as $slug => $label) : ?>
                <a href="<?= base_url('admin/konten/keberatan-informasi?status=' . $slug) ?>"
                    class="btn btn-sm rounded-pill <?= ($activeStatus ?? '') === $slug ? 'btn-primary' : 'btn-outline-secondary' ?>">
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
                        <th>No. Registrasi</th>
                        <th>Pemohon</th>
                        <th class="d-none d-md-table-cell">Alasan</th>
                        <th class="d-none d-lg-table-cell">Tanggal</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($items ?? []) === []) : ?>
                        <tr>
                            <td colspan="7" class="px-4 py-5 text-center text-secondary">
                                Belum ada keberatan informasi.
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
                            $status = (string) ($row['status'] ?? 'diterima');
                            $badgeClass = \App\Models\InformationObjectionModel::statusBadgeClass($status);
                            $statusLabel = \App\Models\InformationObjectionModel::statusLabel($status);
                            $dateLabel = \App\Models\InformationObjectionModel::displayDateFromRow($row);
                            $reasonLabel = \App\Models\InformationObjectionModel::reasonLabel((string) ($row['objection_reason'] ?? ''));
                            ?>
                            <tr>
                                <td class="ps-4 text-secondary small"><?= $startNo + $idx ?></td>
                                <td>
                                    <a href="<?= base_url('admin/konten/keberatan-informasi/' . (int) $row['id']) ?>"
                                        class="fw-medium text-decoration-none">
                                        <?= esc((string) ($row['registration_number'] ?? '')) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-medium"><?= esc((string) ($row['name'] ?? '')) ?></span>
                                    <div class="small text-secondary"><?= esc((string) ($row['phone'] ?? '')) ?></div>
                                </td>
                                <td class="d-none d-md-table-cell small text-secondary" style="max-width: 200px;">
                                    <?= esc(mb_strimwidth($reasonLabel, 0, 45, '...')) ?>
                                </td>
                                <td class="d-none d-lg-table-cell small text-secondary">
                                    <?= esc($dateLabel) ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill <?= esc($badgeClass) ?>"><?= esc($statusLabel) ?></span>
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary rounded-3"
                                        href="<?= base_url('admin/konten/keberatan-informasi/' . (int) $row['id']) ?>">Detail</a>
                                    <form method="post" action="<?= base_url('admin/konten/keberatan-informasi/' . (int) $row['id'] . '/hapus') ?>"
                                        class="d-inline" data-confirm="Hapus keberatan ini?">
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
