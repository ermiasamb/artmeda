<?php 
namespace pvum;

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('pvum\Ajax')){

	class Ajax {


		function __construct(){

            add_action('wp_ajax_get_views_count',array( $this, 'get_view_count' ) );
            add_action('wp_ajax_nopriv_get_views_count',array( $this, 'get_view_count' ) );

            add_action( 'wp_ajax_get_views',array( $this,'get_viewer_list') );
            add_action( 'wp_ajax_nopriv_get_views',array( $this,'get_viewer_list')  );

            add_action( 'wp_ajax_get_panel_template',array( $this,'get_panel_template') );
            add_action( 'wp_ajax_nopriv_get_panel_template',array( $this,'get_panel_template')  );

            add_action( 'wp_ajax_pvum_clear_views', array( $this, 'clear_views' ) );

		}



        function clear_views(){

            $is_authorised = null;
            $profile_id = absint( $_POST['profile'] );
            if( ! is_user_logged_in() ){
                $is_authorised = false;
            }else{
                $user_id = get_current_user_id();
                if( ! $profile_id || $user_id !== $profile_id ){
                    $is_authorised = false;
                }

                if( $is_authorised === null ){
                    $is_authorised = true;
                }
            }
            ob_start();
            if( $is_authorised == true ){
                delete_user_meta( $profile_id,'_profile_views' );
                pvum()->get_template_part( 'ajax/no-viewer' );
            }else{
                pvum()->get_template_part( 'ajax/not-authorised' );
            }
            $contents = ob_get_contents();
            ob_end_clean();
            echo wp_kses( $contents, array( 'p' => array(
                'class' => array()
            ) ) );
            die;
        }



        function get_panel_template(){

            ob_start();
            pvum()->get_template_part('ajax/panel');
            $contents = ob_get_contents();
            ob_end_clean();
            echo  esc_html( $contents );
            die;
        }

        /*
        Output in
        count number of new viewers
        */
        function get_view_count(){

            ob_start();
            if(isset($_GET['profile'])){
                $profile_id = absint( $_GET['profile'] );

                if( $profile_id != get_current_user_id()){
                    echo 00;
                    die;
                }

                echo pvum()->helper()->count_new_views( $profile_id );

                $contents = ob_get_contents();
                ob_end_clean();
                echo esc_html( $contents );
                die;

            }

        }


        /*
        Output html
        list of viewers
        */

        function get_viewer_list(){

            $allowed_html = array(
                'p' => array( 'class'=> array())
            );

            ob_start();
            if(isset($_GET['profile'])){
                $profile_id = absint( $_GET['profile'] );

                if( $profile_id != get_current_user_id()){

                    _e( 'Invalid request','profile-views-um' );
                    die;

                }

                $viewers = get_user_meta( $profile_id,'_profile_views',true );

                if( $viewers && is_array($viewers) && ! empty($viewers)){
                    $allowed_html = array(
                        'ul' => array(),
                        'li' => array( 'class'=> array()),
                        'div' => array('class'=> array()),
                        'a' => array('href'=> array(),'class'=> array()),
                        'img' => array('class'=> array(),'src' => array(),'alt'   => array() ),
                        'strong' => array('class'=> array()),
                        'small' => array('class'=> array()),
                        'span' => array('class'=> array())
                    );
                    pvum()->helper()->mark_read( $profile_id, $viewers );
                    krsort( $viewers );
                    pvum()->get_template_part('ajax/viewers',[ 'viewers' => $viewers ] );

                }else{
                    $allowed_html = array( 'p' => array( 'class'=> array()) );
                    pvum()->get_template_part( 'ajax/no-viewer' );
                    
                }
            }

            $contents = ob_get_contents();
            ob_end_clean();
            echo wp_kses( $contents, $allowed_html );
            die;
        }



	}

}