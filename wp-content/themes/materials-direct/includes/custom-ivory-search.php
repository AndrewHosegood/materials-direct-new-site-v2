<?php
// AJAX handler for logged-in and non-logged-in users
add_action('wp_ajax_custom_product_search', 'custom_product_search_handler');
add_action('wp_ajax_nopriv_custom_product_search', 'custom_product_search_handler');

/*
function custom_product_search_handler() {
    global $wpdb;

    $term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
    if (empty($term) || strlen($term) < 2) {  
        wp_send_json_success(['html' => '<p>Type at least 2 characters...</p>']);
    }

    $search = '%' . $wpdb->esc_like($term) . '%';

    $sql = $wpdb->prepare("
        SELECT DISTINCT
            p.ID,
            p.post_title,
            p.post_excerpt,
            p.post_content,
            pm_priority.meta_value AS search_priority,
            pm_sku.meta_value AS sku
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_priority
            ON pm_priority.post_id = p.ID AND pm_priority.meta_key = 'search_priority'
        LEFT JOIN {$wpdb->postmeta} pm_sku
            ON pm_sku.post_id = p.ID AND pm_sku.meta_key = '_sku'
        WHERE p.post_type = 'product'
          AND p.post_status = 'publish'
          AND (
              p.post_title LIKE %s
              OR p.post_excerpt LIKE %s
              OR p.post_content LIKE %s
              OR pm_sku.meta_value LIKE %s
          )
        ORDER BY
            (pm_priority.meta_value IS NULL) ASC,   -- No priority = last
            pm_priority.meta_value ASC,             -- Text priority (identical strings group together)
            p.post_title ASC
        LIMIT 10
    ", $search, $search, $search, $search);

    $results = $wpdb->get_results($sql);

    if (empty($results)) {
        $html = '<p>No products found.</p>';
    } else {
        $html = '<ul class="custom-search-results">';
        foreach ($results as $row) {
            $title = esc_html($row->post_title);
            $permalink = get_permalink($row->ID);
            $excerpt = !empty($row->post_excerpt) ? truncate_plain_text($row->post_excerpt, 150)
                     : truncate_plain_text($row->post_content, 150);
            $sku = !empty($row->sku) ? ' (SKU: ' . esc_html($row->sku) . ')' : '';

            $html .= '<li>';
            $html .= '<a class="search-result-title" href="' . esc_url($permalink) . '"><strong>' . $title . $sku . '</strong></a>';
            $html .= '<br><p class="search-result-text">' . esc_html($excerpt) . '</p>';
            $html .= '</li>';
        }
        $html .= '</ul>';

        // "View more" link - points to a standard WooCommerce search page (or create a custom one)
        $view_more_url = add_query_arg('s', $term, home_url('/shop/'));  // Adjust /shop/ to your shop page
        $html .= '<p class="view-more"><a href="' . esc_url($view_more_url) . '">View more results →</a></p>';
    }

    wp_send_json_success(['html' => $html]);
}
*/



