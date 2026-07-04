<?php

/**
 * Single Product detail page template.
 *
 * @package JerseyPlug
 */

get_header();

$product = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : null;

if (! $product instanceof WC_Product) {
	echo '<div class="container mx-auto py-16 text-center text-gray-500">Product details not available.</div>';
	get_footer();
	return;
}

// Get related products
$related_ids = function_exists('wc_get_related_products') ? wc_get_related_products($product->get_id(), 4) : [];
$related_products = [];
if (! empty($related_ids)) {
	$related_products = wc_get_products(['include' => $related_ids, 'limit' => 4]);
}
?>

<div class="min-h-screen bg-white text-gray-900 pb-24 md:pb-16">
	<!-- Top Bar / Back button -->
	<div class="container mx-auto px-4 pt-6 pb-2">
		<a
			href="#"
			onclick="history.back(); return false;"
			class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-primary transition-colors">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
				<line x1="19" y1="12" x2="5" y2="12"></line>
				<polyline points="12 19 5 12 12 5"></polyline>
			</svg>
			<?php echo esc_html(jerseyplug_pll('Back to List')); ?>
		</a>
	</div>

	<!-- Main Details Layout -->
	<main class="container mx-auto px-4 py-8">
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
			<!-- Gallery Column (7 cols) -->
			<div class="lg:col-span-7 space-y-8">
				<?php get_template_part('components/products/product-detail-gallery', null, ['product' => $product]); ?>

				<!-- Description / Info section -->
				<div class="border-t border-gray-200 pt-8 mt-10">
					<h3 class="font-black text-lg text-gray-900 mb-6 uppercase tracking-wider">
						<?php echo esc_html(jerseyplug_pll('Product Details')); ?>
					</h3>
					<div class="prose prose-zinc max-w-none text-sm text-gray-600 leading-relaxed">
						<?php
						$desc = $product->get_description();
						if (empty($desc)) {
							$desc = $product->get_short_description();
						}
						echo wp_kses_post(str_replace('✅', '<br/>✅', $desc));
						?>
					</div>
				</div>
			</div>

			<!-- Info and Form Purchase Column (5 cols) -->
			<div class="lg:col-span-5">
				<div class="lg:sticky lg:top-24">
					<?php get_template_part('components/products/product-detail-info', null, ['product' => $product]); ?>
				</div>
			</div>
		</div>

		<!-- Related Products Section -->
		<?php if (! empty($related_products)) : ?>
			<div class="mt-16 border-t border-gray-100 pt-16">
				<h2 class="text-xl font-black uppercase text-gray-900 mb-8 tracking-wider">
					<?php echo esc_html(jerseyplug_pll('You May Also Like')); ?>
				</h2>
				<ul class="products grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 md:gap-x-4 md:gap-y-10 lg:grid-cols-4 before:!hidden after:!hidden list-none !m-0 !p-0">
					<?php
					foreach ($related_products as $index => $rel_prod) :
						if (! $rel_prod instanceof WC_Product) {
							continue;
						}

						$image_id = $rel_prod->get_image_id();
						$image = $image_id > 0 ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : '';
						$terms = get_the_terms($rel_prod->get_id(), 'product_cat');
						$category = '';
						if (is_array($terms) && ! empty($terms) && $terms[0] instanceof WP_Term) {
							$category = (string) $terms[0]->name;
						}

						$card_data = [
							'id'           => $rel_prod->get_id(),
							'slug'         => $rel_prod->get_slug(),
							'name'         => $rel_prod->get_name(),
							'url'          => get_permalink($rel_prod->get_id()),
							'image'        => $image ?: (function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('woocommerce_thumbnail') : ''),
							'category'     => $category,
							'price'        => $rel_prod->get_price_html(),
							'rating_label' => jerseyplug_get_random_rating_and_reviews((int) $rel_prod->get_id())['rating'],
							'tag'          => $rel_prod->is_featured()
								? (function_exists('jerseyplug_pll') ? jerseyplug_pll('Trending Now') : 'Trending Now')
								: (function_exists('jerseyplug_is_new_product') && jerseyplug_is_new_product($rel_prod) ? (function_exists('jerseyplug_pll') ? jerseyplug_pll('New') : 'New') : ''),
						];

						get_template_part('components/products/product-card', null, ['product' => $card_data, 'index' => $index]);
					endforeach;
					?>
				</ul>
			</div>
		<?php endif; ?>
	</main>

	<!-- Size Guide Modal -->
	<?php get_template_part('components/products/size-guide-modal'); ?>
</div>

<?php
get_footer();
