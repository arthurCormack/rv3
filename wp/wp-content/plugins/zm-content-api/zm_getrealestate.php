<?php

function zm_getrealestate() {

//REAL ESTATE POSTS
	$args = array(
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'tag' => 'real-estate',
					'orderby' => 'post_date', 'order' => 'DESC'
				);

	$regularPosts = Array();

	$the_query = new WP_Query( $args );
	$result = new stdClass;


	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();
			global $post;
			$postID = get_the_ID();
			$tagID = get_tag($postID);

			$somePost = new stdClass;
			$somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
			$somePost->id = get_the_ID();
			$somePost->post_date = $post->post_date;
			$somePost->author = get_the_author();
			$somePost->tags = get_the_tags($postID);
			$somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));// gives us a relative url!
			$somePost->post_slug = basename(get_permalink());


				$theThumb = null;
				if ( has_post_thumbnail($somePost->id) ) {
					$theThumb = get_the_post_thumbnail_url($somePost->id, 'teaser_square');
				}

				if ($theThumb !== null) {
					$somePost->post_thumb = S3Substitute($theThumb);
				}


				$someThumbnailURLs = Array();
				$urlSubstitutionForThumbnails = true;
				if ( has_post_thumbnail() ) {
					$post_thumbnail_id = get_post_thumbnail_id( $somePost->id );
					$someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
					if (isset($urlSubstitutionForThumbnails) && $urlSubstitutionForThumbnails !== false) {
						$someThumbnailJSON = json_encode($someThumbnail);
						$someAlteredThumbnailJSON = str_replace('ez2.local', 'everythingzoomer.com', $someThumbnailJSON);
						$someThumbnail = json_decode($someAlteredThumbnailJSON);
						// also fix the content
						// $somePost->post_content = str_replace('ez2.local', 'everythingzoomer.com', $somePost->post_content);
					}
					array_push($someThumbnailURLs, $someThumbnail);
				}
				$somePost->thumbnails = $someThumbnailURLs;

			array_push($regularPosts, $somePost);

		endwhile;

		$result->resultSet = $regularPosts;

	endif;


//RESULT SET Data
$termData = get_term_by('slug', 'real-estate', 'post_tag');
$somePostCount = $termData->count;

$resultSetData = new stdClass;
$resultSetData->publish = $somePostCount;
$resultSetData->termData = $termData;

$taxonomyIDString = 'post_tag_' . $termData->term_id;// to find $featuredArticles

$theBanner = '';
$termBanner = null;
if (get_field('ez2_banner_image', $taxonomyIDString) !== 'undefined' && get_field('ez2_banner_image', $taxonomyIDString) !== false) {
	$termBanner = get_field('ez2_banner_image', $taxonomyIDString);
}
$termData->bannerImage = $termBanner;


	$result->resultSetData = $resultSetData;



	//ADS

		$ads = new stdClass;
		$ads->theId = $taxonomyIDString;
		$ads->leaderboard = getTermAd($taxonomyIDString, 'ez2_ad_leaderboard');
		$ads->bigbox1 = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_re_1');
		$ads->bigbox2 = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_re_2');
		$ads->bigbox3 = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_re_3');
		$ads->bigbox4 = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_re_4');
		$ads->bigbox5 = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_re_5');
		$ads->bigbox6 = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_re_6');
		$ads->bigbox7 = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_re_7');

		$ads->sponsored1 = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_re_1');
		$ads->sponsored2 = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_re_2');
		$ads->sponsored3 = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_re_3');
		$ads->sponsored4 = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_re_4');
		$ads->sponsored5 = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_re_5');
		$ads->sponsored6 = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_re_6');

		$ads->mobileInterstitial = getTermAd($taxonomyIDString, 'ez2_ad_mobile_interstitial');
		$ads->interstitial = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_interstitial');
		$ads->wallpaper = getTermAd($taxonomyIDString, 'ez2_ad_wallpaper');

		$result->ads = $ads;


	return $result;
	// Reset Post Data
	wp_reset_postdata();

}
