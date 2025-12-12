---
description: "Veritabanı işlemleri, migration'lar ve SQL query standartları"
globs: 
  - "database/**"
  - "**/*.sql"
  - "app/models/**"
alwaysApply: false
---

# Database Operations Rules

LooMix.Click projesi için veritabanı işlemleri kuralları.

## Tablo ve Kolon İsimlendirme

### Tablo İsimleri
- **Çoğul isimler**: `news`, `categories`, `users`, `tags`
- **snake_case**: `news_views`, `user_preferences`, `ad_zones`

### Kolon İsimleri
- **snake_case**: `created_at`, `updated_at`, `is_active`
- **Açıklayıcı isimler**: `view_count`, `featured_image`, `publish_date`
- **Boolean kolonlar**: `is_` prefix: `is_featured`, `is_active`, `show_in_menu`

### Primary Keys
- Her zaman `id` kolonu kullan
- `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`

### Foreign Keys
- `table_id` formatı: `category_id`, `user_id`, `news_id`
- Foreign key constraint ekle:
```sql
CONSTRAINT fk_news_category 
FOREIGN KEY (category_id) 
REFERENCES categories(id) 
ON DELETE CASCADE
```

## SQL Query Standartları

### Prepared Statements (ZORUNLU)

```php
// DOĞRU - Prepared statements
$stmt = $this->db->prepare("
    SELECT * FROM news 
    WHERE id = :id AND status = :status
");
$stmt->execute([
    'id' => $newsId,
    'status' => 'published'
]);

// YANLIŞ - String concatenation (SQL Injection riski!)
$query = "SELECT * FROM news WHERE id = " . $newsId;
$result = $this->db->query($query);
```

### Query Formatı

SQL query'leri okunabilir formatta yaz:

```sql
-- DOĞRU - Okunabilir format
SELECT 
    n.id,
    n.title,
    n.slug,
    n.publish_date,
    c.name as category_name,
    c.slug as category_slug
FROM news n
INNER JOIN categories c ON n.category_id = c.id
WHERE n.status = 'published'
  AND n.publish_date <= NOW()
ORDER BY n.publish_date DESC
LIMIT 10;

-- YANLIŞ - Tek satır
SELECT n.id, n.title, n.slug, c.name FROM news n INNER JOIN categories c ON n.category_id = c.id WHERE n.status = 'published' LIMIT 10;
```

### Query Optimization

```sql
-- DOĞRU - Index kullanımı
SELECT * FROM news 
WHERE status = 'published' 
  AND publish_date <= NOW() 
ORDER BY publish_date DESC 
LIMIT 10;

-- YANLIŞ - Function kullanımı index'i bozar
SELECT * FROM news 
WHERE DATE(publish_date) = '2024-01-01';

-- DOĞRU - Date range kullan
SELECT * FROM news 
WHERE publish_date >= '2024-01-01' 
  AND publish_date < '2024-01-02';
```

## Indexing Kuralları

### Primary Index
```sql
-- Her tabloda id primary key
CREATE TABLE news (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ...
);
```

### Unique Index
```sql
-- Slug kolonları için unique index
CREATE UNIQUE INDEX idx_news_slug ON news(slug);
CREATE UNIQUE INDEX idx_categories_slug ON categories(slug);
```

### Composite Index
```sql
-- Sık kullanılan query'ler için composite index
CREATE INDEX idx_news_status_date ON news(status, publish_date);
CREATE INDEX idx_news_category_status ON news(category_id, status);
```

### Full-Text Index
```sql
-- Arama için full-text index
ALTER TABLE news ADD FULLTEXT INDEX idx_news_search (title, summary, content);

-- Kullanımı
SELECT * FROM news 
WHERE MATCH(title, summary, content) AGAINST('arama kelimesi' IN NATURAL LANGUAGE MODE);
```

## Migration Dosyaları

### Migration Formatı

`database/migrations/` dizinine migration dosyaları ekle.

Naming: `001_migration_name.sql`, `002_add_column.sql`, `003_add_indexes.sql`

```sql
-- Migration: 005_add_show_in_menu_to_categories.sql
-- Description: Kategorilere menüde göster/gizle özelliği ekleme
-- Date: 2024-01-15

-- Add column
ALTER TABLE categories 
ADD COLUMN show_in_menu TINYINT(1) DEFAULT 1 AFTER parent_id;

-- Add index if needed
CREATE INDEX idx_categories_show_in_menu ON categories(show_in_menu);

-- Update existing data if needed
UPDATE categories SET show_in_menu = 1 WHERE parent_id IS NULL;

-- Rollback (comment olarak)
-- ALTER TABLE categories DROP COLUMN show_in_menu;
```

