<?php
/**
 * Film Maker Lite Theme page
 *
 * @package Film Maker Lite
 */

function film_maker_lite_admin_scriptss() {
	wp_dequeue_script('film-maker-lite-custom-scripts');
}
add_action( 'admin_enqueue_scripts', 'film_maker_lite_admin_scriptss' );

if ( ! defined( 'FILM_MAKER_LITE_FREE_THEME_URL' ) ) {
	define( 'FILM_MAKER_LITE_FREE_THEME_URL', 'https://www.themespride.com/themes/free-film-maker-wordpress-theme/' );
}
if ( ! defined( 'FILM_MAKER_LITE_PRO_THEME_URL' ) ) {
	define( 'FILM_MAKER_LITE_PRO_THEME_URL', 'https://www.themespride.com/themes/film-maker-wordpress-theme/' );
}
if ( ! defined( 'FILM_MAKER_LITE_DEMO_THEME_URL' ) ) {
	define( 'FILM_MAKER_LITE_DEMO_THEME_URL', 'https://www.themespride.com/film-maker-pro/' );
}
if ( ! defined( 'FILM_MAKER_LITE_DOCS_THEME_URL' ) ) {
    define( 'FILM_MAKER_LITE_DOCS_THEME_URL', 'https://www.themespride.com/demo/docs/film-maker-lite/' );
}
if ( ! defined( 'FILM_MAKER_LITE_RATE_THEME_URL' ) ) {
    define( 'FILM_MAKER_LITE_RATE_THEME_URL', 'https://wordpress.org/support/theme/film-maker-lite/reviews/#new-post' );
}
if ( ! defined( 'FILM_MAKER_LITE_CHANGELOG_THEME_URL' ) ) {
    define( 'FILM_MAKER_LITE_CHANGELOG_THEME_URL', get_template_directory() . '/readme.txt' );
}
if ( ! defined( 'FILM_MAKER_LITE_SUPPORT_THEME_URL' ) ) {
    define( 'FILM_MAKER_LITE_SUPPORT_THEME_URL', 'https://wordpress.org/support/theme/film-maker-lite/' );
}

/**
 * Add theme page
 */
function film_maker_lite_menu() {
	add_theme_page( esc_html__( 'About Theme', 'film-maker-lite' ), esc_html__( 'About Theme', 'film-maker-lite' ), 'edit_theme_options', 'film-maker-lite-about', 'film_maker_lite_about_display' );
}
add_action( 'admin_menu', 'film_maker_lite_menu' );

/**
 * Display About page
 */
function film_maker_lite_about_display() {
	$film_maker_lite_theme = wp_get_theme();
	?>
	<div class="wrap about-wrap full-width-layout">
		<h1><?php echo esc_html( $film_maker_lite_theme ); ?></h1>
		<div class="about-theme">
			<div class="theme-description">
				<p class="about-text">
					<?php
					// Remove last sentence of description.
					$film_maker_lite_description = explode( '. ', $film_maker_lite_theme->get( 'Description' ) );

					array_pop( $film_maker_lite_description );

					$film_maker_lite_description = implode( '. ', $film_maker_lite_description );

					echo esc_html( $film_maker_lite_description . '.' );
				?></p>
				<p class="actions">
					<a href="<?php echo esc_url( FILM_MAKER_LITE_FREE_THEME_URL ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Info', 'film-maker-lite' ); ?></a>

					<a href="<?php echo esc_url( FILM_MAKER_LITE_DEMO_THEME_URL ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'View Demo', 'film-maker-lite' ); ?></a>

					<a href="<?php echo esc_url( FILM_MAKER_LITE_DOCS_THEME_URL ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Instructions', 'film-maker-lite' ); ?></a>

					<a href="<?php echo esc_url( FILM_MAKER_LITE_RATE_THEME_URL ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Rate this theme', 'film-maker-lite' ); ?></a>

					<a href="<?php echo esc_url( FILM_MAKER_LITE_PRO_THEME_URL ); ?>" class="green button button-secondary" target="_blank"><?php esc_html_e( 'Upgrade to pro', 'film-maker-lite' ); ?></a>
				</p>
			</div>

			<div class="theme-screenshot">
				<img src="<?php echo esc_url( $film_maker_lite_theme->get_screenshot() ); ?>" />
			</div>

		</div>

		<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu', 'film-maker-lite' ); ?>">
			<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'film-maker-lite-about' ), 'themes.php' ) ) ); ?>" class="nav-tab<?php echo ( isset( $_GET['page'] ) && 'film-maker-lite-about' === $_GET['page'] && ! isset( $_GET['tab'] ) ) ?' nav-tab-active' : ''; ?>"><?php esc_html_e( 'About', 'film-maker-lite' ); ?></a>

			<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'film-maker-lite-about', 'tab' => 'free_vs_pro' ), 'themes.php' ) ) ); ?>" class="nav-tab<?php echo ( isset( $_GET['tab'] ) && 'free_vs_pro' === $_GET['tab'] ) ?' nav-tab-active' : ''; ?>"><?php esc_html_e( 'Compare free Vs Pro', 'film-maker-lite' ); ?></a>

			<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'film-maker-lite-about', 'tab' => 'changelog' ), 'themes.php' ) ) ); ?>" class="nav-tab<?php echo ( isset( $_GET['tab'] ) && 'changelog' === $_GET['tab'] ) ?' nav-tab-active' : ''; ?>"><?php esc_html_e( 'Changelog', 'film-maker-lite' ); ?></a>
		</nav>

		<?php
			film_maker_lite_main_screen();

			film_maker_lite_changelog_screen();

			film_maker_lite_free_vs_pro();
		?>

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? esc_html_e( 'Return to Updates', 'film-maker-lite' ) : esc_html_e( 'Return to Dashboard &rarr; Updates', 'film-maker-lite' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home', 'film-maker-lite' ) : esc_html_e( 'Go to Dashboard', 'film-maker-lite' ); ?></a>
		</div>
	</div>
	<?php
}

