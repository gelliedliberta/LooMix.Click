# AdSense Operasyonları - LooMix.Click

## Google AdSense Kurulumu

### 1. Konfigürasyon Ayarları
```php
// config/config.php
define('GOOGLE_ADSENSE_ID', 'ca-pub-3967023544942784');
define('ADS_ENABLED', true);
```

### 2. Reklam Alanları
Sistemde tanımlı reklam alanları:

| Reklam Alanı | Boyut | Konum | Tip | Responsive |
|--------------|-------|--------|-----|------------|
| header_banner | 728x90 | Header | Banner | Evet |
| sidebar_square | 300x250 | Sidebar | Rectangle | Hayır |
| content_inline | 336x280 | İçerik arası | In-Article | Evet |
| footer_banner | 970x250 | Footer | Banner | Evet |
| mobile_banner | 320x50 | Mobil | Banner | Hayır |

### 3. AdSense Script Yerleşimi
AdSense script'i aşağıdaki konumlarda yüklenir:

#### Head Bölümünde (Ana Script):
```html
<!-- templates/layouts/main.php satır 39-42 -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3967023544942784" 
        crossorigin="anonymous"></script>
```

#### Layout'ta Kullanılan Reklam Alanları:
1. **Header Banner**: Ana layout header bölümünde (satır 221)
2. **Footer Banner**: Ana layout footer bölümünde (yeni eklendi)

### 4. Sayfa Bazlı Reklam Kullanımı

#### Ana Sayfa (`templates/home/index.php`):
- Header banner (layout'ta)
- Sidebar square (satır 232)
- Content inline (satır 187) 
- Footer banner (layout'ta)

#### Haber Detay (`templates/news/detail.php`):
- Header banner (layout'ta)
- Content inline (satır 127)
- Sidebar square (satır 266)
- Footer banner (layout'ta)

### 5. AdSense Reklam Kodları (Slot ID'ler)

Gerçek slot ID'leri Google AdSense panelinden alınmalıdır:

```sql
-- Örnek slot ID'ler (gerçek değerlerle değiştirilmeli)
UPDATE ad_zones SET ad_code = 
'<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-3967023544942784" data-ad-slot="GERÇEK_SLOT_ID" data-ad-format="auto" data-full-width-responsive="true"></ins>'
WHERE zone_name = 'header_banner';
```

### 6. Ad Blocker Detection
`assets/js/ad-detection.js` dosyası ile ad blocker tespiti yapılır:

- Sayfa yüklendiğinde ad blocker kontrolü
- Ad blocker tespit edilirse alternatif mesaj gösterir
- Reklam performans tracking'i
- Lazy loading desteği

### 7. Reklam Yönetimi Fonksiyonları

#### `app/helpers/AdManager.php`:
- Veritabanından reklam alanlarını yönetir
- Display rules kontrolü
- A/B test desteği
- Performans tracking

#### `includes/functions.php`:
- `displayAd($zoneName)`: Reklam gösterir
- `lazyAd($zoneName, $height)`: Lazy loading reklam
- `getAdPlaceholder($zoneName)`: Debug placeholder

### 8. Reklam Alanı Ekleme

Yeni reklam alanı eklemek için:

1. **Veritabanına kayıt ekle**:
```sql
INSERT INTO ad_zones (zone_name, zone_description, ad_code, ad_type, position, width, height, is_responsive) 
VALUES ('new_zone', 'Açıklama', 'AdSense Kodu', 'adsense', 'konum', 300, 250, 1);
```

2. **Template'te kullan**:
```php
<?= displayAd('new_zone') ?>
```

3. **CSS stil ekle** (gerekiyorsa):
```css
.ad-zone[data-zone="new_zone"] {
    margin: 20px 0;
    text-align: center;
}
```

### 9. Performans Tracking

Reklam performansı şu şekilde izlenir:
- **Gösterim tracking**: IntersectionObserver ile
- **Tıklama tracking**: Click event ile  
- **API endpoint'leri**: `/api/ads/track-impression`, `/api/ads/track-click`

### 10. Mobil Optimizasyon

Mobil cihazlarda:
- Responsive reklamlar otomatik boyutlanır
- Mobil özel banner reklamları gösterilir
- Touch-friendly reklam alanları

### 11. AdSense Politika Uyumluluğu

- Reklam sayısı makul düzeyde tutulur
- Reklam alanları açıkça belirtilir  
- Kullanıcı deneyimini bozmayacak şekilde yerleştirilir
- Ad blocker mesajları nazik tonda

### 12. Debug Mode

Debug mode aktifken:
- Reklam alanları görünür placeholder'lar gösterir
- AdManager hataları ekranda gösterilir
- Konsol logları aktif olur

### 13. Güncelleme Notları

**Yapılan Değişiklikler (Bugün)**:
- AdSense ID güncellendi: `ca-pub-3967023544942784`
- Script head bölümüne taşındı
- Ad detection sistemi eklendi
- Footer banner reklam alanı eklendi
- Sample data güncellendi
- AdManager entegrasyonu geliştirildi

**Sonraki Adımlar**:
1. Google AdSense panelinden gerçek slot ID'leri alıp güncelleyin
2. Reklam performans tracking API'lerini implement edin
3. A/B test sistemi aktif edin
4. Gelir raporlama sistemini tamamlayın
