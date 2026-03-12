<?php
// Add Job Title field to billing fields
add_filter('woocommerce_checkout_fields', 'add_billing_job_title_field');
function add_billing_job_title_field($fields) {
    $fields['billing']['billing_job_title'] = array(
        'type' => 'text',
        'label' => __('Job Title'),
        'placeholder' => __('Please add your job title'),
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 25, // After last name (priority 20)
    );
    return $fields;
}

// Customize the form field output to add helper text for Job Title
add_filter('woocommerce_form_field_text', 'custom_job_title_form_field', 10, 4);
function custom_job_title_form_field($field, $key, $args, $value) {
    if ($key === 'billing_job_title') {
        $helper = '<p class="jobtitle-helper-text" style="margin-top:8px;">If this order is for personal use / a personal order not related to a business – please enter "personal" in this box.</p>';
        $field = str_replace('</div>', $helper . '</div>', $field);
    }
    return $field;
}

// Add PO/Order Ref. No. field at the top of billing form
add_action('woocommerce_before_checkout_billing_form', 'custom_add_po_order_ref_field_top');
function custom_add_po_order_ref_field_top($checkout) {
    woocommerce_form_field('po_order_ref_no', array(
        'type'          => 'text',
        'class'         => array('form-row-wide'),
        'label'         => __('Your PO/Order Ref. No.'),
        'placeholder'   => __('Please enter a unique order reference'),
        'required'      => true,
    ), $checkout->get_value('po_order_ref_no'));
}

