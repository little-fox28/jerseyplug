<?php
/**
 * Editor configuration and routing.
 *
 * @package JerseyPlug
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'jerseyplug_custom_editor_routing' ) ) {
	/**
	 * Route post types to either Block Editor (Gutenberg) or Classic Editor.
	 *
	 * @param bool   $use_block_editor Whether the post type can be edited with the block editor.
	 * @param string $post_type        The post type being checked.
	 * @return bool
	 */
	function jerseyplug_custom_editor_routing( bool $use_block_editor, string $post_type ): bool {
		$block_editor_enabled = [
			'page' => true,  // Bật Block Editor cho các Trang (Page) để dùng Lazy Blocks
			'post' => false, // Tắt Block Editor (dùng Classic) cho bài viết (Post)
		];

		if ( isset( $block_editor_enabled[ $post_type ] ) ) {
			return $block_editor_enabled[ $post_type ];
		}

		// Giữ nguyên mặc định cho các post type khác
		return $use_block_editor;
	}
}

// Bắt buộc ưu tiên cao (999) để không bị các plugin khác ghi đè
add_filter( 'use_block_editor_for_post_type', 'jerseyplug_custom_editor_routing', 999, 2 );
// Hỗ trợ thêm cho trường hợp plugin Gutenberg đang được kích hoạt riêng
add_filter( 'gutenberg_can_edit_post_type', 'jerseyplug_custom_editor_routing', 999, 2 );
