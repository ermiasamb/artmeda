<?php


$film_maker_lite_tp_theme_css = '';


//menu font size
$film_maker_lite_menu_font_size = get_theme_mod('film_maker_lite_menu_font_size', 15);{
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

//preloader

$film_maker_lite_tp_preloader_color1_option = get_theme_mod('film_maker_lite_tp_preloader_color1_option');
$film_maker_lite_tp_preloader_color2_option = get_theme_mod('film_maker_lite_tp_preloader_color2_option');
$film_maker_lite_tp_preloader_bg_color_option = get_theme_mod('film_maker_lite_tp_preloader_bg_color_option');

if($film_maker_lite_tp_preloader_color1_option != false){
$film_maker_lite_tp_theme_css .='.center1{';
	$film_maker_lite_tp_theme_css .='border-color: '.esc_attr($film_maker_lite_tp_preloader_color1_option).' !important;';
$film_maker_lite_tp_theme_css .='}';
}
if($film_maker_lite_tp_preloader_color1_option != false){
$film_maker_lite_tp_theme_css .='.center1 .ring::before{';
	$film_maker_lite_tp_theme_css .='background: '.esc_attr($film_maker_lite_tp_preloader_color1_option).' !important;';
$film_maker_lite_tp_theme_css .='}';
}
if($film_maker_lite_tp_preloader_color2_option != false){
$film_maker_lite_tp_theme_css .='.center2{';
	$film_maker_lite_tp_theme_css .='border-color: '.esc_attr($film_maker_lite_tp_preloader_color2_option).' !important;';
$film_maker_lite_tp_theme_css .='}';
}
if($film_maker_lite_tp_preloader_color2_option != false){
$film_maker_lite_tp_theme_css .='.center2 .ring::before{';
	$film_maker_lite_tp_theme_css .='background: '.esc_attr($film_maker_lite_tp_preloader_color2_option).' !important;';
$film_maker_lite_tp_theme_css .='}';
}
if($film_maker_lite_tp_preloader_bg_color_option != false){
$film_maker_lite_tp_theme_css .='.loader{';
	$film_maker_lite_tp_theme_css .='background: '.esc_attr($film_maker_lite_tp_preloader_bg_color_option).';';
$film_maker_lite_tp_theme_css .='}';
}

//theme color
$film_maker_lite_tp_color_option = get_theme_mod('film_maker_lite_tp_color_option');

if($film_maker_lite_tp_color_option != false){
$film_maker_lite_tp_theme_css .='button[type="submit"], .top-header,.main-navigation .menu > ul > li.highlight,.readmore-btn a,.more-btn a,.box:before,.box:after,a.added_to_cart.wc-forward,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,a.added_to_cart.wc-forward,.page-numbers,.prev.page-numbers,.next.page-numbers,span.meta-nav,#theme-sidebar button[type="submit"],#footer button[type="submit"],#comments input[type="submit"],.site-info,.book-tkt-btn a.register-btn,#slider .carousel-control-prev-icon, #slider .carousel-control-next-icon,.paly-icon i {';
$film_maker_lite_tp_theme_css .='background-color: '.esc_attr($film_maker_lite_tp_color_option).';';
$film_maker_lite_tp_theme_css .='}';
}
if($film_maker_lite_tp_color_option != false){
$film_maker_lite_tp_theme_css .='a,#theme-sidebar .textwidget a,#footer .textwidget a,.comment-body a,.entry-content a,.entry-summary a,.page-template-front-page .media-links a:hover,.topbar-home i.fas.fa-phone-volume,#theme-sidebar h3,.main-navigation .current_page_item > a,#footer h3{';
$film_maker_lite_tp_theme_css .='color: '.esc_attr($film_maker_lite_tp_color_option).';';
$film_maker_lite_tp_theme_css .='}';
}

//hover color
$film_maker_lite_tp_color_option_link = get_theme_mod('film_maker_lite_tp_color_option_link');

if($film_maker_lite_tp_color_option_link != false){
$film_maker_lite_tp_theme_css .='.prev.page-numbers:focus, .prev.page-numbers:hover, .next.page-numbers:focus, .next.page-numbers:hover, .readmore-btn a:hover,#slider .carousel-control-prev-icon:hover, #slider .carousel-control-next-icon:hover,span.meta-nav:hover, #comments input[type="submit"]:hover,.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, #footer button[type="submit"]:hover,#theme-sidebar .tagcloud a:hover, #theme-sidebar button[type="submit"]:hover,.book-tkt-btn a.register-btn:hover,.more-btn a:hover{';
	$film_maker_lite_tp_theme_css .='background: '.esc_attr($film_maker_lite_tp_color_option_link).';';
$film_maker_lite_tp_theme_css .='}';
}
if($film_maker_lite_tp_color_option_link != false){
$film_maker_lite_tp_theme_css .='a:hover,#theme-sidebar a:hover,.main-navigation a:hover,.media-links i:hover,#footer li a:hover{';
	$film_maker_lite_tp_theme_css .='color: '.esc_attr($film_maker_lite_tp_color_option_link).';';
$film_maker_lite_tp_theme_css .='}';
}
if($film_maker_lite_tp_color_option_link != false){
$film_maker_lite_tp_theme_css .='#footer .tagcloud a:hover{';
	$film_maker_lite_tp_theme_css .='border-color: '.esc_attr($film_maker_lite_tp_color_option_link).';';
$film_maker_lite_tp_theme_css .='}';
}

// footer-bg-color
$film_maker_lite_tp_footer_bg_color_option = get_theme_mod('film_maker_lite_tp_footer_bg_color_option');

if($film_maker_lite_tp_footer_bg_color_option != false){
$film_maker_lite_tp_theme_css .='#footer{';
	$film_maker_lite_tp_theme_css .='background: '.esc_attr($film_maker_lite_tp_footer_bg_color_option).' !important;';
$film_maker_lite_tp_theme_css .='}';
}

//footer image
$film_maker_lite_footer_widget_image = get_theme_mod('film_maker_lite_footer_widget_image');
if($film_maker_lite_footer_widget_image != false){
$film_maker_lite_tp_theme_css .='#footer{';
	$film_maker_lite_tp_theme_css .='background: url('.esc_attr($film_maker_lite_footer_widget_image).');';
$film_maker_lite_tp_theme_css .='}';
}