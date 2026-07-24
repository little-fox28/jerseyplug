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
	function jerseyplug_pll( string $text, string $domain = 'jerseyplug' ): string {
		$pll_translated = $text;
		
		if ( defined( 'JERSEYPLUG_TRUE_LANG' ) && function_exists( 'pll_translate_string' ) ) {
			$pll_translated = (string) pll_translate_string( $text, JERSEYPLUG_TRUE_LANG );
		} elseif ( function_exists( 'pll__' ) ) {
			$pll_translated = (string) pll__( $text );
		}

		// If Polylang returned a different string, it means it has a translation.
		if ( $pll_translated !== $text ) {
			return $pll_translated;
		}

		// Fallback to standard WordPress translation (.po/.mo files via Loco Translate)
		$wp_translated = (string) __( $text, $domain );
		if ( $wp_translated !== $text ) {
			return $wp_translated;
		}

		// Hardcoded fallback translations for common strings in AF if no .po/Polylang entry exists
		$current_lang = defined( 'JERSEYPLUG_TRUE_LANG' ) ? JERSEYPLUG_TRUE_LANG : ( strpos( get_locale(), 'af' ) === 0 ? 'af' : 'en' );
		if ( 'af' === $current_lang ) {
			$af_defaults = [
				'Log out'         => 'Teken uit',
				'Logout'          => 'Teken uit',
				'Log in'          => 'Teken in',
				'Login'           => 'Teken in',
				'Shipment'        => 'Versending',
				'Shipping'        => 'Versending',
				'Shipping to %s.' => 'Versending na %s.',
				'Change address'  => 'Verander adres',
				'Flat rate'       => 'Vaste tarief',
				'Flat rate:'      => 'Vaste tarief:',
				'Enter a different address' => 'Voer \'n ander adres in',
				'Order Confirmed!' => 'Bestelling Bevestig!',
				'Thank you. Your order has been received and is now being processed.' => 'Dankie. Jou bestelling is ontvang en word nou verwerk.',
				'View Order Details' => 'Bekyk bestellingbesonderhede',
				'Order Details' => 'Bestellingbesonderhede',
				'Order Failed'  => 'Bestelling het misluk',
				'Customer Info' => 'Kliënt inligting',
				'Billing Address'  => 'Faktuurasres',
				'Billing address'  => 'Faktuurasres',
				'Shipping Address' => 'Afleweringsadres',
				'Shipping address' => 'Afleweringsadres',
				'Order Note'    => 'Bestelling nota',
				'Order Summary' => 'Bestelling opsomming',
				'Pay'           => 'Betaal',
			];
			if ( isset( $af_defaults[ $text ] ) ) {
				return $af_defaults[ $text ];
			}
		}

		return $text;
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
