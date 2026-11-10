<?php
/**
 * Plugin Name: JONILUX - Compatibilidad de enlaces de categorías
 * Description: Redirige los enlaces antiguos de categorías a su URL actual.
 */

defined('ABSPATH') || exit;

add_action('template_redirect', function () {
    if (!is_404()) {
        return;
    }

    global $wp;
    if (!preg_match('#^categoria-producto/([^/]+)/?$#', $wp->request, $matches)) {
        return;
    }

    $term = get_term_by('slug', rawurldecode($matches[1]), 'product_cat');
    if (!$term) {
        return;
    }

    $url = get_term_link($term);
    if (!is_wp_error($url)) {
        wp_safe_redirect($url, 301);
        exit;
    }
});
