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
			<p class="stock out-of-stock"><?php echo esc_html(apply_filters('woocommerce_out_of_stock_message', jerseyplug_pll( 'This product is currently out of stock and unavailable.' ))); ?></p>
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

					// Watch parent's customName and customNumber to auto-update the hidden variation (REMOVED: Custom Name is now global)

					// Auto-select 'No Patch' and initialize Custom Name variation
					setTimeout(() => {
						const patchSelect = this.$el.querySelector('select[name=\'attribute_pa_patch\']');
						if (patchSelect && (!patchSelect.value || patchSelect.value === '')) {
							let noPatchValue = null;
							Array.from(patchSelect.options).forEach(opt => {
								const txt = opt.text.toLowerCase();
								const val = opt.value.toLowerCase();
								if (txt.includes('no patch') || val.includes('no-patch') || val.includes('no_patch') || txt === 'none' || val === 'none') {
									noPatchValue = opt.value;
								}
							});
							if (noPatchValue) {
								this.selectedOptions['pa_patch'] = noPatchValue;
								patchSelect.value = noPatchValue;
								patchSelect.dispatchEvent(new Event('change', { bubbles: true }));
								if (typeof jQuery !== 'undefined') { jQuery(patchSelect).trigger('change'); }
							}
						}
					}, 200);
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
					if ($attribute_name === 'pa_patch') {
						continue;
					}
					$sanitized_name = sanitize_title($attribute_name);
					$group_class = 'variation-group';
				?>
					<div class="<?php echo esc_attr($group_class); ?>">
						<div class="flex items-center justify-between mb-2">
							<span class="text-xs font-black uppercase tracking-widest text-gray-500">
								<?php echo wc_attribute_label($attribute_name);
								?>
							</span>
							<?php if (strtolower($attribute_name) === 'pa_size' || strtolower($attribute_name) === 'size') : ?>
								<button type="button" @click.prevent="$dispatch('open-size-guide')" class="text-xs font-bold text-primary underline underline-offset-2 hover:text-accent transition-colors">
									<?php echo esc_html(jerseyplug_pll('Size Guide')); ?>
								</button>
							<?php endif; ?>
						</div>

						<!-- Tailwind Buttons -->
						<div class="flex flex-wrap gap-2">
							<?php
							if (! empty($options)) {
								if (taxonomy_exists($attribute_name)) {
									$terms = wc_get_product_terms($product->get_id(), $attribute_name, array('fields' => 'all'));

									// Custom Size Sorting (XS, S, M, L, XL...)
									if (strtolower($attribute_name) === 'pa_sp_size' || strtolower($attribute_name) === 'pa_size' || strtolower($attribute_name) === 'sp_size' || strtolower($attribute_name) === 'size') {
										$size_order = array('xxs' => 1, 'xs' => 2, 's' => 3, 'm' => 4, 'l' => 5, 'xl' => 6, 'xxl' => 7, '2xl' => 7, '3xl' => 8, '4xl' => 9);
										usort($terms, function ($a, $b) use ($size_order) {
											$slug_a = strtolower($a->slug);
											$slug_b = strtolower($b->slug);
											$order_a = isset($size_order[$slug_a]) ? $size_order[$slug_a] : 99;
											$order_b = isset($size_order[$slug_b]) ? $size_order[$slug_b] : 99;
											if ($order_a === $order_b) {
												return strnatcmp($slug_a, $slug_b); // Fallback to natural sort for numbers
											}
											return $order_a - $order_b;
										});
									}

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
									// Custom Size Sorting (XS, S, M, L, XL...)
									if (strtolower($attribute_name) === 'pa_sp_size' || strtolower($attribute_name) === 'pa_size' || strtolower($attribute_name) === 'sp_size' || strtolower($attribute_name) === 'size') {
										$size_order = array('xxs' => 1, 'xs' => 2, 's' => 3, 'm' => 4, 'l' => 5, 'xl' => 6, 'xxl' => 7, '2xl' => 7, '3xl' => 8, '4xl' => 9);
										usort($options, function ($a, $b) use ($size_order) {
											$val_a = strtolower(trim($a));
											$val_b = strtolower(trim($b));
											$order_a = isset($size_order[$val_a]) ? $size_order[$val_a] : 99;
											$order_b = isset($size_order[$val_b]) ? $size_order[$val_b] : 99;
											if ($order_a === $order_b) {
												return strnatcmp($val_a, $val_b); // Fallback to natural sort for numbers
											}
											return $order_a - $order_b;
										});
									}

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


				<?php do_action('woocommerce_after_variations_table'); ?>

				<!-- Custom Personalization UI -->
				<div class="rounded-2xl border border-gray-100 bg-zinc-50 p-4 space-y-4">
					<h3 class="text-xs font-black uppercase tracking-widest text-gray-500">
						<?php echo esc_html( jerseyplug_pll( 'Personalization Details' ) ); ?>
					</h3>

					<div class="grid grid-cols-3 gap-3">
						<div class="col-span-2">
							<label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5" for="custom_name_display">
								<?php echo esc_html( jerseyplug_pll( 'Name' ) ); ?>
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
								<?php echo esc_html( jerseyplug_pll( 'Number' ) ); ?>
							</label>
							<input
								id="custom_number_display"
								type="text"
								x-model="customNumber"
								@input="customNumber = customNumber.replace(/\D/g, '')"
								maxlength="2"
								placeholder="10"
								autocomplete="off"
								class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold text-gray-900 placeholder:text-gray-300 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors" />
						</div>
					</div>

					<!-- Patches (Variation Attribute) -->
					<?php
					$has_patches = isset($attributes['pa_patch']) && !empty($attributes['pa_patch']);
					if ($has_patches) :
						$attribute_name = 'pa_patch';
						$options = $attributes['pa_patch'];
						$sanitized_name = sanitize_title($attribute_name);
					?>
						<div class="variation-group pt-4 border-t border-gray-100">
							<span class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">
								<?php echo esc_html( jerseyplug_pll( 'Patches' ) ); ?>
							</span>
							<div class="flex flex-wrap gap-2">
								<?php
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
												class="px-4 py-2 rounded-xl border-2 text-xs font-black uppercase transition-all duration-200">
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
											class="px-4 py-2 rounded-xl border-2 text-xs font-black uppercase transition-all duration-200">
											<?php echo esc_html($label); ?>
										</button>
								<?php
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
					<?php endif; ?>
				</div>

				<div class="single_variation_wrap mt-6 space-y-4 [&>.woocommerce-variation]:!hidden">
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
