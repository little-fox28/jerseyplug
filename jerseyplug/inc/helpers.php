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
		if ( function_exists( 'pll__' ) ) {
			return (string) pll__( $text );
		}

		return (string) __( $text, 'jerseyplug' );
	}
}
