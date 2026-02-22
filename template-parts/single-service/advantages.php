<?php
/**
 * Single service "Advantages" block — title + 2×2 cards grid.
 *
 * @package Brooklyn_Beauty
 */

$service_id    = isset( $args['service_id'] ) ? (int) $args['service_id'] : (int) get_the_ID();
$service_title = isset( $args['service_title'] ) ? (string) $args['service_title'] : get_the_title( $service_id );

$section_title = sprintf(
	/* translators: %s: service title */
	__( 'advantages of professional<br>%s', 'brooklyn-beauty' ),
	$service_title
);
$section_label = __( 'refined', 'brooklyn-beauty' );

$fallback_items = array(
	array(
		'number' => '001',
		'title'  => __( 'expert knowledge & skills', 'brooklyn-beauty' ),
		'text'   => __( 'Our expert stylists, trained in the latest techniques and trends, combine artistry with advanced skills to deliver healthy, modern, and perfectly tailored looks.', 'brooklyn-beauty' ),
	),
	array(
		'number' => '002',
		'title'  => __( 'personalized consultation', 'brooklyn-beauty' ),
		'text'   => __( 'Every service starts with a personalized consultation, where our stylists assess your hair and goals. You will get a personalized treatment plan to make hair healthy, shiny, and strong.', 'brooklyn-beauty' ),
	),
	array(
		'number' => '003',
		'title'  => __( 'high-quality products', 'brooklyn-beauty' ),
		'text'   => __( 'We use only professional, salon-grade products that protect, nourish, and strengthen your hair for vibrant, healthy, and long-lasting results.', 'brooklyn-beauty' ),
	),
	array(
		'number' => '004',
		'title'  => __( 'stress-free experience', 'brooklyn-beauty' ),
		'text'   => __( 'We create a calm, welcoming atmosphere where you can relax, unwind, and leave feeling refreshed, confident, and effortlessly beautiful.', 'brooklyn-beauty' ),
	),
);

$items = $fallback_items;

if ( function_exists( 'get_field' ) && $service_id > 0 ) {
	$acf_title = trim( (string) get_field( 'service_advantages_title', $service_id ) );
	if ( '' !== $acf_title ) {
		$section_title = $acf_title;
	}

	$acf_label = trim( (string) get_field( 'service_advantages_label', $service_id ) );
	if ( '' !== $acf_label ) {
		$section_label = $acf_label;
	}

	$acf_items = get_field( 'service_advantages_items', $service_id );
	if ( is_array( $acf_items ) && ! empty( $acf_items ) ) {
		$items = array();
		foreach ( $acf_items as $index => $acf_item ) {
			if ( ! is_array( $acf_item ) ) {
				continue;
			}

			$item_title  = isset( $acf_item['title'] ) ? trim( (string) $acf_item['title'] ) : '';
			$item_text   = isset( $acf_item['text'] ) ? trim( (string) $acf_item['text'] ) : '';
			$item_number = isset( $acf_item['number'] ) ? trim( (string) $acf_item['number'] ) : '';
			$item_background_url = '';

			if ( '' === $item_title && '' === $item_text ) {
				continue;
			}

			if ( '' === $item_number ) {
				$item_number = str_pad( (string) ( $index + 1 ), 3, '0', STR_PAD_LEFT );
			}

			if ( isset( $acf_item['background_svg'] ) ) {
				$acf_item_background = $acf_item['background_svg'];
				if ( is_numeric( $acf_item_background ) ) {
					$item_background_url = (string) wp_get_attachment_url( (int) $acf_item_background );
				} elseif ( is_array( $acf_item_background ) && ! empty( $acf_item_background['url'] ) ) {
					$item_background_url = (string) $acf_item_background['url'];
				} elseif ( is_string( $acf_item_background ) && '' !== trim( $acf_item_background ) ) {
					$item_background_url = trim( $acf_item_background );
				}
			}

			$items[] = array(
				'number'         => $item_number,
				'title'          => $item_title,
				'text'           => $item_text,
				'background_url' => $item_background_url,
			);
		}

		if ( empty( $items ) ) {
			$items = $fallback_items;
		}
	}
}
?>
<section class="bb-service-advantages" aria-labelledby="bb-service-advantages-heading">
	<div class="bb-container">
		<div class="bb-service-advantages__header">
			<h2 id="bb-service-advantages-heading" class="bb-service-advantages__title"><?php echo wp_kses( $section_title, array( 'br' => array() ) ); ?></h2>
			<span class="bb-service-advantages__label t-decor"><?php echo esc_html( $section_label ); ?></span>
		</div>

		<div class="bb-service-advantages__grid">
			<?php foreach ( $items as $item ) : ?>
				<article class="bb-service-advantages__card"<?php if ( ! empty( $item['background_url'] ) ) : ?> style="--bb-service-advantages-card-bg: url('<?php echo esc_url( $item['background_url'] ); ?>');"<?php endif; ?>>
					<div class="bb-service-advantages__card-top">
						<h3 class="bb-service-advantages__card-title"><?php echo esc_html( $item['title'] ); ?></h3>
						<span class="bb-service-advantages__card-number"><?php echo esc_html( $item['number'] ); ?></span>
					</div>
					<p class="bb-service-advantages__card-text"><?php echo esc_html( $item['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
