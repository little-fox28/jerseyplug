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
        ->menus(fn($manager) => $manager->add('primary', __( 'Primary Menu', 'tailpress')))
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