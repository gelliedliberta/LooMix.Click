<?php
/**
 * API Controller - SEO ve API endpoint'leri
 * LooMix.Click
 */

class ApiController extends Controller {
    
    /**
     * XML Sitemap
     */
    public function sitemap() {
        $newsModel = new News();
        $categoryModel = new Category();
        $tagModel = new Tag();
        
        header('Content-Type: application/xml; charset=utf-8');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Ana sayfa
        $xml .= '<url>' . "\n";
        $xml .= '<loc>' . SITE_URL . '</loc>' . "\n";
        $xml .= '<changefreq>hourly</changefreq>' . "\n";
        $xml .= '<priority>1.0</priority>' . "\n";
        $xml .= '<lastmod>' . date('c') . '</lastmod>' . "\n";
        $xml .= '</url>' . "\n";
        
        // Haberler
        $news = $newsModel->getForSitemap(1000);
        foreach ($news as $item) {
            $xml .= '<url>' . "\n";
            $xml .= '<loc>' . url('/haber/' . $item['slug']) . '</loc>' . "\n";
            $xml .= '<changefreq>monthly</changefreq>' . "\n";
            $xml .= '<priority>0.8</priority>' . "\n";
            $xml .= '<lastmod>' . date('c', strtotime($item['updated_at'])) . '</lastmod>' . "\n";
            $xml .= '</url>' . "\n";
        }
        
        // Kategoriler
        $categories = $categoryModel->getForSitemap();
        foreach ($categories as $item) {
            $xml .= '<url>' . "\n";
            $xml .= '<loc>' . url('/kategori/' . $item['slug']) . '</loc>' . "\n";
            $xml .= '<changefreq>daily</changefreq>' . "\n";
            $xml .= '<priority>0.7</priority>' . "\n";
            $xml .= '<lastmod>' . date('c', strtotime($item['updated_at'])) . '</lastmod>' . "\n";
            $xml .= '</url>' . "\n";
        }
        
        // Etiketler
        $tags = $tagModel->getForSitemap();
        foreach ($tags as $item) {
            $xml .= '<url>' . "\n";
            $xml .= '<loc>' . url('/etiket/' . $item['slug']) . '</loc>' . "\n";
            $xml .= '<changefreq>weekly</changefreq>' . "\n";
            $xml .= '<priority>0.5</priority>' . "\n";
            $xml .= '<lastmod>' . date('c', strtotime($item['updated_at'])) . '</lastmod>' . "\n";
            $xml .= '</url>' . "\n";
        }
        
        // Sabit sayfalar
        $staticPages = [
            'hakkimizda' => ['changefreq' => 'monthly', 'priority' => '0.3'],
            'iletisim' => ['changefreq' => 'monthly', 'priority' => '0.3'],
            'gizlilik-politikasi' => ['changefreq' => 'yearly', 'priority' => '0.2']
        ];
        
        foreach ($staticPages as $page => $config) {
            $xml .= '<url>' . "\n";
            $xml .= '<loc>' . url('/' . $page) . '</loc>' . "\n";
            $xml .= '<changefreq>' . $config['changefreq'] . '</changefreq>' . "\n";
            $xml .= '<priority>' . $config['priority'] . '</priority>' . "\n";
            $xml .= '</url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        echo $xml;
        exit();
    }
    
    /**
     * Robots.txt
     */
    public function robots() {
        header('Content-Type: text/plain; charset=utf-8');
        
        $robots = "User-agent: *\n";
        
        if (ROBOTS_INDEX) {
            $robots .= "Allow: /\n";
        } else {
            $robots .= "Disallow: /\n";
        }
        
        // Admin paneli ve özel dizinleri engelle
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /includes/\n";
        $robots .= "Disallow: /app/\n";
        $robots .= "Disallow: /config/\n";
        $robots .= "Disallow: /database/\n";
        $robots .= "Disallow: /search\n";
        
        // Sitemap lokasyonu
        $robots .= "\nSitemap: " . url('/sitemap.xml') . "\n";
        
        echo $robots;
        exit();
    }
    
    /**
     * Son haberler JSON API
     */
    public function latestNews() {
        $limit = (int)$this->get('limit', 10);
        $categoryId = $this->get('category_id');
        
        if ($limit > 50) $limit = 50; // Maksimum limit
        
        $newsModel = new News();
        $news = $newsModel->getPublishedNews($limit, 0, $categoryId);
        
        // API için temizle
        $apiNews = [];
        foreach ($news as $item) {
            $apiNews[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'slug' => $item['slug'],
                'summary' => strip_tags($item['summary']),
                'featured_image' => getImageUrl($item['featured_image']),
                'category' => [
                    'name' => $item['category_name'],
                    'slug' => $item['category_slug']
                ],
                'publish_date' => $item['publish_date'],
                'url' => url('/haber/' . $item['slug'])
            ];
        }
        
        $this->json([
            'success' => true,
            'data' => $apiNews,
            'count' => count($apiNews)
        ]);
    }

    /**
     * Etiket arama (autocomplete)
     * GET /api/tags/search?q=term&limit=10
     */
    public function searchTags() {
        $query = trim((string)$this->get('q', ''));
        $limit = (int)$this->get('limit', 10);
        if ($limit > 50) { $limit = 50; }

        if ($query === '') {
            return $this->json(['success' => true, 'data' => []]);
        }

        $tagModel = new Tag();
        $results = $tagModel->searchByName($query, $limit);

        // 0 kullanımda gizle
        $results = array_values(array_filter($results, function($t) {
            return (int)($t['usage_count'] ?? 0) > 0;
        }));

        $payload = array_map(function($t) {
            return [
                'id' => (int)$t['id'],
                'name' => $t['name'],
                'slug' => $t['slug'],
                'color' => $t['color'] ?: '#6c757d',
                'usage_count' => (int)$t['usage_count'],
                'url' => url('/etiket/' . $t['slug'])
            ];
        }, $results);

        return $this->json(['success' => true, 'data' => $payload]);
    }
    
    /**
     * JSON-LD Structured Data Generator
     */
    public function structuredData($type = 'website') {
        $structuredData = [];
        
        switch ($type) {
            case 'website':
                $structuredData = [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'name' => SITE_NAME,
                    'description' => SITE_DESCRIPTION,
                    'url' => SITE_URL,
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => SITE_NAME,
                        'url' => SITE_URL
                    ],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/arama?q={search_term_string}'),
                        'query-input' => 'required name=search_term_string'
                    ]
                ];
                break;
                
            case 'organization':
                $structuredData = [
                    '@context' => 'https://schema.org',
                    '@type' => 'NewsMediaOrganization',
                    'name' => SITE_NAME,
                    'description' => SITE_DESCRIPTION,
                    'url' => SITE_URL,
                    'logo' => url(SITE_LOGO),
                    'sameAs' => [
                        // Sosyal medya hesapları buraya eklenebilir
                    ]
                ];
                break;
        }
        
