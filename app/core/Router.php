<?php
/**
 * Router Sınıfı - URL yönlendirme ve route yönetimi
 * LooMix.Click
 */

class Router {
    private $routes = [];
    private $notFoundCallback;
    private $currentUrl;
    private $method;
    
    public function __construct() {
        $this->currentUrl = $this->getCurrentUrl();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * GET route tanımla
     */
    public function get($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
    }
    
    /**
     * POST route tanımla
     */
    public function post($pattern, $callback) {
        $this->addRoute('POST', $pattern, $callback);
    }

    /**
     * POST route tanımla
     */
    public function delete($pattern, $callback) {
        $this->addRoute('DELETE', $pattern, $callback);
    }
    
    /**
     * Route ekle
     */
    private function addRoute($method, $pattern, $callback) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }
    
    /**
     * 404 callback'ini ayarla
     */
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
    }
    
    /**
     * Router'ı çalıştır
     */
    public function run() {
        $matched = false;
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $this->method) {
                continue;
            }
            
            $params = $this->matchRoute($route['pattern'], $this->currentUrl);
            
            if ($params !== false) {
                $matched = true;
                $this->executeCallback($route['callback'], $params);
                break;
            }
        }
        
        if (!$matched) {
            if ($this->notFoundCallback) {
                call_user_func($this->notFoundCallback);
            } else {
                http_response_code(404);
                echo "404 - Sayfa bulunamadı";
            }
        }
    }
    
    /**
     * Route pattern'i URL ile eşleştir
     */
    private function matchRoute($pattern, $url) {
        // {} parametrelerini regex'e çevir
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $url, $matches)) {
            array_shift($matches); // İlk tam eşleşmeyi çıkar
            return $matches;
        }
        
        return false;
    }
    
    /**
     * Callback'i çalıştır
     */
    private function executeCallback($callback, $params = []) {
        if (is_callable($callback)) {
            call_user_func_array($callback, $params);
        } elseif (is_string($callback)) {
            $this->executeControllerAction($callback, $params);
        }
    }
    
    /**
     * Controller action'ını çalıştır
     */
    private function executeControllerAction($callback, $params) {
        list($controllerName, $actionName) = explode('@', $callback);
        
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            
            if (method_exists($controller, $actionName)) {
                call_user_func_array([$controller, $actionName], $params);
            } else {
                throw new Exception("Method bulunamadı: {$controllerName}@{$actionName}");
            }
        } else {
            throw new Exception("Controller bulunamadı: {$controllerName}");
        }
    }
    
    /**
     * Mevcut URL'yi al
     */
    private function getCurrentUrl() {
        $url = $_SERVER['REQUEST_URI'];
        
        // Query string'i kaldır
        if (($pos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $pos);
        }
        
        // Base path'i çıkar (XAMPP için)
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptPath !== '/' && strpos($url, $scriptPath) === 0) {
            $url = substr($url, strlen($scriptPath));
        }
        
        // Başlangıçta / yoksa ekle
        if (empty($url) || $url[0] !== '/') {
            $url = '/' . $url;
        }
        
        return $url;
    }
    
    /**
     * URL oluştur
     */
    public static function url($path = '') {
        return url($path);
    }
    
    /**
     * Yönlendirme yap
     */
    public static function redirect($url, $statusCode = 302) {
        header("Location: " . url($url), true, $statusCode);
        exit();
    }
}
?>
