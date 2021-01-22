<?php

// function zm_getArchive($data) {
// 	// we need to get range and offset as well as category slug / permalink
// }


// define('DEFAULT_GETARCHIVE_RANGE', 12);
// define('DEFAULT_GETARCHIVE_OFFSET', 0);
// define('DEFAULT_GETARCHIVE_POSTTYPE', 'post');

define('DEFAULT_GETARCHIVE2_HEROES_PER_CHUNK', 2);
define('DEFAULT_GETARCHIVE2_REGULAR_ITEMS_PER_CHUNK', 8);


function zm_getArchive2($data) {
	// preliminaryQuery first.
	// if we are calling getPosts in single mode, ie for a single post or page, we could bypass this iniital query altogether, no?
	// return "hello";

	// we also need to be able to do our magical lookup / 301 or 404 if the slug is not found. This is crucial.

	if (isset($data['category']) || isset($data['tag'])) {
		if (isset($data['category'])) {
			$taxSlug = $data['category'];
			$taxonomy = 'category';

		} else if (isset($data['tag'])) {
			$taxSlug = $data['tag'];
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

	$chunkNum = isset($data['chunk']) ? (int)$data['chunk'] : 0;
	$hero_range = DEFAULT_GETARCHIVE2_HEROES_PER_CHUNK;
	// //DEFAULT_GETARCHIVE2_REGULAR_ITEMS_PER_CHUNK
	$hero_offset = $chunkNum * DEFAULT_GETARCHIVE2_HEROES_PER_CHUNK;
	$regular_offset = $chunkNum * DEFAULT_GETARCHIVE2_REGULAR_ITEMS_PER_CHUNK;
	$args = array(
					//'post_type' => $post_type,
					'posts_per_page' => $hero_range,
					'post_status' => 'publish',
					'orderby' => 'post_date', 'order' => 'DESC', 'offset' => $hero_offset
				);

	$specificCategory = isset($data['category']) ? $data['category'] : false;
	$specificTag = isset($data['tag']) ? $data['tag'] : false;
	if (!$specificCategory && !$specificTag) {
		return "no taxonomy term specified :(";
	}

	$taxonomyIDString = null;
	if ($specificCategory) {
		$args['category_name'] = $specificCategory;
		$termData = get_term_by('slug', $specificCategory, 'category');
		$taxonomyIDString = 'category_' . $termData->term_id;// to find $featuredArticles
	} else if ($specificTag) {
		$args['tag'] = $specificTag;
		$termData = get_term_by('slug', $specificTag, 'post_tag');
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
	// die("\$termBanner=={$termBanner}");
	$termData->bannerImage =  $termBanner;

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

	$args['posts_per_page'] = DEFAULT_GETARCHIVE2_REGULAR_ITEMS_PER_CHUNK;
	$args['offset'] = $regular_offset;

	// $regular_offset
	//'posts_per_page' => $hero_range,
	// $args['post__not_in'] = $heroPostIDs;// its not enough to make them not be in the ones that we just queried ... we have to make sure they are not in any of the ones that may have preceded it or will come after.

	// $postsQuery = query_posts( $args );
	$regularPosts = Array();
	$postsQuery = query_posts( $args );

	while(have_posts()) : the_post();
		$somePost = zm_getArchivePost();
		array_push($regularPosts, $somePost);
		// $i++;
		// if ($i >= DEFAULT_GETARCHIVE2_REGULAR_ITEMS_PER_CHUNK) {
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

	$resultSetData = new stdClass;
	$resultSetData->publish = $somePostCount;
	$resultSetData->termData = $termData;

	$result = new stdClass;
	$result->resultSet = $regularPosts;

	//	$result->range = $range;
	//	$result->offset = $offset;

	$result->resultSetData = $resultSetData;

	$ads = new stdClass;
	$ads->leaderboard = getTermAd($taxonomyIDString, 'ez2_ad_leaderboard');
	$ads->bigbox = getTermAd($taxonomyIDString, 'ez2_ad_bigbox');
	$ads->mobileInterstitial = getTermAd($taxonomyIDString, 'ez2_ad_mobile_interstitialGroup');

	$ads->sponsored_one = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_one');
	$ads->sponsored_two = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_two');
	$ads->interstitial = getTermAd($taxonomyIDString, 'ez2_ad_sponsored_interstitial');
	$ads->wallpaper = getTermAd($taxonomyIDString, 'ez2_ad_wallpaper');


	$result->ads = $ads;
	return ($result);
}
