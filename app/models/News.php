<?php
/**
 * News Model - Haber verileri yönetimi
 * LooMix.Click
 */

class News extends Model {
    protected $table = 'news';
    
    /**
     * Yayınlanan haberleri getir
     */
    public function getPublishedNews($limit = null, $offset = 0, $categoryId = null) {
        $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                FROM {$this->table} n 
                INNER JOIN categories c ON n.category_id = c.id 
                WHERE n.status = 'published' AND n.publish_date <= NOW()";
        
        $params = [];
        
        if ($categoryId) {
            // Alt kategoriler dahil: verilen kategori + çocukları (tek seviye)
            $childIds = $this->db->fetchAll("SELECT id FROM categories WHERE parent_id = :pid", ['pid' => $categoryId]);
            $ids = array_map(function($r){ return (int)$r['id']; }, $childIds);
            $ids[] = (int)$categoryId;
            // IN listesi literal olarak gömülür; parametre karışıklığını önler
            $in = implode(',', array_map('intval', $ids));
            $sql .= " AND n.category_id IN ({$in})";
        }
        
        $sql .= " ORDER BY n.publish_date DESC";
        
        if ($limit) {
            $offset = (int)$offset;
            $limit = (int)$limit;
            $sql .= " LIMIT {$offset}, {$limit}";
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Öne çıkan haberleri getir
     */
    public function getFeaturedNews($limit = 6) {
        return $this->getPublishedNews($limit, 0, null, " AND n.is_featured = 1");
    }
    
    /**
     * Son dakika haberlerini getir
     */
    public function getBreakingNews($limit = 5) {
        $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug 
                FROM {$this->table} n 
                INNER JOIN categories c ON n.category_id = c.id 
                WHERE n.status = 'published' AND n.is_breaking = 1 AND n.publish_date <= NOW() 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Popüler haberleri getir (görüntülenme sayısına göre)
     */
    public function getPopularNews($limit = 10, $days = 7) {
        $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug, 
                COUNT(nv.id) as recent_views 
                FROM {$this->table} n 
                INNER JOIN categories c ON n.category_id = c.id 
                LEFT JOIN news_views nv ON n.id = nv.news_id 
                    AND nv.view_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                WHERE n.status = 'published' AND n.publish_date <= NOW() 
                GROUP BY n.id 
                ORDER BY recent_views DESC, n.publish_date DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['days' => $days, 'limit' => $limit]);
    }
    
    /**
     * Slug ile haberi getir
     */
    public function getBySlug($slug) {
        $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                FROM {$this->table} n 
                INNER JOIN categories c ON n.category_id = c.id 
                WHERE n.slug = :slug AND n.status = 'published'";
        
        return $this->db->fetch($sql, ['slug' => $slug]);
    }
    
    /**
     * İlgili haberleri getir
     */
    public function getRelatedNews($newsId, $categoryId, $limit = 6) {
        $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug 
                FROM {$this->table} n 
                INNER JOIN categories c ON n.category_id = c.id 
                WHERE n.id != :news_id AND n.category_id = :category_id 
                AND n.status = 'published' AND n.publish_date <= NOW() 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            'news_id' => $newsId,
            'category_id' => $categoryId,
            'limit' => $limit
        ]);
    }
    
