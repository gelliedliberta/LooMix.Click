# Admin Panel Veritabanı İşlemleri

Bu dosya, LooMix.Click admin panel veritabanı işlemlerini ve güncellemelerini içerir.

## Son Güncelleme: 2025-01-14 - Layout Render Sorunu Çözüldü - SİSTEM TAMAM ✅

---

## Veritabanı Tabloları

### admin_users Tablosu
Admin kullanıcıları için temel tablo.

```sql
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    login_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Gerekli Admin Kullanıcı Verisi
```sql
-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT INTO admin_users (username, password, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin');
```

---

## Son Yapılan İşlemler

### 2025-01-14: Route Sistemi ve Template'ler Tamamlandı

**Problem:**
- Index.php'de eksik route'lar vardı (haberler, kategoriler, AMP)
- HomeController'da eksik metodlar vardı (RSS, sitemap)
- Çoğu template dosyası eksikti, sayfalar çalışmıyordu
- Path düzeltmelerinden sonra render sisteminde uyumsuzluklar

**Çözüm:**
1. **Index.php route'ları tamamlandı**
   - `/haberler` - NewsController@index
   - `/kategoriler` - CategoryController@index  
   - `/amp/haber/{slug}` - NewsController@amp
   - Tüm route'lar sistemli olarak düzenlendi

2. **HomeController eksik metodları eklendi**
   - `sitemap()` - Site haritası sayfası
   - `rss()` - RSS feed oluşturma
   - Tüm metodlar doğru template render'ı ile

3. **Template dosyaları oluşturuldu**
   - `templates/news/index.php` - Haber listesi
   - `templates/category/index.php` - Kategori listesi
   - `templates/home/about.php` - Hakkımızda
   - `templates/home/contact.php` - İletişim
   - `templates/home/privacy.php` - Gizlilik Politikası
   - `templates/home/sitemap.php` - Site Haritası
   - `templates/home/search.php` - Arama sonuçları

4. **Path sistemi optimize edildi**
   - `TEMPLATE_PATH` sabiti eklendi (config.php)
   - Absolute path kullanımı
   - Working directory'den bağımsız çalışma
   - Upload path'i de düzeltildi

**Sonuç:**
- ✅ Tüm route'lar çalışıyor
- ✅ Template dosyaları mevcut ve responsive
- ✅ Path sorunları tamamen çözüldü
- ✅ RSS feed çalışıyor
- ✅ Site haritası otomatik oluşturuluyor
- ✅ Modern ve kullanışlı template'ler

**Etkilenen Dosyalar:**
- `index.php` - Route'lar güncellendi
- `app/controllers/HomeController.php` - sitemap() ve rss() metodları eklendi
- `config/config.php` - ROOT_PATH ve TEMPLATE_PATH sabitleri eklendi
- `app/core/View.php` - Path sistemi absolute path'e geçirildi
- Tüm template dosyaları yenilendi/oluşturuldu

### 2025-01-14: View Render Sistemi Tamamen Yenilendi

**Problem:**
- Controller base sınıfında render() metodu ile View sınıfındaki render() metodu çakışıyor
- İki farklı render sistemi karışıklık yaratıyordu
- Template/layout bağlantılarında sorunlar vardı

**Çözüm:**
1. **Controller base sınıfındaki render() metodu kaldırıldı**
   - Artık tüm controller'lar doğrudan View sınıfını kullanıyor
   - Tek bir tutarlı render sistemi
   - Karışıklık tamamen giderildi

2. **Tüm controller'larda kullanım standartlaştırıldı**
   - Eski: `$view->set([...]); $view->render('template', 'layout');`
   - Yeni: `$view->render('template', [...], 'layout');`
   - Daha temiz, daha performanslı

3. **AdminController metodları güncellendi**
   - `addNews()` metodu güncellendi
   - `editNews()` metodu güncellendi
   - Tüm admin sayfaları tutarlı render kullanımına geçti

**Etkilenen Dosyalar:**
- `app/core/Controller.php` - render ve renderPartial metodları kaldırıldı
- `app/controllers/AdminController.php` - addNews ve editNews metodları güncellendi
- Tüm layout'lar düzgün çalışıyor (main.php, admin.php)

### 2025-01-14: Render Fonksiyon Kullanımı Düzeltmesi

**Problem:**
- View render fonksiyonu hatalı imzaya sahipti: `render($viewName, $layout = 'main')`
- Doğru imza olması gerekiyordu: `render($view, $data = [], $layout = 'main')`
- Controller'larda `$view->set()` kullanımı ve sonra render çağrısı gereksiz karmaşıklık yaratıyordu
- Layout entegrasyonu sorunları vardı

**Çözüm:**
1. **View sınıfında render metodu güncellendi**
   - Yeni imza: `render($viewName, $data = [], $layout = 'main')`
   - Data parametresi doğrudan render'a geçilebiliyor
   - Daha temiz ve standart kullanım

2. **Tüm Controller'larda kullanım güncellendi**
   - Eski: `$view->set([...]); $view->render('template', 'layout');`
   - Yeni: `$view->render('template', [...], 'layout');`
   - Daha az kod, daha okunabilir

3. **Layout entegrasyonu düzeltildi**
   - Tüm sayfalarda doğru layout render ediliyor
   - Header, footer ve navigation doğru şekilde gösteriliyor

**Etkilenen Dosyalar:**
- `app/core/View.php` - render metodu güncellendi
- `app/controllers/HomeController.php` - tüm render çağrıları güncellendi
- `app/controllers/AdminController.php` - tüm render çağrıları güncellendi
- `app/controllers/NewsController.php` - tüm render çağrıları güncellendi
- `app/controllers/CategoryController.php` - tüm render çağrıları güncellendi
- `app/controllers/ErrorController.php` - tüm render çağrıları güncellendi

**Önceki Değişiklikler:**

### 2025-01-14: Layout Entegrasyon Sorunu Düzeltmesi

**Problem:** Layout parametresi eksiklikleri ve CLI ortamı sorunları
**Çözüm:** Layout parametreleri eklendi, CLI ortamı düzeltmeleri yapıldı

### 2024-01-XX: Admin Login Sonsuz Yönlendirme Düzeltmesi

**Problem:**
- Admin login sayfasında "çok fazla yönlendirme" hatası
- Sonsuz yönlendirme döngüsü oluşuyordu

**Çözüm:**
1. **AdminController::__construct()** içinde route kontrolü düzeltildi
2. Session kontrolleri tutarlı hale getirildi
3. **getCurrentRoute()** metodu eklendi (Router ile tutarlı URL temizleme)

**Değişiklikler:**
- `$_SERVER['REQUEST_URI']` yerine `getCurrentRoute()` kullanılıyor
- Tüm session kontrolleri `session_status()` ile yapılıyor
- `session_start()` yerine güvenli session yönetimi

**Etkilenen Dosyalar:**
- `app/controllers/AdminController.php`
- `app/core/Controller.php`

---

## Session Güvenliği

### Session Yönetimi
```php
// Güvenli session başlatma
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

### Admin Session Kontrolü
```php
if (!isset($_SESSION[ADMIN_SESSION_NAME]) || $_SESSION[ADMIN_SESSION_NAME] !== true) {
    $this->redirect('/admin/login');
}
```

### Logout İşlemi
```php
// Güvenli logout
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
```

---

## Route Kontrolü

### getCurrentRoute() Metodu
XAMPP ortamında doğru çalışan route kontrolü:

```php
private function getCurrentRoute() {
    $url = $_SERVER['REQUEST_URI'];
    
    // Query string'i kaldır
    if (($pos = strpos($url, '?')) !== false) {
        $url = substr($url, 0, $pos);
    }
    
    // Base path'i çıkar (XAMPP için)
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptPath !== '/' && strpos($url, $scriptPath) === 0) {
        $url = substr($url, strlen($scriptPath));
    }
    
    // Başlangıçta / yoksa ekle
    if (empty($url) || $url[0] !== '/') {
        $url = '/' . $url;
    }
    
    return $url;
}
```

---

## Önemli Notlar

### Güvenlik
- Tüm admin işlemlerde CSRF token kontrolü yapılır
- Session güvenliği artırıldı
- Password verification ile güvenli login
- Ayarlar API'sinde kısmi kayıt desteklenir: yalnızca gönderilen alanlar güncellenir

### Performance
- Session kontrolleri optimize edildi
- Gereksiz session_start() çağrıları kaldırıldı
- Route eşleştirme performansı artırıldı

### Maintenance
- Session timeout ayarları config'de tutulur
- Admin session name konfigüre edilebilir
- Debug mode'da detaylı hata mesajları

---

## Gelecek Güncellemeler

### Planlanenlar
- [ ] Remember me functionality
- [ ] Two-factor authentication
- [ ] Session timeout warning
- [ ] Admin activity logging
- [ ] Password strength validation

### Bilinen Sorunlar
- Yok

---

## Veritabanı Sorguları
### Site Ayarları
`site_settings` tablosu `setting_key` ve `setting_value` sütunlarından oluşur. API, beyaz listeye alınmış anahtarlarla çalışır ve kısmi güncellemeyi destekler. Örnek akış:

1) İstek (JSON):
```
POST /admin/api/settings/save
{
  "csrf_token": "<token>",
  "site_name": "Yeni İsim",
  "enable_cache": "1"
}
```

2) Davranış:
- Gönderilen alanlar güncellenir, eksik alanlar mevcut değerlerini korur.
- Boolean değerler `1/0` olarak saklanır.
- `debug_mode`, `enable_cache`, `timezone` değişimlerinde yeniden yükleme önerisi döner.


### Login Doğrulama
```sql
SELECT * FROM admin_users 
WHERE username = :username AND is_active = 1
```

### Login Sayacı Güncelleme
```sql
UPDATE admin_users 
SET last_login = NOW(), login_count = login_count + 1 
WHERE id = :id
```

### Aktif Kullanıcı Listesi
```sql
SELECT id, username, full_name, role, last_login, login_count 
FROM admin_users 
WHERE is_active = 1 
ORDER BY last_login DESC
```

---

*Bu dosya her veritabanı değişikliğinde güncellenir.*
