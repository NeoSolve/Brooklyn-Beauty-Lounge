<?php
/**
 * Template Name: Blog Page
 *
 * Blog page template.
 *
 * @package Brooklyn_Beauty
 */

get_header();
?>

<?php
get_template_part( 'template-parts/components/blog-hero-banner' );
get_template_part( 'template-parts/blog/posts' );
?>

<?php
get_footer();
?>
