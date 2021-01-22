<?php
// this gets all the book club posts.
// we can also get a zm_getbookclubpost ... for an individual one.


function sandwichContentAroundListicle($post_content)
{
  $sandwhich = new stdClass;
  $components = explode("<!--listicle-->", $post_content);
  $sandwhich->post_content_top = force_balance_tags($components[0]);
  $sandwhich->post_content_bottom = force_balance_tags($components[1]);
  return $sandwhich;
}
function zm_getbookclub($data)
{
  // $data will have a postslug
  $postslug = $data['postslug'];
  $args = array(
    'post_type' => 'zed_the_zoomer_book',
    // 'posts_per_page' => -1,
    'name' => $postslug,
    'post_status' => 'publish',
    'orderby' => 'post_date', 'order' => 'DESC'
  );
  //
  // alternate featured image
  // add photo credit + caption
  // listicle_mode
  // format blurb
  // mobile ad switch ... use is_mobile() function to switch out for mobile unit.

  $postsQuery = query_posts($args);
  // return $postsQuery;
  $posts = array();
  while (have_posts()) : the_post();
    // array_push($posts, $post);
    global $post;
    $somePost = new stdClass;
    $somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
    $somePost->subtitle = get_field('subtitle');
    $somePost->sponsoredPost = get_field('sponsored_post');

    $post_content = apply_filters('the_content', $post->post_content);

    $somePost->post_content = $post_content;
    $somePost->relatedContent =
      $hasBookList = get_field('has_book_listicle');
    if ($hasBookList && strpos($post_content, '<!--listicle-->') !== -1) { // always use this approach
      $sandwichContent = sandwichContentAroundListicle($post_content);
      $somePost->sandwichContent = $sandwichContent;
    }
    // now ... two questions ... 
    // if $hasBookList ... then check the post_content for an occurance of '<!--listicle-->'
    // and if found, then snip the content before, and after and put them into post_content_top and post_content_bottom.
    // nee dto stydy what was don for multipage on the single post pages.

    $somePost->post_date = date('F jS, Y', strtotime($post->post_date));
    $somePost->author = get_the_author();
    $somePost->authorID = get_the_author_meta('ID');
    $somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));
    $hasBookList = get_field('has_book_listicle');
    $somePost->has_book_listicle = $hasBookList;
    // add subtitle field, and authorname

    $somePost->book_listicle = get_field('book_listicle');
    $somePost->listicle_mode = get_field('listicle_mode');
    $category = getZedCats($post);
    $category_slug = $category[0]->slug;
    $somePost->cats = $category;

    if (has_post_thumbnail()) {
      $post_thumbnail_id = get_post_thumbnail_id($somePost->id);
      $someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
      $somePost->thumbnail = $someThumbnail;
    } else if (get_field('book_club_featured_image')) {
      $post_thumbnail_id = get_field('book_club_featured_image');
      $someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
      $somePost->thumbnail = $someThumbnail;
    } else {
      $somePost->thumbnail = false;
    }
    $somePost->relatedPosts = zm_getsinglecategoryposts($category_slug);
    $somePost->authorFullname = getAuthorFullName();

    $ads = new stdClass;
    $ads->a_leaderboard = getPostAd($somePost->id, 'ez2_ad_leaderboard');
    $ads->a_bigboxOne = getPostAd($somePost->id, 'ez2_ad_bigbox');
    $ads->a_bigboxTwo = getPostAd($somePost->id, 'ez2_ad_bigbox_two');
    $ads->b_leaderboard = getPostAd($somePost->id, 'ez2_ad_leaderboard_two');
    $ads->b_bigboxOne = getPostAd($somePost->id, 'ez2_ad_bigbox_b_one');
    $ads->b_bigboxTwo = getPostAd($somePost->id, 'ez2_ad_bigbox_b_two');
    $ads->c_leaderboard = getPostAd($somePost->id, 'ez2_ad_leaderboard_three');
    $ads->c_bigboxOne = getPostAd($somePost->id, 'ez2_ad_bigbox_c_one');
    $ads->c_bigboxTwo = getPostAd($somePost->id, 'ez2_ad_bigbox_c_two');
    $ads->sponsored_one = getPostAd($somePost->id, 'ez2_ad_sponsored_one');
    $ads->sponsored_two = getPostAd($somePost->id, 'ez2_ad_sponsored_two');
    $ads->interstitial = getPostAd($somePost->id, 'ez2_ad_sponsored_interstitial');
    $ads->wallpaper = getPostAd($somePost->id, 'ez2_ad_wallpaper');
    $ads->mobileInterstitial = getPostAd($somePost->id, 'ez2_ad_mobile_interstitial'); // this works on the home page, but does not on the post pages.
    $ads->mobileInterstitial2 = getPostAd($somePost->id, 'ez2_ad_mobile_interstitial_2');
    $ads->ez2_ad_desktop_interstitial = getPostAd($somePost->id, 'ez2_ad_desktop_interstitial'); //

    $somePost->ads = $ads;
    // what else do we need here? we need to know any categories that the post might be in.
    // we of course require the post content, and the thumbnail.
    // the author. Any other important custom fields that might be required?
    // $somePost = zm_getArchivePost();
    array_push($posts, $somePost);
  endwhile;

  $somePost->theFeed = zm_getthefeed();
  array_push($posts, $somePost);

  $somePost->onOurRadar = zed_onOurRadar();
  array_push($posts, $somePost);

  return $posts[0];
  // return ["The Cat in the Hat", "The Sleep Book", "The Sneetches"];
}

