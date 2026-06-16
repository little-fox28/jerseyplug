<?php

/**
 * Lazy Block: Product Categories — Bento Grid.
 *
 * Attributes:
 *   - section_title   (Text)     Section heading, default "Product Categories"
 *   - view_all_link   (URL)      "View All" link target
 *   - categories      (Repeater) Each item: cat-image (Image), cat-title (Text),
 *                                cat-description (Textarea), cat-link (URL)
 *
 * @package JerseyPlug
 */

// --- Data -------------------------------------------------------------------

$section_title = isset($attributes['section-title']) && $attributes['section-title'] !== ''
	? $attributes['section-title']
	: (function_exists('jerseyplug_pll') ? jerseyplug_pll('Product Categories') : __('Product Categories', 'jerseyplug'));

$view_all_link = isset($attributes['view-all-link']) && $attributes['view-all-link'] !== ''
	? $attributes['view-all-link']
	: (function_exists('jerseyplug_get_homepage_shop_url') ? jerseyplug_get_homepage_shop_url() : home_url('/shop'));

$items = isset($attributes['categories']) ? $attributes['categories'] : [];

if (empty($items)) {
	return;
}

// --- Build validated card list -----------------------------------------------

$cards = [];
foreach ($items as $index => $item) {
	// Bulletproof image parsing (Array / Attachment ID / URL string).
	$image_url  = '';
	$image_data = isset($item['cat-image']) ? $item['cat-image'] : null;

	if (is_array($image_data) && isset($image_data['url'])) {
		$image_url = $image_data['url'];
	} elseif (is_numeric($image_data)) {
		$image_url = wp_get_attachment_image_url((int) $image_data, 'large');
	} elseif (is_string($image_data) && $image_data !== '') {
		$image_url = $image_data;
	}

	// Skip items without an image — the bento grid is image-driven.
	if (! $image_url) {
		continue;
	}

	$title = isset($item['cat-title']) ? $item['cat-title'] : '';
	$desc = isset($item['cat-description']) ? $item['cat-description'] : '';
	$link = isset($item['cat-link']) && $item['cat-link'] !== ''
		? $item['cat-link']
		: '#';

	$cards[] = [
		'image' => $image_url,
		'title'  => $title,
		'desc'  => $desc,
		'link'  => $link,
	];
}

if (empty($cards)) {
	return;
}

$count = count($cards);

// --- Dynamic Grid Class Mapping ----------------------------------------------
// Mobile: grid-cols-2 (custom) | Tablet: md:grid-cols-2 | Desktop: lg:grid-cols-4
$grid_classes    = [];
$heading_classes = [];

