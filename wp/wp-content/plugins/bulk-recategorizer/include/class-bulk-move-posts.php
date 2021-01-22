<?php
/**
 * Utility class for moving posts
 *
 * @package Bulk_Move
 * @since   1.3.1
 * @author  Arthur Cormack
 */

if ( !defined('ABSPATH') )
    exit; // Shhh

class Bulk_Move_Posts {

    /**
     * Render move categories box
     *
     * @since 1.0
     */
    public static function render_move_category_box() {

        if ( Bulk_Move_Util::is_posts_box_hidden( Bulk_Move::BOX_CATEGORY ) ) {
            printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-move' ), 'tools.php?page=' . Bulk_Move::POSTS_PAGE_SLUG );
            return;
        }
?>
        <!-- Category Start-->
        <h4><?php _e( 'On the left side, select the category whose post you want to move. In the right side select the category to which you want the posts to be moved.', 'bulk-move' ) ?></h4>

        <fieldset class="options">
		<table class="optiontable">
            <tr>
                <td scope="row" >
<?php
                wp_dropdown_categories( array(
                    'name'         => 'smbm_mc_selected_cat',
                    'show_count'   => TRUE,
                    'hierarchical' => TRUE,
                    'orderby'      => 'NAME',
                    'hide_empty'   => FALSE
                ) );
?>
                ==>
                </td>
                <td scope="row" >
<?php
                wp_dropdown_categories( array(
                    'name'             => 'smbm_mc_mapped_cat',
                    'show_count'       => TRUE,
                    'hierarchical'     => TRUE,
                    'orderby'          => 'NAME',
                    'hide_empty'       => FALSE,
                    'show_option_none' => __( 'Remove Category', 'bulk-move' )
                ) );
?>
                </td>
            </tr>

		</table>
        <p>
            <?php _e( 'If the post contains other categories, then', 'bulk-move' ); ?>
            <input type="radio" name="smbm_mc_overwrite" value="overwrite" checked><?php _e ( 'Remove them', 'bulk-move' ); ?>
            <input type="radio" name="smbm_mc_overwrite" value="no-overwrite"><?php _e ( "Don't remove them", 'bulk-move' ); ?>
        </p>

		</fieldset>
        <?php /*<p class="submit">
            <button type="submit" name="bm_action" value="move_cats" class="button-primary"><?php _e( 'Bulk Move (normal)', 'bulk-move' ) ?>&raquo;</button>
        </p>*/ ?>
        <p class="submit">
            <button name="bm_ajax_action" value="move_cats" class="button-primary bm_ajax_action" data-moveType="cats-cats"><?php _e( 'GO!', 'bulk-move' ) ?>&raquo;</button>
        </p>
        <div class="bm_ajax_status"></div>
        <!-- Category end-->
<?php
    }

    /**
     * Move posts from one category to another
     *
     * @static
     * @access public
     * @since  1.2.0
     */
    public static function move_cats() {
        if ( check_admin_referer( 'sm-bulk-move-posts', 'sm-bulk-move-posts-nonce' ) ) {

            do_action( 'bm_pre_request_handler' );

            $wp_query = new WP_Query;
            $bm       = BULK_MOVE();

            // move by cats
            $old_cat = absint( $_POST['smbm_mc_selected_cat'] );
            $new_cat = ( $_POST['smbm_mc_mapped_cat'] == -1 ) ? -1 : absint( $_POST['smbm_mc_mapped_cat'] );

            $posts   = $wp_query->query(array(
                'category__in' => array( $old_cat ),
                'post_type'    => 'post',
                'nopaging'     => 'true'
            ) );

            foreach ( $posts as $post ) {
                $current_cats = array_diff( wp_get_post_categories( $post->ID ), array( $old_cat ) );

                if ( $new_cat != -1 ) {
                    if ( isset( $_POST['smbm_mc_overwrite'] ) && 'overwrite' == $_POST['smbm_mc_overwrite'] ) {
                        // Remove old categories
                        $current_cats = array( $new_cat );
                    } else {
                        // Add to existing categories
                        $current_cats[] = $new_cat;
                    }
                }

                if ( count( $current_cats ) == 0 ) {
                    $current_cats = array( get_option( 'default_category' ) );
                }
                $current_cats = array_values( $current_cats );
                wp_update_post(array(
                    'ID'            => $post->ID,
                    'post_category' => $current_cats
                ) );
            }

            $bm->msg = sprintf( _n( 'Moved %d post from the selected category', 'Moved %d posts from the selected category' , count( $posts ), 'bulk-move' ), count( $posts ) );
        }
    }


