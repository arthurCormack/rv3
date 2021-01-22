<?php

function zm_getquote() {

  $homePage = get_page_by_path( 'Home' );

  $someObj = new stdClass;

  $someObj->quote = get_field('quote', $homePage->ID);

  // we need an api call to piggy back on, so that we can will wp_request data with stuff like ads, so that we can display the correct ads on the homepage.
  // and this looks like a goood candidate!

  $ads = new stdClass;
	$ads->leaderboard = getPostAd($homePage->ID, 'ez2_ad_leaderboard');
	$ads->bigbox = getPostAd($homePage->ID, 'ez2_ad_bigbox');

	$ads->sponsored_one = getPostAd($homePage->ID, 'ez2_ad_sponsored_one');
	$ads->sponsored_two = getPostAd($homePage->ID, 'ez2_ad_sponsored_two');
	$ads->interstitial = getPostAd($homePage->ID, 'ez2_ad_sponsored_interstitial');
  $ads->mobileInterstitial2 = getPostAd($homePage->ID, 'ez2_ad_mobile_interstitial_2');

	$ads->wallpaper = getPostAd($homePage->ID, 'ez2_ad_wallpaper');
  // $ads->mobileInterstitial = getPostAd($homePage->ID, 'ez2_ad_mobile_interstitialGroup');
  $ads->mobileInterstitial = getPostAd($homePage->ID, 'ez2_ad_mobile_interstitial');

  $someObj->ads = $ads;

  return $someObj;

}

function getBetterQuote() {
  $someQuoteObj = new stdClass;
  $someQuoteObj->test = 'apples';
  return $someQuoteObj;
}
