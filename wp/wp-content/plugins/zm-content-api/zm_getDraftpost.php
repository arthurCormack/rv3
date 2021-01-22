<?php

function zm_getDraftpost( $data ) {
	//
	$somePostID = $data['id'];
	$args = array( 'post_type' => 'post', 'post_status' => array( 'draft', 'auto-draft'), 'p' => $somePostID);
	// wp_reset_query();
	$somePosts = Array();
	$postsQuery = query_posts( $args );
	while(have_posts()) : the_post();
		global $post;
		$somePost = new stdClass;
		$somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
		$somePost->post_content = apply_filters( 'the_content', get_the_content());
		$somePost->post_date = $post->post_date;
		$somePost->author = get_the_author();
		$somePost->id = $somePostID;
		$somePost->actualID = get_the_ID();
		$somePost->post_status = get_post_status();
		$post_category = get_the_category($somePostID);
		$somePost->cat_name = $post_category[0]->cat_name;
		$someThumbnailURLs = Array();
		if ( has_post_thumbnail() ) {
			$post_thumbnail_id = get_post_thumbnail_id( $somePostID );// is $post_id defined at this point?
			$someThumbnail = (object) Array(
				'thumbnail' => wp_get_attachment_image_src( $post_thumbnail_id, $size = 'thumbnail', $icon = false ),
				'medium'	=> wp_get_attachment_image_src( $post_thumbnail_id, $size = 'medium', $icon = false ),
				'large'		=> wp_get_attachment_image_src( $post_thumbnail_id, $size = 'large', $icon = false ),
				'category-thumb_300' => wp_get_attachment_image_src( $post_thumbnail_id, $size = 'category-thumb_300', $icon = false )
			);
			array_push($someThumbnailURLs, $someThumbnail);
		}
		$somePost->thumbnails = $someThumbnailURLs;//
		array_push($somePosts, $somePost);
	endwhile;
	return $somePosts[0];
}
