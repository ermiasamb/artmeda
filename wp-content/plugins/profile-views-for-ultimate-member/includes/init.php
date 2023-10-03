<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('Profile_Views_For_Ultimate_Member')){

    class Profile_Views_For_Ultimate_Member {

        static protected $instance = null;

        public $classes = [];


        /**
		 * Autoload classes handler
		 *
		 * @since 1.0
		 *
		 * @param $class
		 */

        public static function autoload( $class_name ){

            $class_name = str_replace( 'pvum','includes', $class_name );
            $class_name = str_replace( '\\','/', $class_name );
            $array = explode( '/', strtolower( $class_name ) );
            $class_file_name = 'class-'. end( $array ).'.php';
            $array[ count( $array ) - 1 ] = strtolower($class_file_name);
            $class_name = implode('/',$array);

            if( file_exists( PVUM_PATH.$class_name ) ){
                require PVUM_PATH.$class_name;
            }

        }

        /**
		 * Main Profile_Views_For_Ultimate_Member Instance
		 *
		 * Ensures only one instance of Profile_Views_For_Ultimate_Member is loaded or can be loaded.
		 *
		 * @since 1.1
		 * @static
		 * @see pvum()
		 * @return Profile_Views_For_Ultimate_Member - Main instance
		 */

        public static function instance(){

            if( is_null( self::$instance ) ){
                self::$instance = new self();
            }

            return self::$instance;

        }


        function __construct(){
            $this->includes();
        }


        /**
		 * Include required core files.
		 * @return void
		 */
        function includes(){

            $this->enqueue();
            $this->action();
            $this->ajax();
            $this->helper();
            
	        $this->admin();

        }

        /*
		 * @return pvum\Enqueue()
		 */
        function enqueue(){
            if( empty($this->classes['enqueue'])){
                $this->classes['enqueue'] = new pvum\Enqueue();
            }

            return $this->classes['enqueue'];
        }

       

        /*
		 * @return pvum\admin\Admin()
		 */
        function admin(){
            if( empty($this->classes['admin'])){
                $this->classes['admin'] = new pvum\admin\Admin();
            }

            return $this->classes['admin'];
        }

        /*
		 * @return pvum\Ajax()
		 */
        function ajax(){
            if( empty($this->classes['ajax'])){
                $this->classes['ajax'] = new pvum\Ajax();
            }

            return $this->classes['action'];
        }



        /*
		 * @return pvum\Helper()
		 */
        function helper(){
            if( empty($this->classes['helper'])){
                $this->classes['helper'] = new pvum\Helper();
            }

            return $this->classes['helper'];
        }



        /*
		 * @return pvum\Action()
		 */
	    function action(){
            if( empty($this->classes['action'])){
                $this->classes['action'] = new pvum\Action();
            }

            return $this->classes['action'];
        }

        /*
		 * includes template file
         * @param $template (file name) eg: ajax/panel
         * @param $args (array) eg: ['id' => $id ]
		 */
        function get_template_part( $template , $args = [] ){


            if( ! empty( $args ) ){

                extract( $args );
            }

            $path = trailingslashit( get_stylesheet_directory() ).'profile-views-for-um/'.$template.'.php';

            if( ! file_exists($path)){

                $path = PVUM_PATH.'templates/'.$template.'.php';

            }

            $path = apply_filters( 'PVUM_template',$path, $template );

            include $path;

        }




    }

}

spl_autoload_register( array( 'Profile_Views_For_Ultimate_Member','autoload' ) );

/*
 *@return Profile_Views_For_Ultimate_Member instance 
 */

if( ! function_exists('pvum')){

    function pvum(){
        return Profile_Views_For_Ultimate_Member::instance();
    }

}

pvum();