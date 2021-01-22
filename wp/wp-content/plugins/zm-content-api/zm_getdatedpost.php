<?php

function possiblySplitPostContent($somePostContent) {
	// if the length of $somePostContent, and there are more than one paragraph and the first and second halfs would both be past minimum length threshold,
	// then, split and return object that has {format: single|split, post_content(if single), post_content_top(if split), post_content_bottom(if split)}
	$postItem = new stdClass;
	$numPars = substr_count($somePostContent, '</p');
	$charCount = strlen($somePostContent);
	// use DOM module: http://htmlparsing.com/php.html ?
	$arbitraryCharCountThreshold = 213;// characters, not words
	if ($charCount > $arbitraryCharCountThreshold && $numPars >= 2) {

		// now figure out where to split!
		if ($numPars % 2 === 0) {	// even
			$splitAfterPar = $numPars / 2;
		} else {									// odd
			$splitAfterPar = ($numPars - 1)/ 2;// so if there were 3 paragraphs, we would be splitting after 1, not after 2.
		}
		// find all the occurances of paragraphs!
		// $postItem->numPars = $numPars;
		// $postItem->splitAfterPar = $splitAfterPar;
		// $endParPositions = Array();
		preg_match_all('/(<\/p>)/', $somePostContent, $endParPositions, PREG_OFFSET_CAPTURE);
		// $postItem->endParPositions = $endParPositions;

		$splitPos = $endParPositions[0][$splitAfterPar][1] + 4;// 4 because the position is that the start of the </p> tag, and we want it at the end.
		$firstHalf = substr($somePostContent, 0, $splitPos);
		$secondHalf = substr($somePostContent, $splitPos);
		// this works
		$postItem->format = 'split';
		$postItem->post_content_top = force_balance_tags($firstHalf);
		$postItem->post_content_bottom = force_balance_tags($secondHalf);
		$postItem->post_content = null;



		/*
		lets do a differnt kind of splitting. Initially, we'll keep the old, but then later deprecate, and decomission hte older one.
		instead of top and bottom, we'll have simply portions (I wanted to use the name chunks, but tha tis already taken.)
		we need some kind of arnbitrary logic, like every third, fourth, or fifth paragraph. Le't say every third for now. We could also consider how big the paragraphs are and use three if they are big and four if they are small.
		... we could consider the characters in a given paragraph.
		we still have the $endParPositions to work with. ... so ... count the character positions ...
		*/
		$minimumNumOfParsForPortionSplitting = 5;

		$contentPortions = false;
		if ($numPars > $minimumNumOfParsForPortionSplitting ) {
			$contentPortions = Array();
			$minCharacterThreshold = 555;// threshold, before would could conceivable place another ad
			$minParagraphTheshold = 3;// threshold, before would could conceivable place another ad
			// so loop through the $endParPositions, to determine potential splitting points.
			$lastSplitPos = 0;// the character position
			$lastParPos = 0;// the paragraph position
			$postItem->endParPositions = $endParPositions;
			for($i = 0; $i < count($endParPositions[0]); $i++) {// tricky ... we need to progressively snip away at the content.
				if ($endParPositions[0][$i][1] - $lastSplitPos > $minCharacterThreshold && $i + 1 - $lastParPos > $minParagraphTheshold) {
					$currentSplitPos = $endParPositions[0][$i][1] + 4;// this is the pos of the end of the last </p> tag.
					$contentPortion = force_balance_tags( substr($somePostContent, $lastSplitPos, $currentSplitPos - $lastSplitPos));// we don't know the next snip position yet! or do we?
					array_push($contentPortions, $contentPortion);
					// array_push($contentPortions, $contentPortion . '<div>' . $endParPositions[0][$i][1] . '</div>');
					$lastSplitPos = $currentSplitPos;
					$lastParPos = $i;
					//

				}
				if ($i === count($endParPositions[0]) -1 ) {
					if ($currentSplitPos < strlen($somePostContent)) {
						// then it didn't end with a </p> and we need to tack on the end.
						$lastChunkToTackOn = substr($somePostContent, $lastSplitPos);//
						array_push($contentPortions, $lastChunkToTackOn);
						// array_push($contentPortions, "something_$i");
					}
				} else {
					// array_push($contentPortions, "nothing_$i");
				}
			}
		}

		$postItem->portions = $contentPortions;
	} else {
		$postItem->format = 'single';
		$postItem->post_content = $somePostContent;
	}
	return $postItem;
}

