<?php
/**
 * WooCommerce-specific hooks and helpers.
 *
 * @package JerseyPlug
 */

/**
 * Optimize WooCommerce scripts.
 */
function jerseyplug_optimize_woocommerce_scripts() {
	if ( function_exists( 'is_woocommerce' ) ) {
		// Nếu không phải trang thuộc WooCommerce thì dẹp bỏ script của nó
		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'wc-add-to-cart' );

			// Loại bỏ các style của WooCommerce
			wp_dequeue_style( 'woocommerce-general' );
			wp_dequeue_style( 'woocommerce-layout' );
			wp_dequeue_style( 'woocommerce-smallscreen' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'jerseyplug_optimize_woocommerce_scripts', 99 );

/**
 * Shared cart markup for header and WooCommerce fragments.
 */
function jerseyplug_get_header_cart_markup(): string {
	$cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
	$cart_count = 0;

	if ( function_exists( 'WC' ) && WC()->cart ) {
		$cart_count = (int) WC()->cart->get_cart_contents_count();
	}

	$cart_url   = (string) apply_filters( 'jerseyplug_header_cart_url', $cart_url );
	$cart_count = (int) apply_filters( 'jerseyplug_header_cart_count', $cart_count );

	ob_start();
	?>
	<a
		href="<?php echo esc_url( $cart_url ); ?>"
		class="header-cart-contents relative hover:opacity-80 transition-transform active:scale-90 group"
		aria-label="<?php echo esc_attr( jerseyplug_pll( 'Cart' ) ); ?>"
		data-cart-count="<?php echo esc_attr( (string) $cart_count ); ?>"
		data-cart-drawer-trigger="header"
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
function jerseyplug_header_cart_fragments( array $fragments ): array {
	$fragments['a.header-cart-contents'] = jerseyplug_get_header_cart_markup();
	return $fragments;
}

if ( wp_doing_ajax() ) {
	add_filter( 'woocommerce_add_to_cart_fragments', 'jerseyplug_header_cart_fragments' );
}

if ( ! function_exists( 'get_jerseyplug_mega_menu' ) ) {
	/**
	 * Build and cache the WooCommerce mega menu data structure.
	 */
	function get_jerseyplug_mega_menu(): array {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return [];
		}

		$lang          = function_exists( 'pll_current_language' ) ? (string) pll_current_language( 'slug' ) : 'default';
		$cache_version = 4;
		$cache_key     = sprintf( 'jerseyplug_mega_menu_data_%d_%s', $cache_version, $lang );
		$stored_version = (int) get_option( 'jerseyplug_mega_menu_cache_version', 0 );
		if ( $stored_version !== $cache_version ) {
			update_option( 'jerseyplug_mega_menu_cache_version', $cache_version, false );
			if ( function_exists( 'jerseyplug_flush_mega_menu_cache' ) ) {
				jerseyplug_flush_mega_menu_cache();
			}
		}
		$cached    = get_transient( $cache_key );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$terms = get_terms(
			[
				'taxonomy'               => 'product_cat',
				'hide_empty'             => false,
				'orderby'                => 'menu_order',
				'order'                  => 'ASC',
				'update_term_meta_cache' => true,
			]
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return [];
		}

		$by_parent = [];
		foreach ( $terms as $term ) {
			$parent_id = (int) $term->parent;
			if ( ! isset( $by_parent[ $parent_id ] ) ) {
				$by_parent[ $parent_id ] = [];
			}
			$by_parent[ $parent_id ][] = $term;
		}

		$get_logo_data = static function ( int $term_id ): array {
			$thumbnail_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
			$logo_url     = '';

			if ( $thumbnail_id > 0 ) {
				$thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' );
				if ( $thumbnail_url ) {
					$logo_url = (string) $thumbnail_url;
				}
			}

			return [
				'thumbnail_id'       => $thumbnail_id,
				'logo_url'           => $logo_url,
				'external_logo_url'  => (string) get_term_meta( $term_id, 'external_logo_url', true ),
			];
		};

		$root_terms = $by_parent[0] ?? [];
		$menu_data  = [];

		foreach ( $root_terms as $root ) {
			$root_id   = (int) $root->term_id;
			$root_link = get_term_link( $root );
			$root_children = $by_parent[ (int) $root->term_id ] ?? [];
			$child_items   = [];

			foreach ( $root_children as $child ) {
				$child_id       = (int) $child->term_id;
				$child_link     = get_term_link( $child );
				$grand_children = $by_parent[ (int) $child->term_id ] ?? [];
				$grand_items    = [];

				foreach ( $grand_children as $grandchild ) {
					$grand_id   = (int) $grandchild->term_id;
					$grand_link = get_term_link( $grandchild );
					$grand_logo = $get_logo_data( $grand_id );
					$grand_items[ $grand_id ] = [
						'term_id'  => (int) $grandchild->term_id,
						'slug'     => (string) $grandchild->slug,
						'name'     => (string) $grandchild->name,
						'link'     => is_wp_error( $grand_link ) ? '' : (string) $grand_link,
						'thumbnail_id'      => (int) $grand_logo['thumbnail_id'],
						'logo_url'          => (string) $grand_logo['logo_url'],
						'external_logo_url' => (string) $grand_logo['external_logo_url'],
					];
				}

				$child_logo = $get_logo_data( $child_id );
				$child_items[ $child_id ] = [
					'term_id'  => (int) $child->term_id,
					'slug'     => (string) $child->slug,
					'name'     => (string) $child->name,
					'link'     => is_wp_error( $child_link ) ? '' : (string) $child_link,
					'thumbnail_id'      => (int) $child_logo['thumbnail_id'],
					'logo_url'          => (string) $child_logo['logo_url'],
					'external_logo_url' => (string) $child_logo['external_logo_url'],
					'children' => $grand_items,
				];
			}

			$menu_data[ $root_id ] = [
				'term_id'  => (int) $root->term_id,
				'slug'     => (string) $root->slug,
				'name'     => (string) $root->name,
				'link'     => is_wp_error( $root_link ) ? '' : (string) $root_link,
				'children' => $child_items,
			];
		}

		$menu_data = apply_filters( 'jerseyplug_mega_menu_data', $menu_data, $lang );

		$cache_ttl = (int) apply_filters( 'jerseyplug_mega_menu_cache_ttl', 12 * HOUR_IN_SECONDS, $lang );
		if ( $cache_ttl <= 0 ) {
			$cache_ttl = 12 * HOUR_IN_SECONDS;
		}

		set_transient( $cache_key, $menu_data, $cache_ttl );

		return $menu_data;
	}
}

/**
 * Capture custom personalization data (name, number, patches) when adding to cart.
 */
function jerseyplug_add_personalization_to_cart( array $cart_item_data, int $product_id, int $variation_id ): array {
	if ( ! empty( $_POST['custom_name'] ) ) {
		$cart_item_data['custom_name'] = sanitize_text_field( wp_unslash( $_POST['custom_name'] ) );
	}
	if ( ! empty( $_POST['custom_number'] ) ) {
		$cart_item_data['custom_number'] = sanitize_text_field( wp_unslash( $_POST['custom_number'] ) );
	}
	if ( ! empty( $_POST['selected_patch'] ) ) {
		$patch_json = sanitize_text_field( wp_unslash( $_POST['selected_patch'] ) );
		$patch = json_decode( html_entity_decode( $patch_json ), true );
		if ( is_array( $patch ) ) {
			$cart_item_data['selected_patch'] = [
				'name'  => sanitize_text_field( $patch['name'] ?? $patch['label'] ?? '' ),
				'price' => (float) ( $patch['price'] ?? 0 ),
			];
		}
	}
	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'jerseyplug_add_personalization_to_cart', 10, 3 );

/**
 * Display personalization data in WooCommerce cart/checkout items lists.
 */
function jerseyplug_display_personalization_in_cart( array $item_data, array $cart_item ): array {
	if ( ! empty( $cart_item['custom_name'] ) ) {
		$item_data[] = [
			'key'     => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Custom Name' ) : __( 'Custom Name', 'jerseyplug' ),
			'display' => strtoupper( $cart_item['custom_name'] ),
		];
	}
	if ( ! empty( $cart_item['custom_number'] ) ) {
		$item_data[] = [
			'key'     => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Custom Number' ) : __( 'Custom Number', 'jerseyplug' ),
			'display' => $cart_item['custom_number'],
		];
	}
	if ( ! empty( $cart_item['selected_patch'] ) && ! empty( $cart_item['selected_patch']['name'] ) ) {
		$patch_label = function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Patch' ) : __( 'Patch', 'jerseyplug' );
		$item_data[] = [
			'key'     => $patch_label,
			'display' => sprintf( '%s (+%s)', $cart_item['selected_patch']['name'], wc_price( $cart_item['selected_patch']['price'] ) ),
		];
	}
	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'jerseyplug_display_personalization_in_cart', 10, 2 );

/**
 * Adjust product price dynamically based on personalization options.
 */
function jerseyplug_calculate_custom_cart_item_prices( WC_Cart $cart ): void {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}
	foreach ( $cart->get_cart() as $cart_item ) {
		$extra_fee = 0.0;
		$product = $cart_item['data'];

		$has_print = ! empty( $cart_item['custom_name'] ) || ! empty( $cart_item['custom_number'] );
		if ( $has_print ) {
			$print_price = (float) get_post_meta( $product->get_id(), '_print_price', true );
			if ( $print_price > 0 ) {
				$extra_fee += $print_price;
			}
		}

		if ( ! empty( $cart_item['selected_patch'] ) && isset( $cart_item['selected_patch']['price'] ) ) {
			$extra_fee += (float) $cart_item['selected_patch']['price'];
		}

		if ( $extra_fee > 0 ) {
			$product->set_price( (float) $product->get_price() + $extra_fee );
		}
	}
}
add_action( 'woocommerce_before_calculate_totals', 'jerseyplug_calculate_custom_cart_item_prices', 10, 1 );

