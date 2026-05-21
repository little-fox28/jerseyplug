<?php
/**
 * Footer component.
 *
 * @package JerseyPlug
 */

$current_year = (int) gmdate( 'Y' );
$shop_menu_args = [
	'theme_location' => 'footer_shop',
	'container'      => false,
	'menu_class'     => 'list-none',
	'fallback_cb'    => false,
	'depth'          => 1,
];

$support_menu_args = [
	'theme_location' => 'footer_support',
	'container'      => false,
	'menu_class'     => 'list-none',
	'fallback_cb'    => false,
	'depth'          => 1,
];

$legal_menu_args = [
	'theme_location' => 'footer-legal',
	'container'      => false,
	'menu_class'     => 'list-none flex flex-wrap justify-center gap-4 md:gap-6 text-xs text-gray-400 [&_a]:text-gray-400 [&_a]:hover:text-yellow-400',
	'fallback_cb'    => false,
	'depth'          => 1,
];

$locations = get_nav_menu_locations();

$shop_title = __( 'Shop', 'jerseyplug' );
if ( ! empty( $locations['footer_shop'] ) ) {
	$shop_menu = wp_get_nav_menu_object( $locations['footer_shop'] );
	if ( $shop_menu instanceof WP_Term && ! empty( $shop_menu->name ) ) {
		$shop_title = $shop_menu->name;
	}
}

$support_title = __( 'Support', 'jerseyplug' );
if ( ! empty( $locations['footer_support'] ) ) {
	$support_menu = wp_get_nav_menu_object( $locations['footer_support'] );
	if ( $support_menu instanceof WP_Term && ! empty( $support_menu->name ) ) {
		$support_title = $support_menu->name;
	}
}

$get_setting = function( $key, $default = '' ) {
	if ( function_exists( 'get_jerseyplug_setting' ) ) {
		return get_jerseyplug_setting( $key, $default );
	}

	return get_theme_mod( $key, $default );
};

$contact_address    = $get_setting( 'jerseyplug_contact_address', '' );
$contact_phone      = $get_setting( 'jerseyplug_contact_phone', '' );
$contact_email      = $get_setting( 'jerseyplug_contact_email', '' );
$facebook_url       = $get_setting( 'jerseyplug_social_facebook', '' );
$instagram_url      = $get_setting( 'jerseyplug_social_instagram', '' );
$twitter_url        = $get_setting( 'jerseyplug_social_twitter', '' );
$youtube_url        = $get_setting( 'jerseyplug_social_youtube', '' );
?>

<?php do_action( 'jerseyplug_before_footer' ); ?>

