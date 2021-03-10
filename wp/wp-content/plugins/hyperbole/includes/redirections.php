<?php
  // integration with the redirection plugin.
  // first, check to see if the plugin is present, and enabled.
  //
  // within the context of  a function please !

  function getRedirectForURL($url) {
    // class_exists
    // if (!defined(REDIRECTION_FILE)) {// a constant, defined by the redirection plugin; proof that it exists.
    // REDIRECTION_FILE diesn't work
    //   return "false";// bail out if it doesn't exist.
    // }
    if (!class_exists("Red_Item")) {
      return false;
    }

    // we want to try it with and without a preceding slash.
    // so first with a /
    if(substr($url, 0, 1) !== '/') {
      $url = '/' . $url;
    }
    $redirects = Red_Item::get_for_url($url);
    if (count($redirects)>0) {
      if ($redirects[0]->get_action_code() == '301') {
        return $redirects[0]->get_action_data();
      }
    }

    // now without a /
    $url = ltrim($url, '/');
    $redirects = Red_Item::get_for_url($url);
    if (count($redirects)>0) {
      if ($redirects[0]->get_action_code() == '301') {
        return $redirects[0]->get_action_data();
      }
    }

    return false;
  }
