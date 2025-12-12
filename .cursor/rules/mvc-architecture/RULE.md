---
description: "MVC mimarisi yapısı ve kuralları - Controller, Model, View organizasyonu"
alwaysApply: false
---

# MVC Architecture Rules

LooMix.Click projesi MVC (Model-View-Controller) mimarisini kullanır.

## Dizin Yapısı

```
app/
├── controllers/     # Controller sınıfları
│   ├── HomeController.php
│   ├── NewsController.php
│   └── AdminController.php
├── models/         # Model sınıfları
│   ├── News.php
│   ├── Category.php
│   └── Tag.php
├── core/           # Core sistem sınıfları
│   ├── Controller.php
│   ├── Model.php
│   ├── Router.php
│   ├── Database.php
│   └── View.php
└── helpers/        # Yardımcı sınıflar
    ├── AdManager.php
    └── SeoHelper.php

templates/          # View dosyaları
├── layouts/
│   ├── main.php
│   └── admin.php
├── home/
├── news/
└── admin/
```

## Controller Kuralları

### Controller Sınıfı Yapısı

```php
<?php
/**
 * News Controller
 * LooMix.Click
 */

class NewsController extends Controller {
    private $newsModel;
    
    public function __construct() {
        // Model'leri başlat
        $this->newsModel = new News();
    }
    
    /**
     * Haber detay sayfası
     */
    public function detail($slug) {
        $news = $this->newsModel->getBySlug($slug);
        
        if (!$news) {
            return $this->error404();
        }
        
        $this->view('news/detail', [
            'news' => $news,
            'relatedNews' => $this->newsModel->getRelatedNews($news['id'])
        ]);
    }
}
```

### Controller Sorumlulukları

- **HTTP isteklerini işle**: Request verilerini al ve validate et
- **Model'leri çağır**: İş mantığı için model metodlarını kullan
- **View'leri render et**: View'lere veri gönder
- **İş mantığı YAZMA**: İş mantığı model'lerde olmalı

```php
// DOĞRU - Controller sadece orkestra eder
public function create() {
    $data = $this->validateNewsData($_POST);
    $newsId = $this->newsModel->create($data);
    $this->redirect('/admin/news');
}

// YANLIŞ - İş mantığı controller'da olmamalı
public function create() {
    $slug = strtolower(str_replace(' ', '-', $_POST['title']));
    $stmt = $db->prepare("INSERT INTO news...");
    // ... database işlemleri
}
```

## Model Kuralları

### Model Sınıfı Yapısı

```php
<?php
/**
 * News Model
 * LooMix.Click
 */

class News extends Model {
    protected $table = 'news';
    
    /**
     * Yayındaki haberleri getir
     */
    public function getPublishedNews(int $limit = 10, int $offset = 0): array {
        $query = "SELECT n.*, c.name as category_name, c.slug as category_slug
                  FROM {$this->table} n
                  INNER JOIN categories c ON n.category_id = c.id
                  WHERE n.status = 'published'
                  AND n.publish_date <= NOW()
                  ORDER BY n.publish_date DESC
                  LIMIT :limit OFFSET :offset";
        
        return $this->db->fetchAll($query, [
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    /**
     * Haber oluştur
     */
    public function create(array $data): int {
        // Validation
        $this->validateNewsData($data);
        
        // Slug oluştur
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        
        // Insert
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Private helper - slug unique kontrolü
     */
    private function generateUniqueSlug(string $title): string {
        $slug = createSlug($title);
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
```

### Model Sorumlulukları

- **Veritabanı işlemleri**: CRUD operasyonları
- **İş mantığı**: Validation, hesaplamalar, veri manipülasyonu
- **Veri formatı**: Veritabanı ile uygulama arasında veri dönüşümü
- **View mantığı YAZMA**: Görünüm ile ilgili kod view'lerde olmalı

### Query Optimization

```php
// DOĞRU - Eager loading (N+1 problemi yok)
public function getNewsWithCategory(): array {
    return $this->db->fetchAll("
        SELECT n.*, c.name as category_name
        FROM news n
        INNER JOIN categories c ON n.category_id = c.id
    ");
}

// YANLIŞ - N+1 query problemi
public function getNewsWithCategory(): array {
    $news = $this->db->fetchAll("SELECT * FROM news");
    foreach ($news as &$item) {
        $item['category'] = $this->db->fetch(
            "SELECT * FROM categories WHERE id = ?", 
            [$item['category_id']]
        );
    }
    return $news;
}
```