        $this->json($structuredData);
    }
    
    /**
     * AMP Cache Temizleme
     */
    public function clearAmpCache() {
        $urls = $this->post('urls', []);
        
        if (empty($urls)) {
            $this->json(['error' => 'URL listesi boş'], 400);
            return;
        }
        
        // Google AMP Cache temizleme işlemi
        $results = [];
        foreach ($urls as $url) {
            $ampUrl = 'https://cdn.ampproject.org/update-cache/c/s/' . str_replace(['http://', 'https://'], '', $url);
            
            // cURL ile AMP cache temizleme isteği
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ampUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $results[$url] = [
                'success' => $httpCode === 200,
                'http_code' => $httpCode
            ];
        }
        
        $this->json([
            'success' => true,
            'results' => $results
        ]);
    }
    
    /**
     * Google Search Console Sitemap Ping
     */
    public function pingSitemap() {
        $sitemapUrl = url('/sitemap.xml');
        
        // Google'a sitemap bildirimi gönder
        $pingUrl = 'http://www.google.com/ping?sitemap=' . urlencode($sitemapUrl);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pingUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Bing'e de bildirim gönder
        $bingPingUrl = 'http://www.bing.com/ping?sitemap=' . urlencode($sitemapUrl);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $bingPingUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $bingResponse = curl_exec($ch);
        $bingHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->json([
            'success' => true,
            'google' => [
                'success' => $httpCode === 200,
                'http_code' => $httpCode
            ],
            'bing' => [
                'success' => $bingHttpCode === 200,
                'http_code' => $bingHttpCode
            ]
        ]);
    }
    
    /**
     * Reklam Alanı İçeriğini Yükle
     * GET /api/ads/load-zone/{zone}
     */
    public function loadAdZone($zoneName = null) {
        $zone = $zoneName ?? ($this->get('zone') ?: null);
        if (!$zone) {
            return $this->json(['success' => false, 'error' => 'Zone gerekli'], 400);
        }
        
        if (!ADS_ENABLED) {
            return $this->json(['success' => true, 'html' => '']);
        }
        try {
            $adManager = new AdManager();
            // Lazy placeholder yerine gerçek ad HTML'i dön
            $html = $adManager->displayAd($zone, ['class' => 'ad-loaded']);
            return $this->json(['success' => true, 'html' => $html]);
        } catch (Exception $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return $this->json(['success' => false], 500);
        }
    }
    
    /**
     * Reklam Gösterim Takibi
     * POST /api/ads/track-impression
     */
    public function trackImpression() {
        $payload = $this->post();
        $zone = isset($payload['zone']) ? $payload['zone'] : null;
        $url = isset($payload['url']) ? $payload['url'] : null;
        $timestamp = isset($payload['timestamp']) ? (int)$payload['timestamp'] : time() * 1000;
        
        if (!$zone) {
            return $this->json(['success' => false, 'error' => 'Zone gerekli'], 400);
        }
        
        // Şimdilik sadece kabul edip 200 döndür (gelecekte DB'ye yazılabilir)
        return $this->json(['success' => true, 'tracked' => [
            'type' => 'impression',
            'zone' => $zone,
            'url' => $url,
            'timestamp' => $timestamp
        ]]);
    }
    
    /**
     * Reklam Tıklama Takibi
     * POST /api/ads/track-click
     */
    public function trackClick() {
        $payload = $this->post();
        $zone = isset($payload['zone']) ? $payload['zone'] : null;
        $url = isset($payload['url']) ? $payload['url'] : null;
        $timestamp = isset($payload['timestamp']) ? (int)$payload['timestamp'] : time() * 1000;
        
        if (!$zone) {
            return $this->json(['success' => false, 'error' => 'Zone gerekli'], 400);
        }
        
        return $this->json(['success' => true, 'tracked' => [
            'type' => 'click',
            'zone' => $zone,
            'url' => $url,
            'timestamp' => $timestamp
        ]]);
    }
    
    /**
     * Meta tag'leri dinamik olarak getir
     */
    public function getMetaTags() {
        $url = $this->get('url');
        
        if (empty($url)) {
            $this->json(['error' => 'URL parametresi gerekli'], 400);
            return;
        }
        
        // URL'yi parse et ve sayfa tipini belirle
        $parsedUrl = parse_url($url);
        $path = trim($parsedUrl['path'], '/');
        $pathParts = explode('/', $path);
        
        $metaTags = [
            'title' => SITE_NAME,
            'description' => SITE_DESCRIPTION,
            'keywords' => SITE_KEYWORDS,
            'image' => DEFAULT_META_IMAGE,
            'url' => $url
        ];
        
        // Sayfa tipine göre meta bilgileri getir
        if (empty($path)) {
            // Ana sayfa
            $metaTags['title'] = SITE_NAME . ' - ' . SITE_DESCRIPTION;
        } elseif ($pathParts[0] === 'haber' && isset($pathParts[1])) {
            // Haber detayı
            $newsModel = new News();
            $news = $newsModel->getBySlug($pathParts[1]);
            
            if ($news) {
                $metaTags['title'] = $news['title'] . META_TITLE_SUFFIX;
                $metaTags['description'] = truncateText(strip_tags($news['summary']), 160);
                $metaTags['image'] = getImageUrl($news['featured_image']);
            }
        } elseif ($pathParts[0] === 'kategori' && isset($pathParts[1])) {
            // Kategori sayfası
            $categoryModel = new Category();
            $category = $categoryModel->getBySlug($pathParts[1]);
            
            if ($category) {
                $metaTags['title'] = $category['name'] . ' Haberleri' . META_TITLE_SUFFIX;
                $metaTags['description'] = $category['description'] ?: $category['name'] . ' kategorisinden en güncel haberler';
            }
        } elseif ($pathParts[0] === 'etiket' && isset($pathParts[1])) {
            // Etiket sayfası
            $tagModel = new Tag();
            $tag = $tagModel->getBySlug($pathParts[1]);
            if ($tag) {
                $metaTags['title'] = $tag['name'] . ' Etiketi Haberleri' . META_TITLE_SUFFIX;
                $metaTags['description'] = $tag['description'] ?: ($tag['name'] . ' etiketi ile ilgili en güncel haberler');
            }
        } elseif ($pathParts[0] === 'etiketler') {
            $metaTags['title'] = 'Tüm Etiketler' . META_TITLE_SUFFIX;
            $metaTags['description'] = 'Sitede kullanılan tüm etiketler ve içerikleri';
        }
        
        $this->json($metaTags);
    }
}
?>
