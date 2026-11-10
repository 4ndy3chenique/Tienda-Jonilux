<?php
/**
 * Plugin Name: JONILUX – SEO & OpenGraph para Redes Sociales y WhatsApp
 * Description: Genera automáticamente las tarjetas de vista previa (OpenGraph / Twitter Cards) con logo, imagen del producto, precios y descripción al compartir enlaces de la tienda por WhatsApp, Facebook o Instagram.
 * Version: 1.0.0
 * Author: JONILUX Team
 */

defined('ABSPATH') || exit;

add_action('wp_head', 'jonilux_render_opengraph_meta_tags', 2);
function jonilux_render_opengraph_meta_tags() {
    $site_name = 'JONILUX';
    $default_desc = 'Moda urbana y esenciales contemporáneos en Perú. Polos oversize, hoodies, joggers y accesorios con envíos a Lima y provincias. Pagos con Yape, Plin y Contra Entrega.';
    $default_img = content_url('/themes/lunaris/assets/images/hero.webp');

    $title = get_bloginfo('name');
    $desc = $default_desc;
    $url = home_url('/');
    $type = 'website';
    $image = $default_img;

    if (is_front_page()) {
        $title = 'JONILUX | Moda Urbana & Esenciales Contemporáneos';
        $desc = $default_desc;
        $url = home_url('/');
    } elseif (function_exists('is_product') && is_product()) {
        global $post;
        $product = wc_get_product($post->ID);
        if ($product) {
            $title = $product->get_name() . ' | JONILUX';
            $short_desc = wp_strip_all_tags($product->get_short_description());
            $desc = !empty($short_desc) ? $short_desc : ('Compra ' . $product->get_name() . ' al mejor precio en JONILUX. Envío rápido en Lima y todo el Perú.');
            $url = get_permalink($post->ID);
            $type = 'product';

            $img_id = $product->get_image_id();
            if ($img_id) {
                $img_src = wp_get_attachment_image_url($img_id, 'large');
                if ($img_src) {
                    $image = $img_src;
                }
            }
        }
    } elseif (is_page()) {
        $title = get_the_title() . ' | JONILUX';
        $excerpt = wp_strip_all_tags(get_the_excerpt());
        $desc = !empty($excerpt) ? $excerpt : $default_desc;
        $url = get_permalink();
    } elseif (function_exists('is_shop') && is_shop()) {
        $title = 'Catálogo Oficial de Ropa y Accesorios | JONILUX';
        $desc = 'Explora toda la colección de moda urbana: polos, hoodies, sobrecamisas, pantalones y accesorios urbanos en JONILUX.';
        $url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
    }

    ?>
    <!-- OpenGraph / WhatsApp Meta Tags generados por JONILUX -->
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($desc); ?>">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:type" content="<?php echo esc_attr($type); ?>">
    <meta property="og:image" content="<?php echo esc_url($image); ?>">
    <meta property="og:locale" content="es_PE">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($desc); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($image); ?>">
    <?php
}

