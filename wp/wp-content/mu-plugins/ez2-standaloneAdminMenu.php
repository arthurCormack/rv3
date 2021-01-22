<?php

//return false;

/*
Plugin Name: StandAloneAdminMenu
Plugin URI: https://github.com/arthurCormack
Description: The StandAloneAdminMenu mu-plugin tricks wp into believing it is at a different address than it actually is.
Author: Arthur Cormack
Author URI:  https://github.com/arthurCormack
Version: 0.0.1
Text Domain: zm-id
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


define( 'STANDALONEADMINMENU_URL',  trailingslashit( plugins_url('', __FILE__) ));
define( 'STANDALONEADMINMENU_PATH', trailingslashit( plugin_dir_path( __FILE__) ) );

//cookies
define('ZM_LOGGEDINTOWP', 'zm_loggedintowp');
define('ZM_COOKIE_EXPIRY', time() + 3600);

function isBotDetected() {
	if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|face|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function isJSONAPI() {
	//look at the path, and if
	if ( strpos($_SERVER['REQUEST_URI'], 'wp-json') !== false) {
		return true;
	} else {
		return false;
	}
}
function is_user_logged_in() {
	if (get_current_user_id() !== 0) {
		return true;
	}
	return false;
}
function ez2_standaloneadminmenu() {

	if (isBotDetected() || isJSONAPI() ) {
		return false;//don't do any checking, etc here.
	}
	// check to see if the user is logged in and has priviledges

	if ( isset($_GET['secretdiagnostic']) && $_GET['secretdiagnostic']==true) {
		// var_dump($_SERVER);
		// echo("<p>\$_SERVER['HTTP_USER_AGENT']==".$_SERVER['HTTP_USER_AGENT']."</p>");
		// echo("<p>\$_SERVER['QUERY_STRING']==".$_SERVER['QUERY_STRING']."</p>");
		// echo("<p>\$_SERVER['HTTP_REFERER']==".$_SERVER['HTTP_REFERER']."</p>");
		// echo("<p>\$_SERVER['REMOTE_HOST']==".$_SERVER['REMOTE_HOST']."</p>");
		// var_dump($_REQUEST);
		phpinfo();
		// echo("<p>Queried Object:</p>\n");
		// $queried_object = get_queried_object();
		// there is no queried object at this point
		// var_dump( $queried_object );
		echo("Here are all the \$GLOBALS");
		var_dump(array_keys($GLOBALS));
		die("secretdiagnostic complete");
	}

	// echo("Here are all the \$GLOBALS");
	// var_dump(array_keys($GLOBALS));
	// setcookie(ZM_LOGGEDINTOWP, "true", ZM_COOKIE_EXPIRY, '/');
	// maybe this just isn't the right place to set the cookie. maybe its best to do it in the theme, after wp has had a chance to bootstrap everything.
	// if (is_user_logged_in()) {
	// 	setcookie(ZM_LOGGEDINTOWP, "true", ZM_COOKIE_EXPIRY, '/');
	// } else {
	// 	unset($_COOKIE[ZM_LOGGEDINTOWP]);
	// }

}
add_action( 'muplugins_loaded', 'ez2_standaloneadminmenu' );
