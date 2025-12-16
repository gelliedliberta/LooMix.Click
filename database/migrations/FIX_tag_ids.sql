-- =====================================================
-- TAG ID Problemini Düzelt
-- =====================================================
-- id=0 olan kayıtlara yeni ID'ler ata
-- Foreign key'leri düzelt
-- =====================================================

-- GÜVENLİK YEDEK
CREATE TABLE IF NOT EXISTS tags_before_id_fix AS SELECT * FROM tags;
CREATE TABLE IF NOT EXISTS news_tags_before_id_fix AS SELECT * FROM news_tags;

-- =====================================================
-- ADIM 1: Mevcut durumu analiz et
-- =====================================================

-- Kaç tane id=0 var?
SELECT COUNT(*) as zero_id_count FROM tags WHERE id = 0;

-- id=0 olan etiketleri göster
SELECT id, name, slug FROM tags WHERE id = 0;

-- En büyük ID nedir?
SELECT MAX(id) as max_id FROM tags;

-- =====================================================
-- ADIM 2: Geçici olarak foreign key kontrollerini kapat
-- =====================================================
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- ADIM 3: Primary key'i kaldır (düzeltme için)
-- =====================================================
ALTER TABLE tags DROP PRIMARY KEY;

-- =====================================================
-- ADIM 4: id=0 olan kayıtlara yeni ID'ler ata
-- =====================================================

-- En büyük ID'yi bul
SET @max_id = (SELECT IFNULL(MAX(id), 0) FROM tags WHERE id > 0);

-- id=0 olan her kayda yeni bir ID ata
-- NOT: Bu işlem her çalıştırıldığında farklı ID'ler verecek

UPDATE tags 
SET id = (@max_id := @max_id + 1)
WHERE id = 0
ORDER BY name;

-- =====================================================
-- ADIM 5: Tüm ID'leri kontrol et ve sıralı yap
-- =====================================================

-- Geçici tablo oluştur
CREATE TABLE tags_temp AS 
SELECT 
    ROW_NUMBER() OVER (ORDER BY 
        CASE WHEN id > 0 THEN id ELSE 999999 END,
        name
    ) as new_id,
    id as old_id,
    name,
    slug,
    description,
    color,
    usage_count,
    is_active,
    created_at,
    updated_at
FROM tags;

-- news_tags tablosundaki ilişkileri güncelle
UPDATE news_tags nt
INNER JOIN tags_temp tt ON nt.tag_id = tt.old_id
SET nt.tag_id = tt.new_id;

-- Eski tags tablosunu sil ve yenisini oluştur
DROP TABLE tags;

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#6c757d',
    usage_count INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_usage_count (usage_count)
);

-- Verileri yeni yapıya aktar
INSERT INTO tags (id, name, slug, description, color, usage_count, is_active, created_at, updated_at)
SELECT new_id, name, slug, description, color, usage_count, is_active, created_at, updated_at
FROM tags_temp;

-- Geçici tabloyu sil
DROP TABLE tags_temp;

-- =====================================================
-- ADIM 6: news_tags tablosunu düzelt
-- =====================================================

-- news_tags yapısını kontrol et
DESCRIBE news_tags;

-- Geçersiz ilişkileri temizle (artık var olmayan tag_id'ler)
DELETE FROM news_tags 
WHERE tag_id NOT IN (SELECT id FROM tags);

-- news_tags yapısını yeniden oluştur
ALTER TABLE news_tags DROP PRIMARY KEY;

ALTER TABLE news_tags 
ADD PRIMARY KEY (news_id, tag_id),
ADD INDEX idx_news_id (news_id),
ADD INDEX idx_tag_id (tag_id);

-- =====================================================
-- ADIM 7: Foreign key'leri ekle
-- =====================================================

-- Önce var olan foreign key'leri kaldır (varsa)
SET @drop_fk = (
    SELECT CONCAT('ALTER TABLE news_tags DROP FOREIGN KEY ', CONSTRAINT_NAME, ';')
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'news_tags'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    LIMIT 1
);

-- Foreign key'leri ekle
ALTER TABLE news_tags
ADD CONSTRAINT fk_news_tags_news 
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE,
ADD CONSTRAINT fk_news_tags_tag 
    FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE;

-- =====================================================
-- ADIM 8: Foreign key kontrollerini aç
-- =====================================================
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- ADIM 9: AUTO_INCREMENT'i ayarla
-- =====================================================
SET @max_new_id = (SELECT MAX(id) FROM tags);
SET @alter_auto_inc = CONCAT('ALTER TABLE tags AUTO_INCREMENT = ', @max_new_id + 1);
PREPARE stmt FROM @alter_auto_inc;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- KONTROL SORULARI
-- =====================================================

-- id=0 var mı? (OLMAMALI!)
SELECT COUNT(*) as zero_id_count FROM tags WHERE id = 0;

-- ID'ler benzersiz mi?
SELECT id, COUNT(*) as count 
FROM tags 
GROUP BY id 
HAVING count > 1;

-- Toplam kayıt sayısı
SELECT COUNT(*) as total_tags FROM tags;

-- En küçük ve en büyük ID
SELECT MIN(id) as min_id, MAX(id) as max_id FROM tags;

-- Foreign key'ler doğru mu?
SELECT 
    COUNT(DISTINCT nt.tag_id) as tags_in_use,
    COUNT(DISTINCT t.id) as total_tags
FROM news_tags nt
LEFT JOIN tags t ON nt.tag_id = t.id;

-- Yapı kontrolü
SHOW CREATE TABLE tags;
SHOW CREATE TABLE news_tags;

-- =====================================================
-- BAŞARILI!
-- =====================================================
-- Eğer sorun olursa geri dönüş:
-- DROP TABLE tags, news_tags;
-- CREATE TABLE tags AS SELECT * FROM tags_before_id_fix;
-- CREATE TABLE news_tags AS SELECT * FROM news_tags_before_id_fix;
-- =====================================================

