<?php
/**
 * Unified Product card component.
 *
 * @package JerseyPlug
 */

$args    = wp_parse_args( $args ?? [], [ 'product' => [] ] );
$product = is_array( $args['product'] ?? null ) ? $args['product'] : [];

if ( empty( $product ) ) {
	return;
}

$product_id = (int) ( $product['id'] ?? 0 );
$url        = (string) ( $product['url'] ?? '#' );
$image_front = (string) ( $product['image'] ?? '' );
$image_back = (string) ( $product['image_back'] ?? '' );

// Fallback: If image_back is empty, attempt to load the first gallery image from WooCommerce
if ( empty( $image_back ) && $product_id > 0 && function_exists( 'wc_get_product' ) ) {
	$wc_product = wc_get_product( $product_id );
	if ( $wc_product ) {
		$gallery_ids = $wc_product->get_gallery_image_ids();
		if ( ! empty( $gallery_ids ) ) {
			$image_back = (string) wp_get_attachment_image_url( $gallery_ids[0], 'woocommerce_thumbnail' );
		}
	}
}

if ( empty( $image_back ) ) {
	$image_back = $image_front;
}

// WooCommerce add to cart helper
$is_simple = false;
$is_purchasable = false;
if ( $product_id > 0 && function_exists( 'wc_get_product' ) ) {
	$wc_product = wc_get_product( $product_id );
	if ( $wc_product && $wc_product->is_purchasable() && $wc_product->is_in_stock() ) {
		$is_purchasable = true;
		$is_simple = $wc_product->is_type( 'simple' );
	}
}
?>

<article class="group relative flex flex-col gap-3">
	<div class="relative aspect-4/5 overflow-hidden rounded-xl bg-gray-100 shadow-sm transition-all duration-300 hover:shadow-lg">
		<?php if ( ! empty( $product['tag'] ) ) : ?>
			<span class="absolute left-2 top-2 z-10 rounded-sm bg-white/90 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-primary backdrop-blur">
				<?php echo esc_html( $product['tag'] ); ?>
			</span>
		<?php endif; ?>

		<button type="button" class="absolute right-2 top-2 z-10 rounded-full bg-white/60 p-2 text-black transition-colors hover:bg-white" aria-label="<?php echo esc_attr( jerseyplug_pll( 'Wishlist' ) ); ?>">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M20.8 4.6c-1.6-1.7-4.2-1.7-5.8 0l-.9.9-.9-.9c-1.6-1.7-4.2-1.7-5.8 0-1.7 1.8-1.7 4.7 0 6.5l6.7 6.9 6.7-6.9c1.7-1.8 1.7-4.7 0-6.5Z"></path>
			</svg>
		</button>

		<a href="<?php echo esc_url( $url ); ?>" class="block h-full w-full">
			<?php if ( ! empty( $image_front ) ) : ?>
				<!-- Front Image -->
				<img src="<?php echo esc_url( $image_front ); ?>" 
					alt="<?php echo esc_attr( $product['name'] ?? '' ); ?>" 
					class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105 <?php echo ( $image_back !== $image_front ) ? 'group-hover:opacity-0' : ''; ?>" 
					loading="lazy" 
					decoding="async" />
			<?php endif; ?>

			<?php if ( ! empty( $image_back ) && $image_back !== $image_front ) : ?>
				<!-- Hover Back Image -->
				<img src="<?php echo esc_url( $image_back ); ?>" 
					alt="<?php echo esc_attr( $product['name'] ?? '' ); ?>" 
					class="absolute inset-0 h-full w-full object-cover opacity-0 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100" 
					loading="lazy" 
					decoding="async" />
			<?php endif; ?>
		</a>

		<div class="absolute inset-x-3 bottom-3 hidden translate-y-full transition-transform duration-300 group-hover:translate-y-0 lg:block">
			<?php if ( $is_purchasable && $is_simple ) : ?>
				<a href="?add-to-cart=<?php echo $product_id; ?>" 
					data-product_id="<?php echo $product_id; ?>" 
					class="button product_type_simple add_to_cart_button ajax_add_to_cart flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-2.5 text-xs font-bold text-white shadow-lg transition-colors hover:bg-accent hover:text-primary">
					<svg aria-hidden="true" viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<line x1="12" y1="5" x2="12" y2="19"></line>
						<line x1="5" y1="12" x2="19" y2="12"></line>
					</svg>
					<?php echo esc_html( jerseyplug_pll( 'Quick Add' ) ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $url ); ?>" 
					class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-2.5 text-xs font-bold text-white shadow-lg transition-colors hover:bg-accent hover:text-primary">
					<svg aria-hidden="true" viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"></path>
						<circle cx="12" cy="12" r="3"></circle>
					</svg>
					<?php echo esc_html( jerseyplug_pll( 'View Details' ) ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div>
		<?php if ( ! empty( $product['category'] ) ) : ?>
			<p class="mb-1 text-[10px] uppercase tracking-wide text-gray-400">
				<?php echo esc_html( $product['category'] ); ?>
			</p>
		<?php endif; ?>

		<a href="<?php echo esc_url( $url ); ?>">
			<h3 class="mb-1 line-clamp-1 text-sm font-bold text-gray-900 transition-colors group-hover:text-primary">
				<?php echo esc_html( $product['name'] ?? '' ); ?>
			</h3>
		</a>

		<div class="flex items-center justify-between">
			<span class="font-bold text-gray-900 [&_del]:mr-1.5 [&_del]:text-xs [&_del]:font-normal [&_del]:line-through [&_del]:text-gray-400 [&_ins]:no-underline [&_ins]:text-primary">
				<?php echo wp_kses_post( $product['price'] ?? '' ); ?>
			</span>
			<div class="flex items-center gap-0.5 text-[10px] text-yellow-500">
				<svg aria-hidden="true" viewBox="0 0 20 20" class="h-2.5 w-2.5 fill-current" fill="currentColor">
					<path d="m10 15.27 5.18 3.13-1.45-5.88L18.5 8.5l-6.06-.48L10 2.5 7.56 8.02 1.5 8.5l4.77 4.02-1.45 5.88L10 15.27Z"></path>
				</svg>
				<span class="text-gray-400"><?php echo esc_html( $product['rating_label'] ?? '5.0' ); ?></span>
			</div>
		</div>
	</div>
</article>
