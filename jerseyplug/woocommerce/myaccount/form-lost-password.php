<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
$translate = function_exists('jerseyplug_pll') ? 'jerseyplug_pll' : '__';
?>

<div class="max-w-md mx-auto py-6 md:py-12 px-4">
	
	<!-- Single Interactive Card -->
	<div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden relative group">
		
		<div class="p-8 md:p-10">
			
			<h2 class="text-2xl font-black text-[#163300] uppercase tracking-wider mb-4 flex items-center gap-3">
				<svg class="w-7 h-7 text-[#f2c86c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
				<?php esc_html_e( 'Lost password', 'woocommerce' ); ?>
			</h2>

			<p class="text-gray-500 text-sm font-medium leading-relaxed mb-8">
				<?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?>
			</p>

			<form method="post" class="woocommerce-ResetPassword lost_reset_password space-y-6">

				<div>
					<label for="user_login" class="block text-sm font-bold text-gray-700 mb-1.5"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?>&nbsp;<span class="text-red-500">*</span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50 focus:bg-white focus:border-[#65cf21] focus:ring-0 transition-all outline-none text-gray-800 font-medium" type="text" name="user_login" id="user_login" autocomplete="username" required />
				</div>

				<div class="clear"></div>

				<?php do_action( 'woocommerce_lostpassword_form' ); ?>

				<div>
					<input type="hidden" name="wc_reset_password" value="true" />
					<button type="submit" class="woocommerce-Button button w-full bg-[#163300] hover:bg-[#0f2400] text-[#f2c86c] font-black py-4 px-4 rounded-xl shadow-[0_4px_14px_0_rgba(22,51,0,0.39)] hover:shadow-[0_6px_20px_rgba(22,51,0,0.23)] hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm flex items-center justify-center gap-2" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>">
						<?php esc_html_e( 'Reset password', 'woocommerce' ); ?>
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
					</button>
				</div>

				<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

			</form>
			
			<p class="text-center text-sm font-medium text-gray-500 mt-8 pt-6 border-t border-gray-100">
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="text-[#163300] font-bold hover:text-[#65cf21] hover:underline transition-colors focus:outline-none">
					&larr; <?php esc_html_e( 'Back to Login', 'woocommerce' ); ?>
				</a>
			</p>
		</div>
	</div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_form' );
