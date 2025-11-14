<?php
add_action( 'woocommerce_after_customer_login_form', 'custom_account_banner' );

function custom_account_banner() {
    if ( ! is_user_logged_in() ) { ?>

<section class="featured-icons">
    <div class="featured-icons__container">
		<div class="featured-icons__column">
			<div class="featured-icons__icon-left">
				<img class="featured-icons__icon" alt="No Tooling Charge" src="http://localhost:8888/wp-content/uploads/2025/11/tooling-cost.svg">
			</div>
			<div class="featured-icons__content-right">
				<h6 class="featured-icons__heading">No Tooling Charge</h6>
				<p class="featured-icons__content">There is NEVER a tooling charge for manufacturing</p>
			</div>
		</div>
		<div class="featured-icons__column">
			<div class="featured-icons__icon-left">
				<img class="featured-icons__icon" alt="" src="http://localhost:8888/wp-content/uploads/2025/11/fast-manufacture.svg">
			</div>
			<div class="featured-icons__content-right">
				<h6 class="featured-icons__heading">Fast Manufacturing</h6>
				<p class="featured-icons__content">Parts made &amp; shipped worldwide in as little as 24 hours</p>
			</div>
		</div>
		<div class="featured-icons__column">
			<div class="featured-icons__icon-left">
				<img class="featured-icons__icon" alt="" src="http://localhost:8888/wp-content/uploads/2025/11/support.svg">
			</div>
			<div class="featured-icons__content-right">
				<h6 class="featured-icons__heading">Technical Support</h6>
				<p class="featured-icons__content">Technical expertise available from our specialists</p>
			</div>
		</div>
		<div class="featured-icons__column">
			<div class="featured-icons__icon-left">
				<img class="featured-icons__icon" alt="" src="http://localhost:8888/wp-content/uploads/2025/11/secure.svg">
			</div>
			<div class="featured-icons__content-right">
				<h6 class="featured-icons__heading">100% Payment Secure</h6>
				<p class="featured-icons__content">We ensure secure payment with a wide array of options</p>
			</div>
		</div> 
    </div>
</section>
<br><br>
   <?php  }
}