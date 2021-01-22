<?php

//This is a generic function that is used to return the category of the Posts START
function getZedCatsbookshelf($post)
{
  $someCat = get_the_terms($post, 'zed');
  $someMinimalCats = false;
  if (count($someCat) > 0) {
    $someMinimalCats = array();
    for ($i = 0; $i < count($someCat); $i++) {
      $someMinimalCatObj = new stdClass;
      $someMinimalCatObj->slug = $someCat[$i]->slug;
      $someMinimalCatObj->term_id = $someCat[$i]->term_id;
      $someMinimalCatObj->name = $someCat[$i]->name;
      array_push($someMinimalCats, $someMinimalCatObj);
    }
  } else {
  }
  return $someMinimalCats;
}
//This is a generic function that is used to return the category of the Posts END


//Similar to On Our Radar, this is the section that returns three posts for displaying under On Our BookShelf section on Bookshelf START
function zed_onOurBookShelf()
{
  $zedPage = get_page_by_path('bookshelf-book-club');
  $morePosts = get_field('on_our_bookshelf', $zedPage->ID);
  $morePostStack = array();

  foreach ($morePosts as $postID) {
    $someObj = new stdClass;
    $morePost = get_post($postID);
    $post_thumbnail_id = get_post_thumbnail_id($postID);
    if (isset($post_thumbnail_id)) {
      $someObj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'teaser_square');
    } else {
      $someObj->thumbnail = false;
    }
    $someObj->post_title = $morePost->post_title;
    $someObj->subtitle = get_field('subtitle', $morePost);
    $someObj->cats = getZedCats($morePost);
    $someObj->permalink = str_replace(home_url(), "", get_permalink($postID));
    array_push($morePostStack, $someObj);
  }
  return $morePostStack;
}
//Similar to On Our Radar, this is the section that returns three posts for displaying under On Our BookShelf section on Bookshelf END


//This is the section that returns the HERO for the Bookshelf Landing screen returns only 1 value with COPY on Bookshelf START
function zed_getherobookshelf()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('bookshelf-book-club');
  $zedPagePostID = $zedPage->ID;
  $heroPost = get_field('hero_post', $zedPage->ID);
  $heroBlurb = get_field('hero_blurb', $zedPage->ID);
  $someObj->heroBlurb = $heroBlurb;
  $heroPostID = $heroPost[0];
  $someObj->heroTitle = get_the_title($heroPostID);
  $post_thumbnail_id = get_post_thumbnail_id($heroPostID);
  if (isset($post_thumbnail_id)) {
    $someObj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
  } else {
    $someObj->thumbnail = false;
  }
  $somePossiibleAlternateHeroImageID = get_field('hero_image', $zedPage->ID);
  if ($somePossiibleAlternateHeroImageID) {
    $someObj->alternateHero = zm_get_attachment_image($somePossiibleAlternateHeroImageID, 'huge720');
  } else {
    $someObj->alternateHero = false;
  }
  $someObj->permalink = str_replace(home_url(), "", get_permalink($heroPostID));
  $ads = new stdClass;
  $ads->leaderboard = getPostAd($zedPagePostID, 'ez2_ad_leaderboard');
  $ads->bigbox = getPostAd($zedPagePostID, 'ez2_ad_bigbox');
  $ads->a_leaderboard = getPostAd($zedPagePostID, 'ez2_ad_leaderboard');
  $ads->a_bigboxOne = getPostAd($zedPagePostID, 'ez2_ad_bigbox');
  $ads->a_bigboxTwo = getPostAd($zedPagePostID, 'ez2_ad_bigbox_two');
  $ads->b_leaderboard = getPostAd($zedPagePostID, 'ez2_ad_leaderboard_two');
  $ads->b_bigboxOne = getPostAd($zedPagePostID, 'ez2_ad_bigbox_b_one');
  $ads->b_bigboxTwo = getPostAd($zedPagePostID, 'ez2_ad_bigbox_b_two');
  $ads->c_leaderboard = getPostAd($zedPagePostID, 'ez2_ad_leaderboard_three');
  $ads->c_bigboxOne = getPostAd($zedPagePostID, 'ez2_ad_bigbox_c_one');
  $ads->c_bigboxTwo = getPostAd($zedPagePostID, 'ez2_ad_bigbox_c_two');
  $ads->sponsored_one = getPostAd($zedPagePostID, 'ez2_ad_sponsored_one');
  $ads->sponsored_two = getPostAd($zedPagePostID, 'ez2_ad_sponsored_two');
  $ads->interstitial = getPostAd($zedPagePostID, 'ez2_ad_sponsored_interstitial');
  $ads->wallpaper = getPostAd($zedPagePostID, 'ez2_ad_wallpaper');
  $ads->mobileInterstitial = getPostAd($zedPagePostID, 'ez2_ad_mobile_interstitial'); // this works on the home page, but does not on the post pages.
  $ads->mobileInterstitial2 = getPostAd($zedPagePostID, 'ez2_ad_mobile_interstitial_2');
  $ads->ez2_ad_desktop_interstitial = getPostAd($zedPagePostID, 'ez2_ad_desktop_interstitial'); //
  $someObj->ads = $ads;
  return $someObj;
}
//This is the section that returns the HERO for the Bookshelf Landing screen returns only 1 value with COPY on Bookshelf START