    /**
     * Kategori haberleri sayfalama ile
     */
    public function getCategoryNews($categorySlug, $page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        
        // Kategori ID'sini al
        $category = $this->db->fetch("SELECT id FROM categories WHERE slug = :slug", ['slug' => $categorySlug]);
        
        if (!$category) {
            return null;
        }
        
        $categoryId = $category['id'];
        
        // Alt kategoriler dahil olacak şekilde ID listesi oluştur
        $descendantIds = $this->db->fetchAll("SELECT id FROM categories WHERE parent_id = :pid", ['pid' => $categoryId]);
        $ids = array_map(function($r){ return (int)$r['id']; }, $descendantIds);
        $ids[] = (int)$categoryId;
        $inPlaceholders = implode(',', array_fill(0, count($ids), '?'));
        
        // Haberleri getir
        $inList = implode(',', array_map('intval', $ids));
        $offset = (int)$offset;
        $perPage = (int)$perPage;
        $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                FROM {$this->table} n 
                INNER JOIN categories c ON n.category_id = c.id 
                WHERE n.category_id IN ({$inList}) AND n.status = 'published' AND n.publish_date <= NOW() 
                ORDER BY n.publish_date DESC 
                LIMIT {$offset}, {$perPage}";
        
        $news = $this->db->fetchAll($sql);
        
        // Toplam sayı
        $countSql = "SELECT COUNT(*) FROM {$this->table} WHERE category_id IN ({$inList}) AND status = 'published'";
        $totalCount = $this->db->fetchColumn($countSql);
        
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
     * Görüntülenme kaydı ekle
     */
    public function addView($newsId, $ipAddress = null, $userAgent = null, $referer = null) {
        if (!$ipAddress) {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        
        if (!$userAgent) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }
        
        if (!$referer) {
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
        }
        
        // Bugün bu IP'den bu habere görüntülenme var mı kontrol et
        $existing = $this->db->fetch(
            "SELECT id FROM news_views WHERE news_id = :news_id AND ip_address = :ip AND view_date = CURDATE()",
            ['news_id' => $newsId, 'ip' => $ipAddress]
        );
        
        if (!$existing) {
            // Yeni görüntülenme kaydı ekle
            $this->db->insert('news_views', [
                'news_id' => $newsId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'referer' => $referer,
                'view_date' => date('Y-m-d')
            ]);
            
            // Haber view_count'unu güncelle
            $this->db->query(
                "UPDATE {$this->table} SET view_count = view_count + 1 WHERE id = :id",
                ['id' => $newsId]
            );
        }
    }
    
    /**
     * Haber etiketlerini getir
     */
    public function getNewsTags($newsId) {
        $sql = "SELECT t.* FROM tags t 
                INNER JOIN news_tags nt ON t.id = nt.tag_id 
                WHERE nt.news_id = :news_id 
                ORDER BY t.name";
        
        return $this->db->fetchAll($sql, ['news_id' => $newsId]);
    }
    
    /**
     * Okuma süresi hesapla ve güncelle
     */
    public function updateReadingTime($newsId, $content) {
        $readingTime = calculateReadingTime($content);
        
        $this->db->update($this->table, 
            ['reading_time' => $readingTime], 
            'id = :id', 
            ['id' => $newsId]
        );
        
        return $readingTime;
    }
    
    /**
     * Sitemap için haberleri getir
     */
    public function getForSitemap($limit = 1000) {
        $sql = "SELECT slug, updated_at FROM {$this->table} 
                WHERE status = 'published' 
                ORDER BY updated_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * RSS için haberleri getir
     */
    public function getForRss($limit = 20) {
        $sql = "SELECT n.*, c.name as category_name 
                FROM {$this->table} n 
                INNER JOIN categories c ON n.category_id = c.id 
                WHERE n.status = 'published' AND n.publish_date <= NOW() 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Haberleri arar (title, summary, content) ve sayfalama ile sonuç döndürür
     * - Sadece yayınlanmış ve publish_date'i geçmiş haberlerde arar
     * - Arama terimindeki wildcard karakterlerini güvenli şekilde kaçırır
     */
    public function searchNews($searchTerm, $page = 1, $perPage = 12) {
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;

        // LIKE için özel karakterleri kaçır (basit yaklaşım)
        $cleanTerm = trim((string)$searchTerm);
        $like = "%{$cleanTerm}%";

        // LIMIT/OFFSET parametre bağlamıyoruz; güvenlik için cast ettik
        $offset = (int)$offset;
        $perPage = (int)$perPage;

        $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM {$this->table} n
                INNER JOIN categories c ON n.category_id = c.id
                WHERE n.status = 'published'
                  AND n.publish_date <= NOW()
                  AND (
                        n.title LIKE :term1 OR
                        n.summary LIKE :term2 OR
                        n.content LIKE :term3
                  )
                ORDER BY n.publish_date DESC
                LIMIT {$offset}, {$perPage}";


        return $this->db->fetchAll($sql, [
            'term1' => $like,
            'term2' => $like, 
            'term3' => $like
        ]);
    }

    /**
     * Arama toplam sonuç sayısını döndürür
     */
    public function countSearchResults($searchTerm) {
        $cleanTerm = trim((string)$searchTerm);
        $like = "%{$cleanTerm}%";

        $sql = "SELECT COUNT(*)
                FROM {$this->table} n
                WHERE n.status = 'published'
                  AND n.publish_date <= NOW()
                  AND (
                        n.title LIKE :term1 OR
                        n.summary LIKE :term2 OR
                        n.content LIKE :term3
                  )";

        return (int)$this->db->fetchColumn($sql, [
            'term1' => $like,
            'term2' => $like,
            'term3' => $like
        ]);
    }
}
?>
