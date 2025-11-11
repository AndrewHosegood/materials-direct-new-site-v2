jQuery(document).ready(function($){
        // Burger menu
        $('#nav-icon4').click(function(){
            $(this).toggleClass('open');
        });
        // Burger menu

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

        // shipping address edit
        $(".shipping-address-form__saved-edit").on("click", function() {
            //alert("Hello");
            
            $(".shipping-address-form__saved").toggleClass("hide");
            $("#shipping-address-form").addClass("show");
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
            console.log(keyup_value);
            $("#input_length").val(keyup_value);
            $("#input_width").val(keyup_value);
            $("#generate_price").prop("disabled", false);
        });
        // add width and length values based on circle radius input

        // Owl Carousel
        $('.testimonials__carousel').owlCarousel({
            loop:true,
            margin:10,
            nav:false,
            autoplay: true,            
            autoplayTimeout: 3000,     
            autoplayHoverPause: true,  
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:1
                },
                1000:{
                    items:1
                }
            }
        });
        $('.our-partners__carousel').owlCarousel({
            loop:true,
            margin:10,
            nav:true,
            autoplay: true,            
            autoplayTimeout: 3000,     
            autoplayHoverPause: true, 
            navText: [
                '<span class="our-partners__nav-previous"><img src="http://localhost:8888//wp-content/themes/materials-direct/images/prev-1.svg" alt="Prev"></span>',
                '<span class="our-partners__nav-next"><img src="http://localhost:8888//wp-content/themes/materials-direct/images/next-1.svg" alt="Next"></span>'
            ],
            responsive:{
                0:{
                    items:2
                },
                600:{
                    items:3
                },
                960:{
                    items:4
                },
                1240:{
                    items:5
                }
            }
        });
        $('.banner').owlCarousel({
            loop:true,
            margin:10,
            nav:false,
            autoplay: true,            
            autoplayTimeout: 8000,     
            autoplayHoverPause: true,  
            items: 1, // fade works only with one item
            animateOut: 'fadeOut', // enable fade
            smartSpeed: 700, // optional: smooth fade transition
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:1
                },
                1000:{
                    items:1
                }
            }
        });
        // Owl Carousel
}); 