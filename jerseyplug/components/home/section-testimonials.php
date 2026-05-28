<?php
/**
 * Testimonials section.
 *
 * @package JerseyPlug
 */

$args         = wp_parse_args( $args ?? [], [ 'page_id' => 0 ] );
$testimonials = jerseyplug_get_homepage_testimonials();

if ( empty( $testimonials ) ) {
	return;
}

do_action( 'jerseyplug_before_home_testimonials', $testimonials, (int) $args['page_id'] );
?>

<section class="bg-white py-16">
	<div class="container mx-auto px-4">
		<div class="mb-12 text-center">
			<h2 class="mb-4 text-2xl font-bold text-primary md:text-3xl">
				<?php echo esc_html( jerseyplug_pll( 'Testimonials' ) ); ?>
			</h2>
			<div class="mx-auto h-1 w-16 rounded bg-accent"></div>
		</div>

		<div class="grid gap-4 md:grid-cols-3 md:gap-8">
			<?php foreach ( $testimonials as $testimonial ) : ?>
				<div class="rounded-2xl border border-gray-100 bg-gray-50 p-6 shadow-sm">
					<p class="mb-4 text-sm leading-7 text-textSub md:text-base">
						"<?php echo esc_html( $testimonial['quote'] ?? '' ); ?>"
					</p>
					<p class="font-bold text-primary">
						<?php echo esc_html( $testimonial['name'] ?? '' ); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php do_action( 'jerseyplug_after_home_testimonials', $testimonials, (int) $args['page_id'] ); ?>
