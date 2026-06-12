<?php
/**
 * Product grid component with result count and empty state.
 *
 * Renders the server-side initial product grid and provides
 * an Alpine.js target for AJAX-loaded product cards.
 *
 * @package JerseyPlug
 */

$args     = wp_parse_args( $args ?? [], [ 'products' => [], 'total' => 0 ] );
$products = is_array( $args['products'] ) ? $args['products'] : [];
$total    = (int) $args['total'];

$showing_label = jerseyplug_pll( 'Showing' );
$results_label = jerseyplug_pll( 'results' );
$in_label      = jerseyplug_pll( 'in' );
$no_products_label      = jerseyplug_pll( 'No products found' );
$try_adjusting_label    = jerseyplug_pll( 'Try adjusting your filters or search criteria.' );
$clear_all_label        = jerseyplug_pll( 'Clear All Filters' );
?>

<div class="container mx-auto px-4 py-8">

	<!-- Result count -->
	<div class="mb-6 flex flex-col items-end justify-between gap-2 md:flex-row">
		<div class="text-sm text-gray-500">
			<?php echo esc_html( $showing_label ); ?>
			<strong class="text-primary" x-text="totalProducts"><?php echo esc_html( (string) $total ); ?></strong>
			<?php echo esc_html( $results_label ); ?>
			<template x-if="selectedCompetitions.length > 0">
				<span>
					<?php echo esc_html( $in_label ); ?>
					<span class="font-bold text-primary" x-text="selectedCompetitions.join(', ')"></span>
				</span>
			</template>
		</div>
	</div>

	<!-- Loading overlay -->
	<div x-show="loading" class="flex items-center justify-center py-16">
		<div class="h-8 w-8 animate-spin rounded-full border-2 border-gray-200 border-t-primary"></div>
	</div>

	<!-- Product Grid -->
	<div
		x-show="totalProducts > 0 && !loading"
		id="product-grid"
		x-ref="productGrid"
		class="grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 md:gap-x-4 md:gap-y-10 lg:grid-cols-4"
	>
		<?php if ( ! empty( $products ) ) : ?>
			<?php foreach ( $products as $product ) : ?>
				<?php get_template_part( 'components/products/product-card', null, [ 'product' => $product ] ); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<!-- Empty state -->
	<div
		x-show="totalProducts === 0 && !loading"
		x-cloak
		class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 py-32 text-center"
	>
		<div class="mb-4 inline-flex rounded-full bg-white p-4 shadow-sm">
			<svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
				<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
			</svg>
		</div>
		<h3 class="mb-2 text-lg font-bold text-gray-900"><?php echo esc_html( $no_products_label ); ?></h3>
		<p class="mb-6 text-gray-500"><?php echo esc_html( $try_adjusting_label ); ?></p>
		<button
			@click="clearAllFilters()"
			class="rounded-lg bg-primary px-6 py-2.5 text-sm font-bold text-white transition-colors hover:bg-accent hover:text-primary"
		>
			<?php echo esc_html( $clear_all_label ); ?>
		</button>
	</div>
</div>
