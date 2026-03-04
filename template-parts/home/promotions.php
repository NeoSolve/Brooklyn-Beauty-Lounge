<?php
/**
 * Our promotions block
 *
 * @package Brooklyn_Beauty
 */
$section_intro = '';
$section_title = __( 'our promotions', 'brooklyn-beauty' );
$section_background_image = '';
$slides        = array();

$resolve_image_url = static function ( $field_value ) {
	if ( is_numeric( $field_value ) ) {
		$image_url = wp_get_attachment_image_url( (int) $field_value, 'full' );
		return $image_url ? $image_url : '';
	}

	if ( is_array( $field_value ) ) {
		if ( ! empty( $field_value['ID'] ) ) {
			$image_url = wp_get_attachment_image_url( (int) $field_value['ID'], 'full' );
			return $image_url ? $image_url : '';
		}

		if ( ! empty( $field_value['url'] ) && is_string( $field_value['url'] ) ) {
			return trim( $field_value['url'] );
		}
	}

	if ( is_string( $field_value ) ) {
		return trim( $field_value );
	}

	return '';
};

if ( function_exists( 'get_field' ) ) {
	$front_page_id = (int) get_option( 'page_on_front' );
	$field_post_id = $front_page_id > 0 ? $front_page_id : get_queried_object_id();

	$acf_section_intro = trim( (string) get_field( 'promotions_intro', $field_post_id ) );
	if ( '' !== $acf_section_intro ) {
		$section_intro = $acf_section_intro;
	}

	$acf_section_title = trim( (string) get_field( 'promotions_title', $field_post_id ) );
	if ( '' !== $acf_section_title ) {
		$section_title = $acf_section_title;
	}

	$acf_section_background = get_field( 'promotions_background_image', $field_post_id );
	$resolved_section_background = $resolve_image_url( $acf_section_background );
	if ( '' !== $resolved_section_background ) {
		$section_background_image = $resolved_section_background;
	}

	$acf_slides = get_field( 'promotions_slides', $field_post_id );
	if ( is_array( $acf_slides ) && ! empty( $acf_slides ) ) {
		foreach ( $acf_slides as $slide ) {
			if ( ! is_array( $slide ) ) {
				continue;
			}

			$slide_title       = isset( $slide['card_title'] ) ? trim( (string) $slide['card_title'] ) : '';
			$slide_validity    = isset( $slide['validity_text'] ) ? trim( (string) $slide['validity_text'] ) : '';
			$slide_description = isset( $slide['description'] ) ? trim( (string) $slide['description'] ) : '';
			$slide_button_text = isset( $slide['button_text'] ) ? trim( (string) $slide['button_text'] ) : '';
			$slide_button_link = isset( $slide['button_link'] ) ? trim( (string) $slide['button_link'] ) : '';
			$slide_first_image = isset( $slide['first_image'] ) ? $resolve_image_url( $slide['first_image'] ) : '';
			$slide_second_image = isset( $slide['second_image'] ) ? $resolve_image_url( $slide['second_image'] ) : '';

			if ( '' === $slide_title && '' === $slide_validity && '' === $slide_description && '' === $slide_first_image && '' === $slide_second_image ) {
				continue;
			}

			$slides[] = array(
				'card_title'       => $slide_title,
				'validity_text'    => $slide_validity,
				'description'      => $slide_description,
				'button_text'      => $slide_button_text,
				'button_link'      => $slide_button_link,
				'first_image'      => $slide_first_image,
				'second_image'     => $slide_second_image,
			);
		}
	}
}

if ( empty( $slides ) ) {
	$slides[] = array(
		'card_title'       => __( 'fall balayage + blowout promo $250', 'brooklyn-beauty' ),
		'validity_text'    => __( 'Limited time offer valid Oct 21 - Nov 21.', 'brooklyn-beauty' ),
		'description'      => __( 'Includes full balayage and blowout with newest stylist. Pre-Book Now and use any time before Nov 21.', 'brooklyn-beauty' ),
		'button_text'      => __( 'book now', 'brooklyn-beauty' ),
		'button_link'      => '#book',
		'first_image'      => '',
		'second_image'     => '',
	);
}

