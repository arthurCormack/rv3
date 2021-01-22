<?php

function zm_getstart() {

  $homePage = get_page_by_path( 'Home' );

  $someObj = new stdClass;

  $someObj->quote = get_field('quote', $homePage->ID);

  // we need an api call to piggy back on, so that we can will wp_request data with stuff like ads, so that we can display the correct ads on the homepage.
  // and this looks like a goood candidate!

  $ads = new stdClass;
	$ads->leaderboard = getPostAd($homePage->ID, 'ez2_ad_leaderboard');
	$ads->bigbox = getPostAd($homePage->ID, 'ez2_ad_bigbox');
  $ads->a_bigboxOne = getPostAd($homePage->id, 'ez2_ad_bigbox');
  $ads->b_bigboxOne = getPostAd($homePage->id, 'ez2_ad_bigbox_b_one');
  $ads->c_bigboxOne = getPostAd($homePage->id, 'ez2_ad_bigbox_c_one');

	$ads->sponsored_one = getPostAd($homePage->ID, 'ez2_ad_sponsored_one');
	$ads->sponsored_two = getPostAd($homePage->ID, 'ez2_ad_sponsored_two');
	$ads->interstitial = getPostAd($homePage->ID, 'ez2_ad_sponsored_interstitial');
  $ads->mobileInterstitial2 = getPostAd($homePage->ID, 'ez2_ad_mobile_interstitial_2');

	$ads->wallpaper = getPostAd($homePage->ID, 'ez2_ad_wallpaper');
  // $ads->mobileInterstitial = getPostAd($homePage->ID, 'ez2_ad_mobile_interstitialGroup');
  $ads->mobileInterstitial = getPostAd($homePage->ID, 'ez2_ad_mobile_interstitial');

  $someObj->ads = $ads;

	// die(var_export($homePage, true));

	$someObj->ideacitystream = get_field('ideacity_livestream_embed_code', $homePage->ID);
	$someObj->ideacity_livestream_title = get_field('ideacity_livestream_title', $homePage->ID);
	$someObj->ideacity_livestream_paragraph = get_field('ideacity_livestream_paragraph', $homePage->ID);
  // so that is the base object ... now let's add to that all of the stuff that we will need to make the initiall load on the homepage. The goal here, is to reduce the homepage load on the server down to 1 api call only.
  // we'll alter all of the other dynamic sagas so that they only load on the client.
  $someObj->featuredFour = zm_getfeaturedfour();

  return $someObj;

}


function getSpecialRecentPostsForTag($taxSlug) {
  // easiest / best way to get post count for a term, is with get_term!
  $termData = get_term_by('slug', $taxSlug, 'post_tag');
  if ($termData === null) {
		$termData = new stdClass;
  }
  $taxonomyIDString = 'post_tag_' . $termData->term_id;
  $somePostCount = $termData->count;

	// actual query, with limits, etc.
	wp_reset_query();
	$range = 3;
	$offset = 0;
	$post_type = 'post';

	$args = array(
					'post_type' => $post_type,
					'posts_per_page' => $range,
					'post_status' => 'publish',
					'orderby' => 'post_date', 'order' => 'DESC', 'offset' => $offset, 'tag' => $taxSlug
				);

	$postsQuery = query_posts( $args );

  $resultSetData = new stdClass;
	$resultSetData->tag = $termData;
	$resultSetData->publish = $somePostCount;

	$posts = array();
	$i = 0;
	while(have_posts()) : the_post();
		global $post;
		$somePost = new stdClass;
		$somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
		$somePost->post_content = apply_filters( 'the_content', get_the_content());
		$somePost->id = get_the_ID();
		//zmExcerpt($whichPostID=false, $limit=100, $stripShortCodes=true, $displayReadMoreLink=true, $readMoreText=false, $beforeMarkup="<p>", $afterMarkup="</p>", $ellipsis = " ...")
		$somePost->excerpt = zmExcerpt($somePost->id, 100, true, false, false, "", "", "...");
		$somePost->post_date = $post->post_date;
		$somePost->author = get_the_author();
		$somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));// gives us a relative url!
		//$somePost->post_slug = $post->post_name;
		$somePost->post_slug = basename(get_permalink());
		$somePost->region = get_field('post_region');

		$someCat = get_the_category();
		if (count($someCat) > 0) {
			$someMinimalCats = array();
			for ($j=0;$j<count($someCat);$j++) {
				$someMinimalCatObj = new stdClass;
				$someMinimalCatObj->slug = $someCat[$j]->slug;
				$someMinimalCatObj->term_id = $someCat[$j]->term_id;
				$someMinimalCatObj->name = $someCat[$j]->name;
				array_push($someMinimalCats, $someMinimalCatObj);
			}
			$somePost->cats = $someMinimalCats;
		}
		$someThumbnailURLs = Array();
		if ( has_post_thumbnail() ) {
			$post_thumbnail_id = get_post_thumbnail_id( $somePost->id );// is $post_id defined at this point?
			$someThumbnail = (object) Array(
				'thumbnail' => wp_get_attachment_image_src( $post_thumbnail_id, $size = 'thumbnail', $icon = false ),
				'medium'	=> wp_get_attachment_image_src( $post_thumbnail_id, $size = 'medium', $icon = false ),
				'medium_large'	=> wp_get_attachment_image_src( $post_thumbnail_id, $size = 'medium_large', $icon = false ),
				'large'		=> wp_get_attachment_image_src( $post_thumbnail_id, $size = 'large', $icon = false ),
				'full'		=> wp_get_attachment_image_src( $post_thumbnail_id, $size = 'full', $icon = false ),
				'category-thumb_300' => wp_get_attachment_image_src( $post_thumbnail_id, $size = 'category-thumb_300', $icon = false )
			);
			array_push($someThumbnailURLs, $someThumbnail);
		}
		$somePost->thumbnails = $someThumbnailURLs;//
		array_push($posts, $somePost);
		$i++;
		if ($i >= $range) {
			break;
		}
	endwhile;

	$result = new stdClass;
	$result->resultSet = $posts;
	$result->range = $range;
	$result->offset = $offset;
	$result->resultSetData = $resultSetData;
	return ($result);

}



function zm_getFeaturedPopularTrending() {
  // specialCategoriesColumn
  // HomeTile
  // Trending Column
  //
  $homePage = get_page_by_path( 'Home' );

  $specialCategories = ['zoomer-daily', 'politics-policy', 'arts-and-entertainment', 'stars-and-royals', 'sex-love-relating'];

  $featuredPopularTrending = new stdClass;

  // $featuredPopularTrending->specialCategories = zm_gettrendinglist();
  // i don't think this is right ...
  // it looks like the SpecialCategoriesColumn is calling APICALLURL_GETSPECIALCATRECENTPOSTS,
  // which is really just going to getposts, with ?tag=whatever
  // so ... let's do one

  $featuredPopularTrending->specialCategories = [];
  foreach($specialCategories as $k) {
    $featuredPopularTrending->specialCategories[$k] = getSpecialRecentPostsForTag($k);// 2 wp queries: a tasonomy quary, and a posts query. making this have total of 10 wp queries ...
  }



  $featuredPopularTrending->homeTile = zm_gethometile();
  $featuredPopularTrending->trendingColumn = zm_gettrendinglist();// ok

  return $featuredPopularTrending;
}
