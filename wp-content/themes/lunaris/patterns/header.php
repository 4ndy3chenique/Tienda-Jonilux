<?php
/**
 * Title: JONILUX header
 * Slug: lunaris/header
 * Categories: featured
 * Inserter: no
 */
if (!defined("ABSPATH")) { exit; }
$shop = function_exists("wc_get_page_permalink") ? wc_get_page_permalink("shop") : home_url("/shop/");
$hombre = home_url("/#coleccion-hombre");
$mujer = home_url("/#coleccion-mujer");
$acc = home_url("/#accesorios");
if (function_exists('wc_get_page_permalink')) {
    foreach (['hombre', 'mujer', 'accesorios'] as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        $url = $term ? get_term_link($term) : $shop;
        if (is_wp_error($url)) { $url = $shop; }
        if ($slug === 'hombre') { $hombre = $url; }
        elseif ($slug === 'mujer') { $mujer = $url; }
        else { $acc = $url; }
    }
}
$myaccount = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : wp_login_url();
?>
<!-- wp:html -->
<div class="lunaris-note">JONILUX • Moda urbana para Hombre y Mujer • Envíos a todo el Perú</div>
<!-- /wp:html -->
<!-- wp:group {"className":"lunaris-header","layout":{"type":"default"}} -->
<div class="wp-block-group lunaris-header"><!-- wp:group {"className":"lunaris-header-inner","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"nowrap"}} -->
<div class="wp-block-group lunaris-header-inner">
<!-- wp:html --><h1 class="wp-block-site-title lunaris-logo"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home">JONILUX</a></h1><!-- /wp:html -->
<!-- wp:search {"label":"Buscar productos","showLabel":false,"placeholder":"Buscar productos…","buttonText":"Buscar","buttonPosition":"button-inside","buttonUseIcon":true,"query":{"post_type":"product"},"className":"lunaris-header-search"} /-->
<!-- wp:navigation {"overlayMenu":"mobile","className":"lunaris-nav","layout":{"type":"flex"}} -->
<!-- wp:navigation-link {"label":"Inicio","url":"<?php echo esc_url(home_url('/')); ?>"} /-->
<!-- wp:navigation-link {"label":"Hombre","url":"<?php echo esc_url($hombre); ?>"} /-->
<!-- wp:navigation-link {"label":"Mujer","url":"<?php echo esc_url($mujer); ?>"} /-->
<!-- wp:navigation-link {"label":"Accesorios","url":"<?php echo esc_url($acc); ?>"} /-->
<!-- wp:navigation-link {"label":"Tienda","url":"<?php echo esc_url($shop); ?>"} /-->
<!-- wp:navigation-link {"label":"Mi cuenta","url":"<?php echo esc_url($myaccount); ?>"} /-->
<!-- /wp:navigation -->
<!-- wp:woocommerce/mini-cart /-->
</div><!-- /wp:group --></div><!-- /wp:group -->
