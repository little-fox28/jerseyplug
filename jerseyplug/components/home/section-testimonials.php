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

<section class="bg-white py-16 overflow-hidden">
	<div class="container mx-auto px-4">
		<div class="mb-12 text-center">
			<h2 class="mb-4 text-2xl font-bold text-[#163300] md:text-3xl">
				<?php echo esc_html( jerseyplug_pll( 'Testimonials' ) ); ?>
			</h2>
			<div class="mx-auto h-1 w-16 rounded bg-[#65cf21]"></div>
		</div>

		<!-- CSS Marquee Animation for Performance -->
		<style>
			@keyframes marquee {
				0% { transform: translateX(0); }
				100% { transform: translateX(calc(-50% - 0.5rem)); /* Offset by half gap */ }
			}
			@media (min-width: 768px) {
				@keyframes marquee {
					0% { transform: translateX(0); }
					100% { transform: translateX(calc(-50% - 0.75rem)); /* Offset by half gap for md */ }
				}
			}
			.animate-marquee.is-visible {
				animation: marquee 35s linear infinite;
			}
			/* Optimize GPU usage */
			.animate-marquee {
				will-change: transform;
			}
		</style>

		<!-- Carousel Container -->
		<div class="overflow-hidden -mx-4 sm:mx-0 relative before:absolute before:left-0 before:top-0 before:z-10 before:h-full before:w-12 before:bg-gradient-to-r before:from-white before:to-transparent after:absolute after:right-0 after:top-0 after:z-10 after:h-full after:w-12 after:bg-gradient-to-l after:from-white after:to-transparent">
			<div id="testimonials-marquee" class="flex w-max animate-marquee hover:[animation-play-state:paused] gap-4 md:gap-6 pb-8">
				<?php 
				// Duplicate the array to create a seamless infinite loop
				$loop_testimonials = array_merge($testimonials, $testimonials);
				foreach ( $loop_testimonials as $testimonial ) : 
					$name = $testimonial['name'] ?? 'Customer';
					$initial = mb_substr( $name, 0, 1 );
				?>
					<div class="w-[280px] sm:w-[350px] md:w-[380px] rounded-2xl border border-gray-100 bg-gray-50 p-5 md:p-8 shadow-sm flex flex-col justify-between transition-transform hover:-translate-y-1 hover:shadow-md cursor-default">
						<div>
							<!-- Rating Stars -->
							<div class="flex gap-1 mb-3 md:mb-4 text-[#F79E1B]">
								<?php for ( $i = 0; $i < 5; $i++ ) : ?>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 md:w-5 md:h-5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
								<?php endfor; ?>
							</div>
							<!-- Quote -->
							<p class="mb-5 md:mb-6 text-[13px] md:text-base leading-relaxed md:leading-7 text-gray-600 italic">
								"<?php echo esc_html( $testimonial['quote'] ?? '' ); ?>"
							</p>
						</div>
						
						<!-- User Info & Avatar -->
						<div class="flex items-center gap-3 md:gap-4 mt-auto pt-4 border-t border-gray-200/50">
							<div class="flex h-10 w-10 md:h-12 md:w-12 shrink-0 items-center justify-center rounded-full bg-[#163300] text-base md:text-lg font-bold text-white uppercase shadow-sm">
								<?php echo esc_html( $initial ); ?>
							</div>
							<div class="flex flex-col">
								<span class="font-bold text-sm md:text-base text-[#163300]"><?php echo esc_html( $name ); ?></span>
								<span class="text-[11px] md:text-xs text-gray-500 font-medium"><?php echo esc_html( jerseyplug_pll( 'Verified Buyer' ) ); ?></span>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const marquee = document.getElementById('testimonials-marquee');
	if (!marquee) return;

	// Use Intersection Observer to only animate when visible
	const observer = new IntersectionObserver((entries) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				marquee.classList.add('is-visible');
			} else {
				marquee.classList.remove('is-visible');
			}
		});
	}, {
		rootMargin: '100px 0px', // Start animation slightly before it scrolls into view
		threshold: 0
	});

	observer.observe(marquee);
});
</script>

<?php do_action( 'jerseyplug_after_home_testimonials', $testimonials, (int) $args['page_id'] ); ?>