		/**
     * Ajax Move single posts from one category to another
     *
     * @static
     * @access public
     * @since  1.3.0
     */

		public static function ajax_move_cats() {
			//how do we do check_admin_referer( 'sm-bulk-move-posts', 'sm-bulk-move-posts-nonce' ) ?
			$wp_query = new WP_Query;
			// move by cats
			$old_cat = intval(sanitize_text_field($_POST['from']));
			$new_cat = intval(sanitize_text_field($_POST['to']));
			$postArray = Bulk_Move_Posts::getPostIdsArray();

			$posts   = $wp_query->query(array(
					'category__in' => array( $old_cat ),
					'post_type'    => 'post',
					'nopaging'     => 'true',
					'post__in'      => $postArray
			) );
			$processedItems = Array();

			foreach ( $posts as $post ) {
					$current_cats = array_diff( wp_get_post_categories( $post->ID ), array( $old_cat ) );
					if ( $new_cat != -1 ) {
							if ( isset( $_POST['doWeOverwrite'] ) && 'overwrite' == $_POST['doWeOverwrite'] ) {
									// Remove old categories
									$current_cats = array( $new_cat );
							} else {
									// Add to existing categories
									$current_cats[] = $new_cat;
							}
					}
					if ( count( $current_cats ) == 0 ) {
							$current_cats = array( get_option( 'default_category' ) );
					}
					$current_cats = array_values( $current_cats );
					// wp_update_post(array(
					// 		'ID'            => $post->ID,
					// 		'post_category' => $current_cats
					// ) );
          //do we really need to do an wp_update_post? it is expensive? can we do something similar to wp_set_post_tags( $post->ID, $current_tags ); for categories instead? let's try
          wp_set_post_categories($post->ID, $current_cats);

          $someTitle = $post->post_title;
					$somePostID = $post->ID;
					array_push($processedItems, (object)Array('title' => $someTitle, 'id' => $somePostID));
			}
			die(json_encode( (object) Array('originalPostIDs' => $_POST['ids'], 'postArray' => $postArray, 'old_cat' => $old_cat, 'new_cat' => $new_cat, 'processed' => $processedItems) ));
		}

    /**
     * Render move by tag box
     *
     * @since 1.1
     * @static
     * @access public
     */
    public static function render_move_tag_box() {

        if ( Bulk_Move_Util::is_posts_box_hidden( Bulk_Move::BOX_TAG ) ) {
            printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-move' ), 'tools.php?page=' . Bulk_Move::POSTS_PAGE_SLUG );
            return;
        }
?>
        <!-- Tag Start-->
        <h4><?php _e( 'On the left side, select the tag whose post you want to move. In the right side select the tag to which you want the posts to be moved.', 'bulk-move' ) ?></h4>

        <fieldset class="options">
		<table class="optiontable">
            <tr>
                <td scope="row" >
                <select name="smbm_mt_old_tag">
<?php
                $tags =  get_tags( array( 'hide_empty' => false ) );
                foreach ( $tags as $tag ) {
?>
                    <option value="<?php echo $tag->term_id; ?>">
                    <?php echo $tag->name; ?> (<?php echo $tag->count . ' '; _e( 'Posts', 'bulk-move' ); ?>)
                    </option>
<?php
                }
?>
                </select>
                ==>
                </td>
                <td scope="row" >
                <select name="smbm_mt_new_tag">
                    <option value="-1"><?php _e( 'Remove Tag', 'bulk-move' ); ?></option>
<?php
                foreach ($tags as $tag) {
?>
                    <option value="<?php echo $tag->term_id; ?>">
                        <?php echo $tag->name; ?> (<?php echo $tag->count . ' '; _e( 'Posts', 'bulk-move' ); ?>)
                    </option>
<?php
                }
?>
                </select>
                </td>
            </tr>

		</table>
        <p>
            <?php _e( 'If the post contains other tags, then', 'bulk-move' ); ?>
            <input type="radio" name="smbm_mt_overwrite" value="overwrite" checked><?php _e ( 'Remove them', 'bulk-move' ); ?>
            <input type="radio" name="smbm_mt_overwrite" value="no-overwrite"><?php _e ( "Don't remove them", 'bulk-move' ); ?>
        </p>
		</fieldset>
        <?php /*<p class="submit">
            <button type="submit" name="bm_action" value="move_tags" class="button-primary"><?php _e( 'Bulk Move (normal)', 'bulk-move' ) ?>&raquo;</button>
        </p>*/ ?>
        <p class="submit">
            <button name="bm_ajax_action" value="move_tags" class="button-primary bm_ajax_action" data-moveType="tags-tags"><?php _e( 'GO!', 'bulk-move' ) ?>&raquo;</button>
        </p>
        <div class="bm_ajax_status"></div>
        <!-- Tag end-->
<?php
    }

