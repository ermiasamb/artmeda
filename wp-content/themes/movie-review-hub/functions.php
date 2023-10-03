<?php

require get_stylesheet_directory() . '/customizer/customizer.php';

add_action( 'after_setup_theme', 'movie_review_hub_after_setup_theme' );
function movie_review_hub_after_setup_theme() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( "responsive-embeds" );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'movie-review-hub-featured-image', 2000, 1200, true );
    add_image_size( 'movie-review-hub-thumbnail-avatar', 100, 100, true );

    // Set the default content width.
    $GLOBALS['content_width'] = 525;

    // Add theme support for Custom Logo.
    add_theme_support( 'custom-logo', array(
        'width'       => 250,
        'height'      => 250,
        'flex-width'  => true,
        'flext-height' => true,
    ) );

    add_theme_support( 'custom-background', array(
        'default-color' => '1c1c1c'
    ) );

    add_theme_support( 'html5', array('comment-form','comment-list','gallery','caption',) );

    add_editor_style( array( 'assets/css/editor-style.css') );
}

/**
 * Register widget area.
 */
function movie_review_hub_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Blog Sidebar', 'movie-review-hub' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'movie-review-hub' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Page Sidebar', 'movie-review-hub' ),
        'id'            => 'sidebar-2',
        'description'   => __( 'Add widgets here to appear in your sidebar on pages.', 'movie-review-hub' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Sidebar 3', 'movie-review-hub' ),
        'id'            => 'sidebar-3',
        'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'movie-review-hub' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 1', 'movie-review-hub' ),
        'id'            => 'footer-1',
        'description'   => __( 'Add widgets here to appear in your footer.', 'movie-review-hub' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 2', 'movie-review-hub' ),
        'id'            => 'footer-2',
        'description'   => __( 'Add widgets here to appear in your footer.', 'movie-review-hub' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 3', 'movie-review-hub' ),
        'id'            => 'footer-3',
        'description'   => __( 'Add widgets here to appear in your footer.', 'movie-review-hub' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 4', 'movie-review-hub' ),
        'id'            => 'footer-4',
        'description'   => __( 'Add widgets here to appear in your footer.', 'movie-review-hub' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'movie_review_hub_widgets_init' );

// enqueue styles for child theme
function movie_review_hub_enqueue_styles() {

    // Bootstrap
    wp_enqueue_style( 'bootstrap-css', get_theme_file_uri( '/assets/css/bootstrap.css' ) );

    // Theme block stylesheet.
    wp_enqueue_style( 'movie-review-hub-block-style', get_theme_file_uri( '/assets/css/blocks.css' ), array( 'movie-review-hub-child-style' ), '1.0' );

    // enqueue parent styles
    wp_enqueue_style('film-maker-lite-style', get_template_directory_uri() .'/style.css');

    // enqueue child styles
    wp_enqueue_style('film-maker-lite-child-style', get_stylesheet_directory_uri() .'/style.css', array('film-maker-lite-style'));

    require get_theme_file_path( '/tp-body-layout.php' );
        wp_add_inline_style( 'film-maker-lite-child-style',$film_maker_lite_tp_theme_css );

    wp_enqueue_script( 'comment-reply', '/wp-includes/js/comment-reply.min.js', array(), false, true );
}
add_action('wp_enqueue_scripts', 'movie_review_hub_enqueue_styles');

function movie_review_hub_admin_scripts() {
    // Backend CSS
    wp_enqueue_style( 'movie-review-hub-backend-css', get_theme_file_uri( '/assets/css/customizer.css' ) );
}
add_action( 'admin_enqueue_scripts', 'movie_review_hub_admin_scripts' );


if ( ! defined( 'FILM_MAKER_LITE_PRO_THEME_NAME' ) ) {
    define( 'FILM_MAKER_LITE_PRO_THEME_NAME', esc_html__( 'Movie Review Hub Pro', 'movie-review-hub' ));
}
if ( ! defined( 'FILM_MAKER_LITE_PRO_THEME_URL' ) ) {
    define( 'FILM_MAKER_LITE_PRO_THEME_URL', esc_url('https://www.themespride.com/themes/movie-streaming-wordpress-theme/'));
}
if ( ! defined( 'FILM_MAKER_LITE_FREE_THEME_URL' ) ) {
	define( 'FILM_MAKER_LITE_FREE_THEME_URL', 'https://www.themespride.com/themes/free-movie-review-wordpress-theme/' );
}
if ( ! defined( 'FILM_MAKER_LITE_DEMO_THEME_URL' ) ) {
	define( 'FILM_MAKER_LITE_DEMO_THEME_URL', 'https://www.themespride.com/movie-review-hub-pro/' );
}
if ( ! defined( 'FILM_MAKER_LITE_DOCS_THEME_URL' ) ) {
    define( 'FILM_MAKER_LITE_DOCS_THEME_URL', 'https://www.themespride.com/demo/docs/movie-review-hub/' );
}
if ( ! defined( 'FILM_MAKER_LITE_DOC_URL' ) ) {
	define( 'FILM_MAKER_LITE_DOC_URL', esc_url('https://www.themespride.com/demo/docs/movie-review-hub/'));
}
if ( ! defined( 'FILM_MAKER_LITE_RATE_THEME_URL' ) ) {
    define( 'FILM_MAKER_LITE_RATE_THEME_URL', 'https://wordpress.org/support/theme/movie-review-hub/reviews/#new-post' );
}
if ( ! defined( 'FILM_MAKER_LITE_SUPPORT_THEME_URL' ) ) {
    define( 'FILM_MAKER_LITE_SUPPORT_THEME_URL', 'https://wordpress.org/support/theme/movie-review-hub' );
}
if ( ! defined( 'FILM_MAKER_LITE_CHANGELOG_THEME_URL' ) ) {
    define( 'FILM_MAKER_LITE_CHANGELOG_THEME_URL', get_stylesheet_directory() . '/readme.txt' );
}

// Time Meta
function movie_review_hub_bn_custom_meta_time() {
    add_meta_box( 'bn_meta', __( 'Movie Time Meta Feilds', 'movie-review-hub' ), 'movie_review_hub_meta_callback_movie_time', 'post', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
  add_action('admin_menu', 'movie_review_hub_bn_custom_meta_time');
}

function movie_review_hub_meta_callback_movie_time( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'movie_review_hub_movie_time_meta_nonce' );
    $movie_review_hub_bn_stored_meta = get_post_meta( $post->ID );
    $movie_review_hub_movie_time = get_post_meta( $post->ID, 'movie_review_hub_movie_time', true );
    ?>
    <div id="testimonials_custom_stuff">
        <table id="list">
            <tbody id="the-list" data-wp-lists="list:meta">
                <tr id="meta-8">
                    <td class="left">
                        <?php esc_html_e( 'Movie Time', 'movie-review-hub' )?>
                    </td>
                    <td class="left">
                        <input type="text" name="movie_review_hub_movie_time" id="movie_review_hub_movie_time" value="<?php echo esc_attr($movie_review_hub_movie_time); ?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}

/* Saves the custom meta input */
function movie_review_hub_bn_metadesig_save( $post_id ) {
    if (!isset($_POST['movie_review_hub_movie_time_meta_nonce']) || !wp_verify_nonce( strip_tags( wp_unslash( $_POST['movie_review_hub_movie_time_meta_nonce']) ), basename(__FILE__))) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Save Trip Amount
    if( isset( $_POST[ 'movie_review_hub_movie_time' ] ) ) {
        update_post_meta( $post_id, 'movie_review_hub_movie_time', strip_tags( wp_unslash( $_POST[ 'movie_review_hub_movie_time' ]) ) );
    }
}
add_action( 'save_post', 'movie_review_hub_bn_metadesig_save' );


define('MOVIE_REVIEW_HUB_CREDIT',__('https://www.themespride.com/themes/free-movie-review-wordpress-theme/','movie-review-hub') );
if ( ! function_exists( 'movie_review_hub_credit' ) ) {
    function movie_review_hub_credit(){
        echo "<a href=".esc_url(MOVIE_REVIEW_HUB_CREDIT)." target='_blank'>".esc_html__(get_theme_mod('film_maker_lite_footer_text',__('Movie Review WordPress Theme','movie-review-hub')))."</a>";
    }
}
