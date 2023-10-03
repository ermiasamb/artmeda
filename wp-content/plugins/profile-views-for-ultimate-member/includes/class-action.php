<?php 
namespace pvum;

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('pvum\Action')){

	class Action {


		function __construct(){

			add_action( 'plugins_loaded', array( $this, 'plugin_i18n' ) );

			add_action( 'um_access_profile', array( $this, 'track_viewers' ));

			add_action( 'um_pre_header_editprofile', array( $this,'show_views_icon' ));

			add_action( 'um_profile_footer', array( $this, 'load_panel_template') );
		}


		function load_panel_template( $args ){

			if( ! um_user( 'enable_profile_views' ) ){
				return;
			}

			pvum()->get_template_part( 'ajax/panel');
		}


		function show_views_icon( $args ){
			
			if ( ! is_user_logged_in() || UM()->fields()->editing == true  || ! pvum()->helper()->is_profile_owner( um_profile_id() ) ) {
				// not allowed for guests
				return;
			}

			if( ! um_user( 'enable_profile_views' ) ){
				return;
			}

			$user_id = get_current_user_id();
			$new_views = pvum()->helper()->count_new_views( $user_id );
			pvum()->get_template_part( 'views-icon',[ 'new_views' => $new_views ] );

		}


		function track_viewers( $profile_id ){

			um_fetch_user( $profile_id );
			
			if( ! um_user( 'enable_profile_views' ) ){
				return;
			}

			// check if profile views is enabled for the role
			$viewers = get_user_meta( $profile_id,'_profile_views',true );

			if( ! $viewers ){
				$viewers = [];
			}

			if( is_user_logged_in()){

				$viewer_id = get_current_user_id();

			}else{
				$viewer_id = null;
				$track_guests = apply_filters( 'pvum_track_anonymous_users',true );
				if( ! $track_guests ){
					return;
				}
			}

			if( $viewer_id != $profile_id ){

		
				$time = time();
				$ip = pvum()->helper()->get_ip_address();
				$viewer_index = pvum()->helper()->has_already_viewed( $ip, $profile_id, $viewer_id );
		
				if( $viewer_index !== false ){
		
					$count = $viewers[$viewer_index]['number'];
					$viewers[$viewer_index]['number'] = absint( $count ) + 1;
					$viewers[$viewer_index]['status'] = 'new';
					$viewers[$viewer_index]['viewed_on'] = $time;
		
				}else{
		
					$location = pvum()->helper()->ip_to_location( $ip );
		
					$viewers[] = [
						'viewer' => $viewer_id,
						'ip'	=> $ip,
						'viewed_on' => $time,
						'location' => $location,
						'number' => 1,
						'status' => 'new'
					];
		
				}
				
		
				update_user_meta( $profile_id, '_profile_views',$viewers );

			}

		}


		function plugin_i18n(){

			load_plugin_textdomain('profile-views-um', false, dirname( plugin_basename( PVUM_PLUGIN ) ).'/languages/');

		}




	}

}