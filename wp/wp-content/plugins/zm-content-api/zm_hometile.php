<?php

function zm_gethometile() {

  $homePage = get_page_by_path( 'Home' );
  $homeTile = get_field('home_tile', $homePage->ID);

    $someObj = new stdClass;
    $someObj->hometile = $homeTile['sizes']['tile'];

  return $someObj;

}
