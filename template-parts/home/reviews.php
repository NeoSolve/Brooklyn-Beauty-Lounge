<?php
/**
 * Reviews block
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_reviews_title', __( 'Reviews', 'brooklyn-beauty' ) );
$reviews = array(
	array(
		'text'   => __( 'Wonderful salon, always leave happy. The team is professional and the atmosphere is very pleasant.', 'brooklyn-beauty' ),
		'author' => __( 'Maria K.', 'brooklyn-beauty' ),
	),
	array(
		'text'   => __( 'Best haircut I have had in a long time. Will definitely come back.', 'brooklyn-beauty' ),
		'author' => __( 'Anna L.', 'brooklyn-beauty' ),
	),
	array(
		'text'   => __( 'Clean, friendly, and great results. Highly recommend Brooklyn Beauty Lounge.', 'brooklyn-beauty' ),
		'author' => __( 'Elena S.', 'brooklyn-beauty' ),
	),
);
?>
<section class="bb-section bb-section--alt" id="reviews">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-section__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<div class="bb-reviews">
			<?php foreach ( $reviews as $review ) : ?>
				<blockquote class="bb-review">
					<p class="bb-review__text">&#8220;<?php echo esc_html( $review['text'] ); ?>&#8221;</p>
					<cite class="bb-review__author"><?php echo esc_html( $review['author'] ); ?></cite>
				</blockquote>
			<?php endforeach; ?>
		</div>
	</div>
</section>