    /**
     * Move posts from one tag to another
     *
     * @static
     * @access public
     * @since  1.2.0
     */
    public static function move_tags() {

        if ( check_admin_referer( 'sm-bulk-move-posts', 'sm-bulk-move-posts-nonce' ) ) {

            do_action( 'bm_pre_request_handler' );

            $wp_query       = new WP_Query;
            $bm             = BULK_MOVE();

            // move by tags
            $old_tag        = absint( $_POST['smbm_mt_old_tag'] );
            $new_tag        = ( $_POST['smbm_mt_new_tag'] == -1 ) ? -1 : absint( $_POST['smbm_mt_new_tag'] );

            $posts = $wp_query->query( array(
                'tag__in'   => $old_tag,
                'post_type' => 'post',
                'nopaging'  => 'true'
            ));

            foreach ( $posts as $post ) {
                $current_tags = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
                $current_tags = array_diff( $current_tags, array( $old_tag ) );

                if ( $new_tag != -1 ) {
                    if ( isset( $_POST['smbm_mt_overwrite'] ) && 'overwrite' == $_POST['smbm_mt_overwrite'] ) {
                        // Remove old tags
                        $current_tags = array( $new_tag );
                    } else {
                        // add to existing tags
                        $current_tags[] = $new_tag;
                    }
                }

                $current_tags = array_values( $current_tags );
                wp_set_post_tags( $post->ID, $current_tags );
            }

            $bm->msg = sprintf( _n( 'Moved %d post from the selected tag', 'Moved %d posts from the selected tag' , count( $posts ), 'bulk-move' ), count( $posts ) );
        }
    }
		/**
     * Ajax Move posts from one tag to another
     *
     * @static
     * @access public
     * @since  1.2.0
     */
		public static function ajax_move_tags() {
			$wp_query = new WP_Query;
			// move by tags
			$old_tag = sanitize_text_field($_POST['from']);
			$new_tag = sanitize_text_field($_POST['to']);
			$postArray = Bulk_Move_Posts::getPostIdsArray();

			$posts   = $wp_query->query(array(
					'tag__in' => array( $old_tag ),
					'post_type'    => 'post',
					'nopaging'     => 'true',
					'post__in'      => $postArray
			) );
			$processedItems = Array();
			foreach ( $posts as $post ) {
				$current_tags = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
				$current_tags = array_diff( $current_tags, array( $old_tag ) );

				if ( $new_tag != -1 ) {
						if ( isset( $_POST['doWeOverwrite'] ) && 'overwrite' == $_POST['doWeOverwrite'] ) {
								// Remove old tags
								$current_tags = array( $new_tag );
						} else {
								// add to existing tags
								$current_tags[] = $new_tag;
						}
				}

				$current_tags = array_values( $current_tags );
				wp_set_post_tags( $post->ID, $current_tags );

				$someTitle = $post->post_title;
				$somePostID = $post->ID;
				array_push($processedItems, (object)Array('title' => $someTitle, 'id' => $somePostID));
			}
			die(json_encode( (object) Array('processed' => $processedItems, 'old_tag' => $old_tag, 'new_tag' => $new_tag) ));
		}


