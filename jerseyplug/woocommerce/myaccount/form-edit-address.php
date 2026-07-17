<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing address', 'woocommerce' ) : esc_html__( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

<style>
    /* Styling for WooCommerce form fields */
    .woocommerce-address-fields__field-wrapper .form-row { margin-bottom: 1.25rem; }
    .woocommerce-address-fields__field-wrapper .form-row label { display: block; font-size: 0.875rem; font-weight: 700; color: #334155; margin-bottom: 0.5rem; }
    .woocommerce-address-fields__field-wrapper .form-row .required { color: #ef4444; text-decoration: none; }
    .woocommerce-address-fields__field-wrapper .form-row input.input-text,
    .woocommerce-address-fields__field-wrapper .form-row select,
    .woocommerce-address-fields__field-wrapper .form-row textarea { 
        width: 100%; border-radius: 0.75rem; border: 1px solid #cbd5e1; padding: 0.75rem 1rem; font-size: 0.875rem; color: #0f172a; transition: all 0.2s; background-color: #f8fafc;
    }
    .woocommerce-address-fields__field-wrapper .form-row input.input-text:focus,
    .woocommerce-address-fields__field-wrapper .form-row select:focus,
    .woocommerce-address-fields__field-wrapper .form-row textarea:focus { 
        outline: none; border-color: #163300; box-shadow: 0 0 0 3px rgba(22, 51, 0, 0.1); background-color: #ffffff;
    }
    .select2-container .select2-selection--single { height: 46px !important; border-radius: 0.75rem !important; border: 1px solid #cbd5e1 !important; display: flex !important; align-items: center !important; background-color: #f8fafc !important; }
    .select2-container--focus .select2-selection--single { border-color: #163300 !important; box-shadow: 0 0 0 3px rgba(22, 51, 0, 0.1) !important; background-color: #ffffff !important; }
</style>

<div class="bg-white border border-gray-200 rounded-[2rem] p-6 md:p-10 shadow-sm overflow-hidden max-w-4xl mx-auto">
    
    <div class="flex items-center gap-4 mb-8">
        <!-- Nút Back -->
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-500 hover:text-slate-900 hover:bg-gray-100 transition-colors shadow-sm border border-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
            <?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); ?>
        </h2>
    </div>

	<form method="post" class="flex flex-col gap-6">
		<div class="woocommerce-address-fields">
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

			<div class="woocommerce-address-fields__field-wrapper">
				<?php
				foreach ( $address as $key => $field ) {
					if ( isset( $field['required'] ) && $field['required'] ) {
						if ( ! isset( $field['custom_attributes'] ) ) {
							$field['custom_attributes'] = array();
						}
						$field['custom_attributes']['required'] = 'required';
					}
					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
				}
				?>
			</div>

			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

			<div class="mt-8 pt-6 border-t border-gray-100">
				<button type="submit" class="inline-flex items-center justify-center rounded-xl py-4 px-8 font-extrabold text-sm uppercase tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 w-full sm:w-auto text-[#f2c86c] bg-primary" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>
				<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
				<input type="hidden" name="action" value="edit_address" />
			</div>
		</div>
	</form>
</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