/**
 * Persist custom personalization meta data into order item details.
 */
function jerseyplug_save_personalization_to_order_items( WC_Order_Item_Product $item, string $cart_item_key, array $values, WC_Order $order ): void {
	if ( ! empty( $values['custom_name'] ) ) {
		$key = function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Custom Name' ) : __( 'Custom Name', 'jerseyplug' );
		$item->update_meta_data( '_custom_name', strtoupper( $values['custom_name'] ) );
		$item->update_meta_data( $key, strtoupper( $values['custom_name'] ) );
	}
	if ( ! empty( $values['custom_number'] ) ) {
		$key = function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Custom Number' ) : __( 'Custom Number', 'jerseyplug' );
		$item->update_meta_data( '_custom_number', $values['custom_number'] );
		$item->update_meta_data( $key, $values['custom_number'] );
	}
	if ( ! empty( $values['selected_patch'] ) && ! empty( $values['selected_patch']['name'] ) ) {
		$key = function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Patch' ) : __( 'Patch', 'jerseyplug' );
		$item->update_meta_data( '_selected_patch_name', $values['selected_patch']['name'] );
		$item->update_meta_data( '_selected_patch_price', $values['selected_patch']['price'] );
		$item->update_meta_data( $key, sprintf( '%s (+%s)', $values['selected_patch']['name'], wc_price( $values['selected_patch']['price'] ) ) );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'jerseyplug_save_personalization_to_order_items', 10, 4 );

