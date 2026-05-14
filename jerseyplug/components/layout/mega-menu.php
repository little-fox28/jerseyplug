<?php
/**
 * Dynamic WooCommerce mega menu.
 *
 * @package JerseyPlug
 */

$args = wp_parse_args(
	$args ?? [],
	[
		'root_slug' => '',
		'mode'      => 'desktop',
	]
);

$root_slug = sanitize_title( (string) $args['root_slug'] );
$mode      = $args['mode'] === 'mobile' ? 'mobile' : 'desktop';

if ( $root_slug === '' || ! function_exists( 'jerseyplug_get_product_category_tree' ) ) {
	return;
}

$lang      = function_exists( 'pll_current_language' ) ? (string) pll_current_language( 'slug' ) : 'default';
$cache_key = sprintf( 'jerseyplug_mega_menu_%s_%s_%s', $mode, $root_slug, $lang );
$cached    = get_transient( $cache_key );

if ( is_string( $cached ) ) {
	echo $cached; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$tree = jerseyplug_get_product_category_tree( $root_slug );

ob_start();
if ( ! empty( $tree ) ) :
	if ( $mode === 'desktop' ) :
		?>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
			<?php foreach ( $tree as $branch ) : ?>
				<div class="flex flex-col border-r border-gray-100 last:border-0 pr-4">
					<div class="flex items-center gap-3 mb-4 pb-2 border-b border-gray-100">
						<img
							src="<?php echo esc_url( $branch['logo_url'] ); ?>"
							alt="<?php echo esc_attr( jerseyplug_pll( $branch['term']->name ) ); ?>"
							class="w-8 h-8 rounded-full object-cover"
							loading="lazy"
							decoding="async"
						/>
						<a href="<?php echo esc_url( get_term_link( $branch['term'] ) ); ?>" class="font-bold text-sm text-primary hover:underline">
							<?php echo esc_html( jerseyplug_pll( $branch['term']->name ) ); ?>
						</a>
					</div>
					<ul class="space-y-3">
						<?php foreach ( $branch['children'] as $grandchild ) : ?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $grandchild ) ); ?>" class="flex items-center gap-3 hover:bg-gray-50 p-1.5 rounded group">
									<img
										src="<?php echo esc_url( jerseyplug_get_category_logo_url( (int) $grandchild->term_id ) ); ?>"
										alt="<?php echo esc_attr( jerseyplug_pll( $grandchild->name ) ); ?>"
										class="w-6 h-6 object-cover rounded-full"
										loading="lazy"
										decoding="async"
									/>
									<span class="text-sm text-gray-600 group-hover:text-primary transition-colors">
										<?php echo esc_html( jerseyplug_pll( $grandchild->name ) ); ?>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	else :
		?>
		<div class="max-h-64 overflow-y-auto space-y-3 pr-2">
			<?php foreach ( $tree as $branch ) : ?>
				<div>
					<a href="<?php echo esc_url( get_term_link( $branch['term'] ) ); ?>" @click="isMobileMenuOpen = false" class="flex items-center gap-2 text-white py-1 text-sm font-semibold hover:opacity-80">
						<img
							src="<?php echo esc_url( $branch['logo_url'] ); ?>"
							alt="<?php echo esc_attr( jerseyplug_pll( $branch['term']->name ) ); ?>"
							class="w-5 h-5 object-cover rounded-full"
							loading="lazy"
							decoding="async"
						/>
						<?php echo esc_html( jerseyplug_pll( $branch['term']->name ) ); ?>
					</a>

					<?php if ( ! empty( $branch['children'] ) ) : ?>
						<div class="pl-7 pt-1 space-y-1">
							<?php foreach ( $branch['children'] as $grandchild ) : ?>
								<a href="<?php echo esc_url( get_term_link( $grandchild ) ); ?>" @click="isMobileMenuOpen = false" class="flex items-center gap-2 text-gray-300 py-1 text-sm hover:text-white">
									<img
										src="<?php echo esc_url( jerseyplug_get_category_logo_url( (int) $grandchild->term_id ) ); ?>"
										alt="<?php echo esc_attr( jerseyplug_pll( $grandchild->name ) ); ?>"
										class="w-4 h-4 object-cover rounded-full"
										loading="lazy"
										decoding="async"
									/>
									<?php echo esc_html( jerseyplug_pll( $grandchild->name ) ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	endif;
endif;

$html = (string) ob_get_clean();
set_transient( $cache_key, $html, DAY_IN_SECONDS );
echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
