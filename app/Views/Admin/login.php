<?= $this->extend('layouts/template_admin_guest') ?>

<?= $this->section('title') ?>Login Admin<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-login-wrap d-flex align-items-center justify-content-center min-vh-100 px-3">
    <div class="w-100" style="max-width: 420px;">
        <div class="text-center mb-4">
            <div class="admin-login-icon rounded-4 d-inline-flex align-items-center justify-content-center mb-3">
                <i class="bi bi-shield-lock fs-2 text-primary"></i>
            </div>
            <h1 class="h4 fw-bold text-body mb-1">Panel Admin</h1>
            <p class="text-secondary small mb-0">Masuk dengan email dan kata sandi Anda.</p>
        </div>

        <div class="card border-0 shadow rounded-4 admin-login-card">
            <div class="card-body p-4 p-md-5">
                <?php if ($flash = session()->getFlashdata('message')) : ?>
                    <div class="alert alert-success small rounded-3 py-2 mb-3"><?= esc($flash) ?></div>
                <?php endif; ?>

                <?php if ($flash = session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger small rounded-3 py-2 mb-3"><?= esc($flash) ?></div>
                <?php endif; ?>

                <?php
                $errs = session()->getFlashdata('errors');
                if (is_array($errs) && $errs !== []) :
                ?>
                    <div class="alert alert-danger small rounded-3 py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errs as $err) : ?>
                                <li><?= esc(is_string($err) ? $err : (string) $err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('admin') ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Email</label>
                        <input type="email" class="form-control form-control-lg rounded-3" id="email" name="email"
                            autocomplete="username" value="<?= esc(old('email')) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-medium">Kata sandi</label>
                        <input type="password" class="form-control form-control-lg rounded-3" id="password" name="password"
                            autocomplete="current-password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center mt-4 mb-0">
            <a href="<?= base_url('/') ?>" class="text-decoration-none small text-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke beranda
            </a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
