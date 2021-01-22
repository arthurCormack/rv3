<?php

function zm_gettiles() {
  $someFeaturedStuff = array();

  $args = array (
  'post_type' => array ('ez2_tiles'),
  // 'orderby' => array( 'menu_order' => 'ASC'),
  'posts_per_page' => -1
  );

  $tileQuery = new WP_Query($args);

 while ( $tileQuery->have_posts() ) : $tileQuery->the_post();
    $someObj = new stdClass;
    $someObj->post_title = get_the_title();
    // $largeTileImage = get_the_post_thumbnail_url($tileQuery->ID, 'large');
    // $someObj->largeTileImage = S3Substitute($largeTileImage);
    $post_thumbnail_id = get_post_thumbnail_id( $tileQuery->ID );
    // $someThumbnail = zm_get_attachment_image($post_thumbnail_id, 'large');

    $someObj->homeTile = zm_get_attachment_image($post_thumbnail_id , 'tile');
    $someObj->largeTileImage = zm_get_attachment_image($post_thumbnail_id , 'large');
    $someObj->mediumTileImage = zm_get_attachment_image($post_thumbnail_id , 'thumbnail');
    $someObj->squareTile = zm_get_attachment_image($post_thumbnail_id , 'teaser_square');
    $someObj->permalink = str_replace(home_url(), "", get_permalink());// gives us a relative url!
    $someObj->tiles_permalink = "/tiles";// gives us a relative url!
    array_push($someFeaturedStuff, $someObj);

 endwhile;
wp_reset_postdata();
return $someFeaturedStuff;
}
