<?php

/**
 * Cart totals
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined('ABSPATH') || exit;
?>
<div class="cart_totals !w-full !float-none bg-gray-50 rounded-2xl p-6 lg:p-8 border border-gray-200 shadow-sm <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">

	<?php do_action('woocommerce_before_cart_totals'); ?>

	<h2 class="text-xl font-bold text-[#163300] mb-6"><?php esc_html_e('Order Summary', 'woocommerce'); ?></h2>

	<!-- Removed shop_table to prevent WooCommerce default CSS from overriding our Tailwind layout -->
	<table cellspacing="0" class="w-full text-sm">

		<tr class="cart-subtotal border-b border-gray-200/60">
			<th class="py-3 text-left font-normal text-gray-500 !bg-transparent "><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
			<td class="py-3 text-right font-bold text-[#163300] !bg-transparent " data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
			<tr class="cart-discount border-b border-gray-200/60 coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
				<th class="py-3 text-left font-normal text-gray-500 !bg-transparent "><?php wc_cart_totals_coupon_label($coupon); ?></th>
				<td class="py-3 text-right font-bold text-[#163300] !bg-transparent " data-title="<?php echo esc_attr(wc_cart_totals_coupon_label($coupon, false)); ?>">
					<div class="flex items-center justify-end gap-2">
						<?php 
						ob_start();
						wc_cart_totals_coupon_html($coupon);
						$coupon_html = ob_get_clean();
						
						// Replace [Remove] with a trash can icon
						$remove_text = __('[Remove]', 'woocommerce');
						$trash_icon = '<span class="!inline-flex !items-center !justify-center !w-8 !h-8 !rounded-full text-gray-300 hover:!text-red-500 hover:!bg-red-50 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></span>';
						
						// The default HTML is like: "-$10.00 <a href=...>[Remove]</a>"
						$coupon_html = str_replace($remove_text, $trash_icon, $coupon_html);
						
						// Also remove default WooCommerce text-decoration/color by applying inline style or class
						$coupon_html = str_replace('class="woocommerce-remove-coupon"', 'class="woocommerce-remove-coupon !no-underline"', $coupon_html);
						
						echo $coupon_html; 
						?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>

		<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
			<?php do_action('woocommerce_cart_totals_before_shipping'); ?>

			<tr class="shipping border-b border-gray-200/60">
				<th class="py-3 text-left font-normal text-gray-500 !bg-transparent "><?php esc_html_e('Shipping', 'woocommerce'); ?></th>
				<td class="py-3 text-right font-bold text-[#163300] !bg-transparent  [&>ul]:m-0 [&>ul]:p-0 [&>ul>li]:m-0 [&>ul>li]:p-0 [&>ul>li]:list-none" data-title="<?php esc_attr_e('Shipping', 'woocommerce'); ?>">
					<?php wc_cart_totals_shipping_html(); ?>
				</td>
			</tr>

			<?php do_action('woocommerce_cart_totals_after_shipping'); ?>
		<?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>
			<tr class="shipping border-b border-gray-200/60">
				<th class="py-3 text-left font-normal text-gray-500 !bg-transparent "><?php esc_html_e('Shipping', 'woocommerce'); ?></th>
				<td class="py-3 text-right font-bold text-[#163300] !bg-transparent " data-title="<?php esc_attr_e('Shipping', 'woocommerce'); ?>"><?php woocommerce_shipping_calculator(); ?></td>
			</tr>
		<?php endif; ?>

		<?php foreach (WC()->cart->get_fees() as $fee) : ?>
			<tr class="fee border-b border-gray-200/60">
				<th class="py-3 text-left font-normal text-gray-500 !bg-transparent "><?php echo esc_html($fee->name); ?></th>
				<td class="py-3 text-right font-bold text-[#163300] !bg-transparent " data-title="<?php echo esc_attr($fee->name); ?>"><?php wc_cart_totals_fee_html($fee); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php
		if (wc_tax_enabled() && ! WC()->cart->display_prices_including_tax()) {
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';
			if (WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()) {
				$estimated_text = sprintf(' <small>' . esc_html__('(estimated for %s)', 'woocommerce') . '</small>', WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]]);
			}
			if ('itemized' === get_option('woocommerce_tax_total_display')) {
				foreach (WC()->cart->get_tax_totals() as $code => $tax) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		?>
					<tr class="tax-rate border-b border-gray-200/60 tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
						<th class="py-3 text-left font-normal text-gray-500 !bg-transparent "><?php echo esc_html($tax->label) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
																								?></th>
						<td class="py-3 text-right font-bold text-[#163300] !bg-transparent " data-title="<?php echo esc_attr($tax->label); ?>"><?php echo wp_kses_post($tax->formatted_amount); ?></td>
					</tr>
				<?php
				}
			} else {
				?>
				<tr class="tax-total border-b border-gray-200/60">
					<th class="py-3 text-left font-normal text-gray-500 !bg-transparent "><?php echo esc_html(WC()->countries->tax_or_vat()) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
																							?></th>
					<td class="py-3 text-right font-bold text-[#163300] !bg-transparent " data-title="<?php echo esc_attr(WC()->countries->tax_or_vat()); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
		<?php
			}
		}
		?>

		<?php do_action('woocommerce_cart_totals_before_order_total'); ?>

		<tr class="order-total">
			<th class="py-6 text-left font-bold text-gray-900 text-lg !bg-transparent !p-0 !pt-6"><?php esc_html_e('Total', 'woocommerce'); ?></th>
			<td class="py-6 text-right font-black text-2xl text-[#163300] !bg-transparent !p-0 !pt-6" data-title="<?php esc_attr_e('Total', 'woocommerce'); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action('woocommerce_cart_totals_after_order_total'); ?>

	</table>
	<!-- Input Coupon code -->
	<?php if (wc_coupons_enabled()) { ?>
		<form class="checkout_coupon woocommerce-form-coupon mt-8 mb-4 bg-white !rounded-2xl border border-gray-200 p-4" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
			<label for="coupon_code" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3"><?php esc_html_e('Coupon Code', 'woocommerce'); ?></label>
			<div class="flex gap-2">
				<input type="text" name="coupon_code" class="input-text !w-full !rounded-xl !border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-[#163300] placeholder:text-gray-300 focus:border-[#65cf21] focus:outline-none transition-colors" id="coupon_code" value="" placeholder="<?php esc_attr_e('Enter code', 'woocommerce'); ?>" />
				<button type="submit" class="button shrink-0 flex items-center justify-center !w-auto h-auto px-6 !rounded-xl !bg-[#163300] text-sm font-bold !text-white transition-colors hover:!bg-[#0a1700]" name="apply_coupon" value="<?php esc_attr_e('Apply', 'woocommerce'); ?>"><?php esc_html_e('Apply', 'woocommerce'); ?></button>
			</div>
			<?php do_action('woocommerce_cart_coupon'); ?>
			<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
		</form>
	<?php } ?>

	<div class="mt-4 flex flex-col gap-3 relative z-10">
		<!-- Custom button matching the design -->
		<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="w-full flex items-center justify-center gap-2 bg-[#163300] !text-white font-bold py-4 rounded-xl shadow-lg hover:bg-[#0a1700] transition-colors group">
			Proceed to Checkout
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:translate-x-1">
				<path d="M5 12h14" />
				<path d="m12 5 7 7-7 7" />
			</svg>
		</a>
		<div class="wc-proceed-to-checkout !hidden">
			<?php do_action('woocommerce_proceed_to_checkout'); ?>
		</div>
	</div>

	<!-- Payment Safe Footer -->
	<div class="mt-8 text-center pt-6 border-t border-gray-200/60">
		<span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-4">Guaranteed Safe Checkout</span>
		<div class="flex items-center justify-center gap-2 flex-wrap">
			<span class="bg-white border border-gray-200 rounded px-2 py-1 text-[10px] font-bold text-[#1434CB]">Visa</span>
			<span class="bg-white border border-gray-200 rounded px-2 py-1 text-[10px] font-bold text-[#EB001B]">Mastercard</span>
			<span class="bg-white border border-gray-200 rounded px-2 py-1 text-[10px] font-bold text-[#e40000]">PayFast</span>
			<span class="bg-black rounded px-2 py-1 text-[10px] font-bold text-white">PayJustNow</span>
		</div>
	</div>

	<?php do_action('woocommerce_after_cart_totals'); ?>

</div>