<?php
/**
 * Ortak Fonksiyonlar
 * LooMix.Click
 */

/**
 * Güvenli output için HTML encode
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * URL temizle ve SEO dostu slug oluştur
 */
function createSlug($text) {
    $text = trim($text);
    $text = mb_strtolower($text, 'UTF-8');
    
    // Türkçe karakterleri değiştir
    $search = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü'];
    $replace = ['c', 'g', 'i', 'o', 's', 'u'];
    $text = str_replace($search, $replace, $text);
    
    // Özel karakterleri kaldır ve tire ile değiştir
    $text = preg_replace('/[^a-z0-9\s]/', '', $text);
    $text = preg_replace('/\s+/', '-', $text);
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Tarih formatla
 */
function formatDate($date, $format = 'd.m.Y H:i') {
    $ts = is_numeric($date) ? (int)$date : strtotime($date);
    $out = date($format, $ts);
    // Ay ve gün adlarını TR'ye çevir (numeric formatlar etkilenmez)
    $en = ['January','February','March','April','May','June','July','August','September','October','November','December',
        'Mon','Tue','Wed','Thu','Fri','Sat','Sun',
        'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $tr = ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık',
        'Pzt','Sal','Çar','Per','Cum','Cmt','Paz',
        'Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'];
    return str_replace($en, $tr, $out);
}

/**
 * Türkçe tarih formatı (Intl yoksa basit çeviriyle)
 */
function turkishDate($format = 'd F Y, l', $date = null) {
    $timestamp = $date ? (is_numeric($date) ? (int)$date : strtotime($date)) : time();
    $result = date($format, $timestamp);
    $en = ['January','February','March','April','May','June','July','August','September','October','November','December',
        'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $tr = ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık',
        'Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'];
    return str_replace($en, $tr, $result);
}

/**
 * Metni kısalt
 */
function truncateText($text, $length = 150) {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    $truncated = mb_substr($text, 0, $length);
    $lastSpace = mb_strrpos($truncated, ' ');
    
    if ($lastSpace !== false) {
        $truncated = mb_substr($truncated, 0, $lastSpace);
    }
    
    return $truncated . '...';
}

/**
 * Resim URL'si oluştur
 */
function getImageUrl($imagePath, $default = null) {
    if (empty($imagePath)) {
        return $default ?: 'assets/images/no-image.svg';
    }
    
    if (strpos($imagePath, 'http') === 0) {
        return $imagePath;
    }
    
    return '/' . ltrim($imagePath, '/');
}

/**
 * Meta tag'leri güvenli şekilde temizle
 */
function cleanMetaContent($content) {
    $content = strip_tags($content);
    $content = str_replace(['"', "'"], ['&quot;', '&#039;'], $content);
    return trim($content);
}

/**
 * Sayfa URL'si oluştur
 */
function url($path = '') {
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Asset URL'si oluştur
 */
function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Debug için dump fonksiyonu
 */
function dd($data) {
    if (DEBUG_MODE) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
}

/**
 * Okunma süresini hesapla (dakika)
 */
function calculateReadingTime($content) {
    $wordCount = str_word_count(strip_tags($content));
    $minutes = ceil($wordCount / 200); // Dakikada ortalama 200 kelime
    return $minutes;
}

/**
 * View içerme fonksiyonu
 */
function view($viewName, $data = []) {
    extract($data);
    $viewPath = "templates/{$viewName}.php";
    
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        throw new Exception("View bulunamadı: {$viewName}");
    }
}

/**
 * Reklam göster (AdManager entegrasyonu)
 */
function displayAd($zoneName, $attributes = []) {
    static $adManager = null;
    
    // AdManager instance'ını oluştur
    if ($adManager === null) {
        try {
            $adManager = new AdManager();
        } catch (Exception $e) {
            // AdManager yüklenemezse basit placeholder göster
            return getAdPlaceholder($zoneName);
        }
    }
    
    try {
        return $adManager->displayAd($zoneName, $attributes);
    } catch (Exception $e) {
        // Hata durumunda placeholder göster
        if (DEBUG_MODE) {
            return "<div class='ad-error bg-warning text-dark p-2 small rounded'>
                        AdManager Hatası: {$e->getMessage()} - Zone: {$zoneName}
                    </div>";
        }
        
        return getAdPlaceholder($zoneName);
    }
}

/**
 * Ad placeholder oluştur
 */
function getAdPlaceholder($zoneName) {
    $adSizes = [
        'header_banner' => ['width' => 728, 'height' => 90],
        'sidebar_square' => ['width' => 300, 'height' => 250],
        'content_inline' => ['width' => 728, 'height' => 90],
        'sidebar_skyscraper' => ['width' => 160, 'height' => 600],
        'mobile_banner' => ['width' => 320, 'height' => 50],
        'footer_banner' => ['width' => 728, 'height' => 90]
    ];
    
    $size = $adSizes[$zoneName] ?? ['width' => 300, 'height' => 250];
    $sizeText = "{$size['width']}x{$size['height']}";
    
    if (DEBUG_MODE) {
        return "<div class='ad-placeholder bg-light text-center p-3 border rounded' 
                     style='width: {$size['width']}px; height: {$size['height']}px; display: flex; align-items: center; justify-content: center;'>
                    <div>
                        <i class='fas fa-ad fa-2x text-muted mb-2'></i><br>
                        <small class='text-muted'><strong>{$zoneName}</strong><br>{$sizeText}</small>
                    </div>
                </div>";
    }
    
    return "<div class='ad-zone' data-zone='{$zoneName}' style='min-height: {$size['height']}px;'></div>";
}

/**
 * Lazy loading reklam göster
 */
function lazyAd($zoneName, $height = 250) {
    static $adManager = null;
    
    if ($adManager === null) {
        try {
            $adManager = new AdManager();
        } catch (Exception $e) {
            return getAdPlaceholder($zoneName);
        }
    }
    
    try {
        return $adManager->getLazyAdPlaceholder($zoneName, $height);
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            return "<div class='ad-error bg-warning text-dark p-2 small rounded'>
                        Lazy Ad Hatası: {$e->getMessage()}
                    </div>";
        }
        
        return "<div class='ad-zone ad-lazy-placeholder' data-zone='{$zoneName}' style='height: {$height}px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;'>
                    <div class='text-center text-muted'>
                        <div class='spinner-border spinner-border-sm' role='status'></div>
                        <div class='small mt-2'>Reklam yükleniyor...</div>
                    </div>
                </div>";
    }
}

/**
 * Ad blocker detection script
 */
function getAdBlockerDetectionScript() {
    return AdManager::getAdBlockerDetectionScript();
}
?>
