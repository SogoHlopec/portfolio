<?php

add_action('wp_enqueue_scripts', 'sogohlopec_likes_system_add_js_and_css');
function sogohlopec_likes_system_add_js_and_css()
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
function sogohlopec_likes_system_get_number_of_votes($post_id)
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

// Recording likes/dislikes in a DB table
function sogohlopec_likes_system_save_post_vote($post_id, $vote_type, $user_ip)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'sogohlopec_likes';

    $existing_vote = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE post_id = %d AND user_ip = %s",
        $post_id,
        $user_ip
    ));

    if ($existing_vote) {
        $wpdb->update(
            $table_name,
            [
                'vote_type' => $vote_type,
                'vote_time' => current_time('mysql')
            ],
            [
                'id' => $existing_vote->id
            ],
            ['%s', '%s'],
            ['%d']
        );
    } else {
        $wpdb->insert(
            $table_name,
            [
                'post_id' => $post_id,
                'user_ip' => $user_ip,
                'vote_type' => $vote_type,
                'vote_time' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s']
        );
    }

    return sogohlopec_likes_system_get_number_of_votes($post_id);
}

// AJAX handler registration
add_action('wp_ajax_sogohlopec_likes_system_handle_vote', 'sogohlopec_likes_system_handle_vote_callback');
add_action('wp_ajax_nopriv_sogohlopec_likes_system_handle_vote', 'sogohlopec_likes_system_handle_vote_callback');

function sogohlopec_likes_system_handle_vote_callback()
{
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $vote_type = isset($_POST['vote_type']) ? $_POST['vote_type'] : '';

    if (!$post_id || !$vote_type) {
        wp_send_json_error('Incorrect data');
    }

    $user_ip = $_SERVER['REMOTE_ADDR'];

    $votes = sogohlopec_likes_system_save_post_vote($post_id, $vote_type, $user_ip);

    wp_send_json_success($votes);
}

// Add a page in the admin with likes and dislikes
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Likes_Stats_Table extends WP_List_Table
{
    function get_columns()
    {
        return [
            'post_title' => 'Post',
            'likes'      => 'Количество лайков',
            'dislikes'   => 'Количество дизлайков',
        ];
    }

    function prepare_items()
    {
        global $wpdb;

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $table_name = $wpdb->prefix . 'sogohlopec_likes';
        $query = "
            SELECT p.ID, p.post_title,
                   SUM(CASE WHEN l.vote_type = 'like' THEN 1 ELSE 0 END) as likes,
                   SUM(CASE WHEN l.vote_type = 'dislike' THEN 1 ELSE 0 END) as dislikes
            FROM {$wpdb->posts} p
            LEFT JOIN $table_name l ON p.ID = l.post_id
            WHERE p.post_type = 'post' AND p.post_status = 'publish'
            GROUP BY p.ID, p.post_title
        ";

        $total_items = count($wpdb->get_results($query));
        $query .= " LIMIT $offset, $per_page";
        $this->items = $wpdb->get_results($query, ARRAY_A);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);

        $this->_column_headers = [$this->get_columns(), [], []];
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'post_title':
                $edit_link = get_edit_post_link($item['ID']);
                return '<a href="' . esc_url($edit_link) . '">' . esc_html($item['post_title']) . '</a>';
            case 'likes':
            case 'dislikes':
                return $item[$column_name] ?: 0;
            default:
                return print_r($item, true);
        }
    }
}

function sogohlopec_likes_system_admin_menu()
{
    add_menu_page(
        'Статистика лайков',
        'Статистика лайков',
        'manage_options',
        'sogohlopec_likes_stats',
        'sogohlopec_likes_system_stats_page'
    );
}
add_action('admin_menu', 'sogohlopec_likes_system_admin_menu');

function sogohlopec_likes_system_stats_page()
{
?>
    <div class="wrap">
        <h1>Статистика лайков</h1>
        <?php
        $stats_table = new Likes_Stats_Table();
        $stats_table->prepare_items();
        $stats_table->display();
        ?>
    </div>
<?php
}
