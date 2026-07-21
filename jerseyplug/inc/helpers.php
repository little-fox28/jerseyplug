<?php
/**
 * Helper utilities.
 *
 * @package JerseyPlug
 */

if ( ! function_exists( 'jerseyplug_pll' ) ) {
	/**
	 * Polylang-safe translation helper.
	 */
	function jerseyplug_pll( string $text ): string {
		if ( defined( 'JERSEYPLUG_TRUE_LANG' ) && function_exists( 'pll_translate_string' ) ) {
			return (string) pll_translate_string( $text, JERSEYPLUG_TRUE_LANG );
		}

		if ( function_exists( 'pll__' ) ) {
			return (string) pll__( $text );
		}

		return (string) __( $text, 'jerseyplug' );
	}
}

if ( ! function_exists( 'get_jerseyplug_setting' ) ) {
	/**
	 * Safely fetch a theme setting.
	 */
	function get_jerseyplug_setting( string $key, $default = '' ) {
		$options = get_option( 'jerseyplug_global_options', [] );
		if ( ! is_array( $options ) ) {
			return $default;
		}

		if ( array_key_exists( $key, $options ) ) {
			return $options[ $key ];
		}

		return $default;
	}
}

if ( ! function_exists( 'jerseyplug_get_random_rating_and_reviews' ) ) {
	/**
	 * Generate deterministic rating (4.5 - 5.0) and review count (< 200) based on product ID.
	 *
	 * @param int $product_id Product ID to seed the random calculation.
	 * @return array{rating: string, reviews: int}
	 */
	function jerseyplug_get_random_rating_and_reviews( int $product_id ): array {
		$seed = (int) $product_id;
		// Ratings: 4.5, 4.6, 4.7, 4.8, 4.9, 5.0
		$ratings = [ 4.5, 4.6, 4.7, 4.8, 4.9, 5.0 ];
		$rating  = $ratings[ $seed % count( $ratings ) ];

		// Reviews: under 200 (between 45 and 195)
		$reviews = 45 + ( ( $seed * 13 ) % 150 );

		return [
			'rating'  => number_format_i18n( $rating, 1 ),
			'reviews' => $reviews,
		];
	}
}
