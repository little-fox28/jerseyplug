<?php

/**
 * Footer component.
 *
 * @package JerseyPlug
 */

$current_year = (int) gmdate('Y');
$shop_menu_args = [
	'theme_location' => 'footer_shop',
	'container'      => false,
	'menu_class'     => 'list-none',
	'fallback_cb'    => false,
	'depth'          => 1,
];

$support_menu_args = [
	'theme_location' => 'footer_support',
	'container'      => false,
	'menu_class'     => 'list-none',
	'fallback_cb'    => false,
	'depth'          => 1,
];

$legal_menu_args = [
	'theme_location' => 'footer-legal',
	'container'      => false,
	'menu_class'     => 'list-none flex flex-wrap justify-center gap-4 md:gap-6 text-xs text-gray-400 [&_a]:text-gray-400 [&_a]:hover:text-yellow-400',
	'fallback_cb'    => false,
	'depth'          => 1,
];

$locations = get_nav_menu_locations();

$shop_title = jerseyplug_pll('Shop');
if (! empty($locations['footer_shop'])) {
	$shop_menu = wp_get_nav_menu_object($locations['footer_shop']);
	if ($shop_menu instanceof WP_Term && ! empty($shop_menu->name)) {
		$shop_title = $shop_menu->name;
	}
}

$support_title = jerseyplug_pll('Support');
if (! empty($locations['footer_support'])) {
	$support_menu = wp_get_nav_menu_object($locations['footer_support']);
	if ($support_menu instanceof WP_Term && ! empty($support_menu->name)) {
		$support_title = $support_menu->name;
	}
}

$get_setting = function ($key, $default = '') {
	if (function_exists('get_jerseyplug_setting')) {
		return get_jerseyplug_setting($key, $default);
	}

	return get_theme_mod($key, $default);
};

$contact_address    = $get_setting('jerseyplug_contact_address', '');
$contact_phone      = $get_setting('jerseyplug_contact_phone', '');
$contact_email      = $get_setting('jerseyplug_contact_email', '');
?>

<?php do_action('jerseyplug_before_footer'); ?>

<footer class="bg-primary text-white border-t-4 border-accent" role="contentinfo">
	<div class="container mx-auto px-4 py-10 md:py-16">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-8">
			<div class="space-y-6">
				<?php
				get_template_part(
					'components/ui/logo',
					null,
					[
						'class'         => 'w-auto !h-20 !md:h-24 lg:!h-28',
						'img_class'     => 'object-contain transition-all',
						'wrapper_class' => 'flex items-center group',
						'aria_label'    => jerseyplug_pll('JerseyPlug home'),
						'loading'       => 'lazy',
						'decoding'      => 'async',
					]
				);
				?>
				<p class="text-gray-400 text-sm leading-relaxed max-w-xs">
					<?php echo esc_html(jerseyplug_pll('Footer Description')); ?>
				</p>
			</div>

			<div>
				<h4 class="font-bold text-lg mb-4 md:mb-6 text-white"><?php echo esc_html($shop_title); ?></h4>
				<nav class="[&_ul]:space-y-2.5 [&_ul]:text-sm [&_ul]:list-none [&_a]:text-gray-400 [&_a]:transition-colors [&_a:hover]:text-secondary">
					<?php
					wp_nav_menu($shop_menu_args);
					?>
				</nav>
			</div>

			<div>
				<h4 class="font-bold text-lg mb-4 md:mb-6 text-white"><?php echo esc_html($support_title); ?></h4>
				<nav class="[&_ul]:space-y-2.5 [&_ul]:text-sm [&_ul]:list-none [&_a]:text-gray-400 [&_a]:transition-colors [&_a:hover]:text-secondary">
					<?php
					wp_nav_menu($support_menu_args);
					?>
				</nav>
			</div>

			<div class="space-y-6">
				<h4 class="font-bold text-lg mb-4 md:mb-6 text-white"><?php echo esc_html(jerseyplug_pll('Contact')); ?></h4>
				<div class="space-y-4">
					<div class="flex items-start gap-3 text-gray-400 text-sm group">
						<svg class="w-4 h-4 text-gray-400 group-hover:text-secondary shrink-0 mt-0.5" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M12 21s-6-5.3-6-10a6 6 0 1 1 12 0c0 4.7-6 10-6 10Z"></path>
							<circle cx="12" cy="11" r="2.5"></circle>
						</svg>
						<span class="group-hover:text-secondary transition-colors">
							<?php echo esc_html($contact_address); ?>
						</span>
					</div>
					<div class="flex items-center gap-3 text-gray-400 text-sm group">
						<svg class="w-4 h-4 text-gray-400 group-hover:text-secondary shrink-0" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M22 16.9v2a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 3 4.2 2 2 0 0 1 5 2h2a2 2 0 0 1 2 1.7l.4 2a2 2 0 0 1-.5 1.9l-1 1a16 16 0 0 0 6 6l1-1a2 2 0 0 1 1.9-.5l2 .4A2 2 0 0 1 22 16.9Z"></path>
						</svg>
						<span class="group-hover:text-secondary transition-colors">
							<?php echo esc_html($contact_phone); ?>
						</span>
					</div>
					<div class="flex items-center gap-3 text-gray-400 text-sm group">
						<svg class="w-4 h-4 text-gray-400 group-hover:text-secondary shrink-0" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="5" width="18" height="14" rx="2"></rect>
							<polyline points="3 7 12 13 21 7"></polyline>
						</svg>
						<span class="group-hover:text-secondary transition-colors">
							<?php echo esc_html($contact_email); ?>
						</span>
					</div>
				</div>

				<div class="pt-6 border-t border-white/10">
					<p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3"><?php echo esc_html(jerseyplug_pll('We Accept')); ?></p>
					<div class="flex flex-wrap gap-2">
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-[#1A1F71] text-white italic">VISA</span>
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-gradient-to-r from-[#EB001B] to-[#F79E1B] text-white">MASTERCARD</span>
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-[#E3000F] text-white">PAYFAST</span>
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-black text-[#00E5FF]">PAYJUSTNOW</span>
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-[#00E573] text-black">OZOW</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="bg-black/20 border-t border-white/10">
		<div class="container mx-auto px-4 py-6">
			<div class="flex flex-col-reverse md:flex-row justify-between items-center gap-4 text-xs text-gray-400">
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d is the current year. */
							jerseyplug_pll('© %d JerseyPlug. All rights reserved.'),
							$current_year
						)
					);
					?>
				</p>
				<?php
				wp_nav_menu($legal_menu_args);
				?>
			</div>
		</div>
	</div>
</footer>

<?php do_action('jerseyplug_after_footer'); ?>