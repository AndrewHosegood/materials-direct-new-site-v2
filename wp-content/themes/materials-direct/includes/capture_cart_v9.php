<?php

// COLLECT THE DATA 
function get_cart_capture_data() {    

    if (!function_exists('WC') || !WC()->cart) {
        return []; 
    }

    $results = [];  

    if (WC()->cart->get_cart_contents_count() > 0) {
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

        if (empty($cart_item['custom_inputs']) || !is_array($cart_item['custom_inputs'])) {
            continue;
        }

        $inputs   = $cart_item['custom_inputs'];
        $address  = $inputs['shipping_address'] ?? [];
        $fees     = $cart_item['optional_fees'] ?? [];

        $product       = $cart_item['data'];
        $product_id    = $cart_item['product_id'];
        $variation_id  = $cart_item['variation_id'] ?? 0;
        $variation     = $cart_item['variation'] ?? [];
        $quantity      = $cart_item['quantity'];
        $product_name  = sanitize_text_field($product->get_name());
        //$price         = wc_get_price_to_display($product);
      	$price =        $inputs['price'];
        $total_price   = wc_format_decimal($price * $quantity, 2);
        $thumb_image   = wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') ?: 'https://placehold.co/150';

        $results[] = [
            'product_id' => $product_id,
            'variation_id' => $variation_id,
            'variation' => $variation,
            'product' => $product_name,
            'thumb_image' => $thumb_image,
            'Price' => wc_format_decimal($price, 2),
            'Total Price' => $total_price,
            'Quantity' => $quantity,

            // === Meta mapping (NEW SITE → OLD STRUCTURE) ===
            'Part shape' => sanitize_text_field($inputs['shape_type'] ?? ''),
            'Width' => floatval($inputs['width'] ?? 0),
            'Length' => floatval($inputs['length'] ?? 0),
            'Radius' => floatval($inputs['custom_radius'] ?? 0),
            'Notes' => sanitize_text_field($inputs['despatch_notes'] ?? ''),
            'Shipping Weights' => floatval($inputs['total_del_weight'] ?? 0),
            'Total number of parts' => intval($inputs['qty'] ?? 0),
            'Manufacturers COFC' => floatval($fees['add_manufacturers_COFC'] ?? 0),
            'First Article Inspection Report' => floatval($inputs['add_fair'] ?? 0),

            'Width (Inch)' => floatval($inputs['width_inches'] ?? 0),
            'Length (Inch)' => floatval($inputs['length_inches'] ?? 0),
            'Radius (Inch)' => floatval($inputs['custom_radius_inches'] ?? 0),

            'Cost Per Part' => floatval($inputs['cost_per_part'] ?? 0),
            'Customer Shipping Weights' => floatval($inputs['total_del_weight'] ?? 0),

            'Upload .DXF Drawing' => sanitize_text_field($inputs['dxf_path'] ?? ''),
            'Upload .PDF Drawing' => sanitize_text_field($inputs['pdf_path'] ?? ''),

            'Sheets Required' => intval($inputs['sheets_required'] ?? 0),
            'shipping_total_raw' => floatval($inputs['final_shipping'] ?? 0),
            'on_backorder' => intval($inputs['is_backorder'] ?? 0),
            'raw_date' => sanitize_text_field($inputs['shipments'] ?? ''),
            'discount_raw_new' => floatval($inputs['discount_rate'] ?? 0),
            'cost_per_part_raw' => floatval($inputs['cost_per_part'] ?? 0),
            'country_value' => sanitize_text_field($inputs['country'] ?? ''),
            '_Shipping Total' => floatval($inputs['final_shipping'] ?? 0),

            // === Address mapping ===
            'Address-1' => sanitize_text_field($address['street_address'] ?? ''),
            'Address-2' => sanitize_text_field($address['address_line2'] ?? ''),
            'Address-3' => sanitize_text_field($address['city'] ?? ''),
            'Address-4' => sanitize_text_field($address['county_state'] ?? ''),
            'Address-5' => sanitize_text_field($address['zip_postal'] ?? ''),
            'Address-6' => sanitize_text_field($address['country'] ?? ''),
        ];
    }
    }

    return $results;
}

