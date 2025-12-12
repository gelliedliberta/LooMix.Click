---
description: "SEO optimizasyonu, meta tags, structured data ve best practices"
alwaysApply: false
---

# SEO Optimization Rules

LooMix.Click projesi için SEO standartları ve en iyi uygulamalar.

## 📋 Meta Tags

### Title Tag

```php
<?php
// Dynamic title generation
$pageTitle = !empty($news['meta_title']) 
    ? $news['meta_title'] 
    : $news['title'] . META_TITLE_SUFFIX;

// Character limit: 50-60 characters
if (mb_strlen($pageTitle) > 60) {
    $pageTitle = mb_substr($pageTitle, 0, 57) . '...';
}
?>
<title><?= escape($pageTitle) ?></title>
```

### Meta Description

```php
<?php
// Dynamic meta description
$metaDescription = !empty($news['meta_description']) 
    ? $news['meta_description'] 
    : truncateText(strip_tags($news['summary']), 160);

// Clean for meta tag
$metaDescription = cleanMetaContent($metaDescription);
?>
<meta name="description" content="<?= $metaDescription ?>">
```

### Meta Keywords

```php
<?php
// Generate keywords from tags
$keywords = array_column($news['tags'], 'name');
$keywords[] = $news['category_name'];
$keywords[] = SITE_NAME;
$metaKeywords = implode(', ', $keywords);
?>
<meta name="keywords" content="<?= escape($metaKeywords) ?>">
```

### Canonical URL

```php
<?php
// Canonical URL - prevent duplicate content
$canonicalUrl = url('/haber/' . $news['slug']);
?>
<link rel="canonical" href="<?= $canonicalUrl ?>">
```

### Robots Meta

```php
<?php
// Control indexing
$robotsContent = ROBOTS_INDEX ? 'index, follow' : 'noindex, nofollow';

// Don't index admin pages
if (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) {
    $robotsContent = 'noindex, nofollow';
}
?>
<meta name="robots" content="<?= $robotsContent ?>">
```

## 📱 Open Graph Tags

```php
<?php
// Open Graph for social sharing
$ogTitle = $news['title'];
$ogDescription = truncateText(strip_tags($news['summary']), 200);
$ogImage = !empty($news['featured_image']) 
    ? url($news['featured_image']) 
    : url(DEFAULT_META_IMAGE);
$ogUrl = url('/haber/' . $news['slug']);
?>

<meta property="og:type" content="article">
<meta property="og:title" content="<?= escape($ogTitle) ?>">
<meta property="og:description" content="<?= cleanMetaContent($ogDescription) ?>">
<meta property="og:image" content="<?= $ogImage ?>">
<meta property="og:url" content="<?= $ogUrl ?>">
<meta property="og:site_name" content="<?= SITE_NAME ?>">
<meta property="og:locale" content="tr_TR">

<!-- Article specific -->
<meta property="article:published_time" content="<?= date('c', strtotime($news['publish_date'])) ?>">
<meta property="article:modified_time" content="<?= date('c', strtotime($news['updated_at'])) ?>">
<meta property="article:author" content="<?= escape($news['author_name']) ?>">
<meta property="article:section" content="<?= escape($news['category_name']) ?>">

<?php foreach ($news['tags'] as $tag): ?>
<meta property="article:tag" content="<?= escape($tag['name']) ?>">
<?php endforeach; ?>
```

## 🐦 Twitter Cards

```php
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= escape($ogTitle) ?>">
<meta name="twitter:description" content="<?= cleanMetaContent($ogDescription) ?>">
<meta name="twitter:image" content="<?= $ogImage ?>">
<meta name="twitter:site" content="@loomixclick">
<meta name="twitter:creator" content="@loomixclick">
```

## 🏗️ Structured Data (JSON-LD)

### NewsArticle Schema

```php
<?php
$structuredData = [
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => $news['title'],
    'description' => strip_tags($news['summary']),
    'image' => [
        '@type' => 'ImageObject',
        'url' => url($news['featured_image']),
        'width' => 1200,
        'height' => 630
    ],
    'datePublished' => date('c', strtotime($news['publish_date'])),
    'dateModified' => date('c', strtotime($news['updated_at'])),
    'author' => [
        '@type' => 'Person',
        'name' => $news['author_name']
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => SITE_NAME,
        'logo' => [
            '@type' => 'ImageObject',
            'url' => url(SITE_LOGO),
            'width' => 600,
            'height' => 60
        ]
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => url('/haber/' . $news['slug'])
    ],
    'articleSection' => $news['category_name'],
    'keywords' => implode(', ', array_column($news['tags'], 'name'))
];
?>

<script type="application/ld+json">
<?= json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
```

