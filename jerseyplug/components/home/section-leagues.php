<?php
/**
 * Homepage leagues section.
 *
 * @package JerseyPlug
 */

$args   = wp_parse_args( $args ?? [], [ 'page_id' => 0 ] );
$leagues = jerseyplug_get_homepage_featured_leagues();

if ( empty( $leagues ) ) {
	return;
}

do_action( 'jerseyplug_before_home_leagues', $leagues, (int) $args['page_id'] );
?>

<section class="bg-white py-12 md:py-16">
	<div class="container mx-auto px-4">
		<div class="mb-8 text-center md:mb-12">
			<h2 class="mb-4 text-2xl font-bold text-primary md:text-3xl">
				<?php echo esc_html( jerseyplug_pll( 'Top Leagues' ) ); ?>
			</h2>
			<div class="mx-auto h-1 w-16 rounded bg-accent"></div>
		</div>

		<div class="grid grid-cols-3 justify-items-center gap-4 md:gap-8 lg:grid-cols-6">
			<?php foreach ( $leagues as $league ) : ?>
				<a href="<?php echo esc_url( $league['url'] ?? '#' ); ?>" class="group flex flex-col items-center gap-2 md:gap-4">
					<div class="flex h-16 w-16 items-center justify-center p-2 transition-transform duration-300 group-hover:scale-110 md:h-24 md:w-24">
						<img src="<?php echo esc_url( $league['logo'] ?? '' ); ?>" alt="<?php echo esc_attr( $league['name'] ?? '' ); ?>" class="h-full w-full object-contain drop-shadow-md" loading="lazy" decoding="async" />
					</div>
					<span class="text-center text-xs font-bold text-gray-800 group-hover:text-primary md:text-base">
						<?php echo esc_html( $league['name'] ?? '' ); ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php do_action( 'jerseyplug_after_home_leagues', $leagues, (int) $args['page_id'] ); ?>