foreach ($cards as $i => $card) {

	// ── Featured Item (Item 1) — always prominent across all breakpoints ──
	if (0 === $i) {
		if ($count === 1) {
			// Solo: full width on all breakpoints
			$grid_classes[$i]    = 'col-span-2 row-span-2 min-h-[250px] sm:min-h-[320px] md:col-span-2 md:row-span-2 md:min-h-[400px] lg:col-span-4 lg:row-span-2 lg:min-h-[480px]';
			$heading_classes[$i] = 'text-2xl font-bold text-white md:text-3xl lg:text-4xl';
		} elseif ($count === 2) {
			// Pair: half-width on desktop, full-width on tablet & mobile
			$grid_classes[$i]    = 'col-span-2 row-span-2 min-h-[250px] sm:min-h-[320px] md:col-span-2 md:row-span-2 md:min-h-[400px] lg:col-span-2 lg:row-span-2 lg:min-h-[480px]';
			$heading_classes[$i] = 'text-xl font-bold text-white md:text-2xl lg:text-3xl';
		} else {
			// 3–6+ items: standard featured
			$grid_classes[$i]    = 'col-span-2 row-span-2 min-h-[250px] sm:min-h-[320px] md:col-span-2 md:row-span-2 md:min-h-[400px] lg:col-span-2 lg:row-span-2 lg:min-h-[400px]';
			$heading_classes[$i] = 'text-xl font-bold text-white md:text-2xl lg:text-3xl';
		}

		// ── Non-Featured Items ──
	} else {
		if ($count === 2) {
			// Item 2 mirrors Item 1 on desktop, stacks on mobile
			$grid_classes[$i]    = 'col-span-2 min-h-[200px] md:col-span-1 md:min-h-[200px] lg:col-span-2 lg:row-span-2 lg:min-h-[480px]';
			$heading_classes[$i] = 'text-xl font-bold text-white md:text-2xl lg:text-3xl';
		} elseif ($count === 3) {
			// Items 2 & 3 side-by-side on mobile
			$grid_classes[$i]    = 'col-span-1 min-h-[150px] sm:min-h-[200px] md:col-span-1 md:min-h-[200px] lg:col-span-2 lg:row-span-1 lg:min-h-[192px]';
			$heading_classes[$i] = 'text-base sm:text-lg font-bold text-white md:text-xl lg:text-2xl';
		} elseif ($count === 4) {
			if (1 === $i) {
				// Item 2: full width mobile, half desktop
				$grid_classes[$i]    = 'col-span-2 min-h-[180px] sm:min-h-[200px] md:col-span-1 md:min-h-[200px] lg:col-span-2 lg:row-span-1 lg:min-h-[192px]';
				$heading_classes[$i] = 'text-lg font-bold text-white md:text-xl lg:text-2xl';
			} else {
				// Items 3 & 4: side-by-side
				$grid_classes[$i]    = 'col-span-1 min-h-[150px] sm:min-h-[200px] md:col-span-1 md:min-h-[200px] lg:col-span-1 lg:row-span-1 lg:min-h-[192px]';
				$heading_classes[$i] = 'text-base sm:text-lg font-bold text-white md:text-xl lg:text-xl uppercase';
			}
		} elseif ($count === 5) {
			// Items 2–5: side-by-side on mobile (2 cols)
			$grid_classes[$i]    = 'col-span-1 min-h-[150px] sm:min-h-[200px] md:col-span-1 md:min-h-[200px] lg:col-span-1 lg:row-span-1 lg:min-h-[192px]';
			$heading_classes[$i] = 'text-base sm:text-lg font-bold text-white md:text-xl lg:text-xl uppercase';
		} elseif ($count === 6) {
			if ($i <= 4) {
				// Items 2–5: side-by-side
				$grid_classes[$i]    = 'col-span-1 min-h-[150px] sm:min-h-[200px] md:col-span-1 md:min-h-[200px] lg:col-span-1 lg:row-span-1 lg:min-h-[192px]';
				$heading_classes[$i] = 'text-base sm:text-lg font-bold text-white md:text-xl lg:text-xl uppercase';
			} else {
				// Item 6: full-width footer
				$grid_classes[$i]    = 'col-span-2 min-h-[180px] sm:min-h-[200px] md:col-span-2 md:min-h-[200px] lg:col-span-4 lg:row-span-1 lg:min-h-[100px]';
				$heading_classes[$i] = 'text-lg font-bold text-white md:text-xl lg:text-2xl';
			}
		} else {
			// 7+ items: Featured + Grid fallback
			if ($i <= 4) {
				$grid_classes[$i]    = 'col-span-1 min-h-[150px] sm:min-h-[200px] md:col-span-1 md:min-h-[200px] lg:col-span-1 lg:row-span-1 lg:min-h-[192px]';
				$heading_classes[$i] = 'text-base sm:text-lg font-bold text-white md:text-xl lg:text-xl uppercase';
			} else {
				// Additional items: 2 per row on desktop and mobile
				$grid_classes[$i]    = 'col-span-1 min-h-[150px] sm:min-h-[200px] md:col-span-1 md:min-h-[200px] lg:col-span-2 lg:row-span-1 lg:min-h-[192px]';
				$heading_classes[$i] = 'text-base sm:text-lg font-bold text-white md:text-xl lg:text-2xl';

				// Center the last item if it's alone on its row
				if ($i === $count - 1 && ($count % 2 !== 0)) {
					$grid_classes[$i] = 'col-span-2 min-h-[180px] sm:min-h-[200px] md:col-span-2 md:min-h-[200px] lg:col-span-4 lg:row-span-1 lg:min-h-[192px]';
				}
			}
		}
	}
}

