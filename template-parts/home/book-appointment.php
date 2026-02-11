<?php
/**
 * Book an appointment CTA block
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_book_title', __( 'Book an appointment in a few clicks', 'brooklyn-beauty' ) );
$section_text  = get_theme_mod( 'bb_book_text', __( 'Choose a convenient time and we will be glad to see you.', 'brooklyn-beauty' ) );
$btn_text      = get_theme_mod( 'bb_book_btn', __( 'Book now', 'brooklyn-beauty' ) );
$btn_url       = get_theme_mod( 'bb_book_btn_url', '#book' );
?>
<section class="bb-book" id="book">
	<div class="bb-container">
		<h2 class="bb-book__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php if ( $section_text ) : ?>
			<p class="bb-book__text"><?php echo esc_html( $section_text ); ?></p>
		<?php endif; ?>
		<?php if ( $btn_text ) : ?>
			<a href="<?php echo esc_url( $btn_url ); ?>" class="bb-book__btn"><?php echo esc_html( $btn_text ); ?></a>
		<?php endif; ?>
	</div>
</section>
