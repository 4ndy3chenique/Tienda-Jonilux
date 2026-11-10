<?php
if (!defined('ABSPATH')) { exit; }
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('lunaris', get_stylesheet_uri(), ['fashion-nook-style'], filemtime(get_stylesheet_directory() . '/style.css'));
    wp_enqueue_style('lunaris-editorial', get_stylesheet_directory_uri() . '/assets/css/editorial.css', ['lunaris'], filemtime(get_stylesheet_directory() . '/assets/css/editorial.css'));
});
add_action('after_setup_theme', function () { add_editor_style(['style.css', 'assets/css/editorial.css']); });
