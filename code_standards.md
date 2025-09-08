# LooMix.Click - Kod Standartları

Bu döküman, LooMix.Click haber sitesi projesi için geliştiricilerin uyması gereken kod standartlarını ve en iyi uygulamaları içerir.

## İçindekiler

1. [Genel Prensipler](#genel-prensipler)
2. [PHP Kod Standartları](#php-kod-standartları)
3. [JavaScript Standartları](#javascript-standartları)
4. [CSS/SCSS Standartları](#cssscss-standartları)
5. [HTML Standartları](#html-standartları)
6. [Veritabanı Standartları](#veritabanı-standartları)
7. [Güvenlik Standartları](#güvenlik-standartları)
8. [Performans Standartları](#performans-standartları)
9. [SEO Standartları](#seo-standartları)
10. [Test Standartları](#test-standartları)

## Genel Prensipler

### SOLID Prensipleri
- **S**ingle Responsibility Principle: Her sınıf tek bir sorumluluğa sahip olmalı
- **O**pen/Closed Principle: Sınıflar genişletmeye açık, değişikliğe kapalı olmalı
- **L**iskov Substitution Principle: Alt sınıflar, üst sınıfların yerine geçebilmeli
- **I**nterface Segregation Principle: İstemciler kullanmadıkları arayüzlere bağımlı olmamalı
- **D**ependency Inversion Principle: Soyutlamalara bağımlı olunmalı, somut sınıflara değil

### DRY (Don't Repeat Yourself)
- Kod tekrarından kaçının
- Ortak fonksiyonları helper'larda toplayın
- Benzer mantıkları abstract sınıflarda birleştirin

### KISS (Keep It Simple, Stupid)
- Kod basit ve anlaşılır olmalı
- Karmaşık yapılardan kaçının
- Açık ve net variable/function isimleri kullanın

## PHP Kod Standartları

### Dosya Yapısı
```php
<?php
/**
 * Dosya açıklaması
 * LooMix.Click
 */

// Namespace kullan (gerekiyorsa)
namespace App\Models;

// Use statement'ları
use Database;
use Exception;

/**
 * Sınıf açıklaması
 */
class NewsModel extends Model {
    // Kod burada
}
```

### Naming Conventions

#### Sınıf İsimleri
- PascalCase kullanın: `NewsController`, `UserModel`
- Açıklayıcı isimler: `EmailService`, `PaymentProcessor`

#### Method İsimleri
- camelCase kullanın: `getUserData()`, `validateEmail()`
- Verb + noun yapısı: `createUser()`, `deletePost()`

#### Variable İsimleri
- camelCase kullanın: `$userId`, `$newsData`
- Açıklayıcı isimler: `$isValidEmail`, `$userCount`

#### Sabitler
- UPPER_SNAKE_CASE: `DB_HOST`, `MAX_FILE_SIZE`
- Descriptive names: `DEFAULT_META_IMAGE`

### Kod Formatı

#### Indentation
- 4 space kullanın (tab değil)
- Tutarlı girinti uygulayın

#### Çizgi Uzunluğu
- Maksimum 120 karakter
- Uzun satırları mantıklı yerlerde bölün

#### Parantezler
```php
// Doğru
if ($condition) {
    doSomething();
}

// Yanlış
if($condition){
    doSomething();
}
```

### Error Handling
```php
try {
    $result = $this->processData($data);
    return $result;
} catch (ValidationException $e) {
    $this->logError($e->getMessage());
    throw $e;
} catch (Exception $e) {
    $this->logError('Unexpected error: ' . $e->getMessage());
    throw new SystemException('System error occurred');
}
```

### Comments
```php
/**
 * Kullanıcı verilerini doğrular
 * 
 * @param array $data Doğrulanacak veri
 * @return bool Doğrulama sonucu
 * @throws ValidationException Geçersiz veri durumunda
 */
public function validateUserData(array $data): bool {
    // Implementasyon
}
```

## JavaScript Standartları

### ES6+ Syntax
- const/let kullanın, var kullanmayın
- Arrow functions tercih edin
- Template literals kullanın

```javascript
// Doğru
const getUserData = (userId) => {
    return fetch(`/api/users/${userId}`)
        .then(response => response.json());
};

// Yanlış
var getUserData = function(userId) {
    return fetch('/api/users/' + userId)
        .then(function(response) {
            return response.json();
        });
};
```

### Naming Conventions
- camelCase: `userName`, `isLoading`
- PascalCase for classes: `UserService`, `NewsManager`
- UPPER_SNAKE_CASE for constants: `API_BASE_URL`

### Event Handling
```javascript
// Doğru - Event delegation
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('news-share-btn')) {
        handleNewsShare(e.target);
    }
});

// Yanlış - Her elemana ayrı listener
document.querySelectorAll('.news-share-btn').forEach(btn => {
    btn.addEventListener('click', handleNewsShare);
});
```

### Async/Await
```javascript
// Doğru
const loadNews = async () => {
    try {
        const response = await fetch('/api/news');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error loading news:', error);
        throw error;
    }
};

// Promise chain kullanmayın (gerekli değilse)
```

## CSS/SCSS Standartları

### BEM Methodology
```css
/* Block */
.news-card { }

/* Element */
.news-card__title { }
.news-card__content { }
.news-card__meta { }

/* Modifier */
.news-card--featured { }
.news-card__title--large { }
```

### Naming Conventions
- Kebab-case: `news-card`, `user-profile`
- Semantic names: `primary-button`, `hero-section`

### Organization
```scss
// 1. Variables
$primary-color: #007bff;
$font-family-base: 'Inter', sans-serif;

// 2. Mixins
@mixin button-style($bg-color, $text-color) {
    background-color: $bg-color;
    color: $text-color;
    border: none;
    border-radius: 0.375rem;
}

// 3. Base styles
body {
    font-family: $font-family-base;
    line-height: 1.6;
}

// 4. Components
.news-card {
    @include button-style($primary-color, white);
    
    &__title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    &--featured {
        border: 2px solid $primary-color;
    }
}
```

### Responsive Design
```css
/* Mobile First */
.news-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    
    /* Tablet */
    @media (min-width: 768px) {
        grid-template-columns: repeat(2, 1fr);
    }
    
    /* Desktop */
    @media (min-width: 1024px) {
        grid-template-columns: repeat(3, 1fr);
    }
}
```

## HTML Standartları

### Semantic HTML
```html
<!-- Doğru -->
<article class="news-article">
    <header class="news-article__header">
        <h1 class="news-article__title">Başlık</h1>
        <time class="news-article__date" datetime="2024-01-01">1 Ocak 2024</time>
    </header>
    
    <main class="news-article__content">
        <p>İçerik...</p>
    </main>
    
    <footer class="news-article__footer">
        <div class="news-article__tags">
            <span class="tag">Teknoloji</span>
        </div>
    </footer>
</article>

<!-- Yanlış -->
<div class="news">
    <div class="title">Başlık</div>
    <div class="date">1 Ocak 2024</div>
    <div class="content">İçerik...</div>
</div>
```

### Accessibility
```html
<!-- Alt text -->
<img src="news-image.jpg" alt="Teknoloji fuarında sergilenen yeni ürünler">

<!-- Form labels -->
<label for="search-input">Haber ara:</label>
<input type="search" id="search-input" name="q" placeholder="Aranacak kelime">

<!-- ARIA attributes -->
<button aria-expanded="false" aria-controls="mobile-menu">
    Menüyü aç
</button>

<!-- Skip links -->
<a href="#main-content" class="skip-link">İçeriğe geç</a>
```

## Veritabanı Standartları

### Tablo İsimleri
- Çoğul isimler: `news`, `categories`, `users`
- Snake_case: `news_views`, `user_preferences`

### Kolon İsimleri
- Snake_case: `created_at`, `updated_at`, `is_active`
- Açıklayıcı isimler: `view_count`, `featured_image`

### Primary Keys
- `id` kolonu kullanın
- AUTO_INCREMENT, UNSIGNED INT

### Foreign Keys
- `table_id` formatı: `category_id`, `user_id`
- Referential integrity constraints

### Indexing
```sql
-- Arama için full-text index
ALTER TABLE news ADD FULLTEXT(title, summary, content);

-- Slug için unique index
CREATE UNIQUE INDEX idx_news_slug ON news(slug);

-- Composite index
CREATE INDEX idx_news_status_date ON news(status, publish_date);
```

### Query Optimization
```sql
-- Doğru - Index kullanımı
SELECT * FROM news 
WHERE status = 'published' 
AND publish_date <= NOW() 
ORDER BY publish_date DESC 
LIMIT 10;

-- Yanlış - Function kullanımı index'i bozar
SELECT * FROM news 
WHERE DATE(publish_date) = '2024-01-01';
```

## Güvenlik Standartları

### SQL Injection Prevention
```php
// Doğru - Prepared statements
$stmt = $this->db->prepare("SELECT * FROM news WHERE id = :id");
$stmt->execute(['id' => $newsId]);

// Yanlış - String concatenation
$query = "SELECT * FROM news WHERE id = " . $newsId;
```

### XSS Prevention
```php
// Doğru - HTML encoding
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// Template'de
<?= escape($news['title']) ?>

// Yanlış - Raw output
echo $userInput;
```

### CSRF Protection
```php
// Form'da token
<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

// Controller'da doğrulama
if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
    throw new SecurityException('CSRF token mismatch');
}
```

### Authentication
```php
// Password hashing
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Verification
if (password_verify($inputPassword, $hashedPassword)) {
    // Login successful
}

// Session security
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict'
]);
```

## Performans Standartları

### Database Query Optimization
```php
// Doğru - Eager loading
$news = $this->db->fetchAll("
    SELECT n.*, c.name as category_name, c.slug as category_slug 
    FROM news n 
    INNER JOIN categories c ON n.category_id = c.id 
    WHERE n.status = 'published' 
    LIMIT 10
");

// Yanlış - N+1 query problem
$news = $this->db->fetchAll("SELECT * FROM news WHERE status = 'published' LIMIT 10");
foreach ($news as &$item) {
    $item['category'] = $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$item['category_id']]);
}
```

### Image Optimization
```php
// WebP desteği
function getOptimizedImageUrl($imagePath) {
    $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $imagePath);
    
    if (file_exists(PUBLIC_PATH . $webpPath) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
        return $webpPath;
    }
    
    return $imagePath;
}
```

### Caching
```php
// Page caching
$cacheKey = 'news_homepage_' . md5($params);
$cached = $this->cache->get($cacheKey);

if (!$cached) {
    $data = $this->generateHomepageData();
    $this->cache->set($cacheKey, $data, 3600); // 1 hour
    return $data;
}

return $cached;
```

### JavaScript Performance
```javascript
// Debounce search input
const searchInput = document.getElementById('search');
const debouncedSearch = debounce((query) => {
    performSearch(query);
}, 300);

searchInput.addEventListener('input', (e) => {
    debouncedSearch(e.target.value);
});

// Lazy loading images
const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove('lazy');
            imageObserver.unobserve(img);
        }
    });
});
```

## SEO Standartları

### Meta Tags
```php
// Dynamic meta tags
$metaTitle = !empty($news['meta_title']) ? $news['meta_title'] : $news['title'] . META_TITLE_SUFFIX;
$metaDescription = !empty($news['meta_description']) ? $news['meta_description'] : truncateText(strip_tags($news['summary']), 160);

// Template'de
<title><?= escape($metaTitle) ?></title>
<meta name="description" content="<?= escape($metaDescription) ?>">
```

### URL Structure
```php
// SEO friendly URLs
/haber/yapay-zeka-teknolojisi-2024-donusum
/kategori/teknoloji
/etiket/samsung

// Canonical URLs
<link rel="canonical" href="<?= $canonicalUrl ?>">
```

### Structured Data
```php
// JSON-LD
$structuredData = [
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => $news['title'],
    'datePublished' => date('c', strtotime($news['publish_date'])),
    'author' => ['@type' => 'Person', 'name' => $news['author_name']],
    'publisher' => [
        '@type' => 'Organization',
        'name' => SITE_NAME,
        'logo' => ['@type' => 'ImageObject', 'url' => SITE_LOGO]
    ]
];
```

### XML Sitemap
```php
// Dynamic sitemap generation
public function generateSitemap() {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Homepage
    $xml .= '<url>';
    $xml .= '<loc>' . SITE_URL . '</loc>';
    $xml .= '<changefreq>hourly</changefreq>';
    $xml .= '<priority>1.0</priority>';
    $xml .= '</url>';
    
    // News pages
    $news = $this->getPublishedNews(1000);
    foreach ($news as $item) {
        $xml .= '<url>';
        $xml .= '<loc>' . url('/haber/' . $item['slug']) . '</loc>';
        $xml .= '<lastmod>' . date('c', strtotime($item['updated_at'])) . '</lastmod>';
        $xml .= '<changefreq>monthly</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';
    }
    
    $xml .= '</urlset>';
    return $xml;
}
```

## Test Standartları

### Unit Tests
```php
class NewsModelTest extends PHPUnit\Framework\TestCase {
    private $newsModel;
    
    public function setUp(): void {
        $this->newsModel = new News();
    }
    
    public function testGetPublishedNews() {
        $news = $this->newsModel->getPublishedNews(5);
        
        $this->assertIsArray($news);
        $this->assertLessThanOrEqual(5, count($news));
        
        foreach ($news as $item) {
            $this->assertEquals('published', $item['status']);
            $this->assertLessThanOrEqual(time(), strtotime($item['publish_date']));
        }
    }
}
```

### Integration Tests
```php
public function testNewsCreationWorkflow() {
    // Create category
    $categoryId = $this->categoryModel->create([
        'name' => 'Test Kategori',
        'slug' => 'test-kategori'
    ]);
    
    // Create news
    $newsId = $this->newsModel->create([
        'title' => 'Test Haberi',
        'slug' => 'test-haberi',
        'category_id' => $categoryId,
        'content' => 'Test içeriği',
        'status' => 'published'
    ]);
    
    // Verify news can be retrieved
    $news = $this->newsModel->getBySlug('test-haberi');
    $this->assertNotNull($news);
    $this->assertEquals('Test Haberi', $news['title']);
}
```

### JavaScript Tests
```javascript
describe('Search functionality', () => {
    test('should debounce search input', (done) => {
        const mockSearch = jest.fn();
        const debouncedSearch = debounce(mockSearch, 100);
        
        debouncedSearch('test');
        debouncedSearch('test query');
        debouncedSearch('test query final');
        
        setTimeout(() => {
            expect(mockSearch).toHaveBeenCalledTimes(1);
            expect(mockSearch).toHaveBeenCalledWith('test query final');
            done();
        }, 150);
    });
});
```

## Git Standartları

### Commit Messages
```
feat: Add user authentication system
fix: Fix SQL injection vulnerability in search
docs: Update API documentation
style: Fix code formatting in NewsController
refactor: Extract email service to separate class
test: Add unit tests for news model
perf: Optimize database queries in homepage
```

### Branch Naming
```
feature/user-authentication
bugfix/search-sql-injection
hotfix/critical-security-patch
release/v2.1.0
```

### Pull Request Template
```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Unit tests pass
- [ ] Integration tests pass
- [ ] Manual testing completed

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] Security considerations reviewed
```

---

## Sonuç

Bu standartlara uyarak:
- Kodun okunabilirliğini artırırız
- Hata sayısını azaltırız
- Performansı optimize ederiz
- Güvenliği sağlarız
- SEO uyumluluğunu koruruz
- Takım çalışmasını kolaylaştırırız

**Unutmayın**: Bu standartlar yaşayan bir döküman olup, proje gelişimiyle birlikte güncellenmelidir.
