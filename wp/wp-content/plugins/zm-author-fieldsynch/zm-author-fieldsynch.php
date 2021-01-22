<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//no ... scratch that ... lets consider calling it directly. thats how it will run. an external cron job wll call this periodically.
//like every 2 min or something.
/*
	Plugin Name: zm-author-fieldsynch
	Plugin URI: http://arthurcormack.com/code/zm-author-fieldsynch
	Description: Automatically Takes the Nice name, from author meta data, and sticks it into a custom field for the author.
	Version: 0.1
	Author: Arthur Cormack
	Author URI: http://arthurcormack.com
*/
/*

	Copyright 2014  Zoomer Media  (email : a.cormack@zoomermedia.ca)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

/*
	so how is this going to work precisely?
	let's start with as simply as is possible.

	Givens: we have only 1 popup in play at any given time.
	Where does the popup come from? Does it get served from the same server that the site is on?
	Or do we have it come from a utility/sort of popup server?

	//ok. we need a public facing server, unless we are going to have the server curl the popup in
	//let js do it. And have a puplic server. The same one that serves the embeddable videos! And let that be a drupal thing: yea!

	//so we will have a javascript library: zm-popup.js ... also requires jquery
	//and also the code that will call functions onReady or whatever

	//so what does the js do?
	//it alters the dom, to make a little container
	(where in the dom?) (at the end?)
	(do we need to add some code to the dom in the footer, to make a place to put the popup?)



*/

/*
 *  TODO: the currentTime < the end time?, if so, make scheduled popup still popup
 *
 */
//what is the baseURL of the popUp Server? pull it out of the options. For now, just hard-code it.

$current_site = $_SERVER['HTTP_HOST'];


function setTheAuthorFieldForPost($whichPostID) {
	/*if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!wp_verify_nonce($_POST['_attributes_noncename'], plugin_basename(__FILE__))) return;
	if ('page' == $_POST['post_type']) {
    if (!current_user_can('edit_page', $post_id)) return;
  } else {
    if (!current_user_can('edit_post', $post_id)) return;
  }

  if (empty($_POST['acf'])) {
		return;
	}*/

	if (!isset($whichPostID) || $whichPostID == '') {
		return false;
	}
	$somePost = get_post($whichPostID);
	if (!isset($somePost) || empty($somePost)) {
		return false;
	}

	$someAuthorDisplayName = get_the_author_meta( 'display_name', $somePost->post_author );//this is correct!

	//update_field( 'authorindex', $someAuthorDisplayName, $whichPostID );

	//echo 'You are viewing ' . $current_site->site_name;

	/*if (!function_exists('get_current_site')) {
		include_once('../../wp-includes/ms-functions.php');
	}*/

	//$current_site = get_current_site();
	$current_site = $_SERVER['HTTP_HOST'];
	//wp_mail( 'arthur@puppetsprite.com',  $current_site . ':special testing of the zm author synch', 'here is the test:'.$whichPostID.', authorindex='. $someAuthorDisplayName);
	//why might this not be working? either it isn't getting fired, or there is a problem with the mail configurations.
	//update_field( 'field_5463acad9c382', $someAuthorDisplayName, $whichPostID );
	update_field( 'authorindex', $someAuthorDisplayName, $whichPostID );
	//$fields = $_POST['acf'];

	/*
	$field = $_POST['acf']['field_5463acad9c382'];
	if (isset($field) && $field != '') {
		$_POST['acf']['field_5463acad9c382'] = $someAuthorDisplayName;
	}
	*/
	//$_POST['acf']['field_5463acad9c382'] = "speedy gonzales";

	//$_POST['acf']['field_5463acad9c382'] = "speedy gonzales";

	//$_POST['acf']['field_abc123'];
	//maybe, what is happening, is that it is being overwritten, by an empty value!



	//echo("<h1>WTF!!!</h1>");

	//maybe it's not working, because the field doesn't exist for this post, and we need to do more than just update it - we need to create it
	//update_field( 'field_5463acad9c382', "Peppy La Pueue", $whichPostID);//this simply is not working.

	//h'mm
	/*If the reference for a value already exists, you can use the $field_name as the first parameter in the update_field function. ACF will lookup the field reference / field object from that field name.*/

	//field_key, on ez.local = field_5463acad9c382
	 //echo '<div class="updated"><p>This is my notice that happens when we save :)</p></div>';
	 //zm_af_makecustomtables();
}

//add_action( 'acf/input/form_data', 'setTheAuthorFieldForPost', 10, 1 );
add_action('acf/save_post', 'setTheAuthorFieldForPost', 20);