    /**
     * Render move category by tag box
     *
     * @since 1.2
     * @static
     * @access public
     */
    public static function render_move_category_by_tag_box() {

        if ( Bulk_Move_Util::is_posts_box_hidden( Bulk_Move::BOX_CATEGORY_BY_TAG ) ) {
            printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-move' ), 'tools.php?page=' . Bulk_Move::POSTS_PAGE_SLUG );
            return;
        }
        ?>
        <!-- Tag Start-->
        <h4><?php _e( 'On the left side, select the tag whose post you want to move. In the right side select the category to which you want the posts to be moved.', 'bulk-move' ) ?></h4>

        <fieldset class="options">
            <table class="optiontable">
                <tr>
                    <td scope="row" >
                        <select name="smbm_mtc_old_tag">
<?php
                            $tags =  get_tags( array( 'hide_empty' => false ) );
                            foreach ( $tags as $tag ) {
?>
                                <option value="<?php echo $tag->term_id; ?>">
                                    <?php echo $tag->name; ?> (<?php echo $tag->count . ' '; _e( 'Posts', 'bulk-move' ); ?>)
                                </option>
<?php
                            }
?>
                        </select>
                        ==>
                    </td>
                    <td scope="row" >
<?php
                        wp_dropdown_categories( array(
                            'name'             => 'smbm_mtc_mapped_cat',
                            'show_count'       => TRUE,
                            'hierarchical'     => TRUE,
                            'orderby'          => 'NAME',
                            'hide_empty'       => FALSE,
                            'show_option_none' => __( 'Choose Category', 'bulk-move' )
                        ) );
?>
                    </td>
                </tr>

            </table>
        <p>
            <?php _e( 'If the post contains other categories, then', 'bulk-move' ); ?>
            <input type="radio" name="smbm_mtc_overwrite" value="overwrite" checked><?php _e ( 'Remove them', 'bulk-move' ); ?>
            <input type="radio" name="smbm_mtc_overwrite" value="no-overwrite"><?php _e ( "Don't remove them", 'bulk-move' ); ?>
        </p>
        </fieldset>
        <?php /* <p class="submit">
            <button type="submit" name="bm_action" value="move_category_by_tag" class="button-primary"><?php _e( 'Bulk Move (normal)', 'bulk-move' ) ?>&raquo;</button>
        </p>*/ ?>
        <p class="submit">
            <button name="bm_ajax_action" value="move_category_by_tag" class="button-primary bm_ajax_action" data-moveType="tags-cats"><?php _e( 'GO!', 'bulk-move' ) ?>&raquo;</button>
        </p>
        <div class="bm_ajax_status"></div>
        <!-- Tag end-->
<?php
    }

    /**
     * Move posts from a tag to another category
     *
     * @static
     * @access public
     * @since  1.2.0
     */
    public static function move_category_by_tag() {

        if ( check_admin_referer( 'sm-bulk-move-posts', 'sm-bulk-move-posts-nonce' ) ) {

            do_action( 'bm_pre_request_handler' );

            $wp_query = new WP_Query;
            $bm       = BULK_MOVE();

            // move by tags
            $old_tag = absint( $_POST['smbm_mct_old_tag'] );
            $new_cat = ( $_POST['smbm_mct_mapped_cat'] == -1 ) ? -1 : absint( $_POST['smbm_mct_mapped_cat'] );

            $posts = $wp_query->query( array(
                'tag__in'   => $old_tag,
                'post_type' => 'post',
                'nopaging'  => 'true'
            ));

            foreach ( $posts as $post ) {
                $current_cats = wp_get_post_categories( $post->ID );

                if ( $new_cat != -1 ) {
                    if ( isset( $_POST['smbm_mct_overwrite'] ) && 'overwrite' == $_POST['smbm_mct_overwrite'] ) {
                        // Remove old categories
                        $current_cats = array( $new_cat );
                    } else {
                        // Add to existing categories
                        $current_cats[] = $new_cat;
                    }
                }

                if ( count( $current_cats ) == 0) {
                    $current_cats = array( get_option( 'default_category' ) );
                }
                $current_cats = array_values( $current_cats );
                // wp_update_post( array(
                //     'ID'            => $post->ID,
                //     'post_category' => $current_cats
                // ) );

                //do we really need to do an wp_update_post? it is expensive? can we do something similar to wp_set_post_tags( $post->ID, $current_tags ); for categories instead? let's try
                wp_set_post_categories($post->ID, $current_cats);
            }

            $bm->msg = sprintf( _n( 'Moved %d post from the selected tag to the new category.', 'Moved %d posts from the selected tag to the new category.' , count( $posts ), 'bulk-move' ), count( $posts ) );
        }
    }


