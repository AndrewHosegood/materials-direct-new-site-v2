jQuery(document).ready(function($) {
    // Log for debugging
    console.log('Captured Carts Admin JS Loaded', capturedCartsAjax);

    // Email input AJAX search and save
    $('.customer-email-input').each(function() {
        var $input = $(this);
        var $suggestions = $input.siblings('.email-suggestions');
        var timeout;
        var isTyping = false;

        $input.on('keyup', function(e) {
            clearTimeout(timeout);
            var searchTerm = $input.val().trim();
            isTyping = true;

            // Clear previous feedback
            $input.next('.email-feedback').remove();

            // Handle Enter key to save email
            if (e.key === 'Enter' && isEmailValid(searchTerm)) {
                isTyping = false;
                $suggestions.hide().empty();
                saveEmail($input, searchTerm);
                return;
            }

            // Only trigger search if typing and search term is 4+ characters
            if (searchTerm.length < 4) {
                $suggestions.hide().empty();
                isTyping = false;
                return;
            }

            timeout = setTimeout(function() {
                $.ajax({
                    url: capturedCartsAjax.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'search_customer_emails',
                        nonce: capturedCartsAjax.nonce,
                        search: searchTerm
                    },
                    beforeSend: function() {
                        $input.prop('disabled', true);
                        $input.after('<span class="email-feedback" style="color: blue; margin-left: 10px;">Searching...</span>');
                    },
                    success: function(response) {
                        $input.next('.email-feedback').remove();
                        if (response.success && response.data.emails.length > 0) {
                            $suggestions.empty();
                            response.data.emails.forEach(function(email) {
                                $suggestions.append(
                                    $('<div>', {
                                        class: 'email-suggestion',
                                        text: email,
                                        css: {
                                            padding: '5px',
                                            cursor: 'pointer'
                                        }
                                    }).hover(
                                        function() { $(this).css('background', '#f0f0f0'); },
                                        function() { $(this).css('background', 'white'); }
                                    ).on('click', function() {
                                        $input.val(email);
                                        $suggestions.hide().empty();
                                        isTyping = false;
                                        saveEmail($input, email);
                                    })
                                );
                            });
                            $suggestions.show();
                        } else {
                            $suggestions.hide().empty();
                            $input.after('<span class="email-feedback" style="color: red; margin-left: 10px;">No matching emails found.</span>');
                            setTimeout(function() {
                                $('.email-feedback').fadeOut(2000);
                            }, 3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        $input.next('.email-feedback').remove();
                        $input.after('<span class="email-feedback" style="color: red; margin-left: 10px;">Failed to search emails. Please try again.</span>');
                        console.error('AJAX error searching emails: ' + error);
                        $suggestions.hide().empty();
                    },
                    complete: function() {
                        $input.prop('disabled', false);
                    }
                });
            }, 300); // Debounce delay
        });

        // Allow editing by clearing the input on focus if not typing
        $input.on('focus', function() {
            if (!isTyping) {
                $input.val('');
                $suggestions.hide().empty();
                $input.next('.email-feedback').remove();
            }
        });

        // Hide suggestions and save email when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.customer-email-input, .email-suggestions').length) {
                $suggestions.hide().empty();
                var email = $input.val().trim();
                if (isTyping && isEmailValid(email)) {
                    isTyping = false;
                    saveEmail($input, email);
                } else if (isTyping) {
                    $input.next('.email-feedback').remove();
                    $input.after('<span class="email-feedback" style="color: red; margin-left: 10px;">Please enter a valid email address.</span>');
                    setTimeout(function() {
                        $('.email-feedback').fadeOut(2000);
                    }, 3000);
                }
                isTyping = false;
            }
        });

        // Email validation function
        function isEmailValid(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
    });

    // Save email function
    function saveEmail($input, email) {
        var postId = $input.data('post-id');

        $.ajax({
            url: capturedCartsAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'save_customer_email',
                nonce: capturedCartsAjax.nonce,
                post_id: postId,
                email: email
            },
            beforeSend: function() {
                $input.prop('disabled', true);
                $input.after('<span class="email-feedback" style="color: blue; margin-left: 10px;">Saving...</span>');
            },
            success: function(response) {
                $input.next('.email-feedback').remove();
                if (response.success) {
                    $input.after('<span class="email-feedback" style="color: green; margin-left: 10px;">' + response.data.message + '</span>');
                    // Update all inputs for affected post IDs
                    if (response.data.updated_post_ids && Array.isArray(response.data.updated_post_ids)) {
                        response.data.updated_post_ids.forEach(function(id) {
                            var $targetInput = $('.customer-email-input[data-post-id="' + id + '"]');
                            $targetInput.val(email);
                            $targetInput.closest('tr').find('.send-email').prop('disabled', false); // Enable Send button
                        });
                    } else {
                        // Fallback for the current row
                        $input.closest('tr').find('.send-email').prop('disabled', false);
                    }
                    setTimeout(function() {
                        $('.email-feedback').fadeOut(2000);
                    }, 3000);
                } else {
                    $input.after('<span class="email-feedback" style="color: red; margin-left: 10px;">' + response.data.message + '</span>');
                    $input.closest('tr').find('.send-email').prop('disabled', true);
                }
            },
            error: function(xhr, status, error) {
                $input.next('.email-feedback').remove();
                $input.after('<span class="email-feedback" style="color: red; margin-left: 10px;">Failed to save email. Please try again.</span>');
                console.error('AJAX error saving email for post ID ' + postId + ': ' + error);
                $input.closest('tr').find('.send-email').prop('disabled', true);
            },
            complete: function() {
                $input.prop('disabled', false);
            }
        });
    }

    // Add to Cart Action
    function addToCart(postId) {
        console.log('Attempting add to cart for post ID: ' + postId);

        $.ajax({
            url: capturedCartsAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'add_to_cart_from_capture',
                nonce: capturedCartsAjax.nonce,
                post_id: postId
            },
            success: function(response) {
                console.log('Add to cart response:', response);
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = response.data.cart_url;
                } else {
                    console.error('Add to cart failed:', response.data.message);
                    alert('Failed to add to cart: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Add to cart AJAX error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    responseJSON: xhr.responseJSON
                });
                var message = xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message
                    ? xhr.responseJSON.data.message
                    : 'An error occurred while adding to cart.';
                alert('Error: ' + message);
            },
        });
    }

    // Admin Add to Cart
    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        addToCart(postId);
    });

    // My Account Restore Cart
    $(document).on('click', '#capture_cart_email', function(e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        addToCart(postId);
    });

    // Send Email Action
    $('.send-email').click(function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var postId = $button.data('post-id');
        console.log('Attempting to send email for post ID: ' + postId);

        $.ajax({
            url: capturedCartsAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'send_customer_email',
                nonce: capturedCartsAjax.nonce,
                post_id: postId
            },
            beforeSend: function() {
                $button.prop('disabled', true).text('Sending...');
            },
            success: function(response) {
                console.log('Send email response:', response);
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert('Failed to send email: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Send email AJAX error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    responseJSON: xhr.responseJSON
                });
                var message = xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message
                    ? xhr.responseJSON.data.message
                    : 'An error occurred while sending the email.';
                alert('Error sending email: ' + message);
            },
            complete: function() {
                $button.prop('disabled', false).text('Send');
            }
        });
    });

    // Select All Checkbox
    $('#select-all-carts').click(function() {
        $('input[name="delete_cart_ids[]"]').prop('checked', $(this).prop('checked'));
    });

    // Bulk Delete Action
    $('#bulk-delete-captured-carts').click(function(e) {
        e.preventDefault();
        var selectedIds = $('input[name="delete_cart_ids[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one cart to delete.');
            return;
        }

        if (confirm('Delete ' + selectedIds.length + ' selected cart(s)?')) {
            console.log('Attempting bulk delete for post IDs: ' + selectedIds.join(', '));
            $.ajax({
                url: capturedCartsAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'bulk_delete_captured_carts',
                    nonce: capturedCartsAjax.nonce,
                    post_ids: selectedIds
                },
                success: function(response) {
                    console.log('Bulk delete response:', response);
                    if (response.success) {
                        selectedIds.forEach(function(postId) {
                            $('tr[data-post-id="' + postId + '"]').remove();
                        });
                        alert(response.data.message);
                    } else {
                        console.error('Bulk delete failed:', response.data.message);
                        alert('Failed to delete: ' + response.data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Bulk delete AJAX error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText,
                        responseJSON: xhr.responseJSON
                    });
                    var message = xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message
                        ? xhr.responseJSON.data.message
                        : 'An error occurred while deleting the selected carts.';
                    alert('Error: ' + message);
                }
            });
        }
    });

    // Handle Custom Expiry Schedule change
    $('.custom-expiry-schedule').on('change', function() {
        var $select = $(this);
        var post_id = $select.data('post-id');
        var expiry_hours = $select.val();

        $.ajax({
            url: capturedCartsAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'save_custom_expiry_schedule',
                nonce: capturedCartsAjax.nonce,
                post_id: post_id,
                expiry_hours: expiry_hours
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('An error occurred while saving the expiry schedule.');
            }
        });
    });



});