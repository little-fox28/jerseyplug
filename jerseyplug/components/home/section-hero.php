<?php
/**
 * Homepage hero section.
 *
 * @package JerseyPlug
 */

$args    = wp_parse_args( $args ?? [], [ 'page_id' => 0 ] );
$page_id = (int) $args['page_id'];
$slides  = jerseyplug_get_homepage_hero_slides( $page_id );

if ( empty( $slides ) ) {
	return;
}

do_action( 'jerseyplug_before_home_hero', $slides, $page_id );
?>

<section class="relative isolate overflow-hidden bg-gray-900 text-white" data-home-slider>
	<div class="absolute inset-0">
		<?php foreach ( $slides as $index => $slide ) : ?>
			<?php
			$is_active = 0 === (int) $index;
			$image     = $slide['image'] ?? '';
			?>
			<div
				data-home-slide
				class="absolute inset-0 transition-opacity duration-1000 ease-in-out <?php echo $is_active ? 'opacity-100' : 'opacity-0'; ?>"
			>
				<?php if ( $image !== '' ) : ?>
					<img
						src="<?php echo esc_url( $image ); ?>"
						alt="<?php echo esc_attr( jerseyplug_pll( 'Hero image' ) ); ?>"
						class="absolute inset-0 h-full w-full object-cover brightness-75"
						loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>"
						decoding="async"
					/>
				<?php endif; ?>
				<div class="absolute inset-0 bg-linear-to-r from-primary/95 via-primary/60 to-transparent"></div>
				<div class="relative container mx-auto flex h-full items-center px-4 py-20 md:py-28 lg:py-32">
					<div class="max-w-2xl">
						<?php if ( ! empty( $slide['badge'] ) ) : ?>
							<span class="mb-4 inline-flex rounded-sm bg-secondary px-3 py-1 text-xs font-bold uppercase tracking-wider text-primary">
								<?php echo esc_html( $slide['badge'] ); ?>
							</span>
						<?php endif; ?>

						<h1 class="mb-6 text-3xl font-bold leading-tight md:text-5xl lg:text-6xl">
							<?php echo wp_kses_post( $slide['title'] ?? '' ); ?>
						</h1>

						<?php if ( ! empty( $slide['desc'] ) ) : ?>
							<p class="mb-8 max-w-xl text-base text-gray-200 md:text-lg lg:text-xl">
								<?php echo wp_kses_post( $slide['desc'] ); ?>
							</p>
						<?php endif; ?>

						<div class="flex flex-wrap gap-4">
							<?php if ( ! empty( $slide['button_label'] ) && ! empty( $slide['button_url'] ) ) : ?>
								<a href="<?php echo esc_url( $slide['button_url'] ); ?>" class="inline-flex items-center gap-2 rounded bg-secondary px-8 py-3 font-bold text-primary shadow-lg transition hover:shadow-xl">
									<?php echo esc_html( $slide['button_label'] ); ?>
								</a>
							<?php endif; ?>

							<?php if ( ! empty( $slide['secondary_label'] ) && ! empty( $slide['secondary_url'] ) ) : ?>
								<a href="<?php echo esc_url( $slide['secondary_url'] ); ?>" class="inline-flex items-center gap-2 rounded border-2 border-white px-8 py-3 font-bold text-white transition hover:bg-white hover:text-primary">
									<?php echo esc_html( $slide['secondary_label'] ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( count( $slides ) > 1 ) : ?>
		<div class="absolute bottom-6 left-1/2 z-20 flex -translate-x-1/2 gap-2 md:bottom-8">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<button
					type="button"
					data-home-slider-dot
					class="rounded-full p-3 transition-all duration-300"
					aria-label="<?php echo esc_attr( sprintf( jerseyplug_pll( 'Go to slide %d' ), $index + 1 ) ); ?>"
				>
					<span class="block h-2 rounded-full transition-all duration-300 <?php echo 0 === (int) $index ? 'w-8 bg-secondary' : 'w-2 bg-white/50'; ?>"></span>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<?php do_action( 'jerseyplug_after_home_hero', $slides, $page_id ); ?>
