<?php
/**
 * Blog block helpers and AJAX handlers.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get post reading time label.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function brooklyn_beauty_get_post_reading_time_label( $post_id ) {
	$post_content = (string) get_post_field( 'post_content', (int) $post_id );
	$word_count   = (int) str_word_count( wp_strip_all_tags( $post_content ) );
	$minutes      = max( 1, (int) ceil( $word_count / 200 ) );

	/* translators: %s: reading time in minutes. */
	return sprintf( _n( '%s minute', '%s minutes', $minutes, 'brooklyn-beauty' ), number_format_i18n( $minutes ) );
}

/**
 * Build blog cards markup for selected category.
 *
 * @param string $category_slug Blog category slug.
 *
 * @return string
 */
function brooklyn_beauty_get_blog_cards_markup( $category_slug = 'all-posts' ) {
	$query_args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array(
			'date'  => 'DESC',
			'title' => 'ASC',
		),
	);

	$category_slug = sanitize_title( (string) $category_slug );
	if ( '' !== $category_slug && 'all-posts' !== $category_slug ) {
		$query_args['category_name'] = $category_slug;
	}

	$blog_posts = get_posts( $query_args );

	if ( empty( $blog_posts ) ) {
		return '<div class="bb-services-cards__empty">' . esc_html__( 'No blog posts found in this category.', 'brooklyn-beauty' ) . '</div>';
	}

	$fallback_media_classes = array( 'pedicure', 'brows', 'makeup' );

	ob_start();

	foreach ( $blog_posts as $index => $blog_post ) {
		$card_excerpt = get_the_excerpt( $blog_post );
		if ( '' === trim( $card_excerpt ) ) {
			$card_excerpt = wp_trim_words( wp_strip_all_tags( $blog_post->post_content ), 26, '...' );
		}

		$card_image          = (string) get_the_post_thumbnail_url( $blog_post, 'large' );
		$fallback_media_name = $fallback_media_classes[ $index % count( $fallback_media_classes ) ];
		$reading_time_label  = brooklyn_beauty_get_post_reading_time_label( (int) $blog_post->ID );

		get_template_part(
			'template-parts/blog/card',
			null,
			array(
				'post_id'             => (int) $blog_post->ID,
				'excerpt'             => $card_excerpt,
				'reading_time_label'  => $reading_time_label,
				'fallback_media_name' => $fallback_media_name,
				'media_image'         => $card_image,
			)
		);
	}

	return (string) ob_get_clean();
}

/**
 * AJAX callback: return blog cards by category.
 *
 * @return void
 */
function brooklyn_beauty_ajax_filter_blog_posts() {
	check_ajax_referer( 'bb_services_filter', 'nonce' );

	$category_slug = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( (string) $_POST['category'] ) ) : 'all-posts';
	$cards_html    = brooklyn_beauty_get_blog_cards_markup( $category_slug );

	wp_send_json_success(
		array(
			'html' => $cards_html,
		)
	);
}
add_action( 'wp_ajax_bb_filter_blog_posts', 'brooklyn_beauty_ajax_filter_blog_posts' );
add_action( 'wp_ajax_nopriv_bb_filter_blog_posts', 'brooklyn_beauty_ajax_filter_blog_posts' );

