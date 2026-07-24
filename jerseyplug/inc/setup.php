<?php

/**
 * Theme setup and global configuration.
 *
 * @package JerseyPlug
 */

function jerseyplug_register_theme_features(): void
{
	TailPress\Framework\Theme::instance()
		->features(
			fn($manager) => $manager->add(TailPress\Framework\Features\MenuOptions::class)
		)
		->menus(
			fn($manager) => $manager
				->add('primary', __('Primary Menu', 'jerseyplug'))
				->add('header_utility', __('Header Utility Menu', 'jerseyplug'))
				->add('footer_shop', __('Footer Menu - Shop', 'jerseyplug'))
				->add('footer_support', __('Footer Menu - Support', 'jerseyplug'))
				->add('footer-legal', __('Footer Legal', 'jerseyplug'))
		)
		->themeSupport(
			fn($manager) => $manager->add(
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

function jerseyplug_setup_textdomain(): void
{
	load_theme_textdomain('jerseyplug', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'jerseyplug_setup_textdomain');

function jerseyplug_register_nav_menus(): void
{
	register_nav_menus(
		[
			'primary'        => __('Primary Menu', 'jerseyplug'),
			'header_utility' => __('Header Utility Menu', 'jerseyplug'),
			'footer_shop'    => __('Footer Menu - Shop', 'jerseyplug'),
			'footer_support' => __('Footer Menu - Support', 'jerseyplug'),
			'footer-legal'   => __('Footer Legal', 'jerseyplug'),
		]
	);
}
add_action('after_setup_theme', 'jerseyplug_register_nav_menus');

/**
 * Clean up wp_head() from WordPress default signatures.
 */
function jerseyplug_cleanup_head()
{
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
}
add_action('init', 'jerseyplug_cleanup_head');

/**
 * Loại bỏ thông báo lỗi đăng nhập để tránh lộ username/email nếu login sai.
 */
function jerseyplug_hide_login_errors()
{
	return 'Something is wrong!';
}
add_filter('login_errors', 'jerseyplug_hide_login_errors');

/**
 * Clean up body classes.
 */
function jerseyplug_clean_body_class($classes)
{
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
remove_action('wp_maybe_auto_update', 'wp_maybe_auto_update');
remove_action('admin_init', '_maybe_update_plugins');
remove_action('admin_init', '_maybe_update_themes');

function jerseyplug_disable_image_sizes($sizes)
{
	unset($sizes['medium_large']); // Thường không dùng đến
	unset($sizes['1536x1536']);
	unset($sizes['2048x2048']);
	return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'jerseyplug_disable_image_sizes');

// Loại bỏ link RSS feed
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);

/**
 * Register Polylang strings used in theme UI components.
 */
function jerseyplug_register_polylang_strings(): void
{
	if (! function_exists('pll_register_string')) {
		return;
	}

	$strings = [
		'#',
		'(estimated for %s)',
		'A link to set a new password will be sent to your email address.',
		'A password reset email has been sent to the email address on file for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.',
		'Above R2000',
		'Absolutely love my new jersey! The material feels premium.',
		'Account details',
		'Add',
		'Add to Bag',
		'Add to Cart',
		'Addresses',
		'Already have an account?',
		'Apply',
		'Available on backorder',
		'Back to Cart',
		'Back to List',
		'Back to Login',
		'Back to Orders',
		'Best place to buy football jerseys. Will be ordering again.',
		'Browse our latest collection and gear up for the season.',
		'Cart',
		'Change Language',
		'Change address',
		'Check chest/waist measurements in cm.',
		'Check your order status & history.',
		'Checkout',
		'Chest (cm)',
		'Clear',
		'Clear All Filters',
		'Close',
		'Competitions',
		'Confirm new password',
		'Contact',
		'Continue Shopping',
		'Coupon Code',
		'Current password (leave blank to leave unchanged)',
		'Customer Info',
		'Dashboard',
		'Date',
		'Decrease quantity',
		'Details',
		'Discover',
		'Display name',
		'Don',
		'Easy Returns',
		'Easy to order and the support team was helpful.',
		'Edit',
		'Email',
		'Email Sent!',
		'Email address',
		'Enter a different address',
		'Enter code',
		'Fast Delivery',
		'Featured',
		'Featured Category',
		'Filters',
		'First name',
		'Flat rate:',
		'Flexible support when plans change.',
		'Footer Description',
		'Free Delivery',
		'Friendly Support',
		'From your account dashboard you can view your <a href="%1$s" class="text-primary hover:underline font-bold">recent orders</a>, manage your <a href="%2$s" class="text-primary hover:underline font-bold">shipping and billing addresses</a>, and <a href="%3$s" class="text-primary hover:underline font-bold">edit your password and account details</a>.',
		'Go Home',
		'Go to shop',
		'Go to slide %d',
		'Great quality and fast delivery.',
		'Guaranteed Safe Checkout',
		'Hello, %s!',
		'Hero image',
		'Home',
		'If you are in between sizes, order the smaller size for a tighter fit or the larger size for a looser fit.',
		'Image',
		'In Stock',
		'Increase quantity',
		'JerseyPlug home',
		'Language',
		'Last name',
		'Load More',
		'Loading...',
		'Log in',
		'Login',
		'Log out',
		'Logout',
		'Looks like you haven',
		'Looks like you haven\'t added anything to your cart yet. Browse our collections and find something you love!',
		'Lost password',
		'Lost your password?',
		'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.',
		'Manage your delivery locations.',
		'My Account',
		'My account',
		'N/A',
		'Name',
		'New',
		'New Arrivals',
		'New password (leave blank to leave unchanged)',
		'Newest',
		'Next',
		'Next image',
		'No content',
		'No orders has been made yet.',
		'No products found',
		'Number',
		'Official Badge',
		'On Sale',
		'Order #%s',
		'Order Confirmed!',
		'Order Details',
		'Order Failed',
		'Order History',
		'Order Note',
		'Order Number',
		'Order Summary',
		'Order received',
		'Order updates',
		'Orders',
		'Order',
		'Out of Stock',
		'Page Not Found',
		'Password',
		'Password change',
		'Patch',
		'Patches',
		'Pay',
		'Payment Method',
		'Perfect sizing and exactly as described on the website.',
		'Personalization',
		'Personalization Details',
		'Please select a size',
		'Previous',
		'Previous image',
		'Price',
		'Price: High to Low',
		'Price: Low to High',
		'Pro Tip',
		'Proceed to Checkout',
		'Product',
		'Product Categories',
		'Product Details',
		'Product details not available.',
		'Qty',
		'Quality gear, responsive support, and a better shopping experience.',
		'Quantity',
		'Quick Add',
		'Quick help from a team that knows sport.',
		'R1000 - R2000',
		'Read more',
		'Register',
		'Register now',
		'Reliable shipping across South Africa.',
		'Remember me',
		'Remove',
		'Remove %s from cart',
		'Reset',
		'Reset password',
		'Return to Login',
		'Return to shop',
		'Reviews',
		'Safe payments and trusted service.',
		'Sale',
		'Save address',
		'Save changes',
		'Search',
		'Search products...',
		'Search...',
		'Secure Checkout',
		'Select Language',
		'Select Size',
		'Shipment',
		'Shipping',
		'Shipping to %s.',
		'Shipping was super fast and the packaging was great.',
		'Shop',
		'Shop Now',
		'Shopping Cart',
		'Show Results',
		'Showing',
		'Size',
		'Size Guide',
		'Slim fit. Choose one size up if you prefer loose.',
		'Sorry, the page you are looking for could not be found.',
		'Sort',
		'Subtotal',
		'Support',
		'Teams',
		'Testimonials',
		'Thank you. Your order has been received and is now being processed.',
		'The custom name printing is flawless. Highly recommended!',
		'The fit and print detail are excellent.',
		'The following addresses will be used on the checkout page by default.',
		'This product is currently out of stock and unavailable.',
		'This will be how your name will be displayed in the account section and in reviews',
		'Toggle password visibility',
		'Top Leagues',
		'Total',
		'Trending Now',
		'Try adjusting your filters or search criteria.',
		'Under R1000',
		'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.',
		'Update cart',
		'Update password & profile info.',
		'Username',
		'Username or email',
		'Username or email address',
		'Verified Buyer',
		'Version',
		'View All',
		'View All Sale Products',
		'View Details',
		'View Order Details',
		'View image',
		'Waist (cm)',
		'We Accept',
		'Why JerseyPlug',
		'Wishlist',
		'You',
		'You May Also Like',
		'You have not set up this type of address yet.',
		'You must be logged in to checkout.',
		'You\'ve unlocked Free Delivery!',
		'Your cart is empty',
		'Your order',
		'[Remove]',
		'in',
		'l jS of F Y, h:ia',
		'of',
		'products',
		'results',
		'© %d JerseyPlug. All rights reserved.',
	];

	foreach ($strings as $label) {
		pll_register_string('jerseyplug-' . sanitize_title($label), $label, 'jerseyplug');
	}
}
add_action('init', 'jerseyplug_register_polylang_strings');
