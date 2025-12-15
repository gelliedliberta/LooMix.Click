# Sosyal Medya Yönetim Sistemi - Veritabanı Dokümantasyonu

## 📋 Genel Bakış

LooMix.Click projesi için sosyal medya linklerinin admin panelden yönetilmesini sağlayan sistem.

**Tarih:** 15 Aralık 2025  
**Migration:** `007_social_media_links.sql`  
**İlgili Dosyalar:**
- Model: `app/models/SocialMedia.php`
- Controller: `app/controllers/AdminController.php`
- Template: `templates/admin/social-media/index.php`
- Helper: `includes/functions.php` (displaySocialLinks, getSocialLink)

---

## 🗄️ Tablo Yapısı

### `social_media_links` Tablosu

Sosyal medya platformlarının URL'lerini ve görüntüleme ayarlarını saklar.

```sql
CREATE TABLE `social_media_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) NOT NULL COMMENT 'Platform adı (facebook, twitter, instagram, vb.)',
  `name` varchar(100) NOT NULL COMMENT 'Görünen isim',
  `icon` varchar(100) NOT NULL COMMENT 'Font Awesome icon class (fab fa-facebook)',
  `url` varchar(255) DEFAULT NULL COMMENT 'Sosyal medya profil URL',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif',
  `display_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Gösterim sırası (küçük önce)',
  `show_in_header` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Header\'da göster',
  `show_in_footer` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Footer\'da göster',
  `color` varchar(7) DEFAULT NULL COMMENT 'Platform rengi (hex)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `platform_unique` (`platform`),
  KEY `active_order` (`is_active`, `display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Kolonlar

