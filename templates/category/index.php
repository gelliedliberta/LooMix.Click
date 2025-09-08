<?php
/**
 * Kategoriler Sayfası
 */
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">Kategoriler</h1>
            <p class="text-muted mb-5">Haberleri kategorilere göre keşfedin</p>
        </div>
    </div>
    
    <?php if (!empty($categoryTree)): ?>
        <div class="row">
            <?php foreach ($categoryTree as $category): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="<?= $category['icon'] ?? 'fas fa-folder' ?> fa-3x" 
                                   style="color: <?= escape($category['color'] ?? '#007bff') ?>"></i>
                            </div>
                            
                            <h5 class="card-title mb-3"><?= escape($category['name']) ?></h5>
                            
                            <?php if (!empty($category['description'])): ?>
                                <p class="card-text text-muted small mb-3">
                                    <?= escape(truncateText($category['description'], 100)) ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <span class="badge bg-light text-dark">
                                    <?= number_format($category['news_count'] ?? 0) ?> haber
                                </span>
                            </div>
                            
                            <a href="<?= url('/kategori/' . $category['slug']) ?>" 
                               class="btn btn-outline-primary btn-sm">
                                Haberleri Gör <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Popüler Kategoriler -->
    <?php if (!empty($popularCategories)): ?>
        <div class="mt-5">
            <h3 class="h4 mb-4">En Popüler Kategoriler</h3>
            <div class="row">
                <?php foreach ($popularCategories as $category): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <a href="<?= url('/kategori/' . $category['slug']) ?>" 
                           class="card border-0 bg-light text-decoration-none h-100 hover-shadow">
                            <div class="card-body text-center py-3">
                                <i class="<?= $category['icon'] ?? 'fas fa-folder' ?> mb-2" 
                                   style="color: <?= escape($category['color'] ?? '#007bff') ?>"></i>
                                <h6 class="card-title text-dark mb-1"><?= escape($category['name']) ?></h6>
                                <small class="text-muted">
                                    <?= number_format($category['news_count'] ?? 0) ?> haber
                                </small>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.hover-shadow {
    transition: box-shadow 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}
</style>
