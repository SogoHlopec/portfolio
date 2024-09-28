<?php
// add to the functions.php file of the current theme

function enqueue_versioned_script($handle, $src = false, $deps = array(), $in_footer = false)
{
    wp_enqueue_script($handle, get_stylesheet_directory_uri() . $src, $deps, filemtime(get_stylesheet_directory() . $src), $in_footer);
}

function enqueue_versioned_style($handle, $src = false, $deps = array(), $media = 'all')
{
    wp_enqueue_style($handle, get_stylesheet_directory_uri() . $src, $deps = array(), filemtime(get_stylesheet_directory() . $src), $media);
}

function themename_scripts()
{
    enqueue_versioned_style('themename', '/custom.css');
    enqueue_versioned_script('themename', '/custom.js', array('jquery'), true);
}

add_action('wp_enqueue_scripts', 'themename_scripts');