// Add the other two fields at the bottom of billing form
add_action('woocommerce_after_checkout_billing_form', 'custom_add_bottom_checkout_fields');
function custom_add_bottom_checkout_fields($checkout) {
    // VAT / TAX No. - Optional field
    woocommerce_form_field('vat_tax_no', array(
        'type'          => 'text',
        'class'         => array('form-row-wide'),
        'label'         => __('VAT / TAX No.'),
        'placeholder'   => __('Please enter a VAT Tax number'),
        'required'      => false,
    ), $checkout->get_value('vat_tax_no'));

    // How did you find us?
    woocommerce_form_field('how_did_you_find_us', array(
        'type'          => 'select',
        'class'         => array('form-row-wide'),
        'label'         => __('How did you find us?'),
        'required'      => false,
        'options'       => array(
            ''                      => __('Please select'),
            'search_engine'         => __('Search Engine'),
            'google_ads'            => __('Google Ads'),
            'facebook_ads'          => __('Facebook Ads'),
            'facebook_post_group'   => __('Facebook post / group'),
            'youtube_ads'           => __('YouTube Ads'),
            'other_social_ads'      => __('Other social media advertising'),
            'twitter_post'          => __('Twitter post'),
            'email'                 => __('Email'),
            'word_of_mouth'         => __('Word of Mouth'),
            'other'                 => __('Other'),
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
    $job_title = get_post_meta($order->get_id(), '_billing_job_title', true);
    $found_us = $order->get_meta('_how_did_you_find_us');
    if ($po_ref) {
        echo '<p><strong>' . __('PO/Order Ref. No.') . ':</strong> ' . esc_html($po_ref) . '</p>';
    }
    if ($vat_no) {
        echo '<p><strong>' . __('VAT / TAX No.') . ':</strong> ' . esc_html($vat_no) . '</p>';
    }
    if ($job_title) {
        echo '<p><strong>' . __('Job Title') . ':</strong> ' . esc_html($job_title) . '</p>';
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
    $job_title = get_post_meta($order->get_id(), '_billing_job_title', true);
    $find_us = get_post_meta($order->get_id(), '_how_did_you_find_us', true);
    if ($po_ref || $vat_no || $job_title || $find_us) {
        echo '<br>';
        echo '<table class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">';
        echo '<tbody>';
        if ($po_ref) {
            echo '<tr>';
            echo '<th><strong>' . __('PO/Order Ref. No.') . ':</strong></th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html($po_ref);
            echo '</td>';
            echo '</tr>';
        }
        if ($vat_no) {
            echo '<tr>';
            echo '<th><strong>' . __('VAT / TAX No.') . ':</strong></th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html($vat_no);
            echo '</td>';
            echo '</tr>';
        }
        if ($job_title) {
            echo '<tr>';
            echo '<th><strong>' . __('Job Title') . ':</strong></th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html($job_title);
            echo '</td>';
            echo '</tr>';
        }
        if ($find_us) {
            echo '<tr>';
            echo '<th><strong>' . __('How did you find us?') . ':</strong></th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html(ucwords(str_replace('_', ' ', $find_us)));
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
    //$job_title = get_post_meta($order->get_id(), '_billing_job_title', true);

    if ($plain_text) {
        // Plain text version (already PO first)
        if ($po_ref) {
            echo "\nPO/Order Ref. No.: " . $po_ref;
        }
        if ($vat_no) {
            echo "\nVAT / TAX No.: " . $vat_no;
        }
        // if ($job_title) {
        //     echo "\nJob Title: " . $job_title;
        // }
    } else {
        // HTML version - now PO first for consistency
        if ($po_ref) {
            echo '<h2 style="font-size: 20px; line-height: 22px; font-weight: bold; margin: 0 0 18px; text-align: left; color: #ef9003; margin: 0;"><strong>Customer PO:</strong> ' . esc_html($po_ref) . '</h2>';
        }
        if ($vat_no) {
            echo '<h2 style="font-size: 20px; line-height: 22px; font-weight: bold; margin: 0 0 18px; text-align: left; color: #ef9003; margin: 0;"><strong>VAT/TAX No.:</strong> ' . esc_html($vat_no) . '</h2>';
        }
        // if ($job_title) {
        //     echo '<h2 style="font-size: 20px; line-height: 22px; font-weight: bold; margin: 0 0 18px; text-align: left; color: #ef9003; margin: 0;"><strong>Job Title:</strong> ' . esc_html($job_title) . '</h2>';
        // }
    }
}


// Display Job Title after the order table in emails
add_action('woocommerce_email_after_order_table', 'custom_display_job_title_in_emails', 10, 4);
function custom_display_job_title_in_emails($order, $sent_to_admin, $plain_text, $email) {
    // Only display for admin emails
    if (!$sent_to_admin) {
        return;
    }

    $job_title = get_post_meta($order->get_id(), '_billing_job_title', true);

    if ($job_title) {
        if ($plain_text) {
            echo "\nJob Title: " . $job_title;
        } else {
            echo '<h2 style="font-size: 16px; line-height: 20px; font-weight: normal; margin: 0 0 18px; text-align: left; color: #636363; margin: 0; border: 1px solid #ccc; padding: 0.5rem"><strong>Job Title:</strong> ' . esc_html($job_title) . '</h2>';
        }
    }
}



/*
// Add Job Title field to billing fields
add_filter('woocommerce_checkout_fields', 'add_billing_job_title_field');
function add_billing_job_title_field($fields) {
    $fields['billing']['billing_job_title'] = array(
        'type' => 'text',
        'label' => __('Job Title'),
        'placeholder' => __('Please add your job title'),
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 25, // After last name (priority 20)
    );
    return $fields;
}

// Customize the form field output to add helper text for Job Title
add_filter('woocommerce_form_field_text', 'custom_job_title_form_field', 10, 4);
function custom_job_title_form_field($field, $key, $args, $value) {
    if ($key === 'billing_job_title') {
        $helper = '<p class="jobtitle-helper-text" style="margin-top:8px;">If this order is for personal use / a personal order not related to a business – please enter "personal" in this box.</p>';
        $field = str_replace('</div>', $helper . '</div>', $field);
    }
    return $field;
}

// Add PO/Order Ref. No. field at the top of billing form
add_action('woocommerce_before_checkout_billing_form', 'custom_add_po_order_ref_field_top');
function custom_add_po_order_ref_field_top($checkout) {
    woocommerce_form_field('po_order_ref_no', array(
        'type'          => 'text',
        'class'         => array('form-row-wide'),
        'label'         => __('Your PO/Order Ref. No.'),
        'placeholder'   => __('Please enter a unique order reference'),
        'required'      => true,
    ), $checkout->get_value('po_order_ref_no'));
}

// Add the other two fields at the bottom of billing form
add_action('woocommerce_after_checkout_billing_form', 'custom_add_bottom_checkout_fields');
function custom_add_bottom_checkout_fields($checkout) {
    // VAT / TAX No. - Optional field
    woocommerce_form_field('vat_tax_no', array(
        'type'          => 'text',
        'class'         => array('form-row-wide'),
        'label'         => __('VAT / TAX No.'),
        'placeholder'   => __('Please enter a VAT Tax number'),
        'required'      => false,
    ), $checkout->get_value('vat_tax_no'));

    // How did you find us?
    woocommerce_form_field('how_did_you_find_us', array(
        'type'          => 'select',
        'class'         => array('form-row-wide'),
        'label'         => __('How did you find us?'),
        'required'      => false,
        'options'       => array(
            ''                      => __('Please select'),
            'search_engine'         => __('Search Engine'),
            'google_ads'            => __('Google Ads'),
            'facebook_ads'          => __('Facebook Ads'),
            'facebook_post_group'   => __('Facebook post / group'),
            'youtube_ads'           => __('YouTube Ads'),
            'other_social_ads'      => __('Other social media advertising'),
            'twitter_post'          => __('Twitter post'),
            'email'                 => __('Email'),
            'word_of_mouth'         => __('Word of Mouth'),
            'other'                 => __('Other'),
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
    if ($job_title) {
        echo '<p><strong>' . __('Job Title') . ':</strong> ' . esc_html($job_title) . '</p>';
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
    $job_title = get_post_meta($order->get_id(), '_billing_job_title', true);
    $find_us = get_post_meta($order->get_id(), '_how_did_you_find_us', true);
    if ($po_ref || $vat_no || $job_title || $find_us) {
        echo '<br>';
        echo '<table class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">';
        echo '<tbody>';
        if ($po_ref) {
            echo '<tr>';
            echo '<th><strong>' . __('PO/Order Ref. No.') . ':</strong></th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html($po_ref);
            echo '</td>';
            echo '</tr>';
        }
        if ($vat_no) {
            echo '<tr>';
            echo '<th><strong>' . __('VAT / TAX No.') . ':</strong></th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html($vat_no);
            echo '</td>';
            echo '</tr>';
        }
        if ($job_title) {
            echo '<tr>';
            echo '<th><strong>' . __('Job Title') . ':</strong></th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html($job_title);
            echo '</td>';
            echo '</tr>';
        }
        if ($find_us) {
            echo '<tr>';
            echo '<th><strong>' . __('How did you find us?') . ':</strong></th>';
            echo '<td class="woocommerce-table__product-table product-total">';
            echo esc_html(ucwords(str_replace('_', ' ', $find_us)));
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
    $job_title = get_post_meta($order->get_id(), '_billing_job_title', true);

    if ($plain_text) {
        // Plain text version (already PO first)
        if ($po_ref) {
            echo "\nPO/Order Ref. No.: " . $po_ref;
        }
        if ($vat_no) {
            echo "\nVAT / TAX No.: " . $vat_no;
        }
        if ($job_title) {
            echo "\nJob Title: " . $job_title;
        }
    } else {
        // HTML version - now PO first for consistency
        if ($po_ref) {
            echo '<h2 style="font-size: 20px; line-height: 22px; font-weight: bold; margin: 0 0 18px; text-align: left; color: #ef9003; margin: 0;"><strong>Customer PO:</strong> ' . esc_html($po_ref) . '</h2>';
        }
        if ($vat_no) {
            echo '<h2 style="font-size: 20px; line-height: 22px; font-weight: bold; margin: 0 0 18px; text-align: left; color: #ef9003; margin: 0;"><strong>VAT/TAX No.:</strong> ' . esc_html($vat_no) . '</h2>';
        }
        if ($job_title) {
            echo '<h2 style="font-size: 20px; line-height: 22px; font-weight: bold; margin: 0 0 18px; text-align: left; color: #ef9003; margin: 0;"><strong>Job Title:</strong> ' . esc_html($job_title) . '</h2>';
        }
    }
}
    */