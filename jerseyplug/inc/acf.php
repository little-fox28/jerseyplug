<?php
/**
 * ACF field logic and mega-menu caching helpers.
 *
 * @package JerseyPlug
 */

/**
 * ACF option pages for header-managed fields (logo + announcement).
 */
function jerseyplug_register_acf_options_pages(): void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		[
			'page_title' => 'Theme Settings',
			'menu_title' => 'Theme Settings',
			'menu_slug'  => 'jerseyplug-theme-settings',
			'capability' => 'manage_options',
			'redirect'   => false,
		]
	);
}

if ( is_admin() ) {
	add_action( 'acf/init', 'jerseyplug_register_acf_options_pages' );
}

/**
 * Resolve category logo URL from native WooCommerce and custom external_logo_url meta.
 * Legacy ACF category_logo field has been removed in favor of:
 * 1. Native WooCommerce thumbnail_id (primary source)
 * 2. Custom external_logo_url term meta (fallback source)
 */
function jerseyplug_get_category_logo_url( int $term_id ): string {
	// Primary: WooCommerce native thumbnail_id
	$thumbnail_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
	if ( $thumbnail_id > 0 ) {
		$thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' );
		if ( $thumbnail_url ) {
			return $thumbnail_url;
		}
	}

	// Fallback: Custom external_logo_url term meta
	$external_logo = (string) get_term_meta( $term_id, 'external_logo_url', true );
	if ( $external_logo !== '' ) {
		return $external_logo;
	}

	// Final fallback: WooCommerce or theme placeholder image
	return function_exists( 'wc_placeholder_img_src' )
		? wc_placeholder_img_src( 'woocommerce_thumbnail' )
		: get_theme_file_uri( '/resources/images/placeholder-category.png' );
}

/**
 * Build product category tree (children + grandchildren) for a top-level root slug.
 */
function jerseyplug_get_product_category_tree( string $root_slug ): array {
	if ( ! taxonomy_exists( 'product_cat' ) || $root_slug === '' ) {
		return [];
	}

	$root_terms = get_terms(
		[
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'slug'       => $root_slug,
			'number'     => 1,
		]
	);

	if ( is_wp_error( $root_terms ) || empty( $root_terms ) ) {
		return [];
	}

	$root = $root_terms[0];

	$descendants = get_terms(
		[
			'taxonomy'               => 'product_cat',
			'hide_empty'             => false,
			'child_of'               => (int) $root->term_id,
			'orderby'                => 'name',
			'order'                  => 'ASC',
			'update_term_meta_cache' => true,
		]
	);

	if ( is_wp_error( $descendants ) ) {
		return [];
	}

	$by_parent = [];
	foreach ( $descendants as $term ) {
		$parent_id = (int) $term->parent;
		if ( ! isset( $by_parent[ $parent_id ] ) ) {
			$by_parent[ $parent_id ] = [];
		}
		$by_parent[ $parent_id ][] = $term;
	}

	$children = $by_parent[ (int) $root->term_id ] ?? [];
	$tree     = [];

	foreach ( $children as $child ) {
		$tree[] = [
			'term'       => $child,
			'children'   => $by_parent[ (int) $child->term_id ] ?? [],
			'logo_url'   => jerseyplug_get_category_logo_url( (int) $child->term_id ),
			'translated' => jerseyplug_pll( $child->name ),
		];
	}

	return $tree;
}

/**
 * Flush mega-menu transient cache when product categories change.
 */
function jerseyplug_flush_mega_menu_cache(): void {
	global $wpdb;

	$wpdb->query(
		"DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_jerseyplug_mega_menu_%'
            OR option_name LIKE '_transient_timeout_jerseyplug_mega_menu_%'
            OR option_name LIKE '_transient_jerseyplug_mega_menu_data_%'
            OR option_name LIKE '_transient_timeout_jerseyplug_mega_menu_data_%'"
	);
}
add_action( 'created_product_cat', 'jerseyplug_flush_mega_menu_cache' );
add_action( 'edited_product_cat', 'jerseyplug_flush_mega_menu_cache' );
add_action( 'delete_product_cat', 'jerseyplug_flush_mega_menu_cache' );

/**
 * Flush mega-menu cache when ACF term meta changes.
 */
function jerseyplug_flush_mega_menu_cache_on_acf_save( $post_id ): void {
	if ( is_string( $post_id ) && strpos( $post_id, 'product_cat_' ) === 0 ) {
		jerseyplug_flush_mega_menu_cache();
	}
}
add_action( 'acf/save_post', 'jerseyplug_flush_mega_menu_cache_on_acf_save', 20 );
