<?php
/**
 * About us block
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_about_title', __( 'About Us', 'brooklyn-beauty' ) );
$about_text = get_theme_mod( 'bb_about_text', __( 'Brooklyn Beauty Lounge is a place where we combine expertise and care. We create a welcoming atmosphere so that every visit becomes a small holiday. Our mission is to help you look and feel your best.', 'brooklyn-beauty' ) );
?>
<section class="bb-section bb-section--alt" id="about-us">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-section__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<div class="bb-about__content">
			<?php echo wp_kses_post( wpautop( $about_text ) ); ?>
		</div>
	</div>
</section>
