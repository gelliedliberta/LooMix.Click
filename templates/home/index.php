<div class="container my-4">
    <!-- Hero Section with Featured News -->
    <?php if (!empty($featuredNews)): ?>
    <section class="hero-section mb-5">
        <div class="row">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <?php $slides = array_slice($featuredNews, 0, 5); ?>
                <?php if (!empty($slides)): ?>
                <!-- Main Featured Slider -->
                <div id="featuredCarousel" class="carousel slide featured-main mb-3" data-bs-ride="carousel" data-bs-interval="5000">
                    <?php if (count($slides) > 1): ?>
                    <div class="carousel-indicators">
                        <?php foreach ($slides as $idx => $slide): ?>
                            <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" aria-current="<?= $idx === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="carousel-inner">
                        <?php foreach ($slides as $idx => $mainFeatured): ?>
                        <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                            <a href="<?= url('/haber/' . $mainFeatured['slug']) ?>" class="text-decoration-none text-white">
                                <div class="featured-image-wrapper position-relative overflow-hidden rounded-3" style="height: 400px;">
                                    <img src="<?= getImageUrl($mainFeatured['featured_image']) ?>" 
                                         alt="<?= escape($mainFeatured['title']) ?>" 
                                         class="w-100 h-100 object-fit-cover">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>
                                    <div class="position-absolute bottom-0 start-0 p-4 w-100">
                                        <span class="badge bg-<?= $mainFeatured['category_color'] ?: 'primary' ?> mb-2">
                                            <?= escape($mainFeatured['category_name']) ?>
                                        </span>
                                        <!-- Responsive başlık: mobilde küçük, desktop'ta büyük -->
                                        <h2 class="text-white mb-2 fw-bold featured-carousel-title"><?= escape($mainFeatured['title']) ?></h2>
                                        <!-- Summary text: mobilde gizli, tablet+ görünür -->
                                        <p class="text-light mb-2 opacity-75 d-none d-md-block"><?= truncateText(strip_tags($mainFeatured['summary']), 120) ?></p>
                                        <div class="d-flex align-items-center text-light small opacity-75">
                                            <i class="far fa-clock me-1"></i>
                                            <?= formatDate($mainFeatured['publish_date'], 'd.m.Y H:i') ?>
                                            <?php if (defined('SHOW_VIEW_COUNTS') && SHOW_VIEW_COUNTS): ?>
                                            <span class="mx-2">•</span>
                                            <i class="far fa-eye me-1"></i>
                                            <?= number_format($mainFeatured['view_count']) ?> görüntülenme
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($slides) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Önceki</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Sonraki</span>
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Side Featured News (next 2 after slider) -->
            <div class="col-lg-4">
                <?php $sideStart = min(5, count($featuredNews)); ?>
                <?php $sideItems = array_slice($featuredNews, $sideStart, 2); ?>
                <?php if (!empty($sideItems)): ?>
                    <?php foreach ($sideItems as $sideFeatured): ?>
                        <div class="side-featured position-relative mb-3">
                            <a href="<?= url('/haber/' . $sideFeatured['slug']) ?>" class="text-decoration-none text-white">
                                <div class="side-featured-wrapper position-relative overflow-hidden rounded-3" style="height: 190px;">
                                    <img src="<?= getImageUrl($sideFeatured['featured_image']) ?>" 
                                         alt="<?= escape($sideFeatured['title']) ?>" 
                                         class="w-100 h-100 object-fit-cover">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-opacity-40"></div>
                                    <div class="position-absolute bottom-0 start-0 p-3 w-100">
                                        <span class="badge bg-<?= $sideFeatured['category_color'] ?: 'primary' ?> mb-2">
                                            <?= escape($sideFeatured['category_name']) ?>
                                        </span>
                                        <h3 class="h6 text-white mb-1 fw-bold"><?= escape($sideFeatured['title']) ?></h3>
                                        <div class="d-flex align-items-center text-light small opacity-75">
                                            <i class="far fa-clock me-1"></i>
                                            <?= formatDate($sideFeatured['publish_date'], 'd.m.Y') ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php else: ?>
    <!-- No Featured News Fallback -->
    <section class="hero-section mb-5">
        <div class="alert alert-warning">
            <h3>⚠️ Henüz öne çıkan haber bulunmuyor</h3>
            <p>Lütfen admin panelinden haberler ekleyin veya örnek verileri yükleyin.</p>
            <a href="admin" class="btn btn-primary">Admin Panel</a>
            <a href="test.php" class="btn btn-secondary">Test Sayfası</a>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Main Content -->
    <div class="row">
        <!-- News Content -->
        <div class="col-lg-8">
            <!-- Latest News Section -->
            <section class="latest-news mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title h4 fw-bold mb-0">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Son Haberler
                    </h2>
                    <a href="<?= url('/haberler') ?>" class="btn btn-outline-primary btn-sm">
                        Tümünü Gör <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                
                <?php if (!empty($latestNews)): ?>
                    <div class="row news-grid">
                        <?php foreach ($latestNews as $news): ?>
                        <div class="col-sm-6 mb-4">
                            <article class="news-card h-100">
                                <div class="card border-0 shadow-sm h-100 hover-shadow">
                                    <div class="card-img-wrapper position-relative">
                                        <a href="<?= url('/haber/' . $news['slug']) ?>">
                                            <img src="<?= getImageUrl($news['featured_image']) ?>" 
                                                 alt="<?= escape($news['title']) ?>" 
                                                 class="card-img-top" style="height: 200px; object-fit: cover;">
                                        </a>
                                        <?php if ($news['is_breaking']): ?>
                                            <span class="position-absolute top-0 start-0 badge bg-danger m-2">
                                                <i class="fas fa-bolt me-1"></i>SON DAKİKA
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <div class="card-category mb-2">
                                            <a href="<?= url('/kategori/' . $news['category_slug']) ?>" 
                                               class="badge text-decoration-none text-white"
                                               style="background-color: <?= $news['category_color'] ?: '#007bff' ?>">
                                                <?= escape($news['category_name']) ?>
                                            </a>
                                        </div>
                                        
                                        <h3 class="card-title h6 fw-bold mb-2 lh-sm">
                                            <a href="<?= url('/haber/' . $news['slug']) ?>" 
                                               class="text-decoration-none text-dark stretched-link">
                                                <?= escape($news['title']) ?>
                                            </a>
                                        </h3>
                                        
                                        <?php if ($news['summary']): ?>
                                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                                <?= truncateText(strip_tags($news['summary']), 100) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <div class="card-meta d-flex align-items-center justify-content-between small text-muted mt-auto">
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-clock me-1"></i>
                                                <?= formatDate($news['publish_date'], 'd.m.Y H:i') ?>
                                            </div>
                                            <?php if (defined('SHOW_VIEW_COUNTS') && SHOW_VIEW_COUNTS): ?>
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-eye me-1"></i>
                                                <?= number_format($news['view_count']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4>📰 Henüz haber yok</h4>
                        <p>Henüz site için haber eklenmemiş. Admin panelinden haber ekleyebilirsiniz.</p>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Content Ad -->
            <?php if (ADS_ENABLED): ?>
                <?php $inlineAd = displayAd('content_inline'); ?>
                <?php if (!empty($inlineAd)): ?>
                <div class="content-ad text-center mb-5">
                    <?= $inlineAd ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <aside class="sidebar">
                <!-- Popular News Widget -->
                <?php if (!empty($popularNews)): ?>
                <div class="sidebar-widget mb-4">
                    <h3 class="widget-title h5 fw-bold mb-3">
                        <i class="fas fa-fire text-danger me-2"></i>
                        Popüler Haberler
                    </h3>
                    
                    <div class="popular-news-list">
                        <?php foreach (array_slice($popularNews, 0, 6) as $index => $popular): ?>
                        <article class="popular-news-item d-flex mb-3 pb-3 <?= $index < 5 ? 'border-bottom' : '' ?>">
                            <div class="popular-rank me-3">
                                <span class="badge bg-danger rounded-circle d-flex align-items-center justify-content-center" 
                                      style="width: 30px; height: 30px;">
                                    <?= $index + 1 ?>
                                </span>
                            </div>
                            <div class="popular-content flex-grow-1">
                                <h4 class="h6 fw-bold mb-1 lh-sm">
                                    <a href="<?= url('/haber/' . $popular['slug']) ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= truncateText($popular['title'], 80) ?>
                                    </a>
                                </h4>
                                <?php if (defined('SHOW_VIEW_COUNTS') && SHOW_VIEW_COUNTS): ?>
                                <div class="small text-muted">
                                    <i class="far fa-eye me-1"></i>
                                    <?= number_format($popular['view_count']) ?> görüntülenme
                                </div>
                                <?php endif; ?>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Sidebar Ad -->
                <?php if (ADS_ENABLED): ?>
                    <?php $sidebarAd = displayAd('sidebar_square'); ?>
                    <?php if (!empty($sidebarAd)): ?>
                    <div class="sidebar-widget mb-4">
                        <?= $sidebarAd ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Categories Widget -->
                <?php if (!empty($categories)): ?>
                <div class="sidebar-widget mb-4">
                    <h3 class="widget-title h5 fw-bold mb-3">
                        <i class="fas fa-folder-open text-primary me-2"></i>
                        Kategoriler
                    </h3>
                    
                    <div class="categories-list">
                        <?php foreach (array_slice($categories, 0, 8) as $category): ?>
                        <div class="category-item">
                            <a href="<?= url('/kategori/' . $category['slug']) ?>" 
                               class="d-flex align-items-center justify-content-between text-decoration-none text-dark py-2 border-bottom">
                                <div class="d-flex align-items-center">
                                    <?php if ($category['icon']): ?>
                                        <i class="<?= $category['icon'] ?> me-2" 
                                           style="color: <?= $category['color'] ?: '#007bff' ?>"></i>
                                    <?php endif; ?>
                                    <span><?= escape($category['name']) ?></span>
                                </div>
                                <span class="badge bg-light text-dark"><?= $category['news_count'] ?></span>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: box-shadow 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.breaking-scroll {
    overflow: hidden;
    white-space: nowrap;
}

.breaking-item {
    display: inline-block;
    padding-right: 3rem;
    animation: scroll-left 30s linear infinite;
}

@keyframes scroll-left {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}
</style>