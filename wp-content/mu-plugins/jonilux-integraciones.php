<?php
/**
 * Plugin Name: JONILUX - Integraciones configuradas
 * Description: Evita invocar el pixel de Reddit antes de configurar su identificador.
 */
defined('ABSPATH') || exit;

// Evitar tambien los callbacks PHP de Purchase/confirmacion. El filtro de datos
// del navegador por si solo no cubre esos callbacks ni su marca de pedido rastreado.
// No se modifica la opcion almacenada ni la configuracion de conversiones CAPI.
add_filter('pre_option_reddit_ads_pixel_enabled', function ($pre) {
    return get_option('reddit_pixel_id', '') ? $pre : 'no';
});

add_filter('reddit_for_woocommerce_filter_tracking_data', function ($data) {
    // El plugin activa el seguimiento por defecto incluso sin crear window.rdt.
    // Conservar conversiones y toda configuracion existente; solo desactivar el
    // pixel del navegador cuando no existe un identificador para inicializarlo.
    if (class_exists('RedditForWooCommerce\\Utils\\Storage\\Options')
        && !RedditForWooCommerce\Utils\Storage\Options::get('pixel_id')) {
        $data['is_pixel_enabled'] = false;
    }
    return $data;
});
