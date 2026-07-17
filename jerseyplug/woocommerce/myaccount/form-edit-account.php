<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>

<div class="bg-white border border-gray-200 rounded-[2rem] p-6 md:p-10 shadow-sm overflow-hidden max-w-4xl mx-auto">
    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight mb-8">
        <?php esc_html_e( 'Account details', 'woocommerce' ); ?>
    </h2>

	<form class="woocommerce-EditAccountForm edit-account flex flex-col gap-6" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

		<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-slate-700" for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?> <span class="text-red-500">*</span></label>
                <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-gray-50 focus:bg-white" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" required aria-required="true" />
            </div>
            
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-slate-700" for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?> <span class="text-red-500">*</span></label>
                <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-gray-50 focus:bg-white" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" required aria-required="true" />
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-slate-700" for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?> <span class="text-red-500">*</span></label>
            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-gray-50 focus:bg-white" name="account_display_name" id="account_display_name" aria-describedby="account_display_name_description" value="<?php echo esc_attr( $user->display_name ); ?>" required aria-required="true" />
            <span class="text-xs text-gray-500 mt-1"><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></span>
        </div>

        <div class="flex flex-col gap-2 mb-4">
            <label class="text-sm font-bold text-slate-700" for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?> <span class="text-red-500">*</span></label>
            <input type="email" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-gray-50 focus:bg-white" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" required aria-required="true" />
        </div>

		<?php do_action( 'woocommerce_edit_account_form_fields' ); ?>

        <!-- Password Change Section -->
		<fieldset class="border border-gray-100 rounded-2xl p-6 bg-white shadow-sm flex flex-col gap-4 mt-2">
			<legend class="text-lg font-extrabold text-slate-900 px-2 uppercase tracking-wide"><?php esc_html_e( 'Password change', 'woocommerce' ); ?></legend>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-slate-700" for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
                <input type="password" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-gray-50 focus:bg-white" name="password_current" id="password_current" autocomplete="current-password" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-bold text-slate-700" for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
                    <input type="password" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-gray-50 focus:bg-white" name="password_1" id="password_1" autocomplete="new-password" />
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-bold text-slate-700" for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
                    <input type="password" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-gray-50 focus:bg-white" name="password_2" id="password_2" autocomplete="new-password" />
                </div>
            </div>
		</fieldset>

		<?php do_action( 'woocommerce_edit_account_form' ); ?>

		<div class="mt-4 pt-6 border-t border-gray-100">
			<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
			<button type="submit" class="inline-flex items-center justify-center rounded-xl py-4 px-8 font-extrabold text-sm uppercase tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 w-full sm:w-auto text-[#f2c86c] bg-primary" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
			<input type="hidden" name="action" value="save_account_details" />
		</div>

		<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
	</form>
</div>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
