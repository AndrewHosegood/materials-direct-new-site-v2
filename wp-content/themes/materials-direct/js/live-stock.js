jQuery(function($){

    $('.live-stock-wrapper').each(function(){

        var wrapper = $(this);
        var product_id = wrapper.data('product-id');

        $.post(live_stock_params.ajax_url, {
            action: 'get_live_stock',
            product_id: product_id
        }, function(response){

            wrapper.html(response);

        });

    });

});
