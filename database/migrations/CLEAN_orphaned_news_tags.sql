-- =====================================================
-- Yetim news_tags Kayıtlarını Temizle
-- =====================================================
-- tags tablosunda olmayan tag_id'leri sil
-- =====================================================

-- YEDEK AL
CREATE TABLE IF NOT EXISTS news_tags_backup_orphan_clean AS SELECT * FROM news_tags;

-- =====================================================
-- ADIM 1: Yetim kayıtları KONTROL ET
-- =====================================================

-- Kaç tane yetim kayıt var?
SELECT COUNT(*) as orphaned_count
FROM news_tags nt
LEFT JOIN tags t ON nt.tag_id = t.id
WHERE t.id IS NULL;

-- Hangi tag_id'ler yetim? (detaylı)
SELECT 
    nt.tag_id,
    COUNT(*) as news_count,
    'SİLİNECEK' as action
FROM news_tags nt
LEFT JOIN tags t ON nt.tag_id = t.id
WHERE t.id IS NULL
GROUP BY nt.tag_id
ORDER BY news_count DESC;

-- Hangi haberlerde yetim tag var?
SELECT 
    n.id as news_id,
    n.title,
    nt.tag_id as orphaned_tag_id
FROM news_tags nt
LEFT JOIN tags t ON nt.tag_id = t.id
LEFT JOIN news n ON nt.news_id = n.id
WHERE t.id IS NULL
LIMIT 20;

-- =====================================================
-- ADIM 2: Yetim kayıtları SİL
-- =====================================================

-- Foreign key kontrollerini kapat (güvenlik için)
SET FOREIGN_KEY_CHECKS = 0;

-- Yetim kayıtları sil
DELETE nt FROM news_tags nt
LEFT JOIN tags t ON nt.tag_id = t.id
WHERE t.id IS NULL;

-- Foreign key kontrollerini aç
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- ADIM 3: KONTROL - Temizlendi mi?
-- =====================================================

-- Yetim kayıt kaldı mı? (0 olmalı!)
SELECT COUNT(*) as remaining_orphans
FROM news_tags nt
LEFT JOIN tags t ON nt.tag_id = t.id
WHERE t.id IS NULL;

-- Toplam sonuç
SELECT 
    (SELECT COUNT(*) FROM news_tags_backup_orphan_clean) as once,
    (SELECT COUNT(*) FROM news_tags) as sonra,
    (SELECT COUNT(*) FROM news_tags_backup_orphan_clean) - (SELECT COUNT(*) FROM news_tags) as silinen;

-- Her tag kaç haberde kullanılıyor?
SELECT 
    t.id,
    t.name,
    COUNT(nt.news_id) as news_count
FROM tags t
LEFT JOIN news_tags nt ON t.id = nt.tag_id
GROUP BY t.id
ORDER BY news_count DESC
LIMIT 20;

-- =====================================================
-- TEMİZLİK BAŞARILI!
-- =====================================================
-- Geri dönüş (gerekirse):
-- DROP TABLE news_tags;
-- CREATE TABLE news_tags AS SELECT * FROM news_tags_backup_orphan_clean;
-- ALTER TABLE news_tags ADD PRIMARY KEY (news_id, tag_id);
-- =====================================================

