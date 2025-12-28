<?php
add_filter( 'woocommerce_email_styles', 'custom_woocommerce_email_table_styles' );
function custom_woocommerce_email_table_styles( $css ) {
  	$css .= "
        #body_content_inner_cell #body_content_inner .email-introduction {
            padding-bottom: 0;
        }
        #body_content_inner .email-order-detail-heading {
            color: #ef9003;
            line-height: 22px;
        }
        #body_content_inner .email-order-detail-heading a {
            color: #ef9003;
        }
        #body_content_inner .email-order-detail-heading .link {
            color: #ef9003;
            font-weight: bold;
            line-height: 22px;
        }
        #template_header_image {
            padding: 28px 32px;
            text-align: center;
        }
        #template_header_image p img {
            width: 175px;
        }
        #template_body td#body_content {
            background: #f4f4f4 !important;
        }
		#template_body table.email-order-details  {
			border: 1px solid #ccc !important;
		}
        #template_body table.email-order-details thead th  {
			padding: 8px 12px !important;
            border-bottom: 1px solid #ccc;
		}
        #template_body table.email-order-details thead th:nth-child(3) {
            border-left: 1px solid #ccc !important;
            text-align: right !important;
        }
        #template_body table.email-order-details .order_item td:nth-child(3) {
            border-left: 1px solid #ccc !important;
            text-align: right !important;
            padding: 8px 12px !important;
        }
        #template_body table.email-order-details thead tr {
            border-bottom: 1px solid #ccc !important;
        }
        #template_body table.email-order-details .order-item-data tr td:nth-child(1) {
            display: none !important;
        }
        #template_body table.email-order-details .order-item-data tr td {
            padding: 8px 12px !important;
        }
        #template_body table.email-order-details .order-item-data .email-order-item-meta {
            margin: 10px 0;
            line-height: 170%;
        }
        #template_body table.email-order-details .order-item-data .email-order-item-meta span {
            font-weight: bold;
        }
        #template_body table.email-order-details .order-item-data .wc-email-product-link {
            color: #ef9003;
        }
        #template_body table.email-order-details .order_item td {
            border: 0px solid #ccc !important;
        }
        #template_body table.email-order-details tfoot .order-totals th {
            padding: 8px 12px !important;
            border-top: 1px solid #ccc !important;
        }
        #template_body table.email-order-details tfoot .order-totals td {
            padding: 8px 12px !important;
            border-top: 1px solid #ccc !important;
            border-left: 1px solid #ccc !important;
        }
        #template_body table.email-order-details tfoot .woocommerce-Price-amount {
            font-size: 16px;
        }
        #addresses .address-title {
            color: #ef9003;
            margin: 10px 0;
            display: block;
        }
        #addresses .address {
            border: 1px solid #ccc;
            padding: 10px;
            font-style: italic;
        }
        .email-additional-content {
            display: none;
        }
	";
	return $css;
}







add_filter( 'woocommerce_order_item_name', 'add_product_link_to_order_item_name', 10, 3 );
function add_product_link_to_order_item_name( $item_name, $item, $is_visible ) {

    // Only modify output in emails
    if ( ! did_action( 'woocommerce_email_header' ) ) {
        return $item_name;
    }

    $item_name = str_replace( '™', '', $item_name );

    // Get the product
    $product = $item->get_product();

    if ( ! $product ) {
        return $item_name;
    }

    // Get product permalink
    $permalink = get_permalink( $product->get_id() );

    if ( ! $permalink ) {
        return $item_name;
    }

    // Wrap product name in a link
    $item_name = sprintf(
        '<a href="%s" class="wc-email-product-link">%s</a>',
        esc_url( $permalink ),
        esc_html( $item_name )
    );

    return $item_name;
}
