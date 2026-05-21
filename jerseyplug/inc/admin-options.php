<?php
/**
 * Global admin options for JerseyPlug.
 *
 * @package JerseyPlug
 */

if ( ! function_exists( 'jerseyplug_get_global_options' ) ) {
	/**
	 * Fetch stored global options safely.
	 */
	function jerseyplug_get_global_options(): array {
		$options = get_option( 'jerseyplug_global_options', [] );
		return is_array( $options ) ? $options : [];
	}
}

if ( ! function_exists( 'jerseyplug_sanitize_global_options' ) ) {
	/**
	 * Sanitize global options before saving.
	 */
	function jerseyplug_sanitize_global_options( $options ): array {
		$options   = is_array( $options ) ? $options : [];
		$sanitized = [];

		$fields = [
			'jerseyplug_contact_address'   => 'sanitize_text_field',
			'jerseyplug_contact_phone'     => 'sanitize_text_field',
			'jerseyplug_contact_email'     => 'sanitize_email',
			'jerseyplug_social_facebook'   => 'esc_url_raw',
			'jerseyplug_social_instagram'  => 'esc_url_raw',
			'jerseyplug_social_twitter'    => 'esc_url_raw',
			'jerseyplug_social_youtube'    => 'esc_url_raw',
			'jerseyplug_social_tiktok'     => 'esc_url_raw',
		];

		foreach ( $fields as $key => $callback ) {
			if ( array_key_exists( $key, $options ) ) {
				$value = $options[ $key ];
				$sanitized[ $key ] = is_callable( $callback ) ? call_user_func( $callback, $value ) : $value;
			}
		}

		return $sanitized;
	}
}

if ( ! function_exists( 'jerseyplug_register_global_settings_page' ) ) {
	/**
	 * Register top-level Global Settings menu.
	 */
	function jerseyplug_register_global_settings_page(): void {
		add_menu_page(
			__( 'JerseyPlug Global Settings', 'jerseyplug' ),
			__( 'Global Settings', 'jerseyplug' ),
			'manage_options',
			'jerseyplug-global-settings',
			'jerseyplug_render_global_settings_page',
			'dashicons-admin-generic',
			30
		);
	}
	add_action( 'admin_menu', 'jerseyplug_register_global_settings_page' );
}

if ( ! function_exists( 'jerseyplug_register_global_settings' ) ) {
	/**
	 * Register settings and fields for Global Settings.
	 */
	function jerseyplug_register_global_settings(): void {
		register_setting(
			'jerseyplug_global_settings',
			'jerseyplug_global_options',
			[
				'type'              => 'array',
				'sanitize_callback' => 'jerseyplug_sanitize_global_options',
				'default'           => [],
			]
		);

		add_settings_section(
			'jerseyplug_global_contact',
			__( 'Contact Details', 'jerseyplug' ),
			'jerseyplug_render_contact_section',
			'jerseyplug-global-settings'
		);

		add_settings_section(
			'jerseyplug_global_social',
			__( 'Social Links', 'jerseyplug' ),
			'jerseyplug_render_social_section',
			'jerseyplug-global-settings'
		);

		add_settings_field(
			'jerseyplug_contact_address',
			__( 'Address', 'jerseyplug' ),
			'jerseyplug_render_text_field',
			'jerseyplug-global-settings',
			'jerseyplug_global_contact',
			[
				'label_for'   => 'jerseyplug_contact_address',
				'option_key'  => 'jerseyplug_contact_address',
				'placeholder' => __( 'Street, City, Province', 'jerseyplug' ),
				'type'        => 'text',
			]
		);

		add_settings_field(
			'jerseyplug_contact_phone',
			__( 'Phone', 'jerseyplug' ),
			'jerseyplug_render_text_field',
			'jerseyplug-global-settings',
			'jerseyplug_global_contact',
			[
				'label_for'   => 'jerseyplug_contact_phone',
				'option_key'  => 'jerseyplug_contact_phone',
				'placeholder' => __( '+27 00 000 0000', 'jerseyplug' ),
				'type'        => 'text',
			]
		);

		add_settings_field(
			'jerseyplug_contact_email',
			__( 'Email', 'jerseyplug' ),
			'jerseyplug_render_text_field',
			'jerseyplug-global-settings',
			'jerseyplug_global_contact',
			[
				'label_for'   => 'jerseyplug_contact_email',
				'option_key'  => 'jerseyplug_contact_email',
				'placeholder' => __( 'support@jerseyplug.co.za', 'jerseyplug' ),
				'type'        => 'email',
			]
		);

		$social_fields = [
			'jerseyplug_social_facebook'  => __( 'Facebook URL', 'jerseyplug' ),
			'jerseyplug_social_instagram' => __( 'Instagram URL', 'jerseyplug' ),
			'jerseyplug_social_twitter'   => __( 'Twitter URL', 'jerseyplug' ),
			'jerseyplug_social_youtube'   => __( 'YouTube URL', 'jerseyplug' ),
			'jerseyplug_social_tiktok'    => __( 'TikTok URL', 'jerseyplug' ),
		];

		foreach ( $social_fields as $key => $label ) {
			add_settings_field(
				$key,
				$label,
				'jerseyplug_render_text_field',
				'jerseyplug-global-settings',
				'jerseyplug_global_social',
				[
					'label_for'   => $key,
					'option_key'  => $key,
					'placeholder' => __( 'https://', 'jerseyplug' ),
					'type'        => 'url',
				]
			);
		}
	}
	add_action( 'admin_init', 'jerseyplug_register_global_settings' );
}

if ( ! function_exists( 'jerseyplug_render_contact_section' ) ) {
	/**
	 * Contact section description.
	 */
	function jerseyplug_render_contact_section(): void {
		echo '<p>' . esc_html__( 'Set the contact details shown across the site.', 'jerseyplug' ) . '</p>';
	}
}

if ( ! function_exists( 'jerseyplug_render_social_section' ) ) {
	/**
	 * Social section description.
	 */
	function jerseyplug_render_social_section(): void {
		echo '<p>' . esc_html__( 'Add social profiles displayed in the footer.', 'jerseyplug' ) . '</p>';
	}
}

if ( ! function_exists( 'jerseyplug_render_text_field' ) ) {
	/**
	 * Render a standard text input field.
	 */
	function jerseyplug_render_text_field( array $args ): void {
		$option_key  = $args['option_key'] ?? '';
		$type        = $args['type'] ?? 'text';
		$placeholder = $args['placeholder'] ?? '';

		if ( $option_key === '' ) {
			return;
		}

		$options = jerseyplug_get_global_options();
		$value   = $options[ $option_key ] ?? '';

		printf(
			'<input type="%1$s" id="%2$s" name="jerseyplug_global_options[%2$s]" value="%3$s" class="regular-text" placeholder="%4$s" />',
			esc_attr( $type ),
			esc_attr( $option_key ),
			esc_attr( $value ),
			esc_attr( $placeholder )
		);
	}
}

if ( ! function_exists( 'jerseyplug_render_global_settings_page' ) ) {
	/**
	 * Render the Global Settings page.
	 */
	function jerseyplug_render_global_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'JerseyPlug Global Settings', 'jerseyplug' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'jerseyplug_global_settings' );
				do_settings_sections( 'jerseyplug-global-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
