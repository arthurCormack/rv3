<?php

/**
 * Plugin Name: ZM Content API
 * Plugin URI: none
 * Description: Creates a custom JSON REST endpoint for getting a content out of various rest endpoints.
 * Version: 0.2.7
 * Authors: Arthur Cormack, Nicole Tseronakis
 * Author URI: none
 * License: none
 */

add_action('init', 'zm_setAdminCookie');

// my_setcookie() set the cookie on the domain and directory WP is installed on
function zm_setAdminCookie()
{
	// $path = parse_url(get_option('siteurl'), PHP_URL_PATH);
	// $host = parse_url(get_option('siteurl'), PHP_URL_HOST);
	// $expiry = strtotime('+1 month');
	// setcookie('my_cookie_name_1', 'my_cookie_value_1', $expiry, $path, $host);
	// /* more cookies */
	// setcookie('my_cookie_name_2', 'my_cookie_value_2', $expiry, $path, $host);
	if (get_current_user_id() !== 0) {
		setcookie('zm_loggedintowp', "true", time() + 86400, '/'); //~24 hours
	}
}

function hide_update_notice()
{
	remove_action('admin_notices', 'update_nag', 3);
}
add_action('admin_notices', 'hide_update_notice', 1);

DEFINE('S3_BUCKETADDRESS', 'https://s3.amazonaws.com/zweb-s3.uploads/ez2');

require('ads.php');
require('zm_getdatedpost.php');
require('zm_getfeaturedfour.php');
require('zm_getfeaturedbin.php');
require('zm_getfeaturedgames.php');
require('zm_gethomehero.php');
require('zm_getmostpopular.php');
require('zm_featuredsinglebig.php');
require('zm_trendinglist.php');
require('zm_hottopics.php');
require('zm_customhottopics.php');
require('zm_recentposts.php');
require('zm_singlefeaturedinset.php');
require('zm_getarchive.php');
require('zm_getbookclub.php');
require('zm_getbookshelfcontent.php');
require('zm_getbuzzcontent.php');
require('zm_getbantercontent.php');
require('zm_currentmag.php');
require('zm_homepoll.php');
require('zm_hometile.php');
require('zm_ideacity.php');
require('zm_quote.php');
require('zm_homepagestuff.php');
require('zm_gettiles.php');
require('zm_redirections.php');
require('zm_gethoroscope.php');
require('zm_getgallery.php');
require('zm_getrealestate.php');
require('zm_getauthor.php');
require('zm_getcontactform.php');
require('zm_signuppage.php');
require('zm_subscribe.php');
require('zm_getDraftpost.php');
require('listRecentPostLinks.php');
require('decryptsfdata.php');
require('zm_download_our_app.php');
require('zm_image_deletions.php');

function zm_content_api_setupthumbnails()
{
	add_theme_support('post-thumbnails');
	// add_image_size( 'category-thumb_300', 300, 300, true ); // 300 pixels wide (and unlimited height)
	// add_image_size( 'slide_800x600', 800, 600, true ); // (cropped)
	// add_image_size( 'regular300x225', 300, 225, true ); // 4:3
	add_image_size('teaser_square', 350, 350, true); // 4:3
	add_image_size('huge1440', 2560, 1440, false); // 4:3
	add_image_size('huge', 1920, 1080, false); // 4:3
	add_image_size('huge720', 1280, 720, false); // 4:3
	add_image_size('huge480', 720, 480, false); // 4:3
	add_image_size('huge360', 480, 360, false); // 4:3
	add_image_size('large_crop', 1024, 700, true); // 4:3
	add_image_size('tile', 480, 600, true);
	add_image_size('sharing', 560, 480, false); // 1:1,
	add_image_size('large_crop', 1024, 700, true); // 4:3

}
add_action('init', 'zm_content_api_setupthumbnails');