// CREATE CUSTOM POST TYPE
function register_capture_cart_cpt() {
    error_log('Registering capture_cart post type');
    register_post_type('capture_cart', [
        'labels' => [
            'name' => 'Captured Carts',
            'singular_name' => 'Captured Cart',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'supports' => ['title', 'custom-fields'],
        'menu_position' => 25,
        'menu_icon' => 'dashicons-cart',
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ]);
}
add_action('init', 'register_capture_cart_cpt');

// ADD CUSTOM ADMIN MENU PAGE
function add_captured_carts_admin_menu() {
    error_log('Adding Captured Carts admin menu');
    add_menu_page(
        'Captured Carts',
        'Captured Carts',
        'edit_posts',
        'captured-carts',
        'display_captured_carts_table',
        'dashicons-cart',
        25
    );
}
add_action('admin_menu', 'add_captured_carts_admin_menu');




// ENQUEUE ADMIN SCRIPTS
function enqueue_captured_carts_admin_scripts($hook) {
    // Enqueue on the Captured Carts admin page and My Account page
    if ($hook === 'toplevel_page_captured-carts' || is_account_page()) {
        wp_enqueue_script(
            'captured-carts-admin',
            get_theme_file_uri('js/captured-carts-admin-v9.js'),
            ['jquery'],
            '1.1.6', // Increment version to avoid caching issues
            true
        );

        wp_localize_script(
            'captured-carts-admin',
            'capturedCartsAjax',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('captured_carts_nonce'),
            ]
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_captured_carts_admin_scripts'); // Changed to wp_enqueue_scripts for front-end
add_action('admin_enqueue_scripts', 'enqueue_captured_carts_admin_scripts'); // Keep for admin




// DISPLAY CAPTURED CARTS TABLE
function display_captured_carts_table() {
    ?>
    <div class="wrap">
        <h1>Captured Carts</h1>
        <?php
        $args = [
            'post_type' => 'capture_cart',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];
        $carts = new WP_Query($args);

        if (is_wp_error($carts)) {
            echo '<p>Error retrieving captured carts: ' . esc_html($carts->get_error_message()) . '</p>';
            return;
        }

        // Fetch all user emails
        $users = get_users([
            'fields' => ['user_email'],
            'number' => -1,
            'orderby' => 'user_email',
            'order'   => 'ASC',
        ]);
        ?>
        <form id="bulk-delete-captured-carts-form" method="post">
            <?php wp_nonce_field('captured_carts_nonce', 'bulk_delete_carts_nonce_field'); ?>
            <div style="min-width:100%; overflow:scroll; min-height: 330px;">
            <table style="min-width: 1750px;" class="capture-cart-list-table wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="font-size: 11.5px;"><strong>Image</strong></th>
                        <th style="font-size: 11.5px;"><strong>Cart Group ID</strong></th>
                        <th style="font-size: 11.5px;"><strong>Capture Date</strong></th>
                        <th style="font-size: 11.5px;"><strong>Customer Shipping Weights</strong></th>
                        <th style="font-size: 11.5px;"><strong>Product</strong></th>
                        <th style="font-size: 11.5px;"><strong>Part Shape</strong></th> 
                        <th style="font-size: 11.5px;"><strong>Width</strong></th>
                        <th style="font-size: 11.5px;"><strong>Length</strong></th>
                        <th style="font-size: 11.5px;"><strong>Width (Inch)</strong></th>
                        <th style="font-size: 11.5px;"><strong>Length (Inch)</strong></th>
                        <th style="font-size: 11.5px;"><strong>Radius</strong></th>
                        <th style="font-size: 11.5px; width: 120px;"><strong>Notes</strong></th>
                        <th style="font-size: 11.5px;"><strong>PDF Drawing</strong></th>
                        <th style="font-size: 11.5px;"><strong>DXF Drawing</strong></th>
                        <th style="font-size: 11.5px;"><strong>Total Parts</strong></th>
                        <th style="font-size: 11.5px;"><strong>Subtotal</strong></th>
                        <th style="font-size: 11.5px;"><strong>Is This A Delivery Option Order?</strong></th>
                        <th style="font-size: 11.5px;"><strong>Does The Customer Have Credit Account?</strong></th>
                        <th style="font-size: 11.5px; width: 180px;"><strong>Customer Email</strong></th>
                        <th style="font-size: 11.5px; width: 85px;"><strong>Send Email</strong></th>
                        <th style="font-size: 11.5px;"><strong>Address</strong></th>
                        <th style="font-size: 11.5px;"><strong><input style="margin: 0;" type="checkbox" id="select-all-carts"> Delete</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($carts->have_posts()) {
                        while ($carts->have_posts()) {
                            $carts->the_post();
                            $post_id = get_the_ID();
                            $capture_date = get_post_meta($post_id, 'capture_date', true) ?: 'N/A';
                            $product_name = get_post_meta($post_id, 'product_name', true) ?: 'N/A';
                            $total_parts = get_post_meta($post_id, 'total_parts', true) ?: 0;
                            $total_price = get_post_meta($post_id, 'total_price', true) ?: 0;
                            $cart_group_id = get_post_meta($post_id, 'cart_group_id', true) ?: 'N/A';
                            $width = get_post_meta($post_id, 'width', true) ?: 'N/A';
                            $length = get_post_meta($post_id, 'length', true) ?: 'N/A';
                            $radius = get_post_meta($post_id, 'radius', true) ?: 'N/A';
                            $email = get_post_meta($post_id, 'customer_email', true) ?: '';
                            $cart_item = get_post_meta($post_id, 'cart_item', true);
                            $notes = !empty($cart_item['Notes']) ? esc_html($cart_item['Notes']) : 'N/A';
                            $cpp = !empty($cart_item['Customer Shipping Weights']) ? esc_html($cart_item['Customer Shipping Weights']) : 'N/A';
                            $dxf = !empty($cart_item['Upload .DXF Drawing']) ? esc_html($cart_item['Upload .DXF Drawing']) : 'N/A';
                            $pdf = !empty($cart_item['Upload .PDF Drawing']) ? esc_html($cart_item['Upload .PDF Drawing']) : 'N/A';
                            $width_inch = !empty($cart_item['Width (Inch)']) ? esc_html($cart_item['Width (Inch)']) : 'N/A';
                            $length_inch = !empty($cart_item['Length (Inch)']) ? esc_html($cart_item['Length (Inch)']) : 'N/A';
                            $thumb_image = !empty($cart_item['thumb_image']) ? esc_url($cart_item['thumb_image']) : 'https://placehold.co/150';
                            $delivery_option = get_post_meta($post_id, 'delivery_option', true) ?: 'N/A';
                            $custom_expiry_schedule = get_post_meta($post_id, 'custom_expiry_schedule', true) ?: 'N/A';
                            $sheets_required = !empty($cart_item['Sheets Required']) ? esc_html($cart_item['Sheets Required']) : 'N/A';
                            $country_value = !empty($cart_item['country_value']) ? esc_html($cart_item['country_value']) : 'N/A';
                            $mcofc_fair_final_hidden = !empty($cart_item['mcofc_fair_final_hidden']) ? esc_html($cart_item['mcofc_fair_final_hidden']) : 'N/A';
                            $part_shape = !empty($cart_item['Part shape']) ? esc_html($cart_item['Part shape']) : 'N/A';
                            $total_number_of_parts = !empty($cart_item['Total number of parts']) ? esc_html($cart_item['Total number of parts']) : 'N/A';
                            $is_split_schedule = !empty($cart_item['_is_split_schedule']) ? esc_html($cart_item['_is_split_schedule']) : 'N/A';
                            $cost_per_part_raw = !empty($cart_item['cost_per_part_raw']) ? esc_html($cart_item['cost_per_part_raw']) : 'N/A';
                            $discount_raw_new = !empty($cart_item['discount_raw_new']) ? esc_html($cart_item['discount_raw_new']) : 'N/A';
                            $raw_date = !empty($cart_item['raw_date']) ? esc_html($cart_item['raw_date']) : 'N/A';
                            $delivery_count = !empty($cart_item['delivery_count']) ? esc_html($cart_item['delivery_count']) : 'N/A';
                            $on_backorder = !empty($cart_item['on_backorder']) ? esc_html($cart_item['on_backorder']) : 'N/A';
                            $mcofc_fair_values = !empty($cart_item['mcofc_fair_values']) ? esc_html($cart_item['mcofc_fair_values']) : 'N/A';
                            $shipping_total_raw = !empty($cart_item['shipping_total_raw']) ? esc_html($cart_item['shipping_total_raw']) : 'N/A';
                            $rolls_value = !empty($cart_item['rolls_value']) ? esc_html($cart_item['rolls_value']) : 'N/A';
                            $currency_rate = !empty($cart_item['currency_rate']) ? esc_html($cart_item['currency_rate']) : 'N/A';
                            $currently_showing = !empty($cart_item['_Currently Showing']) ? esc_html($cart_item['_Currently Showing']) : 'N/A';
                            $untitled = !empty($cart_item['_Untitled']) ? esc_html($cart_item['_Untitled']) : 'N/A';
                            $shipping_total = !empty($cart_item['_Shipping Total']) ? esc_html($cart_item['_Shipping Total']) : 'N/A';
                            $roll_length_metres = !empty($cart_item['Roll Length (Metres)']) ? esc_html($cart_item['Roll Length (Metres)']) : 'N/A';
                            $address_1 = !empty($cart_item['Address-1']) ? esc_html($cart_item['Address-1']) : 'N/A';
                            $address_2 = !empty($cart_item['Address-2']) ? esc_html($cart_item['Address-2']) : 'N/A';
                            $address_3 = !empty($cart_item['Address-3']) ? esc_html($cart_item['Address-3']) : 'N/A';
                            $address_4 = !empty($cart_item['Address-4']) ? esc_html($cart_item['Address-4']) : 'N/A';
                            $address_5 = !empty($cart_item['Address-5']) ? esc_html($cart_item['Address-5']) : 'N/A';
                            $address_6 = !empty($cart_item['Address-6']) ? esc_html($cart_item['Address-6']) : 'N/A';
                            $user = get_user_by('email', $email);
                            $user_id = $user ? $user->ID : 0;
                            $allow_credit = $user_id ? get_field('credit_options_allow_user_credit_option', 'user_' . $user_id) : 'N/A';
                            ?>
                            <tr data-post-id="<?php echo esc_attr($post_id); ?>">

                                <td style="font-size: 11.5px;"><img src="<?php echo esc_url($thumb_image); ?>" alt="<?php echo esc_attr($product_name); ?>" style="width: 50px; height: 50px; object-fit: cover;" /></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($cart_group_id); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($capture_date); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($cpp); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($product_name); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($part_shape); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($width); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($length); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($width_inch); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($length_inch); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($radius); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($notes); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($pdf); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($dxf); ?></td>
                                <td style="font-size: 11.5px;"><?php echo esc_html($total_number_of_parts); ?></td>
                                <td style="font-size: 11.5px;"><?php echo wc_price($total_price); ?></td>
                                <td style="font-size: 11.5px;"><?php if($delivery_option == 1){ echo "YES"; } else { echo "NO"; } ?></td>
                                <td style="font-size: 11.5px;"><?php if($allow_credit == 1){ echo "YES"; } else { echo "NO"; } ?></td>
   

                                <td style="font-size: 11.5px; position: relative;">
                                    <input type="text" class="customer-email-input" data-post-id="<?php echo esc_attr($post_id); ?>" value="<?php echo esc_attr($email); ?>" placeholder="Search email..." style="width: 100%;">
                                    <div class="email-suggestions" style="position: absolute; z-index: 9; width: 100%; max-height: 150px; overflow-y: auto; background: white; border: 1px solid #ccc; display: none;"></div>
                                </td>


                                <td style="font-size: 11.5px;">
                                    <button class="button send-email" data-post-id="<?php echo esc_attr($post_id); ?>" <?php echo empty($email) ? 'disabled' : ''; ?>>
                                        Send
                                    </button>
                                </td>

                                <td style="font-size: 11.5px;">
                                    <?php echo $address_1; ?>, <?php echo $address_2; ?>, <?php echo $address_3; ?>, <?php echo $address_4; ?>, <?php echo $address_5; ?>, <?php echo $address_6; ?>
                                </td>


                                <td style="font-size: 11.5px;">
                                    <input type="checkbox" name="delete_cart_ids[]" value="<?php echo esc_attr($post_id); ?>">
                                </td>

                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="21">No captured carts found.</td>
                        </tr>
                        <?php
                    }
                    wp_reset_postdata();
                    ?>
                </tbody>
            </table>
            </div>
            <p>
                <button type="submit" class="button button-primary" id="bulk-delete-captured-carts">Delete Selected</button>
                <a href="/wp-admin/admin.php?page=captured-carts" class="button button-primary">Update Table</a>
            </p>
        </form>
    </div>
    <?php
}



// AJAX HANDLER FOR EMAIL SEARCH
function search_customer_emails() {
    check_ajax_referer('captured_carts_nonce', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error(['message' => 'Insufficient permissions.'], 403);
    }

    $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    $args = [
        'fields' => ['user_email'],
        'number' => 10, // Limit results for performance
        'search' => '*' . $search_term . '*',
        'search_columns' => ['user_email'],
    ];

    $users = get_users($args);
    $emails = array_map(function($user) {
        return $user->user_email;
    }, $users);

    wp_send_json_success(['emails' => $emails]);
}
add_action('wp_ajax_search_customer_emails', 'search_customer_emails');
// AJAX HANDLER FOR EMAIL SEARCH


// AJAX HANDLER FOR ADDING TO CART
function add_to_cart_from_capture() {
    try {

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

        // Verify post-specific nonce
        if (!wp_verify_nonce($nonce, 'restore_cart_' . $post_id)) {
            wp_send_json_error(['message' => 'Security check failed.'], 403);
        }

        if ($post_id <= 0 || get_post_type($post_id) !== 'capture_cart') {
            wp_send_json_error(['message' => 'Invalid post ID.'], 400);
        }

        // Check if item has already been restored
        $restored = get_post_meta($post_id, 'restored_to_cart', true);
        if ($restored === 'yes') {
            wp_send_json_error(['message' => 'This item has already been added to the cart.'], 400);
        }

        // Check permissions: Allow if user has edit_posts or their email matches customer_email
        $current_user = wp_get_current_user();
        $customer_email = get_post_meta($post_id, 'customer_email', true);
        if (!current_user_can('edit_posts') && (!is_user_logged_in() || $current_user->user_email !== $customer_email)) {
            wp_send_json_error(['message' => 'You are not authorized to restore this cart.'], 403);
        }


        $cart_item = get_post_meta($post_id, 'cart_item', true);
        if (empty($cart_item) || !is_array($cart_item)) {
            wp_send_json_error(['message' => 'No cart item data found.'], 400);
        }

        if (!function_exists('WC') || !WC()->cart) {
            wp_send_json_error(['message' => 'WooCommerce cart not available.'], 500);
        }

        if (!WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
            if (!WC()->session->has_session()) {
                error_log('Failed to start WooCommerce session');
                wp_send_json_error(['message' => 'Failed to start WooCommerce session.'], 500);
            }
        }

        $product_id = isset($cart_item['product_id']) ? intval($cart_item['product_id']) : 0;
        $quantity = isset($cart_item['Quantity']) ? intval($cart_item['Quantity']) : 1;
        $variation_id = isset($cart_item['variation_id']) ? intval($cart_item['variation_id']) : 0;
        $variation = isset($cart_item['variation']) && is_array($cart_item['variation']) ? $cart_item['variation'] : [];


        if ($product_id <= 0) {
            error_log('Invalid product ID: ' . $product_id);
            wp_send_json_error(['message' => 'Invalid product ID.'], 400);
        }

        $cart_item_data = [
            '_gravity_form_lead' => isset($cart_item['gravity_form_lead']) ? $cart_item['gravity_form_lead'] : [],
            'restored_from_capture' => true,
            'cart_metadata' => [
                'Upload .DXF Drawing' => isset($cart_item['Upload .DXF Drawing']) ? $cart_item['Upload .DXF Drawing'] : 'N/A',
                'Upload .PDF Drawing' => isset($cart_item['Upload .PDF Drawing']) ? $cart_item['Upload .PDF Drawing'] : 'N/A',
                'Width (MM)' => isset($cart_item['Width']) ? $cart_item['Width'] : 'N/A',
                'width_inch'    => isset($cart_item['Width (Inch)']) ? $cart_item['Width (Inch)'] : 'N/A',
                'Length (MM)' => isset($cart_item['Length']) ? $cart_item['Length'] : 'N/A',
                'length_inch'   => isset($cart_item['Length (Inch)']) ? $cart_item['Length (Inch)'] : 'N/A',
                'Total number of parts' => isset($cart_item['Total number of parts']) ? $cart_item['Total number of parts'] : 'N/A',
                'Despatch Notes' => isset($cart_item['Notes']) ? $cart_item['Notes'] : 'N/A',
                'Cost Per Part' => isset($cart_item['Cost Per Part']) ? $cart_item['Cost Per Part'] : 'N/A',
                'Customer Shipping Weight(s)' => isset($cart_item['Customer Shipping Weights']) ? $cart_item['Customer Shipping Weights'] : 'N/A',
                'Sheets Required' => isset($cart_item['Sheets Required']) ? $cart_item['Sheets Required'] : 'N/A',
                'shipping_total_raw' => isset($cart_item['shipping_total_raw']) ? $cart_item['shipping_total_raw'] : 'N/A',
                'rolls_value' => isset($cart_item['rolls_value']) ? $cart_item['rolls_value'] : 'N/A',
                'currency_rate' => isset($cart_item['currency_rate']) ? $cart_item['currency_rate'] : 'N/A',
                'mcofc_fair_values' => isset($cart_item['mcofc_fair_values']) ? $cart_item['mcofc_fair_values'] : 'N/A',
                'on_backorder' => isset($cart_item['on_backorder']) ? $cart_item['on_backorder'] : 'N/A',
                'delivery_count' => isset($cart_item['delivery_count']) ? $cart_item['delivery_count'] : 'N/A',
                'raw_date' => isset($cart_item['raw_date']) ? $cart_item['raw_date'] : 'N/A',
                'discount_raw_new' => isset($cart_item['discount_raw_new']) ? $cart_item['discount_raw_new'] : 'N/A',
                'cost_per_part_raw' => isset($cart_item['cost_per_part_raw']) ? $cart_item['cost_per_part_raw'] : 'N/A',
                '_is_split_schedule' => isset($cart_item['_is_split_schedule']) ? $cart_item['_is_split_schedule'] : 'N/A',
                'mcofc_fair_final_hidden' => isset($cart_item['mcofc_fair_final_hidden']) ? $cart_item['mcofc_fair_final_hidden'] : 'N/A',
                'country_value' => isset($cart_item['country_value']) ? $cart_item['country_value'] : 'N/A',
                'Part shape' => isset($cart_item['Part shape']) ? $cart_item['Part shape'] : 'N/A',
                '_Currently Showing' => isset($cart_item['_Currently Showing']) ? $cart_item['_Currently Showing'] : 'N/A',
                '_Untitled' => isset($cart_item['_Untitled']) ? $cart_item['_Untitled'] : 'N/A',
                '_Shipping Total' => isset($cart_item['_Shipping Total']) ? $cart_item['_Shipping Total'] : 'N/A',
                'Roll Length (Metres)' => isset($cart_item['Roll Length (Metres)']) ? $cart_item['Roll Length (Metres)'] : 'N/A',
                'form_id' => isset($cart_item['form_id']) ? $cart_item['form_id'] : 'N/A', // Explicitly include form_id
            ],
        ];

        

        // Set custom price with validation
        if (isset($cart_item['Total Price']) && is_numeric($cart_item['Total Price']) && floatval($cart_item['Total Price']) > 0 && $quantity > 0) {
            $custom_price = floatval($cart_item['Total Price']) / $quantity;
            $cart_item_data['custom_price'] = $custom_price;
            //error_log('Using Total Price for custom_price: Total Price = ' . $cart_item['Total Price'] . ', Quantity = ' . $quantity . ', Custom Price = ' . $custom_price);
        } else {
            //error_log('Invalid or missing Total Price for post ID ' . $post_id . ': ' . (isset($cart_item['Total Price']) ? $cart_item['Total Price'] : 'unset'));
            $product = wc_get_product($product_id);
            if ($product && $product->exists()) {
                $cart_item_data['custom_price'] = floatval(wc_get_price_to_display($product));
                //error_log('Falling back to product price: ' . $cart_item_data['custom_price']);
            } else {
                //error_log('Product not found for ID: ' . $product_id);
                wp_send_json_error(['message' => 'Unable to determine price for product ID ' . $product_id], 400);
            }
        }


        // Validate product
        $product = wc_get_product($product_id);
        if (!$product || !$product->exists() || !$product->is_purchasable()) {
            //error_log('Invalid or unpurchasable product: ' . $product_id);
            wp_send_json_error(['message' => 'Product is no longer available.'], 400);
        }

        // Validate variation if applicable
        if ($variation_id > 0) {
            $variation_product = wc_get_product($variation_id);
            if (!$variation_product || !$variation_product->exists()) {
                //error_log('Invalid variation: ' . $variation_id);
                wp_send_json_error(['message' => 'Product variation is no longer available.'], 400);
            }
            // Validate variation attributes
            $variation_attributes = $variation_product->get_variation_attributes();
            foreach ($variation as $key => $value) {
                if (!isset($variation_attributes[$key]) || ($variation_attributes[$key] !== '' && $variation_attributes[$key] !== $value)) {
                    //error_log('Invalid variation attribute: ' . $key . ' => ' . $value);
                    wp_send_json_error(['message' => 'Invalid variation attributes.'], 400);
                }
            }
        }

        // Attempt to add to cart
        //error_log('Attempting to add to cart: Product ID ' . $product_id . ', Quantity: ' . $quantity . ', Variation ID: ' . $variation_id . ', Custom Price: ' . $cart_item_data['custom_price']);
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $cart_item_data);

        if (!$cart_item_key) {
            //error_log('Failed to add product to cart: ' . $product_id);
            wp_send_json_error(['message' => 'Failed to add item to cart.'], 500);
        }

        // Mark as restored
        update_post_meta($post_id, 'restored_to_cart', 'yes');

        // Restore shipping details
        $existing_shipments = WC()->session->get('shipments', []);
        $shipment_data = [];
        if (!empty($cart_item['Shipments']) && is_array($cart_item['Shipments'])) {
            $date = date('d/m/Y', strtotime('+35 days'));
            foreach ($cart_item['Shipments'] as $shipment) {
                $shipment_data[$date] = [
                    'qty' => intval($shipment['qty']),
                    'weight' => isset($cart_item['Customer Shipping Weights']) ? floatval($cart_item['Customer Shipping Weights']) : 0,
                    'shipping_total' => floatval($shipment['shipping_total']),
                ];
            }
        }
        if (!empty($shipment_data)) {
            $existing_shipments[] = $shipment_data;
            WC()->session->set('shipments', $existing_shipments);
            error_log('Restored shipment data: ' . print_r($shipment_data, true));
        } else {
            error_log('No valid shipment data for post ID ' . $post_id);
        }


        // Check and restore address session variables
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $address_fields = [
            'street_address' => $cart_item['Address-1'],
            'address_line_2' => $cart_item['Address-2'],
            'city' => $cart_item['Address-3'],
            'county' => $cart_item['Address-4'],
            'postcode' => $cart_item['Address-5'],
            'country' => $cart_item['Address-6'],
        ];

        $required_fields = ['street_address', 'city', 'postcode', 'country'];
        $is_valid_address = true;
        foreach ($required_fields as $key) {
            if (empty($address_fields[$key]) || $address_fields[$key] === 'N/A') {
                $is_valid_address = false;
                error_log("Invalid or missing captured address field: $key for post ID $post_id");
                break;
            }
        }

        if ($is_valid_address) {
            error_log('Restoring captured cart address to session for post ID ' . $post_id . ': ' . print_r($address_fields, true));
            foreach ($address_fields as $session_key => $cart_value) {
                $_SESSION[$session_key] = ($cart_value !== 'N/A' && !empty($cart_value)) ? sanitize_text_field($cart_value) : '';
                error_log("Set session variable $session_key: " . $_SESSION[$session_key]);
            }
        } else {
            error_log('Captured cart address is incomplete for post ID ' . $post_id . ', clearing session address variables');
            foreach ($address_fields as $session_key => $cart_value) {
                $_SESSION[$session_key] = '';
                error_log("Cleared session variable $session_key due to incomplete address");
            }
            wp_send_json_error(['message' => 'Captured cart address is incomplete. Please update the address before proceeding.'], 400);
        }


        session_write_close();


        WC()->cart->calculate_totals();
        error_log('Cart totals calculated successfully');

        // Send notification email to andrewh@materials-direct.com



        // Check if all posts in the cart_group_id are restored
        $cart_group_id = get_post_meta($post_id, 'cart_group_id', true);
        if ($cart_group_id) {
            $args = [
                'post_type' => 'capture_cart',
                'post_status' => 'publish',
                'meta_query' => [
                    [
                        'key' => 'cart_group_id',
                        'value' => $cart_group_id,
                    ],
                ],
                'posts_per_page' => -1,
            ];
            $group_posts = get_posts($args);
            $all_restored = true;
            foreach ($group_posts as $group_post) {
                $restored_status = get_post_meta($group_post->ID, 'restored_to_cart', true);
                if ($restored_status !== 'yes') {
                    $all_restored = false;
                    break;
                }
            }

            if ($all_restored) {
                // Send notification email for the entire cart group
                send_restore_notification_email($cart_group_id, $customer_email);
            } else {
                error_log('Not all posts in cart_group_id ' . $cart_group_id . ' are restored yet.');
            }
        } else {
            error_log('No cart_group_id found for post ID ' . $post_id);
        }




        wp_send_json_success([
            'message' => 'Item added to cart successfully.',
            //'cart_url' => wc_get_cart_url() . '?refresh=' . time(),
        ]);
    } catch (Exception $e) {
        error_log('Error in add_to_cart_from_capture: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        wp_send_json_error(['message' => 'Server error: ' . $e->getMessage()], 500);
    }
}
add_action('wp_ajax_add_to_cart_from_capture', 'add_to_cart_from_capture');

// END AJAX HANDLER FOR ADDING TO CART






// SEND NOTIFICATION EMAIL ON CART RESTORATION
function send_restore_notification_email($cart_group_id, $customer_email) {
    try {
        error_log('Starting send_restore_notification_email for cart_group_id: ' . $cart_group_id);

        // Validate inputs
        if (empty($cart_group_id)) {
            error_log('Invalid cart_group_id');
            return;
        }

        // Query all restored posts with the given cart_group_id
        $args = [
            'post_type' => 'capture_cart',
            'post_status' => 'publish',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'cart_group_id',
                    'value' => $cart_group_id,
                ],
                [
                    'key' => 'restored_to_cart',
                    'value' => 'yes',
                ],
            ],
            'posts_per_page' => -1,
        ];
        $posts = get_posts($args);

        if (empty($posts)) {
            error_log('No restored posts found for cart_group_id: ' . $cart_group_id);
            return;
        }

        // Prepare email data for all posts
        $items_data = [];
        $delivery_option = 'N/A';
        $first_customer_email = $customer_email;

        foreach ($posts as $post) {
            $post_id = $post->ID;
            $cart_item = get_post_meta($post_id, 'cart_item', true);
            if (empty($cart_item) || !is_array($cart_item)) {
                error_log('Invalid cart item data for post ID: ' . $post_id);
                continue;
            }

            // Get post meta
            $capture_date = get_post_meta($post_id, 'capture_date', true) ?: 'N/A';
            $post_delivery_option = get_post_meta($post_id, 'delivery_option', true) ?: 'N/A';
            $product_name = get_post_meta($post_id, 'product_name', true) ?: $cart_item['product'] ?: 'N/A';
            $total_price = get_post_meta($post_id, 'total_price', true) ?: $cart_item['Total Price'] ?: '0';
            $post_customer_email = get_post_meta($post_id, 'customer_email', true) ?: 'N/A';

            // Validate consistent customer email
            if ($post_customer_email !== $first_customer_email) {
                error_log("Customer email mismatch in cart_group_id $cart_group_id: $post_customer_email vs $first_customer_email");
            }

            // Set delivery option (assume consistent across group)
            if ($delivery_option === 'N/A' && $post_delivery_option !== 'N/A') {
                $delivery_option = $post_delivery_option;
            }

            // Prepare address
            $address_parts = array_filter([
                $cart_item['Address-1'] ?? 'N/A',
                $cart_item['Address-2'] ?? 'N/A',
                $cart_item['Address-3'] ?? 'N/A',
                $cart_item['Address-4'] ?? 'N/A',
                $cart_item['Address-5'] ?? 'N/A',
                $cart_item['Address-6'] ?? 'N/A',
            ], function($part) {
                return $part !== 'N/A' && !empty($part);
            });
            $address = !empty($address_parts) ? implode(', ', $address_parts) : 'N/A';

            // Prepare file URLs
            $pdf_url = !empty($cart_item['Upload .PDF Drawing']) && filter_var($cart_item['Upload .PDF Drawing'], FILTER_VALIDATE_URL) ? $cart_item['Upload .PDF Drawing'] : 'N/A';
            $dxf_url = !empty($cart_item['Upload .DXF Drawing']) && filter_var($cart_item['Upload .DXF Drawing'], FILTER_VALIDATE_URL) ? $cart_item['Upload .DXF Drawing'] : 'N/A';
            $pdf_filename = $pdf_url !== 'N/A' ? basename($pdf_url) : 'N/A';
            $dxf_filename = $dxf_url !== 'N/A' ? basename($dxf_url) : 'N/A';

            // Prepare item data
            $items_data[] = [
                'Post ID' => $post_id,
                'Image' => !empty($cart_item['thumb_image']) ? esc_url($cart_item['thumb_image']) : 'https://placehold.co/150',
                'Cart Group ID' => $cart_group_id,
                'Capture Date' => $capture_date,
                'Customer Shipping Weights' => $cart_item['Customer Shipping Weights'] ?? 'N/A',
                'Product' => $product_name,
                'Part Shape' => $cart_item['Part shape'] ?? 'N/A',
                'Width' => $cart_item['Width'] ?? 'N/A',
                'Length' => $cart_item['Length'] ?? 'N/A',
                'Width (Inch)' => $cart_item['Width (Inch)'] ?? 'N/A',
                'Length (Inch)' => $cart_item['Length (Inch)'] ?? 'N/A',
                'Radius' => $cart_item['Radius'] ?? 'N/A',
                'Notes' => $cart_item['Notes'] ?? 'N/A',
                'PDF Drawing' => $pdf_url !== 'N/A' ? '<a href="' . esc_url($pdf_url) . '" target="_blank">' . esc_html($pdf_filename) . '</a>' : 'N/A',
                'DXF Drawing' => $dxf_url !== 'N/A' ? '<a href="' . esc_url($dxf_url) . '" target="_blank">' . esc_html($dxf_filename) . '</a>' : 'N/A',
                'Total Parts' => $cart_item['Total number of parts'] ?? 'N/A',
                'Subtotal' => wc_price($total_price),
                'Is This A Delivery Option Order?' => $delivery_option == 1 ? 'YES' : 'NO',
                'Does The Customer Have Credit Account?' => 'N/A', // Set below
                'Customer Email' => $post_customer_email,
                'Send Email' => 'N/A',
                'Address' => $address,
            ];
        }

        if (empty($items_data)) {
            error_log('No valid items to include in email for cart_group_id: ' . $cart_group_id);
            return;
        }

        // Get allow_credit status (based on first customer email)
        $user = get_user_by('email', $first_customer_email);
        $user_id = $user ? $user->ID : 0;
        $allow_credit = $user_id && function_exists('get_field') ? get_field('credit_options_allow_user_credit_option', 'user_' . $user_id) : 'N/A';
        foreach ($items_data as &$item) {
            $item['Does The Customer Have Credit Account?'] = $allow_credit == 1 ? 'YES' : 'NO';
        }

        // Prepare email
        $subject = 'Captured Cart Restored - Cart Group ID ' . $cart_group_id;
        $message = '
            <html>
            <body>
                <div class="container">
                    <div class="header">
                        <h2 style="font-family: \'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; font-size: 26px; color: #000000;">Captured Cart Restoration Notification</h2>
                    </div>
                    <div class="content">
                        <p style="color: #000000;">A customer has restored all items in captured cart (Cart Group ID: ' . esc_html($cart_group_id) . ').</p>
                        <table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 100%; font-family: Helvetica, Roboto, Arial, sans-serif;" width="100%">
                            <thead>
                                <tr>
                                    <th class="td" scope="col" style="padding: 10px; color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;" align="left">Field</th>
                                    <th class="td" scope="col" style="padding: 10px; color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;" align="left">Value</th>
                                </tr>
                            </thead>
                            <tbody>';

        foreach ($items_data as $index => $item_data) {
            $message .= '<tr><td colspan="2" style="padding: 10px; background: #f0f0f0; font-weight: bold;">Item ' . ($index + 1) . ' (Post ID: ' . esc_html($item_data['Post ID']) . ')</td></tr>';
            foreach ($item_data as $field => $value) {
                if ($field === 'Post ID') continue; // Skip Post ID in table
                $message .= '
                    <tr>
                        <td class="td" style="padding: 10px; color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;" align="left">' . esc_html($field) . '</td>
                        <td class="td" style="padding: 10px; color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;" align="left">' . ($field === 'Image' ? '<img src="' . esc_url($value) . '" style="width: 50px; height: 50px; object-fit: cover;" />' : wp_kses_post($value)) . '</td>
                    </tr>';
            }
        }

        $message .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </body>
            </html>';

        $headers = ['Content-Type' => 'text/html; charset=UTF-8'];
        $to = 'jordank@materials-direct.com, andrewh@materials-direct.com';

        // Send email
        $sent = wp_mail($to, $subject, $message, $headers);
        if ($sent) {
            error_log('Restore notification email sent successfully to ' . $to . ' for cart_group_id: ' . $cart_group_id);
        } else {
            error_log('Failed to send restore notification email to ' . $to . ' for cart_group_id: ' . $cart_group_id);
        }
    } catch (Exception $e) {
        error_log('Error in send_restore_notification_email: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
    }
}
// END SEND NOTIFICATION EMAIL ON CART RESTORATION





// APPLY CUSTOM PRICE TO CART ITEMS
add_action('woocommerce_before_calculate_totals', function ($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['custom_price']) && is_numeric($cart_item['custom_price']) && $cart_item['custom_price'] > 0) {
            $price = floatval($cart_item['custom_price']);
            $cart_item['data']->set_price($price);
            error_log('Applying custom price for cart item ' . $cart_item_key . ': ' . $price);
        } else {
            error_log('No valid custom price for cart item ' . $cart_item_key . ': ' . (isset($cart_item['custom_price']) ? $cart_item['custom_price'] : 'unset'));
        }
    }
}, 10, 1);
// END APPLY CUSTOM PRICE TO CART ITEMS

