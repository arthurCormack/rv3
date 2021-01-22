<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the
 * plugin admin area. This file also defines a function that starts the plugin.
 *
 * @link              ''
 * @since             1.0.0
 * @package           ZM Custom_Admin_Settings
 *
 * @wordpress-plugin
 * Plugin Name:       ZM Admin Menu
 * Plugin URI:        ''
 * Description:       Ads a special admin route that will render the admin menu by itself for admin users.
 * Version:           0.0.1
 * Author:            Arthur Cormack
 * Author URI:        ''
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
     die;
}

add_action( 'plugins_loaded', 'zm_adminmenu' );
/**
 * Starts the plugin.
 *
 * @since 1.0.0
 */
function zm_adminmenu() {

}