    /**
     * Ajax Move posts from a tag to another category
     *
     * @static
     * @access public
     * @since  1.2.0
     */
    public static function ajax_move_category_by_tag() {
			$wp_query = new WP_Query;
			// move by tags
			$old_tag = sanitize_text_field($_POST['from']);
			$new_cat = sanitize_text_field($_POST['to']);
			$postArray = Bulk_Move_Posts::getPostIdsArray();
			$posts   = $wp_query->query(array(
					'tag__in' => array( $old_tag ),
					'post_type'    => 'post',
					'nopaging'     => 'true',
					'post__in'      => $postArray
			) );
			$processedItems = Array();
			foreach ( $posts as $post ) {
				$current_cats = wp_get_post_categories( $post->ID );

				if ( $new_cat != -1 ) {
						if ( isset( $_POST['doWeOverwrite'] ) && 'overwrite' == $_POST['doWeOverwrite'] ) {
								// Remove old categories
								$current_cats = array( $new_cat );
						} else {
								// Add to existing categories
								$current_cats[] = $new_cat;
						}
				}

				if ( count( $current_cats ) == 0) {
						$current_cats = array( get_option( 'default_category' ) );
				}
				$current_cats = array_values( $current_cats );
				// wp_update_post( array(
				// 		'ID'            => $post->ID,
				// 		'post_category' => $current_cats
				// ) );
        //do we really need to do an wp_update_post? it is expensive? can we do something similar to wp_set_post_tags( $post->ID, $current_tags ); for categories instead? let's try
        wp_set_post_categories($post->ID, $current_cats);
        
				$someTitle = $post->post_title;
				$somePostID = $post->ID;
				array_push($processedItems, (object)Array('title' => $someTitle, 'id' => $somePostID));
			}
			die(json_encode( (object) Array('processed' => $processedItems, 'old_tag' => $old_tag, 'new_cat' => $new_cat) ));
		}







     /**
     * Render move tag by category box
     *
     * @since 1.3.1
     * @static
     * @access public
     */
    public static function render_move_tag_by_category_box() {

        if ( Bulk_Move_Util::is_posts_box_hidden( Bulk_Move::BOX_TAG_BY_CATEGORY ) ) {
            printf( __( 'This section just got enabled. Kindly <a href = "%1$s">refresh</a> the page to fully enable it.', 'bulk-move' ), 'tools.php?page=' . Bulk_Move::POSTS_PAGE_SLUG );
            return;
        }
        ?>
        <!-- Tag Start-->
        <h4><?php _e( 'On the left side, select the category whose post you want to tag. In the right side select the tag which you want add to the posts in that category.', 'bulk-move' ) ?></h4>

        <fieldset class="options">
            <table class="optiontable">
                <tr>
                    <td scope="row" >
<?php


                    		 wp_dropdown_categories( array(
                            'name'         => 'smbm_mc_selected_cat',
														'show_count'   => TRUE,
														'hierarchical' => TRUE,
														'orderby'      => 'NAME',
														'hide_empty'   => FALSE
                        ) );
 ?>


                        ==>
                    </td>
                    <td scope="row" >
 												<select name="smbm_mt_new_tag">
                    			<option value="-1"><?php _e( 'Remove Tag', 'bulk-move' ); ?></option>
<?php
														$tags =  get_tags( array( 'hide_empty' => false ) );
                						foreach ($tags as $tag) {
?>
															<option value="<?php echo $tag->term_id; ?>">
																	<?php echo $tag->name; ?> (<?php echo $tag->count . ' '; _e( 'Posts', 'bulk-move' ); ?>)
															</option>
<?php
                						}
?>
                				</select>
                    </td>
                </tr>
            </table>
        <p>
            <?php _e( 'If the post contains other categories, then', 'bulk-move' ); ?>
            <input type="radio" name="smbm_mct_overwrite" value="overwrite" checked><?php _e ( 'Remove them', 'bulk-move' ); ?>
            <input type="radio" name="smbm_mct_overwrite" value="no-overwrite"><?php _e ( "Don't remove them", 'bulk-move' ); ?>
        </p>
        </fieldset>
        <?php /*<p class="submit">
            <button type="submit" name="bm_action" value="move_tag_by_category" class="button-primary"><?php _e( 'Bulk Move (normal)', 'bulk-move' ) ?>&raquo;</button>
        </p>*/ ?>
        <p class="submit">
            <button type="button" name="bm_ajax_action" value="move_tag_by_category" class="button-primary bm_ajax_action" data-movetype="cats-tags"><?php _e( 'GO!', 'bulk-move' ) ?>&raquo;</button>
        </p>
        <div class="bm_ajax_status"></div>
        <!-- Tag end-->
<?php
    }

