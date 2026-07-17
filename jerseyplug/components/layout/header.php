<?php

/**
 * Header component.
 *
 * @package JerseyPlug
 */

$args = wp_parse_args( $args ?? [], [] );

$shop_url = get_post_type_archive_link('product') ?: home_url('/shop/');

$logo_field = function_exists('get_field') ? get_field('header_logo', 'option') : null;
$logo_url   = is_array($logo_field) && ! empty($logo_field['url']) ? $logo_field['url'] : get_theme_file_uri('/resources/images/jerseyplug-logo.svg');
$logo_alt   = is_array($logo_field) && ! empty($logo_field['alt']) ? $logo_field['alt'] : get_bloginfo('name');

$announcement = function_exists('get_field') ? (string) get_field('header_announcement', 'option') : '';

$raw_languages = function_exists('pll_the_languages')
	? pll_the_languages(
		[
			'raw'                    => 1,
			'hide_if_empty'          => 0,
			'hide_if_no_translation' => 0,
		]
	)
	: [];

$flag_map = [
	'en' => 'https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg',
	'af' => 'https://upload.wikimedia.org/wikipedia/commons/a/af/Flag_of_South_Africa.svg',
];

foreach ($raw_languages as &$lang_item) {
	$slug = strtolower((string) ($lang_item['slug'] ?? ''));
	$lang_item['flag_url'] = $flag_map[$slug] ?? '';
}
unset($lang_item);

$languages_json = esc_attr(wp_json_encode(array_values($raw_languages)));
$current_lang   = '';

foreach ($raw_languages as $lang_item) {
	if (! empty($lang_item['current_lang'])) {
		$current_lang = strtoupper((string) $lang_item['slug']);
		break;
	}
}

$translate = static function (string $text): string {
	return function_exists('jerseyplug_pll') ? jerseyplug_pll($text) : $text;
};

$logo_url   = (string) apply_filters('jerseyplug_header_logo_url', $logo_url);
$logo_alt   = (string) apply_filters('jerseyplug_header_logo_alt', $logo_alt);

$mega_menu = function_exists('get_jerseyplug_mega_menu') ? get_jerseyplug_mega_menu() : [];
$mega_menu = is_array($mega_menu) ? $mega_menu : [];

$header_classes        = (string) apply_filters('jerseyplug_header_classes', 'sticky top-0 z-50 w-full shadow-md h-20 bg-primary text-white', $args);
$container_classes     = (string) apply_filters('jerseyplug_header_container_classes', 'container mx-auto px-4 h-full flex items-center justify-between relative', $args);
$nav_classes           = (string) apply_filters('jerseyplug_header_nav_classes', 'hidden lg:flex items-center gap-6 font-medium text-sm tracking-wide h-full absolute left-1/2 -translate-x-1/2 z-10', $args);
$actions_classes       = (string) apply_filters('jerseyplug_header_actions_classes', 'flex items-center gap-4 lg:gap-6 flex-1 justify-end', $args);
$mobile_drawer_classes = (string) apply_filters('jerseyplug_header_mobile_drawer_classes', 'fixed top-0 left-0 w-80 h-full overflow-y-auto bg-darkBg z-50 lg:hidden', $args);
$mobile_overlay_classes = (string) apply_filters('jerseyplug_header_mobile_overlay_classes', 'lg:hidden fixed inset-0 bg-black/50 z-40', $args);
?>

<?php do_action('jerseyplug_before_header'); ?>

<?php if ($announcement !== '') : ?>
	<div class="bg-darkBg text-white text-center text-xs md:text-sm py-2 px-4">
		<?php echo esc_html($announcement); ?>
	</div>
<?php endif; ?>

