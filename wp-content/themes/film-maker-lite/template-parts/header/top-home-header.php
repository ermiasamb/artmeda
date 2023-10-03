<?php 
/*
* Display Logo and contact details
*/
?>

<span class="media-links mb-3 mb-md-0">
  <?php if( get_theme_mod( 'film_maker_lite_facebook_url' ) != '' || get_theme_mod( 'film_maker_lite_twitter_url' ) != '' || get_theme_mod( 'film_maker_lite_instagram_url' ) != '' || get_theme_mod( 'film_maker_lite_youtube_url' ) != '' || get_theme_mod( 'film_maker_lite_pint_url' ) != '') { ?>
    <span class="pr-5"><?php esc_html_e('CONNECT WITH US','film-maker-lite'); ?></span>
    <?php if( get_theme_mod( 'film_maker_lite_facebook_url' ) != '') { ?>
      <a href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_facebook_url','' ) ); ?>"><i class="fab fa-facebook-f mr-2"></i></a>
    <?php } ?>
    <?php if( get_theme_mod( 'film_maker_lite_twitter_url' ) != '') { ?>
      <a href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_twitter_url','' ) ); ?>"><i class="fab fa-twitter mr-2"></i></a>
    <?php } ?>
    <?php if( get_theme_mod( 'film_maker_lite_instagram_url' ) != '') { ?>
      <a href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_instagram_url','' ) ); ?>"><i class="fab fa-instagram mr-2"></i></a>
    <?php } ?>
    <?php if( get_theme_mod( 'film_maker_lite_youtube_url' ) != '') { ?>
      <a href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_youtube_url','' ) ); ?>"><i class="fab fa-youtube mr-2"></i></a>
    <?php } ?>
    <?php if( get_theme_mod( 'film_maker_lite_pint_url' ) != '') { ?>
      <a href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_pint_url','' ) ); ?>"><i class="fab fa-pinterest mr-2"></i></a>
    <?php } ?>
  <?php } ?>
</span>
<?php if( get_theme_mod( 'film_maker_lite_phone_number' ) != '') { ?>
  <span class="call-text pr-5"><?php esc_html_e('CALL','film-maker-lite'); ?></span><span><i class="fas fa-phone-volume mr-2"></i><?php echo esc_html( get_theme_mod('film_maker_lite_phone_number','')); ?></span>
<?php } ?>