<?php

// ADD DELIVERY OPTIONS MODAL TO THE PRODUCT PAGE
function add_modal_to_product_page_footer() {
    if (!is_product()) {
        return;
    }
    $user_id = get_current_user_id();
    $allow_credit = get_field('credit_options_allow_user_credit_option', 'user_' . $user_id);
    if (is_user_logged_in() && $allow_credit && !is_admin()) {
        $custom_qty = WC()->session->get('custom_qty', 0); // Get custom_qty from session
        $shipments = WC()->session->get('custom_shipments', []); // Get existing shipments
        $total_parts = array_sum(array_column($shipments, 'parts')); // Sum of parts in shipments
        $remaining_parts = max(0, $custom_qty - $total_parts); // Calculate remaining parts
        ?>
        <div class="delivery-options-modal__outer" style="display: none;"> 
            <div class="delivery-options-modal">
                <div class="delivery-options-modal__header">
                    <h4 class="delivery-options-modal__title">Add Shipment</h4>
                    <a class="delivery-options-modal__close-btn" href="#"><i class="fa-solid fa-xmark delivery-options-modal__icon-close"></i></a>
                </div>
                <div class="delivery-options-modal__content">
                    <p class="delivery-options-modal__info">You have <span id="remaining-parts"><?php echo esc_html($remaining_parts); ?></span> parts remaining to set delivery date(s) for.</p>
                    
                    <label class="delivery-options-modal__label">Despatch Date</label>
                    <input type="text" class="datepicker delivery-options-modal__form-field" name="despatch_date" value="" placeholder="dd/mm/yyyy">
                    
                    <p class="delivery-options-modal__small-text">
                        Please set up your deliveries in order of date; you cannot create deliveries prior to an existing date.
                    </p>

                    <label class="delivery-options-modal__label">Total number of parts</label>
                    <input type="number" class="delivery-options-modal__form-field parts-input" name="shipment_parts" value="" placeholder="Enter a number ≥ 1" min="1">
                    
                    <input type="submit" value="Add Despatch Date" class="delivery-options-modal__submit">
                </div>
            </div>
        </div>
        <?php
    }
}
add_action('wp_footer', 'add_modal_to_product_page_footer');
// ADD DELIVERY OPTIONS MODAL TO THE PRODUCT PAGE