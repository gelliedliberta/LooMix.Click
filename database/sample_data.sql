-- LooMix.Click Sample Data
-- Örnek veriler ile sitenin test edilmesi için

USE u920805771_loomix;

-- Kategoriler
INSERT INTO categories (name, slug, description, color, icon, sort_order) VALUES
('Teknoloji', 'teknoloji', 'En güncel teknoloji haberleri ve yenilikleri', '#007bff', 'fa-laptop', 1),
('Spor', 'spor', 'Futbol, basketbol ve diğer spor dallarından haberler', '#28a745', 'fa-futbol', 2),
('Ekonomi', 'ekonomi', 'Ekonomi, finans ve iş dünyasından haberler', '#ffc107', 'fa-chart-line', 3),
('Sağlık', 'saglik', 'Sağlık, tıp ve yaşam haberleri', '#17a2b8', 'fa-heartbeat', 4),
('Siyaset', 'siyaset', 'Siyasi gelişmeler ve gündem haberleri', '#dc3545', 'fa-landmark', 5),
('Kültür-Sanat', 'kultur-sanat', 'Sanat, kültür ve etkinlik haberleri', '#6f42c1', 'fa-palette', 6),
('Dünya', 'dunya', 'Dünyadan haberler ve uluslararası gelişmeler', '#fd7e14', 'fa-globe', 7),
('Gündem', 'gundem', 'Güncel olaylar ve genel haberler', '#20c997', 'fa-newspaper', 8);

-- Alt kategoriler
INSERT INTO categories (name, slug, description, parent_id, sort_order) VALUES
('Yapay Zeka', 'yapay-zeka', 'AI ve makine öğrenmesi haberleri', 1, 1),
('Mobil', 'mobil', 'Smartphone ve mobil teknolojiler', 1, 2),
('Futbol', 'futbol', 'Futbol haberleri ve maç sonuçları', 2, 1),
('Basketbol', 'basketbol', 'Basketbol haberleri ve sonuçları', 2, 2);

