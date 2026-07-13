<?php
/**
 * Generic page template.
 *
 * @package JerseyPlug
 */

get_header();
?>

<main id="primary" class="site-main py-6 md:py-16">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'container mx-auto px-4 max-w-5xl' ); ?>>
				<?php if ( ! ( function_exists( 'is_account_page' ) && is_account_page() ) ) : ?>
				<header class="mb-8 md:mb-10">
					<h1 class="text-3xl md:text-5xl font-bold text-primary">
						<?php the_title(); ?>
					</h1>
				</header>
				<?php endif; ?>

				<?php if ( function_exists('is_checkout') && is_checkout() && ! is_order_received_page() ) : ?>
					<div class="mb-8">
						<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#163300] transition-colors">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
							<?php esc_html_e( 'Back to Cart', 'woocommerce' ); ?>
						</a>
					</div>
				<?php endif; ?>

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
