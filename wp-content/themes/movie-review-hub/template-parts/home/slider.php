<?php
/**
 * Template part for displaying slider section
 *
 * @package Movie Review Hub
 * @subpackage movie_review_hub
 */

?>
<?php $static_image= get_stylesheet_directory_uri() . '/assets/images/sliderimage.png'; ?>
<?php if( get_theme_mod( 'film_maker_lite_slider_arrows') != '') { ?>

<section id="slider">
  <div class="topbar-home">
    <?php get_template_part( 'template-parts/header/top-home', 'header' ); ?>
  </div>
  <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <?php $film_maker_lite_slide_pages = array();
      for ( $film_maker_lite_count = 1; $film_maker_lite_count <= 4; $film_maker_lite_count++ ) {
        $film_maker_lite_mod = intval( get_theme_mod( 'film_maker_lite_slider_page' . $film_maker_lite_count ));
        if ( 'page-none-selected' != $film_maker_lite_mod ) {
          $film_maker_lite_slide_pages[] = $film_maker_lite_mod;
        }
      }
      if( !empty($film_maker_lite_slide_pages) ) :
        $film_maker_lite_args = array(
          'post_type' => 'page',
          'post__in' => $film_maker_lite_slide_pages,
          'orderby' => 'post__in'
        );
        $film_maker_lite_query = new WP_Query( $film_maker_lite_args );
        if ( $film_maker_lite_query->have_posts() ) :
          $i = 1;
    ?>
    <div class="carousel-inner" role="listbox">
      <?php  while ( $film_maker_lite_query->have_posts() ) : $film_maker_lite_query->the_post(); ?>
        <div <?php if($i == 1){echo 'class="carousel-item active"';} else{ echo 'class="carousel-item"';}?>>
          <?php if(has_post_thumbnail()){ ?>
           <img src="<?php the_post_thumbnail_url('full'); ?>"/>
            <?php }else {echo ('<img src="'.$static_image.'">'); } ?>
          <div class="carousel-caption">
            <div class="inner_carousel">
              <h2><?php the_title(); ?></h2>
              <p><?php echo wp_trim_words( get_the_content(),20 );?></p>
              <div class="more-btn">
                <a href="<?php the_permalink(); ?>"><i class="fas fa-play mr-2"></i><?php esc_html_e('WATCH NOW','movie-review-hub'); ?></a>
              </div>
            </div>
          </div>
        </div>
      <?php $i++; endwhile;
      wp_reset_postdata();?>
    </div>
    <?php else : ?>
        <div class="no-postfound"></div>
      <?php endif;
    endif;?>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
    </a>
  </div>
  <div class="clearfix"></div>
</section>

<?php } ?>
