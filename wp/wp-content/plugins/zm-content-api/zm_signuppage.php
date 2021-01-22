<?php

function zm_signuppage() {

  // return "fun times";
  $signUpPage = get_page_by_path( 'sign-up' );
  // $homePage = get_page_by_path( 'home' );
  // console.log($signUpPage);
  // return $signUpPage;
  $currentMagCoverObj = get_field('signupcoverimage', $signUpPage->ID);
  // $currentMagCoverUrl = $currentMagCoverObj['url'];
  // $currentMagCoverUrl = $currentMagCoverObj->url;// produces an error ...
  if ($currentMagCoverObj) {
    $currentMagCoverUrl = $currentMagCoverObj['url'];
    $someObj = new stdClass;
    $someObj->mag_cover = $currentMagCoverUrl;
    return $someObj;
  } else {
    return null;
  }

  // $currentMagCover = S3Substitute($currentMagCoverUrl);
  //
  // // $currentMagObj = array();
  // $someObj = new stdClass;
  // // $someObj->mag_cover = $currentMagCover;
  // $someObj->mag_cover = $currentMagCoverUrl;
  // // array_push($currentMagObj, $someObj);
  // return $someObj;

}
