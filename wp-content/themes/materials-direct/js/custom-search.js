jQuery(document).ready(function($) {
    let timeout;
    const $results = $('#custom-search-results');
    const $spinner = $('.header__custom-search-results-spinner')
    $spinner.hide();

    $('#custom-product-search-input').on('keyup', function() {
        clearTimeout(timeout);
        const term = $(this).val().trim();
        $spinner.show();
        timeout = setTimeout(function() {
            if (term.length < 2) {
                $results.html('').hide();
                return;
            }

            //$results.html('<p>Loading...</p>').show();
            $results.html('<div class="custom-search-results-wrapper"><p>Loading...</p></div>').show();

            $.get(customSearch.ajax_url, {
                action: 'custom_product_search',
                term: term
            }, function(response) {
                if (response.success) {
                    $results.html(response.data.html);
                    $spinner.hide();
                } else {
                    $results.html('<p>Error.</p>');
                    $spinner.hide();
                }
            });
        }, 300);  // Debounce 300ms
    });

    // Optional: Hide results on click outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#custom-search-container').length) {
            $results.hide();
        }
    });
});