<?php

// This is a generic function that is used to return the category of the Posts START
function getZedCatsBuzz($post)
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
// This is a generic function that is used to return the category of the Posts END


function zed_getheroforbuzz()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('buzz-book-club');
  $zedPagePostID = $zedPage->ID;
  $heroPost = get_field('hero_post', $zedPage->ID);
  $heroBlurb = get_field('hero_blurb', $zedPage->ID);
  $someObj->heroBlurb = $heroBlurb;
  $heroPostID = $heroPost[0];
  $someObj->category = getZedCatsBuzz($heroPostID);
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


function zm_getthefeedbuzz()
{
  $args = array(
    'post_type' => 'zed_the_zoomer_book',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'post_date',
    'order' => 'ASC',
    'tax_query' => array(
      array(
        'taxonomy' => 'zed',
        'field' => 'slug',
        'terms' => 'the-scroll'
      )
    ),
  );
  $postsQuery = query_posts($args);
  $posts = array();
  while (have_posts()) : the_post();
    global $post;
    $somePost = new stdClass;
    $somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
    $somePost->subtitle = get_field('subtitle', $postID);
    $somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));
    $post_thumbnail_id = get_post_thumbnail_id($somePost->id);
    $somePost->category = getZedCatsBuzz(the_post());
    
    if (isset($post_thumbnail_id)) {
      $somePost->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
    } else {
      $somePost->thumbnail = false;
    }
    array_push($posts, $somePost);
  endwhile;
  return $posts;
}


function zed_getquoteforbuzz()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('zed-book-club');
  $someObj->quotation = get_field('quotation', $zedPage->ID);
  $someObj->quotation_attribution = get_field('quotation_attribution', $zedPage->ID);
  return $someObj;
}


function zed_getopinionforbuzz()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('buzz-book-club');
  $opinionSelections = get_field('opinion_selections', $zedPage->ID);
  $opinions = array();
  foreach ($opinionSelections as $postID) {
    $obj = new stdClass;
    $opinionPost = get_post($postID);
    $obj->post_title = $opinionPost->post_title;
    $obj->permalink = str_replace(home_url(), "", get_permalink($postID));
    $post_thumbnail_id = get_post_thumbnail_id($postID);
    $obj->category = getZedCatsBuzz($opinionPost);
    $obj->subtitle = get_field('subtitle', $postID);

    if (isset($post_thumbnail_id)) {
      $obj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
    } else {
      $obj->thumbnail = false;
    }
    array_push($opinions, $obj);
  }
  $someObj->opinions = $opinions;
  return $someObj;
}

function zed_getlisticlesforbuzz()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('buzz-book-club');
  $listiclesSelections = get_field('the_listicles', $zedPage->ID);
  $listicles = array();
  foreach ($listiclesSelections as $postID) {
    $obj = new stdClass;
    $listiclesPost = get_post($postID);
    $obj->post_title = $listiclesPost->post_title;
    $obj->permalink = str_replace(home_url(), "", get_permalink($postID));
    $post_thumbnail_id = get_post_thumbnail_id($postID);
    if (isset($post_thumbnail_id)) {
      $obj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
    } else {
      $obj->thumbnail = false;
    }
    array_push($listicles, $obj);
  }
  $someObj->listicles = $listicles;
  return $someObj;
}

function zed_getbestsellers()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('buzz-book-club');
  $someObj->best_sellers = get_field('best_sellers_and_blockbusters', $zedPage->ID);
  $someObj->top_of_the_charts = get_field('top_of_the_charts', $zedPage->ID);
  $someObj->day_in_history = get_field('day_in_history', $zedPage->ID);
  return $someObj;
}