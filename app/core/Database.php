<?php
/**
 * Database Sınıfı - PDO tabanlı veritabanı yönetimi
 * LooMix.Click
 */

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Singleton pattern
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Veritabanına bağlan
     */
    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            // MySQL session time_zone'ı uygulama zaman dilimine ayarla
            try {
                // PHP timezone'u offset'e çevir (+03:00 gibi)
                $dt = new DateTime('now', new DateTimeZone(defined('APP_TIMEZONE') ? APP_TIMEZONE : date_default_timezone_get()));
                $offsetSeconds = $dt->getOffset();
                $sign = $offsetSeconds >= 0 ? '+' : '-';
                $abs = abs($offsetSeconds);
                $hours = str_pad((string)floor($abs / 3600), 2, '0', STR_PAD_LEFT);
                $minutes = str_pad((string)floor(($abs % 3600) / 60), 2, '0', STR_PAD_LEFT);
                $mysqlTz = "$sign$hours:$minutes"; // örn: +03:00
                $this->pdo->exec("SET time_zone = '" . $mysqlTz . "'");
            } catch (Throwable $t) {
                // Sessiyon time_zone set edilemezse sessizce devam et
            }
            
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Veritabanı bağlantı hatası: " . $e->getMessage());
            } else {
                die("Veritabanı bağlantı hatası");
            }
        }
    }
    
    /**
     * PDO örneğini al
     */
    public function getPdo() {
        return $this->pdo;
    }
    
    /**
     * Sorgu çalıştır
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                throw new Exception("SQL Hatası: " . $e->getMessage() . " - SQL: " . $sql);
            } else {
                throw new Exception("Veritabanı hatası");
            }
        }
    }
    
    /**
     * Tek satır getir
     */
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Çoklu satır getir
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Tek değer getir
     */
    public function fetchColumn($sql, $params = [], $column = 0) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }
    
    /**
     * INSERT işlemi
     */
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * UPDATE işlemi
     */
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = '';
        foreach (array_keys($data) as $key) {
            $setClause .= $key . ' = :' . $key . ', ';
        }
        $setClause = rtrim($setClause, ', ');
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $params = array_merge($data, $whereParams);
        return $this->query($sql, $params);
    }
    
    /**
     * DELETE işlemi
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params);
    }
    
    /**
     * Tabloda kayıt sayısını getir
     */
    public function count($table, $where = '', $params = []) {
        $sql = "SELECT COUNT(*) FROM {$table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        
        return $this->fetchColumn($sql, $params);
    }
    
    /**
     * Tablo var mı kontrol et
     */
    public function tableExists($table) {
        $sql = "SHOW TABLES LIKE :table";
        $result = $this->fetch($sql, ['table' => $table]);
        return !empty($result);
    }
    
    /**
     * Transaction başlat
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Transaction'ı onayla
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Transaction'ı geri al
     */
    public function rollback() {
        return $this->pdo->rollback();
    }
}
?>
