<?php
// we make a call to the horoscope url, and than pack it up into easily digestable json for react / redux to deal w on client side of spa.
function zm_gethoroscopes() {
  // $someHoroScopes = "Here are some horoscopes";

  // return $someHoroScopes;

  $url = "https://astrotwinsdaily.wordpress.com/feed/";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  $data = curl_exec($ch);

  if($data == false){
    echo 'Curl error: ' . curl_error($ch);
  }

  curl_close($ch);

  preg_match('/<content:encoded>(.*?)<\/content:encoded>/s', $data, $matches);

  $horoScopeString = $matches[0];
  $horoScopeString = stripcslashes($horoScopeString);

  $horoScopeString = strip_tags($horoScopeString, '<br>');
  $horoScopeString = html_entity_decode($horoScopeString);
  $someHoroScopes = Array();
  $symbols = array('aries','taurus','gemini','cancer','leo','virgo','libra','sagittarius','capricorn','aquarius','pisces','scorpio');
  // //$string = htmlspecialchars_decode($string);
  $horoScopeString = str_replace(']]>','',$horoScopeString);
  $horoScopeString = htmlspecialchars_decode($horoScopeString, ENT_HTML5);// works, sort of.

  foreach($symbols as $symbol){
    $pattern = '/<' . $symbol . '>(.*?)<\/' . $symbol . '>/s';

    preg_match($pattern, $horoScopeString, $someSignsHoroscopeArray);
    $someSignsHoroscope = $someSignsHoroscopeArray[0];
    // array_push($someSignsHoroscope);
    $someSignsHoroscope = str_replace('<'.$symbol . '>', '', $someSignsHoroscope);
    $someSignsHoroscope = str_replace('</'.$symbol . '>', '', $someSignsHoroscope);
    $someSignsHoroscope = str_replace('<br />', '', $someSignsHoroscope);
    $someSignsHoroscope = str_replace(PHP_EOL, '', $someSignsHoroscope);
    $someSignsHoroscope = str_replace(ucfirst($symbol).": ",'', $someSignsHoroscope);
    // $someHoroScopes[$symbol] = $someSignsHoroscope;
    // $someHoroScopes[$symbol] = $pattern;
    $someSignsHoroscopeObj = new stdClass;
    $someSignsHoroscopeObj->sign = $symbol;
    $someSignsHoroscopeObj->horoscope = $someSignsHoroscope;
    array_push($someHoroScopes, $someSignsHoroscopeObj);
  }


  preg_match('/<pubDate>(.*?)<\/pubDate>/s', $data, $pubDate);
  $pubDate = $pubDate[1];
  preg_match('/<title>(.*?)<\/title>/s', $data, $pubTitle);
  $pubTitle = $pubTitle[1];
  preg_match('/<link>(.*?)<\/link>/s', $data, $pubLink);
  $pubLink = $pubLink[1];

  $horoscopeData = new stdClass;
  $horoscopeData->pubDate = $pubDate;
  $horoscopeData->horoscopes = $someHoroScopes;
  $horoscopeData->pubTitle = $pubTitle;
  $horoscopeData->pubLink = $pubLink;


  // now where are we going to get this $result from?
  $someHoroscopePage = get_page_by_path('horoscopes');
	// $someFeaturedTags = get_field('ez2_featuredtags', $someHoroscopePage->ID);

  $ads = new stdClass;
  $ads->leaderboard = getPostAd($someHoroscopePage->id, 'ez2_ad_leaderboard');
  $ads->bigbox = getPostAd($someHoroscopePage->id, 'ez2_ad_bigbox');
  $ads->sponsored_one = getPostAd($someHoroscopePage->id, 'ez2_ad_sponsored_one');
  $ads->sponsored_two = getPostAd($someHoroscopePage->id, 'ez2_ad_sponsored_two');
  $ads->interstitial = getPostAd($someHoroscopePage->id, 'ez2_ad_sponsored_interstitial');
  $ads->wallpaper = getPostAd($someHoroscopePage->id, 'ez2_ad_wallpaper');
  $ads->mobileInterstitial = getPostAd($someHoroscopePage->id, 'ez2_ad_mobile_interstitial');// this works on the home page, but does not on the post pages.
  $ads->mobileInterstitial2 = getPostAd($someHoroscopePage->id, 'ez2_ad_mobile_interstitial_2');
  $ads->ez2_ad_desktop_interstitial = getPostAd($someHoroscopePage->id, 'ez2_ad_desktop_interstitial');//

  $horoscopeData->ads = $ads;

  // $horoscopeData->test = "grapes";
  return $horoscopeData;
  // Horoscopes provided by The <a href="http://www.astrostyle.com/The_AstroTwins/index.htm" target="_blank">AstroTwins</a>
}
