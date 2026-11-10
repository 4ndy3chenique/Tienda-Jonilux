<?php
if (PHP_SAPI !== 'cli') { exit; }
define('ABSPATH', dirname(__DIR__) . '/');
function add_action(...$args) {}
function add_filter(...$args) {}
require ABSPATH . 'wp-content/mu-plugins/jonilux-mejoras-tienda.php';
ob_start();
jonilux_render_promocion_bar();
if (ob_get_clean() !== '') { throw new RuntimeException('No debe mostrarse una promocion sin WooCommerce.'); }
echo "PASS La portada no depende de WooCommerce para mostrar la barra de promocion.\n";
