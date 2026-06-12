<?php

/**
 * Product detail gallery component.
 *
 * @package JerseyPlug
 */

$args    = wp_parse_args($args ?? [], ['product' => null]);
$product = $args['product'];

if (! $product instanceof WC_Product) {
	return;
}

$main_image_id = $product->get_image_id();
$main_image_url = $main_image_id > 0 ? wp_get_attachment_image_url($main_image_id, 'large') : wc_placeholder_img_src('large');

$gallery_image_ids = $product->get_gallery_image_ids();
$gallery_images = [];

if ($main_image_url) {
	$gallery_images[] = $main_image_url;
}

foreach ($gallery_image_ids as $img_id) {
	$url = wp_get_attachment_image_url($img_id, 'large');
	if ($url) {
		$gallery_images[] = $url;
	}
}

$in_stock = $product->is_in_stock();
?>

<div
	class="space-y-4"
	x-data="{ 
		activeImage: '<?php echo esc_url($main_image_url); ?>',
		selectedIndex: 0,
		images: <?php echo esc_attr(wp_json_encode($gallery_images)); ?>
	}">
	<!-- Main Display Image -->
	<div class="relative aspect-5/5 overflow-hidden rounded-2xl bg-zinc-100 shadow-sm">
		<!-- Stock Badge -->
		<?php if (! $in_stock) : ?>
			<span class="absolute left-4 top-4 z-10 rounded-full bg-red-600 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-white">
				<?php echo esc_html(jerseyplug_pll('Out of Stock')); ?>
			</span>
		<?php else : ?>
			<span class="absolute left-4 top-4 z-10 rounded-full bg-emerald-600 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-white">
				<?php echo esc_html(jerseyplug_pll('In Stock')); ?>
			</span>
		<?php endif; ?>

		<img
			:src="activeImage"
			alt="<?php echo esc_attr($product->get_name()); ?>"
			class="h-full w-full object-cover transition-all duration-500"
			loading="lazy"
			decoding="async" />
	</div>

	<!-- Thumbnails Grid -->
	<?php if (count($gallery_images) > 1) : ?>
		<div class="grid grid-cols-5 gap-3">
			<template x-for="(img, index) in images" :key="index">
				<button
					type="button"
					@click="activeImage = img; selectedIndex = index"
					class="aspect-square overflow-hidden rounded-lg border-2 bg-zinc-100 transition-all duration-200 hover:opacity-85"
					:class="selectedIndex === index ? 'border-primary shadow-md' : 'border-transparent opacity-70'">
					<img :src="img" alt="Thumbnail" class="h-full w-full object-cover" />
				</button>
			</template>
		</div>
	<?php endif; ?>
</div>