<?php
/**
 * About us block
 *
 * @package Brooklyn_Beauty
 */
$section_title    = '';
$left_text        = '';
$left_bottom_text = '';
$right_text       = '';
$main_image_url   = '';
$gallery_images   = array();
$about_us_slides  = array();

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

	$acf_section_title = trim( (string) get_field( 'about_us_title', $field_post_id ) );
	if ( '' !== $acf_section_title ) {
		$section_title = $acf_section_title;
	}

	$acf_left_text = trim( (string) get_field( 'about_us_left_text', $field_post_id ) );
	if ( '' !== $acf_left_text ) {
		$left_text = $acf_left_text;
	}

	$acf_right_text = trim( (string) get_field( 'about_us_right_text', $field_post_id ) );
	if ( '' !== $acf_right_text ) {
		$right_text = $acf_right_text;
	}

	$acf_left_bottom_text = trim( (string) get_field( 'about_us_left_bottom_text', $field_post_id ) );
	if ( '' !== $acf_left_bottom_text ) {
		$left_bottom_text = $acf_left_bottom_text;
	}

	$acf_main_image = get_field( 'about_us_main_image', $field_post_id );
	$resolved_main  = $resolve_image_url( $acf_main_image );
	if ( '' !== $resolved_main ) {
		$main_image_url = $resolved_main;
	}

	$acf_gallery = get_field( 'about_us_gallery', $field_post_id );
	if ( is_array( $acf_gallery ) && ! empty( $acf_gallery ) ) {
		foreach ( $acf_gallery as $gallery_item ) {
			$image_url = $resolve_image_url( $gallery_item );
			if ( '' !== $image_url ) {
				$gallery_images[] = $image_url;
			}
		}
	}

	$acf_slides = get_field( 'about_us_slides', $field_post_id );
	if ( is_array( $acf_slides ) && ! empty( $acf_slides ) ) {
		$prepared_slides = array();

		foreach ( $acf_slides as $acf_slide ) {
			if ( ! is_array( $acf_slide ) ) {
				continue;
			}

			$slide_name          = isset( $acf_slide['name'] ) ? trim( (string) $acf_slide['name'] ) : '';
			$slide_quote         = isset( $acf_slide['quote'] ) ? trim( (string) $acf_slide['quote'] ) : '';
			$slide_highlight     = isset( $acf_slide['highlighted_line'] ) ? trim( (string) $acf_slide['highlighted_line'] ) : '';
			$slide_portrait      = isset( $acf_slide['portrait'] ) ? $resolve_image_url( $acf_slide['portrait'] ) : '';
			$slide_quote_lines   = array();
			$raw_quote_line_list = preg_split( '/\r\n|\r|\n/', $slide_quote );

			if ( is_array( $raw_quote_line_list ) ) {
				foreach ( $raw_quote_line_list as $raw_quote_line ) {
					$line = trim( (string) $raw_quote_line );
					if ( '' !== $line ) {
						$slide_quote_lines[] = $line;
					}
				}
			}

			if ( '' === $slide_name && empty( $slide_quote_lines ) && '' === $slide_highlight && '' === $slide_portrait ) {
				continue;
			}

			$prepared_slides[] = array(
				'portrait_url'     => $slide_portrait,
				'name'             => '' !== $slide_name ? $slide_name : __( 'Team member', 'brooklyn-beauty' ),
				'quote_lines'      => $slide_quote_lines,
				'highlighted_line' => $slide_highlight,
			);
		}

		if ( ! empty( $prepared_slides ) ) {
			$about_us_slides = $prepared_slides;
		}
	}
}

