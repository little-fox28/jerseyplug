<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 */

defined( 'ABSPATH' ) || exit;

// $order is provided by WooCommerce
if ( ! isset( $order ) || ! $order ) {
    return;
}

$notes = $order->get_customer_order_notes();
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
        <?php printf( esc_html__( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?>
    </h2>
    <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider
        <?php
            $status = $order->get_status();
            if ( in_array( $status, array( 'completed', 'processing' ) ) ) {
                echo 'bg-green-100 text-green-800';
            } elseif ( in_array( $status, array( 'on-hold', 'pending' ) ) ) {
                echo 'bg-yellow-100 text-yellow-800';
            } else {
                echo 'bg-red-100 text-red-800';
            }
        ?>
    ">
        <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
    </span>
</div>

<!-- LƯỚI NỘI DUNG CHÍNH (1 Cột giống thankyou.php) -->
<div class="flex flex-col gap-8 mt-2">

    <!-- PHẦN 1: THÔNG TIN KHÁCH HÀNG (Customer Info) -->
    <div class="flex flex-col gap-6">
        <div class="text-xl font-extrabold text-[#0a2310] uppercase tracking-wide"><?php esc_html_e('Customer Info', 'jerseyplug'); ?></div>

        <div class="bg-white border border-gray-200 rounded-[2rem] p-6 md:p-8 shadow-sm flex flex-col gap-8">

            <!-- Billing Address -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                    <div class="font-extrabold text-sm text-[#0a2310] uppercase tracking-wider"><?php esc_html_e('Billing Address', 'woocommerce'); ?></div>
                </div>
                <address class="text-sm text-gray-600 font-medium not-italic leading-relaxed">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>
                            <?php
                            $billing_address = $order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'));
                            echo wp_kses_post(str_replace(array('<br/>', '<br />', '<br>', "\n"), ', ', $billing_address));
                            ?>
                        </span>
                    </div>

                    <?php if ($order->get_billing_phone() || $order->get_billing_email()) : ?>
                        <div class="mt-3 flex flex-col gap-1 text-gray-500">
                            <?php if ($order->get_billing_phone()) : ?>
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span><?php echo esc_html($order->get_billing_phone()); ?></span>
                                </span>
                            <?php endif; ?>
                            <?php if ($order->get_billing_email()) : ?>
                                <span class="flex items-center gap-2 truncate">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="truncate" title="<?php echo esc_attr($order->get_billing_email()); ?>"><?php echo esc_html($order->get_billing_email()); ?></span>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </address>
            </div>

            <?php if (! wc_ship_to_billing_address_only() && $order->needs_shipping_address()) : ?>
                <!-- Ngăn cách -->
                <div class="w-full h-px bg-gray-100"></div>

                <!-- Shipping Address -->
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                        <div class="font-extrabold text-sm text-[#0a2310] uppercase tracking-wider"><?php esc_html_e('Shipping Address', 'woocommerce'); ?></div>
                    </div>
                    <address class="text-sm text-gray-600 font-medium not-italic leading-relaxed">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>
                                <?php
                                $shipping_address = $order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce'));
                                echo wp_kses_post(str_replace(array('<br/>', '<br />', '<br>', "\n"), ', ', $shipping_address));
                                ?>
                            </span>
                        </div>
                    </address>
                </div>
            <?php endif; ?>

            <!-- Ghi chú đơn hàng (Nếu có) -->
            <?php if ($order->get_customer_note()) : ?>
                <div>
                    <div class="w-full h-px bg-gray-100 mb-6"></div>
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <div class="font-extrabold text-sm text-[#0a2310] uppercase tracking-wider"><?php esc_html_e('Order Note', 'woocommerce'); ?></div>
                    </div>
                    <p class="text-sm font-medium text-gray-600 italic bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <?php echo wp_kses_post(nl2br(wptexturize($order->get_customer_note()))); ?>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- PHẦN 2: CHI TIẾT SẢN PHẨM & ĐƠN HÀNG (Order Details) -->
    <div class="flex flex-col gap-6">
        <div class="text-xl font-extrabold text-[#0a2310] uppercase tracking-wide"><?php esc_html_e('Order Details', 'woocommerce'); ?></div>

        <div class="bg-white border border-gray-200 rounded-[2rem] p-6 md:p-8 shadow-sm">

            <!-- Order Meta (Number, Date, Payment Method) -->
            <div class="flex flex-wrap gap-6 justify-between mb-8 pb-8 border-b border-gray-100">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest"><?php esc_html_e('Date', 'woocommerce'); ?></span>
                    <span class="font-bold text-slate-800 text-sm"><?php echo wc_format_datetime($order->get_date_created()); ?></span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest"><?php esc_html_e('Payment Method', 'woocommerce'); ?></span>
                    <span class="font-bold text-slate-800 text-sm"><?php echo wp_kses_post($order->get_payment_method_title()); ?></span>
                </div>
            </div>

            <!-- Danh sách Item -->
            <div class="flex flex-col gap-6 mb-6">
                <?php
                $order_items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
                foreach ($order_items as $item_id => $item) :
                    $product = $item->get_product();
                ?>
                    <div class="flex gap-4 items-center">
                        <!-- Ảnh sản phẩm -->
                        <div class="w-20 h-20 bg-gray-100 rounded-xl border border-gray-200 flex-shrink-0 overflow-hidden relative">
                            <?php
                            if ($product) {
                                echo $product->get_image('thumbnail', ['class' => 'w-full h-full object-cover mix-blend-multiply']);
                            }
                            ?>
                        </div>

                        <!-- Chi tiết -->
                        <div class="flex-1">
                            <div class="font-extrabold text-sm text-primary leading-snug mb-1 pr-4 line-clamp-2 sm:line-clamp-none">
                                <?php echo wp_kses_post(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false)); ?>
                            </div>

                            <!-- Order Item Meta (Size, Custom Name, etc) -->
                            <div class="flex flex-col gap-1 text-xs font-medium text-gray-500 mt-1.5 [&_p]:inline">
                                <?php
                                $meta_data = $item->get_formatted_meta_data('_', true);
                                if (! empty($meta_data)) {
                                    foreach ($meta_data as $meta_id => $meta) {
                                        $clean_value = str_replace(array('<p>', '</p>'), '', wp_kses_post($meta->display_value));
                                        echo '<span>' . wp_kses_post($meta->display_key) . ': <span class="text-gray-700">' . trim($clean_value) . '</span></span>';
                                    }
                                }
                                ?>
                                <span><?php esc_html_e('Qty', 'woocommerce'); ?>: <span class="text-gray-700"><?php echo esc_html($item->get_quantity()); ?></span></span>
                            </div>
                        </div>

                        <!-- Giá -->
                        <div class="font-extrabold text-[#0a2310] text-right">
                            <?php echo $order->get_formatted_line_subtotal($item); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="w-full h-px bg-gray-100 mb-6"></div>

            <!-- Bảng tính tiền -->
            <div class="flex flex-col gap-3 text-sm">
                <?php
                foreach ($order->get_order_item_totals() as $key => $total) :
                    // Map keys to specific styling if needed
                    $is_total = ($key === 'order_total');
                ?>
                    <?php if ($is_total) : ?>
                        <div class="w-full h-px bg-gray-100 my-2"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-base font-extrabold text-[#0a2310] uppercase"><?php echo esc_html(wp_strip_all_tags($total['label'])); ?></span>
                            <span class="text-2xl font-black text-primary"><?php echo wp_kses_post($total['value']); ?></span>
                        </div>
                    <?php else : ?>
                        <div class="flex justify-between text-gray-500 font-medium">
                            <span><?php echo esc_html(wp_strip_all_tags($total['label'])); ?></span>
                            <span class="text-slate-800 font-bold"><?php echo wp_kses_post($total['value']); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

        </div>

    </div>

    <!-- Order Updates (nếu có cập nhật đơn hàng) -->
    <?php if ( $notes ) : ?>
        <div class="flex flex-col gap-6 mt-4">
            <div class="text-xl font-extrabold text-[#0a2310] uppercase tracking-wide"><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></div>
            
            <div class="bg-white border border-gray-200 rounded-[2rem] p-6 shadow-sm">
                <ol class="flex flex-col gap-4">
                    <?php foreach ( $notes as $note ) : ?>
                    <li class="pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                        <div class="text-xs font-bold text-gray-400 mb-2"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                        <div class="text-sm text-gray-700">
                            <?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Execute the native hook just in case plugins need to inject something.
// Wrapped in display:none to prevent duplicate tables since we rendered everything beautifully above.
echo '<div style="display:none;">';
do_action( 'woocommerce_view_order', $order_id );
echo '</div>';
?>
