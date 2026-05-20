<?php
/**
 * Asset enqueuing.
 *
 * @package JerseyPlug
 */

function jerseyplug_register_assets(): void {
	TailPress\Framework\Theme::instance()
		->assets(
			fn( $manager ) => $manager
				->withCompiler(
					new TailPress\Framework\Assets\ViteCompiler(),
					fn( $compiler ) => $compiler
						->registerAsset( 'resources/css/app.css' )
						->registerAsset( 'resources/js/app.js' )
						->editorStyleFile( 'resources/css/editor-style.css' )
				)
				->enqueueAssets()
		);
}
jerseyplug_register_assets();

/**
 * Enqueue Alpine.js from CDN.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', [], null, [ 'strategy' => 'defer' ] );
	}
);

/**
 * Remove unnecessary scripts and styles.
 */
function jerseyplug_remove_header_scripts() {
	// Loại bỏ WP Embed (Nếu bạn không dán link từ web khác để nó tự render)
	wp_deregister_script( 'wp-embed' );

	// Loại bỏ các CSS mặc định của Block Editor (Gutenberg) nếu bạn code tay toàn bộ
	// Cẩn thận: Nếu bạn dùng block editor cho bài viết thì không nên xóa dòng này
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-block-style' ); // Loại bỏ CSS của WooCommerce Blocks
}
add_action( 'wp_enqueue_scripts', 'jerseyplug_remove_header_scripts', 100 );
