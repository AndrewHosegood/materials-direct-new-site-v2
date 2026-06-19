<?php
add_action( 'wpo_wcpdf_custom_styles', function( $document_type, $document ) {
	if ( $order = $document->order ) {
		?>
			.meta .weight, .item-meta .weight {
				display: none;
			}
		<?php
	}
}, 10 , 2 );