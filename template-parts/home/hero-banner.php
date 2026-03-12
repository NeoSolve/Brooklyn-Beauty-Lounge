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
$hero_video_id      = 0;
$hero_video_url     = '';
$hero_video_type    = '';
$hero_video_tagline_1 = '';
$hero_video_tagline_2 = '';
$hero_video_labels  = array();

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

	$hero_image_mobile = '';
	$acf_hero_image_mobile = get_field( 'hero_background_image_mobile', $front_page_id );
	if ( is_numeric( $acf_hero_image_mobile ) ) {
		$url_mobile = wp_get_attachment_image_url( (int) $acf_hero_image_mobile, 'full' );
		if ( $url_mobile ) {
			$hero_image_mobile = $url_mobile;
		}
	} elseif ( is_array( $acf_hero_image_mobile ) && ! empty( $acf_hero_image_mobile['ID'] ) ) {
		$url_mobile = wp_get_attachment_image_url( (int) $acf_hero_image_mobile['ID'], 'full' );
		if ( $url_mobile ) {
			$hero_image_mobile = $url_mobile;
		}
	} elseif ( is_string( $acf_hero_image_mobile ) && '' !== trim( $acf_hero_image_mobile ) ) {
		$hero_image_mobile = trim( $acf_hero_image_mobile );
	}

	$acf_hero_video = get_field( 'hero_video', $front_page_id );
	if ( is_numeric( $acf_hero_video ) ) {
		$hero_video_id = (int) $acf_hero_video;
	} elseif ( is_array( $acf_hero_video ) && ! empty( $acf_hero_video['ID'] ) ) {
		$hero_video_id = (int) $acf_hero_video['ID'];
	}

	$acf_tagline_1 = trim( (string) get_field( 'hero_video_tagline_line_1', $front_page_id ) );
	if ( '' !== $acf_tagline_1 ) {
		$hero_video_tagline_1 = $acf_tagline_1;
	}
	$acf_tagline_2 = trim( (string) get_field( 'hero_video_tagline_line_2', $front_page_id ) );
	if ( '' !== $acf_tagline_2 ) {
		$hero_video_tagline_2 = $acf_tagline_2;
	}
	$acf_labels = get_field( 'hero_video_labels', $front_page_id );
	if ( is_array( $acf_labels ) && ! empty( $acf_labels ) ) {
		$hero_video_labels = array_values( array_filter( array_map( function ( $row ) {
			return isset( $row['label'] ) ? trim( (string) $row['label'] ) : '';
		}, $acf_labels ) ) );
	}
}

if ( '' === $hero_video_tagline_1 ) {
	$hero_video_tagline_1 = __( 'Be fabulous with', 'brooklyn-beauty' );
}
if ( '' === $hero_video_tagline_2 ) {
	$hero_video_tagline_2 = __( 'Brooklyn Beauty Lounge!', 'brooklyn-beauty' );
}
if ( empty( $hero_video_labels ) ) {
	$hero_video_labels = array(
		__( 'beauty', 'brooklyn-beauty' ),
		__( 'salon', 'brooklyn-beauty' ),
		__( 'nails', 'brooklyn-beauty' ),
		__( 'hair services', 'brooklyn-beauty' ),
	);
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
$hero_class = 'bb-hero';
if ( '' !== $hero_image ) {
	if ( '' !== $hero_image_mobile ) {
		$hero_class .= ' has-mobile-bg';
		$hero_style = ' style="--hero-bg-desktop: url(' . esc_url( $hero_image ) . '); --hero-bg-mobile: url(' . esc_url( $hero_image_mobile ) . ');"';
	} else {
		$hero_style = ' style="background-image: url(' . esc_url( $hero_image ) . ');"';
	}
}
?>
<section class="<?php echo esc_attr( $hero_class ); ?>"<?php echo $hero_style; ?>>
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
			<div class="bb-hero__video-wrap">
				<p class="bb-hero__video-tagline t-decor-2">
					<?php echo esc_html( $hero_video_tagline_1 ); ?><br>
					<?php echo esc_html( $hero_video_tagline_2 ); ?>
				</p>
				<?php
				$open_icon  = get_template_directory_uri() . '/assets/images/open.svg';
				$close_icon = get_template_directory_uri() . '/assets/images/close.svg';
				?>
				<div class="bb-hero__video-fullscreen" id="bb-hero-video-fullscreen">
					<div class="bb-hero__video-card">
						<div class="bb-hero__video-media">
							<video autoplay muted loop playsinline preload="metadata" data-hero-video>
								<source src="<?php echo esc_url( $hero_video_url ); ?>"<?php echo $hero_video_type ? ' type="' . esc_attr( $hero_video_type ) . '"' : ''; ?>>
							</video>
							<button type="button" class="bb-hero__video-btn bb-hero__video-btn-open" aria-label="<?php esc_attr_e( 'Open video fullscreen', 'brooklyn-beauty' ); ?>" data-hero-video-open>
								<img src="<?php echo esc_url( $open_icon ); ?>" width="44" height="44" alt="" aria-hidden="true" loading="lazy" decoding="async">
							</button>
						</div>
					</div>
					<button type="button" class="bb-hero__video-btn bb-hero__video-btn-close" aria-label="<?php esc_attr_e( 'Close fullscreen', 'brooklyn-beauty' ); ?>" data-hero-video-close hidden>
						<img src="<?php echo esc_url( $close_icon ); ?>" width="44" height="44" alt="" aria-hidden="true" loading="lazy" decoding="async">
					</button>
				</div>
				<?php if ( ! empty( $hero_video_labels ) ) : ?>
				<div class="bb-hero__video-labels">
					<?php foreach ( $hero_video_labels as $label ) : ?>
						<span class="bb-hero__video-label"><?php echo esc_html( $label ); ?></span>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
