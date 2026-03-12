<?php
// Remove original stock quantity element

function remove_in_stock_text_form_single_products( $html, $text, $product ) {
    if ( is_product() ) {
        return '';
    }
    return $html;
}
    
add_filter( 'woocommerce_stock_html', 'remove_in_stock_text_form_single_products', 10, 3 );

// Remove original stock quantity element


// display stock container on product page
add_action( 'woocommerce_before_add_to_cart_form', 'new_display_stock', 20, 1 );

function new_display_stock() {
	?>
	<div class="live-stock-wrapper" data-product-id="<?php echo get_the_ID(); ?>">
		<span style="color: white" class="stock stock-loading">Checking availability...</span>
	</div>
	<?php
}
// display stock container on product page


// ajax php handler to retrieve stock quantity
add_action('wp_ajax_get_live_stock', 'get_live_stock');
add_action('wp_ajax_nopriv_get_live_stock', 'get_live_stock');

function get_live_stock() {

    if ( empty($_POST['product_id']) ) {
        wp_die();
    }

    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);

    if ( ! $product ) {
        wp_die();
    }

    $stock_quantity = $product->get_stock_quantity();

    // If stock is managed and greater than 0
    if ( $product->managing_stock() && $stock_quantity > 0 ) {

        echo '<span class="stock in-stock">' . esc_html($stock_quantity) . ' Sheets In Stock</span>';

    } else {

        echo '<span class="stock out-of-stock">Available with lead time | <a id="leadtime" class="backorder-leadtime" data-tooltip="Contact us for information on lead times" href="/contact/">Contact Us</a></span>';

    }

    wp_die();
}
// ajax php handler to retrieve stock quantity

// Lets enqueue the javascript only on the product page
add_action('wp_enqueue_scripts', 'enqueue_live_stock_script');

function enqueue_live_stock_script() {

    if ( is_product() ) {

        wp_enqueue_script(
            'live-stock',
            get_stylesheet_directory_uri() . '/js/live-stock.js',
            array('jquery'),
            '1.0',
            true
        );

        wp_localize_script('live-stock', 'live_stock_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }
}
// Lets enqueue the javascript only on the product page