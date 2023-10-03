<?php
/**
 * The front page template file
 *
 * @package Film Maker Lite
 * @subpackage film_maker_lite
 */

get_header(); ?>

<main id="tp_content" role="main">
	<?php do_action( 'film_maker_lite_before_slider' ); ?>

	<?php get_template_part( 'template-parts/home/slider' ); ?>
	<?php do_action( 'film_maker_lite_after_slider' ); ?>

	<?php get_template_part( 'template-parts/home/latest-release' ); ?>
	<?php do_action( 'film_maker_lite_after_latest_release' ); ?>

	<?php get_template_part( 'template-parts/home/home-content' ); ?>
	<?php do_action( 'film_maker_lite_after_home_content' ); ?>
</main>

<?php get_footer();