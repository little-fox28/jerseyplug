<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation flex flex-col p-4 space-y-1">
	<div class="px-4 py-3 mb-2 flex items-center gap-3 border-b border-gray-100">
		<div class="w-10 h-10 rounded-full bg-[#f2c86c] text-[#163300] flex items-center justify-center font-black text-lg">
			<?php 
			$current_user = wp_get_current_user();
			echo esc_html( strtoupper( substr( $current_user->display_name, 0, 1 ) ) ); 
			?>
		</div>
		<div class="flex flex-col">
			<span class="text-xs text-gray-500 font-bold uppercase tracking-wider"><?php esc_html_e( 'Welcome back', 'woocommerce' ); ?></span>
			<span class="text-sm font-black text-gray-900 truncate max-w-[150px]"><?php echo esc_html( $current_user->display_name ); ?></span>
		</div>
	</div>

	<ul class="space-y-1">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : 
			$classes = wc_get_account_menu_item_classes( $endpoint );
			$is_active = strpos( $classes, 'is-active' ) !== false;
			$link_class = $is_active ? 'bg-[#163300] text-[#f2c86c] shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#163300]';
		?>
			<li class="<?php echo esc_attr( $classes ); ?> rounded-lg overflow-hidden transition-colors">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="block px-4 py-3 text-sm font-bold transition-all <?php echo esc_attr( $link_class ); ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
