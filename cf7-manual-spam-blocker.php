<?php
/*
Plugin Name:  WordPress Spam Blocker
Description:  WordPress Spam Blocker, formally Contact Form 7 Manual Spam Blocker, is an easy-to-use plugin to block all those spammers who manually fill out forms on your website. It provides with an extra tab on each Contact Form 7 (CF7) edit screen where you can set block conditions for each input field in the form.
Author URI:   http://www.omaksolutions.com 
Author:       Om Ak Solutions
Version:      2.0.4
Text Domain:  cf7-manual-spam-blocker
License: GPLv3
*/

class CF7Msb_INITIALIZE {
	
	public function __construct() {
		
		define( 'CF7Msb_VERSION', '2.0.4' );
		define( 'CF7Msb_REQUIRED_WP_VERSION', '3.5' );
		define( 'CF7Msb_PLUGIN_TITLE', 'Wordpress SPAM Blocker' );
		define( 'CF7Msb_PLUGIN', __FILE__ );
		define( 'CF7Msb_PLUGIN_BASENAME', plugin_basename( CF7Msb_PLUGIN ) );
		define( 'CF7Msb_PLUGIN_NAME', trim( dirname( CF7Msb_PLUGIN_BASENAME ), '/' ) );
		define( 'CF7Msb_PLUGIN_DIR', untrailingslashit( dirname( CF7Msb_PLUGIN ) ) );
		define( 'CF7Msb_PLUGIN_LIBS_DIR', CF7Msb_PLUGIN_DIR . '/libs' );
		define( 'CF7Msb_PLUGIN_URL', plugins_url( '' , __FILE__ ) );
		define( 'CF7Msb_PLUGIN_IMG_URL', CF7Msb_PLUGIN_URL . '/libs/js/img' );
		define( 'CF7Msb_PLUGIN_PREFIX', 'CF7Msb' );
		define( 'CF7Msb_CC_URL', 'https://codecanyon.net/item/contact-form-7-manual-spam-blocker/20605470?ref=OmAkSols');

		if ( ! defined( 'CF7Msb_DEBUG' ) ) {
			define ( 'CF7Msb_DEBUG', FALSE );
		}
		if ( ! defined( 'SITE_URL' ) ) {
			define ( 'SITE_URL', site_url() );	
		}
		if ( ! defined( 'ADMIN_URL' ) ) {
			define ( 'ADMIN_URL', trim( admin_url(), '/' ) );	
		}	
		
		$this->include_files(); 
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
		
		register_activation_hook(__FILE__, array($this, 'on_activate')); 
		register_deactivation_hook(__FILE__, array($this, 'on_deactivate'));

        add_action('plugins_loaded', array($this, 'plugin_version_check'));

        add_filter( "plugin_action_links_".plugin_basename(__FILE__), array($this, 'cf7msb_plugin_add_settings_link') );
		
	}

	public function include_files() {
		require_once( CF7Msb_PLUGIN_DIR . '/settings.php' );
		require_once( CF7Msb_PLUGIN_LIBS_DIR . '/admin/admin-functions.php' );
		require_once( CF7Msb_PLUGIN_LIBS_DIR . '/admin/admin-pages.php' );
		require_once( CF7Msb_PLUGIN_LIBS_DIR . '/admin/support-page.php' );
		require_once( CF7Msb_PLUGIN_LIBS_DIR . '/admin/cf7msb-form-panels.php' );
		require_once( CF7Msb_PLUGIN_LIBS_DIR . '/admin/cf7msb-integrate-honeypot.php' );
	}

    function plugin_version_check() {
		
		$new_ver = CF7Msb_VERSION; 
		$old_ver = get_option('cf7msb_version_lite', '0'); 
        
		if($new_ver === $old_ver) {
            return;
        }

        $cf7msb_settings = get_option('cf7msb_settings', cf7msb_settings_defaults());
        $options = array_merge(cf7msb_settings_defaults(), $cf7msb_settings);
        update_option('cf7msb_settings', $options);
		
		$compare = version_compare($new_ver, $old_ver);
		
		if($compare==1) {

			if(version_compare($old_ver, '2.0.0')===-1) {
				CF7Msb_UPDATES::update_for_2_0_0();  
			}
			
		}

        update_option('cf7msb_version_lite', CF7Msb_VERSION);
    }

    /*
     * Activation Hook
     */
	public function on_activate() {

		update_option('cf7msb_install_date', current_time('Y-m-d H:i:s'));

        $cf7msb_settings = get_option('cf7msb_settings', cf7msb_settings_defaults());
        $options = array_merge(cf7msb_settings_defaults(), $cf7msb_settings);
        update_option('cf7msb_settings', $options);
	
		set_transient( 'cf7msb_activated', 1 );
	}

	/*
	 * Deactivation Hook
	 */
	public function on_deactivate() {

	}

	/**
	 * Set Plugin URL Path (SSL/non-SSL)
	 * @param  string - $path
	 * @return string - $url 
	 * Return https or non-https URL from path
	 */
	public function plugin_url( $path = '' ) {
		$url = plugins_url( $path, CF7Msb_PLUGIN );
		if ( is_ssl() && 'http:' == substr( $url, 0, 5 ) ) {
			$url = 'https:' . substr( $url, 5 );
		}
		return $url;
	}

	/////////////////////////////////////////
	// Enqueue Admin Scripts
	/////////////////////////////////////////
	public function admin_scripts($hook_suffix) {

		if ( false !== strpos( $hook_suffix, 'cf7msb' ) || false !== strpos( $hook_suffix, 'wpcf7' ) ) {
            wp_register_style('cf7msb-css', $this->plugin_url('/libs/js/style.css'));
            wp_enqueue_style('cf7msb-css');
        }

        wp_register_script('cf7msb-custom', $this->plugin_url( '/libs/js/custom-admin.js'));
		wp_enqueue_script('cf7msb-custom');
	}

	public function cf7msb_plugin_add_settings_link( $links ) {	
		$settings_link = '<a href="admin.php?page=cf7msb-settings">' . __( 'Settings' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

}
$CF7Msb_PLUGIN = new CF7Msb_INITIALIZE(); 

/////////////////////////////////////
// Uninstall Hook
/////////////////////////////////////
register_uninstall_hook(__FILE__, 'cf7msb_on_uninstall'); 
function cf7msb_on_uninstall() {
	
	//Delete options used for plugin settings		
	delete_option('cf7msb_install_date');
	delete_option('cf7msb_review_notice');

	delete_option('cf7msb_forms');
	delete_option('cf7msb_settings');
}

function cf7msb_is_admin_page(){
    if(isset($_GET['page'])) {
        $page = sanitize_text_field($_GET['page']);
        return (isset($page) && ((strpos($page, 'cf7msb') !== false)));
    }
    return false;
}

add_filter('admin_body_class', 'cf7msb_body_class');
function cf7msb_body_class($classes){
    if (cf7msb_is_admin_page()) {
        $classes .= ' cf7msb-page';
    }
    return $classes;
}