<?php
/**
 * Homepage featured categories section.
 *
 * @package JerseyPlug
 */

$args      = wp_parse_args( $args ?? [], [ 'page_id' => 0 ] );
$categories = jerseyplug_get_homepage_featured_categories();

if ( empty( $categories ) ) {
	return;
}

do_action( 'jerseyplug_before_home_categories', $categories, (int) $args['page_id'] );
?>

<section class="bg-lightBg py-16">
	<div class="container mx-auto px-4">
		<div class="mb-8 flex items-end justify-between md:mb-10">
			<h2 class="text-2xl font-bold text-primary md:text-3xl">
				<?php echo esc_html( jerseyplug_pll( 'Product Categories' ) ); ?>
			</h2>
			<a href="<?php echo esc_url( jerseyplug_get_homepage_shop_url() ); ?>" class="hidden items-center gap-2 font-bold text-primary hover:underline md:flex">
				<?php echo esc_html( jerseyplug_pll( 'View All' ) ); ?>
				<span aria-hidden="true">→</span>
			</a>
		</div>

		<div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4">
			<?php foreach ( $categories as $index => $category ) : ?>
				<?php
				$variant = $category['variant'] ?? 'small';
				$card_class = 'col-span-1 row-span-1 min-h-32';
				if ( 'large' === $variant ) {
					$card_class = 'col-span-2 row-span-1 md:row-span-2 min-h-40 md:min-h-full';
				} elseif ( 'medium' === $variant ) {
					$card_class = 'col-span-2 row-span-1 min-h-40';
				}
				?>
				<a href="<?php echo esc_url( $category['url'] ?? '#' ); ?>" class="<?php echo esc_attr( trim( 'group relative overflow-hidden rounded-xl shadow-md ' . $card_class ) ); ?>">
					<?php if ( ! empty( $category['image'] ) ) : ?>
						<img src="<?php echo esc_url( $category['image'] ); ?>" alt="<?php echo esc_attr( $category['name'] ?? '' ); ?>" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy" decoding="async" />
					<?php endif; ?>
					<div class="absolute inset-0 bg-black/40 flex flex-col <?php echo 'medium' === $variant ? 'justify-center' : 'justify-end'; ?> p-4 md:p-8">
						<h3 class="text-lg font-bold text-white <?php echo 'large' === $variant ? 'md:text-3xl' : ( 'medium' === $variant ? 'md:text-2xl' : 'md:text-xl uppercase' ); ?>">
							<?php echo esc_html( $category['name'] ?? '' ); ?>
						</h3>
						<?php if ( ! empty( $category['description'] ) && 'small' !== $variant ) : ?>
							<p class="hidden text-xs text-gray-200 md:block <?php echo 'large' === $variant ? 'text-base' : 'text-sm'; ?>">
								<?php echo esc_html( $category['description'] ); ?>
							</p>
						<?php endif; ?>
						<?php if ( 'medium' === $variant ) : ?>
							<span class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-secondary md:text-sm">
								<?php echo esc_html( jerseyplug_pll( 'Discover' ) ); ?>
								<span aria-hidden="true">›</span>
							</span>
						<?php endif; ?>
					</div>
				</a>
			<?php endforeach; ?>
		</div>

		<div class="mt-4 text-center md:hidden">
			<a href="<?php echo esc_url( jerseyplug_get_homepage_shop_url() ); ?>" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">
				<?php echo esc_html( jerseyplug_pll( 'View All' ) ); ?>
				<span aria-hidden="true">→</span>
			</a>
		</div>
	</div>
</section>

<?php do_action( 'jerseyplug_after_home_categories', $categories, (int) $args['page_id'] ); ?>
