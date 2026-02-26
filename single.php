<?php
/**
 * Single post template.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/blog/single-hero' );
	get_template_part( 'template-parts/blog/single-content' );
	get_template_part( 'template-parts/home/book-appointment' );
	get_template_part( 'template-parts/blog/other-articles' );
	get_template_part( 'template-parts/home/faq' );
endwhile;

get_footer();
?>
