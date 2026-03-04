<?php
/**
 * Reviews block.
 *
 * @package Brooklyn_Beauty
 */
$section_title   = '';
$section_text    = '';
$section_label   = '';
$google_icon_url = get_template_directory_uri() . '/assets/images/ri_google-fill.svg';
$star_icon_url   = get_template_directory_uri() . '/assets/images/star.svg';
$reviews = array();

if ( function_exists( 'get_field' ) ) {
	$front_page_id = (int) get_option( 'page_on_front' );
	$field_post_id = $front_page_id > 0 ? $front_page_id : get_queried_object_id();

	$acf_section_title = trim( (string) get_field( 'reviews_title', $field_post_id ) );
	if ( '' !== $acf_section_title ) {
		$section_title = $acf_section_title;
	}

	$acf_section_text = trim( (string) get_field( 'reviews_text', $field_post_id ) );
	if ( '' !== $acf_section_text ) {
		$section_text = $acf_section_text;
	}

	$acf_section_label = trim( (string) get_field( 'reviews_label', $field_post_id ) );
	if ( '' !== $acf_section_label ) {
		$section_label = $acf_section_label;
	}

	$acf_reviews = get_field( 'reviews_items', $field_post_id );
	if ( is_array( $acf_reviews ) && ! empty( $acf_reviews ) ) {
		$reviews = array();

		foreach ( $acf_reviews as $review_item ) {
			if ( ! is_array( $review_item ) ) {
				continue;
			}

			$review_title  = isset( $review_item['title'] ) ? trim( (string) $review_item['title'] ) : '';
			$review_text   = isset( $review_item['text'] ) ? trim( (string) $review_item['text'] ) : '';
			$review_author = isset( $review_item['author'] ) ? trim( (string) $review_item['author'] ) : '';
			$review_time   = isset( $review_item['time'] ) ? trim( (string) $review_item['time'] ) : '';
			$review_link   = isset( $review_item['link'] ) ? trim( (string) $review_item['link'] ) : '';
			$review_rating = isset( $review_item['rating'] ) ? (int) $review_item['rating'] : 5;
			if ( $review_rating < 1 || $review_rating > 5 ) {
				$review_rating = 5;
			}

			if ( '' === $review_title && '' === $review_text && '' === $review_author && '' === $review_time ) {
				continue;
			}

			$reviews[] = array(
				'title'  => $review_title,
				'text'   => $review_text,
				'author' => $review_author,
				'time'   => $review_time,
				'link'   => $review_link,
				'rating' => $review_rating,
			);
		}
	}
}

if ( empty( $reviews ) ) {
	return;
}
?>
<section class="bb-reviews-section" id="reviews" data-reviews>
	<div class="bb-container">
		<div class="bb-reviews__header">
			<div class="bb-reviews__intro">
				<?php if ( $section_title ) : ?>
					<h2 class="bb-reviews__title"><?php echo esc_html( $section_title ); ?></h2>
				<?php endif; ?>
				<?php if ( $section_text ) : ?>
					<p class="bb-reviews__text"><?php echo esc_html( $section_text ); ?></p>
				<?php endif; ?>
			</div>

			<div class="bb-reviews__controls" aria-label="<?php esc_attr_e( 'Reviews slider controls', 'brooklyn-beauty' ); ?>">
				<button class="bb-reviews__arrow bb-reviews__arrow--prev" type="button" data-reviews-nav="prev" aria-label="<?php esc_attr_e( 'Previous reviews', 'brooklyn-beauty' ); ?>"></button>
				<button class="bb-reviews__arrow bb-reviews__arrow--next" type="button" data-reviews-nav="next" aria-label="<?php esc_attr_e( 'Next reviews', 'brooklyn-beauty' ); ?>"></button>
			</div>
		</div>

		<div class="bb-reviews__content">
			<?php if ( $section_label ) : ?>
				<p class="bb-reviews__label"><?php echo nl2br( esc_html( $section_label ) ); ?></p>
			<?php endif; ?>

			<div class="bb-reviews__track" data-reviews-track>
				<?php foreach ( $reviews as $review ) : ?>
					<article class="bb-review-card">
						<div class="bb-review-card__meta">
							<p class="bb-review-card__stars" aria-label="<?php echo esc_attr( sprintf( _n( '%d star', '%d stars', (int) $review['rating'], 'brooklyn-beauty' ), (int) $review['rating'] ) ); ?>">
								<?php for ( $star_index = 0; $star_index < (int) $review['rating']; $star_index++ ) : ?>
									<img class="bb-review-card__star-icon" src="<?php echo esc_url( $star_icon_url ); ?>" alt="" aria-hidden="true" loading="lazy">
								<?php endfor; ?>
							</p>
							<span class="bb-review-card__source" aria-hidden="true">
								<img class="bb-review-card__source-icon" src="<?php echo esc_url( $google_icon_url ); ?>" alt="" loading="lazy">
							</span>
						</div>
						<h3 class="bb-review-card__title"><?php echo esc_html( $review['title'] ); ?></h3>
						<p class="bb-review-card__text"><?php echo esc_html( $review['text'] ); ?></p>
						<div class="bb-review-card__footer">
							<div>
								<p class="bb-review-card__author"><?php echo esc_html( $review['author'] ); ?></p>
								<p class="bb-review-card__date"><?php echo esc_html( $review['time'] ); ?></p>
							</div>
							<?php if ( ! empty( $review['link'] ) ) : ?>
								<a class="bb-review-card__link" href="<?php echo esc_url( $review['link'] ); ?>">
									<?php esc_html_e( 'read more', 'brooklyn-beauty' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
