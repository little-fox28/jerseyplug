<?php
/**
 * Desktop filter toolbar component.
 *
 * Sticky bar with dropdown filters and sort select.
 * Uses Alpine.js for dropdown toggle and filter selection.
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

$filters_label = jerseyplug_pll( 'Filters' );
$clear_label   = jerseyplug_pll( 'Clear' );
$sort_label    = jerseyplug_pll( 'Sort' );
$reset_label   = jerseyplug_pll( 'Reset' );
$apply_label   = jerseyplug_pll( 'Apply' );
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
				<div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
					<button
						@click="open = !open"
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all"
						:class="open || selectedCompetitions.length > 0 ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'"
					>
						<?php echo esc_html( jerseyplug_pll( 'Competitions' ) ); ?>
						<span x-show="selectedCompetitions.length > 0" x-text="selectedCompetitions.length" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"></span>
						<svg class="h-4 w-4 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div x-show="open" x-transition.origin.top.left class="absolute left-0 top-full z-50 mt-2 w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $competitions as $comp ) : ?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all"
										:class="selectedCompetitions.includes('<?php echo esc_js( $comp ); ?>') ? 'border-primary bg-primary' : 'border-gray-300 bg-white'">
										<svg x-show="selectedCompetitions.includes('<?php echo esc_js( $comp ); ?>')" class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="checkbox" class="hidden" :checked="selectedCompetitions.includes('<?php echo esc_js( $comp ); ?>')" @change="toggleFilter('competitions', '<?php echo esc_js( $comp ); ?>')">
									<span class="text-sm" :class="selectedCompetitions.includes('<?php echo esc_js( $comp ); ?>') ? 'font-bold text-primary' : 'text-gray-600'"><?php echo esc_html( $comp ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button @click="selectedCompetitions = []; applyFilters()" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button @click="open = false; applyFilters()" class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $teams ) ) : ?>
				<!-- Teams -->
				<div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
					<button
						@click="open = !open"
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all"
						:class="open || selectedTeams.length > 0 ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'"
					>
						<?php echo esc_html( jerseyplug_pll( 'Teams' ) ); ?>
						<span x-show="selectedTeams.length > 0" x-text="selectedTeams.length" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"></span>
						<svg class="h-4 w-4 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div x-show="open" x-transition.origin.top.left class="absolute left-0 top-full z-50 mt-2 w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $teams as $team ) : ?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all"
										:class="selectedTeams.includes('<?php echo esc_js( $team ); ?>') ? 'border-primary bg-primary' : 'border-gray-300 bg-white'">
										<svg x-show="selectedTeams.includes('<?php echo esc_js( $team ); ?>')" class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="checkbox" class="hidden" :checked="selectedTeams.includes('<?php echo esc_js( $team ); ?>')" @change="toggleFilter('teams', '<?php echo esc_js( $team ); ?>')">
									<span class="text-sm" :class="selectedTeams.includes('<?php echo esc_js( $team ); ?>') ? 'font-bold text-primary' : 'text-gray-600'"><?php echo esc_html( $team ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button @click="selectedTeams = []; applyFilters()" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button @click="open = false; applyFilters()" class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $versions ) ) : ?>
				<!-- Version -->
				<div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
					<button
						@click="open = !open"
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all"
						:class="open || selectedVersions.length > 0 ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'"
					>
						<?php echo esc_html( jerseyplug_pll( 'Version' ) ); ?>
						<span x-show="selectedVersions.length > 0" x-text="selectedVersions.length" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"></span>
						<svg class="h-4 w-4 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div x-show="open" x-transition.origin.top.left class="absolute left-0 top-full z-50 mt-2 w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $versions as $version ) : ?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all"
										:class="selectedVersions.includes('<?php echo esc_js( $version ); ?>') ? 'border-primary bg-primary' : 'border-gray-300 bg-white'">
										<svg x-show="selectedVersions.includes('<?php echo esc_js( $version ); ?>')" class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="checkbox" class="hidden" :checked="selectedVersions.includes('<?php echo esc_js( $version ); ?>')" @change="toggleFilter('versions', '<?php echo esc_js( $version ); ?>')">
									<span class="text-sm" :class="selectedVersions.includes('<?php echo esc_js( $version ); ?>') ? 'font-bold text-primary' : 'text-gray-600'"><?php echo esc_html( $version ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button @click="selectedVersions = []; applyFilters()" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button @click="open = false; applyFilters()" class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $sizes ) ) : ?>
				<!-- Size -->
				<div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
					<button
						@click="open = !open"
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all"
						:class="open || selectedSizes.length > 0 ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'"
					>
						<?php echo esc_html( jerseyplug_pll( 'Size' ) ); ?>
						<span x-show="selectedSizes.length > 0" x-text="selectedSizes.length" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white"></span>
						<svg class="h-4 w-4 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div x-show="open" x-transition.origin.top.left class="absolute left-0 top-full z-50 mt-2 w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $sizes as $size ) : ?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all"
										:class="selectedSizes.includes('<?php echo esc_js( $size ); ?>') ? 'border-primary bg-primary' : 'border-gray-300 bg-white'">
										<svg x-show="selectedSizes.includes('<?php echo esc_js( $size ); ?>')" class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="checkbox" class="hidden" :checked="selectedSizes.includes('<?php echo esc_js( $size ); ?>')" @change="toggleFilter('sizes', '<?php echo esc_js( $size ); ?>')">
									<span class="text-sm" :class="selectedSizes.includes('<?php echo esc_js( $size ); ?>') ? 'font-bold text-primary' : 'text-gray-600'"><?php echo esc_html( $size ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button @click="selectedSizes = []; applyFilters()" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button @click="open = false; applyFilters()" class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $price_ranges ) ) : ?>
				<!-- Price -->
				<div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
					<button
						@click="open = !open"
						class="flex items-center gap-2 whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition-all"
						:class="open || selectedPriceRange ? 'border-primary bg-gray-50 text-primary' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'"
					>
						<?php echo esc_html( jerseyplug_pll( 'Price' ) ); ?>
						<span x-show="selectedPriceRange" class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-white">1</span>
						<svg class="h-4 w-4 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<div x-show="open" x-transition.origin.top.left class="absolute left-0 top-full z-50 mt-2 w-64 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
						<div class="max-h-64 overflow-y-auto p-2">
							<?php foreach ( $price_ranges as $range ) : ?>
								<label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-50">
									<div class="flex h-5 w-5 shrink-0 items-center justify-center rounded border transition-all"
										:class="selectedPriceRange === '<?php echo esc_js( $range['id'] ); ?>' ? 'border-primary bg-primary' : 'border-gray-300 bg-white'">
										<svg x-show="selectedPriceRange === '<?php echo esc_js( $range['id'] ); ?>'" class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
									</div>
									<input type="radio" class="hidden" name="desktop_price" :checked="selectedPriceRange === '<?php echo esc_js( $range['id'] ); ?>'" @change="selectedPriceRange = selectedPriceRange === '<?php echo esc_js( $range['id'] ); ?>' ? null : '<?php echo esc_js( $range['id'] ); ?>'; applyFilters()">
									<span class="text-sm" :class="selectedPriceRange === '<?php echo esc_js( $range['id'] ); ?>' ? 'font-bold text-primary' : 'text-gray-600'"><?php echo esc_html( $range['label'] ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 p-3">
							<button @click="selectedPriceRange = null; applyFilters()" class="text-xs text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-red-500"><?php echo esc_html( $reset_label ); ?></button>
							<button @click="open = false; applyFilters()" class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary/90"><?php echo esc_html( $apply_label ); ?></button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<!-- Clear all -->
			<button
				x-show="totalFilters > 0"
				x-transition
				@click="clearAllFilters()"
				class="ml-2 flex items-center gap-1 whitespace-nowrap text-sm font-medium text-red-500 hover:underline"
			>
				<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
				<?php echo esc_html( $clear_label ); ?> (<span x-text="totalFilters"></span>)
			</button>
		</div>

		<!-- Sort -->
		<div class="flex shrink-0 items-center gap-3 border-l border-gray-200 pl-6">
			<span class="text-sm text-gray-500"><?php echo esc_html( $sort_label ); ?>:</span>
			<select
				x-model="sortBy"
				@change="applyFilters()"
				class="cursor-pointer border-none bg-transparent text-sm font-bold text-primary focus:ring-0"
			>
				<option value="featured"><?php echo esc_html( jerseyplug_pll( 'Featured' ) ); ?></option>
				<option value="price_low"><?php echo esc_html( jerseyplug_pll( 'Price: Low to High' ) ); ?></option>
				<option value="price_high"><?php echo esc_html( jerseyplug_pll( 'Price: High to Low' ) ); ?></option>
				<option value="newest"><?php echo esc_html( jerseyplug_pll( 'Newest' ) ); ?></option>
			</select>
		</div>
	</div>
</div>