if ( empty( $gallery_images ) && '' !== $main_image_url ) {
	$gallery_images[] = $main_image_url;
}
?>
<?php if ( $section_title || $left_text || $left_bottom_text || $right_text || ! empty( $about_us_slides ) || ! empty( $gallery_images ) ) : ?>
<section class="bb-about-us-section" id="about-us" data-about-us>
	<div class="bb-container">
		<div class="bb-about-us">
			<?php if ( $section_title ) : ?>
				<h2 class="bb-about-us__title"><?php echo esc_html( $section_title ); ?></h2>
			<?php endif; ?>
			<div class="bb-about-us__panel">
				<?php if ( ! empty( $about_us_slides ) ) : ?>
					<div class="bb-about-us__slider" data-about-slider>
						<div class="bb-about-us__slides">
							<?php foreach ( $about_us_slides as $slide_index => $slide_item ) : ?>
								<div class="bb-about-us__slide<?php echo 0 === $slide_index ? ' is-active' : ''; ?>" data-about-slide aria-hidden="<?php echo 0 === $slide_index ? 'false' : 'true'; ?>">
									<div class="bb-about-us__slide-top">
										<div class="bb-about-us__portrait-wrap">
											<?php if ( ! empty( $slide_item['portrait_url'] ) ) : ?>
												<img class="bb-about-us__portrait" src="<?php echo esc_url( $slide_item['portrait_url'] ); ?>" alt="<?php echo esc_attr( $slide_item['name'] ); ?>" loading="lazy">
											<?php endif; ?>
										</div>
										<div class="bb-about-us__quote-block">
											<div class="bb-about-us__controls" aria-label="<?php esc_attr_e( 'About us slider controls', 'brooklyn-beauty' ); ?>">
												<button class="bb-about-us__arrow bb-about-us__arrow--prev" type="button" data-about-nav="prev" aria-label="<?php esc_attr_e( 'Previous team member', 'brooklyn-beauty' ); ?>"></button>
												<button class="bb-about-us__arrow bb-about-us__arrow--next" type="button" data-about-nav="next" aria-label="<?php esc_attr_e( 'Next team member', 'brooklyn-beauty' ); ?>"></button>
											</div>
											<div class="bb-about-us__quote">
												<?php
												if ( ! empty( $slide_item['quote_lines'] ) ) :
													foreach ( $slide_item['quote_lines'] as $quote_line ) :
														?>
														<span class="bb-about-us__quote-line"><?php echo esc_html( $quote_line ); ?></span>
														<?php
													endforeach;
												endif;
												?>
												<?php if ( ! empty( $slide_item['highlighted_line'] ) ) : ?>
													<span class="bb-about-us__quote-line bb-about-us__quote-line--accent"><?php echo esc_html( $slide_item['highlighted_line'] ); ?></span>
												<?php endif; ?>
											</div>
										</div>
									</div>
									<p class="bb-about-us__person-name"><?php echo esc_html( $slide_item['name'] ); ?></p>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="bb-about-us__text-columns">
					<div class="bb-about-us__text-column">
						<?php if ( $left_text ) : ?>
							<p class="bb-about-us__text"><?php echo esc_html( $left_text ); ?></p>
						<?php endif; ?>
						<?php if ( $left_bottom_text ) : ?>
							<p class="bb-about-us__text"><?php echo esc_html( $left_bottom_text ); ?></p>
						<?php endif; ?>
					</div>
					<div class="bb-about-us__text-column">
						<?php if ( $right_text ) : ?>
							<p class="bb-about-us__text"><?php echo esc_html( $right_text ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="bb-about-us__image-wrap">
				<?php if ( ! empty( $gallery_images ) ) : ?>
					<div class="bb-about-us__gallery" data-about-gallery>
						<?php foreach ( $gallery_images as $image_index => $image_url ) : ?>
							<figure class="bb-about-us__gallery-item<?php echo 0 === $image_index ? ' is-active' : ''; ?>" data-about-gallery-item aria-hidden="<?php echo 0 === $image_index ? 'false' : 'true'; ?>">
								<img class="bb-about-us__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $section_title . ' ' . ( $image_index + 1 ) ); ?>" loading="lazy">
							</figure>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>
