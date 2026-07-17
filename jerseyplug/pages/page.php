<?php

/**
 * Generic page template.
 *
 * @package JerseyPlug
 */

get_header();
?>

<main id="primary" class="site-main">
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('container mx-auto px-4 max-w-5xl py-12'); ?>>
				<?php if (! (function_exists('is_account_page') && is_account_page()) && ! (function_exists('is_cart') && is_cart()) && ! (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received')) && ! (function_exists('is_checkout') && is_checkout()) ) : ?>
					<header class="mb-10 text-center">
						<h1 class="text-4xl md:text-5xl font-black text-primary tracking-tight">
							<?php the_title(); ?>
						</h1>
						<div class="h-1 w-20 bg-primary mx-auto mt-6 rounded-full opacity-80"></div>
					</header>
				<?php endif; ?>

				<?php if (function_exists('is_checkout') && is_checkout() && ! is_order_received_page()) : ?>
					<div class="mb-8">
						<a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-primary transition-colors">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
								<path d="m15 18-6-6 6-6" />
							</svg>
							<?php esc_html_e('Back to Cart', 'woocommerce'); ?>
						</a>
					</div>
				<?php endif; ?>

				<?php if (has_post_thumbnail()) : ?>
					<div class="mb-10 overflow-hidden rounded-2xl shadow-lg">
						<?php the_post_thumbnail('full', ['class' => 'w-full h-auto object-cover']); ?>
					</div>
				<?php endif; ?>

				<div class="entry-content prose prose-zinc md:prose-lg max-w-none prose-headings:font-bold prose-headings:tracking-tight prose-headings:text-zinc-900 prose-a:text-primary prose-a:font-bold prose-a:no-underline hover:prose-a:underline">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	<?php endif; ?>
</main>

<?php get_footer(); ?>