function zed_gethero($data)
{
  // similar to how things work on the front page.
  // we look for the hero post (which also gives us the image), the hero tagline, and the hero blurb
  // hero_post, hero_blurb, hero_tagline

  // ads will piggyback onto this!

  $someObj = new stdClass;

  $zedPage = get_page_by_path('zed-book-club');
  $zedPagePostID = $zedPage->ID;
  $heroPost = get_field('hero_post', $zedPage->ID);
  $heroBlurb = get_field('hero_blurb', $zedPage->ID);
  $someObj->heroBlurb = $heroBlurb;
  $someObj->bigreadsuggestions = get_field('big_read_suggestions', $zedPage->ID);

  $heroPostID = $heroPost[0];
  $someObj->heroTitle = get_the_title($heroPostID);
  $post_thumbnail_id = get_post_thumbnail_id($heroPostID);
  if (isset($post_thumbnail_id)) {
    $someObj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
  } else {
    $someObj->thumbnail = false;
  }
  // $somePost = get_post($heroPostID);


  $somePossiibleAlternateHeroImageID = get_field('hero_image', $zedPage->ID);
  // $someObj->somePossiibleAlternateHeroImageID = $somePossiibleAlternagitteHeroImageID;
  if ($somePossiibleAlternateHeroImageID) {
    //
    $someObj->alternateHero = zm_get_attachment_image($somePossiibleAlternateHeroImageID, 'huge720');
  } else {
    $someObj->alternateHero = false;
  }


  $someObj->permalink = str_replace(home_url(), "", get_permalink($heroPostID));

  $ads = new stdClass;

  $ads->leaderboard = getPostAd($zedPagePostID, 'ez2_ad_leaderboard');
  $ads->bigbox = getPostAd($zedPagePostID, 'ez2_ad_bigbox');
  // now we add two additional sets!.
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


function getAuthorFullName()
{
  $fname = get_the_author_meta('first_name');
  $lname = get_the_author_meta('last_name');
  $full_name = '';
  if (empty($fname)) {
    $full_name = $lname;
  } elseif (empty($lname)) {
    $full_name = $fname;
  } else {
    //both first name and last name are present
    $full_name = "{$fname} {$lname}";
  }
  return $full_name;
}

function getZedCats($post)
{
  //return false;
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
    // $someObj->cats = $someMinimalCats;
  } else {
    // $someObj->cats = false;
  }
  // return $someCat;
  return $someMinimalCats;
}
function zed_getclubhouse($data)
{

  $zedPage = get_page_by_path('zed-book-club');

  $club_house_pick_one = get_field('club_house_pick_one', $zedPage->ID);
  $club_house_pick_two = get_field('club_house_pick_two', $zedPage->ID);
  $club_house_pick_three = get_field('club_house_pick_three', $zedPage->ID);
  $club_house_pick_four = get_field('club_house_pick_four', $zedPage->ID);
  $someObj = new stdClass;

  // PICK ONE
  $someObj->club_house_pick_one = new stdClass;
  $someObj->club_house_pick_one->tagline = $club_house_pick_one['tagline'];
  $someObj->club_house_pick_one->selection = new stdClass;
  $postID = $club_house_pick_one['selection'][0];
  $selectionPost = get_post($postID);
  $someObj->club_house_pick_one->selection->post_title = $selectionPost->post_title;
  $post_thumbnail_id = get_post_thumbnail_id($postID);
  if (isset($post_thumbnail_id)) {
    $someObj->club_house_pick_one->selection->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
  } else {
    $someObj->club_house_pick_one->selection->thumbnail = false;
  }
  $someObj->club_house_pick_one->excerpt = zmExcerpt($postID, 100, true, false, false, "", "");
  $someObj->club_house_pick_one->permalink = str_replace(home_url(), "", get_permalink($postID));
  $someObj->club_house_pick_one->cats = getZedCats($selectionPost);

  // PICK TWO
  $someObj->club_house_pick_two = new stdClass;
  $someObj->club_house_pick_two->tagline = $club_house_pick_two['tagline'];
  $someObj->club_house_pick_two->selection = new stdClass;
  $postID = $club_house_pick_two['selection'][0];
  $selectionPost = get_post($postID);
  $someObj->club_house_pick_two->selection->post_title = $selectionPost->post_title;
  $post_thumbnail_id = get_post_thumbnail_id($postID);
  if (isset($post_thumbnail_id)) {
    $someObj->club_house_pick_two->selection->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
  } else {
    $someObj->club_house_pick_two->selection->thumbnail = false;
  }
  $someObj->club_house_pick_two->excerpt = zmExcerpt($postID, 100, true, false, false, "", "");
  $someObj->club_house_pick_two->permalink = str_replace(home_url(), "", get_permalink($postID));
  $someObj->club_house_pick_two->cats = getZedCats($selectionPost);

  // PICK THREE
  $someObj->club_house_pick_three = new stdClass;
  $someObj->club_house_pick_three->tagline = $club_house_pick_three['tagline'];
  $someObj->club_house_pick_three->selection = new stdClass;
  $postID = $club_house_pick_three['selection'][0];
  $selectionPost = get_post($postID);
  $someObj->club_house_pick_three->selection->post_title = $selectionPost->post_title;
  $post_thumbnail_id = get_post_thumbnail_id($postID);
  if (isset($post_thumbnail_id)) {
    $someObj->club_house_pick_three->selection->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
  } else {
    $someObj->club_house_pick_three->selection->thumbnail = false;
  }
  $someObj->club_house_pick_three->excerpt = zmExcerpt($postID, 100, true, false, false, "", "");
  $someObj->club_house_pick_three->permalink = str_replace(home_url(), "", get_permalink($postID));
  $someObj->club_house_pick_three->cats = getZedCats($selectionPost);

  // PICK FOUR
  $someObj->club_house_pick_four = new stdClass;
  $someObj->club_house_pick_four->tagline = $club_house_pick_four['tagline'];
  $someObj->club_house_pick_four->selection = new stdClass;
  $postID = $club_house_pick_four['selection'][0];
  $selectionPost = get_post($postID);
  $someObj->club_house_pick_four->selection->post_title = $selectionPost->post_title;
  $post_thumbnail_id = get_post_thumbnail_id($postID);
  if (isset($post_thumbnail_id)) {
    $someObj->club_house_pick_four->selection->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
  } else {
    $someObj->club_house_pick_four->selection->thumbnail = false;
  }
  $someObj->club_house_pick_four->excerpt = zmExcerpt($postID, 100, true, false, false, "", "");
  $someObj->club_house_pick_four->permalink = str_replace(home_url(), "", get_permalink($postID));
  $someObj->club_house_pick_four->cats = getZedCats($selectionPost);

  return $someObj;
}

