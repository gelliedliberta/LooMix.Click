<?php
/**
 * Ad Manager - Reklam yönetimi ve Google AdSense entegrasyonu
 * LooMix.Click
 */

class AdManager {
    private $db;
    private $adZones = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->loadAdZones();
    }
    
    /**
     * Reklam alanlarını veritabanından yükle
     */
    private function loadAdZones() {
        $zones = $this->db->fetchAll("
            SELECT * FROM ad_zones 
            WHERE is_active = 1 
            ORDER BY zone_name
        ");
        
        foreach ($zones as $zone) {
            $this->adZones[$zone['zone_name']] = $zone;
        }
    }
    
    /**
     * Reklam göster
     */
    public function displayAd($zoneName, $attributes = []) {
        if (!ADS_ENABLED) {
            return '';
        }
        
        if (!isset($this->adZones[$zoneName])) {
            return $this->getPlaceholderAd($zoneName);
        }
        
        $zone = $this->adZones[$zoneName];
        
        // Display rules kontrolü
        if (!$this->checkDisplayRules($zone)) {
            return '';
        }
        
        return $this->renderAd($zone, $attributes);
    }
    
    /**
     * Reklam render et
     */
    private function renderAd($zone, $attributes = []) {
        // Wrapper div - Sabit boyut yok, sadece content boyutuna göre
        $classes = ['ad-zone'];
        if (isset($attributes['class'])) {
            $classes[] = $attributes['class'];
        }
        
        // Sadece overflow kontrolü için style (reklam yüklendikten sonra dinamik boyut)
        $style = 'overflow: hidden;';
        
        $html = sprintf(
            '<div class="%s" data-zone="%s" data-ad-type="%s" style="%s">',
            implode(' ', $classes),
            $zone['zone_name'],
            $zone['ad_type'],
            $style
        );
        
        switch ($zone['ad_type']) {
            case 'adsense':
                $html .= $this->renderAdSense($zone);
                break;
            case 'custom':
                $html .= $this->renderCustomAd($zone);
                break;
            case 'banner':
                $html .= $this->renderBannerAd($zone);
                break;
            default:
                // Placeholder sadece DEBUG modunda göster
                if (DEBUG_MODE) {
                    $html .= $this->getPlaceholderAd($zone['zone_name']);
                } else {
                    // Production'da reklam yoksa hiç div oluşturma
                    return '';
                }
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Google AdSense reklam render et
     */
    private function renderAdSense($zone) {
        if (empty(GOOGLE_ADSENSE_ID)) {
            return '<!-- AdSense ID tanımlanmamış -->';
        }
        
        $slotId = $this->extractSlotId($zone['ad_code']);
        if (!$slotId) {
            $slotId = '1234567890'; // Varsayılan slot ID
        }
        
        $html = '';
        
        // AdSense script (yüklenmemişse yüklenir; async olduğu için tekrar eklemek sorun yaratmaz)
        $html .= sprintf(
            '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=%s" crossorigin="anonymous"></script>',
            GOOGLE_ADSENSE_ID
        );

        // Ad unit - Responsive veya dinamik boyut
        // Sabit boyut kullanmıyoruz, AdSense kendi boyutunu belirleyecek
        $insStyle = 'display:block';
        
        $html .= '<ins class="adsbygoogle"';
        $html .= sprintf(' style="%s"', $insStyle);
        $html .= sprintf(' data-ad-client="%s"', GOOGLE_ADSENSE_ID);
        $html .= sprintf(' data-ad-slot="%s"', $slotId);
        
        // Her zaman responsive yap (reklam yoksa yer kaplamaz)
        $html .= ' data-ad-format="auto"';
        $html .= ' data-full-width-responsive="true"';

        $html .= '></ins>';
        // Not: push işlemi client tarafında ad-detection.js initializeAds() ile yapılır
        
        return $html;
    }
    
    /**
     * Özel reklam kodu render et
     */
    private function renderCustomAd($zone) {
        return $zone['ad_code'];
    }
    
    /**
     * Banner reklam render et
     */
    private function renderBannerAd($zone) {
        // Banner reklamları için HTML oluştur
        $adCode = json_decode($zone['ad_code'], true);
        
        if (!$adCode || !isset($adCode['image_url'], $adCode['link_url'])) {
            return $this->getPlaceholderAd($zone['zone_name']);
        }
        
        $html = sprintf(
            '<a href="%s" target="_blank" rel="nofollow sponsored">',
            htmlspecialchars($adCode['link_url'])
        );
        
        $html .= sprintf(
            '<img src="%s" alt="%s" class="img-fluid" style="max-width: 100%%; height: auto;">',
            htmlspecialchars($adCode['image_url']),
            htmlspecialchars($adCode['alt_text'] ?? 'Reklam')
        );
        
        $html .= '</a>';
        
        return $html;
    }
    
    /**
     * Placeholder reklam (sadece DEBUG modunda)
     */
    private function getPlaceholderAd($zoneName) {
        if (DEBUG_MODE) {
            return sprintf(
                '<div class="ad-placeholder bg-light border p-2 text-center text-muted" style="min-height: 50px; display: flex; align-items: center; justify-content: center;">
                    <small><i class="fas fa-ad me-2"></i>Reklam Alanı: %s</small>
                </div>',
                $zoneName
            );
        }
        
        // Production'da reklam yoksa hiçbir şey gösterme
        return '';
    }
    
    /**
     * Display rules kontrolü
     */
    private function checkDisplayRules($zone) {
        if (empty($zone['display_rules'])) {
            return true;
        }
        
        $rules = json_decode($zone['display_rules'], true);
        if (!$rules) {
            return true;
        }
        
        // Sayfa türü kontrolü
        if (isset($rules['pages'])) {
            $currentPage = $this->getCurrentPageType();
            if (!in_array($currentPage, $rules['pages']) && !in_array('all', $rules['pages'])) {
                return false;
            }
        }
        
        // Kategori kontrolü
        if (isset($rules['categories'])) {
            $currentCategory = $this->getCurrentCategory();
            if ($currentCategory && !in_array($currentCategory, $rules['categories'])) {
                return false;
            }
        }
        
        // Mobil/Masaüstü kontrolü
        if (isset($rules['devices'])) {
            $isMobile = $this->isMobile();
            if ($isMobile && !in_array('mobile', $rules['devices'])) {
                return false;
            }
            if (!$isMobile && !in_array('desktop', $rules['devices'])) {
                return false;
            }
        }
        
        // Zaman kontrolü
        if (isset($rules['schedule'])) {
            if (!$this->checkSchedule($rules['schedule'])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Mevcut sayfa türünü belirle
     */
    private function getCurrentPageType() {
        $uri = $_SERVER['REQUEST_URI'];
        
        if ($uri === '/' || $uri === '') {
            return 'home';
        } elseif (strpos($uri, '/haber/') === 0) {
            return 'news';
        } elseif (strpos($uri, '/kategori/') === 0) {
            return 'category';
        } elseif (strpos($uri, '/etiket/') === 0) {
            return 'tag';
        } elseif (strpos($uri, '/arama') === 0) {
            return 'search';
        }
        
        return 'other';
    }
    
    /**
     * Mevcut kategoriyi al
     */
    private function getCurrentCategory() {
        $uri = $_SERVER['REQUEST_URI'];
        
        if (strpos($uri, '/kategori/') === 0) {
            $parts = explode('/', trim($uri, '/'));
            return $parts[1] ?? null;
        }
        
        // Haber sayfasından kategori çıkar
        if (strpos($uri, '/haber/') === 0) {
            $parts = explode('/', trim($uri, '/'));
            $slug = $parts[1] ?? null;
            
            if ($slug) {
                $news = $this->db->fetch("
                    SELECT c.slug FROM news n 
                    INNER JOIN categories c ON n.category_id = c.id 
                    WHERE n.slug = :slug
                ", ['slug' => $slug]);
                
                return $news['slug'] ?? null;
            }
        }
        
        return null;
    }
    
    /**
     * Mobil cihaz kontrolü
     */
    private function isMobile() {
        return isset($_SERVER['HTTP_USER_AGENT']) && 
               preg_match('/Mobile|Android|iPhone|iPad/', $_SERVER['HTTP_USER_AGENT']);
    }
    
    /**
     * Zaman planlaması kontrolü
     */
    private function checkSchedule($schedule) {
        $currentHour = (int)date('H');
        $currentDay = (int)date('w'); // 0 = Sunday
        
        // Saat kontrolü
        if (isset($schedule['hours'])) {
            if (!in_array($currentHour, $schedule['hours'])) {
                return false;
            }
        }
        
        // Gün kontrolü
        if (isset($schedule['days'])) {
            if (!in_array($currentDay, $schedule['days'])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * AdSense slot ID'sini çıkar
     */
    private function extractSlotId($adCode) {
        if (preg_match('/data-ad-slot=["\']([^"\']+)["\']/', $adCode, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Reklam performans istatistikleri
     */
    public function getAdStats($zoneName, $days = 30) {
        // Bu fonksiyon gelecekte reklam tıklamaları ve gösterimleri
        // takip etmek için kullanılabilir
        
        return [
            'impressions' => 0,
            'clicks' => 0,
            'ctr' => 0.0,
            'revenue' => 0.0
        ];
    }
    
    /**
     * A/B test desteği
     */
    public function getAdVariant($zoneName) {
        // A/B test için farklı reklam varyantları döndür
        $variants = $this->db->fetchAll("
            SELECT * FROM ad_zones 
            WHERE zone_name LIKE :zone_pattern AND is_active = 1
        ", ['zone_pattern' => $zoneName . '%']);
        
        if (count($variants) > 1) {
            // Random varyant seç
            $randomIndex = array_rand($variants);
            return $variants[$randomIndex];
        }
        
        return $this->adZones[$zoneName] ?? null;
    }
    
    /**
     * Ad blocker tespit etme JavaScript'i
     */
    public static function getAdBlockerDetectionScript() {
        return '
        <script>
        (function() {
            var adBlockDetected = false;
            var testAd = document.createElement("div");
            testAd.innerHTML = "&nbsp;";
            testAd.className = "adsbox";
            testAd.style.position = "absolute";
            testAd.style.left = "-10000px";
            document.body.appendChild(testAd);
            
            setTimeout(function() {
                if (testAd.offsetHeight === 0) {
                    adBlockDetected = true;
                    
                    // Analytics\'e gönder
                    if (typeof gtag !== "undefined") {
                        gtag("event", "ad_blocker_detected", {
                            event_category: "Ad Blocker",
                            non_interaction: true
                        });
                    }
                    
                    // Alternatif içerik göster
                    document.querySelectorAll(".ad-zone").forEach(function(zone) {
                        if (zone.children.length === 0) {
                            zone.innerHTML = "<div class=\'ad-blocker-message bg-light p-3 text-center rounded\'>" +
                                "<p class=\'mb-0 small text-muted\'>Bu içeriği desteklemek için reklam engelleyicinizi kapatabilirsiniz.</p>" +
                                "</div>";
                        }
                    });
                }
                document.body.removeChild(testAd);
            }, 100);
        })();
        </script>';
    }
    
    /**
     * Lazy loading için reklam placeholder'ı
     * Artık sabit boyut kullanmıyoruz - reklam yüklendikçe yer açılacak
     */
    public function getLazyAdPlaceholder($zoneName) {
        // Sadece loading indicator, sabit boyut yok
        return sprintf(
            '<div class="ad-lazy-placeholder text-center py-2" data-zone="%s">
                <div class="spinner-border spinner-border-sm text-muted" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            </div>',
            $zoneName
        );
    }
}
?>
