<?php
function add_admin_mobile_menu_fix() {
    if ( is_user_logged_in() && current_user_can('administrator') ) {
        ?>
        <style>
        @media screen and (max-width: 1100px) {
            #mega-menu-menu-1 {
                margin-top: 32px !important;
            }
            .mega-close {
                top: 2rem !important;
            }
        }    
        @media screen and (max-width: 782px) {
            #mega-menu-menu-1 {
                margin-top: 46px !important;
            }
            .mega-close {
                top: 2.8rem !important;
            }
        }
        </style>
        <?php
    }
}
add_action('wp_head', 'add_admin_mobile_menu_fix');