//This is the section that returns all posts tagged under the-big-read category under the Book Club posts to be displayed on Bookshelf START
function zm_getthefeedbookshelf()
{
  $args = array(
    'post_type' => 'zed_the_zoomer_book',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'post_date',
    'order' => 'DESC',
    'tax_query' => array(
      array(
        'taxonomy' => 'zed',
        'field' => 'slug',
        'terms' => 'the-big-read'
      )
    ),
  );
  $postsQuery = query_posts($args);
  $posts = array();
  while (have_posts()) : the_post();
    global $post;
    $somePost = new stdClass;
    $somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
    $somePost->subtitle = get_field('subtitle');
    $somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));
    $post_thumbnail_id = get_post_thumbnail_id($somePost->id);
    if (isset($post_thumbnail_id)) {
      $somePost->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
    } else {
      $somePost->thumbnail = false;
    }
    array_push($posts, $somePost);
  endwhile;
  return $posts;
}
//This is the section that returns all posts tagged under the-big-read category under the Book Club posts to be displayed on Bookshelf END


//This is the section that returns QUOTATION WITH ATTRIBUTION on Bookshelf START
function zed_getquoteforbookshelf()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('zed-book-club');
  $someObj->quotation = get_field('quotation', $zedPage->ID);
  $someObj->quotation_attribution = get_field('quotation_attribution', $zedPage->ID);
  return $someObj;
}
//This is the section that returns QUOTATION WITH ATTRIBUTION on Bookshelf END


//This is the section that returns the NOVEL ENCOUNTERS for the Bookshelf Landing screen returns only 1 value with COPY on Bookshelf START
function zed_getnovelencountersbookshelf()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('bookshelf-book-club');
  $zedPagePostID = $zedPage->ID;
  $novelPost = get_field('novel_post', $zedPage->ID);
  $novelBlurb = get_field('novel_blurb', $zedPage->ID);
  $someObj->novelBlurb = $novelBlurb;
  $novelPostID = $novelPost[0];
  $someObj->novelTitle = get_the_title($novelPostID);
  $post_thumbnail_id = get_post_thumbnail_id($novelPostID);
  $somePossiibleAlternateHeroImageID = get_field('book_club_featured_image', $novelPostID);
  if ($somePossiibleAlternateHeroImageID) {
    $someObj->thumbnail = zm_get_attachment_image($somePossiibleAlternateHeroImageID, 'huge720');
  } else {
    $someObj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
  }
  $someObj->permalink = str_replace(home_url(), "", get_permalink($novelPostID));
  return $someObj;
}
//This is the section that returns the NOVEL ENCOUNTERS for the Bookshelf Landing screen returns only 1 value with COPY on Bookshelf START


//This is the section that returns one posts for displaying under READ AND RECOMMENDED section on Bookshelf START
function zed_getreadandrecommendedforbookshelf()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('bookshelf-book-club');
  $someObj->title = get_field('read_and_recommendation_headline', $zedPage->ID);
  $readAndRecommendedSelections = get_field('read_and_recommended_selections', $zedPage->ID);
  $recommendations = array();
  foreach ($readAndRecommendedSelections as $postID) {
    $obj = new stdClass;
    $recommendedPost = get_post($postID);
    $obj->post_title = $recommendedPost->post_title;
    $obj->permalink = str_replace(home_url(), "", get_permalink($postID));
    $post_thumbnail_id = get_post_thumbnail_id($postID);
    $obj->read_and_recommended_blurb = get_field('read_and_recommended_blurb', $postID);
    if (isset($post_thumbnail_id)) {
      $obj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
    } else {
      $obj->thumbnail = false;
    }
    array_push($recommendations, $obj);
  }
  $someObj->recommendations = $recommendations;
  return $someObj;
}
//This is the section that returns one posts for displaying under READ AND RECOMMENDED section on Bookshelf END


//This is the section that returns all book related under READ AND RECOMMENDED section on Bookshelf START
function zed_getrelatedreadandrecommendedbookshelf()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('bookshelf-book-club');
  $someObj->read_and_recommended_suggestions = get_field('read_and_recommended_suggestions', $zedPage->ID);
  return $someObj;
}
//This is the section that returns all book related under READ AND RECOMMENDED section on Bookshelf END


//Similar to Listicles, this is the section that returns two posts for displaying under AT HOME section on Bookshelf START
function zm_getathomeforbookshelf()
{
  $zedPage = get_page_by_path('bookshelf-book-club');
  $atHome = get_field('at_home', $zedPage->ID);
  $atHomeStack = array();
  foreach ($atHome as $postID) {
    $someObj = new stdClass;
    $atHomePost = get_post($postID);
    $someObj->post_title = $atHomePost->post_title;
    $someObj->subtitle = get_field('subtitle', $postID);
    $someObj->permalink = str_replace(home_url(), "", get_permalink($postID));
    $someObj->category = getZedCatsbookshelf($atHomePost);
    $post_thumbnail_id = get_post_thumbnail_id($postID);
    if (isset($post_thumbnail_id)) {
      $someObj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'teaser_square');
    } else {
      $someObj->thumbnail = false;
    }
    array_push($atHomeStack, $someObj);
  }
  return $atHomeStack;
}
//Similar to Listicles, this is the section that returns two posts for displaying under AT HOME section on Bookshelf END