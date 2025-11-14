<?php

// Display COFCs on product page
add_action('woocommerce_before_add_to_cart_button', 'md_custom_product_checkboxes');

function md_custom_product_checkboxes() {
    ?>
    <div class="md-extra-options">
        <label>
            <input name="add_manufacturers_COFC" type="checkbox" value="10" id="add_manufacturers_COFC">
            <span>Manufacturers COFC £10
                <span class="cfc__tooltip" data-tooltip="A Manufacturers Certificate of Conformity (MCOFC) is a document that manufacturers issue to confirm that a product has been made to a specific standard and meets quality and regulatory requirements.">ⓘ</span>
            </span>
        </label><br>

        <label>
            <input name="add_fair" type="checkbox" value="95" id="add_fair">
            <span>FAIR £95
                <span class="cfc__tooltip" data-tooltip="A First Article Inspection Report (FAIR) or ISIR is the first item we make for the customer and measure to confirm all dimensions meet the drawing and tolerances.">ⓘ</span>
            </span>
        </label><br>

        <label>
            <input name="add_materials_direct_cofc" type="checkbox" value="12.5" id="add_materials_direct_cofc">
            <span>Materials Direct COFC £12.50
                <span class="cfc__tooltip" data-tooltip="A certificate from Materials Direct confirming that the part meets the criteria ordered (RoHS and REACH compliant).">ⓘ</span>
            </span>
        </label>
    </div>
    <?php
}
// Display COFCs on product page



// Save selected checkboxes to cart item data
add_filter('woocommerce_add_cart_item_data', 'md_add_custom_fees_to_cart_item', 10, 2);

function md_add_custom_fees_to_cart_item($cart_item_data, $product_id) {
    $extra_options = [];

    if (isset($_POST['add_manufacturers_COFC'])) {
        $extra_options['Manufacturers COFC'] = floatval($_POST['add_manufacturers_COFC']);
		error_log("Manufacturers COFC: " . $extra_options['Manufacturers COFC']);
    }
    if (isset($_POST['add_fair'])) {
        $extra_options['FAIR'] = floatval($_POST['add_fair']);
		error_log("FAIR: " . $extra_options['FAIR']);
    }
    if (isset($_POST['add_materials_direct_cofc'])) {
        $extra_options['Materials Direct COFC'] = floatval($_POST['add_materials_direct_cofc']);
		error_log("Materials Direct COFC: " . $extra_options['Materials Direct COFC']);
    }

    if (!empty($extra_options)) {
        $cart_item_data['md_extra_options'] = $extra_options;
        $cart_item_data['unique_key'] = md5(microtime().rand());
		error_log("Extra Options: " . print_r($extra_options));
    }

    return $cart_item_data;
	error_log("Cart Item Data: " . print_r($cart_item_data));
}
// Save selected checkboxes to cart item data




// Display extra options in the cart and checkout
add_filter('woocommerce_get_item_data', 'md_display_extra_options_cart', 10, 2);
function md_display_extra_options_cart($item_data, $cart_item) {
    if (isset($cart_item['md_extra_options'])) {
        foreach ($cart_item['md_extra_options'] as $name => $price) {
            $item_data[] = array(
                'name' => $name,
                'value' => wc_price($price)
            );
        }
    }
    return $item_data;
}
// Display extra options in the cart and checkout

// Adjust cart item price to include the selected fees
add_action('woocommerce_before_calculate_totals', 'md_add_extra_costs_to_cart', 10, 1);
function md_add_extra_costs_to_cart($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    foreach ($cart->get_cart() as $cart_item) {
        if (isset($cart_item['md_extra_options'])) {
            $extra_total = array_sum($cart_item['md_extra_options']);
            error_log("Extra Total: " . print_r($extra_total));
            $cart_item['data']->set_price($cart_item['data']->get_price() + $extra_total);
        }
    }
}
