<?php
/**
 * 403 Error Page - Erişim Engellendi
 * LooMix.Click
 */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="error-page text-center">
                <!-- Error Icon -->
                <div class="error-icon mb-4">
                    <i class="fas fa-lock text-danger" style="font-size: 8rem; opacity: 0.7;"></i>
                </div>
                
                <!-- Error Code -->
                <h1 class="display-1 fw-bold text-danger mb-3">403</h1>
                
                <!-- Error Title -->
                <h2 class="h3 mb-4">Erişim Engellendi</h2>
                
                <!-- Error Description -->
                <div class="error-description mb-4">
                    <p class="lead text-muted mb-3">
                        Bu sayfaya erişim yetkiniz bulunmuyor. 
                        Giriş yapmış olmanız veya özel izninizin olması gerekebilir.
                    </p>
                    
                    <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger border-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <?= escape($errorMessage) ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Possible Reasons -->
                <div class="reasons mb-4">
                    <h4 class="h5 mb-3">Bu Durum Neden Oluşmuş Olabilir?</h4>
                    <div class="row text-start">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-slash text-warning me-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Giriş Yapmamışsınız</h6>
                                    <small class="text-muted">Bu sayfaya erişmek için giriş yapmanız gerekebilir</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-times text-warning me-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Yetkiniz Bulunmuyor</h6>
                                    <small class="text-muted">Bu içerik için özel izniniz olması gerekebilir</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock text-warning me-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Oturumunuz Sonlanmış</h6>
                                    <small class="text-muted">Güvenlik nedeniyle tekrar giriş yapmanız gerekebilir</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-shield-alt text-warning me-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Güvenlik Kısıtlaması</h6>
                                    <small class="text-muted">Bu alan sadece yetkili kişiler tarafından erişilebilir</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="error-actions mb-4">
                    <a href="<?= url('/admin/login') ?>" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Giriş Yap
                    </a>
                    <a href="<?= url('/') ?>" class="btn btn-outline-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i>
                        Ana Sayfa
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-lg" onclick="history.back()">
                        <i class="fas fa-arrow-left me-2"></i>
                        Geri Dön
                    </button>
                </div>
                
                <!-- Additional Help -->
                <div class="additional-help">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h5 class="card-title h6 mb-2">
                                <i class="fas fa-question-circle text-primary me-2"></i>
                                Yardıma İhtiyacınız Var mı?
                            </h5>
                            <p class="card-text small text-muted mb-3">
                                Eğer bu sayfaya erişmeniz gerektiğini düşünüyorsanız, 
                                lütfen sistem yöneticisiyle iletişime geçin.
                            </p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="<?= url('/iletisim') ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-envelope me-1"></i>
                                    İletişim
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#searchModal">
                                    <i class="fas fa-search me-1"></i>
                                    Arama Yap
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Help Text -->
                <div class="help-text mt-5">
                    <hr class="mb-4">
                    <p class="small text-muted mb-0">
                        Bu bir güvenlik önlemidir. Yetkisiz erişim girişimleri loglanmaktadır.
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

.reasons .d-flex {
    transition: all 0.3s ease;
    padding: 0.5rem;
    border-radius: 0.375rem;
}

.reasons .d-flex:hover {
    background-color: rgba(0,0,0,0.05);
    transform: translateX(5px);
}

.additional-help .card {
    transition: all 0.3s ease;
}

.additional-help .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
