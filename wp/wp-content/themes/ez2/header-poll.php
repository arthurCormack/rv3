<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php
  // Get HTTP/HTTPS (the possible values for this vary from server to server)
  $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']),array('off','no'))) ? 'https' : 'http';
  // Get domain portion
  $baseURL .= '://'.$_SERVER['HTTP_HOST'];

?>
<?php
/*
<script type="text/javascript" src="<?php echo($baseURL); ?>/wp-includes/js/jquery/jquery.js?ver=1.12.4"></script>
<script type="text/javascript" src="<?php echo($baseURL); ?>/wp-content/plugins/totalpoll/assets/js/fastclick.min.js?ver=4.7.2"></script>
<script type="text/javascript" src="<?php echo($baseURL); ?>/wp-content/plugins/totalpoll/assets/js/totalpoll.min.js?ver=2.7"></script>

<script type="text/javascript" src="<?php echo($baseURL); ?>/wp-content/plugins/totalpoll/addons/cache-compatibility/async-load.min.js?ver=2.7"></script>

*/
?>

<?php wp_head(); ?>
</head>
<body>
