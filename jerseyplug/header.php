<?php
/**
 * Theme header template.
 *
 * @package JerseyPlug
 */

// Define navigation data arrays.
$top_5_leagues = [
    [
        'nameKey' => 'Premier League',
        'logo' => 'premier-league.png',
        'teams' => [
            ['nameKey' => 'Arsenal', 'logo' => 'arsenal.png'],
            ['nameKey' => 'Chelsea', 'logo' => 'chelsea.png'],
            ['nameKey' => 'Liverpool', 'logo' => 'liverpool.png'],
            ['nameKey' => 'Manchester City', 'logo' => 'man_city.png'],
            ['nameKey' => 'Manchester United', 'logo' => 'man_utd.png'],
        ],
    ],
    [
        'nameKey' => 'La Liga',
        'logo' => 'la-liga.png',
        'teams' => [
            ['nameKey' => 'FC Barcelona', 'logo' => 'barcelona.png'],
            ['nameKey' => 'Real Madrid', 'logo' => 'real_madrid.png'],
            ['nameKey' => 'Atletico Madrid', 'logo' => 'atletico_madrid.png'],
        ],
    ],
    [
        'nameKey' => 'Serie A',
        'logo' => 'serie-a.png',
        'teams' => [
            ['nameKey' => 'AC Milan', 'logo' => 'ac_milan.png'],
            ['nameKey' => 'Inter Milan', 'logo' => 'inter_milan.png'],
            ['nameKey' => 'Juventus', 'logo' => 'juventus.png'],
        ],
    ],
    [
        'nameKey' => 'Bundesliga',
        'logo' => 'bundesliga.png',
        'teams' => [
            ['nameKey' => 'Bayern Munich', 'logo' => 'bayern_munich.png'],
            ['nameKey' => 'Borussia Dortmund', 'logo' => 'dortmund.png'],
        ],
    ],
    [
        'nameKey' => 'Ligue 1',
        'logo' => 'ligue-1.png',
        'teams' => [
            ['nameKey' => 'Paris Saint-Germain', 'logo' => 'psg.png'],
        ],
    ],
];

$national_teams = [
    ['nameKey' => 'Argentina', 'flag' => '🇦🇷'],
    ['nameKey' => 'Brazil', 'flag' => '🇧🇷'],
    ['nameKey' => 'England', 'flag' => '🏴󠁧󠁢󠁥󠁮󠁧󠁿'],
    ['nameKey' => 'France', 'flag' => '🇫🇷'],
    ['nameKey' => 'Germany', 'flag' => '🇩🇪'],
    ['nameKey' => 'Portugal', 'flag' => '🇵🇹'],
    ['nameKey' => 'Spain', 'flag' => '🇪🇸'],
];

$other_leagues = [
    ['nameKey' => 'MLS', 'logo' => '⚽️'],
    ['nameKey' => 'Brasileirão', 'logo' => '⚽️'],
    ['nameKey' => 'Liga Portugal', 'logo' => '⚽️'],
    ['nameKey' => 'Eredivisie', 'logo' => '⚽️'],
];

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-white text-zinc-900 antialiased'); ?>>

<?php wp_body_open(); ?>

<?php do_action('tailpress_site_before'); ?>

