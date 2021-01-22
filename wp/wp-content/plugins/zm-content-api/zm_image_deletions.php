<?php

  function featuredImageSwitch($post_id) {
    global $wpdb;
    $someObj = new stdClass;

    $thumbnailIDForPostRequest = $wpdb->get_row("SELECT meta_id FROM remove_verbotten_featuredimages WHERE post_id = '$post_id'");
    $meta_id =  $thumbnailIDForPostRequest->meta_id;
    
    $updateResult = $wpdb->update( 'wp_postmeta', array( 'meta_value' => '507117'), array('meta_id'=>$meta_id));
    $someObj->meta_id = $meta_id;
    $someObj->updateResult = $updateResult;
    if (isset($updateResult) && $updateResult == 1) {
      $updateRemovedQuery = $wpdb->update('remove_verbotten_featuredimages', array('removed' => 1), array('post_id'=>$post_id));
    }
    return $someObj;
  };

  function snipVerbottenImagesFromPost($post_id) {
    // Omri says that this is eligible for SR&ED ( SHRED ) Credits
    // make a query to the verbottenimage_posts table
    // question: what are the verbotten_image_id s? remeber there might be multiple
    // now find their positiions iin the post_content, and cycle through them stating with last first ( so as to not upset subsequent positiions)
    global $wpdb;
    $someObj = new stdClass;


    // The post featured image link stored in the WordPress database is stored in wp_postmeta with a meta_key called _thumbnail_id.
    // in wp_posts_meta, wwhere meta_key = _thumbnail_id and meta_value = the verbotten_image_id and the post_id is the post_id ... then we need to 
    // should we delete? or should we instead change the meta key from _thumbnail_id ==> verbottenimage_id and also do a substitution ... add a new record 
    // ... mostly identical to the previous one, putting _thumbnail_id --> a known substitute image. Alternatively, we could store all the data pertaining to the deleted verbotten images someplace else. LEt's say, in a different table altogether.
   

    // get the data for that post_id
    // verbottenimage_posts
    // check the thumbnail id, if the thumbnail id is one of the verbotten images, then fix it.

    
      // do the backup + featured image substitution first.


    $verbottenimage_post_images_query_result = $wpdb->get_results("SELECT * FROM verbottenimage_posts WHERE verbottenimage_posts.post_id = {$post_id}");
 
    if (count($verbottenimage_post_images_query_result) > 0 ) {



      // change the meta key from _thumbnail_id in wp_post_meta 507117

      

      $somePostContent = $verbottenimage_post_images_query_result[0]->post_content;

      // get the $imgPositions
      preg_match_all('/(<img)/', $somePostContent, $imgPositions, PREG_OFFSET_CAPTURE);

      $snips = Array();
      // find the position if the needle in the hayStack
      forEach($verbottenimage_post_images_query_result AS $item) {
        $needle = 'wp-image-' . $item->verbotten_image_id . '"';
        // strpos ( string $haystack , mixed $needle [, int $offset = 0 ] ) : int
        $needlePos = strpos($somePostContent, $needle);
        if ($needlePos === false) {
          $needle = 'wp-image-' . $item->verbotten_image_id . ' ';
          $needlePos = strpos($somePostContent, $needle);
        }
        // and then reset needle back to original
        $needle = 'wp-image-' . $item->verbotten_image_id;// it is not used, just going pack in the json response

        $snipObj = new stdClass;
        $snipObj->id = $item->verbotten_image_id;
        $snipObj->identifier = $needle;

        $snipObj->needlePos = $needlePos;
        // $snipObj->imgPositions = $imgPositions;
       
        $possibleSnipStartPositions = Array();
        forEach($imgPositions[0] AS $possibleSnipStartObj) {// for some reason there are two identical arrays in an array?! so we jsut use the first one, which gives us an array of 2-item arrays, the second of which [1], is the positiion
          array_push($possibleSnipStartPositions, $possibleSnipStartObj[1]);
        }
        
        $snipStart = 0;
        forEach($possibleSnipStartPositions AS $pos) {
          if($pos < $needlePos && $pos > $snipStart) {
            $snipStart = $pos;
          }
        }
       
        $snipObj->snipStart = $snipStart;// the the highest in imgPositions that is below $needlePos
        $snipObj->snipEnd = strpos($somePostContent, '>', $needlePos) + 1; // the lowest > character that is above $needlePos. +1 because the '>' is precisely 1 character in length
        array_push($snips, $snipObj);

      }
      // remove all the snips where !needlePos
      foreach ($snips as $key => $snipObj) {
      if ($snipObj->needlePos === false) {
          unset($snips[$key]);
      }
}
      // make sure that the $snips are ordered in order of their needlePos
      usort($snips, "sortOnReverseNeedlePos");

      // and now we start snipping. We have $somePostContent already to start with.


		// this works

      $snippedContent = $somePostContent;// start with somePostContent, but allow progressive snips to occur.
      forEach($snips AS $snipObj) {
        // make the html comment that has the edit link in it. And sandwhich iit between the snips
        $editLinkComment = "<!-- AnImageWasRemoved edit-link: https://www.everythingzoomer.com/wp-admin/upload.php?item={$snipObj->id} -->";
        $firstHalf = substr($snippedContent, 0, $snipObj->snipStart);
        $secondHalf = substr($snippedContent, $snipObj->snipEnd);
        $snippedContent = $firstHalf . $editLinkComment . $secondHalf;
      }
      // $someObj->snippedContent = $snippedContent;
      // now we are done with all the snipping, and are ready to make the query to update.

      // and now make the query 
      // $verbottenimage_post_images_query_result = $wpdb->get_results("SELECT * FROM verbottenimage_posts WHERE verbottenimage_posts.post_id = {$post_id}");
      
      // $snipQuery = $wpdb->get_results("UPDATE verbottenimage_posts SET fixed_post_content = {$snippedContent} WHERE verbottenimage_posts.post_id = {$post_id}");// this does them all at once, instead of doing it sequentially. it will do them all, multiple times, but whatever, it will be inefficient, but still work
      $snipQuery = $wpdb->update(
        'verbottenimage_posts',
        array( 
          'fixed_post_content' => $snippedContent
        ),
        array(
          'post_id' => $post_id  
        )
      );

      // now, let's actually snip the post_content from wp_posts
      $replaceActualPostContentQuery = $wpdb->update(
        'wp_posts',
        array( 
          'post_content' => $snippedContent
        ),
        array(
          'ID' => $post_id  
        )
      );

      // and lastly, let's update  the removed for each of the verbottenimages .. only after te post has been updated.
       $reeplacedQuery = $wpdb->update(
        'verbottenimage_posts',
        array( 
          'removed' => 1
        ),
        array(
          'post_id' => $post_id  
        )
      );


      $someObj->snipQuery = $snipQuery;
    }
    
    // $numberOfImages = substr_count($somePostContent, '<img');
   
    $someObj->snips = $snips;
    return $someObj;
    // return $verbottenimage_post_images_query_result;
  }

  // simple helper function to sort array on reverse needlePos
  function sortOnReverseNeedlePos($a, $b) {
    if ($a->needlePos == $b->needlePos) {
        return 0;
    }
    return ($a->needlePos > $b->needlePos) ? -1 : 1;
  }

  function zm_imagedeletionscheck ($data) {
    // this is what gets called with a GET
  }
  function zm_imagedeletions($data, $otherStuff = 'bupkis') {
    // this gets called with a POST, and gives us an opportunity to check that the secret is in place, and if not then do nothing.
    // $testObj = new stdClass;
    // $testObj->data = $data;
    // $testObj->otherStuff = $otherStuff;
    // return $testObj;
    // return $data;
    // if (!isset($data['secret'] || $data['secret'] !== 'topsecret')) {
    //   return false; 
    // }
    $start_time=microtime(true);

    // so ... what are we doing here?
    // this is the callback function ... so we do a query of a few rows, and then snip out the offending images, and
    // figuring out how to snip it will be more than just a simple regex.
    // we need to find the position of wp-image-vvvvv where vvvvv is the verbotten_image_id
    // 
    // 
      // $content = "this is something with an <img src=\"test.png\"/> in it.";
  //   $content = preg_replace("/<img[^>]+\>/i", "(image) ", $content); 
  //   echo $content;
  //   $posts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish'
  //  AND post_type='post' ORDER BY comment_count DESC LIMIT 0,4")
    $limit = 1;
    global $wpdb;
    $someObj = new stdClass;
    
    // $someObj->numImages = $wpdb->get_results("SELECT COUNT(*) FROM verbottenimage_posts WHERE verbottenimage_posts.removed <> 1;");
    $nextPostsWithVerbottenImages = $wpdb->get_results("SELECT DISTINCT verbottenimage_posts.post_id FROM verbottenimage_posts WHERE verbottenimage_posts.removed <> 1 ORDER BY post_id ASC LIMIT {$limit};");
    
    // $someObj->nextPostsWithVerbottenImages = count($nextPostsWithVerbottenImages) > 0 ? $nextPostsWithVerbottenImages : false;
    $someObj->nextPostsWithVerbottenImages = $nextPostsWithVerbottenImages;

    $imagesSnipped = Array();
    forEach($nextPostsWithVerbottenImages AS $nextPost) {
      $nextPostID = $nextPost->post_id;
      $imagesSnipped[$nextPostID] = snipVerbottenImagesFromPost($nextPostID);// returns an array of all the verbotten_image_id snipped from th post
    }
    // $someObj->nextPostsWithVerbottenImages = 

    $someObj->imagesSnipped = $imagesSnipped;

    $someObj->featuredImageSwitch = featuredImageSwitch($nextPostID);
    // $someObj->nextPostWithVerbottenImages = $nextPostWithVerbottenImages;
    // $someObj->nextPostWithVerbottenImages = $nextPostWithVerbottenImages[0]->post_id;

    // $numberOfVerbottenImagesQueryResult = $wpdb->get_results("SELECT COUNT(*) FROM verbottenimage_posts WHERE verbottenimage_posts.removed <> 1;");
    // $numberOfRemainingVerbottenImages = count($numberOfVerbottenImagesQueryResult) > 0 ? $numberOfVerbottenImagesQueryResult[0] : 0;
    // $someObj->numberOfRemainingVerbottenImages = $numberOfRemainingVerbottenImages;

    // determine the next post to work on.
    // $someObj->numPosts = $wpdb->get_results("SELECT COUNT(DISTINCT post_id) FROM verbottenimage_posts WHERE verbottenimage_posts.removed <> 1;");
    
    $end_time = microtime(true);
    $execution_time = bcsub($end_time, $start_time, 4);
    $someObj->execution_time = $execution_time;
    $someObj->memory_peak = memory_get_peak_usage(false) / (1024 * 1024) . 'M';
    return $someObj;
  }