function zed_getshelflife($data)
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('zed-book-club');
  // shelf_life_title, shelf_life_article
  $shelf_life_title = get_field('shelf_life_title', $zedPage->ID);
  // $shelf_life_article = get_field('shelf_life_article', $zedPage->ID);//
  $postID = get_field('shelf_life_article', $zedPage->ID)[0];
  $shelf_life_article = get_post($postID);
  $someObj->post_title = $shelf_life_article->post_title;
  $someObj->shelf_life_title = get_field('shelf_life_title', $zedPage->ID);

  $post_thumbnail_id = get_post_thumbnail_id($postID);
  if (isset($post_thumbnail_id)) {
    $someObj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
  } else {
    $someObj->thumbnail = false;
  }
  $someObj->permalink = str_replace(home_url(), "", get_permalink($postID));

  return $someObj;
}

function zed_getauthorspotlight($data)
{
  // $someObj = new stdClass;
  $zedPage = get_page_by_path('zed-book-club');
  $authorSpotlights = get_field('author_spotlight_selections', $zedPage->ID);

  $spotlights = array();

  foreach ($authorSpotlights as $postID) {
    // $authorSpotlightsPostID = 
    $someObj = new stdClass;
    $authorPost = get_post($postID);
    $post_thumbnail_id = get_post_thumbnail_id($postID);
    if (isset($post_thumbnail_id)) {
      $someObj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'teaser_square');
    } else {
      $someObj->thumbnail = false;
    }
    $someObj->post_title = $authorPost->post_title;
    $someObj->permalink = str_replace(home_url(), "", get_permalink($postID));
    array_push($spotlights, $someObj);
  }
  return $spotlights;
}