    /**
     * Move posts from a category to another tag (add tag to category)
     *
     * @static
     * @access public
     * @since  1.3.1
     */
    public static function move_tag_by_category() {
        // die('move_tag_by_category');
        if ( check_admin_referer( 'sm-bulk-move-posts', 'sm-bulk-move-posts-nonce' ) ) {

            do_action( 'bm_pre_request_handler' );

            $wp_query = new WP_Query;
            $bm       = BULK_MOVE();

            /*
            how could we make this a job? and have it be done asynchronously, with a status update, in a similar fashion to what we do with

            */

						// tag by cats
            $old_cat = absint( $_POST['smbm_mc_selected_cat'] );
            $new_tag = ( $_POST['smbm_mt_new_tag'] == -1 ) ? -1 : absint( $_POST['smbm_mt_new_tag'] );

            $posts   = $wp_query->query(array(
                'category__in' => array( $old_cat ),
                'post_type'    => 'post',
                'nopaging'     => 'true'
            ) );

            foreach ( $posts as $post ) {
							//$current_cats = wp_get_post_categories( $post->ID );
							$current_tags = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
							//$current_tags = array_diff( $current_tags, array( $old_tag ) );
							if ( $new_tag != -1 ) {
									if ( isset( $_POST['smbm_mt_overwrite'] ) && 'overwrite' == $_POST['smbm_mt_overwrite'] ) {
											// Remove old tags
											$current_tags = array( $new_tag );
									} else {
											// add to existing tags
											$current_tags[] = $new_tag;
									}
							}

							$current_tags = array_values( $current_tags );
							wp_set_post_tags( $post->ID, $current_tags );



            	$bm->msg = sprintf( _n( 'Moved %d post from the selected tag to the new category.', 'Moved %d posts from the selected tag to the new category.' , count( $posts ), 'bulk-move' ), count( $posts ) );
        		}
    		}
    }



    /**
     * Ajax Move posts from a category to another tag (add tag to category)
     *
     * @static
     * @access public
     * @since  1.3.1
     */
    public static function ajax_move_tag_by_category() {
      // die('ajax_move_tag_by_category here :)');
			$wp_query = new WP_Query;
			// move by tags
			$old_cat = intval(sanitize_text_field($_POST['from']));
			$new_tag = intval(sanitize_text_field($_POST['to']));
			//$post_id = $_POST['id'];
			//$postArray = array($post_id);
			$postArray = Bulk_Move_Posts::getPostIdsArray();
      // echo("count(\$postArray)==" . count($postArray));
      // echo("\$postArray==");
      // var_dump($postArray);

			// $posts   = $wp_query->query(array(
			// 		'category__in' => array( $old_cat ),
			// 		'post_type'    => 'post',
			// 		'nopaging'     => 'true',
			// 		'post__in'      => $postArray
			// ) );
      $posts   = $wp_query->query(array(
					'post_type'    => 'post',
					'nopaging'     => 'true',
					'post__in'      => $postArray
			) );


      // we are not getting the full 50 that we would expect? why is that? the post array has 50, but the category__in filter reduces that subset ... but we would have expected that this would be 50 ...
      // perhaps the subset that we have been provided was incorrectly deduced?! ie not 50 that are in the category that we want to move. we have maybe more than one error here to correct.
      //
      // why are these turning out to be empty?!

      // echo("here are the posts that we found");
      // var_dump($posts);
      // die("from ajax_move_tag_by_category");
      // maybe the problem is that the post array is too large; ie when we are doing legacy content, with 15k+ posts ?!
			$processedItems = Array();
			foreach ( $posts as $post ) {
				//$current_cats = wp_get_post_categories( $post->ID );
				$current_tags = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
				//$current_tags = array_diff( $current_tags, array( $old_tag ) );
				if ( $new_tag != -1 ) {
						if ( isset( $_POST['doWeOverwrite'] ) && 'overwrite' == $_POST['doWeOverwrite'] ) {
								// Remove old tags
								$current_tags = array( $new_tag );
						} else {
								// add to existing tags
								$current_tags[] = $new_tag;
						}
				}

				$current_tags = array_values( $current_tags );
        // echo("new \$current_tags==");
        // var_dump($current_tags);
				wp_set_post_tags( $post->ID, $current_tags );// this doesn't seem to actually save the post!

				$someTitle = $post->post_title;
				$somePostID = $post->ID;
        //it looks like we are not updating the post!
				array_push($processedItems, (object)Array('title' => $someTitle, 'id' => $somePostID));
			}
			die(json_encode( (object) Array('processed' => $processedItems, 'old_cat' => $old_cat, 'new_tag' => $new_tag) ));


		}



