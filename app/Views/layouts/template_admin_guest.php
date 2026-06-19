<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?: 'Admin' ?></title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('bootstrap-icons/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/theme-tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>

<body class="admin-guest-body">
    <?= $this->renderSection('content') ?>
    <script src="<?= asset('js/jquery.min.js') ?>"></script>
    <script src="<?= asset('js/bootstrap.min.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>
