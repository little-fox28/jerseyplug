<?php
/**
 * Desktop filter toolbar component.
 *
 * Sticky bar with dropdown filters and sort select.
 * Uses data attributes for Vanilla JS dropdown toggle and filter selection.
 *
 * @package JerseyPlug
 */

$args           = wp_parse_args( $args ?? [], [ 'filter_options' => [], 'active_filters' => [] ] );
$filter_options = is_array( $args['filter_options'] ) ? $args['filter_options'] : [];
$active_filters = is_array( $args['active_filters'] ) ? $args['active_filters'] : [];

$competitions = $filter_options['competitions'] ?? [];
$teams        = $filter_options['teams'] ?? [];
$versions     = $filter_options['versions'] ?? [];
$sizes        = $filter_options['sizes'] ?? [];
$price_ranges = $filter_options['priceRanges'] ?? [];

$active_competitions = $active_filters['competitions'] ?? [];
$active_teams        = $active_filters['teams'] ?? [];
$active_versions     = $active_filters['versions'] ?? [];
$active_sizes        = $active_filters['sizes'] ?? [];
$active_price        = $active_filters['price'] ?? '';
$active_sort         = $active_filters['sort'] ?? 'featured';

$filters_label = jerseyplug_pll( 'Filters' );
$clear_label   = jerseyplug_pll( 'Clear' );
$sort_label    = jerseyplug_pll( 'Sort' );
$reset_label   = jerseyplug_pll( 'Reset' );
$apply_label   = jerseyplug_pll( 'Apply' );

$total_active = count( $active_competitions ) + count( $active_teams ) + count( $active_versions ) + count( $active_sizes ) + ( $active_price ? 1 : 0 );
?>

