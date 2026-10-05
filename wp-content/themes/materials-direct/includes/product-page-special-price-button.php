<?php
/**
 * ============================================================
 * SPECIAL PRICE - PRODUCT PAGE
 * ============================================================
 *
 * TEST VERSION
 *
 * Front-end visibility:
 *   Only logged-in Administrator with User ID 1
 *
 * AJAX security:
 *   - Nonce verification
 *   - Logged-in check
 *   - User ID 1 check
 *   - Administrator role check
 *   - WooCommerce session check
 *   - Price validation
 */


/**
 * ------------------------------------------------------------
 * 1. DISPLAY SPECIAL PRICE CONTROLS
 * ------------------------------------------------------------
 *
 * Displays the controls immediately before:
 *
 * <form class="cart">
 *
 */

function md_user_can_use_special_price() {

    if ( ! is_user_logged_in() ) {
        return false;
    }

    $current_user = wp_get_current_user();

    /* Get authorised salesman email addresses from the ACF Options Page.*/
    $admin_email_1 = get_field(
        'captured_carts_admin_email',
        'option'
    );

    $admin_email_2 = get_field(
        'captured_carts_admin_email_2',
        'option'
    );

    /* Build an array of authorised email addresses. Empty fields are ignored. */
    $allowed_emails = array_filter(
        array(
            strtolower( trim( sanitize_email( $admin_email_1 ) ) ),
            strtolower( trim( sanitize_email( $admin_email_2 ) ) ),
        )
    );

    /* * Get the current user's WordPress email address * and also convert it to lowercase. */ 
    $current_user_email = strtolower( trim( $current_user->user_email ) );

    /* User must be an Administrator AND their WordPress account email must match one of the authorised ACF email addresses.*/
    return (
        in_array( 'administrator', (array) $current_user->roles, true ) &&
        in_array( $current_user_email, $allowed_emails, true )
    );

}


add_action( 'woocommerce_before_add_to_cart_form', 'md_display_special_price_controls', 9999 );

function md_display_special_price_controls() {

    global $product;

    $user_id = get_current_user_id();
    $credit_options = get_field('credit_options', 'user_' . $user_id);
    $allow_credit = $credit_options['allow_user_credit_option'] ?? false; 

    // Do not display Special Price controls on single-product pages.
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    $is_product_single = function_exists('get_field') ? get_field('is_product_single', $product->get_id()) : false;

    if ($is_product_single) {
        return;
    }

    // User must be logged in.
    if ( ! is_user_logged_in() ) {
        return;
    }

    if ( ! md_user_can_use_special_price() ) {
        return;
    }

    /*
    $current_user = wp_get_current_user();

    // TEST VERSION:
    // Only Administrator with User ID 1 can see the controls.
    if (
        $current_user->ID !== 1 ||
        ! in_array( 'administrator', (array) $current_user->roles, true )
    ) {
        return;
    }
    */

    $session_special_price = WC()->session ? WC()->session->get( 'special_price' ) : null;
    $special_price_is_active = (is_numeric( $session_special_price ) && (float) $session_special_price > 0);
    
    ?>
    <div class="product-page__special-price-outer<?php echo $special_price_is_active ? ' active' : ''; ?>">
    <?php if ( $special_price_is_active ) : ?>
    
    <div id="special-price-status" style="margin-bottom: 10px; padding: 10px 12px; background: red; color: white; font-weight: 600;">
        SPECIAL PRICE ACTIVE —
        £<?php echo esc_html( number_format( (float) $session_special_price, 2, '.', ',' ) ); ?>
        per part
    </div>

<?php else : ?>
    <div id="special-price-status" style="margin-bottom: 10px; padding: 10px 12px; background: green; color: white; font-weight: 600;">
        NO SPECIAL PRICE ACTIVE — NORMAL PRICING
    </div>

<?php endif; ?>

    <div id="special-price-wrapper" style="margin-bottom: 10px; margin-top: 20px;">

        <div style="display: flex; justify-content: space-between;">

            <input
                type="text"
                name="special_price"
                id="special_price_input"
                class="product-page__special-price"
                value=""
                style="width: 39%; padding: .5rem; font-weight: 400; font-size: .9rem; background: white;"
                placeholder="£0.00"
                <?php //if($allow_credit != 1){ ?>
                    disabled
                <?php //} ?>
            >

            <button
                type="button"
                id="special_price"
                class="product-page__generate-price"
                style="margin: 0; width: 28%; font-size: 0.82rem;"
                <?php //if($allow_credit != 1){ ?>
                    disabled
                <?php //} ?>
            >
                <i class="fa-solid fa-key"></i> Add Special Price
            </button>

            <button
                type="button"
                id="reset_special_price"
                class="product-page__generate-price"
                style="margin: 0; width: 31%; font-size: 0.82rem;"
                <?php echo ! $special_price_is_active ? 'disabled' : ''; ?>
            >
                <i class="fa-solid fa-rotate-right"></i> Reset Special Price
            </button>

        </div>

        <!-- AJAX response message -->
        <div
            id="special-price-message"
            style="display: none; margin-top: 17px;"
        ></div>

    </div>
    </div>

    <?php
}


