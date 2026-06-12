<?php
/**
 * Template router for page templates in /pages.
 *
 * @package JerseyPlug
 */

function jerseyplug_custom_router( string $template ): string {
	// Products / Shop page.
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		$products_template = get_theme_file_path( '/pages/products-page.php' );
		if ( is_string( $products_template ) && file_exists( $products_template ) ) {
			return $products_template;
		}
	}

	// Homepage.
	$homepage_template = get_theme_file_path( '/pages/home-page.php' );
	if ( is_front_page() && is_string( $homepage_template ) && file_exists( $homepage_template ) ) {
		return $homepage_template;
	}

	// Single Product details.
	if ( function_exists( 'is_product' ) && is_product() ) {
		$detail_template = get_theme_file_path( '/pages/product-detail.php' );
		if ( is_string( $detail_template ) && file_exists( $detail_template ) ) {
			return $detail_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'jerseyplug_custom_router', 99 );

