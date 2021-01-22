<?php

function zed_getwelcomecontent()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('banter-book-club');
  $someObj->editorImage = get_field('editor_image', $zedPage->ID);
  $someObj->editorContent = get_field('editor_content', $zedPage->ID);
  return $someObj;
}

function zed_getcalendarcontent()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('banter-book-club');
  $someObj->calendarContent = get_field('calendar_content', $zedPage->ID);
  return $someObj;
}

function zed_getpodcastcontent()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('banter-book-club');  
  $someObj->podcastImage = get_field('podcast_image', $zedPage->ID);
  $someObj->podcastTitle = get_field('podcast_title', $zedPage->ID);
  $someObj->podcastDescription = get_field('podcast_description', $zedPage->ID);
  $someObj->podcastRedirect = get_field('podcast_redirect', $zedPage->ID);
  return $someObj;
}

function zed_getvotecontent()
{
  $someObj = new stdClass;
  $zedPage = get_page_by_path('banter-book-club');
  $postIDThatHasVotingListicle = get_field('voting_listicle', $zedPage->ID);
  if (is_array($postIDThatHasVotingListicle) && count($postIDThatHasVotingListicle) > 0) {
    $postIDThatHasVotingListicle = $postIDThatHasVotingListicle[0];
  }
  // what do we need here? the slug/post_name to be used as the voteId
  $postThatHasVotingListicle = get_post($postIDThatHasVotingListicle);
  // $somePost->book_listicle = get_field('book_listicle');
  // $somePost->listicle_mode = get_field('listicle_mode');
  $someObj->voteID = $postThatHasVotingListicle->post_name;
  $someObj->listicle_mode = get_field('listicle_mode', $postIDThatHasVotingListicle);
  $someObj->book_listicle = get_field('book_listicle', $postIDThatHasVotingListicle);
  $someObj->post = $postThatHasVotingListicle;
  $someObj->zedPage = $zedPage;
  $someObj->postIDThatHasVotingListicle = $postIDThatHasVotingListicle;
  return $someObj;
}