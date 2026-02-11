<?php
function inject_custom_jquery_for_product_dimensions() {
    if ( ! is_product() ) {
        return; // Only run on single product pages
    }

    global $product;
    $product_id = get_the_ID();
    $product = wc_get_product($product_id);

    if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
        return;
    }

    if ($product->get_status() !== 'publish') {
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
        jQuery(document).ready(function ($) {
            const maxWidth = <?php echo esc_js( $allowed_width ); ?>;
            const maxLength = <?php echo esc_js( $allowed_length ); ?>;

            const $panel = $('.product-page__grey-panel');
            const $button = $('#generate_price');

            // Remove existing message function
            function removeMessage() {
                $('.product-page__backorder-message').remove();
            }

            // Add message function
            function showMessage(message) {
                removeMessage(); // prevent duplicates
                $panel.after(`
                    <div style="margin-bottom: 1rem;" class="product-page__backorder-message">
                        <p style="line-height: 1.7rem;" class="product-page__backorder-message-text">
                            <strong>Notice:</strong> ${message}
                        </p>
                    </div>
                `);
            }

            $('#input_width, #input_length').on('keyup change', function () {
                const width = parseFloat($('#input_width').val()) || 0;
                const length = parseFloat($('#input_length').val()) || 0;

                removeMessage(); // clear old notices

                if (width > maxWidth || length > maxLength) {
                    if (width > maxWidth) {
                        showMessage("Unfortunately your drawing wont fit the sheet. Part width exceeds stock sheet size. Enter a width of " + maxWidth +"mm or less");
                    } else if (length > maxLength) {
                        showMessage("Unfortunately your drawing wont fit the sheet. Part length exceeds stock sheet size. Enter a width of "+ maxLength+"mm or less");
                    }
                    $button.prop('disabled', true);
                } else {
                    $button.prop('disabled', false);
                }
            });
        });
    </script>
    <?php
}
add_action( 'wp_head', 'inject_custom_jquery_for_product_dimensions' );

/*
function inject_custom_jquery_for_product_dimensions() {
	// Lets stop user entering width and length greater the stock sheet size
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
    $sheet_width_mm = $product->get_width() * 10;

	// set an allowed length and width minus the border size
	$allowed_length = $sheet_length_mm - $allowed_border;
	$allowed_width = $sheet_width_mm - $allowed_border;

    if ( ! $sheet_length_mm || ! $sheet_width_mm ) {
        return; // Avoid injecting if dimensions are missing
    }

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            const maxWidth = <?php echo esc_js( $allowed_width ); ?>;
            const maxLength = <?php echo esc_js( $allowed_length ); ?>;

            $('#input_width, #input_length').on('keyup', function () {
                const width = parseFloat($('#input_width').val()) || 0;
                const length = parseFloat($('#input_length').val()) || 0;

                const $button = $('#generate_price');

                if (width > maxWidth || length > maxLength) {
                    if (width > maxWidth) {
                        alert("Width exceeds allowed stock sheet width");
                    }
                    if (length > maxLength) {
                        alert("Length exceeds allowed stock sheet length");
                    }

                    // Disable the button
                    $button.prop('disabled', true);
                } else {
                    // Re-enable the button if values are within limits
                    $button.prop('disabled', false);
                }
            });
        });
    </script>
    <?php
}
add_action( 'wp_head', 'inject_custom_jquery_for_product_dimensions' );
*/