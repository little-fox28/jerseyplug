<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<div class="mb-8">
	<h3 class="text-2xl font-black text-[#163300] uppercase tracking-wider mb-2">
		<?php esc_html_e( 'Dashboard', 'woocommerce' ); ?>
	</h3>
	<p class="text-gray-500 text-sm">
		<?php
		printf(
			/* translators: 1: user display name 2: logout url */
			wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s" class="text-red-500 hover:underline">Log out</a>)', 'woocommerce' ), $allowed_html ),
			'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
			esc_url( wc_logout_url() )
		);
		?>
	</p>
	<p class="text-gray-500 text-sm mt-2">
		<?php
		/* translators: 1: Orders URL 2: Address URL 3: Account URL. */
		$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s" class="text-[#65cf21] font-bold hover:underline">recent orders</a>, manage your <a href="%2$s" class="text-[#65cf21] font-bold hover:underline">shipping and billing addresses</a>, and <a href="%3$s" class="text-[#65cf21] font-bold hover:underline">edit your password and account details</a>.', 'woocommerce' );
		if ( wc_shipping_enabled() ) {
			/* translators: 1: Orders URL 2: Addresses URL 3: Account URL. */
			$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s" class="text-[#65cf21] font-bold hover:underline">recent orders</a>, manage your <a href="%2$s" class="text-[#65cf21] font-bold hover:underline">shipping and billing addresses</a>, and <a href="%3$s" class="text-[#65cf21] font-bold hover:underline">edit your password and account details</a>.', 'woocommerce' );
		} elseif ( wc_tax_enabled() ) {
			/* translators: 1: Orders URL 2: Addresses URL 3: Account URL. */
			$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s" class="text-[#65cf21] font-bold hover:underline">recent orders</a>, manage your <a href="%2$s" class="text-[#65cf21] font-bold hover:underline">billing address</a>, and <a href="%3$s" class="text-[#65cf21] font-bold hover:underline">edit your password and account details</a>.', 'woocommerce' );
		}
		printf(
			wp_kses( $dashboard_desc, $allowed_html ),
			esc_url( wc_get_endpoint_url( 'orders' ) ),
			esc_url( wc_get_endpoint_url( 'edit-address' ) ),
			esc_url( wc_get_endpoint_url( 'edit-account' ) )
		);
		?>
	</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
	<!-- Orders Card -->
	<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="group block p-6 bg-gray-50 rounded-xl border border-gray-100 hover:border-[#65cf21] hover:bg-white hover:shadow-lg transition-all duration-300">
		<div class="w-12 h-12 bg-white rounded-lg shadow-sm flex items-center justify-center mb-4 text-[#163300] group-hover:scale-110 transition-transform">
			<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
				<path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
			</svg>
		</div>
		<h4 class="font-bold text-gray-900 group-hover:text-[#65cf21] transition-colors uppercase tracking-wider text-sm"><?php esc_html_e( 'Orders', 'woocommerce' ); ?></h4>
		<p class="text-xs text-gray-500 mt-1"><?php esc_html_e( 'View your recent orders', 'woocommerce' ); ?></p>
	</a>

	<!-- Addresses Card -->
	<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="group block p-6 bg-gray-50 rounded-xl border border-gray-100 hover:border-[#65cf21] hover:bg-white hover:shadow-lg transition-all duration-300">
		<div class="w-12 h-12 bg-white rounded-lg shadow-sm flex items-center justify-center mb-4 text-[#163300] group-hover:scale-110 transition-transform">
			<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
				<path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
				<path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
			</svg>
		</div>
		<h4 class="font-bold text-gray-900 group-hover:text-[#65cf21] transition-colors uppercase tracking-wider text-sm"><?php esc_html_e( 'Addresses', 'woocommerce' ); ?></h4>
		<p class="text-xs text-gray-500 mt-1"><?php esc_html_e( 'Manage shipping & billing', 'woocommerce' ); ?></p>
	</a>

	<!-- Account Details Card -->
	<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="group block p-6 bg-gray-50 rounded-xl border border-gray-100 hover:border-[#65cf21] hover:bg-white hover:shadow-lg transition-all duration-300">
		<div class="w-12 h-12 bg-white rounded-lg shadow-sm flex items-center justify-center mb-4 text-[#163300] group-hover:scale-110 transition-transform">
			<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
				<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
			</svg>
		</div>
		<h4 class="font-bold text-gray-900 group-hover:text-[#65cf21] transition-colors uppercase tracking-wider text-sm"><?php esc_html_e( 'Account Details', 'woocommerce' ); ?></h4>
		<p class="text-xs text-gray-500 mt-1"><?php esc_html_e( 'Edit password & profile', 'woocommerce' ); ?></p>
	</a>
</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );
	
	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );
	
	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );
?>
