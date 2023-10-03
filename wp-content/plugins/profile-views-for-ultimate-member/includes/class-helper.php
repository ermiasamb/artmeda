<?php 
namespace pvum;

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('pvum\Helper')){

	class Helper {


        function get_ip_address() {

            $ipaddress = '';
            
            if (getenv('HTTP_CLIENT_IP'))
                $ipaddress = getenv('HTTP_CLIENT_IP');
            else if(getenv('HTTP_X_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            else if(getenv('HTTP_X_FORWARDED'))
                $ipaddress = getenv('HTTP_X_FORWARDED');
            else if(getenv('HTTP_FORWARDED_FOR'))
                $ipaddress = getenv('HTTP_FORWARDED_FOR');
            else if(getenv('HTTP_FORWARDED'))
               $ipaddress = getenv('HTTP_FORWARDED');
            else if(getenv('REMOTE_ADDR'))
                $ipaddress = getenv('REMOTE_ADDR');
            else
                $ipaddress = 'UNKNOWN';

            return $ipaddress;
            
        }


        function ip_to_location( $ip_address ){
        
            $location = __( 'Unknown place','profile-views-um' );
            $data = wp_remote_get( 'http://ip-api.com/php/'.$ip_address );
            if( ! is_wp_error( $data  ) && is_array( $data ) && isset( $data['response']['code'] ) && $data['response']['code'] == 200 ){
                $raw_data = $data['body'];
                $raw_data_arr = json_decode( $raw_data );
                if( $raw_data_arr == null ){
                    $raw_data_arr = unserialize( $raw_data );
                }
        

        
                if(is_array( $raw_data_arr) && isset($raw_data_arr['status']) && $raw_data_arr['status'] == 'success' ){
        
                    $location = '';
                    if( isset( $raw_data_arr['city']) ){
                        $location .= $raw_data_arr['city'].',';
                    }
                    if( isset($raw_data_arr['countryCode'])){
                        $location .= $raw_data_arr['countryCode'];
                    }
        
                }
            } 
        
            return $location;
        }



		function count_new_views( $profile_id, $viewers = null ){

            if( ! $viewers ){
                $viewers = get_user_meta( absint($profile_id),'_profile_views',true );
            }
        
            if( $viewers && is_array($viewers) && ! empty($viewers)){
        
                $status = array_column($viewers,'status');

                $counts = array_count_values( $status );

                if( isset($counts['new'])){
                    return absint($counts['new']);
                }
        
            }
        
            return 0;

        }


        function has_already_viewed( $ip_address ,$profile_id, $viewer_id =  0 ){

            $viewers = get_user_meta( absint( $profile_id ),'_profile_views',true );
            if( ! $viewers || empty( $viewers ) ){
                return false;
            }

            if( $viewer_id == 0 ){
                    // search by ip
                    $key = array_search( $ip_address, array_column( $viewers, 'ip' ) );
                    if( $key !== false ){
                        return $key;
                    }
                }else{
                    // search with user id
                    $key = array_search( $viewer_id, array_column($viewers, 'viewer'));
                    if( $key !== false ){
                        return $key;
                    }   
                }

            return false;

        }



        function mark_read( $profile_id,$viewers = null ){

            if( ! $viewers){
                $viewers = get_user_meta( $profile_id,'_profile_views',true );
            }

            array_walk ( $viewers, function (&$key) { 
                $key["status"] = 'read'; 
            });

            update_user_meta( $profile_id, '_profile_views',$viewers );

        }



        function clear_all( $profile_id ){

            delete_user_meta( $profile_id, '_profile_views' );

        }



        function is_profile_owner( $profile_id ){

            if( is_user_logged_in() ){
                $user_id = get_current_user_id();
                
                if( $user_id == absint( $profile_id  )){
                    return true;
                }

            }

            return false;

        }



	}

}