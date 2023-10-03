<?php

function movie_review_hub_remove_customize_register() {
    global $wp_customize;
    $wp_customize->remove_section( 'film_maker_lite_social_media' );
    $wp_customize->remove_section( 'film_maker_lite_latest_release_section' );

    $wp_customize->remove_setting( 'film_maker_lite_slider_top_content' );
    $wp_customize->remove_control( 'film_maker_lite_slider_top_content' );

    $wp_customize->remove_setting( 'film_maker_lite_phone_number' );
    $wp_customize->remove_control( 'film_maker_lite_phone_number' );

    $wp_customize->remove_setting( 'film_maker_lite_book_ticket_button' );
    $wp_customize->remove_control( 'film_maker_lite_book_ticket_button' );

    $wp_customize->remove_setting( 'film_maker_lite_book_ticket_link' );
    $wp_customize->remove_control( 'film_maker_lite_book_ticket_link' );

}
add_action( 'customize_register', 'movie_review_hub_remove_customize_register', 11 );

function movie_review_hub_customize_register( $wp_customize ) {

    $wp_customize->add_setting('movie_review_hub_more_videos_button',array(
        'default'   => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('movie_review_hub_more_videos_button',array(
        'label' => esc_html__('Button Text ','movie-review-hub'),
        'section'   => 'film_maker_lite_topbar',
        'type'      => 'text'
    ));

    $wp_customize->add_setting('movie_review_hub_more_videos_link',array(
        'default'   => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control('movie_review_hub_more_videos_link',array(
        'label' => esc_html__('Button URL','movie-review-hub'),
        'section'   => 'film_maker_lite_topbar',
        'type'      => 'url'
    ));

    // Post Section
    $wp_customize->add_section( 'movie_review_hub_movie_time_section' , array(
        'title'      => __( 'Latest Movies Settings', 'movie-review-hub' ),
        'priority' => 10,
        'panel' => 'film_maker_lite_panel_id'
    ) );

    $wp_customize->add_setting('movie_review_hub_offer_section_tittle',array(
        'default'   => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('movie_review_hub_offer_section_tittle',array(
        'label' => __('Section Title','movie-review-hub'),
        'section'   => 'movie_review_hub_movie_time_section',
        'type'      => 'text'
    ));

    $wp_customize->add_setting('movie_review_hub_section_more_videos_button',array(
        'default'   => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('movie_review_hub_section_more_videos_button',array(
        'label' => esc_html__('Button Text ','movie-review-hub'),
        'section'   => 'movie_review_hub_movie_time_section',
        'type'      => 'text'
    ));

    $wp_customize->add_setting('movie_review_hub_section_more_videos_link',array(
        'default'   => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control('movie_review_hub_section_more_videos_link',array(
        'label' => esc_html__('Button URL','movie-review-hub'),
        'section'   => 'movie_review_hub_movie_time_section',
        'type'      => 'url'
    ));

    $categories = get_categories();
    $cats = array();
    $i = 0;
    $offer_cat[]= 'select';
    foreach($categories as $category){
        if($i==0){
            $default = $category->slug;
            $i++;
        }
        $offer_cat[$category->slug] = $category->name;
    }

    $wp_customize->add_setting('movie_review_hub_offer_section_category',array(
        'default'   => 'select',
        'sanitize_callback' => 'film_maker_lite_sanitize_choices',
    ));
    $wp_customize->add_control('movie_review_hub_offer_section_category',array(
        'type'    => 'select',
        'choices' => $offer_cat,
        'label' => __('Select Category','movie-review-hub'),
        'section' => 'movie_review_hub_movie_time_section',
    ));
}
add_action( 'customize_register', 'movie_review_hub_customize_register' );
