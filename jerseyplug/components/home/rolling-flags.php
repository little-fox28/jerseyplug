<?php

/**
 * Rolling flags component.
 *
 * @package JerseyPlug
 */

$context = [
	'component' => 'rolling-flags',
	'location'   => 'home',
];

do_action('jerseyplug_before_rolling_flags', $context);

$translate = static function (string $text): string {
	return function_exists('jerseyplug_pll') ? jerseyplug_pll($text) : __($text, 'jerseyplug');
};

$national_teams = apply_filters(
	'jerseyplug_home_rolling_flags_national_teams',
	[
		['name' => $translate('Vietnam'), 'flag' => 'VN'],
		['name' => $translate('Argentina'), 'flag' => 'AR'],
		['name' => $translate('France'), 'flag' => 'FR'],
		['name' => $translate('Brazil'), 'flag' => 'BR'],
		['name' => $translate('Germany'), 'flag' => 'DE'],
		['name' => $translate('Spain'), 'flag' => 'ES'],
		['name' => $translate('South Africa'), 'flag' => 'ZAR'],
		['name' => $translate('Portugal'), 'flag' => 'PT'],
		['name' => $translate('Japan'), 'flag' => 'JP'],
		['name' => $translate('India'), 'flag' => 'INR'],
		['name' => $translate('Indonesia'), 'flag' => 'IDR'],
		['name' => $translate('Mexico'), 'flag' => 'MXN'],
	],
	$context
);

$wrapper_classes = apply_filters('jerseyplug_home_rolling_flags_wrapper_classes', 'w-full h-20 md:h-32 relative overflow-hidden bg-white mb-0', $context);
$fill_classes    = apply_filters('jerseyplug_home_rolling_flags_fill_classes', 'h-full absolute top-0 left-0 bg-primary w-full origin-left', $context);
$arrow_classes    = apply_filters('jerseyplug_home_rolling_flags_arrow_classes', 'absolute top-0 h-20 w-20 md:h-32 md:w-32 flex items-center justify-center z-20 bg-accent rounded-full border-4 border-primary', $context);
$flags_classes    = apply_filters('jerseyplug_home_rolling_flags_flags_classes', 'absolute top-0 h-full flex items-center z-10 gap-6', $context);

$flags_dir = get_theme_file_uri('/assets/images/flags/');
?>

<?php do_action('jerseyplug_before_rolling_flags_markup', $national_teams, $context); ?>
<div
	x-data="rollingFlags()"
	x-init="init()"
	@scroll.window.passive="handleScroll()"
	x-ref="container"
	class="<?php echo esc_attr($wrapper_classes); ?>">
	<?php do_action('jerseyplug_before_rolling_flags_fill', $context); ?>
	<div
		class="<?php echo esc_attr($fill_classes); ?>"
		:style="shouldAnimate ? `transform: scaleX(${percentage / 100})` : 'transform: scaleX(1)'"
		style="width: 100%; transform-origin: left; will-change: transform;">
	</div>

	<?php do_action('jerseyplug_before_rolling_flags_arrow', $context); ?>
	<div
		class="<?php echo esc_attr($arrow_classes); ?>"
		:style="shouldAnimate ? `left: ${percentage}%; transform: translateX(-50%)` : 'left: 100%; transform: translateX(-50%)'">
		<svg aria-hidden="true" viewBox="0 0 24 24" class="h-10 w-10 md:h-20 md:w-20 text-primary" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
			<path d="m9 18 6-6-6-6"></path>
		</svg>
	</div>
	<?php do_action('jerseyplug_after_rolling_flags_arrow', $context); ?>

	<?php do_action('jerseyplug_before_rolling_flags_flags', $national_teams, $context); ?>
	<div
		class="<?php echo esc_attr($flags_classes); ?>"
		:style="shouldAnimate ? `left: calc(${percentage}% + 5rem); --flag-rotation: ${percentage * 8}deg` : 'left: calc(100% + 5rem); --flag-rotation: 0deg'"
		style="will-change: transform;">
		<?php foreach ($national_teams as $nation) : ?>
			<img
				src="<?php echo esc_url($flags_dir . strtolower($nation['flag']) . '.svg'); ?>"
				alt="<?php echo esc_attr($nation['name']); ?>"
				class="h-20 w-20 md:h-32 md:w-32"
				style="transform: rotate(var(--flag-rotation))"
				loading="lazy"
				decoding="async" />
		<?php endforeach; ?>
	</div>
</div>
<?php do_action('jerseyplug_after_rolling_flags_markup', $national_teams, $context); ?>

<script>
	if (typeof window.rollingFlags !== 'function') {
		window.rollingFlags = function() {
			return {
				percentage: 5,
				shouldAnimate: true,
				handleScroll() {
					if (!this.shouldAnimate) {
						return
					}

					const container = this.$refs.container
					if (!container) {
						return
					}

					const rect = container.getBoundingClientRect()
					const windowHeight = window.innerHeight
					const totalDist = windowHeight + rect.height
					const distTravelled = windowHeight - rect.top

					let percentage = (distTravelled / totalDist) * 100
					percentage = Math.min(Math.max(percentage, 5), 100)

					this.percentage = percentage
				},
				init() {
					const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches
					const deviceMemory = navigator.deviceMemory || 4
					const hardwareConcurrency = navigator.hardwareConcurrency || 4

					if (prefersReduced || deviceMemory <= 1 || hardwareConcurrency <= 2) {
						this.shouldAnimate = false
						this.percentage = 100
						return
					}

					this.handleScroll()
				},
			}
		}
	}
</script>
<?php do_action('jerseyplug_after_rolling_flags', $context); ?>