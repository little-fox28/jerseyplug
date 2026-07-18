<?php

/**
 * Floating Social Links component.
 *
 * @package JerseyPlug
 */

$get_setting = function ($key, $default = '') {
	if (function_exists('get_jerseyplug_setting')) {
		return get_jerseyplug_setting($key, $default);
	}
	return get_theme_mod($key, $default);
};

$socials = [
	'facebook'  => [
		'url'   => $get_setting('jerseyplug_social_facebook', ''),
		'icon'  => '<svg class="w-14 h-14" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M13.5 9H16V6h-2.2C11.5 6 10 7.5 10 9.7V12H8v3h2v6h3v-6h2.4l.6-3H13V9.8c0-.5.3-.8.8-.8Z" /></svg>',
		'label' => __('Facebook', 'jerseyplug'),
		'color' => 'bg-[#1877F2] hover:bg-[#1877F2]/90', // Facebook Blue
	],
	'instagram' => [
		'url'   => $get_setting('jerseyplug_social_instagram', ''),
		'icon'  => '<svg class="w-14 h-14" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17" cy="7" r="1.5" fill="currentColor" stroke="none"></circle></svg>',
		'label' => __('Instagram', 'jerseyplug'),
		'color' => 'bg-gradient-to-tr from-[#FD1D1D] to-[#833AB4] hover:opacity-90', // Instagram Gradient
	],
	'tiktok'    => [
		'url'   => $get_setting('jerseyplug_social_tiktok', ''),
		'icon'  => '<svg class="w-14 h-14" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 15.66a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1.04-.1z"></path></svg>',
		'label' => __('TikTok', 'jerseyplug'),
		'color' => 'bg-black hover:bg-black/90', // TikTok Black
	],
	'twitter'   => [
		'url'   => $get_setting('jerseyplug_social_twitter', ''),
		'icon'  => '<svg class="w-14 h-14" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M18.9 7.4c.7 6.8-4.8 11.8-11.1 11.8-2.2 0-4.3-.6-6-1.8 2.1.3 4.2-.3 5.8-1.7-1.7 0-3.2-1.1-3.7-2.7.6.1 1.2.1 1.7-.1-1.9-.4-3.1-2.2-2.7-4 .6.4 1.4.7 2.2.8-1.7-1.2-1.9-3.7-.6-5.1 2 2.4 5 4 8.4 4.2-.5-2.1 1.1-4.2 3.3-4.2 1 0 1.9.4 2.6 1.1.8-.2 1.5-.5 2.2-.9-.3.8-.8 1.4-1.5 1.9.7-.1 1.3-.3 1.9-.6-.5.7-1.1 1.3-1.8 1.8Z" /></svg>',
		'label' => __('Twitter', 'jerseyplug'),
		'color' => 'bg-[#1DA1F2] hover:bg-[#1DA1F2]/90', // Twitter Blue
	],
	'youtube'   => [
		'url'   => $get_setting('jerseyplug_social_youtube', ''),
		'icon'  => '<svg class="w-14 h-14" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M21.6 7.2c-.2-.8-.8-1.4-1.6-1.6C18.5 5.2 12 5.2 12 5.2s-6.5 0-8 .4c-.8.2-1.4.8-1.6 1.6-.4 1.5-.4 4.8-.4 4.8s0 3.3.4 4.8c.2.8.8 1.4 1.6 1.6 1.5.4 8 .4 8 .4s6.5 0 8-.4c.8-.2 1.4-.8 1.6-1.6.4-1.5.4-4.8.4-4.8s0-3.3-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z" /></svg>',
		'label' => __('YouTube', 'jerseyplug'),
		'color' => 'bg-[#FF0000] hover:bg-[#FF0000]/90', // YouTube Red
	],
];

// Check if any links exist
$has_links = false;
foreach ($socials as $social) {
	if (!empty($social['url'])) {
		$has_links = true;
		break;
	}
}

if (!$has_links) {
	return; // Don't render anything if no URLs are provided
}
?>

<div class="fixed right-4 bottom-10 md:bottom-8 z-40 flex flex-col gap-3 group">
	<?php foreach ($socials as $key => $social) : ?>
		<?php if (!empty($social['url'])) : ?>
			<a
				href="<?php echo esc_url($social['url']); ?>"
				target="_blank"
				rel="noopener noreferrer"
				aria-label="<?php echo esc_attr($social['label']); ?>"
				class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center rounded-full text-white shadow-lg shadow-black/20 hover:scale-110 transition-all duration-300 <?php echo esc_attr($social['color']); ?>">
				<?php echo $social['icon']; // Icon SVG is hardcoded above, safe to output unescaped 
				?>
			</a>
		<?php endif; ?>
	<?php endforeach; ?>
</div>