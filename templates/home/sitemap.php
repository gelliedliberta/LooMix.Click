<?php
/**
 * Site Haritası Sayfası
 */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="h2">Site Haritası</h1>
                <p class="lead text-muted">Tüm sayfalarımıza kolayca erişin</p>
            </div>
            
            <div class="row">
                <!-- Ana Sayfalar -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h4 class="h5 mb-4">
                                <i class="fas fa-home text-primary me-3"></i>
                                Ana Sayfalar
                            </h4>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="<?= url('/') ?>" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-muted small"></i>
                                        Ana Sayfa
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/haberler') ?>" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-muted small"></i>
                                        Tüm Haberler
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/kategoriler') ?>" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-muted small"></i>
                                        Kategoriler
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/ara') ?>" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-muted small"></i>
                                        Arama
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Kurumsal Sayfalar -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h4 class="h5 mb-4">
                                <i class="fas fa-building text-success me-3"></i>
                                Kurumsal
                            </h4>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="<?= url('/hakkimizda') ?>" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-muted small"></i>
                                        Hakkımızda
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/iletisim') ?>" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-muted small"></i>
                                        İletişim
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/gizlilik-politikasi') ?>" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-muted small"></i>
                                        Gizlilik Politikası
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= url('/rss') ?>" class="text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 text-muted small"></i>
                                        RSS Feed
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Kategoriler -->
            <?php if (!empty($mainCategories)): ?>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h4 class="h5 mb-4">
                            <i class="fas fa-tags text-info me-3"></i>
                            Haber Kategorileri
                        </h4>
                        <div class="row">
                            <?php foreach ($mainCategories as $category): ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                    <a href="<?= url('/kategori/' . $category['slug']) ?>" 
                                       class="text-decoration-none d-flex align-items-center">
                                        <i class="<?= $category['icon'] ?? 'fas fa-folder' ?> me-2" 
                                           style="color: <?= escape($category['color'] ?? '#007bff') ?>"></i>
                                        <span><?= escape($category['name']) ?></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Son Haberler -->
            <?php if (!empty($latestNews)): ?>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h4 class="h5 mb-4">
                            <i class="fas fa-clock text-warning me-3"></i>
                            Son Haberler
                        </h4>
                        <div class="row">
                            <?php foreach (array_slice($latestNews, 0, 8) as $item): ?>
                                <div class="col-lg-6 mb-2">
                                    <a href="<?= url('/haber/' . $item['slug']) ?>" 
                                       class="text-decoration-none d-flex align-items-start">
                                        <i class="fas fa-chevron-right me-2 text-muted small mt-1"></i>
                                        <span class="small"><?= escape(truncateText($item['title'], 60)) ?></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="<?= url('/haberler') ?>" class="btn btn-outline-primary btn-sm">
                                Tüm Haberleri Gör <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>XML Sitemap:</strong> Arama motorları için 
                <a href="<?= url('/api/sitemap') ?>" class="text-decoration-none" target="_blank">
                    XML sitemap'imizi
                </a> kullanabilirsiniz.
            </div>
        </div>
    </div>
</div>
