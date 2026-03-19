<?php
/**
 * SEO-related filters and metadata.
 *
 * Outputs Open Graph meta tags (og:title, og:type, og:url, og:description, og:image)
 * for the current request. Supports manual ACF overrides and sensible fallbacks.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve image URL from ACF field value.
 *
 * @param mixed $image_value ACF image value.
 *
 * @return string
 */
function brooklyn_beauty_get_og_image_url_from_value( $image_value ) {
	if ( is_numeric( $image_value ) ) {
		return (string) wp_get_attachment_image_url( (int) $image_value, 'large' );
	}

	if ( is_array( $image_value ) && ! empty( $image_value['url'] ) ) {
		return (string) $image_value['url'];
	}

	if ( is_string( $image_value ) ) {
		return trim( $image_value );
	}

	return '';
}

/**
 * Get current request URL for Open Graph.
 *
 * @return string
 */
function brooklyn_beauty_get_current_og_url() {
	if ( is_singular() ) {
		return (string) get_permalink();
	}

	if ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		if ( $posts_page_id > 0 ) {
			return (string) get_permalink( $posts_page_id );
		}
	}

	if ( is_post_type_archive( 'service' ) ) {
		return (string) get_post_type_archive_link( 'service' );
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string) $_SERVER['REQUEST_URI'] ) : '/';

	return (string) home_url( $request_uri );
}

/**
 * Get default Open Graph image from site settings.
 *
 * @return string
 */
function brooklyn_beauty_get_default_og_image() {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	return brooklyn_beauty_get_og_image_url_from_value( get_field( 'default_og_image', 'option' ) );
}

/**
 * Build current request URL for canonical tag.
 *
 * Keeps only content-changing query parameters.
 *
 * @return string
 */
function brooklyn_beauty_get_current_canonical_url() {
	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string) $_SERVER['REQUEST_URI'] ) : '/';
	$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );
	$request_path = is_string( $request_path ) && '' !== $request_path ? $request_path : '/';

	$canonical_url = home_url( $request_path );
	$query_string  = wp_parse_url( $request_uri, PHP_URL_QUERY );

	if ( ! is_string( $query_string ) || '' === $query_string ) {
		return $canonical_url;
	}

	parse_str( $query_string, $query_args );
	if ( ! is_array( $query_args ) || empty( $query_args ) ) {
		return $canonical_url;
	}

	$allowed_query_keys = array(
		'category',
		'paged',
		'page',
	);
	$filtered_query_args = array();

	foreach ( $allowed_query_keys as $allowed_query_key ) {
		if ( ! isset( $query_args[ $allowed_query_key ] ) ) {
			continue;
		}

		$query_value = $query_args[ $allowed_query_key ];
		if ( is_array( $query_value ) ) {
			continue;
		}

		$query_value = trim( (string) $query_value );
		if ( '' === $query_value ) {
			continue;
		}

		$filtered_query_args[ $allowed_query_key ] = $query_value;
	}

	if ( empty( $filtered_query_args ) ) {
		return $canonical_url;
	}

	return (string) add_query_arg( $filtered_query_args, $canonical_url );
}

/**
 * Get canonical override set in ACF for a post.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function brooklyn_beauty_get_manual_canonical_url( $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	return trim( (string) get_field( 'canonical_url', (int) $post_id ) );
}

/**
 * Get canonical URL for current request.
 *
 * @return string
 */
function brooklyn_beauty_get_current_canonical_tag_url() {
	if ( is_singular() ) {
		$post_id            = (int) get_queried_object_id();
		$manual_canonical   = brooklyn_beauty_get_manual_canonical_url( $post_id );
		$default_canonical  = (string) get_permalink( $post_id );

		return '' !== $manual_canonical ? $manual_canonical : $default_canonical;
	}

	if ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		if ( $posts_page_id > 0 ) {
			$manual_canonical = brooklyn_beauty_get_manual_canonical_url( $posts_page_id );
			if ( '' !== $manual_canonical ) {
				return $manual_canonical;
			}
		}
	}

	if ( is_post_type_archive( 'service' ) ) {
		return brooklyn_beauty_get_current_canonical_url();
	}

	if ( is_archive() || is_front_page() ) {
		return brooklyn_beauty_get_current_canonical_url();
	}

	return '';
}

