<?php
/**
 * Autoloader - Sınıf dosyalarını otomatik yükler
 * LooMix.Click
 */

spl_autoload_register(function($className) {
    $directories = [
        'app/controllers/',
        'app/models/',
        'app/core/',
        'app/helpers/',
        'includes/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // PSR-4 style autoloading için namespace desteği
    $namespaceFile = str_replace('\\', '/', $className) . '.php';
    
    foreach ($directories as $directory) {
        $file = $directory . $namespaceFile;
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Ortak fonksiyonları yükle
require_once 'includes/functions.php';
?>
