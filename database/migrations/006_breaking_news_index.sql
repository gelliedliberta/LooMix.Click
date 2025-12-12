-- Breaking news performans optimizasyonu için index
-- Migration 006: Breaking News Index
-- Date: 2025-12-12
-- Description: is_breaking ve publish_date alanları için composite index

USE u920805771_loomix;

-- Breaking news için özel composite index
-- Bu index, breaking news sorgularını optimize eder
-- WHERE is_breaking = 1 AND status = 'published' AND publish_date <= NOW()
-- ORDER BY publish_date DESC sorguları için

SET @have_idx_breaking := (
    SELECT COUNT(1) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_schema = DATABASE() 
    AND table_name = 'news' 
    AND index_name = 'idx_news_breaking'
);

SET @sql_breaking := IF(
    @have_idx_breaking = 0, 
    'ALTER TABLE news ADD INDEX idx_news_breaking (is_breaking, status, publish_date DESC);', 
    'SELECT "Index already exists" as message;'
);

PREPARE stmt FROM @sql_breaking; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;

-- İstatistikleri güncelle
ANALYZE TABLE news;


