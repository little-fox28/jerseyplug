<?php

if (is_file(__DIR__.'/vendor/autoload_packages.php')) {
    require_once __DIR__.'/vendor/autoload_packages.php';
}

function tailpress(): TailPress\Framework\Theme
{
    return TailPress\Framework\Theme::instance()
        ->assets(fn($manager) => $manager
            ->withCompiler(new TailPress\Framework\Assets\ViteCompiler, fn($compiler) => $compiler
                ->registerAsset('resources/css/app.css')
                ->registerAsset('resources/js/app.js')
                ->editorStyleFile('resources/css/editor-style.css')
            )
            ->enqueueAssets()
        )
        ->features(fn($manager) => $manager->add(TailPress\Framework\Features\MenuOptions::class))
        ->menus(fn($manager) => $manager
            ->add('primary', __( 'Primary Menu', 'jerseyplug'))
            ->add('header_utility', __( 'Header Utility Menu', 'jerseyplug'))
        )
        ->themeSupport(fn($manager) => $manager->add([
            'title-tag',
            'custom-logo',
            'post-thumbnails',
            'align-wide',
            'wp-block-styles',
            'responsive-embeds',
            'html5' => [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ]
        ]));
}

tailpress();

/**
 * Enqueue Alpine.js from CDN
 */
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', array(), null, array( 'strategy' => 'defer' ) );
} );

/**
 * Clean up wp_head() from WordPress default signatures
 */
function jerseyplug_cleanup_head() {
    // Ẩn phiên bản WordPress hiện tại
    remove_action('wp_head', 'wp_generator');

    // Loại bỏ link cho Windows Live Writer
    remove_action('wp_head', 'wlwmanifest_link');

    // Loại bỏ link cho Really Simple Discovery (RSD)
    remove_action('wp_head', 'rsd_link');

    // Loại bỏ các link REST API (nếu bạn không dùng block editor/giao tiếp app ngoài)
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

    // Loại bỏ link bài viết/trang ngắn (shortlinks)
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);

    // Loại bỏ CSS và JS cho Emojis (giúp giảm bớt script lạ trong head)
    // remove_action('wp_head', 'print_emoji_detection_script', 7);
    // remove_action('wp_print_styles', 'print_emoji_styles');
}
add_action('init', 'jerseyplug_cleanup_head');

/**
 * Loại bỏ thông báo lỗi đăng nhập để tránh lộ username/email nếu login sai
 */
function jerseyplug_hide_login_errors() {
    return 'Something is wrong!';
}
add_filter('login_errors', 'jerseyplug_hide_login_errors');

/**
 * Remove unnecessary scripts and styles
 */
function jerseyplug_remove_header_scripts() {
    // Loại bỏ Emoji script và CSS (Cực kỳ thừa thãi)
    // remove_action('wp_head', 'print_emoji_detection_script', 7);
    // remove_action('wp_print_styles', 'print_emoji_styles');
    // remove_action('admin_print_scripts', 'print_emoji_detection_script');
    // remove_action('admin_print_styles', 'print_emoji_styles');

    // Loại bỏ WP Embed (Nếu bạn không dán link từ web khác để nó tự render)
    wp_deregister_script('wp-embed');

    // Loại bỏ các CSS mặc định của Block Editor (Gutenberg) nếu bạn code tay toàn bộ
    // Cẩn thận: Nếu bạn dùng block editor cho bài viết thì không nên xóa dòng này
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style'); // Loại bỏ CSS của WooCommerce Blocks
}
add_action('wp_enqueue_scripts', 'jerseyplug_remove_header_scripts', 100);

// Loại bỏ link RSS feed
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);

// Loại bỏ link DNS Prefetch (Tiết kiệm 1 vài miligiây)
// add_filter('emoji_svg_url', '__return_false');

/**
 * Optimize WooCommerce Scripts
 */
