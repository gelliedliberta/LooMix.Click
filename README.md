# 🚀 LooMix.Click - Modern Haber Sitesi

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue.svg)](https://php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-%3E%3D5.7-orange.svg)](https://mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.2-purple.svg)](https://getbootstrap.com/)

Profesyonel, SEO uyumlu ve reklam dostu modern haber sitesi. Ham PHP ile geliştirilmiş, MVC mimarisine dayalı, responsive tasarıma sahip.

## ✨ Özellikler

### 🏗️ Teknik Özellikler
- **MVC Mimarisi**: Profesyonel kod organizasyonu
- **SEO Optimized**: Arama motorları için optimize edilmiş
- **Responsive Design**: Tüm cihazlarda mükemmel görünüm
- **PWA Ready**: Progressive Web App desteği
- **Performance**: Hızlı yükleme ve optimizasyon
- **Security**: XSS, CSRF ve SQL Injection koruması

### 📰 Haber Yönetimi
- Kategoriler ve alt kategoriler
- Etiket sistemi
- Öne çıkan haberler
- Son dakika haberleri
- Görüntülenme sayısı takibi
- Okuma süresi hesaplama
- İlgili haberler

### 💰 Reklam Sistemi
- Google AdSense entegrasyonu
- Reklam alanları yönetimi
- Ad Blocker detection
- A/B test desteği
- Responsive reklamlar
- Display rules (görüntüleme kuralları)

### 🔍 SEO & Analytics
- Meta tag yönetimi
- Open Graph & Twitter Cards
- JSON-LD Structured Data
- XML Sitemap otomatik oluşturma
- Canonical URL'ler
- Google Analytics entegrasyonu
- Core Web Vitals tracking

### 📱 Modern UI/UX
- Bootstrap 5.3.2
- Font Awesome icons
- Inter font family
- Dark/Light mode
- Smooth animations
- Lazy loading
- Infinite scroll

## 🛠️ Kurulum

### Gereksinimler
- PHP >= 7.4
- MySQL >= 5.7
- Apache/Nginx web server
- Composer (opsiyonel, gelecekteki güncellemeler için)

### 1. Projeyi İndirin
```bash
git clone https://github.com/your-username/loomix-click.git
cd loomix-click
```

### 2. Veritabanını Kurun
```bash
# MySQL'e giriş yapın
mysql -u root -p

# Veritabanını oluşturun ve verileri yükleyin
mysql -u root -p < database/migration.sql
mysql -u root -p < database/sample_data.sql
```

### 3. Konfigürasyon
`config/config.php` dosyasını düzenleyin:

```php
// Veritabanı ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'u920805771_loomix');
define('DB_USER', 'u920805771_loomix');
define('DB_PASS', '');

// Site bilgileri
define('SITE_URL', 'https://your-domain.com');
define('GOOGLE_ADSENSE_ID', 'ca-pub-xxxxxxxxxxxxxxxx');
```

### 4. Dizin İzinleri
```bash
chmod -R 755 assets/
chmod -R 775 assets/uploads/
chmod -R 755 templates/
```

### 5. Web Server Konfigürasyonu

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## 🎯 Kullanım

### Admin Paneli
1. Tarayıcınızda `/admin` adresine gidin
2. Varsayılan giriş bilgileri:
   - **Kullanıcı adı**: admin
   - **Şifre**: admin123

### Haber Ekleme
1. Admin panelinde "Haber Ekle" seçin
2. Başlık, içerik, kategori seçin
3. SEO bilgilerini doldurun
4. Yayınla butonuna tıklayın

### Kategori Yönetimi
1. "Kategoriler" bölümünden kategori ekleyin
2. Alt kategoriler oluşturun
3. Renk ve ikon atayın

### Reklam Yönetimi
1. `ad_zones` tablosuna reklam alanları ekleyin
2. Google AdSense kodlarınızı girin
3. Display rules ile görüntüleme kuralları belirleyin

## 📁 Dizin Yapısı

```
LooMix.Click/
├── app/
│   ├── controllers/     # Controller sınıfları
│   ├── models/         # Model sınıfları
│   ├── core/           # Core sistem sınıfları
│   └── helpers/        # Yardımcı sınıflar
├── assets/
│   ├── css/           # Stil dosyaları
│   ├── js/            # JavaScript dosyaları
│   ├── images/        # Resim dosyaları
│   └── uploads/       # Yüklenen dosyalar
├── config/            # Konfigürasyon dosyaları
├── database/          # Veritabanı dosyaları
├── includes/          # Ortak include dosyaları
├── templates/         # View template'leri
│   ├── layouts/       # Layout dosyaları
│   ├── home/          # Ana sayfa template'leri
│   ├── news/          # Haber template'leri
│   └── admin/         # Admin template'leri
└── index.php          # Ana entry point
```

## 🔧 Konfigürasyon

### Site Ayarları
```php
// Site bilgileri
define('SITE_NAME', 'LooMix.Click');
define('SITE_DESCRIPTION', 'En güncel haberler...');
define('SITE_URL', 'https://loomix.click');

// SEO ayarları
define('ROBOTS_INDEX', true);
define('DEFAULT_META_IMAGE', '/assets/images/default-share.jpg');

// Reklam ayarları
define('ADS_ENABLED', true);
define('GOOGLE_ADSENSE_ID', 'ca-pub-xxxxxxxxxxxxxxxx');
```

### Veritabanı Ayarları
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'loomix_click');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

