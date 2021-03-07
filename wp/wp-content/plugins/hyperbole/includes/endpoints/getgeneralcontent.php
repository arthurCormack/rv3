<?php
function getgeneralcontent($request) {
  
  $responseObj = new stdClass;
  $responseObj->cheese = "Stilton";
  
  $permalink = $request['permalink'];
  
  // inspect thhe permalink, and figure out what to do with it.
  // we count the number of items in the permalink
  $permalinkSlugs = explode($permalink, '/');
  // the number of slugs gives use the first clue.
  // it's the same opiniionated guesses thhat occur at the client. BUt then if a result is not found, based on guess, then we make 
  // a look for redirections, and then make a guess. If we find a redirection, or a guess, then we issue a resonse that can be used to do a 301 redirect. And if we still can't find anythhing, then we return a 404

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