// DISPLAY META DATA IN CART
add_filter('woocommerce_cart_item_name', function ($item_name, $cart_item, $cart_item_key) {

    error_log('Cart item metadata for key ' . $cart_item_key . ': ' . print_r($cart_item['cart_metadata'] ?? [], true));

    // Define keys to hide from display
    $hidden_keys = [
        'Sheets Required',
        'shipping_total_raw',
        'currency_rate',
        'mcofc_fair_values',
        'delivery_count',
        'raw_date',
        'discount_raw_new',
        'cost_per_part_raw',
        '_is_split_schedule',
        'mcofc_fair_final_hidden',
        'country_value',
        'on_backorder',
        '_Currently Showing',
        '_Untitled',
        '_Shipping Total',
        'rolls_value',
        'form_id',
        'Part shape',
    ];

    if (isset($cart_item['cart_metadata']) && is_array($cart_item['cart_metadata'])) {
        $metadata = $cart_item['cart_metadata'];
        $item_name .= '<dl class="cart-item-metadata">';


        $form_id = isset($metadata['form_id']) ? $metadata['form_id'] : 'N/A';

        // PART SHAPE
        if ($form_id !== '19' && isset($metadata['Part shape']) && $metadata['Part shape'] !== 'N/A' && !empty($metadata['Part shape'])) {
            $item_name .= '<dt style="margin: 0.4rem 0"><strong>Part Shape:</strong> ' . esc_html($metadata['Part shape']) . '</dt>';
        } elseif ($form_id === '19') {
            error_log('Hiding Part Shape for cart item key ' . $cart_item_key . ' because form_id is 19');
        }
        // PART SHAPE

        // TOTAL NUMBER OF PARTS
        // Display "Total number of parts" if it exists and is not empty or 'N/A'
        if (isset($metadata['Total number of parts']) && $metadata['Total number of parts'] !== 'N/A' && !empty($metadata['Total number of parts'])) {
            $item_name .= '<dt style="margin: 0.4rem 0"><strong>Total number of parts:</strong> ' . esc_html($metadata['Total number of parts']) . '</dt>';
        }
        // TOTAL NUMBER OF PARTS

        // WIDTH (MM)
        if ($form_id !== '19' && isset($metadata['Width (MM)']) && $metadata['Width (MM)'] !== 'N/A' && !empty($metadata['Width (MM)'])) {
            $item_name .= '<dt style="margin: 0.4rem 0"><strong>Width (MM):</strong> ' . esc_html($metadata['Width (MM)']) . '</dt>';
        } elseif ($form_id === '19') {
            error_log('Hiding Width (MM) for cart item key ' . $cart_item_key . ' because form_id is 19');
        }

        // LENGTH (MM)
        if ($form_id !== '19' && isset($metadata['Length (MM)']) && $metadata['Length (MM)'] !== 'N/A' && !empty($metadata['Length (MM)']) && !(isset($metadata['rolls_value']) && strtolower($metadata['rolls_value']) === 'rolls')) {
            $item_name .= '<dt style="margin: 0.4rem 0"><strong>Length (MM):</strong> ' . esc_html($metadata['Length (MM)']) . '</dt>';
        } elseif ($form_id === '19') {
            error_log('Hiding Length (MM) for cart item key ' . $cart_item_key . ' because form_id is 19');
        }

        // COST PER PART
        if ($form_id !== '19' && isset($metadata['Cost Per Part']) && $metadata['Cost Per Part'] !== 'N/A' && !empty($metadata['Cost Per Part'])) {
            $item_name .= '<dt style="margin: 0.4rem 0"><strong>Cost Per Part:</strong> ' . esc_html($metadata['Cost Per Part']) . '</dt>';
        } elseif ($form_id === '19') {
            error_log('Hiding Cost Per Part for cart item key ' . $cart_item_key . ' because form_id is 19');
        }


        // ROLL LENGTH
        // Display "Roll Length (Metres)" after "Total number of parts" if rolls_value is 'rolls' (case-insensitive)
        if (isset($metadata['Roll Length (Metres)']) && isset($metadata['rolls_value']) && strtolower($metadata['rolls_value']) === 'rolls' && $metadata['Roll Length (Metres)'] !== 'N/A' && !empty($metadata['Roll Length (Metres)'])) {
            error_log('Displaying Roll Length (Metres) after Total number of parts: ' . $metadata['Roll Length (Metres)']);
            $item_name .= '<dt class="roll-length-metres" style="margin: 0.4rem 0"><strong>Roll Length (Metres):</strong> ' . esc_html($metadata['Roll Length (Metres)']) . '</dt>';
        } else {
            error_log('Not displaying Roll Length (Metres): rolls_value = ' . ($metadata['rolls_value'] ?? 'not set') . ', value = ' . ($metadata['Roll Length (Metres)'] ?? 'not set'));
        }
        // ROLL LENGTH

        // Loop through remaining metadata
        foreach ($metadata as $key => $value) {
            // Log processing of each key for debugging
            error_log('Processing metadata key: ' . $key . ', value: ' . ($value ?? 'not set') . ', rolls_value: ' . ($metadata['rolls_value'] ?? 'not set'));

          // Skip hidden keys and already displayed fields
            if (in_array($key, $hidden_keys) || in_array($key, ['Part shape', 'Total number of parts', 'Roll Length (Metres)', 'Width (MM)', 'Length (MM)', 'Cost Per Part'])) {
                continue;
            }

            // Conditionally skip "Length (mm)" if rolls_value is 'rolls' (case-insensitive)
            if (strtolower($key) === 'length (mm)' && isset($metadata['rolls_value']) && strtolower($metadata['rolls_value']) === 'rolls') {
                error_log('Hiding Length (mm) because rolls_value = ' . ($metadata['rolls_value'] ?? 'not set') . ', key = ' . $key . ', value = ' . ($value ?? 'not set'));
                continue;
            }

            // Handle file uploads
            if ($key === 'Upload .PDF Drawing') {
                $pdf_url = !empty($value) && $value !== 'N/A' && filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
                if ($pdf_url) {
                    $pdf_filename = basename($pdf_url);
                    $item_name .= '<dt style="margin: 0.4rem 0"><strong>' . esc_html($key) . ':</strong> <a href="' . esc_url($pdf_url) . '" target="_blank">' . esc_html($pdf_filename) . '</a></dt>';
                }
            } elseif ($key === 'Upload .DXF Drawing') {
                $dxf_url = !empty($value) && $value !== 'N/A' && filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
                if ($dxf_url) {
                    $dxf_filename = basename($dxf_url);
                    $item_name .= '<dt style="margin: 0.4rem 0"><strong>' . esc_html($key) . ':</strong> <a href="' . esc_url($dxf_url) . '" target="_blank">' . esc_html($dxf_filename) . '</a></dt>';
                }
            } elseif ($value !== 'N/A' && !empty($value)) {
                $item_name .= '<dt style="margin: 0.4rem 0"><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</dt>';
            }
        }
        $item_name .= '</div>';
    }
    return $item_name;
}, 10, 3);
// END DISPLAY META DATA IN CART





