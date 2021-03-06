<?php

function zm_gethomehero() {
  //Get the page that has the title "Home"
  $homePage = get_page_by_path( 'Home' );
  $someHeroPost = get_field('ez2_hero_posts', $homePage->ID);
  // return $someHeroPost;
  $someFeaturedHeros = array();
  $someFeaturedTags = zm_getFeaturedTags();//outside of loop, so only called once!
  foreach($someHeroPost as $someFeaturedThingID) {
    $someObj = new stdClass;
    $somePost = get_post($someFeaturedThingID);

    $someObj->post_title = $somePost->post_title;
    $someObj->post_url = str_replace(home_url(), "", get_permalink($someFeaturedThingID));
    // $someObj->post_url = get_permalink($someFeaturedThingID);


    $urlSubstitutionForThumbnails = true;
    $someThumbnailURLs = Array();
		if ( has_post_thumbnail($someFeaturedThingID) ) {
			$post_thumbnail_id = get_post_thumbnail_id( $someFeaturedThingID );
			$someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'huge');
			if (isset($urlSubstitutionForThumbnails) && $urlSubstitutionForThumbnails !== false) {
				$someThumbnailJSON = json_encode($someThumbnail);
				$someAlteredThumbnailJSON = str_replace('ez2.local', 'everythingzoomer.com', $someThumbnailJSON);
				$someThumbnail = json_decode($someAlteredThumbnailJSON);
				// also fix the content
				$somePost->post_content = str_replace('ez2.local', 'everythingzoomer.com', $somePost->post_content);
			}
			array_push($someThumbnailURLs, $someThumbnail);
		}

    // $fixedThumb = str_replace('ez2.local', 'everythingzoomer.com', $someThumbnailURLs);

		// $someObj->post_thumb = S3Substitute($someThumbnailURLs[0]->src);
    $someObj->post_thumb = $someThumbnailURLs[0];
    $catObj = get_the_category($someFeaturedThingID);
    $someObj->post_cat = $catObj[0]->name;
    $someObj->post_cat_link = $catObj[0]->slug;

    // $someTags = zm_gettags_featuringFeatureTags($someFeaturedThingID);
    $someTags = zm_getAPostsTagsInSetOfTags($someFeaturedThingID, $someFeaturedTags);
    if (count($someTags) > 0) {
      $someObj->post_cat = $someTags[0]->name;
      $someObj->post_cat_link = $someTags[0]->permalink;
    }


    array_push($someFeaturedHeros, $someObj);
  }
  return $someFeaturedHeros;
  //Get the post id of that page
  //Get field (get_field) of ez2_featured_four
}
