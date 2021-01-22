<?php

/**
* Plugin Name: ZM GoogleMaps API Key
* Plugin URI: none
* Description: Sets the GoogleMaps API KEy, so that it is in scope, and geolocation functions used by ACF can work properly.
* Version: 0.1
* Author: Arthur Cormack
* Author URI: none
* License: none
*/

 function my_acf_google_map_api( $api ){

	$api['key'] = 'AIzaSyDQGPNSkHdTESW_kimdoa4y504uO0GEgeM';

	return $api;

}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