// ADD CART METADATA TO ORDER ITEM META DURING CHECKOUT
add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    // Check if the cart item is restored from a captured cart
    if (isset($values['restored_from_capture']) && $values['restored_from_capture'] === true && isset($values['cart_metadata'])) {
        error_log('Adding cart_metadata to order item for cart_item_key: ' . $cart_item_key);

        // List of meta keys to include in the order
        $meta_keys_to_include = [
            'Part shape',
            'Width (MM)',
            'Length (MM)',
            'Total number of parts',
            'Sheets Required',
            'Customer Shipping Weight(s)',
            'Despatch Notes',
            'Upload .PDF Drawing',
            'Upload .DXF Drawing',
            'Sheets Required',
            'shipping_total_raw',
            'rolls_value',
            'currency_rate',
            'mcofc_fair_values',
            'on_backorder',
            'delivery_count',
            'raw_date',
            'discount_raw_new',
            'cost_per_part_raw',
            '_is_split_schedule',
            'mcofc_fair_final_hidden',
            'country_value',
            '_Currently Showing',
            '_Untitled',
            '_Shipping Total',
            'Roll Length (Metres)',
        ];


        

        

        // Add each meta key-value pair to the order item
        foreach ($meta_keys_to_include as $meta_key) {
            if (isset($values['cart_metadata'][$meta_key]) && $values['cart_metadata'][$meta_key] !== 'N/A') {
                $item->add_meta_data(
                    $meta_key, // Meta key
                    $values['cart_metadata'][$meta_key], // Meta value
                    true // Unique (only one value per key)
                );
                error_log("Added order item meta: $meta_key => " . $values['cart_metadata'][$meta_key]);
            }
        }
    }
}, 10, 4);

