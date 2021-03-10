<?php
function getgeneralcontent($request) {
  
  // $responseObj = new stdClass;
  // $responseObj->cheese = "Stilton";
  
  $permalink = rtrim($request['permalink'], "/");

  // inspect thhe permalink, and figure out what to do with it.
  // we count the number of items in the permalink
  // $decodedPermalink = urldecode($permalink);
  // $responseObj->permalink = $permalink;
  // $responseObj->decodedPermalink = $decodedPermalink;

  $permalinkSlugs = explode('/', $permalink);
  $slugCount = count($permalinkSlugs);
  // $responseObj->slugCount = $slugCount;
  // the number of slugs gives use the first clue.
  // it's the same opiniionated guesses thhat occur at the client. BUt then if a result is not found, based on guess, then we make 
  // a look for redirections, and then make a guess. If we find a redirection, or a guess, then we issue a resonse that can be used to do a 301 redirect. And if we still can't find anythhing, then we return a 404


  // so this is kind of a big deal here

  // this is the single endpoint for all content.
  // we recieve the full permalink as an arguement here. how does it arrive? encodeURIComponent'd base64 encoded?
  // whatever shape the date response is it has to be consistant, and cofrom to the expectations of the client. IN shummary - there is a contract of expectations about what comes in and goes out
  // the $responseObj has to have { resultItem: { permalink, data, } (is this the best name? requestItem?  requestedItem? data?), ads, nextItem}
  // does it matter how we nest the data? Yes. The client is going to store it in the redux state, and we want to make it easy for them.
  // is it easy now? It's ok. sufficient. the content, the ads, the nextItem. Is it always clear that there is a nextItem? what about on an archivePage?
  // only if it is a continuous thing.
  // do we want to call posts in trances of three? like a codon in s gene sequence? maybe. for noow, let's deal with them one at a time. if we want to do trances of three, then we could set t flag in the request asking for it.
  // 
  // so ... first step. analyze the $permalinkSlugs to determine what kind of thing this probably is. 
  // query the thing. if found, return that.
  // then look for a redirect. if found return that.
  // then do a guess permalink, if it was for a post. if found, return that redirect.
  // then return a 404.

  // if the permalink slugs is [] ... then it is the home page. and what do we return there?
  // a bunch of little buckets. but initially, on the SSR, just the featured four ... or enough to create the first meaningful render.
  if ($slugCount === 0) {
    // then we are dealing with the home  page
    // then what part of the home page's assorment of content are we requesting? the initial ssr load, or other specific buckets?
    // do we want to get them all at once? or as part of a collection?
  } else {
    // is there a redirect? getRedirectForURL is defined in redirections.php
    // this will take care of all redirections for all possible kinds of content!
    $somePossibleRedirectLocation = getRedirectForURL($somePermalink);
    if ($somePossibleRedirectLocation) {
      $redirectionResponse = new stdClass;
      $redirectionResponse->redirectLocation = somePossibleRedirectLocation;
      // it's not that it was notFound, it's that it has a redirect. We need to check this first. Whether it is found or not is irrelevalnt,because we found a deliberate rediirectioon first.
      return $redirectionResponse;
    }
    // do we look at each kind of thing first? or at the number of items?
    // how about the kind of thing first.
    if ($slugCount === 1)  {
      // it could be a page or a category
      $singleSlug = $permalinkSlugs[0];
      // look for page first
      $somePossiblePage = get_singlepage($singleSlug);
      if ($somePossiblePage) {
        return $somePossiblePage;
      }
      // then category
      // but we might want to be making a 
      $somePossibleCategory = get_category($singleSlug);
      if ($somePossibleCategory) {
        return $somePossibleCategory;
      }
      // then best guess or nothing
      $response = new stdClass;
      $response->notFound = true;
      $response->redirectLocation = guess_404_permalink($singleSlug); // something, or false
      return $response;
    } 
    // if ($slugCount >=1 && $slugCount <= 3) {
    //   // then it is a category
    // }
    

    // then we are dealing with a tag, or a category or a page.
    // if it is precisely 1, then let's check to see if it is a page first.
    // actually, let's first chheck to see if there is a redirect first.
    // and then see if it is a page.
    // and if not a page, then check to see if it is an archive.
    if ($slugCount >= 1) {
      // then it could be a category or a tag ... or something special. like a book club post or someting like that.
      $firstSlug = $permalinkSlugs[0];
      if($firstSlug === 'tag' || $firstSlug === 'tags') {
        $someTag = $permalinkSlugs[1];
        // and we have a tag to deal with
      } else if ($firstSlug === 'zed-book-club') {
        // what else might we be dealing with?
        // on ez, zed-book-club posts. the type is zed_the_zoomer_book, but the slug prefix is zed-book-club
        // like in https://www.everythingzoomer.com/zed-book-club/2021/03/05/10-brilliant-books-to-read-this-international-womens-day/
        // if there are more custom post types that need to be handled with a different response here, then this is the place to do it. we can easily add additional
        // else if clauses for different posible $firstSlugsl
        // it  could be zed-book-club page, or it could be a book club post
      } else {
        // then we are dealing with something that is not a special case 
        // things like pages
        if ($slugCount >= 1 && $slugCount <= 3) {
          // the assumption is that it is a hierarchical category page.
          $somePossibleCategory = get_category($permalink);// we use the whole permalink, not just a part of the url path. there mightbe duplicate parts. for example, /recipes/christmas and /holidays/christmas would both have the same slug christmas
          if ($somePossibleCategory) {
            return $somePossibleCategory;
          }
          // then best guess or nothing
          $response = new stdClass;
          $response->notFound = true;
          $response->redirectLocation = guess_404_permalink($singleSlug); // something, or false
          return $response;
        } else {
          // we assume that  we are dealing with a specific post here instead.
          // get_singlepage
          $somePossibleSinglePage = get_singlepage($permalink);
          if ($somePossibleSinglePage) {
            return $somePossibleSinglePage;
          }
          // then best guess or nothing
          $response = new stdClass;
          $response->notFound = true;
          $response->redirectLocation = guess_404_permalink($singleSlug);// something, or false
        }
      }
    }
  }
  return $responseObj;
}
// function getgeneralcontent($request) {
  
