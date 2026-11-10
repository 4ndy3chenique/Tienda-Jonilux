<?php
/**
 * Plugin Name: JONILUX – Mejoras del carrito
 * Description: Barra de progreso hacia el envío gratis y mensajes de confianza en el carrito.
 */

defined('ABSPATH') || exit;

const JONILUX_ENVIO_GRATIS = 150; // Mismo monto mínimo que las zonas de envío de WooCommerce

// Si el envío gratis está disponible, ocultar el envío pagado (se mantiene el recojo en tienda)
add_filter('woocommerce_package_rates', function ($rates) {
	$hay_gratis = array_filter($rates, fn($r) => $r->get_method_id() === 'free_shipping');
	if (!$hay_gratis) return $rates;
	return array_filter($rates, fn($r) => !($r->get_method_id() === 'flat_rate' && $r->get_instance_id() === 1));
}, 100);

// Mensajes de confianza bajo el botón "Finalizar compra"
add_filter('render_block_woocommerce/proceed-to-checkout-block', function ($html) {
	$svg = fn($path) => '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
	return $html . '
	<ul class="jonilux-confianza">
		<li>' . $svg('<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>') . 'Pago seguro</li>
		<li>' . $svg('<path d="M3 12a9 9 0 0 1 15.5-6.2L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/>') . 'Cambios hasta 7 días</li>
		<li>' . $svg('<path d="M1 6h13v10H1z"/><path d="M14 9h4l3 3v4h-7"/><circle cx="5.5" cy="18" r="1.8"/><circle cx="17.5" cy="18" r="1.8"/>') . 'Envíos a todo el Perú</li>
	</ul>';
});

// Barra de envío gratis: se lee el carrito del store de WooCommerce Blocks,
// así se actualiza sola al cambiar cantidades o quitar productos.
add_action('wp_enqueue_scripts', function () {
	if (!function_exists('is_cart') || !is_cart()) return;

	wp_register_script('jonilux-carrito', '', ['wp-data', 'wc-blocks-data-store'], '1.0', true);
	wp_enqueue_script('jonilux-carrito');
	wp_add_inline_script('jonilux-carrito', sprintf('(function () {
		var META = %d;
		var bar = document.createElement("div");
		bar.className = "jonilux-envio";
		bar.setAttribute("role", "status");

		function render() {
			var cart = document.querySelector(".wp-block-woocommerce-cart");
			if (!cart) return;
			if (!bar.isConnected) cart.parentNode.insertBefore(bar, cart);

			var totals = wp.data.select("wc/store/cart").getCartTotals();
			if (!totals || !totals.total_items) { bar.hidden = true; return; }
			var unit = Math.pow(10, totals.currency_minor_unit || 0);
			var subtotal = Math.max(0, (parseInt(totals.total_items, 10) + parseInt(totals.total_items_tax || 0, 10)
				- parseInt(totals.total_discount || 0, 10) - parseInt(totals.total_discount_tax || 0, 10)) / unit);
			var items = wp.data.select("wc/store/cart").getCartData().itemsCount;
			if (!items) { bar.hidden = true; return; }
			bar.hidden = false;

			var pct = Math.min(100, subtotal / META * 100);
			var falta = Math.max(0, META - subtotal);
			bar.innerHTML = (falta > 0
				? "Te faltan <strong>S/ " + falta.toFixed(2) + "</strong> para el <strong>envío estándar gratis en Lima y Callao</strong>"
				: "Tu pedido alcanza el mínimo para <strong>envío estándar gratis en Lima y Callao</strong>") +
				"<div class=\"jonilux-envio__track\"><div class=\"jonilux-envio__fill\" style=\"width:" + pct + "%%\"></div></div>";
		}

		wp.data.subscribe(render);
		document.addEventListener("DOMContentLoaded", render);
	})();', JONILUX_ENVIO_GRATIS));
});

add_action('wp_head', function () {
	if (!function_exists('is_cart') || !is_cart()) return;
	?>
	<style>
		.jonilux-envio{max-width:var(--wp--style--global--wide-size,1200px);margin:0 auto 24px;padding:14px 18px;border:1px solid #e5e5e5;border-radius:8px;background:#fafafa;font-size:15px}
		.jonilux-envio__track{height:8px;margin-top:10px;border-radius:4px;background:#e5e5e5;overflow:hidden}
		.jonilux-envio__fill{height:100%;background:#111;transition:width .3s}
		.jonilux-confianza{list-style:none;margin:14px 0 0;padding:0;display:flex;flex-wrap:wrap;gap:6px 18px;justify-content:center;font-size:13px;color:#555}
		.jonilux-confianza li{display:flex;align-items:center;gap:6px;white-space:nowrap}
	</style>
	<?php
});
