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

// Shop page styling
require_once('includes/shop-page-styling.php');
// Shop page styling

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
//require_once('includes/validation-for-product-width-and-length.php');
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

// add credit account balance info to product page
require_once('includes/add-credit-account-fund-status-to-product-page.php');
// add credit account balance info to product page

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

/* Add currency switcher and subtitle to product page */
require_once('includes/add-currency-switcher-to-product-page.php');
/* Add currency switcher and subtitle to product page */

/* Add view products link and need help link to product page */
require_once('includes/add-view-product-details-link-and-need-help-link.php');
/* Add view products link and need help link to product page */

/* Add download datasheet button on product page */
require_once('includes/add-download-datasheet-product-page.php');
/* Add download datasheet button on product page */

/* add custom id to woocoomerce default tabs on product page */
require_once('includes/add-custom-id-to-tabs-on-product-page.php');
/* add custom id to woocoomerce default tabs on product page */

/* Disable payment gateway items when the user has a credit account */
require_once('includes/payment-gateway-disable-items-for-credit-account.php');
/* Disable payment gateway items when the user has a credit account */

/* Remove paynow buttons on thankyou page */
require_once('includes/remove-paynow-buttons-on-thankyou-page.php');
/* Remove paynow buttons on thankyou page */



/* END CUSTOM FUNCTIONS */







// Remove product thumbnails in WooCommerce emails
/*
add_filter( 'woocommerce_email_order_items_args', function( $args ) {
    $args['show_image'] = false; // disable thumbnails
    return $args;
});
*/













/*
if (!function_exists('add_shortcode_to_shop_page')) {

    function add_shortcode_to_shop_page() {
        if (is_shop() || is_product_category()) {
			
			echo '<div id="advanced-filter" class="filter-heading-background">';
			echo '<div class="filter-content-wrapper">';
			echo '<h4 class="filter-heading">Product Filter</h4>';
			echo '<a class="filter-btn" href="/shop/">Reset</a>';
			echo '<a class="filter-btn-hide" href="">Hide</a>';
			echo '</div>';
			echo '</div>';

            echo '<div class="filter-wrapper">';
			echo '<div class="filter-wrapper-inner">';
			echo do_shortcode('[woof]');
            //echo do_shortcode("[woof sid='generator_669ebf62086a6 woof_auto_4_columns' autohide='0' autosubmit='0' is_ajax='1' ajax_redraw='0' start_filtering_btn='0' btn_position='b' dynamic_recount='1' hide_terms_count_txt='0' mobile_mode='1' ]");
            
			echo '</div>';
			echo '</div>';
        }
    }
    add_action('woocommerce_before_shop_loop', 'add_shortcode_to_shop_page');
}
*/


// add_action( 'woocommerce_before_shop_loop', 'add_woof_builder_before_shop' );
// function add_woof_builder_before_shop() {
//     if ( is_shop() ) {
//         echo do_shortcode('[woof]');
//     }
// }






add_action( 'woocommerce_before_shop_loop_item', 'inject_hover_image_in_related_products', 5 );
function inject_hover_image_in_related_products() {

    // Run ONLY inside the related products loop
    if ( wc_get_loop_prop( 'name' ) !== 'related' ) {
        return;
    }

    // Inject image BEFORE <a> tag starts
    echo '<img class="woocommerce-shop__cat-hover-image" src="/wp-content/uploads/2025/11/category_hover_with_text.jpg" alt="">';
}







/*
function add_stock_sheet_dimension() {
	if ( ! is_product() ) {
        return; // Only run on single product pages
    }

    global $product;
    $product_id = get_the_ID();
    $product = wc_get_product($product_id);

	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
        return;
    }

	// Get the border
    $item_border = floatval(get_field('border_around', $product_id)) * 10;
    $allowed_border = $item_border * 2;

	// Get product dimensions (in cm), convert to mm
	$sheet_length_mm = $product->get_length() * 10;
    $sheet_width_mm  = $product->get_width() * 10;

	// Allowed values
    $allowed_length = $sheet_length_mm - $allowed_border;
    $allowed_width  = $sheet_width_mm - $allowed_border;

	if ( ! $sheet_length_mm || ! $sheet_width_mm ) {
        return; // Avoid injecting if dimensions are missing
    }

	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			const stocksheetWidth = <?php echo esc_js($sheet_length_mm); ?>;
			const stocksheetLength = <?php echo esc_js($sheet_width_mm); ?>;

			function handleTabChange() {
				const selectedTab = $('[name="tabs_input"]:checked').val();
				const $widthInput = $('input[name="custom_width"]');
				const $lengthInput = $('input[name="custom_length"]');

				if (!$widthInput.length || !$lengthInput.length) {
					return; // Don't proceed if inputs are not found
				}

				if (selectedTab === "stock-sheets") {
					$widthInput.val(stocksheetWidth).prop('disabled', true).trigger('change');
					$lengthInput.val(stocksheetLength).prop('disabled', true).trigger('change');
				} else {
					$widthInput.prop('disabled', false);
					$lengthInput.prop('disabled', false);
				}
			}

			// Run on page load
			handleTabChange();

			// Run when tab changes
			$('[name="tabs_input"]').on('change', function() {
				// Optional delay in case DOM state is updated after change
				setTimeout(handleTabChange, 100);
			});
		});
	</script>
	<?php
}

add_action( 'wp_head', 'add_stock_sheet_dimension' );

*/





/*
function custom_remove_woocommerce_page_titles( $show ) {
    if ( is_shop() || is_product_category() ) {
        return false;
    }
    return $show;
}
add_filter( 'woocommerce_show_page_title', 'custom_remove_woocommerce_page_titles' );
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


