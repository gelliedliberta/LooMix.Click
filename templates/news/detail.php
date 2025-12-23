<div class="container my-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <?php if (!empty($categoryBreadcrumb)): ?>
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= url('/') ?>" class="text-decoration-none">Ana Sayfa</a>
                    </li>
                    <?php foreach ($categoryBreadcrumb as $crumb): ?>
                        <li class="breadcrumb-item">
                            <a href="<?= url('/kategori/' . $crumb['slug']) ?>" class="text-decoration-none">
                                <?= escape($crumb['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= truncateText($news['title'], 50) ?>
                    </li>
                </ol>
            </nav>
            <?php endif; ?>
            
            <!-- News Article -->
            <article class="news-article">
                <!-- Article Header -->
                <header class="news-header mb-4">
                    <!-- Category -->
                    <div class="mb-3">
                        <a href="<?= url('/kategori/' . $news['category_slug']) ?>" 
                           class="badge bg-primary text-decoration-none fs-6 py-2 px-3"
                           style="background-color: <?= $news['category_color'] ?: '#007bff' ?> !important">
                            <?= escape($news['category_name']) ?>
                        </a>
                        
                        <?php if ($news['is_breaking']): ?>
                            <span class="badge bg-danger fs-6 py-2 px-3 ms-2">
                                <i class="fas fa-bolt me-1"></i>SON DAKİKA
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($news['is_featured']): ?>
                            <span class="badge bg-warning text-dark fs-6 py-2 px-3 ms-2">
                                <i class="fas fa-star me-1"></i>ÖZEL
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="news-title display-5 fw-bold text-dark mb-3">
                        <?= escape($news['title']) ?>
                    </h1>
                    
                    <!-- Summary -->
                    <?php if ($news['summary']): ?>
                    <div class="news-summary lead text-muted mb-4 border-start border-primary border-4 ps-3">
                        <?= escape($news['summary']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Meta Information -->
                    <div class="news-meta d-flex flex-wrap align-items-center text-muted mb-4">
                        <div class="me-4 mb-2">
                            <i class="fas fa-user me-1"></i>
                            <strong><?= escape($news['author_name']) ?></strong>
                        </div>
                        <div class="me-4 mb-2">
                            <i class="fas fa-calendar me-1"></i>
                            <?= formatDate($news['publish_date'], 'd F Y, l H:i') ?>
                        </div>
                        <?php if (defined('SHOW_VIEW_COUNTS') && SHOW_VIEW_COUNTS): ?>
                        <div class="me-4 mb-2">
                            <i class="fas fa-eye me-1"></i>
                            <?= number_format($news['view_count']) ?> görüntülenme
                        </div>
                        <?php endif; ?>
                        <?php if ($news['reading_time']): ?>
                        <div class="me-4 mb-2">
                            <i class="fas fa-clock me-1"></i>
                            <?= $news['reading_time'] ?> dakika okuma
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Social Share -->
                    <div class="social-share mb-4">
                        <div class="d-flex align-items-center">
                            <span class="me-3 fw-bold">Paylaş:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" 
                               class="btn btn-outline-primary btn-sm me-2 social-share-btn" data-platform="facebook" target="_blank">
                                <i class="fab fa-facebook-f me-1"></i>Facebook
                            </a>
                            <a href="https://x.com/intent/post?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($news['title']) ?>" 
                               class="btn btn-outline-dark btn-sm me-2 social-share-btn" data-platform="x" target="_blank">
                                <i class="fab fa-x-twitter me-1"></i>X
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($shareUrl) ?>" 
                               class="btn btn-outline-secondary btn-sm me-2 social-share-btn" data-platform="linkedin" target="_blank">
                                <i class="fab fa-linkedin me-1"></i>LinkedIn
                            </a>
                            <a href="whatsapp://send?text=<?= urlencode($news['title'] . ' ' . $shareUrl) ?>" 
                               class="btn btn-outline-success btn-sm me-2 social-share-btn" data-platform="whatsapp">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>                            
                            <button class="btn btn-outline-secondary btn-sm me-2 social-share-btn" onclick="copyToClipboard('<?= $shareUrl ?>')">
                                    <i class="fas fa-link me-1"></i>Kopyala
                                </button>
                        </div>
                    </div>
                </header>
                
                <!-- Featured Image -->
                <div class="news-image mb-4">
                    <figure class="figure mb-0">
                        <div class="news-hero">
                            <img src="<?= getImageUrl($news['featured_image']) ?>" 
                                 alt="<?= escape($news['image_alt'] ?: $news['title']) ?>" 
                                 class="news-hero-img"
                                 loading="eager"
                                 fetchpriority="high"
                                 decoding="async">
                        </div>
                        <?php if ($news['image_alt']): ?>
                        <figcaption class="figure-caption text-center mt-2">
                            <?= escape($news['image_alt']) ?>
                        </figcaption>
                        <?php endif; ?>
                    </figure>
                </div>
                
                <!-- Content Ad -->
                <?php if (ADS_ENABLED): ?>
                    <?php $contentAd = displayAd('content_inline'); ?>
                    <?php if (!empty($contentAd)): ?>
                    <div class="content-ad-top text-center mb-4">
                        <?= $contentAd ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Article Content -->
                <div class="news-content">
                    <?= renderNewsContent($news['content']) ?>
                </div>
                
                
            </article>          
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <aside class="sidebar">
                <!-- Popular News -->
                <?php if (!empty($popularNews)): ?>
                <div class="sidebar-widget mb-4">
                    <h3 class="widget-title h5 fw-bold mb-3 pb-2 border-bottom border-primary">
                        <i class="fas fa-fire text-primary me-2"></i>
                        Popüler Haberler <small class="text-muted">(7g)</small>
                    </h3>
                    
                    <div class="popular-news-list">
                        <?php foreach (array_slice($popularNews, 0, 6) as $index => $popular): ?>
                        <article class="popular-news-item d-flex mb-3 pb-3 <?= $index < 5 ? 'border-bottom' : '' ?>">
                            <div class="popular-rank me-3">
                                <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                      style="width: 30px; height: 30px; font-weight: 600;">
                                    <?= $index + 1 ?>
                                </span>
                            </div>
                            <div class="popular-content flex-grow-1">
                                <h6 class="fw-bold mb-1 lh-sm">
                                    <a href="<?= url('/haber/' . $popular['slug']) ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= truncateText($popular['title'], 80) ?>
                                    </a>
                                </h6>
                                <?php if (defined('SHOW_VIEW_COUNTS') && SHOW_VIEW_COUNTS): ?>
                                <div class="small text-muted">
                                    <i class="far fa-eye me-1"></i>
                                    <?= number_format((int)($popular['recent_views'] ?? 0)) ?> görüntülenme
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
                
                <!-- Latest News -->
                <?php if (!empty($latestNews)): ?>
                <div class="sidebar-widget mb-4">
                    <h3 class="widget-title h5 fw-bold mb-3 pb-2 border-bottom border-primary">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Son Haberler
                    </h3>
                    
                    <div class="latest-news-list">
                        <?php foreach (array_slice($latestNews, 0, 5) as $latest): ?>
                        <article class="latest-news-item mb-3">
                            <div class="row g-2">
                                <?php if ($latest['featured_image']): ?>
                                <div class="col-4">
                                    <a href="<?= url('/haber/' . $latest['slug']) ?>">
                                        <img src="<?= getImageUrl($latest['featured_image']) ?>" 
                                             alt="<?= escape($latest['title']) ?>" 
                                             class="img-fluid rounded" style="height: 60px; object-fit: cover;">
                                    </a>
                                </div>
                                <div class="col-8">
                                <?php else: ?>
                                <div class="col-12">
                                <?php endif; ?>
                                    <h6 class="fw-bold mb-1 lh-sm">
                                        <a href="<?= url('/haber/' . $latest['slug']) ?>" 
                                           class="text-decoration-none text-dark">
                                            <?= truncateText($latest['title'], 60) ?>
                                        </a>
                                    </h6>
                                    <div class="small text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        <?= formatDate($latest['publish_date'], 'd.m.Y') ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
    <!-- Tags -->
    <?php if (!empty($tags)): ?>
                <div class="news-tags mt-5 pt-4 border-top">
                    <h5 class="mb-3">
                        <i class="fas fa-tags me-2 text-primary"></i>
                        Etiketler
                    </h5>
                    <div class="tag-list">
                        <?php foreach ($tags as $tag): ?>
                            <a href="<?= url('/etiket/' . $tag['slug']) ?>" 
                               class="badge bg-light text-dark text-decoration-none me-2 mb-2 p-2 border">
                                <?= escape($tag['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Bottom Social Share -->
                <div class="bottom-social-share mt-5 pt-4 border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-2"></h6>
                                                <!-- Social Share -->
                    <div class="social-share mb-4">
                        <div class="d-flex align-items-center">
                            <span class="me-3 fw-bold">Bu haberi paylaş:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" 
                               class="btn btn-outline-primary btn-sm me-2 social-share-btn" data-platform="facebook" target="_blank">
                                <i class="fab fa-facebook-f me-1"></i>Facebook
                            </a>
                            <a href="https://x.com/intent/post?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($news['title']) ?>" 
                               class="btn btn-outline-dark btn-sm me-2 social-share-btn" data-platform="x" target="_blank">
                                <i class="fab fa-x-twitter me-1"></i>X
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($shareUrl) ?>" 
                               class="btn btn-outline-secondary btn-sm me-2 social-share-btn" data-platform="linkedin" target="_blank">
                                <i class="fab fa-linkedin me-1"></i>LinkedIn
                            </a>
                            <a href="whatsapp://send?text=<?= urlencode($news['title'] . ' ' . $shareUrl) ?>" 
                               class="btn btn-outline-success btn-sm me-2 social-share-btn" data-platform="whatsapp">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>                            
                            <button class="btn btn-outline-secondary btn-sm me-2 social-share-btn" onclick="copyToClipboard('<?= $shareUrl ?>')">
                                    <i class="fas fa-link me-1"></i>Kopyala
                                </button>
                        </div>
                    </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">
                                Son güncelleme: <?= formatDate($news['updated_at'], 'd.m.Y H:i') ?>
                            </small>
                        </div>
                    </div>
                </div>
     <!-- Related News -->
     <?php if (!empty($relatedNews)): ?>
            <section class="related-news mt-5">
                <h3 class="section-title h4 fw-bold mb-4 pb-2 border-bottom border-primary">
                    <i class="fas fa-newspaper text-primary me-2"></i>
                    İlgili Haberler
                </h3>
                
                <div class="row">
                    <?php foreach ($relatedNews as $related): ?>
                    <div class="col-md-4 mb-4">
                        <article class="related-news-item">
                            <div class="card border-0 shadow-sm h-100">
                                <?php if ($related['featured_image']): ?>
                                <div class="card-img-wrapper">
                                    <a href="<?= url('/haber/' . $related['slug']) ?>">
                                        <img src="<?= getImageUrl($related['featured_image']) ?>" 
                                             alt="<?= escape($related['title']) ?>" 
                                             class="card-img-top" style="height: 150px; object-fit: cover;">
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title h6 fw-bold mb-2">
                                        <a href="<?= url('/haber/' . $related['slug']) ?>" 
                                           class="text-decoration-none text-dark">
                                            <?= escape($related['title']) ?>
                                        </a>
                                    </h5>
                                    
                                    <div class="card-meta small text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= formatDate($related['publish_date'], 'd.m.Y') ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Social share tracking
    document.querySelectorAll('.social-share-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const platform = this.dataset.platform;
            
            // Analytics tracking
            if (typeof gtag !== 'undefined') {
                gtag('event', 'share', {
                    method: platform,
                    content_type: 'article',
                    item_id: '<?= $news['slug'] ?>'
                });
            }
        });
    });
    
    // Reading progress tracking
    let readingStartTime = Date.now();
    let hasScrolled = false;
    
    window.addEventListener('scroll', function() {
        if (!hasScrolled) {
            hasScrolled = true;
            
            // Track reading start
            if (typeof gtag !== 'undefined') {
                gtag('event', 'scroll', {
                    event_category: 'engagement',
                    event_label: '<?= $news['slug'] ?>'
                });
            }
        }
    });
    
    // Track reading time on page unload
    window.addEventListener('beforeunload', function() {
        const readingTime = Math.round((Date.now() - readingStartTime) / 1000);
        
        if (readingTime > 10 && typeof gtag !== 'undefined') {
            gtag('event', 'timing_complete', {
                name: 'reading_time',
                value: readingTime,
                event_category: 'engagement',
                event_label: '<?= $news['slug'] ?>'
            });
        }
    });
});

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show notification
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Kopyalandı!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
        
        // Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'share', {
                method: 'copy_link',
                content_type: 'article',
                item_id: '<?= $news['slug'] ?>'
            });
        }
    }).catch(function() {
        alert('Link kopyalanamadı. Lütfen manuel olarak kopyalayın.');
    });
}
</script>
