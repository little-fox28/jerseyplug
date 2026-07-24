<?php
/**
 * Shipping Methods Display Override
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.8.0
 */

defined('ABSPATH') || exit;

$formatted_destination    = isset($formatted_destination) ? $formatted_destination : WC()->countries->get_formatted_address($package['destination'], ', ');
$has_calculated_shipping  = ! empty($has_calculated_shipping);
$show_shipping_calculator = ! empty($show_shipping_calculator);
$calculator_text          = '';
?>
<tr class="woocommerce-shipping-totals shipping border-b border-gray-200/60">
	<th class="py-3 text-left font-normal text-gray-500 !bg-transparent"><?php echo esc_html( jerseyplug_pll( $package_name ) ); ?></th>
	<td class="py-3 text-right font-bold text-[#163300] !bg-transparent [&>ul]:m-0 [&>ul]:p-0 [&>ul>li]:m-0 [&>ul>li]:p-0 [&>ul>li]:list-none" data-title="<?php echo esc_attr( jerseyplug_pll( $package_name ) ); ?>">
		<?php if (! empty($available_methods) && is_array($available_methods)) : ?>
			<ul id="shipping_method" class="woocommerce-shipping-methods">
				<?php foreach ($available_methods as $method) : ?>
					<li>
						<?php
						if (1 < count($available_methods)) {
							printf('<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />', $index, esc_attr(sanitize_title($method->id)), esc_attr($method->id), checked($method->id, $chosen_method, false));
						} else {
							printf('<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" />', $index, esc_attr(sanitize_title($method->id)), esc_attr($method->id));
						}
						printf('<label for="shipping_method_%1$s_%2$s">%3$s</label>', $index, esc_attr(sanitize_title($method->id)), wc_cart_totals_shipping_method_label($method));
						do_action('woocommerce_after_shipping_rate', $method, $index);
						?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if (is_cart()) : ?>
				<p class="woocommerce-shipping-destination text-xs font-normal text-gray-500 mt-2">
					<?php
					if ($formatted_destination) {
						printf(esc_html( jerseyplug_pll( 'Shipping to %s.' ) ) . ' ', '<strong class="font-bold text-[#163300]">' . esc_html($formatted_destination) . '</strong>');
						$calculator_text = jerseyplug_pll('Change address');
					} else {
						echo wp_kses_post(apply_filters('woocommerce_shipping_estimate_html', jerseyplug_pll('Shipping options will be updated during checkout.')));
					}
					?>
				</p>
			<?php endif; ?>
		<?php
		elseif (! $has_calculated_shipping || ! $formatted_destination) :
			if (is_cart() && 'no' === get_option('woocommerce_enable_shipping_calc')) {
				echo wp_kses_post(apply_filters('woocommerce_shipping_not_enabled_on_cart_html', jerseyplug_pll('Shipping costs are calculated during checkout.')));
			} else {
				echo wp_kses_post(apply_filters('woocommerce_shipping_may_be_available_html', jerseyplug_pll('Enter your address to view shipping options.')));
			}
		elseif (! is_cart()) :
			echo wp_kses_post(apply_filters('woocommerce_no_shipping_available_html', jerseyplug_pll('There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.')));
		else :
			echo wp_kses_post(
				apply_filters(
					'woocommerce_cart_no_shipping_available_html',
					sprintf(esc_html( jerseyplug_pll( 'No shipping options were found for %s.' ) ) . ' ', '<strong>' . esc_html($formatted_destination) . '</strong>'),
					$formatted_destination
				)
			);
			$calculator_text = jerseyplug_pll('Enter a different address');
		endif;
		?>

		<?php if ($show_package_details) : ?>
			<?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html($package_details) . '</small></p>'; ?>
		<?php endif; ?>

		<?php if ($show_shipping_calculator) : ?>
			<?php woocommerce_shipping_calculator($calculator_text); ?>
		<?php endif; ?>
	</td>
</tr>
