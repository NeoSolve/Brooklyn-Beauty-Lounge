<?php
/**
 * Single service "Prices" block.
 *
 * @package Brooklyn_Beauty
 */

$service_id = isset( $args['service_id'] ) ? (int) $args['service_id'] : (int) get_the_ID();

$section_title = __( 'prices', 'brooklyn-beauty' );
$section_label = __( 'price menu', 'brooklyn-beauty' );
$fallback_intro_title = __( 'hair service', 'brooklyn-beauty' );
$fallback_intro_text  = __( 'From everyday maintenance to complete transformations, our hair services are tailored to your style and needs. Whether you are looking for a fresh cut, vibrant color, sleek blowout, or a nourishing treatment, our stylists deliver to your needs.', 'brooklyn-beauty' );

$fallback_items = array(
	array(
		'title'       => __( 'blowout', 'brooklyn-beauty' ),
		'price'       => __( '45 USD', 'brooklyn-beauty' ),
		'duration'    => __( '40 min', 'brooklyn-beauty' ),
		'description' => __( 'Service include shampoo, deep condition treatment and blow dry.', 'brooklyn-beauty' ),
		'button_text' => __( 'fast booking', 'brooklyn-beauty' ),
		'button_link' => home_url( '/#book' ),
		'badge_image' => 0,
	),
	array(
		'title'       => __( 'cut & blowout', 'brooklyn-beauty' ),
		'price'       => __( '45 USD', 'brooklyn-beauty' ),
		'duration'    => __( '1 hour - 1 hour 15 min', 'brooklyn-beauty' ),
		'description' => __( 'Service include shampoo, deep condition treatment and blow dry.', 'brooklyn-beauty' ),
		'button_text' => __( 'fast booking', 'brooklyn-beauty' ),
		'button_link' => home_url( '/#book' ),
		'badge_image' => 0,
	),
	array(
		'title'       => __( 'bangs cut', 'brooklyn-beauty' ),
		'price'       => __( '25 USD', 'brooklyn-beauty' ),
		'duration'    => __( '15 min', 'brooklyn-beauty' ),
		'description' => __( 'Keep your fringe fresh and polished with a quick trim for shape, style and easy maintenance between full haircuts.', 'brooklyn-beauty' ),
		'button_text' => __( 'fast booking', 'brooklyn-beauty' ),
		'button_link' => home_url( '/#book' ),
		'badge_image' => 0,
	),
	array(
		'title'       => __( 'classic perm (short - medium hair)', 'brooklyn-beauty' ),
		'price'       => __( 'from 150 USD', 'brooklyn-beauty' ),
		'duration'    => __( '2 hours', 'brooklyn-beauty' ),
		'description' => __( 'Transform your look with long-lasting curls or waves tailored to your style. This service includes a customized perm for a natural texture and volume, with options for short or long hair. Ideal for adding body, bounce, and movement that lasts for weeks. Pricing varies by hair length and density.', 'brooklyn-beauty' ),
		'button_text' => __( 'fast booking', 'brooklyn-beauty' ),
		'button_link' => home_url( '/#book' ),
		'badge_image' => 0,
	),
);

$sections = array(
	array(
		'intro_title' => $fallback_intro_title,
		'intro_text'  => $fallback_intro_text,
		'cards'       => $fallback_items,
	),
);

$normalize_cards = static function ( $raw_cards ) use ( $fallback_items ) {
	if ( ! is_array( $raw_cards ) || empty( $raw_cards ) ) {
		return $fallback_items;
	}

	$normalized_cards = array();
	foreach ( $raw_cards as $raw_card ) {
		if ( ! is_array( $raw_card ) ) {
			continue;
		}

		$item_title       = isset( $raw_card['title'] ) ? trim( (string) $raw_card['title'] ) : '';
		$item_price       = isset( $raw_card['price'] ) ? trim( (string) $raw_card['price'] ) : '';
		$item_duration    = isset( $raw_card['duration'] ) ? trim( (string) $raw_card['duration'] ) : '';
		$item_description = isset( $raw_card['description'] ) ? trim( (string) $raw_card['description'] ) : '';
		$item_button_text = isset( $raw_card['button_text'] ) ? trim( (string) $raw_card['button_text'] ) : '';
		$item_button_link = isset( $raw_card['button_link'] ) ? trim( (string) $raw_card['button_link'] ) : '';
		$item_badge_image = isset( $raw_card['badge_image'] ) ? $raw_card['badge_image'] : 0;

		if ( '' === $item_title && '' === $item_price && '' === $item_description ) {
			continue;
		}

		$normalized_cards[] = array(
			'title'       => $item_title,
			'price'       => $item_price,
			'duration'    => $item_duration,
			'description' => $item_description,
			'button_text' => '' !== $item_button_text ? $item_button_text : __( 'fast booking', 'brooklyn-beauty' ),
			'button_link' => '' !== $item_button_link ? $item_button_link : home_url( '/#book' ),
			'badge_image' => $item_badge_image,
		);
	}

	return ! empty( $normalized_cards ) ? $normalized_cards : $fallback_items;
};