$view_all_text = function_exists('jerseyplug_pll') ? jerseyplug_pll('View All') : __('View All', 'jerseyplug');
$discover_text = function_exists('jerseyplug_pll') ? jerseyplug_pll('Discover') : __('Discover', 'jerseyplug');
?>

<section id="product-categories" class="bg-lightBg py-16">
	<div class="container mx-auto px-4">

		<!-- Section Header -->
		<div class="mb-8 flex items-end justify-between md:mb-10">
			<h2 class="text-2xl font-bold text-primary md:text-3xl">
				<?php echo esc_html($section_title); ?>
			</h2>
			<a href="<?php echo esc_url($view_all_link); ?>"
				class="hidden items-center gap-2 font-bold text-primary hover:underline md:flex">
				<?php echo esc_html($view_all_text); ?>
				<span aria-hidden="true">→</span>
			</a>
		</div>

		<!-- Bento Grid -->
		<div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-2 lg:grid-cols-4">
			<?php foreach ($cards as $i => $card) :
				$card_grid    = $grid_classes[$i] ?? 'col-span-1 min-h-[150px] sm:min-h-[200px] md:col-span-1 lg:col-span-1 lg:row-span-1';
				$card_heading = $heading_classes[$i] ?? 'text-base sm:text-lg font-bold text-white md:text-xl uppercase';
				$is_large  = (0 === $i);
				$is_medium = (($count > 1 && 1 === $i) || ($count > 4 && $i > 4));
				$is_small  = (($count >= 4 && (2 === $i || 3 === $i)) || ($count === 5 && 4 === $i));
			?>
				<a href="<?php echo esc_url($card['link']); ?>"
					class="<?php echo esc_attr(trim('group relative overflow-hidden rounded-[24px] shadow-md transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-1 hover:scale-[1.01] hover:shadow-lg ' . $card_grid)); ?>">

					<!-- Card Image -->
					<img
						src="<?php echo esc_url($card['image']); ?>"
						alt="<?php echo esc_attr($card['title']); ?>"
						class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
						<?php echo $i === 0 ? 'fetchpriority="high" loading="eager"' : 'loading="lazy" decoding="async"'; ?>>

					<!-- Gradient Overlay for text readability -->
					<div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-transparent flex flex-col <?php echo $is_medium ? 'justify-center' : 'justify-end'; ?> p-4 md:p-8">

						<h3 class="<?php echo esc_attr($card_heading); ?> cat-title">
							<?php echo esc_html($card['title']); ?>
						</h3>

						<?php if (! empty($card['desc']) && ! $is_small) : ?>
							<p class="mt-1 hidden text-gray-200 md:block <?php echo $is_large ? 'text-base' : 'text-sm'; ?>">
								<?php echo esc_html($card['desc']); ?>
							</p>
						<?php endif; ?>

						<?php if ($is_medium) : ?>
							<span class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-secondary md:text-sm">
								<?php echo esc_html($discover_text); ?>
								<span aria-hidden="true">›</span>
							</span>
						<?php endif; ?>
					</div>
				</a>
			<?php endforeach; ?>
		</div>

		<!-- Mobile "View All" link -->
		<div class="mt-4 text-center md:hidden">
			<a href="<?php echo esc_url($view_all_link); ?>"
				class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">
				<?php echo esc_html($view_all_text); ?>
				<span aria-hidden="true">→</span>
			</a>
		</div>

	</div>
</section>