---
description: "Güvenlik kuralları - XSS, CSRF, SQL Injection koruması ve best practices"
alwaysApply: true
---

# Security Guidelines

LooMix.Click projesi için güvenlik standartları ve en iyi uygulamalar.

## 🔒 SQL Injection Prevention (ZORUNLU)

### Prepared Statements Kullan

```php
// DOĞRU - Prepared statements ile güvenli
$stmt = $this->db->prepare("SELECT * FROM news WHERE id = :id");
$stmt->execute(['id' => $newsId]);
$news = $stmt->fetch();

// YANLIŞ - SQL Injection riski!
$query = "SELECT * FROM news WHERE id = " . $_GET['id'];
$news = $this->db->query($query);

// YANLIŞ - String concatenation
$query = "SELECT * FROM news WHERE title LIKE '%" . $_POST['search'] . "%'";
```

### Always Validate Input

```php
// Input validation
$newsId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$newsId) {
    throw new ValidationException('Invalid news ID');
}

// Use whitelisting for specific values
$allowedStatus = ['published', 'draft', 'pending'];
$status = $_POST['status'] ?? 'draft';
if (!in_array($status, $allowedStatus, true)) {
    throw new ValidationException('Invalid status');
}
```

## 🛡️ XSS (Cross-Site Scripting) Prevention (ZORUNLU)

### Her Çıktıda escape() Kullan

```php
// DOĞRU - HTML encode edilmiş output
<?= escape($news['title']) ?>
<?= escape($user['name']) ?>
<?= escape($_GET['search']) ?>

// YANLIŞ - Raw output (XSS riski!)
<?= $news['title'] ?>
<?= $_GET['search'] ?>
```

### escape() Fonksiyonu

Projede tanımlı `escape()` fonksiyonunu kullan:

```php
// includes/functions.php
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
```

### Rich Content için renderNewsContent()

Haber içeriği gibi HTML içeren contentler için:

```php
// DOĞRU - Güvenli HTML render
<?= renderNewsContent($news['content']) ?>

// Bu fonksiyon:
// - Tehlikeli tagları kaldırır (script, style, meta, link)
// - İframe'leri normalize eder
// - Tehlikeli inline style'ları temizler
// - Safe HTML taglarına izin verir
```

### Meta Tag Content

```php
// DOĞRU - Meta tag için özel cleaning
$metaDescription = cleanMetaContent($news['summary']);
?>
<meta name="description" content="<?= $metaDescription ?>">

// cleanMetaContent() fonksiyonu:
// - HTML taglarını kaldırır
// - Tırnak işaretlerini encode eder
```

## 🔐 CSRF (Cross-Site Request Forgery) Protection

### Form Token Kullan

```php
// Form'da token ekle
<form method="POST" action="/admin/news/create">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <!-- Form fields -->
</form>

// Controller'da token doğrula
public function create() {
    if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
        throw new SecurityException('Invalid CSRF token');
    }
    
    // Process form
}
```

### CSRF Token Functions

```php
// Token generate
function generateCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Token verify
function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) 
        && hash_equals($_SESSION['csrf_token'], $token);
}
```

## 🔑 Authentication & Authorization

### Password Hashing

```php
// DOĞRU - Password hashing
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Login verification
if (password_verify($inputPassword, $hashedPassword)) {
    // Login successful
    $_SESSION[ADMIN_SESSION_NAME] = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'logged_in' => true
    ];
}

// YANLIŞ - Plain text password (ASLA YAPMA!)
$password = md5($inputPassword); // MD5 güvenli değil!
$password = sha1($inputPassword); // SHA1 de güvenli değil!
```

### Session Security

```php
// Session configuration (config.php)
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => true,  // HTTPS için
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Session regenerate after login
session_regenerate_id(true);
```

### Admin Route Protection

```php
// Admin controller'da her zaman auth check
class AdminController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
    }
    
    private function checkAuth() {
        if (!isset($_SESSION[ADMIN_SESSION_NAME]['logged_in'])) {
            header('Location: /admin/login');
            exit;
        }
    }
}
```

## 📁 File Upload Security

### File Validation

```php
public function uploadImage(array $file): string {
    // 1. Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new ValidationException('File too large');
    }
    
    // 2. Check MIME type
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedMimes, true)) {
        throw new ValidationException('Invalid file type');
    }
    
    // 3. Check extension (whitelist)
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS, true)) {
        throw new ValidationException('Invalid file extension');
    }
    
    // 4. Generate safe filename
    $filename = uniqid() . '.' . $extension;
    
    // 5. Move to safe directory
    $uploadPath = UPLOAD_PATH . date('Y/m/d/') . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('File upload failed');
    }
    
    return $uploadPath;
}
```