<header
	x-data="{
		isMobileMenuOpen: false,
		activeDropdown: null,
		langSwitcherOpen: false,
		mobileAccordion: {}
	}"
	@keydown.escape.window="isMobileMenuOpen = false; activeDropdown = null; langSwitcherOpen = false"
	class="<?php echo esc_attr($header_classes); ?>">
	<?php do_action('jerseyplug_before_header_inner', $args); ?>
	<div class="<?php echo esc_attr($container_classes); ?>">
		<div class="flex items-center gap-4 flex-1 justify-start">
			<button
				class="lg:hidden p-1 rounded transition-colors hover:bg-white/10"
				@click="isMobileMenuOpen = !isMobileMenuOpen"
				aria-label="<?php echo esc_attr($translate('Toggle Mobile Menu')); ?>"
				:aria-expanded="isMobileMenuOpen">
				<template x-if="!isMobileMenuOpen">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<line x1="3" y1="12" x2="21" y2="12"></line>
						<line x1="3" y1="6" x2="21" y2="6"></line>
						<line x1="3" y1="18" x2="21" y2="18"></line>
					</svg>
				</template>
				<template x-if="isMobileMenuOpen">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<line x1="18" y1="6" x2="6" y2="18"></line>
						<line x1="6" y1="6" x2="18" y2="18"></line>
					</svg>
				</template>
			</button>
			<?php
			get_template_part(
				'components/ui/logo',
				null,
				[
					'class'         => 'w-auto !h-20 md:!h-24 lg:!h-28',
					'img_class'     => 'object-contain transition-all',
					'wrapper_class' => 'flex items-center gap-1 group active:scale-95 transition-transform absolute left-1/2 -translate-x-1/2 lg:static lg:translate-x-0',
					'logo_url'      => $logo_url,
					'logo_alt'      => $logo_alt,
					'fetchpriority' => 'high',
					'loading'       => 'eager',
					'decoding'      => 'async',
				]
			);
			?>
		</div>

		<?php do_action('jerseyplug_before_header_nav', $args); ?>
		<nav class="<?php echo esc_attr($nav_classes); ?>" aria-label="<?php echo esc_attr($translate('Primary Navigation')); ?>">
			<?php foreach ($mega_menu as $menu_group) : ?>
				<?php
				$root_slug  = sanitize_title((string) ($menu_group['slug'] ?? ''));
				$root_name  = (string) ($menu_group['name'] ?? '');
				$root_link  = (string) ($menu_group['link'] ?? '');
				$root_label = (string) apply_filters('jerseyplug_header_mega_menu_label', $translate($root_name), $menu_group);
				$children   = is_array($menu_group['children'] ?? null) ? $menu_group['children'] : [];
				?>
				<?php if ($root_slug === '') : ?>
					<?php continue; ?>
				<?php endif; ?>

				<?php if (empty($children)) : ?>
					<a href="<?php echo esc_url($root_link); ?>" class="transition-colors relative group py-2 h-full flex items-center hover:opacity-80">
						<?php echo esc_html($root_label); ?>
					</a>
				<?php else : ?>
					<div class="relative h-full flex items-center" @mouseenter="activeDropdown = '<?php echo esc_js($root_slug); ?>'" @mouseleave="activeDropdown = null">
						<button class="flex items-center gap-1 hover:opacity-80">
							<?php echo esc_html($root_label); ?>
							<svg class="w-[14px] h-[14px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<polyline points="6 9 12 15 18 9"></polyline>
							</svg>
						</button>
						<div x-show="activeDropdown === '<?php echo esc_js($root_slug); ?>'" x-cloak x-transition class="absolute top-full left-1/2 -translate-x-1/2 w-[90vw] max-w-6xl bg-white text-gray-900 shadow-xl border-t-4 border-accent rounded-b-lg p-6">
							<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
								<?php foreach ($children as $child) : ?>
									<?php
									$child_name     = (string) ($child['name'] ?? '');
									$child_link     = (string) ($child['link'] ?? '');
									$child_logo_src = (string) ($child['logo_src'] ?? '');
									$child_label    = (string) apply_filters('jerseyplug_header_mega_menu_group_label', $translate($child_name), $child, $menu_group);
									$grand_children = is_array($child['children'] ?? null) ? $child['children'] : [];
									?>
									<div class="flex flex-col gap-3">
										<?php if ($child_link !== '') : ?>
											<a href="<?php echo esc_url($child_link); ?>" class="flex items-center gap-3 pb-2 border-b border-gray-100 hover:opacity-90 transition-colors">
												<img
													src="<?php echo esc_url($child_logo_src); ?>"
													alt="<?php echo esc_attr($child_label); ?>"
													class="w-6 h-6 rounded-full object-contain"
													loading="lazy"
													decoding="async" />
												<span class="text-sm font-bold text-primary">
													<?php echo esc_html($child_label); ?>
												</span>
											</a>
										<?php else : ?>
											<div class="flex items-center gap-3 pb-2 border-b border-gray-100">
												<img
													src="<?php echo esc_url($child_logo_src); ?>"
													alt="<?php echo esc_attr($child_label); ?>"
													class="w-6 h-6 rounded-full object-contain"
													loading="lazy"
													decoding="async" />
												<span class="text-sm font-bold text-primary">
													<?php echo esc_html($child_label); ?>
												</span>
											</div>
										<?php endif; ?>

										<?php if (! empty($grand_children)) : ?>
											<ul>
												<?php foreach ($grand_children as $grandchild) : ?>
													<?php
													$grand_name     = (string) ($grandchild['name'] ?? '');
													$grand_link     = (string) ($grandchild['link'] ?? '');
													$grand_logo_src = (string) ($grandchild['logo_src'] ?? '');
													$grand_label    = (string) apply_filters('jerseyplug_header_mega_menu_item_label', $translate($grand_name), $grandchild, $child, $menu_group);
													?>
													<li class="flex items-center space-x-2 py-1.5">
														<span class="flex items-center justify-center w-5 h-5 shrink-0">
															<img
																src="<?php echo esc_url($grand_logo_src); ?>"
																alt="<?php echo esc_attr($grand_label); ?>"
																class="w-5 h-5 rounded-full object-contain"
																loading="lazy"
																decoding="async" />
														</span>
														<?php if ($grand_link !== '') : ?>
															<a href="<?php echo esc_url($grand_link); ?>" class="text-sm text-gray-700 hover:text-green-800 transition-colors">
																<?php echo esc_html($grand_label); ?>
															</a>
														<?php else : ?>
															<span class="text-sm text-gray-700">
																<?php echo esc_html($grand_label); ?>
															</span>
														<?php endif; ?>
													</li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>
		<?php do_action('jerseyplug_after_header_nav', $args); ?>

		<?php do_action('jerseyplug_before_header_actions', $args); ?>
		<div class="<?php echo esc_attr($actions_classes); ?>">
			<?php if (! empty($raw_languages)) : ?>
				<div x-data="{ languages: <?php echo $languages_json; ?> }" @click.away="langSwitcherOpen = false" class="relative hidden sm:block">
					<button @click="langSwitcherOpen = !langSwitcherOpen" class="flex items-center gap-1.5 hover:opacity-80 transition-all py-2 px-2 rounded" :class="langSwitcherOpen ? 'bg-white/10' : ''" aria-label="<?php echo esc_attr($translate('Change Language')); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10"></circle>
							<line x1="2" y1="12" x2="22" y2="12"></line>
							<path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
						</svg>
						<span class="text-xs font-bold"><?php echo esc_html($current_lang !== '' ? $current_lang : 'EN'); ?></span>
						<svg class="w-3 h-3 transition-transform duration-200" :class="langSwitcherOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
						</svg>
					</button>
					<div x-show="langSwitcherOpen" x-cloak x-transition class="absolute top-full right-0 mt-2 w-40 bg-white text-gray-900 shadow-xl rounded-lg py-2 border border-gray-100 z-50">
						<p class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 mb-1">
							<?php echo esc_html($translate('Select Language')); ?>
						</p>
						<template x-for="lang in languages" :key="lang.slug">
							<a :href="lang.url" :class="lang.current_lang ? 'text-[#163300] font-bold bg-gray-50' : 'text-gray-600'" class="w-full text-left px-4 py-2 text-sm flex items-center gap-3 hover:bg-gray-50 transition-colors no-underline">
								<img :src="lang.flag_url" :alt="lang.name" class="w-5 h-4 object-cover inline-block mr-2" loading="lazy" x-show="lang.flag_url" />
								<span x-text="lang.name"></span>
								<span x-show="lang.current_lang" class="ml-auto w-1.5 h-1.5 rounded-full bg-[#65cf21]"></span>
							</a>
						</template>
					</div>
				</div>
			<?php endif; ?>

			<form role="search" method="get" class="hidden md:flex relative group" action="<?php echo esc_url($shop_url); ?>" 
				x-data="{
					query: '<?php echo esc_js(get_search_query()); ?>',
					results: [],
					isLoading: false,
					isOpen: false,
					cache: {},
					selectedIndex: -1,
					fetchResults() {
						if (!this.query.trim()) {
							this.results = [];
							this.isOpen = false;
							return;
						}
						
						const searchKey = this.query.trim().toLowerCase();
						if (this.cache[searchKey]) {
							this.results = this.cache[searchKey];
							this.isOpen = true;
							this.selectedIndex = -1;
							return;
						}

						this.isLoading = true;
						this.isOpen = true;
						this.selectedIndex = -1;
						fetch(jerseyplug_ajax.url + '?action=jerseyplug_live_search&nonce=' + jerseyplug_ajax.nonce + '&q=' + encodeURIComponent(this.query))
							.then(res => res.json())
							.then(res => {
								this.isLoading = false;
								if (res.success) {
									this.results = res.data.items || [];
									this.cache[searchKey] = this.results;
								}
							});
					},
					highlight(text) {
						if (!this.query.trim()) return text;
						const regex = new RegExp(`(${this.query.trim().replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi');
						return text.replace(regex, '<span class=\'bg-yellow-200 text-gray-900 px-0.5 rounded\'>$1</span>');
					},
					handleKeydown(e) {
						if (!this.isOpen || this.results.length === 0) return;
						if (e.key === 'ArrowDown') {
							e.preventDefault();
							this.selectedIndex = (this.selectedIndex + 1) % this.results.length;
						} else if (e.key === 'ArrowUp') {
							e.preventDefault();
							this.selectedIndex = this.selectedIndex - 1 < 0 ? this.results.length - 1 : this.selectedIndex - 1;
						} else if (e.key === 'Enter' && this.selectedIndex >= 0) {
							e.preventDefault();
							window.location.href = this.results[this.selectedIndex].url;
						}
					}
				}"
				@submit="if(!query.trim()) { $event.preventDefault(); $refs.searchInput.focus(); }"
				@keydown="handleKeydown($event)"
				@click.away="isOpen = false">
				<div class="relative flex items-center p-[1.5px] rounded-full bg-white/20 focus-within:bg-gradient-to-r focus-within:from-pink-500 focus-within:via-purple-500 focus-within:to-indigo-500 transition-all duration-500 w-36 focus-within:w-56 focus-within:shadow-[0_0_15px_rgba(168,85,247,0.5)]">
					<input x-ref="searchInput" type="search" name="s" x-model="query" @input.debounce.500ms="fetchResults" @focus="if(query.trim()) isOpen = true" placeholder="<?php echo esc_attr($translate('Search...')); ?>" class="w-full bg-[#163300] text-white text-sm rounded-full pl-4 pr-10 py-1.5 focus:outline-none placeholder-white/60 transition-colors" autocomplete="off" />
					<input type="hidden" name="post_type" value="product" />
					<button type="submit" class="absolute right-3 text-white/70 hover:text-white transition-colors" aria-label="<?php echo esc_attr($translate('Search')); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"></circle>
							<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
						</svg>
					</button>
				</div>

				<!-- Live Search Dropdown -->
				<div x-show="isOpen" x-transition x-cloak class="absolute top-full mt-3 w-full min-w-[320px] right-0 md:left-auto md:-right-4 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden text-gray-800">
					<div x-show="isLoading" class="p-6 text-center text-gray-400">
						<svg class="animate-spin h-6 w-6 mx-auto text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
					</div>
					<div x-show="!isLoading && results.length === 0" class="p-6 text-center text-sm text-gray-500 font-medium">
						<?php echo esc_html($translate('No products found.')); ?>
					</div>
					<div x-show="!isLoading && results.length > 0">
						<template x-for="(item, index) in results" :key="item.id">
							<a :href="item.url" class="flex items-center gap-3 p-3 border-b border-gray-50 hover:bg-gray-50 transition-colors" :class="{'bg-gray-50': selectedIndex === index}">
								<img :src="item.image" class="w-12 h-12 object-cover rounded shadow-sm shrink-0" />
								<div class="flex flex-col min-w-0">
									<span class="text-sm font-bold text-[#163300] truncate block" x-html="highlight(item.title)"></span>
									<span class="text-xs text-red-600 font-bold block" x-html="item.price"></span>
								</div>
							</a>
						</template>
						<button type="submit" class="w-full p-3 text-center text-xs font-bold text-[#163300] hover:bg-gray-50 uppercase tracking-widest border-t border-gray-100 bg-gray-50/50">
							<?php echo esc_html($translate('View All Results →')); ?>
						</button>
					</div>
				</div>
			</form>

			<a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : wp_login_url()); ?>" class="hover:opacity-80 transition-transform active:scale-90" aria-label="<?php echo esc_attr($translate('My Account')); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
					<circle cx="12" cy="7" r="4"></circle>
				</svg>
			</a>

			<?php if (function_exists('jerseyplug_get_header_cart_markup')) : ?>
				<?php echo jerseyplug_get_header_cart_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
				?>
			<?php endif; ?>
		</div>
		<?php do_action('jerseyplug_after_header_actions', $args); ?>
	</div>
	<?php do_action('jerseyplug_after_header_inner', $args); ?>

	<?php do_action('jerseyplug_before_header_mobile_menu', $args); ?>
	<div x-show="isMobileMenuOpen" x-cloak
		x-transition:enter="transition-transform duration-300 ease-in-out"
		x-transition:enter-start="-translate-x-full"
		x-transition:enter-end="translate-x-0"
		x-transition:leave="transition-transform duration-300 ease-in-out"
		x-transition:leave-start="translate-x-0"
		x-transition:leave-end="-translate-x-full"
		class="<?php echo esc_attr($mobile_drawer_classes); ?>">
		<div class="p-4">
			<div class="flex justify-between items-center mb-4">
				<button @click="isMobileMenuOpen = false" class="text-white ml-auto" aria-label="<?php echo esc_attr($translate('Close Mobile Menu')); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<line x1="18" y1="6" x2="6" y2="18"></line>
						<line x1="6" y1="6" x2="18" y2="18"></line>
					</svg>
				</button>
			</div>

			<div class="relative mb-4 z-50">
				<form role="search" method="get" class="search-form" action="<?php echo esc_url($shop_url); ?>"
					x-data="{
						query: '<?php echo esc_js(get_search_query()); ?>',
						results: [],
						isLoading: false,
						isOpen: false,
						cache: {},
						selectedIndex: -1,
						fetchResults() {
							if (!this.query.trim()) {
								this.results = [];
								this.isOpen = false;
								return;
							}
							
							const searchKey = this.query.trim().toLowerCase();
							if (this.cache[searchKey]) {
								this.results = this.cache[searchKey];
								this.isOpen = true;
								this.selectedIndex = -1;
								return;
							}

							this.isLoading = true;
							this.isOpen = true;
							this.selectedIndex = -1;
							fetch(jerseyplug_ajax.url + '?action=jerseyplug_live_search&nonce=' + jerseyplug_ajax.nonce + '&q=' + encodeURIComponent(this.query))
								.then(res => res.json())
								.then(res => {
									this.isLoading = false;
									if (res.success) {
										this.results = res.data.items || [];
										this.cache[searchKey] = this.results;
									}
								});
						},
						highlight(text) {
							if (!this.query.trim()) return text;
							const regex = new RegExp(`(${this.query.trim().replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi');
							return text.replace(regex, '<span class=\'bg-yellow-200 text-gray-900 px-0.5 rounded\'>$1</span>');
						},
						handleKeydown(e) {
							if (!this.isOpen || this.results.length === 0) return;
							if (e.key === 'ArrowDown') {
								e.preventDefault();
								this.selectedIndex = (this.selectedIndex + 1) % this.results.length;
							} else if (e.key === 'ArrowUp') {
								e.preventDefault();
								this.selectedIndex = this.selectedIndex - 1 < 0 ? this.results.length - 1 : this.selectedIndex - 1;
							} else if (e.key === 'Enter' && this.selectedIndex >= 0) {
								e.preventDefault();
								window.location.href = this.results[this.selectedIndex].url;
							}
						}
					}"
					@submit="if(!query.trim()) { $event.preventDefault(); $refs.searchMob.focus(); }"
					@keydown="handleKeydown($event)"
					@click.away="isOpen = false">
					<div class="relative flex items-center p-[1.5px] rounded-xl bg-white/20 focus-within:bg-gradient-to-r focus-within:from-pink-500 focus-within:via-purple-500 focus-within:to-indigo-500 focus-within:shadow-[0_0_15px_rgba(168,85,247,0.4)] transition-all duration-500">
						<input x-ref="searchMob" type="search" x-model="query" @input.debounce.500ms="fetchResults" @focus="if(query.trim()) isOpen = true" class="w-full bg-[#163300] text-white text-sm rounded-[10.5px] pl-10 pr-4 py-3 focus:outline-none placeholder-white/50" placeholder="<?php echo esc_attr($translate('Search products...')); ?>" name="s" autocomplete="off" />
						<button type="submit" class="absolute left-3 text-white/50 hover:text-white transition-colors" aria-label="<?php echo esc_attr($translate('Search')); ?>">
							<svg class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
								<circle cx="11" cy="11" r="8"></circle>
								<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
							</svg>
						</button>
						<input type="hidden" name="post_type" value="product" />
					</div>

					<!-- Live Search Dropdown Mobile -->
					<div x-show="isOpen" x-transition x-cloak class="absolute top-full mt-2 left-0 w-full bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden text-gray-800">
						<div x-show="isLoading" class="p-6 text-center text-gray-400">
							<svg class="animate-spin h-6 w-6 mx-auto text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
						</div>
						<div x-show="!isLoading && results.length === 0" class="p-6 text-center text-sm text-gray-500 font-medium">
							<?php echo esc_html($translate('No products found.')); ?>
						</div>
						<div x-show="!isLoading && results.length > 0">
							<template x-for="(item, index) in results" :key="item.id">
								<a :href="item.url" class="flex items-center gap-3 p-3 border-b border-gray-50 hover:bg-gray-50 transition-colors" :class="{'bg-gray-50': selectedIndex === index}">
									<img :src="item.image" class="w-12 h-12 object-cover rounded shadow-sm shrink-0" />
									<div class="flex flex-col min-w-0">
										<span class="text-sm font-bold text-[#163300] truncate block" x-html="highlight(item.title)"></span>
										<span class="text-xs text-red-600 font-bold block" x-html="item.price"></span>
									</div>
								</a>
							</template>
							<button type="submit" class="w-full p-3 text-center text-xs font-bold text-[#163300] hover:bg-gray-50 uppercase tracking-widest border-t border-gray-100 bg-gray-50/50">
								<?php echo esc_html($translate('View All Results →')); ?>
							</button>
						</div>
					</div>
				</form>
			</div>

			<nav class="flex flex-col space-y-2" aria-label="<?php echo esc_attr($translate('Primary Navigation')); ?>">
				<?php foreach ($mega_menu as $menu_group) : ?>
					<?php
					$root_slug  = sanitize_title((string) ($menu_group['slug'] ?? ''));
					$root_name  = (string) ($menu_group['name'] ?? '');
					$root_link  = (string) ($menu_group['link'] ?? '');
					$root_label = (string) apply_filters('jerseyplug_header_mega_menu_label', $translate($root_name), $menu_group);
					$children   = is_array($menu_group['children'] ?? null) ? $menu_group['children'] : [];
					?>
					<?php if ($root_slug === '') : ?>
						<?php continue; ?>
					<?php endif; ?>

					<?php if (empty($children)) : ?>
						<a href="<?php echo esc_url($root_link); ?>" @click="isMobileMenuOpen = false" class="text-white py-3 border-b border-gray-700/50 font-bold hover:opacity-80">
							<?php echo esc_html($root_label); ?>
						</a>
					<?php else : ?>
						<div class="py-2 border-b border-gray-700/50">
							<button @click="mobileAccordion['<?php echo esc_js($root_slug); ?>'] = !mobileAccordion['<?php echo esc_js($root_slug); ?>']" class="w-full flex justify-between items-center text-white py-2 font-bold">
								<?php echo esc_html($root_label); ?>
								<svg :class="{'rotate-180': mobileAccordion['<?php echo esc_js($root_slug); ?>']}" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<polyline points="6 9 12 15 18 9"></polyline>
								</svg>
							</button>
							<div x-show="mobileAccordion['<?php echo esc_js($root_slug); ?>']" x-collapse x-cloak class="pt-2 pl-4 space-y-3">
								<?php foreach ($children as $child) : ?>
									<?php
									$child_name     = (string) ($child['name'] ?? '');
									$child_link     = (string) ($child['link'] ?? '');
									$child_logo     = (string) ($child['logo_url'] ?? '');
									$child_external = (string) ($child['external_logo_url'] ?? '');
									$child_thumb_id = (int) ($child['thumbnail_id'] ?? 0);
									$child_logo_src = $child_external;
									if ($child_thumb_id > 0 && $child_logo !== '') {
										$child_logo_src = $child_logo;
									} elseif ($child_logo_src === '') {
										$child_logo_src = $child_logo;
									}
									$child_label    = (string) apply_filters('jerseyplug_header_mega_menu_group_label', $translate($child_name), $child, $menu_group);
									$grand_children = is_array($child['children'] ?? null) ? $child['children'] : [];
									?>
									<div>
										<a href="<?php echo esc_url($child_link); ?>" @click="isMobileMenuOpen = false" class="flex items-center gap-2 text-white py-1 text-sm font-semibold hover:opacity-80">
											<img
												src="<?php echo esc_url($child_logo_src); ?>"
												alt="<?php echo esc_attr($child_label); ?>"
												class="w-5 h-5 object-contain rounded-full"
												loading="lazy"
												decoding="async" />
											<?php echo esc_html($child_label); ?>
										</a>
										<?php if (! empty($grand_children)) : ?>
											<div class="pl-7 pt-2 space-y-1">
												<?php foreach ($grand_children as $grandchild) : ?>
													<?php
													$grand_name     = (string) ($grandchild['name'] ?? '');
													$grand_link     = (string) ($grandchild['link'] ?? '');
													$grand_logo_src = (string) ($grandchild['logo_src'] ?? '');
													$grand_label    = (string) apply_filters('jerseyplug_header_mega_menu_item_label', $translate($grand_name), $grandchild, $child, $menu_group);
													?>
													<a href="<?php echo esc_url($grand_link); ?>" @click="isMobileMenuOpen = false" class="flex items-center gap-2 text-gray-300 py-1 text-sm hover:text-white">
														<img
															src="<?php echo esc_url($grand_logo_src); ?>"
															alt="<?php echo esc_attr($grand_label); ?>"
															class="w-4 h-4 object-contain rounded-full"
															loading="lazy"
															decoding="async" />
														<?php echo esc_html($grand_label); ?>
													</a>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>

				<?php if (! empty($raw_languages)) : ?>
					<div class="flex items-center justify-between py-3 border-b border-gray-700/50">
						<span class="text-white font-bold"><?php echo esc_html($translate('Language')); ?></span>
						<div class="flex gap-2">
							<?php foreach ($raw_languages as $lang) : ?>
								<a
									href="<?php echo esc_url($lang['url']); ?>"
									class="px-2 py-1 text-xs rounded <?php echo ! empty($lang['current_lang']) ? 'bg-accent text-primary' : 'bg-white/10 text-white'; ?>">
									<?php echo esc_html(strtoupper((string) $lang['slug'])); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</nav>
		</div>
	</div>

	<div x-show="isMobileMenuOpen" @click="isMobileMenuOpen = false" x-cloak
		x-transition:enter="ease-out duration-300"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100"
		x-transition:leave="ease-in duration-200"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
		class="<?php echo esc_attr($mobile_overlay_classes); ?>"></div>
	<?php do_action('jerseyplug_after_header_mobile_menu', $args); ?>

</header>

<?php do_action('jerseyplug_after_header'); ?>