<?php
/**
 * Services block
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_services_title', __( 'Our Services', 'brooklyn-beauty' ) );
$services = array(
	array(
		'title' => __( 'Hair Styling', 'brooklyn-beauty' ),
		'text'  => __( 'Cut, color, and styling for every look.', 'brooklyn-beauty' ),
	),
	array(
		'title' => __( 'Nails', 'brooklyn-beauty' ),
		'text'  => __( 'Manicure, pedicure, and nail art.', 'brooklyn-beauty' ),
	),
	array(
		'title' => __( 'Skincare', 'brooklyn-beauty' ),
		'text'  => __( 'Facials and treatments for healthy skin.', 'brooklyn-beauty' ),
	),
	array(
		'title' => __( 'Makeup', 'brooklyn-beauty' ),
		'text'  => __( 'Makeup for day, evening, and special events.', 'brooklyn-beauty' ),
	),
);
?>
<section class="bb-section bb-section--alt" id="services">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-section__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<div class="bb-services">
			<?php foreach ( $services as $service ) : ?>
				<div class="bb-service">
					<h3 class="bb-service__title"><?php echo esc_html( $service['title'] ); ?></h3>
					<p class="bb-service__text"><?php echo esc_html( $service['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
