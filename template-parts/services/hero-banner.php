<?php
/**
 * Services page hero banner.
 *
 * @package Brooklyn_Beauty
 */

$page_id = (int) get_queried_object_id();

$hero_title       = __( 'services', 'brooklyn-beauty' );
$hero_tagline     = __( 'be fabulous with brooklyn beauty lounge!', 'brooklyn-beauty' );
$hero_subtitle    = __( 'self-care', 'brooklyn-beauty' );
$hero_description = __( 'Brooklyn Beauty Lounge offers everything from hair and makeup to facials, nails and laser treatments, providing personalized care and expert techniques that make you look and feel your best.', 'brooklyn-beauty' );
$hero_image_url   = '';
$hero_image_alt   = $hero_title ? $hero_title : get_bloginfo( 'name' );

if ( function_exists( 'get_field' ) ) {
	$acf_hero_tagline = trim( (string) get_field( 'services_hero_tagline', $page_id ) );
	if ( '' !== $acf_hero_tagline ) {
		$hero_tagline = $acf_hero_tagline;
	}

	$acf_hero_subtitle = trim( (string) get_field( 'services_hero_subtitle', $page_id ) );
	if ( '' !== $acf_hero_subtitle ) {
		$hero_subtitle = $acf_hero_subtitle;
	}

	$acf_hero_description = trim( (string) get_field( 'services_hero_description', $page_id ) );
	if ( '' !== $acf_hero_description ) {
		$hero_description = $acf_hero_description;
	}

	$acf_hero_image = get_field( 'services_hero_image', $page_id );
	if ( is_numeric( $acf_hero_image ) ) {
		$hero_image_id  = (int) $acf_hero_image;
		$hero_image_url = (string) wp_get_attachment_image_url( $hero_image_id, 'large' );
		$alt_text       = get_post_meta( $hero_image_id, '_wp_attachment_image_alt', true );
		if ( is_string( $alt_text ) && '' !== trim( $alt_text ) ) {
			$hero_image_alt = $alt_text;
		}
	} elseif ( is_array( $acf_hero_image ) ) {
		if ( ! empty( $acf_hero_image['url'] ) ) {
			$hero_image_url = (string) $acf_hero_image['url'];
		}
		if ( ! empty( $acf_hero_image['alt'] ) && is_string( $acf_hero_image['alt'] ) ) {
			$hero_image_alt = $acf_hero_image['alt'];
		}
	} elseif ( is_string( $acf_hero_image ) && '' !== trim( $acf_hero_image ) ) {
		$hero_image_url = $acf_hero_image;
	}
}

if ( '' === $hero_image_url && $page_id > 0 && has_post_thumbnail( $page_id ) ) {
	$hero_image_url = (string) get_the_post_thumbnail_url( $page_id, 'large' );
	$thumb_alt      = get_post_meta( (int) get_post_thumbnail_id( $page_id ), '_wp_attachment_image_alt', true );
	if ( is_string( $thumb_alt ) && '' !== trim( $thumb_alt ) ) {
		$hero_image_alt = $thumb_alt;
	}
}

?>
<section class="bb-services-hero" aria-labelledby="bb-services-hero-title">
	<div class="bb-container bb-services-hero__inner">
		<nav class="bb-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'brooklyn-beauty' ); ?>">
			<a class="bb-breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Home', 'brooklyn-beauty' ); ?>
			</a>
			<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
			<span class="bb-breadcrumbs__current" aria-current="page"><?php echo esc_html( $hero_title ); ?></span>
		</nav>

		<div class="bb-services-hero__stage">
			<h1 class="bb-services-hero__title" id="bb-services-hero-title"><?php echo esc_html( $hero_title ); ?></h1>

			<p class="bb-services-hero__tagline t-decor"><?php echo esc_html( $hero_tagline ); ?></p>
			<p class="bb-services-hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
			<p class="bb-services-hero__description"><?php echo esc_html( $hero_description ); ?></p>

			<div class="bb-services-hero__media">
				<?php if ( '' !== $hero_image_url ) : ?>
					<img src="<?php echo esc_url( $hero_image_url ); ?>" alt="<?php echo esc_attr( $hero_image_alt ); ?>" loading="lazy" decoding="async">
				<?php else : ?>
					<div class="bb-services-hero__media-fallback" aria-hidden="true"></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