// END ADD CART METADATA TO ORDER ITEM META DURING CHECKOUT








// AJAX HANDLER FOR BULK DELETE
function bulk_delete_captured_carts() {
    error_log('Attempting bulk delete with data: ' . print_r($_POST, true));
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'captured_carts_nonce')) {
        error_log('Nonce verification failed for bulk delete');
        wp_send_json_error(['message' => 'Security check failed.'], 400);
    }

    if (!current_user_can('edit_posts')) {
        error_log('Insufficient permissions for user: ' . wp_get_current_user()->user_login);
        wp_send_json_error(['message' => 'Insufficient permissions.'], 403);
    }

    $post_ids = isset($_POST['post_ids']) && is_array($_POST['post_ids']) ? array_map('intval', $_POST['post_ids']) : [];

    if (empty($post_ids)) {
        error_log('No post IDs provided for bulk delete');
        wp_send_json_error(['message' => 'No carts selected for deletion.'], 400);
    }

    $deleted_count = 0;
    foreach ($post_ids as $post_id) {
        if (get_post_type($post_id) !== 'capture_cart') {
            error_log('Invalid post type for ID: ' . $post_id);
            continue;
        }

        $result = wp_delete_post($post_id, true);
        if ($result) {
            $deleted_count++;
            error_log('Successfully deleted captured cart ID: ' . $post_id);
        } else {
            error_log('Failed to delete captured cart ID: ' . $post_id);
        }
    }

    if ($deleted_count > 0) {
        wp_send_json_success([
            'message' => sprintf(_n('%d cart deleted successfully.', '%d carts deleted successfully.', $deleted_count, 'woocommerce'), $deleted_count),
            'deleted_count' => $deleted_count,
        ]);
    } else {
        error_log('No carts were deleted in bulk delete');
        wp_send_json_error(['message' => 'Failed to delete selected carts.'], 500);
    }
}
add_action('wp_ajax_bulk_delete_captured_carts', 'bulk_delete_captured_carts');
// END AJAX HANDLER FOR BULK DELETE






// AJAX HANDLER FOR SENDING EMAIL
function send_customer_email() {
    try {
        //error_log('Starting send_customer_email for post_id: ' . (isset($_POST['post_id']) ? $_POST['post_id'] : 'not set'));

        // Verify nonce
        check_ajax_referer('captured_carts_nonce', 'nonce');

        // Check user permissions
        if (!current_user_can('edit_posts')) {
            //error_log('Insufficient permissions for user: ' . wp_get_current_user()->user_login);
            wp_send_json_error(['message' => 'Insufficient permissions.'], 403);
        }

        // Validate post ID
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        //error_log('Post ID: ' . $post_id);
        if ($post_id <= 0) {
            //error_log('Invalid post ID: ' . $post_id);
            wp_send_json_error(['message' => 'Invalid post ID.'], 400);
        }

        // Check post existence and type
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'capture_cart') {
            //error_log('Post does not exist or is not a capture_cart: ID ' . $post_id);
            wp_send_json_error(['message' => 'Invalid capture cart post.'], 400);
        }
        if ($post->post_status !== 'publish') {
            //error_log('Post is not published: ID ' . $post_id . ', Status: ' . $post->post_status);
            wp_send_json_error(['message' => 'Cannot send email for unpublished post.'], 400);
        }

        // Get customer email and cart group ID
        $email = get_post_meta($post_id, 'customer_email', true);
        $cart_group_id = get_post_meta($post_id, 'cart_group_id', true);
        //error_log('Email to send to: ' . ($email ?: 'none') . ', Cart Group ID: ' . ($cart_group_id ?: 'none'));
        if (empty($email) || !is_email($email)) {
            //error_log('Invalid or empty email for post ID: ' . $post_id);
            wp_send_json_error(['message' => 'No valid customer email address found.'], 400);
        }
        if (empty($cart_group_id)) {
            //error_log('Invalid or empty cart group ID for post ID: ' . $post_id);
            wp_send_json_error(['message' => 'No valid cart group ID found.'], 400);
        }

        // Retrieve customer first name from users table
        $user = get_user_by('email', $email);
        $customer_name = $user && !empty($user->first_name) ? $user->first_name : 'Customer';
        //error_log("Customer Name for Post ID $post_id: " . $customer_name);

        // Query all capture_cart posts with matching email and cart_group_id
        $args = [
            'post_type'      => 'capture_cart',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => 'customer_email',
                    'value'   => $email,
                    'compare' => '=',
                ],
                [
                    'key'     => 'cart_group_id',
                    'value'   => $cart_group_id,
                    'compare' => '=',
                ],
            ],
        ];
        $captured_carts = new WP_Query($args);

        if (!$captured_carts->have_posts()) {
            error_log('No captured carts found for email: ' . $email . ' and cart group ID: ' . $cart_group_id);
            wp_send_json_error(['message' => 'No captured carts found for this email and cart group.'], 400);
        }

        // Initialize allow_credit and delivery_option checks
        $user_id = $user ? $user->ID : 0;
        $allow_credit = $user_id ? get_field('credit_options_allow_user_credit_option', 'user_' . $user_id) : 'N/A';
        $has_delivery_option = false;

        // Collect cart data
        $cart_items = [];
        while ($captured_carts->have_posts()) {
            $captured_carts->the_post();
            $current_post_id = get_the_ID();
            $cart_item = get_post_meta($current_post_id, 'cart_item', true);
            $delivery_option = get_post_meta($current_post_id, 'delivery_option', true) ?: 'N/A';
            if ($delivery_option == 1) {
                $has_delivery_option = true;
            }

            $pdf_url = !empty($cart_item['Upload .PDF Drawing']) && filter_var($cart_item['Upload .PDF Drawing'], FILTER_VALIDATE_URL) ? $cart_item['Upload .PDF Drawing'] : null;
            $dxf_url = !empty($cart_item['Upload .DXF Drawing']) && filter_var($cart_item['Upload .DXF Drawing'], FILTER_VALIDATE_URL) ? $cart_item['Upload .DXF Drawing'] : null;
            $pdf_filename = $pdf_url !== 'N/A' ? basename($pdf_url) : 'N/A';
            $dxf_filename = $dxf_url !== 'N/A' ? basename($dxf_url) : 'N/A';

            $cart_items[] = [
                'product_name'  => get_post_meta($current_post_id, 'product_name', true) ?: 'N/A',
                'Upload .PDF Drawing' => $pdf_url,
                'Upload .PDF Drawing Filename' => $pdf_filename,
                'Upload .DXF Drawing' => $dxf_url,
                'Upload .DXF Drawing Filename' => $dxf_filename,
                'width'         => get_post_meta($current_post_id, 'width', true) ?: 'N/A',
                'length'        => get_post_meta($current_post_id, 'length', true) ?: 'N/A',
                'radius'        => get_post_meta($current_post_id, 'radius', true) ?: 'N/A',
                'Total number of parts' => !empty($cart_item['Total number of parts']) ? esc_html($cart_item['Total number of parts']) : '0',
                'Roll Length (Metres)' => !empty($cart_item['Roll Length (Metres)']) ? esc_html($cart_item['Roll Length (Metres)']) : '0',
                'total_price'   => get_post_meta($current_post_id, 'total_price', true) ?: '0',
                'part_shape'    => !empty($cart_item['Part shape']) ? esc_html($cart_item['Part shape']) : 'N/A',
                'notes'         => !empty($cart_item['Notes']) ? esc_html($cart_item['Notes']) : 'N/A',
                'cpp'           => !empty($cart_item['Customer Shipping Weights']) ? esc_html($cart_item['Customer Shipping Weights']) : 'N/A',
                'width_inch'    => !empty($cart_item['Width (Inch)']) ? esc_html($cart_item['Width (Inch)']) : 'N/A',
                'length_inch'   => !empty($cart_item['Length (Inch)']) ? esc_html($cart_item['Length (Inch)']) : 'N/A',
                'delivery_option' => $delivery_option,
                'thumb_image'   => !empty($cart_item['thumb_image']) ? esc_url($cart_item['thumb_image']) : 'https://placehold.co/150',
            ];
        }



        wp_reset_postdata();

        // Prepare email
        $subject = 'Your Quotation from Materials Direct';

        // Conditionally add custom message if allow_credit is not 1 and any cart has delivery_option = 1
        $custom_message = ($has_delivery_option && $allow_credit != 1)
            ? '<p style="color: #ff0000;">Our system has detected that you do not have a credit account. You will need to have a credit account to fulfil this order. You can click <a style="color:red;" href="' . esc_url(home_url('/credit-account-application/')) . '">here</a> to submit a Credit Account request online. Or alternatively call the Materials Direct accounts department on <a style="color:red;" href="mailto:+44(0)1908222211">+44 (0)1908 222211</a> for help with this.</p>'
            : '';

        //$expiration_hours = get_field('captured_carts_expiry', 'option');
        $default_expiration_hours = get_field('captured_carts_expiry', 'option') ?: 48;
        $delivery_option = get_post_meta($post_id, 'delivery_option', true) ?: 'N/A';
        $expiration_hours = $default_expiration_hours;
        if ($delivery_option == 1) {
            $custom_expiry_schedule = get_post_meta($post_id, 'custom_expiry_schedule', true);
            $expiration_hours = $custom_expiry_schedule && in_array($custom_expiry_schedule, ['2', '3', '4']) ? intval($custom_expiry_schedule) : 2;
            //error_log("Using custom_expiry_schedule for post ID $post_id: $expiration_hours hours");
        }

        // Build email content with all cart items
        $message = '
            <html>
            <body>
                <div class="container">
                    <div class="header">
                        <h2 style="display: block; font-family: \'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; font-size: 26px; color: #000000;">Materials Direct Quotation</h2>
                    </div>
                    <div class="content">
                        <p style="color: #000000;">Dear ' . esc_html($customer_name) . ',</p>
                        <p style="color: #000000;">Thank you for requesting a quotation from Materials Direct. To confirm, your order is in a group of carts with ID: ' . esc_html($cart_group_id) . '. You can place your order by clicking the link below.</p>
                        <p style="color: #000000;">Please ensure your cart is empty before clicking any of the links below.</p>
                        <p style="color: #000000;">Make sure to place your order as soon as possible. This cart item will expire in '.$expiration_hours.' hour(s).</p>
                        ' . $custom_message . '
                        <p style="color: #000000;">You will need to login to place your order.</p>
                        <table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 100%; font-family: Helvetica, Roboto, Arial, sans-serif;" width="100%">
                            <thead>
                                <tr>
                                    <th class="td" scope="col" style="padding: 10px; color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; text-align: left;" align="left">Product</th>
                                </tr>
                            </thead>
                            <tbody>';

        foreach ($cart_items as $index => $item) {

            
            $message .= '
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; text-align: left; vertical-align: middle; font-family: Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;" align="left">
                                        <table cellspacing="0" cellpadding="4" border="0" class="data-table">
               
                                             <p style="margin:0; padding:1px 4px;"><strong>' . esc_html($item['product_name']) . ' (Cart ' . ($index + 1) . ')</strong></p>';
                                            
                                            $message .= '
                                            <tr>
                                                <th style="text-align:left; width: 140px;">Part shape:</th>
                                                <td style="text-align:left;">' . esc_html($item['part_shape']) . '</td>
                                            </tr>
                                            ';

                                            // Conditionally include PDF row only if valid URL
                                            if (!empty($item['Upload .PDF Drawing']) && filter_var($item['Upload .PDF Drawing'], FILTER_VALIDATE_URL)) {
                                                $message .= '
                                                                            <tr>
                                                                                <th style="text-align:left; width: 140px;">Upload PDF Drawing:</th>
                                                                                <td style="text-align:left;"><a href="' . esc_url($item['Upload .PDF Drawing']) . '" target="_blank">' . esc_html($item['Upload .PDF Drawing Filename']) . '</a></td>
                                                                            </tr>';
                                            }

                                            // Conditionally include DXF row only if valid URL
                                            if (!empty($item['Upload .DXF Drawing']) && filter_var($item['Upload .DXF Drawing'], FILTER_VALIDATE_URL)) {
                                                $message .= '
                                                                            <tr>
                                                                                <th style="text-align:left; width: 140px;">Upload DXF Drawing:</th>
                                                                                <td style="text-align:left;"><a href="' . esc_url($item['Upload .DXF Drawing']) . '" target="_blank">' . esc_html($item['Upload .DXF Drawing Filename']) . '</a></td>
                                                                            </tr>';
                                            }
                                            
                                            if($item['radius'] != "N/A"){
                                                $message .= '
                                                <tr>
                                                    <th style="text-align:left; width: 140px;">Radius (MM):</th>
                                                    <td style="text-align:left;">' . esc_html($item['radius']) . '</td>
                                                </tr>';
                                            }


                                            $message .= '
                                            <tr>
                                                <th style="text-align:left; width: 140px;">Width (MM):</th>
                                                <td style="text-align:left;">' . esc_html($item['width']) . '</td>
                                            </tr>';

                                            if($item['Roll Length (Metres)'] == 0){
                                                $message .= '
                                                    <tr>
                                                        <th style="text-align:left; width: 140px;">Length (MM):</th>
                                                        <td style="text-align:left;">' . esc_html($item['length']) . '</td>
                                                    </tr>
                                                ';
                                            }


                                            if($item['width_inch'] != 'N/A'){
                                                $message .= '

                                                <tr>
                                                    <th style="text-align:left; width: 140px;">Width (INCH):</th>
                                                    <td style="text-align:left;">' . esc_html($item['width_inch']) . '</td>
                                                </tr>';
                                            
                                            }
                                            if($item['length_inch'] != 'N/A'){
                                                $message .= '

                                                <tr>
                                                    <th style="text-align:left; width: 140px;">Length (INCH):</th>
                                                    <td style="text-align:left;">' . esc_html($item['length_inch']) . '</td>
                                                </tr>';
                                            }


                                            if($item['Roll Length (Metres)'] != 0){
                                                $message .= '
                                                <tr>
                                                    <th style="text-align:left; width: 140px;">Roll Length (Metres):</th>
                                                    <td style="text-align:left;">' . esc_html($item['Roll Length (Metres)']) . '</td>
                                                </tr>
                                                ';
                                            }

                                            $message .= '
                                            <tr>
                                                <th style="text-align:left; width: 140px;">Total number of parts:</th>
                                                <td style="text-align:left;">' . esc_html($item['Total number of parts']) . '</td>
                                            </tr>
                                            ';

                                            $message .= '
                                            <tr>
                                                <th style="text-align:left; width: 140px;">Despatch Notes:</th>
                                                <td style="text-align:left;">' . esc_html($item['notes']) . '</td>
                                            </tr>
                                            <tr>
                                                <th style="text-align:left; width: 140px;">Customer Shipping Weight(s):</th>
                                                <td style="text-align:left;">' . esc_html($item['cpp']) . '</td>
                                            </tr>

                                            <tr>
                                                <th style="text-align:left; width: 140px;">Subtotal:</th>
                                                <td style="text-align:left;">' . wp_kses_post(wc_price($item['total_price'])) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>';
        }

        $message .= '
                            </tbody>
                        </table>
                        
                        <a style="background: #ef9003; padding: 10px 17px; color: #ffffff; text-decoration: none;" href="' . esc_url(home_url('/my-account/')) . '">Go To Your My Account Page And Place Your Order</a>
                    </div>
                </div>
            </body>
            </html>';

        $headers = ['Content-Type' => 'text/html; charset=UTF-8'];

        // Send email
        error_log('Attempting to send email to ' . $email . ' with ' . count($cart_items) . ' cart items');
        $sent = wp_mail($email, $subject, $message, $headers);
        if ($sent) {
            error_log('Email sent successfully to ' . $email . ' for post ID: ' . $post_id . ' with cart group ID: ' . $cart_group_id);
            wp_send_json_success(['message' => 'Email sent successfully to ' . esc_html($email) . ' with ' . count($cart_items) . ' cart items']);
        } else {
            error_log('Failed to send email to ' . $email . ' for post ID: ' . $post_id);
            wp_send_json_error(['message' => 'Failed to send email.'], 500);
        }
    } catch (Exception $e) {
        error_log('Error in send_customer_email: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        wp_send_json_error(['message' => 'Server error: ' . $e->getMessage()], 500);
    }
}
add_action('wp_ajax_send_customer_email', 'send_customer_email');
// END AJAX HANDLER FOR SENDING EMAIL








