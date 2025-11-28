<?php
function disable_width_length_qty_if_credit_account() {
    if (!is_product()) {
        return;
    }
    $user_id = get_current_user_id();
    $allow_credit = get_field('credit_options_allow_user_credit_option', 'user_' . $user_id);
    if (is_user_logged_in() && $allow_credit && !is_admin()) {
        ?>
        <script>
        jQuery(document).ready(function($) {

			let selectedTabCA

            if ($('input[name="allow_credit"]').val() === '1') {

                $('#generate_price').on('click', function() {

                    // Disable fields after generate_price click for credit users
                    $('#input_width, #input_length, #input_qty, #input_radius').prop('readonly', true);

					//product-page__grey-panel
                });
            }

			


        });
        </script>
        <?php
    }
}

add_action('wp_footer', 'disable_width_length_qty_if_credit_account');