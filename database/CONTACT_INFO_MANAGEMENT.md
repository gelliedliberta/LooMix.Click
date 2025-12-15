# İletişim Bilgileri Yönetim Sistemi - 15 Aralık 2025

## 📋 Özet

İletişim sayfasındaki e-posta adresleri, telefon, adres ve sosyal medya kullanıcı adları artık admin panelden yönetilebilir.

**Problem:** İletişim bilgileri template'de hardcoded olarak yazılıydı, değiştirmek için kod düzenlemesi gerekiyordu.  
**Çözüm:** Tüm iletişim bilgileri `site_settings` tablosunda saklanıyor ve admin panelden düzenlenebiliyor.

---

## 🔧 Yapılan Değişiklikler

### 1. Admin Controller Güncellemesi (`app/controllers/AdminController.php`)

#### Yeni Ayar Anahtarları Eklendi

```php
$allowedKeys = [
    // ... mevcut anahtarlar
    // İletişim bilgileri
    'contact_email',               // Genel iletişim e-posta
    'contact_email_editor',        // Editör e-posta
    'contact_phone',               // Telefon (opsiyonel)
    'contact_address',             // Adres (opsiyonel)
    'contact_twitter_handle',      // Twitter kullanıcı adı
    'contact_facebook_page',       // Facebook sayfa adı
    'contact_instagram_handle',    // Instagram kullanıcı adı
    'contact_linkedin_page'        // LinkedIn sayfa adı
];
```

### 2. Helper Fonksiyonlar (`includes/functions.php`)

#### getSetting() - Genel Ayar Getir

```php
function getSetting($key, $default = null)
```

- Site ayarlarını veritabanından çeker
- Static cache kullanır (performans için)
- Ayar yoksa varsayılan değer döner

**Kullanım:**
```php
$email = getSetting('contact_email', 'info@loomix.click');
```

#### getContactInfo() - İletişim Bilgileri Getir

```php
function getContactInfo()
```

- Tüm iletişim bilgilerini array olarak döner
- Varsayılan değerler içerir
- Template'lerde kolayca kullanılabilir

**Dönen Değerler:**
```php
[
    'email' => 'info@loomix.click',
    'email_editor' => 'editor@loomix.click',
    'phone' => '+90 XXX XXX XX XX',
    'address' => 'İstanbul, Türkiye',
    'twitter_handle' => '@LooMixClick',
    'facebook_page' => 'LooMix.Click',
    'instagram_handle' => '@loomixclick',
    'linkedin_page' => 'LooMix Click'
]
```

### 3. İletişim Sayfası Güncellemesi (`templates/home/contact.php`)

#### Öncesi (Hardcoded)

```php
<a href="mailto:info@loomix.click">
    info@loomix.click
</a>
```

#### Sonrası (Dinamik)

```php
<?php
$contactInfo = getContactInfo();
$socialMedia = new SocialMedia();
$socialLinks = $socialMedia->getActive();
?>

<a href="mailto:<?= escape($contactInfo['email']) ?>">
    <?= escape($contactInfo['email']) ?>
</a>
```

**Özellikler:**
- ✅ E-posta adresleri dinamik
- ✅ Telefon ve adres opsiyonel (varsa gösterilir)
- ✅ Sosyal medya linkleri veritabanından
- ✅ Sosyal medya kullanıcı adları ayarlardan
- ✅ Platform ikonları renkli
- ✅ URL yoksa link gösterilmez

### 4. Admin Settings Sayfası (`templates/admin/settings/index.php`)

#### Yeni Sekme: İletişim Bilgileri

**3 Alt Bölüm:**

1. **E-posta Adresleri**
   - Genel iletişim e-posta
   - Editör e-posta

2. **İletişim Bilgileri**
   - Telefon (opsiyonel)
   - Adres (opsiyonel)

3. **Sosyal Medya Kullanıcı Adları**
   - Twitter: @kullaniciadi
   - Facebook: Sayfa Adı
   - Instagram: @kullaniciadi
   - LinkedIn: Sayfa Adı

**Not:** URL'ler ayrı olarak Sosyal Medya Yönetimi sayfasından düzenlenir.

---

## 🎯 Kullanım

### Admin Panelden Ayarlama

1. **Admin Panel → Ayarlar**
   ```
   http://localhost/LooMix.Click/admin/ayarlar
   ```

