<?php

/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php do_action('woocommerce_before_add_to_cart_button'); ?>

	<div class="flex items-center gap-3">
		<!-- Alpine Quantity Stepper -->
		<?php do_action('woocommerce_before_add_to_cart_quantity'); ?>
		<div class="flex h-12 items-center rounded-xl border-2 border-gray-200 px-1 bg-white gap-1">
			<button
				type="button"
				@click="if (quantity > 1) quantity--"
				class="w-9 h-9 flex items-center justify-center font-black text-gray-500 hover:text-primary transition-colors rounded-xl hover:bg-gray-50"
				aria-label="<?php esc_attr_e('Decrease quantity', 'jerseyplug'); ?>">
				&minus;
			</button>
			<!-- Hidden native input to sync with form submission -->
			<input
				type="number"
				name="quantity"
				x-model.number="quantity"
				min="<?php echo apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product); ?>"
				max="<?php echo apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product); ?>"
				class="w-10 text-center text-sm font-black text-gray-900 border-none bg-transparent focus:outline-none"
				aria-label="<?php esc_attr_e('Quantity', 'jerseyplug'); ?>" />
			<button
				type="button"
				@click="quantity++"
				class="w-9 h-9 flex items-center justify-center font-black text-gray-500 hover:text-primary transition-colors rounded-xl hover:bg-gray-50"
				aria-label="<?php esc_attr_e('Increase quantity', 'jerseyplug'); ?>">
				&plus;
			</button>
		</div>
		<?php do_action('woocommerce_after_add_to_cart_quantity'); ?>

		<!-- Tailwind Submit Button -->
		<button
			type="submit"
			style="background-color: #163300 !important; color: #f2c86c !important; border-radius: 12px !important;"
			class="single_add_to_cart_button button alt flex-1 h-12 rounded-xl text-xs font-black uppercase tracking-widest shadow-[0_4px_14px_0_rgba(22,51,0,0.39)] hover:shadow-[0_6px_20px_rgba(22,51,0,0.23)] hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
			<?php echo esc_html(jerseyplug_pll('Add to Cart')); ?>
		</button>

		<?php do_action('woocommerce_after_add_to_cart_button'); ?>
	</div>

	<input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>