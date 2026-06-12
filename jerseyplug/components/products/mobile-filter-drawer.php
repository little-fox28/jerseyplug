<?php
/**
 * Mobile filter drawer component.
 *
 * Full-screen slide-in drawer with accordion filter groups.
 * Maps from MobileFilterDrawer.jsx.
 *
 * @package JerseyPlug
 */

$args           = wp_parse_args( $args ?? [], [ 'filter_options' => [] ] );
$filter_options = is_array( $args['filter_options'] ) ? $args['filter_options'] : [];

$competitions = $filter_options['competitions'] ?? [];
$teams        = $filter_options['teams'] ?? [];
$versions     = $filter_options['versions'] ?? [];
$sizes        = $filter_options['sizes'] ?? [];
$price_ranges = $filter_options['priceRanges'] ?? [];

$filters_label      = jerseyplug_pll( 'Filters' );
$reset_label        = jerseyplug_pll( 'Reset' );
$show_results_label = jerseyplug_pll( 'Show Results' );
?>

<!-- Overlay -->
<div
	x-show="drawerOpen"
	x-transition:enter="transition-opacity duration-300"
	x-transition:enter-start="opacity-0"
	x-transition:enter-end="opacity-100"
	x-transition:leave="transition-opacity duration-300"
	x-transition:leave-start="opacity-100"
	x-transition:leave-end="opacity-0"
	@click="drawerOpen = false"
	class="fixed inset-0 z-40 bg-black/50"
	style="display: none;"
></div>

<!-- Drawer -->
<div
	x-show="drawerOpen"
	x-transition:enter="transition-transform duration-300 ease-out"
	x-transition:enter-start="translate-x-full"
	x-transition:enter-end="translate-x-0"
	x-transition:leave="transition-transform duration-300 ease-in"
	x-transition:leave-start="translate-x-0"
	x-transition:leave-end="translate-x-full"
	class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md flex-col bg-white shadow-2xl"
	style="display: none;"
