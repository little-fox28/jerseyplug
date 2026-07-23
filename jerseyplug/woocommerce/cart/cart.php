<?php

/**
 * Cart Page
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');

// Calculate Free Shipping Progress dynamically from WooCommerce Shipping Zones
$shipping_threshold = 0;

if (function_exists('WC') && WC()->cart && WC()->customer) {
	$packages = WC()->shipping()->get_packages();
	if (! empty($packages)) {
		$package = $packages[0];

		// Attempt to get the shipping zone matching the current customer package
		$zone = WC_Shipping_Zones::get_zone_matching_package($package);
		if (! $zone || ! $zone->get_id()) {
			// Fallback to "Rest of the World" zone if no specific location is matched
			$zone = new WC_Shipping_Zone(0);
		}

		$methods = $zone->get_shipping_methods();
		foreach ($methods as $method) {
			if ($method->id === 'free_shipping' && in_array($method->requires, array('min_amount', 'either', 'both'), true)) {
				$shipping_threshold = (float) $method->min_amount;
				break;
			}
		}
	}
}

$subtotal = WC()->cart->get_subtotal();
$amount_to_free_shipping = max(0, $shipping_threshold - $subtotal);
$progress_percentage = $shipping_threshold > 0 ? min(100, ($subtotal / $shipping_threshold) * 100) : 0;
?>

<div class="animate-in slide-in-from-left duration-300 mx-auto py-8">

	<!-- Title and Progress in main column -->
	<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

		<!-- Left Column: Cart Items -->
		<div class="lg:col-span-6 xl:col-span-7">
			<style>
				/* Hide default theme title "Cart" */
				article>header,
				.entry-header,
				.page-title {
					display: none !important;
				}
			</style>
			<div class="mb-6">
				<a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#163300] transition-colors">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<path d="m15 18-6-6 6-6" />
					</svg>
					<?php echo esc_html(jerseyplug_pll('Continue Shopping')); ?>
				</a>
			</div>
			<h1 class="text-3xl font-bold text-[#163300] mb-8"><?php echo esc_html(jerseyplug_pll('Shopping Cart')); ?></h1>

			<!-- Cart Table Form -->
			<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
				<?php do_action('woocommerce_before_cart_table'); ?>

				<?php if ($shipping_threshold > 0) : ?>
					<!-- Free Shipping Progress -->
					<div class="mb-8 bg-gray-50 p-4 rounded-xl border border-gray-200">
						<?php if ($amount_to_free_shipping > 0) : ?>
							<p class="text-sm text-gray-600 mb-2">
								Spend
								<span class="font-bold text-[#163300]">
									<?php echo wc_price($amount_to_free_shipping); ?>
								</span>
								more for
								<span class="text-[#65cf21] font-bold uppercase"><?php echo esc_html(jerseyplug_pll('Free Delivery')); ?></span>
							</p>
						<?php else : ?>
							<p class="text-sm text-[#163300] font-bold mb-2 flex items-center gap-2">
								<svg class="w-4 h-4 text-[#65cf21]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M10 17h4V5H2v12h3" />
									<path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5" />
									<path d="M14 17h1" />
									<circle cx="7.5" cy="17.5" r="2.5" />
									<circle cx="17.5" cy="17.5" r="2.5" />
								</svg>
								<?php echo esc_html(jerseyplug_pll('You\'ve unlocked Free Delivery!')); ?>
							</p>
						<?php endif; ?>
						<div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
							<div class="h-full bg-[#65cf21] transition-all duration-500" style="width: <?php echo esc_attr($progress_percentage); ?>%;"></div>
						</div>
					</div>
				<?php endif; ?>

				<div class="border-t border-gray-100">
					<!-- Removed shop_table to prevent WooCommerce default CSS from overriding our Tailwind layout -->
					<table class="cart woocommerce-cart-form__contents w-full" cellspacing="0">
						<!-- Hide header but keep for WC JS compatibility -->
						<thead class="sr-only">
							<tr>
								<th class="product-remove"><?php echo esc_html(jerseyplug_pll('Remove')); ?></th>
								<th class="product-thumbnail"><?php echo esc_html(jerseyplug_pll('Image')); ?></th>
								<th class="product-name"><?php echo esc_html(jerseyplug_pll('Product')); ?></th>
								<th class="product-price"><?php echo esc_html(jerseyplug_pll('Price')); ?></th>
								<th class="product-quantity"><?php echo esc_html(jerseyplug_pll('Quantity')); ?></th>
								<th class="product-subtotal"><?php echo esc_html(jerseyplug_pll('Subtotal')); ?></th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100">
							<?php do_action('woocommerce_before_cart_contents'); ?>

							<?php
							foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
								$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
								$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
								$visible    = apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key);

								if ($_product instanceof WC_Product && $_product->exists() && $cart_item['quantity'] > 0 && $visible) {
									$product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
									$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
							?>
									<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item !flex flex-row !py-6 relative gap-4 sm:gap-6 border-b border-gray-100', $cart_item, $cart_item_key)); ?>">

										<!-- Image column -->
										<td class="product-thumbnail !w-24 sm:!w-32 shrink-0 !p-0 !border-none !block text-left before:hidden">
											<?php
											$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail', array('class' => '!w-full !max-w-full !h-auto !object-cover !rounded-xl !bg-gray-50 !shadow-none')), $cart_item, $cart_item_key);

											if (! $product_permalink) {
												echo $thumbnail;
											} else {
												printf('<a href="%s" class="block">%s</a>', esc_url($product_permalink), $thumbnail);
											}
											?>
										</td>

										<!-- Content Column -->
										<td class="product-info flex-1 !flex flex-col justify-between !p-0 !border-none !block before:hidden">
											<div class="flex justify-between items-start gap-4">
												<!-- Name and Meta -->
												<div class="product-name" data-title="<?php echo esc_attr(jerseyplug_pll('Product')); ?>">
													<?php
													if (! $product_permalink) {
														echo wp_kses_post('<span class="font-bold text-[#163300] text-sm sm:text-base leading-snug block">' . $product_name . '</span>');
													} else {
														echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="font-bold text-[#163300] text-sm sm:text-base leading-snug hover:text-[#65cf21] transition-colors block">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
													}

													do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

													// Meta data (Size, Print, etc)
													echo '<div class="mt-2 text-xs text-gray-500 [&>dl]:m-0 [&>dl]:grid [&>dl]:grid-cols-[max-content_1fr] [&>dl]:items-center [&>dl]:gap-x-2 [&>dl]:gap-y-1 [&>dl>dt]:font-bold [&>dl>dd]:!m-0 [&>dl>dt]:!m-0 [&>dl>dt]:self-center [&>dl>dd]:self-center">';
													$meta_data = wc_get_formatted_cart_item_data($cart_item);
													echo str_replace(['<p>', '</p>'], '', $meta_data);
													echo '</div>';

													// Backorder
													if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
														echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification text-xs text-orange-500 mt-1">' . esc_html(jerseyplug_pll('Available on backorder')) . '</p>', $product_id));
													}
													?>
												</div>

												<!-- Remove Button -->
												<div class="product-remove shrink-0">
													<?php
													echo apply_filters(
														'woocommerce_cart_item_remove_link',
														sprintf(
															'<a href="%s" class="remove !inline-flex !items-center !justify-center !w-8 !h-8 !rounded-full text-gray-300 hover:!text-red-500 hover:!bg-red-50 transition-colors" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></a>',
															esc_url(wc_get_cart_remove_url($cart_item_key)),
															esc_attr(sprintf(jerseyplug_pll('Remove %s from cart'), wp_strip_all_tags($product_name))),
															esc_attr($product_id),
															esc_attr($_product->get_sku())
														),
														$cart_item_key
													);
													?>
												</div>
											</div>

											<!-- Quantity & Price -->
											<div class="flex justify-between items-end mt-4">
												<!-- Quantity -->
												<div class="product-quantity" data-title="<?php echo esc_attr(jerseyplug_pll('Quantity')); ?>">
													<?php
													if ($_product->is_sold_individually()) {
														$min_quantity = 1;
														$max_quantity = 1;
													} else {
														$min_quantity = 0;
														$max_quantity = $_product->get_max_purchase_quantity();
													}

													$product_quantity = woocommerce_quantity_input(
														array(
															'input_name'   => "cart[{$cart_item_key}][qty]",
															'input_value'  => $cart_item['quantity'],
															'max_value'    => $max_quantity,
															'min_value'    => $min_quantity,
															'product_name' => $product_name,
															'classes'      => array('!w-16', '!text-center', '!font-bold', 'text-[#163300]', '!border-none', '!bg-transparent', 'focus:outline-none', '!py-1', '!px-0', 'appearance-none'),
														),
														$_product,
														false
													);

													echo '<div class="flex items-center border border-gray-200 rounded-lg h-9 overflow-hidden">';
													echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
													echo '</div>';
													?>
												</div>

												<!-- Subtotal -->
												<div class="product-subtotal font-black text-lg text-[#163300]" data-title="<?php echo esc_attr(jerseyplug_pll('Subtotal')); ?>">
													<?php
													echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
													?>
												</div>
											</div>

											<!-- Hidden price cell to keep WC happy -->
											<div class="product-price hidden" data-title="<?php echo esc_attr(jerseyplug_pll('Price')); ?>">
												<?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
											</div>
										</td>
									</tr>
							<?php
								}
							}
							?>

							<?php do_action('woocommerce_cart_contents'); ?>

							<tr class="!block w-full !border-none pt-6">
								<td colspan="6" class="actions !block !p-0 !border-none before:hidden">

									<div class="flex flex-col sm:flex-row justify-end items-center gap-4">
										<style>
											button[name="update_cart"]:disabled {
												display: none !important;
											}
										</style>
										<button type="submit" class="button !w-full sm:!w-auto h-11 px-6 !rounded-xl !border border-gray-200 !bg-white text-sm font-bold !text-[#163300] transition-colors hover:!border-[#65cf21]" name="update_cart" value="<?php echo esc_attr(jerseyplug_pll('Update cart')); ?>" disabled><?php echo esc_html(jerseyplug_pll('Update cart')); ?></button>
									</div>

									<?php do_action('woocommerce_cart_actions'); ?>
									<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
								</td>
							</tr>

							<?php do_action('woocommerce_after_cart_contents'); ?>
						</tbody>
					</table>
				</div>
				<?php do_action('woocommerce_after_cart_table'); ?>
			</div>

			<!-- Right Column: Cart Totals -->
			<div class="lg:col-span-6 xl:col-span-5">
				<div class="cart-collaterals lg:sticky lg:top-24">
					<?php
					/**
					 * Cart collaterals hook.
					 *
					 * @hooked woocommerce_cross_sell_display
					 * @hooked woocommerce_cart_totals - 10
					 */
					do_action('woocommerce_cart_collaterals');
					?>
				</div>
			</div>
		</div>
	</form>
</div>

<?php do_action('woocommerce_after_cart'); ?>