/**
 * ------------------------------------------------------------
 * 2. AJAX HANDLER
 * ------------------------------------------------------------
 *
 * This is the important security layer.
 *
 * Even if somebody manually attempts to call:
 *
 * /wp-admin/admin-ajax.php
 *
 * they still have to pass:
 *
 *   1. Valid nonce
 *   2. Logged-in check
 *   3. User ID 1 check
 *   4. Administrator role check
 *
 */
add_action( 'wp_ajax_md_add_special_price', 'md_add_special_price' );

function md_add_special_price() {

    /**
     * --------------------------------------------------------
     * SECURITY CHECK 1
     * Verify AJAX nonce.
     * --------------------------------------------------------
     */
    check_ajax_referer(
        'md_special_price_nonce',
        'nonce'
    );


    /**
     * --------------------------------------------------------
     * SECURITY CHECK 2
     * Make sure there is a logged-in WordPress user.
     * --------------------------------------------------------
     */
    if ( ! is_user_logged_in() ) {

        wp_send_json_error(
            array(
                'message' => 'You must be logged in to use the special price function.'
            ),
            403
        );
    }


    /**
     * --------------------------------------------------------
     * SECURITY CHECK 3
     * Get current WordPress user.
     * --------------------------------------------------------
     */
    
    if ( ! md_user_can_use_special_price() ) {

        wp_send_json_error(
            array(
                'message' => 'You are not authorised to use the special price function.'
            ),
            403
        );
    }



    /**
     * --------------------------------------------------------
     * SECURITY CHECK 4
     * Make sure WooCommerce session exists.
     * --------------------------------------------------------
     */
    if ( ! function_exists( 'WC' ) || ! WC()->session ) {

        wp_send_json_error(
            array(
                'message' => 'WooCommerce session is unavailable.'
            ),
            500
        );
    }


    /**
     * --------------------------------------------------------
     * 3. GET SUBMITTED SPECIAL PRICE
     * --------------------------------------------------------
     */
    $special_price = isset( $_POST['special_price'] )
        ? sanitize_text_field( wp_unslash( $_POST['special_price'] ) )
        : '';


    /**
     * --------------------------------------------------------
     * 4. CLEAN PRICE INPUT
     * --------------------------------------------------------
     *
     * Allows values such as:
     *
     * £999
     * £999.50
     * 999
     * 999.50
     * £1,299.50
     *
     */
    $special_price = str_replace(
        array( '£', ',', ' ' ),
        '',
        $special_price
    );


    /**
     * --------------------------------------------------------
     * 5. VALIDATE PRICE
     * --------------------------------------------------------
     */
    if (
        $special_price === '' ||
        ! is_numeric( $special_price )
    ) {

        wp_send_json_error(
            array(
                'message' => 'Please enter a valid special price.'
            ),
            400
        );
    }


    /**
     * --------------------------------------------------------
     * 6. CONVERT TO FLOAT
     * --------------------------------------------------------
     */
    $special_price = (float) $special_price;


    /**
     * --------------------------------------------------------
     * 7. PREVENT ZERO / NEGATIVE PRICES
     * --------------------------------------------------------
     */
    if ( $special_price <= 0 ) {

        wp_send_json_error(
            array(
                'message' => 'Please enter a special price greater than £0.00.'
            ),
            400
        );
    }


    /**
     * --------------------------------------------------------
     * 8. SAVE TO WOOCOMMERCE SESSION
     * --------------------------------------------------------
     */
    WC()->session->set('special_price', $special_price);


    /**
     * --------------------------------------------------------
     * 9. RETURN SUCCESS RESPONSE
     * --------------------------------------------------------
     */
    wp_send_json_success(
        array(
            'special_price' => $special_price,

            'formatted_price' => number_format(
                $special_price,
                2,
                '.',
                ','
            ),

            'message' => 'Your special cost per part price of £' .
                         number_format(
                             $special_price,
                             2,
                             '.',
                             ','
                         ) .
                         ' has been added'
        )
    );
}


