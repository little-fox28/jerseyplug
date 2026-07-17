<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); 
$translate = function_exists('jerseyplug_pll') ? 'jerseyplug_pll' : '__';
$enable_registration = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
?>

<div class="max-w-md mx-auto py-2 md:py-12 px-4" id="customer_login" x-data="{ view: 'login', showLoginPass: false, showRegPass: false }">
	
	<!-- Single Interactive Card -->
	<div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden relative group">
		
		<div class="p-8 md:p-10">
			
			<!-- Interactive Toggle Switch -->
			<?php if ( $enable_registration ) : ?>
			<div class="bg-gray-100 p-1.5 rounded-2xl flex items-center mb-8 relative">
				<!-- Sliding background indicator -->
				<div class="absolute top-1.5 left-1.5 bottom-1.5 w-[calc(50%-6px)] bg-white rounded-xl shadow-md transition-all duration-500 ease-spring"
					 :class="view === 'login' ? 'translate-x-0' : 'translate-x-full'">
				</div>
				
				<!-- Login Button -->
				<button type="button" @click="view = 'login'" 
					class="relative z-10 w-1/2 py-2.5 text-sm font-bold uppercase tracking-wider transition-colors duration-300 rounded-xl flex items-center justify-center gap-2"
					:class="view === 'login' ? 'text-[#163300]' : 'text-gray-400 hover:text-gray-600'">
					<svg class="w-5 h-5" :class="view === 'login' ? 'text-[#f2c86c]' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
					<?php esc_html_e( 'Login', 'woocommerce' ); ?>
				</button>

				<!-- Register Button -->
				<button type="button" @click="view = 'register'" 
					class="relative z-10 w-1/2 py-2.5 text-sm font-bold uppercase tracking-wider transition-colors duration-300 rounded-xl flex items-center justify-center gap-2"
					:class="view === 'register' ? 'text-[#163300]' : 'text-gray-400 hover:text-gray-600'">
					<svg class="w-5 h-5" :class="view === 'register' ? 'text-[#65cf21]' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
					<?php esc_html_e( 'Register', 'woocommerce' ); ?>
				</button>
			</div>
			<?php else : ?>
			<h2 class="text-2xl font-black text-[#163300] uppercase tracking-wider mb-6 flex items-center justify-center gap-3">
				<svg class="w-6 h-6 text-[#f2c86c]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
				<?php esc_html_e( 'Login', 'woocommerce' ); ?>
			</h2>
			<?php endif; ?>

			<!-- Forms Container (Relative for absolute positioning of fading elements) -->
			<div class="relative min-h-[350px]">
				
				<!-- Login Form Area -->
				<div x-show="view === 'login'" 
					 x-transition:enter="transition ease-out duration-500 delay-100"
					 x-transition:enter-start="opacity-0 -translate-x-8"
					 x-transition:enter-end="opacity-100 translate-x-0"
					 x-transition:leave="transition ease-in duration-300 absolute top-0 left-0 w-full"
					 x-transition:leave-start="opacity-100 translate-x-0"
					 x-transition:leave-end="opacity-0 -translate-x-8"
					 class="w-full">
					
					<form class="woocommerce-form woocommerce-form-login login space-y-5" method="post">
						<?php do_action( 'woocommerce_login_form_start' ); ?>
						<div>
							<label for="username" class="block text-sm font-bold text-gray-700 mb-1.5"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="text-red-500">*</span></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50 focus:bg-white focus:border-[#65cf21] focus:ring-0 transition-all outline-none text-gray-800 font-medium" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required />
						</div>

						<div>
							<label for="password" class="block text-sm font-bold text-gray-700 mb-1.5"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="text-red-500">*</span></label>
							<div class="relative">
								<input type="password" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3.5 pr-12 rounded-xl border-2 border-gray-100 bg-gray-50 focus:bg-white focus:border-[#65cf21] focus:ring-0 transition-all outline-none text-gray-800 font-medium" :type="showLoginPass ? 'text' : 'password'" name="password" id="password" autocomplete="current-password" required />
								<button type="button" @click="showLoginPass = !showLoginPass" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#163300] focus:outline-none transition-colors" title="<?php esc_attr_e( 'Toggle password visibility', 'woocommerce' ); ?>">
									<svg x-show="!showLoginPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
									<svg x-show="showLoginPass" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
								</button>
							</div>
						</div>
						<?php do_action( 'woocommerce_login_form' ); ?>

						<div class="flex items-center justify-between pt-2">
							<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme inline-flex items-center cursor-pointer group">
								<input class="woocommerce-form__input woocommerce-form__input-checkbox w-5 h-5 text-[#65cf21] border-2 border-gray-300 rounded focus:ring-[#65cf21] cursor-pointer" name="rememberme" type="checkbox" id="rememberme" value="forever" />
								<span class="ml-2.5 text-sm font-bold text-gray-600 group-hover:text-gray-900 transition-colors"><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
							</label>
							
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-sm font-bold text-[#163300] hover:text-[#65cf21] transition-colors"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
						</div>

						<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
						
						<button type="submit" class="woocommerce-button button woocommerce-form-login__submit w-full mt-6 bg-[#163300] hover:bg-[#0f2400] text-[#f2c86c] font-black py-4 px-4 rounded-xl shadow-[0_4px_14px_0_rgba(22,51,0,0.39)] hover:shadow-[0_6px_20px_rgba(22,51,0,0.23)] hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm flex items-center justify-center gap-2" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>">
							<?php esc_html_e( 'Log in', 'woocommerce' ); ?>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
						</button>

						<?php if ( $enable_registration ) : ?>
						<p class="text-center text-sm font-medium text-gray-500 mt-6 pt-5 border-t border-gray-100">
							<?php echo esc_html( $translate( "Don't have an account yet?" ) ); ?>
							<button type="button" @click="view = 'register'" class="text-[#163300] font-bold hover:text-[#65cf21] hover:underline transition-colors ml-1 focus:outline-none">
								<?php echo esc_html( $translate( 'Register now' ) ); ?> &rarr;
							</button>
						</p>
						<?php endif; ?>

						<?php do_action( 'woocommerce_login_form_end' ); ?>
					</form>
				</div>

				<?php if ( $enable_registration ) : ?>
				<!-- Register Form Area -->
				<div x-show="view === 'register'" 
					 x-transition:enter="transition ease-out duration-500 delay-100"
					 x-transition:enter-start="opacity-0 translate-x-8"
					 x-transition:enter-end="opacity-100 translate-x-0"
					 x-transition:leave="transition ease-in duration-300 absolute top-0 left-0 w-full"
					 x-transition:leave-start="opacity-100 translate-x-0"
					 x-transition:leave-end="opacity-0 translate-x-8"
					 class="w-full" style="display: none;">
					
					<form method="post" class="woocommerce-form woocommerce-form-register register space-y-5" <?php do_action( 'woocommerce_register_form_tag' ); ?> x-data="{ email: '<?php echo esc_js( ! empty( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '' ); ?>' }">
						<?php do_action( 'woocommerce_register_form_start' ); ?>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
							<div>
								<label for="reg_username" class="block text-sm font-bold text-gray-700 mb-1.5"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="text-red-500">*</span></label>
								<input type="text" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50 focus:bg-white focus:border-[#65cf21] focus:ring-0 transition-all outline-none text-gray-800 font-medium" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required />
							</div>
						<?php endif; ?>

						<div>
							<label for="reg_email" class="block text-sm font-bold text-gray-700 mb-1.5"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="text-red-500">*</span></label>
							<input type="email" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50 focus:bg-white focus:border-[#65cf21] focus:ring-0 transition-all outline-none text-gray-800 font-medium" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" x-model="email" required />
						</div>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
							<div>
								<label for="reg_password" class="block text-sm font-bold text-gray-700 mb-1.5"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="text-red-500">*</span></label>
								<div class="relative">
									<input type="password" :type="showRegPass ? 'text' : 'password'" class="woocommerce-Input woocommerce-Input--text input-text w-full px-5 py-3.5 pr-12 rounded-xl border-2 border-gray-100 bg-gray-50 focus:bg-white focus:border-[#65cf21] focus:ring-0 transition-all outline-none text-gray-800 font-medium" name="password" id="reg_password" autocomplete="new-password" required />
									<button type="button" @click="showRegPass = !showRegPass" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#163300] focus:outline-none transition-colors" title="<?php esc_attr_e( 'Toggle password visibility', 'woocommerce' ); ?>">
										<svg x-show="!showRegPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
										<svg x-show="showRegPass" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
									</button>
								</div>
							</div>
						<?php else : ?>
							<div x-show="email.length > 0" 
								 x-transition:enter="transition ease-out duration-300"
								 x-transition:enter-start="opacity-0 -translate-y-2"
								 x-transition:enter-end="opacity-100 translate-y-0"
								 x-transition:leave="transition ease-in duration-200"
								 x-transition:leave-start="opacity-100"
								 x-transition:leave-end="opacity-0"
								 class="bg-blue-50/50 border border-blue-100 p-4 rounded-xl flex gap-3 items-start" style="display: none;">
								<svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
								<p class="text-sm text-blue-800 font-medium leading-relaxed">
									<?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?>
								</p>
							</div>
						<?php endif; ?>

						<?php do_action( 'woocommerce_register_form' ); ?>

						<!-- HONEYPOT FIELD (Anti-Spam) -->
						<div style="position: absolute; left: -9999px;" aria-hidden="true">
							<label for="trap_email">Leave this field empty if you're human: </label>
							<input type="text" name="trap_email" id="trap_email" value="" tabindex="-1" autocomplete="off" />
						</div>

						<div class="pt-2">
							<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
							<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit w-full mt-4 bg-[#163300] hover:bg-[#0f2400] text-[#65cf21] font-black py-4 px-4 rounded-xl shadow-[0_4px_14px_0_rgba(22,51,0,0.39)] hover:shadow-[0_6px_20px_rgba(22,51,0,0.23)] hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest text-sm flex items-center justify-center gap-2" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>">
								<?php esc_html_e( 'Register', 'woocommerce' ); ?>
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
							</button>
						</div>

						<p class="text-center text-sm font-medium text-gray-500 mt-6 pt-5 border-t border-gray-100">
							<?php echo esc_html( $translate( 'Already have an account?' ) ); ?>
							<button type="button" @click="view = 'login'" class="text-[#163300] font-bold hover:text-[#65cf21] hover:underline transition-colors ml-1 focus:outline-none">
								&larr; <?php esc_html_e( 'Log in', 'woocommerce' ); ?>
							</button>
						</p>

						<?php do_action( 'woocommerce_register_form_end' ); ?>
					</form>
				</div>
				<?php endif; ?>
				
			</div> <!-- /Forms Container -->

		</div>
	</div>
</div>

<style>
/* Custom easing for the toggle switch */
.ease-spring {
	transition-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Highlight Privacy Policy Text */
.woocommerce-privacy-policy-text {
	font-size: 0.75rem;
	line-height: 1.6;
	color: #4b5563;
	background-color: #f8fafc;
	padding: 1rem;
	border-radius: 0.75rem;
	border-left: 4px solid #f2c86c;
	margin-top: 1.25rem !important;
	margin-bottom: 0 !important;
}
.woocommerce-privacy-policy-text a {
	color: #163300;
	font-weight: 800;
	text-decoration: underline;
	transition: color 0.2s;
}
.woocommerce-privacy-policy-text a:hover {
	color: #65cf21;
}
</style>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
