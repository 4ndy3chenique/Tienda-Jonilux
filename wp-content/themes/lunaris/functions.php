<?php
if (!defined('ABSPATH')) { exit; }
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('lunaris', get_stylesheet_uri(), ['fashion-nook-style'], filemtime(get_stylesheet_directory() . '/style.css'));
    wp_enqueue_style('lunaris-editorial', get_stylesheet_directory_uri() . '/assets/css/editorial.css', ['lunaris'], filemtime(get_stylesheet_directory() . '/assets/css/editorial.css'));
});
add_action('after_setup_theme', function () { add_editor_style(['style.css', 'assets/css/editorial.css']); });

// Google Tag Manager - JONILUX Store
add_action('wp_head', function () {
    ?>
    <!-- Google Tag Manager -->
    <script async src="https://www.googletagmanager.com/gtm.js?id=GTM-WFC2XLBF"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    </script>
    <!-- End Google Tag Manager -->
    <?php
});