### Never Trust User Input

```php
// DOĞRU - Safe filename
$filename = uniqid() . '.' . $extension;

// YANLIŞ - User controlled filename (Path Traversal riski!)
$filename = $_FILES['image']['name'];
$filename = basename($_POST['filename']); // Yine tehlikeli!
```

## 🚫 Input Validation

### Whitelist Approach

```php
// DOĞRU - Whitelist
$allowedSortColumns = ['title', 'publish_date', 'view_count'];
$sortBy = $_GET['sort'] ?? 'publish_date';

if (!in_array($sortBy, $allowedSortColumns, true)) {
    $sortBy = 'publish_date';
}

// YANLIŞ - Blacklist (her durumu kapsayamaz)
$sortBy = str_replace(['..', '/', '\\'], '', $_GET['sort']);
```

### Type Validation

```php
// Integer validation
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
if ($page === false || $page < 1) {
    $page = 1;
}

// Email validation
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    throw new ValidationException('Invalid email');
}

// URL validation
$url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
```

## 🔍 Error Handling & Information Disclosure

### Don't Expose Sensitive Information

```php
// DOĞRU - Generic error message
try {
    $news = $this->newsModel->getById($id);
} catch (Exception $e) {
    // Log detailed error
    error_log('News fetch error: ' . $e->getMessage());
    
    // Show generic message to user
    if (DEBUG_MODE) {
        throw $e; // Development'ta detay göster
    } else {
        throw new Exception('An error occurred'); // Production'da generic
    }
}

// YANLIŞ - Sensitive information leak
catch (Exception $e) {
    echo "Database error: " . $e->getMessage(); // SQL query açığa çıkabilir!
}
```

### Debug Mode

```php
// config.php
define('DEBUG_MODE', env_bool('DEBUG_MODE', false));

// Production'da MUTLAKA false olmalı!
// .env dosyasında:
// DEBUG_MODE=false
```

## 🌐 HTTP Headers Security

### Security Headers

```php
// Security headers ekle (layout dosyalarında veya .htaccess'te)
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// HTTPS zorla (production)
if ($_SERVER['HTTPS'] !== 'on' && getenv('APP_ENV') === 'production') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}
```

### Content Security Policy

```php
// CSP header (Google AdSense için gerekli domainler dahil)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://pagead2.googlesyndication.com; img-src 'self' data: https:; style-src 'self' 'unsafe-inline';");
```

## 🗝️ API Security (Gelecek için)

### API Authentication

```php
// Bearer token kullan
function validateApiToken(): bool {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    
    if (strpos($token, 'Bearer ') !== 0) {
        return false;
    }
    
    $token = substr($token, 7);
    return hash_equals(API_TOKEN, $token);
}

// Rate limiting uygula
function checkRateLimit(string $clientId): bool {
    $key = "rate_limit:{$clientId}";
    $requests = $cache->get($key) ?? 0;
    
    if ($requests >= 100) { // 100 requests per hour
        return false;
    }
    
    $cache->set($key, $requests + 1, 3600);
    return true;
}
```

## ✅ Security Checklist

Yeni özellik eklerken kontrol et:

- [ ] Tüm user input'lar validate edildi mi?
- [ ] SQL query'ler prepared statement kullanıyor mu?
- [ ] Tüm output'lar escape() ile encode edildi mi?
- [ ] CSRF token kontrolü var mı? (POST/PUT/DELETE için)
- [ ] File upload güvenli mi?
- [ ] Authentication/Authorization kontrolleri yapıldı mı?
- [ ] Error mesajları sensitive bilgi içermiyor mu?
- [ ] Session güvenli yapılandırıldı mı?
- [ ] HTTPS kullanılıyor mu? (production)
- [ ] Security header'ları eklendi mi?

## 🚨 Common Vulnerabilities to Avoid

1. **SQL Injection** - Always use prepared statements
2. **XSS** - Always escape output
3. **CSRF** - Use tokens for state-changing requests
4. **Path Traversal** - Never use user input in file paths
5. **Session Hijacking** - Use secure session configuration
6. **Information Disclosure** - Don't expose sensitive errors
7. **Weak Passwords** - Use password_hash() with strong algorithms
8. **Insecure File Upload** - Validate type, size, and content
9. **Missing Authorization** - Check permissions on every request
10. **Insecure Direct Object References** - Validate ownership before access

