# LooMix.Click - AI Agent Instructions

Bu proje **LooMix.Click** haber sitesidir. PHP tabanlı, MVC mimarisinde geliştirilmiş modern bir web uygulamasıdır.

## 📋 Proje Hakkında

### Teknoloji Stack
- **Backend**: PHP 7.4+ (MVC Architecture)
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5.3.2, Vanilla JavaScript (ES6+)
- **Server**: Apache/Nginx
- **Ads**: Google AdSense Integration

### Proje Yapısı
```
app/
├── controllers/    # HTTP request handlers
├── models/         # Business logic & database
├── core/           # Framework core (Router, Database, etc.)
└── helpers/        # Utility classes

templates/          # Views (PHP templates)
├── layouts/        # Layout files
├── home/           # Public pages
├── news/           # News pages
└── admin/          # Admin panel

assets/             # Static files
├── css/            # Stylesheets
├── js/             # JavaScript
├── images/         # Images
└── uploads/        # User uploads

database/           # Database files
├── migrations/     # SQL migrations
└── *.md           # Database documentation
```

## 🎯 Geliştirme Kuralları

### 1. Kod Standartları
- **PHP**: PSR benzeri, camelCase metodlar, PascalCase sınıflar
- **JavaScript**: ES6+, camelCase, async/await
- **CSS**: BEM methodology, kebab-case, mobile-first
- **HTML**: Semantic HTML5, accessibility (ARIA)

### 2. Güvenlik (ÖNEMLİ)
- **SQL**: Her zaman prepared statements kullan
- **XSS**: Her output'ta `escape()` fonksiyonu kullan
- **CSRF**: Form'larda token kontrolü yap
- **File Upload**: Type ve size validation yap

### 3. MVC Mimarisi
- **Controller**: HTTP request handler, thin controllers
- **Model**: Business logic, database operations, fat models
- **View**: Sadece presentation, helper fonksiyonlar kullan

### 4. Veritabanı
- **Tablo/kolon**: snake_case (news, category_id, created_at)
- **Index**: Sık kullanılan query'ler için index ekle
- **Migration**: Her değişiklik için migration dosyası oluştur
- **Documentation**: `database/*.md` dosyalarını güncelle

### 5. SEO
- Meta tags her sayfada unique olmalı
- URL'ler SEO-friendly (slug-based)
- Structured Data (JSON-LD) ekle
- Image alt text zorunlu

## 🔧 Yardımcı Fonksiyonlar

Projede tanımlı fonksiyonları kullan (`includes/functions.php`):

```php
escape($string)                    // HTML encode (XSS koruması)
createSlug($text)                  // SEO dostu URL oluştur
formatDate($date, $format)         // Türkçe tarih formatla
truncateText($text, $length)       // Metni kısalt
url($path)                         // Site URL oluştur
asset($path)                       // Asset URL oluştur
displayAd($zoneName)               // Reklam göster
renderNewsContent($html)           // Güvenli HTML render
cleanMetaContent($content)         // Meta tag için temizle
```

## 📝 Yeni Özellik Ekleme Süreci

### 1. Planning
- Kod standartlarını kontrol et (`code_standards.md`)
- Veritabanı değişikliği gerekiyor mu?
- Güvenlik riskleri neler?

### 2. Database
- Migration dosyası oluştur: `database/migrations/XXX_description.sql`
- Documentation güncelle: `database/*.md` dosyaları
- Rollback planı yap

### 3. Model
- `app/models/` içinde model oluştur
- Business logic burada olmalı
- Prepared statements kullan

### 4. Controller
- `app/controllers/` içinde controller oluştur
- Thin controller (sadece orkestrasyon)
- Input validation yap

### 5. View
- `templates/` içinde view oluştur
- Semantic HTML kullan
- Her output'ta `escape()` kullan

### 6. Testing
- Manual testing yap
- Güvenlik kontrolü yap (XSS, SQL Injection)
- Responsive design kontrol et
- SEO kontrol et

