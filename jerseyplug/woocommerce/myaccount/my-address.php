<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => jerseyplug_pll( 'Billing address' ),
			'shipping' => jerseyplug_pll( 'Shipping address' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => jerseyplug_pll( 'Billing address' ),
		),
		$customer_id
	);
}
?>

<div class="bg-white border border-gray-200 rounded-[2rem] p-6 md:p-10 shadow-sm overflow-hidden">
    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight mb-3"><?php echo esc_html( jerseyplug_pll( 'Addresses' ) ); ?></h2>
    <p class="text-gray-500 font-medium mb-8">
        <?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html( jerseyplug_pll( 'The following addresses will be used on the checkout page by default.' ) ) ); ?>
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php foreach ( $get_addresses as $name => $address_title ) : ?>
            <?php
                $address = wc_get_account_formatted_address( $name );
            ?>
            <div class="flex flex-col border border-gray-100 rounded-2xl p-6 bg-gray-50/50 relative group hover:border-gray-200 transition-colors">
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
                    <h3 class="font-extrabold text-lg text-slate-900"><?php echo esc_html( $address_title ); ?></h3>
                    <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="text-primary font-bold text-sm hover:underline flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        <?php echo $address ? esc_html( jerseyplug_pll( 'Edit' ) ) : esc_html( jerseyplug_pll( 'Add' ) ); ?>
                    </a>
                </div>
                <address class="text-gray-600 not-italic font-medium text-sm leading-relaxed flex-1">
                    <?php
                        echo $address ? wp_kses_post( str_replace(array('<br/>', '<br />', '<br>', "\n"), ', ', $address) ) : esc_html( jerseyplug_pll( 'You have not set up this type of address yet.' ) );
                        do_action( 'woocommerce_my_account_after_my_address', $name );
                    ?>
                </address>
            </div>
        <?php endforeach; ?>
    </div>
</div>