function setAuthorIndexFieldsForChunkOfPosts() {
	//chunksize? 50
	//starting point.
	//look to see if there is a wordpress variable set, if not, set it to start
	//$startingIndex =
	//update_option();
	//create a table: authorIndexSynch to hold the [post_id, modifiedtime, authorindex]
	//use wp_query
	//load posts, starting at the last
	//do a wp_query? on an array of post id's, that have
	//so first a mysql query, to get batch of post ids that match criteria - type = post or page or whatever, and published, and not a revision, but the actual post
	//then a WP_Query of posts, passing array of post id's
	//why

	wp_mail( 'arthur@puppetsprite.com', $current_site . ':5min zm author synch import', 'This is to notify you that a new hourly zm author synch import is happening');

	/*
	include_once('../../../wp-config.php');
	$dsn	= 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
	$user	= DB_USER;
	$pass	= DB_PASSWORD;

	try {
		$pdo = new PDO( $dsn , $user , $pass );

		$lastImportedPostIDQuery = <<<EOQ
			SELECT post_id AS postID FROM wp_authorIndexSynch
EOQ;

		$statement = $pdo->prepare( $lastImportedPostIDQuery );
		$statement->execute();
		$lastImportedPostID = $statement->fetch();

		wp_mail( 'a.cormack@zoomermedia.ca', 'hourly zm author synch import', 'This is to notify you that a new hourly zm author synch import is happening');
	} catch ( PDOException $e ) {
		echo 'Connection failed: ' . $e->getMessage();
	}
	*/


/*
	if (defined(DB_USER)) {
	} else {
		include_once('../../../wp-config.php');
	}
	$dsn	= 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
	$user	= DB_USER;
	$pass	= DB_PASSWORD;
	try {
		$pdo = new PDO( $dsn , $user , $pass );

		$lastImportedPostIDQuery = <<<EOQ
			SELECT post_id AS postID FROM wp_authorIndexSynch
EOQ;

		$statement = $pdo->prepare( $lastImportedPostIDQuery );
		$statement->execute();
		$lastImportedPostID = $statement->fetch();

		wp_mail( 'a.cormack@zoomermedia.ca', 'hourly zm author synch import', 'This is to notify you that a new hourly zm author synch import is happening');
	} catch ( PDOException $e ) {
		echo 'Connection failed: ' . $e->getMessage();
	}
	*/
}

//


