<?php

function zm_getrecentposts() {

  $args = array(
    'post_type' => 'post',
    'posts_per_page' => 26,
    'post_status' => 'publish',
  );

	$recent_posts = wp_get_recent_posts( $args );

  $someFeaturedStuff = array();

	foreach( $recent_posts as $someRecentPost ){

    $someObj = new stdClass;
    // $somePost = get_post($someFeaturedThingID);

    $someFeaturedThingID = $someRecentPost['ID'];
    $someObj->post_title = $someRecentPost['post_title'];
    $someObj->post_permalink = str_replace(home_url(), "", get_permalink($someRecentPost['ID']));
    // $someObj->post_permalink = get_permalink($someRecentPost['ID']);


    $theThumb = null;
    $theHugeThumb = null;
            // $someThumbnailURLs = Array();
        		if ( has_post_thumbnail($someFeaturedThingID) ) {
        			// $post_thumbnail_id = get_post_thumbnail_id( $someFeaturedThingID );
        			// $someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'teaser_square');
              $theThumb = get_the_post_thumbnail_url($someFeaturedThingID, 'teaser_square');
              $theHugeThumb = get_the_post_thumbnail_url($someFeaturedThingID, 'huge');
        			// array_push($someThumbnailURLs, $someThumbnail);
        		}

            // $fixedThumb = str_replace('ez2.local', 'everythingzoomer.com', $someThumbnailURLs);

        		$someObj->post_thumb = S3Substitute($theThumb);
        		$someObj->post_thumb_huge = S3Substitute($theHugeThumb);

            $catObj = get_the_category($someFeaturedThingID);
            $someObj->post_cat = $catObj[0]->name;
            $someObj->post_cat_link = $catObj[0]->slug;

    array_push($someFeaturedStuff, $someObj);
	}

  return $someFeaturedStuff;
	// wp_reset_query();

}
