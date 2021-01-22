<?php
/**
* Plugin Name: ZM Image COntrol 0.0.1
* Plugin Script: zm-iimagecontrol.php
* Description: does a batch process on all images in the wp media library tto see if they are possibly violating licensing of copyrighted images
* Version: 0.0.1
* License: GPL
* Author: Arthur Cormack
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// a plugin that does a batch process on all images in the wp media library
// so ... a query with range and offset of wp_posts where post_type == 'attachment' and post_mime_type == image/jpeg || image/gif || image/png
// and also where the post_date is before 2018.
// then we load the image, load the metadata for it, and check the caption and the credit values.
// 

// but first, how are we going to invoke this?

if( ! class_exists( 'ImageControl' ) ):

/**
* Main WP Bulk Delete class
*/
class ImageControl {
  	/** Singleton *************************************************************/
	/**
	 * ImageControl The one true ImageControl.
	 */
  private static $instance;

  public static function instance() {
		if( ! isset( self::$instance ) && ! (self::$instance instanceof ImageControl ) ) {
			self::$instance = new ImageControl();
			self::$instance->setup_constants();

			// add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			self::$instance->includes();
			// self::$instance->api = new ImageControl_API();
		}
		return self::$instance;	
	}

  	/** Magic Methods *********************************************************/

	/**
	 * A dummy constructor to prevent ImageControl from being loaded more than once.
	 *
	 * @since 0.0.1
	 * @see ImageControl::instance()
	 * @see wpbulkdelete()
	 */
	private function __construct() { /* Do nothing here */ }

	/**
	 * A dummy magic method to prevent ImageControl from being cloned.
	 *
	 * @since 0.0.1
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'zm-imagecontrol' ), '0.0.1' ); }

	/**
	 * A dummy magic method to prevent WP_Bulk_Delete from being unserialized.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'zm-imagecontrol' ), '0.0.1' ); }



  /**
	 * Setup plugins constants.
	 *
	 * @access private
	 * @since 0.0.1
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if( ! defined( 'ZMIMAGECONTRL_VERSION' ) ){
			define( 'ZMIMAGECONTRL_VERSION', '0.0.1' );
		}

		// Plugin folder Path.
		if( ! defined( 'ZMIMAGECONTRL_PLUGIN_DIR' ) ){
			define( 'ZMIMAGECONTRL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin folder URL.
		if( ! defined( 'ZMIMAGECONTRL_PLUGIN_URL' ) ){
			define( 'ZMIMAGECONTRL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin root file.
		if( ! defined( 'ZMIMAGECONTRL_PLUGIN_FILE' ) ){
			define( 'ZMIMAGECONTRL_PLUGIN_FILE', __FILE__ );
		}
  
    
  }
  
  /**
	 * Include required files.
	 *
	 * @access private
	 * @since 0.0.1
	 * @return void
	 */
	private function includes() {
		require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/scripts.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/class-delete-api.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/common-functions.php';
    // require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/ajax-functions.php';
    
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/delele-posts-form-functions.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/delele-users-form-functions.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/delele-comments-form-functions.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/delele-meta-form-functions.php';
    // require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/delele-terms-form-functions.php';
    
		require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/admin-pages.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/admin-sidebar.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/posts/display-delete-posts.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/comments/display-delete-comments.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/users/display-delete-users.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/meta/display-delete-meta.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/terms/display-delete-terms.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/cleanup/cleanup-form.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/cleanup/cleanup-page.php';
		// require_once ZMIMAGECONTRL_PLUGIN_DIR . 'includes/admin/support-page.php';
	}
  
  /**
	 * Loads the plugin language files.
	 * 
	 * @access public
	 * @since 0.0.1
	 * @return void
	 */
	public function load_textdomain(){

		load_plugin_textdomain(
			'zm-imagecontrol',
			false,
			basename( dirname( __FILE__ ) ) . '/languages'
		);
	
  }
  
}

endif; // End If class exists check.

function zm_imagecontrol() {
	return ImageControl::instance();
}

// Get ImageControl Running.
zm_imagecontrol();