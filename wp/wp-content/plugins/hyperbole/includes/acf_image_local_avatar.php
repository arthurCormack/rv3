<?php
/**
 * Use ACF image field as avatar
 * @author Mike Hemberger
 * @link http://thestizmedia.com/acf-pro-simple-local-avatars/
 * @uses ACF Pro image field (tested return value set as Array )
 */
add_filter('get_avatar', 'tsm_acf_profile_avatar', 10, 5);
function tsm_acf_profile_avatar( $avatar, $id_or_email, $size, $default, $alt ) {

    $user = '';
    
    // Get user by id or email
    if ( is_numeric( $id_or_email ) ) {

        $id   = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {
            $id   = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }

    } else {
        $user = get_user_by( 'email', $id_or_email );
    }

    if ( ! $user ) {
        return $avatar;
    }

    // Get the user id
    $user_id = $user->ID;

    // Get the file id
    $image_id = get_user_meta($user_id, 'tsm_local_avatar', true); // CHANGE TO YOUR FIELD NAME

    // Bail if we don't have a local avatar
    if ( ! $image_id ) {
        return $avatar;
    }

    // Get the file size
    $image_url  = wp_get_attachment_image_src( $image_id, 'full' ); // Set image size by name
    // Get the file url
    $avatar_url = $image_url[0];
    // Get the img markup
    $avatar = '<img alt="' . $alt . '" src="' . $avatar_url . '" class="avatar avatar-' . $size . '" height="' . '284' . '" width="' . '376' . '"/>';

    // Return our new avatar
    return $avatar;
}