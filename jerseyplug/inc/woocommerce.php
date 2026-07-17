<?php

/**
 * WooCommerce-specific hooks and helpers.
 *
 * @package JerseyPlug
 */

/**
 * Optimize WooCommerce scripts.
 */
function jerseyplug_optimize_woocommerce_scripts()
{
	if (function_exists('is_woocommerce')) {
		// Nếu không phải trang thuộc WooCommerce thì dẹp bỏ script của nó
		if (! is_woocommerce() && ! is_cart() && ! is_checkout()) {
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
 * Shared cart markup for header and WooCommerce fragments.
 */
function jerseyplug_get_header_cart_markup(): string
{
	$cart_url   = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');
	$cart_count = 0;

	if (function_exists('WC') && WC()->cart) {
		$cart_count = (int) WC()->cart->get_cart_contents_count();
	}

	$cart_url   = (string) apply_filters('jerseyplug_header_cart_url', $cart_url);
	$cart_count = (int) apply_filters('jerseyplug_header_cart_count', $cart_count);

	ob_start();
?>
	<a
		href="<?php echo esc_url($cart_url); ?>"
		class="header-cart-contents relative hover:opacity-80 transition-transform active:scale-90 group"
		aria-label="<?php echo esc_attr(function_exists('jerseyplug_pll') ? jerseyplug_pll('Cart') : 'Cart'); ?>"
		data-cart-count="<?php echo esc_attr((string) $cart_count); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-100 hover:text-gray-300">
			<circle cx="8" cy="21" r="1" />
			<circle cx="19" cy="21" r="1" />
			<path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
		</svg>
		<span class="header-cart-count absolute top-[-5px] right-[-5px] bg-[#f2c86c] text-[#163300] text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full group-hover:scale-110 transition-transform">
			<?php echo esc_html((string) $cart_count); ?>
		</span>
		<span class="sr-only">
			<?php echo esc_html(sprintf('%s: %d', function_exists('jerseyplug_pll') ? jerseyplug_pll('Cart') : 'Cart', $cart_count)); ?>
		</span>
	</a>
<?php

	return (string) ob_get_clean();
}

/**
 * Refresh header cart quantity/price after WooCommerce AJAX add-to-cart.
 */
function jerseyplug_header_cart_fragments(array $fragments): array
{
	$fragments['a.header-cart-contents'] = jerseyplug_get_header_cart_markup();
	return $fragments;
}

if (wp_doing_ajax()) {
	add_filter('woocommerce_add_to_cart_fragments', 'jerseyplug_header_cart_fragments');
}

if (! function_exists('get_jerseyplug_mega_menu')) {
	/**
	 * Build and cache the WordPress custom mega menu data structure.
	 */
	function get_jerseyplug_mega_menu(): array
	{
		$menu_items = wp_get_nav_menu_items('Main Menu');
		if (!$menu_items) {
			$menu_items = wp_get_nav_menu_items('main-menu');
		}
		if (!$menu_items) {
			$locations = get_nav_menu_locations();
			if (isset($locations['primary'])) {
				$menu_items = wp_get_nav_menu_items($locations['primary']);
			}
		}

		if (empty($menu_items)) {
			return [];
		}

		$lang          = function_exists('pll_current_language') ? (string) pll_current_language('slug') : 'default';
		$cache_version = 5;
		$cache_key     = sprintf('jerseyplug_mega_menu_data_%d_%s', $cache_version, $lang);
		$cached        = get_transient($cache_key);

		if (is_array($cached)) {
			return $cached;
		}

		// Helper to get category logo data
		$get_logo_data = static function (int $term_id): array {
			$thumbnail_id = (int) get_term_meta($term_id, 'thumbnail_id', true);
			$logo_url     = '';

			if ($thumbnail_id > 0) {
				$thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'thumbnail');
				if ($thumbnail_url) {
					$logo_url = (string) $thumbnail_url;
				}
			}

			return [
				'thumbnail_id'       => $thumbnail_id,
				'logo_url'           => $logo_url,
				'external_logo_url'  => (string) get_term_meta($term_id, 'external_logo_url', true),
			];
		};

		// Group items by parent ID
		$items_by_parent = [];
		foreach ($menu_items as $item) {
			$parent_id = (int) $item->menu_item_parent;
			if (! isset($items_by_parent[$parent_id])) {
				$items_by_parent[$parent_id] = [];
			}
			$items_by_parent[$parent_id][] = $item;
		}

		$build_item_data = static function ($item) use ($get_logo_data) {
			$logo_url          = '';
			$external_logo_url = '';
			$thumbnail_id      = 0;
			$term_id           = 0;

			if ($item->object === 'product_cat') {
				$term_id = (int) $item->object_id;
				$logo    = $get_logo_data($term_id);
				$logo_url          = $logo['logo_url'];
				$external_logo_url = $logo['external_logo_url'];
				$thumbnail_id      = $logo['thumbnail_id'];
			}

			if ($logo_url === '' && $external_logo_url === '' && function_exists('get_field')) {
				$menu_logo = get_field('menu_logo', $item);
				if ($menu_logo) {
					$logo_url = is_array($menu_logo) ? ($menu_logo['url'] ?? '') : $menu_logo;
				}
			}

			$logo_src = $external_logo_url;
			if ($thumbnail_id > 0 && $logo_url !== '') {
				$logo_src = $logo_url;
			} elseif ($logo_src === '') {
				$logo_src = $logo_url;
			}

			return [
				'id'                => (int) $item->ID,
				'term_id'           => $term_id,
				'slug'              => sanitize_title($item->title),
				'name'              => $item->title,
				'link'              => $item->url,
				'thumbnail_id'      => $thumbnail_id,
				'logo_url'          => $logo_url,
				'external_logo_url' => $external_logo_url,
				'logo_src'          => $logo_src,
			];
		};

		$root_items = $items_by_parent[0] ?? [];
		$menu_data  = [];

		foreach ($root_items as $root) {
			$root_id    = (int) $root->ID;
			$root_data  = $build_item_data($root);
			$child_items = [];
			$root_children = $items_by_parent[$root_id] ?? [];

			foreach ($root_children as $child) {
				$child_id    = (int) $child->ID;
				$child_data  = $build_item_data($child);
				$grand_items = [];
				$child_children = $items_by_parent[$child_id] ?? [];

				foreach ($child_children as $grandchild) {
					$grand_id   = (int) $grandchild->ID;
					$grand_items[$grand_id] = $build_item_data($grandchild);
				}

				$child_data['children'] = $grand_items;
				$child_items[$child_id] = $child_data;
			}

			$root_data['children'] = $child_items;
			$menu_data[$root_id] = $root_data;
		}

		$menu_data = apply_filters('jerseyplug_mega_menu_data', $menu_data, $lang);

		$cache_ttl = (int) apply_filters('jerseyplug_mega_menu_cache_ttl', 12 * HOUR_IN_SECONDS, $lang);
		if ($cache_ttl <= 0) {
			$cache_ttl = 12 * HOUR_IN_SECONDS;
		}

		set_transient($cache_key, $menu_data, $cache_ttl);

		return $menu_data;
	}
}

/**
 * Clear the mega menu cache when a menu is updated.
 */
function jerseyplug_clear_mega_menu_cache()
{
	$cache_version = 5;
	delete_transient(sprintf('jerseyplug_mega_menu_data_%d_%s', $cache_version, 'default'));
	
	if (function_exists('pll_languages_list')) {
		$langs = pll_languages_list();
		foreach ($langs as $lang) {
			delete_transient(sprintf('jerseyplug_mega_menu_data_%d_%s', $cache_version, $lang));
		}
	}
}
add_action('wp_update_nav_menu', 'jerseyplug_clear_mega_menu_cache');
add_action('wp_update_nav_menu_item', 'jerseyplug_clear_mega_menu_cache');

/**
 * Capture custom personalization data (name, number) when adding to cart.
 */
function jerseyplug_add_personalization_to_cart(array $cart_item_data, int $product_id, int $variation_id): array
{
	if (! empty($_POST['custom_name'])) {
		$cart_item_data['custom_name'] = sanitize_text_field(wp_unslash($_POST['custom_name']));
	}
	if (! empty($_POST['custom_number'])) {
		$cart_item_data['custom_number'] = sanitize_text_field(wp_unslash($_POST['custom_number']));
	}
	return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'jerseyplug_add_personalization_to_cart', 10, 3);

/**
 * Display personalization data in WooCommerce cart/checkout items lists.
 */
function jerseyplug_display_personalization_in_cart(array $item_data, array $cart_item): array
{
	if (! empty($cart_item['custom_name'])) {
		$item_data[] = [
			'key'     => function_exists('jerseyplug_pll') ? jerseyplug_pll('Custom Name') : __('Custom Name', 'jerseyplug'),
			'display' => strtoupper($cart_item['custom_name']),
		];
	}
	if (! empty($cart_item['custom_number'])) {
		$item_data[] = [
			'key'     => function_exists('jerseyplug_pll') ? jerseyplug_pll('Custom Number') : __('Custom Number', 'jerseyplug'),
			'display' => $cart_item['custom_number'],
		];
	}
	return $item_data;
}
add_filter('woocommerce_get_item_data', 'jerseyplug_display_personalization_in_cart', 10, 2);

/**
 * Adjust cart item price to include personalization printing fee.
 */
function jerseyplug_calculate_personalization_fee($cart_object): void
{
	if (is_admin() && !defined('DOING_AJAX')) {
		return;
	}

	foreach ($cart_object->get_cart() as $cart_item) {
		if (!empty($cart_item['data']->jerseyplug_fee_added)) {
			continue;
		}

		if (!empty($cart_item['custom_name']) || !empty($cart_item['custom_number'])) {
			$product_id  = $cart_item['product_id']; // Use base product ID for global setting
			$print_price = (float) get_post_meta($product_id, '_print_price', true);

			if ($print_price <= 0) {
				$print_price = 0.0; // Fallback default
			}

			// Get the active price (variation price or simple product price)
			$active_price = (float) $cart_item['data']->get_price();

			// Add print price
			$new_price = $active_price + $print_price;
			$cart_item['data']->set_price($new_price);
			$cart_item['data']->jerseyplug_fee_added = true;
		}
	}
}
add_action('woocommerce_before_calculate_totals', 'jerseyplug_calculate_personalization_fee', 10, 1);

/**
 * Persist custom personalization meta data into order item details.
 */
function jerseyplug_save_personalization_to_order_items(WC_Order_Item_Product $item, string $cart_item_key, array $values, WC_Order $order): void
{
	if (! empty($values['custom_name'])) {
		$key = function_exists('jerseyplug_pll') ? jerseyplug_pll('Custom Name') : __('Custom Name', 'jerseyplug');
		$item->update_meta_data('_custom_name', strtoupper($values['custom_name']));
		$item->update_meta_data($key, strtoupper($values['custom_name']));
	}
	if (! empty($values['custom_number'])) {
		$key = function_exists('jerseyplug_pll') ? jerseyplug_pll('Custom Number') : __('Custom Number', 'jerseyplug');
		$item->update_meta_data('_custom_number', $values['custom_number']);
		$item->update_meta_data($key, $values['custom_number']);
	}
}
add_action('woocommerce_checkout_create_order_line_item', 'jerseyplug_save_personalization_to_order_items', 10, 4);

/**
 * Inject hidden fields into the cart form to capture Alpine.js state for Name & Number.
 * This is necessary because the visible UI inputs might be placed outside the form tag in the template.
 */
function jerseyplug_add_hidden_personalization_fields_to_cart_form(): void
{
?>
	<template x-if="true">
		<div>
			<input type="hidden" name="custom_name" :value="customName" />
			<input type="hidden" name="custom_number" :value="customNumber" />
		</div>
	</template>
<?php
}
add_action('woocommerce_before_add_to_cart_button', 'jerseyplug_add_hidden_personalization_fields_to_cart_form', 10);

/**
 * Customize WooCommerce Breadcrumb with Tailwind CSS.
 */
function jerseyplug_custom_woocommerce_breadcrumbs($defaults)
{
	$margin = is_product() ? 'mb-0 lg:mb-2' : 'mb-4 md:mb-6';
	$style = is_product() ? '<style>.woocommerce-breadcrumb ol li:last-child { display: none !important; }</style>' : '';
	$defaults['wrap_before'] = $style . '<nav class="woocommerce-breadcrumb ' . $margin . '" aria-label="Breadcrumb"><ol class="flex items-center flex-wrap text-[10px] md:text-xs font-black uppercase tracking-widest text-gray-400 pb-2 md:pb-4 gap-y-2"><li class="flex items-center">';
	$defaults['wrap_after']  = '</li></ol></nav>';
	$defaults['delimiter']   = '</li><li class="flex items-center"><svg class="w-3 h-3 md:w-3.5 md:h-3.5 mx-2 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
	$defaults['before']      = '<span class="hover:text-primary transition-colors duration-200">';
	$defaults['after']       = '</span>';
	return $defaults;
}
add_filter('woocommerce_breadcrumb_defaults', 'jerseyplug_custom_woocommerce_breadcrumbs');


/**
 * Move breadcrumb above the product image on single product pages.
 */
function jerseyplug_move_woocommerce_breadcrumb()
{
	if (is_product()) {
		remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
		add_action('woocommerce_before_single_product_summary', 'woocommerce_breadcrumb', 5);
	}
}
add_action('wp', 'jerseyplug_move_woocommerce_breadcrumb');

/**
 * Remove default WooCommerce Sale Flash on single product pages.
 * We are using a custom badge system inside the product gallery instead.
 */
function jerseyplug_remove_default_sale_flash()
{
	remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
}
add_action('wp', 'jerseyplug_remove_default_sale_flash');

/**
 * WooCommerce SPA-like Toast Notification System
 * Disable default notice output.
 */
function jerseyplug_disable_default_wc_notices(): void
{
	remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
	remove_action('woocommerce_before_single_product', 'woocommerce_output_all_notices', 10);
	remove_action('woocommerce_before_cart', 'woocommerce_output_all_notices', 10);
	remove_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10);
	remove_action('woocommerce_account_content', 'woocommerce_output_all_notices', 5);
	remove_action('woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10);
	remove_action('woocommerce_before_lost_password_form', 'woocommerce_output_all_notices', 10);
	remove_action('woocommerce_before_reset_password_form', 'woocommerce_output_all_notices', 10);
	remove_action('woocommerce_before_edit_account_form', 'woocommerce_output_all_notices', 10);
	remove_action('woocommerce_share', 'woocommerce_output_all_notices', 10);
}
add_action('init', 'jerseyplug_disable_default_wc_notices');

/**
 * Remove default WooCommerce UI elements from the shop loop.
 */
function jerseyplug_remove_default_woo_ui(): void
{
	remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
	remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
	remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
}
add_action('init', 'jerseyplug_remove_default_woo_ui');

/**
 * Inject WooCommerce notices into the footer as Alpine.js custom events.
 */
function jerseyplug_inject_wc_notices_to_footer(): void
{
	if (!function_exists('WC') || !WC()->session) {
		return;
	}

	$notices = WC()->session->get('wc_notices', []);

	if (!empty($notices)) {
		$scripts = [];
		foreach ($notices as $type => $messages) {
			foreach ($messages as $message) {
				$raw_message = is_array($message) ? ($message['notice'] ?? '') : $message;
				$clean_message = wp_strip_all_tags($raw_message);
				$clean_type = esc_js($type);
				$clean_message_js = esc_js($clean_message);
				$scripts[] = "window.dispatchEvent(new CustomEvent('notify', { detail: { message: '{$clean_message_js}', type: '{$clean_type}' } }));";
			}
		}

		if (!empty($scripts)) {
			echo "<script>document.addEventListener('DOMContentLoaded', function() {\n" . implode("\n", $scripts) . "\n});</script>";
		}

		// Clear session notices so they don't display again
		wc_clear_notices();
	}
}
add_action('wp_footer', 'jerseyplug_inject_wc_notices_to_footer', 10);

/**
 * Add custom print price field to WooCommerce product data panel (General tab)
 */
function jerseyplug_add_custom_print_price_field() {
	echo '<div class="options_group show_if_simple show_if_variable show_if_external show_if_grouped">';
	woocommerce_wp_text_input(array(
		'id' => '_print_price',
		'label' => function_exists('jerseyplug_pll') ? jerseyplug_pll('Custom Name & Number Fee (R)') : __('Custom Name & Number Fee (R)', 'jerseyplug'),
		'desc_tip' => true,
		'description' => __('Enter the additional fee for custom name & number printing (e.g. 10.00). Leave empty or 0 if free.', 'jerseyplug'),
		'type' => 'number',
		'custom_attributes' => array(
			'step' => '0.01',
			'min' => '0'
		)
	));
	echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'jerseyplug_add_custom_print_price_field');

/**
 * Save custom print price field
 */
function jerseyplug_save_custom_print_price_field($post_id) {
	if (isset($_POST['_print_price'])) {
		update_post_meta($post_id, '_print_price', wc_clean($_POST['_print_price']));
	}
}
add_action('woocommerce_process_product_meta', 'jerseyplug_save_custom_print_price_field');

/**
 * Remove default WooCommerce proceed to checkout button to use custom design in cart-totals.php
 */
remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);

/**
 * Disable WooCommerce redirecting to the single product page when there is only one search result.
 */
add_filter('woocommerce_redirect_single_search_result', '__return_false');