/**
 * ------------------------------------------------------------
 * AJAX HANDLER - RESET SPECIAL PRICE
 * ------------------------------------------------------------
 */
add_action( 'wp_ajax_md_reset_special_price', 'md_reset_special_price' );

function md_reset_special_price() {

    /**
     * Security check 1:
     * Verify AJAX nonce.
     */
    check_ajax_referer(
        'md_special_price_nonce',
        'nonce'
    );


    /**
     * Security check 2:
     * User must be logged in.
     */
    if ( ! is_user_logged_in() ) {

        wp_send_json_error(
            array(
                'message' => 'You must be logged in to use this function.'
            ),
            403
        );
    }


    /**
     * Security check 3:
     * Only specific  User IDs.
     */
    if ( ! md_user_can_use_special_price() ) {

        wp_send_json_error(
            array(
                'message' => 'You are not authorised to use this function.'
            ),
            403
        );
    }


    /**
     * Security check 4:
     * Make sure WooCommerce session exists.
     */
    if ( ! function_exists( 'WC' ) || ! WC()->session ) {

        wp_send_json_error(
            array(
                'message' => 'WooCommerce session is unavailable.'
            ),
            500
        );
    }


    /**
     * Remove special price from WooCommerce session.
     */
    WC()->session->set( 'special_price', null );


    /**
     * Return success response.
     */
    wp_send_json_success(
        array(
            'message' => 'The special price has been reset. Normal price generation is now active.'
        )
    );
}


/**
 * ------------------------------------------------------------
 * 10. JAVASCRIPT
 * ------------------------------------------------------------
 *
 * Sends the special price to WordPress via AJAX.
 *
 * No page refresh occurs.
 */
add_action( 'wp_footer', 'md_special_price_javascript' );

