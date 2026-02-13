jQuery(document).ready(function($) {
    let timeout;
    let last_term = '';
    let last_html = '';

    const $input     = $('#custom-product-search-input');
    const $results   = $('#custom-search-results');
    const $spinner   = $('.header__custom-search-results-spinner');

    $spinner.hide();

    // Use 'input' instead of 'keyup' — it catches paste, autofill, mobile keyboards, etc.
    $input.on('input', function() {
        clearTimeout(timeout);
        const term = $(this).val().trim();

        // Clear results if term too short
        if (term.length < 2) {
            $results.html('').hide();
            $spinner.hide();
            return;
        }

        // Show spinner and temporary loading message
        $spinner.show();
        $results.html('<div class="custom-search-results-wrapper"><p>Loading...</p></div>').show();

        timeout = setTimeout(function() {
            $.get(customSearch.ajax_url, {
                action: 'custom_product_search',
                term: term
            }, function(response) {
                $spinner.hide();

                if (response.success) {
                    $results.html(response.data.html);
                    // Cache the successful results
                    last_term = term;
                    last_html = response.data.html;
                } else {
                    $results.html('<p>Error.</p>');
                    // Optionally cache error state too
                    last_html = '<p>Error.</p>';
                }
            });
        }, 300); // Debounce 300ms
    });

    // NEW: Re-show cached results when focusing the input if term matches last search
    $input.on('focus', function() {
        const term = $(this).val().trim();

        if (term.length >= 2 && term === last_term && last_html) {
            $results.html(last_html).show();
            // No spinner — instant from cache
        }
    });

    // Existing: Hide results when clicking outside the container
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#custom-search-container').length) {
            $results.hide();
        }
    });
});

/*
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
*/