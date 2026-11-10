<?php
/**
 * Plugin Name: JONILUX – Registro y Cuentas de Compradores
 * Description: Optimiza la experiencia de Mi Cuenta para JONILUX: traduce textos al espanol, retira descargas digitales innecesarias, anade tarjetas interactivas y estila la interfaz con diseno editorial ordenado (sin emojis).
 * Version: 1.1.0
 * Author: JONILUX Team
 */

defined('ABSPATH') || exit;

// 1. Forzar opciones de registro de clientes en WooCommerce y WordPress
add_filter('option_users_can_register', '__return_true');
add_filter('option_woocommerce_enable_myaccount_registration', fn() => 'yes');
add_filter('option_woocommerce_enable_signup_and_login_from_checkout', fn() => 'yes');
add_filter('option_woocommerce_registration_generate_password', fn() => 'no');
add_filter('option_woocommerce_registration_generate_username', fn() => 'no');

// 2. Texto formal de privacidad en registro (espanol)
add_filter('option_woocommerce_registration_privacy_policy_text', function ($text) {
    return 'Sus datos personales seran procesados conforme a la Ley N 29733 para gestionar su cuenta y pedidos en JONILUX, segun lo detallado en nuestra [privacy_policy].';
});

// 3. Traducir titulo de la pagina a espanol
add_filter('the_title', function ($title, $id = null) {
    if (!is_admin() && function_exists('wc_get_page_id') && $id === wc_get_page_id('myaccount')) {
        return 'Mi cuenta';
    }
    return $title;
}, 99, 2);

add_filter('woocommerce_page_title', function ($title) {
    if (strcasecmp($title, 'My account') === 0) {
        return 'Mi cuenta';
    }
    return $title;
});

// 4. Limpiar y estructurar los elementos del menu de Mi Cuenta
add_filter('woocommerce_account_menu_items', function ($items) {
    // Retirar descargas ya que JONILUX es una tienda de ropa y accesorios fisicos
    unset($items['downloads']);

    if (isset($items['dashboard']))       $items['dashboard']       = 'Panel General';
    if (isset($items['orders']))          $items['orders']          = 'Mis Pedidos';
    if (isset($items['edit-address']))    $items['edit-address']    = 'Direcciones de Envío';
    if (isset($items['edit-account']))    $items['edit-account']    = 'Detalles de la Cuenta';
    if (isset($items['customer-logout'])) $items['customer-logout'] = 'Cerrar Sesión';

    return $items;
}, 99);

// 5. Inyectar tarjetas interactivas de navegacion rapida en el Escritorio del cliente
add_action('woocommerce_account_dashboard', function () {
    $orders_url   = wc_get_endpoint_url('orders');
    $address_url  = wc_get_endpoint_url('edit-address');
    $account_url  = wc_get_endpoint_url('edit-account');
    $shop_url     = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
    ?>
    <div class="jonilux-account-grid">
        <a href="<?php echo esc_url($orders_url); ?>" class="jonilux-account-card">
            <div class="jonilux-card-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            </div>
            <div class="jonilux-card-info">
                <h4>Mis Pedidos</h4>
                <p>Historial de compras, estado del envío y seguimiento de tus pedidos.</p>
                <span class="jonilux-card-link">Ver pedidos &rarr;</span>
            </div>
        </a>

        <a href="<?php echo esc_url($address_url); ?>" class="jonilux-account-card">
            <div class="jonilux-card-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            </div>
            <div class="jonilux-card-info">
                <h4>Direcciones de Envío</h4>
                <p>Direcciones de facturación y entrega en Lima Metropolitana y Provincias.</p>
                <span class="jonilux-card-link">Gestionar direcciones &rarr;</span>
            </div>
        </a>

        <a href="<?php echo esc_url($account_url); ?>" class="jonilux-account-card">
            <div class="jonilux-card-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <div class="jonilux-card-info">
                <h4>Detalles de la Cuenta</h4>
                <p>Modifica tu nombre, correo electrónico y contraseña de acceso.</p>
                <span class="jonilux-card-link">Editar datos &rarr;</span>
            </div>
        </a>

        <a href="<?php echo esc_url($shop_url); ?>" class="jonilux-account-card">
            <div class="jonilux-card-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </div>
            <div class="jonilux-card-info">
                <h4>Catálogo JONILUX</h4>
                <p>Explora nuestras colecciones urbanas de Polos, Hoodies y Gorras.</p>
                <span class="jonilux-card-link">Ir a la tienda &rarr;</span>
            </div>
        </a>
    </div>

    <div class="jonilux-account-support">
        <div class="jonilux-support-text">
            <h5>¿Necesitas asistencia con tu compra o entrega?</h5>
            <p>Atención inmediata de Lunes a Sábado (9:00 - 20:00) para resolver consultas de tallas, cambios o envíos.</p>
        </div>
        <a href="https://wa.me/51987654321?text=Hola%20JONILUX,%20tengo%20una%20consulta%20sobre%20mi%20cuenta" target="_blank" rel="noopener noreferrer" class="jonilux-support-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
            <span>Atención WhatsApp</span>
        </a>
    </div>
    <?php
});