<div class="sticky top-0 z-30 hidden border-b border-gray-200 bg-white/95 backdrop-blur lg:block">
	<div class="container mx-auto flex items-center justify-between px-4 py-3">

		<!-- Filter Dropdowns -->
		<div class="flex flex-wrap items-center gap-2">
			<div class="mr-3 flex shrink-0 items-center gap-2 text-gray-400">
				<svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
				</svg>
				<span class="text-xs font-bold uppercase tracking-wider"><?php echo esc_html( $filters_label ); ?></span>
			</div>

			<?php if ( ! empty( $competitions ) ) : ?>
				<!-- Competitions -->
				<div class="relative shrink-0" data-dropdown>
					<button
						type="button"
						data-dropdown-trigger
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all <?php echo ! empty( $active_competitions ) ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'; ?>"
					>
						<?php echo esc_html( jerseyplug_pll( 'Competitions' ) ); ?>
						<?php if ( ! empty( $active_competitions ) ) : ?>
							<span data-filter-count="competitions" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"><?php echo count( $active_competitions ); ?></span>
						<?php else : ?>
							<span data-filter-count="competitions" class="ml-1 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"></span>
						<?php endif; ?>
						<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div data-dropdown-panel class="absolute left-0 top-full z-50 mt-2 hidden w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $competitions as $comp ) :
								$checked = in_array( $comp, $active_competitions, true );
							?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all <?php echo $checked ? 'border-primary bg-primary' : 'border-gray-300 bg-white'; ?>" data-check-visual>
										<svg class="h-3 w-3 text-white <?php echo $checked ? '' : 'hidden'; ?>" data-check-icon fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="checkbox" class="hidden" name="filter_competition" value="<?php echo esc_attr( $comp ); ?>" <?php checked( $checked ); ?> data-filter="competitions">
									<span class="text-sm <?php echo $checked ? 'font-bold text-primary' : 'text-gray-600'; ?>"><?php echo esc_html( $comp ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button type="button" data-reset-group="competitions" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button type="button" data-apply-group class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $teams ) ) : ?>
				<!-- Teams -->
				<div class="relative shrink-0" data-dropdown>
					<button
						type="button"
						data-dropdown-trigger
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all <?php echo ! empty( $active_teams ) ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'; ?>"
					>
						<?php echo esc_html( jerseyplug_pll( 'Teams' ) ); ?>
						<?php if ( ! empty( $active_teams ) ) : ?>
							<span data-filter-count="teams" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"><?php echo count( $active_teams ); ?></span>
						<?php else : ?>
							<span data-filter-count="teams" class="ml-1 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"></span>
						<?php endif; ?>
						<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div data-dropdown-panel class="absolute left-0 top-full z-50 mt-2 hidden w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $teams as $team ) :
								$checked = in_array( $team, $active_teams, true );
							?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all <?php echo $checked ? 'border-primary bg-primary' : 'border-gray-300 bg-white'; ?>" data-check-visual>
										<svg class="h-3 w-3 text-white <?php echo $checked ? '' : 'hidden'; ?>" data-check-icon fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="checkbox" class="hidden" name="filter_team" value="<?php echo esc_attr( $team ); ?>" <?php checked( $checked ); ?> data-filter="teams">
									<span class="text-sm <?php echo $checked ? 'font-bold text-primary' : 'text-gray-600'; ?>"><?php echo esc_html( $team ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button type="button" data-reset-group="teams" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button type="button" data-apply-group class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $versions ) ) : ?>
				<!-- Version -->
				<div class="relative shrink-0" data-dropdown>
					<button
						type="button"
						data-dropdown-trigger
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all <?php echo ! empty( $active_versions ) ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'; ?>"
					>
						<?php echo esc_html( jerseyplug_pll( 'Version' ) ); ?>
						<?php if ( ! empty( $active_versions ) ) : ?>
							<span data-filter-count="versions" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"><?php echo count( $active_versions ); ?></span>
						<?php else : ?>
							<span data-filter-count="versions" class="ml-1 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"></span>
						<?php endif; ?>
						<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div data-dropdown-panel class="absolute left-0 top-full z-50 mt-2 hidden w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $versions as $version ) :
								$checked = in_array( $version, $active_versions, true );
							?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all <?php echo $checked ? 'border-primary bg-primary' : 'border-gray-300 bg-white'; ?>" data-check-visual>
										<svg class="h-3 w-3 text-white <?php echo $checked ? '' : 'hidden'; ?>" data-check-icon fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="checkbox" class="hidden" name="filter_version" value="<?php echo esc_attr( $version ); ?>" <?php checked( $checked ); ?> data-filter="versions">
									<span class="text-sm <?php echo $checked ? 'font-bold text-primary' : 'text-gray-600'; ?>"><?php echo esc_html( $version ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button type="button" data-reset-group="versions" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button type="button" data-apply-group class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $sizes ) ) : ?>
				<!-- Size -->
				<div class="relative shrink-0" data-dropdown>
					<button
						type="button"
						data-dropdown-trigger
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all <?php echo ! empty( $active_sizes ) ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'; ?>"
					>
						<?php echo esc_html( jerseyplug_pll( 'Size' ) ); ?>
						<?php if ( ! empty( $active_sizes ) ) : ?>
							<span data-filter-count="sizes" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"><?php echo count( $active_sizes ); ?></span>
						<?php else : ?>
							<span data-filter-count="sizes" class="ml-1 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"></span>
						<?php endif; ?>
						<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div data-dropdown-panel class="absolute left-0 top-full z-50 mt-2 hidden w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $sizes as $size ) :
								$checked = in_array( $size, $active_sizes, true );
							?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all <?php echo $checked ? 'border-primary bg-primary' : 'border-gray-300 bg-white'; ?>" data-check-visual>
										<svg class="h-3 w-3 text-white <?php echo $checked ? '' : 'hidden'; ?>" data-check-icon fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="checkbox" class="hidden" name="filter_size" value="<?php echo esc_attr( $size ); ?>" <?php checked( $checked ); ?> data-filter="sizes">
									<span class="text-sm <?php echo $checked ? 'font-bold text-primary' : 'text-gray-600'; ?>"><?php echo esc_html( $size ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button type="button" data-reset-group="sizes" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button type="button" data-apply-group class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $price_ranges ) ) : ?>
				<!-- Price -->
				<div class="relative shrink-0" data-dropdown>
					<button
						type="button"
						data-dropdown-trigger
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all <?php echo $active_price ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'; ?>"
					>
						<?php echo esc_html( jerseyplug_pll( 'Price' ) ); ?>
						<?php if ( $active_price ) : ?>
							<span data-filter-count="price" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white">1</span>
						<?php else : ?>
							<span data-filter-count="price" class="ml-1 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"></span>
						<?php endif; ?>
						<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div data-dropdown-panel class="absolute left-0 top-full z-50 mt-2 hidden w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $price_ranges as $range ) :
								$checked = $active_price === ( $range['id'] ?? '' );
							?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all <?php echo $checked ? 'border-primary bg-primary' : 'border-gray-300 bg-white'; ?>" data-check-visual>
										<svg class="h-3 w-3 text-white <?php echo $checked ? '' : 'hidden'; ?>" data-check-icon fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="radio" class="hidden" name="filter_price" value="<?php echo esc_attr( $range['id'] ); ?>" <?php checked( $checked ); ?> data-filter="price">
									<span class="text-sm <?php echo $checked ? 'font-bold text-primary' : 'text-gray-600'; ?>"><?php echo esc_html( $range['label'] ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button type="button" data-reset-group="price" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button type="button" data-apply-group class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<!-- Clear all -->
			<button
				type="button"
				id="desktop-clear-all"
				class="ml-2 items-center gap-1 whitespace-nowrap text-sm font-medium text-red-500 hover:underline <?php echo $total_active > 0 ? 'flex' : 'hidden'; ?>"
			>
				<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
				<?php echo esc_html( $clear_label ); ?> (<span data-total-filter-count><?php echo esc_html( (string) $total_active ); ?></span>)
			</button>
		</div>

		<!-- Sort -->
		<div class="flex shrink-0 items-center gap-2 border-l border-gray-200 pl-6">
			<span class="text-sm text-gray-500"><?php echo esc_html( $sort_label ); ?>:</span>
			
			<div class="relative shrink-0" data-dropdown>
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
					class="flex items-center gap-1 text-sm font-bold text-primary transition-colors hover:text-accent"
				>
					<span data-sort-label><?php echo esc_html( $current_sort_label ); ?></span>
					<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div data-dropdown-panel class="absolute right-0 top-full z-50 mt-2 hidden w-48 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
					<div class="p-1">
						<?php foreach ( $sort_options as $val => $label ) : ?>
							<button 
								type="button" 
								data-sort-option="<?php echo esc_attr( $val ); ?>"
								class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm transition-colors hover:bg-gray-50 <?php echo $active_sort === $val ? 'bg-gray-50 font-bold text-primary' : 'text-gray-600'; ?>"
							>
								<?php echo esc_html( $label ); ?>
								<?php if ( $active_sort === $val ) : ?>
									<svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
								<?php endif; ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<!-- Hidden native select for JS state sync -->
			<select id="desktop-shop-sort" class="hidden">
				<option value="featured" <?php selected( $active_sort, 'featured' ); ?>><?php echo esc_html( jerseyplug_pll( 'Featured' ) ); ?></option>
				<option value="price_low" <?php selected( $active_sort, 'price_low' ); ?>><?php echo esc_html( jerseyplug_pll( 'Price: Low to High' ) ); ?></option>
				<option value="price_high" <?php selected( $active_sort, 'price_high' ); ?>><?php echo esc_html( jerseyplug_pll( 'Price: High to Low' ) ); ?></option>
				<option value="newest" <?php selected( $active_sort, 'newest' ); ?>><?php echo esc_html( jerseyplug_pll( 'Newest' ) ); ?></option>
			</select>
		</div>
	</div>
</div>
