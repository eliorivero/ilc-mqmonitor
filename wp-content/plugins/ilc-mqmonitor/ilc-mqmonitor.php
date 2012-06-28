<?php
/**
 * Plugin Name: ILC Media Queries Monitor
 * Plugin URI: http://ilovecolors.com.ar/
 * Description: Displays which media query is currently in use in Admin Bar.
 * Author: Elio Rivero
 * Author URI: http://ilovecolors.com.ar
 * Version: 1.0.0
 */


/**
 * Load localization file
 */
function ilc_localization() {
	load_plugin_textdomain( 'ilc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
add_action('plugins_loaded', 'ilc_localization');

/**
 * Create Settings Page
 * @since 1.0.0
 */
require_once ('ilc-admin.php');
$ilc_mqm = new ILCMQMAdmin();

/**
 * Create template tag and setup content filter
 * @since 1.0.0
 */
class ILC_MQM {
	
	static $settings;
	static $version;
	
	function __construct() {
		self::$version = '1.0.0';
		add_action( 'wp_head', array(&$this, 'enqueue') );
	}
	
	/**
	 * Register and/or enqueue scripts and stylesheets to use later
	 * @since 1.0.0
	 */
	function enqueue(){
		if( !is_user_logged_in() ) return;
		global $ilc_mqm;
		$media_queries = $ilc_mqm->get('queries_int');
		$mq = split(',', $media_queries);
		
		// Register styles
		wp_enqueue_script('ilc-mqm-js', plugin_dir_url(__FILE__) . "includes/jquery.ilc-mqmonitor.js", array('jquery'), self::$version, true);
		wp_localize_script('ilc-mqm-js', 'ilcmqm', array(
			'mq' => $media_queries,
			'fs' => $mq[0]
		));
	}

}
// Initialize class
new ILC_MQM();


?>