<?php
/**
 * Load more component with progress bar.
 *
 * Shows a progress indicator and a "Load More" button
 * for AJAX pagination of products.
 *
 * @package JerseyPlug
 */

$args      = wp_parse_args( $args ?? [], [ 'total' => 0, 'max_pages' => 0, 'per_page' => 12 ] );
$total     = (int) $args['total'];
$max_pages = (int) $args['max_pages'];
$per_page  = (int) $args['per_page'];

$viewed_label    = jerseyplug_pll( "You've viewed" );
$of_label        = jerseyplug_pll( 'of' );
$products_label  = jerseyplug_pll( 'products' );
$load_more_label = jerseyplug_pll( 'Load More' );
?>

<div
	x-show="totalProducts > 0 && currentPage < maxPages && !loading"
	x-cloak
	class="container mx-auto px-4"
>
	<div class="mt-16 text-center">
		<p class="mb-4 text-xs text-gray-400">
			<?php echo esc_html( $viewed_label ); ?>
			<span x-text="displayedCount"></span>
			<?php echo esc_html( $of_label ); ?>
			<span x-text="totalProducts"></span>
			<?php echo esc_html( $products_label ); ?>
		</p>

		<!-- Progress bar -->
		<div class="mx-auto mb-6 h-1 w-48 overflow-hidden rounded-full bg-gray-100">
			<div
				class="h-full bg-primary transition-all duration-500"
				:style="'width: ' + Math.min(100, Math.round(displayedCount / totalProducts * 100)) + '%'"
			></div>
		</div>

		<!-- Load More button -->
		<button
			@click="loadMore()"
			:disabled="loadingMore"
			class="rounded-full border-2 border-primary bg-white px-10 py-3 font-bold text-primary shadow-sm transition-all hover:bg-primary hover:text-white active:scale-95 disabled:opacity-50"
		>
			<span x-show="!loadingMore"><?php echo esc_html( $load_more_label ); ?></span>
			<span x-show="loadingMore" class="flex items-center gap-2">
				<svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
				<?php echo esc_html( jerseyplug_pll( 'Loading...' ) ); ?>
			</span>
		</button>
	</div>
</div>
