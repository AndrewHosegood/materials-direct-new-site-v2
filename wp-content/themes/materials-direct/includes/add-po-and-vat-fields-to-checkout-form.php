<?php
// Add custom checkout fields
add_action('woocommerce_after_checkout_billing_form', 'custom_add_checkout_fields');

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
        'label'       => __('VAT / TAX No.'),
        'placeholder' => __('Please enter a VAT Tax number'),
        'required'    => false,
    ), $checkout->get_value('vat_tax_no'));

    // How did you find us?
    woocommerce_form_field('how_did_you_find_us', array(
        'type'     => 'select',
        'class'    => array('form-row-wide'),
        'label'    => __('How did you find us?'),
        'required' => false,
        'options'  => array(
            ''                        => __('Please select'),
            'search_engine'           => __('Search Engine'),
            'google_ads'              => __('Google Ads'),
            'facebook_ads'            => __('Facebook Ads'),
            'facebook_post_group'     => __('Facebook post / group'),
            'youtube_ads'             => __('YouTube Ads'),
            'other_social_ads'        => __('Other social media advertising'),
            'twitter_post'            => __('Twitter post'),
            'email'                   => __('Email'),
            'word_of_mouth'           => __('Word of Mouth'),
            'other'                   => __('Other'),
        ),
    ), $checkout->get_value('how_did_you_find_us'));

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

    if (!empty($_POST['how_did_you_find_us'])) {
        $order->update_meta_data(
            '_how_did_you_find_us',
            sanitize_text_field($_POST['how_did_you_find_us'])
        );
    }
}


// Display custom fields in admin order edit page
add_action('woocommerce_admin_order_data_after_billing_address', 'custom_display_fields_in_admin_order', 10, 1);

function custom_display_fields_in_admin_order($order) {
    $po_ref = get_post_meta($order->get_id(), '_po_order_ref_no', true);
    $vat_no = get_post_meta($order->get_id(), '_vat_tax_no', true);
    $found_us = $order->get_meta('_how_did_you_find_us');

    if ($po_ref) {
        echo '<p><strong>' . __('PO/Order Ref. No.') . ':</strong> ' . esc_html($po_ref) . '</p>';
    }
    if ($vat_no) {
        echo '<p><strong>' . __('VAT / TAX No.') . ':</strong> ' . esc_html($vat_no) . '</p>';
    }
    if ($found_us) {
        echo '<p><strong>' . __('How did you find us?') . ':</strong> ' . esc_html(ucwords(str_replace('_', ' ', $found_us))) . '</p>';
    }
}


// Display custom fields on Thank You page (after order details table)
add_action('woocommerce_order_details_after_order_table', 'custom_display_fields_on_thankyou', 10, 1);

function custom_display_fields_on_thankyou($order) {
    $po_ref = get_post_meta($order->get_id(), '_po_order_ref_no', true);
    $vat_no = get_post_meta($order->get_id(), '_vat_tax_no', true);
    $find_us = get_post_meta($order->get_id(), '_how_did_you_find_us', true);


    if ($po_ref || $vat_no || $find_us) {
        echo '<br>';
        echo '<table class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">';
        echo '<tbody>';

        if ($po_ref) {
            echo '<tr>';
            echo '<th>';
            echo '<strong>' . __('PO/Order Ref. No.') . ':</strong>';
            echo '</th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html($po_ref);
            echo '</td>';
            echo '</tr>';
        }
        if ($vat_no) {
            echo '<tr>';
            echo '<th>';
            echo '<strong>' . __('VAT / TAX No.') . ':</strong>';
            echo '</th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html($vat_no);
            echo '</td>';
            echo '</tr>';
        }
        if ($find_us) {
            echo '<tr>';
            echo '<th>';
            echo '<strong>' . __('How did you find us') . ':</strong>';
            echo '</th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html($find_us);
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }
}


// Add custom fields to WooCommerce emails (both admin and customer)

add_action('woocommerce_email_before_order_table', 'custom_display_fields_in_emails', 10, 4);

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
        if ($vat_no) {
            echo '<h2 style="font-size: 20px; line-height: 22px; font-weight: bold; margin: 0 0 18px; text-align: left; color: #ef9003; margin: 0;"><strong>VAT/TAX No.:</strong> ' . esc_html($vat_no) . '</h2>';
        }
        if ($po_ref) {
            echo '<h2 style="font-size: 20px; line-height: 22px; font-weight: bold; margin: 0 0 18px; text-align: left; color: #ef9003; margin: 0;"><strong>Customer PO:</strong> ' . esc_html($po_ref) . '</h2>';
        }

    }
}


/*
add_filter( 'woocommerce_email_order_details_heading', 'custom_email_order_heading_with_meta', 10, 3 );

function custom_email_order_heading_with_meta( $heading, $order, $email ) {

    if ( ! $order instanceof WC_Order ) {
        return $heading;
    }

    $po_ref = $order->get_meta( '_po_order_ref_no' );
    $vat_no = $order->get_meta( '_vat_tax_no' );

    // Nothing to add
    if ( ! $po_ref && ! $vat_no ) {
        return $heading;
    }

    // Plain text emails
    if ( $email && $email->is_plain_text() ) {
        if ( $po_ref ) {
            $heading .= "\nPO / Order Ref: " . $po_ref;
        }
        if ( $vat_no ) {
            $heading .= "\nVAT / TAX No: " . $vat_no;
        }
        return $heading;
    }

    // HTML emails
    $extra = '<br><span style="font-size:14px; font-weight:normal; color:#555;">';

    if ( $po_ref ) {
        $extra .= '<strong>PO / Order Ref:</strong> ' . esc_html( $po_ref ) . '<br>';
    }

    if ( $vat_no ) {
        $extra .= '<strong>VAT / TAX No:</strong> ' . esc_html( $vat_no );
    }

    $extra .= '</span>';

    return $heading . $extra;
}
*/
