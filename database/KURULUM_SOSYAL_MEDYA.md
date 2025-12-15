# Sosyal Medya Yönetim Sistemi - Kurulum Rehberi

## 📋 Kurulum Adımları

### 1. Migration'ı Çalıştır

phpMyAdmin'de veya MySQL CLI'da:

```bash
mysql -u root -p loomix < database/migrations/007_social_media_links.sql
```

**Veya phpMyAdmin:**
1. `http://localhost/phpmyadmin` adresini açın
2. Sol taraftan `loomix` veritabanını seçin
3. Üst menüden **"SQL"** sekmesine tıklayın
4. `database/migrations/007_social_media_links.sql` dosyasını import edin
5. **"Git"** butonuna tıklayın

### 2. Veritabanı Kontrolü

Migration başarılı olduysa şu sorguyu çalıştırın:

```sql
SELECT * FROM social_media_links ORDER BY display_order;
```

**Beklenen Sonuç:** 9 satır (Facebook, Twitter, Instagram, YouTube, LinkedIn, TikTok, Telegram, WhatsApp, RSS)

### 3. Admin Panele Giriş

```
http://localhost/LooMix.Click/admin/sosyal-medya
```

### 4. Sosyal Medya Linklerini Güncelle

Her platform için:

1. **"Düzenle"** butonuna tıklayın
2. **URL** alanına gerçek sosyal medya adresinizi girin:
   - Facebook: `https://facebook.com/yourpage`
   - Twitter: `https://x.com/yourhandle`
   - Instagram: `https://instagram.com/yourprofile`
   - YouTube: `https://youtube.com/@yourchannel`
3. **Header'da Göster / Footer'da Göster** seçeneklerini ayarlayın
4. **Sıra** değerini düzenleyin (küçük sayılar önce gösterilir)
5. **Kaydet** butonuna tıklayın

### 5. Önizleme

Sayfanın altında **"Header Önizleme"** ve **"Footer Önizleme"** kartlarında değişikliklerinizi görebilirsiniz.

### 6. Frontend Kontrolü

Tarayıcıda ana sayfayı açın:

```
http://localhost/LooMix.Click/
```

- **Üst kısımda (Header):** Sosyal medya ikonları görünmeli
- **Alt kısımda (Footer):** Daha büyük sosyal medya ikonları görünmeli

---

## 🎨 Özelleştirme

### Yeni Platform Ekle

Admin panelden **"Yeni Link Ekle"** butonuna tıklayın:

**Örnek: Discord Eklemek**

- **Platform Kodu:** `discord`
- **Görünen İsim:** `Discord`
- **Font Awesome İkon:** `fab fa-discord`
- **URL:** `https://discord.gg/yourserver`
- **Renk:** `#5865F2`
- **Sıra:** `10`
- ✅ **Aktif**
- ✅ **Header'da Göster**
- ✅ **Footer'da Göster**

### İkon Bulmak

Font Awesome 6.x kullanıyoruz:

1. https://fontawesome.com/icons adresini açın
2. Arama yapın (örn: "discord")
3. İkon adını kopyalayın (örn: `fab fa-discord`)
4. Admin paneldeki **İkon** alanına yapıştırın
5. **Önizleme** butonuna tıklayarak kontrol edin

### Renk Kodları

Platform renklerini bulabileceğiniz kaynak:
- https://brandcolors.net/

**Popüler Platform Renkleri:**

```
Facebook:   #1877F2
Twitter/X:  #000000
Instagram:  #E4405F
YouTube:    #FF0000
LinkedIn:   #0A66C2
TikTok:     #000000
Telegram:   #0088CC
WhatsApp:   #25D366
Pinterest:  #BD081C
Discord:    #5865F2
Reddit:     #FF4500
```

---

## 🔧 Sorun Giderme

### Problem: Linkler Görünmüyor

**Çözüm 1:** URL alanı dolu mu kontrol edin
```sql
SELECT platform, name, url FROM social_media_links WHERE is_active = 1;
```

