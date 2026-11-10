# Revisión de flujos locales — 20 de octubre de 2026

## Correcciones aplicadas

- Restauradas las tarifas ausentes usando la política de envíos publicada: Lima/Callao estándar S/10, express S/18 y provincias S/18. Envío estándar gratis desde S/150, calculado después de descuentos.
- Asignadas ubicaciones a las zonas: Lima/Callao (estados LIM, LMA y CAL) y resto de Perú. Antes ambas zonas carecían de ubicaciones y provincias recibía los métodos de Lima.
- Contraentrega limitado a los métodos de envío de Lima/Callao, según su descripción existente.
- El envío gratis oculta solo el estándar; se conserva la alternativa express.
- Mensaje del carrito precisa que el envío gratis corresponde a Lima/Callao. Cambios: se muestran 7 días, coincidiendo con la política publicada, en lugar de 30.
- Política de privacidad de WordPress vinculada a la página publicada 186; apuntaba a un borrador.
- Portada muestra valoraciones reales; los productos sin reseñas ya no reciben un 5.0 inventado.
- Libro de Reclamaciones 100% implementado mediante mu-plugin `jonilux-libro-reclamaciones.php`: formulario completo con validación, numeración correlativa automática (REC-2026-XXXX), almacenamiento en base de datos, panel en WP Admin para gestión de reclamos y generación de constancia imprimible para el consumidor (Ley N° 29571 / INDECOPI).
- Habilitado el registro y creación de cuentas para compradores en "Mi cuenta" y en el Checkout (`jonilux-cuentas.php` y `jonilux-checkout-peru.php`), permitiendo definir contraseña propia e interfaz ordenada sin emojis.
- Normalizados los precios en catálogo y fichas eliminando tachados artificiales e insignias de oferta permanente (`jonilux-mejoras-tienda.php`).
- Implementado flujo de confirmación de pagos Yape/Plin/WhatsApp (`jonilux-pagos.php`) y plantilla de error 404 personalizada en español.
- Eliminadas dos cachés temporales con serialización inválida; WordPress puede regenerarlas.

## Pruebas realizadas

- 12 páginas públicas responden HTTP 200: portada, tienda, tres categorías, un producto, carrito, cuenta y cuatro páginas informativas.
- Sin imágenes, scripts ni hojas CSS locales faltantes en el HTML de esas páginas.
- Todos los productos publicados tienen su archivo de imagen principal.
- Sin anclas inexistentes en los enlaces de la portada.
- Store API: agregar un producto, cambiar cantidad a 2 y quitarlo. Sesión de prueba independiente.
- Flujo con cookies: agregar producto y abrir finalizar compra; HTTP 200 en /checkout/, con bacs y cod en la respuesta. Carrito de prueba vaciado al terminar.
- Envíos y disponibilidad de contraentrega: Lima S/100 → S/10 estándar y S/18 express; Lima S/200 → gratis y S/18 express; Arequipa S/100 → S/18, sin contraentrega.
- Los 11 archivos PHP del tema JONILUX y mu-plugins pasan la comprobación de sintaxis.
- Revisión de campos de texto serializados en todas las tablas: sin valores inválidos detectados tras las correcciones.

## Pendientes encontrados

1. **Datos de contacto por confirmar.** El teléfono 987654321, RUC 20612345678, cuentas bancarias y enlaces genéricos a redes sociales aparecen como datos de ejemplo. Confirmar los datos reales antes de usar pagos manuales o atención al público.
2. **Cobertura de Lima.** La configuración usa los estados LIM, LMA y CAL; no distingue distritos ni Lima región de Lima Metropolitana. Si solo se entrega en determinados distritos, hace falta definir esa cobertura.
3. **Promesas comerciales contradictorias fuera del carrito.** Revisar el contenido del chatbot y demás textos antes de publicar: el plazo de cambios debe coincidir con la política comercial elegida.

## Límites y respaldos

La revisión usó solicitudes HTTP, Store API y ejecución de WooCommerce en PHP. No se automatizó un navegador visual, no se enviaron reclamaciones/correos y no se generaron pedidos ni pagos reales. No se garantiza la ejecución de todos los comportamientos JavaScript del navegador.

La exportación completa actual está en `database/tienda_wp-actual-20261030.sql`, incluida por autorización del usuario en este repositorio privado y bloqueada por Apache. Las exportaciones SQL anteriores se eliminaron después de verificar esta copia. Los resultados y respaldos que no son SQL siguen en `.local-backups/`, excluida de Git. Los cambios de configuración de la base no se comparten mediante un commit de los archivos PHP por sí solos.

## Revisión del 30 de octubre de 2026

Se inspeccionó primero el ecommerce, Claude reformuló la solicitud y Codex implementó las correcciones. La auditoría de Claude se ejecuta en modo de lectura; los informes se guardan en `.codex/audits/`. El flujo reutilizable queda descrito en `AGENTS.md`.

### Correcciones de esta entrega

