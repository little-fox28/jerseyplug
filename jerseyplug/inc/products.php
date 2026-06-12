<?php
/**
 * Products page data helpers and AJAX handler.
 *
 * @package JerseyPlug
 */

if ( ! function_exists( 'jerseyplug_get_products_page_filter_options' ) ) {
	/**
	 * Build the filter configuration for the products page.
	 *
	 * Sources options from WooCommerce product categories and attributes
	 * where available, with apply_filters() hooks for customization.
	 *
	 * @return array<string, array> Filter groups keyed by slug.
	 */
	function jerseyplug_get_products_page_filter_options(): array {
		$options = [];

		// --- Competitions (product_cat children of a "competitions" parent, or fallback) ---
		$competitions = apply_filters(
			'jerseyplug_products_filter_competitions',
			[
				'World Cup 2026',
				'AFF Cup',
				'Premier League',
				'La Liga',
				'Serie A',
				'Bundesliga',
				'Champions League',
				'Saudi Pro League',
				'MLS',
			]
		);

		if ( taxonomy_exists( 'product_cat' ) ) {
			$competition_terms = get_terms( [
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'number'     => 20,
			] );

			if ( ! is_wp_error( $competition_terms ) && ! empty( $competition_terms ) ) {
				$term_names = wp_list_pluck( $competition_terms, 'name' );
				$competitions = apply_filters( 'jerseyplug_products_filter_competitions', $term_names );
			}
		}
		$options['competitions'] = $competitions;

		// --- Teams ---
		$teams = apply_filters(
			'jerseyplug_products_filter_teams',
			[
				'Argentina',
				'Vietnam',
				'France',
				'Brazil',
				'Man City',
				'Arsenal',
				'Real Madrid',
				'Barcelona',
				'Inter Miami',
				'Al Nassr',
			]
		);
		$options['teams'] = $teams;

		// --- Versions (pa_version attribute or fallback) ---
		$versions = [ 'Authentic (Player)', 'Replica (Fan)', 'Retro' ];
		if ( taxonomy_exists( 'pa_version' ) ) {
			$version_terms = get_terms( [
				'taxonomy'   => 'pa_version',
				'hide_empty' => true,
			] );
			if ( ! is_wp_error( $version_terms ) && ! empty( $version_terms ) ) {
				$versions = wp_list_pluck( $version_terms, 'name' );
			}
		}
		$options['versions'] = apply_filters( 'jerseyplug_products_filter_versions', $versions );

		// --- Sizes (pa_size attribute or fallback) ---
		$sizes = [ 'S', 'M', 'L', 'XL', '2XL', '3XL' ];
		if ( taxonomy_exists( 'pa_size' ) ) {
			$size_terms = get_terms( [
				'taxonomy'   => 'pa_size',
				'hide_empty' => true,
			] );
			if ( ! is_wp_error( $size_terms ) && ! empty( $size_terms ) ) {
				$sizes = wp_list_pluck( $size_terms, 'name' );
			}
		}
		$options['sizes'] = apply_filters( 'jerseyplug_products_filter_sizes', $sizes );

		// --- Price Ranges ---
		$options['priceRanges'] = apply_filters(
			'jerseyplug_products_filter_price_ranges',
			[
				[ 'id' => 'p1', 'label' => jerseyplug_pll( 'Under R1000' ),    'min' => 0,    'max' => 1000 ],
				[ 'id' => 'p2', 'label' => jerseyplug_pll( 'R1000 - R2000' ),  'min' => 1000, 'max' => 2000 ],
				[ 'id' => 'p3', 'label' => jerseyplug_pll( 'Above R2000' ),    'min' => 2000, 'max' => 99999 ],
			]
		);

		return $options;
	}
}

