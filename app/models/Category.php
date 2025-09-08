<?php
/**
 * Category Model - Kategori verileri yönetimi
 * LooMix.Click
 */

class Category extends Model {
    protected $table = 'categories';
    
    /**
     * Aktif kategorileri getir
     */
    public function getActiveCategories($parentId = null) {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        $params = [];
        
        if ($parentId === null) {
            $sql .= " AND parent_id IS NULL";
        } else {
            $sql .= " AND parent_id = :parent_id";
            $params['parent_id'] = $parentId;
        }
        
        $sql .= " ORDER BY sort_order ASC, name ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Ana kategorileri getir (navigation için)
     */
    public function getMainCategories() {
        return $this->getActiveCategories(null);
    }
    
    /**
     * Alt kategorileri getir
     */
    public function getSubCategories($parentId) {
        return $this->getActiveCategories($parentId);
    }
    
    /**
     * Slug ile kategori getir
     */
    public function getBySlug($slug) {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND is_active = 1";
        return $this->db->fetch($sql, ['slug' => $slug]);
    }
    
    /**
     * Kategori haber sayıları ile birlikte getir
     */
    public function getCategoriesWithNewsCount() {
        $sql = "SELECT c.*, COUNT(n.id) as news_count 
                FROM {$this->table} c 
                LEFT JOIN news n ON c.id = n.category_id AND n.status = 'published' 
                WHERE c.is_active = 1 AND c.parent_id IS NULL 
                GROUP BY c.id 
                ORDER BY c.sort_order ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Kategorinin haber sayısını getir
     */
    public function getNewsCount($categoryId) {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM news WHERE category_id = :category_id AND status = 'published'",
            ['category_id' => $categoryId]
        );
    }
    
    /**
     * Popüler kategorileri getir (haber sayısına göre)
     */
    public function getPopularCategories($limit = 10) {
        $sql = "SELECT c.*, COUNT(n.id) as news_count 
                FROM {$this->table} c 
                INNER JOIN news n ON c.id = n.category_id AND n.status = 'published' 
                WHERE c.is_active = 1 
                GROUP BY c.id 
                HAVING news_count > 0 
                ORDER BY news_count DESC, c.sort_order ASC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Kategori breadcrumb'ı getir
     */
    public function getBreadcrumb($categoryId) {
        $breadcrumb = [];
        $currentId = $categoryId;
        
        while ($currentId) {
            $category = $this->find($currentId);
            if (!$category) break;
            
            array_unshift($breadcrumb, $category);
            $currentId = $category['parent_id'];
        }
        
        return $breadcrumb;
    }
    
    /**
     * Kategori ağacını getir (hiyerarşik yapı)
     */
    public function getCategoryTree() {
        // Ana kategorileri al
        $mainCategories = $this->getMainCategories();
        
        // Her ana kategorinin alt kategorilerini ekle
        foreach ($mainCategories as &$mainCategory) {
            $mainCategory['subcategories'] = $this->getSubCategories($mainCategory['id']);
        }
        
        return $mainCategories;
    }
    
    /**
     * Kategorideki son haberleri getir
     */
    public function getCategoryLatestNews($categoryId, $limit = 5) {
        $sql = "SELECT n.id, n.title, n.slug, n.featured_image, n.publish_date 
                FROM news n 
                WHERE n.category_id = :category_id AND n.status = 'published' AND n.publish_date <= NOW() 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            'category_id' => $categoryId,
            'limit' => $limit
        ]);
    }
    
    /**
     * SEO bilgileri ile kategori getir
     */
    public function getCategoryWithSeo($slug) {
        $category = $this->getBySlug($slug);
        
        if (!$category) {
            return null;
        }
        
        // SEO meta bilgilerini kontrol et
        $seoMeta = $this->db->fetch(
            "SELECT * FROM seo_meta WHERE page_type = 'category' AND page_identifier = :slug AND is_active = 1",
            ['slug' => $slug]
        );
        
        if ($seoMeta) {
            $category = array_merge($category, [
                'seo_title' => $seoMeta['title'],
                'seo_description' => $seoMeta['description'],
                'seo_keywords' => $seoMeta['keywords'],
                'canonical_url' => $seoMeta['canonical_url'],
                'og_title' => $seoMeta['og_title'],
                'og_description' => $seoMeta['og_description'],
                'og_image' => $seoMeta['og_image']
            ]);
        }
        
        return $category;
    }
    
    /**
     * Kategori slug'ını güncelle
     */
    public function updateSlug($id, $name) {
        $slug = createSlug($name);
        
        // Slug benzersizliği kontrol et
        $existingCategory = $this->db->fetch(
            "SELECT id FROM {$this->table} WHERE slug = :slug AND id != :id",
            ['slug' => $slug, 'id' => $id]
        );
        
        if ($existingCategory) {
            $slug = $slug . '-' . $id;
        }
        
        $this->update($id, ['slug' => $slug]);
        return $slug;
    }
    
    /**
     * Sitemap için kategorileri getir
     */
    public function getForSitemap() {
        $sql = "SELECT slug, updated_at FROM {$this->table} 
                WHERE is_active = 1 
                ORDER BY sort_order ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Admin panel için kategori listesi
     */
    public function getAllForAdmin() {
        $sql = "SELECT c.*, p.name as parent_name, 
                COUNT(n.id) as news_count 
                FROM {$this->table} c 
                LEFT JOIN {$this->table} p ON c.parent_id = p.id 
                LEFT JOIN news n ON c.id = n.category_id 
                GROUP BY c.id 
                ORDER BY c.sort_order ASC, c.name ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Kategori sıralamasını güncelle
     */
    public function updateOrder($categoryId, $newOrder) {
        return $this->update($categoryId, ['sort_order' => $newOrder]);
    }
    
    /**
     * Kategoriyi aktif/pasif et
     */
    public function toggleActive($categoryId) {
        $category = $this->find($categoryId);
        if (!$category) {
            return false;
        }
        
        $newStatus = $category['is_active'] ? 0 : 1;
        return $this->update($categoryId, ['is_active' => $newStatus]);
    }
}
?>
