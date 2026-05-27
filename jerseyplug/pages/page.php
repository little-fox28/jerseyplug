<?php
/**
 * Generic page template.
 *
 * @package JerseyPlug
 */

get_header();
?>

<main id="primary" class="site-main py-12 md:py-16">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'container mx-auto px-4 max-w-5xl' ); ?>>
				<header class="mb-8 md:mb-10">
					<h1 class="text-3xl md:text-5xl font-bold text-primary">
						<?php the_title(); ?>
					</h1>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="mb-8 overflow-hidden rounded-2xl shadow-lg">
						<?php the_post_thumbnail( 'full', [ 'class' => 'w-full h-auto object-cover' ] ); ?>
					</div>
				<?php endif; ?>

				<div class="entry-content prose max-w-none prose-headings:text-primary prose-a:text-primary">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