// ADD CAPTURE CART BUTTON TO CART
function add_capture_cart_button_to_cart() {


    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();

        $current_user_can = get_field('captured_carts_admin_email', 'option');

        if ($current_user->user_email === $current_user_can) { ?>
                    <form method="POST" action="" onsubmit="return confirm('Capture the current cart?');">
                        <?php wp_nonce_field('capture_cart_nonce', 'capture_cart_nonce_field'); ?>
                        <input type="hidden" name="capture_cart_action" value="1" />
                        <button type="submit" class="product-page__capture-cart-btn" style="border-radius: 0.25rem; margin-bottom:1rem;">
                            Capture Cart
                        </button>
                    </form>
        <?php }
    }
}
add_action('woocommerce_before_cart_totals', 'add_capture_cart_button_to_cart', 20);
// END ADD CAPTURE CART BUTTON TO CART


// HANDLE CART CAPTURE
function handle_capture_cart() {
    if (
        isset($_POST['capture_cart_action']) &&
        $_POST['capture_cart_action'] == '1' &&
        isset($_POST['capture_cart_nonce_field']) &&
        wp_verify_nonce($_POST['capture_cart_nonce_field'], 'capture_cart_nonce') &&
        function_exists('WC') &&
        WC()->cart &&
        !empty(WC()->cart->get_cart())
    ) {
        $cart_items = get_cart_capture_data();
        //error_log('The cart items are:' . print_r($cart_items));
        if (empty($cart_items)) {
            wc_add_notice(__('No cart data captured.', 'woocommerce'), 'error');
            wp_safe_redirect(wc_get_cart_url());
            exit;
        }

        $captured_count = 0;
        $cart_group_id = date('YmdHis');
        foreach ($cart_items as $item) {
            $post_id = wp_insert_post([
                'post_type'   => 'capture_cart',
                'post_status' => 'publish',
                'post_title'  => 'Captured Cart - ' . $item['product'] . ' - ' . date('Y-m-d H:i:s'),
                'post_content' => '',
            ]);

            if ($post_id && !is_wp_error($post_id)) {
                error_log('Saving cart_item for post ' . $post_id . ': ' . print_r($item, true));
                update_post_meta($post_id, 'product_name', $item['product']);
                update_post_meta($post_id, 'capture_date', current_time('mysql'));
                update_post_meta($post_id, 'total_parts', intval($item['Total Number of Parts']));
                update_post_meta($post_id, 'width', $item['Width']); // Store Width
                update_post_meta($post_id, 'length', $item['Length']); // Store Length
                update_post_meta($post_id, 'radius', $item['Radius']); // Store Radius
                update_post_meta($post_id, 'total_price', floatval($item['Total Price']));
                update_post_meta($post_id, 'cart_item', $item);
                update_post_meta($post_id, 'cart_group_id', $cart_group_id);


                // Store Field 123 (Delivery Option) from gravity_form_lead
                $delivery_option = isset($item['gravity_form_lead'][123]) ? sanitize_text_field($item['gravity_form_lead'][123]) : 'N/A';
                update_post_meta($post_id, 'delivery_option', $delivery_option);
                //error_log('Saved delivery_option for post ' . $post_id . ': ' . $delivery_option);

                // Store Custom Expiry Schedule if delivery_option == 1
                if ($delivery_option == 1) {
                    update_post_meta($post_id, 'custom_expiry_schedule', '2'); // Default to 2 hours
                    //error_log('Saved custom_expiry_schedule for post ' . $post_id . ': 2');
                }

                $captured_count++;
            } else {
                error_log('Failed to create capture_cart post for item: ' . print_r($item, true));
            }
        }

        if ($captured_count > 0) {
            wc_add_notice(sprintf(_n('%d item captured successfully!', '%d items captured successfully!', $captured_count, 'woocommerce'), $captured_count), 'success');
        } else {
            wc_add_notice(__('Failed to capture cart items.', 'woocommerce'), 'error');
        }

        wp_safe_redirect(wc_get_cart_url());
        exit;
    }
}

add_action('template_redirect', 'handle_capture_cart');
// END HANDLE CART CAPTURE


