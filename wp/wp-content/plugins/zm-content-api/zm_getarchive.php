<?php

// function zm_getArchive($data) {
// 	// we need to get range and offset as well as category slug / permalink
// }


// define('DEFAULT_GETARCHIVE_RANGE', 12);
// define('DEFAULT_GETARCHIVE_OFFSET', 0);
// define('DEFAULT_GETARCHIVE_POSTTYPE', 'post');
// define('HEROES_PER_CHUNK', 2);


define('DEFAULT_GETARCHIVE_HEROES_PER_CHUNK', 2);
define('DEFAULT_GETARCHIVE_REGULAR_ITEMS_PER_CHUNK', 8);


function zm_getArchive($data) {
	// preliminaryQuery first.
	// if we are calling getPosts in single mode, ie for a single post or page, we could bypass this iniital query altogether, no?
	// return "hello";

	// we also need to be able to do our magical lookup / 301 or 404 if the slug is not found. This is crucial.

	// chunknum

	if (isset($data['taxonomy']) && isset($data['termname'])) {
		if ($data['taxonomy'] == 'category') {
			$taxSlug = $data['termname'];
			$taxonomy = 'category';

		} else if ($data['taxonomy'] == 'tags') {
			$taxSlug = $data['termname'];
			$taxonomy = 'post_tag';
		}

		$someTerm = get_term_by('slug', $taxSlug, $taxonomy);

		if (!$someTerm) {
			// then we have to bail out of here, and do the lookup / redirect or 404 thing
			$result = new stdClass;
			$result->notFound = true;
			$somePermalink = $taxSlug;
			// if ( isset($data['fullpermalink']) ) {
			// 	$somePermalink = $data['fullpermalink'];
			// 	if (strpos($somePermalink, '/') === 0) {
			// 		$somePermalink = substr($somePermalink, 1); // removes the 0th or first character, which was a /
			// 	}
			// }
			$somePossibleRedirectLocation = getRedirectForURL($somePermalink);
			if ($somePossibleRedirectLocation !== false) {
				$result->redirectLocation = $somePossibleRedirectLocation;

			} else {
				$result->redirectLocation = guess_404_permalink($somePermalink);// something, or false
			}
			// attempt redirection.
			return $result;
		}
	}

	$somePostCount = $someTerm->count;


	wp_reset_query();

	// $range = isset($data['range']) ? (int)$data['range'] : DEFAULT_GETARCHIVE_RANGE;
	// $offset = isset($data['offset']) ? (int)$data['offset'] : DEFAULT_GETARCHIVE_OFFSET;
	// $post_type =  isset($data['post_type']) ? $data['post_type'] : DEFAULT_GETARCHIVE_POSTTYPE;

	$chunkNum = isset($data['chunknum']) ? (int)$data['chunknum'] : 0;
	$hero_range = DEFAULT_GETARCHIVE_HEROES_PER_CHUNK;
	// //DEFAULT_GETARCHIVE_REGULAR_ITEMS_PER_CHUNK
	$hero_offset = $chunkNum * DEFAULT_GETARCHIVE_HEROES_PER_CHUNK;
	$regular_offset = $chunkNum * DEFAULT_GETARCHIVE_REGULAR_ITEMS_PER_CHUNK;
	$args = array(
					//'post_type' => $post_type,
					'posts_per_page' => $hero_range,
					'post_status' => 'publish',
					'orderby' => 'post_date', 'order' => 'DESC', 'offset' => $hero_offset
				);

	// $specificCategory = isset($data['category']) ? $data['category'] : false;
	// $specificTag = isset($data['tag']) ? $data['tag'] : false;
	if (!$data['termname']) {
		return "no taxonomy term specified :(";
	}

	// die(var_export($data, true));

	$taxonomyIDString = null;
	if ($data['taxonomy'] == 'category') {
		$args['category_name'] = $data['termname'];
		$termData = get_term_by('slug', $data['termname'], 'category');
		$taxonomyIDString = 'category_' . $termData->term_id;// to find $featuredArticles

	} else if ($data['taxonomy'] == 'tag' || $data['taxonomy'] == 'tags') {
		$args['tag'] = $data['termname'];
		$termData = get_term_by('slug', $data['termname'], 'post_tag');
		$taxonomyIDString = 'post_tag_' . $termData->term_id;// to find $featuredArticles
	}

	// exclude posts in sponsored-content
	//'tag__not_in' => array($tag_id_1, $tag_id_2)
	$someSponsoredContentTag = get_term_by('slug', 'sponsored-content', 'post_tag');
	$someSponsoredContentTagID = isset($someSponsoredContentTag) ? $someSponsoredContentTag->term_id : false;
	// echo("\$someSponsoredContentTagID=={$someSponsoredContentTagID}");
	if ($someSponsoredContentTagID) {
		$args['tag__not_in'] = array($someSponsoredContentTagID);
	}
	// $termData = new stdClass;
	$theBanner = '';
	$termBanner = null;
	if (get_field('ez2_banner_image', $taxonomyIDString) !== 'undefined' && get_field('ez2_banner_image', $taxonomyIDString) !== false) {
		$termBanner = get_field('ez2_banner_image', $taxonomyIDString);
	}
	// return $termBanner;

	$termData->bannerImage = $termBanner;


	// $termData = new stdClass;
	$ogImg = null;
	if (get_field('meta_image', $taxonomyIDString) !== 'undefined' && get_field('meta_image', $taxonomyIDString) !== false) {
		$ogImg = get_field('meta_image', $taxonomyIDString);
	}
	// return $termBanner;

	$termData->ogImage = $ogImg;

	// $parentCatPath = get_category_parents( $termData->term_id );
	// $parentCat = strtok($parentCatPath, '/');
  //
  //
	// $termData->bannerImage =  $termBanner;
	// $termData->parent_category =  $parentCat;
  //
	// $parentCatID = get_term_by( 'name', $parentCat, 'category' );
	// $termData->parent_category_id =  $parentCatID->term_id;
	// $parentCatBannerObj =  get_field('ez2_banner_image', 'post_tag_' . $parentCatID->term_id);
	// $parentCatBannerUrl =  $parentCatBannerObj['url'];
  //
	// $termData->parent_category_banner = $parentCatBannerUrl;


	// add a meta query to get posts that have
	// $args['meta_key'] = 'hero_spot';
	// $args['meta_value'] = 1;
	$args['meta_query'] = array(
		array(
			'key' => 'hero_spot',
			'compare' => '==',
			'value' => '1'
		)
	);
	// $heros = Array();

	// $featuredArticles = get_field('ez2_featured_category_or_tag_items', $taxonomyIDString);
  //
	$heroPosts = array();
	$heroPostIDs = array();
	// $args['post__in'] = $featuredArticles;
	$postsQuery = query_posts( $args );
	$i = 0;
	while(have_posts()) : the_post();
		$somePost = zm_getArchivePost();
		array_push($heroPosts, $somePost);
		array_push($heroPostIDs, $somePost->id);
		$i++;
		// if ($i >= $range) {
		// 	break;
		// }
	endwhile;


	unset($postsQuery);
	wp_reset_query();
	unset($args['meta_query']);

	$args['meta_query'] = array(
		'relation' => 'OR',
		array(
			'key' => 'hero_spot',
			'compare' => 'NOT EXISTS',
			'value' => '1'
		),
		array(
			'key' => 'hero_spot',
			'compare' => '==',
			'value' => '0'
		)
	);

	// keep same meta key, but switch value!
	// unset($args['meta_value']);
	// $args['meta_value'] = 0;

	$args['posts_per_page'] = DEFAULT_GETARCHIVE_REGULAR_ITEMS_PER_CHUNK;
	$args['offset'] = $regular_offset;

	// $regular_offset
	//'posts_per_page' => $hero_range,
	// $args['post__not_in'] = $heroPostIDs;// its not enough to make them not be in the ones that we just queried ... we have to make sure they are not in any of the ones that may have preceded it or will come after.

	// $postsQuery = query_posts( $args );
	$regularPosts = Array();

	$postsQuery = query_posts( $args );

	$someFeaturedTags = zm_getFeaturedTags();//outside of loop, so only called once!

	while(have_posts()) : the_post();
		$somePost = zm_getArchivePost($someFeaturedTags);
		array_push($regularPosts, $somePost);
		// $i++;
		// if ($i >= DEFAULT_GETARCHIVE_REGULAR_ITEMS_PER_CHUNK) {
		// 	break;
		// }
	endwhile;
	// return $regularPosts;
	// ok ... here's the deal schlemele ...
	// instead of returning an array of items, we return an object that has that array inside it, and also other properties, like deails about the resultset
	// ... filters, range, offset, totalitems (in filtered result set)
	// if there is only 1 item? do we need to provide a resultset? and additional data ... let's put that off for now ...
	// ... but ... we also need to specify a limit. if we impose a limit, to the query itself, we will never get more than that total number specified in the query.
	// so instead, we need to get the total ... the -1 limit, and then, use code to limit the resultset. use: $i++; if ($i >= $range) break
	// $totalNumberOfPosts

	// now construct $posts, using $heroPosts and $regularPosts
	// insert into position 3 the second hero, and unset into 0th the first.
	/*$arr_alphabet = array('a', 'b', 'd');
array_splice($arr_alphabet, 2, 0, 'c');
print_r($arr_alphabet);*/
	// array_splice($regularPosts, 3, 0, $heroPosts[1]);

	$firstHeroPost = count($heroPosts) >= 1 ? $heroPosts[0] : null;
	$secondHeroPost =  count($heroPosts) >= 2 ? $heroPosts[1] : null;


	// return $heroPosts;
	array_splice($regularPosts, 4, 0, [$secondHeroPost]);
	array_unshift($regularPosts, $firstHeroPost);
	//

	$resultSetData = new stdClass;
	$resultSetData->publish = $somePostCount;
	$resultSetData->termData = $termData;

	$result = new stdClass;
	$result->resultSet = $regularPosts;

	//	$result->range = $range;
	//	$result->offset = $offset;

	$result->resultSetData = $resultSetData;

	$ads = new stdClass;
	// $ads->test = 'bananas';
	// $ads->secondTest = 'pears';
	$ads->leaderboard = getTermAd($taxonomyIDString, 'ez2_ad_leaderboard');
	$ads->leaderboard_two = getTermAd($taxonomyIDString, 'ez2_ad_leaderboard_two');
	$ads->bigbox = getTermAd($taxonomyIDString, 'ez2_ad_bigbox');
	$ads->bigbox_two = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_two');
	$ads->mobileInterstitial = getTermAd($taxonomyIDString, 'ez2_ad_mobile_interstitial');
	$ads->mobileInterstitial2 = getTermAd($taxonomyIDString, 'ez2_ad_mobile_interstitial_2');


	$ads->sponsored_one = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_one');
	$ads->sponsored_two = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_two');


	$ads->bigboxAOne = getTermAd($taxonomyIDString, 'ez2_ad_bigbox');
	$ads->bigboxATwo = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_two');

	$ads->bigboxBOne = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_b_one');
	$ads->bigboxBTwo = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_b_two');

	$ads->bigboxCOne = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_c_one');
	// NCAP is a new acronym that I just invented, which stands for Namespace-Collision-Avoiding-Prefix. In this case, I am using the prefix ncap_ to honour the invention of this most useful dirty monkey hack
	// $ads->ncap_bigbox = [$ads->bigbox, $ads->bigbox_two, $ads->bigboxAOne, $ads->bigboxATwo, $ads->bigboxBOne, $ads->bigboxBTwo];
	$ads->bigbox_pool = [$ads->bigbox, $ads->bigbox_two, $ads->bigboxAOne, $ads->bigboxATwo, $ads->bigboxBOne, $ads->bigboxBTwo];// in this case, we are using an NCAS (Namespace collision avoiding suffix) ... Suffix, instead, because a pool is what this is.


	$ads->sponsoredAOne = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_one');
	$ads->sponsoredATwo = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_two');

	$ads->sponsoredBOne = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_b_one');
	$ads->sponsoredBTwo = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_b_two');



	$ads->interstitial = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_interstitial');
	$ads->wallpaper = getTermAd($taxonomyIDString, 'ez2_ad_wallpaper');
	$ads->a_leaderboard = getTermAd($taxonomyIDString, 'ez2_ad_leaderboard');
	$ads->a_bigboxOne = getTermAd($taxonomyIDString, 'ez2_ad_bigbox');
	$ads->a_bigboxTwo = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_two');
	$ads->b_leaderboard = getTermAd($taxonomyIDString, 'ez2_ad_leaderboard_two');
	$ads->b_bigboxOne = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_b_one');
	$ads->b_bigboxTwo = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_b_two');
	$ads->c_leaderboard = getTermAd($taxonomyIDString, 'ez2_ad_leaderboard_three');
	$ads->c_bigboxOne = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_c_one');
	$ads->c_bigboxTwo = getTermAd($taxonomyIDString, 'ez2_ad_bigbox_c_two');

	// we are now moving to a better and more consistant structure for ads that maintains shape accross different types of content / route containers
	// channel [the ad unit that we are concerned with. If it has multiple ad tags that we need to cycle through, it is phased. we put the phases in a cycle.]
	/*
			shape: {
				generalAdCohort: {
					leaderboard: {
						phased:BOOL,
						units: [{

						},],
					},
				},
				stackedAdCohorts: {// a keyed array of adCohorts, where the keys are post_ids or something like that.
					post_id: adCohort (just like in generalAdCohort)
				}
			}
	*/

	$ads->stackedAdCohorts = false;
	$generalAdCohort = new stdClass;// ad cohorts have ad channels, which can be either an object, with ad unit, and sizes, or an array of such objects
	$generalAdCohort->leaderboard = array(
		'a' => getTermAd($taxonomyIDString, 'ez2_ad_leaderboard'),
		'b' => getTermAd($taxonomyIDString, 'ez2_ad_leaderboard_two'),
		'c' => getTermAd($taxonomyIDString, 'ez2_ad_leaderboard_three')
	);
	$generalAdCohort->bigBoxOne = array(
		'a' => getTermAd($taxonomyIDString, 'ez2_ad_bigbox'),
		'b' => getTermAd($taxonomyIDString, 'ez2_ad_bigbox_b_one'),
		'c' => getTermAd($taxonomyIDString, 'ez2_ad_bigbox_c_one')
	);
	$generalAdCohort->bigBoxTwo = array(
		'a' => getTermAd($taxonomyIDString, 'ez2_ad_bigbox_two'),
		'b' => getTermAd($taxonomyIDString, 'ez2_ad_bigbox_b_two'),
		'c' => getTermAd($taxonomyIDString, 'ez2_ad_bigbox_c_two')
	);
	$generalAdCohort->sponsoredOne = array(
		'a' => getTermAd($taxonomyIDString, 'ez2_ad_sponsored_one'),
		'b' => getTermAd($taxonomyIDString, 'ez2_ad_sponsored_two'),
		'c' => getTermAd($taxonomyIDString, 'ez2_ad_sponsored_three')
	);
	$generalAdCohort->sponsoredTwo = array(
		'a' => getTermAd($taxonomyIDString, 'ez2_ad_sponsored_b_one'),
		'b' => getTermAd($taxonomyIDString, 'ez2_ad_sponsored_b_two'),
		'c' => getTermAd($taxonomyIDString, 'ez2_ad_sponsored_b_three')
	);
	$generalAdCohort->interstitial = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_interstitial');
	$generalAdCohort->wallpaper = getTermAd($taxonomyIDString, 'ez2_ad_wallpaper');


	$ads->generalAdCohort = $generalAdCohort;
	$result->ads = $ads;
	return ($result);
}

