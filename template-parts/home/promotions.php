<?php
/**
 * Our promotions block
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_promos_title', __( 'Our Promotions', 'brooklyn-beauty' ) );
$promos = array(
	array(
		'title' => __( 'First Visit', 'brooklyn-beauty' ),
		'text'  => __( 'Special offer for new clients.', 'brooklyn-beauty' ),
	),
	array(
		'title' => __( 'Seasonal Discounts', 'brooklyn-beauty' ),
		'text'  => __( 'Check current seasonal promotions.', 'brooklyn-beauty' ),
	),
	array(
		'title' => __( 'Loyalty Program', 'brooklyn-beauty' ),
		'text'  => __( 'Earn points and get rewards on your next visit.', 'brooklyn-beauty' ),
	),
);
?>
<section class="bb-section" id="promotions">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-section__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<div class="bb-promos">
			<?php foreach ( $promos as $promo ) : ?>
				<div class="bb-promo">
					<h3 class="bb-promo__title"><?php echo esc_html( $promo['title'] ); ?></h3>
					<p class="bb-promo__text"><?php echo esc_html( $promo['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
