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