function md_special_price_javascript() {

    // Only output JavaScript when the user is logged in.
    if ( ! is_user_logged_in() ) {
        return;
    }

    if ( ! md_user_can_use_special_price() ) {
        return;
    }

    ?>

    <script>
    jQuery(function($) {

        $('#special_price').on('click', function(e) {

            e.preventDefault();

            var button = $(this);
            var priceInput = $('#special_price_input');
            var message = $('#special-price-message');

            var specialPrice = priceInput.val().trim();

            /**
             * Clear previous message.
             */
            message.hide().html('');


            /**
             * Basic front-end validation.
             */
            if (specialPrice === '') {

                message
                    .css({
                        'color': '#c00',
                        'font-weight': '600'
                    })
                    .html('Please enter a special price.')
                    .show();

                priceInput.focus();

                return;
            }


            /**
             * Prevent multiple clicks while AJAX
             * request is being processed.
             */
            button.prop('disabled', true);


            /**
             * AJAX request.
             */
            $.ajax({

                url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',

                type: 'POST',

                data: {
                    action: 'md_add_special_price',

                    nonce: '<?php echo esc_js( wp_create_nonce( 'md_special_price_nonce' ) ); ?>',

                    special_price: specialPrice
                },


                /**
                 * Successful HTTP response.
                 */
                success: function(response) {

                    if (response.success) {

                        $('.product-page__special-price-outer').addClass('active');
                        $('#reset_special_price').prop('disabled', false);

                        message
                            .css({
                                'font-weight': '600'
                            })
                            .html(response.data.message)
                            .show();

                            $('#special-price-status')
                            .css({
                                'background': 'red',
                                'border': '0px solid #80c980',
                                'color': 'white'
                            })
                            .html(
                                'SPECIAL PRICE ACTIVE — £' +
                                response.data.formatted_price +
                                ' per part'
                            )
                            .show();

                    } else {

                        message
                            .css({
                                'color': 'rgb(27, 102, 253)',
                                'font-weight': '600'
                            })
                            .html(
                                response.data && response.data.message
                                    ? response.data.message
                                    : 'Unable to add the special price.'
                            )
                            .show();
                    }
                },


                /**
                 * AJAX/network/server error.
                 */
                error: function(xhr) {

                    var errorMessage = 'There was a server error. Please try again.';

                    /**
                     * Try to retrieve the message returned
                     * by wp_send_json_error().
                     */
                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.data &&
                        xhr.responseJSON.data.message
                    ) {
                        errorMessage = xhr.responseJSON.data.message;
                    }

                    message
                        .css({
                            'color': 'rgb(52, 210, 9)',
                            'font-weight': '600'
                        })
                        .html(errorMessage)
                        .show();
                },


                /**
                 * Always re-enable button when request finishes.
                 */
                complete: function() {

                    button.prop('disabled', false);

                }

            });

        });

        $('#reset_special_price').on('click', function(e) {

            e.preventDefault();

            var button = $(this);
            var priceInput = $('#special_price_input');
            var message = $('#special-price-message');

            $('#special_price_input').prop('disabled', true);
            $('#special_price').prop('disabled', true);

            // setTimeout(() => {
            // $('.product-page__special-price-outer').fadeOut(500); // 0.5 second fade
            // }, 2000);

            /**
             * Prevent multiple clicks.
             */
            button.prop('disabled', true);

            /**
             * Clear previous message.
             */
            message.hide().html('');

            /**
             * AJAX request to remove special_price
             * from WooCommerce session.
             */
            $.ajax({

                url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',

                type: 'POST',

                data: {
                    action: 'md_reset_special_price',

                    nonce: '<?php echo esc_js( wp_create_nonce( 'md_special_price_nonce' ) ); ?>'
                },

                success: function(response) {

                    if (response.success) {

                        $('.product-page__special-price-outer').removeClass('active');

                        /**
                         * Clear the input field.
                         */
                        priceInput.val('');

                        /**
                         * Display confirmation.
                         */
                        message
                            .css({
                                'font-weight': '600'
                            })
                            .html(response.data.message)
                            .show();

                            $('#special-price-status')
                            .css({
                                'background': 'green',
                                'border': '0px solid #ccc',
                                'color': 'white'
                            })
                            .html('NO SPECIAL PRICE ACTIVE — NORMAL PRICING')
                            .show();
                                            

                    } else {

                        message
                            .css({
                                'color': '#c00',
                                'font-weight': '600'
                            })
                            .html(
                                response.data && response.data.message
                                    ? response.data.message
                                    : 'Unable to reset the special price.'
                            )
                            .show();
                    }
                },

                error: function(xhr) {

                    var errorMessage = 'There was a server error. Please try again.';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.data &&
                        xhr.responseJSON.data.message
                    ) {
                        errorMessage = xhr.responseJSON.data.message;
                    }

                    message
                        .css({
                            'color': '#c00',
                            'font-weight': '600'
                        })
                        .html(errorMessage)
                        .show();
                },

                complete: function() {

                    button.prop('disabled', false);

                }

            });

        });

    });
    </script>

    <?php
}