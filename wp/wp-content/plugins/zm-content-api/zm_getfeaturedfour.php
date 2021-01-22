<?php

function zm_getfeaturedfour() {
  //Get the page that has the title "Home"
  $homePage = get_page_by_path( 'Home' );
  $someFeaturedFour = get_field('ez2_featured_four', $homePage->ID);
  // return $someFeaturedFour;
  $someFeaturedStuff = array();
  // die(var_export($someFeaturedFour, true));
  $msg = "";
  $someFeaturedTags = zm_getFeaturedTags();//outside of loop, so only called once!
  foreach($someFeaturedFour as $someFeaturedThingID) {
    $someObj = new stdClass;
    $somePost = get_post($someFeaturedThingID);


    // $msg .= "<p>\$somePost:$someFeaturedThingID</p>\n";
    // $msg .= "<p>" . var_export($somePost, true) . "</p>\n";
    //CATEGORY


    $someObj->post_title = $somePost->post_title;
    // $someObj->post_url = get_permalink($someFeaturedThingID);
    $someObj->post_url = str_replace(home_url(), "", get_permalink($someFeaturedThingID));

    if ( has_post_thumbnail($someFeaturedThingID) ) {
      $theThumb = get_the_post_thumbnail_url($someFeaturedThingID, 'teaser_square');
    }

    $someObj->post_thumb = S3Substitute($theThumb);//?!

   
    $catObj = get_the_category($someFeaturedThingID);
    $someObj->post_cat = $catObj[0]->name;
    //str_replace(home_url(), "", get_tag_link($someTagsThatPostHas[$i]->term_id));
    $someObj->post_cat_link = $catObj[0]->slug;

    if ($somePost->post_type === 'zed_the_zoomer_book') {
      $someObj->post_cat = "Zed Book Club";
      $someObj->post_cat_link = "/zed-book-club";
    }

    // $someTags = zm_gettags_featuringFeatureTags($someFeaturedThingID);
    $someTags = zm_getAPostsTagsInSetOfTags($someFeaturedThingID, $someFeaturedTags);
    /*
    $somePostID = 11;// whatever post
    $someFeaturedTags = zm_getFeaturedTags();
 	  $someTagsThatAreFeatured = zm_getAPostsTagsInSetOfTags($somePostID, $someFeaturedTags);
    */
    if (count($someTags) > 0) {
      $someObj->post_cat = $someTags[0]->name;
      $someObj->post_cat_link = $someTags[0]->permalink;
    }

    if( $someTags ) {
      $someObj->tags = $someTags;
    } else {

    }

    array_push($someFeaturedStuff, $someObj);
  }
  // die($msg);

  return $someFeaturedStuff;

}
