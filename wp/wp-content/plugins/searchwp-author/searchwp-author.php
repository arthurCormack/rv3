<?php
/*
	Plugin Name: searchwp author
	Plugin URI: http://arthurcormack.com/code/searchwp-author
	Description: Adds author to searchwp 
	Version: 0.1
	Author: Arthur Cormack
	Author URI: http://arthurcormack.com
*/



function my_searchwp_extra_metadata( $extra_meta, $post_being_indexed ) {

    // available author meta: http://codex.wordpress.org/Function_Reference/get_the_author_meta

    // retrieve the author's name(s)
    $author_nicename      = get_the_author_meta( 'user_nicename', $post_being_indexed->post_author );
    $author_display_name  = get_the_author_meta( 'display_name', $post_being_indexed->post_author );
    $author_nickname      = get_the_author_meta( 'nickname', $post_being_indexed->post_author );
    $author_first_name    = get_the_author_meta( 'first_name', $post_being_indexed->post_author );
    $author_last_name     = get_the_author_meta( 'last_name', $post_being_indexed->post_author );

    // grab the author bio
    $author_bio           = get_the_author_meta( 'description', $post_being_indexed->post_author );

    // index the author name and bio with each post
    $extra_meta['my_author_meta_nicename']     = $author_nicename;
    $extra_meta['my_author_meta_display_name'] = $author_display_name;
    $extra_meta['my_author_meta_nickname']     = $author_nickname;
    $extra_meta['my_author_meta_first_name']   = $author_first_name;
    $extra_meta['my_author_meta_last_name']    = $author_last_name;
    $extra_meta['my_author_meta_bio']          = $author_bio;

    return $extra_meta;
}
add_filter( 'searchwp_extra_metadata', 'my_searchwp_extra_metadata', 10, 2 );
