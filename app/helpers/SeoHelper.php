<?php
/**
 * SEO Helper - SEO optimizasyonu için yardımcı sınıf
 * LooMix.Click
 */

class SeoHelper {
    
    /**
     * Meta tag'leri render et
     */
    public static function renderMetaTags($data = []) {
        $title = $data['pageTitle'] ?? SITE_NAME;
        $description = $data['metaDescription'] ?? SITE_DESCRIPTION;
        $keywords = $data['metaKeywords'] ?? SITE_KEYWORDS;
        $image = $data['metaImage'] ?? DEFAULT_META_IMAGE;
        $url = $data['canonicalUrl'] ?? self::getCurrentUrl();
        $robots = isset($data['robotsIndex']) && $data['robotsIndex'] === false ? 'noindex, nofollow' : 'index, follow';
        
        $html = '';
        
        // Basic Meta Tags
        $html .= '<title>' . htmlspecialchars($title) . '</title>' . "\n";
        $html .= '<meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
        $html .= '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">' . "\n";
        $html .= '<meta name="robots" content="' . $robots . '">' . "\n";
        
        // Canonical URL
        $html .= '<link rel="canonical" href="' . htmlspecialchars($url) . '">' . "\n";
        
        // Open Graph Tags
        $html .= '<meta property="og:type" content="' . ($data['ogType'] ?? 'website') . '">' . "\n";
        $html .= '<meta property="og:title" content="' . htmlspecialchars($data['ogTitle'] ?? $title) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . htmlspecialchars($data['ogDescription'] ?? $description) . '">' . "\n";
        $html .= '<meta property="og:image" content="' . htmlspecialchars($image) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . htmlspecialchars($url) . '">' . "\n";
        $html .= '<meta property="og:site_name" content="' . htmlspecialchars(SITE_NAME) . '">' . "\n";
        
        // Twitter Card Tags
        $html .= '<meta name="twitter:card" content="' . ($data['twitterCard'] ?? 'summary_large_image') . '">' . "\n";
        $html .= '<meta name="twitter:title" content="' . htmlspecialchars($data['twitterTitle'] ?? $title) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . htmlspecialchars($data['twitterDescription'] ?? $description) . '">' . "\n";
        $html .= '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">' . "\n";
        
        // Article specific tags (haber detayı için)
        if (isset($data['isNewsDetail']) && $data['isNewsDetail']) {
            $publishTime = $data['publishTime'] ?? date('c');
            $modifiedTime = $data['modifiedTime'] ?? date('c');
            $author = $data['author'] ?? 'LooMix Editör';
            $section = $data['section'] ?? '';
            $tags = $data['articleTags'] ?? [];
            
            $html .= '<meta property="article:published_time" content="' . $publishTime . '">' . "\n";
            $html .= '<meta property="article:modified_time" content="' . $modifiedTime . '">' . "\n";
            $html .= '<meta property="article:author" content="' . htmlspecialchars($author) . '">' . "\n";
            
            if (!empty($section)) {
                $html .= '<meta property="article:section" content="' . htmlspecialchars($section) . '">' . "\n";
            }
            
            foreach ($tags as $tag) {
                $html .= '<meta property="article:tag" content="' . htmlspecialchars($tag) . '">' . "\n";
            }
        }
        
        // Pagination tags
        if (isset($data['pagination'])) {
            $pagination = $data['pagination'];
            $currentPage = $pagination['current_page'];
            $totalPages = $pagination['total_pages'];
            $baseUrl = $data['baseUrl'] ?? $url;
            
            if ($currentPage > 1) {
                $prevUrl = $currentPage == 2 ? $baseUrl : $baseUrl . '?page=' . ($currentPage - 1);
                $html .= '<link rel="prev" href="' . htmlspecialchars($prevUrl) . '">' . "\n";
            }
            
            if ($currentPage < $totalPages) {
                $nextUrl = $baseUrl . '?page=' . ($currentPage + 1);
                $html .= '<link rel="next" href="' . htmlspecialchars($nextUrl) . '">' . "\n";
            }
        }
        
        return $html;
    }
    
    /**
     * JSON-LD Structured Data render et
     */
    public static function renderStructuredData($data) {
        if (empty($data)) {
            return '';
        }
        
        $json = is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        return '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
    }
    
