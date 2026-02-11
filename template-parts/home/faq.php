<?php
/**
 * FAQ block
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_faq_title', __( 'FAQ', 'brooklyn-beauty' ) );
$faq_items = array(
	array(
		'q' => __( 'How do I book an appointment?', 'brooklyn-beauty' ),
		'a' => __( 'You can book online through our website, call us, or visit in person. We will confirm your appointment.', 'brooklyn-beauty' ),
	),
	array(
		'q' => __( 'What are your opening hours?', 'brooklyn-beauty' ),
		'a' => __( 'We are open Monday to Saturday. Please check the contact section or call us for current hours.', 'brooklyn-beauty' ),
	),
	array(
		'q' => __( 'Do you use quality products?', 'brooklyn-beauty' ),
		'a' => __( 'Yes. We work only with trusted brands that are safe for your skin and hair.', 'brooklyn-beauty' ),
	),
);
?>
<section class="bb-section" id="faq">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-section__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<div class="bb-faq">
			<?php foreach ( $faq_items as $faq ) : ?>
				<details class="bb-faq__item">
					<summary class="bb-faq__question"><?php echo esc_html( $faq['q'] ); ?></summary>
					<div class="bb-faq__answer"><?php echo esc_html( $faq['a'] ); ?></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
