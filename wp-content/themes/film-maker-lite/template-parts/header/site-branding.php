<?php
/*
* Display Logo and contact details
*/
?>
<?php
$film_maker_lite_sticky = get_theme_mod('film_maker_lite_sticky');
    $film_maker_lite_data_sticky = "false";
    if ($film_maker_lite_sticky) {
    $film_maker_lite_data_sticky = "true";
    }
    global $wp_customize;
 ?>

<div class="headerbox py-3 <?php if( is_user_logged_in() && !isset( $wp_customize ) ){ echo "login-user";} ?>" data-sticky="<?php echo esc_attr($film_maker_lite_data_sticky); ?>">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 col-md-4 align-self-center">
        <?php $film_maker_lite_logo_settings = get_theme_mod( 'film_maker_lite_logo_settings','Different Line');
        if($film_maker_lite_logo_settings == 'Different Line'){ ?>
          <div class="logo mb-3 mb-md-0">
            <?php if( has_custom_logo() ) film_maker_lite_the_custom_logo(); ?>
            <?php if(get_theme_mod('film_maker_lite_site_title_text',true) != ''){ ?>
              <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
            <?php }?>
            <?php $film_maker_lite_description = get_bloginfo( 'description', 'display' );
            if ( $film_maker_lite_description || is_customize_preview() ) : ?>
              <?php if(get_theme_mod('film_maker_lite_tagline_text',false) != ''){ ?>
                <p class="site-description mb-0"><?php echo esc_html($film_maker_lite_description); ?></p>
              <?php }?>
            <?php endif; ?>
          </div>
        <?php }else if($film_maker_lite_logo_settings == 'Same Line'){ ?>
          <div class="logo logo-same-line mb-3 mb-md-0">
            <div class="row">
              <div class="col-lg-5 col-md-5 align-self-md-center">
                <?php if( has_custom_logo() ) film_maker_lite_the_custom_logo(); ?>
              </div>
              <div class="col-lg-7 col-md-7 align-self-md-center">
                <?php if(get_theme_mod('film_maker_lite_site_title_text',true) != ''){ ?>
                  <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <?php }?>
                <?php $film_maker_lite_description = get_bloginfo( 'description', 'display' );
                if ( $film_maker_lite_description || is_customize_preview() ) : ?>
                  <?php if(get_theme_mod('film_maker_lite_tagline_text',false) != ''){ ?>
                    <p class="site-description mb-0"><?php echo esc_html($film_maker_lite_description); ?></p>
                  <?php }?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php }?>
      </div>
      <div class="col-lg-6 col-md-4 col-3 align-self-center">
        <?php get_template_part( 'template-parts/navigation/site', 'nav' ); ?>
      </div>
      <div class="col-lg-3 col-md-4 col-9 align-self-center text-right">
        <div class="book-tkt-btn">
          <?php if( get_theme_mod( 'film_maker_lite_book_ticket_link' ) != '' || get_theme_mod( 'film_maker_lite_book_ticket_button' ) != '') { ?>
            <a href="<?php echo esc_url( get_theme_mod( 'film_maker_lite_book_ticket_link','' ) ); ?>" class="register-btn"><?php echo esc_html( get_theme_mod( 'film_maker_lite_book_ticket_button','' ) ); ?></a>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
