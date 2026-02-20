<?php
/**
 * Single service "Reasons" block — headline + benefits list, video, CTA.
 *
 * @package Brooklyn_Beauty
 */

$service_id = isset( $args['service_id'] ) ? (int) $args['service_id'] : (int) get_the_ID();

$headline = __( 'choosing premium hair services in brooklyn beauty lounge, you will get:', 'brooklyn-beauty' );

$fallback_items = array(
	__( 'A cozy atmosphere, professional and sterile instruments, and the latest modern tools', 'brooklyn-beauty' ),
	__( 'Premium beauty services from a highly skilled professional team', 'brooklyn-beauty' ),
	__( 'Visible and lasting hair and skin care results', 'brooklyn-beauty' ),
	__( 'A personalized experience due to your needs', 'brooklyn-beauty' ),
	__( 'A right care products to keep your hair and skin beautiful and healthy', 'brooklyn-beauty' ),
	__( 'Unforgettable experience that you will want to repeat again and again!', 'brooklyn-beauty' ),
);

$slogan = __( 'exclusive experience', 'brooklyn-beauty' );
$button_text = __( 'book now', 'brooklyn-beauty' );
$button_link = home_url( '/#book' );
$video_poster_url = '';
$video_url        = '';
$video_file_url   = '';

if ( function_exists( 'get_field' ) && $service_id > 0 ) {
	$acf_headline = trim( (string) get_field( 'service_reasons_headline', $service_id ) );
	if ( '' !== $acf_headline ) {
		$headline = $acf_headline;
	}

	$acf_items = get_field( 'service_reasons_items', $service_id );
	if ( is_array( $acf_items ) && ! empty( $acf_items ) ) {
		$fallback_items = array_values( array_filter( array_map( function ( $item ) {
			$text = is_array( $item ) ? ( isset( $item['text'] ) ? trim( (string) $item['text'] ) : '' ) : trim( (string) $item );
			return '' !== $text ? $text : null;
		}, $acf_items ) ) );
		if ( empty( $fallback_items ) ) {
			$fallback_items = array(
				__( 'A cozy atmosphere, professional and sterile instruments, and the latest modern tools', 'brooklyn-beauty' ),
				__( 'Premium beauty services from a highly skilled professional team', 'brooklyn-beauty' ),
				__( 'Visible and lasting hair and skin care results', 'brooklyn-beauty' ),
				__( 'A personalized experience due to your needs', 'brooklyn-beauty' ),
				__( 'A right care products to keep your hair and skin beautiful and healthy', 'brooklyn-beauty' ),
				__( 'Unforgettable experience that you will want to repeat again and again!', 'brooklyn-beauty' ),
			);
		}
	}

	$acf_slogan = trim( (string) get_field( 'service_reasons_slogan', $service_id ) );
	if ( '' !== $acf_slogan ) {
		$slogan = $acf_slogan;
	}

	$acf_btn = trim( (string) get_field( 'service_reasons_button_text', $service_id ) );
	if ( '' !== $acf_btn ) {
		$button_text = $acf_btn;
	}

	$acf_btn_link = trim( (string) get_field( 'service_reasons_button_link', $service_id ) );
	if ( '' !== $acf_btn_link ) {
		$button_link = $acf_btn_link;
	}

	$acf_poster = get_field( 'service_reasons_video_poster', $service_id );
	if ( is_numeric( $acf_poster ) ) {
		$video_poster_url = (string) wp_get_attachment_image_url( (int) $acf_poster, 'large' );
	} elseif ( is_array( $acf_poster ) && ! empty( $acf_poster['url'] ) ) {
		$video_poster_url = (string) $acf_poster['url'];
	}

	$acf_video = trim( (string) get_field( 'service_reasons_video_url', $service_id ) );
	if ( '' !== $acf_video ) {
		$video_url = $acf_video;
	}

	$acf_video_file = get_field( 'service_reasons_video_file', $service_id );
	if ( is_numeric( $acf_video_file ) ) {
		$video_file_url = (string) wp_get_attachment_url( (int) $acf_video_file );
	} elseif ( is_array( $acf_video_file ) && ! empty( $acf_video_file['url'] ) ) {
		$video_file_url = (string) $acf_video_file['url'];
	} elseif ( is_string( $acf_video_file ) && '' !== trim( $acf_video_file ) ) {
		$video_file_url = trim( $acf_video_file );
	}
}

