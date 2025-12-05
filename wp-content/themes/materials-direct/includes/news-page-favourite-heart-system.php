<?php
/**
 * Get the number of favourites for a post
 */
function md_get_post_favourites( $post_id ) {
    $count = get_post_meta( $post_id, '_md_favourites', true );
    return $count ? intval($count) : 0;
}

/**
 * Check if current user has favourited this post
 */
function md_user_has_favourited( $user_id, $post_id ) {
    $favs = get_user_meta( $user_id, '_md_user_favourites', true );
    if (!is_array($favs)) $favs = [];
    return in_array($post_id, $favs);
}

/**
 * AJAX: Toggle favourite
 */
add_action('wp_ajax_md_toggle_favourite', 'md_toggle_favourite_callback');

function md_toggle_favourite_callback() {
    if (!is_user_logged_in()) wp_send_json_error(['message' => 'Not logged in']);

    $post_id = intval($_POST['post_id']);
    $user_id = get_current_user_id();

    $favs = get_user_meta($user_id, '_md_user_favourites', true);
    if (!is_array($favs)) $favs = [];

    $count = md_get_post_favourites($post_id);

    if (in_array($post_id, $favs)) {
        // Remove favourite
        $favs = array_diff($favs, [$post_id]);
        update_user_meta($user_id, '_md_user_favourites', $favs);
        update_post_meta($post_id, '_md_favourites', max(0, $count - 1));
        wp_send_json_success(['favourited' => false, 'count' => $count - 1]);
    } else {
        // Add favourite
        $favs[] = $post_id;
        update_user_meta($user_id, '_md_user_favourites', $favs);
        update_post_meta($post_id, '_md_favourites', $count + 1);
        wp_send_json_success(['favourited' => true, 'count' => $count + 1]);
    }

    wp_die();
}

/**
 * Load AJAX script
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('md-favourites', get_template_directory_uri() . '/js/favourites.js', ['jquery'], null, true);
    wp_localize_script('md-favourites', 'md_fav_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'logged_in' => is_user_logged_in()
    ]);
});
