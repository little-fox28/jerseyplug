<?php

/**
 * Single Product Price, including Rating.
 *
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $product;

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

$rating_data  = function_exists('jerseyplug_get_random_rating_and_reviews') ? jerseyplug_get_random_rating_and_reviews($product->get_id()) : ['rating' => '4.8', 'reviews' => 120];
$rating       = $rating_data['rating'];
$review_count = $rating_data['reviews'];
?>

<div class="flex flex-wrap items-center justify-between gap-4 mt-4 mb-6">
	<!-- Dynamic Price (Alpine bound) -->
	<div class="flex items-baseline gap-2 text-2xl md:text-3xl font-black text-gray-900 tracking-tight">
		<del x-show="singleProductRegularPrice > singleProductPrice" x-text="formatCurrency(singleProductRegularPrice)" class="text-lg text-gray-400 font-bold" style="display: none;"></del>
		<ins class="no-underline text-primary" x-text="formatCurrency(singleProductPrice)"><?php echo $product->get_price_html(); ?></ins>
	</div>

	<!-- Rating -->
	<div class="!flex items-center gap-1.5 text-sm text-yellow-500">
		<svg aria-hidden="true" viewBox="0 0 20 20" class="h-4 w-4 fill-current shrink-0">
			<path d="m10 15.27 5.18 3.13-1.45-5.88L18.5 8.5l-6.06-.48L10 2.5 7.56 8.02 1.5 8.5l4.77 4.02-1.45 5.88L10 15.27Z"></path>
		</svg>
		<span class="font-bold text-gray-900 whitespace-nowrap leading-none mt-0.5"><?php echo esc_html($rating); ?></span>
		<span class="text-gray-400 whitespace-nowrap leading-none mt-0.5">(<?php echo esc_html($review_count); ?> <?php esc_html_e('Reviews', 'jerseyplug'); ?>)</span>
	</div>
</div>