function zed_getreadandrecommended($data)
{
  // add blurb field.
  $someObj = new stdClass;
  $zedPage = get_page_by_path('zed-book-club');
  $someObj->title = get_field('read_and_recommended', $zedPage->ID);
  $someObj->readothers = get_field('read_and_recommended_suggestions', $zedPage->ID);
  $readAndRecommendedSelections = get_field('read_and_recommended_selections', $zedPage->ID); // an array of post_titles   
  $recommendations = array();
  foreach ($readAndRecommendedSelections as $postID) {
    $obj = new stdClass;
    $recommendedPost = get_post($postID);
    $obj->post_title = $recommendedPost->post_title;
    $obj->permalink = str_replace(home_url(), "", get_permalink($postID));
    $post_thumbnail_id = get_post_thumbnail_id($postID);
    if (isset($post_thumbnail_id)) {
      $obj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
    } else {
      $obj->thumbnail = false;
    }
    array_push($recommendations, $obj);
  }
  $someObj->recommendations = $recommendations;
  return $someObj; // SEE OUR FAVES
}

function zed_getopinion($data)
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('zed-book-club');
  $optionSelections = get_field('opinion_selections', $zedPage->ID);
  $someObj->opinion_blurb = get_field('opinion_blurb', $zedPage->ID);
  $recommendations = array();
  foreach ($optionSelections as $postID) {
    $obj = new stdClass;
    $recommendedPost = get_post($postID);
    $obj->post_title = $recommendedPost->post_title;
    $obj->permalink = str_replace(home_url(), "", get_permalink($postID));
    $post_thumbnail_id = get_post_thumbnail_id($postID);
    $somePossiibleAlternateHeroImageID = get_field('book_club_featured_image', $postID);
    if ($somePossiibleAlternateHeroImageID) {
      $obj->thumbnail = zm_get_attachment_image($somePossiibleAlternateHeroImageID, 'huge720');
    } else {
      $obj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');;
    }
    // if (isset($post_thumbnail_id)) {
    //   $obj->thumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
    // } else {
    //   $obj->thumbnail = false;
    // }
    array_push($recommendations, $obj);
  }
  $someObj->opinions = $recommendations;
  return $someObj;
}

//quotation quotation_attribution

function zed_getquote()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('zed-book-club');
  $someObj->quotation = get_field('quotation', $zedPage->ID);
  $someObj->quotation_attribution = get_field('quotation_attribution', $zedPage->ID);
  return $someObj;
}

function zm_getthefeed()
{
  $postslug = $data['postslug'];
  $args = array(
    'post_type' => 'zed_the_zoomer_book',
    'posts_per_page' => -1,
    'name' => $postslug,
    'post_status' => 'publish',
    'orderby' => 'post_date', 'order' => 'DESC',
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
    $somePost->subtitle = get_field('subtitle');
    $somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));
    array_push($posts, $somePost);
  endwhile;
  return $posts;
}

function zed_entertainingIdeas($data)
{
  $zedPage = get_page_by_path('zed-book-club');
  $morePosts = get_field('entertaining_ideas', $zedPage->ID);
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
    $someObj->sponsoredPost = get_field('sponsored_post', $morePost);
    $someObj->cats = getZedCats($morePost);
    $someObj->permalink = str_replace(home_url(), "", get_permalink($postID));
    array_push($morePostStack, $someObj);
  }
  return $morePostStack;
}

function zed_onOurRadar()
{
  $zedPage = get_page_by_path('zed-book-club');
  $morePosts = get_field('on_our_radar', $zedPage->ID);
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


function zm_getbookclubsublandingcontent($data)
{
  $postslug = $data['postslug'];
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
        'terms' => $postslug
      )
    ),
  );
  $postsQuery = query_posts($args);
  $posts = array();
  while (have_posts()) : the_post();
    global $post;
    $somePost = new stdClass;
    $somePost->cat = getZedCats($somePost->id);
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


function zm_getsinglecategoryposts($data)
{
  $postslug = $data['postslug'];
  $post = get_page_by_path($postslug, OBJECT, 'post');
  $args = array(
    'post_type' => 'zed_the_zoomer_book',
    'name' => $postslug,
    'post_status' => 'publish',
    'orderby' => 'post_date',
    'order' => 'DESC'
  );

  $postsQuery = query_posts($args);
  $category = "";
  while (have_posts()) : the_post();
    global $post;
    $postObj = new stdClass;
    $category = getZedCats($post)[0]->slug;
    $categoryName = getZedCats($post)[0]->name;
  endwhile;

  $args = array(
    'post_type' => 'zed_the_zoomer_book',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post__not_in' => array($post->ID),
    'tax_query' => array(
      array(
        'taxonomy' => 'zed',
        'field' => 'slug',
        'terms' => $category
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
    $somePost->categoryName = $categoryName;
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
