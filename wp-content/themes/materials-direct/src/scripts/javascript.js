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
            $('.delivery-options-modal').addClass('active');
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

        $('#input_qty_rolls').hide();

        // display active class for product page tabs
        $("#custom_drawing").closest("li").addClass("active");

        // Custom drawing click
        $("#custom_drawing").on("click", function() {
            $(".product-page__tabs-list").removeClass("active");
            $(this).closest("li").addClass("active");
        });
         // Custom drawing click

        // Circle Radius click
        $("#circle-radius").on("click", function() {
            $(".product-page__tabs-list").removeClass("active");
            $(this).closest("li").addClass("active");
        });
        // Circle Radius click

        // Square Rectangle click
        $("#square_rectangle").on("click", function() {
            $(".product-page__tabs-list").removeClass("active");
            $(this).closest("li").addClass("active");
        });
        // Square Rectangle click

        // Stock Sheets click
        $("#stock_sheets").on("click", function() {
            $(".product-page__tabs-list").removeClass("active");
            $(this).closest("li").addClass("active");
        });
        // Stock Sheets click

        // Rolls click
        $("#rolls").on("click", function() {
            $(".product-page__tabs-list").removeClass("active");
            $(this).closest("li").addClass("active");
        });
        // Rolls click

        // shipping address edit
        $(".shipping-address-form__saved-edit").on("click", function() {
            $(".shipping-address-form__saved").toggleClass("hide");
            $("#shipping-address-form").addClass("show");
        });
        // shipping address edit

        // display cofc on product page 'on keyup'
        $('#input_qty').on('keyup change', function() {
            const qty = parseInt($(this).val(), 10);

            if (!isNaN(qty) && qty > 0) {
            $('.product-page__optional-fees').css('display', 'flex');
            } else {
            $('.product-page__optional-fees').css('display', 'none');
            }
        });

        // display cofc on product page 'on keyup' dor rolls
        $('#input_qty_rolls').on('keyup change', function() {
            const qty_rolls = parseInt($(this).val(), 10);

            if (!isNaN(qty_rolls) && qty_rolls > 0) {
            $('.product-page__optional-fees').css('display', 'flex');
            } else {
            $('.product-page__optional-fees').css('display', 'none');
            }
        });

        $('body').on('change', '#add_manufacturers_COFC', function() {
		    alert("Supplying the manufacturers CofC may effect the lead time");
	    });
        $('body').on('change', '#add_manufacturers_COFC_ss', function() {
		    alert("Supplying the manufacturers CofC may effect the lead time");
	    });
        // display cofc on product page 'on keyup'

        // need_help_with_ordering product page link
        $("#need_help_with_ordering").on("click", function(e) {
            e.preventDefault();
            $('.help-with-ordering__outer').fadeToggle();
        });
        $('.help-with-ordering__outer').on('click', function(e) {
            if ($(e.target).closest('.help-with-ordering').length === 0) {
                $(this).fadeOut();
            }
        });
        $('.help-with-ordering__close-btn').on('click', function(e) {
            e.preventDefault();
            $('.help-with-ordering__outer').fadeOut();
        });
        // need_help_with_ordering product page link

        // Voucher discount link
        $(document).on('click', '#voucherClick', function(e) {
            e.preventDefault();
            $(this).closest('tr').next('.voucher-discount').toggle();
        });
        // Voucher discount link

        // Mobile Contact Link
        $(document).on('click', '.header__mobile-contact-link', function(e) {
            e.preventDefault();
            $('.header__contact-details').toggleClass('header__contact-details-active');
            $('.header__mobile-contact-link a').toggleClass('active');

        });
    
        // hide/show file uploads when square rectangle/circle radius is clicked
        $("#rolls").click(function(){
            $('#circle-radius').closest(".product-page__tabs-label").removeClass("active");
            $('#custom_drawing').closest(".product-page__tabs-label").removeClass("active");
            $('#pdf_upload_label').hide();
            $('#uploadPdf').hide();
            $('#pdf_upload_text').hide();
            $('#dxf_upload_text').hide();
            $('#dxf_upload_label').hide();
            $('#uploadDxf').hide();
            $('#drawing_guide').hide();
            $('#input_width').closest("label").show();
            $('#input_length').closest("label").show();
            $('#input_radius').closest("label").hide();
            $("#input_radius").val("");
            $('#input_qty_rolls').show();
        });
        $("#square_rectangle").click(function(){
            $('#circle-radius').closest(".product-page__tabs-label").removeClass("active");
            $('#custom_drawing').closest(".product-page__tabs-label").removeClass("active");
            $('#pdf_upload_label').hide();
            $('#uploadPdf').hide();
            $('#pdf_upload_text').hide();
            $('#dxf_upload_text').hide();
            $('#dxf_upload_label').hide();
            $('#uploadDxf').hide();
            $('#drawing_guide').hide();
            $('#input_width').closest("label").show();
            $('#input_length').closest("label").show();
            $('#input_radius').closest("label").hide();
            $("#input_radius").val("");
            $('#input_qty_rolls').hide();
        });
        $("#stock_sheets").click(function(){
            $('#circle-radius').closest(".product-page__tabs-label").removeClass("active");
            $('#custom_drawing').closest(".product-page__tabs-label").removeClass("active");
            $('#pdf_upload_label').hide();
            $('#uploadPdf').hide();
            $('#pdf_upload_text').hide();
            $('#dxf_upload_text').hide();
            $('#dxf_upload_label').hide();
            $('#uploadDxf').hide();
            $('#drawing_guide').hide();
            $('#input_width').closest("label").show();
            $('#input_length').closest("label").show();
            $('#input_radius').closest("label").hide();
            $("#input_radius").val("");
            $('#input_qty_rolls').hide();
        });
        $("#circle-radius").click(function(){
            $('#pdf_upload_label').hide();
            $('#uploadPdf').hide();
            $('#pdf_upload_text').hide();
            $('#dxf_upload_text').hide();
            $('#dxf_upload_label').hide();
            $('#uploadDxf').hide();
            $('#drawing_guide').hide();
            $('#input_width').closest("label").hide();
            $('#input_length').closest("label").hide();
            $('#input_radius').closest("label").show();
            $('#input_qty_rolls').hide();
        });
        $("#custom_drawing").click(function(){
            $('#pdf_upload_label').show();
            $('#uploadPdf').show();
            $('#pdf_upload_text').show();
            $('#dxf_upload_text').show();
            $('#dxf_upload_label').show();
            $('#uploadDxf').show();
            $('#drawing_guide').show();
            $('#input_width').closest("label").show();
            $('#input_length').closest("label").show();
            $('#input_radius').closest("label").hide();
            $("#input_radius").val("");
            $('#input_qty_rolls').hide();
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

        // add quantity value based on rolls quantity input
        var rollLength = parseFloat($('#tabs_status_message_3').text());

        $('#input_qty_rolls').on('keyup', function() {
            var keyup_value_rolls = $(this).val() * rollLength;
            console.log(keyup_value_rolls);
            $("#input_qty").val(keyup_value_rolls);
        });
        // add quantity value based on rolls quantity input

        // faqs accordion
        $(".faqs__ui-accordion .faqs__ui-accordion-header").on("click", function() {

            const panelID = $(this).attr("aria-controls");
            const panel = $("#" + panelID);

            if (panel.is(":visible")) {
                panel.slideUp(200);
                $(this)
                    .removeClass("ui-state-active ui-accordion-header-active")
                    .addClass("ui-accordion-header-collapsed");
                return;
            }

            $(".faqs__ui-accordion .faqs__ui-accordion-content:visible").not(panel).slideUp(200);

            $(".faqs__ui-accordion .faqs__ui-accordion-header")
                .not(this)
                .removeClass("ui-state-active ui-accordion-header-active")
                .addClass("ui-accordion-header-collapsed");

            $(this)
                .removeClass("ui-accordion-header-collapsed")
                .addClass("ui-state-active ui-accordion-header-active");
            
            panel.slideDown(200);
        });
        // faqs accordion

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
        $('.manufacturing-services__carousel').owlCarousel({
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
                    items:1
                },
                494:{
                    items:2
                },
                768:{
                    items:3
                },
                1240:{
                    items:3
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
        $('.brands-banner').owlCarousel({
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