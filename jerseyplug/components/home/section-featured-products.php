<?php
/**
 * Homepage featured products section.
 *
 * @package JerseyPlug
 */

$args     = wp_parse_args( $args ?? [], [ 'page_id' => 0 ] );
$products = jerseyplug_get_homepage_products( 4, 'featured' );

if ( empty( $products ) ) {
	return;
}

do_action( 'jerseyplug_before_home_featured_products', $products, (int) $args['page_id'] );
?>

<section class="bg-lightBg py-16">
	<div class="container mx-auto px-4">
		<div class="mb-12 text-center">
			<h2 class="mb-4 text-2xl font-bold text-primary md:text-4xl">
				<?php echo esc_html( jerseyplug_pll( 'Trending Now' ) ); ?>
			</h2>
			<div class="mx-auto h-1 w-20 rounded bg-accent"></div>
		</div>

		<div class="grid grid-cols-2 gap-3 md:gap-8 lg:grid-cols-4">
			<?php foreach ( $products as $product ) : ?>
				<?php get_template_part( 'parts/home/product-card', null, [ 'product' => $product ] ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php do_action( 'jerseyplug_after_home_featured_products', $products, (int) $args['page_id'] ); ?>
