<?php
/**
 * Hero banner block
 *
 * @package Brooklyn_Beauty
 */
$hero_title         = '';
$hero_subtitle      = '';
$hero_description   = '';
$hero_description_2 = '';
$hero_btn_text      = '';
$hero_btn_url       = '';
$hero_image         = '';
$hero_video_id   = 0;
$hero_video_url  = '';
$hero_video_type = '';

if ( function_exists( 'get_field' ) ) {
	$front_page_id = (int) get_option( 'page_on_front' );

	if ( $front_page_id <= 0 ) {
		$front_page_id = (int) get_queried_object_id();
	}

	$acf_hero_title = (string) get_field( 'hero_title', $front_page_id );
	if ( '' !== trim( $acf_hero_title ) ) {
		$hero_title = $acf_hero_title;
	}

	$acf_hero_subtitle = (string) get_field( 'hero_subtitle', $front_page_id );
	if ( '' !== trim( $acf_hero_subtitle ) ) {
		$hero_subtitle = $acf_hero_subtitle;
	}

	$acf_hero_description = (string) get_field( 'hero_description', $front_page_id );
	if ( '' !== trim( $acf_hero_description ) ) {
		$hero_description = $acf_hero_description;
	}

	$acf_hero_description_2 = (string) get_field( 'hero_description_2', $front_page_id );
	if ( '' !== trim( $acf_hero_description_2 ) ) {
		$hero_description_2 = $acf_hero_description_2;
	}

	$acf_hero_button_text = (string) get_field( 'hero_button_text', $front_page_id );
	if ( '' !== trim( $acf_hero_button_text ) ) {
		$hero_btn_text = $acf_hero_button_text;
	}

	$acf_hero_button_link = (string) get_field( 'hero_button_link', $front_page_id );
	if ( '' !== trim( $acf_hero_button_link ) ) {
		$hero_btn_url = $acf_hero_button_link;
	}

	$acf_hero_image = get_field( 'hero_background_image', $front_page_id );
	if ( is_numeric( $acf_hero_image ) ) {
		$acf_hero_image_url = wp_get_attachment_image_url( (int) $acf_hero_image, 'full' );
		if ( $acf_hero_image_url ) {
			$hero_image = $acf_hero_image_url;
		}
	} elseif ( is_array( $acf_hero_image ) && ! empty( $acf_hero_image['ID'] ) ) {
		$acf_hero_image_url = wp_get_attachment_image_url( (int) $acf_hero_image['ID'], 'full' );
		if ( $acf_hero_image_url ) {
			$hero_image = $acf_hero_image_url;
		}
	} elseif ( is_string( $acf_hero_image ) && '' !== trim( $acf_hero_image ) ) {
		$hero_image = $acf_hero_image;
	}

	$acf_hero_video = get_field( 'hero_video', $front_page_id );
	if ( is_numeric( $acf_hero_video ) ) {
		$hero_video_id = (int) $acf_hero_video;
	} elseif ( is_array( $acf_hero_video ) && ! empty( $acf_hero_video['ID'] ) ) {
		$hero_video_id = (int) $acf_hero_video['ID'];
	}
}

if ( $hero_video_id > 0 ) {
	$hero_video_url = (string) wp_get_attachment_url( $hero_video_id );
	$hero_video_mime = (string) get_post_mime_type( $hero_video_id );

	if ( '' !== $hero_video_mime && 0 === strpos( $hero_video_mime, 'video/' ) ) {
		$hero_video_type = $hero_video_mime;
	} else {
		$hero_video_filetype = wp_check_filetype( $hero_video_url );
		if ( ! empty( $hero_video_filetype['type'] ) ) {
			$hero_video_type = (string) $hero_video_filetype['type'];
		}
	}
}

$hero_style = '';
if ( '' !== $hero_image ) {
	$hero_style = ' style="background-image: url(' . esc_url( $hero_image ) . ');"';
}
?>
<section class="bb-hero"<?php echo $hero_style; ?>>
	<div class="bb-hero__overlay" aria-hidden="true"></div>
	
	<div class="bb-hero__inner bb-container">
		<div class="bb-hero__content">
			<h1 class="bb-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
		<?php if ( $hero_subtitle ) : ?>
			<p class="bb-hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
		<?php endif; ?>
		<div class="bb-hero__body">
			<?php if ( $hero_description ) : ?>
				<p class="bb-hero__description"><?php echo esc_html( $hero_description ); ?></p>
			<?php endif; ?>
			<?php if ( $hero_description_2 ) : ?>
				<p class="bb-hero__description"><?php echo esc_html( $hero_description_2 ); ?></p>
			<?php endif; ?>
			<?php if ( $hero_btn_text && $hero_btn_url ) : ?>
				<a href="<?php echo esc_url( $hero_btn_url ); ?>" class="btn btn--medium"><?php echo esc_html( $hero_btn_text ); ?></a>
			<?php endif; ?>
		</div>
		</div>

		<?php if ( '' !== $hero_video_url ) : ?>
			<div class="bb-hero__video-card">
				<div class="bb-hero__video-media">
					<video autoplay muted loop playsinline controls preload="metadata">
						<source src="<?php echo esc_url( $hero_video_url ); ?>"<?php echo $hero_video_type ? ' type="' . esc_attr( $hero_video_type ) . '"' : ''; ?>>
					</video>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
