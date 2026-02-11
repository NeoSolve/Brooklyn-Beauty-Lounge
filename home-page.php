<?php
/**
 * Template Name: Home Page
 * Main homepage template
 *
 * @package Brooklyn_Beauty
 */

get_header();
?>

<?php get_template_part( 'template-parts/home/hero-banner' ); ?>
<?php get_template_part( 'template-parts/home/services' ); ?>
<?php get_template_part( 'template-parts/home/our-work' ); ?>
<?php get_template_part( 'template-parts/home/why-us' ); ?>
<?php get_template_part( 'template-parts/home/video-tour' ); ?>
<?php get_template_part( 'template-parts/home/about-us' ); ?>
<?php get_template_part( 'template-parts/home/promotions' ); ?>
<?php get_template_part( 'template-parts/home/reviews' ); ?>
<?php get_template_part( 'template-parts/home/book-appointment' ); ?>
<?php get_template_part( 'template-parts/home/faq' ); ?>

<?php
get_footer();