function jerseyplug_optimize_woocommerce_scripts() {
    if (function_exists('is_woocommerce')) {
        // Nếu không phải trang thuộc WooCommerce thì dẹp bỏ script của nó
        if (!is_woocommerce() && !is_cart() && !is_checkout()) {
            wp_dequeue_script('wc-cart-fragments');
            wp_dequeue_script('woocommerce');
            wp_dequeue_script('wc-add-to-cart');
            
            // Loại bỏ các style của WooCommerce
            wp_dequeue_style('woocommerce-general');
            wp_dequeue_style('woocommerce-layout');
            wp_dequeue_style('woocommerce-smallscreen');
        }
    }
}
add_action('wp_enqueue_scripts', 'jerseyplug_optimize_woocommerce_scripts', 99);

/**
 * Clean up body classes
 */
function jerseyplug_clean_body_class($classes) {
    // Danh sách các class muốn giữ lại (whitelist)
    $whitelist = ['admin-bar', 'logged-in', 'cart-empty']; 
    
    // Lọc bỏ những class chứa chữ "wordpress", "page-id-", "template-"
    foreach ($classes as $key => $value) {
        if (strpos($value, 'wp-') !== false || strpos($value, 'page-id-') !== false) {
            unset($classes[$key]);
        }
    }
    return $classes;
}
add_filter('body_class', 'jerseyplug_clean_body_class');

// Tắt tự động kiểm tra cập nhật plugin/theme (chỉ làm thủ công khi cần)
remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
remove_action( 'admin_init', '_maybe_update_plugins' );
remove_action( 'admin_init', '_maybe_update_themes' );

function jerseyplug_disable_image_sizes($sizes) {
    unset($sizes['medium_large']); // Thường không dùng đến
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'jerseyplug_disable_image_sizes');

/**
 * Polylang-safe translation helper.
 */
function jerseyplug_pll(string $text): string {
    if (function_exists('pll__')) {
        return (string) pll__($text);
    }

    return (string) __($text, 'jerseyplug');
}

/**
 * Register Polylang strings used in theme UI components.
 */
function jerseyplug_register_polylang_strings(): void {
    if (!function_exists('pll_register_string')) {
        return;
    }

    $strings = [
        'Toggle Mobile Menu',
        'Close Mobile Menu',
        'Primary Navigation',
        'World Cup 2026',
        'Top 5 Leagues',
        'National',
        'National Teams',
        'Other',
        'Other Leagues',
        'View All',
        'Change Language',
        'Select Language',
        'Language',
        'Search',
        'Search...',
        'Search products...',
        'My Account',
        'Cart',
    ];

    foreach ($strings as $label) {
        pll_register_string('jerseyplug-' . sanitize_title($label), $label, 'jerseyplug');
    }
}
add_action('init', 'jerseyplug_register_polylang_strings');

/**
 * ACF option pages for header-managed fields (logo + announcement).
 */
