<?php

$slides = isset($attributes['slides']) ? $attributes['slides'] : [];

if (empty($slides)) {
	return;
}

// Build valid slide list.
$valid_slides = [];
foreach ($slides as $index => $slide) {
	$image_url  = '';
	$image_data = isset($slide['image']) ? $slide['image'] : null;

	if (is_array($image_data) && isset($image_data['url'])) {
		$image_url = $image_data['url'];
	} elseif (is_numeric($image_data)) {
		$image_url = wp_get_attachment_image_url($image_data, 'full');
	} elseif (is_string($image_data) && $image_data !== '') {
		$image_url = $image_data;
	}

	$heading    = isset($slide['title']) ? $slide['title'] : '';
	$subheading = isset($slide['description']) ? $slide['description'] : '';
	$label      = isset($slide['badge']) ? $slide['badge'] : '';

	// Skip completely empty slides.
	if (! $image_url && ! $heading && ! $subheading && ! $label) {
		continue;
	}

	$shop_url = function_exists('wc_get_page_permalink')
		? (string) wc_get_page_permalink('shop')
		: home_url('/shop');

	$button_label = (isset($slide['primary-button-lable']) && $slide['primary-button-lable'] !== '')
		? $slide['primary-button-lable']
		: (function_exists('jerseyplug_pll') ? jerseyplug_pll('Shop Now') : __('Shop Now', 'jerseyplug'));

	$button_url = (isset($slide['primary-button-url']) && $slide['primary-button-url'] !== '')
		? $slide['primary-button-url']
		: $shop_url;

	$secondary_label = (isset($slide['secondary-button-lable-copy']) && $slide['secondary-button-lable-copy'] !== '')
		? $slide['secondary-button-lable-copy']
		: (function_exists('jerseyplug_pll') ? jerseyplug_pll('View All') : __('View All', 'jerseyplug'));

	$secondary_url = (isset($slide['secondary-button-url']) && $slide['secondary-button-url'] !== '')
		? $slide['secondary-button-url']
		: home_url('/categories');

	$valid_slides[] = [
		'index'           => $index,
		'heading'         => $heading,
		'subheading'      => $subheading,
		'label'           => $label,
		'image'           => $image_url,
		'button_label'    => $button_label,
		'button_url'      => $button_url,
		'secondary_label' => $secondary_label,
		'secondary_url'   => $secondary_url,
	];
}

if (empty($valid_slides)) {
	return;
}

$total_slides = count($valid_slides);
?>

<section
	id="hero-slider"
	class="relative w-full overflow-hidden bg-gray-900"
	data-home-slider>
	<!-- Slides wrapper -->
	<div class="relative h-[500px] md:h-[700px] w-full">

		<?php foreach ($valid_slides as $i => $slide) : ?>
			<div
				data-home-slide
				class="absolute inset-0 transition-opacity duration-1000 ease-in-out <?php echo 0 === $i ? 'opacity-100' : 'opacity-0'; ?>"
				aria-hidden="<?php echo 0 === $i ? 'false' : 'true'; ?>">
				<!-- Background Image / Fallback -->
				<div class="absolute inset-0 z-0 bg-gray-900">
					<?php if (! empty($slide['image'])) : ?>
						<img
							src="<?php echo esc_url($slide['image']); ?>"
							alt="<?php echo esc_attr($slide['heading']); ?>"
							class="w-full h-full object-cover brightness-75"
							<?php echo 0 === $i ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"'; ?>
							decoding="async">
					<?php else : ?>
						<!-- Gray background with green gradient fallback -->
						<div class="w-full h-full bg-gradient-to-tr from-gray-900 via-gray-800 to-accent/25 relative overflow-hidden" aria-hidden="true">
							<div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-accent/10 blur-3xl"></div>
							<div class="absolute right-1/4 bottom-1/4 w-96 h-96 rounded-full bg-primary/20 blur-3xl"></div>
						</div>
					<?php endif; ?>
					<!-- Gradient overlay: matches JSX from-primary/95 via-primary/50 to-transparent -->
					<div class="absolute inset-0 bg-gradient-to-r from-primary/95 via-primary/50 to-transparent"></div>
				</div>

				<!-- Slide Content -->
				<div class="relative z-10 container mx-auto px-4 h-full flex items-center text-white">
					<div class="max-w-2xl">

						<?php if ($slide['label']) : ?>
							<span class="inline-block py-1 px-3 text-xs font-bold tracking-wider uppercase mb-4 rounded-sm bg-secondary text-primary">
								<?php echo esc_html($slide['label']); ?>
							</span>
						<?php endif; ?>

						<?php if ($slide['heading']) : ?>
							<h1 class="text-3xl md:text-6xl font-bold leading-tight mb-6">
								<?php echo esc_html($slide['heading']); ?>
							</h1>
						<?php endif; ?>

						<?php if ($slide['subheading']) : ?>
							<p class="text-lg md:text-xl text-gray-200 mb-8 max-w-lg">
								<?php echo esc_html($slide['subheading']); ?>
							</p>
						<?php endif; ?>

						<div class="flex flex-wrap gap-4">
							<!-- Primary CTA -->
							<a
								href="<?php echo esc_url($slide['button_url']); ?>"
								class="font-bold py-3 px-8 rounded flex items-center gap-2 hover:shadow-xl shadow-lg bg-secondary text-primary transition-all duration-300 hover:scale-105">
								<?php echo esc_html($slide['button_label']); ?>
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
									<path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
								</svg>
							</a>

							<!-- Secondary CTA -->
							<a
								href="<?php echo esc_url($slide['secondary_url']); ?>"
								class="bg-transparent border-2 border-white hover:bg-white text-white hover:text-primary font-bold py-3 px-8 rounded transition-all duration-300">
								<?php echo esc_html($slide['secondary_label']); ?>
							</a>
						</div>

					</div>
				</div>
			</div>
		<?php endforeach; ?>

	</div>



	<!-- Indicator Dots — data-home-slider-dot hooks into jerseyplugInitHomeSlider -->
	<?php if ($total_slides > 1) : ?>
		<div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20">
			<?php foreach ($valid_slides as $i => $slide) : ?>
				<button
					type="button"
					data-home-slider-dot
					class="relative rounded-full transition-all duration-300 p-3"
					aria-label="<?php echo esc_attr(sprintf(function_exists('jerseyplug_pll') ? jerseyplug_pll('Go to slide %d') : __('Go to slide %d', 'jerseyplug'), $i + 1)); ?>">
					<span class="block h-2 rounded-full transition-all duration-300 <?php echo 0 === $i ? 'w-8 bg-secondary' : 'w-2 bg-white/50'; ?>"></span>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>