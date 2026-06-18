<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?><?= esc($news['title']) ?> - Dinas Kelautan dan Perikanan Papua
Tengah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/public-page.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/beranda.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="public-page-wrapper berita-detail-wrapper">
    <?= $this->include('public/partials/hero_header') ?>

    <section class="public-page-content">
        <div class="container px-sm-5 px-lg-0">
            <div class="content-card berita-detail-card">

                <div class="row g-4 g-lg-5">
                    <div class="col-lg-8">
                        <article class="detail-article">
                            <div class="detail-header mb-4">
                                <div class="detail-meta d-flex flex-wrap align-items-center gap-3 mt-3">
                                    <span><i class="bi bi-calendar-event me-1"></i><?= esc($news['date']) ?></span>
                                    <span><i class="bi bi-person me-1"></i><?= esc($news['author'] ?? 'Admin') ?></span>
                                    <span><i class="bi bi-eye me-1"></i><?= esc($news['views'] ?? '0') ?> tayangan</span>
                                    <span><i class="bi bi-hand-thumbs-up me-1 text-primary"></i><?= esc($news['likes'] ?? 0) ?> suka</span>
                                    <span><i class="bi bi-hand-thumbs-down me-1 text-danger"></i><?= esc($news['dislikes'] ?? 0) ?> tidak suka</span>
                                </div>
                            </div>

                            <div class="detail-share mb-4">
                                <span class="fw-semibold me-2">Bagikan:</span>
                                <?php
                                $shareUrl = urlencode(current_url());
                                $shareTitle = urlencode((string) ($news['title'] ?? ''));
                                ?>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank"
                                    rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>"
                                    target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-twitter-x"></i>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>"
                                    target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-linkedin"></i>
                                </a>
                                <a href="https://wa.me/?text=<?= $shareTitle . '%20' . $shareUrl ?>" target="_blank"
                                    rel="noopener noreferrer" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                                <a href="mailto:?subject=<?= $shareTitle ?>&body=<?= $shareUrl ?>"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-envelope"></i>
                                </a>
                            </div>

                            <div class="detail-featured-image mb-4">
                                <img src="<?= esc($news['image']) ?>" alt="<?= esc($news['title']) ?>">
                            </div>

                            <div class="detail-content">
                                <?= $news['content'] ?? '' ?>
                            </div>

                            <!-- Reaksi Suka / Tidak Suka -->
                            <?php
                            $likedNews = session()->get('liked_news') ?? [];
                            $dislikedNews = session()->get('disliked_news') ?? [];
                            $hasLiked = in_array((int)$news['id'], $likedNews);
                            $hasDisliked = in_array((int)$news['id'], $dislikedNews);
                            ?>
                            <div class="detail-reactions d-flex gap-2 my-4 py-3 border-top border-bottom">
                                <form action="<?= base_url('berita/' . (int)$news['id'] . '/like') ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn <?= $hasLiked ? 'btn-primary' : 'btn-outline-primary' ?> d-flex align-items-center gap-2 rounded-pill px-3">
                                        <i class="bi bi-hand-thumbs-up<?= $hasLiked ? '-fill' : '' ?>"></i>
                                        <span>Suka (<?= esc($news['likes'] ?? 0) ?>)</span>
                                    </button>
                                </form>
                                <form action="<?= base_url('berita/' . (int)$news['id'] . '/dislike') ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn <?= $hasDisliked ? 'btn-danger' : 'btn-outline-danger' ?> d-flex align-items-center gap-2 rounded-pill px-3">
                                        <i class="bi bi-hand-thumbs-down<?= $hasDisliked ? '-fill' : '' ?>"></i>
                                        <span>Tidak Suka (<?= esc($news['dislikes'] ?? 0) ?>)</span>
                                    </button>
                                </form>
                            </div>

                            <!-- Kolom Komentar -->
                            <div class="comments-section mt-5">
                                <h3 class="fw-bold mb-4 d-flex align-items-center gap-2 h5">
                                    <i class="bi bi-chat-left-text text-primary"></i>
                                    <span>Komentar (<?= count($comments ?? []) ?>)</span>
                                </h3>

                                <!-- Alert Messages -->
                                <?php if (session()->getFlashdata('message')) : ?>
                                    <div class="alert alert-success rounded-3 small mb-4">
                                        <?= esc(session()->getFlashdata('message')) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (session()->getFlashdata('errors')) : ?>
                                    <div class="alert alert-danger rounded-3 small mb-4">
                                        <ul class="mb-0 ps-3">
                                            <?php foreach (session()->getFlashdata('errors') as $err) : ?>
                                                <li><?= esc($err) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <!-- Daftar Komentar -->
                                <div class="comments-list d-flex flex-column gap-3 mb-4">
                                    <?php if (empty($comments)) : ?>
                                        <div class="text-muted small py-4 text-center border rounded-3 bg-light">
                                            Belum ada komentar. Jadilah yang pertama memberikan komentar!
                                        </div>
                                    <?php else : ?>
                                        <?php foreach ($comments as $comment) : ?>
                                            <div class="comment-item p-3 border rounded-3 bg-white shadow-sm d-flex gap-3">
                                                <div class="comment-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-semibold flex-shrink-0" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                    <?= esc(strtoupper(substr((string) ($comment['name'] ?? 'P'), 0, 1))) ?>
                                                </div>
                                                <div class="comment-body flex-grow-1">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="fw-bold text-dark small"><?= esc((string) ($comment['name'] ?? 'Anonim')) ?></span>
                                                        <span class="text-secondary small" style="font-size: 0.75rem;">
                                                            <?= esc(\App\Models\NewsArticleModel::formatIndonesianDate(explode(' ', $comment['created_at'])[0])) ?>
                                                        </span>
                                                    </div>
                                                    <p class="text-secondary small mb-0"><?= nl2br(esc((string) ($comment['comment'] ?? ''))) ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Form Komentar Baru -->
                                <div class="card border border-light shadow-sm rounded-3 mt-4">
                                    <div class="card-body p-4">
                                        <h4 class="h6 fw-bold text-dark mb-3">Tulis Komentar</h4>
                                        <form action="<?= base_url('berita/' . (int)$news['id'] . '/komentar') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="mb-3">
                                                <label for="comment-name" class="form-label small fw-semibold text-secondary">Nama Anda</label>
                                                <input type="text" name="name" id="comment-name" class="form-control rounded-3" required placeholder="Masukkan nama..." value="<?= esc(old('name')) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="comment-body" class="form-label small fw-semibold text-secondary">Isi Komentar</label>
                                                <textarea name="comment" id="comment-body" rows="4" class="form-control rounded-3" required placeholder="Tulis komentar Anda di sini..." style="resize: none;"><?= esc(old('comment')) ?></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 small fw-semibold">Kirim Komentar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </article>
                    </div>

                    <div class="col-lg-4">
                        <aside class="popular-news-box">
                            <h3 class="popular-title"><i class="bi bi-graph-up-arrow me-2"></i>Berita Terpopuler</h3>
                            <div class="popular-list">
                                <?php foreach ($popularNews as $article): ?>
                                    <a href="<?= base_url('berita/' . (int) $article['id']) ?>" class="popular-item">
                                        <img src="<?= esc($article['image']) ?>" alt="<?= esc($article['title']) ?>">
                                        <div>
                                            <h4><?= esc($article['title']) ?></h4>
                                            <p class="mb-1"><i
                                                    class="bi bi-calendar-event me-1"></i><?= esc($article['date']) ?></p>
                                            <p class="mb-0"><i class="bi bi-eye me-1"></i><?= esc($article['views']) ?></p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <a href="<?= base_url('berita') ?>" class="popular-all-link">Lihat Semua Berita <i
                                    class="bi bi-arrow-right"></i></a>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>