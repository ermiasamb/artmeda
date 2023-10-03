<?php
/*
 * Plugin Name: Ultimate Member Widgets for Elementor - WordPress User Directory
 * Description: Awesome Ultimate Member widgets for the Elementor page builder.
 * Plugin URI: http://userelements.com/ultimate-member-elementor
 * Version: 1.6
 * Author: userelements
 * Author URI: http://userelements.com/
 * Requires at least: 6.0
 * Requires PHP: 7.3 
 * Text Domain: um-elementor
 * Domain Path: /languages

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined( 'UM_USER_ELEMENTOR_PATH' ) ) {
    define( 'UM_USER_ELEMENTOR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'UM_USER_ELEMENTOR_URL' ) ) {
    define( 'UM_USER_ELEMENTOR_URL', plugins_url( '/', __FILE__ ) );
}

require_once UM_USER_ELEMENTOR_PATH . 'inc/elementor-um-essential.php';

add_action( 'plugins_loaded', 'user_elementor_um_init' );

/**
 * Initialize
 */
if ( ! function_exists( 'user_elementor_um_init' ) ) {
	function user_elementor_um_init() {

	    // Check if Elementor installed and activated
	    if ( ! did_action( 'elementor/loaded' ) ) {
	        add_action( 'admin_notices', 'user_elementor_um_dependency' );
	        return;
	    }

		if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
			add_action( 'admin_notices', 'user_elementor_um_fail_php' );
			return;
		}

		add_action( 'init', 'user_elementor_um_textdomain' );
		add_action( 'elementor/init', 'user_elementor_um_category' );
		add_action( 'elementor/init', 'user_elementor_um_modules' );
		add_action( 'wp_enqueue_scripts', 'user_elementor_um_scripts' );
	}
}

