<?php
/**
 * Theme header template.
 *
 * @package JerseyPlug
 */

// Header menu is fully managed via WordPress Appearance -> Menus
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-white text-zinc-900 antialiased' ); ?>>

<?php wp_body_open(); ?>
<?php do_action( 'tailpress_site_before' ); ?>

<div id="page" class="min-h-screen flex flex-col">
	<?php do_action( 'tailpress_header' ); ?>

	<?php get_template_part('components/layout/header'); ?>

	<div id="content" class="site-content grow">
		<?php do_action( 'tailpress_content_start' ); ?>
		<main>