/**
 * Build Open Graph data for singular content.
 *
 * @param int $post_id Current post ID.
 *
 * @return array<string, string>
 */
function brooklyn_beauty_get_singular_og_data( $post_id ) {
	$post_id = (int) $post_id;
	$post    = get_post( $post_id );

	if ( ! $post ) {
		return array();
	}

	$og_title       = wp_strip_all_tags( (string) get_the_title( $post_id ) );
	$og_description = '';
	$og_image       = '';
	$og_url         = (string) get_permalink( $post_id );
	$og_type        = 'article';

	if ( 'page' === get_post_type( $post_id ) ) {
		$og_type = 'website';
	}

	if ( function_exists( 'get_field' ) ) {
		$acf_og_title       = trim( (string) get_field( 'og_title', $post_id ) );
		$acf_og_description = trim( (string) get_field( 'og_description', $post_id ) );
		$acf_og_image       = brooklyn_beauty_get_og_image_url_from_value( get_field( 'og_image', $post_id ) );

		if ( '' !== $acf_og_title ) {
			$og_title = $acf_og_title;
		}

		if ( '' !== $acf_og_description ) {
			$og_description = $acf_og_description;
		}

		if ( '' !== $acf_og_image ) {
			$og_image = $acf_og_image;
		}
	}

	if ( '' === $og_description && function_exists( 'get_field' ) ) {
		$acf_meta_description = trim( (string) get_field( 'meta_description', $post_id ) );
		if ( '' !== $acf_meta_description ) {
			$og_description = $acf_meta_description;
		}
	}

	if ( '' === $og_description && defined( 'WPSEO_VERSION' ) ) {
		$yoast_description = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		if ( is_string( $yoast_description ) && '' !== trim( $yoast_description ) ) {
			$og_description = trim( $yoast_description );
		}
	}

	if ( '' === $og_description ) {
		$og_description = trim( (string) get_the_excerpt( $post_id ) );
	}

	if ( '' === $og_description ) {
		$og_description = wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 30, '...' );
	}

	if ( '' === $og_image && function_exists( 'get_field' ) ) {
		if ( 'post' === get_post_type( $post_id ) ) {
			$og_image = brooklyn_beauty_get_og_image_url_from_value( get_field( 'single_post_hero_image', $post_id ) );
		} elseif ( 'service' === get_post_type( $post_id ) ) {
			$og_image = brooklyn_beauty_get_og_image_url_from_value( get_field( 'service_hero_image', $post_id ) );
		} elseif ( 'page' === get_post_type( $post_id ) ) {
			$og_image = brooklyn_beauty_get_og_image_url_from_value( get_field( 'services_hero_image', $post_id ) );
		}
	}

	if ( '' === $og_image && has_post_thumbnail( $post_id ) ) {
		$og_image = (string) get_the_post_thumbnail_url( $post_id, 'large' );
	}

	if ( '' === $og_image ) {
		$og_image = brooklyn_beauty_get_default_og_image();
	}

	return array(
		'title'       => $og_title,
		'description' => $og_description,
		'image'       => $og_image,
		'url'         => $og_url,
		'type'        => $og_type,
	);
}

/**
 * Build Open Graph data for service archive.
 *
 * @return array<string, string>
 */
