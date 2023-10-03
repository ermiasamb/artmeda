<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Film Maker Lite
 * @subpackage film_maker_lite
 */

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function film_maker_lite_categorized_blog() {
	$film_maker_lite_category_count = get_transient( 'film_maker_lite_categories' );

	if ( false === $film_maker_lite_category_count ) {
		// Create an array of all the categories that are attached to posts.
		$film_maker_lite_categories = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$film_maker_lite_category_count = count( $film_maker_lite_categories );

		set_transient( 'film_maker_lite_categories', $film_maker_lite_category_count );
	}

	// Allow viewing case of 0 or 1 categories in post preview.
	if ( is_preview() ) {
		return true;
	}

	return $film_maker_lite_category_count > 1;
}

if ( ! function_exists( 'film_maker_lite_the_custom_logo' ) ) :
/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 *
 * @since Film Maker Lite
 */
function film_maker_lite_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}
endif;

/**
 * Flush out the transients used in film_maker_lite_categorized_blog.
 */
function film_maker_lite_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'film_maker_lite_categories' );
}
add_action( 'edit_category', 'film_maker_lite_category_transient_flusher' );
add_action( 'save_post',     'film_maker_lite_category_transient_flusher' );
