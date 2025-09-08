<?php
/**
 * Ana Konfigürasyon Dosyası
 * LooMix.Click
 */

// Path Konfigürasyonu
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('TEMPLATE_PATH', ROOT_PATH . 'templates' . DIRECTORY_SEPARATOR);

// Site Bilgileri
define('SITE_NAME', 'LooMix.Click');
define('SITE_DESCRIPTION', 'En güncel haberler, teknoloji, spor, ekonomi ve daha fazlası');
define('SITE_KEYWORDS', 'haber, güncel, teknoloji, spor, ekonomi, magazin');
define('SITE_URL', 'http://localhost/LooMix.Click');
//define('SITE_URL', 'https://loomix.click');
define('SITE_LOGO', 'assets/images/logo.png');

// Veritabanı Ayarları
define('DB_HOST', '193.203.168.203');
define('DB_NAME', 'u920805771_loomix');
define('DB_USER', 'u920805771_loomix');
define('DB_PASS', '3Fx~cSQ=n/=P');
define('DB_CHARSET', 'utf8mb4');

// Güvenlik Ayarları
define('SECRET_KEY', 'your-secret-key-here');
define('ADMIN_SESSION_NAME', 'loomix_admin');

// SEO Ayarları
define('META_TITLE_SUFFIX', ' - LooMix.Click');
define('DEFAULT_META_IMAGE', 'assets/images/default-share.jpg');
define('ROBOTS_INDEX', true);

// Reklam Ayarları
define('GOOGLE_ADSENSE_ID', 'ca-pub-3967023544942784');
define('ADS_ENABLED', true);
// Ad Blocker davranışı: true ise tespit edildiğinde reklam yükleme ve izleme durur
define('ADBLOCK_STRICT_MODE', false);

// Sayfalama Ayarları
define('NEWS_PER_PAGE', 12);
define('ADMIN_NEWS_PER_PAGE', 20);

// Dosya Yükleme Ayarları
define('UPLOAD_PATH', ROOT_PATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Cache Ayarları
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 saat

// Debug Mode
define('DEBUG_MODE', true);
?>
