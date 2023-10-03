<?php
/**
 * Template part for displaying offer section
 *
 * @package Movie Review Hub
 * @subpackage movie_review_hub
 */
?>

<section id="movie-time" class="py-5">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 col-md-8 col-sm-8 align-self-center">
        <?php if( get_theme_mod('movie_review_hub_offer_section_tittle') != ''){ ?>
          <h2 class="my-3"><?php echo esc_html(get_theme_mod('movie_review_hub_offer_section_tittle','')); ?></h2>
        <?php }?>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 align-self-center text-center text-md-right">
        <?php if( get_theme_mod( 'movie_review_hub_section_more_videos_link' ) != '' || get_theme_mod( 'movie_review_hub_section_more_videos_button' ) != '') { ?>
          <div class="book-tkt-btn">
            <a href="<?php echo esc_url( get_theme_mod( 'movie_review_hub_section_more_videos_link','' ) ); ?>" class="register-btn"><?php echo esc_html( get_theme_mod( 'movie_review_hub_section_more_videos_button','' ) ); ?></a>
          </div>
        <?php } ?>
      </div>
    </div>
    <div class="row mt-4">
      <?php
        $film_maker_lite_post_category = get_theme_mod('movie_review_hub_offer_section_category');
        if($film_maker_lite_post_category){
          $film_maker_lite_page_query = new WP_Query(array( 'category_name' => esc_html( $film_maker_lite_post_category ,'movie-review-hub')));?>
          <?php while( $film_maker_lite_page_query->have_posts() ) : $film_maker_lite_page_query->the_post(); ?>
            <div class="col-lg-4 col-md-6 col-sm-6">
              <div class="cat-inner-box mb-4">
                <div class="image-icon">
                  <?php if(has_post_thumbnail()) { ?><?php the_post_thumbnail(); ?><?php } ?>
                  <div class="paly-icon">
                    <a href="<?php the_permalink(); ?>"><i class="fas fa-play"></i></a>
                  </div>
                </div>
                <div class="offer-box pt-2">
                  <h3><?php the_title(); ?></h3>
                  <?php if( get_post_meta($post->ID, 'movie_review_hub_movie_time', true) ) {?>
                    <span><i class="far fa-play-circle mr-2"></i><?php echo esc_html(get_post_meta($post->ID,'movie_review_hub_movie_time',true)); ?></span>
                  <?php }?>
                </div>
              </div>
            </div>
          <?php endwhile;
          wp_reset_postdata();
        }
      ?>
    </div>
  </div>
</section>
