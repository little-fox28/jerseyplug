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
