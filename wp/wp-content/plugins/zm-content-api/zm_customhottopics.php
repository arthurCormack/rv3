<?php
function zm_customhottopics() {
//ez2_custom_zoomer_insider_topics
$homePage = get_page_by_path( 'front-page-secondary-buckets' );
$someCustomTopics = get_field('ez2_custom_zoomer_insider_topics', $homePage->ID);
$someFeaturedStuff = array();
foreach($someCustomTopics as $someCustomTopic) {
  // $someObj = new stdClass;
  // $someObj->post_title = $someCustomTopic->title;
  // $somePost = get_post($someCustomTopic);
  //
  // // $someObj->post = $someCustomTopic;
  // $someObj->post_title = $someCustomTopic->title;
  //
  // array_push($someFeaturedStuff, $someObj);

  array_push($someFeaturedStuff, $someCustomTopic);
}
return $someFeaturedStuff;
//Get the post id of that page
//Get field (get_field) of ez2_featured_four
}
