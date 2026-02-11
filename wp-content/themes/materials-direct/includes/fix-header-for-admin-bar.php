<?php
function fix_header_for_admin_bar() {
    if ( is_admin_bar_showing() ) {
        ?>
        <style>
            .header__main {
                top: 20px;
            }

            .header__main-fixed {
                top: 32px;
            }
            @media screen and (max-width: 782px) {
                .header__main-fixed {
                    top: 45px;
                }
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'fix_header_for_admin_bar');