/**
 * Output the main about screen.
 */
function film_maker_lite_main_screen() {
	if ( isset( $_GET['page'] ) && 'film-maker-lite-about' === $_GET['page'] && ! isset( $_GET['tab'] ) ) {
	?>
		<div class="feature-section two-col">
			<div class="col card">
				<h2 class="title"><?php esc_html_e( 'Theme Customizer', 'film-maker-lite' ); ?></h2>
				<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'film-maker-lite' ) ?></p>
				<p><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Customize', 'film-maker-lite' ); ?></a></p>
			</div>

			<div class="col card">
				<h2 class="title"><?php esc_html_e( 'Got theme support question?', 'film-maker-lite' ); ?></h2>
				<p><?php esc_html_e( 'Get genuine support from genuine people. Whether it\'s customization or compatibility, our seasoned developers deliver tailored solutions to your queries.', 'film-maker-lite' ) ?></p>
				<p><a href="<?php echo esc_url( FILM_MAKER_LITE_SUPPORT_THEME_URL ); ?>" class="button button-primary"><?php esc_html_e( 'Support Forum', 'film-maker-lite' ); ?></a></p>
			</div>

			<div class="col card">
				<h2 class="title"><?php esc_html_e( 'Upgrade To Premium With Straight 20% OFF.', 'film-maker-lite' ); ?></h2>
				<p><?php esc_html_e( 'Get our amazing WordPress theme with exclusive 20% off use the coupon', 'film-maker-lite' ) ?>"<input type="text" value="GETPro20" id="myInput">".</p>
				<button class="button button-primary" onclick="film_maker_lite_text_copyied()"><?php esc_html_e( 'GETPro20', 'film-maker-lite' ); ?></button>
			</div>
		</div>
	<?php
	}
}

/**
 * Output the changelog screen.
 */
function film_maker_lite_changelog_screen() {
	if ( isset( $_GET['tab'] ) && 'changelog' === $_GET['tab'] ) {
		global $wp_filesystem;
	?>
		<div class="wrap about-wrap">

			<p class="about-description"><?php esc_html_e( 'View changelog below:', 'film-maker-lite' ); ?></p>

			<?php
				$changelog_file = apply_filters( 'film_maker_lite_changelog_file', FILM_MAKER_LITE_CHANGELOG_THEME_URL );
				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = film_maker_lite_parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
			?>
		</div>
	<?php
	}
}

/**
 * Parse changelog from readme file.
 * @param  string $content
 * @return string
 */
function film_maker_lite_parse_changelog( $content ) {
	// Explode content with ==  to juse separate main content to array of headings.
	$content = explode ( '== ', $content );

	$changelog_isolated = '';

	// Get element with 'Changelog ==' as starting string, i.e isolate changelog.
	foreach ( $content as $key => $value ) {
		if (strpos( $value, 'Changelog ==') === 0) {
	    	$changelog_isolated = str_replace( 'Changelog ==', '', $value );
	    }
	}

	// Now Explode $changelog_isolated to manupulate it to add html elements.
	$changelog_array = explode( '= ', $changelog_isolated );

	// Unset first element as it is empty.
	unset( $changelog_array[0] );

	$changelog = '<pre class="changelog">';

	foreach ( $changelog_array as $value) {
		// Replace all enter (\n) elements with </span><span> , opening and closing span will be added in next process.
		$value = preg_replace( '/\n+/', '</span><span>', $value );

		// Add openinf and closing div and span, only first span element will have heading class.
		$value = '<div class="block"><span class="heading">= ' . $value . '</span></div>';

		// Remove empty <span></span> element which newr formed at the end.
		$changelog .= str_replace( '<span></span>', '', $value );
	}

	$changelog .= '</pre>';

	return wp_kses_post( $changelog );
}

/**
 * Import Demo data for theme using catch themes demo import plugin
 */
function film_maker_lite_free_vs_pro() {
	if ( isset( $_GET['tab'] ) && 'free_vs_pro' === $_GET['tab'] ) {
	?>
		<div class="wrap about-wrap">

			<p class="about-description"><?php esc_html_e( 'View Free vs Pro Table below:', 'film-maker-lite' ); ?></p>
			<div class="vs-theme-table">
				<table>
					<thead>
						<tr><th scope="col"></th>
							<th class="head" scope="col"><?php esc_html_e( 'Free Theme', 'film-maker-lite' ); ?></th>
							<th class="head" scope="col"><?php esc_html_e( 'Pro Theme', 'film-maker-lite' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><span><?php esc_html_e( 'Theme Demo Set Up', 'film-maker-lite' ); ?></span></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Additional Templates, Color options and Fonts', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Included Demo Content', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Section Ordering', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Multiple Sections', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Additional Plugins', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Premium Technical Support', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Access to Support Forums', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Free updates', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Unlimited Domains', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Responsive Design', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Live Customizer', 'film-maker-lite' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td class="feature feature--empty"></td>
							<td class="feature feature--empty"></td>
							<td headers="comp-2" class="td-btn-2"><a class="sidebar-button single-btn" href="<?php echo esc_url( FILM_MAKER_LITE_PRO_THEME_URL );?>"><?php esc_html_e( 'Go for Premium', 'film-maker-lite' ); ?></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	<?php
	}
}
