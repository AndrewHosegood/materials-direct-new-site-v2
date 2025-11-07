jQuery(document).ready(function($) {
    $('#reset_button').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: resetShipmentsVars.ajax_url,
            type: 'POST',
            data: {
                action: 'reset_custom_shipments'
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                console.log('Failed to reset session.', error);
            }
        });
    });
});