$promotions_style = '';
if ( '' !== $section_background_image ) {
	$promotions_style = '--bb-promotions-bg-image:url(' . esc_url( $section_background_image ) . ');';
}
?>
<section class="bb-promotions-section" id="promotions" data-promotions>
	<div class="bb-container">
		<div class="bb-promotions<?php echo '' === $section_background_image ? ' is-no-image' : ''; ?>"<?php echo '' !== $promotions_style ? ' style="' . esc_attr( $promotions_style ) . '"' : ''; ?>>
			<div class="bb-promotions__left">
				<?php if ( $section_intro ) : ?>
					<p class="bb-promotions__intro"><?php echo esc_html( $section_intro ); ?></p>
				<?php endif; ?>
				<div class="bb-promotions__left-bottom">
					<?php if ( $section_title ) : ?>
						<h2 class="bb-promotions__title"><?php echo esc_html( $section_title ); ?></h2>
					<?php endif; ?>
					<div class="bb-promotions__controls" aria-label="<?php esc_attr_e( 'Promotions slider controls', 'brooklyn-beauty' ); ?>">
						<button class="bb-promotions__arrow bb-promotions__arrow--prev" type="button" data-promotions-nav="prev" aria-label="<?php esc_attr_e( 'Previous promotion', 'brooklyn-beauty' ); ?>"></button>
						<button class="bb-promotions__arrow bb-promotions__arrow--next" type="button" data-promotions-nav="next" aria-label="<?php esc_attr_e( 'Next promotion', 'brooklyn-beauty' ); ?>"></button>
					</div>
				</div>
			</div>

			<div class="bb-promotions__right">
				<?php foreach ( $slides as $index => $slide ) : ?>
					<article class="bb-promo-card<?php echo 0 === $index ? ' is-active' : ''; ?>" data-promotion-slide aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">
						<?php if ( $slide['card_title'] ) : ?>
							<h3 class="bb-promo-card__title"><?php echo wp_kses( $slide['card_title'], array( 'br' => array() ) ); ?></h3>
						<?php endif; ?>

						<div class="bb-promo-card__images">
							<?php if ( $slide['first_image'] ) : ?>
								<figure class="bb-promo-card__image-wrap">
									<img class="bb-promo-card__image" src="<?php echo esc_url( $slide['first_image'] ); ?>" alt="<?php echo esc_attr( $slide['card_title'] . ' 1' ); ?>" loading="lazy">
								</figure>
							<?php endif; ?>
							<?php if ( $slide['second_image'] ) : ?>
								<figure class="bb-promo-card__image-wrap">
									<img class="bb-promo-card__image" src="<?php echo esc_url( $slide['second_image'] ); ?>" alt="<?php echo esc_attr( $slide['card_title'] . ' 2' ); ?>" loading="lazy">
								</figure>
							<?php endif; ?>
						</div>

						<div class="bb-promo-card__footer">
							<div class="bb-promo-card__content">
								<?php if ( $slide['validity_text'] ) : ?>
									<p class="bb-promo-card__validity"><?php echo esc_html( $slide['validity_text'] ); ?></p>
								<?php endif; ?>
								<?php if ( $slide['description'] ) : ?>
									<p class="bb-promo-card__description"><?php echo esc_html( $slide['description'] ); ?></p>
								<?php endif; ?>
							</div>
							<?php if ( $slide['button_text'] && $slide['button_link'] ) : ?>
								<a class="btn btn--white bb-promo-card__button" href="<?php echo esc_url( $slide['button_link'] ); ?>"><?php echo esc_html( $slide['button_text'] ); ?></a>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
