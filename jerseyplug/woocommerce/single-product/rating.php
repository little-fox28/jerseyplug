<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

// We use the custom helper to generate a random rating for the design system as per previous logic.
$rating_data  = function_exists('jerseyplug_get_random_rating_and_reviews') ? jerseyplug_get_random_rating_and_reviews($product->get_id()) : ['rating' => '4.8', 'reviews' => 120];
$rating       = $rating_data['rating'];
$review_count = $rating_data['reviews'];
?>

<div class="woocommerce-product-rating flex items-center gap-1 text-sm text-yellow-500 mb-4">
	<svg aria-hidden="true" viewBox="0 0 20 20" class="h-4 w-4 fill-current">
		<path d="m10 15.27 5.18 3.13-1.45-5.88L18.5 8.5l-6.06-.48L10 2.5 7.56 8.02 1.5 8.5l4.77 4.02-1.45 5.88L10 15.27Z"></path>
	</svg>
	<span class="font-bold text-gray-900"><?php echo esc_html($rating); ?></span>
	<span class="text-gray-400 ml-1">(<?php echo esc_html($review_count); ?>&nbsp;<?php esc_html_e('Reviews', 'jerseyplug'); ?>)</span>
</div>
