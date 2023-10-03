<?php 
/*
* Display Logo and contact details
*/
?>

<div class="top-header py-2">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-md-6 align-self-center">
        <div class="media-links">
          <?php                 
            $film_maker_lite_header_fb_new_tab = esc_attr(get_theme_mod('film_maker_lite_header_fb_new_tab','true'));
            $film_maker_lite_header_twt_new_tab = esc_attr(get_theme_mod('film_maker_lite_header_twt_new_tab','true'));
            $film_maker_lite_header_ins_new_tab = esc_attr(get_theme_mod('film_maker_lite_header_ins_new_tab','true'));
            $film_maker_lite_header_ut_new_tab = esc_attr(get_theme_mod('film_maker_lite_header_ut_new_tab','true'));
            $film_maker_lite_header_pint_new_tab = esc_attr(get_theme_mod('film_maker_lite_header_pint_new_tab','true'));
            ?>
          <?php if( get_theme_mod( 'film_maker_lite_facebook_url' ) != '' || get_theme_mod( 'film_maker_lite_twitter_url' ) != '' || get_theme_mod( 'film_maker_lite_instagram_url' ) != '' || get_theme_mod( 'film_maker_lite_youtube_url' ) != '' || get_theme_mod( 'film_maker_lite_pint_url' ) != '') { ?>
            <span class="pr-md-5 pr-2"><?php esc_html_e('CONNECT WITH US','film-maker-lite'); ?></span>
            <?php if( get_theme_mod( 'film_maker_lite_facebook_url' ) != '') { ?>
              <a <?php if($film_maker_lite_header_fb_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_facebook_url','' ) ); ?>"><i class="fab fa-facebook-f mr-2"></i></a>
            <?php } ?>
            <?php if( get_theme_mod( 'film_maker_lite_twitter_url' ) != '') { ?>
              <a <?php if($film_maker_lite_header_twt_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_twitter_url','' ) ); ?>"><i class="fab fa-twitter mr-2"></i></a>
            <?php } ?>
            <?php if( get_theme_mod( 'film_maker_lite_instagram_url' ) != '') { ?>
              <a <?php if($film_maker_lite_header_ins_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_instagram_url','' ) ); ?>"><i class="fab fa-instagram mr-2"></i></a>
            <?php } ?>
            <?php if( get_theme_mod( 'film_maker_lite_youtube_url' ) != '') { ?>
              <a <?php if($film_maker_lite_header_ut_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_youtube_url','' ) ); ?>"><i class="fab fa-youtube mr-2"></i></a>
            <?php } ?>
            <?php if( get_theme_mod( 'film_maker_lite_pint_url' ) != '') { ?>
              <a <?php if($film_maker_lite_header_pint_new_tab != false ) { ?>target="_blank" <?php } ?>href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_pint_url','' ) ); ?>"><i class="fab fa-pinterest mr-2"></i></a>
            <?php } ?>
          <?php } ?>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 text-center text-md-right align-self-center">
        <?php if( get_theme_mod( 'film_maker_lite_phone_number' ) != '') { ?>
          <span class="call-text pr-md-5 pr-2"><?php esc_html_e('CALL','film-maker-lite'); ?></span><span><i class="fas fa-phone-volume mr-2"></i><?php echo esc_html( get_theme_mod('film_maker_lite_phone_number','')); ?></span>
        <?php } ?>
      </div>
    </div>
  </div>
</div>