Boş URL'leri güncelleyin:
```sql
UPDATE social_media_links 
SET url = 'https://facebook.com/yourpage' 
WHERE platform = 'facebook';
```

**Çözüm 2:** Aktif mi kontrol edin
```sql
UPDATE social_media_links SET is_active = 1 WHERE platform = 'facebook';
```

**Çözüm 3:** Header/Footer ayarlarını kontrol edin
```sql
UPDATE social_media_links 
SET show_in_header = 1, show_in_footer = 1 
WHERE platform = 'facebook';
```

### Problem: İkonlar Görünmüyor

**Kontrol 1:** Font Awesome yüklü mü?

`templates/layouts/main.php` dosyasında şu satır olmalı:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

**Kontrol 2:** İkon class'ı doğru mu?

Admin panelde **Düzenle** → **İkon** alanını kontrol edin:
- Brand ikonlar: `fab fa-{platform}` (örn: `fab fa-facebook`)
- Solid ikonlar: `fas fa-{icon}` (örn: `fas fa-rss`)

### Problem: Sıralama Çalışmıyor

Sıra değerlerini manuel düzenleyin:

```sql
UPDATE social_media_links SET display_order = 1 WHERE platform = 'facebook';
UPDATE social_media_links SET display_order = 2 WHERE platform = 'twitter';
UPDATE social_media_links SET display_order = 3 WHERE platform = 'instagram';
UPDATE social_media_links SET display_order = 4 WHERE platform = 'youtube';
```

Veya admin panelden **Sıra** input'larını değiştirin ve Enter'a basın.

### Problem: RSS Linki Silinmiyor

RSS linki **sistem linki** olduğu için silinemez. Bu normal bir davranıştır.

Pasif yapmak için:
```sql
UPDATE social_media_links SET is_active = 0 WHERE platform = 'rss';
```

---

## 🚀 Gelişmiş Kullanım

### Template'de Özel Kullanım

**Sadece Facebook Linkini Göster:**
```php
<?= getSocialLink('facebook') ?>
```

**Özel Boyut ve Stil:**
```php
<?= displaySocialLinks('header', 'large', 'my-custom-class') ?>
```

**Tüm Aktif Linkleri Listele:**
```php
<?php
$socialModel = new SocialMedia();
$links = $socialModel->getActive();

foreach ($links as $link) {
    echo '<a href="' . $link['url'] . '">' . $link['name'] . '</a>';
}
?>
```

### Programatik Yönetim

**PHP'den Link Ekle:**
```php
$socialModel = new SocialMedia();
$socialModel->save([
    'platform' => 'threads',
    'name' => 'Threads',
    'icon' => 'fab fa-threads',
    'url' => 'https://threads.net/@yourhandle',
    'is_active' => 1,
    'display_order' => 5,
    'show_in_header' => 1,
    'show_in_footer' => 1,
    'color' => '#000000'
]);
```

---

## 📚 Daha Fazla Bilgi

- **Detaylı Dokümantasyon:** `database/SOCIAL_MEDIA_OPERATIONS.md`
- **Tüm Değişiklikler:** `database/DB_DEGISIKLIKLERI.md`
- **Kod Standartları:** `code_standards.md`

---

## ✅ Kurulum Kontrol Listesi

- [ ] Migration çalıştırıldı (`007_social_media_links.sql`)
- [ ] Veritabanında 9 varsayılan platform var
- [ ] Admin panelde "Sosyal Medya" menüsü görünüyor
- [ ] Her platform için URL güncellendi
- [ ] Header'da sosyal medya ikonları görünüyor
- [ ] Footer'da sosyal medya ikonları görünüyor
- [ ] Tıklanınca doğru sayfaya yönlendiriyor
- [ ] Renklendirme ve sıralama düzgün

---

**Tebrikler! 🎉**  
Sosyal medya yönetim sistemi başarıyla kuruldu!

**Yardıma mı ihtiyacınız var?**  
- Admin panel: `/admin/sosyal-medya`
- Dokümantasyon: `database/SOCIAL_MEDIA_OPERATIONS.md`

