<?php

/**
 * Variable product add to cart
 *
 * This template overrides the native WooCommerce variable.php template to implement
 * a custom Alpine.js + Tailwind CSS UI for variations, while keeping the native
 * hidden select fields for backend compatibility.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 6.1.0
 */

defined('ABSPATH') || exit;

global $product;

$attribute_keys  = array_keys($attributes);
$variations_json = wp_json_encode($available_variations);
$variations_attr = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars($variations_json, ENT_QUOTES, 'UTF-8', true);

do_action('woocommerce_before_add_to_cart_form'); ?>

<div class="custom-variations-wrapper">
	<form class="variations_form cart space-y-6" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. 
																																																																											?>">
		<?php do_action('woocommerce_before_variations_form'); ?>

		<?php if (empty($available_variations) && false !== $available_variations) : ?>
			<p class="stock out-of-stock"><?php echo esc_html(apply_filters('woocommerce_out_of_stock_message', __('This product is currently out of stock and unavailable.', 'woocommerce'))); ?></p>
		<?php else : ?>

			<!-- Custom Alpine + Tailwind UI -->
			<div class="space-y-4 variations" x-data="{
				selectedOptions: {},
				get hasSelection() { return Object.values(this.selectedOptions).some(val => val !== ''); },
				init() {
					jQuery(this.$el).closest('form').on('reset_data', () => {
						setTimeout(() => {
							const selects = this.$el.querySelectorAll('select[name^=\'attribute_\']');
							selects.forEach(select => {
								const attrName = select.name.replace('attribute_', '');
								this.selectedOptions[attrName] = select.value;
							});
						}, 10);
					});
				},
				selectOption(attributeName, value, event) {
					if (this.selectedOptions[attributeName] === value) { value = ''; }
					this.selectedOptions[attributeName] = value;
					const selectEl = event.currentTarget.closest('.variation-group').querySelector('select');
					if (selectEl) {
						selectEl.value = value;
						selectEl.dispatchEvent(new Event('change', { bubbles: true }));
						if (typeof jQuery !== 'undefined') { jQuery(selectEl).trigger('change'); }
					}
				}
			}">
				<?php foreach ($attributes as $attribute_name => $options) :
					$sanitized_name = sanitize_title($attribute_name);
				?>
					<div class="variation-group">
						<div class="flex items-center justify-between mb-2">
							<span class="text-xs font-black uppercase tracking-widest text-gray-500">
								<?php echo wc_attribute_label($attribute_name); // WPCS: XSS ok. 
								?>
							</span>
							<?php if (strtolower($attribute_name) === 'pa_size' || strtolower($attribute_name) === 'size') : ?>
								<button type="button" @click.prevent="$dispatch('open-size-guide')" class="text-xs font-bold text-primary underline underline-offset-2 hover:text-accent transition-colors">
									<?php esc_html_e('Size Guide', 'jerseyplug'); ?>
								</button>
							<?php endif; ?>
						</div>

						<!-- Tailwind Buttons -->
						<div class="flex flex-wrap gap-2">
							<?php
							if (! empty($options)) {
								if (taxonomy_exists($attribute_name)) {
									$terms = wc_get_product_terms($product->get_id(), $attribute_name, array('fields' => 'all'));
									foreach ($terms as $term) {
										if (in_array($term->slug, $options, true)) {
											$value = $term->slug;
											$label = apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute_name, $product);
							?>
											<button
												type="button"
												@click="selectOption('<?php echo esc_js($sanitized_name); ?>', '<?php echo esc_js($value); ?>', $event)"
												:class="selectedOptions['<?php echo esc_js($sanitized_name); ?>'] === '<?php echo esc_js($value); ?>' ? 'border-primary bg-primary text-white shadow-md' : 'border-gray-200 bg-white text-gray-900 hover:border-gray-400'"
												class="min-w-12 h-12 rounded-xl border-2 px-3 text-sm font-black uppercase transition-all duration-200">
												<?php echo esc_html($label); ?>
											</button>
										<?php
										}
									}
								} else {
									foreach ($options as $option) {
										$value = $option;
										$label = apply_filters('woocommerce_variation_option_name', $option, null, $attribute_name, $product);
										?>
										<button
											type="button"
											@click="selectOption('<?php echo esc_js($sanitized_name); ?>', '<?php echo esc_js($value); ?>')"
											:class="selectedOptions['<?php echo esc_js($sanitized_name); ?>'] === '<?php echo esc_js($value); ?>' ? 'border-primary bg-primary text-white shadow-md' : 'border-gray-200 bg-white text-gray-900 hover:border-gray-400'"
											class="min-w-12 h-12 rounded-xl border-2 px-3 text-sm font-black uppercase transition-all duration-200">
											<?php echo esc_html($label); ?>
										</button>
							<?php
									}
								}
							}
							?>
						</div>

						<!-- Native WooCommerce Select (Hidden via Tailwind) -->
						<div class="hidden">
							<?php
							wc_dropdown_variation_attribute_options(array('options' => $options, 'attribute' => $attribute_name, 'product' => $product));
							?>
						</div>
					</div>
				<?php endforeach; ?>

				<!-- Clear Variations Link -->
				<div x-show="hasSelection" style="display: none;" class="pt-2">
					<?php echo apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations text-xs font-bold text-red-500 hover:underline" href="#">' . esc_html__('Clear selection', 'woocommerce') . '</a>'); ?>
				</div>
			</div>

			<?php do_action('woocommerce_after_variations_table'); ?>

			<div class="single_variation_wrap mt-6 space-y-4">
				<?php
				do_action('woocommerce_before_single_variation');
				do_action('woocommerce_single_variation');
				do_action('woocommerce_after_single_variation');
				?>
			</div>
		<?php endif; ?>

		<?php do_action('woocommerce_after_variations_form'); ?>
	</form>
</div>

<?php
do_action('woocommerce_after_add_to_cart_form');
