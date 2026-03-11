<?php
/**
 * SEO-related filters and metadata.
 *
 * Outputs Open Graph meta tags (og:title, og:type, og:url, og:description, og:image)
 * for single blog posts. Falls back to ACF meta_description, Yoast meta desc,
 * excerpt, or trimmed content.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output Open Graph meta tags for single posts.
 */
function brooklyn_beauty_og_meta_tags() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post_id   = (int) get_the_ID();
	$post      = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	$og_title   = wp_strip_all_tags( (string) get_the_title( $post_id ) );
	$og_url     = (string) get_permalink( $post_id );
	$og_type    = 'article';

	$og_description = '';
	if ( function_exists( 'get_field' ) ) {
		$acf_desc = trim( (string) get_field( 'meta_description', $post_id ) );
		if ( '' !== $acf_desc ) {
			$og_description = $acf_desc;
		}
	}
	if ( '' === $og_description && defined( 'WPSEO_VERSION' ) ) {
		$yoast_desc = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		if ( is_string( $yoast_desc ) && '' !== trim( $yoast_desc ) ) {
			$og_description = $yoast_desc;
		}
	}
	if ( '' === $og_description ) {
		$og_description = trim( (string) get_the_excerpt( $post_id ) );
	}
	if ( '' === $og_description ) {
		$og_description = wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 30, '...' );
	}

	$og_image = '';
	if ( function_exists( 'get_field' ) ) {
		$acf_image = get_field( 'single_post_hero_image', $post_id );
		if ( is_numeric( $acf_image ) ) {
			$og_image = (string) wp_get_attachment_image_url( (int) $acf_image, 'large' );
		} elseif ( is_array( $acf_image ) && ! empty( $acf_image['url'] ) ) {
			$og_image = (string) $acf_image['url'];
		}
	}
	if ( '' === $og_image && has_post_thumbnail( $post_id ) ) {
		$og_image = (string) get_the_post_thumbnail_url( $post_id, 'large' );
	}

	?>
	<meta property="og:title" content="<?php echo esc_attr( $og_title ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $og_type ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $og_url ); ?>">
	<?php if ( '' !== $og_description ) : ?>
		<meta property="og:description" content="<?php echo esc_attr( $og_description ); ?>">
	<?php endif; ?>
	<?php if ( '' !== $og_image ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $og_image ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'brooklyn_beauty_og_meta_tags', 5 );
