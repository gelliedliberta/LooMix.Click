-- =====================================================
-- 20 Karakterden Uzun Etiketleri Sil
-- =====================================================
-- Bu SQL dosyasını phpMyAdmin veya MySQL client'ta çalıştırın
-- =====================================================

-- ÖNCE YEDEK ALIN!
CREATE TABLE IF NOT EXISTS tags_backup_long_delete AS SELECT * FROM tags;
CREATE TABLE IF NOT EXISTS news_tags_backup_long_delete AS SELECT * FROM news_tags;

-- 1. Hangi etiketler silinecek kontrol edin (önce bakın)
SELECT 
    id, 
    name, 
    LENGTH(name) as length,
    usage_count
FROM tags 
WHERE LENGTH(name) > 20
ORDER BY LENGTH(name) DESC;

-- 2. İlişkili haberleri temizle (news_tags tablosundan)
DELETE nt FROM news_tags nt
INNER JOIN tags t ON nt.tag_id = t.id
WHERE LENGTH(t.name) > 20;

-- 3. Uzun etiketleri sil
DELETE FROM tags 
WHERE LENGTH(name) > 20;

-- =====================================================
-- SONUÇ KONTROLÜ
-- =====================================================

-- Kaç etiket kaldı?
SELECT COUNT(*) as total_tags FROM tags;

-- En uzun etiket kaç karakter?
SELECT MAX(LENGTH(name)) as max_length FROM tags;

-- Etiket uzunluk dağılımı
SELECT 
    CASE 
        WHEN LENGTH(name) <= 10 THEN '1-10 karakter'
        WHEN LENGTH(name) <= 15 THEN '11-15 karakter'
        WHEN LENGTH(name) <= 20 THEN '16-20 karakter'
        ELSE '20+ karakter'
    END as length_range,
    COUNT(*) as count
FROM tags
GROUP BY length_range
ORDER BY MAX(LENGTH(name));

-- =====================================================
-- GERİ DÖNÜŞ (İhtiyaç olursa)
-- =====================================================
-- DROP TABLE IF EXISTS tags;
-- CREATE TABLE tags AS SELECT * FROM tags_backup_long_delete;
-- ALTER TABLE tags ADD PRIMARY KEY (id);
-- 
-- DROP TABLE IF EXISTS news_tags;
-- CREATE TABLE news_tags AS SELECT * FROM news_tags_backup_long_delete;
-- =====================================================

