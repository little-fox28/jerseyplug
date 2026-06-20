<?php

/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

// We do not call do_action( 'woocommerce_cart_is_empty' ) here to prevent the default 
// WooCommerce info message from rendering above our custom UI.
?>

<div class="container mx-auto px-4 py-6 lg:py-10">
	<div class="text-center py-20 animate-in fade-in zoom-in duration-300">
		<div class="inline-flex p-6 bg-gray-50 rounded-full mb-6 text-gray-300">
			<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
				stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="8" cy="21" r="1" />
				<circle cx="19" cy="21" r="1" />
				<path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
			</svg>
		</div>
		<h2 class="text-2xl font-bold text-gray-900 mb-2"><?php esc_html_e('Your cart is empty', 'woocommerce'); ?></h2>
		<p class="text-gray-500 mb-8"><?php esc_html_e("Looks like you haven't added any gear yet.", 'woocommerce'); ?></p>
		<?php if (wc_get_page_id('shop') > 0) : ?>
			<a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>"
				class="inline-block px-8 py-3 bg-[#65cf21] text-white font-bold rounded-lg shadow-lg hover:bg-[#163300] hover:text-white transition-colors">
				<?php esc_html_e('Start Shopping', 'woocommerce'); ?>
			</a>
		<?php endif; ?>
	</div>
</div>