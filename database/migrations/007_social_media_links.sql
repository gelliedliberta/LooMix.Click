-- ================================================
-- Sosyal Medya Linkleri Yönetimi
-- Migration: 007_social_media_links.sql
-- Tarih: 2025-01-15
-- ================================================

-- Sosyal medya linkleri tablosu
CREATE TABLE IF NOT EXISTS `social_media_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) NOT NULL COMMENT 'Platform adı (facebook, twitter, instagram, vb.)',
  `name` varchar(100) NOT NULL COMMENT 'Görünen isim',
  `icon` varchar(100) NOT NULL COMMENT 'Font Awesome icon class (fab fa-facebook)',
  `url` varchar(255) DEFAULT NULL COMMENT 'Sosyal medya profil URL',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktif/Pasif',
  `display_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Gösterim sırası (küçük önce)',
  `show_in_header` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Header\'da göster',
  `show_in_footer` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Footer\'da göster',
  `color` varchar(7) DEFAULT NULL COMMENT 'Platform rengi (hex)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `platform_unique` (`platform`),
  KEY `active_order` (`is_active`, `display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sosyal medya linkleri yönetimi';

-- Varsayılan sosyal medya platformlarını ekle
INSERT INTO `social_media_links` (`platform`, `name`, `icon`, `url`, `is_active`, `display_order`, `show_in_header`, `show_in_footer`, `color`) VALUES
('facebook', 'Facebook', 'fab fa-facebook', 'https://facebook.com', 1, 1, 1, 1, '#1877F2'),
('twitter', 'Twitter (X)', 'fab fa-x-twitter', 'https://x.com', 1, 2, 1, 1, '#000000'),
('instagram', 'Instagram', 'fab fa-instagram', 'https://instagram.com', 1, 3, 1, 1, '#E4405F'),
('youtube', 'YouTube', 'fab fa-youtube', NULL, 1, 4, 0, 1, '#FF0000'),
('linkedin', 'LinkedIn', 'fab fa-linkedin', NULL, 0, 5, 0, 0, '#0A66C2'),
('tiktok', 'TikTok', 'fab fa-tiktok', NULL, 0, 6, 0, 0, '#000000'),
('telegram', 'Telegram', 'fab fa-telegram', NULL, 0, 7, 0, 0, '#0088CC'),
('whatsapp', 'WhatsApp', 'fab fa-whatsapp', NULL, 0, 8, 0, 0, '#25D366');

-- RSS için özel kayıt (sistem tarafından yönetilir)
INSERT INTO `social_media_links` (`platform`, `name`, `icon`, `url`, `is_active`, `display_order`, `show_in_header`, `show_in_footer`, `color`) VALUES
('rss', 'RSS', 'fas fa-rss', '/rss', 1, 99, 1, 0, '#FF6600');

