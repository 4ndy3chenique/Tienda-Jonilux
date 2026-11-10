<?php
/**
 * Configuración de WordPress lista para Azure App Service.
 * En local, si existe wp-config-local.php, se usa esa configuración.
 */

if (file_exists(__DIR__ . '/wp-config-local.php')) {
    require_once __DIR__ . '/wp-config-local.php';
} else {
    // HTTPS detrás del proxy de Azure App Service
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $_SERVER['HTTPS'] = 'on';
    }

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    define('DB_NAME', getenv('DB_NAME') ?: 'tienda_wp');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_CHARSET', 'utf8mb4');
    define('DB_COLLATE', '');

    define('AUTH_KEY', getenv('AUTH_KEY') ?: '');
    define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY') ?: '');
    define('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY') ?: '');
    define('NONCE_KEY', getenv('NONCE_KEY') ?: '');
    define('AUTH_SALT', getenv('AUTH_SALT') ?: '');
    define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT') ?: '');
    define('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT') ?: '');
    define('NONCE_SALT', getenv('NONCE_SALT') ?: '');

    $table_prefix = 'wp_';

    define('WP_DEBUG', filter_var(getenv('WP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));

    define('WP_HOME', getenv('WP_HOME') ?: $protocol . '://' . $host);
    define('WP_SITEURL', getenv('WP_SITEURL') ?: $protocol . '://' . $host);

    // MySQL SSL para Azure Database for MySQL
    if (getenv('MYSQL_SSL_CA') || getenv('DB_SSL')) {
        define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
        define('MYSQL_SSL_CA', getenv('MYSQL_SSL_CA') ?: '');
    }
}

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
