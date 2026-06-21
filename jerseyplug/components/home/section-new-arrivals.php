<?php
/**
 * Homepage new arrivals section.
 *
 * @package JerseyPlug
 */

$args     = wp_parse_args( $args ?? [], [ 'page_id' => 0 ] );
$products = jerseyplug_get_homepage_products( 4, 'new' );

if ( empty( $products ) ) {
	return;
}

do_action( 'jerseyplug_before_home_new_arrivals', $products, (int) $args['page_id'] );
?>

<section class="bg-lightBg py-16">
	<div class="container mx-auto px-4">
		<div class="mb-12 flex items-end justify-between">
			<div>
				<h2 class="mb-2 text-2xl font-bold text-primary md:text-4xl">
					<?php echo esc_html( jerseyplug_pll( 'New Arrivals' ) ); ?>
				</h2>
				<div class="h-1 w-20 rounded bg-accent"></div>
			</div>
			<a href="<?php echo esc_url( jerseyplug_get_homepage_shop_url() ); ?>" class="hidden items-center gap-1 font-bold text-primary transition-all hover:underline md:flex">
				<?php echo esc_html( jerseyplug_pll( 'View All' ) ); ?>
				<span aria-hidden="true">→</span>
			</a>
		</div>

		<ul class="products grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 md:gap-x-4 md:gap-y-10 lg:grid-cols-4 before:!hidden after:!hidden list-none !m-0 !p-0">
			<?php foreach ( $products as $index => $product ) : ?>
				<?php get_template_part( 'components/products/product-card', null, [ 'product' => $product, 'index' => $index ] ); ?>
			<?php endforeach; ?>
		</ul>

		<div class="mt-8 text-center md:hidden">
			<a href="<?php echo esc_url( jerseyplug_get_homepage_shop_url() ); ?>" class="inline-flex items-center gap-2 font-bold text-primary hover:underline">
				<?php echo esc_html( jerseyplug_pll( 'View All' ) ); ?>
				<span aria-hidden="true">→</span>
			</a>
		</div>
	</div>
</section>

<?php do_action( 'jerseyplug_after_home_new_arrivals', $products, (int) $args['page_id'] ); ?>
