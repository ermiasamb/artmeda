<?php

	$film_maker_lite_tp_theme_css = "";

//body-layout
$film_maker_lite_theme_lay = get_theme_mod( 'film_maker_lite_tp_body_layout_settings','Full');
if($film_maker_lite_theme_lay == 'Container'){
$film_maker_lite_tp_theme_css .='body{';
	$film_maker_lite_tp_theme_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;';
$film_maker_lite_tp_theme_css .='}';
$film_maker_lite_tp_theme_css .='.page-template-front-page .menubar{';
	$film_maker_lite_tp_theme_css .='position: static;';
$film_maker_lite_tp_theme_css .='}';
$film_maker_lite_tp_theme_css .='.scrolled{';
	$film_maker_lite_tp_theme_css .='width: auto; left:0; right:0;';
$film_maker_lite_tp_theme_css .='}';
}else if($film_maker_lite_theme_lay == 'Container Fluid'){
$film_maker_lite_tp_theme_css .='body{';
	$film_maker_lite_tp_theme_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
$film_maker_lite_tp_theme_css .='}';
$film_maker_lite_tp_theme_css .='.page-template-front-page .menubar{';
	$film_maker_lite_tp_theme_css .='width: 99%';
$film_maker_lite_tp_theme_css .='}';
$film_maker_lite_tp_theme_css .='.scrolled{';
	$film_maker_lite_tp_theme_css .='width: auto; left:0; right:0;';
$film_maker_lite_tp_theme_css .='}';
}else if($film_maker_lite_theme_lay == 'Full'){
$film_maker_lite_tp_theme_css .='body{';
	$film_maker_lite_tp_theme_css .='max-width: 100%;';
$film_maker_lite_tp_theme_css .='}';
}

//scrol-top
$film_maker_lite_scroll_position = get_theme_mod( 'film_maker_lite_scroll_top_position','Right');
if($film_maker_lite_scroll_position == 'Right'){
$film_maker_lite_tp_theme_css .='#return-to-top{';
    $film_maker_lite_tp_theme_css .='right: 20px;';
$film_maker_lite_tp_theme_css .='}';
}else if($film_maker_lite_scroll_position == 'Left'){
$film_maker_lite_tp_theme_css .='#return-to-top{';
    $film_maker_lite_tp_theme_css .='left: 20px;';
$film_maker_lite_tp_theme_css .='}';
}else if($film_maker_lite_scroll_position == 'Center'){
$film_maker_lite_tp_theme_css .='#return-to-top{';
    $film_maker_lite_tp_theme_css .='right: 50%;left: 50%;';
$film_maker_lite_tp_theme_css .='}';
}

//Social icon Font size
$film_maker_lite_social_icon_fontsize = get_theme_mod('film_maker_lite_social_icon_fontsize');
	$film_maker_lite_tp_theme_css .='.media-links a i{';
$film_maker_lite_tp_theme_css .='font-size: '.esc_attr($film_maker_lite_social_icon_fontsize).'px;';
$film_maker_lite_tp_theme_css .='}';

// site title font size option
$film_maker_lite_site_title_font_size = get_theme_mod('film_maker_lite_site_title_font_size', 25);{
$film_maker_lite_tp_theme_css .='.logo h1 , .logo p a{';
	$film_maker_lite_tp_theme_css .='font-size: '.esc_attr($film_maker_lite_site_title_font_size).'px;';
$film_maker_lite_tp_theme_css .='}';
}

//site tagline font size option
$film_maker_lite_site_tagline_font_size = get_theme_mod('film_maker_lite_site_tagline_font_size', 15);{
$film_maker_lite_tp_theme_css .='.logo p{';
	$film_maker_lite_tp_theme_css .='font-size: '.esc_attr($film_maker_lite_site_tagline_font_size).'px;';
$film_maker_lite_tp_theme_css .='}';
}

