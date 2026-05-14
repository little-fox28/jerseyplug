<?php
/**
 * Header component.
 *
 * @package JerseyPlug
 */

$args = wp_parse_args(
	$args ?? [],
	[
		'top_5_leagues'  => [],
		'national_teams' => [],
		'other_leagues'  => [],
	]
);

$top_5_leagues  = is_array( $args['top_5_leagues'] ) ? $args['top_5_leagues'] : [];
$national_teams = is_array( $args['national_teams'] ) ? $args['national_teams'] : [];
$other_leagues  = is_array( $args['other_leagues'] ) ? $args['other_leagues'] : [];

$shop_url      = get_post_type_archive_link( 'product' ) ?: home_url( '/shop/' );
$world_cup_url = add_query_arg( 'competition', 'World Cup 2026', $shop_url );

$logo_field = function_exists( 'get_field' ) ? get_field( 'header_logo', 'option' ) : null;
$logo_url   = is_array( $logo_field ) && ! empty( $logo_field['url'] ) ? $logo_field['url'] : get_theme_file_uri( '/resources/images/jerseyplug-logo.svg' );
$logo_alt   = is_array( $logo_field ) && ! empty( $logo_field['alt'] ) ? $logo_field['alt'] : get_bloginfo( 'name' );

$announcement = function_exists( 'get_field' ) ? (string) get_field( 'header_announcement', 'option' ) : '';

$raw_languages = function_exists( 'pll_the_languages' )
	? pll_the_languages(
		[
			'raw'                    => 1,
			'hide_if_empty'          => 0,
			'hide_if_no_translation' => 0,
		]
	)
	: [];

$languages_json = esc_attr( wp_json_encode( array_values( $raw_languages ) ) );
$current_lang   = '';

foreach ( $raw_languages as $lang_item ) {
	if ( ! empty( $lang_item['current_lang'] ) ) {
		$current_lang = strtoupper( (string) $lang_item['slug'] );
		break;
	}
}

$translate = static function ( string $text ): string {
	return function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( $text ) : $text;
};
?>

<?php do_action( 'jerseyplug_before_header' ); ?>

<?php if ( $announcement !== '' ) : ?>
	<div class="bg-darkBg text-white text-center text-xs md:text-sm py-2 px-4">
		<?php echo esc_html( $announcement ); ?>
	</div>
<?php endif; ?>

<header
	x-data="{
		isMobileMenuOpen: false,
		activeDropdown: null,
		langSwitcherOpen: false,
		mobileAccordion: { top5: false, national: false, other: false }
	}"
	@keydown.escape.window="isMobileMenuOpen = false; activeDropdown = null; langSwitcherOpen = false"
	class="sticky top-0 z-50 w-full shadow-md h-20 bg-primary text-white"
