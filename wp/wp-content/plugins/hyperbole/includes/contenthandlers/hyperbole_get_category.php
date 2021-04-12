<?php
  //
  function hyperbole_get_category($slugPath) {
    // use get_category_by_path. https://developer.wordpress.org/reference/functions/get_category_by_path/
    // $slugPath could be  something like health of a hierarchical path like /health/diets/ketogenic of /money/investing/bitcoin
    // 
    $somePossibleCategory = get_category_by_path($slugPath);
    if (!$somePossibleCategory) {
      return false;
    }
    return $somePossibleCategory;// will need to add aditional  fields, like ads to produce the appropriate data shape / format
  }