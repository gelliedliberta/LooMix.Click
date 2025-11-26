<?php
/**
 * Haberler Listesi Sayfası
 */
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0">
                    <?php if ($currentPage > 1): ?>
                        Tüm Haberler - Sayfa <?= $currentPage ?>
                    <?php else: ?>
                        Tüm Haberler
                    <?php endif; ?>
                </h1>
                <small class="text-muted">
                    <?= number_format($pagination['total_count']) ?> haber
                </small>
            </div>
            
            <?php if (!empty($news)): ?>
                <div class="row">
                    <?php foreach ($news as $item): ?>
                        <div class="col-md-6 mb-4">
                            <article class="news-card h-100">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="<?= getImageUrl($item['featured_image']) ?>" 
                                         alt="<?= escape($item['image_alt'] ?: $item['title']) ?>" 
                                         class="card-img-top" 
                                         style="height: 200px; object-fit: cover;">
                                    
                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-2">
                                            <span class="badge" style="background-color: <?= escape($item['category_color'] ?? '#007bff') ?>">
                                                <?= escape($item['category_name'] ?? 'Genel') ?>
                                            </span>
                                        </div>
                                        
                                        <h5 class="card-title">
                                            <a href="<?= url('/haber/' . $item['slug']) ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= escape($item['title']) ?>
                                            </a>
                                        </h5>
                                        
                                        <p class="card-text text-muted small flex-grow-1">
                                            <?= escape(truncateText($item['summary'] ?? '', 150)) ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= formatDate($item['publish_date']) ?>
                                            </small>
                                            <?php if (defined('SHOW_VIEW_COUNTS') && SHOW_VIEW_COUNTS): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-eye me-1"></i>
                                                <?= number_format($item['view_count'] ?? 0) ?>
                                            </small>
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
                    <nav aria-label="Sayfalama" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <!-- Önceki sayfa -->
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= url('/haberler?page=' . ($pagination['current_page'] - 1)) ?>">
                                        <i class="fas fa-chevron-left"></i> Önceki
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Sayfa numaraları -->
                            <?php
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= url('/haberler?page=' . $i) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Sonraki sayfa -->
                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= url('/haberler?page=' . ($pagination['current_page'] + 1)) ?>">
                                        Sonraki <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-newspaper text-muted mb-3" style="font-size: 3rem;"></i>
                    <h3 class="text-muted">Henüz haber yok</h3>
                    <p class="text-muted">İlk haberlerin yayınlanması için bekleyin.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <!-- Son Haberler -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Son Haberler</h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($latestNews)): ?>
                            <?php foreach (array_slice($latestNews, 0, 5) as $item): ?>
                                <div class="p-3 border-bottom">
                                    <h6 class="mb-1">
                                        <a href="<?= url('/haber/' . $item['slug']) ?>" 
                                           class="text-decoration-none text-dark">
                                            <?= escape(truncateText($item['title'], 80)) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <?= formatDate($item['publish_date']) ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Kategoriler -->
                <?php if (!empty($mainCategories)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Kategoriler</h6>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($mainCategories as $category): ?>
                                <a href="<?= url('/kategori/' . $category['slug']) ?>" 
                                   class="d-flex align-items-center p-3 text-decoration-none text-dark border-bottom">
                                    <i class="<?= $category['icon'] ?? 'fas fa-folder' ?> me-3" 
                                       style="color: <?= escape($category['color'] ?? '#007bff') ?>"></i>
                                    <span><?= escape($category['name']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
