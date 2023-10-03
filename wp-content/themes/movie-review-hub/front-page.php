<?php
/**
 * The front page template file
 *
 * @package Movie Review Hub
 * @subpackage movie_review_hub
 */

get_header(); ?>

<main id="tp_content" role="main">
	<?php get_template_part( 'template-parts/home/slider' ); ?>
	<?php get_template_part( 'template-parts/home/latest-movie' ); ?>
	<?php get_template_part( 'template-parts/home/home-content' ); ?>	
</main>

<?php get_footer();