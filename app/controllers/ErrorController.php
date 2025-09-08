<?php
/**
 * Error Controller - Hata sayfalarını yönetir
 * LooMix.Click
 */

class ErrorController extends Controller {
    
    /**
     * 404 - Sayfa bulunamadı
     */
    public function notFound() {
        http_response_code(404);
        
        $view = new View();
        $view->render('errors/404', [
            'pageTitle' => '404 - Sayfa Bulunamadı' . META_TITLE_SUFFIX,
            'metaDescription' => 'Aradığınız sayfa bulunamadı. Ana sayfaya dönün veya site haritasını kullanın.',
            'metaKeywords' => '404, sayfa bulunamadı, hata',
            'robotsIndex' => false
        ], 'main');
    }
    
    /**
     * 500 - Sunucu hatası
     */
    public function serverError($message = 'Beklenmeyen bir hata oluştu') {
        http_response_code(500);
        
        $view = new View();
        $view->render('errors/500', [
            'pageTitle' => '500 - Sunucu Hatası' . META_TITLE_SUFFIX,
            'metaDescription' => 'Sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.',
            'errorMessage' => $message,
            'robotsIndex' => false
        ], 'main');
    }
    
    /**
     * 403 - Erişim engellendi
     */
    public function forbidden($message = 'Bu sayfaya erişim yetkiniz bulunmuyor') {
        http_response_code(403);
        
        $view = new View();
        $view->render('errors/403', [
            'pageTitle' => '403 - Erişim Engellendi' . META_TITLE_SUFFIX,
            'metaDescription' => 'Bu sayfaya erişim yetkiniz bulunmuyor.',
            'errorMessage' => $message,
            'robotsIndex' => false
        ], 'main');
    }
    
    /**
     * Maintenance - Bakım modu
     */
    public function maintenance() {
        http_response_code(503);
        
        $view = new View();
        $view->render('errors/maintenance', [
            'pageTitle' => 'Site Bakımda' . META_TITLE_SUFFIX,
            'metaDescription' => 'Sitemiz şu anda bakım aşamasında. Kısa süre sonra tekrar açılacak.',
            'robotsIndex' => false
        ], false); // Layout kullanma
    }
}
?>
