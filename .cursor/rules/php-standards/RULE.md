---
description: "PHP kod standartları ve en iyi uygulamalar - LooMix.Click projesi için"
alwaysApply: true
---

# PHP Kod Standartları

Bu rule, LooMix.Click projesindeki tüm PHP dosyaları için geçerli kod standartlarını içerir.

## Dosya Yapısı

Her PHP dosyası şu yapıda olmalı:

```php
<?php
/**
 * Dosya açıklaması
 * LooMix.Click
 */

// Namespace (gerekiyorsa)
namespace App\Models;

// Use statement'ları
use Database;
use Exception;

/**
 * Sınıf açıklaması
 */
class ClassName extends ParentClass {
    // Kod burada
}
```

## Naming Conventions

### Sınıf İsimleri
- **PascalCase** kullan: `NewsController`, `UserModel`, `AdManager`
- Açıklayıcı isimler: `EmailService`, `PaymentProcessor`

### Method İsimleri
- **camelCase** kullan: `getUserData()`, `validateEmail()`, `createNews()`
- Verb + noun yapısı: `createUser()`, `deletePost()`, `updateCategory()`

### Variable İsimleri
- **camelCase** kullan: `$userId`, `$newsData`, `$categoryList`
- Açıklayıcı isimler: `$isValidEmail`, `$userCount`, `$publishedNews`

### Sabitler
- **UPPER_SNAKE_CASE**: `DB_HOST`, `MAX_FILE_SIZE`, `ADS_ENABLED`
- Açıklayıcı isimler: `DEFAULT_META_IMAGE`, `SITE_URL`

## Kod Formatı

### Indentation
- **4 space** kullan (tab DEĞİL)
- Tutarlı girinti uygula

### Çizgi Uzunluğu
- Maksimum **120 karakter**
- Uzun satırları mantıklı yerlerde böl

### Parantezler ve Boşluklar
```php
// DOĞRU
if ($condition) {
    doSomething();
}

foreach ($items as $item) {
    processItem($item);
}

// YANLIŞ
if($condition){
    doSomething();
}
```

## Error Handling

Her zaman try-catch kullan ve anlamlı hata mesajları ver:

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

## Comments ve Documentation

Her public method için PHPDoc yorumu ekle:

```php
/**
 * Kullanıcı verilerini doğrular
 * 
 * @param array $data Doğrulanacak veri
 * @return bool Doğrulama sonucu
 * @throws ValidationException Geçersiz veri durumunda
 */
public function validateUserData(array $data): bool {
    // İmplementasyon
}
```

## SOLID Prensipleri

- **S**ingle Responsibility: Her sınıf tek bir sorumluluğa sahip olmalı
- **O**pen/Closed: Sınıflar genişletmeye açık, değişikliğe kapalı
- **L**iskov Substitution: Alt sınıflar, üst sınıfların yerine geçebilmeli
- **I**nterface Segregation: İstemciler kullanmadıkları arayüzlere bağımlı olmamalı
- **D**ependency Inversion: Soyutlamalara bağımlı ol, somut sınıflara değil

## DRY (Don't Repeat Yourself)

- Kod tekrarından kaçın
- Ortak fonksiyonları `includes/functions.php` içinde topla
- Benzer mantıkları abstract sınıflarda birleştir

## KISS (Keep It Simple, Stupid)

- Kod basit ve anlaşılır olmalı
- Karmaşık yapılardan kaçın
- Açık ve net variable/function isimleri kullan

## Type Hints

PHP 7.4+ type hints kullan:

```php
public function createNews(string $title, array $data): int {
    // Implementation
}

public function getNewsById(int $id): ?array {
    // Implementation
}
```

## Project-Specific Functions

Projede tanımlı helper fonksiyonları kullan:

- `escape()` - XSS koruması için HTML encode
- `createSlug()` - SEO dostu URL oluştur
- `formatDate()` - Türkçe tarih formatla
- `truncateText()` - Metni kısalt
- `url()` - Site URL'i oluştur
- `asset()` - Asset URL'i oluştur
- `dd()` - Debug için dump ve die

Örnek:
```php
// DOĞRU
echo escape($userInput);
$newsSlug = createSlug($newsTitle);
$newsUrl = url('/haber/' . $news['slug']);

// YANLIŞ
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8'); // escape() kullan
```

