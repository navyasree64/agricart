<?php
/**
 * Railway PHP Router Script
 * Handles serving static files and PHP scripts for Railway deployment
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Remove leading slash
$file = __DIR__ . $uri;

// Serve static files directly (images, CSS, JS)
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    // Set correct content type for static assets
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $mime_types = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'ico'  => 'image/x-icon',
        'svg'  => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
    ];
    if (isset($mime_types[$ext])) {
        header('Content-Type: ' . $mime_types[$ext]);
        readfile($file);
        return true;
    }
    // For PHP files, return false so built-in server handles them
    if ($ext === 'php') {
        return false;
    }
    readfile($file);
    return true;
}

// Route directory requests to index.php
if ($uri === '/' || is_dir($file)) {
    require __DIR__ . '/index.php';
    return true;
}

// For everything else, let PHP built-in server handle it
return false;