// HANDLE SAVE CUSTOMER EMAIL
function save_customer_email() {
    try {
        //error_log('Starting save_customer_email for post_id: ' . (isset($_POST['post_id']) ? $_POST['post_id'] : 'not set'));

        // Verify nonce
        check_ajax_referer('captured_carts_nonce', 'nonce');

        // Check user permissions
        if (!current_user_can('edit_posts')) {
            error_log('Insufficient permissions for user: ' . wp_get_current_user()->user_login);
            wp_send_json_error(['message' => 'Insufficient permissions.'], 403);
        }

        // Validate post ID
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        //error_log('Post ID: ' . $post_id);
        if ($post_id <= 0) {
            error_log('Invalid post ID: ' . $post_id);
            wp_send_json_error(['message' => 'Invalid post ID.'], 400);
        }

        // Check post existence and status
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'capture_cart') {
            error_log('Post does not exist or is not a capture_cart: ID ' . $post_id);
            wp_send_json_error(['message' => 'Invalid capture cart post.'], 400);
        }
        if ($post->post_status !== 'publish') {
            error_log('Post is not published: ID ' . $post_id . ', Status: ' . $post->post_status);
            wp_send_json_error(['message' => 'Cannot save email for unpublished post.'], 400);
        }

        // Validate and sanitize email
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        error_log('Email to save: ' . $email);
        if (empty($email) || !is_email($email)) {
            error_log('Invalid or empty email provided for post ID: ' . $post_id);
            wp_send_json_error(['message' => 'Please provide a valid email address.'], 400);
        }

        // Get cart group ID
        $cart_group_id = get_post_meta($post_id, 'cart_group_id', true);
        error_log('Cart Group ID for post ID ' . $post_id . ': ' . ($cart_group_id ?: 'none'));
        if (empty($cart_group_id)) {
            error_log('Invalid or empty cart group ID for post ID: ' . $post_id);
            wp_send_json_error(['message' => 'No valid cart group ID found.'], 400);
        }

        // Get User ID from email
        $user = get_user_by('email', $email);
        $user_id = $user ? $user->ID : 0;
        //error_log('User ID for email ' . $email . ': ' . $user_id);

        // Get ACF field value for allow_credit
        $allow_credit = $user_id ? get_field('credit_options_allow_user_credit_option', 'user_' . $user_id) : 'N/A';
        //error_log('Allow Credit for user ID ' . $user_id . ': ' . ($allow_credit !== null ? var_export($allow_credit, true) : 'N/A'));

        // Query all capture_cart posts with the same cart_group_id
        $args = [
            'post_type'      => 'capture_cart',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => 'cart_group_id',
                    'value'   => $cart_group_id,
                    'compare' => '=',
                ],
            ],
        ];
        $captured_carts = new WP_Query($args);

        if (!$captured_carts->have_posts()) {
            error_log('No captured carts found for cart group ID: ' . $cart_group_id);
            wp_send_json_error(['message' => 'No captured carts found for this cart group.'], 400);
        }

        // Update customer email and allow_credit for all matching carts
        $updated_posts = [];
        while ($captured_carts->have_posts()) {
            $captured_carts->the_post();
            $current_post_id = get_the_ID();
            //error_log('Attempting to save customer_email meta for post ID: ' . $current_post_id);

            // Save customer email
            $result_email = update_post_meta($current_post_id, 'customer_email', $email);
            if ($result_email === false) {
                global $wpdb;
                $last_error = $wpdb->last_error;
                //error_log('Failed to save customer email for post ID: ' . $current_post_id . '. WPDB Error: ' . ($last_error ?: 'None'));
            } else {
                $updated_posts[] = $current_post_id;
            }

            // Save allow_credit
            $result_credit = update_post_meta($current_post_id, 'allow_credit', $allow_credit !== null ? $allow_credit : 'N/A');
            if ($result_credit === false) {
                global $wpdb;
                $last_error = $wpdb->last_error;
                //error_log('Failed to save allow_credit for post ID: ' . $current_post_id . '. WPDB Error: ' . ($last_error ?: 'None'));
            }
        }
        wp_reset_postdata();

        if (empty($updated_posts)) {
            error_log('No posts were updated for cart group ID: ' . $cart_group_id);
            wp_send_json_error(['message' => 'Failed to save email address for any carts.'], 500);
        }

        //error_log('Successfully saved customer email for ' . count($updated_posts) . ' posts in cart group ID: ' . $cart_group_id . ': ' . $email);
        wp_send_json_success([
            'message' => 'Email address saved successfully for ' . count($updated_posts) . ' cart(s) in group ' . esc_html($cart_group_id) . '.',
            'email' => $email,
            'updated_post_ids' => $updated_posts,
        ]);
    } catch (Exception $e) {
        error_log('Error in save_customer_email: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        wp_send_json_error(['message' => 'Server error: ' . $e->getMessage()], 500);
    }
}
add_action('wp_ajax_save_customer_email', 'save_customer_email');
// END HANDLE SAVE CUSTOMER EMAIL





// ADD CUSTOM MESSAGE TO THE MY-ACCOUNTS PAGE

function display_custom_message_on_my_account() {
    if (!is_user_logged_in() || !function_exists('WC')) {
        return;
    }

    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email;

    // Check the ACF field for the logged-in user
    $user_id = $current_user->ID;
    $allow_credit = $user_id ? get_field('credit_options_allow_user_credit_option', 'user_' . $user_id) : 'N/A';
    error_log('Allow Credit for user ID ' . $user_id . ' (' . $user_email . '): ' . ($allow_credit !== null ? var_export($allow_credit, true) : 'N/A'));

    // Query all capture_cart posts with matching customer_email
    $args = [
        'post_type'      => 'capture_cart',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => 'customer_email',
                'value'   => $user_email,
                'compare' => '=',
            ],
        ],
    ];

    $captured_carts = new WP_Query($args);

    // Check if the user has ever had a captured cart (optional tracking)
    $has_had_carts = get_user_meta($user_id, 'has_had_captured_carts', true);

    // If no carts are found and the user previously had carts, assume expiration
    if (!$captured_carts->have_posts() && $has_had_carts) {
        echo '<div class="woocommerce-message woocommerce-error" style="margin-bottom: 20px;">';
        echo esc_html__('Your cart has expired. Please contact Jordan King on: ', 'woocommerce');
        echo '<a href="tel:+447415778341">+44 (0)7415 778 341</a> or ';
        echo '<a href="mailto:jordan@materials-direct.com">jordan@materials-direct.com</a>';
        echo '</div>';
        return; // Exit early to avoid displaying other cart-related content
    }

    // If carts exist, update user meta to indicate they have had carts
    if ($captured_carts->have_posts() && !$has_had_carts) {
        update_user_meta($user_id, 'has_had_captured_carts', true);
        //error_log('Marked user ID ' . $user_id . ' as having captured carts.');
    }

    // Check for delivery_option in captured carts
    $has_delivery_option = false;
    if ($captured_carts->have_posts()) {
        while ($captured_carts->have_posts()) {
            $captured_carts->the_post();
            $post_id = get_the_ID();
            $delivery_option = get_post_meta($post_id, 'delivery_option', true) ?: 'N/A';
            if ($delivery_option == 1) {
                $has_delivery_option = true;
            }
        }
        wp_reset_postdata();
    }

    // Display captured carts
    if ($captured_carts->have_posts()) {
        echo '<p>' . esc_html__('You have saved carts ready to order:', 'woocommerce') . '</p>';


        if($allow_credit != 1 && $has_delivery_option == 1){
            echo '<div style="pointer-events:none; user-select:none; opacity:0.5;" class="my-accounts-capture-cart">';
            echo '<div style="background: red; color: white; padding: 10px; text-align: center; font-weight: bold; margin-bottom: 15px;">You need a credit account to place this order.<br> Call Materials Direct on +44 (0)1908 222211</div>';
        } elseif($allow_credit != 1 && $has_delivery_option == 'null'){
            echo '<div style="pointer-events:none; user-select:none; opacity:0.5;" class="my-accounts-capture-cart">';
            echo '<div style="background: red; color: white; padding: 10px; text-align: center; font-weight: bold; margin-bottom: 15px;">You need a credit account to place this order.<br> Call Materials Direct on +44 (0)1908 222211</div>';
        }
        else {
            echo '<div class="my-accounts-capture-cart">';
        }

        $expiration_value = get_field('captured_carts_expiry', 'option');
        if($expiration_value == 1){
            $expiration_display = "hour";
        } else {
            $expiration_display = "hours";
        }

        echo '<h6><strong>You recently contacted Materials Direct and requested a quotation</strong></h6>';
        echo '<p>To confirm, your order is now set up on our system and you can place your order by clicking the link(s) below. Make sure your cart is empty before clicking any of the link(s) below. Please place your order as soon as possible. These link(s) will expire in '.$expiration_value.' '.$expiration_display.': </p>';

        

        // Loop through all captured carts
        $cart_data = [];
        while ($captured_carts->have_posts()) {
            $captured_carts->the_post();
            $post_id = get_the_ID();

            $cart_item = get_post_meta($post_id, 'cart_item', true);
            // Log cart_item for debugging
            //error_log('Cart item for post ID ' . $post_id . ': ' . print_r($cart_item, true));

            // Determine thumb_image with fallback to product image
            $thumb_image = !empty($cart_item['thumb_image']) ? esc_url($cart_item['thumb_image']) : null;
            if (!$thumb_image && !empty($cart_item['product_id'])) {
                $product = wc_get_product($cart_item['product_id']);
                $thumb_image = $product ? wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') : null;
            }
            $thumb_image = $thumb_image ?: 'https://placehold.co/150';
            //error_log('Thumb image for post ID ' . $post_id . ': ' . $thumb_image);

            $cart_data[$post_id] = [
                'product_name'  => get_post_meta($post_id, 'product_name', true) ?: 'N/A',
                'capture_date'  => get_post_meta($post_id, 'capture_date', true) ?: 'N/A',
                'cart_group_id' => get_post_meta($post_id, 'cart_group_id', true) ?: 'N/A',
                'total_price'   => get_post_meta($post_id, 'total_price', true) ?: 'N/A',
                'restored'      => get_post_meta($post_id, 'restored_to_cart', true),
                'thumb_image'   => $thumb_image,
            ];
        }
        wp_reset_postdata();

        foreach ($cart_data as $post_id => $cart) {
            $product_name = $cart['product_name'];
            $capture_date = $cart['capture_date'];
            $cart_group_id = $cart['cart_group_id'];
            $total_price = $cart['total_price'];
            $restored = $cart['restored'];
            $thumb_image = $cart['thumb_image'];
            $button_text = $restored === 'yes' ? 'Product Added To Cart!' : 'Click Here To Place Your Order';
            $button_disabled = $restored === 'yes' ? ' disabled="disabled"' : '';
            $nonce = wp_create_nonce('restore_cart_' . $post_id);

            if ($restored != 'yes') {
                echo '<div class="my-accounts-capture-cart__item">';
            } else {
                echo '<div class="my-accounts-capture-cart__item" style="opacity:0.6;" disabled>';
            }
            echo '<div style="display: flex;">';
            echo '<div style="width: 20%; margin-right: 5%;">';
            echo '<img src="' . esc_url($thumb_image) . '" alt="' . esc_attr($product_name) . '" style="width: 100%; height: 119px; margin-right: 10px; object-fit: cover;" />';
            echo '</div>';

            echo '<div style="width: 75%; font-size: 0.8rem; line-height: 1.15rem;">';
            echo '<p style="display: block; width: 100%; border-bottom: 0px dotted black; padding-bottom: 0.3rem; margin-bottom: 0.1rem; display: flex; justify-content: space-between; flex-wrap:wrap;">';
            echo '<strong style="margin-right: 1rem; width: 100%;">' . esc_html($product_name) . '</strong>';
            echo '<strong>Subtotal: £' . esc_html($total_price) . '</strong>';
            echo '<ul>';
            echo '<li style="margin: 0;">' . esc_html__('Captured on', 'woocommerce') . ' ' . esc_html($capture_date) . '</li>';
            echo '<li style="margin: 0;">Cart Group ID: ' . esc_html($cart_group_id) . '</li>';
            echo '</ul>';
            if ($restored != 'yes') {
                echo '<a href="#" class="capture-cart-restore" data-post-id="' . esc_attr($post_id) . '" data-nonce="' . esc_attr($nonce) . '" ' . $button_disabled . '>' . esc_html($button_text) . '</a>';
            } else {
                echo '<a class="capture-cart-restore-disabled">' . esc_html($button_text) . '</a>';
            }
            echo '</p>';
            echo '</div>';
            echo '</div>';

            echo '</div>';
        }

        echo '</div>';

        // Enqueue JavaScript for AJAX restoration
        wp_enqueue_script('captured-carts-frontend', get_theme_file_uri('js/captured-carts-frontend.js'), ['jquery'], '1.0.3', true);
        wp_localize_script('captured-carts-frontend', 'capturedCartsAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('captured_carts_nonce'),
        ]);
    }
}
add_action('woocommerce_account_dashboard', 'display_custom_message_on_my_account', 10);