2. **İletişim Bilgileri** sekmesine tıklayın

3. Bilgileri doldurun:
   - ✅ **Genel E-posta:** info@yoursite.com
   - ✅ **Editör E-posta:** editor@yoursite.com
   - ⚪ **Telefon:** +90 XXX XXX XX XX (opsiyonel)
   - ⚪ **Adres:** İstanbul, Türkiye (opsiyonel)
   - ✅ **Twitter:** @yourhandle
   - ✅ **Facebook:** Your Page Name
   - ✅ **Instagram:** @yourhandle
   - ✅ **LinkedIn:** Your Company Name

4. **Kaydet** butonuna tıklayın

5. İletişim sayfasını kontrol edin:
   ```
   http://localhost/LooMix.Click/iletisim
   ```

### Template'de Kullanım

#### Tek Bir Bilgi Getir

```php
$email = getSetting('contact_email', 'default@email.com');
echo $email;
```

#### Tüm İletişim Bilgilerini Getir

```php
$contact = getContactInfo();
echo $contact['email'];
echo $contact['phone'];
echo $contact['address'];
```

#### Conditional Rendering

```php
<?php if (!empty($contact['phone'])): ?>
    <a href="tel:<?= escape($contact['phone']) ?>">
        <?= escape($contact['phone']) ?>
    </a>
<?php endif; ?>
```

---

## 📊 Veritabanı

### site_settings Tablosu

İletişim bilgileri bu tabloda saklanır:

```sql
SELECT * FROM site_settings 
WHERE setting_key LIKE 'contact_%';
```

**Örnek Kayıtlar:**

| setting_key | setting_value |
|-------------|---------------|
| contact_email | info@loomix.click |
| contact_email_editor | editor@loomix.click |
| contact_phone | +90 XXX XXX XX XX |
| contact_address | İstanbul, Türkiye |
| contact_twitter_handle | @LooMixClick |
| contact_facebook_page | LooMix.Click |
| contact_instagram_handle | @loomixclick |
| contact_linkedin_page | LooMix Click |

### Manuel SQL Ekleme

```sql
-- E-posta adresleri
INSERT INTO site_settings (setting_key, setting_value, category) 
VALUES 
('contact_email', 'info@yoursite.com', 'contact'),
('contact_email_editor', 'editor@yoursite.com', 'contact')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Telefon ve adres (opsiyonel)
INSERT INTO site_settings (setting_key, setting_value, category) 
VALUES 
('contact_phone', '+90 555 123 45 67', 'contact'),
('contact_address', 'İstanbul, Türkiye', 'contact')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Sosyal medya kullanıcı adları
INSERT INTO site_settings (setting_key, setting_value, category) 
VALUES 
('contact_twitter_handle', '@yourhandle', 'contact'),
('contact_facebook_page', 'Your Page', 'contact'),
('contact_instagram_handle', '@yourhandle', 'contact'),
('contact_linkedin_page', 'Your Company', 'contact')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
```

---

## 🔗 Sosyal Medya Entegrasyonu

### İletişim Sayfasında Gösterim

İletişim sayfası:
1. **Sosyal medya linklerini** `social_media_links` tablosundan çeker
2. **Kullanıcı adlarını** `site_settings` tablosundan çeker
3. İkisini birleştirerek gösterir

**Örnek:**

```php
// URL: social_media_links tablosundan
$twitter_url = 'https://x.com/yourhandle';

// Kullanıcı adı: site_settings tablosundan
$twitter_handle = '@LooMixClick';

// Gösterim:
<a href="https://x.com/yourhandle">
    <i class="fab fa-twitter"></i>
    @LooMixClick
</a>
```

### URL vs Kullanıcı Adı

| Platform | URL (Sosyal Medya Yönetimi) | Kullanıcı Adı (İletişim Ayarları) |
|----------|------------------------------|-------------------------------------|
| Twitter | https://x.com/loomixclick | @LooMixClick |
| Facebook | https://facebook.com/loomixclick | LooMix.Click |
| Instagram | https://instagram.com/loomixclick | @loomixclick |
| LinkedIn | https://linkedin.com/company/loomix | LooMix Click |

---

## 🎨 İletişim Sayfası Özellikleri

### E-posta Kartı

