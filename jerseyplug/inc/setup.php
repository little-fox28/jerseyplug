<?php
/**
 * Theme setup and global configuration.
 *
 * @package JerseyPlug
 */

function jerseyplug_register_theme_features(): void {
	TailPress\Framework\Theme::instance()
		->features(
			fn( $manager ) => $manager->add( TailPress\Framework\Features\MenuOptions::class )
		)
		->menus(
			fn( $manager ) => $manager
				->add( 'primary', __( 'Primary Menu', 'jerseyplug' ) )
				->add( 'header_utility', __( 'Header Utility Menu', 'jerseyplug' ) )
				->add( 'footer_shop', __( 'Footer Menu - Shop', 'jerseyplug' ) )
				->add( 'footer_support', __( 'Footer Menu - Support', 'jerseyplug' ) )
				->add( 'footer-legal', __( 'Footer Legal', 'jerseyplug' ) )
		)
		->themeSupport(
			fn( $manager ) => $manager->add(
				[
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
					],
					'woocommerce',
				]
			)
		);
}
jerseyplug_register_theme_features();

function jerseyplug_setup_textdomain(): void {
	load_theme_textdomain( 'jerseyplug', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'jerseyplug_setup_textdomain' );

function jerseyplug_register_nav_menus(): void {
	register_nav_menus(
		[
			'primary'        => __( 'Primary Menu', 'jerseyplug' ),
			'header_utility' => __( 'Header Utility Menu', 'jerseyplug' ),
			'footer_shop'    => __( 'Footer Menu - Shop', 'jerseyplug' ),
			'footer_support' => __( 'Footer Menu - Support', 'jerseyplug' ),
			'footer-legal'   => __( 'Footer Legal', 'jerseyplug' ),
		]
	);
}
add_action( 'after_setup_theme', 'jerseyplug_register_nav_menus' );

/**
 * Clean up wp_head() from WordPress default signatures.
 */
function jerseyplug_cleanup_head() {
	// Ẩn phiên bản WordPress hiện tại
	remove_action( 'wp_head', 'wp_generator' );

	// Loại bỏ link cho Windows Live Writer
	remove_action( 'wp_head', 'wlwmanifest_link' );

	// Loại bỏ link cho Really Simple Discovery (RSD)
	remove_action( 'wp_head', 'rsd_link' );

	// Loại bỏ các link REST API (nếu bạn không dùng block editor/giao tiếp app ngoài)
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

	// Loại bỏ link bài viết/trang ngắn (shortlinks)
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
}
add_action( 'init', 'jerseyplug_cleanup_head' );

/**
 * Loại bỏ thông báo lỗi đăng nhập để tránh lộ username/email nếu login sai.
 */
function jerseyplug_hide_login_errors() {
	return 'Something is wrong!';
}
add_filter( 'login_errors', 'jerseyplug_hide_login_errors' );

/**
 * Clean up body classes.
 */
function jerseyplug_clean_body_class( $classes ) {
	// Danh sách các class muốn giữ lại (whitelist)
	$whitelist = [ 'admin-bar', 'logged-in', 'cart-empty' ];

	// Lọc bỏ những class chứa chữ "wordpress", "page-id-", "template-"
	foreach ( $classes as $key => $value ) {
		if ( strpos( $value, 'wp-' ) !== false || strpos( $value, 'page-id-' ) !== false ) {
			unset( $classes[ $key ] );
		}
	}
	return $classes;
}
add_filter( 'body_class', 'jerseyplug_clean_body_class' );

// Tắt tự động kiểm tra cập nhật plugin/theme (chỉ làm thủ công khi cần)
remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
remove_action( 'admin_init', '_maybe_update_plugins' );
remove_action( 'admin_init', '_maybe_update_themes' );

function jerseyplug_disable_image_sizes( $sizes ) {
	unset( $sizes['medium_large'] ); // Thường không dùng đến
	unset( $sizes['1536x1536'] );
	unset( $sizes['2048x2048'] );
	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'jerseyplug_disable_image_sizes' );

// Loại bỏ link RSS feed
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );

/**
 * Register Polylang strings used in theme UI components.
 */
function jerseyplug_register_polylang_strings(): void {
	if ( ! function_exists( 'pll_register_string' ) ) {
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
	'Shop Now',
	'Hero Slider',
	'Hero image',
	'Go to slide %d',
	'Official Badge',
	'Featured Category',
	'Discover',
	'View Details',
	'Product Categories',
	'Top Leagues',
	'Trending Now',
	'New Arrivals',
	'Testimonials',
	'Why JerseyPlug',
	'Quality gear, responsive support, and a better shopping experience.',
	'Wishlist',
	'Fast Delivery',
	'Reliable shipping across South Africa.',
	'Secure Checkout',
	'Safe payments and trusted service.',
	'Easy Returns',
	'Flexible support when plans change.',
	'Friendly Support',
	'Quick help from a team that knows sport.',
	// Products page strings.
	'Filters',
	'Competitions',
	'Teams',
	'Version',
	'Size',
	'Price',
	'Sort',
	'Clear',
	'Reset',
	'Apply',
	'Featured',
	'Price: Low to High',
	'Price: High to Low',
	'Price: Low-High',
	'Price: High-Low',
	'Newest',
	'Showing',
	'results',
	'in',
	'No products found',
	'Try adjusting your filters or search criteria.',
	'Clear All Filters',
	'Load More',
	'Loading...',
	"You've viewed",
	'of',
	'products',
	'Show Results',
	'Quick Add',
	'Under R1000',
	'R1000 - R2000',
	'Above R2000',
	'Back to List',
	'Product Details',
	'You May Also Like',
	'Select Size',
	'Size Guide',
	'Personalization',
	'Reviews',
	'Add to Bag',
	'Custom Name',
	'Custom Number',
	'Patch',
	'Out of Stock',
	'In Stock',
	'Close',
	'Check chest/waist measurements in cm.',
	'Slim fit. Choose one size up if you prefer loose.',
	'Chest (cm)',
	'Waist (cm)',
	'Pro Tip',
	'If you are in between sizes, order the smaller size for a tighter fit or the larger size for a looser fit.',
	// Login/Register strings.
	"Don't have an account yet?",
	'Register now',
	'Already have an account?',
	'Magic Link',
	// 404 page strings
	'Page Not Found',
	'Sorry, the page you are looking for could not be found.',
	'Go to Shop',
	'Go Home',
	// Footer strings
	'Footer Description',
	// Order strings
	'Back to Orders',
	'Verified Buyer',
	// Cart custom strings
	'Free Delivery',
	"You've unlocked Free Delivery!",
	'Guaranteed Safe Checkout',
	'Total',
	'Product details not available.',
	'No content',
	];

	foreach ( $strings as $label ) {
		pll_register_string( 'jerseyplug-' . sanitize_title( $label ), $label, 'jerseyplug' );
	}
}
add_action( 'init', 'jerseyplug_register_polylang_strings' );
