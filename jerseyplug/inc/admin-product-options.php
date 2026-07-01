<?php
/**
 * WooCommerce Admin Product Options.
 *
 * Implements a custom "Jersey Options" tab in the WooCommerce Product Data meta box.
 *
 * @package JerseyPlug
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Wataco_Admin_Product_Options {

	/**
	 * Singleton instance.
	 *
	 * @var Wataco_Admin_Product_Options|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Wataco_Admin_Product_Options
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Add custom tab to Product Data meta box.
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_custom_product_data_tab' ] );

		// Render the content of the custom tab.
		add_action( 'woocommerce_product_data_panels', [ $this, 'render_custom_tab_content' ] );

		// Save the custom fields.
		add_action( 'woocommerce_process_product_meta', [ $this, 'save_custom_fields' ] );
	}

	/**
	 * Add the "Jersey Options" tab.
	 *
	 * @param array $tabs Existing product data tabs.
	 * @return array
	 */
	public function add_custom_product_data_tab( $tabs ) {
		$tabs['jersey_options'] = [
			'label'    => __( 'Jersey Options', 'jerseyplug' ),
			'target'   => 'jersey_options_data',
			'class'    => [ 'show_if_simple', 'show_if_variable' ],
			'priority' => 50,
		];
		return $tabs;
	}

	/**
	 * Render the fields inside the custom tab.
	 */
	public function render_custom_tab_content() {
		global $post;

		echo '<div id="jersey_options_data" class="panel woocommerce_options_panel hidden">';

		// 1. Checkbox: Allow Personalization
		woocommerce_wp_checkbox( [
			'id'          => '_allow_personalization',
			'label'       => __( 'Allow Personalization', 'jerseyplug' ),
			'description' => __( 'Enable custom name and number printing for this jersey.', 'jerseyplug' ),
			'desc_tip'    => true,
		] );

		// 2. Multi-Select Dropdown: Available Patches
		// Note: woocommerce_wp_select does not fully support array values for multiple selects in older WC versions, 
		// but using 'wc-enhanced-select' triggers Select2. For robustness with arrays, we manually render 
		// the select using WooCommerce's native HTML structure if needed, or stick to the requested native function.
		// We'll use the native wrapper but handle the selected state properly.
		
		$patches_options = $this->get_patches_options();
		$current_patches = get_post_meta( $post->ID, '_available_patches', true );
		if ( ! is_array( $current_patches ) ) {
			$current_patches = [];
		}

		?>
		<p class="form-field _available_patches_field">
			<label for="_available_patches"><?php esc_html_e( 'Available Patches', 'jerseyplug' ); ?></label>
			<select id="_available_patches" name="_available_patches[]" class="wc-enhanced-select" multiple="multiple" style="width: 50%;">
				<?php foreach ( $patches_options as $id => $label ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( in_array( $id, $current_patches ) ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php echo wc_help_tip( __( 'Select which patches are available for this jersey. You can search and select multiple.', 'jerseyplug' ) ); ?>
		</p>
		<?php

		echo '</div>';
	}

	/**
	 * Save the custom fields.
	 *
	 * @param int $post_id Product ID being saved.
	 */
	public function save_custom_fields( $post_id ) {
		// Check for autosave or lack of capabilities.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save Allow Personalization checkbox.
		$allow_personalization = isset( $_POST['_allow_personalization'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_allow_personalization', $allow_personalization );

		// Save Available Patches multi-select.
		if ( isset( $_POST['_available_patches'] ) && is_array( $_POST['_available_patches'] ) ) {
			// Sanitize array elements.
			$patches = array_map( 'sanitize_text_field', wp_unslash( $_POST['_available_patches'] ) );
			update_post_meta( $post_id, '_available_patches', $patches );
		} else {
			// If none selected, delete the meta key.
			delete_post_meta( $post_id, '_available_patches' );
		}
	}

	/**
	 * Helper function to fetch simple products (representing patches).
	 *
	 * @return array Array formatted for options (Product ID => 'Product Name (+R Price)').
	 */
	private function get_patches_options() {
		$options = [];

		// Query for Simple Products (assuming patches are saved as simple products).
		$args = [
			'status' => 'publish',
			'type'   => 'simple',
			'limit'  => -1,
			'return' => 'objects',
		];

		// Execute optimized WooCommerce query.
		$products = wc_get_products( $args );

		foreach ( $products as $product ) {
			if ( ! $product instanceof WC_Product ) {
				continue;
			}
			
			// Format the price securely, stripping HTML for the select option.
			$price = wc_price( $product->get_price() );
			$price_plain = wp_strip_all_tags( $price ); 
			
			// Populate options array (e.g. 105 => 'Champions League (+R 120)')
			$options[ $product->get_id() ] = sprintf( '%s (+%s)', $product->get_name(), $price_plain );
		}

		return $options;
	}
}

// Instantiate the class.
Wataco_Admin_Product_Options::get_instance();
