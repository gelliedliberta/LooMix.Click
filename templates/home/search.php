<?php
/**
 * Arama Sonuçları Sayfası
 */
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="mb-4">
                <h1 class="h2 mb-3">
                    <?php if (!empty($query)): ?>
                        "<?= escape($query) ?>" için Arama Sonuçları
                    <?php else: ?>
                        Arama
                    <?php endif; ?>
                </h1>
                
                <?php if (!empty($query) && $totalResults > 0): ?>
                    <p class="text-muted">
                        <?= number_format($totalResults) ?> sonuç bulundu
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- Arama Formu -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= url('/ara') ?>">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   name="q" 
                                   value="<?= escape($query) ?>" 
                                   placeholder="Haber ara..." 
                                   required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search me-2"></i>Ara
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Debug Bilgileri (geliştirme aşamasında) -->
            <?php if (DEBUG_MODE && !empty($query)): ?>
                <div class="alert alert-info">
                    <strong>Debug:</strong> 
                    Arama terimi: "<?= escape($query) ?>" | 
                    Sonuç sayısı: <?= $totalResults ?> | 
                    Dönen kayıt: <?= count($results) ?>
                </div>
            <?php endif; ?>
            
            <!-- Arama Sonuçları -->
            <?php if (!empty($query)): ?>
                <?php if (!empty($results)): ?>
                    <div class="results">
                        <?php
                        // Basit highlight fonksiyonu
                        $q = trim($query);
                        $qEsc = preg_quote($q, '/');
                        $doHighlight = strlen($q) >= 3;
                        foreach ($results as $item):
                            $title = $item['title'];
                            $summary = $item['summary'] ?? '';
                            if ($doHighlight) {
                                $title = preg_replace('/(' . $qEsc . ')/iu', '<mark>$1</mark>', $title);
                                $summary = preg_replace('/(' . $qEsc . ')/iu', '<mark>$1</mark>', $summary);
                            }
                        ?>
                            <article class="mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="row g-0">
                                        <div class="col-md-3 d-none d-md-block">
                                            <img src="<?= getImageUrl($item['featured_image']) ?>" 
                                                 alt="<?= escape($item['image_alt'] ?: $item['title']) ?>" 
                                                 class="img-fluid h-100 object-fit-cover rounded-start">
                                        </div>
                                        <div class="col-md-9">
                                                <div class="card-body">
                                                    <div class="mb-2">
                                                        <span class="badge" style="background-color: <?= escape($item['category_color'] ?? '#007bff') ?>">
                                                            <?= escape($item['category_name'] ?? 'Genel') ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <h5 class="card-title">
                                                        <a href="<?= url('/haber/' . $item['slug']) ?>" 
                                                           class="text-decoration-none text-dark">
                                                            <?= $title ?>
                                                        </a>
                                                    </h5>
                                                    
                                                    <p class="card-text text-muted">
                                                        <?= truncateText(strip_tags($summary), 200) ?>
                                                    </p>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center">
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
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
                        <nav aria-label="Arama sonuçları sayfalama" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($pagination['current_page'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= url('/ara?q=' . urlencode($query) . '&page=' . ($pagination['current_page'] - 1)) ?>">
                                            <i class="fas fa-chevron-left"></i> Önceki
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                $start = max(1, $pagination['current_page'] - 2);
                                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                                ?>
                                
                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= url('/ara?q=' . urlencode($query) . '&page=' . $i) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= url('/ara?q=' . urlencode($query) . '&page=' . ($pagination['current_page'] + 1)) ?>">
                                            Sonraki <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                        <h3 class="text-muted">Sonuç bulunamadı</h3>
                        <p class="text-muted">
                            "<?= escape($query) ?>" için sonuç bulunamadı. 
                            Farklı kelimeler deneyebilir veya daha genel terimler kullanabilirsiniz.
                        </p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                    <h3 class="text-muted">Arama Yapın</h3>
                    <p class="text-muted">Yukarıdaki arama kutusunu kullanarak haber arayabilirsiniz.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <!-- Popüler Aramalar -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-fire me-2"></i>Popüler Aramalar</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= url('/ara?q=teknoloji') ?>" class="badge bg-light text-dark text-decoration-none">teknoloji</a>
                            <a href="<?= url('/ara?q=spor') ?>" class="badge bg-light text-dark text-decoration-none">spor</a>
                            <a href="<?= url('/ara?q=ekonomi') ?>" class="badge bg-light text-dark text-decoration-none">ekonomi</a>
                            <a href="<?= url('/ara?q=sağlık') ?>" class="badge bg-light text-dark text-decoration-none">sağlık</a>
                            <a href="<?= url('/ara?q=eğitim') ?>" class="badge bg-light text-dark text-decoration-none">eğitim</a>
                        </div>
                    </div>
                </div>
                
                <!-- Son Haberler -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
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
            </div>
        </div>
    </div>
</div>


