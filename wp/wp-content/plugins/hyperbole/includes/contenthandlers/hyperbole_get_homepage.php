<?php

function hyperbole_get_homepage() {

  $somePage = get_page_by_path('home');
  if ($somePage=== null) {
    return false;
  }
  // but we want to consider more than returning just a page.
  // the homepage has a special assortment of data
  // the data that is essential for a serverside render of the home page
  // other, non-1st-load-essential portions of the homepage content can be loaded by subsequent api calls from the client.
  // we need to know, what is essential before hand. This has to be opinionated.
  // let's say that there are fields that pertain to meta keywords, ogtags, and the Name, description, etc, and that All that stuff is content-editor controlled
  // and then furthermore, let's say that there are 5 pieces of content on the home page: The Featured Four, and optionally, a Hero Spot. 
  
  // no - we don't return somepage! for a route handler ... that lives at a specific url, always 
  // always a resultItem
  // with a     .permalink
  // or a resultItemSet ...
  // at a particular route, a respponse for that route is either a responseItem, or a responseItemSet.
  
  return $somePage;// this is just a start. probably, we will want to return a bunch of other things about the page, such as ads, and so on.
  
	// $somePageSlug = $data['pageslug'];
	// $args = array(
	// 	'post_type' => 'page',
	// 	'name' => $slug,
	// 	'post_status' => 'publish',
	// 	'orderby' => 'post_date', 'order' => 'DESC',
	// );
	// wp_reset_query();
	// $pageQuery = query_posts($args);

	// $posts = array();
	
	// while (have_posts()) : the_post();
	// 	global $post;
	// 	$somePost = new stdClass;

	// 	$somePost->post_content = apply_filters('the_content', get_the_content());
	// 	$somePost->id = get_the_ID();
	// 	// $somePost->post_title = wp_specialchars_decode(get_the_title($somePost->id), ENT_QUOTES);
	// 	$somePost->post_title = wp_specialchars_decode($post->post_title, ENT_QUOTES);
	// 	$somePost->post_date = $post->post_date;
	// 	$somePost->author = get_the_author();
	// 	$somePost->post_slug = $post->post_name;

	// 	// need to add some useful og stuff, like image, and excerpt.
	// 	$somePost->excerpt = zmExcerpt($somePost->id, 100, true, false, false, "", "", "...");


	// 	$someThumbnailURLs = array();
	// 	if (has_post_thumbnail()) {
	// 		$post_thumbnail_id = get_post_thumbnail_id($somePost->id); // is $post_id defined at this point?
	// 		$someThumbnail = (object) array(
	// 			'thumbnail' => wp_get_attachment_image_src($post_thumbnail_id, $size = 'thumbnail', $icon = false),
	// 			'medium'	=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'medium', $icon = false),
	// 			'medium_large'	=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'medium_large', $icon = false),
	// 			'large'		=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'large', $icon = false),
	// 			'full'		=> wp_get_attachment_image_src($post_thumbnail_id, $size = 'full', $icon = false),
	// 			'category-thumb_300' => wp_get_attachment_image_src($post_thumbnail_id, $size = 'category-thumb_300', $icon = false)
	// 		);
	// 		array_push($someThumbnailURLs, $someThumbnail);
	// 	}
	// 	$somePost->thumbnails = $someThumbnailURLs; //

	// 	array_push($posts, $somePost);
	// 	$i++;

	// endwhile;

	// if (count($posts) == 0) {
	// 	// invoke the not found, attempt redirectToClosestMatch
	// 	$result = new stdClass;
	// 	$result->notFound = true;
	// 	// $specificPostName or $specificPageName
	// 	// $someSlug =
	// 	$result->redirectLocation = guess_404_permalink($specificPageName); // something, or false
	// 	// attempt redirection.
	// 	return $result;
	// } else {
	// 	$result = $posts[0];
	// 	$toggleAdButton = get_field('turn_off_ads', $result->id);
	// 	$result->adsTurnedOff = $toggleAdButton;
	// 	$ads = new stdClass;
	// 	$ads->leaderboard = getPostAd($result->id, 'ez2_ad_leaderboard');
	// 	$ads->bigbox = getPostAd($result->id, 'ez2_ad_bigbox');
	// 	$ads->sponsored_one = getPostAd($result->id, 'ez2_ad_sponsored_one');
	// 	$ads->sponsored_two = getPostAd($result->id, 'ez2_ad_sponsored_two');
	// 	$ads->interstitial = getPostAd($result->id, 'ez2_ad_sponsored_interstitial');
	// 	$ads->wallpaper = getPostAd($result->id, 'ez2_ad_wallpaper');
	// 	$ads->mobileInterstitial = getPostAd($result->id, 'ez2_ad_mobile_interstitial'); // this works on the home page, but does not on the post pages.
	// 	$ads->mobileInterstitial2 = getPostAd($result->id, 'ez2_ad_mobile_interstitial_2');
	// 	$ads->ez2_ad_desktop_interstitial = getPostAd($result->id, 'ez2_ad_desktop_interstitial'); //

	// 	if (!isset($toggleAdButton) || $toggleAdButton === false) {
	// 		$result->ads = $ads;
	// 	}
	// 	// $result->test = "apples";
	// 	return ($result);
	// }
	// // return $somePost;
}