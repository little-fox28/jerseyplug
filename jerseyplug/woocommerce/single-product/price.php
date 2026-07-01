<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
?>
<p class="price mt-4 flex items-baseline gap-2 text-2xl font-black text-primary">
	<!-- Alpine calculated price (reacts to variations + personalization) -->
	<span x-text="formatCurrency(singleProductPrice)"></span>
	
	<!-- Native WooCommerce price string as fallback / SEO / initial load (hidden once Alpine kicks in) -->
	<span class="text-sm font-normal text-gray-500 line-through" x-show="false">
		<?php echo $product->get_price_html(); ?>
	</span>
</p>
