<?php
/**
 * View Sınıfı - Template ve layout yönetimi
 * LooMix.Click
 */

class View {
    private $data = [];
    private $layoutData = [];
    
    /**
     * View verisi ayarla
     */
    public function set($key, $value = null) {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        
        return $this;
    }
    
    /**
     * Layout verisi ayarla
     */
    public function setLayout($key, $value = null) {
        if (is_array($key)) {
            $this->layoutData = array_merge($this->layoutData, $key);
        } else {
            $this->layoutData[$key] = $value;
        }
        
        return $this;
    }
    
    /**
     * View render et
     */
    public function render($viewName, $data = [], $layout = 'main') {
        // Geçilen data'yı mevcut data ile merge et
        if (!empty($data)) {
            $this->data = array_merge($this->data, $data);
        }
        
        // Global veriler ekle
        $this->addGlobalData();
        
        // View içeriğini render et
        $content = $this->renderView($viewName, $this->data);
        
        // Layout ile render et
        if ($layout) {
            $layoutData = array_merge($this->layoutData, [
                'content' => $content,
                'viewName' => $viewName
            ], $this->data);
            
            try {
                $finalOutput = $this->renderView("layouts/{$layout}", $layoutData);
                echo $finalOutput;
            } catch (Exception $e) {
                if (DEBUG_MODE) {
                    error_log("View::render() - LAYOUT ERROR: " . $e->getMessage());
                }
                // Layout hatası durumunda sadece content'i göster
                echo $content;
            }
        } else {
            echo $content;
        }
    }
    
    /**
     * Partial view render et
     */
    public function partial($viewName, $data = []) {
        $viewData = array_merge($this->data, $data);
        return $this->renderView($viewName, $viewData);
    }
    
    /**
     * View dosyasını render et
     */
    private function renderView($viewName, $data = []) {
        // viewName'i korumak için extract'tan önce path'i hesapla
        $viewPath = TEMPLATE_PATH . str_replace('/', DIRECTORY_SEPARATOR, $viewName) . '.php';
        
        if (!file_exists($viewPath)) {
            // Debug için detaylı hata mesajı
            $debugInfo = DEBUG_MODE ? " (Path: {$viewPath})" : "";
            throw new Exception("View bulunamadı: {$viewName}{$debugInfo}");
        }
        
        // Data'yı extract et (artık viewName güvenli)
        extract($data);
        
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
    
    /**
     * Global veriler ekle
     */
    private function addGlobalData() {
        // Site bilgileri
        $this->data['siteTitle'] = SITE_NAME;
        $this->data['siteDescription'] = SITE_DESCRIPTION;
        $this->data['siteUrl'] = SITE_URL;
        $this->data['siteLogo'] = SITE_LOGO;
        
        // Varsayılan meta bilgileri
        if (!isset($this->data['pageTitle'])) {
            $this->data['pageTitle'] = SITE_NAME;
        }
        
        if (!isset($this->data['metaDescription'])) {
            $this->data['metaDescription'] = SITE_DESCRIPTION;
        }
        
        if (!isset($this->data['metaKeywords'])) {
            $this->data['metaKeywords'] = SITE_KEYWORDS;
        }
        
        if (!isset($this->data['metaImage'])) {
            $this->data['metaImage'] = DEFAULT_META_IMAGE;
        }
        
        // Canonical URL
        if (!isset($this->data['canonicalUrl'])) {
            $this->data['canonicalUrl'] = $this->getCurrentUrl();
        }
        
        // Reklam ayarları
        $this->data['adsEnabled'] = ADS_ENABLED;
        $this->data['googleAdsenseId'] = GOOGLE_ADSENSE_ID;
        
        // Current URL
        $this->data['currentUrl'] = $this->getCurrentUrl();
        
        // Admin session kontrolü
        $this->addAdminData();
        
        // Navigation için kategorileri yükle
        $this->loadNavigationData();
    }
    
    /**
     * Admin verileri ekle
     */
    private function addAdminData() {
        // Session kontrolü ve admin kullanıcı verilerini ekle
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Admin session kontrolü
        if (isset($_SESSION[ADMIN_SESSION_NAME]) && $_SESSION[ADMIN_SESSION_NAME] === true) {
            $this->data['isAdmin'] = true;
            $this->data['currentUser'] = $_SESSION['admin_user'] ?? null;
        } else {
            $this->data['isAdmin'] = false;
            $this->data['currentUser'] = null;
        }
        
        // Breadcrumb için varsayılan değer
        if (!isset($this->data['breadcrumb'])) {
            $this->data['breadcrumb'] = [];
        }
    }
    
    /**
     * Navigation verileri yükle
     */
    private function loadNavigationData() {
        try {
            $db = Database::getInstance();
            
            // Ana kategoriler
            $categories = $db->fetchAll("
                SELECT id, name, slug, icon, color 
                FROM categories 
                WHERE parent_id IS NULL AND is_active = 1 
                ORDER BY sort_order ASC
            ");
            
            $this->data['mainCategories'] = $categories;
            
            // Son dakika haberleri
            $breakingNews = $db->fetchAll("
                SELECT id, title, slug 
                FROM news 
                WHERE is_breaking = 1 AND status = 'published' 
                ORDER BY publish_date DESC 
                LIMIT 5
            ");
            
            $this->data['breakingNews'] = $breakingNews;
            
        } catch (Exception $e) {
            // Hata durumunda boş arrays
            $this->data['mainCategories'] = [];
            $this->data['breakingNews'] = [];
        }
    }
    
    /**
     * Mevcut URL'yi al
     */
    private function getCurrentUrl() {
        // CLI ortamında veya eksik server değişkenleri durumunda varsayılan URL döndür
        if (!isset($_SERVER['HTTP_HOST']) || !isset($_SERVER['REQUEST_URI'])) {
            return SITE_URL;
        }
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * JSON response
     */
    public static function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
    
    /**
     * XML response (Sitemap için)
     */
    public static function xml($content, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/xml; charset=utf-8');
        echo $content;
        exit();
    }
    
    /**
     * Template helper fonksiyonu
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Asset URL helper
     */
    public static function asset($path) {
        return asset($path);
    }
    
    /**
     * URL helper
     */
    public static function url($path = '') {
        return url($path);
    }
    
    /**
     * Date format helper
     */
    public static function date($date, $format = 'd.m.Y H:i') {
        return formatDate($date, $format);
    }
    
    /**
     * Text truncate helper
     */
    public static function truncate($text, $length = 150) {
        return truncateText($text, $length);
    }
}
?>
