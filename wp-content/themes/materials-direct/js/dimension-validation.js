jQuery(document).ready(function($) {

    $('#input_width, #input_length').on('keyup', function () {
        const width = parseFloat($('#input_width').val()) || 0;
        const length = parseFloat($('#input_length').val()) || 0;

        const $button = $('#generate_price');

        if (width > 300 || length > 1000) {
            if (width > 300) {
                alert("Width exceeds allowed stock sheet width");
            }
            if (length > 1000) {
                alert("Length exceeds allowed stock sheet length");
            }

            // Disable the button
            $button.prop('disabled', true);
        } else {
            // Re-enable the button if values are within limits
            $button.prop('disabled', false);
        }
    });

});


/*
jQuery(document).ready(function($) {
   
        $('#input_width, #input_length').on('keyup', function () {
        const width = parseFloat($('#input_width').val()) || 0;
        const length = parseFloat($('#input_length').val()) || 0;

        if (width > 300) {
            alert("Width exceeds allowed stock sheet width");
        }

        if (length > 1000) {
            alert("Length exceeds allowed stock sheet length");
        }
    });

});
*/