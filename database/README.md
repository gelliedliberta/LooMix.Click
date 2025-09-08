# LooMix.Click Veritabanı Dokümantasyonu

## Veritabanı Yapısı

Bu dokümanda LooMix.Click haber sitesinin veritabanı yapısı ve işlemleri açıklanmaktadır.

## Kurulum

### 1. Veritabanını Oluşturma
```sql
-- migration.sql dosyasını çalıştırın
mysql -u root -p < database/migration.sql
```

### 2. Örnek Verileri Yükleme
```sql
-- sample_data.sql dosyasını çalıştırın
mysql -u root -p < database/sample_data.sql
```

## Tablo Yapıları

### 1. categories (Kategoriler)
Haber kategorilerini yönetir. Hiyerarşik yapı destekler.

**Önemli Alanlar:**
- `slug`: SEO dostu URL
- `parent_id`: Alt kategori desteği
- `meta_title`, `meta_description`: SEO bilgileri

### 2. news (Haberler)
Ana haber tablosu. Tüm haber bilgileri burada saklanır.

**Önemli Alanlar:**
- `slug`: SEO dostu URL (unique)
- `content`: HTML formatında haber içeriği
- `status`: draft/published/archived durumları
- `is_featured`: Ana sayfada öne çıkarma
- `is_breaking`: Son dakika haberi
- `structured_data`: JSON-LD formatında yapısal veri

### 3. news_views (Haber Görüntülenmeleri)
Detaylı analitik için haber görüntülenme kayıtları.

**Özellikler:**
- IP bazlı takip
- Referrer bilgisi
- Günlük bazda gruplama

### 4. tags (Etiketler)
Haber etiketlerini yönetir.

**Önemli Alanlar:**
- `usage_count`: Kaç haberde kullanıldığını takip eder
- `slug`: SEO dostu URL

### 5. admin_users (Admin Kullanıcıları)
Yönetim paneli kullanıcıları.

**Roller:**
- `admin`: Tam yetki
- `editor`: Haber yönetimi
- `author`: Sadece haber yazma

### 6. site_settings (Site Ayarları)
Dinamik site konfigürasyonu.

**Kategoriler:**
- `general`: Genel ayarlar
- `social`: Sosyal medya
- `analytics`: Analitik kodları
- `ads`: Reklam ayarları

### 7. seo_meta (SEO Meta Bilgileri)
Özel sayfalar için SEO ayarları.

**Sayfa Türleri:**
- `page`: Ana sayfalar
- `category`: Kategori sayfaları
- `tag`: Etiket sayfaları
- `custom`: Özel sayfalar

### 8. ad_zones (Reklam Alanları)
Reklam yerleşimlerini yönetir.

**Reklam Türleri:**
- `adsense`: Google AdSense
- `custom`: Özel HTML/JS kodu
- `banner`: Banner reklamları

## Performans İndeksleri

### Kritik İndeksler
- `news.slug`: Haber URL'leri için
- `news.status + publish_date`: Yayınlanan haberleri listelerken
- `categories.slug`: Kategori URL'leri için
- `news_views.news_id + view_date`: Analitik sorgular için

### Full-text Search
```sql
-- Haberlerde arama yapmak için
SELECT * FROM news 
WHERE MATCH(title, summary, content) AGAINST('arama terimi' IN NATURAL LANGUAGE MODE)
AND status = 'published';
```

## Önemli Sorgular

### 1. Popüler Haberler (Son 7 gün)
```sql
SELECT n.*, COUNT(nv.id) as view_count
FROM news n
LEFT JOIN news_views nv ON n.id = nv.news_id AND nv.view_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
WHERE n.status = 'published'
GROUP BY n.id
ORDER BY view_count DESC
LIMIT 10;
```

### 2. Kategori Bazında Haber Sayıları
```sql
SELECT c.name, c.slug, COUNT(n.id) as news_count
FROM categories c
LEFT JOIN news n ON c.id = n.category_id AND n.status = 'published'
GROUP BY c.id
ORDER BY news_count DESC;
```

### 3. En Çok Kullanılan Etiketler
```sql
SELECT t.name, t.slug, COUNT(nt.id) as usage_count
FROM tags t
INNER JOIN news_tags nt ON t.id = nt.tag_id
INNER JOIN news n ON nt.news_id = n.id AND n.status = 'published'
GROUP BY t.id
ORDER BY usage_count DESC
LIMIT 20;
```

## Bakım İşlemleri

### 1. Eski Görüntülenme Kayıtlarını Temizleme
```sql
-- 1 yıldan eski kayıtları sil
DELETE FROM news_views 
WHERE view_date < DATE_SUB(CURDATE(), INTERVAL 1 YEAR);
```

### 2. Etiket Kullanım Sayılarını Güncelleme
```sql
UPDATE tags t 
SET usage_count = (
    SELECT COUNT(*) 
    FROM news_tags nt 
    INNER JOIN news n ON nt.news_id = n.id 
    WHERE nt.tag_id = t.id AND n.status = 'published'
);
```

### 3. Haber Görüntülenme Sayılarını Güncelleme
```sql
UPDATE news n 
SET view_count = (
    SELECT COUNT(*) 
    FROM news_views nv 
    WHERE nv.news_id = n.id
);
```

## Yedekleme

### Günlük Yedekleme Scripti
```bash
#!/bin/bash
DATE=$(date +%Y%m%d)
mysqldump -u root -p loomix_click > backup_${DATE}.sql
gzip backup_${DATE}.sql
```

### Sadece Yapısal Yedekleme
```bash
mysqldump -u root -p --no-data loomix_click > structure_backup.sql
```

## Güvenlik Notları

1. **SQL Injection**: Tüm sorgularda prepared statement kullanın
2. **Admin Şifreleri**: Bcrypt ile hashlenmiş şifreler kullanın
3. **Session Güvenliği**: Admin oturumları için güvenli session yönetimi
4. **Backup Güvenliği**: Yedek dosyalarını güvenli yerlerde saklayın

## Sürüm Bilgileri

- **Veritabanı Versiyonu**: 1.0.0
- **MySQL Minimum Versiyon**: 5.7
- **Karakter Seti**: utf8mb4_unicode_ci
- **Motor**: InnoDB (Foreign Key desteği için)

---

**Son Güncelleme**: 2024-01-01
**Geliştiren**: LooMix Team
