<?php
add_action( 'wp_head', 'get_the_roll_length_value_form_insert' );

function get_the_roll_length_value_form_insert() {

	// Only run on single product pages
    if ( ! is_product() ) {
        return;
    }

	$ah_roll_length = (float) get_field('roll_length');

    ?>

    <script type="text/javascript">

		jQuery(document).ready(function($) {

			$("#rolls").click(function(){ 

				let rollLength = <?php echo $ah_roll_length; ?>;

				if ($('.rollsLengthInput').length === 0) { 
					$('#cont_length_mm .product-page__rolls-label-text-1').after(
						`<div class="rollsLengthInput">
							${rollLength / 1000}
						</div>`
					);
				}
				
			});

    });
    </script>

    <?php
}