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

        global $product;
        if ( $product instanceof WC_Product ) {

            $product_id   = $product->get_id();
            $backorders   = $product->get_backorders(); // 'no', 'notify', or 'yes'
            $stock_status = $product->get_stock_status();

            $no_cofc_ss = function_exists('get_field') ? get_field('no_cofc', $product->get_id()) : '';
            $is_cofc_disabled_ss = ($no_cofc_ss === 'yes');
            $cofc_disabled_attr_ss = $is_cofc_disabled_ss ? 'disabled="disabled"' : '';
            $cofc_wrapper_class_ss = $is_cofc_disabled_ss ? 'cofc-disabled' : '';

            $no_fair_ss = function_exists('get_field') ? get_field('no_fair', $product->get_id()) : '';
            $is_fair_disabled_ss = ($no_fair_ss === 'yes');
            $fair_disabled_attr_ss = $is_fair_disabled_ss ? 'disabled="disabled"' : '';
            $fair_wrapper_class_ss = $is_fair_disabled_ss ? 'fair-disabled' : '';

            $no_mcofc_ss = function_exists('get_field') ? get_field('no_mcofc', $product->get_id()) : '';
            $is_mcofc_disabled_ss = ($no_mcofc_ss === 'yes');
            $mcofc_disabled_attr_ss = $is_mcofc_disabled_ss ? 'disabled="disabled"' : '';
            $mcofc_wrapper_class_ss = $is_mcofc_disabled_ss ? 'mcofc-disabled' : '';

        }
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

                    
                    <?php if($stock_status === "onbackorder"){ ?>
                        <input type="text" id="delivery_date_backorder" autocomplete="off" class="datepicker delivery-options-modal__form-field" name="despatch_date" value="" placeholder="dd/mm/yyyy">
                    <?php } else { ?>
                        <input type="text" id="delivery_date" autocomplete="off" class="datepicker delivery-options-modal__form-field" name="despatch_date" value="" placeholder="dd/mm/yyyy">
                    <?php } ?>
                    
                    <p class="delivery-options-modal__small-text">
                        Please set up your deliveries in order of date; you cannot create deliveries prior to an existing date.
                    </p>

                    <label class="delivery-options-modal__label">Total number of parts</label>
                    <input type="number" class="delivery-options-modal__form-field parts-input" name="shipment_parts" value="" placeholder="Enter a number ≥ 1" min="1">


                    <!-- COFC FAIR CONTENT -->
                    <div id="cofc_hide_show_2" class="product-page__optional-fees-credit-account"><div class="product-page__optional-fees">

                        <h4 class="product-page__optional-fees-title">Do you require these addons with your product?</h4>

                        <div class="product-page__checkbox-label  <?php echo $cofc_wrapper_class_ss; ?>">
                            <p class="product-page__checkbox-title">Add Manufacturers COFC</p>

                            <?php
                                if($no_cofc_ss === 'yes'){
                                    echo '<p class="product-page__cofc-not-available">Not available for this product</p>';
                                }
                            ?>

                            <input type="checkbox" name="add_manufacturers_COFC_ss" value="10" id="add_manufacturers_COFC_ss" class="styled-checkbox-cofc" <?php echo $cofc_disabled_attr_ss; ?>>
                            <label style="display: block;" for="add_manufacturers_COFC_ss" class="product-page__checkbox-heading">Manufacturers COFC <span class="product-page__checkbox-price">£10</span>
                                <span class="cfc__tooltip" data-tooltip="A Manufacturers Certificate of Conformity (MCOFC) is a document that manufacturers issue to confirm that a product has been made to a specific standard and meets quality and regulatory requirements.">?</span>
                            </label>
                        </div><br>

                        <div id="fair_label_credit_account" class="product-page__checkbox-label <?php echo $fair_wrapper_class_ss; ?>">
                            <p class="product-page__checkbox-title">Add First Article Inspection Report</p>
                            <?php
                                if($no_fair_ss === 'yes'){
                                    echo '<p class="product-page__cofc-not-available">Not available for this product</p>';
                                }
                            ?>
                            <input type="checkbox" name="add_fair_ss" value="95" id="add_fair_ss" class="styled-checkbox-cofc" <?php echo $fair_disabled_attr_ss; ?>>
                            <label style="display: block;" for="add_fair_ss" class="product-page__checkbox-heading">FAIR <span class="product-page__checkbox-price">£95</span>
                                <span class="cfc__tooltip" data-tooltip="A First Article Inspection Report (FAIR) or ISIR is the first item we make for the customer and measure to confirm all dimensions meet the drawing and tolerances.">?</span>
                            </label>
                        </div><br>

                        <div class="product-page__checkbox-label <?php echo $mcofc_wrapper_class_ss; ?>">
                            <p class="product-page__checkbox-title">Add Materials Direct COFC?</p>
                             <?php
                                if($no_mcofc_ss === 'yes'){
                                    echo '<p class="product-page__cofc-not-available">Not available for this product</p>';
                                }
                            ?>
                            <input type="checkbox" name="add_materials_direct_COFC_ss" value="12.50" id="add_materials_direct_COFC_ss" class="styled-checkbox-cofc" <?php echo $mcofc_disabled_attr_ss; ?>>
                            <label style="display: block;" for="add_materials_direct_COFC_ss" class="product-page__checkbox-heading">Materials Direct COFC <span class="product-page__checkbox-price">£12.50</span>
                                <span class="cfc__tooltip" data-tooltip="A certificate from Materials Direct confirming that the part meets the criteria ordered (RoHS and REACH compliant).">?</span>
                            </label>
                        </div>

                    </div></div>
                    <!-- COFC FAIR CONTENT -->


                    <input type="submit" value="Add Despatch Date" class="delivery-options-modal__submit">


                </div>
            </div>
        </div>
        <?php
    }
}
add_action('wp_footer', 'add_modal_to_product_page_footer');
// ADD DELIVERY OPTIONS MODAL TO THE PRODUCT PAGE