<?php
/**
 * AJAX Live Search for products.
 *
 * @package JerseyPlug
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'jerseyplug_ajax_live_search' ) ) {
	/**
	 * Handle AJAX request for live product search.
	 */
	function jerseyplug_ajax_live_search() {
		check_ajax_referer( 'jerseyplug_live_search_nonce', 'nonce' );

		$query = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

		if ( empty( $query ) ) {
			wp_send_json_success( [] );
		}

		$lang          = function_exists( 'pll_current_language' ) ? (string) pll_current_language( 'slug' ) : 'default';
		$transient_key = 'jp_search_' . md5( $query . '_' . $lang );
		
		$cached_response = get_transient( $transient_key );
		if ( false !== $cached_response ) {
			wp_send_json_success( $cached_response );
		}

		if ( ! function_exists( 'wc_get_products' ) ) {
			wp_send_json_error( 'WooCommerce is not active' );
		}

		// Configure the query to search products by title
		$args = [
			'status' => 'publish',
			'limit'  => 5,
			's'      => $query,
			'return' => 'objects',
		];

		$products = wc_get_products( $args );
		$results  = [];

		foreach ( $products as $product ) {
			if ( ! $product instanceof WC_Product ) {
				continue;
			}

			// Get thumbnail URL
			$image_id  = $product->get_image_id();
			$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : wc_placeholder_img_src();

			$results[] = [
				'id'    => $product->get_id(),
				'title' => $product->get_name(),
				'url'   => $product->get_permalink(),
				'image' => $image_url,
				'price' => $product->get_price_html(),
			];
		}

		// Also return the view all URL
		$view_all_url = add_query_arg( [
			's'         => rawurlencode( $query ),
			'post_type' => 'product',
		], wc_get_page_permalink( 'shop' ) );

		$response_data = [
			'items'        => $results,
			'view_all_url' => $view_all_url,
		];

		set_transient( $transient_key, $response_data, 12 * HOUR_IN_SECONDS );

		wp_send_json_success( $response_data );
	}
}

add_action( 'wp_ajax_jerseyplug_live_search', 'jerseyplug_ajax_live_search' );
add_action( 'wp_ajax_nopriv_jerseyplug_live_search', 'jerseyplug_ajax_live_search' );

if ( ! function_exists( 'jerseyplug_print_search_script' ) ) {
	/**
	 * Output script for ajax url.
	 */
	function jerseyplug_print_search_script() {
		$ajax_data = [
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'jerseyplug_live_search_nonce' ),
		];
		echo '<script>window.jerseyplug_ajax = ' . wp_json_encode( $ajax_data ) . ';</script>';
	}
}
add_action( 'wp_head', 'jerseyplug_print_search_script', 5 );
