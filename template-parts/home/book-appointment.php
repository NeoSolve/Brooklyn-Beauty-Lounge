<?php
/**
 * Book an appointment CTA block
 *
 * @package Brooklyn_Beauty
 */
$section_title            = '';
$section_text_top         = '';
$section_text_bottom      = '';
$btn_text                 = '';
$btn_url                  = '';
$content_image_url        = '';
$main_image_url           = '';
$fallback_media_image_url = '';
$section_id               = 'book';

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

	$acf_title = trim( (string) get_field( 'book_appointment_title', $field_post_id ) );
	if ( '' !== $acf_title ) {
		$section_title = $acf_title;
	}

	$acf_text_top = trim( (string) get_field( 'book_appointment_text_top', $field_post_id ) );
	if ( '' !== $acf_text_top ) {
		$section_text_top = $acf_text_top;
	}

	$acf_text_bottom = trim( (string) get_field( 'book_appointment_text_bottom', $field_post_id ) );
	if ( '' !== $acf_text_bottom ) {
		$section_text_bottom = $acf_text_bottom;
	}

	$acf_button_text = trim( (string) get_field( 'book_appointment_button_text', $field_post_id ) );
	if ( '' !== $acf_button_text ) {
		$btn_text = $acf_button_text;
	}

	$acf_button_link = trim( (string) get_field( 'book_appointment_button_link', $field_post_id ) );
	if ( '' !== $acf_button_link ) {
		$btn_url = $acf_button_link;
	}

	$acf_content_image = get_field( 'book_appointment_content_image', $field_post_id );
	$resolved_content  = $resolve_image_url( $acf_content_image );
	if ( '' !== $resolved_content ) {
		$content_image_url = $resolved_content;
	}

	$acf_main_image  = get_field( 'book_appointment_main_image', $field_post_id );
	$resolved_main   = $resolve_image_url( $acf_main_image );
	if ( '' !== $resolved_main ) {
		$main_image_url = $resolved_main;
	}

	$acf_fallback_media = get_field( 'book_appointment_fallback_media_image', $field_post_id );
	$resolved_fallback  = $resolve_image_url( $acf_fallback_media );
	if ( '' !== $resolved_fallback ) {
		$fallback_media_image_url = $resolved_fallback;
	}
}

$media_image_url = $main_image_url;
$is_fallback     = false;

if ( '' === $media_image_url && '' !== $fallback_media_image_url ) {
	$media_image_url = $fallback_media_image_url;
	$is_fallback     = true;
}

$button_link_target = $btn_url;
$button_attributes  = '';

if ( '#' === substr( $btn_url, 0, 1 ) ) {
	$button_link_target = $btn_url;
} elseif ( false === strpos( $btn_url, '://' ) && '/' !== substr( $btn_url, 0, 1 ) && '#' !== substr( $btn_url, 0, 1 ) ) {
	$button_link_target = '#' . ltrim( $btn_url, '#' );
}

if ( '#' === substr( $button_link_target, 0, 1 ) ) {
	$button_attributes = ' data-book-scroll';
}
?>
<section class="bb-book-section" id="<?php echo esc_attr( $section_id ); ?>" data-book-appointment>
	<div class="bb-container">
		<div class="bb-book">
			<div class="bb-book__content">
				<?php if ( $section_title ) : ?>
					<h2 class="bb-book__title"><?php echo esc_html( $section_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $section_text_top ) : ?>
					<p class="bb-book__text bb-book__text--top"><?php echo esc_html( $section_text_top ); ?></p>
				<?php endif; ?>

				<?php if ( $content_image_url ) : ?>
					<figure class="bb-book__preview">
						<img class="bb-book__preview-image" src="<?php echo esc_url( $content_image_url ); ?>" alt="<?php esc_attr_e( 'Service preview', 'brooklyn-beauty' ); ?>" loading="lazy">
					</figure>
				<?php endif; ?>

				<?php if ( $section_text_bottom ) : ?>
					<p class="bb-book__text bb-book__text--bottom"><?php echo esc_html( $section_text_bottom ); ?></p>
				<?php endif; ?>
			</div>

			<div class="bb-book__media<?php echo $is_fallback ? ' is-fallback' : ''; ?>">
				<?php if ( '' !== $media_image_url ) : ?>
					<img class="bb-book__media-image" src="<?php echo esc_url( $media_image_url ); ?>" alt="<?php esc_attr_e( 'Book an appointment', 'brooklyn-beauty' ); ?>" loading="lazy">
				<?php endif; ?>

				<?php if ( $btn_text ) : ?>
					<a href="<?php echo esc_url( $button_link_target ); ?>" class="btn btn--small bb-book__btn"<?php echo wp_kses_post( $button_attributes ); ?>><?php echo esc_html( $btn_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
