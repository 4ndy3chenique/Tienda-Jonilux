# Despliegue en Azure App Service

Este repositorio está preparado para desplegarse en **Azure App Service** con **PHP 8.5** y **Azure Database for MySQL Flexible Server**.

## 1. Configuración del App Service

En el portal de Azure, entra a tu App Service y abre **Configuración → Configuración de la aplicación**. Añade estas variables:

| Clave | Valor |
|---|---|
| `DB_NAME` | `tienda_wp` |
| `DB_USER` | `JONILUX` |
| `DB_PASSWORD` | la contraseña del servidor MySQL |
| `DB_HOST` | `mysql-jonilux-db.mysql.database.azure.com:3306` |
| `WP_HOME` | `https://app-jonilux-store-ddhpbxfdd5a4g6ff.brazilsouth-01.azurewebsites.net` |
| `WP_SITEURL` | `https://app-jonilux-store-ddhpbxfdd5a4g6ff.brazilsouth-01.azurewebsites.net` |
| `MYSQL_SSL_CA` | `/home/site/wwwroot/bin/DigiCertGlobalRootG2.crt.pem` (Linux) o `D:\home\site\wwwroot\bin\DigiCertGlobalRootG2.crt.pem` (Windows) |
| `WEBSITES_PHP_VERSION` | `8.5` |
| `WEBSITES_ENABLE_APP_SERVICE_STORAGE` | `true` |

También añade las 8 claves de seguridad de WordPress (`AUTH_KEY`, `SECURE_AUTH_KEY`, `LOGGED_IN_KEY`, `NONCE_KEY`, `AUTH_SALT`, `SECURE_AUTH_SALT`, `LOGGED_IN_SALT`, `NONCE_SALT`). Puedes generarlas en https://api.wordpress.org/secret-key/1.1/salt/.

Guarda los cambios y **reinicia** el App Service.

## 2. Regla de firewall de MySQL

En el servidor **Azure Database for MySQL**, abre **Redes** y permite el acceso desde el App Service:

- Activa **Allow public access from any Azure service within Azure to this server**, o
- Añade las IPs de salida del App Service como regla de firewall.

## 3. SSL obligatorio

El servidor MySQL requiere SSL. El archivo `bin/DigiCertGlobalRootG2.crt.pem` ya está en el repositorio y `wp-config.php` lo usa cuando existe `MYSQL_SSL_CA`. No desactiva SSL.

## 4. Despliegue

El repositorio incluye el workflow de GitHub Actions (`.github/workflows/main_app-jonilux-store.yml`) que despliega automáticamente en cada push a `main`. También puedes desencadenarlo manualmente desde **Acciones** en GitHub.

## 5. Notas

- El `.htaccess` usa una base dinámica: `/tienda/` en local y `/` en Azure. En Linux, el App Service usa NGINX e ignora `.htaccess`. El repositorio incluye `nginx.conf` para los permalinks de WordPress. Si los enlaces permanentes fallan, en **Configuración → Configuración general → Comando de inicio** pon:
  `cp /home/site/wwwroot/nginx.conf /etc/nginx/conf.d/default.conf`
- Las subidas de `wp-content/uploads` se persisten en el almacenamiento del App Service gracias a `WEBSITES_ENABLE_APP_SERVICE_STORAGE=true`.
- La base de datos ya fue importada en Azure con las URLs de producción.
