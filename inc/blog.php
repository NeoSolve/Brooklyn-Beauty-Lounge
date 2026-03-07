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
 * @param int    $paged         Current page number.
 * @param string $base_url      Base URL for pagination links.
 *
 * @return array{cards: string, pagination: string}
 */
function brooklyn_beauty_get_blog_cards_markup( $category_slug = 'all-posts', $paged = 1, $base_url = '' ) {
	$query_args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'paged'          => max( 1, (int) $paged ),
		'orderby'        => array(
			'date'  => 'DESC',
			'title' => 'ASC',
		),
	);

	$category_slug = sanitize_title( (string) $category_slug );
	if ( '' !== $category_slug && 'all-posts' !== $category_slug ) {
		$query_args['category_name'] = $category_slug;
	}

	$blog_posts_query = new WP_Query( $query_args );

	if ( ! $blog_posts_query->have_posts() ) {
		return array(
			'cards'      => '<div class="bb-blog-cards__empty">' . esc_html__( 'No blog posts found in this category.', 'brooklyn-beauty' ) . '</div>',
			'pagination' => '',
		);
	}

	$fallback_media_classes = array( 'pedicure', 'brows', 'makeup' );

	ob_start();

	foreach ( $blog_posts_query->posts as $index => $blog_post ) {
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

	$cards_markup = (string) ob_get_clean();

	$pagination_markup = '';
	$resolved_base_url = '' !== $base_url ? $base_url : get_pagenum_link( 1 );
	$resolved_base_url = remove_query_arg( 'paged', $resolved_base_url );
	$current_page      = max( 1, (int) $paged );
	$total_pages       = max( 1, (int) $blog_posts_query->max_num_pages );

	$pagination_links = paginate_links(
		array(
			'base'      => add_query_arg( 'paged', '%#%', $resolved_base_url ),
			'format'    => '',
			'current'   => $current_page,
			'total'     => $total_pages,
			'type'      => 'array',
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
		)
	);

	if ( ! is_array( $pagination_links ) ) {
		$pagination_links = array();
	}

	$has_prev_link = false;
	$has_next_link = false;
	$has_current   = false;

	foreach ( $pagination_links as $pagination_link ) {
		if ( false !== strpos( $pagination_link, 'prev page-numbers' ) || false !== strpos( $pagination_link, 'page-numbers prev' ) ) {
			$has_prev_link = true;
		}

		if ( false !== strpos( $pagination_link, 'next page-numbers' ) || false !== strpos( $pagination_link, 'page-numbers next' ) ) {
			$has_next_link = true;
		}

		if ( false !== strpos( $pagination_link, 'current' ) ) {
			$has_current = true;
		}
	}

	if ( ! $has_prev_link && $current_page <= 1 ) {
		array_unshift( $pagination_links, '<span class="page-numbers prev disabled" aria-disabled="true">&laquo;</span>' );
	}

	if ( ! $has_current ) {
		$current_link_markup = sprintf(
			'<span aria-current="page" class="page-numbers current">%d</span>',
			(int) $current_page
		);
		$insert_index        = 0;

		foreach ( $pagination_links as $index => $pagination_link ) {
			if ( false !== strpos( $pagination_link, 'prev page-numbers' ) || false !== strpos( $pagination_link, 'page-numbers prev' ) ) {
				$insert_index = $index + 1;
				break;
			}
		}

		array_splice( $pagination_links, $insert_index, 0, array( $current_link_markup ) );
	}

	if ( ! $has_next_link && $current_page >= $total_pages ) {
		$pagination_links[] = '<span class="page-numbers next disabled" aria-disabled="true">&raquo;</span>';
	}

	if ( ! empty( $pagination_links ) ) {
		$pagination_markup = '<ul class="page-numbers">';
		foreach ( $pagination_links as $pagination_link ) {
			$pagination_markup .= '<li>' . $pagination_link . '</li>';
		}
		$pagination_markup .= '</ul>';
	}

	wp_reset_postdata();

	return array(
		'cards'      => $cards_markup,
		'pagination' => $pagination_markup,
	);
}

/**
 * AJAX callback: return blog cards by category.
 *
 * @return void
 */
function brooklyn_beauty_ajax_filter_blog_posts() {
	check_ajax_referer( 'bb_services_filter', 'nonce' );

	$category_slug = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( (string) $_POST['category'] ) ) : 'all-posts';
	$paged         = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;
	$base_url      = isset( $_POST['base_url'] ) ? esc_url_raw( wp_unslash( (string) $_POST['base_url'] ) ) : '';
	$result        = brooklyn_beauty_get_blog_cards_markup( $category_slug, $paged, $base_url );

	wp_send_json_success(
		array(
			'cards'      => $result['cards'],
			'pagination' => $result['pagination'],
		)
	);
}
add_action( 'wp_ajax_bb_filter_blog_posts', 'brooklyn_beauty_ajax_filter_blog_posts' );
add_action( 'wp_ajax_nopriv_bb_filter_blog_posts', 'brooklyn_beauty_ajax_filter_blog_posts' );