<footer class="bg-primary text-white border-t-4 border-accent" role="contentinfo">
	<div class="container mx-auto px-4 py-10 md:py-16">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-8">
			<div class="space-y-6">
				<?php
				get_template_part(
					'components/ui/logo',
					null,
					[
						'class'         => 'w-auto h-30',
						'img_class'     => 'object-contain transition-all',
						'wrapper_class' => 'flex items-center group',
						'aria_label'    => __( 'JerseyPlug home', 'jerseyplug' ),
						'loading'       => 'lazy',
						'decoding'      => 'async',
					]
				);
				?>
				<p class="text-gray-400 text-sm leading-relaxed max-w-xs">
					<?php echo esc_html( __( 'Premium football jerseys delivered across South Africa with fast, secure checkout.', 'jerseyplug' ) ); ?>
				</p>
				<div class="flex gap-3">
					<?php if ( ! empty( $facebook_url ) ) : ?>
						<a href="<?php echo esc_url( $facebook_url ); ?>" aria-label="<?php echo esc_attr( __( 'Facebook', 'jerseyplug' ) ); ?>" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-yellow-400 transition-all duration-300 group">
							<svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M13.5 9H16V6h-2.2C11.5 6 10 7.5 10 9.7V12H8v3h2v6h3v-6h2.4l.6-3H13V9.8c0-.5.3-.8.8-.8Z"/></svg>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $instagram_url ) ) : ?>
						<a href="<?php echo esc_url( $instagram_url ); ?>" aria-label="<?php echo esc_attr( __( 'Instagram', 'jerseyplug' ) ); ?>" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-yellow-400 transition-all duration-300 group">
							<svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17" cy="7" r="1.5" fill="currentColor" stroke="none"></circle></svg>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $twitter_url ) ) : ?>
						<a href="<?php echo esc_url( $twitter_url ); ?>" aria-label="<?php echo esc_attr( __( 'Twitter', 'jerseyplug' ) ); ?>" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-yellow-400 transition-all duration-300 group">
							<svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M18.9 7.4c.7 6.8-4.8 11.8-11.1 11.8-2.2 0-4.3-.6-6-1.8 2.1.3 4.2-.3 5.8-1.7-1.7 0-3.2-1.1-3.7-2.7.6.1 1.2.1 1.7-.1-1.9-.4-3.1-2.2-2.7-4 .6.4 1.4.7 2.2.8-1.7-1.2-1.9-3.7-.6-5.1 2 2.4 5 4 8.4 4.2-.5-2.1 1.1-4.2 3.3-4.2 1 0 1.9.4 2.6 1.1.8-.2 1.5-.5 2.2-.9-.3.8-.8 1.4-1.5 1.9.7-.1 1.3-.3 1.9-.6-.5.7-1.1 1.3-1.8 1.8Z"/></svg>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $youtube_url ) ) : ?>
						<a href="<?php echo esc_url( $youtube_url ); ?>" aria-label="<?php echo esc_attr( __( 'YouTube', 'jerseyplug' ) ); ?>" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-yellow-400 transition-all duration-300 group">
							<svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M21.6 7.2c-.2-.8-.8-1.4-1.6-1.6C18.5 5.2 12 5.2 12 5.2s-6.5 0-8 .4c-.8.2-1.4.8-1.6 1.6-.4 1.5-.4 4.8-.4 4.8s0 3.3.4 4.8c.2.8.8 1.4 1.6 1.6 1.5.4 8 .4 8 .4s6.5 0 8-.4c.8-.2 1.4-.8 1.6-1.6.4-1.5.4-4.8.4-4.8s0-3.3-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z"/></svg>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<div>
				<h4 class="font-bold text-lg mb-4 md:mb-6 text-white"><?php echo esc_html( $shop_title ); ?></h4>
				<nav class="[&_ul]:space-y-2.5 [&_ul]:text-sm [&_ul]:list-none [&_a]:text-gray-400 [&_a]:transition-colors hover:[&_a]:text-yellow-400">
					<?php
					wp_nav_menu( $shop_menu_args );
					?>
				</nav>
			</div>

			<div>
				<h4 class="font-bold text-lg mb-4 md:mb-6 text-white"><?php echo esc_html( $support_title ); ?></h4>
				<nav class="[&_ul]:space-y-2.5 [&_ul]:text-sm [&_ul]:list-none [&_a]:text-gray-400 [&_a]:transition-colors hover:[&_a]:text-yellow-400">
					<?php
					wp_nav_menu( $support_menu_args );
					?>
				</nav>
			</div>

			<div class="space-y-6">
				<h4 class="font-bold text-lg mb-4 md:mb-6 text-white"><?php echo esc_html( __( 'Contact', 'jerseyplug' ) ); ?></h4>
				<div class="space-y-4">
					<div class="flex items-start gap-3 text-gray-400 text-sm group">
						<svg class="w-4 h-4 text-gray-400 group-hover:text-yellow-400 shrink-0 mt-0.5" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-5.3-6-10a6 6 0 1 1 12 0c0 4.7-6 10-6 10Z"></path><circle cx="12" cy="11" r="2.5"></circle></svg>
						<span class="group-hover:text-yellow-400 transition-colors">
							<?php echo esc_html( $contact_address ); ?>
						</span>
					</div>
					<div class="flex items-center gap-3 text-gray-400 text-sm group">
						<svg class="w-4 h-4 text-gray-400 group-hover:text-yellow-400 shrink-0" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v2a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 3 4.2 2 2 0 0 1 5 2h2a2 2 0 0 1 2 1.7l.4 2a2 2 0 0 1-.5 1.9l-1 1a16 16 0 0 0 6 6l1-1a2 2 0 0 1 1.9-.5l2 .4A2 2 0 0 1 22 16.9Z"></path></svg>
						<span class="group-hover:text-yellow-400 transition-colors">
							<?php echo esc_html( $contact_phone ); ?>
						</span>
					</div>
					<div class="flex items-center gap-3 text-gray-400 text-sm group">
						<svg class="w-4 h-4 text-gray-400 group-hover:text-yellow-400 shrink-0" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"></rect><polyline points="3 7 12 13 21 7"></polyline></svg>
						<span class="group-hover:text-yellow-400 transition-colors">
							<?php echo esc_html( $contact_email ); ?>
						</span>
					</div>
				</div>

				<div class="pt-6 border-t border-white/10">
					<p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3"><?php echo esc_html( __( 'We Accept', 'jerseyplug' ) ); ?></p>
					<div class="flex flex-wrap gap-2">
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-[#1A1F71] text-white italic">VISA</span>
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-gradient-to-r from-[#EB001B] to-[#F79E1B] text-white">MASTERCARD</span>
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-[#E3000F] text-white">PAYFAST</span>
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-black text-[#00E5FF]">PAYJUSTNOW</span>
						<span class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm select-none transition-transform hover:scale-105 bg-[#00E573] text-black">OZOW</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="bg-black/20 border-t border-white/10">
		<div class="container mx-auto px-4 py-6">
			<div class="flex flex-col-reverse md:flex-row justify-between items-center gap-4 text-xs text-gray-400">
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d is the current year. */
							__( '© %d JerseyPlug. All rights reserved.', 'jerseyplug' ),
							$current_year
						)
					);
					?>
				</p>
				<?php
				wp_nav_menu( $legal_menu_args );
				?>
			</div>
		</div>
	</div>
</footer>

<?php do_action( 'jerseyplug_after_footer' ); ?>
