<?php
/**
 * Size Guide Modal Component
 *
 * @package JerseyPlug
 */

defined('ABSPATH') || exit;

// Size Guide Modal Content
$size_guide_post = get_page_by_path('size-guide', OBJECT, 'post');
if (!$size_guide_post) {
	$size_guide_post = get_page_by_path('size-guide', OBJECT, 'page');
}
if (!$size_guide_post) {
	$posts = get_posts([
		'name'        => 'size-guide',
		'post_type'   => 'any',
		'numberposts' => 1,
	]);
	if (!empty($posts)) {
		$size_guide_post = $posts[0];
	}
}

$size_guide_content = '';
if ($size_guide_post instanceof WP_Post) {
	$size_guide_content = apply_filters('the_content', $size_guide_post->post_content);
} else {
	$size_guide_content = '<h3>' . esc_html( jerseyplug_pll( 'No content' ) ) . '</h3>';
}
?>

<!-- Size Guide Modal -->
<div
	x-data="{ isOpen: false }"
	@open-size-guide.window="isOpen = true"
	x-init="if (!sessionStorage.getItem('size_guide_shown')) { isOpen = true; sessionStorage.setItem('size_guide_shown', 'true'); }"
	x-show="isOpen"
	x-cloak
	class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
	
	<!-- Backdrop click closer -->
	<div class="absolute inset-0" @click="isOpen = false"></div>

	<div class="bg-white rounded-2xl w-fit max-w-[95vw] md:max-w-3xl lg:max-w-4xl p-6 relative shadow-2xl z-10 animate-in zoom-in-95 duration-200">
		<!-- Close button -->
		<button
			type="button"
			@click="isOpen = false"
			class="absolute top-4 right-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors"
			aria-label="<?php echo esc_attr(jerseyplug_pll('Close')); ?>">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<line x1="18" y1="6" x2="6" y2="18"></line>
				<line x1="6" y1="6" x2="18" y2="18"></line>
			</svg>
		</button>

		<!-- Header -->
		<div class="flex items-center gap-2 mb-4">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 text-primary" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M5 12h14"></path>
				<path d="M7 9v6"></path>
				<path d="M11 9v6"></path>
				<path d="M15 9v6"></path>
				<path d="M19 9v6"></path>
			</svg>
			<h3 class="text-xl font-bold text-primary">
				<?php echo esc_html(jerseyplug_pll('Size Guide')); ?>
			</h3>
		</div>

		<div class="prose prose-sm prose-zinc max-w-none text-gray-600 max-h-[75vh] overflow-y-auto pr-1">
			<?php echo wp_kses_post($size_guide_content); ?>
		</div>
	</div>
</div>
