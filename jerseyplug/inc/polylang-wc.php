<?php

/**
 * Custom Polylang & WooCommerce Integration
 * Designed for "Shared Content, Translated UI" architecture.
 */

if (!defined('POLYLANG_VERSION')) {
	return; // Bail if Polylang is not active
}

/**
 * 1. Disable Translation for Products & Taxonomies
 * This makes products globally shared across all languages.
 */
add_filter('pll_get_post_types', function ($post_types, $is_settings) {
	if (!$is_settings && isset($post_types['product'])) {
		unset($post_types['product']);
	}
	return $post_types;
}, 10, 2);

add_filter('pll_get_taxonomies', function ($taxonomies, $is_settings) {
	if (!$is_settings) {
		unset($taxonomies['product_cat']);
		unset($taxonomies['product_tag']);
		foreach ($taxonomies as $tax => $value) {
			if (strpos($tax, 'pa_') === 0) {
				unset($taxonomies[$tax]);
			}
		}
	}
	return $taxonomies;
}, 10, 2);

/**
 * 2. Preserve Language URL Context for Untranslated Post Types (Products)
 */
// Hook removed. TRUE_LANG is defined in header.php

function jerseyplug_force_lang_prefix($url)
{
	if (function_exists('pll_default_language')) {
		$locale = get_locale(); // e.g. 'af' or 'af_ZA'
		$current_lang = strpos($locale, 'af') === 0 ? 'af' : 'en';
		$default_lang = pll_default_language('slug');

		if ($current_lang && $default_lang && $current_lang !== $default_lang) {
			$base_url = trailingslashit(get_option('home'));

			// Only replace if the base url matches and the language prefix is not already there
			if (strpos($url, $base_url) === 0 && strpos($url, $base_url . $current_lang . '/') === false) {
				$url = str_replace($base_url, $base_url . $current_lang . '/', $url);
			}
		}
	}

	return $url;
}

add_filter('post_type_link', function ($post_link, $post) {
	if ($post->post_type === 'product') {
		return jerseyplug_force_lang_prefix($post_link);
	}
	return $post_link;
}, PHP_INT_MAX, 2);

add_filter('the_permalink', function ($url) {
	if (is_singular('product') || get_post_type() === 'product' || strpos($url, '/product/') !== false) {
		return jerseyplug_force_lang_prefix($url);
	}
	return $url;
}, PHP_INT_MAX);

/**
 * 3. Translate WooCommerce Page IDs (Shop, Cart, Checkout, My Account)
 * Ensure that translated WooCommerce pages are recognized by WooCommerce's internal wc_get_page_id() function.
 */
function jerseyplug_translate_wc_page_id($id)
{
	if (empty($id) || !function_exists('pll_get_post') || !defined('JERSEYPLUG_TRUE_LANG')) {
		return $id;
	}
	$translated_id = pll_get_post($id, JERSEYPLUG_TRUE_LANG);
	return $translated_id ? $translated_id : $id;
}

add_filter('woocommerce_get_shop_page_id', 'jerseyplug_translate_wc_page_id');
add_filter('woocommerce_get_cart_page_id', 'jerseyplug_translate_wc_page_id');
add_filter('woocommerce_get_checkout_page_id', 'jerseyplug_translate_wc_page_id');
add_filter('woocommerce_get_myaccount_page_id', 'jerseyplug_translate_wc_page_id');
add_filter('woocommerce_get_terms_page_id', 'jerseyplug_translate_wc_page_id');

add_filter('term_link', function ($termlink, $term, $taxonomy) {
	if ($taxonomy === 'product_cat') {
		return jerseyplug_force_lang_prefix($termlink);
	}
	return $termlink;
}, 99, 3);

add_filter('post_type_archive_link', function ($link, $post_type) {
	if ($post_type === 'product') {
		return jerseyplug_force_lang_prefix($link);
	}
	return $link;
}, 99, 2);

// Rewrite rules for language-prefixed products
add_action('init', function () {
	if (!function_exists('pll_languages_list')) {
		return;
	}

	$langs = pll_languages_list();
	$permalinks = get_option('woocommerce_permalinks');
	$product_base = trim($permalinks['item'] ?? 'product', '/');
	$cat_base = trim($permalinks['category_base'] ?? 'product-category', '/');

	foreach ($langs as $lang) {
		if (function_exists('pll_default_language') && $lang === pll_default_language('slug')) {
			continue;
		}
		add_rewrite_rule('^' . $lang . '/' . $product_base . '/([^/]+)/?$', 'index.php?product=$matches[1]&lang=' . $lang, 'top');
		add_rewrite_rule('^' . $lang . '/' . $product_base . '/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?product=$matches[1]&paged=$matches[2]&lang=' . $lang, 'top');
		add_rewrite_rule('^' . $lang . '/' . $cat_base . '/(.+?)/?$', 'index.php?product_cat=$matches[1]&lang=' . $lang, 'top');
		add_rewrite_rule('^' . $lang . '/' . $cat_base . '/(.+?)/page/?([0-9]{1,})/?$', 'index.php?product_cat=$matches[1]&paged=$matches[2]&lang=' . $lang, 'top');
	}
}, 10);


