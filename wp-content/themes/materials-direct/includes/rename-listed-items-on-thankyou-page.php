<?php
add_filter('woocommerce_order_item_get_formatted_meta_data', 'remove_specific_order_item_meta', 10, 2);

function remove_specific_order_item_meta($formatted_meta, $item) {
    // List of meta keys you want to remove
    $meta_keys_to_remove = ['sheets_required', 'parts_backorder', 'able_to_dispatch', 'parts_per_sheet'];
    
    // Loop through each formatted meta item
    foreach ($formatted_meta as $key => $meta) {
        // Check if the meta key is in our removal list
        if (in_array($meta->key, $meta_keys_to_remove)) {
            unset($formatted_meta[$key]); // Remove it from the list
        }
        if ($meta->key === 'shape_type') {
            $formatted_meta[$key]->display_key = 'Shape Type'; // Change the label
            $formatted_meta[$key]->display_value = ucwords(str_replace('-', ' ', $meta->value));
        }
        if ($meta->key === 'despatch_notes') {
            $formatted_meta[$key]->display_key = 'Despatch Notes'; // Change the label
        }
		if ($meta->key === 'total_del_weight') {
            $formatted_meta[$key]->display_key = 'Customer Shipping Weight(s)'; // Change the label
        }
		if ($meta->key === 'width') {
            $formatted_meta[$key]->display_key = 'Width (MM)'; // Change the label
        }
        if ($meta->key === 'roll_length') {
            $formatted_meta[$key]->display_key = 'Roll Length'; // Change the label
        }
		if ($meta->key === 'length') {
            $formatted_meta[$key]->display_key = 'Length (MM)'; // Change the label
        }
		if ($meta->key === 'qty') {
            $formatted_meta[$key]->display_key = 'Total number of parts'; // Change the label
        }
        if ($meta->key === 'ah_shipping_cost') {
            $formatted_meta[$key]->display_key = 'Shipping Cost'; // Change the label
        }
    }
    
    return $formatted_meta; // Return the modified meta data
}