function zm_getfirstdatedpost($data) {
	return zm_getdatedpost($data, true);
}
// getnextdatedpost
function zm_getnextdatedpost($data) {
	return zm_getdatedpost($data, false, true);
}



function zm_getdatedpost($data, $shim = false, $skipToNext = false) {
	//get an individual post, given the category slug, and the post slig (aka 'title')
	//return var_export($data, TRUE);
	global $urlSubstitutionForThumbnails;

	//
	$someYearSlug = $data['yearslug'];
	$someMonthSlug = $data['monthslug'];
	$someDaySlug = $data['dayslug'];
	$someCatSlug = $data['catslug'];// not used. but mistake!
	$somePostSlug = $data['postslug'];

	/**
	 * Special Hacks Here
	 * if the post is system/contests/2019/04/12/win-four-day-three-night-package-rocky-mountaineer/
	 * 	$result = new stdClass;
		$result->notFound = true;
		// $specificPostName or $specificPageName
		// $someSlug =
		$result->redirectLocation = guess_404_permalink($specificPageName);// something, or false
		// attempt redirection.
		return $result;
	 */
	// if (strpos($somePostSlug, 'win-four-day-three-night-package-rocky-mountaineer') !== false ) {
	// 	$result = new stdClass;
	// 	$result->notFound = true;
	// 	$result->redirectLocation = '/vancouver-zoomershow';
	// 	return $result;
	// }
	$args = array(
		'post_type' => 'post',
		'name' => $somePostSlug,
		'date_query' => array( array('year' => 	$someYearSlug, 'month' => $someMonthSlug, 'day' => $someDaySlug) ),
		'posts_per_page' => 1,
		'post_status' => 'publish'
	);

	if ($skipToNext) {
		// then we query the post, and find the next post, and overwrite the args using next post's data
		// die('wtf');
		$postsQuery = query_posts( $args );
		$posts = array();
		$previousPost = false;// scope outsite of while.
		while(have_posts()) : the_post();
			$previousPost = get_previous_post(true);// in same category!
		endwhile;
		wp_reset_query();
		// now overwrite the args
		// and only override, if it actually exists!
		if ($previousPost) {
			$args = array(
				'post_type' => 'post',
	      'post_status' => 'publish',
	      'p' => $previousPost->ID,   // id of the post you want to query
				'posts_per_page' => 1
			);
		} else {
			// die('no previous post found!');
		}
	}

	// wp_reset_query();
	$postsQuery = query_posts( $args );
	$posts = array();
	while(have_posts()) : the_post();
		global $post;
		$somePost = new stdClass;
		$somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
		// $somePost->post_content = force_balance_tags(apply_filters( 'the_content', get_the_content()));
		// $somePost->post_content = force_balance_tags(apply_filters( 'the_content', get_the_content()));
		// $somePost->post_content = get_the_content();// still single page, not all pages
		// $somePost->post_content = apply_filters('the_content', $somePost->post_content);
		// why does this sometimes have improperly balanced tags in the output?
    // disable post_content; stop sending it, and just use the multipage instead.
    
    
    $post_content = apply_filters( 'the_content', $post->post_content);
    
    // localHostToEZImages


		// $somePost->post_content = $post_content;
		// $somePost->post_content = possiblySplitPostContent($post_content);
		// $somePost->post_content = apply_filters( 'the_content', $post->post_content);
		// $somePost->multipage = false;
		$somePost->multipage = Array();
		if (strpos($post_content, '<!--nextpage-->') !== -1 || true) {// always use this approach
			// then we have paginated content.
			// how could we get multiple pages?
			// 2 ways, explode it, and then balance tags on each item, and then deliver all items.
			// or get_the_content, whilst manipulating the global $page (num) variable, to force wp to return subsequent pages of content.
			// we choose get_the_content with global $page because it handles the hassle of auto balancing tags whilst splitting content into chunks

			$nextPageOccurances = substr_count($post_content, '<!--nextpage-->');
			$somePost->nextPageOccurances = $nextPageOccurances;
			$somePost->socialShare = get_field('social_sharing_image', $somePost->id);

			global $page;// starts at 1
			$previousChunks = Array();
			for ($i = 1; $i <= $nextPageOccurances + 1; $i++) {

				$someContentPageChunk = localHostToEZImages(force_balance_tags(apply_filters( 'the_content', get_the_content())));
				// only add it if it is not aready there
				if (!array_search($someContentPageChunk, $previousChunks)) {
					array_push($somePost->multipage, possiblySplitPostContent($someContentPageChunk));
					array_push($previousChunks, $someContentPageChunk);// so that it is there to compare against in subsequnet iterations
				}

				// array_push($somePost->multipage, possiblySplitPostContent($someContentPageChunk));
				// array_push($previousChunks, $someContentPageChunk);// so that it is there to compare against in subsequnet iterations
				// $page = $page + $i;
				$page++;
			}

		} else {
			// array_push($somePost->multipage, $post_content);// it's an array to begin with.
			array_push($somePost->multipage, localHostToEZImages(get_the_content()));// it's an array to begin with.
		}

		$somePost->id = get_the_ID();
		// $somePost->editLink = get_edit_post_link(1);//
		// $somePost->editLink = apply_filters( 'get_edit_post_link', admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) ), get_the_ID() );
		// $somePost->editLink = 'asdf.kjafhsdlk';
		//$somePost->excerpt = zmExcerpt();
		$somePost->excerpt = zmExcerpt($somePost->id, 100, true, false, false, "", "", "...");
		$somePost->post_date = $post->post_date;
		$somePost->author = get_the_author();
		$somePost->authorID = get_the_author_meta('ID');
		// $somePost->author_permalink = "/author/" . get_the_author_meta('user_login');
		$somePost->author_permalink = str_replace(home_url(), "", get_author_posts_url(get_the_author_meta('ID')));

		$somePost->post_slug = $post->post_name;


		$somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));// gives us a relative url!
		// let's check to see if the post has a special video associated with it. This will be in a custom field, called featured_video
		$someFeaturedVideo = get_field('featured_video', $somePost->id);
		$podcastMp3 = get_field('wo_mp3', $somePost->id);
		$contestClosed = get_field('contest_closed', $somePost->id);
		$somePost->contest_closed = $contestClosed;
		$somePost->podcast_mp3 = $podcastMp3;
		$somePost->featured_video = $someFeaturedVideo;
		$somePost->featured_image_mode = get_field('featured_image_mode', $somePost->id);
		$somePost->photo_credit = get_field('photo_credit', $somePost->id);
		// $somePost->experimental_gallery = get_field('experimental_gallery', $somePost->id);// removed.

		$hasGallery = get_field('has_gallery', $somePost->id);

		$gallerySlides = false;
		$rawGallerySlides = false;
		// $rawGallerySlides = get_field('gallery_slides', $somePost->id);// not in the correct format that we need it in. we will need to massage it a bit.
		// $somePost->rawGallerySlides = $rawGallerySlides;
		if ($hasGallery) {// not false, and not null, or undefined
			$somePost->hasGallery = true;
			$gallerySlides = [];
			$rawGallerySlides = get_field('gallery_slides', $somePost->id);// not in the correct format that we need it in. we will need to massage it a bit.
			// return $rawGallerySlides;
			// $somePost->rawGallerySlides = $rawGallerySlides;
			// $somePost->gallerySlides = $rawGallerySlides;
			// foreach($rawGallerySlides as $rawGallerySlide) {
			for($i=0; $i<count($rawGallerySlides); $i++) {
				$rawGallerySlide = $rawGallerySlides[$i];
				// $someType = gettype($rawGallerySlide);
				$slide = new stdClass;
				// $slide->type = $someType;
				// $slide->stuff = $rawGallerySlide;
				// if ($rawGallerySlide['slide_type'] == "Image" ) {// the default
					// $imageID = $rawGallerySlide['image_slide'];
					$imageID = $rawGallerySlide['id'];
					// $post_thumbnail_id = get_post_thumbnail_id( $somePost->id );
					$someThumbnail = zm_get_attachment_image($imageID, 'full');
					$slide->id = $imageID;// this just is the id, apparantly not the whole object. maybe thats the problem
					$slide->thumbnail = $someThumbnail;
					// $slide->attachment = wp_get_attachment_image($imageID);
					$imagePost = get_post($imageID);
					// $slide->post = $imagePost;
					// $slide->postMeta = get_post_meta($imageID);// useless
					$slide->meta = wp_get_attachment_metadata( $imageID );

					// $slide->attachment = wp_get_attachment_image($imageID);// just gives an image tag, not an object, w alt, title, description, etc
					// $slide->caption = get_the_post_thumbnail_caption($imageID);// this does not seem to work.

					$slide->alt = get_post_meta($imageID , '_wp_attachment_image_alt', true);
					$slide->description = $imagePost->post_content;
					$slide->caption = $imagePost->post_excerpt;
					$slide->title = $imagePost->post_title;
					// $slide->description = get_post_meta($imageID , '_wp_attachment_image_description', true);
					// $slide->allMeta = get_post_meta($imageID);
					$slide->type = "Image";
					// $slide->id = $rawGallerySlide['id'];
					// $slide->image = zm_get_attachment_image($rawImageSlide['id'], 'full');//
					// $slide->name = $rawGallerySlide['name'];
					// $slide->date = $rawGallerySlide['date'];
					// $slide->url = $rawGallerySlide['url'];
					// $slide->title = $rawGallerySlide['title'];
					// $slide->description = $rawGallerySlide['description'];
					// $slide->caption = $rawGallerySlide['caption'];
					// $slide->alt = $rawGallerySlide['alt'];
					// $slide->mime_type = $rawGallerySlide['mime_type'];
					// $slide->width = $rawGallerySlide['width'];
					// $slide->height = $rawGallerySlide['height'];

				// } else if ($rawGallerySlide['slide_type'] == "Video") {
				// 	$slide->type = "Video";
				// 	$rawVideoSlideOEmbed = $rawGallerySlide['video_slide_oEmbed'];
				// 	preg_match('/src="([^"]+)"/', $rawVideoSlideOEmbed, $match);
				// 	$videoSlideEmbedURL = $match[1];
				//
				// 	$slide->oEmbed = $rawVideoSlideOEmbed;
				// 	$slide->oEmbedURL = $videoSlideEmbedURL;
				// }
				array_push($gallerySlides, $slide);
				// array_push($gallerySlides, $rawGallerySlide);

			}

		}
		$somePost->hasGallery = $hasGallery;
		$somePost->gallerySlides = $gallerySlides;// false of an array
		$somePost->rawGallerySlides = $rawGallerySlides;// false of an array

		// $somePost->gallerySlides = $rawGallerySlides;// false of an array
		// $somePost->has_gallery = get_field('has_image_gallery', $somePost->id);
		// if (isset()) {
		//
		// }
		// $somePost->gallery = get_field('gallery_slides', $somePost->id);// this puts out the whole gallery. but is it in the format that we can really use?
		//


		// so ... h'mm ... here is an interesting thought. what if we want to have more than just the possibility of images. what about if we wanted to throw in a youtube embed as well?
		// now, that is a bit tricky, because

		// where / how will we interject ads into the gallery? let's first make the gallery, and then worry about how to put ads into it. ok.


		// return get_field('experimental_gallery', $somePost->id);
		// now get some ads! this is a little complex.
		// first we look for the presence of ads in the following priority sequence: post, tag, category, general settings.
		// do we want to have function calls for each possible unit? or just blap them all in here?
		// do this for each ad unit (leaderboard, bigbox, etc):
		// get_field of post. if found, use that. if not, then get tags, and then get_field of those tags. if found in any, use that. if not, then get the categories, and then get_field of those. if found, use that, if not, use general all purpose unit
		// or how about a function that gets an ad tag, given its postid and ad unit id.

		$ads = new stdClass;
		$ads->leaderboard = getPostAd($somePost->id, 'ez2_ad_leaderboard');
		$ads->bigbox = getPostAd($somePost->id, 'ez2_ad_bigbox');

		// now we add two additional sets!.
		$ads->a_leaderboard = getPostAd($somePost->id, 'ez2_ad_leaderboard');
		$ads->a_bigboxOne = getPostAd($somePost->id, 'ez2_ad_bigbox');
		$ads->a_bigboxTwo = getPostAd($somePost->id, 'ez2_ad_bigbox_two');

		$ads->b_leaderboard = getPostAd($somePost->id, 'ez2_ad_leaderboard_two');
		$ads->b_bigboxOne = getPostAd($somePost->id, 'ez2_ad_bigbox_b_one');
		$ads->b_bigboxTwo = getPostAd($somePost->id, 'ez2_ad_bigbox_b_two');
		// so where does the interstitial bigbox come from? let's say that the bigBoxTwo is the interstitial. actually, it is the ez2_ad_desktop_interstitial field.
		//
		$ads->c_leaderboard = getPostAd($somePost->id, 'ez2_ad_leaderboard_three');
		// ez2_ad_bigbox_c_one
		$ads->c_bigboxOne = getPostAd($somePost->id, 'ez2_ad_bigbox_c_one');
		$ads->c_bigboxTwo = getPostAd($somePost->id, 'ez2_ad_bigbox_c_two');
		//

		$ads->sponsored_one = getPostAd($somePost->id, 'ez2_ad_sponsored_one');
		$ads->sponsored_two = getPostAd($somePost->id, 'ez2_ad_sponsored_two');
		$ads->interstitial = getPostAd($somePost->id, 'ez2_ad_sponsored_interstitial');
		$ads->wallpaper = getPostAd($somePost->id, 'ez2_ad_wallpaper');
		$ads->mobileInterstitial = getPostAd($somePost->id, 'ez2_ad_mobile_interstitial');// this works on the home page, but does not on the post pages.
		$ads->mobileInterstitial2 = getPostAd($somePost->id, 'ez2_ad_mobile_interstitial_2');
		$ads->ez2_ad_desktop_interstitial = getPostAd($somePost->id, 'ez2_ad_desktop_interstitial');//
		// $somePost->ads = $ads;
		// ez2_ad_sponsored_one, ez2_ad_sponsored_two, ez2_ad_sponsored_interstitial, ez2_ad_wallpaper

		$someCat = get_the_category();
		if (count($someCat) > 0) {
			$someMinimalCats = array();
			for ($i=0;$i<count($someCat);$i++) {
				$someMinimalCatObj = new stdClass;
				$someMinimalCatObj->slug = $someCat[$i]->slug;
				$someMinimalCatObj->term_id = $someCat[$i]->term_id;
				$someMinimalCatObj->name = $someCat[$i]->name;
				array_push($someMinimalCats, $someMinimalCatObj);
			}
			// TODO add the primary term so that we can have it handly in SPA for passing to GA
			// if ( class_exists('WPSEO_Primary_Term') ) {
			// 	$wpseo_primary_term = new WPSEO_Primary_Term( 'category', $somePost->id );
			// 	$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
			// 	$primary_term = get_term( $wpseo_primary_term );
			// 	foreach($someMinimalCats AS $someMinimalCat) {
			// 		if ($someMinimalCat->term_id === $primary_term->term_id) {
			//
			// 		}
			// 	}
			//
			// }
			$somePost->cats = $someMinimalCats;
		}
		// now add the tags of the post as well
		// wp_get_post_tags( $post_id, $args )
		// $someTags = wp_get_post_tags();
		$somePost->tags = wp_get_post_tags();
		$someThumbnailURLs = Array();
		if ( has_post_thumbnail() ) {
			$post_thumbnail_id = get_post_thumbnail_id( $somePost->id );
			$someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
			// if (isset($urlSubstitutionForThumbnails) && $urlSubstitutionForThumbnails !== false) {
			// 	$someThumbnailJSON = json_encode($someThumbnail);
			// 	$someAlteredThumbnailJSON = str_replace('ez2.local', 'everythingzoomer.com', $someThumbnailJSON);
			// 	$someThumbnail = json_decode($someAlteredThumbnailJSON);
			// 	// also fix the content
			// 	$somePost->post_content = str_replace('ez2.local', 'everythingzoomer.com', $somePost->post_content);
			// }
			array_push($someThumbnailURLs, $someThumbnail);
		}


		$theHugeThumb = null;
		$theMediumThumb = null;
		if ( has_post_thumbnail($somePost->id ) ) {
			// $theHugeThumb = get_the_post_thumbnail_url($somePost->id , 'huge');
			$theHugeThumb = zm_get_attachment_image($post_thumbnail_id, 'huge');
			$theMediumThumb = zm_get_attachment_image($post_thumbnail_id, 'sharing');
		}


		$somePost->thumbnails = $someThumbnailURLs;
		$somePost->thumbnail_huge = $theHugeThumb;
		$somePost->thumbnail_medium = $theMediumThumb;
		// $somePost->thumbnail_medium = zm_get_attachment_image($post_thumbnail_id, 'medium');
		array_push($posts, $somePost);
	endwhile;

	$result = new stdClass;
	if ( count($posts) == 0 ) {
		// invoke the not found, attempt redirectToClosestMatch
		// $result = new stdClass;
		$result->notFound = true;


		$somePermalink = $somePostSlug;
		if ( isset($data['fullpermalink']) ) {
			$somePermalink = $data['fullpermalink'];
			if (strpos($somePermalink, '/') === 0) {
				$somePermalink = substr($somePermalink, 1); // removes the 0th or first character, which was a /
			}
		}
		// die('$somePermalink=='.$somePermalink);
		$somePossibleRedirectLocation = getRedirectForURL($somePermalink);
		// echo('$somePossibleRedirectLocation==');
		// echo("\$data['route']=={$data['route']}");
		// var_dump($data);
		// var_dump($somePossibleRedirectLocation);
		// die('not found ;)');
		if ($somePossibleRedirectLocation !== false) {
			$result->redirectLocation = $somePossibleRedirectLocation;
			// $result->redirectLocation = 'huh';
		} else {
			$result->redirectLocation = guess_404_permalink($somePostSlug);// something, or false
		}


		// attempt redirection.
		return $result;
	} else {
		$result->resultItem = $posts[0];

		$result->ads = $ads;

		if (false && $shim) {
			// then we are going to get the first (most recently published) post in this category, instead of the next one.
			// $firstCat = null;
			// if (count($result->resultItem->cats) > 0) {
			// 	$firstCat = $result->resultItem->cats[0];
			// }
			// // do a query toi get the id of the most recent post in $firstCat
			// // wp_reset_postdata();
			// $firstPostInCatQuery = new WP_Query( array( 'cat' => $firstCat, 'posts_per_page' => 1 ) );
			// // The Loop
			// if ( $firstPostInCatQuery->have_posts() ) {
			//
			// 	while ( $firstPostInCatQuery->have_posts() ) {
			// 		$firstPostInCatQuery->the_post();
			// 		$previousPost = $post;// from global
			// 	}
			// 	/* Restore original Post Data */
			//
			// } else {
			// 	// no posts found
			// }
			// wp_reset_postdata();
		} else {

		}

		// the next item, is actually the previous item!
		$previousPost = get_previous_post(true);// in same category!

		// $firstCat = null;
		// if (count($result->resultItem->cats) > 0) {
		// 	$firstCat = $result->resultItem->cats[0];
		// }
		$firstCat = false;
		if (count($result->resultItem->cats) > 0) {
			$firstCat = $result->resultItem->cats[0];
		}
		// return $firstCat;

		$firstPostInCat = false;
		if ($firstCat) {
			$firstPostInCatQuery = new WP_Query( array( 'cat' => $firstCat->term_id, 'posts_per_page' => 1 ) );
			while ( $firstPostInCatQuery->have_posts() ) {
				$firstPostInCatQuery->the_post();
				$firstPostInCat = $post;// from global
			}
		}
		wp_reset_postdata();
		/* Restore original Post Data */
		// $result->firstPostInCat = $firstPostInCat;

		/*
		$someSponsoredContentTag = get_term_by('slug', 'sponsored-content', 'post_tag');
		$someSponsoredContentTagID = isset($someSponsoredContentTag) ? $someSponsoredContentTag->term_id : false;
		if ($someSponsoredContentTagID) {
			$args['tag__not_in'] => array("post_tag_{$someSponsoredContentTagID}");
		}
		*/

		$someNextItem = new stdClass;
		if ($shim && $firstPostInCat) {

			$someNextItem->id = $firstPostInCat->ID;
			$someNextItem->post_title = $firstPostInCat->post_title;
			$someNextItem->post_date = $firstPostInCat->post_date;
			$someNextItem->post_name = $firstPostInCat->post_name;
			$someNextItem->permalink = str_replace(home_url(), "", get_permalink($someNextItem->id));// gives us a relative url!
			$result->nextItem = $someNextItem;
		} else if ($previousPost !== null && isSet($previousPost->ID)) {
			$someNextItem = new stdClass;
			$someNextItem->id = $previousPost->ID;
			$someNextItem->post_title = $previousPost->post_title;
			$someNextItem->post_date = $previousPost->post_date;
			$someNextItem->post_name = $previousPost->post_name;
			$someNextItem->permalink = str_replace(home_url(), "", get_permalink($someNextItem->id));// gives us a relative url!

		}
		$result->nextItem = $someNextItem;
		// return ($posts[0]);
		return httpToHttpsImages($result);
	}


	// what are the ways that this coould not work?
	// full permalinks must match, so they need to be fully constructed somehow
	// we also need to account for the possibility of hierarchical category structures represented in the permalink.
	// so ... but we won't be getting the full permalink in the api call ... so can we reconstitute it here?
	// no ... can't get category ancestors unless they are actually sent.
	// maybe we could, but it would be some work.
	// alternatively, we could send the full permalink as url parameter in the api call, and just use that. easier, even if there is less SoC.
	// return ($posts[0]);

}

/*
	We need a new way of thinking about ads, and where the available spots are, and what should fill them.
	Let's think of them as opportunities, and use some logic to determine if it makes sense to display an ad there.
	We will want to consider different operational modes. So ... always use specific tag. Or use a tag from a pool of tags.
	Also, logic that determines whether the ad should be displayed in a spot based on some configurable rules.
	Like: in the situation where we have a long post, spread accross multiple 'pages' there is a potential opportunity between each one of those page.
	Also, we could possibly create opportunities, in between paragraphs in a single page post? Maybe let's not go there now. That's a maybe later kind of thing.

	the maxim is that we want to hit the sweet spot, in terms of ads, and not overdo it. we can't simply add new ads in places ad hoc, lest we end up with a craptastic ad smorgasborg.
	We need a way to put things into the mix when we need to.

*/
