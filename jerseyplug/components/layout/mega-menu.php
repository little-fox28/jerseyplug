<?php

/**
 * Dynamic WooCommerce mega menu.
 *
 * @package JerseyPlug
 */

$args = wp_parse_args(
	$args ?? [],
	[
		'root_slug' => '',
		'mode'      => 'desktop',
	]
);

$root_slug = sanitize_title((string) $args['root_slug']);
$mode      = $args['mode'] === 'mobile' ? 'mobile' : 'desktop';

if ($root_slug === '' || ! function_exists('jerseyplug_get_product_category_tree')) {
	return;
}

$lang      = function_exists('pll_current_language') ? (string) pll_current_language('slug') : 'default';
$cache_key = sprintf('jerseyplug_mega_menu_%s_%s_%s', $mode, $root_slug, $lang);
$cached    = get_transient($cache_key);

if (is_string($cached)) {
	echo $cached; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$tree = jerseyplug_get_product_category_tree($root_slug);
$tree = apply_filters('jerseyplug_mega_menu_tree', $tree, $root_slug, $mode);

$container_classes = $mode === 'desktop'
	? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6'
	: 'max-h-64 overflow-y-auto space-y-3 pr-2';
$container_classes = apply_filters('jerseyplug_mega_menu_container_classes', $container_classes, $root_slug, $mode, $tree);

ob_start();
if (! empty($tree)) :
	do_action('jerseyplug_before_mega_menu', $root_slug, $mode, $tree);
	if ($mode === 'desktop') :
?>
		<div class="<?php echo esc_attr($container_classes); ?>">
			<?php foreach ($tree as $branch) : ?>
				<?php
				do_action('jerseyplug_before_mega_menu_branch', $branch, $root_slug, $mode);
				$branch_label = apply_filters('jerseyplug_mega_menu_branch_label', jerseyplug_pll($branch['term']->name), $branch['term'], $root_slug, $mode);
				$branch_logo  = apply_filters('jerseyplug_mega_menu_branch_logo_url', $branch['logo_url'], $branch['term'], $root_slug, $mode);
				?>
				<div class="flex flex-col border-r border-gray-100 last:border-0 pr-4">
					<div class="flex items-center gap-3 mb-4 pb-2 border-b border-gray-100">
						<img
							src="<?php echo esc_url($branch_logo); ?>"
							alt="<?php echo esc_attr($branch_label); ?>"
							class="w-8 h-8 rounded-full object-cover"
							loading="lazy"
							decoding="async" />
						<a href="<?php echo esc_url(get_term_link($branch['term'])); ?>" class="font-bold text-sm text-primary hover:underline">
							<?php echo esc_html($branch_label); ?>
						</a>
					</div>
					<ul class="space-y-3">
						<?php foreach ($branch['children'] as $grandchild) : ?>
							<?php
							do_action('jerseyplug_before_mega_menu_item', $grandchild, $root_slug, $mode, $branch);
							$item_label = apply_filters('jerseyplug_mega_menu_item_label', jerseyplug_pll($grandchild->name), $grandchild, $root_slug, $mode, $branch['term']);
							$item_logo  = apply_filters('jerseyplug_mega_menu_item_logo_url', jerseyplug_get_category_logo_url((int) $grandchild->term_id), $grandchild, $root_slug, $mode, $branch['term']);
							?>
							<li>
								<a href="<?php echo esc_url(get_term_link($grandchild)); ?>" class="flex items-center gap-3 hover:bg-gray-50 p-1.5 rounded group">
									<img
										src="<?php echo esc_url($item_logo); ?>"
										alt="<?php echo esc_attr($item_label); ?>"
										class="w-6 h-6 object-cover rounded-full"
										loading="lazy"
										decoding="async" />
									<span class="text-sm text-gray-600 group-hover:text-primary transition-colors">
										<?php echo esc_html($item_label); ?>
									</span>
								</a>
							</li>
							<?php do_action('jerseyplug_after_mega_menu_item', $grandchild, $root_slug, $mode, $branch); ?>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php do_action('jerseyplug_after_mega_menu_branch', $branch, $root_slug, $mode); ?>
			<?php endforeach; ?>
		</div>
	<?php
	else :
	?>
		<div class="<?php echo esc_attr($container_classes); ?>">
			<?php foreach ($tree as $branch) : ?>
				<?php
				do_action('jerseyplug_before_mega_menu_branch', $branch, $root_slug, $mode);
				$branch_label = apply_filters('jerseyplug_mega_menu_branch_label', jerseyplug_pll($branch['term']->name), $branch['term'], $root_slug, $mode);
				$branch_logo  = apply_filters('jerseyplug_mega_menu_branch_logo_url', $branch['logo_url'], $branch['term'], $root_slug, $mode);
				?>
				<div>
					<a href="<?php echo esc_url(get_term_link($branch['term'])); ?>" @click="isMobileMenuOpen = false" class="flex items-center gap-2 text-white py-1 text-sm font-semibold hover:opacity-80">
						<img
							src="<?php echo esc_url($branch_logo); ?>"
							alt="<?php echo esc_attr($branch_label); ?>"
							class="w-5 h-5 object-cover rounded-full"
							loading="lazy"
							decoding="async" />
						<?php echo esc_html($branch_label); ?>
					</a>

					<?php if (! empty($branch['children'])) : ?>
						<div class="pl-7 pt-1 space-y-1">
							<?php foreach ($branch['children'] as $grandchild) : ?>
								<?php
								do_action('jerseyplug_before_mega_menu_item', $grandchild, $root_slug, $mode, $branch);
								$item_label = apply_filters('jerseyplug_mega_menu_item_label', jerseyplug_pll($grandchild->name), $grandchild, $root_slug, $mode, $branch['term']);
								$item_logo  = apply_filters('jerseyplug_mega_menu_item_logo_url', jerseyplug_get_category_logo_url((int) $grandchild->term_id), $grandchild, $root_slug, $mode, $branch['term']);
								?>
								<a href="<?php echo esc_url(get_term_link($grandchild)); ?>" @click="isMobileMenuOpen = false" class="flex items-center gap-2 text-gray-300 py-1 text-sm hover:text-white">
									<img
										src="<?php echo esc_url($item_logo); ?>"
										alt="<?php echo esc_attr($item_label); ?>"
										class="w-4 h-4 object-cover rounded-full"
										loading="lazy"
										decoding="async" />
									<?php echo esc_html($item_label); ?>
								</a>
								<?php do_action('jerseyplug_after_mega_menu_item', $grandchild, $root_slug, $mode, $branch); ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php do_action('jerseyplug_after_mega_menu_branch', $branch, $root_slug, $mode); ?>
			<?php endforeach; ?>
		</div>
<?php
	endif;
	do_action('jerseyplug_after_mega_menu', $root_slug, $mode, $tree);
endif;

$html = (string) ob_get_clean();
$cache_ttl = (int) apply_filters('jerseyplug_mega_menu_cache_ttl', 12 * HOUR_IN_SECONDS, $root_slug, $mode, $lang);
if ($cache_ttl <= 0) {
	$cache_ttl = 12 * HOUR_IN_SECONDS;
}
set_transient($cache_key, $html, $cache_ttl);
echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
