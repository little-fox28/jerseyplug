<?php

/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.5.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_account_orders', $has_orders); ?>

<div class="overflow-hidden">
	<h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-6"><?php esc_html_e('Order History', 'woocommerce'); ?></h2>

	<?php if ($has_orders) : ?>
		<div class="overflow-x-auto">
			<table class="woocommerce-orders-table w-full min-w-[600px] text-left text-sm whitespace-nowrap">
				<thead>
					<tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
						<?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) :
							if ('order-actions' === $column_id) continue;
						?>
							<th scope="col" class="py-4 px-4"><span class="nobr"><?php echo esc_html($column_name); ?></span></th>
						<?php endforeach; ?>
					</tr>
				</thead>

				<tbody class="divide-y divide-gray-100">
					<?php
					foreach ($customer_orders->orders as $customer_order) {
						$order      = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						$item_count = $order->get_item_count() - $order->get_item_count_refunded();
					?>
						<tr class="hover:bg-gray-50 transition-colors group cursor-pointer" onclick="window.location.href='<?php echo esc_url($order->get_view_order_url()); ?>'">
							<?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) :
								if ('order-actions' === $column_id) continue;
								$is_order_number = 'order-number' === $column_id;
							?>
								<?php if ($is_order_number) : ?>
									<th class="py-5 px-4 font-extrabold text-slate-900" data-title="<?php echo esc_attr($column_name); ?>" scope="row">
									<?php else : ?>
									<td class="py-5 px-4 text-gray-600 font-medium" data-title="<?php echo esc_attr($column_name); ?>">
									<?php endif; ?>

									<?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)) : ?>
										<?php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>

									<?php elseif ($is_order_number) : ?>
										<span class="text-primary group-hover:underline">
											<?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
										</span>

									<?php elseif ('order-date' === $column_id) : ?>
										<time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></time>

									<?php elseif ('order-status' === $column_id) : ?>
										<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
											<?php
											$status = $order->get_status();
											if (in_array($status, array('completed', 'processing'))) {
												echo 'bg-green-50 text-green-700';
											} elseif (in_array($status, array('on-hold', 'pending'))) {
												echo 'bg-yellow-50 text-yellow-700';
											} else {
												echo 'bg-red-50 text-red-700';
											}
											?>
										">
											<?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
										</span>

									<?php elseif ('order-total' === $column_id) : ?>
										<?php
										echo wp_kses_post(sprintf(_n('%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce'), '<span class="font-bold text-slate-900">' . $order->get_formatted_order_total() . '</span>', $item_count));
										?>
									<?php endif; ?>

									<?php if ($is_order_number) : ?>
										</th>
									<?php else : ?>
									</td>
								<?php endif; ?>
							<?php endforeach; ?>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>

		<?php do_action('woocommerce_before_account_orders_pagination'); ?>

		<?php if (1 < $customer_orders->max_num_pages) : ?>
			<div class="mt-8 flex justify-center gap-2">
				<?php if (1 !== $current_page) : ?>
					<a class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-bold text-gray-600 hover:bg-gray-50" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>"><?php esc_html_e('Previous', 'woocommerce'); ?></a>
				<?php endif; ?>

				<?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
					<a class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-bold text-gray-600 hover:bg-gray-50" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>"><?php esc_html_e('Next', 'woocommerce'); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	<?php else : ?>

		<!-- Empty State (Mô phỏng như Prototype) -->
		<div class="text-center py-10">
			<div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
				<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
				</svg>
			</div>
			<h3 class="text-xl font-extrabold text-slate-900 mb-2"><?php esc_html_e('No orders has been made yet.', 'woocommerce'); ?></h3>
			<p class="text-gray-500 font-medium mb-6"><?php esc_html_e('Browse our latest collection and gear up for the season.', 'jerseyplug'); ?></p>
			<a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>" class="inline-block bg-primary text-[#f2c86c] rounded-full py-3 px-8 font-extrabold text-sm uppercase tracking-widest hover:opacity-90 transition-opacity"><?php esc_html_e('Go to shop', 'woocommerce'); ?></a>
		</div>

	<?php endif; ?>
</div>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?>