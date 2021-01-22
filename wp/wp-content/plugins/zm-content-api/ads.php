<?php

function getPostAd($whichPostID, $whichAdUnit) {
  $somePossiblePostAd = null;
  // remember that $somePossiblePostAd will be an array, not an object.
  $somePossiblePostAd = get_field($whichAdUnit, $whichPostID);
  if (isset($somePossiblePostAd) && is_string($somePossiblePostAd) && $somePossiblePostAd !== '') {
    // return "WTF?!";
    return $somePossiblePostAd;

  } else if (is_array($somePossiblePostAd) && !empty($somePossiblePostAd['id'])) {


    return $somePossiblePostAd;
  }

  // if we are still here, then lets look at tags, and then categories
  $somePostTags = get_the_tags($whichPostID);
  if($somePostTags) {
    foreach($somePostTags as $tag) {
      $someTagIDString = 'post_tag_' . $tag->term_id;
      $somePossiblePostAd = get_field($whichAdUnit, $someTagIDString);
      if (isset($somePossiblePostAd) && is_string($somePossiblePostAd) && $somePossiblePostAd !== '') {
        return $somePossiblePostAd;// takes the first one found, and breaks us out of the foreach
      } else if (is_array($somePossiblePostAd) && !empty($somePossiblePostAd['id'])) {

        return $somePossiblePostAd;
      }
    }
  }

  // if we are still here, then look at categories.
  $someCategories = get_the_category($whichPostID);
  // look at Yoast's primary category first
  if (  class_exists('WPSEO_Primary_Term') ) {

		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', $whichPostID );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$category = get_term( $wpseo_primary_term );
    // return $category;
		if (is_wp_error($category)) {
			// do nothing if an error is returned

		} else {
			// Yoast Primary category
      // the category object ($category) returned by WPSEO_Primary_Term is is structured differently than the regular get_the_category call.
      // so we use $category->term_id instead of $category->cat_ID
      $someCategoryIDString = 'category_'.$category->term_id;

      $somePossiblePostAd = get_field($whichAdUnit, $someCategoryIDString);
      // return $somePossiblePostAd;
      if (isset($somePossiblePostAd) && is_string($somePossiblePostAd) && $somePossiblePostAd !== '') {
        return $somePossiblePostAd;// takes the first one found, and breaks us out of the foreach

      } else if (is_array($somePossiblePostAd) && is_string($somePossiblePostAd['id']) && $somePossiblePostAd['id'] !== '') {

        return $somePossiblePostAd;
      } else {
        // don't return here.
      }
		}
  } else {

    foreach($someCategories AS $category) {
      $someCategoryIDString = 'category_'.$category->cat_ID;
      $somePossiblePostAd = get_field($whichAdUnit, $someCategoryIDString);
      return $somePossiblePostAd;
      if (isset($somePossiblePostAd) && is_string($somePossiblePostAd) && $somePossiblePostAd !== '') {
        return $somePossiblePostAd;// takes the first one found, and breaks us out of the foreach
      } else if (is_array($somePossiblePostAd) && !empty($somePossiblePostAd['id'])) {

        return $somePossiblePostAd;
      }
    }
  }

  // if we are still here, then look at all-purpose run of house ad.
  // where does that ad unit get stored? Uncategorized
  $category = get_category_by_slug('general');
  if (isset($category) && $category !== null) {
    $someCategoryIDString = 'category_'.$category->cat_ID;
    $somePossiblePostAd = get_field($whichAdUnit, $someCategoryIDString);

    return $somePossiblePostAd;
    if (isset($somePossiblePostAd) && is_string($somePossiblePostAd) && $somePossiblePostAd !== '') {
      return $somePossiblePostAd;// takes the first one found, and breaks us out of the foreach
    } else if (is_array($somePossiblePostAd) ) {
      return $somePossiblePostAd;
    }
  }
  // and if we are still here , then we are SOL.
  return false;
}


// Author ads

function getAuthorAd($whichAuthorID, $whichAdUnit) {

  // if we are still here, then look at all-purpose run of house ad.
  // where does that ad unit get stored? Uncategorized
  $category = get_category_by_slug('general');
  if (isset($category) && $category !== null) {
    $someCategoryIDString = 'category_'.$category->cat_ID;
    $somePossiblePostAd = get_field($whichAdUnit, $someCategoryIDString);
    if (isset($somePossiblePostAd) && $somePossiblePostAd !== '') {
      return $somePossiblePostAd;// takes the first one found, and breaks us out of the foreach
    }
  }
  // and if we are still here , then we are SOL.
  return false;
}




function getTermAd($whichTermID, $whichAdUnit) {
  $somePossiblePostAd = null;

  $somePossibleTermAd = get_field($whichAdUnit, $whichTermID);
  
  if (!empty($somePossibleTermAd)) {
    // but it could be an empty fieldset! and not have actual values for the fields inside!

    if (is_array($somePossibleTermAd)) {
      if ($somePossibleTermAd['id'] !== '') {
        return $somePossibleTermAd;
      } else {
        // return $somePossibleTermAd;// is it that there is no id set here, and so it fails out?
      }
    }
  }


  // optionally, we could look at a categories parent's ad tags, if we really wanted to.

  // if we are still here, then look at all-purpose run of house ad.
  // where does that ad unit get stored? Uncategorized
  $category = get_category_by_slug('general');
  if (isset($category) && $category !== null) {
    $someCategoryIDString = 'category_'.$category->cat_ID;
    $somePossibleTermAd = get_field($whichAdUnit, $someCategoryIDString);
    if (isset($somePossibleTermAd) && $somePossibleTermAd !== '') {
      return $somePossibleTermAd;// takes the first one found, and breaks us out of the foreach
    }
  }
  // and if we are still here , then we are SOL.
  return false;
}
