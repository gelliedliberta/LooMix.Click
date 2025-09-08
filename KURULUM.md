# LooMix.Click Kurulum Rehberi

## 🚀 Hızlı Başlangıç

### 1️⃣ **Veritabanı Kurulumu**

#### phpMyAdmin ile:
1. **phpMyAdmin**'i açın (`http://localhost/phpmyadmin`)
2. **"SQL"** sekmesine tıklayın
3. `db_setup.sql` dosyasının içeriğini kopyalayın
4. **"Çalıştır"** butonuna basın

#### Alternatif - Manuel Kurulum:
```sql
-- 1. Veritabanı oluştur
CREATE DATABASE loomix_click CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Kullan
USE loomix_click;

-- 3. Tabloları oluşturmak için migration.sql'i çalıştır
-- 4. Örnek verileri eklemek için sample_data.sql'i çalıştır
```

#### Migration ve Değişiklik Takibi

Tüm veritabanı değişikliklerini `database/` altında sürümlü olarak takip edin ve dokümante edin.

Önerilen yapı:

```
database/
  migrations/
    001_create_initial_tables.sql
    002_add_indexes.sql
  seeds/
    001_seed_admin_user.sql
  DB_DEGISIKLIKLERI.md
```

Örnek uygulama:

```
mysql -u root -p loomix_click < database/migrations/001_create_initial_tables.sql
```

Notlar:
- Her değişiklik için yeni bir migration dosyası oluşturun; mevcut dosyaları geriye dönük değiştirmeyin.
- Üretime çıkmadan önce yedek alın.
- Her değişikliği kısa bir özetle `database/DB_DEGISIKLIKLERI.md` içinde kaydedin.

### 2️⃣ **Konfigürasyon**

`config/config.php` dosyasını kontrol edin:
- ✅ `DB_HOST` = 'localhost'
- ✅ `DB_NAME` = 'u920805771_loomix'  
- ✅ `DB_USER` = 'u920805771_loomix'
- ✅ `DB_PASS` = '' (XAMPP'ta genelde boş)

### 3️⃣ **Test**

1. **Ana Sayfa**: `http://localhost/LooMix.Click/`
2. **Admin Panel**: `http://localhost/LooMix.Click/admin`
3. **Test Sayfası**: `http://localhost/LooMix.Click/test.php`

### 4️⃣ **Admin Giriş**

- **Kullanıcı**: `admin`
- **Şifre**: `admin123`

## 🔧 Sorun Giderme

### Problem: Sayfa açılmıyor
- ✅ XAMPP'ta Apache çalışıyor mu?
- ✅ Proje `C:\xampp\htdocs\LooMix.Click\` dizininde mi?

### Problem: Veritabanı hatası  
- ✅ MySQL çalışıyor mu?
- ✅ `db_setup.sql` çalıştırıldı mı?
- ✅ `config/config.php` doğru mu?

### Problem: 404 hatası
- ✅ `.htaccess` dosyası mevcut mu?
- ✅ Apache `mod_rewrite` aktif mi?

## 📁 Dizin Yapısı

```
LooMix.Click/
├── app/
│   ├── controllers/
│   ├── models/
│   └── core/
├── config/
├── templates/
├── assets/
├── database/
└── includes/
```

## 🎯 İlk Adımlar

1. Ana sayfayı açın: `http://localhost/LooMix.Click/`
2. Admin paneline giriş yapın: `http://localhost/LooMix.Click/admin`
   - **Varsayılan Giriş:** admin / admin123
3. Site ayarlarını yapılandırın
4. İlk kategorileri oluşturun
5. İlk haberinizi ekleyin
6. Reklam alanlarını ayarlayın

## 🛡️ Admin Panel Özellikleri

### 📰 İçerik Yönetimi
- **Haberler**: Tam CRUD işlemleri, TinyMCE editör, resim upload
- **Kategoriler**: Hiyerarşik kategori yapısı, renk ve ikon desteği  
- **Etiketler**: Etiket yönetimi, kullanılmayanları temizleme

### 💰 Reklam & Gelir
- **Reklam Alanları**: Google AdSense, özel kod, banner desteği
- **Gelir Raporları**: Detaylı gelir analizi, grafikler, dışa aktarma

### 👥 Kullanıcı & Sistem  
- **Kullanıcı Yönetimi**: Admin, editör, yazar rolleri
- **İstatistikler**: Detaylı site istatistikleri ve analizler
- **Site Ayarları**: Dinamik konfigürasyon yönetimi

### 🔒 Güvenlik
- CSRF koruması
- Session yönetimi
- Rol bazlı erişim kontrolü
- Dosya upload güvenliği

Başarılar! 🚀
