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

    $db_name = getenv('DB_NAME') ?: 'tienda_wp';
    $db_user = getenv('DB_USER') ?: 'root';
    $db_password = getenv('DB_PASSWORD') ?: '';
    $db_host = getenv('DB_HOST') ?: 'localhost';

    // Azure Database for MySQL: usar FQDN sin puerto en DB_HOST
    $db_host = preg_replace('/:\d+$/', '', $db_host);

    define('DB_NAME', $db_name);
    define('DB_USER', $db_user);
    define('DB_PASSWORD', $db_password);
    define('DB_HOST', $db_host);
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

    // MySQL SSL para Azure Database for MySQL (opcional, activado solo con DB_SSL=true)
    $db_ssl = filter_var(getenv('DB_SSL') ?: false, FILTER_VALIDATE_BOOLEAN);
    $mysql_ssl_ca = $db_ssl ? (getenv('MYSQL_SSL_CA') ?: '') : '';
    if ($db_ssl && $mysql_ssl_ca !== '') {
        define('MYSQL_SSL_CA', $mysql_ssl_ca);
    }
}

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// SSL para Azure Database for MySQL: solo si DB_SSL=true
if (!defined('MYSQL_CLIENT_FLAGS')) {
    define('MYSQL_CLIENT_FLAGS', $db_ssl ? MYSQLI_CLIENT_SSL : 0);
}

require_once ABSPATH . 'wp-settings.php';
