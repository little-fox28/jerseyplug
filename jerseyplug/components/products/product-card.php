<?php
/**
 * Unified Product card component.
 *
 * Accepts either a WC_Product object (from WC loop) or a product
 * data array (legacy). Applies LCP optimization for the first 4
 * product images (loading="eager" + fetchpriority="high").
 *
 * @package JerseyPlug
 */

$args  = wp_parse_args( $args ?? [], [ 'product_obj' => null, 'product' => [], 'index' => 0 ] );
$index = (int) $args['index'];

// --- Build product data from WC_Product object or legacy array ---
$wc_product = $args['product_obj'] ?? null;

if ( $wc_product instanceof WC_Product ) {
	$product_id  = (int) $wc_product->get_id();
	$url         = (string) get_permalink( $product_id );
	$name        = (string) $wc_product->get_name();
	$price_html  = $wc_product->get_price_html();
	$image_id    = $wc_product->get_image_id();
	$image_front = $image_id > 0 ? (string) wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '';
	$gallery_ids = $wc_product->get_gallery_image_ids();
	$image_back  = ! empty( $gallery_ids ) ? (string) wp_get_attachment_image_url( $gallery_ids[0], 'woocommerce_thumbnail' ) : '';

	if ( empty( $image_front ) && function_exists( 'wc_placeholder_img_src' ) ) {
		$image_front = (string) wc_placeholder_img_src( 'woocommerce_thumbnail' );
	}
	if ( empty( $image_back ) ) {
		$image_back = $image_front;
	}

	$terms    = get_the_terms( $product_id, 'product_cat' );
	$category = '';
	if ( is_array( $terms ) && ! empty( $terms ) && $terms[0] instanceof WP_Term ) {
		$category = (string) $terms[0]->name;
	}

	$rating_data  = jerseyplug_get_random_rating_and_reviews( $product_id );
	$rating_label = $rating_data['rating'];
	$tag          = $wc_product->is_featured()
		? jerseyplug_pll( 'Trending Now' )
		: ( jerseyplug_is_new_product( $wc_product ) ? jerseyplug_pll( 'New' ) : '' );

	$is_purchasable = $wc_product->is_purchasable() && $wc_product->is_in_stock();
	$is_simple      = $wc_product->is_type( 'simple' );
} else {
	// Legacy array fallback.
	$product = is_array( $args['product'] ?? null ) ? $args['product'] : [];

	if ( empty( $product ) ) {
		return;
	}

	$product_id  = (int) ( $product['id'] ?? 0 );
	$url         = (string) ( $product['url'] ?? '#' );
	$name        = (string) ( $product['name'] ?? '' );
	$price_html  = $product['price'] ?? '';
	$image_front = (string) ( $product['image'] ?? '' );
	$image_back  = (string) ( $product['image_back'] ?? '' );
	$category    = (string) ( $product['category'] ?? '' );
	$rating_label = $product['rating_label'] ?? '5.0';
	$tag         = $product['tag'] ?? '';

	if ( empty( $image_back ) ) {
		$image_back = $image_front;
	}

	// WooCommerce add to cart helper.
	$is_simple      = false;
	$is_purchasable = false;
	if ( $product_id > 0 && function_exists( 'wc_get_product' ) ) {
		$wc_product = wc_get_product( $product_id );
		if ( $wc_product && $wc_product->is_purchasable() && $wc_product->is_in_stock() ) {
			$is_purchasable = true;
			$is_simple      = $wc_product->is_type( 'simple' );
		}
	}
}

// LCP optimization: first 4 images get eager loading.
$is_above_fold = $index < 4;
$loading_attr  = $is_above_fold ? 'eager' : 'lazy';
$priority_attr = $is_above_fold ? 'high' : 'auto';
?>

<li class="group relative flex flex-col gap-3 product text-left !w-full !m-0 !p-0 list-none float-none">
	<div class="relative aspect-4/5 overflow-hidden rounded-xl bg-gray-100 shadow-sm transition-all duration-300 hover:shadow-lg">
		<?php if ( ! empty( $tag ) ) : ?>
			<span class="absolute left-2 top-2 z-10 rounded-sm bg-white/90 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-primary backdrop-blur">
				<?php echo esc_html( $tag ); ?>
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
					alt="<?php echo esc_attr( $name ); ?>"
					class="absolute inset-0 !h-full !w-full object-cover transition-transform duration-700 group-hover:scale-105 <?php echo ( $image_back !== $image_front ) ? 'group-hover:opacity-0' : ''; ?>"
					loading="<?php echo esc_attr( $loading_attr ); ?>"
					fetchpriority="<?php echo esc_attr( $priority_attr ); ?>"
					decoding="async" />
			<?php endif; ?>

			<?php if ( ! empty( $image_back ) && $image_back !== $image_front ) : ?>
				<!-- Hover Back Image -->
				<img src="<?php echo esc_url( $image_back ); ?>"
					alt="<?php echo esc_attr( $name ); ?>"
					class="absolute inset-0 !h-full !w-full object-cover opacity-0 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100"
					loading="lazy"
					decoding="async" />
			<?php endif; ?>
		</a>

		<div class="absolute inset-x-3 bottom-3 hidden translate-y-full transition-transform duration-300 group-hover:translate-y-0 lg:block">
			<a href="<?php echo esc_url( $url ); ?>"
				class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-2.5 text-xs font-bold text-white shadow-lg transition-colors hover:bg-accent hover:text-primary">
				<svg aria-hidden="true" viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"></path>
					<circle cx="12" cy="12" r="3"></circle>
				</svg>
				<?php echo esc_html( jerseyplug_pll( 'View Details' ) ); ?>
			</a>
		</div>
	</div>

	<div>
		<?php if ( ! empty( $category ) ) : ?>
			<p class="mb-1 text-[10px] uppercase tracking-wide text-gray-400">
				<?php echo esc_html( $category ); ?>
			</p>
		<?php endif; ?>

		<a href="<?php echo esc_url( $url ); ?>">
			<h3 class="mb-1 line-clamp-1 !text-sm font-bold text-gray-900 transition-colors group-hover:text-primary">
				<?php echo esc_html( $name ); ?>
			</h3>
		</a>

		<div class="flex items-center justify-between">
			<span class="font-bold text-gray-900 [&_del]:mr-1.5 [&_del]:text-xs [&_del]:font-normal [&_del]:line-through [&_del]:text-gray-400 [&_ins]:no-underline [&_ins]:text-primary">
				<?php echo wp_kses_post( $price_html ); ?>
			</span>
			<div class="flex items-center gap-0.5 text-[10px] text-yellow-500">
				<svg aria-hidden="true" viewBox="0 0 20 20" class="h-2.5 w-2.5 fill-current" fill="currentColor">
					<path d="m10 15.27 5.18 3.13-1.45-5.88L18.5 8.5l-6.06-.48L10 2.5 7.56 8.02 1.5 8.5l4.77 4.02-1.45 5.88L10 15.27Z"></path>
				</svg>
				<span class="text-gray-400"><?php echo esc_html( $rating_label ); ?></span>
			</div>
		</div>
	</div>
</li>
