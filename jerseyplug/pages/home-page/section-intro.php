<?php
/**
 * Homepage intro/content section.
 *
 * @package JerseyPlug
 */

$args    = wp_parse_args( $args ?? [], [ 'page_id' => 0 ] );
$page_id = (int) $args['page_id'];
$content = trim( (string) get_post_field( 'post_content', $page_id ) );

if ( $content === '' ) {
	return;
}
?>

<section class="bg-white py-10 md:py-14">
	<div class="container mx-auto px-4">
		<div class="mx-auto max-w-4xl rounded-2xl border border-gray-100 bg-gray-50 px-6 py-8 shadow-sm md:px-10 md:py-12">
			<div class="entry-content prose max-w-none prose-headings:text-primary prose-a:text-primary">
				<?php echo apply_filters( 'the_content', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</section>