//return to header mobile				
$film_maker_lite_return_to_header_mob = get_theme_mod( 'film_maker_lite_return_to_header_mob',false);
if($film_maker_lite_return_to_header_mob == true && get_theme_mod( 'film_maker_lite_return_to_header',true) != true){
$film_maker_lite_tp_theme_css .='.return-to-header{';
	$film_maker_lite_tp_theme_css .='display:none;';
$film_maker_lite_tp_theme_css .='} ';
}
if($film_maker_lite_return_to_header_mob == true){
$film_maker_lite_tp_theme_css .='@media screen and (max-width:575px) {';
$film_maker_lite_tp_theme_css .='.return-to-header{';
	$film_maker_lite_tp_theme_css .='display:block;';
$film_maker_lite_tp_theme_css .='} }';
}else if($film_maker_lite_return_to_header_mob == false){
$film_maker_lite_tp_theme_css .='@media screen and (max-width:575px){';
$film_maker_lite_tp_theme_css .='.return-to-header{';
	$film_maker_lite_tp_theme_css .='display:none;';
$film_maker_lite_tp_theme_css .='} }';
}

//slider mobile	
$film_maker_lite_slider_buttom_mob = get_theme_mod( 'film_maker_lite_slider_buttom_mob',true);
if($film_maker_lite_slider_buttom_mob == true && get_theme_mod( 'film_maker_lite_slider_button',true) != true){
$film_maker_lite_tp_theme_css .='#slider .more-btn{';
	$film_maker_lite_tp_theme_css .='display:none;';
$film_maker_lite_tp_theme_css .='} ';
}
if($film_maker_lite_slider_buttom_mob == true){
$film_maker_lite_tp_theme_css .='@media screen and (max-width:575px) {';
$film_maker_lite_tp_theme_css .='#slider .more-btn{';
	$film_maker_lite_tp_theme_css .='display:block;';
$film_maker_lite_tp_theme_css .='} }';
}else if($film_maker_lite_slider_buttom_mob == false){
$film_maker_lite_tp_theme_css .='@media screen and (max-width:575px){';
$film_maker_lite_tp_theme_css .='#slider .more-btn{';
	$film_maker_lite_tp_theme_css .='display:none;';
$film_maker_lite_tp_theme_css .='} }';
}

//footer image
$film_maker_lite_footer_widget_image = get_theme_mod('film_maker_lite_footer_widget_image');
if($film_maker_lite_footer_widget_image != false){
$film_maker_lite_tp_theme_css .='#footer{';
	$film_maker_lite_tp_theme_css .='background: url('.esc_attr($film_maker_lite_footer_widget_image).');';
$film_maker_lite_tp_theme_css .='}';
}

// related product
$film_maker_lite_related_product = get_theme_mod('film_maker_lite_related_product',true);
if($film_maker_lite_related_product == false){
$film_maker_lite_tp_theme_css .='.related.products{';
	$film_maker_lite_tp_theme_css .='display: none;';
$film_maker_lite_tp_theme_css .='}';
}

//menu font size
$film_maker_lite_menu_font_size = get_theme_mod('film_maker_lite_menu_font_size', 12);{
$film_maker_lite_tp_theme_css .='.main-navigation a{';
	$film_maker_lite_tp_theme_css .='font-size: '.esc_attr($film_maker_lite_menu_font_size).'px;';
$film_maker_lite_tp_theme_css .='}';
}

// menu text tranform
$film_maker_lite_menu_text_tranform = get_theme_mod( 'film_maker_lite_menu_text_tranform','Uppercase');
if($film_maker_lite_menu_text_tranform == 'Uppercase'){
$film_maker_lite_tp_theme_css .='.main-navigation a {';
	$film_maker_lite_tp_theme_css .='text-transform: uppercase;';
$film_maker_lite_tp_theme_css .='}';
}else if($film_maker_lite_menu_text_tranform == 'Lowercase'){
$film_maker_lite_tp_theme_css .='.main-navigation a {';
	$film_maker_lite_tp_theme_css .='text-transform: lowercase;';
$film_maker_lite_tp_theme_css .='}';
}
else if($film_maker_lite_menu_text_tranform == 'Capitalize'){
$film_maker_lite_tp_theme_css .='.main-navigation a {';
	$film_maker_lite_tp_theme_css .='text-transform: capitalize;';
$film_maker_lite_tp_theme_css .='}';
}