if ( ! function_exists( 'jerseyplug_query_filtered_products' ) ) {
	/**
	 * Query WooCommerce products with filter/sort/pagination support.
	 *
	 * @param array $filters {
	 *     @type string[] $competitions  Selected competition/category names.
	 *     @type string[] $teams         Selected team names.
	 *     @type string[] $versions      Selected version attribute values.
	 *     @type string[] $sizes         Selected size attribute values.
	 *     @type string   $price_range   Price range ID (p1, p2, p3).
	 *     @type string   $sort_by       Sort option string.
	 *     @type int      $page          Current page number.
	 *     @type int      $per_page      Products per page.
	 * }
	 * @return array{ products: array, total: int, max_pages: int }
	 */
	function jerseyplug_query_filtered_products( array $filters = [] ): array {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return [ 'products' => [], 'total' => 0, 'max_pages' => 0 ];
		}

		$defaults = [
			'competitions' => [],
			'teams'        => [],
			'versions'     => [],
			'sizes'        => [],
			'price_range'  => '',
			'sort_by'      => 'featured',
			'page'         => 1,
			'per_page'     => 12,
		];
		$filters = wp_parse_args( $filters, $defaults );

		$args = [
			'status'  => 'publish',
			'limit'   => (int) $filters['per_page'],
			'page'    => max( 1, (int) $filters['page'] ),
			'return'  => 'objects',
			'paginate' => true,
		];

		// --- Sorting ---
		switch ( $filters['sort_by'] ) {
			case 'price_low':
				$args['orderby'] = 'price';
				$args['order']   = 'ASC';
				break;
			case 'price_high':
				$args['orderby'] = 'price';
				$args['order']   = 'DESC';
				break;
			case 'newest':
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
			case 'featured':
			default:
				$args['featured'] = true;
				$args['orderby']  = 'date';
				$args['order']    = 'DESC';
				break;
		}

		// --- Category / taxonomy filters ---
		$tax_query = [];

		if ( ! empty( $filters['competitions'] ) && taxonomy_exists( 'product_cat' ) ) {
			$tax_query[] = [
				'taxonomy' => 'product_cat',
				'field'    => 'name',
				'terms'    => array_map( 'sanitize_text_field', $filters['competitions'] ),
			];
		}

		if ( ! empty( $filters['teams'] ) && taxonomy_exists( 'product_cat' ) ) {
			$tax_query[] = [
				'taxonomy' => 'product_cat',
				'field'    => 'name',
				'terms'    => array_map( 'sanitize_text_field', $filters['teams'] ),
			];
		}

		if ( ! empty( $filters['versions'] ) && taxonomy_exists( 'pa_version' ) ) {
			$tax_query[] = [
				'taxonomy' => 'pa_version',
				'field'    => 'name',
				'terms'    => array_map( 'sanitize_text_field', $filters['versions'] ),
			];
		}

		if ( ! empty( $filters['sizes'] ) && taxonomy_exists( 'pa_size' ) ) {
			$tax_query[] = [
				'taxonomy' => 'pa_size',
				'field'    => 'name',
				'terms'    => array_map( 'sanitize_text_field', $filters['sizes'] ),
			];
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		// --- Price range filter ---
		if ( ! empty( $filters['price_range'] ) ) {
			$price_ranges = jerseyplug_get_products_page_filter_options()['priceRanges'] ?? [];
			foreach ( $price_ranges as $range ) {
				if ( ( $range['id'] ?? '' ) === $filters['price_range'] ) {
					$args['min_price'] = (float) $range['min'];
					$args['max_price'] = (float) $range['max'];
					break;
				}
			}
		}

		$args = apply_filters( 'jerseyplug_products_page_query_args', $args, $filters );

		$results = wc_get_products( $args );

		$cards = [];
		if ( isset( $results->products ) ) {
			foreach ( $results->products as $product ) {
				if ( ! $product instanceof WC_Product ) {
					continue;
				}

				$image_id = $product->get_image_id();
				$image    = $image_id > 0 ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '';
				$gallery_ids = $product->get_gallery_image_ids();
				$image_back  = '';
				if ( ! empty( $gallery_ids ) ) {
					$image_back = wp_get_attachment_image_url( $gallery_ids[0], 'woocommerce_thumbnail' );
				}
				$terms    = get_the_terms( $product->get_id(), 'product_cat' );
				$category = '';
				if ( is_array( $terms ) && ! empty( $terms ) && $terms[0] instanceof WP_Term ) {
					$category = (string) $terms[0]->name;
				}

				$cards[] = [
					'id'           => (int) $product->get_id(),
					'slug'         => (string) $product->get_slug(),
					'name'         => (string) $product->get_name(),
					'url'          => (string) get_permalink( $product->get_id() ),
					'image'        => $image ?: ( function_exists( 'wc_placeholder_img_src' ) ? (string) wc_placeholder_img_src( 'woocommerce_thumbnail' ) : '' ),
					'image_back'   => $image_back ?: $image,
					'category'     => $category,
					'price'        => $product->get_price_html(),
					'rating_label' => jerseyplug_get_random_rating_and_reviews( (int) $product->get_id() )['rating'],
					'tag'          => $product->is_featured()
						? jerseyplug_pll( 'Trending Now' )
						: ( jerseyplug_is_new_product( $product ) ? jerseyplug_pll( 'New' ) : '' ),
				];
			}
		}

		return [
			'products'  => $cards,
			'total'     => isset( $results->total ) ? (int) $results->total : count( $cards ),
			'max_pages' => isset( $results->max_num_pages ) ? (int) $results->max_num_pages : 1,
		];
	}
}

