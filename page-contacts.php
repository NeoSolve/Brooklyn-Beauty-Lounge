<?php
/**
 * Template Name: Contacts Page
 *
 * Contacts page template.
 *
 * @package Brooklyn_Beauty
 */

get_header();
?>

<?php
get_template_part( 'template-parts/components/hero-banner' );
get_template_part( 'template-parts/contacts/location' );
get_template_part( 'template-parts/contacts/contact-form' );
?>

<?php
get_footer();
?>
