<?php
/**
 * LooMix.Click - Modern Haber Sitesi
 * Ana giriş dosyası
 * 
 * @version 1.0.0
 * @author LooMix Team
 */

// Hata raporlama: DEBUG_MODE'a göre ayarla
require_once 'config/config.php';
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
    ini_set('display_errors', 0);
}

// Zaman dilimi ayarla
date_default_timezone_set('Europe/Istanbul');

// Autoloader ve konfigürasyon
require_once 'includes/autoloader.php';
require_once 'includes/functions.php';

// Global hata yakalama (API'ler için tutarlı JSON, diğerleri için özel sayfalar)
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function($e) {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

    $isAdminApi = substr($uri, 0, 11) === '/admin/api/';
    $isPublicApi = substr($uri, 0, 5) === '/api/';
    $wantsJson = (strpos($accept, 'application/json') !== false) || (strtolower($xhr) === 'xmlhttprequest');

    if ($isAdminApi || $isPublicApi || $wantsJson) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        $payload = [
            'error' => 'Internal Server Error',
        ];
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            $payload['message'] = $e->getMessage();
            $payload['file'] = $e->getFile();
            $payload['line'] = $e->getLine();
        }
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Web arayüzü için özel hata sayfası
    try {
        $controller = new ErrorController();
        $controller->serverError(defined('DEBUG_MODE') && DEBUG_MODE ? $e->getMessage() : 'Beklenmeyen bir hata oluştu');
    } catch (Throwable $t) {
        // Yedek basit çıktı
        http_response_code(500);
        echo '500 - Sunucu Hatası';
    }
});

// Router başlat
$router = new Router();

// Ana sayfa
$router->get('/', 'HomeController@index');

// Haber sayfaları
$router->get('/haberler', 'NewsController@index');
$router->get('/haber/{slug}', 'NewsController@show');
$router->get('/amp/haber/{slug}', 'NewsController@amp');

// Kategori sayfaları
$router->get('/kategoriler', 'CategoryController@index');
$router->get('/kategori/{slug}', 'CategoryController@show');
$router->get('/kategori/{slug}/rss', 'CategoryController@rss');

// Arama
$router->get('/ara', 'HomeController@search');

// Diğer sayfalar
$router->get('/hakkimizda', 'HomeController@about');
$router->get('/iletisim', 'HomeController@contact');
$router->get('/gizlilik-politikasi', 'HomeController@privacy');
$router->get('/site-haritasi', 'HomeController@sitemap');

// RSS
$router->get('/rss', 'HomeController@rss');

// Admin paneli
$router->get('/admin', 'AdminController@index');
$router->get('/admin/login', 'AdminController@login');
$router->post('/admin/auth', 'AdminController@authenticate');
$router->get('/admin/cikis', 'AdminController@logout');

// Haber yönetimi
$router->get('/admin/haberler', 'AdminController@news');
$router->get('/admin/haber-ekle', 'AdminController@addNews');
$router->get('/admin/haber-duzenle/{id}', 'AdminController@editNews');
$router->post('/admin/haber-kaydet', 'AdminController@saveNews');
$router->get('/admin/haber-sil/{id}', 'AdminController@deleteNews');
$router->post('/admin/upload-file', 'AdminController@uploadFile');

// Kategori yönetimi
$router->get('/admin/kategoriler', 'AdminController@categories');

// Kategori API'leri
$router->get('/admin/api/categories/{id}', 'AdminController@getCategoryById');
$router->post('/admin/api/categories/save', 'AdminController@saveCategory');
$router->post('/admin/api/categories/{id}/toggle-status', 'AdminController@toggleCategoryStatus');
$router->post('/admin/api/categories/{id}/update-order', 'AdminController@updateCategoryOrder');
$router->delete('/admin/api/categories/{id}/delete', 'AdminController@deleteCategory');

// Kullanıcı yönetimi
$router->get('/admin/kullanicilar', 'AdminController@users');

// Kullanıcı API'leri
$router->get('/admin/api/users/{id}', 'AdminController@getUserById');
$router->post('/admin/api/users/save', 'AdminController@saveUser');
$router->post('/admin/api/users/{id}/toggle-status', 'AdminController@toggleUserStatus');
$router->delete('/admin/api/users/{id}/delete', 'AdminController@deleteUserById');
$router->get('/admin/api/users/{id}/stats', 'AdminController@getUserStats');

// Reklam yönetimi
$router->get('/admin/reklam-alanlari', 'AdminController@adZones');

// Reklam alanları API'leri
$router->get('/admin/api/ad-zones/{id}', 'AdminController@getAdZoneById');
$router->post('/admin/api/ad-zones/save', 'AdminController@saveAdZone');
$router->post('/admin/api/ad-zones/{id}/toggle-status', 'AdminController@toggleAdZoneStatus');
$router->delete('/admin/api/ad-zones/{id}/delete', 'AdminController@deleteAdZone');
$router->get('/admin/api/ad-zones/test', 'AdminController@testAdZones');

// Etiket yönetimi
$router->get('/admin/etiketler', 'AdminController@tags');

// Etiket API'leri
$router->get('/admin/api/tags/{id}', 'AdminController@getTagById');
$router->post('/admin/api/tags/save', 'AdminController@saveTag');
$router->delete('/admin/api/tags/{id}/delete', 'AdminController@deleteTag');
$router->delete('/admin/api/tags/clean-unused', 'AdminController@cleanUnusedTags');

// Site ayarları
$router->get('/admin/ayarlar', 'AdminController@settings');
$router->post('/admin/api/settings/save', 'AdminController@saveSettings');
$router->post('/admin/api/settings/reset', 'AdminController@resetSettings');

// Gelir raporları
$router->get('/admin/gelir-raporlari', 'AdminController@revenueReports');
$router->post('/admin/api/revenue/refresh', 'AdminController@refreshRevenueData');
$router->get('/admin/api/revenue/export', 'AdminController@exportRevenueReport');

// İstatistikler
$router->get('/admin/istatistikler', 'AdminController@statistics');

// API endpoint'leri
$router->get('/api/latest-news', 'ApiController@latestNews');
$router->get('/api/sitemap', 'ApiController@sitemap');
// Ads API
$router->get('/api/ads/load-zone/{zone}', 'ApiController@loadAdZone');
$router->post('/api/ads/track-impression', 'ApiController@trackImpression');
$router->post('/api/ads/track-click', 'ApiController@trackClick');
// SEO: sitemap.xml ve robots.txt
$router->get('/sitemap.xml', 'ApiController@sitemap');
$router->get('/robots.txt', 'ApiController@robots');

// 404 sayfası
$router->notFound(function() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

    $isAdminApi = substr($uri, 0, 11) === '/admin/api/';
    $isPublicApi = substr($uri, 0, 5) === '/api/';
    $wantsJson = (strpos($accept, 'application/json') !== false) || (strtolower($xhr) === 'xmlhttprequest');

    if ($isAdminApi || $isPublicApi || $wantsJson) {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'Endpoint not found',
            'path' => $uri
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $controller = new ErrorController();
    $controller->notFound();
});

// Router'ı çalıştır
$router->run();
?>