function zm_getArchivePost($someFeaturedTags = []) {
	// assume global $post is in scope
	global $post;
	$somePost = new stdClass;
	$somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
	$somePost->id = get_the_ID();
	$somePost->excerpt = zmExcerpt($somePost->id, 100, true, false, false, "", "", "...");
	$somePost->post_date = $post->post_date;
	$somePost->author = get_the_author();
	$somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));// gives us a relative url!
	$somePost->post_slug = basename(get_permalink());
	// $somePost->region = get_field('post_region');

	$hero_spot = get_field('hero_spot');


	$contentClosed = get_field('contest_closed');

	$someHeroImageID = get_field('hero_image');

	if ( $hero_spot && $someHeroImageID ) {
		$somePost->hero = true;
		$somePost->hero_image = zm_get_attachment_image($someHeroImageID, 'huge');
		$somePost->hero_image_huge = zm_get_attachment_image($someHeroImageID, 'huge');
		$somePost->hero_image_large = zm_get_attachment_image($someHeroImageID, 'large');
		$somePost->hero_image_crop_large = zm_get_attachment_image($someHeroImageID, 'large_crop');
	} else {
		$somePost->hero = false;
		$somePost->hero_image = null;
	}

	$someCat = get_the_category();
	$someMinimalCats = array();
	if (count($someCat) > 0) {
		for ($j=0;$j<count($someCat);$j++) {
			$someMinimalCatObj = new stdClass;
			$someMinimalCatObj->slug = $someCat[$j]->slug;
			$someMinimalCatObj->term_id = $someCat[$j]->term_id;
			$someMinimalCatObj->name = $someCat[$j]->name;
			array_push($someMinimalCats, $someMinimalCatObj);
		}
	}
	$somePost->cats = $someMinimalCats;

	$somePost->tags = get_the_tags();
	$somePost->featuredTags = zm_getAPostsTagsInSetOfTags($somePost->id, $someFeaturedTags);;


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
	$somePost->contest_closed = $contentClosed;
	$somePost->thumbnails = $someThumbnailURLs;

	return $somePost;
}
