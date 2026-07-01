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

defined( 'ABSPATH' ) || exit;

global $product;

$product_id = (int) $product->get_id();
$base_price = (float) ( $product->is_on_sale() ? $product->get_sale_price() : $product->get_regular_price() );

$print_price = (float) get_post_meta( $product_id, '_print_price', true );
if ( $print_price <= 0 ) {
	$print_price = 150.0;
}

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'container mx-auto px-4 py-8 lg:py-12', $product ); ?>
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
			do_action( 'woocommerce_before_single_product_summary' );
			?>
			
			<!-- Description (Optional below gallery on desktop) -->
			<?php
			$desc = $product->get_description();
			if ( empty( $desc ) ) {
				$desc = $product->get_short_description();
			}
			if ( ! empty( $desc ) ) :
			?>
			<div class="border-t border-gray-100 pt-8 hidden lg:block">
				<h2 class="font-black text-sm uppercase tracking-wider text-gray-500 mb-4">
					<?php esc_html_e( 'Product Details', 'jerseyplug' ); ?>
				</h2>
				<div class="prose prose-sm prose-zinc text-gray-600 leading-relaxed">
					<?php echo wp_kses_post( str_replace( '✅', '<br/>✅', $desc ) ); ?>
				</div>
			</div>
			<?php endif; ?>
		</div>

		<!-- Right Column: Info & Form -->
		<div class="product-info-column">
			<div class="lg:sticky lg:top-24 space-y-6">
				<?php
				/**
				 * Hook: woocommerce_single_product_summary.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				do_action( 'woocommerce_single_product_summary' );
				?>

				<!-- Custom Name & Number UI (Always Visible, Alpine-bound) -->
				<div class="rounded-2xl border border-gray-100 bg-zinc-50 p-4 space-y-4">
					<h3 class="text-xs font-black uppercase tracking-widest text-gray-500">
						<?php esc_html_e('Personalization Details', 'jerseyplug'); ?>
					</h3>
					<div class="grid grid-cols-3 gap-3">
						<div class="col-span-2">
							<label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5" for="custom_name_display">
								<?php esc_html_e('Name', 'jerseyplug'); ?>
							</label>
							<input
								id="custom_name_display"
								type="text"
								x-model="customName"
								maxlength="12"
								placeholder="MESSI"
								autocomplete="off"
								class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-900 placeholder:text-gray-300 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors" />
						</div>
						<div>
							<label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5" for="custom_number_display">
								<?php esc_html_e('Number', 'jerseyplug'); ?>
							</label>
							<input
								id="custom_number_display"
								type="text"
								x-model="customNumber"
								maxlength="2"
								placeholder="10"
								autocomplete="off"
								class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold text-gray-900 placeholder:text-gray-300 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors" />
						</div>
					</div>
				</div>
				
				<!-- Description (Mobile only) -->
				<?php if ( ! empty( $desc ) ) : ?>
				<div class="border-t border-gray-100 pt-6 mt-8 lg:hidden">
					<h2 class="font-black text-sm uppercase tracking-wider text-gray-500 mb-4">
						<?php esc_html_e( 'Product Details', 'jerseyplug' ); ?>
					</h2>
					<div class="prose prose-sm prose-zinc text-gray-600 leading-relaxed">
						<?php echo wp_kses_post( str_replace( '✅', '<br/>✅', $desc ) ); ?>
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
	do_action( 'woocommerce_after_single_product_summary' );
	?>
	
	<!-- Sticky Mobile Bar -->
	<div
		x-show="showStickyBar"
		x-cloak
		x-transition:enter="transition ease-out duration-300"
		x-transition:enter-start="translate-y-full"
		x-transition:enter-end="translate-y-0"
		x-transition:leave="transition ease-in duration-200"
		x-transition:leave-start="translate-y-0"
		x-transition:leave-end="translate-y-full"
		class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-100 px-4 py-3 shadow-2xl flex items-center justify-between md:hidden">
		<div class="flex flex-col leading-tight">
			<span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
				<?php esc_html_e( 'Total', 'jerseyplug' ); ?>
			</span>
			<span class="text-lg font-black text-primary" x-text="formatCurrency(totalCalculatedPrice)"></span>
		</div>
		<button
			type="button"
			@click="submitForm()"
			class="bg-primary text-white text-[11px] font-black uppercase tracking-widest px-6 py-3 rounded-xl shadow-lg hover:bg-accent hover:text-primary transition-colors disabled:opacity-50"
			:disabled="isAddingToCart">
			<?php esc_html_e( 'Add to Bag', 'jerseyplug' ); ?>
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
		printPrice: <?php echo (float) $print_price; ?>,
		quantity: 1,
		
		customName: '',
		customNumber: '',
		
		get singleProductPrice() {
			return this.basePrice;
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
				}
			});
			
			// Reset base price
			jQuery(this.$el).on('reset_data', '.variations_form', () => {
				this.basePrice = <?php echo (float) $base_price; ?>;
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

<?php do_action( 'woocommerce_after_single_product' ); ?>