function jerseyplug_register_acf_options_pages(): void {
    if (!function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page([
        'page_title' => 'Theme Settings',
        'menu_title' => 'Theme Settings',
        'menu_slug'  => 'jerseyplug-theme-settings',
        'capability' => 'manage_options',
        'redirect'   => false,
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Header Settings',
        'menu_title'  => 'Header Settings',
        'parent_slug' => 'jerseyplug-theme-settings',
    ]);
}
add_action('acf/init', 'jerseyplug_register_acf_options_pages');

/**
 * Shared cart markup for header and WooCommerce fragments.
 */
function jerseyplug_get_header_cart_markup(): string {
	$cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
	$cart_count = 0 ;

	if ( function_exists( 'WC' ) && WC()->cart ) {
		$cart_count = (int) WC()->cart->get_cart_contents_count();
	}

	ob_start();
	?>
	<a
		href="<?php echo esc_url( $cart_url ); ?>"
		class="header-cart-contents relative hover:opacity-80 transition-transform active:scale-90 group"
		aria-label="<?php echo esc_attr( jerseyplug_pll( 'Cart' ) ); ?>"
		data-cart-count="<?php echo esc_attr( (string) $cart_count ); ?>"
	>
		<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
		<?php if ( $cart_count > 0 ) : ?>
			<span class="header-cart-count absolute -top-2 -right-2 text-[10px] font-bold h-5 w-5 flex items-center justify-center rounded-full border border-primary bg-secondary text-primary group-hover:scale-110 transition-transform">
				<?php echo esc_html( (string) $cart_count ); ?>
			</span>
		<?php endif; ?>
		<span class="sr-only">
			<?php echo esc_html( sprintf( '%s: %d', jerseyplug_pll( 'Cart' ), $cart_count ) ); ?>
		</span>
	</a>
	<?php

	return (string) ob_get_clean();
}

/**
 * Refresh header cart quantity/price after WooCommerce AJAX add-to-cart.
 */
function jerseyplug_header_cart_fragments(array $fragments): array {
    $fragments['a.header-cart-contents'] = jerseyplug_get_header_cart_markup();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'jerseyplug_header_cart_fragments');

/**
 * Resolve category logo URL from ACF term field with fallback.
 */
function jerseyplug_get_category_logo_url(int $term_id): string {
    $logo = function_exists('get_field') ? get_field('category_logo', 'product_cat_' . $term_id) : null;

    if (is_array($logo) && !empty($logo['url'])) {
        return (string) $logo['url'];
    }

    if (is_numeric($logo)) {
        $image_url = wp_get_attachment_image_url((int) $logo, 'thumbnail');
        if ($image_url) {
            return $image_url;
        }
    }

    if (is_string($logo) && $logo !== '') {
        return $logo;
    }

    return function_exists('wc_placeholder_img_src')
        ? wc_placeholder_img_src('woocommerce_thumbnail')
        : get_theme_file_uri('/resources/images/placeholder-category.png');
}

/**
 * Build product category tree (children + grandchildren) for a top-level root slug.
 */
function jerseyplug_get_product_category_tree(string $root_slug): array {
    if (!taxonomy_exists('product_cat') || $root_slug === '') {
        return [];
    }

    $root_terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'slug'       => $root_slug,
        'number'     => 1,
    ]);

    if (is_wp_error($root_terms) || empty($root_terms)) {
        return [];
    }

    $root = $root_terms[0];

    $descendants = get_terms([
        'taxonomy'               => 'product_cat',
        'hide_empty'             => false,
        'child_of'               => (int) $root->term_id,
        'orderby'                => 'name',
        'order'                  => 'ASC',
        'update_term_meta_cache' => true,
    ]);

    if (is_wp_error($descendants)) {
        return [];
    }

    $by_parent = [];
    foreach ($descendants as $term) {
        $parent_id = (int) $term->parent;
        if (!isset($by_parent[$parent_id])) {
            $by_parent[$parent_id] = [];
        }
        $by_parent[$parent_id][] = $term;
    }

    $children = $by_parent[(int) $root->term_id] ?? [];
    $tree     = [];

    foreach ($children as $child) {
        $tree[] = [
            'term'         => $child,
            'children'     => $by_parent[(int) $child->term_id] ?? [],
            'logo_url'     => jerseyplug_get_category_logo_url((int) $child->term_id),
            'translated'   => jerseyplug_pll($child->name),
        ];
    }

    return $tree;
}

/**
 * Flush mega-menu transient cache when product categories change.
 */
function jerseyplug_flush_mega_menu_cache(): void {
    global $wpdb;

    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_jerseyplug_mega_menu_%'
            OR option_name LIKE '_transient_timeout_jerseyplug_mega_menu_%'"
    );
}
add_action('created_product_cat', 'jerseyplug_flush_mega_menu_cache');
add_action('edited_product_cat', 'jerseyplug_flush_mega_menu_cache');
add_action('delete_product_cat', 'jerseyplug_flush_mega_menu_cache');
