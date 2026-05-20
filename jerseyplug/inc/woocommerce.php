<?php
/**
 * WooCommerce-specific hooks and helpers.
 *
 * @package JerseyPlug
 */

/**
 * Optimize WooCommerce scripts.
 */
function jerseyplug_optimize_woocommerce_scripts() {
	if ( function_exists( 'is_woocommerce' ) ) {
		// Nếu không phải trang thuộc WooCommerce thì dẹp bỏ script của nó
		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'wc-add-to-cart' );

			// Loại bỏ các style của WooCommerce
			wp_dequeue_style( 'woocommerce-general' );
			wp_dequeue_style( 'woocommerce-layout' );
			wp_dequeue_style( 'woocommerce-smallscreen' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'jerseyplug_optimize_woocommerce_scripts', 99 );

/**
 * Shared cart markup for header and WooCommerce fragments.
 */
function jerseyplug_get_header_cart_markup(): string {
	$cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
	$cart_count = 0;

	if ( function_exists( 'WC' ) && WC()->cart ) {
		$cart_count = (int) WC()->cart->get_cart_contents_count();
	}

	$cart_url   = (string) apply_filters( 'jerseyplug_header_cart_url', $cart_url );
	$cart_count = (int) apply_filters( 'jerseyplug_header_cart_count', $cart_count );

	ob_start();
	?>
	<a
		href="<?php echo esc_url( $cart_url ); ?>"
		class="header-cart-contents relative hover:opacity-80 transition-transform active:scale-90 group"
		aria-label="<?php echo esc_attr( jerseyplug_pll( 'Cart' ) ); ?>"
		data-cart-count="<?php echo esc_attr( (string) $cart_count ); ?>"
		data-cart-drawer-trigger="header"
	>
		<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
		<?php if ( $cart_count > 0 ) : ?>
			<span class="header-cart-count absolute -top-2 -right-2 text-[10px] font-bold h-5 w-5 flex items-center justify-center rounded-full border border-primary bg-secondary text-primary group-hover:scale-110 transition-transform">
				<?php echo esc_html( (string) $cart_count ); ?>
			</span>
		<?php endif; ?>
		<span class="sr-only">
			<?php echo esc_html( sprintf( '%s: %d', jerseyplug_pll( 'Cart' ), $cart_count ) ); ?>
		</span>
	</a>
	<?php

	return (string) ob_get_clean();
}

/**
 * Refresh header cart quantity/price after WooCommerce AJAX add-to-cart.
 */
function jerseyplug_header_cart_fragments( array $fragments ): array {
	$fragments['a.header-cart-contents'] = jerseyplug_get_header_cart_markup();
	return $fragments;
}

if ( wp_doing_ajax() ) {
	add_filter( 'woocommerce_add_to_cart_fragments', 'jerseyplug_header_cart_fragments' );
}
