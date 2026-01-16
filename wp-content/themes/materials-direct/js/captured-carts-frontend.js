jQuery(document).ready(function($) {
    $('.capture-cart-restore').on('click', function(e) {
        e.preventDefault();

        var $button = $(this);
        var post_id = $button.data('post-id');
        var nonce = $button.data('nonce');

        if ($button.prop('disabled')) {
            return;
        }

        $button.text('Adding Your Order To Cart...');

        $('.capture-cart-restore').addClass('disabled').css({
            'pointer-events': 'none',
            'opacity': 0.6
        });

        //$button.prop('disabled', true).text('Adding Your Order To Cart...');

        //alert("Post ID: " + post_id + "Nonce: " + nonce);

        $.ajax({
            url: capturedCartsAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'add_to_cart_from_capture',
                post_id: post_id,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    $button.text('Product Added To Cart!').prop('disabled', true);
                    alert(response.data.message);
                    window.location.href = '/my-account/';
                } else {
                    $button.text('Click Here To Place Your Order');
                    alert(response.data.message || 'Failed to restore cart.');
                    //$button.prop('disabled', false).text('Click Here To Place Your Order');
                }
            },
            error: function() {
                $button.text('Click Here To Place Your Order');
                alert('An error occurred. Please try again.');
                //$button.prop('disabled', false).text('Click Here To Place Your Order');
            },
            complete: function() {
                setTimeout(function() {
                    $('.capture-cart-restore').removeClass('disabled').css({
                        'pointer-events': '',
                        'opacity': ''
                    });
                }, 2000); // Delay of 2000 milliseconds (2 seconds)
            }
        });
    });
});