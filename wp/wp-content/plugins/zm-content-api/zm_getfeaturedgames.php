<?php

function zm_getfeaturedgames() {

  $args = array (
  'post_type' => array ('featured_games'),
  'orderby' => array( 'menu_order' => 'ASC'),
  'posts_per_page' => -1
  );

	$recent_posts = wp_get_recent_posts( $args );

  $someFeaturedStuff = array();

	foreach( $recent_posts as $someRecentPost ){

    $someObj = new stdClass;
    // $somePost = get_post($someFeaturedThingID);

    $someFeaturedThingID = $someRecentPost['ID'];
    $someObj->post_title = $someRecentPost['post_title'];
    $someObj->post_slug = $someRecentPost['post_name'];
    $someObj->post_permalink = str_replace(home_url(), "", get_permalink($someRecentPost['ID']));
    // $someObj->post_permalink = get_permalink($someRecentPost['ID']);

//     $tokens = explode('/', $url);
// echo $tokens[sizeof($tokens)-2];


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
    $post_thumbnail_id = get_post_thumbnail_id($someFeaturedThingID );
    $someObj->post_thumb = zm_get_attachment_image( $post_thumbnail_id, 'post-thumbnail' );
		// $someObj->post_thumb = S3Substitute($theThumb);
		$someObj->post_thumb_huge = S3Substitute($theHugeThumb);
    $someObj->id = $someFeaturedThingID;
    $catObj = get_the_category($someFeaturedThingID);
    if ($catObj) {
      $someObj->post_cat = $catObj[0]->name;
      $someObj->post_cat_link = $catObj[0]->slug;

    }

    array_push($someFeaturedStuff, $someObj);
	}
  // return $someFeaturedStuff[0];
  $someFirstFeaturedThing = $someFeaturedStuff[0];
  $pageID = get_page_by_path('continue-to-games')->ID;
  $ads = new stdClass;
  $ads->leaderboard = getPostAd($pageID, 'ez2_ad_leaderboard');
  $ads->bigbox = getPostAd($pageID, 'ez2_ad_bigbox');
  $ads->sponsored_one = getPostAd($pageID, 'ez2_ad_sponsored_one');
  $ads->sponsored_two = getPostAd($pageID, 'ez2_ad_sponsored_two');
  $ads->interstitial = getPostAd($pageID, 'ez2_ad_sponsored_interstitial');
  $ads->wallpaper = getPostAd($pageID, 'ez2_ad_wallpaper');
  $ads->mobileInterstitial = getPostAd($pageID, 'ez2_ad_mobile_interstitial');// this works on the home page, but does not on the post pages.
  $ads->mobileInterstitial2 = getPostAd($pageID, 'ez2_ad_mobile_interstitial_2');
  $ads->ez2_ad_desktop_interstitial = getPostAd($pageID, 'ez2_ad_desktop_interstitial');//

  $response = new stdClass;
  $response->ads = $ads;
  $response->featured = $someFeaturedStuff;
  return $response;
  // return $someFeaturedStuff;
	// wp_reset_query();

}
function zm_getGamesScript() { 
  $gamesPageID = get_page_by_path('continue-to-games')->ID;
  $someShowsBannerPosts = get_field( 'quiz_code', $gamesPageID );
  return $someShowsBannerPosts;

}