- Registro obligatorio de DNI de 8 dígitos o RUC de 11 en el checkout por bloques y su Store API, conservando el checkout clásico y los metadatos de pedidos con HPOS. Se valida el formato; no se consulta ni certifica la identidad del titular.
- La promoción anuncia el cupón existente `JONILUXVIP`, con su porcentaje y mínimo reales. Se conserva el 15 % desde S/180. La promoción se omite si WooCommerce no está disponible o el cupón dejó de ser utilizable.
- La barra de envío gratis descuenta cupones. Caso verificado: S/160 con `BIENVENIDA10` queda en S/144 y no obtiene envío gratis.
- Las instrucciones de transferencia se muestran solo para pedidos pendientes o en espera. Los pedidos pagados, cancelados, fallidos o reembolsados muestran su estado real. El WhatsApp de contraentrega consulta la entrega y no pide comprobante.
- El HTML insertado desde PHP en JavaScript se serializa con `wp_json_encode` y opciones HEX.
- Se bloqueó por HTTP el acceso a SQL, Git, respaldos, herramientas locales y pruebas. Las reglas se limitan a los directorios locales de la raíz y conservan los recursos de pasarelas bajo directorios internos llamados `scripts`.
- El pixel de Reddit no se invoca si no existe un identificador configurado. Se utiliza el filtro del plugin, sin editarlo ni desactivar las conversiones existentes.
  La segunda auditoría señaló además el evento `Purchase` en la página de confirmación: se añadió un filtro de lectura de la opción para impedir que se registren esos callbacks sin pixel, conservando la configuración almacenada y CAPI. Se verificó con el plugin real cargado en la copia aislada. Esta última corrección no recibió una tercera auditoría, por el límite de dos rondas acordado.

### Verificación

- 15 escenarios funcionales en una copia aislada de la base: documentos, integración real de Reddit sin pixel (incluida ausencia del callback Purchase), descuentos, tarifas, cobertura COD, compras por Store API, persistencia del documento, estados, registro, autenticación y email duplicado. Correos interceptados y HTTP externo bloqueado; no se realizaron cobros reales.
- Prueba independiente de la barra con WooCommerce ausente.
- Chrome con perfil nuevo: portada, producto, carrito, cambio de cantidad, aplicación del cupón, checkout con DNI/RUC en escritorio y móvil, formularios de acceso/registro y limpieza del carrito anónimo. Resultado final sin excepciones JavaScript.
- HTTP 403 confirmado para la exportación actual `database/tienda_wp-actual-20261030.sql`. Los SQL antiguos se eliminaron después de verificar la copia. Se conserva el bloqueo de `.git/config`, respaldos y herramientas; la comprobación anterior dio HTTP 200 para el asset de Mercado Pago que contiene un directorio `scripts`.
- Sintaxis de PHP y configuración de Apache correctas.

### Base para importar

La base activa es `tienda_wp`; esta instalación ya la tiene cargada. No se importó ningún SQL sobre ella.

**Única exportación SQL actual:** `database/tienda_wp-actual-20261030.sql` (UTF-8/utf8mb4; 1 897 677 bytes). Es una copia completa de la base activa: conserva las 57 tablas, el catálogo, la configuración, los 5 pedidos, los datos de 5 compradores y la cuenta de acceso existente. Incluye la marca JONILUX y el cupón `joniluxvip` (15 % desde S/180).

Se verificó importándola en una base temporal nueva: coincidieron los recuentos y checksums de las 57 tablas con la base activa. Se comprobaron el nombre del sitio y las condiciones del cupón. La base temporal se eliminó y `tienda_wp` quedó intacta. La copia conserva todos los datos existentes en la base activa, sin excluir pedidos por su origen.

Por autorización expresa del usuario, esta copia completa **se versiona en el repositorio privado**, incluidos los datos de compradores, hashes de acceso, sesiones y configuración de integraciones. `.gitignore` permite únicamente `database/tienda_wp-actual-20261030.sql` y mantiene excluidos los demás `*.sql`, `*.sql.gz`, `.local-backups/` y la configuración local `wp-config.php`. `.gitattributes` conserva los bytes exactos de este SQL sin conversión de saltos de línea. Los dos SQL antiguos de la raíz se eliminaron; esto no reescribe el historial de Git.

En phpMyAdmin, crear y seleccionar una base **vacía**, importar el archivo con codificación UTF-8 y configurar su nombre en `DB_NAME` del `wp-config.php` local. No importar sobre una base con ventas o clientes que se deban conservar. Si cambia la URL del entorno, ajustar las opciones `siteurl` y `home` antes de abrir la tienda. No hacen falta migraciones de marca después de importar este SQL: ya contiene JONILUX y el cupón actualizado.

Se eliminaron siete exportaciones anteriores: los dos SQL de la raíz y cinco SQL de `.local-backups/`, incluidas las copias antiguas y las generadas para pruebas aisladas. Se conservaron los demás archivos y respaldos locales.

### Límites

No se validaron cobros ni webhooks reales de PayPal/Mercado Pago. Los datos de contacto y cuentas bancarias señalados como ejemplos en la revisión anterior siguen pendientes de confirmación del negocio; no se inventaron sustitutos. Se conservaron las políticas comerciales existentes.