function brooklyn_beauty_get_service_archive_og_data() {
	$archive_title       = post_type_archive_title( '', false );
	$archive_description = trim( wp_strip_all_tags( (string) get_the_archive_description() ) );
	$og_title            = '' !== $archive_title ? $archive_title : wp_strip_all_tags( wp_get_document_title() );
	$og_description      = $archive_description;
	$og_image            = brooklyn_beauty_get_default_og_image();

	if ( function_exists( 'get_field' ) ) {
		$acf_og_title       = trim( (string) get_field( 'service_archive_og_title', 'option' ) );
		$acf_og_description = trim( (string) get_field( 'service_archive_og_description', 'option' ) );
		$acf_og_image       = brooklyn_beauty_get_og_image_url_from_value( get_field( 'service_archive_og_image', 'option' ) );

		if ( '' !== $acf_og_title ) {
			$og_title = $acf_og_title;
		}

		if ( '' !== $acf_og_description ) {
			$og_description = $acf_og_description;
		}

		if ( '' !== $acf_og_image ) {
			$og_image = $acf_og_image;
		}
	}

	if ( '' === $og_description ) {
		$og_description = trim( (string) get_bloginfo( 'description' ) );
	}

	return array(
		'title'       => $og_title,
		'description' => $og_description,
		'image'       => $og_image,
		'url'         => brooklyn_beauty_get_current_og_url(),
		'type'        => 'website',
	);
}

/**
 * Build fallback Open Graph data for non-singular requests.
 *
 * @return array<string, string>
 */
function brooklyn_beauty_get_fallback_og_data() {
	$og_title       = wp_strip_all_tags( wp_get_document_title() );
	$og_description = '';
	$og_image       = brooklyn_beauty_get_default_og_image();

	if ( is_archive() ) {
		$og_description = trim( wp_strip_all_tags( (string) get_the_archive_description() ) );
	}

	if ( '' === $og_description ) {
		$og_description = trim( (string) get_bloginfo( 'description' ) );
	}

	if ( function_exists( 'get_field' ) ) {
		$acf_default_title       = trim( (string) get_field( 'default_og_title', 'option' ) );
		$acf_default_description = trim( (string) get_field( 'default_og_description', 'option' ) );

		if ( is_front_page() || is_home() || is_404() || is_search() ) {
			if ( '' !== $acf_default_title ) {
				$og_title = $acf_default_title;
			}
			if ( '' !== $acf_default_description ) {
				$og_description = $acf_default_description;
			}
		}
	}

	return array(
		'title'       => $og_title,
		'description' => $og_description,
		'image'       => $og_image,
		'url'         => brooklyn_beauty_get_current_og_url(),
		'type'        => 'website',
	);
}

/**
 * Get Open Graph data for current request.
 *
 * @return array<string, string>
 */
function brooklyn_beauty_get_current_og_data() {
	if ( is_singular() ) {
		return brooklyn_beauty_get_singular_og_data( (int) get_queried_object_id() );
	}

	if ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		if ( $posts_page_id > 0 ) {
			return brooklyn_beauty_get_singular_og_data( $posts_page_id );
		}
	}

	if ( is_post_type_archive( 'service' ) ) {
		return brooklyn_beauty_get_service_archive_og_data();
	}

	return brooklyn_beauty_get_fallback_og_data();
}

/**
 * Output Open Graph meta tags.
 *
 * @return void
 */
function brooklyn_beauty_og_meta_tags() {
	$og_data = brooklyn_beauty_get_current_og_data();

	if ( empty( $og_data['title'] ) || empty( $og_data['url'] ) ) {
		return;
	}
	?>
	<meta property="og:title" content="<?php echo esc_attr( $og_data['title'] ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( isset( $og_data['type'] ) ? $og_data['type'] : 'website' ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $og_data['url'] ); ?>">
	<?php if ( ! empty( $og_data['description'] ) ) : ?>
		<meta property="og:description" content="<?php echo esc_attr( $og_data['description'] ); ?>">
	<?php endif; ?>
	<?php if ( ! empty( $og_data['image'] ) ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $og_data['image'] ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'brooklyn_beauty_og_meta_tags', 5 );

/**
 * Output canonical tag.
 *
 * @return void
 */
function brooklyn_beauty_canonical_tag() {
	$canonical_url = brooklyn_beauty_get_current_canonical_tag_url();

	if ( '' === $canonical_url ) {
		return;
	}
	?>
	<link rel="canonical" href="<?php echo esc_url( $canonical_url ); ?>">
	<?php
}
add_action( 'wp_head', 'brooklyn_beauty_canonical_tag', 6 );
