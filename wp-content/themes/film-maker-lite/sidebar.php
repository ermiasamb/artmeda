<?php
/**
 * The sidebar containing the main widget area
 *
 * @package Film Maker Lite
 * @subpackage film_maker_lite
 */
?>

<aside id="theme-sidebar" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Blog Sidebar', 'film-maker-lite' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>