<?php

function zm_getmostpopular() {
  //Get the page that has the title "Home"
  $homePage = get_page_by_path( 'Home' );
  $someMostPopularPost = get_field('ez2_most_popular', $homePage->ID);
  // return $someMostPopularPost;
  $someFeaturedMostPopularPosts = array();
  foreach($someMostPopularPost as $someFeaturedThingID) {
    $someObj = new stdClass;
    $somePost = get_post($someFeaturedThingID);

    $someObj->post_title = $somePost->post_title;
    $someObj->post_url = str_replace(home_url(), "", get_permalink($someFeaturedThingID));
    // $someObj->post_url = get_permalink($someFeaturedThingID);




    // $someThumbnailURLs = Array();
    if ( has_post_thumbnail($someFeaturedThingID) ) {
      $theThumb = get_the_post_thumbnail_url($someFeaturedThingID, 'teaser_square');
    }

    // $fixedThumb = str_replace('ez2.local', 'everythingzoomer.com', $someThumbnailURLs);

    $someObj->post_thumb = $theThumb;

    $catObj = get_the_category($someFeaturedThingID);
    $someObj->post_cat = $catObj[0]->name;

    $someObj->post_cat_link = $catObj[0]->slug;

    array_push($someFeaturedMostPopularPosts, $someObj);
  }
  return $someFeaturedMostPopularPosts;
  //Get the post id of that page
  //Get field (get_field) of ez2_featured_four
}
