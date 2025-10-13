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

        $('#input_radius').closest("label").hide();

        // display active class for product page tabs
        $("#custom_drawing").closest("li").addClass("active");

        // Custom drawing click
        $("#custom_drawing").on("click", function() {
            $(".product-page__tabs-list").removeClass("active");
            $(this).closest("li").addClass("active");
        });

        // Circle Radius click
        $("#circle-radius").on("click", function() {
            $(".product-page__tabs-list").removeClass("active");
            $(this).closest("li").addClass("active");
        });

        // Square Rectangle click
        $("#square_rectangle").on("click", function() {
            $(".product-page__tabs-list").removeClass("active");
            $(this).closest("li").addClass("active");
        });

        // Stock Sheets click
        $("#stock_sheets").on("click", function() {
            $(".product-page__tabs-list").removeClass("active");
            $(this).closest("li").addClass("active");
        });

        // hide/show file uploads when square rectangle/circle radius is clicked
        $("#square_rectangle").click(function(){
            $('#circle-radius').closest(".product-page__tabs-label").removeClass("active");
            $('#custom_drawing').closest(".product-page__tabs-label").removeClass("active");
            $('#pdf_upload_label').hide();
            $('#uploadPdf').hide();
            $('#pdf_upload_text').hide();
            $('#dxf_upload_label').hide();
            $('#uploadDxf').hide();
            $('#drawing_guide').hide();
            $('#input_width').closest("label").show();
            $('#input_length').closest("label").show();
            $('#input_radius').closest("label").hide();
            $("#input_radius").val("");
        });
        $("#stock_sheets").click(function(){
            $('#circle-radius').closest(".product-page__tabs-label").removeClass("active");
            $('#custom_drawing').closest(".product-page__tabs-label").removeClass("active");
            $('#pdf_upload_label').hide();
            $('#uploadPdf').hide();
            $('#pdf_upload_text').hide();
            $('#dxf_upload_label').hide();
            $('#uploadDxf').hide();
            $('#drawing_guide').hide();
            $('#input_width').closest("label").show();
            $('#input_length').closest("label").show();
            $('#input_radius').closest("label").hide();
            $("#input_radius").val("");
        });
        $("#circle-radius").click(function(){
            $('#pdf_upload_label').hide();
            $('#uploadPdf').hide();
            $('#pdf_upload_text').hide();
            $('#dxf_upload_label').hide();
            $('#uploadDxf').hide();
            $('#drawing_guide').hide();
            $('#input_width').closest("label").hide();
            $('#input_length').closest("label").hide();
            $('#input_radius').closest("label").show();
        });
        $("#custom_drawing").click(function(){
            $('#pdf_upload_label').show();
            $('#uploadPdf').show();
            $('#pdf_upload_text').show();
            $('#dxf_upload_label').show();
            $('#uploadDxf').show();
            $('#drawing_guide').show();
            $('#input_width').closest("label").show();
            $('#input_length').closest("label").show();
            $('#input_radius').closest("label").hide();
            $("#input_radius").val("");
        });
        // hide/show file uploads when square rectangle/circle radius is clicked

        // Change price to 'calculating' when 'add shipments' is clicked
        jQuery(document).ready(function($) {
            $('#add_shipments').on('click', function(e) {
                e.preventDefault(); // Prevent default anchor behavior
                $('.product-page__display-price-text').text('Calculating');
            });
        });
        // Change price to 'calculating' when 'add shipments' is clicked

        // force users to enter 3 or more in width and length field
        /*
        $('#input_qty').on('keyup', function() {
            let lengthVal2 = $("#input_length").val();
            let widthVal2 = $("#input_width").val();
            if(lengthVal2 <= 2 && widthVal2 <= 2){
                alert('Please add a width or length greater than 2');
                $("#generate_price").prop("disabled", true);
            }
	    });
        */
        // force users to enter 3 or more in width and length field

        // add width and length values based on circle radius input
        $('#input_radius').on('keyup', function() {
            var keyup_value = $(this).val() * 2;
            $("#input_length").val(keyup_value);
            $("#input_width").val(keyup_value);
            $("#generate_price").prop("disabled", false);
        });
        // add width and length values based on circle radius input
}); 