add_action("rest_api_init", function () {

	register_rest_route('zm-content/v1', '/getdatedpost/(?P<catslug>\S+)/(?P<yearslug>\d+)/(?P<monthslug>\d+)/(?P<dayslug>\d+)/(?P<postslug>\S+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getdatedpost',
		'args' => array(
			'catslug' => array(
				'validate_callback' => function ($param, $request, $key) {
					return true; //how can it work when there are two different items in the route?
				}
			),
			'postslug' => array(
				'validate_callback' => function ($param, $request, $key) {
					return true;
				}
			),
		),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getfirstdatedpost/(?P<catslug>\S+)/(?P<yearslug>\d+)/(?P<monthslug>\d+)/(?P<dayslug>\d+)/(?P<postslug>\S+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getfirstdatedpost',
		'args' => array(
			'catslug' => array(
				'validate_callback' => function ($param, $request, $key) {
					return true; //how can it work when there are two different items in the route?
				}
			),
			'postslug' => array(
				'validate_callback' => function ($param, $request, $key) {
					return true;
				}
			),
		),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));
	register_rest_route('zm-content/v1', '/getDraftpost/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getDraftpost',
		'args' => array(
			'id' => array(
				'validate_callback' => function ($param, $request, $key) {
					return is_numeric($param);
				}
			),
		),
		'permission_callback' => function () {
			return true;
		}
	));
	//
	register_rest_route('zm-content/v1', '/getGamesScript', array(
		'methods' => 'GET',
		'callback' => 'zm_getGamesScript',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
  ));
  
  register_rest_route('zm-content/v1', '/imagedeletions', array(
		'methods' => 'POST',
		'callback' => 'zm_imagedeletions',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
  ));
  
	register_rest_route('zm-content/v1', '/getdownloadourappdata', array(
		'methods' => 'GET',
		'callback' => 'zm_download_our_app',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getnextdatedpost/(?P<catslug>\S+)/(?P<yearslug>\d+)/(?P<monthslug>\d+)/(?P<dayslug>\d+)/(?P<postslug>\S+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getnextdatedpost',
		'args' => array(
			'catslug' => array(
				'validate_callback' => function ($param, $request, $key) {
					return true; //how can it work when there are two different items in the route?
				}
			),
			'postslug' => array(
				'validate_callback' => function ($param, $request, $key) {
					return true;
				}
			),
		),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));
	// hopefully this will do the trick
	// and this is another empty comment

	register_rest_route('zm-content/v1', '/getcurrentmag', array(
		'methods' => 'GET',
		'callback' => 'zm_getcurrentmag',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/gethoroscopes', array(
		'methods' => 'GET',
		'callback' => 'zm_gethoroscopes',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/gethomepoll', array(
		'methods' => 'GET',
		'callback' => 'zm_gethomepoll',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	//
	register_rest_route('zm-content/v1', '/gethomequote', array(
		'methods' => 'GET',
		'callback' => 'zm_getquote',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/gethomestart', array(
		'methods' => 'GET',
		'callback' => 'zm_getstart',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getfeaturedpopulartrending', array(
		'methods' => 'GET',
		'callback' => 'zm_getFeaturedPopularTrending',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));


	register_rest_route('zm-content/v1', '/getauthor/(?P<authorslug>\S+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getauthor',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));
	register_rest_route('zm-content/v1', '/getcontactform', array(
		'methods' => WP_REST_SERVER::CREATABLE,
		'callback' => 'zm_getcontactform',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));
	register_rest_route('zm-content/v1', '/getauthorposts/(?P<authorslug>\S+)/(?P<chunknum>\d+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getauthorposts',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getgallery/(?P<galleryid>\d+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getgallery',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/gethometile', array(
		'methods' => 'GET',
		'callback' => 'zm_gethometile',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getideacity', array(
		'methods' => 'GET',
		'callback' => 'zm_getideacity',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/gettiles', array(
		'methods' => 'GET',
		'callback' => 'zm_gettiles',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getrealestate', array(
		'methods' => 'GET',
		'callback' => 'zm_getrealestate',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getfeaturedfour', array(
		'methods' => 'GET',
		'callback' => 'zm_getfeaturedfour',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getfeaturedgames', array(
		'methods' => 'GET',
		'callback' => 'zm_getfeaturedgames',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getfeaturedbin', array(
		'methods' => 'GET',
		'callback' => 'zm_getfeaturedbin',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));


	register_rest_route('zm-content/v1', '/gethomehero', array(
		'methods' => 'GET',
		'callback' => 'zm_gethomehero',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getmostpopular', array(
		'methods' => 'GET',
		'callback' => 'zm_getmostpopular',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getfeaturedsinglebig', array(
		'methods' => 'GET',
		'callback' => 'zm_featuredsinglebig',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/gettrendinglist', array(
		'methods' => 'GET',
		'callback' => 'zm_gettrendinglist',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/gethottopics', array(
		'methods' => 'GET',
		'callback' => 'zm_hottopics',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/signuppage', array(
		'methods' => 'GET',
		'callback' => 'zm_signuppage',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/subscribe', array(
		'methods' => 'GET',
		'callback' => 'zm_subscribe',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));


	register_rest_route('zm-content/v1', '/getcustomhottopics', array(
		'methods' => 'GET',
		'callback' => 'zm_customhottopics',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getrecentposts', array(
		'methods' => 'GET',
		'callback' => 'zm_getrecentposts',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getsinglefeaturedinset', array(
		'methods' => 'GET',
		'callback' => 'zm_singlefeaturedinset',
		'args' => array(),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getpage/(?P<pageslug>\S+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getpage',
		'args' => array(
			'pageslug' => array(
				'validate_callback' => function ($param, $request, $key) {
					//return is_string( $param );;
					return true; //how can it work when there are two different items in the route?
				}
			),
		),
		'permission_callback' => function () {
			return true;
			//consider using some sort of key, so that this can't just be used willy nilly from external sources
		}
	));

	register_rest_route('zm-content/v1', '/getposts', array(
		'methods' => 'GET',
		'callback' => 'getPosts',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));



	register_rest_route('zm-content/v1', '/gettaggedposts', array(
		'methods' => 'GET',
		'callback' => 'getTaggedPosts',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));


	register_rest_route('zm-content/v1', '/getarchive/(?P<taxonomy>\S+)/(?P<termname>\S+)/(?P<chunknum>\d+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getArchive',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));


	register_rest_route('zm-content/v1', '/zm_getbookclubsublandingcontent/(?P<postslug>\S+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getbookclubsublandingcontent',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/zm_getsinglecategoryposts/(?P<postslug>\S+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getsinglecategoryposts',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	//BookShelf Book Club Landing Page Starts
	register_rest_route('zm-content/v1', '/zed_getherobookshelf', array(
		'methods' => 'GET',
		'callback' => 'zed_getherobookshelf',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/zm_getthefeedbookshelf', array(
		'methods' => 'GET',
		'callback' => 'zm_getthefeedbookshelf',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/zed_getquoteforbookshelf', array(
		'methods' => 'GET',
		'callback' => 'zed_getquoteforbookshelf',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/zed_getreadandrecommendedforbookshelf', array(
		'methods' => 'GET',
		'callback' => 'zed_getreadandrecommendedforbookshelf',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/zed_getrelatedreadandrecommendedbookshelf', array(
		'methods' => 'GET',
		'callback' => 'zed_getrelatedreadandrecommendedbookshelf',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	
	register_rest_route('zm-content/v1', '/zm_getathomeforbookshelf', array(
		'methods' => 'GET',
		'callback' => 'zm_getathomeforbookshelf',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/zed_getnovelencountersbookshelf', array(
		'methods' => 'GET',
		'callback' => 'zed_getnovelencountersbookshelf',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	
	register_rest_route('zm-content/v1', '/zed_onOurBookShelf', array(
		'methods' => 'GET',
		'callback' => 'zed_onOurBookShelf',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	//BookShelf Book Club Landing Page Ends


	//Buzz Book Club Landing Page Starts
	register_rest_route('zm-content/v1', '/zed_getheroforbuzz', array(
		'methods' => 'GET',
		'callback' => 'zed_getheroforbuzz',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zm_getthefeedbuzz', array(
		'methods' => 'GET',
		'callback' => 'zm_getthefeedbuzz',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getquoteforbuzz', array(
		'methods' => 'GET',
		'callback' => 'zed_getquoteforbuzz',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getopinionforbuzz', array(
		'methods' => 'GET',
		'callback' => 'zed_getopinionforbuzz',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getlisticlesforbuzz', array(
		'methods' => 'GET',
		'callback' => 'zed_getlisticlesforbuzz',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getbestsellers', array(
		'methods' => 'GET',
		'callback' => 'zed_getbestsellers',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	//Buzz Book Club Landing Page Ends


	//Banter Book Club Landing Page Starts
	register_rest_route('zm-content/v1', '/zed_getwelcomecontent', array(
		'methods' => 'GET',
		'callback' => 'zed_getwelcomecontent',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getcalendarcontent', array(
		'methods' => 'GET',
		'callback' => 'zed_getcalendarcontent',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getpodcastcontent', array(
		'methods' => 'GET',
		'callback' => 'zed_getpodcastcontent',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getvotecontent', array(
		'methods' => 'GET',
		'callback' => 'zed_getvotecontent',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	//Banter Book Club Landing Page Ends


	// we can't have a single request to get all of the data at the same time - that is too much work to do all at once for php

	register_rest_route('zm-content/v1', '/zm_getbookclub/(?P<postslug>\S+)', array(
		'methods' => 'GET',
		'callback' => 'zm_getbookclub',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	// 
	register_rest_route('zm-content/v1', '/zed_getquote', array(
		'methods' => 'GET',
		'callback' => 'zed_getquote',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));


	// This is for the feed section on the V2 of Zed Book Club
	register_rest_route('zm-content/v1', '/zm_getthefeed', array(
		'methods' => 'GET',
		'callback' => 'zm_getthefeed',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	// register_rest_route( 'zm-content/v1', '/zm_getbookclub', array(
	// 	'methods' => 'GET',
	// 	'callback' => 'zm_getbookclub',
	// 	'args' => array(),
	// 	'permission_callback' => function () {
	// 		return true;
	// 	}
	// ) );

	register_rest_route('zm-content/v1', '/zed_gethero', array(
		'methods' => 'GET',
		'callback' => 'zed_gethero',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getclubhouse', array(
		'methods' => 'GET',
		'callback' => 'zed_getclubhouse',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getshelflife', array(
		'methods' => 'GET',
		'callback' => 'zed_getshelflife',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
	register_rest_route('zm-content/v1', '/zed_getauthorspotlight', array(
		'methods' => 'GET',
		'callback' => 'zed_getauthorspotlight',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/zed_entertainingIdeas', array(
		'methods' => 'GET',
		'callback' => 'zed_entertainingIdeas',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/zed_getreadandrecommended', array(
		'methods' => 'GET',
		'callback' => 'zed_getreadandrecommended',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/zed_getopinion', array(
		'methods' => 'GET',
		'callback' => 'zed_getopinion',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/getrecentpostlinks', array(
		'methods' => 'GET',
		'callback' => 'getRecentPostLinks',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	register_rest_route('zm-content/v1', '/getslideshow', array(
		'methods' => 'GET',
		'callback' => 'getSlideshow',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));

	//decryptsfdata
	register_rest_route('zm-content/v1', '/decryptsfdata', array(
		'methods' => 'POST',
		'callback' => 'decryptSFData',
		'args' => array(),
		'permission_callback' => function () {
			return true;
		}
	));
});
///wp-json/zm-benefits/v1/getbenefit/990 works!
function zm_getpost($data)
{
	//
	$somePostID = $data['id'];
	$somePost = new stdClass;
	$args = array('post_type' => 'post', 'p' => $somePostID, 'post_status' => 'publish');
	wp_reset_query();
	$postsQuery = query_posts($args);
	while (have_posts()) : the_post();
		global $post;
		$somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
		$somePost->post_content = apply_filters('the_content', get_the_content());
		$somePost->post_date = $post->post_date;
		$somePost->author = get_the_author();
		$somePost->id = $somePostID;
		$somePost->actualID = get_the_ID();
	endwhile;
	return $somePost;
}
define('DEFAULT_QUERYOFFSET', 0);
define('DEFAULT_QUERYRANGE', 11);



// so the problem isn't so much what to do when we hit one of these verys specific kinds or urls
// what are the specific patterns that typically fail?
// events/somethingorother ... <catname>/<postslug>
// actually, is moslty just the single slug, like for getting a page. which is getPosts, wiht a pagename



function zm_get_attachment_image($attachment_id, $size = 'thumbnail', $icon = false, $attr = '')
{
	$html = '';
	$image = wp_get_attachment_image_src($attachment_id, $size, $icon);
	// return wp_get_attachment_image_src($attachment_id, $size, $icon);
	if ($image) {
		list($src, $width, $height) = $image;
		$hwstring = image_hwstring($width, $height);
		$size_class = $size;
		if (is_array($size_class)) {
			$size_class = join('x', $size_class);
		}
		$attachment = get_post($attachment_id);
		$default_attr = array(
			'src'   => $src,
			'class' => "attachment-$size_class size-$size_class",
			'alt'   => trim(strip_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true))),
			'srcSet' => wp_get_attachment_image_srcset($attachment_id),
			'width' => $width,
			'height' => $height
		);

		$attr = wp_parse_args($attr, $default_attr);

		// Generate 'srcset' and 'sizes' if not already present.
		if (empty($attr['srcSet'])) {
			$attr['srcset_alreaddyset'] = 'false';
			$image_meta = wp_get_attachment_metadata($attachment_id);

			if (is_array($image_meta)) {
				$size_array = array(absint($width), absint($height));
				$srcset = wp_calculate_image_srcset($size_array, $src, $image_meta, $attachment_id);
				$sizes = wp_calculate_image_sizes($size_array, $src, $image_meta, $attachment_id);

				//not getting in here?
				$attr['srcSet'] = $srcset;

				if ($srcset && ($sizes || !empty($attr['sizes']))) {
					$attr['srcSet'] = $srcset;
					if (empty($attr['sizes'])) {
						$attr['sizes'] = $sizes;
					}
				}
			} else {
				$attr['srcSet'] = 'not available?';
			}
		} else {
			$attr['srcset_alreaddyset'] = 'true';
		}

		/**
		 * Filters the list of attachment image attributes.
		 *
		 *
		 * @param array        $attr       Attributes for the image markup.
		 * @param WP_Post      $attachment Image attachment post.
		 * @param string|array $size       Requested size. Image size or array of width and height values
		 *                                 (in that order). Default 'thumbnail'.
		 */
		$attr = apply_filters('wp_get_attachment_image_attributes', $attr, $attachment, $size);
		$attr = array_map('esc_attr', $attr);

		$s3SubstitutionForThumbnails = true;

		if (isset($s3SubstitutionForThumbnails) && $s3SubstitutionForThumbnails !== false) {
			// vvar_dump($attr);
			$someATTR_JSON = json_encode($attr, JSON_UNESCAPED_SLASHES);
			// echo($someATTR_JSON);
			$currentSiteAddress = get_site_url(); //
			//localHostToEZImages
			$localhostAddress = 'http://localhost:8080';
			if (strpos($currentSiteAddress, '.local') !== false) {
				$localhostAddress = 'http://everythingzoomer.local';
			}

			// $currentSiteAddress = get_option('siteurl');
			// var_dump("<p>\$currentSiteAddress=={$currentSiteAddress}</p>\n");

			// $S3_BUCKETADDRESS = S3_BUCKETADDRESS;// without the trailing /
			// $someATTR_JSON = str_replace($currentSiteAddress, $S3_BUCKETADDRESS, $someATTR_JSON);
			$someATTR_JSON = str_replace($localhostAddress, S3_BUCKETADDRESS, $someATTR_JSON);
			$attr = json_decode($someATTR_JSON);
			// var_dump($attr);
			// die('huh?!');
		}
	}
	// die(get_site_url());
	// return $html;
	// $attr->attachment = $attachment;//delete this late. this is just to see what the captions descriptsion are called
	// $attr->image_meta = $image_meta;
	// $attr->caption = wp_get_attachment_caption($attachment_id);

	/*
    $attachment = get_post( $attachment_id );
return array(
    'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
    'caption' => $attachment->post_excerpt,
    'description' => $attachment->post_content,
    'href' => get_permalink( $attachment->ID ),
    'src' => $attachment->guid,
    'title' => $attachment->post_title
);*/
	$attachment = get_post($attachment_id);
	$attr->meta = array(
		'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
		'caption' => $attachment->post_excerpt,
		'description' => $attachment->post_content,
		'href' => get_permalink($attachment->ID),
		'src' => $attachment->guid,
		'title' => $attachment->post_title
	);

	return $attr;
}


function httpToHttps_filter($matches, $ext)
{
	return 'https://www.everythingzoomer.com/wp-content/uploads/' . $matches[1] . '.' . $ext;
}
function httpToHttps_filter_jpg($matches)
{
	return httpToHttps_filter($matches, 'jpg');
}
function httpToHttps_filter_jpeg($matches)
{
	return httpToHttps_filter($matches, 'jpeg');
}
function httpToHttps_filter_png($matches)
{
	return httpToHttps_filter($matches, 'png');
}
function httpToHttps_filter_gif($matches)
{
	return httpToHttps_filter($matches, 'gif');
}

// a comment
function httpToHttpsImages($stuff, $specificURL = false)
{
	// make any image (ends with jpg, jpeg, gif or png) that is at http://*.everythingzoomer.com got to https://www.everythingzoomer.com instead.
	$someStuff_JSON = json_encode($stuff, JSON_UNESCAPED_SLASHES);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/[a-z]{3,}\.everythingzoomer\.com\/wp-content\/uploads\/(\S+)\.jpg/', 'httpToHttps_filter_jpg', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/[a-z]{3,}\.everythingzoomer\.com\/wp-content\/uploads\/(\S+)\.jpeg/', 'httpToHttps_filter_jpeg', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/[a-z]{3,}\.everythingzoomer\.com\/wp-content\/uploads\/(\S+)\.png/', 'httpToHttps_filter_png', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/[a-z]{3,}\.everythingzoomer\.com\/wp-content\/uploads\/(\S+)\.gif/', 'httpToHttps_filter_gif', $someStuff_JSON);
	// and now fix: http://ez2-wp-prod.pkgdug5ie3.us-east-1.elasticbeanstalk.com images
	$someStuff_JSON = preg_replace_callback('/http\:\/\/ez2-wp-prod\.pkgdug5ie3.us-east-1.elasticbeanstalk\.com\/wp-content\/uploads\/(\S+)\.jpg/', 'httpToHttps_filter_jpg', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/ez2-wp-prod\.pkgdug5ie3.us-east-1.elasticbeanstalk\.com\/wp-content\/uploads\/(\S+)\.jpeg/', 'httpToHttps_filter_jpeg', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/ez2-wp-prod\.pkgdug5ie3.us-east-1.elasticbeanstalk\.com\/wp-content\/uploads\/(\S+)\.png/', 'httpToHttps_filter_png', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/ez2-wp-prod\.pkgdug5ie3.us-east-1.elasticbeanstalk\.com\/wp-content\/uploads\/(\S+)\.gif/', 'httpToHttps_filter_gif', $someStuff_JSON);

	// http://ez2.local
	$someStuff_JSON = preg_replace_callback('/http\:\/\/ez2\.local\/wp-content\/uploads\/(\S+)\.jpg/', 'httpToHttps_filter_jpg', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/ez2\.local\/wp-content\/uploads\/(\S+)\.jpeg/', 'httpToHttps_filter_jpeg', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/ez2\.local\/wp-content\/uploads\/(\S+)\.png/', 'httpToHttps_filter_png', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/ez2\.local\/wp-content\/uploads\/(\S+)\.gif/', 'httpToHttps_filter_gif', $someStuff_JSON);

	return json_decode($someStuff_JSON, true);
}


function localHostToEZImages($stuff, $specificURL = false)
{
	// make any image (ends with jpg, jpeg, gif or png) that is at http://localhost:8080 goto https://www.everythingzoomer.com instead.
	$someStuff_JSON = json_encode($stuff, JSON_UNESCAPED_SLASHES);
	// http://localhost:8080
	$someStuff_JSON = preg_replace_callback('/http\:\/\/localhost:8080\/wp-content\/uploads\/(\S+)\.jpg/', 'httpToHttps_filter_jpg', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/localhost:8080\/wp-content\/uploads\/(\S+)\.jpeg/', 'httpToHttps_filter_jpeg', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/localhost:8080\/wp-content\/uploads\/(\S+)\.png/', 'httpToHttps_filter_png', $someStuff_JSON);
	$someStuff_JSON = preg_replace_callback('/http\:\/\/localhost:8080\/wp-content\/uploads\/(\S+)\.gif/', 'httpToHttps_filter_gif', $someStuff_JSON);
	return json_decode($someStuff_JSON, true);
}

function S3Substitute($stuff, $specificURL = false)
{
	return $stuff;
	$someStuff_JSON = json_encode($stuff, JSON_UNESCAPED_SLASHES);
	$currentSiteAddress = $specificURL ? $specificURL : get_site_url();
	$someStuff_JSON = str_replace($currentSiteAddress, S3_BUCKETADDRESS, $someStuff_JSON);
	// 2 additional passes!
	// , 'http://localhost:8080', , 'http://localhost'
	$someStuff_JSON = str_replace('http://localhost:8080', S3_BUCKETADDRESS, $someStuff_JSON);
	$someStuff_JSON = str_replace('http://localhost', S3_BUCKETADDRESS, $someStuff_JSON);

	return json_decode($someStuff_JSON, true);
	// return $stuff;
}

function zm_getpage($data)
{
	$somePageSlug = $data['pageslug'];
	$args = array(
		'post_type' => 'page',
		'name' => $somePageSlug,
		'post_status' => 'publish',
		'orderby' => 'post_date', 'order' => 'DESC',
	);
	wp_reset_query();
	$pageQuery = query_posts($args);

	$posts = array();
	$i = 0;
	while (have_posts()) : the_post();
		global $post;
		$somePost = new stdClass;

		$somePost->post_content = apply_filters('the_content', get_the_content());
		$somePost->id = get_the_ID();
		// $somePost->post_title = wp_specialchars_decode(get_the_title($somePost->id), ENT_QUOTES);
		$somePost->post_title = wp_specialchars_decode($post->post_title, ENT_QUOTES);
		$somePost->post_date = $post->post_date;
		$somePost->author = get_the_author();
		$somePost->post_slug = $post->post_name;

		// need to add some useful og stuff, like image, and excerpt.
		$somePost->excerpt = zmExcerpt($somePost->id, 100, true, false, false, "", "", "...");


		$someThumbnailURLs = array();
		if (has_post_thumbnail()) {
			$post_thumbnail_id = get_post_thumbnail_id($somePost->id); // is $post_id defined at this point?
			$someThumbnail = (object) array(
				'thumbnail' => wp_get_attachment_image_src($post_thumbnail_id, $size = 'thumbnail', $icon = false),
				'medium'	=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'medium', $icon = false),
				'medium_large'	=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'medium_large', $icon = false),
				'large'		=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'large', $icon = false),
				'full'		=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'full', $icon = false),
				'category-thumb_300' => wp_get_attachment_image_src($post_thumbnail_id, $size = 'category-thumb_300', $icon = false)
			);
			array_push($someThumbnailURLs, $someThumbnail);
		}
		$somePost->thumbnails = $someThumbnailURLs; //

		array_push($posts, $somePost);
		$i++;

	endwhile;

	if (count($posts) == 0) {
		// invoke the not found, attempt redirectToClosestMatch
		$result = new stdClass;
		$result->notFound = true;
		// $specificPostName or $specificPageName
		// $someSlug =
		$result->redirectLocation = guess_404_permalink($specificPageName); // something, or false
		// attempt redirection.
		return $result;
	} else {
		$result = $posts[0];
		$toggleAdButton = get_field('turn_off_ads', $result->id);
		$result->adsTurnedOff = $toggleAdButton;
		$ads = new stdClass;
		$ads->leaderboard = getPostAd($result->id, 'ez2_ad_leaderboard');
		$ads->bigbox = getPostAd($result->id, 'ez2_ad_bigbox');
		$ads->sponsored_one = getPostAd($result->id, 'ez2_ad_sponsored_one');
		$ads->sponsored_two = getPostAd($result->id, 'ez2_ad_sponsored_two');
		$ads->interstitial = getPostAd($result->id, 'ez2_ad_sponsored_interstitial');
		$ads->wallpaper = getPostAd($result->id, 'ez2_ad_wallpaper');
		$ads->mobileInterstitial = getPostAd($result->id, 'ez2_ad_mobile_interstitial'); // this works on the home page, but does not on the post pages.
		$ads->mobileInterstitial2 = getPostAd($result->id, 'ez2_ad_mobile_interstitial_2');
		$ads->ez2_ad_desktop_interstitial = getPostAd($result->id, 'ez2_ad_desktop_interstitial'); //

		if (!isset($toggleAdButton) || $toggleAdButton === false) {
			$result->ads = $ads;
		}
		// $result->test = "apples";
		return ($result);
	}
	// return $somePost;
}


function getFeaturedPosts($whichFeatureField = '')
{
	//could be todays_featured or friends_of_the_new_classical or something else, presumably ...
	if ($whichFeatureField === '') return;
	//initial query. get the id's of the featured post
	wp_reset_query();
	$somePosts = array();
	$args = array(
		'post_status' => 'publish'
	);
	$args['posts_per_page'] = 1;
	if (isset($data['slug']) && $data['slug'] != '') {
		$args['name'] = $data['slug']; //and so we wil be returning just one.
	} else {
		$args['pagename'] = 'home';
	}
	//return $args;
	$featuredItemIDs = array();
	$pageQuery = query_posts($args);
	while (have_posts()) : the_post();
		$somePost = new stdClass;
		$somePost->id = get_the_ID();
		// return $somePost->id;
		$someFeaturedItems = get_field($whichFeatureField, $somePost->id); // it seems to be pulling the slides, and not the todays featured!
		// $someFeaturedItems = get_field('todays_featured', 150);// it seems to be pulling the slides, and not the todays featured!
		if (isset($someFeaturedItems) && count($someFeaturedItems) > 0) {
			foreach ($someFeaturedItems as $someFeaturedItem) {
				array_push($featuredItemIDs, $someFeaturedItem->ID);
			}
		}
	endwhile;

	// return $featuredItemIDs;
	// now we have an array of $featuredItemIDs
	// do a second query, with post__in
	wp_reset_query();
	$args = array(
		'post_status' => 'publish',
		'orderby' => 'ASC',
		'post__in' => $featuredItemIDs
	);

	$pageQuery = query_posts($args);
	while (have_posts()) : the_post();
		//wp_get_attachment_image_src( $attachment_id, $size = 'thumbnail', $icon = false )
		$somePost = new stdClass;
		$somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
		$somePost->post_content = apply_filters('the_content', get_the_content());
		$somePost->id = get_the_ID();
		//$somePost->excerpt = zmExcerpt();
		$somePost->excerpt = zmExcerpt($somePost->id, 100, true, false, false, "", "", "...");
		// $somePost->event_start = get_field('event_start');
		$somePost->author = get_the_author();
		$somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->ID)); // gives us a relative url!
		$somePost->post_slug = basename(get_permalink());
		$someCat = get_the_category();
		if (count($someCat) > 0) {
			$someMinimalCats = array();
			for ($i = 0; $i < count($someCat); $i++) {
				$someMinimalCatObj = new stdClass;
				$someMinimalCatObj->slug = $someCat[$i]->slug;
				$someMinimalCatObj->term_id = $someCat[$i]->term_id;
				$someMinimalCatObj->name = $someCat[$i]->name;
				array_push($someMinimalCats, $someMinimalCatObj);
			}
			$somePost->cats = $someMinimalCats;
		}
		$someThumbnailURLs = array();
		if (has_post_thumbnail()) {
			$post_thumbnail_id = get_post_thumbnail_id($post_id);
			$someThumbnail = (object) array(
				'thumbnail' => wp_get_attachment_image_src($post_thumbnail_id, $size = 'thumbnail', $icon = false),
				'medium'	=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'medium', $icon = false),
				'large'		=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'large', $icon = false),
				'category-thumb_300'		=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'category-thumb_300', $icon = false),
			);
			array_push($someThumbnailURLs, $someThumbnail);
		}
		$somePost->thumbnails = $someThumbnailURLs; //
		$somePost->youtube_video_id = get_field('youtube_video_id');
		array_push($somePosts, $somePost);
	endwhile;
	if (count($somePosts) > 1) {
		return $somePosts;
	} else if (count($somePosts) == 1) {
		return $somePosts[0];
	} else {
		return $somePosts;
	}
}

function getFriendsOfClassical()
{
	// return 'shizzlenits';
	return getFeaturedPosts('friends_of_the_new_classical');
}
function getCommunitySpotlights()
{
	return getFeaturedPosts('community_spotlights');
}
function getTodaysFeatured()
{
	return getFeaturedPosts('todays_featured');
}
function getTaggedPosts($data)
{
	return null;
}

define('DEFAULT_GETPOSTS_RANGE', 23);
define('DEFAULT_GETPOSTS_OFFSET', 0);
define('DEFAULT_GETPOSTS_POSTTYPE', 'post');
function getPosts($data)
{
	//preliminaryQuery first.
	// if we are calling getPosts in single mode, ie for a single post or page, we could bypass this iniital query altogether, no?
	// return "hello";
	// wp_reset_query();
	$post_type =  isset($data['post_type']) ? $data['post_type'] : DEFAULT_GETPOSTS_POSTTYPE;
	$preliminaryArgs = array(
		//'post_type' => $post_type,
		'posts_per_page' => -1,
		'post_status' => 'publish',
	);

	global $wpdb;
	// $result = $wpdb->get_results( "SELECT * FROM wp_usermeta WHERE meta_key = 'points' AND user_id = '1'");
	$someQuery = "SELECT COUNT(*) AS postCount FROM wp_posts WHERE wp_posts.post_status = 'publish'";
	if (isset($data['category']) || isset($data['tag'])) {
		if (isset($data['category'])) {
			$taxSlug = $data['category'];
			$someTaxonomy = 'category';
		} else if (isset($data['tag'])) {
			$taxSlug = $data['tag'];
			$someTaxonomy = 'post_tag';
		}

		$someTaxQuery = <<<SQL
		SELECT COUNT(*) AS postCount FROM wp_posts
		LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
		LEFT JOIN wp_term_taxonomy ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
		LEFT JOIN wp_terms ON wp_terms.term_id = wp_term_taxonomy.term_id
		WHERE wp_term_taxonomy.taxonomy = '$someTaxonomy'
		AND wp_posts.post_status = 'publish'
		AND wp_terms.slug = '$taxSlug'
SQL;
		$someQuery = $someTaxQuery;
	}

	//return $someQuery;
	//$result = $wpdb->get_results( $someQuery );
	$result = $wpdb->get_row($someQuery);
	// print_r($result);
	// return("\$result=={$result}");
	//return($result);
	$somePostCount = $result->postCount;
	// $specificCategory = isset($data['category']) ? $data['category'] : false;
	// if ($specificCategory) $preliminaryArgs['category_name'] = $specificCategory;
	// $firstPostsQuery = new WP_Query( $preliminaryArgs );
	// $somePostCount = $firstPostsQuery->post_count;


	// actual query, with limits, etc.
	wp_reset_query();
	$range = isset($data['range']) ? (int)$data['range'] : DEFAULT_GETPOSTS_RANGE;
	$offset = isset($data['offset']) ? (int)$data['offset'] : DEFAULT_GETPOSTS_OFFSET;
	$post_type =  isset($data['post_type']) ? $data['post_type'] : DEFAULT_GETPOSTS_POSTTYPE;
	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => $range,
		'post_status' => 'publish',
		'orderby' => 'post_date', 'order' => 'DESC', 'offset' => $offset
	);
	$specificCategory = isset($data['category']) ? $data['category'] : false;
	$specificTag = isset($data['tag']) ? $data['tag'] : false;
	$termData = null; // default
	if ($specificCategory) {
		$args['category_name'] = $specificCategory;
		$termData = get_term_by('slug', $specificCategory, 'category');
		$taxonomyIDString = 'category_' . $termData->term_id; // to find $featuredArticles
	} else if ($specificTag) {
		$args['tag'] = $specificTag;
		// die("\$specificTag==$specificTag");
		// var_dump($args);
		// $args['tag__in'] = array($specificTag);
		// $args['tax_query'] = array(
		//
		// 		'taxonomy' => 'post_tag',
		// 		'field'    => 'slug',
		// 		'terms'    => array($specificTag),
		// );
		// var_dump($args);
		$termData = get_term_by('slug', $specificTag, 'post_tag');

		$taxonomyIDString = 'post_tag_' . $termData->term_id; // to find $featuredArticles

	}
	if ($termData === null) {
		$termData = new stdClass;
		$termData->exists = null;
	}

	// $specificPostName = isset($data['name']) ? $data['name'] : false;
	// if ($specificPostName) $args['name'] = $specificPostName;// we can use this to call a specific post by name

	$specificPageName = isset($data['pagename']) ? $data['pagename'] : false;
	if ($specificPageName) $args['pagename'] = $specificPageName; // we can use this to call a specific post by name

	// we assume that if there is only one slug, that it is a page, not a post. but in the case of there being only 1 slug, it might be a post.
	// also, if there is pagename, then the post_type shoulr be page, not post.
	// return $args;

	$specificPostID = isset($data['post_id']) ? $data['post_id'] : false;
	if ($specificPostID) $args['p'] = $specificPostID; // we can use this to call a specific post by id

	$postsQuery = query_posts($args);
	// query_posts( 'tag=aging' );
	// var_dump($postsQuery);
	// $postsQuery = new WP_Query( $args );
	//  = new WP_Query( 'tag=travel' );
	// var_dump($postsQuery);
	// var_dump($postsQuery);
	//try using WP_QUery instead of quer_posts ... maybe then we will have a post_count possibility. also ... consider doing two queries, one without limit / offset, amd the second, with. don't loop through first, do loop through second.


	$resultSetData = new stdClass;
	if (isset($data['tag'])) {
		$resultSetData->tag = $data['tag'];
	}
	$resultSetData->publish = $somePostCount;
	$resultSetData->category = $specificCategory;
	// ...
	// $resultSetData = wp_count_posts();//{publish:publishNumber} other stuff not req'd
	// $wp_query->found_posts;
	// return $resultSetData;
	//

	$posts = array();
	$i = 0;
	while (have_posts()) : the_post();
		global $post;
		$somePost = new stdClass;
		$somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
		$somePost->post_content = apply_filters('the_content', get_the_content());
		$somePost->id = get_the_ID();
		//zmExcerpt($whichPostID=false, $limit=100, $stripShortCodes=true, $displayReadMoreLink=true, $readMoreText=false, $beforeMarkup="<p>", $afterMarkup="</p>", $ellipsis = " ...")
		$somePost->excerpt = zmExcerpt($somePost->id, 100, true, false, false, "", "", "...");
		$somePost->post_date = $post->post_date;
		$somePost->author = get_the_author();
		$somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id)); // gives us a relative url!
		//$somePost->post_slug = $post->post_name;
		$somePost->post_slug = basename(get_permalink());
		$somePost->region = get_field('post_region');

		$someCat = get_the_category();
		if (count($someCat) > 0) {
			$someMinimalCats = array();
			for ($j = 0; $j < count($someCat); $j++) {
				$someMinimalCatObj = new stdClass;
				$someMinimalCatObj->slug = $someCat[$j]->slug;
				$someMinimalCatObj->term_id = $someCat[$j]->term_id;
				$someMinimalCatObj->name = $someCat[$j]->name;
				array_push($someMinimalCats, $someMinimalCatObj);
			}
			$somePost->cats = $someMinimalCats;
		}
		$someThumbnailURLs = array();
		if (has_post_thumbnail()) {
			$post_thumbnail_id = get_post_thumbnail_id($somePost->id); // is $post_id defined at this point?
			$someThumbnail = (object) array(
				'thumbnail' => wp_get_attachment_image_src($post_thumbnail_id, $size = 'thumbnail', $icon = false),
				'medium'	=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'medium', $icon = false),
				'medium_large'	=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'medium_large', $icon = false),
				'large'		=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'large', $icon = false),
				'full'		=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'full', $icon = false),
				'category-thumb_300' => wp_get_attachment_image_src($post_thumbnail_id, $size = 'category-thumb_300', $icon = false)
			);
			array_push($someThumbnailURLs, $someThumbnail);
		}
		$somePost->thumbnails = $someThumbnailURLs; //
		array_push($posts, $somePost);
		$i++;
		if ($i >= $range) {
			break;
		}
	endwhile;
	// ok ... here's the deal schlemele ...
	// instead of returning an array of items, we return an object that has that array inside it, and also other properties, like deails about the resultset
	// ... filters, range, offset, totalitems (in filtered result set)
	// if there is only 1 item? do we need to provide a resultset? and additional data ... let's put that off for now ...
	// ... but ... we also need to specify a limit. if we impose a limit, to the query itself, we will never get more than that total number specified in the query.
	// so instead, we need to get the total ... the -1 limit, and then, use code to limit the resultset. use: $i++; if ($i >= $range) break;

	if ($specificPageName) {
		//return 'count($posts)==' . count($posts);
		if (count($posts) == 0) {
			// invoke the not found, attempt redirectToClosestMatch
			$result = new stdClass;
			$result->notFound = true;
			// $specificPostName or $specificPageName
			// $someSlug =
			$result->redirectLocation = guess_404_permalink($specificPageName); // something, or false
			// attempt redirection.
			return $result;
		} else {
			$result = $posts[0];

			$ads = new stdClass;
			$ads->leaderboard = getPostAd($result->id, 'ez2_ad_leaderboard');
			$ads->bigbox = getPostAd($result->id, 'ez2_ad_bigbox');
			$ads->sponsored_one = getPostAd($result->id, 'ez2_ad_sponsored_one');
			$ads->sponsored_two = getPostAd($result->id, 'ez2_ad_sponsored_two');
			$ads->interstitial = getPostAd($result->id, 'ez2_ad_sponsored_interstitial');
			$ads->wallpaper = getPostAd($result->id, 'ez2_ad_wallpaper');
			$ads->mobileInterstitial = getPostAd($result->id, 'ez2_ad_mobile_interstitial'); // this works on the home page, but does not on the post pages.
			$ads->mobileInterstitial2 = getPostAd($result->id, 'ez2_ad_mobile_interstitial_2');
			$ads->ez2_ad_desktop_interstitial = getPostAd($result->id, 'ez2_ad_desktop_interstitial'); //
			$result->ads = $ads;
			// $result->test = "apples";
			return ($result);
		}
	} else {
		//$totalNumberOfPosts
		$result = new stdClass;
		$result->resultSet = $posts;
		$result->range = $range;
		$result->offset = $offset;
		$result->resultSetData = $resultSetData;
		return ($result);
	}
}

function getPage($data)
{
}


function getSlideshow($data)
{
	//return "hello. this is getSlideshow";//getSlideshow
	//initial query. there can be muiltiple slideshows. default is the slideshow on home.
	wp_reset_query();
	$somePosts = array();
	$args = array(
		'post_status' => 'publish'
	);
	$args['posts_per_page'] = 1;
	if (isset($data['slug']) && $data['slug'] != '') {
		$args['name'] = $data['slug']; //and so we wil be returning just one.
	} else {
		$args['pagename'] = 'home';
	}
	//so ... we get the home page, and then we need to get_field on the slides field, and get an array of ids? and then query each one of those ids, to get slideshow image, title and caption
	$initialSlideQuery = query_posts($args);
	while (have_posts()) : the_post();
		$somePost = new stdClass;
		$somePost->post_title = wp_specialchars_decode(get_the_title());
		// $somePost->post_content = apply_filters( 'the_content', get_the_content());
		$somePost->id = get_the_ID();
		$somePost->permalink = get_permalink($somePost->id);
		$someSlides = get_field('slides', $somePost->id);
		$slides = array();
		//we don't want the whole post object, just the required fields == lighter json load
		foreach ($someSlides as $someSlide) {
			$slide = new stdClass;
			$slide->ID = $someSlide->ID;
			$slide->post_name = $someSlide->post_name;
			$slide->post_title = wp_specialchars_decode($someSlide->post_title, ENT_QUOTES);
			$slide->post_type = $someSlide->post_type;
			$slide->permalink = str_replace(home_url(), "", get_permalink($slide->ID)); // gives us a relative url!
			$slide->slideshow_image = get_field('slideshow_image', $someSlide->ID);
			$slide->slideshow_caption = get_field('slideshow_caption', $someSlide->ID);
			array_push($slides, $slide);
		}
		$somePost->slides = $slides;
		array_push($somePosts, $somePost); //even though we are taking only 1, we will get the 0th one from the array.
	endwhile;
	return $somePosts[0];
}


// utility functions
function zmExcerpt($whichPostID = false, $limit = 100, $stripShortCodes = true, $displayReadMoreLink = true, $readMoreText = false, $beforeMarkup = "<p>", $afterMarkup = "</p>", $ellipsis = " ...")
{
	$limit = intval($limit); //in case we get sent a string
	if ($whichPostID) {
		$somePostID = $whichPostID;
		$somePost = get_post($whichPostID);
	} else {
		//if we aren't passed a post id, use global post by default
		global $post;
		$somePost = $post;
		$somePostID = $somePost->ID;
	}
	//$someContent = "#... zmExcerpt ... #somePostID=={$somePostID}";
	$someContent = "";
	//if($post->post_excerpt) {
	if (has_excerpt($somePostID)) {
		//echo("has_excerpt");
		//$content = strip_tags($post->post_excerpt);
		if ($stripShortCodes) {
			$someContent .= strip_shortcodes(strip_tags($somePost->post_excerpt));
		} else {
			$someContent .= strip_tags($somePost->post_excerpt);
		}
		//$content = strip_tags(strip_shortcodes( get_the_excerpt());
	} else {
		//echo("no has_excerpt");
		if ($stripShortCodes) {
			//echo("get_the_content({$somePostID})==".get_the_content($somePostID) . "...;");//why does this appear to return nothing?
			//echo();
			$someContent .= strip_shortcodes(strip_tags($somePost->post_content));
		} else {
			$someContent .= strip_tags($somePost->post_content);
		}
	}
	// Find the last space (between words we're assuming) after the max length.
	if (strlen($someContent) > $limit) {
		$last_space = strrpos(substr(strip_tags($someContent), 0, $limit), ' ');
		// Trim
		$trimmed_text = substr(strip_tags($someContent), 0, $last_space);
	} else {
		$trimmed_text = $someContent;
	}
	if (!$readMoreText) {
		$readMoreText = "Read More &#187;";
	}
	if ($displayReadMoreLink) {
		return $beforeMarkup . $trimmed_text . $ellipsis .  '<a class="sr_readmore readmore" href="' . esc_url(get_permalink($somePost->ID)) . '" title="Read ' . get_the_title($somePost->ID) . '">' . $readMoreText . '</a>' . $afterMarkup;
	} else {
		return $beforeMarkup . $trimmed_text . $ellipsis . $afterMarkup;
	}
}



add_filter('acf/fields/relationship/query/name=todays_featured', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=friends_of_the_new_classical', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=featured_concerts', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=slides', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=community_spotlights', 'zm_relationship_options_filter', 10, 3);
//ez2_hero_posts
add_filter('acf/fields/relationship/query/name=ez2_hero_posts', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=ez2_featured_four', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=ez2_single_featured', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=ez2_trending_posts', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=ez2_single_featured_inset', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=ez2_hot_topics', 'zm_relationship_options_filter', 10, 3);
add_filter('acf/fields/relationship/query/name=ez2_first_featured_bin', 'zm_relationship_options_filter', 10, 3);


add_filter('acf/fields/relationship/query/name=ez2_featured_category_or_tag_items', 'zm_relationship_restrictToTaxonomyTerm_options_filter', 10, 3);
//ez2_featured_category_or_tag_items Relationship

function zm_relationship_options_filter($options, $field, $the_post)
{
	$options['post_status'] = array('publish');

	return $options;
}
function zm_relationship_restrictToTaxonomyTerm_options_filter($options, $field, $the_post)
{
	// echo('options:');
	// var_dump($options);

	$options['post_status'] = array('publish');
	return $options;
}
/**
 * Attempts to guess the correct URL based on query vars
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return false|string The correct URL if one is found. False on failure.
 */
function guess_404_permalink($someSlug)
{
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

/*
 * zm_getFeaturedTags returns an array of tags.
 * use like this:
*/
function  zm_getFeaturedTags()
{
	$someHomePage = get_page_by_path('home');
	$someFeaturedTags = get_field('ez2_featuredtags', $someHomePage->ID);
	return $someFeaturedTags;
}


function zm_getAPostsTagsInSetOfTags($whichPostID = null, $someFeaturedTags = [])
{
	if (!is_int($whichPostID)) {
		return false;
	}
	$someTagsThatPostHas = get_the_tags($whichPostID);
	$someFoundTags = array();
	if (is_array($someTagsThatPostHas)) {
		foreach ($someTagsThatPostHas as $tag) {
			$someTagObj = new stdClass;
			$someTagObj->permalink = str_replace(home_url(), "", get_tag_link($tag->term_id));
			$someTagObj->term_id = $tag->term_id;
			$someTagObj->name = $tag->name;
			array_push($someFoundTags, $someTagObj);
		}
	}

	if (count($someFoundTags) > 0) {
		$match = -1;
		for ($i = 0; $i < count($someFoundTags); $i++) {
			$someTagID = $someFoundTags[$i]->term_id;
			if (array_search($someTagID, $someFeaturedTags) !== false) {
				$match = $i;
			}
		}
		if ($match !== -1) {
			// then it is a position, and we then want to snip it out of its current position, and unshift it into the beginning at the 0th position.
			$someFeaturedTag = array_slice($someFoundTags, $match, 1); // pluck it out
			array_unshift($someFoundTags, $someFeaturedTag[0]); // and stick it at the front
		}
	}
	return $someFoundTags;
}

function zm_gettags_featuringFeatureTags($whichPostID = null)
{
	if (!is_int($whichPostID)) {
		return false;
	}
	// return the tags, but adjust the order of the tags so that the first one, is a featured one, if the post has any

	$someHomePage = get_page_by_path('home');

	$msg = "";

	$someFeaturedTags = get_field('ez2_featuredtags', $someHomePage->ID);

	$someTagsThatPostHas = get_the_tags($whichPostID);

	$someFoundTags = array();

	if (is_array($someTagsThatPostHas)) {
		foreach ($someTagsThatPostHas as $tag) {
			$someTagObj = new stdClass;
			$someTagObj->permalink = str_replace(home_url(), "", get_tag_link($tag->term_id));
			$someTagObj->term_id = $tag->term_id;
			$someTagObj->name = $tag->name;
			array_push($someFoundTags, $someTagObj);
		}
	}

	// I am a stupid meaningless comment. hear me roar.
	if (count($someFoundTags) > 0) {
		$match = -1;
		for ($i = 0; $i < count($someFoundTags); $i++) {
			$someTagID = $someFoundTags[$i]->term_id;
			if (array_search($someTagID, $someFeaturedTags) !== false) {
				$match = $i;
			}
		}
		if ($match !== -1) {
			// then it is a position, and we then want to snip it out of its current position, and unshift it into the beginning at the 0th position.
			$someFeaturedTag = array_slice($someFoundTags, $match, 1); // pluck it out
			array_unshift($someFoundTags, $someFeaturedTag[0]); // and stick it at the front
		}
	}
	return $someFoundTags;
}

function compareEffectiveDates($a, $b)
{
	return strcmp($a->effectiveDate, $b->effectiveDate);
}

function remove_max_srcset_image_width($max_width)
{
	$max_width = 2000;
	return $max_width;
}
add_filter('max_srcset_image_width', 'remove_max_srcset_image_width');

function ez_responsiveimage_sources($sources, $size_array, $image_src, $image_meta, $attachment_id)
{
	// return $sources;
	// return S3Substitute($sources);
	// return httpToHttpsImages($sources);
	//
	return localHostToEZImages($sources);
}
// // // //
add_filter('wp_calculate_image_srcset', 'ez_responsiveimage_sources', 10, 5); // either this is not being called, or it is not working. which one is it?
// 
add_filter('wp_get_attachment_image_sizes', 'ez_responsiveimage_sources', 10, 5);
//


// add some wp-rest-api-cache stuff.
add_filter('rest_cache_headers', function ($headers) {
	$headers['Cache-Control'] = 'public, max-age=3600';
	return $headers;
});



add_action('publish_post', 'zm_clearall_w3totalcache');

function zm_clearall_w3totalcache($post_id)
{
	// die('zm_clearall_w3totalcache here');
	// w3tc_flush_all
	if (function_exists('w3tc_flush_all')) {
		// die('there is a w3tc_flush_all :)');
		w3tc_flush_all();
	} else {
		// die('there is no w3tc_flush_all :()');
	}
}

function correctUrlPrefixes()
{
	/*
	$output = is_ssl() ? preg_replace( "^http:", "https:", $imageSource ) : $imageSource ;
	echo $output;

	apply_filters( 'rest_pre_echo_response', array $result, WP_REST_Server $this, WP_REST_Request $request )
	*/
}
function set_headless_preview_link($link)
{
	if (WP_DEBUG === false) {
		return site_url()
			. '/' .
			'preview/'
			. get_the_ID() . '/';
		// . wp_create_nonce( 'wp_rest' );
	}
}
add_filter('preview_post_link', 'set_headless_preview_link');
