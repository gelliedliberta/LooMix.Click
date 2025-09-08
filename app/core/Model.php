<?php
/**
 * Base Model Sınıfı
 * LooMix.Click
 */

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $timestamps = true;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Tüm kayıtları getir
     */
    public function all($orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * ID ile kayıt bul
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Koşula göre kayıt bul
     */
    public function findBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        return $this->db->fetch($sql, ['value' => $value]);
    }
    
    /**
     * Koşula göre kayıtları getir
     */
    public function where($column, $operator, $value = null, $orderBy = null, $limit = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->db->fetchAll($sql, ['value' => $value]);
    }
    
    /**
     * Yeni kayıt oluştur
     */
    public function create($data) {
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Kayıt güncelle
     */
    public function update($id, $data) {
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $where = "{$this->primaryKey} = :id";
        return $this->db->update($this->table, $data, $where, ['id' => $id]);
    }
    
    /**
     * Kayıt sil
     */
    public function delete($id) {
        $where = "{$this->primaryKey} = :id";
        return $this->db->delete($this->table, $where, ['id' => $id]);
    }
    
    /**
     * Soft delete (eğer deleted_at kolonu varsa)
     */
    public function softDelete($id) {
        $data = ['deleted_at' => date('Y-m-d H:i:s')];
        return $this->update($id, $data);
    }
    
    /**
     * Kayıt sayısı
     */
    public function count($where = '', $params = []) {
        return $this->db->count($this->table, $where, $params);
    }
    
    /**
     * Sayfalama
     */
    public function paginate($page = 1, $perPage = 10, $where = '', $params = [], $orderBy = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $sql .= " LIMIT {$offset}, {$perPage}";
        
        $data = $this->db->fetchAll($sql, $params);
        
        $totalCount = $this->count($where, $params);
        $totalPages = ceil($totalCount / $perPage);
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_count' => $totalCount,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_previous' => $page > 1
        ];
    }
    
    /**
     * Raw SQL sorgusu
     */
    public function query($sql, $params = []) {
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Raw SQL sorgusu - tek satır
     */
    public function queryOne($sql, $params = []) {
        return $this->db->fetch($sql, $params);
    }
    
    /**
     * Arama yap (LIKE ile)
     */
    public function search($column, $term, $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} LIKE :term";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->db->fetchAll($sql, ['term' => "%{$term}%"]);
    }
    
    /**
     * Son kaydı getir
     */
    public function latest($column = null) {
        $orderBy = $column ?: ($this->timestamps ? 'created_at DESC' : $this->primaryKey . ' DESC');
        return $this->db->fetch("SELECT * FROM {$this->table} ORDER BY {$orderBy} LIMIT 1");
    }
}
?>