## 🚨 Dikkat Edilmesi Gerekenler

### ASLA YAPMA
❌ Raw SQL query (string concatenation)
❌ `echo $userInput` (escaped olmadan)
❌ `$_GET`, `$_POST` direkt kullanım (validate et)
❌ File upload without validation
❌ Business logic controller'da
❌ İş mantığı view'de

### MUTLAKA YAP
✅ Prepared statements
✅ `escape()` fonksiyonu her output'ta
✅ Input validation
✅ CSRF token kontrolü
✅ Fat models, skinny controllers
✅ Helper fonksiyonları kullan
✅ Semantic HTML
✅ BEM methodology (CSS)
✅ ES6+ syntax (JavaScript)
✅ Mobile-first responsive design

## 🔍 Debugging

### Debug Mode
```php
define('DEBUG_MODE', true); // config.php

// Debug için
dd($variable);  // Dump and die
```

### Error Logging
```php
error_log('Debug message: ' . print_r($data, true));
```

## 📚 Dokümantasyon

### Code Comments
```php
/**
 * Haber oluştur
 * 
 * @param array $data Haber verileri
 * @return int Created news ID
 * @throws ValidationException Invalid data durumunda
 */
public function createNews(array $data): int {
    // Implementation
}
```

### Database Documentation
Her database değişikliği için `database/*.md` dosyalarını güncelle:
- `DB_DEGISIKLIKLERI.md` - Tüm değişiklikler
- `admin_operations.md` - Admin paneli değişiklikleri
- `adsense_operations.md` - Reklam sistem değişiklikleri

## 🎨 UI/UX

### Bootstrap Usage
- Bootstrap 5.3.2 kullan
- Custom CSS ile override et
- Responsive utilities kullan

### Icons
- Font Awesome icons
- Semantic icon kullanımı

### Colors
- Primary: #007bff
- Secondary: #6c757d
- Success: #28a745
- Danger: #dc3545

## 🔐 Admin Panel

### Authentication
```php
// Admin controller'da
class AdminController extends Controller {
    public function __construct() {
        $this->checkAuth(); // Her zaman auth check
    }
}
```

### Authorization
```php
// Permission check
if (!$this->hasPermission('edit_news')) {
    throw new UnauthorizedException();
}
```

## 💰 Google AdSense

### Ad Zones
```php
// Ad göster
<?= displayAd('header_banner') ?>
<?= displayAd('sidebar_square') ?>
<?= displayAd('content_inline') ?>
```

### Ad Manager
- `app/helpers/AdManager.php` kullan
- Ad blocker detection var
- Lazy loading destekli

## 🌍 Turkish Support

### Date Formatting
```php
formatDate($date, 'd F Y, l');  // Türkçe tarih
turkishDate('d F Y', $date);    // Alternative
```

### Slug Generation
```php
createSlug('Yapay Zeka Teknolojisi');
// Result: "yapay-zeka-teknolojisi"
```

## 📦 Dependencies

### PHP Extensions
- PDO (MySQL)
- GD or Imagick (Image processing)
- mbstring (Multi-byte string)
- curl (HTTP requests)

### JavaScript Libraries
- Bootstrap 5.3.2
- Font Awesome 6.x

## 🚀 Deployment

### Production Checklist
- [ ] `DEBUG_MODE = false`
- [ ] Database backup
- [ ] File permissions check
- [ ] HTTPS enabled
- [ ] Security headers
- [ ] Cache enabled
- [ ] Minify assets
- [ ] Google Analytics configured
- [ ] Google AdSense configured
- [ ] Sitemap submitted to Google

## 📞 Support

- **Documentation**: `code_standards.md`, `README.md`
- **Database**: `database/*.md` files
- **Configuration**: `config/config.php`

---

**NOT**: Bu talimatlar AI agent'ların projeyi anlaması ve doğru kod üretmesi için hazırlanmıştır. Geliştirme yaparken bu kurallara uyulması beklenir.

