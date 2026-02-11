<?php
/**
 * Video tour block
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_video_title', __( 'Video Tour', 'brooklyn-beauty' ) );
$video_url = get_theme_mod( 'bb_video_url', '' );
?>
<section class="bb-section" id="video-tour">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-section__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<div class="bb-video-wrapper">
			<?php if ( $video_url ) : ?>
				<div class="bb-video">
					<?php echo wp_oembed_get( $video_url ); ?>
				</div>
			<?php else : ?>
				<div class="bb-video" style="display: flex; align-items: center; justify-content: center; color: #999;">
					<p><?php esc_html_e( 'Add a video URL in Customizer to display the tour here.', 'brooklyn-beauty' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
