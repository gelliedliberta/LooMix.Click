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
- Her değişiklik için yeni bir `migrations/00X_*.sql` dosyası oluşturun (şema değişikliği gerektiren durumlarda).
- Canlı ortama çıkmadan önce mutlaka yedek alın.
- Geri alma adımları mümkünse ilgili migration yanında tutulmalıdır.