function cron_add_5min( $schedules ) {
	$schedules['5min'] = array(
			'interval' => 5*60,
			'display' => __( 'Once every five minutes' )
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_5min' );


function zm_af_initialize () {
	wp_mail( 'a.cormack@zoomermedia.ca', 'initializing ', 'This is to notify you that the zm_af_ has been initialized');

	zm_af_makecustomtables();
	zm_af_scheduleHOurlyChunkImport();
}
function zm_af_makecustomtables () {
	$someSQL = "CREATE TABLE wp_authorIndexSynch (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		post_id mediumint(9) NOT NULL,
		authorindex VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		INDEX post_id (post_id),
		UNIQUE KEY id (id)
	);";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($someSQL);
	add_option( "zm_af_db_version", "1.0" );

	//wp_schedule_event($timestamp, $recurrence, $hook, $args);//will run, until it is done.
	/*if ( !wp_next_scheduled( 'setAuthorIndexFieldsForChunkOfPosts' ) ) {
        wp_schedule_event( current_time( 'timestamp' ), 'hourly', 'setAuthorIndexFieldsForChunkOfPosts');
  }*/
}
function zm_af_scheduleHOurlyChunkImport() {
	wp_mail( 'arthur@puppetsprite.com', 'initializing zm_af_scheduleHOurlyChunkImport', 'This is to notify you that the zm_af_scheduleHOurlyChunkImport has been initialized');
	//unschedule it if it is already there.
	//wp_unschedule_event(time(), 'setAuthorIndexFieldsForChunkOfPosts' );

	//wp_schedule_event( time(), '5min', 'setAuthorIndexFieldsForChunkOfPosts' );
	if ( ! wp_next_scheduled( 'setAuthorIndexFieldsForChunkOfPosts' ) ) {
		//wp_schedule_event(time(), '5min', 'setAuthorIndexFieldsForChunkOfPosts');
	}
	wp_schedule_event(time(), '5min', 'setAuthorIndexFieldsForChunkOfPosts');
}

register_activation_hook( __FILE__, 'zm_af_initialize');

//register an activation hook for cron
//on cron, do a batch - setAuthorIndexFieldsForChunkOfPosts


/*
class AuthorIndexFieldSynch_Database_Manager {
	public function __construct() {
		register_activation_hook( __FILE__, array ( $this, 'create_custom_tables' ) );

		//register_activation_hook( __FILE__, array ( $this, 'create_custom_tables' ) );

	}
	public function create_custom_tables() {
		// Creating Database Tables
		global $wpdb;
		$table_name = $wpdb->prefix.authorIndexSynch;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$someSQL = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		modified timestamp,
		post_id mediumint(9) NOT NULL,
		authorindex VARCHAR(255) DEFAULT '' NOT NULL
		UNIQUE KEY id (id),
		KEY post_id (post_id)
		);";//how to add a index

    dbDelta($sql);

	}
	public function init() {
		//nothing yet
		add_action('acf/save_post', array($this, 'setTheAuthorFieldForPost'), 20);
	}
}
$aifsdm = new AuthorIndexFieldSynch_Database_Manager();
$aifsdm->init();*/

function zm_author_fieldsynch_shortcode () {
	//some code to fire, if this is being called directly, in order to do a batch of authorindex updates
	//ob_start();
	//echo("zm_author_fieldsynch_shortcode is here");
	//$someStuff = ob_get_contents();
	//ob_end_clean();

	if (isset($_REQUEST['reset']) && ($_REQUEST['reset'] == "true" || $_REQUEST['reset'] == 1)) {
		update_option( "zmafprogress", 0 );
	}
	if(isset($_REQUEST['i']) && $_REQUEST['i'] != "") {
		$time_start = microtime(true);
		//we can't be calling this directly and use wp functions, bcause if it is called directly, then wp hasn't bootstrapped
		//... isntead, we have to move it out
		//echo("Doing some Author indexing ...");
		//instead, we'll make a shortcode, and then create a page that will do this for us.
		//alternatively

		$defaultRange = 500;
		//


		$queryRange = $defaultRange;
		//add_option( $option, $value, $deprecated, $autoload );
		//get_option( $option, $default );

		if (isset($_REQUEST["range"]) && $_REQUEST["range"] != "") {
			$queryRange = $_REQUEST["range"];
		}
		if (isset($_REQUEST["offset"]) && $_REQUEST["offset"] != "") {
			$queryOffset = $_REQUEST["offset"];
		} else {
			$queryOffset = get_option( "zmafprogress", 1 ) + 1;//get the stored offset of the last successful query, stored on wp_options
		}
		//we will store a zmafprogress option at the end, if we have not kakked out before completion
		//build a query of posts




		$loop = new WP_Query(
									array( 'post_type' => 'post', 'offset' => $queryOffset, 'posts_per_page' => $queryRange, 'orderby' => 'date', 'order' => 'ASC'

								) );

		$postsCompleted = 0;
		$postIDsCompleted = array();
		while ( $loop->have_posts() ) : $loop->the_post();
				//$someAuthorDisplayName = get_the_author_meta( 'display_name', $post->post_author );//this is correct!
				$somePostID = get_the_ID();
				$somePost = get_post( $somePostID );
				$someAuthorDisplayName = get_the_author_meta( 'display_name', $somePost->post_author );//this is correct!
				update_field( 'authorindex', $someAuthorDisplayName, $somePost );

				//wp_update_post( $somePost );//now programatically save the post
				do_action('save_post', $somePostID, $somePost);

				$postsCompleted++;
				//$totalProgress = $queryOffset

				array_push($postIDsCompleted, $somePostID);
		endwhile;
		wp_reset_query();

		//add_option( "zmafprogress",  );
		$currentProgress = intval($queryOffset) + $postsCompleted;
		update_option( "zmafprogress", $currentProgress );
		$someStatusMSG = "<h4>Completed update of $postsCompleted posts: </h4>\n<p>" . implode(', ', $postIDsCompleted) . "</p><p>\$currentProgress==$currentProgress</p>\n";


		$time_end = microtime(true);
		$time_delta = $time_end - $time_start;
		$someStatusMSG  .= "in $time_delta seconds";
		return $someStatusMSG;
	} else {
		return "i was not set to 1 ... so no fieldsynching was done";
	}
	//return $someStuff;
}
add_shortcode( 'zm-author-fieldsynch', 'zm_author_fieldsynch_shortcode' );

//add_filter( 'searchwp_big_selects', '__return_true' );
function zm_override_facetwp_search_limit( $posts_per_page ) {
    return ( 200 == $posts_per_page ) ? 1000 : $posts_per_page;
}
add_filter( 'searchwp_posts_per_page', 'zm_override_facetwp_search_limit', 12 );


?>
