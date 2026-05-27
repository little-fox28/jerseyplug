<?php
/**
 * Homepage template.
 *
 * @package JerseyPlug
 */

get_header();
?>

<main id="primary" class="site-main p-0 m-0 no-extra-spacing bg-white text-textMain">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php
			$page_id = get_the_ID();
			do_action( 'jerseyplug_before_homepage', $page_id );

			get_template_part( 'pages/home-page/section', 'hero', [ 'page_id' => $page_id ] );
			get_template_part( 'pages/home-page/section', 'intro', [ 'page_id' => $page_id ] );
			get_template_part( 'pages/home-page/section', 'categories', [ 'page_id' => $page_id ] );
			get_template_part( 'pages/home-page/section', 'leagues', [ 'page_id' => $page_id ] );
			get_template_part( 'pages/home-page/section', 'featured-products', [ 'page_id' => $page_id ] );
			get_template_part( 'pages/home-page/section', 'new-arrivals', [ 'page_id' => $page_id ] );
			get_template_part( 'pages/home-page/section', 'flags', [ 'page_id' => $page_id ] );
			get_template_part( 'pages/home-page/section', 'testimonials', [ 'page_id' => $page_id ] );
			get_template_part( 'pages/home-page/section', 'features', [ 'page_id' => $page_id ] );

			do_action( 'jerseyplug_after_homepage', $page_id );
			?>
		<?php endwhile; ?>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
