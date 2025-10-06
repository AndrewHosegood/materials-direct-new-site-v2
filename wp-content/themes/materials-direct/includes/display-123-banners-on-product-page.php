<?php
function display_123_banners_on_product_page() {

?>

<div class="product-page__stages-heading">
    <h3 class="product-page__stages-heading-content">Tell us what to manufacture for you</h3>
</div>

<?php

}
add_action('woocommerce_before_add_to_cart_button', 'display_123_banners_on_product_page', 1);