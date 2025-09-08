<?php
/**
 * Base Controller Sınıfı
 * LooMix.Click
 */

class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Note: render() metodu kaldırıldı - View sınıfını doğrudan kullanın
    
    /**
     * JSON yanıt döndür
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * Yönlendirme yap
     */
    protected function redirect($url, $statusCode = 302) {
        Router::redirect($url, $statusCode);
    }
    
    /**
     * POST verisi al (JSON gövde ve diğer methodlar için destekli)
     */
    protected function post($key = null, $default = null) {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');

        // State-changing methodlar için JSON desteği
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            if (stripos($contentType, 'application/json') !== false) {
                $rawInput = file_get_contents('php://input');
                if ($rawInput !== false && $rawInput !== '') {
                    $jsonData = json_decode($rawInput, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                        if ($key === null) {
                            return $jsonData;
                        }
                        return array_key_exists($key, $jsonData) ? $jsonData[$key] : $default;
                    }
                }
            }

            // JSON değilse POST'ta doğrudan $_POST'u kullan
            if ($method === 'POST') {
                if ($key === null) {
                    return $_POST;
                }
                return isset($_POST[$key]) ? $_POST[$key] : $default;
            }

            // PUT/PATCH/DELETE için urlencoded body parse et
            $rawInput = file_get_contents('php://input');
            $parsedData = [];
            if ($rawInput !== false && $rawInput !== '') {
                parse_str($rawInput, $parsedData);
            }
            if ($key === null) {
                return $parsedData;
            }
            return isset($parsedData[$key]) ? $parsedData[$key] : $default;
        }

        // Diğer durumlar (GET vb.)
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * GET verisi al
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * Input validasyonu
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $ruleList) {
            $value = isset($data[$field]) ? $data[$field] : '';
            $fieldRules = explode('|', $ruleList);
            
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $errors[$field][] = "{$field} alanı zorunludur.";
                } elseif (strpos($rule, 'min:') === 0) {
                    $min = (int)substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "{$field} alanı en az {$min} karakter olmalıdır.";
                    }
                } elseif (strpos($rule, 'max:') === 0) {
                    $max = (int)substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = "{$field} alanı en fazla {$max} karakter olmalıdır.";
                    }
                } elseif ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "{$field} alanı geçerli bir email adresi olmalıdır.";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Mevcut URL'yi al
     */
    protected function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Admin girişi kontrol et
     */
    protected function requireAdmin() {
        // Session'ı başlat (zaten başlamışsa devam eder)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION[ADMIN_SESSION_NAME]) || $_SESSION[ADMIN_SESSION_NAME] !== true) {
            $this->redirect('/admin/login');
        }
    }
    
    /**
     * CSRF token oluştur
     */
    protected function generateCsrfToken() {
        // Session'ı başlat (zaten başlamışsa devam eder)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * CSRF token kontrolü
     */
    protected function verifyCsrfToken($token) {
        // Session'ı başlat (zaten başlamışsa devam eder)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        $sessionToken = $_SESSION['csrf_token'];
        if (!is_string($sessionToken) || !is_string($token) || $token === '') {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }
}
?>
