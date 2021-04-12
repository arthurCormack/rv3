<?php
function guess_404_permalink($someSlug) {
  // return 'guess_404_permalink';
	global $wpdb;
  if ( $someSlug ) {
    $where = $wpdb->prepare("post_name LIKE %s", $wpdb->esc_like( $someSlug ) . '%');
    $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $where AND post_status = 'publish' AND post_type IN ('post', 'page', 'zed_the_zoomer_book')");
    // $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $where AND post_status = 'publish' AND post_type = 'post'");
    if ( ! $post_id ) {
      return false;
    }
    $relativeRedirect  = str_replace(home_url(), "", get_permalink($post_id));// gives us a relative url!
    return rtrim($relativeRedirect, '/');// remove the trailing slash!
  }
  return false;
}