if ( function_exists( 'get_field' ) && $service_id > 0 ) {
	$acf_section_title = trim( (string) get_field( 'service_prices_section_title', $service_id ) );
	if ( '' !== $acf_section_title ) {
		$section_title = $acf_section_title;
	}

	$acf_section_label = trim( (string) get_field( 'service_prices_section_label', $service_id ) );
	if ( '' !== $acf_section_label ) {
		$section_label = $acf_section_label;
	}

	$acf_sections = get_field( 'service_prices_sections', $service_id );
	if ( is_array( $acf_sections ) && ! empty( $acf_sections ) ) {
		$sections = array();
		foreach ( $acf_sections as $acf_section ) {
			if ( ! is_array( $acf_section ) ) {
				continue;
			}

			$acf_intro_title = isset( $acf_section['intro_title'] ) ? trim( (string) $acf_section['intro_title'] ) : '';
			$acf_intro_text  = isset( $acf_section['intro_text'] ) ? trim( (string) $acf_section['intro_text'] ) : '';
			$acf_cards       = isset( $acf_section['cards'] ) ? $acf_section['cards'] : array();

			$sections[] = array(
				'intro_title' => '' !== $acf_intro_title ? $acf_intro_title : $fallback_intro_title,
				'intro_text'  => '' !== $acf_intro_text ? $acf_intro_text : $fallback_intro_text,
				'cards'       => $normalize_cards( $acf_cards ),
			);
		}

		if ( empty( $sections ) ) {
			$sections = array(
				array(
					'intro_title' => $fallback_intro_title,
					'intro_text'  => $fallback_intro_text,
					'cards'       => $fallback_items,
				),
			);
		}
	} else {
		// Backward compatibility with previously used single-section fields.
		$legacy_intro_title = trim( (string) get_field( 'service_prices_intro_title', $service_id ) );
		$legacy_intro_text  = trim( (string) get_field( 'service_prices_intro_text', $service_id ) );
		$legacy_cards       = get_field( 'service_prices_cards', $service_id );

		$sections = array(
			array(
				'intro_title' => '' !== $legacy_intro_title ? $legacy_intro_title : $fallback_intro_title,
				'intro_text'  => '' !== $legacy_intro_text ? $legacy_intro_text : $fallback_intro_text,
				'cards'       => $normalize_cards( $legacy_cards ),
			),
		);
	}
}
?>
<section class="bb-service-prices" aria-labelledby="bb-service-prices-heading">
	<div class="bb-container">
		<div class="bb-service-prices__top">
			<h2 id="bb-service-prices-heading" class="bb-service-prices__title"><?php echo esc_html( $section_title ); ?></h2>
			<p class="bb-service-prices__label t-decor"><?php echo esc_html( $section_label ); ?></p>
		</div>

		<div class="bb-service-prices__sections">
			<?php foreach ( $sections as $section ) : ?>
				<div class="bb-service-prices__layout">
					<div class="bb-service-prices__aside">
						<div class="bb-service-prices__intro">
							<h3 class="bb-service-prices__intro-title"><?php echo esc_html( $section['intro_title'] ); ?></h3>
							<p class="bb-service-prices__intro-text"><?php echo esc_html( $section['intro_text'] ); ?></p>
						</div>
					</div>

					<div class="bb-service-prices__cards-col">
						<div class="bb-service-prices__grid">
							<?php foreach ( $section['cards'] as $item ) : ?>
								<?php
								$badge_image_url = '';
								$badge_image_alt = '';
								if ( is_numeric( $item['badge_image'] ) && (int) $item['badge_image'] > 0 ) {
									$badge_image_url = (string) wp_get_attachment_image_url( (int) $item['badge_image'], 'thumbnail' );
									$badge_image_alt = (string) get_post_meta( (int) $item['badge_image'], '_wp_attachment_image_alt', true );
								} elseif ( is_array( $item['badge_image'] ) ) {
									$badge_image_url = isset( $item['badge_image']['url'] ) ? (string) $item['badge_image']['url'] : '';
									$badge_image_alt = isset( $item['badge_image']['alt'] ) ? (string) $item['badge_image']['alt'] : '';
								}
								?>
								<article class="bb-service-prices__card">
									<header class="bb-service-prices__card-head">
										<h4 class="bb-service-prices__card-title"><?php echo esc_html( $item['title'] ); ?></h4>
										<?php if ( '' !== trim( $item['price'] ) ) : ?>
											<p class="bb-service-prices__card-price"><?php echo esc_html( $item['price'] ); ?></p>
										<?php endif; ?>
									</header>

									<?php if ( '' !== trim( $item['duration'] ) ) : ?>
										<p class="bb-service-prices__card-duration"><?php echo esc_html( $item['duration'] ); ?></p>
									<?php endif; ?>

									<?php if ( '' !== trim( $item['description'] ) ) : ?>
										<p class="bb-service-prices__card-description"><?php echo esc_html( $item['description'] ); ?></p>
									<?php endif; ?>

									<div class="bb-service-prices__card-footer">
										<a class="btn btn--big bb-service-prices__card-button" href="<?php echo esc_url( $item['button_link'] ); ?>">
											<?php echo esc_html( $item['button_text'] ); ?>
										</a>
										<?php if ( '' !== $badge_image_url ) : ?>
											<img class="bb-service-prices__card-badge" src="<?php echo esc_url( $badge_image_url ); ?>" alt="<?php echo esc_attr( $badge_image_alt ); ?>" loading="lazy" decoding="async">
										<?php endif; ?>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
