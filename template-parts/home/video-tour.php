<?php
/**
 * Video tour block
 *
 * @package Brooklyn_Beauty
 */
$section_title = __( 'Video Tour', 'brooklyn-beauty' );
$section_label = __( 'step_inside', 'brooklyn-beauty' );
$section_text  = __(
	'Feel the atmosphere and vibe of Brooklyn Beauty Lounge. Take a look at our online space, and a video tour will help you get an impression of the salon before your visit!',
	'brooklyn-beauty'
);
$video_source = 'url';
$video_url    = '';
$video_file_id   = 0;
$video_file_url  = '';
$video_file_type = '';
$video_render    = 'none';
$video_embed     = '';
$video_direct_url  = '';
$video_direct_type = '';

if ( function_exists( 'get_field' ) ) {
	$front_page_id = (int) get_option( 'page_on_front' );
	$field_post_id = $front_page_id > 0 ? $front_page_id : get_queried_object_id();

	$acf_title = trim( (string) get_field( 'video_tour_title', $field_post_id ) );
	if ( '' !== $acf_title ) {
		$section_title = $acf_title;
	}

	$acf_label = trim( (string) get_field( 'video_tour_label', $field_post_id ) );
	if ( '' !== $acf_label ) {
		$section_label = $acf_label;
	}

	$acf_text = trim( (string) get_field( 'video_tour_text', $field_post_id ) );
	if ( '' !== $acf_text ) {
		$section_text = $acf_text;
	}

	$acf_video_source = trim( (string) get_field( 'video_tour_video_source', $field_post_id ) );
	if ( in_array( $acf_video_source, array( 'url', 'file' ), true ) ) {
		$video_source = $acf_video_source;
	}

	$acf_video_url = trim( (string) get_field( 'video_tour_video_url', $field_post_id ) );
	if ( '' !== $acf_video_url ) {
		$video_url = $acf_video_url;
	}

	$acf_video_file = get_field( 'video_tour_video_file', $field_post_id );
	if ( is_numeric( $acf_video_file ) ) {
		$video_file_id = (int) $acf_video_file;
	} elseif ( is_array( $acf_video_file ) && ! empty( $acf_video_file['ID'] ) ) {
		$video_file_id = (int) $acf_video_file['ID'];
	}
}

if ( $video_file_id > 0 ) {
	$video_file_url = (string) wp_get_attachment_url( $video_file_id );
	$video_file_mime = (string) get_post_mime_type( $video_file_id );

	if ( '' !== $video_file_mime && 0 === strpos( $video_file_mime, 'video/' ) ) {
		$video_file_type = $video_file_mime;
	} else {
		$filetype = wp_check_filetype( $video_file_url );
		if ( ! empty( $filetype['type'] ) ) {
			$video_file_type = (string) $filetype['type'];
		}
	}
}

if ( 'file' === $video_source && '' !== $video_file_url ) {
	$video_render = 'file';
} elseif ( 'url' === $video_source && '' !== $video_url ) {
	$video_embed = (string) wp_oembed_get( $video_url );

	if ( '' !== $video_embed ) {
		$video_render = 'oembed';
		if ( preg_match( '/<iframe\b/i', $video_embed ) && ! preg_match( '/\sloading\s*=/i', $video_embed ) ) {
			$video_embed = preg_replace( '/<iframe\b/i', '<iframe loading="lazy"', $video_embed, 1 );
		}
	} else {
		$url_filetype = wp_check_filetype( $video_url );
		$url_mime     = ! empty( $url_filetype['type'] ) ? (string) $url_filetype['type'] : '';

		if ( '' !== $url_mime && 0 === strpos( $url_mime, 'video/' ) ) {
			$video_render      = 'direct';
			$video_direct_url  = $video_url;
			$video_direct_type = $url_mime;
		} else {
			$video_render = 'unsupported';
		}
	}
}
?>
<section class="bb-section" id="video-tour">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-section__title bb-video-tour__title">
				<?php echo esc_html( $section_title ); ?>
				<?php if ( $section_label ) : ?>
					<span class="bb-video-tour__label"><?php echo esc_html( $section_label ); ?></span>
				<?php endif; ?>
			</h2>
		<?php endif; ?>
		<?php if ( $section_text ) : ?>
			<p class="bb-video-tour__text"><?php echo esc_html( $section_text ); ?></p>
		<?php endif; ?>
		<div class="bb-video-wrapper">
			<?php if ( 'file' === $video_render ) : ?>
				<div class="bb-video bb-video--has-play">
					<video playsinline preload="metadata" loop loading="lazy">
						<source src="<?php echo esc_url( $video_file_url ); ?>"<?php echo $video_file_type ? ' type="' . esc_attr( $video_file_type ) . '"' : ''; ?>>
					</video>
					<button class="bb-video__play-btn" type="button" aria-label="<?php esc_attr_e( 'Play video', 'brooklyn-beauty' ); ?>"></button>
				</div>
			<?php elseif ( 'oembed' === $video_render ) : ?>
				<div class="bb-video">
					<?php echo $video_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php elseif ( 'direct' === $video_render ) : ?>
				<div class="bb-video bb-video--has-play">
					<video playsinline preload="metadata" loop loading="lazy">
						<source src="<?php echo esc_url( $video_direct_url ); ?>"<?php echo $video_direct_type ? ' type="' . esc_attr( $video_direct_type ) . '"' : ''; ?>>
					</video>
					<button class="bb-video__play-btn" type="button" aria-label="<?php esc_attr_e( 'Play video', 'brooklyn-beauty' ); ?>"></button>
				</div>
			<?php elseif ( 'unsupported' === $video_render ) : ?>
				<div class="bb-video" style="display: flex; align-items: center; justify-content: center; color: #999;">
					<p><?php esc_html_e( 'Unsupported video URL format. Please use YouTube/Vimeo link or direct video file URL.', 'brooklyn-beauty' ); ?></p>
				</div>
			<?php else : ?>
				<div class="bb-video" style="display: flex; align-items: center; justify-content: center; color: #999;">
					<p><?php esc_html_e( 'Add a video in ACF fields to display the tour here.', 'brooklyn-beauty' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
