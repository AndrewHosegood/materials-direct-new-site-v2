jQuery(document).ready(function($){
        // Footer scroll to top
        $('#back_to_top').on('click', function(e) {
            e.preventDefault(); 
            $('html, body').animate({
            scrollTop: 0
            }, 500);
        });
        // End footer scroll to top

        // hide #generate_price when add shipments is clicked
        $('#add_shipments').on('click', function(e) {
            e.preventDefault();
            $('#generate_price').prop('disabled', true);
        });
        // hide #generate_price when add shipments is clicked

        // scheduled shipments info modal
        $('#split_schedule_instructions_msg_3').on('click', function(e) {
            e.preventDefault();
            $('.scheduled-shipments-info__outer').fadeToggle();
        });
        $('.scheduled-shipments-info__outer').on('click', function(e) {
            if ($(e.target).closest('.delivery-options-modal').length === 0) {
                $(this).fadeOut();
            }
        });
        $('.scheduled-shipments-info__close-btn').on('click', function(e) {
            e.preventDefault();
            $('.scheduled-shipments-info__outer').fadeOut();
        });
        // scheduled shipments info modal

        jQuery(document).ready(function($) {
            $('#add_shipments').on('click', function(e) {
                e.preventDefault(); // Prevent default anchor behavior
                $('.product-page__display-price-text').text('Calculating');
            });
        });
        
}); 