>
	<div class="container mx-auto px-4 h-full flex items-center justify-between relative">
		<div class="flex items-center gap-4 flex-1 justify-start">
			<button
				class="lg:hidden p-1 rounded transition-colors hover:bg-white/10"
				@click="isMobileMenuOpen = !isMobileMenuOpen"
				aria-label="<?php echo esc_attr( $translate( 'Toggle Mobile Menu' ) ); ?>"
				:aria-expanded="isMobileMenuOpen"
			>
				<template x-if="!isMobileMenuOpen">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
				</template>
				<template x-if="isMobileMenuOpen">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
				</template>
			</button>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-1 group active:scale-95 transition-transform">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>" class="w-auto h-18 md:h-20 lg:h-26" fetchpriority="high" decoding="async" />
			</a>
		</div>

		<nav class="hidden lg:flex items-center gap-6 font-medium text-sm tracking-wide h-full absolute left-1/2 -translate-x-1/2 z-10" aria-label="<?php echo esc_attr( $translate( 'Primary Navigation' ) ); ?>">
				<a href="<?php echo esc_url( $world_cup_url ); ?>" class="transition-colors relative group py-2 h-full flex items-center hover:opacity-80">
					<?php echo esc_html( $translate( 'World Cup 2026' ) ); ?>
				</a>
				<div class="relative h-full flex items-center" @mouseenter="activeDropdown = 'top5'" @mouseleave="activeDropdown = null">
					<button class="flex items-center gap-1 hover:opacity-80">
						<?php echo esc_html( $translate( 'Top 5 Leagues' ) ); ?>
						<svg class="w-[14px] h-[14px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
					</button>
					<div x-show="activeDropdown === 'top5'" x-cloak x-transition class="absolute top-full left-1/2 -translate-x-1/2 w-[90vw] max-w-6xl bg-white text-gray-900 shadow-xl border-t-4 border-accent rounded-b-lg p-6">
						<?php get_template_part( 'components/layout/mega-menu', null, [ 'root_slug' => 'top-5-leagues', 'mode' => 'desktop' ] ); ?>
					</div>
				</div>

				<div class="relative h-full flex items-center" @mouseenter="activeDropdown = 'national'" @mouseleave="activeDropdown = null">
					<button class="flex items-center gap-1 hover:opacity-80">
						<?php echo esc_html( $translate( 'National' ) ); ?>
						<svg class="w-[14px] h-[14px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
					</button>
					<div x-show="activeDropdown === 'national'" x-cloak x-transition class="absolute top-full left-1/2 -translate-x-1/2 w-[90vw] max-w-6xl bg-white text-gray-900 shadow-xl border-t-4 border-accent rounded-b-lg p-6">
						<?php get_template_part( 'components/layout/mega-menu', null, [ 'root_slug' => 'national-teams', 'mode' => 'desktop' ] ); ?>
					</div>
				</div>

				<div class="relative h-full flex items-center" @mouseenter="activeDropdown = 'other'" @mouseleave="activeDropdown = null">
					<button class="flex items-center gap-1 hover:opacity-80">
						<?php echo esc_html( $translate( 'Other' ) ); ?>
						<svg class="w-[14px] h-[14px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
					</button>
					<div x-show="activeDropdown === 'other'" x-cloak x-transition class="absolute top-full left-1/2 -translate-x-1/2 w-[90vw] max-w-6xl bg-white text-gray-900 shadow-xl border-t-4 border-accent rounded-b-lg p-6">
						<?php get_template_part( 'components/layout/mega-menu', null, [ 'root_slug' => 'other-leagues', 'mode' => 'desktop' ] ); ?>
					</div>
				</div>
		</nav>

		<div class="flex items-center gap-4 lg:gap-6 flex-1 justify-end">
			<?php if ( ! empty( $raw_languages ) ) : ?>
				<div x-data="{ languages: <?php echo $languages_json; ?> }" @click.away="langSwitcherOpen = false" class="relative hidden sm:block">
					<button @click="langSwitcherOpen = !langSwitcherOpen" class="flex items-center gap-1.5 hover:opacity-80 transition-all py-2 px-2 rounded" :class="langSwitcherOpen ? 'bg-white/10' : ''" aria-label="<?php echo esc_attr( $translate( 'Change Language' ) ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
						<span class="text-xs font-bold"><?php echo esc_html( $current_lang !== '' ? $current_lang : 'EN' ); ?></span>
						<svg class="w-3 h-3 transition-transform duration-200" :class="langSwitcherOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
					</button>
					<div x-show="langSwitcherOpen" x-cloak x-transition class="absolute top-full right-0 mt-2 w-40 bg-white text-gray-900 shadow-xl rounded-lg py-2 border border-gray-100 z-50">
						<p class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 mb-1">
							<?php echo esc_html( $translate( 'Select Language' ) ); ?>
						</p>
						<template x-for="lang in languages" :key="lang.slug">
							<a :href="lang.url" :class="lang.current_lang ? 'text-[#163300] font-bold bg-gray-50' : 'text-gray-600'" class="w-full text-left px-4 py-2 text-sm flex items-center gap-3 hover:bg-gray-50 transition-colors no-underline">
								<span class="text-lg" x-html="lang.flag"></span>
								<span x-text="lang.name"></span>
								<span x-show="lang.current_lang" class="ml-auto w-1.5 h-1.5 rounded-full bg-[#65cf21]"></span>
							</a>
						</template>
					</div>
				</div>
			<?php endif; ?>

			<form role="search" method="get" class="hidden md:flex relative group" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $translate( 'Search...' ) ); ?>" class="text-white text-sm rounded-full pl-4 pr-10 py-1.5 focus:outline-none focus:ring-1 w-32 focus:w-56 transition-all duration-300 placeholder-white/60 bg-black/20" style="--tw-ring-color:#f2c86c" />
				<input type="hidden" name="post_type" value="product" />
				<button type="submit" class="absolute right-3 top-1.5 text-white/60" aria-label="<?php echo esc_attr( $translate( 'Search' ) ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
				</button>
			</form>

			<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>" class="hover:opacity-80 transition-transform active:scale-90" aria-label="<?php echo esc_attr( $translate( 'My Account' ) ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
			</a>

			<?php if ( function_exists( 'jerseyplug_get_header_cart_markup' ) ) : ?>
				<?php echo jerseyplug_get_header_cart_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
		</div>
	</div>

	<div x-show="isMobileMenuOpen" x-cloak
		x-transition:enter="transition-transform duration-300 ease-in-out"
		x-transition:enter-start="-translate-x-full"
		x-transition:enter-end="translate-x-0"
		x-transition:leave="transition-transform duration-300 ease-in-out"
		x-transition:leave-start="translate-x-0"
		x-transition:leave-end="-translate-x-full"
		class="fixed top-0 left-0 w-80 h-full overflow-y-auto bg-darkBg z-50 lg:hidden"
	>
		<div class="p-4">
			<div class="flex justify-between items-center mb-4">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>" class="w-auto h-8" fetchpriority="high" decoding="async" />
				</a>
				<button @click="isMobileMenuOpen = false" class="text-white" aria-label="<?php echo esc_attr( $translate( 'Close Mobile Menu' ) ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
				</button>
			</div>

			<div class="relative mb-4">
				<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<input type="search" class="w-full bg-white/10 text-white text-sm rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:ring-1 focus:ring-secondary placeholder-gray-400" placeholder="<?php echo esc_attr( $translate( 'Search products...' ) ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
					<button type="submit" class="absolute left-3 top-3 text-gray-400" aria-label="<?php echo esc_attr( $translate( 'Search' ) ); ?>">
						<svg class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
					</button>
					<input type="hidden" name="post_type" value="product" />
				</form>
			</div>

			<nav class="flex flex-col space-y-2" aria-label="<?php echo esc_attr( $translate( 'Primary Navigation' ) ); ?>">
					<a href="<?php echo esc_url( $world_cup_url ); ?>" @click="isMobileMenuOpen = false" class="text-white py-3 border-b border-gray-700/50 font-bold hover:opacity-80">
						<?php echo esc_html( $translate( 'World Cup 2026' ) ); ?>
					</a>

					<div class="py-2 border-b border-gray-700/50">
						<button @click="mobileAccordion.top5 = !mobileAccordion.top5" class="w-full flex justify-between items-center text-white py-2 font-bold">
							<?php echo esc_html( $translate( 'Top 5 Leagues' ) ); ?>
							<svg :class="{'rotate-180': mobileAccordion.top5}" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
						</button>
						<div x-show="mobileAccordion.top5" x-collapse x-cloak class="pt-2 pl-4">
							<?php get_template_part( 'components/layout/mega-menu', null, [ 'root_slug' => 'top-5-leagues', 'mode' => 'mobile' ] ); ?>
						</div>
					</div>

					<div class="py-2 border-b border-gray-700/50">
						<button @click="mobileAccordion.national = !mobileAccordion.national" class="w-full flex justify-between items-center text-white py-2 font-bold">
							<?php echo esc_html( $translate( 'National Teams' ) ); ?>
							<svg :class="{'rotate-180': mobileAccordion.national}" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
						</button>
						<div x-show="mobileAccordion.national" x-collapse x-cloak class="pt-2 pl-4">
							<?php get_template_part( 'components/layout/mega-menu', null, [ 'root_slug' => 'national-teams', 'mode' => 'mobile' ] ); ?>
						</div>
					</div>

					<div class="py-2 border-b border-gray-700/50">
						<button @click="mobileAccordion.other = !mobileAccordion.other" class="w-full flex justify-between items-center text-white py-2 font-bold">
							<?php echo esc_html( $translate( 'Other Leagues' ) ); ?>
							<svg :class="{'rotate-180': mobileAccordion.other}" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
						</button>
						<div x-show="mobileAccordion.other" x-collapse x-cloak class="pt-2 pl-4">
							<?php get_template_part( 'components/layout/mega-menu', null, [ 'root_slug' => 'other-leagues', 'mode' => 'mobile' ] ); ?>
						</div>
					</div>

					<?php if ( ! empty( $raw_languages ) ) : ?>
						<div class="flex items-center justify-between py-3 border-b border-gray-700/50">
							<span class="text-white font-bold"><?php echo esc_html( $translate( 'Language' ) ); ?></span>
							<div class="flex gap-2">
								<?php foreach ( $raw_languages as $lang ) : ?>
									<a
										href="<?php echo esc_url( $lang['url'] ); ?>"
										class="px-2 py-1 text-xs rounded <?php echo ! empty( $lang['current_lang'] ) ? 'bg-accent text-primary' : 'bg-white/10 text-white'; ?>"
									>
										<?php echo esc_html( strtoupper( (string) $lang['slug'] ) ); ?>
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
		class="lg:hidden fixed inset-0 bg-black/50 z-40"
	></div>

</header>

<?php do_action( 'jerseyplug_after_header' ); ?>
