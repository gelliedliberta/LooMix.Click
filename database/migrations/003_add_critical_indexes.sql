-- Kritik indeksler
USE u920805771_loomix;

-- Tekil slug indeksleri (idempotent kontrol)
SET @have_idx_news_slug := (
    SELECT COUNT(1) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_schema = DATABASE() AND table_name = 'news' AND index_name = 'idx_news_slug'
);
SET @sql_news_slug := IF(@have_idx_news_slug = 0, 'ALTER TABLE news ADD INDEX idx_news_slug (slug);', 'SELECT 1;');
PREPARE stmt FROM @sql_news_slug; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @have_idx_categories_slug := (
    SELECT COUNT(1) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_schema = DATABASE() AND table_name = 'categories' AND index_name = 'idx_categories_slug'
);
SET @sql_categories_slug := IF(@have_idx_categories_slug = 0, 'ALTER TABLE categories ADD INDEX idx_categories_slug (slug);', 'SELECT 1;');
PREPARE stmt FROM @sql_categories_slug; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Haberler için birleşik indeks (kategori, durum, tarih)
SET @have_idx_combo := (
    SELECT COUNT(1) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_schema = DATABASE() AND table_name = 'news' AND index_name = 'idx_news_cat_status_date'
);
SET @sql_combo := IF(@have_idx_combo = 0, 'ALTER TABLE news ADD INDEX idx_news_cat_status_date (category_id, status, created_at);', 'SELECT 1;');
PREPARE stmt FROM @sql_combo; EXECUTE stmt; DEALLOCATE PREPARE stmt;
