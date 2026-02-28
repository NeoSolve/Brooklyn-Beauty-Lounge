<?php
/**
 * FAQ block
 *
 * @package Brooklyn_Beauty
 */
$left_image_url  = '';
$badge_image_url = '';
$badge_subtitle  = __( 'curious?', 'brooklyn-beauty' );
$default_left_image_url = get_template_directory_uri() . '/assets/images/faq-image.jpg';
$faq_items       = array(
	array(
		'question' => __( 'what safety measures are in place?', 'brooklyn-beauty' ),
		'answer'   => __( 'We understand the importance of feeling comfortable while you are going today. The staff is trained on proper PPE and disinfecting their work areas, tools, and equipment. In addition, we have a designated cleaning crew that cleans and sanitizes our salon daily. We take sanitation logs and have installed extra sanitizing stations throughout the salon. We always use a triple sterilization method with all our tools and equipment (heat, UV, and medical autoclave). We have numerous protocols in place to ensure an extra level of protection for your peace of mind.', 'brooklyn-beauty' ),
	),
	array(
		'question' => __( 'how do I book a service?', 'brooklyn-beauty' ),
		'answer'   => __( 'You can book an appointment via the website, via phone or just walk in to the salon to choose the most relevant date and service!', 'brooklyn-beauty' ),
	),
	array(
		'question' => __( 'can I make an appointment for the day of my visit?', 'brooklyn-beauty' ),
		'answer'   => __( 'Yes, it is possible if there are free time slots and masters available.', 'brooklyn-beauty' ),
	),
	array(
		'question' => __( 'can I reschedule or cancel my appointment?', 'brooklyn-beauty' ),
		'answer'   => __( 'Yes, just tell us about your plans changed via the website, via phone or just walk in to the salon and we will choose a new date for you. Just note our cancellation policies for applicable fees.', 'brooklyn-beauty' ),
	),
	array(
		'question' => __( 'can I choose a specific technician?', 'brooklyn-beauty' ),
		'answer'   => __( 'Yes, it is possible if there are free time slots and masters available.', 'brooklyn-beauty' ),
	),
);

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

	$acf_left_image = get_field( 'faq_left_image', $field_post_id );
	$left_image_url = $resolve_image_url( $acf_left_image );

	$acf_badge_image = get_field( 'faq_badge_image', $field_post_id );
	$badge_image_url = $resolve_image_url( $acf_badge_image );

	$acf_badge_subtitle = trim( (string) get_field( 'faq_badge_subtitle', $field_post_id ) );
	if ( '' !== $acf_badge_subtitle ) {
		$badge_subtitle = $acf_badge_subtitle;
	}

	$acf_faq_items = get_field( 'faq_items', $field_post_id );
	if ( is_array( $acf_faq_items ) && ! empty( $acf_faq_items ) ) {
		$faq_items = array();

		foreach ( $acf_faq_items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$question = isset( $item['question'] ) ? trim( (string) $item['question'] ) : '';
			$answer   = isset( $item['answer'] ) ? trim( (string) $item['answer'] ) : '';

			if ( '' === $question && '' === $answer ) {
				continue;
			}

			$faq_items[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
		}
	}

	// On single post: show block only if this post has FAQ items (no default fallback).
	if ( is_singular( 'post' ) && empty( $faq_items ) ) {
		return;
	}
}

if ( empty( $faq_items ) ) {
	return;
}

if ( '' === $left_image_url ) {
	$left_image_url = $default_left_image_url;
}
?>
<section class="bb-faq-section" id="faq" data-faq>
	<div class="bb-container">
		<div class="bb-faq-layout">
			<div class="bb-faq-layout__left">
				<div class="bb-faq-layout__media">
					<?php if ( '' !== $left_image_url ) : ?>
						<img class="bb-faq-layout__image" src="<?php echo esc_url( $left_image_url ); ?>" alt="<?php esc_attr_e( 'FAQ section image', 'brooklyn-beauty' ); ?>" loading="lazy">
					<?php endif; ?>
				</div>

				<div class="bb-faq-layout__badge">
					<?php if ( '' !== $badge_image_url ) : ?>
						<img class="bb-faq-layout__badge-image" src="<?php echo esc_url( $badge_image_url ); ?>" alt="<?php esc_attr_e( 'FAQ label', 'brooklyn-beauty' ); ?>" loading="lazy">
					<?php else : ?>
						<p class="bb-faq-layout__badge-title">faq</p>
						<p class="bb-faq-layout__badge-subtitle"><?php echo esc_html( $badge_subtitle ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="bb-faq-list" data-faq-list>
				<?php foreach ( $faq_items as $index => $faq ) : ?>
					<?php
					$item_index   = str_pad( (string) ( $index + 1 ), 3, '0', STR_PAD_LEFT );
					$answer_id    = 'bb-faq-answer-' . (string) ( $index + 1 );
					$question_id  = 'bb-faq-question-' . (string) ( $index + 1 );
					$is_open_item = 0 === $index;
					?>
					<article class="bb-faq-item<?php echo $is_open_item ? ' is-open' : ''; ?>" data-faq-item>
						<button
							class="bb-faq-item__trigger"
							type="button"
							id="<?php echo esc_attr( $question_id ); ?>"
							data-faq-trigger
							aria-controls="<?php echo esc_attr( $answer_id ); ?>"
							aria-expanded="<?php echo $is_open_item ? 'true' : 'false'; ?>"
						>
							<span class="bb-faq-item__index"><?php echo esc_html( $item_index ); ?></span>
							<span class="bb-faq-item__question"><?php echo esc_html( $faq['question'] ); ?></span>
						</button>

						<div
							class="bb-faq-item__panel"
							id="<?php echo esc_attr( $answer_id ); ?>"
							data-faq-panel
							role="region"
							aria-labelledby="<?php echo esc_attr( $question_id ); ?>"
						>
							<p class="bb-faq-item__answer"><?php echo esc_html( $faq['answer'] ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
