jQuery(document).ready(function($) {
    if (checkout_override_data.shipping_country) {
        // Set the value, disable auto-complete to reduce browser interference, and trigger change/update
        $('#shipping_country').attr('autocomplete', 'off').val(checkout_override_data.shipping_country).trigger('change');
        $(document.body).trigger('update_checkout');

        // Optional delay to handle any timing issues with browser auto-fill
        setTimeout(function() {
            $('#shipping_country').val(checkout_override_data.shipping_country).trigger('change');
            $(document.body).trigger('update_checkout');
        }, 500);
    }
});