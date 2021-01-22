<?php

function zm_gethomepoll() {

  $homePage = get_page_by_path( 'home' );
  $homePollObj = get_field('poll_embed_code', $homePage->ID);

    $someObj = new stdClass;

    $someObj->home_poll = $homePollObj;

  return $someObj;

}

function zm_gethomepollref() {
  //
  $homePage = get_page_by_path( 'home' ); // not title, path, which is a slug, and therefore lowercase
  $homePollObj = get_field('poll', $homePage->ID);
  // now this will simply be the id of the poll.
  // how do we get at it's stuff?
}