    /**
     * Breadcrumb JSON-LD oluştur
     */
    public static function generateBreadcrumbStructuredData($breadcrumbs) {
        if (empty($breadcrumbs)) {
            return null;
        }
        
        $items = [];
        foreach ($breadcrumbs as $index => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'] ?? null
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];
    }
    
    /**
     * AMP link tag'i oluştur
     */
    public static function renderAmpLink($slug) {
        return '<link rel="amphtml" href="' . url('/amp/haber/' . $slug) . '">' . "\n";
    }
    
    /**
     * RSS link tag'leri
     */
    public static function renderRssLinks($categorySlug = null) {
        $html = '<link rel="alternate" type="application/rss+xml" title="' . htmlspecialchars(SITE_NAME) . ' RSS" href="' . url('/rss') . '">' . "\n";
        
        if ($categorySlug) {
            $html .= '<link rel="alternate" type="application/rss+xml" title="' . htmlspecialchars(SITE_NAME . ' - ' . $categorySlug) . '" href="' . url('/kategori/' . $categorySlug . '/rss') . '">' . "\n";
        }
        
        return $html;
    }
    
    /**
     * Hreflang tag'leri (çoklu dil desteği için)
     */
    public static function renderHreflangTags($alternateUrls = []) {
        $html = '';
        
        // Varsayılan dil
        $html .= '<link rel="alternate" hreflang="tr" href="' . self::getCurrentUrl() . '">' . "\n";
        $html .= '<link rel="alternate" hreflang="x-default" href="' . self::getCurrentUrl() . '">' . "\n";
        
        // Alternatif diller
        foreach ($alternateUrls as $lang => $url) {
            $html .= '<link rel="alternate" hreflang="' . $lang . '" href="' . htmlspecialchars($url) . '">' . "\n";
        }
        
        return $html;
    }
    
    /**
     * Meta viewport ve mobil tag'leri
     */
    public static function renderMobileMetaTags() {
        $html = '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
        $html .= '<meta name="format-detection" content="telephone=no">' . "\n";
        $html .= '<meta name="mobile-web-app-capable" content="yes">' . "\n";
        $html .= '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
        $html .= '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
        
        return $html;
    }
    
    /**
     * DNS prefetch ve preconnect tag'leri
     */
    public static function renderResourceHints() {
        $html = '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
        $html .= '<link rel="dns-prefetch" href="//www.google-analytics.com">' . "\n";
        $html .= '<link rel="dns-prefetch" href="//pagead2.googlesyndication.com">' . "\n";
        $html .= '<link rel="preconnect" href="//fonts.gstatic.com" crossorigin>' . "\n";
        
        return $html;
    }
    
    /**
     * Favicon tag'leri
     */
    public static function renderFaviconTags() {
        $html = '<link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">' . "\n";
        $html .= '<link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">' . "\n";
        $html .= '<link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">' . "\n";
        $html .= '<link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">' . "\n";
        $html .= '<link rel="manifest" href="/site.webmanifest">' . "\n";
        
        return $html;
    }
    
    /**
     * Schema.org WebSite search action
     */
    public static function generateWebsiteStructuredData() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => SITE_NAME,
            'description' => SITE_DESCRIPTION,
            'url' => SITE_URL,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/arama?q={search_term_string}')
                ],
                'query-input' => 'required name=search_term_string'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => SITE_NAME,
                'url' => SITE_URL,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url(SITE_LOGO)
                ]
            ]
        ];
    }
    
    /**
     * Mevcut URL'yi al
     */
    private static function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * URL slug'ını temizle ve optimize et
     */
    public static function optimizeSlug($slug) {
        // Türkçe karakterleri değiştir
        $slug = str_replace(
            ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'],
            ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'o', 's', 'u'],
            $slug
        );
        
        // Küçük harfe çevir
        $slug = strtolower($slug);
        
        // Özel karakterleri kaldır
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Boşlukları tire ile değiştir
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        // Başındaki ve sonundaki tireleri kaldır
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Meta açıklama optimize et
     */
    public static function optimizeMetaDescription($text, $maxLength = 160) {
        // HTML tag'leri kaldır
        $text = strip_tags($text);
        
        // Fazla boşlukları temizle
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Uzunluk kontrolü
        if (strlen($text) <= $maxLength) {
            return trim($text);
        }
        
        // Kelime sınırında kes
        $truncated = substr($text, 0, $maxLength);
        $lastSpace = strrpos($truncated, ' ');
        
        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }
        
        return trim($truncated);
    }
}
?>