| Kolon | Tip | Açıklama |
|-------|-----|----------|
| `id` | INT(11) | Primary key, auto increment |
| `platform` | VARCHAR(50) | Platform kodu (unique) - slug benzeri |
| `name` | VARCHAR(100) | Görünen isim (Facebook, Twitter, vb.) |
| `icon` | VARCHAR(100) | Font Awesome icon class'ı |
| `url` | VARCHAR(255) | Sosyal medya profil URL'i (NULL olabilir) |
| `is_active` | TINYINT(1) | Genel aktif/pasif durumu (0=pasif, 1=aktif) |
| `display_order` | INT(11) | Gösterim sırası (küçük sayılar önce) |
| `show_in_header` | TINYINT(1) | Header'da gösterilsin mi? |
| `show_in_footer` | TINYINT(1) | Footer'da gösterilsin mi? |
| `color` | VARCHAR(7) | Platform rengi (hex format: #1877F2) |
| `created_at` | TIMESTAMP | Oluşturulma zamanı |
| `updated_at` | TIMESTAMP | Güncellenme zamanı (otomatik) |

#### İndeksler

- **PRIMARY KEY:** `id`
- **UNIQUE KEY:** `platform_unique` - Her platform sadece bir kez eklenebilir
- **KEY:** `active_order` - Aktif linkleri sıraya göre hızlı getirmek için

---

## 🔧 Özellikler

### 1. Admin Panel Yönetimi

**URL:** `/admin/sosyal-medya`

#### Özellikler:
- ✅ Yeni sosyal medya linki ekleme
- ✅ Mevcut linkleri düzenleme
- ✅ Link silme (RSS gibi sistem linkleri hariç)
- ✅ Tek tıkla aktif/pasif yapma
- ✅ Header/Footer gösterim kontrolü
- ✅ Sıralama düzenleme
- ✅ Canlı önizleme (header ve footer)
- ✅ İkon önizleme
- ✅ Renk seçici

### 2. Frontend Gösterimi

#### Helper Fonksiyonlar

**displaySocialLinks($position, $size, $class)**
```php
// Header'da göster (küçük)
<?= displaySocialLinks('header', 'small') ?>

// Footer'da göster (büyük)
<?= displaySocialLinks('footer', 'large') ?>

// Tüm aktif linkleri göster
<?= displaySocialLinks(null, 'medium') ?>
```

**getSocialLink($platform)**
```php
// Sadece Facebook linkini al
<?= getSocialLink('facebook') ?>
```

### 3. Varsayılan Platformlar

Migration dosyası şu platformları otomatik ekler:

| Platform | İsim | İkon | Renk | Varsayılan Durum |
|----------|------|------|------|------------------|
| facebook | Facebook | fab fa-facebook | #1877F2 | Aktif (Header+Footer) |
| twitter | Twitter (X) | fab fa-x-twitter | #000000 | Aktif (Header+Footer) |
| instagram | Instagram | fab fa-instagram | #E4405F | Aktif (Header+Footer) |
| youtube | YouTube | fab fa-youtube | #FF0000 | Aktif (Sadece Footer) |
| linkedin | LinkedIn | fab fa-linkedin | #0A66C2 | Pasif |
| tiktok | TikTok | fab fa-tiktok | #000000 | Pasif |
| telegram | Telegram | fab fa-telegram | #0088CC | Pasif |
| whatsapp | WhatsApp | fab fa-whatsapp | #25D366 | Pasif |
| rss | RSS | fas fa-rss | #FF6600 | Aktif (Sadece Header) |

---

## 🚀 Kurulum

### Adım 1: Migration'ı Çalıştır

```bash
# phpMyAdmin'de veya MySQL CLI'da çalıştırın:
mysql -u root -p loomix < database/migrations/007_social_media_links.sql
```

Veya phpMyAdmin:
1. `loomix` veritabanını seçin
2. SQL sekmesini açın
3. `007_social_media_links.sql` dosyasını import edin

### Adım 2: Admin Panele Giriş Yap

```
http://yourdomain.com/admin/sosyal-medya
```

### Adım 3: URL'leri Güncelle

1. Her platform için gerçek sosyal medya URL'nizi girin
2. Header/Footer gösterim ayarlarını yapın
3. Sıralamayı düzenleyin
4. Kaydedin!

---

## 📝 API Endpoint'leri

### Admin API'leri

| Method | Endpoint | Açıklama |
|--------|----------|----------|
| GET | `/admin/sosyal-medya` | Yönetim sayfası |
| GET | `/admin/api/social-media/{id}` | Link detayını getir |
| POST | `/admin/api/social-media/save` | Link kaydet (yeni/güncelle) |
| POST | `/admin/api/social-media/{id}/toggle-status` | Aktif/Pasif yap |
| POST | `/admin/api/social-media/{id}/toggle-header` | Header gösterimini değiştir |
| POST | `/admin/api/social-media/{id}/toggle-footer` | Footer gösterimini değiştir |
| POST | `/admin/api/social-media/{id}/update-order` | Sırayı güncelle |
| DELETE | `/admin/api/social-media/{id}/delete` | Link sil |

### CSRF Koruması

Tüm POST/DELETE işlemleri CSRF token gerektirir:
```javascript
body: `csrf_token=${csrfToken}&...`
```

---

## 🔍 SQL Sorgu Örnekleri

### Aktif Header Linklerini Getir
```sql
SELECT * FROM social_media_links 
WHERE is_active = 1 AND show_in_header = 1 
ORDER BY display_order ASC;
```

### Aktif Footer Linklerini Getir
```sql
SELECT * FROM social_media_links 
WHERE is_active = 1 AND show_in_footer = 1 
ORDER BY display_order ASC;
```

### Platform'a Göre Link Getir
```sql
SELECT * FROM social_media_links 
WHERE platform = 'facebook' AND is_active = 1;
```

### URL'si Olmayan Linkleri Bul
```sql
SELECT * FROM social_media_links 
WHERE url IS NULL OR url = '#';
```

### Sıralamayı Toplu Güncelle
```sql
UPDATE social_media_links SET display_order = 1 WHERE platform = 'facebook';
UPDATE social_media_links SET display_order = 2 WHERE platform = 'twitter';
UPDATE social_media_links SET display_order = 3 WHERE platform = 'instagram';
```

---

## 🎨 Özelleştirme

### Yeni Platform Ekle

Admin panelden veya SQL ile:

```sql
INSERT INTO social_media_links 
(platform, name, icon, url, is_active, display_order, show_in_header, show_in_footer, color) 
VALUES 
('discord', 'Discord', 'fab fa-discord', 'https://discord.gg/yourserver', 1, 10, 1, 1, '#5865F2');
```

### Platform Renkleri

Önerilen hex renk kodları:
```php
'facebook' => '#1877F2'
'twitter' => '#000000'
'instagram' => '#E4405F'
'youtube' => '#FF0000'
'linkedin' => '#0A66C2'
'tiktok' => '#000000'
'telegram' => '#0088CC'
'whatsapp' => '#25D366'
'pinterest' => '#BD081C'
'snapchat' => '#FFFC00'
'reddit' => '#FF4500'
'discord' => '#5865F2'
```

### İkon Sınıfları

Font Awesome 6.x kullanılıyor:
- Brand ikonları: `fab fa-{platform}`
- Solid ikonları: `fas fa-{icon}`

Örnek:
```
fab fa-facebook
fab fa-x-twitter
fab fa-instagram
fab fa-youtube
fab fa-linkedin
fab fa-tiktok
fab fa-telegram
fab fa-whatsapp
fas fa-rss
```

---

## ⚠️ Önemli Notlar

### 1. RSS Linki Özel

- **Platform:** `rss`
- **Silinemez:** Sistem linki olduğu için admin panelden silinemez
- **URL:** `/rss` (internal link)
- **Varsayılan:** Sadece header'da aktif

### 2. URL Kontrolü

- URL boş (`NULL` veya `#`) olan linkler **gösterilmez**
- Internal linkler (başında `http` yok): `url('/rss')` ile işlenir
- External linkler: `target="_blank" rel="noopener noreferrer"` ile açılır

### 3. Performans

- Frontend sorgular cached edilebilir (ileride)
- `active_order` index sorguları hızlandırır
- Sadece aktif linkler sorgulanır

### 4. Güvenlik

- ✅ XSS koruması: `escape()` fonksiyonu her output'ta
- ✅ CSRF koruması: Tüm POST/DELETE işlemlerde
- ✅ SQL Injection: Prepared statements
- ✅ Admin yetkisi: Tüm işlemler admin kontrolünden geçer

---

## 🐛 Sorun Giderme

### Linkler Görünmüyor

1. URL alanının dolu olduğunu kontrol edin
2. `is_active = 1` olduğundan emin olun
3. `show_in_header` veya `show_in_footer` açık olmalı
4. Browser cache'i temizleyin

### İkonlar Görünmüyor

1. Font Awesome yüklendiğinden emin olun
2. İkon class'ının doğru olduğunu kontrol edin (fab/fas)
3. Browser console'da hata var mı kontrol edin

### Sıralama Çalışmıyor

1. `display_order` değerlerinin benzersiz olması gerekmez
2. Küçük sayılar önce gösterilir (0, 1, 2, 3...)
3. Sayfayı yeniledikten sonra kontrol edin

---

## 📚 İlgili Dökümanlar

- `DB_DEGISIKLIKLERI.md` - Tüm veritabanı değişiklikleri
- `README.md` - Genel proje dokümantasyonu
- `code_standards.md` - Kod standartları

---

## 📊 İstatistikler

- **Tablo Boyutu:** ~1KB (8 kayıt)
- **Index Sayısı:** 2 (primary + platform_unique)
- **Varsayılan Kayıt:** 9 platform
- **Cache Süresi:** Yok (cache planlanıyor)

---

**Son Güncelleme:** 15 Aralık 2025  
**Versiyon:** 1.0.0  
**Yazar:** LooMix Team

