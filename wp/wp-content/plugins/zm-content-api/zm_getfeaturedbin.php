<?php

function zm_getfeaturedbin() {
  //Get the page that has the title "Home"
  $homePage = get_page_by_path( 'Home' );
  $someFeaturedFour = get_field('ez2_first_featured_bin', $homePage->ID);
  // return $someFeaturedFour;
  $someFeaturedStuff = array();
  $someFeaturedTags = zm_getFeaturedTags();//outside of loop, so only called once!
  foreach($someFeaturedFour as $someFeaturedThingID) {
    $someObj = new stdClass;
    $somePost = get_post($someFeaturedThingID);


    //CATEGORY


    // $someObj->post = get_post($someFeaturedThingID);
    // $someObj->post_title = get_the_title($someFeaturedThingID);
    // $someObj->post_content = apply_filters( 'the_content', get_the_content($someFeaturedThingID));
    $someObj->post_title = $somePost->post_title;
    $someObj->id = $someFeaturedThingID;
    // $someObj->post_url = get_permalink($someFeaturedThingID);
    $someObj->post_url = str_replace(home_url(), "", get_permalink($someFeaturedThingID));
    // $someObj->post_content = apply_filters( 'the_content', $somePost->post_content);


    if ( has_post_thumbnail($someFeaturedThingID) ) {

      $post_thumbnail_id = get_post_thumbnail_id( $someFeaturedThingID );
  		$someThumbObj = zm_get_attachment_image($post_thumbnail_id, 'teaser_square');
      // $someThumbObj = zm_get_attachment_image($someFeaturedThingID, 'teaser_square');// we need the attachement id, not the post id!
      $theThumb = get_the_post_thumbnail_url($someFeaturedThingID, 'teaser_square');
      //$theThumb = $someThumbObj;
    } else {
      // $theThumb = "huh?!";
    }

    // $someObj->post_thumb = S3Substitute($theThumb);
    //  $someObj->post_thumb = $theThumb;
    //$someObj->thumbObj = $someThumbObj;
    if ($someThumbObj !== null) {
      $someObj->post_thumb = $someThumbObj->src;
    } else {
      $someObj->post_thumb = null;
    }


    $catObj = get_the_category($someFeaturedThingID);
    $someObj->post_cat = $catObj[0]->name;
    $someObj->post_cat_link = $catObj[0]->slug;

    // $someTags = zm_gettags_featuringFeatureTags($someFeaturedThingID);
    $someTags = zm_getAPostsTagsInSetOfTags($someFeaturedThingID, $someFeaturedTags);

    if (count($someTags) > 0) {
      $someObj->post_cat = $someTags[0]->name;
      $someObj->post_cat_link = $someTags[0]->permalink;
    }

    array_push($someFeaturedStuff, $someObj);
  }
  return $someFeaturedStuff;
  //Get the post id of that page
  //Get field (get_field) of ez2_featured_four
}
