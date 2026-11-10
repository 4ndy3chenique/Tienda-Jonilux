<?php
// Ejecutar solo en CLI y sobre una copia: nunca usar la base real de la tienda.
if (PHP_SAPI !== 'cli') { exit; }
$test_db = getenv('JONILUX_TEST_DB');
if (!$test_db || !preg_match('/^jonilux_audit_[0-9]+$/D', $test_db)) {
    throw new RuntimeException('JONILUX_TEST_DB debe identificar una copia aislada jonilux_audit_<fecha>.');
}
define('ABSPATH', dirname(__DIR__) . '/');
define('DB_NAME', $test_db);
define('DB_USER', getenv('JONILUX_TEST_USER') ?: 'root');
define('DB_PASSWORD', getenv('JONILUX_TEST_PASSWORD') ?: '');
define('DB_HOST', '127.0.0.1');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
define('DISABLE_WP_CRON', true);
define('WP_HTTP_BLOCK_EXTERNAL', true);
define('WP_HOME', 'http://localhost/wordpress');
define('WP_SITEURL', WP_HOME);
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', ABSPATH . '.local-backups/test-php.log');
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/wordpress/checkout/';
$table_prefix = 'wp_';
$GLOBALS['audit_mail_blocked'] = 0;
$GLOBALS['wp_filter'] = array(
    'option_active_plugins' => array(1 => array(array('function' => static fn() => array('woocommerce/woocommerce.php','reddit-for-woocommerce/reddit-for-woocommerce.php'), 'accepted_args' => 0))),
    'pre_http_request' => array(1 => array(array('function' => static fn() => new WP_Error('audit_no_network', 'External HTTP blocked'), 'accepted_args' => 0))),
    'pre_wp_mail' => array(1 => array(array('function' => static function () { $GLOBALS['audit_mail_blocked']++; return true; }, 'accepted_args' => 0))),
);
require ABSPATH . 'wp-settings.php';
wc_load_cart();

$passed = 0;
$failed = 0;
function check($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
}
function scenario($name, callable $callback) {
    global $passed, $failed;
    try { $callback(); $passed++; echo "PASS $name\n"; }
    catch (Throwable $e) { $failed++; echo "FAIL $name: {$e->getMessage()}\n"; }
}
function cart_for($state, $price) {
    WC()->cart->empty_cart();
    WC()->customer->set_billing_country('PE');
    WC()->customer->set_shipping_country('PE');
    WC()->customer->set_billing_state($state);
    WC()->customer->set_shipping_state($state);
    check((bool) WC()->cart->add_to_cart(50, 1), 'No se pudo agregar el producto de la copia');
    foreach (WC()->cart->get_cart() as $item) { $item['data']->set_price($price); }
    WC()->cart->calculate_totals();
}
function rates() {
    WC()->cart->calculate_totals();
    $packages = WC()->shipping()->get_packages();
    $rates = array();
    foreach (($packages[0]['rates'] ?? array()) as $id => $rate) { $rates[$id] = (float) $rate->get_cost(); }
    WC()->session->set('chosen_shipping_methods', array(array_key_first($rates)));
    return $rates;
}
function checkout_request($method, $state, $document) {
    cart_for($state, 100);
    rates();
    $address = array('first_name' => 'Prueba', 'last_name' => 'Automatica', 'company' => '', 'address_1' => 'Direccion de prueba 123', 'address_2' => '', 'city' => $state === 'LIM' ? 'Lima' : 'Arequipa', 'state' => $state, 'postcode' => '15001', 'country' => 'PE', 'email' => 'qa-audit@example.invalid', 'phone' => '999000111');
    $request = new WP_REST_Request('POST', '/wc/store/v1/checkout');
    $request->set_header('Nonce', wp_create_nonce('wc_store_api'));
    $request->set_header('Content-Type', 'application/json');
    $request->set_body(wp_json_encode(array('billing_address' => $address, 'shipping_address' => $address, 'payment_method' => $method, 'payment_data' => array(), 'additional_fields' => array('jonilux/documento' => $document))));
    return rest_do_request($request);
}

