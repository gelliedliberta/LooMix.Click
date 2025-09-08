<?php
/**
 * Ana Konfigürasyon Dosyası
 * LooMix.Click
 */

// Güvenli .env yükleme sınıfı
class EnvLoader {
    private static $loaded = false;
    private static $envVars = [];
    
    /**
     * .env dosyasını güvenli şekilde yükler
     */
    public static function load($envPath = null) {
        if (self::$loaded) {
            return; // Bir kez yükle
        }
        
        $envPath = $envPath ?: dirname(__DIR__) . '/.env';
        
        if (!file_exists($envPath)) {
            // Production'da .env dosyası olmayabilir, system env kullan
            if (getenv('APP_ENV') === 'production') {
                self::$loaded = true;
                return;
            }
            throw new Exception('.env dosyası bulunamadı: ' . $envPath);
        }
        
        $content = file_get_contents($envPath);
        if ($content === false) {
            throw new Exception('.env dosyası okunamadı: ' . $envPath);
        }
        
        self::parseEnvContent($content);
        self::$loaded = true;
    }
    
    /**
     * Env içeriğini güvenli şekilde parse eder
     */
    private static function parseEnvContent($content) {
        $lines = preg_split('/\r\n|\r|\n/', $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Boş satır veya yorum satırı
            if (empty($line) || $line[0] === '#') {
                continue;
            }
            
            // = içermeyen satırları atla
            if (strpos($line, '=') === false) {
                continue;
            }
            
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Geçersiz key kontrolü
            if (empty($key) || !preg_match('/^[A-Z_][A-Z0-9_]*$/i', $key)) {
                continue;
            }
            
            // Değeri temizle
            $value = self::parseValue($value);
            
            // Kaydet
            self::$envVars[$key] = $value;
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    
    /**
     * Değeri güvenli şekilde parse eder
     */
    private static function parseValue($value) {
        // Tırnakları temizle
        if (strlen($value) >= 2) {
            if (($value[0] === '"' && $value[-1] === '"') || 
                ($value[0] === "'" && $value[-1] === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        
        // Escape karakterlerini işle
        $value = stripcslashes($value);
        
        // Boolean değerleri convert et
        $lower = strtolower($value);
        if ($lower === 'true') {
            return true;
        }
        if ($lower === 'false') {
            return false;
        }
        
        // Numeric değerleri convert et
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        }
        
        return $value;
    }
    
    /**
     * Env değişkenini döndürür
     */
    public static function get($key, $default = null) {
        // Önce cache'den kontrol et
        if (isset(self::$envVars[$key])) {
            return self::$envVars[$key];
        }
        
        // Sistem environment'tan kontrol et
        $systemValue = getenv($key);
        if ($systemValue !== false) {
            return $systemValue;
        }
        
        // $_ENV'dan kontrol et
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        return $default;
    }
}

// .env dosyasını yükle
try {
    EnvLoader::load();
} catch (Exception $e) {
    // Development'ta hata göster, production'da log'la
    if (php_sapi_name() !== 'cli') {
        error_log('EnvLoader Error: ' . $e->getMessage());
        if (getenv('APP_ENV') !== 'production') {
            die('Konfigürasyon hatası: ' . $e->getMessage());
        }
    }
}

// Kullanım kolaylığı için global fonksiyon
function env($key, $default = null) {
    return EnvLoader::get($key, $default);
}

// Boolean env helper
function env_bool($key, $default = false) {
    $value = EnvLoader::get($key, $default);
    if (is_bool($value)) {
        return $value;
    }
    return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
}

// Integer env helper  
function env_int($key, $default = 0) {
    return (int) EnvLoader::get($key, $default);
}

// Path Konfigürasyonu
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('TEMPLATE_PATH', ROOT_PATH . 'templates' . DIRECTORY_SEPARATOR);

// Site Bilgileri
define('SITE_NAME', 'LooMix.Click');
define('SITE_DESCRIPTION', 'En güncel haberler, teknoloji, spor, ekonomi ve daha fazlası');
define('SITE_KEYWORDS', 'haber, güncel, teknoloji, spor, ekonomi, magazin');
define('SITE_URL', env('SITE_URL', 'http://localhost/LooMix.Click'));
define('SITE_LOGO', 'assets/images/logo.png');

// Veritabanı Ayarları
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'loomix'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// Güvenlik Ayarları
define('SECRET_KEY', env('SECRET_KEY', 'your-secret-key-here'));
define('ADMIN_SESSION_NAME', env('ADMIN_SESSION_NAME', 'loomix_admin'));

// SEO Ayarları
define('META_TITLE_SUFFIX', ' - LooMix.Click');
define('DEFAULT_META_IMAGE', 'assets/images/default-share.jpg');
define('ROBOTS_INDEX', true);

// Reklam Ayarları
define('GOOGLE_ADSENSE_ID', env('GOOGLE_ADSENSE_ID', 'ca-pub-your-adsense-id'));
define('ADS_ENABLED', env_bool('ADS_ENABLED', true));
// Ad Blocker davranışı: true ise tespit edildiğinde reklam yükleme ve izleme durur
define('ADBLOCK_STRICT_MODE', env_bool('ADBLOCK_STRICT_MODE', false));

// Sayfalama Ayarları
define('NEWS_PER_PAGE', 12);
define('ADMIN_NEWS_PER_PAGE', 20);

// Dosya Yükleme Ayarları
define('UPLOAD_PATH', ROOT_PATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('MAX_FILE_SIZE', env_int('MAX_FILE_SIZE', 5 * 1024 * 1024)); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Cache Ayarları
define('CACHE_ENABLED', env_bool('CACHE_ENABLED', true));
define('CACHE_LIFETIME', env_int('CACHE_LIFETIME', 3600)); // 1 saat

// Debug Mode
define('DEBUG_MODE', env_bool('DEBUG_MODE', false));
?>