<div id="page" class="min-h-screen flex flex-col">

    <?php do_action('tailpress_header'); ?>

    <header x-data="{
        isMobileMenuOpen: false,
        activeDropdown: null,
        isSearchOpen: false,
        mobileAccordion: { top5: false, national: false, other: false }
    }" @keydown.escape.window="isMobileMenuOpen = false; isSearchOpen = false; activeDropdown = null" class="sticky top-0 z-50 w-full shadow-md h-20 bg-primary text-white">
        <div class="container mx-auto px-4 h-full flex items-center justify-between relative">
            <div class="flex items-center gap-4 flex-1 justify-start">
                <button
                    class="lg:hidden p-1 rounded transition-colors hover:bg-white/10"
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    aria-label="<?php echo esc_attr__('Toggle Mobile Menu', 'jerseyplug'); ?>"
                >
                    <template x-if="!isMobileMenuOpen">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </template>
                    <template x-if="isMobileMenuOpen">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </template>
                </button>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-1 group active:scale-95 transition-transform">
                    <img src="<?php echo esc_url(get_theme_file_uri('/resources/images/jerseyplug-logo.svg')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="w-auto" style="height: 5rem;">
                </a>
            </div>

            <nav class="hidden lg:flex items-center gap-6 font-medium text-sm tracking-wide h-full absolute left-1/2 -translate-x-1/2 z-10">
                <a href="#" class="transition-colors relative group py-2 h-full flex items-center hover:opacity-80">
                    <?php esc_html_e('World Cup 2026', 'jerseyplug'); ?>
                </a>
                <div class="relative h-full flex items-center" @mouseenter="activeDropdown = 'top5'" @mouseleave="activeDropdown = null">
                    <button class="flex items-center gap-1 hover:opacity-80">
                        <?php esc_html_e('Top 5 Leagues', 'jerseyplug'); ?>
                        <svg class="w-[14px] h-[14px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div x-show="activeDropdown === 'top5'" x-cloak x-transition class="absolute top-full left-1/2 -translate-x-1/2 w-[90vw] max-w-6xl bg-white text-gray-900 shadow-xl border-t-4 border-accent rounded-b-lg p-6 grid grid-cols-5 gap-6">
                        <?php foreach ($top_5_leagues as $league) : ?>
                            <div class="flex flex-col border-r border-gray-100 last:border-0 pr-4">
                                <div class="flex items-center gap-3 mb-4 pb-2 border-b border-gray-100">
                                    <img src="<?php echo get_theme_file_uri('/resources/images/leagues/' . $league['logo']); ?>" alt="<?php echo esc_attr__($league['nameKey'], 'jerseyplug'); ?>" class="w-8 h-8 rounded-full object-cover"/>
                                    <span class="font-bold text-sm text-primary"><?php esc_html_e($league['nameKey'], 'jerseyplug'); ?></span>
                                </div>
                                <ul class="space-y-3">
                                    <?php foreach ($league['teams'] as $team) : ?>
                                        <li>
                                            <a href="#" class="flex items-center gap-3 hover:bg-gray-50 p-1.5 rounded">
                                                <img src="<?php echo get_theme_file_uri('/resources/images/teams/' . $team['logo']); ?>" alt="<?php echo esc_attr__($team['nameKey'], 'jerseyplug'); ?>" class="w-6 h-6 object-cover"/>
                                                <span class="text-sm text-gray-600 hover:text-primary"><?php esc_html_e($team['nameKey'], 'jerseyplug'); ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="relative h-full flex items-center" @mouseenter="activeDropdown = 'national'" @mouseleave="activeDropdown = null">
                    <button class="flex items-center gap-1 hover:opacity-80">
                        <?php esc_html_e('National', 'jerseyplug'); ?>
                        <svg class="w-[14px] h-[14px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div x-show="activeDropdown === 'national'" x-cloak x-transition class="absolute top-full left-0 w-64 bg-white text-gray-900 shadow-xl border-t-4 border-accent rounded-b-lg p-4">
                        <h3 class="font-bold text-xs uppercase mb-3 tracking-wider text-primary"><?php esc_html_e('National Teams', 'jerseyplug'); ?></h3>
                        <ul class="grid grid-cols-1 gap-2">
                            <?php foreach ($national_teams as $nation) : ?>
                                <li>
                                    <a href="#" class="flex items-center gap-3 hover:bg-gray-50 p-2 rounded transition-colors">
                                        <span class="text-lg"><?php echo $nation['flag']; ?></span>
                                        <span class="text-sm font-medium"><?php esc_html_e($nation['nameKey'], 'jerseyplug'); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li class="mt-2 pt-2 border-t border-gray-100">
                                <a href="#" class="flex items-center gap-1 text-xs font-bold hover:underline text-primary">
                                    <?php esc_html_e('View All', 'jerseyplug'); ?>
                                    <svg class="w-[12px] h-[12px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="relative h-full flex items-center" @mouseenter="activeDropdown = 'other'" @mouseleave="activeDropdown = null">
                    <button class="flex items-center gap-1 hover:opacity-80">
                        <?php esc_html_e('Other', 'jerseyplug'); ?>
                        <svg class="w-[14px] h-[14px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div x-show="activeDropdown === 'other'" x-cloak x-transition class="absolute top-full left-0 w-64 bg-white text-gray-900 shadow-xl border-t-4 border-accent rounded-b-lg p-4">
                        <h3 class="font-bold text-xs uppercase mb-3 tracking-wider text-primary"><?php esc_html_e('Other Leagues', 'jerseyplug'); ?></h3>
                        <ul class="space-y-2">
                            <?php foreach ($other_leagues as $league) : ?>
                                <li>
                                    <a href="#" class="flex items-center gap-3 hover:bg-gray-50 p-2 rounded transition-colors">
                                        <span class="text-lg w-6 text-center"><?php echo $league['logo']; ?></span>
                                        <span class="text-sm font-medium"><?php esc_html_e($league['nameKey'], 'jerseyplug'); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="flex items-center gap-4 lg:gap-6 flex-1 justify-end">
                <?php if (function_exists('pll_the_languages')) : 
                    $raw_languages = pll_the_languages([
                        'raw' => 1, 
                        'hide_if_empty' => 0, // Ép hiển thị cả khi chưa có bài viết
                        'hide_if_no_translation' => 0 // Ép hiển thị ngay cả khi trang hiện tại chưa được dịch
                    ]);
                    $languages_json = htmlspecialchars(json_encode(array_values($raw_languages)), ENT_QUOTES, 'UTF-8');    
                ?>
                    <div x-data="{ langSwitcherOpen: false, languages: <?php echo $languages_json; ?> }" @click.away="langSwitcherOpen = false" class="relative">
                        <button @click="langSwitcherOpen = !langSwitcherOpen" class="flex items-center gap-1 hover:opacity-80 transition-opacity">
                            <template x-for="lang in languages" :key="lang.slug">
                                <span x-show="lang.current_lang" x-text="lang.slug.toUpperCase()"></span>
                            </template>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="langSwitcherOpen" x-cloak x-transition class="absolute top-full right-0 mt-2 bg-white text-gray-900 rounded-md shadow-lg w-32 overflow-hidden">
                            <template x-for="lang in languages" :key="lang.slug">
                                <a :href="lang.url" :class="{'bg-gray-100': lang.current_lang}" class="flex items-center gap-2 p-2 hover:bg-gray-100 no-underline">
                                    <img :src="lang.flag" :alt="lang.name" class="w-5 h-auto">
                                    <span x-text="lang.name"></span>
                                </a>
                            </template>
                        </div>
                    </div>
                <?php endif; ?>

                <button @click="isSearchOpen = !isSearchOpen" class="hover:opacity-80 transition-transform active:scale-90" aria-label="<?php echo esc_attr__('Search', 'jerseyplug'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="hover:opacity-80 transition-transform active:scale-90">
                     <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </a>
                <?php if (function_exists('WC')) : ?>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="relative hover:opacity-80 transition-transform active:scale-90 group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    <?php $cart_count = WC()->cart->get_cart_contents_count(); ?>
                    <?php if ($cart_count > 0) : ?>
                        <span class="absolute -top-2 -right-2 text-[10px] font-bold h-5 w-5 flex items-center justify-center rounded-full border border-primary bg-secondary text-primary group-hover:scale-110 transition-transform">
                            <?php echo esc_html($cart_count); ?>
                        </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile Menu -->
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
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <img src="<?php echo esc_url(get_theme_file_uri('/resources/images/jerseyplug-logo.svg')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="w-auto" style="height: 2rem;">
                    </a>
                    <button @click="isMobileMenuOpen = false" class="text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                <div class="relative mb-4">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <input type="search" class="w-full bg-white/10 text-white text-sm rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:ring-1 focus:ring-secondary placeholder-gray-400" placeholder="<?php echo esc_attr_x( 'Search products...', 'placeholder', 'jerseyplug' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                        <button type="submit" class="absolute left-3 top-3 text-gray-400">
                             <svg class="w-[18px] h-[18px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                         <input type="hidden" name="post_type" value="product" />
                    </form>
                </div>

                <nav class="flex flex-col space-y-2">
                    <a href="#" class="text-white py-3 border-b border-gray-700/50 font-bold hover:opacity-80">
                        <?php esc_html_e('World Cup 2026', 'jerseyplug'); ?>
                    </a>

                    <div class="py-2 border-b border-gray-700/50">
                        <button @click="mobileAccordion.top5 = !mobileAccordion.top5" class="w-full flex justify-between items-center text-white py-2 font-bold">
                            <?php esc_html_e('Top 5 Leagues', 'jerseyplug'); ?>
                            <svg :class="{'rotate-180': mobileAccordion.top5}" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div x-show="mobileAccordion.top5" x-collapse x-cloak class="pt-2 pl-4">
                            <?php foreach ($top_5_leagues as $league) : ?>
                                <a href="#" class="block text-gray-300 py-1 text-sm hover:text-white"><?php esc_html_e($league['nameKey'], 'jerseyplug'); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="py-2 border-b border-gray-700/50">
                        <button @click="mobileAccordion.national = !mobileAccordion.national" class="w-full flex justify-between items-center text-white py-2 font-bold">
                            <?php esc_html_e('National Teams', 'jerseyplug'); ?>
                            <svg :class="{'rotate-180': mobileAccordion.national}" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div x-show="mobileAccordion.national" x-collapse x-cloak class="pt-2 pl-4">
                            <?php foreach ($national_teams as $nation) : ?>
                                <a href="#" class="flex items-center gap-2 text-gray-300 py-1 text-sm hover:text-white">
                                    <span><?php echo $nation['flag']; ?></span>
                                    <?php esc_html_e($nation['nameKey'], 'jerseyplug'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="py-2 border-b border-gray-700/50">
                        <button @click="mobileAccordion.other = !mobileAccordion.other" class="w-full flex justify-between items-center text-white py-2 font-bold">
                             <?php esc_html_e('Other Leagues', 'jerseyplug'); ?>
                            <svg :class="{'rotate-180': mobileAccordion.other}" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                         <div x-show="mobileAccordion.other" x-collapse x-cloak class="pt-2 pl-4">
                            <?php foreach ($other_leagues as $league) : ?>
                                <a href="#" class="flex items-center gap-2 text-gray-300 py-1 text-sm hover:text-white">
                                    <span class="text-lg w-6 text-center"><?php echo $league['logo']; ?></span>
                                    <?php esc_html_e($league['nameKey'], 'jerseyplug'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if (function_exists('pll_the_languages')) :
                        $mobile_raw_languages = pll_the_languages([
                            'raw' => 1,
                            'hide_if_empty' => 0,
                            'hide_if_no_translation' => 0
                        ]);
                        $mobile_languages_json = htmlspecialchars(json_encode(array_values($mobile_raw_languages)), ENT_QUOTES, 'UTF-8');
                    ?>
                        <div x-data="{ languages: <?php echo $mobile_languages_json; ?> }" class="flex items-center justify-between py-3 border-b border-gray-700/50">
                            <span class="text-white font-bold"><?php esc_html_e('Language', 'jerseyplug'); ?></span>
                            <div class="flex gap-2">
                                <template x-for="lang in languages" :key="lang.slug">
                                    <a :href="lang.url" :class="lang.current_lang ? 'bg-accent text-primary' : 'bg-white/10 text-white'" class="px-2 py-1 text-xs rounded no-underline">
                                        <span x-text="lang.slug.toUpperCase()"></span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>

        <!-- Backdrop -->
        <div x-show="isMobileMenuOpen" @click="isMobileMenuOpen = false" x-cloak
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="lg:hidden fixed inset-0 bg-black/50 z-40"
        ></div>

        <!-- Search Modal -->
        <div x-show="isSearchOpen" x-cloak
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/70 z-50 flex items-start justify-center pt-20"
            @click.self="isSearchOpen = false"
        >
            <div class="bg-white p-4 rounded-lg w-full max-w-2xl" @click.stop>
                 <form role="search" method="get" class="search-form relative" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="search" class="w-full border-gray-300 rounded-lg pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="<?php echo esc_attr_x( 'Search products...', 'placeholder', 'jerseyplug' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                    <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                         <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                    <input type="hidden" name="post_type" value="product" />
                </form>
            </div>
        </div>
    </header>

    <div id="content" class="site-content grow">
        <?php do_action('tailpress_content_start'); ?>
        <main>
