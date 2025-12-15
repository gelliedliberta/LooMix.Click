<?php
/**
 * SocialMedia Model - Sosyal medya linkleri yönetimi
 * LooMix.Click
 */

class SocialMedia extends Model {
    protected $table = 'social_media_links';
    
    /**
     * Tüm sosyal medya linklerini getir (admin için)
     */
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY display_order ASC, name ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Aktif sosyal medya linklerini getir (frontend için)
     * 
     * @param string|null $position 'header' veya 'footer' - null ise hepsi
     * @return array
     */
    public function getActive($position = null) {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        
        if ($position === 'header') {
            $sql .= " AND show_in_header = 1";
        } elseif ($position === 'footer') {
            $sql .= " AND show_in_footer = 1";
        }
        
        $sql .= " ORDER BY display_order ASC, name ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * ID'ye göre sosyal medya linkini getir
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Platform'a göre sosyal medya linkini getir
     */
    public function getByPlatform($platform) {
        $sql = "SELECT * FROM {$this->table} WHERE platform = :platform";
        return $this->db->fetch($sql, ['platform' => $platform]);
    }
    
    /**
     * Sosyal medya linkini kaydet (yeni veya güncelle)
     * 
     * @param array $data Link verileri
     * @return int|bool Created/Updated ID veya false
     */
    public function save($data) {
        // Gerekli alanları kontrol et
        if (empty($data['platform']) || empty($data['name']) || empty($data['icon'])) {
            return false;
        }
        
        // ID varsa güncelle, yoksa yeni oluştur
        if (!empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            
            $updateData = [
                'name' => $data['name'],
                'icon' => $data['icon'],
                'url' => $data['url'] ?? null,
                'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
                'display_order' => isset($data['display_order']) ? (int)$data['display_order'] : 0,
                'show_in_header' => isset($data['show_in_header']) ? (int)$data['show_in_header'] : 1,
                'show_in_footer' => isset($data['show_in_footer']) ? (int)$data['show_in_footer'] : 1,
                'color' => $data['color'] ?? null
            ];
            
            $this->update($id, $updateData);
            return $id;
        } else {
            // Yeni kayıt oluştur
            $insertData = [
                'platform' => $data['platform'],
                'name' => $data['name'],
                'icon' => $data['icon'],
                'url' => $data['url'] ?? null,
                'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
                'display_order' => isset($data['display_order']) ? (int)$data['display_order'] : 0,
                'show_in_header' => isset($data['show_in_header']) ? (int)$data['show_in_header'] : 1,
                'show_in_footer' => isset($data['show_in_footer']) ? (int)$data['show_in_footer'] : 1,
                'color' => $data['color'] ?? null
            ];
            
            return $this->create($insertData);
        }
    }
    
    /**
     * Sosyal medya linkini aktif/pasif yap
     * 
     * @param int $id Link ID
     * @return bool
     */
    public function toggleStatus($id) {
        $link = $this->getById($id);
        if (!$link) {
            return false;
        }
        
        $newStatus = $link['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }
    
    /**
     * Header'da gösterim durumunu değiştir
     */
    public function toggleHeader($id) {
        $link = $this->getById($id);
        if (!$link) {
            return false;
        }
        
        $newStatus = $link['show_in_header'] ? 0 : 1;
        return $this->update($id, ['show_in_header' => $newStatus]);
    }
    
    /**
     * Footer'da gösterim durumunu değiştir
     */
    public function toggleFooter($id) {
        $link = $this->getById($id);
        if (!$link) {
            return false;
        }
        
        $newStatus = $link['show_in_footer'] ? 0 : 1;
        return $this->update($id, ['show_in_footer' => $newStatus]);
    }
    
    /**
     * Gösterim sırasını güncelle
     * 
     * @param int $id Link ID
     * @param int $order Yeni sıra
     * @return bool
     */
    public function updateOrder($id, $order) {
        return $this->update($id, ['display_order' => (int)$order]);
    }
    
    /**
     * Sosyal medya linkini sil
     * 
     * @param int $id Link ID
     * @return bool
     */
    public function deleteLink($id) {
        // RSS gibi sistem linklerini silme (platform='rss' gibi)
        $link = $this->getById($id);
        if ($link && $link['platform'] === 'rss') {
            return false; // RSS silinemez
        }
        
        return $this->delete($id);
    }
    
    /**
     * Toplu sıralama güncelle
     * 
     * @param array $orders [id => order] şeklinde array
     * @return bool
     */
    public function bulkUpdateOrders($orders) {
        if (empty($orders) || !is_array($orders)) {
            return false;
        }
        
        foreach ($orders as $id => $order) {
            $this->updateOrder($id, $order);
        }
        
        return true;
    }
    
    /**
     * Platform ikonlarının listesi (öneriler için)
     */
    public static function getIconSuggestions() {
        return [
            'fab fa-facebook' => 'Facebook',
            'fab fa-x-twitter' => 'Twitter (X)',
            'fab fa-instagram' => 'Instagram',
            'fab fa-youtube' => 'YouTube',
            'fab fa-linkedin' => 'LinkedIn',
            'fab fa-tiktok' => 'TikTok',
            'fab fa-telegram' => 'Telegram',
            'fab fa-whatsapp' => 'WhatsApp',
            'fab fa-pinterest' => 'Pinterest',
            'fab fa-snapchat' => 'Snapchat',
            'fab fa-reddit' => 'Reddit',
            'fab fa-discord' => 'Discord',
            'fas fa-rss' => 'RSS'
        ];
    }
    
    /**
     * Platform renkleri (öneriler için)
     */
    public static function getColorSuggestions() {
        return [
            'facebook' => '#1877F2',
            'twitter' => '#000000',
            'instagram' => '#E4405F',
            'youtube' => '#FF0000',
            'linkedin' => '#0A66C2',
            'tiktok' => '#000000',
            'telegram' => '#0088CC',
            'whatsapp' => '#25D366',
            'pinterest' => '#BD081C',
            'snapchat' => '#FFFC00',
            'reddit' => '#FF4500',
            'discord' => '#5865F2',
            'rss' => '#FF6600'
        ];
    }
}
?>

