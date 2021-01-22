<?php
/**

 * Template Name: Standalone Admin Menu
 * @subpackage Default_Theme
 *
 * The template for displaying just a Toolbar aka Admin Menu
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

get_header('standalone'); ?>
<?php
  $memory_limit = ini_get('memory_limit');
  $msg = '<!-- memory_limit = ' . $memory_limit . ' -->';
  echo($msg);
?>
<?php get_footer('standalone');
