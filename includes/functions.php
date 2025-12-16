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
 * Türkçe ay ve gün adlarına çevirir
 */
function formatDate($date, $format = 'd.m.Y H:i') {
    $ts = is_numeric($date) ? (int)$date : strtotime($date);
    $out = date($format, $ts);
    
    // Önce uzun isimleri, sonra kısa isimleri çevir (kısa isimler uzun isimlerin içinde olabileceği için)
    // Ay isimleri (uzun)
    $en = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $tr = ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
    $out = str_replace($en, $tr, $out);
    
    // Gün isimleri (uzun) - önce bunları çevir
    $en = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $tr = ['Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'];
    $out = str_replace($en, $tr, $out);
    
    // Gün isimleri (kısa) - sonra kısaları çevir
    $en = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    $tr = ['Pzt','Sal','Çar','Per','Cum','Cmt','Paz'];
    $out = str_replace($en, $tr, $out);
    
    return $out;
}

/**
 * Türkçe tarih formatı (Intl yoksa basit çeviriyle)
 * Tam Türkçe tarih formatı oluşturur
 */
function turkishDate($format = 'd F Y, l', $date = null) {
    $timestamp = $date ? (is_numeric($date) ? (int)$date : strtotime($date)) : time();
    $result = date($format, $timestamp);
    
    // Önce uzun isimleri, sonra kısa isimleri çevir (kısa isimler uzun isimlerin içinde olabileceği için)
    // Ay isimleri (uzun)
    $en = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $tr = ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
    $result = str_replace($en, $tr, $result);
    
    // Gün isimleri (uzun)
    $en = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $tr = ['Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'];
    $result = str_replace($en, $tr, $result);
    
    return $result;
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
        return $default ?: '/assets/images/no-image.jpg';
    }
    
    return ltrim($imagePath, '/');
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
 * Etiket adını temizle - özel karakterleri kaldır
 * Sadece harf, rakam ve boşluk bırak
 * 
 * @param string $tagName Temizlenecek etiket adı
 * @return string Temizlenmiş etiket adı
 */