>
	<!-- Header -->
	<div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
		<h2 class="text-lg font-bold text-zinc-900"><?php echo esc_html( $filters_label ); ?></h2>
		<button @click="drawerOpen = false" class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
			<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
		</button>
	</div>

	<!-- Body -->
	<div class="flex-1 overflow-y-auto px-6 py-4 space-y-2">

		<?php if ( ! empty( $competitions ) ) : ?>
			<!-- Competitions Accordion -->
			<div x-data="{ expanded: false }">
				<button @click="expanded = !expanded" class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Competitions' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div x-show="expanded" x-collapse class="space-y-2 px-3 pb-3">
					<?php foreach ( $competitions as $comp ) : ?>
						<label class="flex cursor-pointer items-center gap-3 rounded-lg py-1.5 transition-colors hover:bg-gray-50">
							<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all"
								:class="selectedCompetitions.includes('<?php echo esc_js( $comp ); ?>') ? 'border-primary bg-primary' : 'border-gray-300 bg-white'">
								<svg x-show="selectedCompetitions.includes('<?php echo esc_js( $comp ); ?>')" class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
							</div>
							<input type="checkbox" class="hidden" @change="toggleFilter('competitions', '<?php echo esc_js( $comp ); ?>')">
							<span class="text-sm" :class="selectedCompetitions.includes('<?php echo esc_js( $comp ); ?>') ? 'font-bold text-primary' : 'text-gray-600'"><?php echo esc_html( $comp ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $teams ) ) : ?>
			<!-- Teams Accordion -->
			<div x-data="{ expanded: false }">
				<button @click="expanded = !expanded" class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Teams' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div x-show="expanded" x-collapse class="space-y-2 px-3 pb-3">
					<?php foreach ( $teams as $team ) : ?>
						<label class="flex cursor-pointer items-center gap-3 rounded-lg py-1.5 transition-colors hover:bg-gray-50">
							<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all"
								:class="selectedTeams.includes('<?php echo esc_js( $team ); ?>') ? 'border-primary bg-primary' : 'border-gray-300 bg-white'">
								<svg x-show="selectedTeams.includes('<?php echo esc_js( $team ); ?>')" class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
							</div>
							<input type="checkbox" class="hidden" @change="toggleFilter('teams', '<?php echo esc_js( $team ); ?>')">
							<span class="text-sm" :class="selectedTeams.includes('<?php echo esc_js( $team ); ?>') ? 'font-bold text-primary' : 'text-gray-600'"><?php echo esc_html( $team ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $versions ) ) : ?>
			<!-- Versions Accordion -->
			<div x-data="{ expanded: false }">
				<button @click="expanded = !expanded" class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Version' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div x-show="expanded" x-collapse class="space-y-2 px-3 pb-3">
					<?php foreach ( $versions as $version ) : ?>
						<label class="flex cursor-pointer items-center gap-3 rounded-lg py-1.5 transition-colors hover:bg-gray-50">
							<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all"
								:class="selectedVersions.includes('<?php echo esc_js( $version ); ?>') ? 'border-primary bg-primary' : 'border-gray-300 bg-white'">
								<svg x-show="selectedVersions.includes('<?php echo esc_js( $version ); ?>')" class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
							</div>
							<input type="checkbox" class="hidden" @change="toggleFilter('versions', '<?php echo esc_js( $version ); ?>')">
							<span class="text-sm" :class="selectedVersions.includes('<?php echo esc_js( $version ); ?>') ? 'font-bold text-primary' : 'text-gray-600'"><?php echo esc_html( $version ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $sizes ) ) : ?>
			<!-- Sizes Accordion -->
			<div x-data="{ expanded: false }">
				<button @click="expanded = !expanded" class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Size' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div x-show="expanded" x-collapse class="px-3 pb-3">
					<div class="grid grid-cols-4 gap-2">
						<?php foreach ( $sizes as $size ) : ?>
							<button
								@click="toggleFilter('sizes', '<?php echo esc_js( $size ); ?>')"
								class="rounded-lg border py-2.5 text-xs font-bold transition-all"
								:class="selectedSizes.includes('<?php echo esc_js( $size ); ?>') ? 'border-primary bg-primary text-white' : 'border-gray-200 bg-white text-gray-600'"
							>
								<?php echo esc_html( $size ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $price_ranges ) ) : ?>
			<!-- Price Accordion -->
			<div x-data="{ expanded: false }">
				<button @click="expanded = !expanded" class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-sm font-bold text-zinc-900 transition-colors hover:bg-gray-50">
					<?php echo esc_html( jerseyplug_pll( 'Price' ) ); ?>
					<svg class="h-4 w-4 transition-transform duration-200" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div x-show="expanded" x-collapse class="space-y-2 px-3 pb-3">
					<?php foreach ( $price_ranges as $range ) : ?>
						<label class="flex cursor-pointer items-center gap-3 rounded-lg py-1.5 transition-colors hover:bg-gray-50">
							<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border transition-all"
								:class="selectedPriceRange === '<?php echo esc_js( $range['id'] ); ?>' ? 'border-primary bg-primary' : 'border-gray-300 bg-white'">
								<div x-show="selectedPriceRange === '<?php echo esc_js( $range['id'] ); ?>'" class="h-2 w-2 rounded-full bg-white"></div>
							</div>
							<input type="radio" class="hidden" name="mobile_price" @change="selectedPriceRange = selectedPriceRange === '<?php echo esc_js( $range['id'] ); ?>' ? null : '<?php echo esc_js( $range['id'] ); ?>'">
							<span class="text-sm" :class="selectedPriceRange === '<?php echo esc_js( $range['id'] ); ?>' ? 'font-bold text-primary' : 'text-gray-600'"><?php echo esc_html( $range['label'] ); ?></span>
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
				@click="clearAllFilters()"
				class="flex-1 rounded-xl border border-gray-200 py-3.5 text-sm font-bold text-gray-500 hover:bg-gray-50"
			>
				<?php echo esc_html( $reset_label ); ?>
			</button>
			<button
				@click="drawerOpen = false; applyFilters()"
				class="flex-[2] rounded-xl bg-primary py-3.5 text-sm font-bold text-white shadow-lg transition-transform active:scale-95"
			>
				<?php echo esc_html( $show_results_label ); ?> (<span x-text="totalProducts"></span>)
			</button>
		</div>
	</div>
</div>