    /*
		*	Ajax end point
		*/
		public static function bulk_move_ajax() {
    	//can be used to getList, of things in the batch, or to update a particular thing in the list

    	global $wpdb;
    	$action = sanitize_text_field($_POST["do"]);
    	if ($action == "getlist") {
    		//return a list of items
    		$batchType = sanitize_text_field($_POST['batchType']);
    		//$batchData = new Object();
    		$fromWhat = sanitize_text_field($_POST['from']);
    		$toWhat = sanitize_text_field($_POST['to']);

    		switch ($batchType) {
    			case 'cats-cats':
    				$post_ids = get_posts(array(
								'numberposts'   => -1, // get all posts.
								'tax_query'     => array(
										array(
												'taxonomy'  => 'category',
												'field'     => 'id',
												'terms'     => $fromWhat,
										),
								),
								'fields'        => 'ids', // Only get post IDs
						));
    				die( json_encode($post_ids) );
    				break;
    			case 'tags-tags':
            $postSlug = get_term_by('id', $fromWhat, 'post_tag')->slug;
    				$post_ids = get_posts(array(
								'numberposts'   => -1, // get all posts.
								'tax_query'     => array(
										array(
												'taxonomy'  => 'post_tag',
												'field'     => 'slug',
												'terms'     => $postSlug,
										),
								),

								'fields'        => 'ids', // Only get post IDs
						));
    				die( json_encode($post_ids) );
    				break;
    			case 'tags-cats':
            $postSlug = get_term_by('id', $fromWhat, 'post_tag')->slug;
    				$post_ids = get_posts(array(
								'numberposts'   => -1, // get all posts.
								'tax_query'     => array(
										array(
												'taxonomy'  => 'post_tag',
												'field'     => 'slug',
												'terms'     => $postSlug,
										),
								),
								'fields'        => 'ids', // Only get post IDs
						));
            // echo("\$fromWhat=={$fromWhat}");
            // echo("\$toWhat=={$toWhat}");
    				die( json_encode($post_ids) );
    				break;
    			case 'cats-tags':
    				$post_ids = get_posts(array(
								'numberposts'   => -1, // get all posts.
								'tax_query'     => array(
										array(
												'taxonomy'  => 'category',
												'field'     => 'id',
												'terms'     => $fromWhat,
										),
								),
								'fields'        => 'ids', // Only get post IDs
						));
    				die( json_encode($post_ids) );
    				break;
    		}
    		//return a job identifier
    		//update_option( $option, $new_value, $autoload );
    		//$jobHash = md5(new Date('U'));
    		//what do we want to store? an object that tells us what to do, each time we call it.

    	} else if ($action == "move_item") {
    		//how will we remember what list they are moving an item to?
    		//should we keep a list of bulkMoveSessions? in play in the db? with associated data about their statuses? or keep it all client side?
    		//if we are replacing, then the number of posts will always be decreasing - we won't want to keep an offset - becuase the query results will always be reduced by the number processed so far
    		//look up the job, by the identifier, there are many ways that we can store this ... wp_save_option? create our own table to store jobs in?
    		//job id ... created time, jobhash, movetype,  and fromWhat toWhat, whetherToRemoveTheFromWhat
    		//and another table of job items ... with post id

    		//what other details are we given to work with here?
    		//die( 1 );

    		/*
    		$somePostID = sanitize_text_field($_POST['id']);
    		$batchType = sanitize_text_field($_POST['batchType']);
    		$fromWhat = sanitize_text_field($_POST['from']);
    		$toWhat = sanitize_text_field($_POST['to']);
    		$doWeOverwrite = sanitize_text_field($_POST[doWeOverwrite]);//overwrite or no-overwrite
    		*/

    		$batchType = sanitize_text_field($_POST['batchType']);
    		switch($batchType) {
    			case 'cats-cats':
    				Bulk_Move_Posts::ajax_move_cats();//returns 0, and does nothing :( ... why
    				break;
    			case 'tags-tags':
    				Bulk_Move_Posts::ajax_move_tags();
    				break;
    			case 'tags-cats':
    				Bulk_Move_Posts::ajax_move_category_by_tag();
    				break;
    			case 'cats-tags':
    				Bulk_Move_Posts::ajax_move_tag_by_category();
    				break;
    		}

    	} else {
    		die(json_encode("wtf!"));
    	}

    }

    public static function getPostIdsArray() {
    	$idSting = sanitize_text_field($_POST['ids']);
			$postArray = explode(',', $idSting);
			return $postArray;
    }


