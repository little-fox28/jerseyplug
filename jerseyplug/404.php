<?php

/**
 * The template for displaying 404 pages (not found).
 *
 * @package JerseyPlug
 */

get_header();

// Print WooCommerce notices if any exist (e.g. invalid product added to cart)
if (function_exists('woocommerce_output_all_notices')) {
	echo '<div class="container mx-auto px-6 mt-8">';
	woocommerce_output_all_notices();
	echo '</div>';
}
?>

<div class="site-main min-h-[60vh] flex items-center justify-center bg-gray-50 text-zinc-900 py-20">
	<div class="container mx-auto px-6 text-center">
		<h1 class="text-9xl font-black text-primary tracking-tighter mb-4">404</h1>
		<h2 class="text-3xl md:text-4xl font-bold mb-6"><?php echo esc_html(jerseyplug_pll('Page Not Found')); ?></h2>
		<p class="text-lg text-gray-600 mb-10 max-w-lg mx-auto">
			<?php echo esc_html(jerseyplug_pll('Sorry, the page you are looking for could not be found.')); ?>
		</p>
		<div class="flex flex-col sm:flex-row items-center justify-center gap-4">
			<a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-8 py-4 font-bold text-white shadow-lg transition-transform hover:-translate-y-1 hover:shadow-xl active:scale-95">
				<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
					<rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
				</svg>
				<?php echo esc_html(jerseyplug_pll('Go to shop')); ?>
			</a>
			<a href="<?php echo esc_url(home_url('/')); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-8 py-4 font-bold text-gray-700 shadow-sm transition-colors hover:bg-gray-50 active:bg-gray-100">
				<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
					<polyline points="9 22 9 12 15 12 15 22"></polyline>
				</svg>
				<?php echo esc_html(jerseyplug_pll('Go Home')); ?>
			</a>
		</div>
	</div>
</div>

<?php
get_footer();
