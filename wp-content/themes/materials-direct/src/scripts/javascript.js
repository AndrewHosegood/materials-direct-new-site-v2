jQuery(document).ready(function($){
        
        // Header onsroll event
        var $header = $('.header__main');
        var $header_container = $('.header__main-container');
        var $header_left = $('.header__left');
        var $header_right = $('.header__right');
        var $main_navigation = $('.main-navigation');
        var $ajax_search_container = $('#custom-search-results');
        //var $form = $('.is-search-form');
        var $form = $('.header__search-result-container');
        var scrollThreshold = 400;
        var throttleDelay = 100; // milliseconds
        var lastExecution = 0;
        function onScrollThrottled() {
            var now = Date.now();

            if (now - lastExecution >= throttleDelay) {
                lastExecution = now;

                if ($(window).scrollTop() >= scrollThreshold) {
                    $header.addClass('header__main-fixed');
                    $header_container.addClass('header__main-container-fixed');
                    $header_left.addClass('header__left-fixed');
                    $header_right.addClass('header__right-fixed');
                    $main_navigation.addClass('header__main-navigation-fixed');
                    $form.addClass('header__is-search-form-fixed');
                    $ajax_search_container.addClass('header__search-result-scroll');
                } else {
                    $header.removeClass('header__main-fixed');
                    $header_container.removeClass('header__main-container-fixed');
                    $header_left.removeClass('header__left-fixed');
                    $header_right.removeClass('header__right-fixed');
                    $main_navigation.removeClass('header__main-navigation-fixed');
                    $form.removeClass('header__is-search-form-fixed');
                    $ajax_search_container.removeClass('header__search-result-scroll');
                }
            }
        }
        $(window).on('scroll', onScrollThrottled);
    
        // on page load enable the generate price button
        $("#generate_price").prop("disabled", false);
        // on page load enable the generate price button

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

        // hide these elements on page load
        $('#cont_width_inches').hide();
        $('#cont_length_inches').hide();
        $('#cont_radius_inches').hide();
        $('#choose_inches_radius').hide();
    
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
            $("#input_radius_inches").val("");
            $('#input_qty_rolls').show();
            $("#use_inches").prop("checked", false);
            $('#cont_radius_inches').hide();
            $('#cont_width_inches').hide();
            $('#cont_length_inches').hide();
            $('#choose_inches').hide();
            $('#choose_inches_radius').hide();
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
            $("#input_radius_inches").val("");
            $('#choose_inches_radius').hide();
            $("#input_width_inches").val("");
            $("#input_length_inches").val("");
            $('#input_qty_rolls').hide();
            $("#use_inches").prop("checked", false);
            $('#cont_radius_inches').hide();
            $('#cont_width_inches').hide();
            $('#cont_length_inches').hide();
            $('#choose_inches').show();
            $('#choose_inches_radius').hide();
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
            $("#input_radius_inches").val("");
            $('#choose_inches_radius').hide();
            $('#input_qty_rolls').hide();
            $("#use_inches").prop("checked", false);
            $('#cont_radius_inches').hide();
            $('#cont_width_inches').hide();
            $('#cont_length_inches').hide();
            $('#choose_inches').hide();
            $('#choose_inches_radius').hide();
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
            $("#input_radius").prop('disabled', false);
            $("#input_radius_inches").prop('disabled', false);
            $('#choose_inches_radius').show();
            $('#choose_inches').hide();
            $('#input_qty_rolls').hide();
            $("#use_inches").prop("checked", false);
            $('.width-inches.product-page__input-wrap').css('display', 'none');
            $('.length-inches.product-page__input-wrap').css('display', 'none');
            $('.radius.product-page__input-wrap').addClass('radius-show');
            $('#cont_radius_inches').hide();
            $('#cont_width_inches').hide();
            $('#cont_length_inches').hide();
            $('#choose_inches').hide();
            $('#choose_inches_radius').show();
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
            $("#input_radius_inches").val("");
            $("#input_radius_inches").prop('disabled', true);
            $('#choose_inches_radius').hide();
            $("#input_width_inches").val("");
            $("#input_length_inches").val("");
            $('#input_qty_rolls').hide();
            $("#use_inches").prop("checked", false);
            $('#cont_width_inches').hide();
            $('#cont_length_inches').hide();
            $('#cont_radius_inches').hide();
            $('#choose_inches').show();
            $('#choose_inches_radius').hide();
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
        $('#input_qty').on('keyup', function() {
            let lengthVal2 = $("#input_length").val();
            let widthVal2 = $("#input_width").val();
            if(lengthVal2 <= 2 && widthVal2 <= 2){
                alert('Please add a width or length greater than 2');
                $("#generate_price").prop("disabled", true);
            }
	    });
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

        // add inch values to width field
        $('#input_width_inches').on('keyup', function() {
            var inch_width_keyup_value = $(this).val() * 25.4;
            console.log(inch_width_keyup_value);
            $("#input_width").val(inch_width_keyup_value);
            $("#generate_price").prop("disabled", false);
        });
        // add inch values to width field

        // add inch values to length field
        $('#input_length_inches').on('keyup', function() {
            var inch_length_keyup_value = $(this).val() * 25.4;
            console.log(inch_length_keyup_value);
            $("#input_length").val(inch_length_keyup_value);
            $("#generate_price").prop("disabled", false);
        });
        // add inch values to length field

        // add inch values to length field
        $('#input_radius_inches').on('keyup', function() {
            var value = $(this).val().trim(); // remove accidental spaces
            console.log(value);
            var inch_radius_keyup_value = '';
            if (value !== '') {
                inch_radius_keyup_value = value * 25.4 * 2;
            } else {
                return; // stop here if empty
            }
            console.log(inch_radius_keyup_value);
            $("#input_length").val(inch_radius_keyup_value);
            $("#input_width").val(inch_radius_keyup_value);
            $("#generate_price").prop("disabled", false);
        });
        // add inch values to length field

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

        // Inches calculations on product page
        $('#use_inches').on('click', function () {
                $('#input_width').val("");
                $('#input_length').val("");
                $('#input_radius').val("");
                $('#input_qty').val("");
        });
        $('#use_inches_radius').on('click', function () {
                $('#input_width').val("");
                $('#input_length').val("");
                $('#input_radius').val("");
                $('#input_qty').val("");
        });
        $('#use_inches_radius').on('change', function () {
            if ($(this).is(':checked')) {
                $('.product-page__input-wrap-radius').hide();
                $('#cont_radius_inches').show();
            } else {
                $('.product-page__input-wrap-radius').show();
                $('#cont_radius_inches').hide();
            }
        });
        $('#use_inches').on('change', function () {
            if ($(this).is(':checked')) {
                $('#cont_width_inches').show();
                $('#cont_length_inches').show();
                $('#cont_width_mm').hide();
                $('#cont_length_mm').hide();
            } else {
                $('#cont_width_inches').hide();
                $('#cont_length_inches').hide();
                $('#cont_width_mm').show();
                $('#cont_length_mm').show();
            }
        });
        // Inches calculations on product page

        // Currency switcher active link
        $('.product-page__currency-switcher-link').on('click', function () {
            $(this).toggleClass("active-currency");
        });
        // Currency switcher active link

        // Delivery Options Modal Validation
        $(document).on('input', '.parts-input', function () {
            
            let value = $(this).val();
            let cleanValue = value.replace(/[^0-9]/g, '');
            cleanValue = cleanValue.replace(/^0+/, '');
            console.log("value:" + value);
            console.log("cleanValue:" + cleanValue);
            if (cleanValue === "") {
                cleanValue = "";
            }
            if (value !== cleanValue) {
                $(this).val(cleanValue);
            }
            if (cleanValue.length > 0) {
                $(".delivery-options-modal__submit").addClass("show");
            } else {
                $(".delivery-options-modal__submit").removeClass("show");
            }
	    });
        // Delivery Options Modal Validation

        // Adjust final smooth scroll position by 100px
        
        jQuery(function ($) {
            var headerOffset = 300;
            var scrollSpeed = 600;

            $('a[href*="#"]:not([href="#"])').not('.wc-tabs a').on('click', function (e) {
                var target = $(this.hash);

                if (target.length) {
                    e.preventDefault();

                    $('html, body').animate({
                        scrollTop: target.offset().top - headerOffset
                    }, scrollSpeed);
                }
            });

            if (window.location.hash) {
                var target = $(window.location.hash);

                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - headerOffset
                    }, scrollSpeed);
                }
            }
        });
        
        // Adjust final smooth scroll position by 100px

        // click event for header Advanced Search
        $('#custom-product-search-input').on('keyup', function () {
            if ($(window).width() > 960 && $('#custom-product-search-input').next('.injected-content').length === 0) {
                $('#custom-product-search-input').after('<div class="injected-content"><a class="is-advanced-search" href="/shop/#advanced-filter"><i class="fa-solid fa-magnifying-glass-plus"></i><span>Advanced search</span></a></div>');
            }
        });
        // click event for header Advanced Search

        /* Category filter show/hide */
        jQuery('.filter-btn-hide').on('click', function(event) {
            event.preventDefault(); // Prevent the default link behavior
            $('.filter-wrapper-inner').fadeToggle('slow');
            var currentText = $(this).text();
            $(this).text(currentText === 'Hide' ? 'Show' : 'Hide');
        });
        /* Category filter show/hide */

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
                '<span class="our-partners__nav-previous"><img src="/wp-content/themes/materials-direct/images/prev-1.svg" alt="Prev"></span>',
                '<span class="our-partners__nav-next"><img src="/wp-content/themes/materials-direct/images/next-1.svg" alt="Next"></span>'
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
                '<span class="our-partners__nav-previous"><img src="/wp-content/themes/materials-direct/images/prev-1.svg" alt="Prev"></span>',
                '<span class="our-partners__nav-next"><img src="/wp-content/themes/materials-direct/images/next-1.svg" alt="Next"></span>'
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