    /**
     * Render debug box
     *
     * @static
     * @access public
     * @since  1.0
     */
    public static function render_debug_box() {

        // Get max script execution time from option.
        $max_execution_time = get_option( Bulk_Move::SCRIPT_TIMEOUT_OPTION );
        if ( !$max_execution_time ) {
            $max_execution_time = '';
        }
?>
        <!-- Debug box start-->

        <table cellspacing="10">
            <tr>
                <th align="right"><?php _e( 'PHP Version ', 'bulk-move' ); ?></th>
                <td><?php echo phpversion(); ?></td>
            </tr>
            <tr>
                <th align="right"><?php _e( 'WordPress Version ', 'bulk-move' ); ?></th>
                <td><?php echo get_bloginfo( 'version' ); ?></td>
            </tr>
            <tr>
                <th align="right"><?php _e( 'Plugin Version ', 'bulk-move' ); ?></th>
                <td><?php echo Bulk_Move::VERSION; ?></td>
            </tr>
            <tr>
                <th align="right"><?php _e( 'Available memory size ', 'bulk-move' );?></th>
                <td><?php echo ini_get( 'memory_limit' ); ?></td>
            </tr>
            <tr>
                <th align="right"><?php _e( 'Script time out ', 'bulk-move' );?></th>
                <td><strong><?php echo ini_get( 'max_execution_time' );?></strong> (<?php _e( 'In php.ini', 'bulk-move' );?>). <?php _e( 'Custom value: ', 'bulk-move' );?><input type="text" id="smbm_max_execution_time" name="smbm_max_execution_time" value="<?php echo $max_execution_time; ?>" > <button type="submit" name="bm_action" value="save_timeout" class="button-primary"><?php _e( 'Save', 'bulk-move' ) ?> &raquo;</button></td>
            </tr>
            <tr>
                <th align="right"><?php _e( 'Script input time ', 'bulk-move' ); ?></th>
                <td><?php echo ini_get( 'max_input_time' ); ?></td>
            </tr>
        </table>

        <?php /*<p><em><?php _e( 'If you are looking to delete posts in bulk, try out my ', 'bulk-move' ); ?> <a href = "http://sudarmuthu.com/wordpress/bulk-delete"><?php _e( 'Bulk Delete Plugin', 'bulk-move' );?></a>.</em></p> */ ?>
        <!-- Debug box end-->
<?php
    }

    /**
     * Save php timeout value
     *
     * @static
     * @access public
     * @since  1.2.0
     */
    public static function save_timeout() {

        if ( check_admin_referer( 'sm-bulk-move-posts', 'sm-bulk-move-posts-nonce' ) ) {
            $bm = BULK_MOVE();
            $new_max_execution_time = $_POST['smbm_max_execution_time'];

            if (is_numeric( $new_max_execution_time ) ) {
                //Update option.
                $option_updated = update_option( Bulk_Move::SCRIPT_TIMEOUT_OPTION, $new_max_execution_time );

                if ( $option_updated === true ) {
                    //Success.
                    $bm->msg = sprintf( __( 'Max execution time was successfully saved as %s seconds.', 'bulk-move' ), $new_max_execution_time );
                } else {
                    //Error saving option.
                    $bm->msg = __( 'An unknown error occurred while saving your options.', 'bulk-move' );
                }
            } else {
                //Error, value was not numeric.
                $bm->msg = sprintf( __( 'Could not update the max execution time to %s, it was not numeric.  Enter the max number of seconds this script should run.', 'bulk-move' ), $new_max_execution_time );
            }
        }
    }

    /**
     * Change php `script_timeout`
     *
     * @static
     * @access public
     * @since  1.2.0
     */
    public static function change_timeout() {
        // get max script execution time from option.
        $max_execution_time = get_option( Bulk_Move::SCRIPT_TIMEOUT_OPTION );
        if ( !$max_execution_time ) {
            //Increase script timeout in order to handle many posts.
            ini_set( 'max_execution_time', $max_execution_time );
        }
    }
}

// Hooks
add_action( 'bm_pre_request_handler'  , array( 'Bulk_Move_Posts', 'change_timeout' ) );
add_action( 'bm_move_cats'            , array( 'Bulk_Move_Posts', 'move_cats' ) );
add_action( 'bm_move_tags'            , array( 'Bulk_Move_Posts', 'move_tags' ) );
add_action( 'bm_move_category_by_tag' , array( 'Bulk_Move_Posts', 'move_category_by_tag' ) );
add_action( 'bm_move_tag_by_category' , array( 'Bulk_Move_Posts', 'move_tag_by_category' ) );
add_action( 'bm_save_timeout'         , array( 'Bulk_Move_Posts', 'save_timeout' ) );
add_action(	'wp_ajax_ajax_bulk_move'	, array( 'Bulk_Move_Posts', 'bulk_move_ajax') );//define the ajax endpoint action