// 6. Estilos tipograficos, estructurados y limpios para Mi Cuenta
add_action('wp_head', 'jonilux_estilos_mi_cuenta', 100);
function jonilux_estilos_mi_cuenta() {
    if (!function_exists('is_account_page') || !is_account_page()) {
        return;
    }
    ?>
    <style id="jonilux-cuentas-css">
        /* Contenedor principal de Mi Cuenta */
        .woocommerce-account .woocommerce {
            max-width: 1140px;
            margin: 32px auto 80px auto;
            padding: 0 24px;
            font-family: var(--lunaris-font-body, "Lunaris Inter", Arial, sans-serif);
            color: #172a22;
        }

        /* Titulo principal de la pagina */
        .woocommerce-account h1,
        .woocommerce-account .entry-title,
        .woocommerce-account h1.wp-block-post-title {
            font-family: var(--lunaris-font-display, "Lunaris Editorial", Georgia, serif);
            font-size: clamp(34px, 4vw, 48px);
            font-weight: 400;
            letter-spacing: -0.025em;
            color: #172a22;
            text-align: center;
            margin: 20px 0 36px 0;
        }

        /* Limpieza del layout flotante de WooCommerce */
        .woocommerce-account .woocommerce::after {
            content: "";
            display: table;
            clear: both;
        }

        /* --- Panel de Navegacion Lateral --- */
        .woocommerce-account .woocommerce-MyAccount-navigation {
            float: left;
            width: 250px;
            background: #ffffff;
            border: 1px solid #dcded3;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
        }

        .woocommerce-account .woocommerce-MyAccount-navigation ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .woocommerce-account .woocommerce-MyAccount-navigation li {
            margin: 0;
            border-bottom: 1px solid #edf0e6;
        }

        .woocommerce-account .woocommerce-MyAccount-navigation li:last-child {
            border-bottom: none;
        }

        .woocommerce-account .woocommerce-MyAccount-navigation a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #172a22;
            text-decoration: none;
            transition: all 0.18s ease;
        }

        .woocommerce-account .woocommerce-MyAccount-navigation a:hover {
            background: #f4f6ee;
            color: #2b3d30;
        }

        .woocommerce-account .woocommerce-MyAccount-navigation li.is-active a {
            background: #172a22;
            color: #ffffff;
        }

        /* Iconos SVG via background-image en los enlaces de navegacion */
        .woocommerce-account .woocommerce-MyAccount-navigation-link a::before {
            content: "";
            display: inline-block;
            width: 16px;
            height: 16px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            flex-shrink: 0;
        }

        /* Escritorio / Panel */
        .woocommerce-account .woocommerce-MyAccount-navigation-link--dashboard a::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23172a22' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='7' height='7'%3E%3C/rect%3E%3Crect x='14' y='3' width='7' height='7'%3E%3C/rect%3E%3Crect x='14' y='14' width='7' height='7'%3E%3C/rect%3E%3Crect x='3' y='14' width='7' height='7'%3E%3C/rect%3E%3C/svg%3E");
        }
        .woocommerce-account .woocommerce-MyAccount-navigation-link--dashboard.is-active a::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='7' height='7'%3E%3C/rect%3E%3Crect x='14' y='3' width='7' height='7'%3E%3C/rect%3E%3Crect x='14' y='14' width='7' height='7'%3E%3C/rect%3E%3Crect x='3' y='14' width='7' height='7'%3E%3C/rect%3E%3C/svg%3E");
        }

        /* Pedidos */
        .woocommerce-account .woocommerce-MyAccount-navigation-link--orders a::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23172a22' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z'%3E%3C/path%3E%3Cline x1='3' y1='6' x2='21' y2='6'%3E%3C/line%3E%3Cpath d='M16 10a4 4 0 0 1-8 0'%3E%3C/path%3E%3C/svg%3E");
        }
        .woocommerce-account .woocommerce-MyAccount-navigation-link--orders.is-active a::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z'%3E%3C/path%3E%3Cline x1='3' y1='6' x2='21' y2='6'%3E%3C/line%3E%3Cpath d='M16 10a4 4 0 0 1-8 0'%3E%3C/path%3E%3C/svg%3E");
        }

        /* Direcciones */
        .woocommerce-account .woocommerce-MyAccount-navigation-link--edit-address a::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23172a22' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z'%3E%3C/path%3E%3Ccircle cx='12' cy='10' r='3'%3E%3C/circle%3E%3C/svg%3E");
        }
        .woocommerce-account .woocommerce-MyAccount-navigation-link--edit-address.is-active a::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z'%3E%3C/path%3E%3Ccircle cx='12' cy='10' r='3'%3E%3C/circle%3E%3C/svg%3E");
        }

        /* Detalles de cuenta */
        .woocommerce-account .woocommerce-MyAccount-navigation-link--edit-account a::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23172a22' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'%3E%3C/path%3E%3Ccircle cx='12' cy='7' r='4'%3E%3C/circle%3E%3C/svg%3E");
        }
        .woocommerce-account .woocommerce-MyAccount-navigation-link--edit-account.is-active a::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'%3E%3C/path%3E%3Ccircle cx='12' cy='7' r='4'%3E%3C/circle%3E%3C/svg%3E");
        }

        /* Salir */
        .woocommerce-account .woocommerce-MyAccount-navigation-link--customer-logout a {
            color: #8b2b2b;
        }
        .woocommerce-account .woocommerce-MyAccount-navigation-link--customer-logout a::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238b2b2b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4'%3E%3C/path%3E%3Cpolyline points='16 17 21 12 16 7'%3E%3C/polyline%3E%3Cline x1='21' y1='12' x2='9' y2='12'%3E%3C/line%3E%3C/svg%3E");
        }
        .woocommerce-account .woocommerce-MyAccount-navigation-link--customer-logout a:hover {
            background: #fdf2f2;
            color: #b71c1c;
        }

        /* --- Panel de Contenido Principal --- */
        .woocommerce-account .woocommerce-MyAccount-content {
            display: flow-root;
            margin-left: 280px;
            float: none;
            width: auto;
            background: #ffffff;
            border: 1px solid #dcded3;
            border-radius: 6px;
            padding: 34px 38px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            min-height: 380px;
        }

        /* Mensaje de bienvenida al cliente */
        .woocommerce-account .woocommerce-MyAccount-content > p:first-of-type {
            background: #f8faf4;
            border-left: 3px solid #172a22;
            padding: 16px 20px;
            border-radius: 0 4px 4px 0;
            font-size: 14px;
            line-height: 1.6;
            color: #2b3830;
            margin: 0 0 24px 0;
        }

        .woocommerce-account .woocommerce-MyAccount-content > p:first-of-type strong {
            color: #172a22;
            font-weight: 600;
        }

        .woocommerce-account .woocommerce-MyAccount-content > p:first-of-type a {
            color: #8b2b2b;
            text-decoration: underline;
            font-size: 12px;
            margin-left: 6px;
            font-weight: 500;
        }

        /* Ocultar el parrafo generico redundante de WooCommerce en el escritorio */
        .woocommerce-account .woocommerce-MyAccount-content > p:nth-of-type(2) {
            display: none;
        }

        /* --- Cuadricula de Tarjetas Interactivas de JONILUX --- */
        .jonilux-account-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .jonilux-account-card {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 22px;
            border: 1px solid #dcded3;
            border-radius: 6px;
            background: #ffffff;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }

        .jonilux-account-card:hover {
            border-color: #172a22;
            box-shadow: 0 4px 14px rgba(23, 42, 34, 0.08);
            transform: translateY(-2px);
        }

        .jonilux-card-icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 6px;
            background: #f1f4eb;
            color: #172a22;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .jonilux-card-info h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: 600;
            color: #172a22;
            letter-spacing: 0.02em;
        }

        .jonilux-card-info p {
            margin: 0 0 10px 0;
            font-size: 12px;
            line-height: 1.5;
            color: #59645b;
        }

        .jonilux-card-link {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #344d3b;
            display: inline-block;
        }

        /* Barra de Asistencia y Soporte WhatsApp */
        .jonilux-account-support {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 20px 24px;
            background: #f4f6ee;
            border: 1px solid #d8decf;
            border-radius: 6px;
        }

        .jonilux-support-text h5 {
            margin: 0 0 4px 0;
            font-size: 13px;
            font-weight: 600;
            color: #172a22;
        }

        .jonilux-support-text p {
            margin: 0;
            font-size: 12px;
            color: #59645b;
            line-height: 1.5;
        }

        .jonilux-support-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #172a22;
            color: #ffffff !important;
            padding: 11px 20px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.18s ease;
        }

        .jonilux-support-btn:hover {
            background: #2f4535;
        }

        /* --- Tabla de Pedidos y Detalles --- */
        .woocommerce-account table.woocommerce-orders-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #dcded3;
            border-radius: 6px;
            overflow: hidden;
            margin-top: 15px;
        }

        .woocommerce-account table.woocommerce-orders-table th {
            background: #f4f6ee;
            color: #172a22;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 13px 16px;
            border-bottom: 1px solid #dcded3;
            text-align: left;
        }

        .woocommerce-account table.woocommerce-orders-table td {
            padding: 14px 16px;
            font-size: 13px;
            color: #2b3830;
            border-bottom: 1px solid #edf0e6;
        }

        .woocommerce-account table.woocommerce-orders-table tr:last-child td {
            border-bottom: none;
        }

        .woocommerce-account .woocommerce-orders-table__cell-order-actions a.button {
            background: #172a22 !important;
            color: #ffffff !important;
            padding: 8px 14px !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            letter-spacing: 0.08em !important;
            text-transform: uppercase !important;
            border-radius: 4px !important;
            text-decoration: none !important;
        }

        /* --- Direcciones de Envio y Facturacion --- */
        .woocommerce-account .woocommerce-Addresses {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 20px;
        }

        .woocommerce-account .woocommerce-Address {
            border: 1px solid #dcded3;
            border-radius: 6px;
            padding: 24px;
            background: #fafbfa;
        }

        .woocommerce-account .woocommerce-Address header.title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            border-bottom: 1px solid #e7ebe0;
            padding-bottom: 10px;
        }

        .woocommerce-account .woocommerce-Address header.title h3 {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #172a22;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .woocommerce-account .woocommerce-Address header.title .edit {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #344d3b;
            text-decoration: underline;
        }

        .woocommerce-account .woocommerce-Address address {
            font-style: normal;
            font-size: 13px;
            line-height: 1.6;
            color: #4b5850;
        }

        /* --- Formulario de Editar Cuenta y Direcciones --- */
        .woocommerce-account form.edit-account,
        .woocommerce-account form.woocommerce-EditAddressForm {
            margin-top: 10px;
        }

        /* Anular floats antiguos y el clear de WooCommerce que causaba el hueco vacio */
        .woocommerce-account form.edit-account .clear,
        .woocommerce-account form.woocommerce-EditAddressForm .clear {
            display: none !important;
            clear: none !important;
        }

        .woocommerce-account form.edit-account,
        .woocommerce-account form.woocommerce-EditAddressForm .woocommerce-address-fields__field-wrapper {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 16px 20px !important;
        }

        .woocommerce-account form.edit-account .woocommerce-form-row--first,
        .woocommerce-account form.edit-account .woocommerce-form-row--last,
        .woocommerce-account form.woocommerce-EditAddressForm .form-row-first,
        .woocommerce-account form.woocommerce-EditAddressForm .form-row-last {
            float: none !important;
            width: calc(50% - 10px) !important;
            margin: 0 !important;
        }

        .woocommerce-account form.edit-account .woocommerce-form-row--wide,
        .woocommerce-account form.woocommerce-EditAddressForm .form-row-wide {
            float: none !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .woocommerce-account form.edit-account em {
            display: block;
            margin-top: -6px;
            font-size: 11px;
            color: #59645b;
            font-style: normal;
        }

        .woocommerce-account form fieldset {
            width: 100% !important;
            float: none !important;
            border: 1px solid #dcded3;
            border-radius: 6px;
            padding: 20px 24px;
            margin: 16px 0 8px 0 !important;
            background: #fafbfa;
            box-sizing: border-box !important;
        }
        }

        .woocommerce-account form fieldset legend {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #172a22;
            padding: 0 10px;
        }

        .woocommerce-account .woocommerce-form-row {
            margin-bottom: 16px;
        }

        .woocommerce-account .woocommerce-form-row label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #172a22;
            margin-bottom: 6px;
        }

        .woocommerce-account .woocommerce-Input,
        .woocommerce-account input[type="text"],
        .woocommerce-account input[type="email"],
        .woocommerce-account input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            font-size: 13px;
            border: 1px solid #c7cbc4;
            border-radius: 4px;
            background: #ffffff;
            color: #111614;
            box-sizing: border-box;
        }

        .woocommerce-account .woocommerce-Input:focus,
        .woocommerce-account input:focus {
            border-color: #172a22;
            outline: none;
        }

        .woocommerce-account .woocommerce-Button.button,
        .woocommerce-account button[type="submit"].button {
            background: #172a22 !important;
            color: #ffffff !important;
            border: none !important;
            padding: 13px 26px !important;
            border-radius: 4px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            letter-spacing: 0.1em !important;
            text-transform: uppercase !important;
            cursor: pointer !important;
            transition: background 0.18s ease !important;
            margin-top: 10px;
        }

        .woocommerce-account .woocommerce-Button.button:hover,
        .woocommerce-account button[type="submit"].button:hover {
            background: #2b3b33 !important;
        }

        /* --- Formularios de Inicio de Sesion y Registro (Usuarios no conectados) --- */
        .woocommerce-account .u-columns.col2-set {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 20px;
            background: #ffffff;
            padding: 35px 40px;
            border: 1px solid #dcded7;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .woocommerce-account .u-column1,
        .woocommerce-account .u-column2 {
            float: none !important;
            width: 100% !important;
        }

        .woocommerce-account .u-column1 h2,
        .woocommerce-account .u-column2 h2 {
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #111614;
            border-bottom: 1px solid #dcded7;
            padding-bottom: 12px;
            margin-bottom: 22px !important;
        }

        .woocommerce-account form.login,
        .woocommerce-account form.register {
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .woocommerce-account .woocommerce-privacy-policy-text {
            font-size: 11px !important;
            color: #626b63 !important;
            line-height: 1.5 !important;
            margin: 14px 0 !important;
        }

        .woocommerce-account .woocommerce-form-login__rememberme {
            font-size: 12px;
            color: #626b63;
            margin: 10px 0 0 0;
            display: block;
        }

        .woocommerce-account .woocommerce-LostPassword {
            margin-top: 14px;
            font-size: 12px;
        }

        .woocommerce-account .woocommerce-LostPassword a {
            color: #172a22;
            text-decoration: underline;
        }

        /* --- Adaptabilidad Responsiva para Tabletas y Celulares --- */
        @media (max-width: 860px) {
            .woocommerce-account .woocommerce-MyAccount-navigation {
                float: none;
                width: 100%;
                margin-bottom: 24px;
            }
            .woocommerce-account .woocommerce-MyAccount-content {
                margin-left: 0;
                padding: 24px 20px;
            }
            .woocommerce-account .jonilux-account-grid {
                grid-template-columns: 1fr;
            }
            .woocommerce-account .jonilux-account-support {
                flex-direction: column;
                align-items: flex-start;
            }
            .woocommerce-account .u-columns.col2-set {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 25px 20px;
            }
            .woocommerce-account .woocommerce-Addresses {
                grid-template-columns: 1fr;
            }
            .woocommerce-account form.edit-account .woocommerce-form-row--first,
            .woocommerce-account form.edit-account .woocommerce-form-row--last,
            .woocommerce-account form.woocommerce-EditAddressForm .form-row-first,
            .woocommerce-account form.woocommerce-EditAddressForm .form-row-last {
                width: 100% !important;
            }
        }
    </style>
    <?php
}
