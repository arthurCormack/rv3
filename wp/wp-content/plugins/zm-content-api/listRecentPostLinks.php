<?php
  //
  function getRecentPostLinks($data) {
    $offset = isset($data['offset']) ? (int)$data['offset'] : 0;
    $range = isset($data['range']) ? (int)$data['range'] : 20;
    $args = array(
  		'post_type' => 'post',
  		'posts_per_page' => $range,
      'orderby' => 'post_date', 'order' => 'DESC', 'offset' => $offset,
  		'post_status' => 'publish'
  	);

    wp_reset_query();
  	$postsQuery = query_posts( $args );
    $posts = array();
  	while(have_posts()) : the_post();
  		global $post;
  		$somePost = new stdClass;
  		$somePost->post_title = wp_specialchars_decode(get_the_title(), ENT_QUOTES);
      $somePost->id = get_the_ID();
      $somePost->permalink = str_replace(home_url(), "", get_permalink($somePost->id));// gives us a relative url!
      array_push($posts, $somePost);
  	endwhile;
    return $posts;
  }
