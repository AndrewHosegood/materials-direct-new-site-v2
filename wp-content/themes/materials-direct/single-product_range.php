<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Materials_Direct
 */

get_header();
?>

<!-- Banner -->
<?php require_once('page-includes/sector/sector-banner.php'); ?>
<!-- Banner -->

<!-- 2 column content -->
<?php require_once('page-includes/sector/sector-two-column.php'); ?>
<!-- 2 column content -->



<?php
//get_sidebar();
get_footer();