-- Admin kullanıcı (şifre: admin123)
INSERT INTO admin_users (username, email, password, full_name, role) VALUES
('admin', 'admin@loomix.click', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Site Yöneticisi', 'admin');

-- Site ayarları
INSERT INTO site_settings (setting_key, setting_value, setting_type, category, description) VALUES
('site_maintenance', '0', 'boolean', 'general', 'Site bakım modu'),
('news_per_page', '12', 'number', 'general', 'Sayfa başına haber sayısı'),
('featured_news_count', '6', 'number', 'homepage', 'Ana sayfada öne çıkan haber sayısı'),
('breaking_news_enabled', '1', 'boolean', 'general', 'Son dakika haberlerini aktif et'),
('comments_enabled', '1', 'boolean', 'general', 'Yorumları aktif et'),
('social_facebook', 'https://facebook.com/loomix.click', 'string', 'social', 'Facebook sayfası'),
('social_twitter', 'https://twitter.com/loomix_click', 'string', 'social', 'Twitter hesabı'),
('social_instagram', 'https://instagram.com/loomix.click', 'string', 'social', 'Instagram hesabı'),
('google_analytics_id', 'G-XXXXXXXXXX', 'string', 'analytics', 'Google Analytics ID'),
('google_adsense_id', 'ca-pub-xxxxxxxxxxxxxxxx', 'string', 'ads', 'Google AdSense ID');

-- Etiketler
INSERT INTO tags (name, slug, description, color) VALUES
('Son Dakika', 'son-dakika', 'Acil ve önemli haberler', '#dc3545'),
('Özel Haber', 'ozel-haber', 'Özel röportaj ve araştırmalar', '#007bff'),
('Video', 'video', 'Video içerikli haberler', '#28a745'),
('Galeri', 'galeri', 'Fotoğraf galerisi içeren haberler', '#ffc107'),
('Analiz', 'analiz', 'Derinlemesine analiz yazıları', '#6f42c1'),
('iPhone', 'iphone', 'iPhone ile ilgili haberler', '#007bff'),
('Samsung', 'samsung', 'Samsung haberleri', '#1976d2'),
('Google', 'google', 'Google haberleri', '#4285f4'),
('Microsoft', 'microsoft', 'Microsoft haberleri', '#00a1f1'),
('Tesla', 'tesla', 'Tesla haberleri', '#cc0000');

-- Örnek haberler
INSERT INTO news (category_id, title, slug, summary, content, featured_image, author_name, status, is_featured, reading_time, publish_date, meta_title, meta_description) VALUES
(1, 'Yapay Zeka Teknolojisi 2024 Yılında Büyük Dönüşüm Yaşayacak', 
'yapay-zeka-teknolojisi-2024-donusum', 
'Teknoloji uzmanları, 2024 yılının yapay zeka alanında devrim niteliğinde gelişmelere sahne olacağını öngörüyor.',
'<h2>Yapay Zeka Devrimi Kapıda</h2>
<p>2024 yılı, yapay zeka teknolojisi açısından dönüm noktası olacak gibi görünüyor. Sektör uzmanlarının tahminlerine göre, bu yıl AI teknolojisinde yaşanacak gelişmeler, günlük yaşamımızdan iş dünyasına kadar her alanı etkileyecek.</p>

<h3>Beklenen Gelişmeler</h3>
<ul>
<li>Daha gelişmiş doğal dil işleme sistemleri</li>
<li>Otonom araçlarda büyük adımlar</li>
<li>Sağlık sektöründe AI uygulamaları</li>
<li>Eğitimde kişiselleştirilmiş AI asistanları</li>
</ul>

<p>Bu gelişmeler beraberinde yeni iş fırsatları yaratacağı gibi, mevcut sektörlerde de köklü değişikliklere yol açabilir.</p>',
'/assets/uploads/ai-tech-2024.jpg', 
'Teknoloji Editörü', 'published', 1, 3, NOW(), 
'Yapay Zeka 2024 Yılı Tahminleri - LooMix.Click', 
'2024 yılında yapay zeka teknolojisinde beklenen gelişmeler ve dönüşümler hakkında detaylı analiz.'),

(2, 'Süper Lig 15. Hafta Maç Sonuçları ve Puan Durumu', 
'super-lig-15-hafta-mac-sonuclari', 
'Süper Lig 15. haftasında oynanan maçlardan sonra puan durumunda önemli değişiklikler yaşandı.',
'<h2>Heyecan Dolu Hafta Geride Kaldı</h2>
<p>Süper Lig''de 15. hafta maçları büyük heyecan yaşattı. Lider takımlar arasındaki puan farkı daralırken, küme düşme hattındaki takımlar da önemli puanlar aldı.</p>

<h3>Haftanın Önemli Sonuçları</h3>
<ul>
<li>Galatasaray 3-1 Beşiktaş</li>
<li>Fenerbahçe 2-0 Trabzonspor</li>
<li>Başakşehir 1-1 Konyaspor</li>
</ul>

<p>Bu sonuçlarla birlikte liderlik yarışı daha da kızışırken, alt sıralardaki takımlar da nefes almaya devam ediyor.</p>',
'/assets/uploads/super-lig-15-hafta.jpg', 
'Spor Editörü', 'published', 1, 2, NOW() - INTERVAL 2 HOUR,
'Süper Lig 15. Hafta Sonuçları - LooMix.Click', 
'Süper Lig 15. haftasında oynanan maçların sonuçları, puan durumu ve hafta analizi.'),

(3, 'Enflasyon Rakamları Açıklandı: Yıllık Bazda Düşüş Devam Ediyor', 
'enflasyon-rakamlari-aciklandi-dusus-devam', 
'TÜİK tarafından açıklanan enflasyon verilerine göre, yıllık enflasyonda düşüş trendi sürdü.',
'<h2>Enflasyonda Olumlu Gelişme</h2>
<p>Türkiye İstatistik Kurumu (TÜİK) tarafından açıklanan son verilere göre, yıllık enflasyon oranında düşüş devam ediyor. Bu durum, ekonomi uzmanları tarafından olumlu bir gelişme olarak değerlendiriliyor.</p>

<h3>Başlıca Bulgular</h3>
<ul>
<li>Aylık enflasyon: %0.85</li>
<li>Yıllık enflasyon: %51.97</li>
<li>Çekirdek enflasyon: %47.23</li>
</ul>

<p>Uzmanlar, bu verilerin merkez bankasının para politikası kararlarında etkili olacağını belirtiyor.</p>',
'/assets/uploads/enflasyon-2024.jpg', 
'Ekonomi Editörü', 'published', 0, 3, NOW() - INTERVAL 4 HOUR,
'Ocak 2024 Enflasyon Rakamları - LooMix.Click', 
'TÜİK Ocak 2024 enflasyon verileri, analizi ve ekonomiye etkileri hakkında detaylı bilgi.');

-- Haber-etiket ilişkileri
INSERT INTO news_tags (news_id, tag_id) VALUES
(1, 1), (1, 5), (1, 8), (1, 9),
(2, 4), (2, 7),
(3, 5), (3, 1);

-- Reklam alanları
INSERT INTO ad_zones (zone_name, zone_description, ad_code, ad_type, position, width, height, is_responsive) VALUES
('header_banner', 'Sayfa üst banner reklamı', 
 '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-3967023544942784" data-ad-slot="1234567890" data-ad-format="auto" data-full-width-responsive="true"></ins>', 
 'adsense', 'header', 728, 90, 1),
('sidebar_square', 'Yan menü kare reklam', 
 '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-3967023544942784" data-ad-slot="2345678901" data-ad-format="rectangle"></ins>', 
 'adsense', 'sidebar', 300, 250, 0),
('content_inline', 'İçerik arası reklam', 
 '<ins class="adsbygoogle" style="display:block; text-align:center;" data-ad-layout="in-article" data-ad-format="fluid" data-ad-client="ca-pub-3967023544942784" data-ad-slot="3456789012"></ins>', 
 'adsense', 'content', 336, 280, 1),
('footer_banner', 'Alt banner reklam', 
 '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-3967023544942784" data-ad-slot="4567890123" data-ad-format="auto" data-full-width-responsive="true"></ins>', 
 'adsense', 'footer', 970, 250, 1),
('mobile_banner', 'Mobil banner reklam', 
 '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-3967023544942784" data-ad-slot="5678901234" data-ad-format="banner" data-ad-width="320" data-ad-height="50"></ins>', 
 'adsense', 'mobile', 320, 50, 0);

-- SEO meta ayarları
INSERT INTO seo_meta (page_type, page_identifier, title, description, keywords, canonical_url) VALUES
('page', 'home', 'LooMix.Click - En Güncel Haberler', 'Türkiye ve dünyadan en son haberler, teknoloji, spor, ekonomi ve daha fazlası LooMix.Click''te', 'haber,güncel,teknoloji,spor,ekonomi', 'https://loomix.click/'),
('category', 'teknoloji', 'Teknoloji Haberleri - LooMix.Click', 'En güncel teknoloji haberleri, yenilikler ve teknoloji dünyasından gelişmeler', 'teknoloji,yenilik,bilim,yapay zeka', 'https://loomix.click/kategori/teknoloji'),
('category', 'spor', 'Spor Haberleri - LooMix.Click', 'Süper Lig, dünya futbolu, basketbol ve diğer spor dallarından haberler', 'spor,futbol,basketbol,süper lig', 'https://loomix.click/kategori/spor');
