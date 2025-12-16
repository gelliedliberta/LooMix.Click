-- =====================================================
-- Dublike Etiketleri Temizle
-- =====================================================
-- Aynı NAME veya SLUG'a sahip etiketlerden
-- BÜYÜK ID'lileri sil, küçük ID'lileri tut
-- =====================================================

-- YEDEK AL
CREATE TABLE IF NOT EXISTS tags_before_duplicate_clean AS SELECT * FROM tags;
CREATE TABLE IF NOT EXISTS news_tags_before_duplicate_clean AS SELECT * FROM news_tags;

-- =====================================================
-- ADIM 1: Analiz - Hangi etiketler dublike?
-- =====================================================

-- NAME'e göre dublikeler
SELECT 
    name,
    COUNT(*) as count,
    MIN(id) as keep_id,
    GROUP_CONCAT(id ORDER BY id) as all_ids
FROM tags
GROUP BY name
HAVING count > 1
ORDER BY count DESC;

-- SLUG'a göre dublikeler
SELECT 
    slug,
    COUNT(*) as count,
    MIN(id) as keep_id,
    GROUP_CONCAT(id ORDER BY id) as all_ids
FROM tags
GROUP BY slug
HAVING count > 1
ORDER BY count DESC;

-- =====================================================
-- ADIM 2: Silinecek ID'leri belirle
-- =====================================================

-- NAME dublikelerinden silinecekler (büyük ID'ler)
SELECT DISTINCT t.id, t.name, t.slug
FROM tags t
INNER JOIN (
    SELECT name, MIN(id) as keep_id
    FROM tags
    GROUP BY name
    HAVING COUNT(*) > 1
) d ON t.name = d.name AND t.id > d.keep_id
ORDER BY t.name, t.id;

-- =====================================================
-- ADIM 3: Foreign key kontrollerini kapat
-- =====================================================
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- ADIM 4: news_tags'deki ilişkileri en küçük ID'ye aktar
-- =====================================================

-- NAME dublikeleri için haberleri en küçük ID'ye aktar
UPDATE IGNORE news_tags nt
INNER JOIN tags t ON nt.tag_id = t.id
INNER JOIN (
    SELECT name, MIN(id) as keep_id
    FROM tags
    GROUP BY name
    HAVING COUNT(*) > 1
) d ON t.name = d.name AND t.id > d.keep_id
SET nt.tag_id = d.keep_id;

-- SLUG dublikeleri için de aynı işlem
UPDATE IGNORE news_tags nt
INNER JOIN tags t ON nt.tag_id = t.id
INNER JOIN (
    SELECT slug, MIN(id) as keep_id
    FROM tags
    GROUP BY slug
    HAVING COUNT(*) > 1
) d ON t.slug = d.slug AND t.id > d.keep_id
SET nt.tag_id = d.keep_id;

-- =====================================================
-- ADIM 5: Artık news_tags'de kullanılmayan büyük ID'leri sil
-- =====================================================

-- NAME dublikelerinden büyük ID'leri sil
DELETE t FROM tags t
INNER JOIN (
    SELECT name, MIN(id) as keep_id
    FROM tags
    GROUP BY name
    HAVING COUNT(*) > 1
) d ON t.name = d.name AND t.id > d.keep_id;

-- SLUG dublikelerinden büyük ID'leri sil
DELETE t FROM tags t
INNER JOIN (
    SELECT slug, MIN(id) as keep_id
    FROM tags
    GROUP BY slug
    HAVING COUNT(*) > 1
) d ON t.slug = d.slug AND t.id > d.keep_id;

-- =====================================================
-- ADIM 6: Yetim kalan news_tags kayıtlarını temizle
-- =====================================================

-- Artık var olmayan tag_id'leri temizle
DELETE FROM news_tags
WHERE tag_id NOT IN (SELECT id FROM tags);

-- =====================================================
-- ADIM 7: Foreign key kontrollerini aç
-- =====================================================
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- KONTROL SORULARI
-- =====================================================

-- NAME dublikesi var mı? (OLMAMALI!)
SELECT name, COUNT(*) as count
FROM tags
GROUP BY name
HAVING count > 1;

-- SLUG dublikesi var mı? (OLMAMALI!)
SELECT slug, COUNT(*) as count
FROM tags
GROUP BY slug
HAVING count > 1;

-- Toplam etiket sayısı
SELECT COUNT(*) as total FROM tags;

-- Kullanım istatistikleri
SELECT 
    COUNT(DISTINCT t.id) as total_tags,
    COUNT(DISTINCT nt.tag_id) as used_tags,
    COUNT(DISTINCT t.id) - COUNT(DISTINCT nt.tag_id) as unused_tags
FROM tags t
LEFT JOIN news_tags nt ON t.id = nt.tag_id;

-- =====================================================
-- TEMİZLİK BAŞARILI!
-- =====================================================
-- Geri dönüş (gerekirse):
-- SET FOREIGN_KEY_CHECKS = 0;
-- DROP TABLE tags;
-- DROP TABLE news_tags;
-- CREATE TABLE tags AS SELECT * FROM tags_before_duplicate_clean;
-- CREATE TABLE news_tags AS SELECT * FROM news_tags_before_duplicate_clean;
-- ALTER TABLE tags ADD PRIMARY KEY (id);
-- ALTER TABLE news_tags ADD PRIMARY KEY (news_id, tag_id);
-- SET FOREIGN_KEY_CHECKS = 1;
-- =====================================================

