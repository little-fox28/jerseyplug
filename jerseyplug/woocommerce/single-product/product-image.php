<?php

/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;

global $product;

$main_image_id  = $product->get_image_id();
$main_image_url = $main_image_id > 0
	? wp_get_attachment_image_url($main_image_id, 'woocommerce_single')
	: (function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('woocommerce_single') : '');

$gallery_image_ids = $product->get_gallery_image_ids();
$gallery_images    = [];

if ($main_image_url) {
	$gallery_images[] = [
		'src'   => $main_image_url,
		'full'  => $main_image_id > 0 ? (string) wp_get_attachment_image_url($main_image_id, 'full') : $main_image_url,
		'alt'   => get_post_meta($main_image_id, '_wp_attachment_image_alt', true) ?: $product->get_name(),
	];
}

foreach ($gallery_image_ids as $img_id) {
	$url  = wp_get_attachment_image_url($img_id, 'woocommerce_single');
	$full = wp_get_attachment_image_url($img_id, 'full');
	$alt  = get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: $product->get_name();
	if ($url) {
		$gallery_images[] = ['src' => $url, 'full' => $full ?: $url, 'alt' => $alt];
	}
}

$in_stock  = $product->is_in_stock();
$has_sale  = $product->is_on_sale();
$is_new    = function_exists('jerseyplug_is_new_product') && jerseyplug_is_new_product($product);
?>

<div
	class="space-y-3"
	x-data="{
		activeIndex: 0,
		images: <?php echo esc_attr(wp_json_encode($gallery_images)); ?>,
		lightboxOpen: false,

		get activeImage() { return this.images[this.activeIndex] ?? this.images[0]; },

		prev() { this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length; },
		next() { this.activeIndex = (this.activeIndex + 1) % this.images.length; },
		select(i) { this.activeIndex = i; },

		handleKey(e) {
			if (!this.lightboxOpen) return;
			if (e.key === 'ArrowRight') this.next();
			if (e.key === 'ArrowLeft') this.prev();
			if (e.key === 'Escape') this.lightboxOpen = false;
		}
	}"
	@keydown.window="handleKey($event)">

	<!-- Main Display Image -->
	<div class="relative group overflow-hidden rounded-2xl bg-zinc-100 shadow-sm aspect-square cursor-zoom-in"
		@click="lightboxOpen = true">

		<!-- Badges -->
		<div class="absolute left-3 top-3 z-10 flex flex-col gap-1.5">
			<!-- <?php if (! $in_stock) : ?>
				<span class="rounded-full bg-red-600 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-sm">
					<?php esc_html_e('Out of Stock', 'jerseyplug'); ?>
				</span>
			<?php else : ?>
				<span class="rounded-full bg-emerald-600 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-sm">
					<?php esc_html_e('In Stock', 'jerseyplug'); ?>
				</span>
			<?php endif; ?> -->
			<?php if ($has_sale) : ?>
				<span class="rounded-full bg-secondary px-3 py-1 text-[10px] font-black uppercase tracking-wider text-primary shadow-sm">
					<?php esc_html_e('Sale', 'jerseyplug'); ?>
				</span>
			<?php elseif ($is_new) : ?>
				<span class="rounded-full bg-primary px-3 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-sm">
					<?php esc_html_e('New', 'jerseyplug'); ?>
				</span>
			<?php endif; ?>
		</div>

		<!-- Zoom hint -->
		<div class="absolute right-3 bottom-3 z-10 rounded-full bg-white/80 backdrop-blur-sm p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7" />
			</svg>
		</div>

		<!-- Active image -->
		<img
			:src="activeImage.src"
			:alt="activeImage.alt"
			class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
			loading="eager"
			fetchpriority="high"
			decoding="sync" />

		<!-- Prev/Next arrows -->
		<?php if (count($gallery_images) > 1) : ?>
			<button
				type="button"
				@click.stop="prev()"
				class="absolute left-2 top-1/2 -translate-y-1/2 z-10 rounded-full bg-white/80 backdrop-blur-sm p-2 opacity-0 group-hover:opacity-100 transition-all hover:bg-white shadow-md"
				aria-label="<?php esc_attr_e('Previous image', 'jerseyplug'); ?>">
				<svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 text-gray-700" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="15 18 9 12 15 6"></polyline>
				</svg>
			</button>
			<button
				type="button"
				@click.stop="next()"
				class="absolute right-2 top-1/2 -translate-y-1/2 z-10 rounded-full bg-white/80 backdrop-blur-sm p-2 opacity-0 group-hover:opacity-100 transition-all hover:bg-white shadow-md"
				aria-label="<?php esc_attr_e('Next image', 'jerseyplug'); ?>">
				<svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 text-gray-700" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="9 18 15 12 9 6"></polyline>
				</svg>
			</button>
		<?php endif; ?>
	</div>

	<!-- Thumbnails -->
	<?php if (count($gallery_images) > 1) : ?>
		<div class="grid grid-cols-5 gap-2">
			<template x-for="(img, i) in images" :key="i">
				<button
					type="button"
					@click="select(i)"
					:aria-label="'<?php esc_attr_e('View image', 'jerseyplug'); ?> ' + (i + 1)"
					class="aspect-square overflow-hidden rounded-xl border-2 bg-zinc-100 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
					:class="activeIndex === i ? 'border-primary shadow-md opacity-100' : 'border-transparent opacity-60 hover:opacity-90'">
					<img :src="img.src" :alt="img.alt" class="h-full w-full object-cover" loading="lazy" decoding="async" />
				</button>
			</template>
		</div>
	<?php endif; ?>

	<!-- Lightbox -->
	<div
		x-show="lightboxOpen"
		x-cloak
		class="fixed inset-0 z-[80] flex items-center justify-center bg-black/90"
		x-transition:enter="transition ease-out duration-200"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100"
		x-transition:leave="transition ease-in duration-150"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
		@click.self="lightboxOpen = false">

		<button
			type="button"
			@click="lightboxOpen = false"
			class="absolute top-4 right-4 z-10 p-2.5 rounded-full bg-white/10 hover:bg-white/20 transition-colors"
			aria-label="<?php esc_attr_e('Close', 'jerseyplug'); ?>">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<line x1="18" y1="6" x2="6" y2="18"></line>
				<line x1="6" y1="6" x2="18" y2="18"></line>
			</svg>
		</button>

		<img
			:src="activeImage.full"
			:alt="activeImage.alt"
			class="max-h-[90vh] max-w-[90vw] rounded-xl object-contain shadow-2xl"
			x-transition:enter="transition ease-out duration-200"
			x-transition:enter-start="scale-95 opacity-0"
			x-transition:enter-end="scale-100 opacity-100" />

		<?php if (count($gallery_images) > 1) : ?>
			<button
				type="button"
				@click="prev()"
				class="absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/10 hover:bg-white/25 transition-colors"
				aria-label="<?php esc_attr_e('Previous image', 'jerseyplug'); ?>">
				<svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="15 18 9 12 15 6"></polyline>
				</svg>
			</button>
			<button
				type="button"
				@click="next()"
				class="absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/10 hover:bg-white/25 transition-colors"
				aria-label="<?php esc_attr_e('Next image', 'jerseyplug'); ?>">
				<svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="9 18 15 12 9 6"></polyline>
				</svg>
			</button>
		<?php endif; ?>
	</div>
</div>