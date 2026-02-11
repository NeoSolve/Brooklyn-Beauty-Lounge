<?php
/**
 * Why us block
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_why_title', __( 'Why Us', 'brooklyn-beauty' ) );
$items = array(
	array(
		'title' => __( 'Experienced Specialists', 'brooklyn-beauty' ),
		'text'  => __( 'Our team has years of experience and ongoing training.', 'brooklyn-beauty' ),
	),
	array(
		'title' => __( 'Quality Products', 'brooklyn-beauty' ),
		'text'  => __( 'We use only proven, safe products for your beauty.', 'brooklyn-beauty' ),
	),
	array(
		'title' => __( 'Comfortable Atmosphere', 'brooklyn-beauty' ),
		'text'  => __( 'Relax in a cozy space designed for your comfort.', 'brooklyn-beauty' ),
	),
);
?>
<section class="bb-section bb-section--alt" id="why-us">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-section__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<div class="bb-why">
			<?php foreach ( $items as $item ) : ?>
				<div class="bb-why__item">
					<h3 class="bb-why__title"><?php echo esc_html( $item['title'] ); ?></h3>
					<p class="bb-why__text"><?php echo esc_html( $item['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
