<?php
/**
 * Theme header template.
 *
 * @package JerseyPlug
 */

$top_5_leagues = [
	[
		'nameKey' => 'Premier League',
		'logo'    => 'premier-league.png',
		'teams'   => [
			['nameKey' => 'Arsenal', 'logo' => 'arsenal.png'],
			['nameKey' => 'Chelsea', 'logo' => 'chelsea.png'],
			['nameKey' => 'Liverpool', 'logo' => 'liverpool.png'],
			['nameKey' => 'Manchester City', 'logo' => 'man_city.png'],
			['nameKey' => 'Manchester United', 'logo' => 'man_utd.png'],
		],
	],
	[
		'nameKey' => 'La Liga',
		'logo'    => 'la-liga.png',
		'teams'   => [
			['nameKey' => 'FC Barcelona', 'logo' => 'barcelona.png'],
			['nameKey' => 'Real Madrid', 'logo' => 'real_madrid.png'],
			['nameKey' => 'Atletico Madrid', 'logo' => 'atletico_madrid.png'],
		],
	],
	[
		'nameKey' => 'Serie A',
		'logo'    => 'serie-a.png',
		'teams'   => [
			['nameKey' => 'AC Milan', 'logo' => 'ac_milan.png'],
			['nameKey' => 'Inter Milan', 'logo' => 'inter_milan.png'],
			['nameKey' => 'Juventus', 'logo' => 'juventus.png'],
		],
	],
	[
		'nameKey' => 'Bundesliga',
		'logo'    => 'bundesliga.png',
		'teams'   => [
			['nameKey' => 'Bayern Munich', 'logo' => 'bayern_munich.png'],
			['nameKey' => 'Borussia Dortmund', 'logo' => 'dortmund.png'],
		],
	],
	[
		'nameKey' => 'Ligue 1',
		'logo'    => 'ligue-1.png',
		'teams'   => [
			['nameKey' => 'Paris Saint-Germain', 'logo' => 'psg.png'],
		],
	],
];

$national_teams = [
	['nameKey' => 'Argentina', 'flag' => '🇦🇷'],
	['nameKey' => 'Brazil', 'flag' => '🇧🇷'],
	['nameKey' => 'England', 'flag' => '🏴'],
	['nameKey' => 'France', 'flag' => '🇫🇷'],
	['nameKey' => 'Germany', 'flag' => '🇩🇪'],
	['nameKey' => 'Portugal', 'flag' => '🇵🇹'],
	['nameKey' => 'Spain', 'flag' => '🇪🇸'],
];

$other_leagues = [
	['nameKey' => 'MLS', 'logo' => '⚽'],
	['nameKey' => 'Brasileirão', 'logo' => '⚽'],
	['nameKey' => 'Liga Portugal', 'logo' => '⚽'],
	['nameKey' => 'Eredivisie', 'logo' => '⚽'],
];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-white text-zinc-900 antialiased' ); ?>>

<?php wp_body_open(); ?>
<?php do_action( 'tailpress_site_before' ); ?>

<div id="page" class="min-h-screen flex flex-col">
	<?php do_action( 'tailpress_header' ); ?>

	<?php
	get_template_part(
		'components/layout/header',
		null,
		[
			'top_5_leagues'  => $top_5_leagues,
			'national_teams' => $national_teams,
			'other_leagues'  => $other_leagues,
		]
	);
	?>

	<div id="content" class="site-content grow">
		<?php do_action( 'tailpress_content_start' ); ?>
		<main>
