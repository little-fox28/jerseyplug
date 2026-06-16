<?php
/**
 * Logo component.
 *
 * @package JerseyPlug
 */

$width_class   = $args['class'] ?? 'w-48';
$wrapper_class = $args['wrapper_class'] ?? '';
$img_class     = $args['img_class'] ?? 'h-auto object-contain transition-all';
$logo_url      = $args['logo_url'] ?? '';
$logo_alt      = $args['logo_alt'] ?? '';
$loading       = $args['loading'] ?? 'eager';
$decoding      = $args['decoding'] ?? 'async';
$fetchpriority = $args['fetchpriority'] ?? '';
$aria_label    = $args['aria_label'] ?? '';

if ( $logo_url === '' && function_exists( 'get_jerseyplug_setting' ) ) {
	$logo_url = (string) get_jerseyplug_setting( 'site_logo' );
}

if ( ( $logo_url === '' || $logo_alt === '' ) && function_exists( 'get_field' ) ) {
	$logo_field = get_field( 'header_logo', 'option' );

	if ( is_array( $logo_field ) ) {
		if ( $logo_url === '' && ! empty( $logo_field['url'] ) ) {
			$logo_url = (string) $logo_field['url'];
		}
		if ( $logo_alt === '' && ! empty( $logo_field['alt'] ) ) {
			$logo_alt = (string) $logo_field['alt'];
		}
	} elseif ( $logo_url === '' && is_numeric( $logo_field ) ) {
		$image_url = wp_get_attachment_image_url( (int) $logo_field, 'full' );
		if ( $image_url ) {
			$logo_url = $image_url;
		}
	} elseif ( $logo_url === '' && is_string( $logo_field ) && $logo_field !== '' ) {
		$logo_url = $logo_field;
	}
}

if ( $logo_url === '' ) {
	$logo_url = get_theme_file_uri( '/resources/images/jerseyplug-logo.svg' );
}

if ( $logo_alt === '' ) {
	$logo_alt = get_bloginfo( 'name' );
}

$fetchpriority_attribute = '';
if ( $fetchpriority !== '' ) {
	$fetchpriority_attribute = ' fetchpriority="' . esc_attr( $fetchpriority ) . '"';
}
?>

<a
	href="<?php echo esc_url( home_url( '/' ) ); ?>"
	class="flex-shrink-0 inline-block <?php echo esc_attr( $wrapper_class ); ?>"
	<?php if ( $aria_label !== '' ) : ?>
		aria-label="<?php echo esc_attr( $aria_label ); ?>"
	<?php endif; ?>
>
	<img
		src="<?php echo esc_url( $logo_url ); ?>"
		alt="<?php echo esc_attr( $logo_alt ); ?>"
		class="<?php echo esc_attr( trim( $width_class . ' ' . $img_class ) ); ?>"
		loading="<?php echo esc_attr( $loading ); ?>"
		decoding="<?php echo esc_attr( $decoding ); ?>"
		<?php echo $fetchpriority_attribute; ?>
	/>
</a>
