<?php

/**
 * Template router for page templates in /pages.
 *
 * @package JerseyPlug
 */

function jerseyplug_custom_router(string $template): string
{
	// Products / Shop page.
	if (function_exists('is_shop') && (is_shop() || is_product_taxonomy())) {
		$products_template = get_theme_file_path('/pages/products-page.php');
		if (is_string($products_template) && file_exists($products_template)) {
			return $products_template;
		}
	}

	// Homepage.
	$homepage_template = get_theme_file_path('/pages/home-page.php');
	if (is_front_page() && is_string($homepage_template) && file_exists($homepage_template)) {
		return $homepage_template;
	}

	// Single Product details.
	if (function_exists('is_product') && is_product()) {
		$detail_template = get_theme_file_path('/pages/product-detail.php');
		if (is_string($detail_template) && file_exists($detail_template)) {
			return $detail_template;
		}
	}

	return $template;
}
add_filter('template_include', 'jerseyplug_custom_router', 99);

/**
 * Automatically redirect the base URL /product/ to /shop/ to avoid 404 errors.
 */
function jerseyplug_redirect_product_base_to_shop(): void
{
	// Get the current URI path (e.g., /product/ or /product).
	$request_uri = $_SERVER['REQUEST_URI'];

	// Parse and remove query parameters like ?filter=... if present to avoid mismatch.
	$path = parse_url($request_uri, PHP_URL_PATH);

	// Trim leading and trailing slashes to normalize the path.
	$cleaned_path = trim($path, '/');

	// If the user accesses the exact base path 'product'.
	if ('product' === $cleaned_path) {
		// Perform a 301 redirect to the WooCommerce Shop page.
		wp_safe_redirect(wc_get_page_permalink('shop'), 301);
		exit;
	}
}
add_action('template_redirect', 'jerseyplug_redirect_product_base_to_shop');
