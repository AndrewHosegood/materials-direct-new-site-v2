<?php
function add_custom_script_to_product_page() {
    if ( is_product() ) { 
        ?>
        <script>
        (function(n,t,i,r){
            var u,f;
            n[i]=n[i]||{},
            n[i].initial={accountCode:"ZXGHD28272",host:"ZXGHD28272.pcapredict.com"},
            n[i].on=n[i].on||function(){(n[i].onq=n[i].onq||[]).push(arguments)},
            u=t.createElement("script"),
            u.async=!0,
            u.src=r,
            f=t.getElementsByTagName("script")[0],
            f.parentNode.insertBefore(u,f)
        })(window,document,"pca","//ZXGHD28272.pcapredict.com/js/sensor.js")
        </script>
		<script>pca.on("options", function(type, key, options) { options.suppressAutocomplete =false;});</script>

        <?php
    }
}
add_action( 'wp_head', 'add_custom_script_to_product_page' );