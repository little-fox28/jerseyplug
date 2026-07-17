<?php

/**
 * Asset enqueuing.
 *
 * @package JerseyPlug
 */

function jerseyplug_register_all_assets(): void
{
	TailPress\Framework\Theme::instance()
		->assets(
			fn($manager) => $manager
				->withCompiler(
					new TailPress\Framework\Assets\ViteCompiler(),
					function ($compiler) {
						// Register main assets
						$compiler->registerAsset('resources/css/app.css')
							->registerAsset('resources/js/app.js')
							->editorStyleFile('resources/css/editor-style.css');

						// Conditionally register products-filter.js
						$is_products_page = (function_exists('is_shop') && (is_shop() || is_product_taxonomy()))
							|| is_page_template('pages/products-page.php');

						if ($is_products_page) {
							$compiler->registerAsset('resources/js/products-filter.js');
						}

						return $compiler;
					}
				)
				->enqueueAssets()
		);
}
add_action('wp', 'jerseyplug_register_all_assets');

/**
 * Add 'defer' attribute to all TailPress scripts to ensure DOM is ready.
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
	if (strpos($src, 'resources/js/') !== false && strpos($tag, 'defer') === false) {
		return str_replace(' src', ' defer="defer" src', $tag);
	}
	return $tag;
}, 10, 3);

/**
 * Enqueue Alpine.js from CDN.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_script('alpine-collapse', 'https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js', [], null, ['strategy' => 'defer']);
		wp_enqueue_script('alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', ['alpine-collapse'], null, ['strategy' => 'defer']);
	}
);

/**
 * Remove unnecessary scripts and styles.
 */
function jerseyplug_remove_header_scripts()
{
	wp_deregister_script('wp-embed');
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	wp_dequeue_style('wc-block-style');
}
add_action('wp_enqueue_scripts', 'jerseyplug_remove_header_scripts', 100);
