<?php

if ( is_file( __DIR__ . '/vendor/autoload_packages.php' ) ) {
	require_once __DIR__ . '/vendor/autoload_packages.php';
}

$jerseyplug_require = static function ( string $relative_path ): void {
	$path = get_theme_file_path( $relative_path );
	if ( is_string( $path ) && $path !== '' && file_exists( $path ) ) {
		require_once $path;
	}
};

$jerseyplug_require( '/inc/helpers.php' );
$jerseyplug_require( '/inc/admin-options.php' );
$jerseyplug_require( '/inc/setup.php' );
$jerseyplug_require( '/inc/enqueue.php' );
$jerseyplug_require( '/inc/homepage.php' );
$jerseyplug_require( '/inc/router.php' );

if ( function_exists( 'WC' ) || class_exists( 'WooCommerce' ) ) {
	$jerseyplug_require( '/inc/woocommerce.php' );
	$jerseyplug_require( '/inc/products.php' );
}

if ( function_exists( 'get_field' ) || is_admin() ) {
	$jerseyplug_require( '/inc/acf.php' );
}

if ( wp_doing_ajax() ) {
	// Placeholder for future AJAX-only modules.
}