### Migration Documentation

**ÖNEMLİ**: Her veritabanı değişikliği için `database/` dizininde bir .md dosyası oluştur veya güncelle.

Örnek: `database/admin_operations.md`

```markdown
# Admin Operations - Database Changes

## 2024-01-15: Show in Menu Feature

### Changes
- Added `show_in_menu` column to `categories` table
- Added index on `show_in_menu` column

### Migration File
- `005_add_show_in_menu_to_categories.sql`

### Purpose
Allow admins to control which categories appear in the main menu.

### Testing
1. Run migration
2. Check admin panel - categories page
3. Toggle show_in_menu for a category
4. Verify category appears/disappears from main menu
```

## Transaction Kullanımı

Birden fazla related işlem için transaction kullan:

```php
try {
    $this->db->beginTransaction();
    
    // Insert news
    $newsId = $this->db->insert('news', $newsData);
    
    // Insert tags
    foreach ($tags as $tagId) {
        $this->db->insert('news_tags', [
            'news_id' => $newsId,
            'tag_id' => $tagId
        ]);
    }
    
    // Update category count
    $this->db->update('categories', 
        ['news_count' => 'news_count + 1'], 
        ['id' => $categoryId]
    );
    
    $this->db->commit();
    return $newsId;
    
} catch (Exception $e) {
    $this->db->rollBack();
    throw $e;
}
```

## Common Patterns

### Pagination Query

```php
public function getPaginatedNews(int $page, int $perPage): array {
    $offset = ($page - 1) * $perPage;
    
    $query = "
        SELECT n.*, c.name as category_name
        FROM news n
        INNER JOIN categories c ON n.category_id = c.id
        WHERE n.status = 'published'
        ORDER BY n.publish_date DESC
        LIMIT :limit OFFSET :offset
    ";
    
    return $this->db->fetchAll($query, [
        'limit' => $perPage,
        'offset' => $offset
    ]);
}
```

### Search Query

```php
public function searchNews(string $keyword, int $limit = 10): array {
    $query = "
        SELECT n.*, c.name as category_name,
               MATCH(n.title, n.summary, n.content) 
               AGAINST(:keyword IN NATURAL LANGUAGE MODE) as relevance
        FROM news n
        INNER JOIN categories c ON n.category_id = c.id
        WHERE n.status = 'published'
          AND MATCH(n.title, n.summary, n.content) 
              AGAINST(:keyword IN NATURAL LANGUAGE MODE)
        ORDER BY relevance DESC, n.publish_date DESC
        LIMIT :limit
    ";
    
    return $this->db->fetchAll($query, [
        'keyword' => $keyword,
        'limit' => $limit
    ]);
}
```

### Slug Existence Check

```php
private function slugExists(string $slug, ?int $excludeId = null): bool {
    $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug";
    $params = ['slug' => $slug];
    
    if ($excludeId) {
        $query .= " AND id != :id";
        $params['id'] = $excludeId;
    }
    
    $result = $this->db->fetch($query, $params);
    return $result['count'] > 0;
}
```

## Performance Best Practices

### 1. Select Only Needed Columns
```sql
-- DOĞRU
SELECT id, title, slug FROM news LIMIT 10;

-- YANLIŞ (gereksiz data transfer)
SELECT * FROM news LIMIT 10;
```

### 2. Avoid Subqueries When Possible
```sql
-- DOĞRU - JOIN kullan
SELECT n.*, c.name 
FROM news n
INNER JOIN categories c ON n.category_id = c.id;

-- YANLIŞ - Subquery
SELECT n.*, (SELECT name FROM categories WHERE id = n.category_id) as category_name
FROM news n;
```

### 3. Use EXPLAIN
Development'ta query'leri analiz et:

```sql
EXPLAIN SELECT * FROM news 
WHERE status = 'published' 
  AND publish_date <= NOW()
ORDER BY publish_date DESC;
```

## Backup ve Recovery

### Regular Backups
```bash
# Database backup
mysqldump -u root -p u920805771_loomix > backup_$(date +%Y%m%d).sql

# Restore
mysql -u root -p u920805771_loomix < backup_20240115.sql
```

### Migration Rollback
Her migration için rollback planı oluştur (SQL comment olarak).

