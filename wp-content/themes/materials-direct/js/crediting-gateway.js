jQuery(function($) {

    if ($('tr.crediting-gateway-order').length) {
        $('tr.crediting-gateway-order td.order_status mark.order-status.status-pending span').each(function() {
            if ($(this).text().trim() === 'Pending payment') {
                $(this).text('Credit Account Processing');
            }
        });
    }

    if ($('body').hasClass('crediting-gateway-order-details')) {
        const selectors = [
            '.select2 .selection .select2-selection .select2-selection__rendered',
            '.select2-results__options .select2-results__option'
        ];

        selectors.forEach(function(selector) {
            $(selector).each(function() {
                if ($(this).text().trim() === 'Pending payment') {
                    $(this).text('Credit Account Processing');
                }
            });
        });
    }

});