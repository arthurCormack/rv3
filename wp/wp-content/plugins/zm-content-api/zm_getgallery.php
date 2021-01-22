<?php
// we make a call to the horoscope url, and than pack it up into easily digestable json for react / redux to deal w on client side of spa.
function zm_getgallery($data) {

  $galleryPostID = $data['galleryid'];
  $someGallery = new stdClass;
  // get the post that has that id?

  $hasGallery = get_field('has_gallery', $galleryPostID);

  $gallerySlides = false;
  $someGallery->hasGallery = false;
  $someGallery->post = get_post($galleryPostID);
  if ($hasGallery) {// not false, and not null, or undefined

    $gallerySlides = [];
    $rawGallerySlides = get_field('gallery_slides', $galleryPostID);// not in the correct format that we need it in. we will need to massage it a bit.
    // return $rawGallerySlides;
    // $gallerySlides = $rawGallerySlides;
    // $someGallery->rawGallerySlides = $rawGallerySlides;
    // foreach($rawGallerySlides as $rawGallerySlide) {
    for($i=0; $i<count($rawGallerySlides); $i++) {
      $rawGallerySlide = $rawGallerySlides[$i];
      $slide = new stdClass;
      $imageID = $rawGallerySlide['id'];

      $someSlide = zm_get_attachment_image($imageID, 'huge');
      $slide->id = $imageID;// this just is the id, apparantly not the whole object. maybe thats the problem
      $slide->slide = $someSlide;
      $slide->thumbnail = zm_get_attachment_image($imageID, 'thumbnail');

      $imagePost = get_post($imageID);
      $imageCredit = false;
      if (class_exists('\Media_Credit')) {
        $imageCredit = \Media_Credit::get_plaintext( $imageID );
      }
      $slide->credit = $imageCredit;
      $slide->sharingImage = zm_get_attachment_image($imageID, 'sharing');
      $slide->meta = wp_get_attachment_metadata( $imageID );

      $slide->alt = get_post_meta($imageID , '_wp_attachment_image_alt', true);
      $slide->description = $imagePost->post_content;
      $slide->caption = $imagePost->post_excerpt;
      $slide->title = $imagePost->post_title;
      // $slide->description = get_post_meta($imageID , '_wp_attachment_image_description', true);
      // $slide->allMeta = get_post_meta($imageID);
      $slide->type = "Image";

      array_push($gallerySlides, $slide);


    //   $rawGallerySlide = $rawGallerySlides[$i];
    //   // $someType = gettype($rawGallerySlide);
    //   $slide = new stdClass;
    //   // $slide->type = $someType;
    //   // $slide->stuff = $rawGallerySlide;
    //   if ($rawGallerySlide['slide_type'] == "Image") {// the default
    //     $imageID = $rawGallerySlide['image_slide'];
    //
    //     $someThumbnail = zm_get_attachment_image($imageID, 'full');
    //     $slide->id = $imageID;// this just is the id, apparantly not the whole object. maybe thats the problem
    //     $slide->thumbnail = $someThumbnail;
    //
    //     $imagePost = get_post($imageID);
    //
    //     $slide->meta = wp_get_attachment_metadata( $imageID );
    //
    //     $slide->alt = get_post_meta($imageID , '_wp_attachment_image_alt', true);
    //     $slide->description = $imagePost->post_content;
    //     $slide->caption = $imagePost->post_excerpt;
    //     $slide->title = $imagePost->post_title;
    //     // $slide->description = get_post_meta($imageID , '_wp_attachment_image_description', true);
    //     // $slide->allMeta = get_post_meta($imageID);
    //     $slide->type = "Image";
    //     // $slide->id = $rawGallerySlide['id'];
    //     // $slide->image = zm_get_attachment_image($rawImageSlide['id'], 'full');//
    //     // $slide->name = $rawGallerySlide['name'];
    //     // $slide->date = $rawGallerySlide['date'];
    //     // $slide->url = $rawGallerySlide['url'];
    //     // $slide->title = $rawGallerySlide['title'];
    //     // $slide->description = $rawGallerySlide['description'];
    //     // $slide->caption = $rawGallerySlide['caption'];
    //     // $slide->alt = $rawGallerySlide['alt'];
    //     // $slide->mime_type = $rawGallerySlide['mime_type'];
    //     // $slide->width = $rawGallerySlide['width'];
    //     // $slide->height = $rawGallerySlide['height'];
    //
    //   } else if ($rawGallerySlide['slide_type'] == "Video") {
    //     $slide->type = "Video";
    //     $rawVideoSlideOEmbed = $rawGallerySlide['video_slide_oEmbed'];
    //     preg_match('/src="([^"]+)"/', $rawVideoSlideOEmbed, $match);
    //     $videoSlideEmbedURL = $match[1];
    //
    //     $slide->oEmbed = $rawVideoSlideOEmbed;
    //     $slide->oEmbedURL = $videoSlideEmbedURL;
    //   }
    //   array_push($gallerySlides, $slide);
    //   // array_push($gallerySlides, $rawGallerySlide);
    //
    }
  }
  $someGallery->hasGallery = $hasGallery;
  $someGallery->gallerySlides = $gallerySlides;// false of an array

  return $someGallery;

}
