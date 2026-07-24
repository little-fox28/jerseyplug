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
 * Fix CSS jumping in Block Editor caused by Vite HMR.
 *
 * When the Vite dev server is running, TailPress's default behavior is to inject
 * editor-style.css via a WebSocket-connected Vite module URL into the editor iframe.
 * This causes the editor styles to reload on every HMR event, making the editor "jump".
 *
 * Solution: Always serve the BUILT editor-style.css from dist/ (bypassing HMR),
 * while still allowing Vite to rebuild it when the source changes on next build/refresh.
 */
add_action('after_setup_theme', function () {
	// Force remove whatever URL TailPress's ViteCompiler registered (may be a Vite HMR URL)
	add_action('admin_init', function () {
		$manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
		$editor_css_uri = null;

		if (file_exists($manifest_path)) {
			$manifest = json_decode(file_get_contents($manifest_path), true);
			$file = $manifest['resources/css/editor-style.css']['file'] ?? null;
			if ($file) {
				$editor_css_uri = get_template_directory_uri() . '/dist/' . $file;
			}
		}

		if (!$editor_css_uri) {
			// Fallback: serve directly from source (no Tailwind compile, but safe)
			$src_path = get_template_directory() . '/resources/css/editor-style.css';
			$editor_css_uri = get_template_directory_uri() . '/resources/css/editor-style.css'
				. '?v=' . (file_exists($src_path) ? filemtime($src_path) : '1');
		}

		// Remove all existing registered editor stylesheets and re-register only ours
		global $editor_styles;
		$editor_styles = [];
		add_theme_support('editor-styles');
		add_editor_style($editor_css_uri);
	}, 20);
});

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
