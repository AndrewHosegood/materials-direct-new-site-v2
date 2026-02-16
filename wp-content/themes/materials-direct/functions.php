<?php
/**
 * Materials Direct functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Materials_Direct
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function materials_direct_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Materials Direct, use a find and replace
		* to change 'materials-direct' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'materials-direct', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'materials-direct' ),
			'action-bar-top-right' => esc_html__( 'action-bar-top-right', 'materials-direct' ),
			'manufacturing-services' => esc_html__( 'manufacturing-services', 'materials-direct' ),
			'service-sectors' => esc_html__( 'service-sectors', 'materials-direct' ),
			'useful-links' => esc_html__( 'useful-links', 'materials-direct' ),
			'page-navigation' => esc_html__( 'page-navigation', 'materials-direct' ),
			'page-not-found' => esc_html__( 'page-not-found', 'materials-direct' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'materials_direct_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	/**
	 * ADD THIS: Declare WooCommerce support
	 * This enables proper template handling, product gallery features (zoom, lightbox, slider),
	 * and ensures category/shop archives use the correct templates.
	 */
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 600,
		'single_image_width'    => 800,
		'product_grid'          => array(
			'default_columns' => 3,  // Matches your custom 3-column change
			'default_rows'    => 4,
			'min_columns'     => 3,
			'max_columns'     => 3,
		),
	) );


}
add_action( 'after_setup_theme', 'materials_direct_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function materials_direct_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'materials_direct_content_width', 640 );
}
add_action( 'after_setup_theme', 'materials_direct_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function materials_direct_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'materials-direct' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'materials-direct' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'materials_direct_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function materials_direct_scripts() {

	wp_enqueue_style( 'materials-direct-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'materials-direct-style', 'rtl', 'replace' );

	//wp_enqueue_script( 'materials-direct-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'materials-direct-scripts', get_template_directory_uri() . '/js/scripts.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'owl-scripts', get_template_directory_uri() . '/js/owl.carousel.min.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css', array(), '6.6.0' );
	wp_enqueue_style( 'owl-carousel-1', get_template_directory_uri() .'/css/owl.carousel.min.css', array(), _S_VERSION );
	wp_enqueue_style( 'owl-carousel-2', get_template_directory_uri() .'/css/owl.theme.default.min.css', array(), _S_VERSION );

	// Enqueue main.css after WooCommerce styles
	wp_enqueue_style(
		'materials-direct-main',
		get_template_directory_uri() . '/css/main.css',
		array( 'woocommerce-general' ), 
		filemtime( get_template_directory() . '/css/main.css' ), 
		'all' 
	);


}
add_action( 'wp_enqueue_scripts', 'materials_direct_scripts', 20 );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


add_action( 'wp_enqueue_scripts', function() {
    if ( function_exists( 'is_woocommerce' ) ) {
        wp_enqueue_script( 'wc-cart-fragments' );
    }
}, 20 );





/* BEGIN CUSTOM FUNCTIONS */

// FORCE CORRECT FROM ADDRESS FOR CUSTOM EMAILS 
require_once('includes/force-correct-from-address.php');
// FORCE CORRECT FROM ADDRESS FOR CUSTOM EMAILS 

// Generate and display PPP for testing
require_once('includes/acf_global_options.php');
// Generate and display PPP for testing

// **** THEME ALGORITHM AND CORE FUNCTIONALITY ****
require_once('includes/algorithm_and_core_functionality.php');
//require_once('includes/fix-for-woocommerce-rounding-errors.php');
// **** THEME ALGORITHM AND CORE FUNCTIONALITY ****

// Lets make sure that a product cannot be added to cart if width length and qty is empty
require_once('includes/custom-price-add-to-cart-validation.php');
// Lets make sure that a product cannot be added to cart if width length and qty is empty

// CODE FOR BYPASSING WORDPRESS BLOCK ON DXF/SVG FILES
require_once('includes/allow-svg-upload.php');
require_once('includes/allow-dxf-upload.php');
// CODE FOR BYPASSING WORDPRESS BLOCK ON DXF/SVG FILES

// Reposition product title on product page
require_once('includes/reposition-product-title-on-product-page.php');
// Reposition product title on product page

// Remove breadcrumbs from product page
require_once('includes/remove-breadcrumbs-from-product-page.php');
// Remove breadcrumbs from product page

// remove different shipping address option on checkout page
require_once('includes/checkout-remove-different-shipping-address.php');
// remove different shipping address option on checkout page

// remove different shipping address option on checkout page
require_once('includes/add-discount-chart-to-product-page.php');
// remove different shipping address option on checkout page

// Inject custom css in header for is_single_product
require_once('includes/inject-custom-css-in-head-for-issingleproduct.php');
// Inject custom css in header for is_single_product

// Add dab terms to checkout 
require_once('includes/add-dap-terms-to-checkout.php');
// Add dab terms to checkout

// Prevent duplicate orders merging in the cart
require_once('includes/prevent-duplicate-order-merge.php');
// Prevent duplicate orders merging in the cart

// Related products styling
require_once('includes/related-products-styling.php');
// Related products styling

// Custom product page tabs
require_once('includes/custom-product-tabs.php');
// Custom product page tabs

// Display stock availability on product page based on stock status
require_once('includes/display-stock-availability.php');
// Display stock availability on product page based on stock status

// display and position 1,2,3 banners on product page
//require_once('includes/display-123-banners-on-product-page.php');
// display and position 1,2,3 banners on product page

// Display backorder message on product page
//require_once('includes/display-backorder-message.php');
// Display backorder message on product page

// conditionally show/hide product price based on single product
require_once('includes/conditionally-hide-price.php');
// conditionally show/hide product price based on single product

// Rename listed items on thankyou page
require_once('includes/rename-listed-items-on-thankyou-page.php');
// Rename listed items on thankyou page

// Cart fragments functionality for cart quantity icon in header
require_once('includes/cart-fragments-functionality-for-cart-icon.php');
// Cart fragments functionality for cart quantity icon in header

// Show stock sheet size on product page
require_once('includes/show-stock-sheet-size-on-product-page.php');
// Show stock sheet size on product page

// change the shop grid to 3 columns
require_once('includes/change-shop-grid-to-3-columns.php');
// change the shop grid to 3 columns

// validation for product width and length - (to be deleted)
require_once('includes/validation-for-product-width-and-length.php');
// validation for product width and length - (to be deleted)

// Fix for VAT exclusion on checkout page if country is not GB
require_once('includes/ensure-vat-is-not-included-on-checkout-page.php');
// Fix for VAT exclusion on checkout page if country is not GB

// Add additional custom PO and VAT input fields to checkout form
require_once('includes/add-po-and-vat-fields-to-checkout-form.php');
// Add additional custom PO and VAT input fields to checkout form

// conditionally display delivery options modal and button on product page
require_once('includes/delivery-options-modal-and-button.php');
// conditionally display delivery options modal and button on product page

// add creditor user role
require_once('includes/add-creditor-user-role.php');
// add creditor user role

// hide the 'despatch within' select menu on the product page
require_once('includes/hide-despatch-within-if-credit-account.php');
// hide the 'despatch within' select menu on the product page

/* display scheduled shipments discount rates table on product page */
require_once('includes/display-shipment-discount-rates-on-product-page.php');
/* display scheduled shipments discount rates table on product page */

/* display PDF and DXF information correctly on cart and checkout page */
require_once('includes/display-pdf-dxf-on-cart-checkout-page.php');
/* display PDF and DXF information correctly on cart and checkout page */

/* Style up product name on checkout page */
require_once('includes/style-up-product-name-on-checkout-page.php');
/* Style up product name on checkout page */

/* remove product title from shop/category page */
require_once('includes/remove-product-title-from-shop-page.php');
/* remove product title from shop/category page */

/* Add mask div wrapper to shop/category page */
require_once('includes/add-mask-div-wrapper-to-shop-page.php');
/* Add mask div wrapper to shop/category page */

// Shop page styling
require_once('includes/shop-page-styling.php');
// Shop page styling

/* shop/category page additional content */
require_once('includes/shop-page-additional-content.php');
/* shop/category page additional content */

/* Sheet size validation and stock sheet logic */
require_once('includes/sheets-size-validation-and-stock-sheet-logic.php');
/* Sheet size validation and stock sheet logic */

/* enqueue javascript for home onscroll counter  */
require_once('includes/home-onscroll-counter.php');
/* enqueue javascript for home onscroll counter  */

/* Replace product page woocommerce gallery image with static image  */
require_once('includes/custom-product-page-featured-image.php');
/* Replace product page woocommerce gallery image with static image  */

/* Remove h1 heading from the my-accounts page */
require_once('includes/remove-heading-from-my-accounts-page.php');
/* Remove h1 heading from the my-accounts page */

/* Remove the shop/category sorting select menu */
require_once('includes/remove-category-shop-sorting-menu.php');
/* Remove the shop/category sorting select menu */

/* Display custom banner on my-accounts page */
require_once('includes/my-accounts-page-custom-banner.php');
/* Display custom banner on my-accounts page */

/* Add logic for calculating COFC fees on cart page */
require_once('includes/cofc-logic-for-cart-page.php');
/* Add logic for calculating COFC fees on cart page */

/* Add view products link and need help link to product page */
require_once('includes/add-view-product-details-link-and-need-help-link.php');
/* Add view products link and need help link to product page */

/* Add download datasheet button on product page */
require_once('includes/add-download-datasheet-product-page.php');
/* Add download datasheet button on product page */

/* add custom id to woocoomerce default tabs on product page */
require_once('includes/add-custom-id-to-tabs-on-product-page.php');
/* add custom id to woocoomerce default tabs on product page */

/* Remove paynow buttons on thankyou page */
require_once('includes/remove-paynow-buttons-on-thankyou-page.php');
/* Remove paynow buttons on thankyou page */

/* Disable width length and quantity when generate price is clicked for credit account user */
require_once('includes/disable-width-length-qty-credit-account.php');
/* Disable width length and quantity when generate price is clicked for credit account user */

/* Favourite heart system for news page */
require_once('includes/news-page-favourite-heart-system.php');
/* Favourite heart system for news page */

/* Show or hide product rolls tab based on ACF value */
require_once('includes/show-hide-product-rolls-tab.php');
/* Show or hide product rolls tab based on ACF value */

/* Display one shipment/scheduled shipment buttons on product page */
require_once('includes/product-page-one-shipment-schedule-shipments-buttons.php');
/* Display one shipment/scheduled shipment buttons on product page */

/* Toggle show/hide credit account related links in header */
require_once('includes/show-hide-credit-account-links-in-header.php');
/* Toggle show/hide credit account related links in header */

/* Remove get the app from emails */
require_once('includes/remove-get-the-app-from-emails.php');
/* Remove get the app from emails */

/* Custom voucher discount */
/* I need to test that this works correctly */
require_once('includes/custom-voucher-system-new.php');
/* Custom voucher discount */

/* Email Template CSS Styling */
require_once('includes/email-template-css-styling.php');
/* Email Template CSS Styling */

// Popular Products Carousel
require_once('includes/popular-products-carousel.php');
// Popular Products Carousel

// style up meta labels on thankyou page and admin orders page
require_once('includes/meta-labels-admin-orders-and-thankyou-page.php');
// style up meta labels on thankyou page and admin orders page

// Fetch the currency from the API
require_once('includes/currency-fetch.php');
// Fetch the currency from the API

/* Add currency switcher and subtitle to product page */
require_once('includes/add-currency-switcher-to-product-page.php');
/* Add currency switcher and subtitle to product page */

// Fetch the currency from the API
require_once('includes/add-currency-switcher-to-cart.php');
// Fetch the currency from the API

// Currency switcher session logic
require_once('includes/currency-switcher-session-logic.php');
// Currency switcher session logic

// Import duty notice in cart
require_once('includes/import-duty-notice-cart.php');
// Import duty notice in cart

// Fix header admin bar styling when logged in
require_once('includes/fix-header-for-admin-bar.php');
// Fix header admin bar styling when logged in

// Add loqate javacript to head on product page
require_once('includes/add-loqate-javascript-to-head.php');
// Add loqate javacript to head on product page

// Add markeplace price action button to the dashboard
require_once('includes/google-marketplace-price.php');
// Add markeplace price action button to the dashboard

// Custom Ivory Search
require_once('includes/custom-ivory-search.php');
// Custom Ivory Search

// Capture Cart Contents
require_once('includes/capture_cart_v9.php');
// Capture Cart Contents

// Get the roll length value and inject a div element into the rolls tab field
require_once('includes/rolls-tab-inject-rolls-length-data.php');
// Get the roll length value and inject a div element into the rolls tab field

// Conditionally add styling to woccommerce notice if is_scheduled exists
require_once('includes/conditionally-add-styling-if-is-schedule-exists.php');
// Conditionally add styling to woccommerce notice if is_scheduled exists

/* TEMPORARILY REMOVE LOAD TEXTDOMAIN WARNING THAT ARE FLOODING MY LOGS */
require_once('includes/remove_load_textdomain_logs.php');
/* TEMPORARILY REMOVE LOAD TEXTDOMAIN WARNING THAT ARE FLOODING MY LOGS */

// change price in schema.org json file 
/* NEED TO SWITCH THIS ON DURING GO LIVE PROCESS */
//require_once('includes/update_price_in_schema_json_file.php');
// change price in schema.org json file 

add_filter('redirect_canonical', function($redirect_url, $requested_url) {
    // Disable canonical redirect only on the custom product search page when a search term is present
    if (is_page('product-search') && isset($_GET['q'])) {
        return false;
    }
    return $redirect_url;
}, 10, 2);

// Tell Bing and Google NOT to index my staging site
add_action('send_headers', function () {
    header('X-Robots-Tag: noindex, nofollow, noarchive', true);
});
// Tell Bing and Google NOT to index my staging site




/* END CUSTOM FUNCTIONS */

/* DELIVERY OPTIONS FUNCTIONS */
require_once('includes/add_split_schedule_status_to_woocommerce_orders.php');
require_once('includes/split_schedule_calendar.php');
require_once('includes/split_schedule_admin.php');
//require_once('includes/admin-email-split-schedule-data.php'); // for displaying the split schedule breakdown on the emails
require_once('includes/admin-email-split-schedule-data-v3.php');
require_once('includes/enqueue-ajax-for-calendar-admin.php'); // Enqueue ajax for calendar admin *
require_once('includes/ajax-for-calendar-admin-new.php'); // Enqueue ajax for calendar admin *
require_once('includes/ajax-for-calendar-admin-merged-dates.php'); // Enqueue ajax for calendar admin *
require_once('includes/ajax-for-calendar-admin-single.php'); // Enqueue ajax for calendar admin *
require_once('includes/ajax-for-calendar-admin-multiple.php'); // Enqueue ajax for calendar admin *
require_once('includes/ajax-for-calendar-admin-select-shipments.php'); // Enqueue ajax for calendar admin *
require_once('includes/ajax-for-calendar-admin-tracking-number.php'); // Enqueue ajax for calendar admin *
require_once('includes/ajax-for-calendar-admin-tracking-url.php'); // Enqueue ajax for calendar admin *
require_once('includes/show-final-dispatch-action.php'); // Enqueue ajax for calendar admin *
require_once('includes/split_schedule_add_to_calendar.php');
require_once('includes/payment-gateway-disable-items-for-credit-account-v2.php'); // disable payment gateway options if logged in as credit account user
require_once('includes/add-credit-account-fund-status-to-product-page.php'); // display credit account fund details on product page
require_once('includes/send-credit-arrears-emails.php'); // send an email if the customers runs out of credit
/* END DELIVERY OPTIONS FUNCTIONS */




// This filter forces WooCommerce to always think the customer does not have a saved shipping address
// Forces customers to re-enter shipping details on every order, even when logged in
// Need to test that this is ok to use long term
add_filter( 'woocommerce_customer_has_shipping_address', '__return_false' );








/*
add_action( 'woocommerce_before_shop_loop_item', 'inject_hover_image_in_related_products', 5 );
function inject_hover_image_in_related_products() {

    // Run ONLY inside the related products loop
    if ( wc_get_loop_prop( 'name' ) !== 'related' ) {
        return;
    }

    // Inject image BEFORE <a> tag starts
    echo '<img class="woocommerce-shop__cat-hover-image" src="/wp-content/uploads/2025/11/category_hover_with_text.jpg" alt="">';
}
*/





/* TEMPORARY FUNCTIONS */

// validation for product width and length
// require_once('includes/display-order-object-on-thankyou-page.php');
// validation for product width and length

// Temporary - display acf is_single_product on product page
//require_once('includes/display-is-single-product-on-product-page.php');
// Temporary - display acf is_single_product on product page

/* TEMPORARY FUNCTIONS */



/*
add_action('woocommerce_before_single_product', 'display_session_values_at_top_of_product_page');
function display_session_values_at_top_of_product_page() {
    if (!is_product()) {
        return;
    }
    $custom_qty = WC()->session->get('custom_qty');
    $product = wc_get_product(get_the_ID());
    ?>
    <div class="product-page__session-info">
        <p><strong>Custom Quantity:</strong> <?php echo $custom_qty; ?> parts</p>
    </div>
    <?php
}
*/




/*
add_action('woocommerce_before_single_product', 'display_wc_session_data_on_product_page');

function display_wc_session_data_on_product_page() {
    if ( ! function_exists('WC') || ! WC()->session ) {
        echo '<div style="border:1px solid red; padding:10px; margin-bottom:20px;">WooCommerce session not available.</div>';
        return;
    }
    $session_data = WC()->session->get_session_data();
    echo '<div style="border:1px solid #0073aa; padding:15px; margin-bottom:20px; background:#f9f9f9;">';
    echo '<h3>WC Session Data</h3>';
	echo "<p>Shipping Address</p>";
	echo "<pre>";
	print_r(WC()->session->get('custom_shipping_address'));
	echo "</pre>";
	echo "<p>Shipments</p>";
	echo "<pre>";
	print_r(WC()->session->get('custom_shipments'));
	echo "</pre>";
	echo "<p>Quantity</p>";
	echo "<pre>";
	print_r(WC()->session->get('custom_qty'));
	print_r(WC()->session->get('stock_quantity'));
	echo "</pre>";

    // if ( empty($session_data) ) {
    //     echo '<p>No session data found.</p>';
    // } else {
    //     echo '<ul style="list-style:disc; padding-left:20px;">';
    //     foreach ( $session_data as $key => $value ) {
    //         // Handle array or object values nicely
    //         echo '<li><strong>' . esc_html($key) . ':</strong> ';
    //         if ( is_array($value) || is_object($value) ) {
    //             echo '<pre>' . esc_html(print_r($value, true)) . '</pre>';
    //         } else {
    //             echo esc_html($value);
    //         }
    //         echo '</li>';
    //     }
    //     echo '</ul>';
    // }

    echo '</div>';
}
*/