### BreadcrumbList Schema

```php
<?php
$breadcrumbData = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Ana Sayfa',
            'item' => url('/')
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $news['category_name'],
            'item' => url('/kategori/' . $news['category_slug'])
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $news['title'],
            'item' => url('/haber/' . $news['slug'])
        ]
    ]
];
?>

<script type="application/ld+json">
<?= json_encode($breadcrumbData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>
```

### WebSite Schema (Homepage)

```php
<?php
$websiteData = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => SITE_NAME,
    'url' => SITE_URL,
    'description' => SITE_DESCRIPTION,
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => [
            '@type' => 'EntryPoint',
            'urlTemplate' => url('/ara?q={search_term_string}')
        ],
        'query-input' => 'required name=search_term_string'
    ]
];
?>

<script type="application/ld+json">
<?= json_encode($websiteData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>
```

## 🔗 URL Structure

### SEO-Friendly URLs

```php
// DOĞRU - Clean, descriptive URLs
/haber/yapay-zeka-teknolojisi-2024-donusum
/kategori/teknoloji
/etiket/samsung
/ara?q=yapay+zeka

// YANLIŞ - Non-descriptive URLs
/news.php?id=123
/category.php?cat=tech
/tag.php?t=5
```

### Slug Generation

```php
// createSlug() function (includes/functions.php)
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

// Usage
$newsSlug = createSlug($newsTitle);
// "Yapay Zeka Teknolojisi 2024 Dönüşüm" -> "yapay-zeka-teknolojisi-2024-donusum"
```

### Unique Slug Check

```php
// Model'de slug uniqueness kontrolü
private function generateUniqueSlug(string $title, ?int $excludeId = null): string {
    $slug = createSlug($title);
    $originalSlug = $slug;
    $counter = 1;
    
    while ($this->slugExists($slug, $excludeId)) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}
```

## 🗺️ XML Sitemap

### Sitemap Generation

```php
public function generateSitemap() {
    header('Content-Type: application/xml; charset=utf-8');
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Homepage
    $xml .= '<url>';
    $xml .= '<loc>' . escape(SITE_URL) . '</loc>';
    $xml .= '<lastmod>' . date('c') . '</lastmod>';
    $xml .= '<changefreq>hourly</changefreq>';
    $xml .= '<priority>1.0</priority>';
    $xml .= '</url>';
    
    // Categories
    $categories = $this->categoryModel->getAllActive();
    foreach ($categories as $category) {
        $xml .= '<url>';
        $xml .= '<loc>' . escape(url('/kategori/' . $category['slug'])) . '</loc>';
        $xml .= '<lastmod>' . date('c', strtotime($category['updated_at'])) . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.9</priority>';
        $xml .= '</url>';
    }
    
    // News (published, last 1000)
    $news = $this->newsModel->getPublishedNews(1000);
    foreach ($news as $item) {
        $xml .= '<url>';
        $xml .= '<loc>' . escape(url('/haber/' . $item['slug'])) . '</loc>';
        $xml .= '<lastmod>' . date('c', strtotime($item['updated_at'])) . '</lastmod>';
        $xml .= '<changefreq>monthly</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';
    }
    
    // Tags
    $tags = $this->tagModel->getAllActive();
    foreach ($tags as $tag) {
        $xml .= '<url>';
        $xml .= '<loc>' . escape(url('/etiket/' . $tag['slug'])) . '</loc>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '<priority>0.7</priority>';
        $xml .= '</url>';
    }
    
    $xml .= '</urlset>';
    
    return $xml;
}
```

### Sitemap Index (for large sites)

```php
// sitemap-index.xml
public function generateSitemapIndex() {
    header('Content-Type: application/xml; charset=utf-8');
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Main sitemap
    $xml .= '<sitemap>';
    $xml .= '<loc>' . url('/sitemap.xml') . '</loc>';
    $xml .= '<lastmod>' . date('c') . '</lastmod>';
    $xml .= '</sitemap>';
    
    // News sitemap (monthly)
    $currentDate = new DateTime();
    for ($i = 0; $i < 12; $i++) {
        $month = $currentDate->format('Y-m');
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url("/sitemap-news-{$month}.xml") . '</loc>';
        $xml .= '<lastmod>' . $currentDate->format('c') . '</lastmod>';
        $xml .= '</sitemap>';
        
        $currentDate->modify('-1 month');
    }
    
    $xml .= '</sitemapindex>';
    
    return $xml;
}
```

