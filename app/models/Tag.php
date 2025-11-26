<?php
/**
 * Tag Model - Etiket verileri yönetimi
 * LooMix.Click
 */

class Tag extends Model {
    protected $table = 'tags';
    
    /**
     * Aktif etiketleri getir
     */
    public function getActiveTags($limit = null, $minUsage = 0) {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        if ($minUsage > 0) {
            $sql .= " AND usage_count >= :min_usage";
        }
        $sql .= " ORDER BY usage_count DESC, name ASC";
        
        if ($limit) {
            $limit = (int)$limit;
            $sql .= " LIMIT {$limit}";
            $params = [];
            if ($minUsage > 0) { $params['min_usage'] = (int)$minUsage; }
            return $this->db->fetchAll($sql, $params);
        }
        
        $params = [];
        if ($minUsage > 0) { $params['min_usage'] = (int)$minUsage; }
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Sayfalı aktif etiketleri getir (kullanımı olanlar öncelikli)
     */
    public function getActiveTagsPaginated($page = 1, $perPage = 48, $minUsage = 0) {
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;

        $offset = (int)$offset; // güvenlik için cast
        $perPage = (int)$perPage;

        $where = "WHERE is_active = 1";
        if ($minUsage > 0) {
            $where .= " AND usage_count >= :min_usage";
        }
        $sql = "SELECT * FROM {$this->table} 
                {$where} 
                ORDER BY usage_count DESC, name ASC 
                LIMIT {$offset}, {$perPage}";

        $params = [];
        if ($minUsage > 0) { $params['min_usage'] = (int)$minUsage; }
        $tags = $this->db->fetchAll($sql, $params);
        $totalCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} {$where}", $params);

        return [
            'tags' => $tags,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_count' => $totalCount,
                'total_pages' => (int)ceil($totalCount / $perPage)
            ]
        ];
    }
    
    /**
     * Popüler etiketleri getir
     */
    public function getPopularTags($limit = 20) {
        $limit = (int)$limit;
        $sql = "SELECT t.*, COUNT(nt.id) as current_usage 
                FROM {$this->table} t 
                INNER JOIN news_tags nt ON t.id = nt.tag_id 
                INNER JOIN news n ON nt.news_id = n.id AND n.status = 'published' 
                WHERE t.is_active = 1 
                GROUP BY t.id 
                HAVING current_usage > 0 
                ORDER BY current_usage DESC, t.name ASC 
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Slug ile etiketi getir
     */
    public function getBySlug($slug) {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND is_active = 1";
        return $this->db->fetch($sql, ['slug' => $slug]);
    }
    
    /**
     * Etiketin haberlerini getir
     */
    public function getTagNews($tagId, $page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        $offset = (int)$offset;
        $perPage = (int)$perPage;
        
        $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                FROM news n 
                INNER JOIN news_tags nt ON n.id = nt.news_id 
                INNER JOIN categories c ON n.category_id = c.id 
                WHERE nt.tag_id = :tag_id AND n.status = 'published' AND n.publish_date <= NOW() 
                ORDER BY n.publish_date DESC 
                LIMIT {$offset}, {$perPage}";
        
        $news = $this->db->fetchAll($sql, [
            'tag_id' => $tagId
        ]);
        
        // Toplam sayı
        $totalCount = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM news n 
             INNER JOIN news_tags nt ON n.id = nt.news_id 
             WHERE nt.tag_id = :tag_id AND n.status = 'published'",
            ['tag_id' => $tagId]
        );
        
        return [
            'news' => $news,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_count' => $totalCount,
                'total_pages' => ceil($totalCount / $perPage)
            ]
        ];
    }
    
    /**
     * Etiket slug'ını oluştur ve kontrol et
     */
    public function createUniqueSlug($name, $excludeId = null) {
        $slug = createSlug($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $sql = "SELECT id FROM {$this->table} WHERE slug = :slug";
            $params = ['slug' => $slug];
            
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
                $params['exclude_id'] = $excludeId;
            }
            
            $existing = $this->db->fetch($sql, $params);
            
            if (!$existing) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Etiket kullanım sayısını güncelle
     */
    public function updateUsageCount($tagId) {
        $sql = "UPDATE {$this->table} 
                SET usage_count = (
                    SELECT COUNT(*) FROM news_tags nt 
                    INNER JOIN news n ON nt.news_id = n.id 
                    WHERE nt.tag_id = :tag_id1 AND n.status = 'published'
                ) 
                WHERE id = :tag_id2";
        
        return $this->db->query($sql, [
            'tag_id1' => $tagId,
            'tag_id2' => $tagId
        ]);
    }
    
    /**
     * Tüm etiketlerin kullanım sayısını güncelle
     */
    public function updateAllUsageCounts() {
        $sql = "UPDATE {$this->table} t 
                SET usage_count = (
                    SELECT COUNT(*) FROM news_tags nt 
                    INNER JOIN news n ON nt.news_id = n.id 
                    WHERE nt.tag_id = t.id AND n.status = 'published'
                )";
        
        return $this->db->query($sql);
    }
    
    /**
     * İsme göre etiket ara
     */
    public function searchByName($query, $limit = 10) {
        $limit = (int)$limit;
        $sql = "SELECT * FROM {$this->table} 
                WHERE name LIKE :query AND is_active = 1 
                ORDER BY usage_count DESC, name ASC 
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql, [
            'query' => "%{$query}%"
        ]);
    }
    
    /**
     * Etiket bulma veya oluşturma
     */
    public function findOrCreate($name) {
        // Önce etiketin var olup olmadığını kontrol et
        $existing = $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE name = :name",
            ['name' => trim($name)]
        );
        
        if ($existing) {
            return $existing;
        }
        
        // Yoksa yeni etiket oluştur
        $slug = $this->createUniqueSlug($name);
        
        $tagId = $this->create([
            'name' => trim($name),
            'slug' => $slug,
            'color' => '#6c757d',
            'usage_count' => 0,
            'is_active' => 1
        ]);
        
        return $this->find($tagId);
    }
    
    /**
     * Habere etiket ekle
     */
    public function attachToNews($tagId, $newsId) {
        // Zaten var mı kontrol et
        $existing = $this->db->fetch(
            "SELECT id FROM news_tags WHERE tag_id = :tag_id AND news_id = :news_id",
            ['tag_id' => $tagId, 'news_id' => $newsId]
        );
        
        if (!$existing) {
            $this->db->insert('news_tags', [
                'tag_id' => $tagId,
                'news_id' => $newsId
            ]);
            
            // Kullanım sayısını güncelle
            $this->updateUsageCount($tagId);
        }
    }
    
    /**
     * Haberden etiket kaldır
     */
    public function detachFromNews($tagId, $newsId) {
        $this->db->delete('news_tags', 'tag_id = :tag_id AND news_id = :news_id', [
            'tag_id' => $tagId,
            'news_id' => $newsId
        ]);
        
        // Kullanım sayısını güncelle
        $this->updateUsageCount($tagId);
    }
    
    /**
     * Haberin tüm etiketlerini değiştir
     */
    public function syncNewsTagsByNames($newsId, $tagNames) {
        // Mevcut etiketleri sil
        $this->db->delete('news_tags', 'news_id = :news_id', ['news_id' => $newsId]);
        
        // Yeni etiketleri ekle
        if (!empty($tagNames)) {
            foreach ($tagNames as $tagName) {
                $tagName = trim($tagName);
                if (empty($tagName)) continue;
                
                $tag = $this->findOrCreate($tagName);
                $this->attachToNews($tag['id'], $newsId);
            }
        }
    }
    
    /**
     * İlgili etiketleri getir
     */
    public function getRelatedTags($tagId, $limit = 10) {
        $limit = (int)$limit;
        $sql = "SELECT t2.*, COUNT(*) as relation_count 
                FROM tags t1 
                INNER JOIN news_tags nt1 ON t1.id = nt1.tag_id 
                INNER JOIN news_tags nt2 ON nt1.news_id = nt2.news_id 
                INNER JOIN tags t2 ON nt2.tag_id = t2.id 
                INNER JOIN news n ON nt1.news_id = n.id 
                WHERE t1.id = :tag_id1 AND t2.id != :tag_id2 
                AND t2.is_active = 1 AND n.status = 'published' 
                GROUP BY t2.id 
                ORDER BY relation_count DESC, t2.usage_count DESC 
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql, [
            'tag_id1' => $tagId,
            'tag_id2' => $tagId
        ]);
    }
    
    /**
     * Sitemap için etiketleri getir
     */
    public function getForSitemap() {
        $sql = "SELECT slug, updated_at FROM {$this->table} 
                WHERE is_active = 1 AND usage_count > 0 
                ORDER BY usage_count DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Kullanılmayan etiketleri temizle
     */
    public function cleanUnusedTags() {
        return $this->db->query(
            "DELETE FROM {$this->table} WHERE usage_count = 0 AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
    }
}
?>
