<div class="container my-4">
    <!-- Tag Header -->
    <div class="tag-header d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold mb-0">
            <i class="fas fa-tag me-2" style="color: <?= escape($tag['color'] ?: '#6c757d') ?>"></i>
            <?= escape($tag['name']) ?> Etiketi
        </h1>
        <span class="badge bg-light text-dark">
            Toplam: <?= number_format($pagination['total_count']) ?> haber
        </span>
    </div>

    <?php if (!empty($tag['description'])): ?>
    <div class="alert alert-secondary">
        <?= escape($tag['description']) ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- News List -->
        <div class="col-lg-8">
            <?php if (!empty($news)): ?>
            <div class="row">
                <?php foreach ($news as $item): ?>
                <div class="col-md-6 mb-4">
                    <article class="news-card h-100">
                        <div class="card border-0 shadow-sm h-100 hover-shadow">
                            <div class="card-img-wrapper position-relative">
                                <a href="<?= url('/haber/' . $item['slug']) ?>">
                                    <img src="<?= getImageUrl($item['featured_image']) ?>"
                                         alt="<?= escape($item['title']) ?>"
                                         class="card-img-top" style="height: 200px; object-fit: cover;">
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="card-category mb-2">
                                    <a href="<?= url('/kategori/' . $item['category_slug']) ?>"
                                       class="badge text-decoration-none text-white"
                                       style="background-color: <?= !empty($item['category_color']) ? $item['category_color'] : '#007bff' ?>">
                                        <?= escape($item['category_name']) ?>
                                    </a>
                                </div>
                                <h3 class="card-title h6 fw-bold mb-2 lh-sm">
                                    <a href="<?= url('/haber/' . $item['slug']) ?>" 
                                       class="text-decoration-none text-dark stretched-link">
                                        <?= escape($item['title']) ?>
                                    </a>
                                </h3>
                                <?php if ($item['summary']): ?>
                                <p class="card-text text-muted small mb-3 flex-grow-1">
                                    <?= truncateText(strip_tags($item['summary']), 100) ?>
                                </p>
                                <?php endif; ?>
                                <div class="card-meta d-flex align-items-center justify-content-between small text-muted mt-auto">
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-clock me-1"></i>
                                        <?= formatDate($item['publish_date'], 'd.m.Y H:i') ?>
                                    </div>
                                <?php if (defined('SHOW_VIEW_COUNTS') && SHOW_VIEW_COUNTS): ?>
                                <div class="d-flex align-items-center">
                                    <i class="far fa-eye me-1"></i>
                                    <?= number_format($item['view_count']) ?>
                                </div>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
            <nav aria-label="Sayfalama">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <li class="page-item <?= $i === (int)$pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('/etiket/' . $tag['slug'] . '?page=' . $i) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>

            <?php else: ?>
            <div class="alert alert-info">
                Bu etiketle ilişkili yayınlanmış haber bulunamadı.
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <aside class="sidebar">
                <?php if (!empty($relatedTags)): ?>
                <div class="sidebar-widget mb-4">
                    <h3 class="widget-title h5 fw-bold mb-3">
                        <i class="fas fa-tags text-primary me-2"></i>
                        İlgili Etiketler
                    </h3>
                    <div class="tags-cloud">
                        <?php foreach ($relatedTags as $rTag): ?>
                        <a href="<?= url('/etiket/' . $rTag['slug']) ?>" 
                           class="badge bg-light text-dark me-2 mb-2"
                           style="border: 1px solid #e9ecef;">
                            <?= escape($rTag['name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (ADS_ENABLED): ?>
                    <?php $sidebarAd = displayAd('sidebar_square'); ?>
                    <?php if (!empty($sidebarAd)): ?>
                    <div class="sidebar-widget mb-4">
                        <?= $sidebarAd ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</div>


