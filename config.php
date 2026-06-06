<?php
/**
 * Configuration File
 * 
 * This file handles database configuration and site configuration
 * dynamically depending on whether it's running locally or on production (InfinityFree).
 */

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. ENVIRONMENT DETECTION
// Detect if running on localhost (local development like XAMPP or WSL, or CLI mode)
$isLocalhost = php_sapi_name() === 'cli'
    || (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) 
    || (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'localhost') 
    || (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

if ($isLocalhost) {
    // ==========================================
    // LOCAL DEVELOPMENT SETTINGS (XAMPP / WSL)
    // ==========================================
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'agri_ecommerce');
    define('DB_PORT', 3306);
    
    // Dynamically build local base URL depending on directory structure
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    if (isset($_SERVER['HTTP_HOST'])) {
        if (strpos($_SERVER['REQUEST_URI'], '/webfinal/') !== false || strpos($_SERVER['SCRIPT_NAME'], '/webfinal/') !== false) {
            define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . '/webfinal/');
        } else {
            define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . '/');
        }
    } else {
        define('BASE_URL', 'http://localhost/webfinal/');
    }
} else {
    // ==========================================
    // PRODUCTION SETTINGS (InfinityFree)
    // ==========================================
    define('DB_HOST', 'sql207.infinityfree.com');
    define('DB_USER', 'if0_42108185');
    define('DB_PASS', 'jpSlFNwdGePrqE');
    define('DB_NAME', 'if0_42108185_agricart');
    define('DB_PORT', 3306);
    
    // InfinityFree free plan has no SSL — always use http://
    // (Upgrading to a paid plan adds HTTPS support)
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'agricart.ifree.page';
    define('BASE_URL', 'http://' . $host . '/');
}
