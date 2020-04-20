<?php
/* 
 Plugin Name: Azad WP Workspace
 Description: A very simple plugin to create workshop
  Plugin URI: gittechs.com/plugin/azad-wp-workspace
      Author: Md. Abul Kalam Azad
  Author URI: gittechs.com/author
Author Email: webdevazad@gmail.com
     Version: 1.0.0
     License: GPL2 or later
 License URI: http: //www.gnu.org/licenses/gpl-2.0.html
 Text Domain: azad-wp-workspace
 Domain Path: /languages
    @package: azad-wp-workspace
*/

defined( 'ABSPATH' ) || exit;

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$plugin_data = get_plugin_data( __FILE__ );

define( 'AWW_NAME', $plugin_data['Name'] );
define( 'AWW_VERSION', $plugin_data['Version'] );
define( 'AWW_TEXTDOMAIN', $plugin_data['TextDomain'] );
define( 'AWW_PATH', plugin_dir_path( __FILE__ ) );
define( 'AWW_URL', plugin_dir_url( __FILE__ ) );
define( 'AWW_BASENAME', plugin_basename( __FILE__ ) );

if( ! class_exists( 'Azad_Workshop' ) ) {

    final class Azad_WP_Workspace{

        public static $_instance = null;
        public $slug = AWW_TEXTDOMAIN;

        public function __construct(){

            add_filter( 'plugin_action_links', array( $this, 'plugin_settings_link' ), 10, 2 );
            add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

            if ( ! get_option( 'aws_install' ) )
                $this->aws_install();
                
            add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
            add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
            add_action( 'admin_init', array( $this, 'wp_permalink' ) );
            // add_action( 'admin_init', array( $this, 'load_script_css' ) );

            // notice perposes
            // add_action( 'admin_notices', array( $this, 'aco_notice_not_checked' ) );
            // add_action( 'wp_ajax_aco_dismiss_notices', array( $this, 'aco_dismiss_notices' ) );
            
            // reset ajax action
            // add_action( 'wp_ajax_aco_reset_order', array( $this, 'aco_ajax_reset_order' ) );
            
        }        

        /* Add the plugin settings link */
        function plugin_settings_link( $actions, $file ) {

            if ( $file != AWW_BASENAME ) {
                return $actions;
            }

            $actions['aws_settings'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=' . $this->slug ) ) . '" aria-label="settings"> ' . __( 'Settings', AWW_TEXTDOMAIN ) . '</a>';

            return $actions;

        }

        public function i18n(){
            load_plugin_textdomain( $this->slug, false, basename( dirname( __FILE__ ) ) . '/languages/' );
        }        

        public function add_settings_page(){

            if( current_user_can( 'activate_plugins' ) && function_exists( 'add_options_page' ) ){
                $hook = add_options_page(
                    esc_html__( 'Azad Workshop', AWW_TEXTDOMAIN ),
                    esc_html__( 'Azad Workshop', AWW_TEXTDOMAIN ),
                    'activate_plugins',
                    $this->slug,
                    array( $this, 'admin_settings_page' )
                );
            }

        }

        public function admin_settings_page(){  
            require AWW_PATH . 'settings.php'; 
        }

        public function update_menu_order_tags() {
            do_action( 'scp_update_menu_order_tags' );
        }

        public function load_script_css() {
            if ( $this->_check_load_script_css() ) {
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui-sortable' );
                wp_enqueue_script( 'aco', AWW_URL . '/assets/aco.js', array( 'jquery' ), AWW_VERSION, true);
    
                wp_enqueue_style( 'aco', AWW_URL . '/assets/aco.css', array(), AWW_VERSION );
            }
        }
        
        public function wp_dashboard_setup(){

            $to_hide = array(
                'dashboard_right_now',
                'dashboard_activity',
                'dashboard_quick_press',
                'dashboard_primary',
                'dashboard_site_health'
              );
            update_user_meta( get_current_user_id(), 'show_welcome_panel', false );
            update_user_meta( get_current_user_id(), 'metaboxhidden_dashboard', $to_hide );

        }

        
        public function wp_permalink(){
            
            global $wp_rewrite;
            $wp_rewrite->set_permalink_structure('/%postname%/');
            $wp_rewrite->flush_rules();

        }

        public function aws_install(){

            update_option( 'aws_install', 1 );

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

if( ! function_exists( 'load_azad_wp_workspace' ) ){
    function load_azad_wp_workspace(){
        return Azad_WP_Workspace::_get_instance();
    }
}

if( is_admin() ){
    $GLOBALS['load_azad_wp_workspace'] = load_azad_wp_workspace();
}

require_once( AWW_PATH . 'class-wp-workspace.php' );
register_activation_hook( __FILE__, array( 'AWS_Activator', 'activate_plugin' ) );

function aws_doing_ajax(){

    if ( function_exists( 'wp_doing_ajax' ) ) {
        return wp_doing_ajax();
    }

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return true;
    }

    return false;

}

/**
 * AWS Uninstall hook
 */
register_uninstall_hook( __FILE__, 'aws_uninstall' );

function aws_uninstall() {
    global $wpdb;
    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
        $curr_blog = $wpdb->blogid;
        $blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
        foreach ( $blogids as $blog_id ) {
            switch_to_blog( $blog_id );
            aws_uninstall_db();
        }
        switch_to_blog( $curr_blog );
    } else {
        aws_uninstall_db();
    }
}

function aws_uninstall_db() {
    global $wpdb;
    $result = $wpdb->query( "DESCRIBE $wpdb->terms `term_order`" );
    if ( $result ) {
        $query = "ALTER TABLE $wpdb->terms DROP `term_order`";
        $result = $wpdb->query( $query );
    }
    delete_option( 'aws_install' );
}