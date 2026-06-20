<?php
/**
 * Mobile filter bar component.
 *
 * Sticky bar shown on mobile with filter button and sort select.
 * Uses data attributes for Vanilla JS interaction.
 *
 * @package JerseyPlug
 */

$args                 = wp_parse_args( $args ?? [], [ 'active_filters' => [], 'total_active_filters' => 0 ] );
$active_filters       = is_array( $args['active_filters'] ) ? $args['active_filters'] : [];
$total_active_filters = (int) $args['total_active_filters'];
$active_sort          = $active_filters['sort'] ?? 'featured';

$filters_label = jerseyplug_pll( 'Filters' );
?>

<div class="sticky top-0 z-30 flex gap-3 border-b border-gray-100 bg-white px-4 py-3 shadow-sm lg:hidden">
	<!-- Filter Toggle -->
	<button
		type="button"
		id="mobile-filter-toggle"
		class="flex h-10 flex-1 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-gray-50 text-sm font-bold text-gray-800 active:bg-gray-100"
	>
		<svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
		</svg>
		<?php echo esc_html( $filters_label ); ?>
		<span
			id="mobile-filter-count"
			class="h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white <?php echo $total_active_filters > 0 ? 'flex' : 'hidden'; ?>"
		><?php echo esc_html( (string) $total_active_filters ); ?></span>
	</button>

	<!-- Sort Select -->
	<div class="relative flex-1">
		<select
			id="mobile-shop-sort"
			class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm font-bold text-gray-800 focus:border-primary focus:outline-none"
		>
			<option value="featured" <?php selected( $active_sort, 'featured' ); ?>><?php echo esc_html( jerseyplug_pll( 'Featured' ) ); ?></option>
			<option value="price_low" <?php selected( $active_sort, 'price_low' ); ?>><?php echo esc_html( jerseyplug_pll( 'Price: Low-High' ) ); ?></option>
			<option value="price_high" <?php selected( $active_sort, 'price_high' ); ?>><?php echo esc_html( jerseyplug_pll( 'Price: High-Low' ) ); ?></option>
			<option value="newest" <?php selected( $active_sort, 'newest' ); ?>><?php echo esc_html( jerseyplug_pll( 'Newest' ) ); ?></option>
		</select>
		<svg aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M3 9l4-4 4 4"/><path d="M7 5v14"/><path d="M13 15l4 4 4-4"/><path d="M17 19V5"/>
		</svg>
	</div>
</div>
