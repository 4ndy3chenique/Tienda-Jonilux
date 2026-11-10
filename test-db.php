<?php
// Script de diagnóstico de conexión MySQL (Azure Database for MySQL Flexible Server).
// No imprime contraseñas. Elimínalo después de diagnosticar.

header('Content-Type: text/plain; charset=utf-8');

$local = __DIR__ . '/wp-config-local.php';
if (is_file($local)) {
    // Cargar solo definiciones de DB_* sin arrancar WordPress
    $contents = file_get_contents($local);
    foreach (['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST'] as $k) {
        if (preg_match("/define\\(\\s*'" . $k . "'\\s*,\\s*'([^']*)'\\s*\\)/", $contents, $m)) {
            if (!defined($k)) {
                define($k, $m[1]);
            }
        }
    }
}

$db_host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
$db_user = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: '');
$db_pass = defined('DB_PASSWORD') ? DB_PASSWORD : (getenv('DB_PASSWORD') ?: '');
$db_name = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: '');

$db_host = preg_replace('/:\d+$/', '', (string) $db_host);
$port = 3306;

$ssl_ca = getenv('MYSQL_SSL_CA') ?: '';
if ($ssl_ca === '') {
    $candidates = [
        __DIR__ . '/DigiCertGlobalRootCA.crt.pem',
        __DIR__ . '/DigiCertGlobalRootCA.crt',
        __DIR__ . '/bin/DigiCertGlobalRootG2.crt.pem',
    ];
    foreach ($candidates as $c) {
        if (is_file($c)) {
            $ssl_ca = $c;
            break;
        }
    }
}

echo "DB_HOST=" . $db_host . "\n";
echo "DB_USER=" . $db_user . "\n";
echo "DB_NAME=" . $db_name . "\n";
echo "SSL_CA=" . ($ssl_ca !== '' ? $ssl_ca : '(none)') . "\n";
echo "SSL_CA_EXISTS=" . ($ssl_ca !== '' && is_file($ssl_ca) ? 'yes' : 'no') . "\n";
echo "MYSQLI_CLIENT_SSL=" . (defined('MYSQLI_CLIENT_SSL') ? MYSQLI_CLIENT_SSL : 'undefined') . "\n\n";

if (!function_exists('mysqli_init')) {
    echo "ERROR: extensión mysqli no disponible\n";
    exit(1);
}

$mysqli = mysqli_init();
if (!$mysqli) {
    echo "ERROR: mysqli_init falló\n";
    exit(1);
}

mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

if ($ssl_ca !== '' && is_file($ssl_ca)) {
    mysqli_ssl_set($mysqli, null, null, $ssl_ca, null, null);
}

$flags = defined('MYSQLI_CLIENT_SSL') ? MYSQLI_CLIENT_SSL : 0;
$ok = @mysqli_real_connect($mysqli, $db_host, $db_user, $db_pass, $db_name, $port, null, $flags);

if ($ok) {
    echo "OK: conexión exitosa\n";
    $res = @mysqli_query($mysqli, 'SELECT VERSION() AS v');
    if ($res && ($row = mysqli_fetch_assoc($res))) {
        echo "SERVER_VERSION=" . $row['v'] . "\n";
    }
    mysqli_close($mysqli);
    exit(0);
}

echo "ERROR: conexión fallida\n";
echo "MYSQL_ERRNO=" . mysqli_connect_errno() . "\n";
echo "MYSQL_ERROR=" . mysqli_connect_error() . "\n";
exit(1);
