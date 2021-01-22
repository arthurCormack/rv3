<?php

function zm_gettrendinglist() {
  //Get the page that has the title "Home"
  $homePage = get_page_by_path( 'Home' );
  $someTrendingList = get_field('ez2_trending_posts', $homePage->ID);
  // return $someTrendingList;
  $someTrendingStuff = array();
  foreach($someTrendingList as $someFeaturedThingID) {
    $someObj = new stdClass;
    $somePost = get_post($someFeaturedThingID);

    $someObj->post_title = $somePost->post_title;
    $someObj->post_url = str_replace(home_url(), "", get_permalink($someFeaturedThingID));
    // $someObj->post_url = get_permalink($someFeaturedThingID);

    // $urlSubstitutionForThumbnails = true;

    array_push($someTrendingStuff, $someObj);
  }
  return $someTrendingStuff; 
  //Get the post id of that page
  //Get field (get_field) of ez2_featured_four
}
