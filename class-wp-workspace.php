<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( ! class_exists('AWS_Activator')){

    class AWS_Activator{

        public static $_instance = null;

        public function __construct(){
            add_action( 'admin_init', array( 'AWS_Activator', 'aws_safe_welcome_redirect' ) );
        }

        public function aws_safe_welcome_redirect(){

			if ( ! get_transient( 'welcome_redirect_aws' ) ) {
                return;
            }
            delete_transient( 'welcome_redirect_aws' );
            if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
                return;
            }
            wp_safe_redirect( add_query_arg(
                array(
                    'page' => AWW_TEXTDOMAIN
                    ),
                admin_url( 'admin.php' )
            ) );

        }

        public static function activate_plugin() {

            set_transient( 'welcome_redirect_aws', true, 60 );
			
            $aws_textdomain = get_option( AWW_TEXTDOMAIN );
            
			if( ! $aws_textdomain ){
                update_option( AWW_TEXTDOMAIN, time() );
            }
            
        }

        public static function _get_instance(){
            if( is_null( self::$_instance ) && ! isset( self::$_instance ) && ! ( self::$_instance instanceof self ) ){
                self::$_instance = new self();            
            }
            return self::$_instance;
        }

        public function __destruct(){}

    }
}

if( ! function_exists( 'load_aws_activator' )){
    function load_aws_activator(){
        return AWS_Activator::_get_instance();
    }
}
$GLOBALS['load_aws_activator'] = load_aws_activator();