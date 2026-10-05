jQuery(document).ready(function($) {

    console.log("Algorithm Triggered");

    // Global fresh nonce storage
    window.customPriceNonce = null;
    /**
     * Fetch a fresh nonce from the server.
     * Returns a Promise that resolves with the nonce string.
     */
    function getFreshNonce() {
        return new Promise(function(resolve, reject) {
            // If we already have one, reuse it
            if (window.customPriceNonce) {
                resolve(window.customPriceNonce);
                return;
            }

            $.ajax({
                url: ajax_params.ajax_url,
                type: 'POST',
                cache: false,
                data: {
                    action: 'get_custom_price_nonce'
                },
                success: function(response) {
                    if (response.success && response.data.nonce) {
                        window.customPriceNonce = response.data.nonce;
                        resolve(window.customPriceNonce);
                    } else {
                        reject('Could not retrieve security token');
                    }
                },
                error: function() {
                    reject('Network error while retrieving security token');
                }
            });
        });
    }
    // End global fresh nonce storage

    // Delete Temporary PDF
    function deleteTemporaryPdf(pdfPath) {
        if (!pdfPath) return;

        function runDelete(isRetry = false) {
            getFreshNonce()
                .then(function(nonce) {
                    return $.ajax({
                        url: ajax_params.ajax_url,
                        type: 'POST',
                        cache: false,
                        data: {
                            action: 'delete_temp_pdf',
                            pdf_path: pdfPath,
                            nonce: nonce
                        }
                    });
                })
                .then(function(response) {
                    if (response.success) {
                        $('#pdf_path').data('last-uploaded-path', '');
                    } else {
                        console.log('Failed to delete temporary PDF: ' + (response.data.message || 'Unknown error'));
                    }
                })
                .catch(function(error) {
                    // Safety net – retry once on 403
                    if (!isRetry && error && error.status === 403) {
                        console.warn('delete_temp_pdf received 403 – clearing nonce and retrying once...');
                        window.customPriceNonce = null;
                        runDelete(true);
                        return;
                    }

                    console.log('Server error while deleting temporary PDF.');
                    window.customPriceNonce = null;
                });
        }

        runDelete(false);
    }
    // End delete Temporary PDF

    //back button fix for PDF/DXF Uploads
    window.addEventListener('pageshow', function () {

        const pdfInput = $('#uploadPdf')[0];
        //const dxfInput = $('#uploadDxf')[0];

        // If the browser has restored a file selection but our hidden
        // AJAX path has been lost, clear the stale browser state.
        if (pdfInput &&
            pdfInput.files.length > 0 &&
            $('#pdf_path').val() === '') {

            $('#uploadPdf').val('');
            $('#uploadDxf').val('');

            $('#pdf_path').val('');
            $('#dxf_path').val('');

            disableButtons();
        }

    });
    //end back button fix for PDF/DXF Uploads

    // Check if the product is single (via data attribute or AJAX)
    const isProductSingle = $('input[name="is_product_single"]').val() === '1';
    const allowCredit = $('input[name="allow_credit"]').val() === '1';


    // Disable calculate/add shipments buttons initially if not single product
    if (!isProductSingle) {
        if (allowCredit) {
            $('#add_shipments').prop('disabled', true);
        } else {
            $('#generate_price').prop('disabled', true);
        }
    }


    // Function to toggle PDF upload field visibility, reset price, and clear PDF
    function togglePdfValidation() {
        const selectedTab = $('input[name="tabs_input"]:checked').val();
        if (selectedTab === 'square-rectangle') {
            $('#pdf_upload_container').addClass('hidden');
            $('#pdf_path').val('');
            $('#custom_price_display').html('');
            $('#uploadPdf').val('');
            $('#input_width').val('');
            $('#input_length').val('');
            $('#input_qty').val('');
            $('#tabs_status_message').html('Square Rectange');
            $('.product-page__rolls-label-text-1').text('Length (MM):');
            $('.product-page__rolls-label-text-2').text('Total number of parts:');
            $('#fair_label').hide();
            $('#fair_label_credit_account').hide();
            $('#input_width, #input_length, #input_qty, #input_radius').prop('readonly', false);
            //$('#cont_width_mm').show();
            //$('#cont_length_mm').show();
            //$('.product-page__input-wrap.part-qty').show();
            $('.product-page__rolls-link').hide();
            enableButtons();

            // Delete temporary PDF file from server
            const pdfPath = $('#pdf_path').data('last-uploaded-path') || '';
            deleteTemporaryPdf(pdfPath);
        }
        else if (selectedTab === 'circle-radius') {
            $('#pdf_upload_container').addClass('hidden');
            $('#pdf_path').val('');
            $('#custom_price_display').html('');
            $('#uploadPdf').val('');
            $('#uploadDxf').val('');
            $('#input_width').val('');
            $('#input_length').val('');
            $('#input_qty').val('');
            $('#tabs_status_message').html('Circle Radius');
            $('.product-page__rolls-label-text-1').text('Length (MM):');
            $('.product-page__rolls-label-text-2').text('Total number of parts:');
            $('#fair_label').hide();
            $('#fair_label_credit_account').hide();
            $('#input_width, #input_length, #input_qty, #input_radius').prop('readonly', false);
            //$('#cont_width_mm').hide();
            //$('#cont_length_mm').hide();
            //$('.product-page__input-wrap.part-qty').show();
            $('.product-page__rolls-link').hide();
            enableButtons();

            // Delete temporary PDF file from server
            const pdfPath = $('#pdf_path').data('last-uploaded-path') || '';
            deleteTemporaryPdf(pdfPath);
        }
        else if (selectedTab === 'stock-sheets') {
            $('#pdf_upload_container').addClass('hidden');
            $('#pdf_path').val('');
            $('#custom_price_display').html('');
            $('#uploadPdf').val('');
            $('#input_width').val('');
            $('#input_length').val('');
            $('#input_qty').val('');
            $('#tabs_status_message').html('Stock Sheet');
            $('.product-page__rolls-label-text-1').text('Length (MM):');
            $('.product-page__rolls-label-text-2').text('Total number of parts:');
            $('#fair_label').hide();
            $('#fair_label_credit_account').hide();
            $('#input_width, #input_length, #input_qty, #input_radius').prop('readonly', false);
            $('.product-page__rolls-label-text-2').text('Quantity of sheets:');
            //$('#cont_width_mm').show();
            //$('#cont_length_mm').show();
            //$('.product-page__input-wrap.part-qty').show();
            $('.product-page__rolls-link').hide();
            enableButtons();

            // Delete temporary PDF file from server
            const pdfPath = $('#pdf_path').data('last-uploaded-path') || '';
            deleteTemporaryPdf(pdfPath);
        }
        else if (selectedTab === 'rolls') {
            $('#tabs_status_message').html('Roll');
            $('.product-page__rolls-label-text-1').text('Length (Metres):');
            // $('.product-page__rolls-label-text-2').text('Quantity of rolls:');
            //$('#cont_width_mm').hide();
            //$('#cont_length_mm').hide();
            //$('.product-page__input-wrap.part-qty').hide();
            $('#fair_label').hide();
            $('#fair_label_credit_account').hide();
            $('.product-page__rolls-link').show();
            $('#input_width, #input_length, #input_qty, #input_radius').prop('readonly', false);
        }
        else {
            $('#pdf_upload_container').removeClass('hidden');
            $('#custom_price_display').html('');
            $('#input_width').val('');
            $('#input_length').val('');
            $('#input_qty').val('');
            $('#tabs_status_message').html('Custom Shape');
            if (!$('#pdf_path').val().trim()) {
                disableButtons();
            }
            $('.product-page__rolls-label-text-1').text('Length (MM):');
            $('.product-page__rolls-label-text-2').text('Total number of parts:');
            $('#fair_label').show();
            $('#fair_label_credit_account').show();
            //$('.product-page__input-wrap.part-qty').show();
            $('.product-page__rolls-link').hide();
        }
    }

    // Initialize PDF validation state on page load
    togglePdfValidation();

    // Handle tab changes
    $('input[name="tabs_input"]').on('change', function() {
        togglePdfValidation();
    });



    // File upload handlers

    $('#uploadPdf').on('change', function() {
        if (this.files.length > 0) {
            uploadFile(this, 'pdf');
        } else {
            $('#pdf_path').val('');
            if ($('input[name="tabs_input"]:checked').val() === 'custom-shape-drawing') {
                disableButtons();
            }
        }
    });


    $('#uploadDxf').on('change', function() {
        if (this.files.length > 0) {
            uploadFile(this, 'dxf');
        } else {
            $('#dxf_path').val('');
        }
    });



    // Function to upload file automatically
    function uploadFile(input, type) {
    $('#price-spinner-overlay').fadeIn(200);

            function runUpload(isRetry = false) {
                getFreshNonce()
                    .then(function(nonce) {
                        var formData = new FormData();
                        formData.append('file', input.files[0]);
                        formData.append('action', 'upload_drawing');
                        formData.append('type', type);
                        formData.append('nonce', nonce);   // ← fresh nonce

                        return $.ajax({
                            url: ajax_params.ajax_url,
                            type: 'POST',
                            cache: false,
                            data: formData,
                            processData: false,
                            contentType: false
                        });
                    })
                    .then(function(response) {
                        $('#price-spinner-overlay').fadeOut(200);

                        if (response.success) {
                            $('#' + type + '_path').val(response.data.path);

                            if (type === 'pdf') {
                                $('#pdf_path').data('last-uploaded-path', response.data.path);
                                if ($('input[name="tabs_input"]:checked').val() === 'custom-shape-drawing') {
                                    enableButtons();
                                }
                            }
                        } else {
                            alert('Upload failed: ' + (response.data.message || 'Unknown error.'));
                            $('#' + type + '_path').val('');
                            if (type === 'pdf' && $('input[name="tabs_input"]:checked').val() === 'custom-shape-drawing') {
                                disableButtons();
                            }
                        }
                    })
                    .catch(function(error) {
                        // ===== SAFETY NET =====
                        if (!isRetry && error && error.status === 403) {
                            console.warn('Upload received 403 – clearing nonce and retrying once...');
                            window.customPriceNonce = null;
                            runUpload(true); // retry once
                            return;
                        }

                        // Final failure
                        $('#price-spinner-overlay').fadeOut(200);
                        alert('Server error during upload.');
                        $('#' + type + '_path').val('');
                        if (type === 'pdf' && $('input[name="tabs_input"]:checked').val() === 'custom-shape-drawing') {
                            disableButtons();
                        }
                        window.customPriceNonce = null;
                    });
            }

            // Start the first attempt
            runUpload(false);
    }


    // Helper to disable buttons
    function disableButtons() {
        if (allowCredit) {
            $('#add_shipments').prop('disabled', true);
        } else {
            $('#generate_price').prop('disabled', true);
        }
    }

    // Helper to enable buttons
    function enableButtons() {
        if (allowCredit) {
            $('#add_shipments').prop('disabled', false);
        } else {
            $('#generate_price').prop('disabled', false);
        }
    }

    // Common function to validate shipping address
    function validateShippingAddress() {
        const street_address = $('#input_street_address').val().trim();
        const city = $('#input_city').val().trim();
        const county_state = $('#input_county_state').val().trim();
        const zip_postal = $('#input_zip_postal').val().trim();
        const country = $('#input_country').val();
        
        return {
            street_address,
            address_line2: $('#input_address_line2').val().trim(),
            city,
            county_state,
            zip_postal,
            country
        };
    }



// New code for dynamic jquery picker

// NEW: Helper to get last shipment date (parses from table, returns Date or null)
/* GETS THE LAST SHIPMENT DATE ENTERED IN THE MODAL POPUP */
function getLastShipmentDate() {
    const tbodyRows = $('.delivery-options-shipment tbody tr');
    for (let i = tbodyRows.length - 1; i >= 0; i--) { // Scan backward for last real row
        const row = $(tbodyRows[i]);
        if (!row.hasClass('delivery-options-shipment__display')) { // Skip "no shipments" row
            const firstTdText = row.find('td:first').text().trim(); // e.g., "26/11/2025 24Hrs (working day)"
            const dateMatch = firstTdText.match(/(\d{2}\/\d{2}\/\d{4})/); // Extract dd/mm/yyyy
            if (dateMatch) {
                const [dd, mm, yyyy] = dateMatch[1].split('/').map(Number);
                return new Date(yyyy, mm - 1, dd); // JS Date: month 0-indexed
            }
        }
    }
    return null; // No shipments
}


// Helper to get base minDate (1 for normal, 36 for backorder)
/* IF THE PRODUCT IS INSTOCK WE CAN CHOOSE NEXT DAY IN THE CALENDAR  */
/* IF THE PRODUCT IS ON BACKORDER WE CAN ONLY CHOOSE A DAY THAT IS 35 DAYS IN ADVANCE  */
function getBaseMinDate() {
    const inputSelector = 'input[name="despatch_date"]'; // Active input in modal
    if ($(inputSelector).attr('id') === 'delivery_date_backorder') {
        return 36; // Backorder base
    }
    return 1; // Normal base
}



// UPDATED: Helper to update datepicker minDate dynamically (+2 days after last shipment)
function updateDatepickerMinDate() {
    const inputSelector = 'input[name="despatch_date"]';
    const lastDate = getLastShipmentDate();

    let newMinDate;

    if (lastDate) {
        // UPDATED: minDate = 2 days after last shipment date (for manufacturing buffer)
        const nextDay = new Date(lastDate);
        nextDay.setDate(lastDate.getDate() + 1); // +1 day
        newMinDate = nextDay; // Date object for datepicker
    } else {
        // Fallback to base (days from today)
        const baseMinDate = getBaseMinDate();
        newMinDate = baseMinDate; // Number of days from today
    }

    // Destroy existing datepicker and re-init with new minDate
    $(inputSelector).datepicker('destroy').datepicker({
        dateFormat: 'dd/mm/yy',
        minDate: newMinDate,
        maxDate: "+1Y",
        appendTo: '.delivery-options-modal'
    });
}
    // NEW: Helper to get last shipment date (parses from table, returns Date or null)


    // Helper: is this date "immediate" (must use stock only) or backorder-eligible (>=35 days from today)
    function isImmediateDate(despatch_date_str) {
        if (!despatch_date_str) return false;
        const [dd, mm, yyyy] = despatch_date_str.split('/').map(Number);
        const despatch = new Date(yyyy, mm - 1, dd);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        despatch.setHours(0, 0, 0, 0);

        const diffTime = despatch - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        return diffDays < 35;   // < 35 days = immediate / stock only
    }

    // Global variables that will hold the latest data every time modal opens
    window.currentShipments = [];
    window.partialBackorderData = { isPartial: false, ableToDispatch: 0, totalOrdered: 0 };


    // End new code for dynamic jquery picker




    
     // Calculate the delivery options price
    function calculateScheduledPrice() {
        const selectedTab = $('input[name="tabs_input"]:checked').val();
        if (selectedTab === 'custom-shape-drawing') {
            const pdfPath = $('#pdf_path').val().trim();
            if (!pdfPath) {
                $('#custom_price_display').html('<span class="product-page__backorder-message"><p class="product-page__backorder-message-text">Please upload a .PDF drawing before calculating the price.</p></span>');
                return;
            }
            const dxfPath = $('#dxf_path').val().trim();
            if (!dxfPath) {
                $('#custom_price_display').html('<span class="product-page__backorder-message"><p class="product-page__backorder-message-text">Please upload a .DXF drawing before calculating the price.</p></span>');
                return;
            }
        }
        const width = parseFloat($('#input_width').val());
        const length = parseFloat($('#input_length').val());
        const qty = parseInt($('#input_qty').val());
        const shipping_address = validateShippingAddress();
        const currency_rate = $('#currency_rate_sum').val();
        const currency_symbol =  $('#currency_rate_symbol').val();

        if (!shipping_address) return;

        // Client-side validation for price calculation
        if (isNaN(width) || isNaN(length) || isNaN(qty) || width <= 0 || length <= 0 || qty < 1) {
            $('#custom_price_display').html('<small>Please enter valid positive Width, Length, and Quantity.</small>');
            return;
        }

        $('#price-spinner-overlay').fadeIn(200);

        function runCalcScheduled(isRetry = false) {
                getFreshNonce()
                    .then(function(nonce) {
                        return $.ajax({
                            url: ajax_params.ajax_url,
                            type: 'POST',
                            cache: false,
                            data: {
                                action: 'calculate_scheduled_price',
                                product_id: ajax_params.product_id,
                                width: width,
                                length: length,
                                qty: qty,
                                nonce: nonce,
                                ...shipping_address,
                                shape_type: selectedTab
                            }
                        });

                    })
                    .then(function(response) {
                        $('#price-spinner-overlay').fadeOut(200);
                        if (response.success) {
                            const price = response.data.price;
                            const per_part = response.data.per_part;
                            const per_part_base = response.data.per_part_base;
                            const sheetsRequired = response.data.sheets_required || 1;
                            const sheet_width_mm = response.data.sheet_width_mm;
                            const sheet_length_mm = response.data.sheet_length_mm;
                            const border = parseFloat(response.data.border_around) * 10;
                            const selectedTab = $('input[name="tabs_input"]:checked').val();


                            let priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per part: <span class="product-page__display-price-text">' + currency_symbol + '' + (per_part * currency_rate).toFixed(2) + '</span></div><div class="product-page__display-price">Total part costs: <span class="product-page__display-price-text">' + currency_symbol + "" + (price * currency_rate).toFixed(2) + '</span></div></div></div>';
                                priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Notice:</strong> This is a scheduled delivery order with varying discounts applied based on despatch dates.</p></div>';


                            $('#custom_price_display').html(priceHtml);

                            let cart_price = price / sheetsRequired;
                            $('#custom_price').val(cart_price);

                            $('input[name="quantity"]').val(sheetsRequired); 
                            
                            $('input[name="custom_backorder_total"], input[name="custom_parts_backorder"], input[name="custom_able_to_dispatch"], input[name="custom_parts_per_sheet"]').remove();
                        } else {
                            $('#custom_price_display').html('Error: ' + (response.data.message || 'Unable to calculate scheduled price.'));
                        }
                    })
                    .catch(function(error) {
                        // Safety net – retry once on 403
                        if (!isRetry && error && error.status === 403) {
                            console.warn('save_shipment received 403 – clearing nonce and retrying once...');
                            window.customPriceNonce = null;
                            runCalcScheduled(true);
                            return;
                        }

                        // Final failure
                        $('#price-spinner-overlay').fadeOut(200);
                        alert('Error: Server error.');
                        window.customPriceNonce = null;
                    });

        }   
        
        // Start the first attempt
        runCalcScheduled(false);

    }
    // Calculate the delivery options price

    // NEW CODE FOR DELIVERY OPTIONS

    // Toggle modal on clicking Add Shipments button
    $('#add_shipments').on('click', function(e) {
        e.preventDefault();

        $('.product-page__tabs .product-page__tabs-list').hide(); 
            
        var shipmentId = $(this).data('id');

        if (shipmentId !== undefined && shipmentId !== '') {
            $('.product-page__tabs').css('background-image', 'url(/wp-content/themes/materials-direct/images/tabbed-menu-background.png)'); 
        } else {
            $('.product-page__tabs').css('background-image', 'url(/wp-content/themes/materials-direct/images/tabbed-menu-background-2.png)'); 
        }

        //$('#price-spinner-overlay').fadeIn(200);

        function runGetShipments(isRetry = false) {
            getFreshNonce()
                .then(function(nonce) {
                    return $.ajax({
                        url: ajax_params.ajax_url,
                        type: 'POST',
                        cache: false,
                        data: {
                            action: 'get_current_shipments',
                            nonce: nonce
                        }
                    });
                })
                .then(function(response) {
                    $('#price-spinner-overlay').fadeOut(200);

                    if (response.success) {
                        const data = response.data;

                        // Update remaining parts
                        $('#remaining-parts').text(data.remaining_parts);

                        // Store fresh data for validation
                        window.currentShipments = data.shipments || [];
                        window.partialBackorderData = {
                            isPartial: !!data.is_partial_backorder,
                            ableToDispatch: parseInt(data.able_to_dispatch) || 0,
                            totalOrdered: parseInt(data.total_ordered_qty) || 0
                        };

                        // Show modal
                        $('.delivery-options-modal__outer').fadeIn();
                    } else {
                        alert('Error fetching current shipment data.');
                    }
                })
                .catch(function(error) {
                    // Safety net – retry once on 403
                    if (!isRetry && error && error.status === 403) {
                        console.warn('get_current_shipments received 403 – clearing nonce and retrying once...');
                        window.customPriceNonce = null;
                        runGetShipments(true);
                        return;
                    }

                    // Final failure
                    $('#price-spinner-overlay').fadeOut(200);
                    alert('Could not load shipment data. Please refresh the page.');
                    window.customPriceNonce = null;
                });
        }

        // Start the first attempt
        runGetShipments(false);

        setTimeout(updateDatepickerMinDate, 100);
        $('.delivery-options-modal__outer').fadeToggle();
    });
    // End toggle modal on clicking Add Shipments button



    // Close modal on clicking the close button
    $('.delivery-options-modal__close-btn').on('click', function(e) {
        e.preventDefault();
        
        $('.delivery-options-modal__outer').fadeOut();
        $('.delivery-options-modal').removeClass('active');
        
        console.log('Modal closed + lock released');
    });



    // Handle modal form submission
    $('.delivery-options-modal__submit').on('click', function(e) {

            e.preventDefault();

            const despatch_date = $('input[name="despatch_date"]').val();
            const parts = parseInt($('input[name="shipment_parts"]').val());
            const add_manufacturers_cofc_ss = $('#add_manufacturers_COFC_ss').is(':checked') ? 45 : 0;
            const add_fair_ss = $('#add_fair_ss').is(':checked') ? 95 : 0;
            const add_materials_direct_cofc_ss = $('#add_materials_direct_COFC_ss').is(':checked') ? 12.50 : 0;

            if (!despatch_date) {
                alert('Please select a despatch date.');
                return;
            }
            if (isNaN(parts) || parts < 1) {
                alert('Please enter a valid number of parts (≥ 1).');
                return;
            }

            // NEW PARTIAL BACKORDER VALIDATION
            if (window.partialBackorderData && window.partialBackorderData.isPartial) {
                const isImmediate = isImmediateDate(despatch_date);

                let currentImmediate = 0;
                (window.currentShipments || []).forEach(function(shipment) {
                    if (isImmediateDate(shipment.date)) {
                        currentImmediate += parseInt(shipment.parts);
                    }
                });

                if (isImmediate && (currentImmediate + parts) > window.partialBackorderData.ableToDispatch) {
                    alert('Your quantity includes backordered items. Please change your order dates to 35 days from today or change your delivery quantity');
                    $('input[name="despatch_date"]').val('');
                    $('input[name="shipment_parts"]').val('');
                    $('.styled-checkbox-cofc').prop('checked', false);
                    return;
                }
            }
            // END NEW VALIDATION

            $('.delivery-options-modal__outer').fadeOut();
            $('#price-spinner-overlay').fadeIn(200);

            function runSaveShipment(isRetry = false) {
                getFreshNonce()
                    .then(function(nonce) {
                        return $.ajax({
                            url: ajax_params.ajax_url,
                            type: 'POST',
                            cache: false,
                            data: {
                                action: 'save_shipment',
                                despatch_date: despatch_date,
                                shipment_parts: parts,
                                add_manufacturers_COFC_ss: add_manufacturers_cofc_ss,
                                add_fair_ss: add_fair_ss,
                                add_materials_direct_COFC_ss: add_materials_direct_cofc_ss,
                                nonce: nonce
                            }
                        });
                    })
                    .then(function(response) {
                        $('#price-spinner-overlay').fadeOut(200);

                        if (response.success) {
                            // Update the shipments table
                            $('.delivery-options-shipment__outer').html(response.data.table_html);

                            // Update remaining parts
                            $('#remaining-parts').text(response.data.remaining_parts);
                            $('#parts_remaining').text(response.data.remaining_parts);

                            // Clear input fields
                            $('input[name="despatch_date"]').val('');
                            $('input[name="shipment_parts"]').val('');
                            $('input[name="add_manufacturers_COFC_ss"][value="45"]').prop('checked', false);
                            $('input[name="add_fair_ss"][value="95"]').prop('checked', false);
                            $('input[name="add_materials_direct_COFC_ss"][value="12.50"]').prop('checked', false);

                            // Close modal / update UI if no parts remain
                            if (response.data.remaining_parts <= 0) {
                                $('.product-page__order-info-message-1').text("Scheduled shipments now complete. Now click Add To Cart");
                                $('#add_shipments').hide();
                                if (allowCredit) {
                                    calculateScheduledPrice();
                                }
                            }
                        } else {
                            alert('Error: ' + (response.data.message || 'Unable to save shipment.'));
                        }
                    })
                    .catch(function(error) {
                        // Safety net – retry once on 403
                        if (!isRetry && error && error.status === 403) {
                            console.warn('save_shipment received 403 – clearing nonce and retrying once...');
                            window.customPriceNonce = null;
                            runSaveShipment(true);
                            return;
                        }

                        // Final failure
                        $('#price-spinner-overlay').fadeOut(200);
                        alert('Error: Server error.');
                        window.customPriceNonce = null;
                    });
            }

            // Start the first attempt
            runSaveShipment(false);
    });

    // End handle modal form submission




    // Handle shipment deletion
    $(document).on('click', '.delete-shipment', function(e) {
    e.preventDefault();
    const index = $(this).data('index');

    if (!confirm('Are you sure you want to delete this shipment?')) {
        return;
    }

    function runDeleteShipment(isRetry = false) {
        getFreshNonce()
            .then(function(nonce) {
                return $.ajax({
                    url: ajax_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'delete_shipment',
                        index: index,
                        nonce: nonce
                    }
                });
            })
            .then(function(response) {
                if (response.success) {
                    $('.delivery-options-shipment__outer').html(response.data.table_html);
                    $('#remaining-parts').text(response.data.remaining_parts);
                    $('#parts_remaining').text(response.data.remaining_parts);

                    if (response.data.remaining_parts > 0) {
                        $('#shipments_display').show();
                        $('#add_shipments').show();
                        $('.product-page__display-price-text').text('Calculating');
                    }

                    if (response.data.remaining_parts <= 0 && allowCredit) {
                        console.log('Remaining <=0 after delete, allowCredit:', allowCredit);
                        calculateScheduledPrice();
                    }
                } else {
                    alert('Error: ' + (response.data.message || 'Unable to delete shipment.'));
                }
            })
            .catch(function(error) {
                // Safety net – retry once on 403
                if (!isRetry && error && error.status === 403) {
                    console.warn('delete_shipment received 403 – clearing nonce and retrying once...');
                    window.customPriceNonce = null;
                    runDeleteShipment(true);
                    return;
                }

                // Final failure
                alert('Error: Server error.');
                window.customPriceNonce = null;
            });
    }

    // Start the first attempt
    runDeleteShipment(false);
});

    // NEW CODE FOR DELIVERY OPTIONS




    // Generate Price Button (only for non-single products)
    if (!isProductSingle) {


        $('#generate_price').on('click', function() {

            const selectedTab = $('input[name="tabs_input"]:checked').val();

            if (selectedTab === 'custom-shape-drawing') {
                const pdfPath = $('#pdf_path').val().trim();
                if (!pdfPath) {
                    $('#custom_price_display').html('<span class="product-page__backorder-message"><p class="product-page__backorder-message-text">Please upload a .PDF drawing before calculating the price.</p></span>');
                    return;
                }
                const dxfPath = $('#dxf_path').val().trim();
                if (!dxfPath) {
                    $('#custom_price_display').html('<span class="product-page__backorder-message"><p class="product-page__backorder-message-text">Please upload a .DXF drawing before calculating the price.</p></span>');
                    return;
                }
            }

            const width = parseFloat($('#input_width').val());
            const length = parseFloat($('#input_length').val());
            const qty = parseInt($('#input_qty').val());
            const discount_rate = parseFloat($('#input_discount_rate').val());
            const shipping_address = validateShippingAddress();
            const currency_rate = $('#currency_rate_sum').val();
            const currency_symbol = $('#currency_rate_symbol').val();

            if (!shipping_address) return;

            // Client-side validation
            if (isNaN(width) || isNaN(length) || isNaN(qty) || width <= 0 || length <= 0 || qty < 1) {
                $('#custom_price_display').html('<small>Please enter valid positive Width, Length, and Quantity.</small>');
                return;
            }

            // Validate discount rate
            const valid_discount_rates = [0, 0.015, 0.02, 0.025, 0.03, 0.035, 0.04, 0.05];
            if (!valid_discount_rates.includes(discount_rate)) {
                if (allowCredit) {
                    $('#custom_price_display').html('');
                } else {
                    $('#custom_price_display').html('<span class="product-page__backorder-message"><p class="product-page__backorder-message-text">Please select a valid delivery time.</p></span>');
                    return;
                }
            }

            $('#price-spinner-overlay').fadeIn(200);

            // Get a fresh nonce, then fire the price calculation
            function runPriceCalculation(isRetry = false) {
            getFreshNonce()
                .then(function(nonce) {
                    return $.ajax({
                        url: ajax_params.ajax_url,
                        type: 'POST',
                        cache: false,
                        data: {
                            action: 'calculate_secure_price',
                            product_id: ajax_params.product_id,
                            width: width,
                            length: length,
                            qty: qty,
                            discount_rate: discount_rate,
                            currency_rate: currency_rate,
                            currency_symbol: currency_symbol,
                            nonce: nonce,
                            ...shipping_address,
                            shape_type: selectedTab
                        }
                    });
                })
                .then(function(response) {
                    $('#price-spinner-overlay').fadeOut(200);

                    if (response.success) {
                        const price = response.data.price;
                        const adjustedPrice = response.data.per_part;
                        const sheetsRequired = response.data.sheets_required || 1;
                        const isBackorder = response.data.is_backorder || false;
                        const isSpecialPrice = response.data.is_special_price || false;
                        const sheet_width_mm = response.data.sheet_width_mm;
                        const sheet_length_mm = response.data.sheet_length_mm;
                        const border = parseFloat(response.data.border_around || 0.2) * 10;
                        const roll_length = response.data.roll_length;
                        const is_full_backorder_rolls = response.data.is_full_backorder_rolls || false;

                        $('#special_price_input').prop('disabled', false);
                        $('#special_price').prop('disabled', false);
                        //$('.product-page__special-price-outer').show();
                        

                        $('#cpp').val(adjustedPrice);

                        let stock_quantity;
                        if (selectedTab === 'rolls') {
                            stock_quantity = Math.round(response.data.stock_quantity / roll_length);
                        } else {
                            stock_quantity = response.data.stock_quantity;
                        }

                        const qty = response.data.entered_quantity;
                        const backorder_adjustedPrice = adjustedPrice * 0.05;
                        const discount_rate = response.data.discount_rate;
                        const globalPriceAdjust = response.data.global_price_adjust;

                        let discount_display;
                        if (discount_rate === 0) {
                            discount_display = "24Hrs (working day)";
                            discount_display_sp = "24Hrs (working day)";
                        } else if (discount_rate === 0.015) {
                            discount_display = "48Hrs (working day) (1.5% Discount)";
                            discount_display_sp = "48Hrs (working day)";
                        } else if (discount_rate === 0.02) {
                            discount_display = "5 Days (working day) (2% Discount)";
                            discount_display_sp = "5 Days (working day)";
                        } else if (discount_rate === 0.025) {
                            discount_display = "7 Days (working day) (2.5% Discount)";
                            discount_display_sp = "7 Days (working day)";
                        } else if (discount_rate === 0.03) {
                            discount_display = "12 Days (working day) (3% Discount)";
                            discount_display_sp = "12 Days (working day)";
                        } else if (discount_rate === 0.035) {
                            discount_display = "14 Days (working day) (3.5% Discount)";
                            discount_display_sp = "14 Days (working day)";
                        } else if (discount_rate === 0.04) {
                            discount_display = "30 Days (working day) (4% Discount)";
                            discount_display_sp = "30 Days (working day)";
                        } else {
                            discount_display = "35 Days (working day) (5% Discount)";
                            discount_display_sp = "35 Days (working day)";
                        }

                        let cart_price = price / sheetsRequired;
                        let sheets_backorder = 0;
                        let total_parts_d;
                        let parts_from_stock;
                        let able_to_dispatch;
                        let parts_backorder;
                        let backorder_adjustedPriceDisplay;
                        let calcPartialbackorderdiscount_1;
                        let calcPartialbackorderdiscount_2;
                        let calcPartialbackorderFinal;
                        let v1;
                        let v2;

                        if (selectedTab === 'stock-sheets' || selectedTab === 'rolls') {
                            v1 = width;
                            v2 = length;
                        } else {
                            v1 = width + (2 * border);
                            v2 = length + (2 * border);
                        }

                        const usable_width = sheet_width_mm;
                        const usable_length = sheet_length_mm;
                        const parts_per_row = Math.floor(usable_width / v1);
                        const parts_per_column = Math.floor(usable_length / v2);
                        const parts_per_sheet = parts_per_row * parts_per_column;

                        let priceHtml = '';

                        // === ROLLS FULL BACKORDER ===
                        if (is_full_backorder_rolls) {
                            priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per roll: <span class="product-page__display-price-text">' + currency_symbol + '' + (adjustedPrice * currency_rate).toFixed(2) + '</span></div><div class="product-page__display-price">Total roll costs: <span class="product-page__display-price-text">' + currency_symbol + "" + (price * currency_rate).toFixed(2) + '</span></div></div></div>';
                            priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Notice:</strong> This order is currently on backorder only. Please allow 35 Days for complete order fulfillment with a 5% discount applied to the total order.</p></div>';
                            
                            cart_price = price;

                            const $select = $('#input_discount_rate');
                            const originalOptions = $select.html();

                            function updateDispatchOptions(is_full_backorder_rolls) {
                                if (is_full_backorder_rolls) {
                                    $select.html('<option value="0.05">35 Days (working days) (5% Discount)</option>').val('0.05');
                                } else {
                                    $select.html(originalOptions);
                                }
                            }

                            if (is_full_backorder_rolls) {
                                $('<input>').attr({ type: 'hidden', name: 'is_full_backorder_rolls', value: '1' }).appendTo('form.cart');
                                updateDispatchOptions(is_full_backorder_rolls);
                            }
                        }
                        // === PARTIAL BACKORDER ===
                        else if (isBackorder && stock_quantity > 0) {
                            if (parts_per_sheet <= 0) {
                                $('#custom_price_display').html('Error: Invalid sheet calculation. Part does not fit on sheet.');
                                return;
                            }

                            const selectedTab = $('input[name="tabs_input"]:checked').val();

                            if (selectedTab !== 'rolls') {
                                sheets_backorder = sheetsRequired - stock_quantity;
                                total_parts_d = qty;
                                parts_from_stock = stock_quantity * parts_per_sheet;
                                able_to_dispatch = Math.min(parts_from_stock, total_parts_d);
                                parts_backorder = total_parts_d - able_to_dispatch;
                                
                                 /* Add special price logic */
                                if (isSpecialPrice) {
                                    backorder_adjustedPriceDisplay = adjustedPrice;
                                    calcPartialbackorderdiscount_1 = able_to_dispatch * adjustedPrice;
                                    calcPartialbackorderdiscount_2 = parts_backorder * adjustedPrice;

                                } else {
                                    backorder_adjustedPriceDisplay = adjustedPrice * 0.95;
                                    calcPartialbackorderdiscount_1 = able_to_dispatch * adjustedPrice;
                                    calcPartialbackorderdiscount_2 = parts_backorder * backorder_adjustedPriceDisplay;

                                }
                                 /* Add special price logic */

                                // backorder_adjustedPriceDisplay = adjustedPrice * 0.95;
                                // calcPartialbackorderdiscount_1 = able_to_dispatch * adjustedPrice;
                                // calcPartialbackorderdiscount_2 = parts_backorder * backorder_adjustedPriceDisplay;

                                calcPartialbackorderFinal = (calcPartialbackorderdiscount_1 + calcPartialbackorderdiscount_2).toFixed(2);

                                cart_price = calcPartialbackorderFinal / sheetsRequired;

                                let partialBackorderNotice;

                                if (isSpecialPrice) {
                                    partialBackorderNotice = 'Please allow 35 Days to complete the back ordered items. No additional discount will apply to these parts because a Special Price has been applied.';
                                } else {
                                    partialBackorderNotice = 'Please allow 35 Days to complete the back ordered items. A 5% discount will apply to these parts.';
                                }

                                priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per part: <span class="product-page__display-price-text">£' + (adjustedPrice * currency_rate).toFixed(2) + '<span style="font-size: 0.82rem; font-weight: 400;"> (' + currency_symbol + "" + (backorder_adjustedPriceDisplay * currency_rate).toFixed(2) + ' for backorder parts)</span></span></div><div class="product-page__display-price">Total part costs: <span class="product-page__display-price-text">' + currency_symbol + "" + (calcPartialbackorderFinal * currency_rate).toFixed(2) + '</span></div></div></div>';
                                
                                if (isSpecialPrice) { 
                                    priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Notice:</strong> This order exceeds current stock, it requires an additional ' + sheets_backorder + ' sheets (' + parts_backorder + ' parts) to be back ordered. We are able to despatch: ' + able_to_dispatch + ' parts within ' + discount_display_sp + '. ' + partialBackorderNotice + '</p></div>';
                                } else {
                                    priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Notice:</strong> This order exceeds current stock, it requires an additional ' + sheets_backorder + ' sheets (' + parts_backorder + ' parts) to be back ordered. We are able to despatch: ' + able_to_dispatch + ' parts within ' + discount_display + '. ' + partialBackorderNotice + '</p></div>';
                                }
                            }
                        }
                        // === FULL BACKORDER (stock <= 0) ===
                        else if (isBackorder && stock_quantity <= 0) {
                            if (selectedTab === 'rolls') {
                                priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per roll: <span class="product-page__display-price-text">' + currency_symbol + '' + (adjustedPrice * currency_rate).toFixed(2) + '</span></div><div class="product-page__display-price">Total roll costs: <span class="product-page__display-price-text">' + currency_symbol + "" + (price * currency_rate).toFixed(2) + '</span></div></div></div>';
                            } else {
                                priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per part: <span class="product-page__display-price-text">' + currency_symbol + '' + (adjustedPrice * currency_rate).toFixed(2) + '</span></div><div class="product-page__display-price">Total part costs: <span class="product-page__display-price-text">' + currency_symbol + "" + (price * currency_rate).toFixed(2) + '</span></div></div></div>';
                            }
                            if (isSpecialPrice) {
                                priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Notice:</strong> This order is currently on backorder only. Please allow 35 Days for complete order fulfillment.</p></div>';
                            } else {
                                priceHtml += '<div class="product-page__backorder-message"><p class="product-page__backorder-message-text"><strong>Notice:</strong> This order is currently on backorder only. Please allow 35 Days for complete order fulfillment with a 5% discount applied to the total order.</p></div>';
                            }
                            
                        }
                        // === NORMAL IN-STOCK ===
                        else {
                            if (selectedTab === 'rolls') {
                                priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per metre: <span class="product-page__display-price-text">' + currency_symbol + '' + (adjustedPrice / roll_length * currency_rate).toFixed(2) + '</span></div><div class="product-page__display-price">Total roll costs: <span class="product-page__display-price-text">' + currency_symbol + "" + (price * currency_rate).toFixed(2) + '</span></div></div></div>';
                            } else {
                                priceHtml = '<div class="product-page__display-price-outer"><div><h4 class="product-page__display-price-heading">Here is your instant quote</h4></div><div class="product-page__display-price-inner"><div class="product-page__display-price">Cost per part: <span class="product-page__display-price-text">' + currency_symbol + '' + (adjustedPrice * currency_rate).toFixed(2) + '</span></div><div class="product-page__display-price">Total part costs: <span class="product-page__display-price-text">' + currency_symbol + "" + (price * currency_rate).toFixed(2) + '</span></div></div></div>';
                            }
                        }

                        // DISPLAY PRICE AND PART COST ON PRODUCT PAGE AFTER CLICKING 'CALCULATE PRICE'
                        $('#custom_price_display').html(priceHtml);

                        // ADD PRICE TO HIDDEN FIELD THAT IS THEN PASSED TO CART
                        $('#custom_price').val(cart_price);
                        $('#shipments_display').fadeToggle();
                        $('#parts_remaining').text(qty);

                        if (allowCredit) {
                            $('#generate_price').hide();
                        }

                        if (isBackorder && stock_quantity > 0) {
                            $('<input>').attr({ type: 'hidden', name: 'custom_backorder_total', value: calcPartialbackorderFinal }).appendTo('form.cart');
                            $('<input>').attr({ type: 'hidden', name: 'custom_parts_backorder', value: parts_backorder }).appendTo('form.cart');
                            $('<input>').attr({ type: 'hidden', name: 'custom_able_to_dispatch', value: able_to_dispatch }).appendTo('form.cart');
                            $('<input>').attr({ type: 'hidden', name: 'custom_parts_per_sheet', value: parts_per_sheet }).appendTo('form.cart');
                        }

                        $('input[name="quantity"]').val(sheetsRequired);

                        // Save partial backorder data (using the same fresh nonce)
                        if (isBackorder && stock_quantity > 0) {
                            $.ajax({
                                url: ajax_params.ajax_url,
                                type: 'POST',
                                cache: false,
                                data: {
                                    action: 'save_partial_backorder_data',
                                    is_partial_backorder: true,
                                    able_to_dispatch: able_to_dispatch,
                                    parts_backorder: parts_backorder,
                                    nonce: window.customPriceNonce
                                }
                            });
                        } else {
                            $.ajax({
                                url: ajax_params.ajax_url,
                                type: 'POST',
                                cache: false,
                                data: {
                                    action: 'save_partial_backorder_data',
                                    is_partial_backorder: false,
                                    able_to_dispatch: qty,
                                    parts_backorder: 0,
                                    nonce: window.customPriceNonce
                                }
                            });
                        }

                    } else {
                        $('#custom_price_display').html('<span class="product-page__backorder-message"><p class="product-page__backorder-message-text">Error: ' + (response.data.message || 'Unable to calculate price.') + '</p></span>');
                    }
                })
                .catch(function(error) {
                    if (!isRetry && error && error.status === 403) {
                            console.warn('Received 403 – clearing nonce and retrying once...');
                            window.customPriceNonce = null;
                            runPriceCalculation(true); // retry once
                            return;
                    }
                    $('#price-spinner-overlay').fadeOut(200);
                    $('#custom_price_display').html('<span class="product-page__backorder-message"><p class="product-page__backorder-message-text">Error: Server error.</p></span>');

                    // Clear stored nonce so the next attempt gets a fresh one
                    window.customPriceNonce = null;
                });
            }  
            
            runPriceCalculation(false); 

        });
        

        // Validate shipping address on Add to Cart form submission
        $('.woocommerce-cart-form, form.cart').on('submit', function(e) {
            const price = $('#custom_price').val(); 
            if (!price) {
                e.preventDefault();
                $('#custom_price_display').html('<span class="product-page__backorder-message"><p class="product-page__backorder-message-text">Please generate a price before adding to cart.</p></span>');
                return false;
            }

            const shipping_address = validateShippingAddress();
            if (!shipping_address) {
                e.preventDefault();
                return false;
            }

            if (allowCredit) {
                const remaining = parseInt($('#parts_remaining').text());
                if (remaining > 0) {
                    e.preventDefault();
                    $('#custom_price_display').html('<span class="product-page__backorder-message"><p class="product-page__backorder-message-text">Please assign all parts to shipments before adding to cart.</p></span>');
                    return false;
                }
            }

        });

    } else {

            function runSingleProductPrice(isRetry = false) {
            getFreshNonce()
                .then(function(nonce) {
                    return $.ajax({
                        url: ajax_params.ajax_url,
                        type: 'POST',
                        cache: false,
                        data: {
                            action: 'calculate_secure_price',
                            product_id: ajax_params.product_id,
                            nonce: nonce
                        }
                    });
                })
                .then(function(response) {
                    if (response.success) {
                        const price = response.data.price;
                        $('#custom_price_display').html(
                            '<div class="product-page__display-price-outer">' +
                                '<div><h4 class="product-page__display-price-heading">Product Price</h4></div>' +
                                '<div class="product-page__display-price-inner">' +
                                    '<div class="product-page__display-price">Price: <span class="product-page__display-price-text">£' + price.toFixed(2) + '</span></div>' +
                                '</div>' +
                            '</div>'
                        );
                        $('#custom_price').val(price);
                        $('input[name="quantity"]').val(1);
                    } else {
                        $('#custom_price_display').html('Error: ' + (response.data.message || 'Unable to fetch price.'));
                    }
                })
                .catch(function(error) {
                    if (!isRetry && error && error.status === 403) {
                        console.warn('calculate_secure_price (single) received 403 – clearing nonce and retrying once...');
                        window.customPriceNonce = null;
                        runSingleProductPrice(true);
                        return;
                    }

                    $('#custom_price_display').html('Error: Server error.');
                    window.customPriceNonce = null;
                });
            }

            // Start the price fetch
            runSingleProductPrice(false);


            // ===== Save shipping address on Add to Cart for single products =====
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

                // Prevent the form from submitting until the AJAX finishes
                e.preventDefault();

                const $form = $(this);

                function runSaveShipping(isRetry = false) {
                    getFreshNonce()
                        .then(function(nonce) {
                            return $.ajax({
                                url: ajax_params.ajax_url,
                                type: 'POST',
                                data: {
                                    action: 'save_single_product_shipping',
                                    product_id: ajax_params.product_id,
                                    nonce: nonce,
                                    ...shipping_address
                                }
                            });
                        })
                        .then(function(response) {
                            if (response.success) {
                                // Allow the form to submit normally
                                $form.off('submit').submit();
                            } else {
                                $('#custom_price_display').html('Error: ' + (response.data.message || 'Unable to save shipping address.'));
                            }
                        })
                        .catch(function(error) {
                            if (!isRetry && error && error.status === 403) {
                                console.warn('save_single_product_shipping received 403 – clearing nonce and retrying once...');
                                window.customPriceNonce = null;
                                runSaveShipping(true);
                                return;
                            }

                            $('#custom_price_display').html('Error: Server error while saving shipping address.');
                            window.customPriceNonce = null;
                        });
                }

                runSaveShipping(false);
            });
        // For single products, display the default price immediately

    }
});