//   $categoryslug = $request['catslug'];
//   $yearslug = $request['yearslug'];
//   $montslug = $request['monthslug'];
//   $dayslug = $request['dayslug'];
//   $postslug = $request['postslug'];
//   $args = array(
//       'post_type' => 'post',
//       'post_status' => 'publish',
//       'name' => $postslug,
//       'date_query' => array(
//       array(
//           'year' => $yearslug,
//           'month' => $montslug,
//           'day' => $dayslug,
//       ),
//     ),
//   );
//   $somePosts = Array();
//   $postsQuery = query_posts( $args );
//   if ( count($postsQuery) === 0 ) {
//     $result->caardd->notFound = true;
//     $somePossibleRedirectLocation = getRedirectForURL($request['slug']);
//     if ($somePossibleRedirectLocation !== false) {
//       $result->caardd->redirectLocation = $somePossibleRedirectLocation;
//     } else {
//       $result->caardd->redirectLocation = guess_404_permalink($request['slug']);// something, or false
//     }
//     return $result;
//   }
// 	foreach( $postsQuery as $post ) {
//     $postObj = new stdClass;
//     $postData = get_post($post);// wtf?
//     $postObj->title = $postData->post_title;
//     $author = get_the_author_meta('display_name', $post->post_author);
//     $postObj->author = $author;
//     // $postObj->excerpt = $postData->post_excerpt;
//     $postObj->excerpt = zmExcerpt($post->ID, 100, true, false, false, "", "", "...");
    

//     $postObj->content = apply_filters( 'the_content', $postData->post_content);
//     $postObj->post_permalink = str_replace(home_url(), "", get_permalink($postData->ID));// gives us a relative url!
//     $imageId = get_post_thumbnail_id($post);
//     $postObj->image = zr_get_attachment_image($imageId, 'full');
//     // if ( has_post_thumbnail() ) {
//     //   $post_thumbnail_id = get_post_thumbnail_id( $postData->id );
//     //   $someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'full');
//     //   $postObj->post_thumbnail_id = $post_thumbnail_id;

//     //   $postObj->image = $someThumbnail;
//     // } else {
//     //   $postObj->image = 'false';
//     // }

//     // $imageId = get_post_thumbnail_id($post);
//     // $postObj->imageId =  $imageId;
//     // if ($imageId === 0) {
//     //   $postObj->image = false;
//     // } else {
//     //   $postObj->image = zr_get_attachment_image($imageId, 'thumb_360p');
//     // }

//     $publishedDate = date('M d, Y',strtotime($post->post_date));
//     $postObj->post_date = $publishedDate;

//     // add any tags and categories that we might need to use, in order to do the load more bit, iff necccesary
//     return $postObj;

//   }
// }

// function getpostsbypostcategory($request) {
//   $slug = $request['slug'];
//   $post = get_page_by_path($slug, OBJECT, 'post');
//   $loadCount = $request['loadcount'];
//   $posts = query_posts(array(
//     // 'posts_per_page'	=> -1,
//     'post_type'		=> 'post',
//     'post_status' => 'publish',
//     'cat' => get_the_category($post->ID)[0]->cat_ID,
//     'post__not_in' => array($post->ID),
//   ));
//   $posts_details = Array();
//   foreach( $posts as $post ) {
//     $postObj = new stdClass;
//     $postObj->title = $post->post_title;
//     $postObj->content = mb_strimwidth($post->post_content, 0, 201, '...');
//     $postObj->post_excerpt = $post->post_excerpt;
//     $postObj->post_created = date('M d, Y', strtotime($post->post_modified));
//     $postObj->post_permalink = str_replace(home_url(), "", get_permalink($post->ID));
//     array_push($posts_details, $postObj);
//   }

//   $detectLastItem = Array();
//     foreach($posts_details as $data) {
//       if( $data === end($posts_details) ) {
//         $data->lastItem = true;
//         array_push($detectLastItem, $data);
//       } else {
//         $data->lastItem = false;
//         array_push($detectLastItem, $data);
//       }
//     }
//   $range = 3; // adds 3 object in an array at a time
//   $getNumOfObjects = $range * $loadCount;
//   $newList = array_slice($posts_details, 0, $getNumOfObjects);
//   return $newList;
// }