## 🚀 Performans Optimizasyonu

### 1. Caching
```php
// Sayfa cache'leme
$cacheKey = 'homepage_' . md5($params);
if (!$cached = Cache::get($cacheKey)) {
    $data = generateHomePageData();
    Cache::set($cacheKey, $data, 3600);
}
```

### 2. Image Optimization
- WebP format desteği
- Lazy loading
- Responsive images
- CDN entegrasyonu (opsiyonel)

### 3. Database Optimization
- Indexing
- Query optimization
- Connection pooling
- Read replicas (gelecek)

## 🔒 Güvenlik

### Implemented Security Measures
- ✅ SQL Injection koruması (Prepared Statements)
- ✅ XSS koruması (HTML Escaping)
- ✅ CSRF token koruması
- ✅ Password hashing (bcrypt)
- ✅ Session güvenliği
- ✅ File upload güvenliği
- ✅ Input validation

### Security Checklist
```php
// XSS Protection
echo escape($userInput);

// SQL Injection Protection
$stmt = $db->prepare("SELECT * FROM news WHERE id = :id");
$stmt->execute(['id' => $newsId]);

// CSRF Protection
if (!verifyCsrfToken($_POST['csrf_token'])) {
    throw new SecurityException();
}
```

## 📊 SEO Özellikleri

### Meta Tags
- Dynamic title generation
- Auto meta descriptions
- Open Graph tags
- Twitter Cards
- Canonical URLs

### Structured Data
```php
// JSON-LD
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "headline": "Haber Başlığı",
  "datePublished": "2024-01-01T10:00:00Z",
  "author": {
    "@type": "Person",
    "name": "Yazar Adı"
  }
}
```

### XML Sitemap
- Otomatik sitemap oluşturma
- Google Search Console ping
- Priority ve changefreq ayarları

## 🎨 Tema Özelleştirme

### CSS Variables
```css
:root {
    --primary-color: #007bff;
    --secondary-color: #6c757d;
    --font-family-base: 'Inter', sans-serif;
}
```

### Component Structure
```html
<article class="news-card">
    <div class="news-card__image">...</div>
    <div class="news-card__content">
        <h3 class="news-card__title">...</h3>
        <p class="news-card__summary">...</p>
    </div>
</article>
```

## 📱 Progressive Web App

### Service Worker
```javascript
// Cache strategies
const CACHE_NAME = 'loomix-v1';
const urlsToCache = [
    '/',
    '/assets/css/style.css',
    '/assets/js/app.js'
];
```

