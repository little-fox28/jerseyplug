<?php
/**
 * Mobile filter drawer component.
 *
 * Full-screen slide-in drawer with accordion filter groups.
 * Uses data attributes for Vanilla JS interaction.
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

$filters_label      = jerseyplug_pll( 'Filters' );
$reset_label        = jerseyplug_pll( 'Reset' );
$show_results_label = jerseyplug_pll( 'Show Results' );
?>

<!-- Overlay -->
<div
	id="mobile-filter-overlay"
	class="fixed inset-0 z-40 bg-black/50 transition-opacity duration-300 hidden"
></div>

<!-- Drawer -->
<div
	id="mobile-filter-drawer"
	class="fixed inset-y-0 right-0 z-50 flex w-[85%] max-w-sm translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 ease-out"
>
	<!-- Header -->
	<div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
		<h2 class="text-lg font-bold text-zinc-900"><?php echo esc_html( $filters_label ); ?></h2>
		<button type="button" id="mobile-drawer-close" class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-bold text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900">
			<?php echo esc_html( jerseyplug_pll( 'Close' ) ); ?>
			<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
		</button>
	</div>

	<!-- Body -->
	<div class="flex-1 overflow-y-auto px-6 py-4 space-y-2">

		<?php if ( ! empty( $competitions ) ) : ?>
			<!-- Competitions Accordion -->
			<div data-accordion>
				<button type="button" data-accordion-trigger class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Competitions' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div data-accordion-panel class="hidden space-y-2 px-3 pb-3">
					<?php foreach ( $competitions as $comp ) :
						$checked = in_array( $comp, $active_competitions, true );
					?>
						<label class="flex cursor-pointer items-center gap-3 rounded-lg py-1.5 transition-colors hover:bg-gray-50">
							<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all <?php echo $checked ? 'border-primary bg-primary' : 'border-gray-300 bg-white'; ?>" data-check-visual>
								<svg class="h-3 w-3 text-white <?php echo $checked ? '' : 'hidden'; ?>" data-check-icon fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
							</div>
							<input type="checkbox" class="hidden" name="filter_competition" value="<?php echo esc_attr( $comp ); ?>" <?php checked( $checked ); ?> data-filter="competitions">
							<span class="text-sm <?php echo $checked ? 'font-bold text-primary' : 'text-gray-600'; ?>"><?php echo esc_html( $comp ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $teams ) ) : ?>
			<!-- Teams Accordion -->
			<div data-accordion>
				<button type="button" data-accordion-trigger class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Teams' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div data-accordion-panel class="hidden space-y-2 px-3 pb-3">
					<?php foreach ( $teams as $team ) :
						$checked = in_array( $team, $active_teams, true );
					?>
						<label class="flex cursor-pointer items-center gap-3 rounded-lg py-1.5 transition-colors hover:bg-gray-50">
							<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all <?php echo $checked ? 'border-primary bg-primary' : 'border-gray-300 bg-white'; ?>" data-check-visual>
								<svg class="h-3 w-3 text-white <?php echo $checked ? '' : 'hidden'; ?>" data-check-icon fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
							</div>
							<input type="checkbox" class="hidden" name="filter_team" value="<?php echo esc_attr( $team ); ?>" <?php checked( $checked ); ?> data-filter="teams">
							<span class="text-sm <?php echo $checked ? 'font-bold text-primary' : 'text-gray-600'; ?>"><?php echo esc_html( $team ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $versions ) ) : ?>
			<!-- Versions Accordion -->
			<div data-accordion>
				<button type="button" data-accordion-trigger class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Version' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div data-accordion-panel class="hidden space-y-2 px-3 pb-3">
					<?php foreach ( $versions as $version ) :
						$checked = in_array( $version, $active_versions, true );
					?>
						<label class="flex cursor-pointer items-center gap-3 rounded-lg py-1.5 transition-colors hover:bg-gray-50">
							<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all <?php echo $checked ? 'border-primary bg-primary' : 'border-gray-300 bg-white'; ?>" data-check-visual>
								<svg class="h-3 w-3 text-white <?php echo $checked ? '' : 'hidden'; ?>" data-check-icon fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
							</div>
							<input type="checkbox" class="hidden" name="filter_version" value="<?php echo esc_attr( $version ); ?>" <?php checked( $checked ); ?> data-filter="versions">
							<span class="text-sm <?php echo $checked ? 'font-bold text-primary' : 'text-gray-600'; ?>"><?php echo esc_html( $version ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $sizes ) ) : ?>
			<!-- Sizes Accordion -->
			<div data-accordion>
				<button type="button" data-accordion-trigger class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Size' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div data-accordion-panel class="hidden px-3 pb-3">
					<div class="grid grid-cols-4 gap-2">
						<?php foreach ( $sizes as $size ) :
							$checked = in_array( $size, $active_sizes, true );
						?>
							<button
								type="button"
								data-size-toggle="<?php echo esc_attr( $size ); ?>"
								class="rounded-lg border py-2.5 text-xs font-bold transition-all <?php echo $checked ? 'border-primary bg-primary text-white' : 'border-gray-200 bg-white text-gray-600'; ?>"
							>
								<?php echo esc_html( $size ); ?>
							</button>
						<?php endforeach; ?>
					</div>
					<!-- Hidden checkboxes for size state sync -->
					<?php foreach ( $sizes as $size ) :
						$checked = in_array( $size, $active_sizes, true );
					?>
						<input type="checkbox" class="hidden" name="filter_size" value="<?php echo esc_attr( $size ); ?>" <?php checked( $checked ); ?> data-filter="sizes">
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $price_ranges ) ) : ?>
			<!-- Price Accordion -->
			<div data-accordion>
				<button type="button" data-accordion-trigger class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Price' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div data-accordion-panel class="hidden space-y-2 px-3 pb-3">
					<?php foreach ( $price_ranges as $range ) :
						$checked = $active_price === ( $range['id'] ?? '' );
					?>
						<label class="flex cursor-pointer items-center gap-3 rounded-lg py-1.5 transition-colors hover:bg-gray-50">
							<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border transition-all <?php echo $checked ? 'border-primary bg-primary' : 'border-gray-300 bg-white'; ?>" data-check-visual>
								<div class="h-2 w-2 rounded-full bg-white <?php echo $checked ? '' : 'hidden'; ?>" data-check-icon></div>
							</div>
							<input type="radio" class="hidden" name="filter_price" value="<?php echo esc_attr( $range['id'] ); ?>" <?php checked( $checked ); ?> data-filter="price">
							<span class="text-sm <?php echo $checked ? 'font-bold text-primary' : 'text-gray-600'; ?>"><?php echo esc_html( $range['label'] ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<!-- Footer -->
	<div class="border-t border-gray-200 bg-white px-6 py-4">
		<div class="flex gap-3">
			<button
				type="button"
				id="mobile-filter-reset"
				class="flex-1 rounded-xl border border-gray-200 py-3.5 text-sm font-bold text-gray-500 hover:bg-gray-50"
			>
				<?php echo esc_html( $reset_label ); ?>
			</button>
			<button
				type="button"
				id="mobile-filter-apply"
				class="flex-[2] rounded-xl bg-primary py-3.5 text-sm font-bold text-white shadow-lg transition-transform active:scale-95"
			>
				<?php echo esc_html( $show_results_label ); ?>
			</button>
		</div>
	</div>
</div>