scenario('DNI y RUC: formato y validacion clasica', function () {
    foreach (array('' => false, 'abcdefgh' => false, '1234567' => false, '123456789' => false, '12345678' => true, '20612345678' => true) as $input => $expected) {
        $input = (string) $input;
        check(jonilux_documento_valido($input) === $expected, 'Formato incorrecto para el documento de prueba');
        $_POST['billing_dni'] = $input;
        wc_clear_notices();
        jonilux_validate_checkout_dni();
        check((count(wc_get_notices('error')) === 0) === $expected, 'Validacion clasica no coincide');
    }
    unset($_POST['billing_dni']); wc_clear_notices();
});
scenario('DNI obligatorio registrado en checkout por bloques', function () {
    $fields = Automattic\WooCommerce\Blocks\Package::container()->get(Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields::class)->get_additional_fields();
    check(isset($fields['jonilux/documento']) && $fields['jonilux/documento']['required'] === true, 'Falta el campo requerido');
    check(is_wp_error(call_user_func($fields['jonilux/documento']['validate_callback'], 'abcdefgh')), 'El campo acepta letras');
});
scenario('Reddit sin pixel no registra Purchase ni desactiva CAPI', function () {
    check(class_exists('RedditForWooCommerce\\Tracking\\PixelTrackingService'), 'El plugin de Reddit no esta cargado');
    check(!RedditForWooCommerce\Tracking\PixelTrackingService::is_enabled(), 'El pixel sigue activo sin identificador');
    check(get_option('reddit_conversion_enabled') === 'yes', 'Se modifico CAPI');
    $callbacks = $GLOBALS['wp_filter']['wp_footer']->callbacks ?? array();
    foreach ($callbacks as $priority) {
        foreach ($priority as $callback) {
            $function = $callback['function'];
            check(!(is_array($function) && $function[0] instanceof RedditForWooCommerce\Tracking\RemotePixelTracker && $function[1] === 'track_purchase_event'), 'Purchase sigue registrado sin pixel');
        }
    }
    // Una configuracion posterior vuelve a respetar la opcion original, sin
    // guardar ni emitir eventos: la peticion HTTP real sigue bloqueada.
    add_filter('pre_option_reddit_pixel_id', static fn() => 'test-pixel-local');
    check(RedditForWooCommerce\Tracking\PixelTrackingService::is_enabled(), 'El pixel no puede activarse al configurarlo');
    remove_all_filters('pre_option_reddit_pixel_id');
});
foreach (array(array('LIM',100,array('flat_rate:1'=>10.0,'flat_rate:3'=>18.0),true),array('LIM',200,array('free_shipping:2'=>0.0,'flat_rate:3'=>18.0),true),array('ARE',100,array('flat_rate:4'=>18.0),false)) as [$state,$price,$expected,$cod]) {
    scenario("Envio y contraentrega $state $price", function () use ($state,$price,$expected,$cod) {
        cart_for($state,$price);
        check(rates() === $expected, 'Tarifas distintas de la politica existente');
        check(WC()->payment_gateways()->payment_gateways()['cod']->is_available() === $cod, 'Cobertura COD incorrecta');
    });
}
scenario('Cupon anunciado existe y aplica 15% con minimo 180', function () {
    cart_for('LIM',200);
    ob_start(); jonilux_render_promocion_bar(); $html = ob_get_clean();
    check(strpos($html,'JONILUXVIP') !== false && strpos($html,'LUNARISVIP') === false, 'Promocion incorrecta');
    check(WC()->cart->apply_coupon('JONILUXVIP'), 'El codigo anunciado no se aplica');
    WC()->cart->calculate_totals();
    check((float) WC()->cart->get_discount_total() === 30.0, 'Descuento distinto del 15%');
    cart_for('LIM',170);
    check(!WC()->cart->apply_coupon('JONILUXVIP'), 'Se omite el minimo de 180');
    wc_clear_notices();
});
scenario('Envio gratis usa el neto despues del descuento', function () {
    cart_for('LIM',160);
    check(WC()->cart->apply_coupon('bienvenida10'), 'No se aplico el cupon existente de 10%');
    WC()->cart->calculate_totals();
    check((float) WC()->cart->get_cart_contents_total() === 144.0, 'Neto inesperado');
    check(!isset(rates()['free_shipping:2']), 'Se regala envio bajo el minimo neto');
});
scenario('Store API rechaza un documento invalido', function () {
    $response = checkout_request('bacs','LIM','abcdefgh');
    check($response->get_status() >= 400, 'El checkout admite documento invalido');
});
foreach (array(array('bacs','LIM','12345678'),array('cod','LIM','20612345678'),array('bacs','ARE','12345678')) as [$method,$state,$document]) {
    scenario("Compra completa Store API $method $state", function () use ($method,$state,$document) {
        $response = checkout_request($method,$state,$document);
        $data = $response->get_data();
        check($response->get_status() === 200, wp_json_encode($data));
        $order = wc_get_order($data['order_id']);
        check($order && $order->get_payment_method() === $method, 'No se creo el pedido con el medio elegido');
        check($order->get_meta('_billing_dni') === $document, 'No se guardo el DNI/RUC en HPOS');
        check($order->has_status($method === 'bacs' ? 'on-hold' : 'processing'), 'Estado posterior al pago manual incorrecto');
    });
}
scenario('Store API no permite contraentrega en provincias', function () {
    $response = checkout_request('cod','ARE','12345678');
    check($response->get_status() >= 400, 'Se pudo comprar con COD en provincias');
});
scenario('Gracias: estados reales y WhatsApp apropiado para COD', function () {
    foreach (array('pending','on-hold','processing','completed','cancelled','failed','refunded') as $status) {
        $order = new WC_Order(); $order->set_payment_method('bacs'); $order->set_status($status); $order->set_total(100); $order->save();
        ob_start(); jonilux_render_order_received_whatsapp_confirmation($order->get_id()); $html=ob_get_clean();
        $pending = in_array($status,array('pending','on-hold'),true);
        check((strpos($html,'Enviar Comprobante por WhatsApp') !== false) === $pending, 'Se pide otro pago en un estado incorrecto');
        check(strpos($html,esc_html(wc_get_order_status_name($status))) !== false, 'No se muestra el estado real');
    }
    $order = new WC_Order(); $order->set_payment_method('cod'); $order->set_status('processing'); $order->set_total(100); $order->save();
    ob_start(); jonilux_render_order_received_whatsapp_confirmation($order->get_id()); $html=rawurldecode(ob_get_clean());
    check(strpos($html,'pago contra entrega') !== false && strpos($html,'adjunto mi comprobante') === false, 'Mensaje de COD incorrecto');
});
scenario('Registro, autenticacion y correo bloqueado', function () {
    $tag = bin2hex(random_bytes(4));
    $email = 'qa-account-' . $tag . '@example.invalid';
    $id = wc_create_new_customer($email,'codex-audit-' . $tag,'OnlyLocalTest-2026!');
    check(!is_wp_error($id), 'No se pudo registrar el cliente ficticio');
    $user = get_user_by('id',$id);
    check(wp_authenticate($user->user_login,'OnlyLocalTest-2026!') instanceof WP_User, 'El cliente no puede iniciar sesion');
    check(is_wp_error(wc_create_new_customer($email,'duplicado-audit','OnlyLocalTest-2026!')), 'Se admitio el email duplicado');
    check($GLOBALS['audit_mail_blocked'] > 0, 'No se ejercito el bloqueo del correo');
    check(is_wp_error(wp_remote_get('https://example.invalid/audit')), 'No se bloquea HTTP externo');
});
WC()->cart->empty_cart();
echo "RESULTADO: $passed correctos, $failed fallidos. Base aislada: " . DB_NAME . ". Correos interceptados: " . $GLOBALS['audit_mail_blocked'] . ".\n";
exit($failed ? 1 : 0);
