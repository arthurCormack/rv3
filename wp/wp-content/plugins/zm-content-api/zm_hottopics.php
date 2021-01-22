<?php

function zm_hottopics() {
  //Get the page that has the title "Home"
  $homePage = get_page_by_path( 'Home' );
  $someHotTopics = get_field('ez2_hot_topics', $homePage->ID);
  // return $someHotTopics;
  $someFeaturedStuff = array();
  foreach($someHotTopics as $someFeaturedThingID) {
    $someObj = new stdClass;

    $tag = get_tag($someFeaturedThingID);

    $theTagTitle = $tag->name;
    $theTagTitle = strtolower($theTagTitle);
    $theTagTitle = ucwords($theTagTitle);
    $someObj->tag_name = $theTagTitle;

    $someObj->tag_permalink = str_replace(home_url(), "", get_tag_link($someFeaturedThingID));
    // $someObj->tag_permalink = get_tag_link($someFeaturedThingID);

    // $thumb = get_field('ez2_tag_image', $tag);

    $thumb = get_field('ez2_tag_image', $tag);

    $someObj->tag_thumb = S3Substitute($thumb['sizes']['teaser_square']);


    // array_push($someFeaturedStuff, $someObj);
    array_push($someFeaturedStuff, $someObj);
  }
  return $someFeaturedStuff;

}
