<?php
/*
Template Name: Left Sidebar Page
*/
?>

<?php get_header(); ?>

<?php get_sidebar(); // sidebar 1 ?>

<div id="main" class="eight columns clearfix" role="main">

	<?php get_template_part( 'loop', 'page' ); ?>

</div> <!-- end #main -->

<?php get_footer(); ?>