if ( ! function_exists( 'jerseyplug_is_new_product' ) ) {
	/**
	 * Check if a product was published within the last 30 days.
	 */
	function jerseyplug_is_new_product( WC_Product $product ): bool {
		$created = $product->get_date_created();
		if ( ! $created ) {
			return false;
		}

		$days = (int) apply_filters( 'jerseyplug_new_product_days', 30 );
		$diff = time() - $created->getTimestamp();
		return $diff < ( $days * DAY_IN_SECONDS );
	}
}

/**
 * AJAX handler for filtered product queries.
 *
 * Accepts POST data with filter parameters, returns HTML fragment
 * of product cards ready to be inserted into the grid.
 */
function jerseyplug_ajax_filter_products(): void {
	check_ajax_referer( 'jerseyplug_products_nonce', 'nonce' );

	$filters = [
		'competitions' => isset( $_POST['competitions'] ) ? array_map( 'sanitize_text_field', (array) $_POST['competitions'] ) : [],
		'teams'        => isset( $_POST['teams'] ) ? array_map( 'sanitize_text_field', (array) $_POST['teams'] ) : [],
		'versions'     => isset( $_POST['versions'] ) ? array_map( 'sanitize_text_field', (array) $_POST['versions'] ) : [],
		'sizes'        => isset( $_POST['sizes'] ) ? array_map( 'sanitize_text_field', (array) $_POST['sizes'] ) : [],
		'price_range'  => isset( $_POST['price_range'] ) ? sanitize_text_field( wp_unslash( $_POST['price_range'] ) ) : '',
		'sort_by'      => isset( $_POST['sort_by'] ) ? sanitize_text_field( wp_unslash( $_POST['sort_by'] ) ) : 'featured',
		'page'         => isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
		'per_page'     => isset( $_POST['per_page'] ) ? min( absint( $_POST['per_page'] ), 48 ) : 12,
	];

	$result = jerseyplug_query_filtered_products( $filters );

	// Render product cards as HTML fragment.
	ob_start();
	foreach ( $result['products'] as $product ) {
		get_template_part( 'components/products/product-card', null, [ 'product' => $product ] );
	}
	$html = ob_get_clean();

	wp_send_json_success( [
		'html'      => $html,
		'total'     => $result['total'],
		'max_pages' => $result['max_pages'],
		'page'      => (int) $filters['page'],
	] );
}
add_action( 'wp_ajax_jerseyplug_filter_products', 'jerseyplug_ajax_filter_products' );
add_action( 'wp_ajax_nopriv_jerseyplug_filter_products', 'jerseyplug_ajax_filter_products' );
