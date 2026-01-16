jQuery(document).ready(function($) {

    $('.calendar__select-status').change(function() {
        //alert('working 1');
        var id = $(this).closest('form').find('input[name="id"]').val();
        var status = $(this).val();
        var loadingImage = $('<img src="/wp-content/uploads/2024/03/loading7_gray.gif" class="calendar__loading-image">');
        var order_no = $(this).closest('form').find('input[name="order_no"]').val();
        var date = $(this).closest('form').find('input[name="date"]').val();

        // Alert the value of order_no
        // alert(id);
        // alert(order_no);
        // alert(date);

        // Check if status is 'dispatch'
        if (status === 'dispatch') {
            // Display confirmation dialog
            var confirmDialog = confirm("You are about to send the customer an invoice and dispatch note. This action cannot be undone! Are you sure you want to proceed?");
            if (!confirmDialog) {
                // If user selects 'No', return without performing further actions
                $(this).val('made');
                return;
            }
        }

        // Append the loading image to a container
        $(this).closest('form').append(loadingImage);

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'update_order_status',
                id: id,
                status: status,
                order_no: order_no,
                date: date,
            },
            beforeSend: function() {
                // Show the loading image before the AJAX call starts
                loadingImage.show();
            },
            success: function(response) {
                console.log(response); 
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
            complete: function() {
                // Hide the loading image after the AJAX call completes
                loadingImage.hide();
            }
        });
    });




    $('.calendar__select-status-merged-date').change(function() {
            //alert('working 2');
            var id = $(this).closest('form').find('input[name="id"]').val();
            var status = $(this).val();
            var loadingImage = $('<img src="/wp-content/uploads/2024/03/loading7_gray.gif" class="calendar__loading-image">');
            var order_no = $(this).closest('form').find('input[name="order_no"]').val();
            var date = $(this).closest('form').find('input[name="date"]').val();
            //alert(id + ", " + status + ", " + order_no);
            
            // Check if status is 'dispatch'
            if (status === 'dispatch') {
                // Display confirmation dialog
                var confirmDialog = confirm("You have merged dates. You must send the customer confirmation manually");
                if (!confirmDialog) {
                    // If user selects 'No', return without performing further actions
                    $(this).val('made');
                    return;
                }
            }

            // Append the loading image to a container
            $(this).closest('form').append(loadingImage);

            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'update_order_status_merged',
                    id: id,
                    status: status,
                    order_no: order_no,
                    date: date,
                },
                beforeSend: function() {
                    // Show the loading image before the AJAX call starts
                    loadingImage.show();
                },
                success: function(response) {
                    console.log(response); 
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                },
                complete: function() {
                    // Hide the loading image after the AJAX call completes
                    loadingImage.hide();
                }
            });
    });



    $('.calendar__select-status-single').change(function() {
        //alert('working single');
        var id = $(this).closest('form').find('input[name="id"]').val();
        var status = "dispatch";
        var loadingImage = $('<img src="/wp-content/uploads/2024/03/loading7_gray.gif" class="calendar__loading-image">');
        var order_no = $(this).closest('form').find('input[name="order_no"]').val();
        var date = $(this).closest('form').find('input[name="date"]').val();
        //alert(id + ", " + status + ", " + order_no + ", " + date);

            // Append the loading image to a container
            $(this).closest('form').append(loadingImage);

            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'update_order_status_single',
                    id: id,
                    status: status,
                    order_no: order_no,
                    date: date,
                },
                beforeSend: function() {
                    // Show the loading image before the AJAX call starts
                    loadingImage.show();
                },
                success: function(response) {
                    console.log(response); 
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                },
                complete: function() {
                    // Hide the loading image after the AJAX call completes
                    loadingImage.hide();
                }
            });
    });


    $('.calendar__select-status-multiple').change(function() {
        //alert('working multiple');
        var id = $(this).closest('form').find('input[name="id"]').val();
        var status = "dispatch";
        var loadingImage = $('<img src="/wp-content/uploads/2024/03/loading7_gray.gif" class="calendar__loading-image">');
        var order_no = $(this).closest('form').find('input[name="order_no"]').val();
        var date = $(this).closest('form').find('input[name="date"]').val();
        //alert(id + ", " + status + ", " + order_no + ", " + date);

            // Append the loading image to a container
            $(this).closest('form').append(loadingImage);

            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'update_order_status_multiple',
                    id: id,
                    status: status,
                    order_no: order_no,
                    date: date,
                },
                beforeSend: function() {
                    // Show the loading image before the AJAX call starts
                    loadingImage.show();
                },
                success: function(response) {
                    console.log(response); 
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                },
                complete: function() {
                    // Hide the loading image after the AJAX call completes
                    loadingImage.hide();
                }
            });
    });



    $('.calendar__select-shipments').change(function() {
        var id = $(this).closest('form').find('input[name="id"]').val();
        
        if ($(this).is(':checked')) {
            var is_merged = 1;
        } else {
            var is_merged = 0;
        }
        var loadingImage = $('<img src="/wp-content/uploads/2024/03/loading7_gray.gif" class="calendar__loading-image">');
        //alert(id);

            // Append the loading image to a container
            $(this).closest('form').append(loadingImage);

            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'update_order_status_checkbox_select',
                    id: id,
                    is_merged: is_merged,
                },
                beforeSend: function() {
                    // Show the loading image before the AJAX call starts
                    loadingImage.show();
                },
                success: function(response) {
                    console.log(response); 
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                },
                complete: function() {
                    // Hide the loading image after the AJAX call completes
                    loadingImage.hide();
                }
            });
    });



    $('.calendar__select-shipments-send').click(function(e) {
        e.preventDefault();
        //alert("WORKING");
        var id = $(this).closest('form').find('input[name="id"]').val();
        var status = $(this).closest('form').find('input[name="status"]').val();
        var loadingImage = $('<img style="position: absolute; right: 340px;" src="/wp-content/uploads/2024/03/loading7_gray.gif" class="calendar__loading-image">');
        var order_no = $(this).closest('form').find('input[name="order_no"]').val();
        var date = $(this).closest('form').find('input[name="date"]').val();
        var is_merged = $(this).closest('form').find('input[name="is_merged"]').val();
        //alert(id + ", " + status + ", " + order_no + ", " + date + ", " + is_merged);

        if (status === 'dispatch') {
            var confirmDialog = confirm("You are about to send the customer an invoice and dispatch note. This action cannot be undone! Are you sure you want to proceed?");
            if (!confirmDialog) {
                $(this).val('made');
                return;
            }
        }

        $(this).closest('form').append(loadingImage);

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'update_order_status',
                id: id,
                status: status,
                order_no: order_no,
                date: date,
                is_merged: is_merged,
            },
            beforeSend: function() {
                // Show the loading image before the AJAX call starts
                loadingImage.show();
            },
            success: function(response) {
                console.log(response); 
                var $results = $('#customer-invoice-results');
                if (response.success) {
                    $results.css({
                        "background": "#e0ffe0",
                        "border": "1px solid #00aa00",
                        "color": "##3c434a",
                        "opacity": "1"
                    }).html(response.data);
                } else {
                    $results.css({
                        "background": "#ffe0e0",
                        "border": "1px solid #aa0000",
                        "color": "#660000",
                        "opacity": "1"
                    }).html(response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
            complete: function() {
                // Hide the loading image after the AJAX call completes
                loadingImage.hide();
            }
        });
    });



    $('.calendar__form-tracking').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);

        const id = $form.find('input[name="id"]').val();
        const tracking_number_details = $form.find('input[name="tracking_number_details"]').val();
        const loadingImage = $('<img src="/wp-content/uploads/2024/03/loading7_gray.gif" class="calendar__loading-image">');

        $form.append(loadingImage);

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'update_tracking_details',
                id: id,
                tracking_number_details: tracking_number_details,
            },
            beforeSend: function() {
                loadingImage.show();
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
            complete: function() {
                loadingImage.remove(); // Optional: completely remove instead of hide
            }
        });
    });



    $('.calendar__form-tracking-url').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);

        const id = $form.find('input[name="id"]').val();
        const tracking_number_url = $form.find('input[name="tracking_number_url"]').val();
        const loadingImage = $('<img src="/wp-content/uploads/2024/03/loading7_gray.gif" class="calendar__loading-image">');

        $form.append(loadingImage);

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'update_tracking_details_url',
                id: id,
                tracking_number_url: tracking_number_url,
            },
            beforeSend: function() {
                loadingImage.show();
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
            complete: function() {
                loadingImage.remove(); // Optional: completely remove instead of hide
            }
        });
    });



        $('#calendar_search').on('input', function() {
            //alert('working');
            var searchQuery = $(this).val();
            console.log(searchQuery);
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'fetch_search_results',
                    search_query: searchQuery
                },
                success: function(response) {
                    // Handle success response
                    if (response.length > 0) {
                        var resultHtml = '<div class="search-results">';
                        response.forEach(function(result) {
                            resultHtml += '<p>Order No: ' + result.order_no + ', Product: ' + result.title + ', Date: ' + result.date + '</p>';
                        });
                        resultHtml += '</div>';
                        $('.search-results').remove(); // Remove existing results
                        $('#calendar_search').after(resultHtml); // Append new results
                    } else {
                        $('.search-results').remove(); // Remove existing results if no results found
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(error);
                }
            });
        });


        /*
        $('.calendar__table tbody tr').eq(-2).addClass('my-class-name');

        $(document).on('change', '.my-class-name td form.calendar__form-0 select.calendar__select-status', function() {

            if ($(this).val() === 'dispatch') {

                let orderNo = $(this).siblings('input[name="order_no"]').val();
                let makeActive = 1;

                $.ajax({
                    url: ajaxurl, 
                    method: 'POST',
                    data: { 
                        action: 'show_final_dispatch_action', 
                        makeActive: makeActive, 
                        orderNo: orderNo 
                    },
                    success: function(response) {
                        console.log('Data inserted successfully:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error inserting data:', error);
                    }
                });

            }
        });
        */


        // $('.calendar__select-status').change(function(){
        //     alert('working');
        //     if($(this).val() === 'dispatch') {
        //         // Do something when "dispatch" is selected
        //         console.log("Dispatch option selected");
        //         // Add your code here to do something
        //     }
        // });



});
