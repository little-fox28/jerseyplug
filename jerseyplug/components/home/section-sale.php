<?php

/**
 * Homepage sale section.
 *
 * @package JerseyPlug
 */

$args     = wp_parse_args($args ?? [], ['page_id' => 0]);
$products = jerseyplug_get_homepage_products(4, 'sale');

if (empty($products)) {
	return;
}

do_action('jerseyplug_before_home_sale', $products, (int) $args['page_id']);
?>

<section class="bg-white py-16">
	<div class="container mx-auto px-4">
		<div class="mb-14 text-center">
			<h2 class="mb-3 text-4xl font-black uppercase md:text-5xl lg:text-6xl text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500 inline-block drop-shadow-sm">
				<?php echo esc_html(jerseyplug_pll('On Sale')); ?>
			</h2>
		</div>

		<ul class="products grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 md:gap-x-4 md:gap-y-10 lg:grid-cols-4 before:!hidden after:!hidden list-none !m-0 !p-0">
			<?php foreach ($products as $index => $product) : ?>
				<?php get_template_part('components/products/product-card', null, ['product' => $product, 'index' => $index]); ?>
			<?php endforeach; ?>
		</ul>

		<div class="mt-12 text-center">
			<a href="<?php echo esc_url(jerseyplug_get_homepage_shop_url()); ?>" class="inline-flex items-center justify-center gap-2 bg-[#163300] text-white font-bold py-4 px-10 rounded-xl hover:bg-[#0a1700] hover:-translate-y-1 hover:shadow-xl hover:shadow-gray-200 transition-all duration-300">
				<?php echo esc_html(jerseyplug_pll('View All Sale Products')); ?>
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:translate-x-1">
					<path d="M5 12h14" />
					<path d="m12 5 7 7-7 7" />
				</svg>
			</a>
		</div>
	</div>
</section>

<?php do_action('jerseyplug_after_home_sale', $products, (int) $args['page_id']); ?>