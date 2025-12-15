# Veritabanı Değişiklik Günlüğü

Bu dosya, veritabanında yapılan tüm değişiklikleri kronolojik olarak özetler.

## 2025-09-04
- Başlangıç migration dosyaları yapılandırıldı (database/migration.sql → öneri: migrations/001_create_initial_tables.sql)
- Örnek veriler database/sample_data.sql
- Kurulum belgesi güncellendi (KURULUM.md)

## 2025-09-04 (Arama Düzeltmesi)
- `app/models/News.php` içine `searchNews` eklendi: `title`, `summary`, `content` alanlarında güvenli LIKE araması yapar. Joker karakterler (`%`, `_`, `\`) kaçırılır ve `ESCAPE '\\'` kullanılır.
- `app/models/News.php` içine `countSearchResults` eklendi; toplam sonuç sayısını döndürür.
- `app/controllers/HomeController.php` içine `search()` action eklendi; sayfalama (`NEWS_PER_PAGE`) ve meta/canonical ayarları yapıldı.
- `templates/home/search.php` oluşturuldu; arama formu, sonuç listesi, vurgulama ve sayfalama içerir.
- Etki: `/ara?q=...` istekleri yayınlanmış haberlerde hızlı ve güvenli arama yapar; UI hazır.

## 2025-12-15 (İletişim Bilgileri Yönetimi)
- **Özellik**: İletişim sayfasındaki e-posta, telefon, adres ve sosyal medya kullanıcı adları admin panelden yönetilebilir hale getirildi.
- **Değişiklikler**:
  - `app/controllers/AdminController.php` - 8 yeni ayar anahtarı eklendi (contact_*)
  - `includes/functions.php` - 2 yeni helper fonksiyon:
    - `getSetting($key, $default)` - Site ayarlarını getir
    - `getContactInfo()` - Tüm iletişim bilgilerini array olarak döndür
  - `templates/home/contact.php` - Dinamik iletişim bilgileri gösterimi
    - E-posta adresleri veritabanından
    - Telefon ve adres opsiyonel (varsa gösterilir)
    - Sosyal medya linkleri `social_media_links` tablosundan
    - Kullanıcı adları `site_settings` tablosundan
  - `templates/admin/settings/index.php` - "İletişim Bilgileri" sekmesi eklendi
    - E-posta adresleri bölümü
    - İletişim bilgileri (telefon, adres)
    - Sosyal medya kullanıcı adları bölümü
- **Ayar Anahtarları**:
  - `contact_email` - Genel iletişim e-posta
  - `contact_email_editor` - Editör e-posta
  - `contact_phone` - Telefon (opsiyonel)
  - `contact_address` - Adres (opsiyonel)
  - `contact_twitter_handle` - Twitter kullanıcı adı
  - `contact_facebook_page` - Facebook sayfa adı
  - `contact_instagram_handle` - Instagram kullanıcı adı
  - `contact_linkedin_page` - LinkedIn sayfa adı
- **Varsayılan Değerler**: info@loomix.click, editor@loomix.click, @LooMixClick, vb.
- **Dokümantasyon**: `database/CONTACT_INFO_MANAGEMENT.md` - Detaylı kullanım rehberi
- **Etki**: İletişim bilgileri artık hardcoded değil, admin panelden kolayca değiştirilebiliyor. Sosyal medya sistemiyle entegre.

## 2025-12-15 (Reklam Sistemi Optimizasyonu)
- **Problem**: Reklam alanları sabit boyutlarla render ediliyordu, reklam yokken bile boşluk bırakıyordu.
- **Çözüm**: Dinamik boyutlandırma sistemi oluşturuldu.
- **Değişiklikler**:
  - `app/helpers/AdManager.php` - Sabit boyut ayarları kaldırıldı, responsive AdSense zorunlu hale getirildi
  - `assets/css/style.css` - `.ad-zone` min-height kaldırıldı, dinamik yapı eklendi
  - Template'ler - Reklam yoksa container bile oluşturulmuyor (conditional rendering)
    - `templates/layouts/main.php` - Header/Footer ad container'ları
    - `templates/home/index.php` - Content/Sidebar ads
    - `templates/news/detail.php` - Content/Sidebar ads
    - `templates/category/show.php` - Sidebar ads
    - `templates/tag/show.php` - Sidebar ads
- **Özellikler**:
  - ✅ Reklam yoksa hiç div oluşturulmaz (0px boşluk)
  - ✅ Reklam varsa dinamik boyutlanır (AdSense responsive)
  - ✅ Debug mode'da minimal placeholder (50px)
  - ✅ Production'da temiz, boşluksuz görünüm
  - ✅ CLS (Cumulative Layout Shift) optimizasyonu
  - ✅ Performans artışı (gereksiz DOM elementleri yok)
- **Dokümantasyon**: `database/AD_SYSTEM_OPTIMIZATION.md` - Detaylı karşılaştırma ve test rehberi
- **Etki**: Sayfa daha hızlı yükleniyor, reklam yokken boşluklar gözükmüyor, kullanıcı deneyimi iyileşti.

## 2025-12-15 (Sosyal Medya Yönetim Sistemi)
- **Özellik**: Admin panelden sosyal medya linklerinin yönetimi için tam kapsamlı sistem eklendi.
- **Migration**: `database/migrations/007_social_media_links.sql`
- **Tablo**: `social_media_links` - Platform bilgileri, URL'ler, görünürlük ayarları
  - Kolonlar: platform (unique), name, icon, url, is_active, display_order, show_in_header, show_in_footer, color
  - 9 varsayılan platform (Facebook, Twitter/X, Instagram, YouTube, LinkedIn, TikTok, Telegram, WhatsApp, RSS)
- **Model**: `app/models/SocialMedia.php` - CRUD işlemleri, toggle fonksiyonları, platform yönetimi
- **Controller**: `app/controllers/AdminController.php` - 8 yeni admin API endpoint'i
  - `/admin/sosyal-medya` - Yönetim sayfası
  - `/admin/api/social-media/*` - CRUD ve toggle API'leri
- **Template**: `templates/admin/social-media/index.php` - Modern yönetim arayüzü, canlı önizleme
- **Helper**: `includes/functions.php` 
  - `displaySocialLinks($position, $size, $class)` - Header/footer için dinamik link gösterimi
  - `getSocialLink($platform)` - Tek platform linki
- **Frontend**: `templates/layouts/main.php` güncellendi
  - Header ve footer'daki static linkler dynamic helper fonksiyonlarıyla değiştirildi
- **Admin Menu**: Sosyal Medya menüsü eklendi
- **Özellikler**:
  - ✅ Tek tıkla aktif/pasif (genel, header, footer ayrı ayrı)
  - ✅ Sıralama düzenleme (drag-drop benzeri)
  - ✅ URL, ikon, renk özelleştirmesi
  - ✅ Canlı önizleme (header ve footer)
  - ✅ RSS linki korumalı (silinemez)
  - ✅ CSRF korumalı tüm işlemler
- **Dokümantasyon**: `database/SOCIAL_MEDIA_OPERATIONS.md` - Detaylı kullanım kılavuzu
- **Etki**: Sosyal medya linkleri artık hardcoded değil, admin panelden kolayca yönetilebiliyor. Header ve footer'da ayrı kontrol.

## Notlar
## 2025-10-11 (Zaman Dilimi Senkronizasyonu)
- Uygulama zaman dilimi yapılandırması eklendi: `APP_TIMEZONE` (varsayılan: Europe/Istanbul)
- PHP `date_default_timezone_set(APP_TIMEZONE)` ile ayarlandı (`index.php`)
- MySQL session `time_zone`, PDO bağlantısı kurulduktan sonra dinamik offset ile ayarlanıyor (ör. `+03:00`): `SET time_zone = '+03:00'`
- Etki: `NOW()`, `CURDATE()` gibi MySQL fonksiyonları uygulama zamanı ile tutarlı çalışır; `publish_date <= NOW()` filtreleri doğru sonuç verir
- Her değişiklik için yeni bir `migrations/00X_*.sql` dosyası oluşturun (şema değişikliği gerektiren durumlarda).
- Canlı ortama çıkmadan önce mutlaka yedek alın.
- Geri alma adımları mümkünse ilgili migration yanında tutulmalıdır.

## 2025-10-13 (Kategori Menü Görünürlüğü)
- Kategorilere `show_in_menu TINYINT(1) NOT NULL DEFAULT 1` alanı eklendi.
- Migration: `database/migrations/005_add_show_in_menu_to_categories.sql`
- Model: `Category::getActiveCategories($parentId, $onlyMenu)` ve `getMainCategories($onlyMenu=true)` güncellendi, menüde gösterilecek kategoriler `show_in_menu=1` ile filtrelenir.
- Admin API: `POST /admin/api/categories/{id}/toggle-menu` (body: `{ show_in_menu, csrf_token }`).
- Admin UI: Kategori listesinde “Menüde Göster” anahtarı ve modalda checkbox eklendi.
- Etki: Ana navigasyonda sadece admin tarafından işaretlenen kategoriler görüntülenir; masaüstünde mobil offcanvas tekrarı giderildi.

## 2025-11-02 (HY093 Placeholder Düzeltmeleri)
- PDO native prepared statements (ATTR_EMULATE_PREPARES=false) kullanımı nedeniyle aynı isimli placeholder'ların bir sorguda tekrar kullanımı HY093 hatasına sebep oluyordu.
- Admin Haberler Arama: `app/controllers/AdminController.php@news()` içinde `:search` tekrarları `:search1..:search4` olarak benzersizleştirildi.
- Etiket kullanım sayısı: `app/models/Tag.php@updateUsageCount()` sorgusundaki `:tag_id` tekrarları `:tag_id1` ve `:tag_id2` olarak ayrıldı.
- Etki: Admin panelde arama ve haber kaydetme/güncelleme akışlarında `SQLSTATE[HY093]: Invalid parameter number` hatası giderildi. Veritabanı şeması değişmedi.

## 2025-12-12 (Breaking News Performans Optimizasyonu)
- **Problem**: Son dakika haberleri (breaking news) sayfa yüklendikten sonra geç görünüyordu.
- **Çözüm 1 - Yükleme Sırası**: `HomeController::index()` metodunda breaking news sorgusu en başa alındı (header'da ilk gösterildiği için öncelikli).
- **Çözüm 2 - SQL Optimizasyonu**: `News::getBreakingNews()` sorgusu sadece gerekli alanları çekecek şekilde optimize edildi (id, title, slug). Gereksiz JOIN kaldırıldı.
- **Çözüm 3 - Database Index**: Breaking news sorguları için özel composite index eklendi.
  - Migration: `database/migrations/006_breaking_news_index.sql`
  - Index: `idx_news_breaking (is_breaking, status, publish_date DESC)`
  - Bu index, `WHERE is_breaking = 1 AND status = 'published' AND publish_date <= NOW() ORDER BY publish_date DESC` sorgularını hızlandırır.
- **Etki**: Breaking news çubuğu sayfa yüklendiğinde hemen görünür, sorgu performansı artırıldı.

## 2025-12-12 (Breaking News Animasyon Optimizasyonu)
- **Problem**: Breaking news ticker'ların (hem header hem ana içerik) kayan animasyonu 8-10 saniye geç başlıyordu.
- **Kök Sebep**: 
  - CSS animasyonu `translateX(100%)` ile başlıyordu (ekran dışından giriş)
  - İçerik duplicate edilmiyordu (seamless loop yoktu)
  - JavaScript trigger eksikti
- **Çözüm 1 - Header Breaking News** (`templates/layouts/main.php`):
  - CSS animasyon başlangıcı: `translateX(100%)` → `translate3d(0, 0, 0)` (instant başlar)
  - GPU acceleration: `will-change: transform` eklendi
  - `padding-left: 100%` ile off-screen başlangıç (gecikme olmadan)
- **Çözüm 2 - Ana İçerik Breaking News** (`assets/css/style.css`):
  - `.breaking-scroll` için özel animasyon kuralları
  - Yeni keyframe: `@keyframes scroll-breaking` - 30 saniye seamless loop
  - GPU acceleration için `will-change: transform` ve `translate3d`
- **Çözüm 3 - JavaScript Optimizasyonu** (`assets/js/app.js`):
  - `initBreakingNewsTicker()` fonksiyonu güncellendi
  - Hem `.breaking-scroll` hem `.marquee` elementlerini destekler
  - DOMContentLoaded'da ilk sırada çalışır (instant başlatma)
  - Breaking news items'ları duplicate eder (seamless loop)
  - `requestAnimationFrame` ile performanslı animasyon tetikleme
- **Teknik Detaylar**:
  - Animasyon süresi: 39 saniye (desktop), 30 saniye (mobil) - %30 daha yavaş
  - Transform: `translate3d` (hardware acceleration)
  - Animation timing: linear infinite
  - Seamless loop için içerik duplicate edilir
  - Gecikme: **0 saniye** (instant başlar)
  - Hover pause: Mouse ile üzerine gelince animasyon durur
- **Etki**: Tüm breaking news ticker'lar sayfa yüklendiğinde **anında** kaymaya başlıyor, smooth ve performanslı. Kullanıcı mouse ile üzerine gelince okumak için duraklatabilir.

## 2025-12-12 (Breaking News UX İyileştirmesi & Cleanup)
- **İstenen Özellikler**:
  1. Mouse ile üzerine gelince animasyon dursun
  2. Animasyon %30 daha yavaş olsun
  3. Ana içerik breaking news alanını kaldır (sadece header kalsın)
- **Uygulama**:
  - Header breaking news için `animation-play-state: paused` (CSS + JavaScript çift katman)
  - Animasyon süresi: 50 saniye (desktop), 30 saniye (mobil)
  - Ana içerik breaking news HTML bloğu tamamen kaldırıldı
- **Teknik Detaylar**:
  - Header: `templates/layouts/main.php` - 50s, hover pause, link effects
  - Seamless loop: İçerik duplicate edilerek kesintisiz döngü
  - GPU acceleration: `translate3d`, `will-change: transform`
- **CSS Değişiklikleri**:
  - Çoklu selector: Hem container hem element hover'ında pause
  - `!important` flag: Override garantisi
  - `cursor: pointer`: Görsel ipucu
  - Link hover: `opacity: 0.85` + underline
- **Kaldırılanlar**:
  - `templates/home/index.php`: Ana içerik breaking news HTML bloğu (24 satır)
  - `assets/js/app.js`: Ana içerik breaking news JavaScript kodu temizlendi
  - Gereksiz CSS kodları kaldı (gelecekte temizlenebilir)
- **Etki**: Tek breaking news alanı (header) - daha temiz UI, daha az kod, performans iyileştirmesi. Kullanıcı mouse ile durdurabilir.
