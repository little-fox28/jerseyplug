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
