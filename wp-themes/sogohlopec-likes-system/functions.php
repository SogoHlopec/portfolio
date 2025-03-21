<?php

add_action('wp_enqueue_scripts', 'add_js_and_css');
function add_js_and_css()
{
    wp_enqueue_style(
        'style_css',
        get_stylesheet_directory_uri() . '/assets/css/style.css',
        array(),
        time(),
    );

    wp_enqueue_script(
        'index_js',
        get_stylesheet_directory_uri() . '/assets/js/index.js',
        array('jquery'),
        time(),
        true
    );
}

add_action('after_setup_theme', 'sogohlopec_likes_theme_system_setup');
function sogohlopec_likes_theme_system_setup()
{
    add_theme_support('post-thumbnails');
}
