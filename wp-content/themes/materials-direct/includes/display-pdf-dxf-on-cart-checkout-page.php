<?php
add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'custom_pdf_dxf_formatted_meta', 15, 2 );

function custom_pdf_dxf_formatted_meta( $formatted_meta, $item ) {
    
    foreach ( $formatted_meta as $meta_id => $meta ) {
        
        if ( $meta->key === 'pdf_path' ) {
            $filename = basename( $meta->value );
            $full_url = '/wp-content/uploads' . $meta->value;
            
            $formatted_meta[$meta_id]->display_key   = 'Upload .PDF Drawing';
            $formatted_meta[$meta_id]->display_value = '<a href="' . esc_url($full_url) . '" target="_blank" rel="noopener">' . esc_html($filename) . '</a>';
        }
        
        if ( $meta->key === 'dxf_path' ) {
            $filename = basename( $meta->value );
            $full_url = '/wp-content/uploads' . $meta->value;
            
            $formatted_meta[$meta_id]->display_key   = 'Upload .DXF Drawing';
            $formatted_meta[$meta_id]->display_value = '<a href="' . esc_url($full_url) . '" target="_blank" rel="noopener">' . esc_html($filename) . '</a>';
        }
    }
    
    return $formatted_meta;
}
/*
add_filter( 'woocommerce_order_item_get_formatted_meta_data', function( $formatted_meta, $item ) {
    if ( is_admin() ) {
        return $formatted_meta; 
    }
    foreach ( $formatted_meta as $meta_id => $meta ) {
        if ( $meta->key === 'pdf_path' ) {
            unset( $formatted_meta[$meta_id] ); // remove pdf
        }
		if ( $meta->key === 'dxf_path' ) {
            unset( $formatted_meta[$meta_id] ); // remove dxf
        }
    }
    return $formatted_meta;
}, 10, 2 );

add_action( 'woocommerce_order_item_meta_end', function( $item_id, $item, $order ) {
    $pdf_path = $item->get_meta( 'pdf_path' );
	$dxf_path = $item->get_meta( 'dxf_path' );

    if ( ! empty( $pdf_path ) ) {
        $filename_pdf = basename( $pdf_path );
        echo '<ul class="wc-item-meta"><li><strong class="wc-item-meta-label">Upload .PDF Drawing:</strong> 
                <p><a href="/wp-content/uploads' . esc_url( $pdf_path ) . '" target="_blank" rel="noopener">' . esc_html( $filename_pdf ) . '</a></p>
              </li></ul>';
    }
	if ( ! empty( $dxf_path ) ) {
        $filename_dxf = basename( $dxf_path );
        echo '<ul class="wc-item-meta"><li><strong class="wc-item-meta-label">Upload .DXF Drawing:</strong> 
                <p><a href="/wp-content/uploads' . esc_url( $dxf_path ) . '" target="_blank" rel="noopener">' . esc_html( $filename_dxf ) . '</a></p>
              </li></ul>';
    }
}, 10, 3 );
*/