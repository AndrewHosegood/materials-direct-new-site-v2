<?php
function add_creditor_user_role() {
    // Get the 'customer' role object
    $customer_role = get_role('customer');

    // Only proceed if 'customer' role exists
    if ($customer_role) {
        // Optional: remove 'creditor' if it already exists
        if (get_role('creditor')) {
            remove_role('creditor');
        }

        // Add new 'creditor' role with same capabilities
        add_role('creditor', 'Creditor', $customer_role->capabilities);
    }
}
add_action('init', 'add_creditor_user_role');