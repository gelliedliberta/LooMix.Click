<?php
/**
 * 500 Error Page - Sunucu Hatası
 * LooMix.Click
 */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="error-page text-center">
                <!-- Error Icon -->
                <div class="error-icon mb-4">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 8rem; opacity: 0.7;"></i>
                </div>
                
                <!-- Error Code -->
                <h1 class="display-1 fw-bold text-danger mb-3">500</h1>
                
                <!-- Error Title -->
                <h2 class="h3 mb-4">Sunucu Hatası Oluştu</h2>
                
                <!-- Error Description -->
                <div class="error-description mb-4">
                    <p class="lead text-muted mb-3">
                        Üzgünüz, sunucumuzda beklenmeyen bir hata oluştu. 
                        Teknik ekibimiz bu sorunu çözmek için çalışıyor.
                    </p>
                    
                    <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-light border">
                        <p class="mb-0 small text-muted">
                            <strong>Hata Detayı:</strong> <?= escape($errorMessage) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- What to do -->
                <div class="what-to-do mb-4">
                    <h4 class="h5 mb-3">Ne Yapabilirsiniz?</h4>
                    <div class="row text-start">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-redo text-primary me-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Sayfayı Yenileyin</h6>
                                    <small class="text-muted">Bu geçici bir sorun olabilir</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock text-primary me-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Biraz Bekleyin</h6>
                                    <small class="text-muted">Sorun kısa sürede çözülecektir</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-home text-primary me-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Ana Sayfaya Gidin</h6>
                                    <small class="text-muted">Diğer sayfalara erişmeyi deneyin</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-envelope text-primary me-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Bize Bildirin</h6>
                                    <small class="text-muted">Sorunu çözmemize yardımcı olun</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="error-actions">
                    <button type="button" class="btn btn-primary btn-lg me-3" onclick="window.location.reload()">
                        <i class="fas fa-redo me-2"></i>
                        Sayfayı Yenile
                    </button>
                    <a href="<?= url('/') ?>" class="btn btn-outline-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i>
                        Ana Sayfa
                    </a>
                    <a href="<?= url('/iletisim') ?>" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-envelope me-2"></i>
                        İletişim
                    </a>
                </div>
                
                <!-- Technical Info (for development) -->
                <?php if (defined('ENVIRONMENT') && ENVIRONMENT === 'development'): ?>
                <div class="technical-info mt-5">
                    <hr class="mb-4">
                    <h5 class="text-danger">Teknik Bilgiler (Geliştirme Modu)</h5>
                    <div class="alert alert-danger text-start">
                        <p class="mb-2"><strong>URL:</strong> <?= escape($_SERVER['REQUEST_URI'] ?? 'Unknown') ?></p>
                        <p class="mb-2"><strong>Method:</strong> <?= escape($_SERVER['REQUEST_METHOD'] ?? 'Unknown') ?></p>
                        <p class="mb-2"><strong>Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
                        <p class="mb-0"><strong>User Agent:</strong> <?= escape($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Help Text -->
                <div class="help-text mt-5">
                    <hr class="mb-4">
                    <p class="small text-muted mb-0">
                        Sorun devam ederse, lütfen 
                        <a href="<?= url('/iletisim') ?>" class="text-decoration-none">teknik destek</a> 
                        ile iletişime geçin.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
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

.what-to-do .d-flex {
    transition: all 0.3s ease;
    padding: 0.5rem;
    border-radius: 0.375rem;
}

.what-to-do .d-flex:hover {
    background-color: rgba(0,0,0,0.05);
    transform: translateX(5px);
}
</style>
