<?php
// Add custom checkout fields
add_action('woocommerce_before_checkout_billing_form', 'custom_add_checkout_fields');

function custom_add_checkout_fields($checkout) {

    // PO/Order Ref. No. - Required field
    woocommerce_form_field('po_order_ref_no', array(
        'type'        => 'text',
        'class'       => array('form-row-wide'),
        'label'       => __('Your PO/Order Ref. No.'),
        'placeholder' => __('Please enter a unique order reference'),
        'required'    => true,
    ), $checkout->get_value('po_order_ref_no'));

    // VAT / TAX No. - Optional field
    woocommerce_form_field('vat_tax_no', array(
        'type'        => 'text',
        'class'       => array('form-row-wide'),
        'label'       => __('VAT / TAX No. (optional)'),
        'placeholder' => __('Please enter a VAT Tax number'),
        'required'    => false,
    ), $checkout->get_value('vat_tax_no'));

}

// Validate the required PO/Order Ref. No. field
add_action('woocommerce_checkout_process', 'custom_validate_po_order_ref_field');

function custom_validate_po_order_ref_field() {
    if (empty($_POST['po_order_ref_no'])) {
        wc_add_notice(__('Please enter a PO/Order Reference Number.'), 'error');
    }
}

// Save the custom fields to order meta
add_action('woocommerce_checkout_create_order', 'custom_save_checkout_fields_hpos_safe', 20, 2);

function custom_save_checkout_fields_hpos_safe( $order, $data ) {

    if (!empty($_POST['po_order_ref_no'])) {
        $order->update_meta_data(
            '_po_order_ref_no',
            sanitize_text_field($_POST['po_order_ref_no'])
        );
    }

    if (!empty($_POST['vat_tax_no'])) {
        $order->update_meta_data(
            '_vat_tax_no',
            sanitize_text_field($_POST['vat_tax_no'])
        );
    }
}
// add_action('woocommerce_checkout_update_order_meta', 'custom_save_checkout_fields');

// function custom_save_checkout_fields($order_id) {
//     if (!empty($_POST['po_order_ref_no'])) {
//         update_post_meta($order_id, '_po_order_ref_no', sanitize_text_field($_POST['po_order_ref_no']));
//     }

//     if (!empty($_POST['vat_tax_no'])) {
//         update_post_meta($order_id, '_vat_tax_no', sanitize_text_field($_POST['vat_tax_no']));
//     }
// }


// Display custom fields in admin order edit page
add_action('woocommerce_admin_order_data_after_billing_address', 'custom_display_fields_in_admin_order', 10, 1);

function custom_display_fields_in_admin_order($order) {
    $po_ref = get_post_meta($order->get_id(), '_po_order_ref_no', true);
    $vat_no = get_post_meta($order->get_id(), '_vat_tax_no', true);

    if ($po_ref) {
        echo '<p><strong>' . __('PO/Order Ref. No.') . ':</strong> ' . esc_html($po_ref) . '</p>';
    }
    if ($vat_no) {
        echo '<p><strong>' . __('VAT / TAX No.') . ':</strong> ' . esc_html($vat_no) . '</p>';
    }
}


// Display custom fields on Thank You page (after order details table)
add_action('woocommerce_order_details_after_order_table', 'custom_display_fields_on_thankyou', 10, 1);

function custom_display_fields_on_thankyou($order) {
    $po_ref = get_post_meta($order->get_id(), '_po_order_ref_no', true);
    $vat_no = get_post_meta($order->get_id(), '_vat_tax_no', true);

    if ($po_ref || $vat_no) {
        echo '<h2>' . __('Additional Information') . '</h2>';
        echo '<ul>';

        if ($po_ref) {
            echo '<li><strong>' . __('PO/Order Ref. No.') . ':</strong> ' . esc_html($po_ref) . '</li>';
        }
        if ($vat_no) {
            echo '<li><strong>' . __('VAT / TAX No.') . ':</strong> ' . esc_html($vat_no) . '</li>';
        }

        echo '</ul>';
    }
}


// Add custom fields to WooCommerce emails (both admin and customer)
add_action('woocommerce_email_after_order_table', 'custom_display_fields_in_emails', 10, 4);

function custom_display_fields_in_emails($order, $sent_to_admin, $plain_text, $email) {
    // Get the values from order meta
    $po_ref = get_post_meta($order->get_id(), '_po_order_ref_no', true);
    $vat_no = get_post_meta($order->get_id(), '_vat_tax_no', true);

    if ($plain_text) {
        // Plain text version
        if ($po_ref) {
            echo "\nPO/Order Ref. No.: " . $po_ref;
        }
        if ($vat_no) {
            echo "\nVAT / TAX No.: " . $vat_no;
        }
    } else {
        // HTML version
        echo '<h2>Additional Information</h2><ul>';
        if ($po_ref) {
            echo '<li><strong>PO/Order Ref. No.:</strong> ' . esc_html($po_ref) . '</li>';
        }
        if ($vat_no) {
            echo '<li><strong>VAT / TAX No.:</strong> ' . esc_html($vat_no) . '</li>';
        }
        echo '</ul>';
    }
}