$heart_icon_url = get_template_directory_uri() . '/assets/images/heart.svg';
?>
<section class="bb-service-reasons" aria-labelledby="bb-service-reasons-heading">
	<div class="bb-service-reasons__headline-wrap">
		<div class="bb-container">
			<h2 id="bb-service-reasons-heading" class="bb-service-reasons__headline"><?php echo esc_html( $headline ); ?></h2>
		</div>
	</div>

	<div class="bb-service-reasons__content-wrap">
		<div class="bb-container bb-service-reasons__content-inner">
			<div class="bb-service-reasons__grid">
				<div class="bb-service-reasons__list-col">
					<ul class="bb-service-reasons__list" aria-label="<?php esc_attr_e( 'Benefits', 'brooklyn-beauty' ); ?>">
						<?php foreach ( $fallback_items as $item_text ) : ?>
							<li class="bb-service-reasons__list-item">
								<span class="bb-service-reasons__list-icon" aria-hidden="true">
									<img src="<?php echo esc_url( $heart_icon_url ); ?>" alt="" width="24" height="23" loading="lazy" decoding="async">
								</span>
								<span class="bb-service-reasons__list-text"><?php echo esc_html( $item_text ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="bb-service-reasons__video-col">
					<div class="bb-service-reasons__video-wrap"
						<?php if ( '' !== $video_url ) : ?>
							data-video-url="<?php echo esc_attr( $video_url ); ?>"
						<?php endif; ?>
						<?php if ( '' !== $video_file_url ) : ?>
							data-video-file="<?php echo esc_attr( $video_file_url ); ?>"
						<?php endif; ?>>
						<?php if ( '' !== $video_file_url ) : ?>
							<video class="bb-service-reasons__video-el" playsinline preload="metadata" controls
								<?php if ( '' !== $video_poster_url ) : ?>poster="<?php echo esc_url( $video_poster_url ); ?>"<?php endif; ?>>
								<source src="<?php echo esc_url( $video_file_url ); ?>" type="video/mp4">
							</video>
						<?php endif; ?>
						<?php if ( '' !== $video_file_url ) : ?>
							<div class="bb-service-reasons__video-poster-overlay" aria-hidden="true"></div>
							<?php if ( '' !== $video_poster_url ) : ?>
								<img class="bb-service-reasons__video-poster" src="<?php echo esc_url( $video_poster_url ); ?>" alt="" loading="lazy" decoding="async" data-poster>
							<?php endif; ?>
						<?php elseif ( '' !== $video_poster_url ) : ?>
							<img class="bb-service-reasons__video-poster" src="<?php echo esc_url( $video_poster_url ); ?>" alt="" loading="lazy" decoding="async">
						<?php else : ?>
							<div class="bb-service-reasons__video-placeholder" aria-hidden="true"></div>
						<?php endif; ?>
						<button type="button" class="bb-service-reasons__play" aria-label="<?php esc_attr_e( 'Play video', 'brooklyn-beauty' ); ?>">
							<span class="bb-service-reasons__play-icon" aria-hidden="true"></span>
						</button>
					</div>
				</div>

				<div class="bb-service-reasons__cta-col">
					<p class="bb-service-reasons__slogan t-decor"><?php echo esc_html( $slogan ); ?></p>
					<a class="btn btn--medium bb-service-reasons__btn" href="<?php echo esc_url( $button_link ); ?>">
						<?php echo esc_html( $button_text ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
