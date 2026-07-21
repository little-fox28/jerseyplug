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

// Remove the default "Your cart is currently empty" message to use our custom UI
remove_action('woocommerce_cart_is_empty', 'wc_empty_cart_message', 10);
?>

<div class="max-w-2xl mx-auto text-center py-16 px-4">

	<!-- Empty Cart Icon / Graphic -->
	<div class="mb-8 inline-flex items-center justify-center w-32 h-32 rounded-full bg-gray-50 text-[#163300]/20 shadow-inner">
		<svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
		</svg>
	</div>

	<!-- Custom Message -->
	<h2 class="text-3xl md:text-4xl font-black text-[#163300] uppercase tracking-wider mb-4">
		<?php echo esc_html( jerseyplug_pll( 'Your cart is empty' ) ); ?>
	</h2>

	<p class="text-gray-500 mb-10 leading-relaxed max-w-md mx-auto font-medium">
		<?php echo esc_html( jerseyplug_pll( 'Looks like you haven\'t added anything to your cart yet. Browse our collections and find something you love!' ) ); ?>
	</p>

	<!-- Allow other plugins to hook in -->
	<?php do_action('woocommerce_cart_is_empty'); ?>

	<!-- Custom Return to Shop Button -->
	<?php if (wc_get_page_id('shop') > 0) : ?>
		<div class="mt-8">
			<a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" style="color: #f2c86c !important;" class="inline-flex items-center justify-center gap-3 bg-[#163300] font-black py-4 px-10 rounded-xl hover:bg-[#0a1700] hover:-translate-y-1 shadow-[0_4px_14px_0_rgba(22,51,0,0.39)] hover:shadow-[0_6px_20px_rgba(22,51,0,0.23)] transition-all duration-300 uppercase tracking-widest text-sm">
				<svg class="w-5 h-5" style="color: #f2c86c !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
				</svg>
				<?php echo esc_html(apply_filters('woocommerce_return_to_shop_text', jerseyplug_pll( 'Return to shop' ))); ?>
			</a>
		</div>
	<?php endif; ?>

</div>

<!-- Inline CSS to beautifully style the "Undo?" success notice matching the theme -->
<style>
	.woocommerce-message {
		background: #ffffff !important;
		border: 1px solid #e5e7eb !important;
		border-left: 6px solid #65cf21 !important;
		color: #1f2937 !important;
		border-radius: 0.75rem !important;
		padding: 1.25rem 1.5rem !important;
		display: flex !important;
		flex-wrap: wrap !important;
		align-items: center !important;
		justify-content: space-between !important;
		box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
		margin-bottom: 2rem !important;
		font-weight: 500 !important;
		gap: 1rem !important;
	}

	.woocommerce-message::before {
		content: none !important;
		/* Hide default icon */
	}

	.woocommerce-message a.restore-item {
		background-color: #163300 !important;
		color: #f2c86c !important;
		padding: 0.5rem 1.25rem !important;
		border-radius: 0.5rem !important;
		text-decoration: none !important;
		font-weight: 800 !important;
		text-transform: uppercase !important;
		font-size: 0.75rem !important;
		letter-spacing: 0.05em !important;
		transition: all 0.3s ease !important;
		box-shadow: 0 4px 6px -1px rgba(22, 51, 0, 0.2) !important;
	}

	.woocommerce-message a.restore-item:hover {
		background-color: #0f2400 !important;
		transform: translateY(-2px) !important;
	}
</style>