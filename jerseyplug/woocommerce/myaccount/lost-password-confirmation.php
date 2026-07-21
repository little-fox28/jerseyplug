<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/lost-password-confirmation.php.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="max-w-md mx-auto py-6 md:py-12 px-4">
	
	<!-- Single Interactive Card -->
	<div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden relative group text-center">
		
		<div class="p-8 md:p-10">
			
			<!-- Success Icon -->
			<div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-[#65cf21]/10 text-[#65cf21] mb-6 shadow-sm">
				<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
			</div>

			<h2 class="text-2xl font-black text-[#163300] uppercase tracking-wider mb-4">
				<?php echo esc_html( jerseyplug_pll( 'Email Sent!' ) ); ?>
			</h2>

			<p class="text-gray-500 text-sm font-medium leading-relaxed mb-8">
				<?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html( jerseyplug_pll( 'A password reset email has been sent to the email address on file for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.' ) ) ) ); ?>
			</p>

			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" style="color: #f2c86c !important; text-decoration: none !important;" class="w-full bg-[#163300] hover:bg-[#0f2400] font-black py-4 px-4 rounded-xl shadow-[0_4px_14px_0_rgba(22,51,0,0.39)] hover:shadow-[0_6px_20px_rgba(22,51,0,0.23)] hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm flex items-center justify-center gap-2">
				&larr; <?php echo esc_html( jerseyplug_pll( 'Return to Login' ) ); ?>
			</a>

		</div>
	</div>
</div>