function cleanTagName($tagName) {
    // Önce trim
    $tagName = trim($tagName);
    
    // Boşsa geri dön
    if (empty($tagName)) {
        return '';
    }
    
    // Türkçe karakterleri koru, özel karakterleri kaldır
    // İzin verilen: a-z, A-Z, 0-9, Türkçe karakterler (ç, ğ, ı, ö, ş, ü, Ç, Ğ, İ, Ö, Ş, Ü) ve boşluk
    $tagName = preg_replace('/[^a-zA-Z0-9çğıöşüÇĞİÖŞÜ\s]/', '', $tagName);
    
    // Çoklu boşlukları tek boşluğa çevir
    $tagName = preg_replace('/\s+/', ' ', $tagName);
    
    // Başındaki ve sonundaki boşlukları temizle
    $tagName = trim($tagName);
    
    // İlk harfi büyük yap (Türkçe karakter destekli)
    $tagName = mb_convert_case($tagName, MB_CASE_TITLE, 'UTF-8');
    
    return $tagName;
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
    if (!ADS_ENABLED) {
        return '';
    }
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
    if (!ADS_ENABLED) {
        return '';
    }
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

/**
 * Safely render imported rich HTML content inside news articles.
 * - Parses the fragment in an isolated wrapper so stray closing tags cannot break outer layout
 * - Removes dangerous/irrelevant tags (script, style, meta, link, head, body)
 * - Normalizes common problematic attributes (iframes width)
 */
function renderNewsContent($html) {
    if (!is_string($html) || $html === '') {
        return '';
    }

    // Prepare DOMDocument
    libxml_use_internal_errors(true);
    $doc = new DOMDocument('1.0', 'UTF-8');

    // Wrap content with a custom HTML tag so stray </div> cannot close it
    // Custom tags are safe in HTML5 parsers and in DOMDocument
    $wrapped = '<x-news-wrapper>' . $html . '</x-news-wrapper>';
    $loaded = $doc->loadHTML('<?xml encoding="utf-8" ?>' . $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    if (!$loaded) {
        // Fallback: return stripped but readable content
        return strip_tags($html, '<p><br><strong><em><b><i><u><ul><ol><li><blockquote><img><a><h1><h2><h3><h4><h5><h6><iframe><figure><figcaption>');
    }

    $xpath = new DOMXPath($doc);

    // Remove forbidden tags entirely
    $forbidden = ['script','style','link','meta','head','body'];
    foreach ($forbidden as $tag) {
        $nodes = $doc->getElementsByTagName($tag);
        // Collect first to avoid live list mutation issues
        $toRemove = [];
        foreach ($nodes as $n) { $toRemove[] = $n; }
        foreach ($toRemove as $n) { $n->parentNode && $n->parentNode->removeChild($n); }
    }

    // Normalize iframes (common in embeds)
    $iframes = $doc->getElementsByTagName('iframe');
    foreach ($iframes as $iframe) {
        $iframe->setAttribute('width', '100%');
        // Remove inline fixed widths that may cause overflow
        if ($iframe->hasAttribute('style')) {
            $style = $iframe->getAttribute('style');
            // Strip width declarations; keep others
            $style = preg_replace('/\bwidth\s*:\s*[^;]+;?/i', '', $style);
            $iframe->setAttribute('style', trim($style));
        }
    }

    // Remove dangerous inline styles on generic elements (position:fixed; huge negative margins)
    $styledNodes = $xpath->query('//*[@style]');
    foreach ($styledNodes as $node) {
        $style = $node->getAttribute('style');
        $blocked = ['position\s*:\s*fixed', 'margin-left\s*:\s*-', 'margin-right\s*:\s*-'];
        if (preg_match('/(' . implode('|', $blocked) . ')/i', $style)) {
            // Soft clean: remove the blocked declarations only
            $style = preg_replace('/\bposition\s*:\s*fixed\s*;?/i', '', $style);
            $style = preg_replace('/\bmargin-left\s*:\s*-\s*[^;]+;?/i', 'margin-left:0;', $style);
            $style = preg_replace('/\bmargin-right\s*:\s*-\s*[^;]+;?/i', 'margin-right:0;', $style);
            $node->setAttribute('style', trim($style));
        }
    }

    // Extract sanitized innerHTML of the wrapper
    $wrappers = $doc->getElementsByTagName('x-news-wrapper');
    if ($wrappers->length === 0) {
        return '';
    }
    $wrapper = $wrappers->item(0);
    $output = '';
    foreach ($wrapper->childNodes as $child) {
        $output .= $doc->saveHTML($child);
    }
    return $output;
}

/**
 * Sosyal medya butonlarını göster
 * 
 * @param string $position 'header' veya 'footer'
 * @param string $size 'small' (default), 'large', 'xlarge'
 * @param string $class Ek CSS class'ları
 * @return string HTML çıktısı
 */
function displaySocialLinks($position = 'header', $size = 'small', $class = '') {
    try {
        $socialModel = new SocialMedia();
        $links = $socialModel->getActive($position);
        
        if (empty($links)) {
            return '';
        }
        
        // Size class belirleme
        $sizeClass = '';
        switch ($size) {
            case 'large':
                $sizeClass = 'fa-2x';
                break;
            case 'xlarge':
                $sizeClass = 'fa-3x';
                break;
            default:
                $sizeClass = '';
        }
        
        $output = '<div class="social-links ' . escape($class) . '">';
        
        foreach ($links as $link) {
            // URL kontrolü
            if (empty($link['url']) || $link['url'] === '#') {
                continue; // URL yoksa gösterme
            }
            
            // Internal link mi (RSS gibi)
            $isInternal = (strpos($link['url'], 'http') !== 0 && strpos($link['url'], '//') !== 0);
            $finalUrl = $isInternal ? url($link['url']) : $link['url'];
            
            // Target belirleme
            $target = $isInternal ? '' : ' target="_blank" rel="noopener noreferrer"';
            
            // Color style
            $colorStyle = '';
            if (!empty($link['color'])) {
                $colorStyle = ' data-color="' . escape($link['color']) . '"';
            }
            
            // Link oluştur
            $linkClass = ($position === 'footer') ? 'text-light' : 'text-muted';
            $spacing = ($position === 'footer') ? 'me-3' : 'me-2';
            
            $output .= sprintf(
                '<a href="%s" class="%s %s" title="%s"%s%s><i class="%s %s"></i></a>',
                escape($finalUrl),
                $linkClass,
                $spacing,
                escape($link['name']),
                $target,
                $colorStyle,
                escape($link['icon']),
                $sizeClass
            );
        }
        
        $output .= '</div>';
        
        return $output;
        
    } catch (Exception $e) {
        // Hata durumunda boş string döndür, sessiz hata
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log('displaySocialLinks error: ' . $e->getMessage());
        }
        return '';
    }
}

/**
 * Tek bir sosyal medya linkini HTML olarak döndür
 * 
 * @param string $platform Platform adı (facebook, twitter, vb.)
 * @return string HTML çıktısı
 */
function getSocialLink($platform) {
    try {
        $socialModel = new SocialMedia();
        $link = $socialModel->getByPlatform($platform);
        
        if (!$link || !$link['is_active'] || empty($link['url'])) {
            return '';
        }
        
        $isInternal = (strpos($link['url'], 'http') !== 0 && strpos($link['url'], '//') !== 0);
        $finalUrl = $isInternal ? url($link['url']) : $link['url'];
        $target = $isInternal ? '' : ' target="_blank" rel="noopener noreferrer"';
        
        return sprintf(
            '<a href="%s" title="%s"%s><i class="%s"></i></a>',
            escape($finalUrl),
            escape($link['name']),
            $target,
            escape($link['icon'])
        );
        
    } catch (Exception $e) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log('getSocialLink error: ' . $e->getMessage());
        }
        return '';
    }
}

/**
 * Site ayarını getir
 * 
 * @param string $key Ayar anahtarı
 * @param mixed $default Varsayılan değer
 * @return mixed Ayar değeri veya varsayılan
 */
function getSetting($key, $default = null) {
    static $settings = null;
    
    // İlk çağrıda tüm ayarları yükle (performance için)
    if ($settings === null) {
        try {
            $db = Database::getInstance();
            $rows = $db->fetchAll("SELECT setting_key, setting_value FROM site_settings WHERE is_active = 1");
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log('getSetting error: ' . $e->getMessage());
            }
            $settings = [];
        }
    }
    
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * İletişim bilgilerini getir
 * 
 * @return array İletişim bilgileri
 */
function getContactInfo() {
    return [
        'email' => getSetting('contact_email', 'info@loomix.click'),
        'email_editor' => getSetting('contact_email_editor', 'editor@loomix.click'),
        'phone' => getSetting('contact_phone', ''),
        'address' => getSetting('contact_address', ''),
        'twitter_handle' => getSetting('contact_twitter_handle', '@LooMixClick'),
        'facebook_page' => getSetting('contact_facebook_page', 'LooMix.Click'),
        'instagram_handle' => getSetting('contact_instagram_handle', '@loomixclick'),
        'linkedin_page' => getSetting('contact_linkedin_page', 'LooMix Click')
    ];
}
?>
