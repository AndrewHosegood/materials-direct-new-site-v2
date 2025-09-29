jQuery(document).ready(function($) {
    // Check if the product is single (via data attribute or AJAX)
    const isProductSingle = $('input[name="is_product_single"]').val() === '1';

    // Common function to validate shipping address
    function validateShippingAddress() {
        const street_address = $('#input_street_address').val().trim();
        const city = $('#input_city').val().trim();
        const county_state = $('#input_county_state').val().trim();
        const zip_postal = $('#input_zip_postal').val().trim();
        const country = $('#input_country').val();
        
        if (!street_address || !city || !county_state || !zip_postal || !country) {
            $('#custom_price_display').html('Please fill in all required shipping address fields.');
            return false;
        }
        return {
            street_address,
            address_line2: $('#input_address_line2').val().trim(),
            city,
            county_state,
            zip_postal,
            country
        };
    }
    
    // Generate Price Button (only for non-single products)
    if (!isProductSingle) {

        $('#generate_price').on('click', function() {

            const width = parseFloat($('#input_width').val());
            const length = parseFloat($('#input_length').val());
            const qty = parseInt($('#input_qty').val());
            const discount_rate = parseFloat($('#input_discount_rate').val());
            const shipping_address = validateShippingAddress();

            if (!shipping_address) return;

            // Client-side validation for price calculation
            if (isNaN(width) || isNaN(length) || isNaN(qty) || width <= 0 || length <= 0 || qty < 1) {
                $('#custom_price_display').html('Please enter valid positive Width, Length, and Quantity.');
                return;
            }

            // Validate discount rate
            const valid_discount_rates = [0, 0.015, 0.02, 0.025, 0.03, 0.035, 0.04, 0.05];
            if (!valid_discount_rates.includes(discount_rate)) {
                $('#custom_price_display').html('Please select a valid delivery time.');
                return;
            }

            $('#price-spinner-overlay').fadeIn(200);

            $.ajax({
                url: ajax_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'calculate_secure_price',
                    product_id: ajax_params.product_id,
                    width: width,
                    length: length,
                    qty: qty,
                    discount_rate: discount_rate,
                    nonce: ajax_params.nonce,
                    ...shipping_address
                },
                success: function(response) {
                    $('#price-spinner-overlay').fadeOut(200);
                    if (response.success) {
                        const price = response.data.price;
                        const adjustedPrice = response.data.per_part;
                        const sheetsRequired = response.data.sheets_required || 1;
                        const isBackorder = response.data.is_backorder || false;
                        const sheet_width_mm = response.data.sheet_width_mm;
                        const sheet_length_mm = response.data.sheet_length_mm;
                        const border = 2;
                        const stock_quantity = response.data.stock_quantity;
                        const qty = response.data.entered_quantity;
                        const backorder_adjustedPrice = adjustedPrice * 0.05;
                        const discount_rate = response.data.discount_rate;
                        

                        // Compile the schedules using discounr_rate
                        let discount_display;
                        if(discount_rate === 0) {
                            discount_display = "24Hrs (working day)";
                        } 
                        else if(discount_rate === 0.015){
                            discount_display = "48Hrs (working day) (1.5% Discount)";
                        }
                        else if(discount_rate === 0.02) {
                            discount_display = "5 Days (working day) (2% Discount)";
                        }
                        else if(discount_rate === 0.025) {
                            discount_display = "7 Days (working day) (2.5% Discount)";
                        }
                        else if(discount_rate === 0.03) {
                            discount_display = "12 Days (working day) (3% Discount)";
                        }
                        else if(discount_rate === 0.035) {
                            discount_display = "14 Days (working day) (3.5% Discount)";
                        }
                        else if(discount_rate === 0.04) {
                            discount_display = "30 Days (working day) (4% Discount)";
                        }
                        else {
                            discount_display = "35 Days (working day) (5% Discount)";
                        }


                        // Calculate price per sheet for the hidden field
                        let cart_price = price / sheetsRequired;

                        // Initialize variables for partial backorder
                        let sheets_backorder = 0;
                        let total_parts_d;
                        let parts_from_stock;
                        let able_to_dispatch;
                        let parts_backorder;
                        let backorder_adjustedPriceDisplay;
                        let calcPartialbackorderdiscount_1;
                        let calcPartialbackorderdiscount_2;
                        let calcPartialbackorderFinal;

                        // Calculate partial backorder figures
                        const v1 = width + (2 * border);
                        const v2 = length + (2 * border);
                        const usable_width = sheet_width_mm;
                        const usable_length = sheet_length_mm;
                        const parts_per_row = Math.floor(usable_width / v1);
                        const parts_per_column = Math.floor(usable_length / v2);
                        const parts_per_sheet = parts_per_row * parts_per_column;
                        


                        let priceHtml = '';

                        if (isBackorder && stock_quantity > 0) {

                            if (parts_per_sheet <= 0) {
                                $('#custom_price_display').html('Error: Invalid sheet calculation. Part does not fit on sheet.');
                                return;
                            }

                            sheets_backorder = sheetsRequired - stock_quantity;
                            total_parts_d = qty;
                            parts_from_stock = stock_quantity * parts_per_sheet;
                            able_to_dispatch = Math.min(parts_from_stock, total_parts_d);
                            parts_backorder = total_parts_d - able_to_dispatch;
                            backorder_adjustedPriceDisplay = adjustedPrice - backorder_adjustedPrice;
                            calcPartialbackorderdiscount_1 = able_to_dispatch * adjustedPrice;
                            calcPartialbackorderdiscount_2 = parts_backorder * backorder_adjustedPriceDisplay;
                            calcPartialbackorderFinal = (calcPartialbackorderdiscount_1 + calcPartialbackorderdiscount_2).toFixed(2);
                            cart_price = calcPartialbackorderFinal / sheetsRequired; //new

                            priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">1. Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per part: <span class="product-page__display-price-text">£' + adjustedPrice.toFixed(2) + '<span style="font-size: 0.82rem; font-weight: 400;"> (£' + backorder_adjustedPriceDisplay.toFixed(2) + ' for backorder parts)</span></span></div><div class="product-page__display-price">Total part costs: <span class="product-page__display-price-text">£' + calcPartialbackorderFinal + '</span></div></div></div>';
                            priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Notice:</strong> This order exceeds current stock, it requires an additional ' + sheets_backorder + ' sheets (' + parts_backorder + ' parts) to be back ordered. We are able to despatch: ' + able_to_dispatch + ' parts within ' + discount_display + '. Please allow 35 Days to complete the back ordered items. A 5% discount will apply to these parts.</p></div>';

                        } else if (isBackorder && stock_quantity <= 0) {
                            // Full backorder case (stock_quantity <= 0)
                            priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">1. Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per part: <span class="product-page__display-price-text">£' + adjustedPrice.toFixed(2) + '</span></div><div class="product-page__display-price">Total part costs: <span class="product-page__display-price-text">£' + price.toFixed(2) + '</span></div></div></div>';
                            priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Notice:</strong> This order is currently on backorder only. Please allow 35 Days for complete order fulfillment with a 5% discount applied to the total order.</p></div>';
                        } else {
                            // No backorder case
                            priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">2. Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per part: <span class="product-page__display-price-text">£' + adjustedPrice.toFixed(2) + '</span></div><div class="product-page__display-price">Total part costs: <span class="product-page__display-price-text">£' + price.toFixed(2) + '</span></div></div></div>';
                        }


                        
                        // Temp for degugging
                        /*
                        if (isBackorder) {
                            priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Debug:</strong> Usable Width: '+usable_width+'<br>Usable Length: '+usable_length+'<br>Parts Per Row: '+parts_per_row+'<br>Parts Per Column: '+parts_per_column+'<br>Parts Per Sheet: '+parts_per_sheet+'<br>Sheets Backorder: '+sheets_backorder+'<br>Total Parts D: '+total_parts_d+'<br>Parts From Stock: '+parts_from_stock+'<br>Able To Dispatch: '+able_to_dispatch+'<br>Parts Backorder: '+parts_backorder+'<br>Adjusted Price: '+adjustedPrice+'<br>Backorder Adjusted Price: '+backorder_adjustedPrice+'<br>Calc 1: '+calcPartialbackorderdiscount_1+'<br>Calc 2: '+calcPartialbackorderdiscount_2+'<br>Total part costs: '+calcPartialbackorderFinal+'<br>Is Backorder: '+isBackorder+'<br></p></div>';
                        }
                        priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Debug:</strong> Parts Per Sheet: '+parts_per_sheet+'<br>Sheets Required: '+sheetsRequired+'<br>Sheet Width: '+sheet_width_mm+'<br>Usable Width: '+usable_width+'<br>Parts per row: '+parts_per_row+'<br>Discount Rate: '+discount_rate+'<br>Discount Display: '+discount_display+'</p></div>';
                        */
                        // Temp for degugging




                        // DISPLAY PRICE AND PART COST ON PRODUCT PAGE AFTER CLICKING 'CALCULATE PRICE'
                        $('#custom_price_display').html(priceHtml);

                        // ADD PRICE TO HIDDEN FIELD THAT IS THEN PASSED TO CART
                        $('#custom_price').val(cart_price);

                        /* new */
                        if (isBackorder && stock_quantity > 0) {
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'custom_backorder_total',
                                value: calcPartialbackorderFinal
                            }).appendTo('form.cart');
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'custom_parts_backorder',
                                value: parts_backorder
                            }).appendTo('form.cart');
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'custom_able_to_dispatch',
                                value: able_to_dispatch
                            }).appendTo('form.cart');
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'custom_parts_per_sheet',
                                value: parts_per_sheet
                            }).appendTo('form.cart');
                        }
                        /* new */
                        $('input[name="quantity"]').val(sheetsRequired); 
                        
                    } else {
                        $('#custom_price_display').html('Error: ' + (response.data.message || 'Unable to calculate price.'));
                    }
                },
                error: function() {
                    $('#price-spinner-overlay').fadeOut(200);
                    $('#custom_price_display').html('Error: Server error.');
                }
            });
        });

        // Validate shipping address on Add to Cart form submission
        $('.woocommerce-cart-form, form.cart').on('submit', function(e) {
            const price = $('#custom_price').val(); 
            if (!price) {
                e.preventDefault();
                $('#custom_price_display').html('Please generate a price before adding to cart.');
                return false;
            }

            const shipping_address = validateShippingAddress();
            if (!shipping_address) {
                e.preventDefault();
                return false;
            }

        });
    } else {
        // For single products, display the default price immediately
        $.ajax({
            url: ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'calculate_secure_price',
                product_id: ajax_params.product_id,
                nonce: ajax_params.nonce
            },
            success: function(response) {
                if (response.success) {
                    const price = response.data.price;
                    $('#custom_price_display').html('<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">Product Price</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Price: <span class="product-page__display-price-text">£' + price.toFixed(2) + '</span></div></div></div>');
                    $('#custom_price').val(price);
                    $('input[name="quantity"]').val(1); // Default quantity
                } else {
                    $('#custom_price_display').html('Error: ' + (response.data.message || 'Unable to fetch price.'));
                }
            },
            error: function() {
                $('#custom_price_display').html('Error: Server error.');
            }
        });

        // Validate and save shipping address on Add to Cart for single products
        $('.woocommerce-cart-form, form.cart').on('submit', function(e) {
            const price = $('#custom_price').val();
            if (!price) {
                e.preventDefault();
                $('#custom_price_display').html('Please generate a price before adding to cart.');
                return false;
            }

            const shipping_address = validateShippingAddress();
            if (!shipping_address) {
                e.preventDefault();
                return false;
            }

            // Send shipping address to server via AJAX before adding to cart
            $.ajax({
                url: ajax_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'save_single_product_shipping',
                    product_id: ajax_params.product_id,
                    nonce: ajax_params.nonce,
                    ...shipping_address
                },
                success: function(response) {
                    if (!response.success) {
                        e.preventDefault();
                        $('#custom_price_display').html('Error: ' + (response.data.message || 'Unable to save shipping address.'));
                    }
                    // Form submission will proceed if AJAX is successful
                },
                error: function() {
                    e.preventDefault();
                    $('#custom_price_display').html('Error: Server error while saving shipping address.');
                }
            });
        });

    }
});