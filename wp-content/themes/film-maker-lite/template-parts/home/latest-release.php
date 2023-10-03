<?php
/**
 * Template part for displaying Latest Release section
 *
 * @package Film Maker Lite
 * @subpackage film_maker_lite
 */
?>

<?php if( get_theme_mod( 'film_maker_lite_latest_release_enable' ) != '') { ?>

<section id="latest-release" class="py-5">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-4">
        <?php if( get_theme_mod( 'film_maker_lite_latest_release_heading' ) != '') { ?>
          <h2><?php echo esc_html(get_theme_mod('film_maker_lite_latest_release_heading')); ?></h2>
        <?php }?>
        <?php if( get_theme_mod( 'film_maker_lite_latest_release_sub_heading' ) != '') { ?>
          <p class="mb-0"><?php echo esc_html(get_theme_mod('film_maker_lite_latest_release_sub_heading')); ?></p>
        <?php }?>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8">
        <div class="pull-up-box">
          <div class="owl-carousel">
            <?php
              $film_maker_lite_post_category = get_theme_mod('film_maker_lite_latest_release_section_category');
              if($film_maker_lite_post_category){
                $film_maker_lite_page_query = new WP_Query(array( 'category_name' => esc_html( $film_maker_lite_post_category ,'film-maker-lite')));?>
                <?php while( $film_maker_lite_page_query->have_posts() ) : $film_maker_lite_page_query->the_post(); ?>
                <div class="box">
                  <?php if(has_post_thumbnail()) { ?><?php the_post_thumbnail(); ?><?php } ?>
                  <div class="box-content">
                    <h3 class="title"><?php the_title(); ?></h3>
                    <p class="post mb-0"><?php echo wp_trim_words( get_the_content(),12 );?></p>
                    <div class="box-btn my-3">
                      <a href="<?php the_permalink(); ?>"><?php esc_html_e('Read More','film-maker-lite'); ?></a>
                    </div>
                  </div>
                </div>
                <?php endwhile;
                wp_reset_postdata();
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php }?>
