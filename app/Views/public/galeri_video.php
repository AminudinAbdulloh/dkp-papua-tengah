<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?>Galeri Video - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= asset('css/public-page.css') ?>">
<link rel="stylesheet" href="<?= asset('css/beranda.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= asset('js/beranda.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="public-page-wrapper">
    <?= $this->include('public/partials/hero_header') ?>

    <section class="public-page-content">
        <div class="container px-sm-5 px-lg-0">
            <div class="content-card">
                <div class="row g-4 g-lg-5">
                    <?php foreach ($latestVideos as $video): ?>
                        <div class="col-md-6 col-lg-4">
                            <a href="#" class="video-card js-video-trigger"
                                data-youtube-id="<?= esc($video['youtube_id']) ?>"
                                data-video-title="<?= esc($video['title']) ?>">
                                <div class="video-thumb-wrap">
                                    <img src="https://img.youtube.com/vi/<?= esc($video['youtube_id']) ?>/hqdefault.jpg"
                                        alt="<?= esc($video['title']) ?>" class="video-thumb">
                                    <div class="video-overlay"></div>
                                    <div class="video-play-center">
                                        <div class="video-play-btn">
                                            <i class="bi bi-play-fill"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="video-meta">
                                    <i class="bi bi-play-circle"></i>
                                    <time><?= esc($video['date']) ?></time>
                                </div>
                                <h3 class="video-title"><?= esc($video['title']) ?></h3>
                            </a>
                        </div>
                    <?php endforeach ?>
                </div>
                <?php if (isset($pager) && $pager !== null): ?>
                    <div class="mt-5 d-flex justify-content-center">
                        <?= $pager->links('public', 'bootstrap_pagination') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?= $this->include('public/partials/video_player_modal') ?>
</div>
<?= $this->endSection() ?>