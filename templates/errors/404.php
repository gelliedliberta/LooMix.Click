<?php
/**
 * 404 Error Page - Sayfa Bulunamadı
 * LooMix.Click
 */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="error-page text-center">
                <!-- Error Icon -->
                <div class="error-icon mb-4">
                    <i class="fas fa-search text-muted" style="font-size: 8rem; opacity: 0.3;"></i>
                </div>
                
                <!-- Error Code -->
                <h1 class="display-1 fw-bold text-primary mb-3">404</h1>
                
                <!-- Error Title -->
                <h2 class="h3 mb-4">Aradığınız Sayfa Bulunamadı</h2>
                
                <!-- Error Description -->
                <p class="lead text-muted mb-4">
                    Üzgünüz, aradığınız sayfa kaldırılmış, taşınmış veya hiç var olmamış olabilir. 
                    Lütfen URL'yi kontrol edin veya ana sayfamızdan devam edin.
                </p>
                
                <!-- Action Buttons -->
                <div class="error-actions">
                    <a href="<?= url('/') ?>" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i>
                        Ana Sayfaya Dön
                    </a>
                    <button type="button" class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fas fa-search me-2"></i>
                        Haber Ara
                    </button>
                </div>
                
                <!-- Popular Categories -->
                <?php if (!empty($mainCategories)): ?>
                <div class="popular-categories mt-5">
                    <h4 class="h5 mb-3 text-muted">Popüler Kategoriler</h4>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <?php foreach (array_slice($mainCategories, 0, 6) as $category): ?>
                        <a href="<?= url('/kategori/' . $category['slug']) ?>" class="btn btn-outline-secondary btn-sm">
                            <?php if ($category['icon']): ?>
                            <i class="<?= $category['icon'] ?> me-1"></i>
                            <?php endif; ?>
                            <?= escape($category['name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Help Text -->
                <div class="help-text mt-5">
                    <hr class="mb-4">
                    <p class="small text-muted mb-0">
                        Bu durumu düzeltmemize yardımcı olmak için 
                        <a href="<?= url('/iletisim') ?>" class="text-decoration-none">bize bildirin</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for better mobile experience -->
<style>
@media (max-width: 576px) {
    .error-icon i {
        font-size: 4rem !important;
    }
    .display-1 {
        font-size: 4rem;
    }
    .error-actions .btn {
        display: block;
        width: 100%;
        margin: 0.5rem 0 !important;
    }
}

.error-page {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.popular-categories .btn {
    transition: all 0.3s ease;
}

.popular-categories .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
