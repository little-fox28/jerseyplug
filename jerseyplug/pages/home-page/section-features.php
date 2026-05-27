<?php
/**
 * Why choose us section.
 *
 * @package JerseyPlug
 */

$args     = wp_parse_args( $args ?? [], [ 'page_id' => 0 ] );
$features = jerseyplug_get_homepage_features();

if ( empty( $features ) ) {
	return;
}

do_action( 'jerseyplug_before_home_features', $features, (int) $args['page_id'] );
?>

<section class="bg-white py-12 md:py-16">
	<div class="container mx-auto px-4">
		<div class="mb-8 text-center md:mb-16">
			<h2 class="mb-2 text-2xl font-bold text-primary md:mb-4 md:text-3xl">
				<?php echo esc_html( jerseyplug_pll( 'Why JerseyPlug' ) ); ?>
			</h2>
			<p class="mx-auto max-w-2xl text-sm text-textSub md:text-base">
				<?php echo esc_html( jerseyplug_pll( 'Quality gear, responsive support, and a better shopping experience.' ) ); ?>
			</p>
		</div>

		<div class="grid grid-cols-2 gap-4 md:gap-8 lg:grid-cols-4">
			<?php foreach ( $features as $feature ) : ?>
				<div class="flex h-32 flex-col items-center justify-center rounded-xl border border-gray-100 bg-gray-50 p-4 text-center shadow-sm md:h-auto md:p-6">
					<div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-accent/20 md:mb-6 md:h-16 md:w-16">
						<?php echo wp_kses_post( jerseyplug_get_homepage_feature_icon( $feature['icon'] ?? '' ) ); ?>
					</div>
					<h3 class="mb-1 text-sm font-bold text-textMain md:text-lg">
						<?php echo esc_html( $feature['title'] ?? '' ); ?>
					</h3>
					<p class="text-[10px] text-textSub md:text-sm">
						<?php echo esc_html( $feature['desc'] ?? '' ); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php do_action( 'jerseyplug_after_home_features', $features, (int) $args['page_id'] ); ?>
