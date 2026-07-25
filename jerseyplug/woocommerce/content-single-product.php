<?php

/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

// Remove sidebar from single product page
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

global $product;

$product_id = (int) $product->get_id();
if ($product->is_type('variable')) {
	$base_price = (float) $product->get_variation_price('min', true);
} else {
	$base_price = (float) wc_get_price_to_display($product);
}

$print_price = (float) get_post_meta($product_id, '_print_price', true);
if ($print_price <= 0) {
	$print_price = 0.0;
}

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('container mx-auto px-4 py-8 lg:py-12', $product); ?>
	x-data="jerseyplugProductForm()">

	<!-- Mobile sticky bar trigger point -->
	<div @scroll.window.passive="showStickyBar = window.scrollY > 350"></div>

	<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14">

		<!-- Left Column: Gallery -->
		<div class="space-y-8 product-gallery-column relative">
			<?php
			/**
			 * Hook: woocommerce_before_single_product_summary.
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action('woocommerce_before_single_product_summary');
			?>

			<!-- Description (Optional below gallery on desktop) -->
			<?php
			$desc = $product->get_description();
			if (empty($desc)) {
				$desc = $product->get_short_description();
			}
			if (! empty($desc)) :
			?>
				<div class="border-t border-gray-100 pt-8 hidden lg:block">
					<h2 class="font-black text-sm uppercase tracking-wider text-gray-500 mb-4">
						<?php echo esc_html( jerseyplug_pll( 'Product Details' ) ); ?>
					</h2>
					<div class="prose prose-sm prose-zinc text-gray-600 leading-relaxed">
						<?php echo wp_kses_post(str_replace('✅', '<br/>✅', $desc)); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!-- Right Column: Info & Form -->
		<div class="product-info-column">
			<div class="lg:sticky lg:top-24 space-y-6">
				<!-- 1. Title -->
				<?php woocommerce_template_single_title(); ?>

				<!-- 2. Price and Rating (Inline Flex Container) -->
				<?php wc_get_template('single-product/price.php'); ?>

				<!-- 3. Excerpt -->
				<?php woocommerce_template_single_excerpt(); ?>

				<!-- 4. Add to Cart Form -->
				<?php woocommerce_template_single_add_to_cart(); ?>

				<!-- Description (Mobile only) -->
				<?php if (! empty($desc)) : ?>
					<div class="border-t border-gray-100 pt-6 mt-8 lg:hidden">
						<h2 class="font-black text-sm uppercase tracking-wider text-gray-500 mb-4">
							<?php echo esc_html( jerseyplug_pll( 'Product Details' ) ); ?>
						</h2>
						<div class="prose prose-sm prose-zinc text-gray-600 leading-relaxed">
							<?php echo wp_kses_post(str_replace('✅', '<br/>✅', $desc)); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

	</div>

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
	do_action('woocommerce_after_single_product_summary');
	?>

	<!-- Sticky Mobile Bar -->
	<div
		class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-100 p-4 shadow-xl flex items-center justify-between lg:hidden transition-transform duration-300"
		x-show="showStickyBar"
		x-cloak
		:class="showStickyBar ? 'translate-y-0' : 'translate-y-full'">
		<div class="flex flex-col">
			<span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"><?php echo esc_html( jerseyplug_pll( 'Total' ) ); ?></span>
			<span class="text-lg font-black text-primary" x-text="formatCurrency(totalCalculatedPrice)"></span>
		</div>
		<button
			type="button"
			@click="submitForm()"
			class="bg-primary text-white text-[11px] font-black uppercase tracking-wider px-6 py-3 rounded-xl shadow-lg hover:bg-accent hover:text-primary transition-colors">
			<?php echo esc_html(jerseyplug_pll('Add to Cart')); ?>
		</button>
	</div>

</div>

<script>
	document.addEventListener('alpine:init', () => {
		Alpine.data('jerseyplugProductForm', () => ({
			showStickyBar: false,
			isAddingToCart: false,

			// Pricing & State
			basePrice: <?php echo (float) $base_price; ?>,
			regularPrice: <?php echo (float) ($product->is_type('variable') ? $product->get_variation_regular_price('min', true) : wc_get_price_to_display($product, ['price' => $product->get_regular_price()])); ?>,
			printPrice: <?php echo (float) $print_price; ?>,
			quantity: 1,
			isVariationSelected: false,

			customName: '',
			customNumber: '',
			selectedPatches: [],

			get singleProductPrice() {
				let price = this.basePrice;
				if (this.customName.trim() !== '' || this.customNumber.trim() !== '') {
					price += this.printPrice;
				}
				this.selectedPatches.forEach(p => {
					price += p.price;
				});
				return price;
			},
			get singleProductRegularPrice() {
				let price = this.regularPrice;
				if (this.customName.trim() !== '' || this.customNumber.trim() !== '') {
					price += this.printPrice;
				}
				this.selectedPatches.forEach(p => {
					price += p.price;
				});
				return price;
			},

			get totalCalculatedPrice() {
				return this.singleProductPrice * this.quantity;
			},

			formatCurrency(amount) {
				return 'R\u00a0' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
			},

			// Sync with WooCommerce native form
			init() {
				// Update base price dynamically when WooCommerce triggers 'found_variation'
				jQuery(this.$el).on('found_variation', '.variations_form', (event, variation) => {
					if (variation && variation.display_price !== undefined) {
						this.basePrice = parseFloat(variation.display_price);
						this.regularPrice = variation.display_regular_price !== undefined ? parseFloat(variation.display_regular_price) : this.basePrice;
						if (variation.print_price !== undefined) {
							this.printPrice = parseFloat(variation.print_price);
						}
						this.isVariationSelected = true;
					}
				});

				// Reset base price
				jQuery(this.$el).on('reset_data', '.variations_form', () => {
					this.basePrice = <?php echo (float) $base_price; ?>;
					this.regularPrice = <?php echo (float) ($product->is_type('variable') ? $product->get_variation_regular_price('min', true) : wc_get_price_to_display($product, ['price' => $product->get_regular_price()])); ?>;
					this.isVariationSelected = false;
				});
			},

			// Personalization actions
			togglePatch(patch) {
				const idx = this.selectedPatches.findIndex(p => p.slug === patch.slug);
				if (idx > -1) {
					this.selectedPatches.splice(idx, 1);
				} else {
					this.selectedPatches.push(patch);
				}
			},
			isPatchSelected(slug) {
				return this.selectedPatches.some(p => p.slug === slug);
			},

			// Handle Form Submit from Sticky Bar
			submitForm() {
				const form = this.$el.querySelector('form.cart');
				if (form) {
					if (form.reportValidity && !form.reportValidity()) {
						return;
					}
					if (form.requestSubmit) {
						form.requestSubmit();
					} else {
						form.submit();
					}
				}
			}
		}));
	});
</script>

<!-- Size Guide Modal -->
<?php get_template_part('components/products/size-guide-modal'); ?>

<!-- Sticky Add to Cart (Mobile) -->
<?php get_template_part('components/products/sticky-add-to-cart'); ?>

<?php do_action('woocommerce_after_single_product'); ?>