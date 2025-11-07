<?php
function combined_custom_jquery_for_dimensions_and_stock_sheets() {
    if ( ! is_product() ) {
        return;
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

    // Allowed usable dimensions
    $allowed_length = $sheet_length_mm - $allowed_border;
    $allowed_width  = $sheet_width_mm - $allowed_border;

    if ( ! $sheet_length_mm || ! $sheet_width_mm ) {
        return;
    }

    ?>
    <script type="text/javascript">
    jQuery(document).ready(function ($) {
        const stocksheetWidth = <?php echo esc_js( $sheet_width_mm ); ?>;
        const stocksheetLength = <?php echo esc_js( $sheet_length_mm ); ?>;
        const maxWidth = <?php echo esc_js( $allowed_width ); ?>;
        const maxLength = <?php echo esc_js( $allowed_length ); ?>;

        const $widthInput = $('input[name="custom_width"]');
        const $lengthInput = $('input[name="custom_length"]');
        const $button = $('#generate_price');
        const $panel = $('.product-page__grey-panel');

        // Utility functions
        function removeMessage() {
            $('.product-page__backorder-message').remove();
        }

        function showMessage(message) {
            removeMessage();
            $panel.after(`
                <div aria-live="polite" style="margin-bottom: 1rem;" class="product-page__backorder-message">
                    <p style="line-height: 1.7rem;" class="product-page__backorder-message-text">
                        <strong>Notice:</strong> ${message}
                    </p>
                </div>
            `);
        }

        // Handle tab changes (on load and when user switches)
        function handleTabChange() {
            const selectedTab = $('[name="tabs_input"]:checked').val();

            if (selectedTab === "stock-sheets") {
                $widthInput.val(stocksheetWidth).prop('disabled', true).trigger('change');
                $lengthInput.val(stocksheetLength).prop('disabled', true).trigger('change');
                $button.prop('disabled', false); // Force enable button
                removeMessage(); // No validation needed
            } else {
                $widthInput.prop('disabled', false);
                $lengthInput.prop('disabled', false);
                validateInputs(); // Re-validate for custom input
            }
        }

        // Validate inputs (only when not "stock-sheets")
        function validateInputs() {
            const selectedTab = $('[name="tabs_input"]:checked').val();

            if (selectedTab === "stock-sheets") {
                return; // Skip validation
            }

            const width = parseFloat($widthInput.val()) || 0;
            const length = parseFloat($lengthInput.val()) || 0;

            removeMessage();

            if (width > maxWidth || length > maxLength) {
                if (width > maxWidth) {
                    showMessage("Unfortunately your drawing won't fit the sheet. Part width exceeds stock sheet size. Enter a width of " + maxWidth + "mm or less.");
                } else if (length > maxLength) {
                    showMessage("Unfortunately your drawing won't fit the sheet. Part length exceeds stock sheet size. Enter a length of " + maxLength + "mm or less.");
                }
                $button.prop('disabled', true);
            } else {
                $button.prop('disabled', false);
            }
        }

        // Initial run
        handleTabChange();

        // Event bindings
        $('[name="tabs_input"]').on('change', function () {
            setTimeout(handleTabChange, 100); // Delay in case of DOM lag
        });

        $widthInput.add($lengthInput).on('keyup change', function () {
            validateInputs();
        });
    });
    </script>
    <?php
}
add_action( 'wp_head', 'combined_custom_jquery_for_dimensions_and_stock_sheets' );