/**
 * 3. Translate WooCommerce Core Page IDs
 * Ensures is_cart(), is_checkout(), etc. work on translated pages.
 */
function jerseyplug_translate_wc_page_id_option($value)
{
	if (function_exists('pll_get_post') && $value) {
		static $is_filtering = false;
		if ($is_filtering) {
			return $value;
		}
		$is_filtering = true;
		$lang = defined('JERSEYPLUG_TRUE_LANG') ? JERSEYPLUG_TRUE_LANG : '';
		$translated_id = $lang ? pll_get_post($value, $lang) : pll_get_post($value);
		$is_filtering = false;

		if ($translated_id) {
			return $translated_id;
		}
	}
	return $value;
}
$wc_pages = [
	'woocommerce_shop_page_id',
	'woocommerce_cart_page_id',
	'woocommerce_checkout_page_id',
	'woocommerce_myaccount_page_id',
	'woocommerce_terms_page_id',
];
foreach ($wc_pages as $option) {
	add_filter("option_{$option}", 'jerseyplug_translate_wc_page_id_option');
}

/**
 * 4. Force WooCommerce Shop Archive on Translated Shop Pages
 * When visiting a translated shop page, WordPress queries it as a normal 'page'.
 * We intercept the main query and convert it to a 'product' archive query.
 */
add_action('pre_get_posts', function ($q) {
	if (!is_admin() && $q->is_main_query() && $q->is_page() && function_exists('wc_get_page_id')) {
		$shop_id = (int) wc_get_page_id('shop');

		$page_id = (int) $q->get('page_id');
		$queried_id = (int) $q->get('p');
		$id_to_check = $page_id ? $page_id : $queried_id;

		if (!$id_to_check) {
			$page_path = $q->get('pagename');
			if ($page_path) {
				$page_obj = get_page_by_path($page_path);
				if ($page_obj) {
					$id_to_check = $page_obj->ID;
				}
			}
		}

		if ($shop_id > 0 && $id_to_check === $shop_id) {
			$q->set('post_type', 'product');
			$q->set('page_id', '');
			$q->set('pagename', '');
			$q->set('p', '');

			$q->is_singular = false;
			$q->is_post_type_archive = true;
			$q->is_archive = true;
			$q->is_page = true; // Keep is_page true so WooCommerce knows it's the shop page (like front-page logic)

			// Let WooCommerce know this is the shop page being queried
			$q->set('wc_query', 'product_query');

			// REMOVE Polylang's language filter that was added because the query started as 'page'
			$tax_query = $q->get('tax_query') ?: [];
			if (!empty($tax_query)) {
				foreach ($tax_query as $k => $v) {
					if (is_array($v) && isset($v['taxonomy']) && $v['taxonomy'] === 'language') {
						unset($tax_query[$k]);
					}
				}
				$q->set('tax_query', $tax_query);
			}
			// Also unset 'lang' if set directly
			$q->set('lang', '');
		}
	}
}, 9);


/**
 * 5. Route theme translations through Polylang String Translation automatically
 * This ensures esc_html_e() and __() use Polylang translations if available.
 */
add_filter('gettext', function ($translation, $text, $domain) {
	if ($domain === 'jerseyplug' && function_exists('pll__') && !is_admin()) {
		$pll_translation = pll__($text);
		if (!empty($pll_translation)) {
			return $pll_translation;
		}
	}
	return $translation;
}, 10, 3);

/**
 * 6. Route WooCommerce global Add to Cart text through Polylang
 */
add_filter('woocommerce_product_add_to_cart_text', function ($text, $product) {
	if (function_exists('pll__') && !is_admin()) {
		if ($product->is_purchasable() && $product->is_in_stock()) {
			$translated = pll__('Add to Cart');
			return !empty($translated) ? $translated : $text;
		} else {
			$translated = pll__('Read more');
			return !empty($translated) ? $translated : $text;
		}
	}
	return $text;
}, 10, 2);

/**
 * 7. Translate WooCommerce Breadcrumb 'Home' text
 */
add_filter('woocommerce_breadcrumb_defaults', function ($defaults) {
	if (function_exists('jerseyplug_pll')) {
		$defaults['home'] = jerseyplug_pll('Home');
	}
	return $defaults;
});