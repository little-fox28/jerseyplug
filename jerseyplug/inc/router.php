<?php
/**
 * Template router for page templates in /pages.
 *
 * @package JerseyPlug
 */

function jerseyplug_custom_router( string $template ): string {
	$homepage_template = get_theme_file_path( '/pages/home-page.php' );
	if ( is_front_page() && is_string( $homepage_template ) && file_exists( $homepage_template ) ) {
		return $homepage_template;
	}

	return $template;
}
add_filter( 'template_include', 'jerseyplug_custom_router', 99 );
