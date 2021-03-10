<?php
/**
* Plugin Name: ZoomerRadio Content Api
* Plugin URI: none
* Description: Custom JSON REST endpoints, an utility functions, for things like thumbnails, and wp hooks
* Version: 0.2.3
* Authors: Arthur Cormack
* Author URI: none
* License: none
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

define( 'HYPERBOLE_VERSION', '4.1.1' );
define( 'HYPERBOLE__MINIMUM_WP_VERSION', '4.0' );
define( 'HYPERBOLE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


require_once( HYPERBOLE__PLUGIN_DIR . 'includes/redirections.php'); // defines getRedirectForURL
require_once( HYPERBOLE__PLUGIN_DIR . 'includes/thumbnails.php');
require_once( HYPERBOLE__PLUGIN_DIR . 'includes/guess404Permalink.php');
require_once( HYPERBOLE__PLUGIN_DIR . 'includes/hyperbole_excerpt.php');

require_once( HYPERBOLE__PLUGIN_DIR . 'includes/getAttachmentImage.php');// defines hyperbole_get_attachment_image
require_once( HYPERBOLE__PLUGIN_DIR . 'includes/acf_image_local_avatar.php');


// import all of the endpoints
$endpointPath = HYPERBOLE__PLUGIN_DIR . 'includes/endpoints';
foreach(glob("{$endpointPath}/*.php") as $file){
  require_once $file;
}
// import all the content handlers
$contentHandlerPath = HYPERBOLE__PLUGIN_DIR . 'includes/contenthandlers';
foreach(glob("{$contentHandlerPath}/*.php") as $file){
  require_once $file;
}

// register_rest_route( 'wpse/v1', '/post_by_permalink/(?P<path>[\S]+)',
add_action( "rest_api_init", function () {
  register_rest_route( 'ez/v3', '/getgeneralcontent/(?P<permalink>\S+)', array(
    'methods' => 'GET',
      'callback' => 'getgeneralcontent',
    'args' => array(
    ),
    'permission_callback' => function () {
      return true;
    }
  ) );
  
  register_rest_route( 'ez/v3', '/getgeneralcontent', array(
    'methods' => 'GET',
      'callback' => 'getgeneralcontent',
    'args' => array(
    ),
    'permission_callback' => function () {
      return true;
    }
  ) );

});

// -->>> Remove Permission for Editior to add Category
function remove_editor_manage_categories() {
  $role = get_role( 'editor' );
  $role->remove_cap( 'manage_categories' );
}
add_action('pre_get_posts', 'remove_editor_manage_categories' );

//  -->>> Remove tags under Posts
function myprefix_unregister_tags() {
  unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'myprefix_unregister_tags');

//  -->> Hide User avatar from all users page
function remove_avatar_from_users_list( $avatar ) {
  if (is_admin()) {
      global $current_screen; 
      if ( $current_screen->base == 'users' ) {
          $avatar = '';
      }
  }
  return $avatar;
}
add_filter( 'get_avatar', 'remove_avatar_from_users_list' );

// -->> Disabling Comments

// Removes from admin menu
add_action( 'admin_menu', 'my_remove_admin_menus' );
function my_remove_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
}
// Removes from post and pages
add_action('init', 'remove_comment_support', 100);

function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}
// Removes from admin bar
function mytheme_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

// -->> Adding Google Map key
function my_acf_init() {

  acf_update_setting('google_api_key', 'AIzaSyDLqFUfRz7HpXDEz1qpS5Hg2PhDvy3kg1w');
}

add_action('acf/init', 'my_acf_init');

// -->> Wp Cache 
// add_filter( 'rest_cache_headers', function( $headers ) {
//  $headers['Cache-Control'] = 'public, max-age=3600';
//  return $headers;
// } );

// add_action('publish_post', 'zm_clearall_w3totalcache');

// function zm_clearall_w3totalcache($post_id) {
//  // die('zm_clearall_w3totalcache here');
//  // w3tc_flush_all
//  if (function_exists('w3tc_flush_all')) {
//    // die('there is a w3tc_flush_all :)');
//    w3tc_flush_all();
//  } else {
//    // die('there is no w3tc_flush_all :()');
//  }
// }

// -->> Preview Page 
function set_headless_preview_link( $link ) {
  if (WP_DEBUG === false) {
      return site_url()
          . '/' .
          'preview/'
          . get_the_ID() . '/';
          // . wp_create_nonce( 'wp_rest' );
  }
}
add_filter( 'preview_post_link', 'set_headless_preview_link' );

// -->> Removed Links manager from dashboard 
function remove_menu_items(){
  remove_menu_page( 'themes.php' );
  remove_menu_page('link-manager.php');
  
}

add_action( 'wp_before_admin_bar_render', 'remove_menu_items', 999 );

// -->> Podcasts AND Episodes Connection

add_action('add_meta_boxes', function() {
  add_meta_box('episodes-parent', 'Select Podcast To Change URL', 'episodes_attributes_meta_box', 'episodes', 'side', 'default');
});

function episodes_attributes_meta_box($post) {
      $pages = wp_dropdown_pages(array('post_type' => 'podcasts', 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __('(no parent)'), 'sort_column'=> 'menu_order, post_title', 'echo' => 0));
      if ( ! empty($pages) ) {
          echo $pages;
      } // end empty pages check
}



function guess_404_permalink($someSlug) {
	global $wpdb;
	if ($someSlug) {
		$where = $wpdb->prepare("post_name LIKE %s", $wpdb->esc_like($someSlug) . '%');

		// if any of post_type, year, monthnum, or day are set, use them to refine the query
		// if ( get_query_var('post_type') )
		//         $where .= $wpdb->prepare(" AND post_type = %s", get_query_var('post_type'));
		// else
		//         $where .= " AND post_type IN ('" . implode( "', '", get_post_types( array( 'public' => true ) ) ) . "')";
		//
		// if ( get_query_var('year') )
		//         $where .= $wpdb->prepare(" AND YEAR(post_date) = %d", get_query_var('year'));
		// if ( get_query_var('monthnum') )
		//         $where .= $wpdb->prepare(" AND MONTH(post_date) = %d", get_query_var('monthnum'));
		// if ( get_query_var('day') )
		//         $where .= $wpdb->prepare(" AND DAYOFMONTH(post_date) = %d", get_query_var('day'));

		$post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $where AND post_status = 'publish'");
		if (!$post_id)
			return false;
		// if ( get_query_var( 'feed' ) )
		//         return get_post_comments_feed_link( $post_id, get_query_var( 'feed' ) );
		// elseif ( get_query_var( 'page' ) && 1 < get_query_var( 'page' ) )
		//         return trailingslashit( get_permalink( $post_id ) ) . user_trailingslashit( get_query_var( 'page' ), 'single_paged' );
		else
			// return get_permalink( $post_id );
			return str_replace(home_url(), "", get_permalink($post_id)); // gives us a relative url!
	}

	return false;
}