<?php
/**
 * Product card partial.
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
?>

<article class="group relative flex flex-col">
	<a href="<?php echo esc_url( $url ); ?>" class="relative mb-4 overflow-hidden rounded-lg border border-transparent bg-gray-100 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:border-secondary hover:shadow-xl">
		<?php if ( ! empty( $product['tag'] ) ) : ?>
			<span class="absolute left-2 top-2 z-10 rounded-sm bg-primary px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white md:text-xs">
				<?php echo esc_html( $product['tag'] ); ?>
			</span>
		<?php endif; ?>

		<button type="button" class="absolute right-2 top-2 z-10 rounded-full bg-white/80 p-1.5 text-gray-400 transition-colors active:scale-90 hover:bg-white hover:text-red-500" aria-label="<?php echo esc_attr( jerseyplug_pll( 'Wishlist' ) ); ?>">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 md:h-[18px] md:w-[18px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M20.8 4.6c-1.6-1.7-4.2-1.7-5.8 0l-.9.9-.9-.9c-1.6-1.7-4.2-1.7-5.8 0-1.7 1.8-1.7 4.7 0 6.5l6.7 6.9 6.7-6.9c1.7-1.8 1.7-4.7 0-6.5Z"></path>
			</svg>
		</button>

		<?php if ( ! empty( $product['image'] ) ) : ?>
			<img src="<?php echo esc_url( $product['image'] ); ?>" alt="<?php echo esc_attr( $product['name'] ?? '' ); ?>" class="aspect-3/4 h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-105" loading="lazy" decoding="async" />
		<?php endif; ?>

		<div class="absolute inset-x-0 bottom-0 hidden translate-y-4 bg-linear-to-t from-black/60 to-transparent p-4 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100 md:block">
			<div class="flex w-full items-center justify-center gap-2 rounded bg-primary py-3 font-bold text-white shadow-lg transition-colors group-hover:bg-secondary group-hover:text-primary">
				<svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"></path>
					<circle cx="12" cy="12" r="3"></circle>
				</svg>
				<?php echo esc_html( jerseyplug_pll( 'View Details' ) ); ?>
			</div>
		</div>
	</a>

	<div class="mt-auto">
		<?php if ( ! empty( $product['category'] ) ) : ?>
			<p class="mb-1 text-[10px] uppercase tracking-wide text-gray-500 md:text-xs">
				<?php echo esc_html( $product['category'] ); ?>
			</p>
		<?php endif; ?>

		<a href="<?php echo esc_url( $url ); ?>">
			<h3 class="line-clamp-1 text-sm font-bold transition-colors group-hover:text-primary md:text-lg">
				<?php echo esc_html( $product['name'] ?? '' ); ?>
			</h3>
		</a>

		<div class="mt-2 flex items-center justify-between gap-3">
			<span class="text-sm font-semibold text-primary md:text-lg">
				<?php echo wp_kses_post( $product['price'] ?? '' ); ?>
			</span>
			<div class="flex items-center text-xs text-yellow-500">
				<svg aria-hidden="true" viewBox="0 0 20 20" class="h-3 w-3 fill-current" fill="currentColor">
					<path d="m10 15.27 5.18 3.13-1.45-5.88L18.5 8.5l-6.06-.48L10 2.5 7.56 8.02 1.5 8.5l4.77 4.02-1.45 5.88L10 15.27Z"></path>
				</svg>
				<span class="ml-1 text-gray-400"><?php echo esc_html( $product['rating_label'] ?? '5.0' ); ?></span>
			</div>
		</div>
	</div>
</article>
