<?php
/**
 * Products page data helpers and server-side query modifiers.
 *
 * Uses pre_get_posts to modify the standard WooCommerce main loop
 * based on URL query parameters for filtering and sorting.
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
		$cache_key = 'jerseyplug_products_filter_options';
		$cached    = get_transient( $cache_key );

		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

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

		set_transient( $cache_key, $options, DAY_IN_SECONDS );

		return $options;
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
 * Modify the main WooCommerce shop query based on URL filter parameters.
 *
 * Reads $_GET params (filter_competition, filter_team, filter_version,
 * filter_size, filter_price, sort) and applies tax_query / orderby
 * modifications to the main product loop.
 *
 * @param WP_Query $query The main query object.
 */
function jerseyplug_modify_shop_query( WP_Query $query ): void {
	// Only modify the main query on the front-end shop/taxonomy archives.
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$is_shop = function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() );
	if ( ! $is_shop ) {
		return;
	}

	// --- Posts per page ---
	$per_page = (int) apply_filters( 'jerseyplug_products_per_page', 12 );
	$query->set( 'posts_per_page', $per_page );

	// --- Taxonomy filters ---
	$tax_query = $query->get( 'tax_query', [] );
	if ( ! is_array( $tax_query ) ) {
		$tax_query = [];
	}

	// Competition filter (product_cat).
	if ( ! empty( $_GET['filter_competition'] ) && taxonomy_exists( 'product_cat' ) ) {
		$comps = array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['filter_competition'] ) );
		$tax_query[] = [
			'taxonomy' => 'product_cat',
			'field'    => 'name',
			'terms'    => $comps,
		];
	}

	// Team filter (product_cat).
	if ( ! empty( $_GET['filter_team'] ) && taxonomy_exists( 'product_cat' ) ) {
		$teams = array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['filter_team'] ) );
		$tax_query[] = [
			'taxonomy' => 'product_cat',
			'field'    => 'name',
			'terms'    => $teams,
		];
	}

	// Version filter (pa_version attribute).
	if ( ! empty( $_GET['filter_version'] ) && taxonomy_exists( 'pa_version' ) ) {
		$versions = array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['filter_version'] ) );
		$tax_query[] = [
			'taxonomy' => 'pa_version',
			'field'    => 'name',
			'terms'    => $versions,
		];
	}

	// Size filter (pa_size attribute).
	if ( ! empty( $_GET['filter_size'] ) && taxonomy_exists( 'pa_size' ) ) {
		$sizes = array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['filter_size'] ) );
		$tax_query[] = [
			'taxonomy' => 'pa_size',
			'field'    => 'name',
			'terms'    => $sizes,
		];
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	if ( ! empty( $tax_query ) ) {
		$query->set( 'tax_query', $tax_query );
	}

	// --- Sorting ---
	$sort = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : 'featured';

	switch ( $sort ) {
		case 'price_low':
			$query->set( 'meta_key', '_price' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
			break;
		case 'price_high':
			$query->set( 'meta_key', '_price' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'DESC' );
			break;
		case 'newest':
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
			break;
		case 'featured':
		default:
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
			break;
	}

	// --- Price range filter (handled via posts_clauses) ---
	if ( ! empty( $_GET['filter_price'] ) ) {
		$price_id = sanitize_text_field( wp_unslash( $_GET['filter_price'] ) );
		$ranges   = jerseyplug_get_products_page_filter_options()['priceRanges'] ?? [];

		foreach ( $ranges as $range ) {
			if ( ( $range['id'] ?? '' ) === $price_id ) {
				// Store for the posts_clauses filter.
				$query->set( 'jerseyplug_min_price', (float) $range['min'] );
				$query->set( 'jerseyplug_max_price', (float) $range['max'] );
				break;
			}
		}
	}
}
add_action( 'pre_get_posts', 'jerseyplug_modify_shop_query' );

/**
 * Filter SQL clauses to add price range filtering via _price meta.
 *
 * Reads custom query vars set by jerseyplug_modify_shop_query().
 *
 * @param array    $clauses SQL clauses array.
 * @param WP_Query $query   The query object.
 * @return array Modified clauses.
 */
function jerseyplug_filter_shop_price_clauses( array $clauses, WP_Query $query ): array {
	if ( is_admin() || ! $query->is_main_query() ) {
		return $clauses;
	}

	$min_price = $query->get( 'jerseyplug_min_price' );
	$max_price = $query->get( 'jerseyplug_max_price' );

	if ( $min_price === '' && $max_price === '' ) {
		return $clauses;
	}

	global $wpdb;

	$min = (float) $min_price;
	$max = (float) $max_price;

	// Use WooCommerce's highly optimized and indexed wc_product_meta_lookup table
	$lookup_table = $wpdb->prefix . 'wc_product_meta_lookup';
	$price_join   = "INNER JOIN {$lookup_table} AS jp_price ON ({$wpdb->posts}.ID = jp_price.product_id)";

	if ( strpos( $clauses['join'], 'jp_price' ) === false ) {
		$clauses['join'] .= " {$price_join}";
	}

	// Match any product that has at least one variation within the selected price range
	$clauses['where'] .= $wpdb->prepare(
		' AND jp_price.min_price <= %f AND jp_price.max_price >= %f',
		$max,
		$min
	);

	return $clauses;
}
add_filter( 'posts_clauses', 'jerseyplug_filter_shop_price_clauses', 10, 2 );
