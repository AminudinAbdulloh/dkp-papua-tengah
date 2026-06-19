<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?: 'Dinas Kelautan dan Perikanan - Papua Tengah' ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('bootstrap-icons/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/theme-tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/navbar-public.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/footer-public.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/scroll-to-top.css') ?>">

    <!-- Page-specific CSS -->
    <?= $this->renderSection('styles') ?>
</head>

<body>

    <?= $this->include('layouts/partials/navbar_public') ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('layouts/partials/footer') ?>

    <!-- Scroll to Top Button -->
    <a href="#" id="scrollToTop" class="scroll-to-top" title="Kembali ke atas">
        <i class="bi bi-arrow-up"></i>
    </a>

    <!-- Bootstrap JS -->
    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('scrollToTop');
            if (btn) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        btn.classList.add('show');
                    } else {
                        btn.classList.remove('show');
                    }
                });
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        });
    </script>

    <!-- Page-specific JS -->
    <?= $this->renderSection('scripts') ?>

</body>

</html>