function custom_product_search_handler() {
    global $wpdb;

    $original_term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';

    // if (empty($term) || strlen($term) < 2) {
    //     wp_send_json_success(['html' => '<p>Type at least 2 characters...</p>']);
    // }

    $term = normalize_search_term($original_term);

    $search = '%' . $wpdb->esc_like($term) . '%';

    $sql = $wpdb->prepare("
        SELECT DISTINCT
            p.ID,
            p.post_title,
            p.post_excerpt,
            p.post_content,
            pm_priority.meta_value AS search_priority,
            pm_sku.meta_value AS sku
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_priority
            ON pm_priority.post_id = p.ID AND pm_priority.meta_key = 'search_priority'
        LEFT JOIN {$wpdb->postmeta} pm_sku
            ON pm_sku.post_id = p.ID AND pm_sku.meta_key = '_sku'
        WHERE p.post_type = 'product'
          AND p.post_status = 'publish'
          AND (
              p.post_title LIKE %s
              OR p.post_excerpt LIKE %s
              OR p.post_content LIKE %s
              OR pm_sku.meta_value LIKE %s
          )
        ORDER BY
            (pm_priority.meta_value IS NULL) ASC,
            pm_priority.meta_value ASC,
            p.post_title ASC
        LIMIT 20
    ", $search, $search, $search, $search);

    $results = $wpdb->get_results($sql);

    if (empty($results)) {
        $html = '<div class="custom-search-results-wrapper">';
        $html .= '<p>No products found.</p>';
        $html .= '</div>';
    } else {
        $html = '<div class="custom-search-results-wrapper">';
        $html .= '<ul class="custom-search-results">';
        foreach ($results as $row) {
            $title     = esc_html($row->post_title);
            $permalink = get_permalink($row->ID);
            $excerpt   = !empty($row->post_excerpt)
                ? truncate_plain_text($row->post_excerpt, 150)
                : truncate_plain_text($row->post_content, 150);
            $sku       = !empty($row->sku) ? ' (SKU: ' . esc_html($row->sku) . ')' : '';

            $html .= '<li>';
            $html .= '<a class="search-result-title" href="' . esc_url($permalink) . '"><strong>' . $title . $sku . '</strong></a>';
            $html .= '<br><p class="search-result-text">' . esc_html($excerpt) . '</p>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';

        // View more footer
        $view_more_url = home_url('/product-search/?q=' . urlencode($original_term));
        $html .= '<div class="custom-search-view-more">';
        $html .= '<p class="header__view-more"><a href="' . esc_url($view_more_url) . '">View more results →</a></p>';
        $html .= '</div>';
    }
    /*
    if (empty($results)) {
        $html = '<p>No products found.</p>';
    } else {
        $html = '<ul class="custom-search-results">';
        foreach ($results as $row) {
            $title     = esc_html($row->post_title);
            $permalink = get_permalink($row->ID);
            $excerpt   = !empty($row->post_excerpt)
                ? truncate_plain_text($row->post_excerpt, 150)
                : truncate_plain_text($row->post_content, 150);
            $sku       = !empty($row->sku) ? ' (SKU: ' . esc_html($row->sku) . ')' : '';

            $html .= '<li>';
            $html .= '<a class="search-result-title" href="' . esc_url($permalink) . '"><strong>' . $title . $sku . '</strong></a>';
            $html .= '<br><p class="search-result-text">' . esc_html($excerpt) . '</p>';
            $html .= '</li>';
        }
        $html .= '</ul>';

       $view_more_url = home_url('/product-search/?q=' . urlencode($original_term));
        $html .= '<p class="header__view-more"><a href="' . esc_url($view_more_url) . '">View more results →</a></p>';
    }
    */
    wp_send_json_success(['html' => $html]);
}



// Reuse your truncate function (add to functions.php if not already)
function truncate_plain_text($text, $limit = 150) {
    $text = wp_strip_all_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = trim(preg_replace('/\s+/', ' ', $text));
    if (mb_strlen($text) <= $limit) return $text;
    $truncated = mb_substr($text, 0, $limit);
    $truncated = preg_replace('/\s+\S*$/', '', $truncated);
    return $truncated . '…';
}

//Filter em dash to hyphens on product search
function normalize_search_term($term) {
    // Replace common typographic dashes with standard hyphen
    $term = str_replace(['–', '—', '−', '‒', '‑'], '-', $term);
    
    // Normalize whitespace (multiple spaces/tabs/newlines → single space)
    $term = preg_replace('/\s+/', ' ', $term);
    
    // Trim
    $term = trim($term);
    
    return $term;
}


// Enqueue JS (in functions.php)
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('custom-search', get_template_directory_uri() . '/js/custom-search.js', ['jquery'], null, true);
    wp_localize_script('custom-search', 'customSearch', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('custom_search')  // Optional security
    ]);
});