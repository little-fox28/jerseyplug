<?php
/**
 * Product grid component with WooCommerce standard loop.
 *
 * Renders the main product grid using woocommerce_product_loop()
 * and the standard WP loop. Provides ID-based containers for
 * Vanilla JS AJAX swap targeting.
 *
 * @package JerseyPlug
 */

$args                 = wp_parse_args( $args ?? [], [ 'active_filters' => [], 'total_active_filters' => 0 ] );
$active_filters       = is_array( $args['active_filters'] ) ? $args['active_filters'] : [];
$total_active_filters = (int) $args['total_active_filters'];

$showing_label     = jerseyplug_pll( 'Showing' );
$results_label     = jerseyplug_pll( 'results' );
$in_label          = jerseyplug_pll( 'in' );
$no_products_label = jerseyplug_pll( 'No products found' );
$try_adjusting     = jerseyplug_pll( 'Try adjusting your filters or search criteria.' );
$clear_all_label   = jerseyplug_pll( 'Clear All Filters' );

global $wp_query;
$total = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;

// Build a label for active competition filters.
$active_comp_label = '';
if ( ! empty( $active_filters['competitions'] ) ) {
	$active_comp_label = implode( ', ', array_map( 'esc_html', $active_filters['competitions'] ) );
}
?>

<div class="container mx-auto px-4 py-8">

	<!-- Result count -->
	<div id="result-count" class="mb-6 flex flex-col items-end justify-between gap-2 md:flex-row">
		<div class="text-sm text-gray-500">
			<?php echo esc_html( $showing_label ); ?>
			<strong class="text-primary"><?php echo esc_html( (string) $total ); ?></strong>
			<?php echo esc_html( $results_label ); ?>
			<?php if ( ! empty( $active_comp_label ) ) : ?>
				<span>
					<?php echo esc_html( $in_label ); ?>
					<span class="font-bold text-primary"><?php echo $active_comp_label; ?></span>
				</span>
			<?php endif; ?>
		</div>
	</div>

	<!-- Loading overlay (hidden by default, shown via JS) -->
	<div id="grid-loading" class="pointer-events-none absolute inset-0 z-20 hidden items-center justify-center">
		<div class="h-8 w-8 animate-spin rounded-full border-2 border-gray-200 border-t-primary"></div>
	</div>

	<!-- Product Grid -->
	<div id="product-grid" class="relative">
		<?php if ( function_exists( 'woocommerce_product_loop' ) && woocommerce_product_loop() ) : ?>
			<?php
			// Track loop index for LCP optimization.
			$loop_index = 0;

			do_action( 'woocommerce_before_shop_loop' );
			?>

			<ul class="products grid !grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 md:gap-x-4 md:gap-y-10 lg:grid-cols-4 before:!hidden after:!hidden list-none !m-0 !p-0">
				<?php
				while ( have_posts() ) :
					the_post();

					global $product;

					if ( empty( $product ) || ! $product instanceof WC_Product ) {
						continue;
					}

					get_template_part( 'components/products/product-card', null, [
						'product_obj' => $product,
						'index'       => $loop_index,
					] );

					$loop_index++;
				endwhile;
				?>
			</ul>

			<?php do_action( 'woocommerce_after_shop_loop' ); ?>

		<?php else : ?>
			<!-- Empty state -->
			<div id="empty-state" class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 py-32 text-center">
				<div class="mb-4 inline-flex rounded-full bg-white p-4 shadow-sm">
					<svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
					</svg>
				</div>
				<h3 class="mb-2 text-lg font-bold text-gray-900"><?php echo esc_html( $no_products_label ); ?></h3>
				<p class="mb-6 text-gray-500"><?php echo esc_html( $try_adjusting ); ?></p>
				<button
					type="button"
					id="clear-all-filters"
					class="rounded-lg bg-primary px-6 py-2.5 text-sm font-bold text-white transition-colors hover:bg-accent hover:text-primary"
				>
					<?php echo esc_html( $clear_all_label ); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>
</div>