## View Kuralları

### View Dosyası Yapısı

```php
<?php
/**
 * News Detail View
 * LooMix.Click
 */
?>

<article class="news-detail">
    <header class="news-header">
        <h1 class="news-title"><?= escape($news['title']) ?></h1>
        
        <div class="news-meta">
            <time datetime="<?= date('c', strtotime($news['publish_date'])) ?>">
                <?= formatDate($news['publish_date'], 'd F Y, l') ?>
            </time>
            <span class="category">
                <a href="<?= url('/kategori/' . $news['category_slug']) ?>">
                    <?= escape($news['category_name']) ?>
                </a>
            </span>
        </div>
    </header>
    
    <div class="news-content">
        <?= renderNewsContent($news['content']) ?>
    </div>
</article>
```

### View Sorumlulukları

- **HTML render et**: Veriyi göster
- **Helper fonksiyonları kullan**: `escape()`, `formatDate()`, `url()` gibi
- **Basit conditional'lar**: if/else, foreach
- **Karmaşık mantık YAZMA**: İş mantığı model'lerde olmalı

### View Helper Usage

```php
// DOĞRU - View'de sadece görüntüleme
<?php if (!empty($news['featured_image'])): ?>
    <img src="<?= getImageUrl($news['featured_image']) ?>" 
         alt="<?= escape($news['title']) ?>">
<?php else: ?>
    <img src="<?= asset('images/no-image.jpg') ?>" 
         alt="Görsel yok">
<?php endif; ?>

// YANLIŞ - View'de iş mantığı
<?php 
$imageUrl = empty($news['featured_image']) 
    ? '/assets/images/no-image.jpg' 
    : '/' . ltrim($news['featured_image'], '/');
?>
<img src="<?= $imageUrl ?>">
```

## Layout Sistemi

### Main Layout

`templates/layouts/main.php` - Public sayfalar için

```php
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= escape($pageTitle ?? SITE_NAME) ?></title>
    <!-- Meta tags, CSS -->
</head>
<body>
    <header>
        <!-- Site header -->
    </header>
    
    <main>
        <?php include $contentView; ?>
    </main>
    
    <footer>
        <!-- Site footer -->
    </footer>
</body>
</html>
```

### View Rendering

```php
// Controller'da
$this->view('news/detail', [
    'news' => $news,
    'relatedNews' => $relatedNews
]);

// Core/View.php içinde view() fonksiyonu
// Layout'u load eder ve content'i içine yerleştirir
```

## Best Practices

### 1. Fat Models, Skinny Controllers

```php
// DOĞRU
class NewsController extends Controller {
    public function create() {
        $newsId = $this->newsModel->create($_POST);
        $this->redirect('/admin/news');
    }
}

class News extends Model {
    public function create(array $data): int {
        // Validation
        // Slug generation
        // Image processing
        // Database insert
        // Cache clear
        return $newsId;
    }
}

// YANLIŞ - Controller'da çok fazla mantık
class NewsController extends Controller {
    public function create() {
        // Validation logic
        // Slug generation
        // Image processing
        // Database insert
        // Cache clear
        $this->redirect('/admin/news');
    }
}
```

### 2. Dependency Injection

```php
// DOĞRU
class NewsController extends Controller {
    private $newsModel;
    private $categoryModel;
    
    public function __construct() {
        $this->newsModel = new News();
        $this->categoryModel = new Category();
    }
}

// YANLIŞ - Her metodda yeni instance
public function index() {
    $newsModel = new News();
    $news = $newsModel->getAll();
}
```

### 3. Single Responsibility

Her controller, model ve view tek bir sorumluluğa odaklanmalı.

```php
// DOĞRU - Her controller kendi domain'i ile ilgilenir
HomeController - Ana sayfa
NewsController - Haber işlemleri
CategoryController - Kategori işlemleri
AdminController - Admin genel işlemler

// YANLIŞ - Tek bir controller her şeyi yapar
MainController - Her şey burada
```

