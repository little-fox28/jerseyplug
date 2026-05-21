<?php
/**
 * Theme Customizer settings.
 *
 * @package JerseyPlug
 */

/**
 * Register JerseyPlug Customizer settings.
 */
function jerseyplug_customize_register( WP_Customize_Manager $wp_customize ): void {
	$panel_id   = 'jerseyplug_global_settings';
	$section_id = 'jerseyplug_global_contact';

	$wp_customize->add_panel(
		$panel_id,
		array(
			'title'      => __( 'JerseyPlug Global Settings', 'jerseyplug' ),
			'capability' => 'edit_theme_options',
			'priority'   => 160,
		)
	);

	$wp_customize->add_section(
		$section_id,
		array(
			'title'      => __( 'Global Contact & Social', 'jerseyplug' ),
			'panel'      => $panel_id,
			'priority'   => 10,
			'capability' => 'edit_theme_options',
		)
	);

	$contact_settings = array(
		'jerseyplug_contact_address' => __( 'Address', 'jerseyplug' ),
		'jerseyplug_contact_phone'   => __( 'Phone Number', 'jerseyplug' ),
		'jerseyplug_contact_email'   => __( 'Email Address', 'jerseyplug' ),
	);

	foreach ( $contact_settings as $setting_id => $label ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => $section_id,
				'type'    => 'text',
			)
		);
	}

	$social_settings = array(
		'jerseyplug_social_facebook'  => __( 'Facebook URL', 'jerseyplug' ),
		'jerseyplug_social_instagram' => __( 'Instagram URL', 'jerseyplug' ),
		'jerseyplug_social_twitter'   => __( 'Twitter (X) URL', 'jerseyplug' ),
		'jerseyplug_social_youtube'   => __( 'YouTube URL', 'jerseyplug' ),
	);

	foreach ( $social_settings as $setting_id => $label ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => $section_id,
				'type'    => 'url',
			)
		);
	}
}
add_action( 'customize_register', 'jerseyplug_customize_register' );
