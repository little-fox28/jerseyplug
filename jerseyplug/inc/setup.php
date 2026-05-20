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
	];

	foreach ( $strings as $label ) {
		pll_register_string( 'jerseyplug-' . sanitize_title( $label ), $label, 'jerseyplug' );
	}
}
add_action( 'init', 'jerseyplug_register_polylang_strings' );
