<?php 
namespace pvum\admin;

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('pvum\admin\Admin')){

	/**
	* Class Admin
	* @package pvum\admin
	*/

	class Admin {

		/**
		 * Admin constructor.
		*/

		function __construct(){

			add_filter( 'um_admin_role_metaboxes', array( &$this, 'add_role_metabox' ), 10, 1 );

		}

		/**
		 * @param $roles_metaboxes
		 *
		 * @return array
		 */
		function add_role_metabox( $roles_metaboxes ){

			$roles_metaboxes[] = array(
				'id'       => "um-admin-form-profile-views{" . PVUM_PATH . "}",
				'title'    => __( 'Profile Views', 'profile-views-um' ),
				'callback' => array( UM()->metabox(), 'load_metabox_role' ),
				'screen'   => 'um_role_meta',
				'context'  => 'normal',
				'priority' => 'default',
			);

			return $roles_metaboxes;

		}



	}

}