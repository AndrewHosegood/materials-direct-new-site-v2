jQuery(function($){

    $(".shipping-address-form__saved-edit").on("click", function(e){

        e.preventDefault();

        alert("working????!!");

        $(".shipping-address-form__saved").toggleClass("hide");
        $("#shipping-address-form").addClass("show");

        $.ajax({
            url: shippingAjax.ajaxurl,
            type: "POST",
            data: {
                action: "clear_custom_shipping_session_ah"
            }
        });

    });

});