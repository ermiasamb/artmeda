<?php 
namespace pvum;

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('pvum\Enqueue')){

	class Enqueue {

		/**
		 * @var string
		 */
		var $suffix = '';

		function __construct(){

			$this->suffix = ( defined( 'SCRIPT_DEBUG' )) ? '' : '.min';
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );

		}


		/**
		 * Include css and js for frontend
		 */

		 
		function frontend_assets(){

			wp_enqueue_style( 'profile-views-for-um_styles', PVUM_URL.'assets/css/profile-views-for-um'.$this->suffix.'.css' );
			wp_enqueue_script( 'jquery' );

			wp_register_script( 'profile-views-for-um_scripts', PVUM_URL.'assets/js/profile-views-for-um'.$this->suffix.'.js', array( 'jquery' ),'1.0',true );

			$count_refresh_interval = apply_filters( 'pvum_count_refresh_interval',10000 );

			wp_localize_script( 'profile-views-for-um_scripts', 'pvum_ajax_data', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'count_refresh_interval' => $count_refresh_interval
			) );

			wp_enqueue_script( 'profile-views-for-um_scripts' );
		}


	}

}