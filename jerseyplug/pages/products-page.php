<?php
/**
 * Products / Shop page template.
 *
 * Server-renders the initial product grid from WooCommerce and uses
 * Alpine.js for client-side filtering, sorting, and load-more.
 *
 * @package JerseyPlug
 */

get_header();

// --- Data -------------------------------------------------------------------

$filter_options = function_exists( 'jerseyplug_get_products_page_filter_options' )
	? jerseyplug_get_products_page_filter_options()
	: [];

$per_page       = (int) apply_filters( 'jerseyplug_products_per_page', 12 );
$initial_result = function_exists( 'jerseyplug_query_filtered_products' )
	? jerseyplug_query_filtered_products( [ 'per_page' => $per_page ] )
	: [ 'products' => [], 'total' => 0, 'max_pages' => 0 ];

$products   = $initial_result['products'];
$total      = $initial_result['total'];
$max_pages  = $initial_result['max_pages'];

// JSON-encode filter options for Alpine.js.
$filter_options_json = wp_json_encode( $filter_options, JSON_HEX_TAG | JSON_HEX_APOS );
$ajax_url            = admin_url( 'admin-ajax.php' );
$nonce               = wp_create_nonce( 'jerseyplug_products_nonce' );

do_action( 'jerseyplug_before_products_page', $products, $total );
?>

<main
	id="products-page"
	class="site-main min-h-screen bg-white text-zinc-900"
	x-data="productsFilter"
	x-init="init()"
	data-ajax-url="<?php echo esc_url( $ajax_url ); ?>"
	data-nonce="<?php echo esc_attr( $nonce ); ?>"
	data-filter-options='<?php echo $filter_options_json; ?>'
	data-per-page="<?php echo esc_attr( (string) $per_page ); ?>"
	data-total="<?php echo esc_attr( (string) $total ); ?>"
	data-max-pages="<?php echo esc_attr( (string) $max_pages ); ?>"
>
	<?php
	// --- Desktop Filter Toolbar (hidden on mobile) ---
	get_template_part( 'components/products/filter-toolbar', null, [
		'filter_options' => $filter_options,
	] );

	// --- Mobile Filter Bar (hidden on desktop) ---
	get_template_part( 'components/products/mobile-filter-bar' );

	// --- Product Grid ---
	get_template_part( 'components/products/product-grid', null, [
		'products' => $products,
		'total'    => $total,
	] );

	// --- Load More ---
	get_template_part( 'components/products/load-more', null, [
		'total'     => $total,
		'max_pages' => $max_pages,
		'per_page'  => $per_page,
	] );

	// --- Mobile Filter Drawer (off-canvas) ---
	get_template_part( 'components/products/mobile-filter-drawer', null, [
		'filter_options' => $filter_options,
	] );
	?>
</main>

<?php
do_action( 'jerseyplug_after_products_page', $products, $total );
get_footer();
