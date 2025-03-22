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
    wp_localize_script('index_js', 'voteAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('vote_nonce')
    ]);
}

add_action('after_setup_theme', 'sogohlopec_likes_system_setup_theme');
function sogohlopec_likes_system_setup_theme()
{
    add_theme_support('post-thumbnails');
}

// Add a table to the DB
add_action('after_setup_theme', 'sogohlopec_likes_system_setup_table');
function sogohlopec_likes_system_setup_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'sogohlopec_likes';
    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            user_ip VARCHAR(45) NOT NULL,
            vote_type ENUM('like', 'dislike') NOT NULL,
            vote_time DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_ip (user_ip)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}

// Get the number of votes of the post
function get_number_of_votes($post_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'sogohlopec_likes';

    $likes = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND vote_type = 'like'",
        $post_id
    ));
    $likes = $likes ? $likes : 0;

    $dislikes = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND vote_type = 'dislike'",
        $post_id
    ));
    $dislikes = $dislikes ? $dislikes : 0;

    $votes = $likes - $dislikes;
    return $votes;
}
