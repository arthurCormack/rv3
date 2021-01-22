<?php

function zm_getideacity() {

  $homePage = get_page_by_path( 'Home' );
  $ideacityEmbedCode = get_field('ideacity_livestream_embed_code', $homePage->ID);
  $ideacityTitle = get_field('ideacity_livestream_title', $homePage->ID);
  $ideacityParagraph = get_field('ideacity_livestream_paragraph', $homePage->ID);

    $someObj = new stdClass;
    $someObj->ideacity_livestream_embed_code = $ideacityEmbedCode;
    $someObj->ideacity_livestream_title = $ideacityTitle;
    $someObj->ideacity_livestream_paragraph = $ideacityParagraph;

  return $someObj;

}
