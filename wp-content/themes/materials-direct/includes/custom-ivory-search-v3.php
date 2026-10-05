<?php

// AJAX handler for logged-in and non-logged-in users
add_action('wp_ajax_custom_product_search', 'custom_product_search_handler');
add_action('wp_ajax_nopriv_custom_product_search', 'custom_product_search_handler');


function custom_product_search_handler() {

    global $wpdb;


    /*
     * ---------------------------------------------------------
     * GET ORIGINAL SEARCH TERM
     * ---------------------------------------------------------
     */

    $original_term = isset($_GET['term'])
        ? sanitize_text_field($_GET['term'])
        : '';


    /*
     * Normalise the original search term.
     */

    $term = normalize_search_term($original_term);


    if (empty($term)) {

        wp_send_json_success([
            'html' => ''
        ]);
    }


    /*
     * ---------------------------------------------------------
     * BUILD FLEXIBLE SEARCH TOKENS
     * ---------------------------------------------------------
     */

    $clean_search = $term;


    /*
     * Remove trademark / registered / copyright symbols.
     */

    $clean_search = str_replace(
        ['™', '®', '©'],
        '',
        $clean_search
    );


    /*
     * ---------------------------------------------------------
     * SPECIAL T-PAD HANDLING
     * ---------------------------------------------------------
     *
     * t-pad
     * t pad
     * t_pad
     * t.pad
     *
     * all become:
     *
     * tpad
     */

    $clean_search = preg_replace(
        '/\bt[\s\-_\.]+pad\b/i',
        'tpad',
        $clean_search
    );


    /*
     * ---------------------------------------------------------
     * SPLIT INTO TOKENS
     * ---------------------------------------------------------
     */

    $tokens = preg_split(
        '/\s+/',
        $clean_search,
        -1,
        PREG_SPLIT_NO_EMPTY
    );


    $clean_tokens = [];


    foreach ($tokens as $token) {


        /*
         * Remove punctuation which should not affect searching.
         *
         * Decimal points are deliberately retained.
         */

        $token = preg_replace(
            '/[|,;:!?()\[\]{}"\'`]/u',
            '',
            $token
        );


        /*
         * Remove separator characters.
         *
         * Decimal points remain.
         */

        $token = str_replace(
            ['-', '–', '—', '−', '‒', '_'],
            '',
            $token
        );


        /*
         * Remove isolated dots but retain decimal points.
         */

        $token = preg_replace(
            '/(?<!\d)\.(?!\d)/u',
            '',
            $token
        );


        $token = trim($token);


        if ($token === '') {
            continue;
        }


        /*
         * Ignore standalone one-character tokens.
         *
         * This prevents "t" from creating an extremely broad
         * search.
         *
         * A0, A1 etc. remain because they contain 2 characters.
         */

        if (mb_strlen($token) === 1) {
            continue;
        }


        $clean_tokens[] = $token;
    }


    /*
     * Remove duplicate tokens.
     */

    $clean_tokens = array_values(
        array_unique($clean_tokens)
    );


    /*
     * ---------------------------------------------------------
     * PRIMARY SEARCH
     * ---------------------------------------------------------
     *
     * This remains the original fast substring search.
     *
     * Searches:
     *
     * - Product title
     * - Short description
     * - Full description
     * - SKU
     * - AJAX search keywords
     */

    $search = '%' . $wpdb->esc_like($term) . '%';


    $where_params = [
        $search, // title
        $search, // excerpt
        $search, // content
        $search, // SKU
        $search  // AJAX search keywords
    ];


    /*
     * NOTE:
     *
     * AJAX keywords are deliberately included separately below
     * in the search condition.
     */


    /*
     * ---------------------------------------------------------
     * FLEXIBLE TOKEN SEARCH
     * ---------------------------------------------------------
     */

    $token_conditions = [];


    foreach ($clean_tokens as $token) {


        /*
         * Normal token search.
         */

        $token_search = '%' . $wpdb->esc_like($token) . '%';


        /*
         * -----------------------------------------------------
         * CREATE SEPARATOR-INSENSITIVE TOKEN
         * -----------------------------------------------------
         *
         * Example:
         *
         * HT800
         *
         * matches:
         *
         * HT-800
         * HT 800
         * HT_800
         */

        $normalised_token = str_replace(
            ['-', '_', ' '],
            '',
            $token
        );


        $normalised_search =
            '%' .
            $wpdb->esc_like($normalised_token) .
            '%';


        /*
         * -----------------------------------------------------
         * NORMALISED DATABASE EXPRESSIONS
         * -----------------------------------------------------
         */

        $normalised_title = "
            REPLACE(
                REPLACE(
                    REPLACE(
                        p.post_title,
                        '-',
                        ''
                    ),
                    '_',
                    ''
                ),
                ' ',
                ''
            )
        ";


        $normalised_excerpt = "
            REPLACE(
                REPLACE(
                    REPLACE(
                        p.post_excerpt,
                        '-',
                        ''
                    ),
                    '_',
                    ''
                ),
                ' ',
                ''
            )
        ";


        $normalised_content = "
            REPLACE(
                REPLACE(
                    REPLACE(
                        p.post_content,
                        '-',
                        ''
                    ),
                    '_',
                    ''
                ),
                ' ',
                ''
            )
        ";


        $normalised_sku = "
            REPLACE(
                REPLACE(
                    REPLACE(
                        pm_sku.meta_value,
                        '-',
                        ''
                    ),
                    '_',
                    ''
                ),
                ' ',
                ''
            )
        ";


        $normalised_keywords = "
            REPLACE(
                REPLACE(
                    REPLACE(
                        pm_keywords.meta_value,
                        '-',
                        ''
                    ),
                    '_',
                    ''
                ),
                ' ',
                ''
            )
        ";


        /*
         * -----------------------------------------------------
         * TOKEN MATCH CONDITION
         * -----------------------------------------------------
         */

        $token_conditions[] = "
            (
                p.post_title LIKE %s
                OR p.post_excerpt LIKE %s
                OR p.post_content LIKE %s
                OR pm_sku.meta_value LIKE %s
                OR pm_keywords.meta_value LIKE %s

                OR {$normalised_title} LIKE %s
                OR {$normalised_excerpt} LIKE %s
                OR {$normalised_content} LIKE %s
                OR {$normalised_sku} LIKE %s
                OR {$normalised_keywords} LIKE %s
            )
        ";


        /*
         * Normal searches.
         */

        $where_params[] = $token_search;
        $where_params[] = $token_search;
        $where_params[] = $token_search;
        $where_params[] = $token_search;
        $where_params[] = $token_search;


        /*
         * Separator-insensitive searches.
         */

        $where_params[] = $normalised_search;
        $where_params[] = $normalised_search;
        $where_params[] = $normalised_search;
        $where_params[] = $normalised_search;
        $where_params[] = $normalised_search;
    }


    /*
     * ---------------------------------------------------------
     * DETERMINE WHETHER FLEXIBLE SEARCH IS REQUIRED
     * ---------------------------------------------------------
     */

    $flexible_search_required = false;


    if (count($clean_tokens) > 1) {

        $flexible_search_required = true;

    } elseif (count($clean_tokens) === 1) {

        if (
            strtolower($clean_tokens[0])
            !==
            strtolower(trim($term))
        ) {

            $flexible_search_required = true;
        }
    }


    /*
     * ---------------------------------------------------------
     * FLEXIBLE SEARCH SQL
     * ---------------------------------------------------------
     */

    $flexible_search_sql = '';


    if (
        $flexible_search_required
        &&
        !empty($token_conditions)
    ) {

        $flexible_search_sql = "
            OR (
                " .
                implode(
                    "\n                AND ",
                    $token_conditions
                )
                . "
            )
        ";
    }


    /*
     * ---------------------------------------------------------
     * RELEVANCE SCORING
     * ---------------------------------------------------------
     *
     * The following values are calculated for every matching
     * product.
     *
     * Higher score = better match.
     *
     * We deliberately keep the scoring relatively simple.
     */


    /*
     * ---------------------------------------------------------
     * SCORE 1
     * EXACT PHRASE IN PRODUCT TITLE
     * ---------------------------------------------------------
     *
     * Highest relevance.
     */

    $title_exact_score = $wpdb->prepare(
        "
        CASE
            WHEN p.post_title LIKE %s
            THEN 1000
            ELSE 0
        END
        ",
        $search
    );


    /*
     * ---------------------------------------------------------
     * SCORE 2
     * EXACT PHRASE IN AJAX KEYWORDS
     * ---------------------------------------------------------
     */

    $keywords_exact_score = $wpdb->prepare(
        "
        CASE
            WHEN pm_keywords.meta_value LIKE %s
            THEN 700
            ELSE 0
        END
        ",
        $search
    );


    /*
     * ---------------------------------------------------------
     * SCORE 3
     * EXACT PHRASE IN SHORT DESCRIPTION
     * ---------------------------------------------------------
     */

    $excerpt_exact_score = $wpdb->prepare(
        "
        CASE
            WHEN p.post_excerpt LIKE %s
            THEN 300
            ELSE 0
        END
        ",
        $search
    );


    /*
     * ---------------------------------------------------------
     * SCORE 4
     * EXACT PHRASE IN FULL DESCRIPTION
     * ---------------------------------------------------------
     */

    $content_exact_score = $wpdb->prepare(
        "
        CASE
            WHEN p.post_content LIKE %s
            THEN 200
            ELSE 0
        END
        ",
        $search
    );


    /*
     * ---------------------------------------------------------
     * TOKEN SCORE
     * ---------------------------------------------------------
     *
     * Each token found in the product title receives a strong
     * score.
     *
     * Each token found in the AJAX keywords receives a slightly
     * lower score.
     *
     * Description matches receive a lower score again.
     */


    $token_score_parts = [];


    foreach ($clean_tokens as $token) {

        $token_search = '%' . $wpdb->esc_like($token) . '%';


        /*
         * Title token score.
         */

        $token_score_parts[] = $wpdb->prepare(
            "
            CASE
                WHEN p.post_title LIKE %s
                THEN 100
                ELSE 0
            END
            ",
            $token_search
        );


        /*
         * AJAX keyword token score.
         */

        $token_score_parts[] = $wpdb->prepare(
            "
            CASE
                WHEN pm_keywords.meta_value LIKE %s
                THEN 60
                ELSE 0
            END
            ",
            $token_search
        );


        /*
         * Short description token score.
         */

        $token_score_parts[] = $wpdb->prepare(
            "
            CASE
                WHEN p.post_excerpt LIKE %s
                THEN 20
                ELSE 0
            END
            ",
            $token_search
        );


        /*
         * Full description token score.
         */

        $token_score_parts[] = $wpdb->prepare(
            "
            CASE
                WHEN p.post_content LIKE %s
                THEN 10
                ELSE 0
            END
            ",
            $token_search
        );
    }


    /*
     * Combine token scoring.
     */

    $token_score_sql = '';


    if (!empty($token_score_parts)) {

        $token_score_sql =
            ' + ' .
            implode(
                ' + ',
                $token_score_parts
            );
    }


    /*
     * ---------------------------------------------------------
     * TOTAL RELEVANCE SCORE
     * ---------------------------------------------------------
     */

    $relevance_score_sql = "
        (
            {$title_exact_score}
            +
            {$keywords_exact_score}
            +
            {$excerpt_exact_score}
            +
            {$content_exact_score}
            {$token_score_sql}
        )
    ";


    /*
     * ---------------------------------------------------------
     * DATABASE QUERY
     * ---------------------------------------------------------
     */

    $sql = $wpdb->prepare("
        SELECT DISTINCT

            p.ID,

            p.post_title,

            p.post_excerpt,

            p.post_content,

            pm_priority.meta_value AS search_priority,

            pm_sku.meta_value AS sku,

            {$relevance_score_sql} AS relevance_score


        FROM {$wpdb->posts} p


        LEFT JOIN {$wpdb->postmeta} pm_priority

            ON pm_priority.post_id = p.ID

            AND pm_priority.meta_key = 'search_priority'


        LEFT JOIN {$wpdb->postmeta} pm_sku

            ON pm_sku.post_id = p.ID

            AND pm_sku.meta_key = '_sku'


        LEFT JOIN {$wpdb->postmeta} pm_keywords

            ON pm_keywords.post_id = p.ID

            AND pm_keywords.meta_key = 'ajax_search_keywords'


        WHERE

            p.post_type = 'product'

            AND p.post_status = 'publish'


            AND

            (

                /*
                 * -------------------------------------------------
                 * ORIGINAL FAST SEARCH
                 * -------------------------------------------------
                 */

                p.post_title LIKE %s

                OR p.post_excerpt LIKE %s

                OR p.post_content LIKE %s

                OR pm_sku.meta_value LIKE %s

                OR pm_keywords.meta_value LIKE %s


                /*
                 * -------------------------------------------------
                 * FLEXIBLE TOKEN SEARCH
                 * -------------------------------------------------
                 */

                {$flexible_search_sql}

            )


        /*
         * -----------------------------------------------------
         * RELEVANCE ORDERING
         * -----------------------------------------------------
         *
         * Highest relevance first.
         *
         * Existing search_priority then acts as a secondary
         * ordering mechanism.
         *
         * Finally product title provides a predictable tie-break.
         */

        ORDER BY

            relevance_score DESC,

            (pm_priority.meta_value IS NULL) ASC,

            pm_priority.meta_value ASC,

            p.post_title ASC


        LIMIT 25

    ", $where_params);


    /*
     * Execute query.
     */

    $results = $wpdb->get_results($sql);


    /*
     * ---------------------------------------------------------
     * BUILD RESULTS HTML
     * ---------------------------------------------------------
     */

    if (empty($results)) {

        $html = '<div class="custom-search-results-wrapper">';
        $html .= '<p>No products found.</p>';
        $html .= '</div>';

    } else {

        $html = '<div class="custom-search-results-wrapper">';
        $html .= '<ul class="custom-search-results">';


        foreach ($results as $row) {

            $title = esc_html(
                $row->post_title
            );


            $permalink = get_permalink(
                $row->ID
            );


            $excerpt = !empty($row->post_excerpt)

                ? truncate_plain_text(
                    $row->post_excerpt,
                    150
                )

                : truncate_plain_text(
                    $row->post_content,
                    150
                );


            $sku = !empty($row->sku)

                ? ' (SKU: ' .
                    esc_html($row->sku) .
                    ')'

                : '';


            $html .= '<li>';


            $html .= '<a class="search-result-title" href="'
                . esc_url($permalink)
                . '">';


            $html .= '<strong>'
                . $title
                . $sku
                . '</strong>';


            $html .= '</a>';


            $html .= '<br>';


            $html .= '<p class="search-result-text">'
                . esc_html($excerpt)
                . '</p>';


            $html .= '</li>';
        }


        $html .= '</ul>';
        $html .= '</div>';


        /*
         * View more footer.
         */

        $html .= '<div class="custom-search-view-more">';

        $html .= '<p class="header__view-more">';

        $html .= '<a href="/shop/">View more results →</a>';

        $html .= '</p>';

        $html .= '</div>';
    }


    /*
     * Return AJAX response.
     */

    wp_send_json_success([
        'html' => $html
    ]);
}


/*
 * ---------------------------------------------------------
 * TRUNCATE FUNCTION
 * ---------------------------------------------------------
 */

function truncate_plain_text($text, $limit = 150) {

    $text = wp_strip_all_tags($text);


    $text = html_entity_decode(
        $text,
        ENT_QUOTES,
        'UTF-8'
    );


    $text = trim(
        preg_replace(
            '/\s+/',
            ' ',
            $text
        )
    );


    if (mb_strlen($text) <= $limit) {
        return $text;
    }


    $truncated = mb_substr(
        $text,
        0,
        $limit
    );


    $truncated = preg_replace(
        '/\s+\S*$/',
        '',
        $truncated
    );


    return $truncated . '…';
}


/*
 * ---------------------------------------------------------
 * NORMALISE SEARCH TERM
 * ---------------------------------------------------------
 */

function normalize_search_term($term) {

    /*
     * Replace typographic dash characters with a standard
     * hyphen.
     */

    $term = str_replace(
        ['–', '—', '−', '‒'],
        '-',
        $term
    );


    /*
     * Normalise whitespace.
     */

    $term = preg_replace(
        '/\s+/u',
        ' ',
        $term
    );


    /*
     * Trim.

     */

    $term = trim($term);


    return $term;
}


/*
 * ---------------------------------------------------------
 * ENQUEUE JAVASCRIPT
 * ---------------------------------------------------------
 */

add_action('wp_enqueue_scripts', function() {

    wp_enqueue_script(
        'custom-search',
        get_template_directory_uri() . '/js/custom-search.js',
        ['jquery'],
        null,
        true
    );


    wp_localize_script(
        'custom-search',
        'customSearch',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('custom_search')
        ]
    );
});