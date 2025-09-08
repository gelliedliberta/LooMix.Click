-- Legacy slug yönlendirme tablosu
USE u920805771_loomix;

CREATE TABLE IF NOT EXISTS legacy_slugs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entity_type ENUM('news','category','tag') NOT NULL,
    old_slug VARCHAR(255) NOT NULL,
    new_slug VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_legacy (entity_type, old_slug),
    INDEX idx_new_slug (new_slug)
) ENGINE=InnoDB;
