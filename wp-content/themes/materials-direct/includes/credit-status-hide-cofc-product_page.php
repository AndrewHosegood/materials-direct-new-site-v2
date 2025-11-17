<?php
function my_hide_optional_fees_for_credit_users() {

    // Only run on single product pages
    if ( ! is_product() ) {
        return;
    }

    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return;
    }

    // Fetch user ACF credit settings
    $credit_options = get_field( 'credit_options', 'user_' . $user_id );
    $allow_credit   = isset( $credit_options['allow_user_credit_option'] ) 
                        ? (bool) $credit_options['allow_user_credit_option'] 
                        : false;

    ?>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            //const $element = $('#cofc_hide_show');

            <?php if ( $allow_credit == 1 ) {
                $('#cofc_hide_show').addClass("hide");
            } else {
                $('#cofc_hide_show').removeClass("hide");
            } ?>



        });
    </script>

    <?php
}
add_action( 'wp_footer', 'my_hide_optional_fees_for_credit_users' );
