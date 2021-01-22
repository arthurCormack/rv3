<?php

function zm_getcurrentmag() {

  $homePage = get_page_by_path( 'Home' );
  $currentMagCoverObj = get_field('current_magazine_cover', $homePage->ID);
  $currentMagCoverUrl = $currentMagCoverObj['url'];
  $currentMagCover = S3Substitute($currentMagCoverUrl);

  // $currentMagObj = array();

    $someObj = new stdClass;

    $someObj->mag_cover = $currentMagCover;

    // array_push($currentMagObj, $someObj); 

  return $someObj;

}
