<?php
define ('AUTHOR_POST_CHUNKSIZE', 12);

function zm_getauthor($data) {

  $response = new stdClass;
  $someAuthorSlug = $data['authorslug'];
  $user = get_user_by('slug', $someAuthorSlug );
  // return $user;

  wp_reset_query();

  $somePosts = array();
  $args = array(
    'post_status' => 'publish',
    'posts_per_page' => '12',
    'order' => 'DESC',
    'author_name' => $someAuthorSlug,
    'offset' => 0,
    'range' => AUTHOR_POST_CHUNKSIZE
    // 'author' => $someAuthorSlug->ID,
    // 'search' => 'Display Name',
  );

  $the_query = new WP_Query( $args );
  while($the_query->have_posts()) : $the_query->the_post();
    // global $post;
    $somePost = new stdClass;
    $somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
    $somePost->post_content = apply_filters( 'the_content', get_the_content());
    $somePost->id = get_the_ID();

    // $somePost->post_date = $post->post_date;
    $somePost->author = get_the_author();
    // $somePost->post_slug = $post->post_name;
    $somePost->excerpt = zmExcerpt($somePost->id, 100, true, false, false, '', '');
    if ( has_post_thumbnail($somePost->id) ) {
      $theThumb = get_the_post_thumbnail_url($somePost->id, 'teaser_square');
    }

    $somePost->post_thumb = S3Substitute($theThumb);

    $post_thumbnail_id = get_post_thumbnail_id($somePost->id );
    $someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'teaser_square');
    $somePost->image = $someThumbnail;
    $somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));
    $someFeaturedTags = zm_getFeaturedTags();
    $somePost->tags = get_the_tags();
  	$somePost->featuredTags = zm_getAPostsTagsInSetOfTags($somePost->id, $someFeaturedTags);;
    array_push($somePosts, $somePost);
  endwhile;
  $response->posts = $somePosts;
  // $response->bio = get_the_author_meta('description', $somePosts->ID);
  // if (strlen($someContent) > $limit) {

  $limit = 1000;
  $intialBioText = get_the_author_meta('description', $user->ID);// we should be passing a user id, not a post id!
	$last_space = strrpos( substr( strip_tags($intialBioText), 0, $limit), ' ');
	// Trim
	$trimmed_text = substr(strip_tags($intialBioText), 0, $limit);

  $response->bio = $trimmed_text;

  $response->authorName = get_the_author('display_name', $user->ID);
  $response->id = $user->ID;
  $someImageID = get_field('user_image', "user_{$user->ID}");
  $someThumbnail = zm_get_attachment_image($someImageID, 'teaser_square');
  if ( has_post_thumbnail($user->ID) ) {
    $theThumb = get_the_post_thumbnail_url($somePost->id, 'teaser_square');
  }

  $response->post_thumb = S3Substitute($theThumb);
  $response->image = $someThumbnail;
  $response->authorSlug = $someAuthorSlug;
  $response->count_user_posts = count_user_posts($user->ID, 'post', true);//

  $ads = new stdClass;
  $ads->leaderboard = getAuthorAd($user->ID, 'ez2_ad_leaderboard');
  $ads->bigbox = getAuthorAd($user->ID, 'ez2_ad_bigbox');
  $ads->sponsored_one = getAuthorAd($user->ID, 'ez2_ad_sponsored_one');
  $ads->sponsored_two = getAuthorAd($user->ID, 'ez2_ad_sponsored_two');
  $ads->interstitial = getAuthorAd($user->ID, 'ez2_ad_sponsored_interstitial');
  $ads->wallpaper = getAuthorAd($user->ID, 'ez2_ad_wallpaper');
  $ads->mobileInterstitial = getAuthorAd($user->ID, 'ez2_ad_mobile_interstitial');
  $response->ads = $ads;
  return $response;




}
function zm_getauthorposts() {
  $chunkNum = $data['chunknum'];
  $response = new stdClass;
  $someAuthorSlug = $data['authorslug'];
  $user = get_user_by('slug', $someAuthorSlug );
  // return $user;
  // $the_user_id = $someAuthorSlug->ID;
  // $the_thumb_id = get_user_meta($someAuthorSlug->ID);
  wp_reset_query();


  $somePosts = array();
  $args = array(
    'post_status' => 'publish',
    'order' => 'DESC',
    'posts_per_page' => '12',
    'author_name' => $someAuthorSlug,
    'offset' => $chunkNum * AUTHOR_POST_CHUNKSIZE,
    'range' => AUTHOR_POST_CHUNKSIZE
    // 'author' => $someAuthorSlug->ID,
    // 'search' => 'Display Name',
  );

  $the_query = new WP_Query( $args );
  while($the_query->have_posts()) : $the_query->the_post();
    $somePost = new stdClass;
    $somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
    $somePost->post_content = apply_filters( 'the_content', get_the_content());
    $somePost->id = get_the_ID();
    $somePost->post_date = $post->post_date;
    $somePost->author = get_the_author();
    $somePost->post_slug = $post->post_name;
    $somePost->excerpt = zmExcerpt($somePost->id, 100, true, false, false, '', '');
    $post_thumbnail_id = get_post_thumbnail_id($somePost->id );
    $someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'teaser_square');
    $somePost->image = $someThumbnail;
    $somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));
    $someFeaturedTags = zm_getFeaturedTags();
    $somePost->tags = get_the_tags();
  	$somePost->featuredTags = zm_getAPostsTagsInSetOfTags($somePost->id, $someFeaturedTags);;

    array_push($somePosts, $somePost);
  endwhile;

  return $somePosts;




}
