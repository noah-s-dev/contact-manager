<?php
/**
 * Base URL Configuration
 * Set the base URL for the application to ensure proper redirects
 * 
 * For local development:
 * - If project is in root: http://localhost/
 * - If project is in subdirectory: http://localhost/project_name/
 * 
 * For production:
 * - Update this to your domain: https://yourdomain.com/
 */

// Auto-detect base URL from current request
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Get the directory path (without the filename)
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = dirname($scriptName);
    
    // Remove trailing slash if it's just '/'
    $basePath = ($dir === '/' || $dir === '\\') ? '' : rtrim($dir, '/');
    
    return $protocol . $host . $basePath;
}

// Define base URL constant
define('BASE_URL', getBaseUrl());

/**
 * Generate a full URL from a relative path
 * @param string $path Relative path (e.g., 'dashboard.php', 'login.php')
 * @return string Full URL
 */
function url($path = '') {
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $base . '/' . $path;
}

/**
 * Redirect to a URL (relative or absolute)
 * @param string $path Path to redirect to
 * @param bool $useBaseUrl Whether to use base URL or relative path
 */
function redirect($path, $useBaseUrl = false) {
    if ($useBaseUrl) {
        header("Location: " . url($path));
    } else {
        header("Location: " . $path);
    }
    exit();
}
?>

