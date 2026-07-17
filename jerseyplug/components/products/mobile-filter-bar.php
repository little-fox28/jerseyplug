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
	<div class="relative flex-1" data-dropdown>
		<?php
		$sort_options = [
			'featured'   => jerseyplug_pll( 'Featured' ),
			'price_low'  => jerseyplug_pll( 'Price: Low to High' ),
			'price_high' => jerseyplug_pll( 'Price: High to Low' ),
			'newest'     => jerseyplug_pll( 'Newest' ),
		];
		$current_sort_label = $sort_options[ $active_sort ] ?? $sort_options['featured'];
		?>
		<button
			type="button"
			data-dropdown-trigger
			class="flex h-10 w-full items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-4 text-sm font-bold text-gray-800 transition-colors focus:border-primary focus:outline-none active:bg-gray-100"
		>
			<div class="flex items-center gap-2">
				<svg aria-hidden="true" class="h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M3 9l4-4 4 4"/><path d="M7 5v14"/><path d="M13 15l4 4 4-4"/><path d="M17 19V5"/>
				</svg>
				<span data-sort-label><?php echo esc_html( $current_sort_label ); ?></span>
			</div>
			<svg class="h-4 w-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
		</button>

		<div data-dropdown-panel class="absolute right-0 top-full z-50 mt-2 hidden w-full overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
			<div class="p-1">
				<?php foreach ( $sort_options as $val => $label ) : ?>
					<button 
						type="button" 
						data-sort-option="<?php echo esc_attr( $val ); ?>"
						class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-left text-sm transition-colors hover:bg-gray-50 <?php echo $active_sort === $val ? 'bg-gray-50 font-bold text-primary' : 'text-gray-600'; ?>"
					>
						<?php echo esc_html( $label ); ?>
						<?php if ( $active_sort === $val ) : ?>
							<svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
						<?php endif; ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Hidden native select for JS state sync -->
		<select id="mobile-shop-sort" class="hidden">
			<option value="featured" <?php selected( $active_sort, 'featured' ); ?>>Featured</option>
			<option value="price_low" <?php selected( $active_sort, 'price_low' ); ?>>Price Low to High</option>
			<option value="price_high" <?php selected( $active_sort, 'price_high' ); ?>>Price High to Low</option>
			<option value="newest" <?php selected( $active_sort, 'newest' ); ?>>Newest</option>
		</select>
	</div>
</div>