// END ADD CUSTOM MESSAGE TO THE MY-ACCOUNTS PAGE



// ADD FORM ID TO THE ORDER OBJECT

add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    // Check if cart_metadata exists and contains form_id
    if (isset($values['cart_metadata']) && is_array($values['cart_metadata']) && isset($values['cart_metadata']['form_id']) && $values['cart_metadata']['form_id'] !== 'N/A' && !empty($values['cart_metadata']['form_id'])) {
        // Save only the form_id to the order item meta
        $item->add_meta_data('form_id', sanitize_text_field($values['cart_metadata']['form_id']));
        error_log('Saved form_id ' . $values['cart_metadata']['form_id'] . ' to order item ID ' . $item->get_id());
    } else {
        error_log('No valid form_id found for cart item key ' . $cart_item_key . ': ' . print_r($values['cart_metadata'] ?? [], true));
    }
}, 10, 4);

// ADD FORM ID TO THE ORDER OBJECT


// HIDE META OBJECTS ON THE THANKYOU PAGE
/**
 * Save form_id to order item meta during checkout
 */
add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    // Check if cart_metadata exists and contains form_id
    if (isset($values['cart_metadata']) && is_array($values['cart_metadata']) && isset($values['cart_metadata']['form_id']) && $values['cart_metadata']['form_id'] !== 'N/A' && !empty($values['cart_metadata']['form_id'])) {
        // Save only the form_id to the order item meta
        $item->add_meta_data('form_id', sanitize_text_field($values['cart_metadata']['form_id']));
        error_log('Saved form_id ' . $values['cart_metadata']['form_id'] . ' to order item ID ' . $item->get_id());
    } else {
        error_log('No valid form_id found for cart item key ' . $cart_item_key . ': ' . print_r($values['cart_metadata'] ?? [], true));
    }
}, 10, 4);

/**
 * Inject jQuery and CSS to hide elements on Thank You page when form_id is 19
 */

add_action('woocommerce_thankyou', function ($order_id) {
    $order = wc_get_order($order_id);
    $has_form_id_19 = false;

    // Check if any order item has form_id equal to 19
    foreach ($order->get_items() as $item_id => $item) {
        $form_id = $item->get_meta('form_id', true);
        error_log('Checking form_id for order item ID ' . $item_id . ': ' . ($form_id ?: 'not set'));
        if ($form_id === '19') {
            $has_form_id_19 = true;
            error_log('Found form_id 19 for order item ID ' . $item_id);
            break;
        }
    }

    // If form_id 19 is found, inject CSS and jQuery
    if ($has_form_id_19) {

        // Inject jQuery into footer to hide Width (MM), Length (MM), and form_id
        add_action('wp_footer', function () {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    // Ensure jQuery is loaded
                    if (typeof $ === 'undefined') {
                        console.error('jQuery is not loaded on Thank You page');
                        return;
                    }
                    // Target .wc-item-meta list and hide specific <li> elements
                    // $('.wc-item-meta li').each(function() {
                    //     var label = $(this).find('.wc-item-meta-label').text().trim();
                    //     if (label === 'Width (MM):' || label === 'Length (MM):' || label === 'form_id:' || label === 'Part shape:') {
                    //         $(this).hide();
                    //         console.log('Hide element with label: ' + label);
                    //     }
                    // });
                    $('.ss-despatch-notes').addClass('ss-despatch-notes-hide');
                    console.log('Added ss-despatch-notes-hide class to .ss-despatch-notes and .hide-despatch-notes');
                });
            </script>
            <?php
            error_log('Injected jQuery to hide Width (MM), Length (MM), and form_id on Thank You page');
        });
    } else {
        error_log('No form_id 19 found for order ID ' . $order_id . ', skipping CSS and jQuery injection');
    }
}, 10, 1);

// HIDE META OBJECTS ON THE THANKYOU PAGE


// AJAX HANDLER FOR SAVING CUSTOM EXPIRY SCHEDULE
function save_custom_expiry_schedule() {
    try {
        error_log('Starting save_custom_expiry_schedule for post_id: ' . (isset($_POST['post_id']) ? $_POST['post_id'] : 'not set'));

        // Verify nonce
        check_ajax_referer('captured_carts_nonce', 'nonce');

        // Check user permissions
        if (!current_user_can('edit_posts')) {
            error_log('Insufficient permissions for user: ' . wp_get_current_user()->user_login);
            wp_send_json_error(['message' => 'Insufficient permissions.'], 403);
        }

        // Validate post ID
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        if ($post_id <= 0) {
            error_log('Invalid post ID: ' . $post_id);
            wp_send_json_error(['message' => 'Invalid post ID.'], 400);
        }

        // Check post existence and type
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'capture_cart') {
            error_log('Post does not exist or is not a capture_cart: ID ' . $post_id);
            wp_send_json_error(['message' => 'Invalid capture cart post.'], 400);
        }

        // Validate delivery option
        $delivery_option = get_post_meta($post_id, 'delivery_option', true) ?: 'N/A';
        if ($delivery_option != 1) {
            error_log('Cannot set custom expiry schedule for post ID ' . $post_id . ': delivery_option is not 1');
            wp_send_json_error(['message' => 'Custom expiry schedule is only available for delivery option orders.'], 400);
        }

        // Validate and sanitize expiry value
        $expiry_hours = isset($_POST['expiry_hours']) ? sanitize_text_field($_POST['expiry_hours']) : '';
        if (!in_array($expiry_hours, ['2', '3', '4'])) {
            error_log('Invalid expiry hours value for post ID ' . $post_id . ': ' . $expiry_hours);
            wp_send_json_error(['message' => 'Invalid expiry hours value.'], 400);
        }

        // Save custom expiry schedule
        $result = update_post_meta($post_id, 'custom_expiry_schedule', $expiry_hours);
        if ($result === false) {
            global $wpdb;
            $last_error = $wpdb->last_error;
            error_log('Failed to save custom_expiry_schedule for post ID ' . $post_id . ': ' . $last_error);
            wp_send_json_error(['message' => 'Failed to save expiry schedule.'], 500);
        }

        error_log('Successfully saved custom_expiry_schedule for post ID ' . $post_id . ': ' . $expiry_hours);
        wp_send_json_success([
            'message' => 'Expiry schedule updated to ' . $expiry_hours . ' hours.',
            'post_id' => $post_id,
            'expiry_hours' => $expiry_hours,
        ]);
    } catch (Exception $e) {
        error_log('Error in save_custom_expiry_schedule: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        wp_send_json_error(['message' => 'Server error: ' . $e->getMessage()], 500);
    }
}
add_action('wp_ajax_save_custom_expiry_schedule', 'save_custom_expiry_schedule');

// AJAX HANDLER FOR SAVING CUSTOM EXPIRY SCHEDULE





/* DELETE CAPTURED CARTS AFTER 48 HOURS */


// Schedule WP-Cron event for deleting expired capture_cart posts
function schedule_capture_cart_cleanup() {
    if (!wp_next_scheduled('cleanup_expired_capture_carts')) {
        wp_schedule_event(time(), 'hourly', 'cleanup_expired_capture_carts');
        error_log('Scheduled cleanup_expired_capture_carts event.');
    }
}
add_action('wp', 'schedule_capture_cart_cleanup');

// Define the cleanup function for expired capture_cart posts
function cleanup_expired_capture_carts() {
    error_log('Running cleanup_expired_capture_carts cron job.');

    // Query all capture_cart posts
    $args = [
        'post_type'      => 'capture_cart',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids', // Only fetch post IDs for performance
    ];

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        error_log('No capture_cart posts found for cleanup.');
        return;
    }
    
    $current_time = current_time('timestamp');
    $default_expiration_hours = get_field('captured_carts_expiry', 'option') ?: 48;
    $deleted_count = 0;

    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $capture_date = get_post_meta($post_id, 'capture_date', true);

        if (empty($capture_date)) {
            error_log("No capture_date found for capture_cart post ID: $post_id");
            continue;
        }

        // Convert capture_date to timestamp
        $capture_timestamp = strtotime($capture_date);
        if (false === $capture_timestamp) {
            error_log("Invalid capture_date for post ID: $post_id - $capture_date");
            continue;
        }

        // Determine expiration hours
        $delivery_option = get_post_meta($post_id, 'delivery_option', true) ?: 'N/A';
        $expiration_hours = $default_expiration_hours;
        if ($delivery_option == 1) {
            $custom_expiry_schedule = get_post_meta($post_id, 'custom_expiry_schedule', true);
            $expiration_hours = $custom_expiry_schedule && in_array($custom_expiry_schedule, ['2', '3', '4']) ? intval($custom_expiry_schedule) : 2;
            error_log("Using custom_expiry_schedule for post ID $post_id: $expiration_hours hours");
        } else {
            error_log("Using default expiration for post ID $post_id: $expiration_hours hours");
        }



        // Calculate time difference in hours
        $time_diff_hours = ($current_time - $capture_timestamp) / 3600;

        if ($time_diff_hours >= $expiration_hours) {
            // Delete the post
            $result = wp_delete_post($post_id, true); // Force delete (bypass trash)
            if ($result) {
                $deleted_count++;
                error_log("Deleted expired capture_cart post ID: $post_id (Captured on: $capture_date)");
            } else {
                error_log("Failed to delete capture_cart post ID: $post_id");
            }
        }
    }

    wp_reset_postdata();

    if ($deleted_count > 0) {
        error_log("Successfully deleted $deleted_count expired capture_cart posts.");
    } else {
        error_log('No expired capture_cart posts found to delete.');
    }
}
add_action('cleanup_expired_capture_carts', 'cleanup_expired_capture_carts');

// Clear scheduled event on plugin/theme deactivation (optional)
function deactivate_capture_cart_cleanup() {
    $timestamp = wp_next_scheduled('cleanup_expired_capture_carts');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'cleanup_expired_capture_carts');
        error_log('Cleared cleanup_expired_capture_carts cron event on deactivation.');
    }
}
register_deactivation_hook(__FILE__, 'deactivate_capture_cart_cleanup');

/* DELETE CAPTURED CARTS AFTER 48 HOURS */


/* Add javascript/jquery fix to convert full url to file name */
add_action('wp_footer', 'custom_thankyou_page_script');
function custom_thankyou_page_script() {
    if (is_wc_endpoint_url('order-received')) : ?>
        <script>
        jQuery(document).ready(function($) {
          function updateFileLinkText(label) {
            $('li:contains("' + label + '") a').each(function() {
              var href = $(this).attr('href');
              if (href && href.startsWith('http')) {
                var fileName = href.split('/').pop();
                $(this).text(fileName);
              }
            });
          }

          updateFileLinkText('Upload .PDF Drawing:');
          updateFileLinkText('Upload .DXF Drawing:');
        });
        </script>
    <?php endif;
}
/* Add javascript/jquery fix to convert full url to file name */