```
┌─────────────────────────────────┐
│ 📧 E-posta                      │
├─────────────────────────────────┤
│ Genel sorular için:             │
│ info@loomix.click               │
│                                  │
│ Editör ekibi için:              │
│ editor@loomix.click             │
│                                  │
│ Telefon: (varsa gösterilir)     │
│ +90 XXX XXX XX XX               │
│                                  │
│ Adres: (varsa gösterilir)       │
│ İstanbul, Türkiye               │
└─────────────────────────────────┘
```

### Sosyal Medya Kartı

```
┌─────────────────────────────────┐
│ 🔗 Sosyal Medya                 │
├─────────────────────────────────┤
│ 🐦 @LooMixClick                 │
│ 👍 LooMix.Click                 │
│ 📷 @loomixclick                 │
│ 💼 LooMix Click                 │
└─────────────────────────────────┘
```

**Özellikler:**
- Renkli platform ikonları
- Sadece aktif sosyal medyalar gösterilir
- URL yoksa link oluşturulmaz
- Kullanıcı adları ayarlardan çekilir

---

## ⚙️ Varsayılan Değerler

Ayar yoksa kullanılacak varsayılan değerler:

```php
'contact_email' => 'info@loomix.click'
'contact_email_editor' => 'editor@loomix.click'
'contact_phone' => '' // Boş
'contact_address' => '' // Boş
'contact_twitter_handle' => '@LooMixClick'
'contact_facebook_page' => 'LooMix.Click'
'contact_instagram_handle' => '@loomixclick'
'contact_linkedin_page' => 'LooMix Click'
```

---

## 🔒 Güvenlik

- ✅ **CSRF Koruması:** Ayar kaydetme işlemlerinde
- ✅ **XSS Koruması:** `escape()` fonksiyonu ile her output
- ✅ **SQL Injection:** Prepared statements
- ✅ **Admin Yetkisi:** Sadece adminler düzenleyebilir
- ✅ **E-posta Validasyonu:** HTML5 type="email"
- ✅ **URL Validasyonu:** Sosyal medya linklerinde

---

## 🐛 Sorun Giderme

### Problem: İletişim bilgileri görünmüyor

**Kontrol 1:** Veritabanında kayıt var mı?
```sql
SELECT * FROM site_settings WHERE setting_key LIKE 'contact_%';
```

**Çözüm 1:** Manuel ekleyin:
```sql
INSERT INTO site_settings (setting_key, setting_value) 
VALUES ('contact_email', 'info@yoursite.com');
```

**Kontrol 2:** `is_active` açık mı?
```sql
UPDATE site_settings 
SET is_active = 1 
WHERE setting_key LIKE 'contact_%';
```

### Problem: Sosyal medya linkleri görünmüyor

**Kontrol:** Sosyal medya tablosunda kayıt var mı ve URL dolu mu?
```sql
SELECT platform, name, url, is_active 
FROM social_media_links 
WHERE is_active = 1 AND url IS NOT NULL AND url != '';
```

**Çözüm:** Sosyal Medya Yönetimi'nden URL ekleyin:
```
/admin/sosyal-medya
```

### Problem: Kullanıcı adları gösterilmiyor

**Çözüm:** Admin panelden kullanıcı adlarını girin:
```
Admin → Ayarlar → İletişim Bilgileri → Sosyal Medya Kullanıcı Adları
```

---

## 📚 İlgili Dosyalar

- **Controller:** `app/controllers/AdminController.php`
- **Helper:** `includes/functions.php`
- **Model:** `app/models/SocialMedia.php`
- **Template (Frontend):** `templates/home/contact.php`
- **Template (Admin):** `templates/admin/settings/index.php`
- **Database:** `site_settings` tablosu, `social_media_links` tablosu

---

## ✅ Sonuç

İletişim bilgileri yönetimi artık:
- ✅ **Admin panelden** düzenlenebilir
- ✅ **Veritabanında** saklanıyor
- ✅ **Dinamik** olarak gösteriliyor
- ✅ **Opsiyonel** alanlar destekleniyor
- ✅ **Sosyal medya** ile entegre
- ✅ **Güvenli** (CSRF, XSS korumalı)

**Önceki durum:** Hardcoded template içinde  
**Yeni durum:** Admin panelden tek tıkla düzenleme

---

**Tarih:** 15 Aralık 2025  
**Versiyon:** 1.0.0  
**Test Durumu:** ✅ Başarılı

