<?php
/*
 Plugin Name: Profile Views for Ultimate Member
 Plugin URI: https://wordpress.org/plugins/profile-views-for-ultimate-member
 Description: Allows users see profile viewers including their location, time and number of times they visited the profile.
 Version: 1.2.1
 Requires at least: 3.0
 Requires PHP: 7.2
 Author: Simple Plugins
 Author URI: https://aswin.com.np
 License: GPL v2 or later
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 Text Domain: profile-views-um
 Domain Path: /languages
 */

if( ! defined( 'PVUM_PLUGIN' )){
	define( 'PVUM_PLUGIN', __FILE__  );
}

if( ! defined( 'PVUM_URL' )){
	define( 'PVUM_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}


if( ! defined( 'PVUM_PATH' )){
	define( 'PVUM_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

add_action( 'plugins_loaded', 'pvum_check_dependencies', -20 );
function pvum_check_dependencies(){

	if( ! defined( 'um_path' ) ){

		add_action( 'admin_notices', function(){
			echo '<div class="error"><p>' .  __( 'The <strong>Profile Views for Ultimate member</strong> plugin requires the <a  target="_blank" href="https://wordpress.org/plugins/ultimate-member"><strong>Ultimate Member</strong></a>  plugin to be activated to work properly.', 'profile-views-um' ) . '</p></div>';
		} );

	}else{

		include PVUM_PATH.'includes/init.php';
		do_action( 'pvum_init' );

	}

}