### Manifest.json
```json
{
    "name": "LooMix.Click",
    "short_name": "LooMix",
    "start_url": "/",
    "display": "standalone",
    "theme_color": "#007bff"
}
```

## 📈 Analytics & Tracking

### Google Analytics 4
```javascript
gtag('config', 'G-XXXXXXXXXX', {
    page_title: 'Haber Başlığı',
    page_location: 'https://loomix.click/haber/slug'
});
```

### Core Web Vitals
```javascript
import {getCLS, getFID, getFCP, getLCP, getTTFB} from 'web-vitals';

getCLS(sendToAnalytics);
getFID(sendToAnalytics);
getFCP(sendToAnalytics);
getLCP(sendToAnalytics);
getTTFB(sendToAnalytics);
```

## 🧪 Testing

### Unit Tests
```bash
# PHPUnit tests
./vendor/bin/phpunit tests/

# JavaScript tests
npm test
```

### Performance Testing
```bash
# Lighthouse CLI
lighthouse https://loomix.click --output html

# PageSpeed Insights
npm install -g psi
psi https://loomix.click --strategy=mobile
```

## 🚀 Production Deployment

### 1. Environment Setup
```php
// Production config
define('DEBUG_MODE', false);
define('CACHE_ENABLED', true);
define('MINIFY_ASSETS', true);
```

### 2. Performance Optimizations
- Enable Gzip compression
- Set proper cache headers
- Minify CSS/JS
- Optimize images
- Enable CDN

### 3. Security Hardening
- Update PHP version
- Disable unused modules
- Set proper file permissions
- Enable HTTPS
- Configure firewall

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

### Kod Standartları
Lütfen `code_standards.md` dosyasındaki kurallara uyun:
- PSR-4 autoloading
- SOLID principles
- Semantic versioning
- Conventional commits

## 📝 Changelog

### v1.0.0 (2024-01-01)
- ✨ Initial release
- 🏗️ MVC architecture implementation
- 📰 News management system
- 💰 Ad management system
- 🔍 SEO optimization
- 📱 Responsive design
- 🚀 PWA features

## 🛣️ Roadmap

### v1.1.0 (Planned)
- [ ] Comment system
- [ ] User registration
- [ ] Newsletter system
- [ ] Social media integration
- [ ] Multi-language support

### v1.2.0 (Planned)
- [ ] Full admin panel
- [ ] Advanced analytics
- [ ] Email notifications
- [ ] API endpoints
- [ ] Mobile app

### v2.0.0 (Future)
- [ ] Microservices architecture
- [ ] Elasticsearch integration
- [ ] Real-time notifications
- [ ] AI content recommendations
- [ ] Machine learning features

## ⚠️ Bilinen Sorunlar

- [ ] Safari'de bazı CSS grid sorunları
- [ ] IE11 desteği sınırlı
- [ ] Çok büyük resimler için timeout sorunu

## 📞 Destek

- 🐛 **Bug Reports**: [GitHub Issues](https://github.com/your-username/loomix-click/issues)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/your-username/loomix-click/discussions)
- 📧 **Email**: support@loomix.click
- 🌐 **Website**: https://loomix.click

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasını inceleyin.

## 👥 Geliştiriciler

- **Lead Developer**: [Your Name](https://github.com/your-username)
- **Contributors**: [Contributors List](https://github.com/your-username/loomix-click/graphs/contributors)

## 🙏 Teşekkürler

- [Bootstrap](https://getbootstrap.com/) - CSS Framework
- [Font Awesome](https://fontawesome.com/) - Icons
- [Inter Font](https://rsms.me/inter/) - Typography
- [PHP](https://php.net/) - Backend Language
- [MySQL](https://mysql.com/) - Database

---

⭐ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!

📝 **Not**: Bu README.md dosyası projenin gelişimiyle birlikte güncellenecektir.
