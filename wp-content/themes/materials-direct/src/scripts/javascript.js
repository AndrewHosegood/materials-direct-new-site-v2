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

        // display active class for product page tabs
        $(".product-page__tabs-input").click(function(){
            $(this).closest(".product-page__tabs-label").toggleClass("active");
            $('#custom_drawing').closest(".product-page__tabs-label").toggleClass("disable");
        });

        $("#custom_drawing").click(function(){
            $('#square_rectangle').closest(".product-page__tabs-label").removeClass("active");
        });
        // display active class for product page tabs

        // hide/show file uploads when square rectangle is clicked
        $("#square_rectangle").click(function(){
            $('#pdf_upload_label').hide();
            $('#uploadPdf').hide();
            $('#pdf_upload_text').hide();
            $('#dxf_upload_label').hide();
            $('#uploadDxf').hide();
            $('#drawing_guide').hide();
        });
        $("#custom_drawing").click(function(){
            $('#pdf_upload_label').show();
            $('#uploadPdf').show();
            $('#pdf_upload_text').show();
            $('#dxf_upload_label').show();
            $('#uploadDxf').show();
            $('#drawing_guide').show();
        });
        // hide/show file uploads when square rectangle is clicked

        jQuery(document).ready(function($) {
            $('#add_shipments').on('click', function(e) {
                e.preventDefault(); // Prevent default anchor behavior
                $('.product-page__display-price-text').text('Calculating');
            });
        });
        
}); 