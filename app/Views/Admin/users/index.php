<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= esc($title) ?></h1>
    <a href="<?= base_url('admin/manajemen-user/tambah') ?>" class="btn btn-primary rounded-3 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah User
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3" style="width: 5%;">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-end" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)) : ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary">Belum ada data user.</td>
                        </tr>
                    <?php else : ?>
                        <?php 
                        $currentPage = isset($pager) ? $pager->getCurrentPage('admin') : 1;
                        $perPage = isset($pager) ? $pager->getPerPage('admin') : 10;
                        $no = ($currentPage - 1) * $perPage + 1;
                        foreach ($users as $user) : 
                        ?>
                            <tr>
                                <td class="px-4 py-3"><?= $no++ ?></td>
                                <td class="px-4 py-3 fw-medium"><?= esc($user['name']) ?></td>
                                <td class="px-4 py-3"><?= esc($user['email']) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ((int)$user['is_active'] === 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= base_url('admin/manajemen-user/' . $user['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary rounded-3" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ((int) session('admin_id') !== (int) $user['id']) : ?>
                                            <form action="<?= base_url('admin/manajemen-user/' . $user['id'] . '/hapus') ?>" method="post" class="d-inline" data-confirm="Apakah Anda yakin ingin menghapus user ini?">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" disabled title="Anda tidak dapat menghapus akun Anda sendiri">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
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
