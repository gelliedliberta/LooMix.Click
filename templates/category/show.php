<div class="container my-4">
    <!-- Breadcrumb -->
    <?php if (!empty($breadcrumb)): ?>
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= url('/') ?>" class="text-decoration-none">Ana Sayfa</a>
            </li>
            <?php foreach ($breadcrumb as $crumb): ?>
                <?php if ($crumb['id'] === $category['id']): ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= escape($crumb['name']) ?>
                    </li>
                <?php else: ?>
                    <li class="breadcrumb-item">
                        <a href="<?= url('/kategori/' . $crumb['slug']) ?>" class="text-decoration-none">
                            <?= escape($crumb['name']) ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php endif; ?>
    
    <!-- Category Header -->
    <div class="category-header mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-3">
                    <?php if ($category['icon']): ?>
                        <div class="category-icon me-3">
                            <i class="<?= $category['icon'] ?> fa-3x" 
                               style="color: <?= $category['color'] ?: '#007bff' ?>"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h1 class="category-title display-4 fw-bold mb-2" 
                            style="color: <?= $category['color'] ?: '#007bff' ?>">
                            <?= escape($category['name']) ?> Haberleri
                        </h1>
                        <?php if ($category['description']): ?>
                            <p class="category-description text-muted fs-5 mb-0">
                                <?= escape($category['description']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="category-stats">
                    <div class="d-inline-flex align-items-center bg-light rounded-pill px-4 py-2">
                        <i class="fas fa-newspaper me-2 text-primary"></i>
                        <span class="fw-bold"><?= number_format($pagination['total_count']) ?></span>
                        <span class="text-muted ms-1">haber</span>
                    </div>
                </div>
                
                <!-- RSS Feed -->
                <div class="mt-3">
                    <a href="<?= url('/kategori/' . $category['slug'] . '/rss') ?>" 
                       class="btn btn-outline-warning btn-sm" target="_blank">
                        <i class="fas fa-rss me-2"></i>RSS
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sub Categories -->
    <?php if (!empty($subCategories)): ?>
    <div class="sub-categories mb-5">
        <h3 class="h5 fw-bold mb-3">
            <i class="fas fa-sitemap me-2 text-primary"></i>
            Alt Kategoriler
        </h3>
        <div class="row">
            <?php foreach ($subCategories as $subCategory): ?>
            <div class="col-lg-3 col-md-4 col-6 mb-3">
                <a href="<?= url('/kategori/' . $subCategory['slug']) ?>" 
                   class="text-decoration-none">
                    <div class="sub-category-card card border-0 shadow-sm h-100 text-center hover-lift">
                        <div class="card-body py-3">
                            <?php if ($subCategory['icon']): ?>
                                <div class="sub-category-icon mb-2">
                                    <i class="<?= $subCategory['icon'] ?> fa-lg" 
                                       style="color: <?= $subCategory['color'] ?: '#6c757d' ?>"></i>
                                </div>
                            <?php endif; ?>
                            <h6 class="card-title mb-0 text-dark"><?= escape($subCategory['name']) ?></h6>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <div class="row">
        <div class="col-lg-8">
            <!-- News Grid -->
            <?php if (!empty($news)): ?>
                <div class="news-grid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h4 fw-bold mb-0">
                            Son Haberler 
                            <span class="text-muted">
                                (<?= $pagination['current_page'] ?>/<?= $pagination['total_pages'] ?>)
                            </span>
                        </h2>
                        
                        <!-- Sort Options -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-sort me-1"></i>Sırala
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?sort=newest">En Yeni</a></li>
                                <li><a class="dropdown-item" href="?sort=popular">En Popüler</a></li>
                                <li><a class="dropdown-item" href="?sort=views">En Çok Okunan</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row news-container" id="newsContainer">
                        <?php foreach ($news as $index => $newsItem): ?>
                        <div class="col-md-6 mb-4 news-item" data-news-id="<?= $newsItem['id'] ?>">
                            <article class="news-card h-100">
                                <div class="card border-0 shadow-sm h-100 hover-lift">
                                    <?php if ($newsItem['featured_image']): ?>
                                    <div class="card-img-wrapper position-relative">
                                        <a href="<?= url('/haber/' . $newsItem['slug']) ?>">
                                            <img src="<?= getImageUrl($newsItem['featured_image']) ?>" 
                                                 alt="<?= escape($newsItem['title']) ?>" 
                                                 class="card-img-top" style="height: 200px; object-fit: cover;">
                                        </a>
                                        
                                        <!-- Breaking News Badge -->
                                        <?php if ($newsItem['is_breaking']): ?>
                                            <span class="position-absolute top-0 start-0 badge bg-danger m-2">
                                                <i class="fas fa-bolt me-1"></i>SON DAKİKA
                                            </span>
                                        <?php endif; ?>
                                        
                                        <!-- Featured Badge -->
                                        <?php if ($newsItem['is_featured']): ?>
                                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                                <i class="fas fa-star me-1"></i>ÖZEL
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h3 class="card-title h6 fw-bold mb-2">
                                            <a href="<?= url('/haber/' . $newsItem['slug']) ?>" 
                                               class="text-decoration-none text-dark stretched-link">
                                                <?= escape($newsItem['title']) ?>
                                            </a>
                                        </h3>
                                        
                                        <?php if ($newsItem['summary']): ?>
                                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                                <?= truncateText(strip_tags($newsItem['summary']), 100) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <div class="card-meta d-flex align-items-center justify-content-between small text-muted mt-auto">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user me-1"></i>
                                                <span><?= escape($newsItem['author_name']) ?></span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-clock me-1"></i>
                                                <?= formatDate($newsItem['publish_date'], 'd.m.Y') ?>
                                            </div>
                                        </div>
                                        
                                        <div class="card-stats d-flex align-items-center justify-content-between small text-muted mt-2">
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-eye me-1"></i>
                                                <?= number_format($newsItem['view_count']) ?>
                                            </div>
                                            <?php if ($newsItem['reading_time']): ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?= $newsItem['reading_time'] ?> dk
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="Kategori haberleri sayfalama" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>">
                                        <i class="fas fa-chevron-left"></i>
                                        <span class="d-none d-sm-inline ms-1">Önceki</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>">
                                        <span class="d-none d-sm-inline me-1">Sonraki</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Toplam <?= number_format($pagination['total_count']) ?> haber, 
                                <?= $pagination['current_page'] ?>/<?= $pagination['total_pages'] ?> sayfa gösteriliyor
                            </small>
                        </div>
                    </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="no-news text-center py-5">
                    <i class="fas fa-newspaper fa-4x text-muted opacity-25 mb-4"></i>
                    <h3 class="text-muted">Bu kategoride henüz haber bulunmuyor</h3>
                    <p class="text-muted">Yakında bu kategoride haberler yayınlanacak.</p>
                    <a href="<?= url('/') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <aside class="sidebar">
                <!-- Popular News in Category -->
                <?php if (!empty($popularNews)): ?>
                <div class="sidebar-widget mb-4">
                    <h3 class="widget-title h5 fw-bold mb-3 pb-2 border-bottom">
                        <i class="fas fa-fire text-danger me-2"></i>
                        Popüler Haberler
                    </h3>
                    
                    <div class="popular-news-list">
                        <?php foreach (array_slice($popularNews, 0, 5) as $index => $popular): ?>
                        <article class="popular-news-item d-flex mb-3 pb-3 <?= $index < 4 ? 'border-bottom' : '' ?>">
                            <div class="popular-rank me-3">
                                <span class="badge bg-danger rounded-circle d-flex align-items-center justify-content-center" 
                                      style="width: 30px; height: 30px;">
                                    <?= $index + 1 ?>
                                </span>
                            </div>
                            <div class="popular-content flex-grow-1">
                                <h6 class="fw-bold mb-1 lh-sm">
                                    <a href="<?= url('/haber/' . $popular['slug']) ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= truncateText($popular['title'], 70) ?>
                                    </a>
                                </h6>
                                <div class="small text-muted">
                                    <i class="far fa-eye me-1"></i>
                                    <?= number_format($popular['view_count']) ?> görüntülenme
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Sidebar Ad -->
                <div class="sidebar-widget mb-4">
                    <?= displayAd('sidebar_square') ?>
                </div>
                
                <!-- Latest News -->
                <?php if (!empty($latestNews)): ?>
                <div class="sidebar-widget mb-4">
                    <h3 class="widget-title h5 fw-bold mb-3 pb-2 border-bottom">
                        <i class="fas fa-clock text-info me-2"></i>
                        Son Haberler
                    </h3>
                    
                    <div class="latest-news-list">
                        <?php foreach (array_slice($latestNews, 0, 6) as $latest): ?>
                        <article class="latest-news-item mb-3">
                            <div class="row g-2">
                                <div class="col-4">
                                    <a href="<?= url('/haber/' . $latest['slug']) ?>">
                                        <img src="<?= getImageUrl($latest['featured_image']) ?>" 
                                             alt="<?= escape($latest['title']) ?>" 
                                             class="img-fluid rounded" style="height: 60px; object-fit: cover;">
                                    </a>
                                </div>
                                <div class="col-8">
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
                    
                    <div class="text-center mt-3">
                        <a href="<?= url('/haberler') ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i>Tüm Haberler
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.2s ease-in-out;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

.news-item {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease-out forwards;
}

.news-item:nth-child(1) { animation-delay: 0.1s; }
.news-item:nth-child(2) { animation-delay: 0.2s; }
.news-item:nth-child(3) { animation-delay: 0.3s; }
.news-item:nth-child(4) { animation-delay: 0.4s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track category page view
    if (typeof gtag !== 'undefined') {
        gtag('event', 'page_view', {
            page_title: '<?= escape($category['name']) ?> Kategori Sayfası',
            page_location: window.location.href,
            content_group1: 'Category',
            content_group2: '<?= escape($category['name']) ?>'
        });
    }
    
    // Infinite scroll (optional)
    let isLoading = false;
    let hasMore = <?= $pagination['current_page'] < $pagination['total_pages'] ? 'true' : 'false' ?>;
    let currentPage = <?= $pagination['current_page'] ?>;
    
    window.addEventListener('scroll', function() {
        if (isLoading || !hasMore) return;
        
        const scrollPosition = window.innerHeight + window.scrollY;
        const threshold = document.body.offsetHeight - 1000;
        
        if (scrollPosition >= threshold) {
            loadMoreNews();
        }
    });
    
    function loadMoreNews() {
        if (isLoading) return;
        
        isLoading = true;
        currentPage++;
        
        // Show loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'text-center my-4';
        loadingDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div>';
        document.getElementById('newsContainer').appendChild(loadingDiv);
        
        fetch(`<?= url('/kategori/' . $category['slug']) ?>?page=${currentPage}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.news.length > 0) {
                // Add new news items
                data.news.forEach(news => {
                    const newsHtml = createNewsCard(news);
                    document.getElementById('newsContainer').insertAdjacentHTML('beforeend', newsHtml);
                });
                
                hasMore = data.hasMore;
            } else {
                hasMore = false;
            }
        })
        .catch(error => {
            console.error('Error loading more news:', error);
            hasMore = false;
        })
        .finally(() => {
            isLoading = false;
            loadingDiv.remove();
        });
    }
    
    function createNewsCard(news) {
        return `
            <div class="col-md-6 mb-4 news-item" data-news-id="${news.id}">
                <article class="news-card h-100">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        ${news.featured_image ? `
                            <div class="card-img-wrapper position-relative">
                                <a href="/haber/${news.slug}">
                                    <img src="${news.featured_image}" alt="${news.title}" 
                                         class="card-img-top" style="height: 200px; object-fit: cover;">
                                </a>
                            </div>
                        ` : ''}
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h6 fw-bold mb-2">
                                <a href="/haber/${news.slug}" class="text-decoration-none text-dark stretched-link">
                                    ${news.title}
                                </a>
                            </h3>
                            <div class="card-meta d-flex align-items-center justify-content-between small text-muted mt-auto">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user me-1"></i>
                                    <span>${news.author_name}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="far fa-clock me-1"></i>
                                    ${news.publish_date}
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        `;
    }
});
</script>