### robots.txt

```txt
User-agent: *
Allow: /

# Sitemaps
Sitemap: https://loomix.click/sitemap.xml

# Disallow
Disallow: /admin/
Disallow: /assets/uploads/
Disallow: /config/
Disallow: /includes/

# Crawl delay
Crawl-delay: 1
```

## 🚀 Performance for SEO

### Core Web Vitals

```javascript
// Track Core Web Vitals
import {getCLS, getFID, getFCP, getLCP, getTTFB} from 'web-vitals';

function sendToAnalytics({name, value, id}) {
    gtag('event', name, {
        event_category: 'Web Vitals',
        event_label: id,
        value: Math.round(value),
        non_interaction: true
    });
}

getCLS(sendToAnalytics);  // Cumulative Layout Shift
getFID(sendToAnalytics);  // First Input Delay
getFCP(sendToAnalytics);  // First Contentful Paint
getLCP(sendToAnalytics);  // Largest Contentful Paint
getTTFB(sendToAnalytics); // Time to First Byte
```

### Image Optimization

```html
<!-- Optimized images for SEO -->
<img src="news-image-800w.jpg"
     srcset="news-image-400w.jpg 400w,
             news-image-800w.jpg 800w,
             news-image-1200w.jpg 1200w"
     sizes="(max-width: 600px) 400px,
            (max-width: 1000px) 800px,
            1200px"
     alt="Yapay zeka teknolojisi 2024 yılında büyük dönüşüm yaratıyor"
     loading="lazy"
     width="800"
     height="450">
```

### Heading Hierarchy

```html
<!-- DOĞRU - Proper heading hierarchy -->
<h1>Ana Haber Başlığı</h1>
    <h2>Haber Alt Başlığı</h2>
        <h3>Bölüm Başlığı</h3>
        <h3>Başka Bölüm</h3>
    <h2>Başka Alt Başlık</h2>

<!-- YANLIŞ - Skip levels -->
<h1>Ana Başlık</h1>
    <h4>Alt başlık</h4> <!-- h2'yi atladık! -->
```

## 📊 Analytics Integration

### Google Analytics 4

```php
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    
    gtag('config', 'G-XXXXXXXXXX', {
        page_title: '<?= escape($pageTitle) ?>',
        page_location: '<?= url($_SERVER['REQUEST_URI']) ?>',
        <?php if (!empty($news)): ?>
        content_type: 'article',
        content_category: '<?= escape($news['category_name']) ?>',
        <?php endif; ?>
    });
</script>
```

### Custom Events

```javascript
// Track article reading
const trackArticleRead = (newsId, newsTitle) => {
    gtag('event', 'article_read', {
        event_category: 'Engagement',
        event_label: newsTitle,
        value: newsId
    });
};

// Track share
const trackShare = (platform, newsTitle) => {
    gtag('event', 'share', {
        method: platform,
        content_type: 'article',
        item_id: newsTitle
    });
};
```

## ✅ SEO Checklist

Her sayfa için kontrol et:

- [ ] Title tag unique ve descriptive (50-60 karakter)
- [ ] Meta description unique ve descriptive (150-160 karakter)
- [ ] Canonical URL belirtilmiş
- [ ] Open Graph tags eklenmiş
- [ ] Twitter Cards eklenmiş
- [ ] Structured Data (JSON-LD) eklenmiş
- [ ] H1 tag unique ve descriptive
- [ ] Heading hierarchy doğru (H1 > H2 > H3)
- [ ] Images'da alt text var
- [ ] Internal linking yapılmış
- [ ] URL SEO-friendly (slug-based)
- [ ] Mobile-friendly (responsive)
- [ ] Page speed optimize edilmiş
- [ ] HTTPS kullanılıyor
- [ ] Sitemap'e eklenmiş

## 🎯 SEO Helper Class (Optional)

```php
class SeoHelper {
    public static function generateMetaTags(array $data): array {
        return [
            'title' => self::generateTitle($data),
            'description' => self::generateDescription($data),
            'keywords' => self::generateKeywords($data),
            'canonical' => self::generateCanonical($data),
            'og' => self::generateOgTags($data),
            'twitter' => self::generateTwitterCards($data),
            'structured_data' => self::generateStructuredData($data)
        ];
    }
    
    private static function generateTitle(array $data): string {
        // Title generation logic
    }
    
    // ... other methods
}

// Usage
$seoData = SeoHelper::generateMetaTags([
    'type' => 'news',
    'data' => $news
]);
```

