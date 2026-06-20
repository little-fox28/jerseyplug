<?php
/**
 * Products / Shop page template.
 *
 * Uses the standard WooCommerce main loop (pre_get_posts filtered)
 * with Vanilla JS for client-side AJAX filtering via Fetch API.
 * All product data is server-rendered for full SSR SEO.
 *
 * @package JerseyPlug
 */

get_header();

// --- Data -------------------------------------------------------------------

$filter_options = function_exists( 'jerseyplug_get_products_page_filter_options' )
	? jerseyplug_get_products_page_filter_options()
	: [];

$per_page = (int) apply_filters( 'jerseyplug_products_per_page', 12 );

// Read active filters from URL for initial server-side state.
$active_competitions = ! empty( $_GET['filter_competition'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['filter_competition'] ) ) : [];
$active_teams        = ! empty( $_GET['filter_team'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['filter_team'] ) ) : [];
$active_versions     = ! empty( $_GET['filter_version'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['filter_version'] ) ) : [];
$active_sizes        = ! empty( $_GET['filter_size'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['filter_size'] ) ) : [];
$active_price        = ! empty( $_GET['filter_price'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_price'] ) ) : '';
$active_sort         = ! empty( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : 'featured';

$active_filters = [
	'competitions' => $active_competitions,
	'teams'        => $active_teams,
	'versions'     => $active_versions,
	'sizes'        => $active_sizes,
	'price'        => $active_price,
	'sort'         => $active_sort,
];

$total_active_filters = count( $active_competitions ) + count( $active_teams ) + count( $active_versions ) + count( $active_sizes ) + ( $active_price ? 1 : 0 );

do_action( 'jerseyplug_before_products_page' );
?>

<div
	id="products-page"
	class="site-main min-h-screen bg-white text-zinc-900"
	data-per-page="<?php echo esc_attr( (string) $per_page ); ?>"
	data-shop-url="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"
>
	<?php
	// --- Desktop Filter Toolbar (hidden on mobile) ---
	get_template_part( 'components/products/filter-toolbar', null, [
		'filter_options' => $filter_options,
		'active_filters' => $active_filters,
	] );

	// --- Mobile Filter Bar (hidden on desktop) ---
	get_template_part( 'components/products/mobile-filter-bar', null, [
		'active_filters'       => $active_filters,
		'total_active_filters' => $total_active_filters,
	] );

	// --- Product Grid ---
	get_template_part( 'components/products/product-grid', null, [
		'active_filters'       => $active_filters,
		'total_active_filters' => $total_active_filters,
	] );

	// --- Pagination ---
	get_template_part( 'components/products/load-more' );

	// --- Mobile Filter Drawer (off-canvas) ---
	get_template_part( 'components/products/mobile-filter-drawer', null, [
		'filter_options' => $filter_options,
		'active_filters' => $active_filters,
	] );
	?>
</div>

<?php
do_action( 'jerseyplug_after_products_page' );
get_footer();
