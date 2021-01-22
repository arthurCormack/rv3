<?php

function zm_download_our_app() {

  // return "fun times";
  $downloadOurAppPage = get_page_by_path( 'download-our-app' );
  $copyAboutIssue = get_field('copy_about_issue', $downloadOurAppPage->ID);
  $articleImage = get_field('article_image', $downloadOurAppPage->ID);
  $buttonGroup = get_field('button_links', $downloadOurAppPage->ID);
  $footerContent = get_field('footer_content', $downloadOurAppPage->ID);
  $greyAreaContent = get_field('grey_area', $downloadOurAppPage->ID);
  $redAreaContent = get_field('red_header', $downloadOurAppPage->ID);
  

  $someObj = new stdClass;
  $someObj->copy_about_issue = $copyAboutIssue;
  $someObj->article_image = $articleImage;
  $someObj->button_description = $buttonGroup['button_description'];
  $someObj->apple_store_link = $buttonGroup['apple_store_link'];
  $someObj->google_play_link = $buttonGroup['google_play_link'];
  $someObj->desktop_icon_link = $buttonGroup['desktop_icon_link'];
  $someObj->apple_plus_icon_link = $buttonGroup['zoomer_apple_plus_link'];
  $someObj->subscribe_button_link = $buttonGroup['subscribe_button_link'];
  $someObj->footer_content = $footerContent;
  $someObj->grey_area = $greyAreaContent;
  $someObj->red_header = $redAreaContent;
  return $someObj;
}
