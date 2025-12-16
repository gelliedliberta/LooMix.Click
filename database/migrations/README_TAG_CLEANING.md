# Etiket Temizleme Sistemi

Bu sistem, veritabanındaki mevcut etiketleri temizler ve gelecekte eklenecek etiketlerin otomatik olarak temizlenmesini sağlar.

## 🎯 Ne Yapar?

Etiket isimlerinden şu karakterleri kaldırır:
- **Noktalama işaretleri**: `, . ! ? : ; - _ = + ( ) [ ] { } / \`
- **Tırnak işaretleri**: `" '`
- **Özel karakterler**: `@ # $ % ^ & * ~ | < >`
- **Virgül ve benzeri**: `, , , ,`

**Korunan karakterler:**
- ✅ Türkçe karakterler: `ç ğ ı ö ş ü Ç Ğ İ Ö Ş Ü`
- ✅ Latin harfleri: `a-z A-Z`
- ✅ Rakamlar: `0-9`
- ✅ Boşluklar

## 📋 Özellikler

### 1. Otomatik Temizleme
Yeni etiketler eklenirken otomatik olarak temizlenir:
- Admin panelinden etiket eklerken
- Haber eklerken etiket girerken
- API üzerinden etiket oluştururken

### 2. Mevcut Etiketleri Temizleme
Veritabanındaki eski etiketleri temizlemek için migration script'i:

```bash
# Terminal üzerinden (önerilen)
cd c:/xampp/htdocs/LooMix.Click
php database/migrations/clean_tags.php
```

**veya**

```
# Tarayıcı üzerinden (admin girişi gerekli)
http://localhost/LooMix.Click/database/migrations/clean_tags.php
```

## 🔧 Kurulum

### Adım 1: Yedek Alın

```sql
-- Yedek tablo oluştur
CREATE TABLE tags_backup_20250116 AS SELECT * FROM tags;
```

### Adım 2: Script'i Çalıştırın

```bash
php database/migrations/clean_tags.php
```

### Adım 3: Sonuçları Kontrol Edin

Script şu bilgileri gösterecek:
- ✅ Temizlenen etiketler
- ⚠️ Silinen etiketler (boş kalanlar)
- 🔄 Birleştirilen dublike etiketler

## 📊 Örnek Temizlemeler

| Önce | Sonra |
|------|-------|
| `"Teknoloji"` | `Teknoloji` |
| `Yapay Zeka!` | `Yapay Zeka` |
| `Covid-19` | `Covid19` |
| `Spor,` | `Spor` |
| `Sağlık & Yaşam` | `Sağlık Yaşam` |
| `İstanbul'da` | `İstanbulda` |
| `A.B.D.` | `ABD` |
| `@teknoloji` | `Teknoloji` |

## ⚙️ Teknik Detaylar

### cleanTagName() Fonksiyonu

```php
// includes/functions.php
function cleanTagName($tagName) {
    // Türkçe karakterleri koru, özel karakterleri kaldır
    $tagName = preg_replace('/[^a-zA-Z0-9çğıöşüÇĞİÖŞÜ\s]/', '', $tagName);
    
    // Çoklu boşlukları tek boşluğa çevir
    $tagName = preg_replace('/\s+/', ' ', $tagName);
    
    // İlk harfi büyük yap (Türkçe destekli)
    $tagName = mb_convert_case($tagName, MB_CASE_TITLE, 'UTF-8');
    
    return trim($tagName);
}
```

### Otomatik Entegrasyon

**Tag Model** (`app/models/Tag.php`):
- `findOrCreate()` - Yeni etiket oluştururken otomatik temizler
- `syncNewsTagsByNames()` - Haber etiketlerini güncellerken temizler

**Admin Controller** (`app/controllers/AdminController.php`):
- `saveTag()` - Admin panelinden etiket kaydederken temizler

## 🔄 Dublike Yönetimi

Script dublike etiketleri otomatik olarak yönetir:

1. **Temizlendikten sonra aynı olan etiketler** (örn: `"Spor"` ve `Spor!` -> `Spor`)
2. **Haberler otomatik taşınır** - Eski etiketteki haberler yeni etikete aktarılır
3. **Eski etiket silinir** - Gereksiz dublikasyon önlenir

## 📝 Log Çıktısı

```
========================================
Etiket Temizleme Script'i
========================================

Toplam 150 etiket bulundu.

✓ Temizlendi: '"Teknoloji"' -> 'Teknoloji' (Slug: teknoloji)
✓ Temizlendi: 'Yapay Zeka!' -> 'Yapay Zeka' (Slug: yapay-zeka)
⚠ UYARI: '...' etiketi temizlendikten sonra boş kaldı, siliniyor...
⚠ DUBLIKASYON: 'Spor!' -> 'Spor' (ID: 5 ile çakışıyor)
✓ Temizlendi: 'Covid-19' -> 'Covid19' (Slug: covid19)

========================================
ÖZET
========================================
Toplam:          150 etiket
Temizlendi:      87 etiket
Değişmedi:       58 etiket
Silindi:         3 etiket
Dublikasyon:     2 etiket
========================================

✓ İşlem tamamlandı!
```

## 🔙 Geri Dönüş (Rollback)

Eğer bir sorun olursa yedekten geri yükleyin:

```sql
-- Mevcut tabloyu sil
DROP TABLE IF EXISTS tags;

-- Yedekten geri yükle
CREATE TABLE tags AS SELECT * FROM tags_backup_20250116;

-- Primary key'i ekle
ALTER TABLE tags ADD PRIMARY KEY (id);

-- İndeksleri yeniden oluştur
ALTER TABLE tags ADD INDEX idx_slug (slug);
ALTER TABLE tags ADD INDEX idx_is_active (is_active);
```

## ✅ Test

Script'i çalıştırmadan önce test edin:

```sql
-- Temizlenmesi gereken etiketleri görüntüle
SELECT 
    id,
    name as original,
    TRIM(REGEXP_REPLACE(name, '[^a-zA-Z0-9çğıöşüÇĞİÖŞÜ ]', '')) as cleaned
FROM tags
WHERE name REGEXP '[^a-zA-Z0-9çğıöşüÇĞİÖŞÜ ]';
```

## 🚨 Önemli Notlar

1. ⚠️ **Mutlaka yedek alın!** Script veriyi değiştirir.
2. 🔒 **Admin yetkisi gerekli** - Tarayıcıdan çalıştırıyorsanız giriş yapın.
3. 📊 **Log'ları saklayın** - Script çıktısını bir dosyaya kaydedin.
4. 🧪 **Önce test ortamında deneyin** - Üretim ortamında çalıştırmadan önce test edin.

## 🆘 Sorun Giderme

### "Class not found" hatası
```bash
# Doğru dizinde olduğunuzdan emin olun
cd c:/xampp/htdocs/LooMix.Click
php database/migrations/clean_tags.php
```

### "Permission denied" hatası
```bash
# Dosya izinlerini kontrol edin
chmod +x database/migrations/clean_tags.php
```

### Tarayıcıdan çalışmıyor
- Admin olarak giriş yaptığınızdan emin olun
- `ADMIN_SESSION_NAME` sabitinin doğru tanımlandığını kontrol edin

## 📞 Destek

Sorun yaşarsanız:
1. Log çıktısını kaydedin
2. Veritabanı yedeklerini kontrol edin
3. DEBUG_MODE'u aktif edin: `define('DEBUG_MODE', true);`

---

**Son Güncelleme:** 2025-01-16
**Versiyon:** 1.0.0

