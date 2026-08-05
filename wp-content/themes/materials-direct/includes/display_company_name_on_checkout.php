<?php
add_filter( 'woocommerce_billing_fields', 'restore_billing_company_field' );
function restore_billing_company_field( $fields ) {
    // Make sure the company field exists and is visible
    $fields['billing_company'] = array(
        'type'        => 'text',
        'label'       => __( 'Company name', 'woocommerce' ),
        'placeholder' => _x( 'Company (optional)', 'placeholder', 'woocommerce' ),
        'required'    => false,
        'class'       => array( 'form-row-wide' ),
        'clear'       => true,
        'priority'    => 30,  // Position after last name
    );
    return $fields;
}