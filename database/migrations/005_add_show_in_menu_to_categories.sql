-- 005_add_show_in_menu_to_categories.sql
-- Kategoriler tablosuna menüde gösterim kontrolü için kolon ekler

ALTER TABLE categories
    ADD COLUMN show_in_menu TINYINT(1) NOT NULL DEFAULT 1 AFTER is_active;

-- Varsayılan olarak mevcut aktif kategorileri menüde göster
UPDATE categories SET show_in_menu = 1 WHERE is_active = 1;


