<?php
/**
 * Template Name: Poll Template
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

/*
So what is the plan here?
well ... we will get some data about the poll, when we call the api on the front page.

so ... we could make in api call to find out what poll is the one currently specified in some field on front page edits,
and thenm laod that poll,
or we could just load in this, and let this template figure out which one is the current one.

//http://www.everythingzoomer.com/wp-content/plugins/totalpoll/assets/js/totalpoll.min.js?ver=2.7
<script type="text/javascript" src="http://www.everythingzoomer.com/wp-includes/js/jquery/jquery.js?ver=1.12.4"></script>
<script type="text/javascript" src="http://www.everythingzoomer.com/wp-content/plugins/totalpoll/assets/js/fastclick.min.js?ver=4.7.2"></script>
<script type="text/javascript" src="http://www.everythingzoomer.com/wp-content/plugins/totalpoll/assets/js/totalpoll.min.js?ver=2.7"></script>
*/
wp_deregister_script( 'wp-embed' );
wp_dequeue_script('wp-embed');
show_admin_bar( false );

get_header('poll'); ?>
<!-- <h1 style="text-align:center;"> EZ Poll</h1> -->
<?php
/*

*/
$homePage = get_page_by_path( 'front-page-secondary-buckets' );
$currentPoll = get_field('ez2_poll', $homePage->ID)[0];
// var_dump($currentPoll);
// echo do_shortcode("[total-poll id=\"{$currentPoll->ID}\"]");

echo do_shortcode('[total-poll id="'. $currentPoll->ID .'"]');
?>
<?php
get_footer('poll');
