<?php
/**
 * Single service "Why choose" block.
 *
 * @package Brooklyn_Beauty
 */

$service_id    = isset( $args['service_id'] ) ? (int) $args['service_id'] : (int) get_the_ID();
$service_title = isset( $args['service_title'] ) ? (string) $args['service_title'] : get_the_title( $service_id );

$section_title = sprintf(
	/* translators: %s: service title */
	__( 'why to choose brooklyn beauty lounge for %s?', 'brooklyn-beauty' ),
	$service_title
);
$section_label = __( 'what sets us apart', 'brooklyn-beauty' );

$fallback_items = array(
	array(
		'number' => '001',
		'title'  => __( 'expert stylists & professionals', 'brooklyn-beauty' ),
		'text'   => __( 'Our team is made up of highly trained professionals who stay up to date with the latest trends, techniques, and treatments to give you the best results.', 'brooklyn-beauty' ),
	),
	array(
		'number' => '002',
		'title'  => __( 'personalized approach', 'brooklyn-beauty' ),
		'text'   => __( 'We do not believe in one size fits all. Every service, from hair to nails to skincare, is tailored to your personal needs, style, and lifestyle.', 'brooklyn-beauty' ),
	),
	array(
		'number' => '003',
		'title'  => __( 'premium products & tools', 'brooklyn-beauty' ),
		'text'   => __( 'We use only salon-quality, professional-grade products that protect, nourish, and enhance your natural beauty, ensuring long-lasting results.', 'brooklyn-beauty' ),
	),
	array(
		'number' => '004',
		'title'  => __( 'relaxing & stylish atmosphere', 'brooklyn-beauty' ),
		'text'   => __( 'More than just a salon, it is a place to relax. Our welcoming space is created to make you feel comfortable, confident, and cared for.', 'brooklyn-beauty' ),
	),
	array(
		'number' => '005',
		'title'  => __( 'full-service beauty in one place', 'brooklyn-beauty' ),
		'text'   => __( 'Hair, nails, skincare, brows, lashes, hair removal - we have got it all. Save time by enjoying a complete beauty experience in one location.', 'brooklyn-beauty' ),
	),
	array(
		'number' => '006',
		'title'  => __( 'consistently outstanding results', 'brooklyn-beauty' ),
		'text'   => __( 'Our clients return to us because they trust us and see the results every time, whether it is a fresh look or long-term beauty care.', 'brooklyn-beauty' ),
	),
);

$items = $fallback_items;

if ( function_exists( 'get_field' ) && $service_id > 0 ) {
	$acf_title = trim( (string) get_field( 'service_why_choose_title', $service_id ) );
	if ( '' !== $acf_title ) {
		$section_title = $acf_title;
	}

	$acf_label = trim( (string) get_field( 'service_why_choose_label', $service_id ) );
	if ( '' !== $acf_label ) {
		$section_label = $acf_label;
	}

	$acf_items = get_field( 'service_why_choose_items', $service_id );
	if ( is_array( $acf_items ) && ! empty( $acf_items ) ) {
		$items = array();
		foreach ( $acf_items as $index => $acf_item ) {
			if ( ! is_array( $acf_item ) ) {
				continue;
			}

			$item_title  = isset( $acf_item['title'] ) ? trim( (string) $acf_item['title'] ) : '';
			$item_text   = isset( $acf_item['text'] ) ? trim( (string) $acf_item['text'] ) : '';
			$item_number = isset( $acf_item['number'] ) ? trim( (string) $acf_item['number'] ) : '';

			if ( '' === $item_title && '' === $item_text ) {
				continue;
			}

			if ( '' === $item_number ) {
				$item_number = str_pad( (string) ( $index + 1 ), 3, '0', STR_PAD_LEFT );
			}

			$items[] = array(
				'number' => $item_number,
				'title'  => $item_title,
				'text'   => $item_text,
			);
		}

		if ( empty( $items ) ) {
			$items = $fallback_items;
		}
	}
}
?>
<section class="bb-service-why">
	<div class="bb-container">
		<div class="bb-service-why__layout">
			<div class="bb-service-why__aside">
				<p class="bb-service-why__label t-decor"><?php echo esc_html( $section_label ); ?></p>
			</div>
			<div class="bb-service-why__main">
				<h2 class="bb-service-why__title"><?php echo wp_kses( $section_title, array( 'br' => array() ) ); ?></h2>
				<div class="bb-service-why__grid">
					<?php foreach ( $items as $item ) : ?>
						<article class="bb-service-why__card">
							<span class="bb-service-why__number"><?php echo esc_html( $item['number'] ); ?></span>
							<h3 class="bb-service-why__card-title"><?php echo esc_html( $item['title'] ); ?></h3>
							<p class="bb-service-why__card-text"><?php echo esc_html( $item['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
