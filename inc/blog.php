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
 * Get configured placeholder cards for blog block.
 *
 * @param string $category_slug Blog category slug (e.g. all-posts or category slug).
 * @param int    $page_id       Blog page ID (0 = current queried object).
 *
 * @return array<int, array<string, mixed>>
 */
function brooklyn_beauty_get_blog_placeholder_cards( $category_slug = 'all-posts', $page_id = 0 ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$page_id = (int) $page_id;
	if ( $page_id <= 0 ) {
		$page_id = (int) get_queried_object_id();
	}
	if ( $page_id <= 0 ) {
		return array();
	}

	$placeholder_rows = get_field( 'blog_placeholder_cards', $page_id );
	if ( ! is_array( $placeholder_rows ) || empty( $placeholder_rows ) ) {
		return array();
	}

	$category_slug     = sanitize_title( (string) $category_slug );
	$is_all_categories = '' === $category_slug || 'all-posts' === $category_slug;
	$placeholder_cards = array();

	foreach ( $placeholder_rows as $placeholder_row ) {
		$placeholder_text = isset( $placeholder_row['text'] ) ? trim( (string) $placeholder_row['text'] ) : '';
		if ( '' === $placeholder_text ) {
			continue;
		}

		$show_in_all_tab = ! empty( $placeholder_row['show_in_all_tab'] );

		if ( $is_all_categories && ! $show_in_all_tab ) {
			continue;
		}

		if ( $show_in_all_tab ) {
			if ( ! $is_all_categories ) {
				continue;
			}
			$placeholder_position = isset( $placeholder_row['position'] ) ? (int) $placeholder_row['position'] : 1;
			if ( $placeholder_position <= 0 ) {
				$placeholder_position = 1;
			}
			$placeholder_cards[] = array(
				'text'           => $placeholder_text,
				'position'       => $placeholder_position,
				'is_placeholder' => true,
			);
			continue;
		}

		$placeholder_category_id = 0;
		if ( isset( $placeholder_row['category'] ) ) {
			if ( is_array( $placeholder_row['category'] ) ) {
				$placeholder_category_id = (int) reset( $placeholder_row['category'] );
			} else {
				$placeholder_category_id = (int) $placeholder_row['category'];
			}
		}
		if ( $placeholder_category_id <= 0 ) {
			continue;
		}

		$placeholder_term = get_term( $placeholder_category_id, 'category' );
		if ( ! $placeholder_term instanceof WP_Term || is_wp_error( $placeholder_term ) ) {
			continue;
		}
		if ( ! $is_all_categories && $placeholder_term->slug !== $category_slug ) {
			continue;
		}

		$placeholder_position = isset( $placeholder_row['position'] ) ? (int) $placeholder_row['position'] : 1;
		if ( $placeholder_position <= 0 ) {
			$placeholder_position = 1;
		}
		$placeholder_cards[] = array(
			'text'           => $placeholder_text,
			'position'       => $placeholder_position,
			'is_placeholder' => true,
		);
	}

	return $placeholder_cards;
}

/**
 * Merge blog cards with placeholder cards by configured position.
 *
 * @param array<int, array<string, mixed>> $blog_cards       Blog post cards (each with is_placeholder false).
 * @param array<int, array<string, mixed>> $placeholder_cards Placeholder cards (each with position, is_placeholder true).
 *
 * @return array<int, array<string, mixed>>
 */
function brooklyn_beauty_merge_blog_cards_with_placeholders( array $blog_cards, array $placeholder_cards ) {
	if ( empty( $placeholder_cards ) ) {
		return $blog_cards;
	}

	$normalized_placeholders = array();
	foreach ( $placeholder_cards as $index => $placeholder_card ) {
		$position = isset( $placeholder_card['position'] ) ? (int) $placeholder_card['position'] : 1;
		if ( $position <= 0 ) {
			$position = 1;
		}
		$normalized_placeholders[] = array(
			'position' => $position,
			'index'    => $index,
			'card'     => $placeholder_card,
		);
	}
	usort(
		$normalized_placeholders,
		static function ( $left, $right ) {
			if ( $left['position'] === $right['position'] ) {
				return $left['index'] <=> $right['index'];
			}
			return $left['position'] <=> $right['position'];
		}
	);

	$merged_cards      = $blog_cards;
	$inserted_cards_no = 0;
	foreach ( $normalized_placeholders as $placeholder_item ) {
		$insert_at = $placeholder_item['position'] - 1 + $inserted_cards_no;
		if ( $insert_at < 0 ) {
			$insert_at = 0;
		}
		if ( $insert_at > count( $merged_cards ) ) {
			$insert_at = count( $merged_cards );
		}
		array_splice( $merged_cards, $insert_at, 0, array( $placeholder_item['card'] ) );
		++$inserted_cards_no;
	}

	return $merged_cards;
}

/**
 * Build blog cards markup for selected category.
 *
 * @param string $category_slug Blog category slug.
 * @param int    $paged         Current page number.
 * @param string $base_url      Base URL for pagination links.
 * @param int    $blog_page_id  Blog page ID for placeholder cards (0 = current).
 *
 * @return array{cards: string, pagination: string}
 */
function brooklyn_beauty_get_blog_cards_markup( $category_slug = 'all-posts', $paged = 1, $base_url = '', $blog_page_id = 0 ) {
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

	$fallback_media_classes = array( 'pedicure', 'brows', 'makeup' );
	$blog_cards = array();

	if ( $blog_posts_query->have_posts() ) {
		foreach ( $blog_posts_query->posts as $index => $blog_post ) {
			$card_excerpt = get_the_excerpt( $blog_post );
			if ( '' === trim( $card_excerpt ) ) {
				$card_excerpt = wp_trim_words( wp_strip_all_tags( $blog_post->post_content ), 26, '...' );
			}
			$card_image          = (string) get_the_post_thumbnail_url( $blog_post, 'large' );
			$fallback_media_name = $fallback_media_classes[ $index % count( $fallback_media_classes ) ];
			$reading_time_label  = brooklyn_beauty_get_post_reading_time_label( (int) $blog_post->ID );

			$blog_cards[] = array(
				'is_placeholder'      => false,
				'post_id'            => (int) $blog_post->ID,
				'excerpt'            => $card_excerpt,
				'reading_time_label' => $reading_time_label,
				'fallback_media_name' => $fallback_media_name,
				'media_image'        => $card_image,
			);
		}
	}

	$placeholder_cards = function_exists( 'brooklyn_beauty_get_blog_placeholder_cards' )
		? brooklyn_beauty_get_blog_placeholder_cards( $category_slug, $blog_page_id )
		: array();
	$merged_cards = function_exists( 'brooklyn_beauty_merge_blog_cards_with_placeholders' )
		? brooklyn_beauty_merge_blog_cards_with_placeholders( $blog_cards, $placeholder_cards )
		: $blog_cards;

	if ( empty( $merged_cards ) ) {
		return array(
			'cards'      => '<div class="bb-blog-cards__empty">' . esc_html__( 'No blog posts found in this category.', 'brooklyn-beauty' ) . '</div>',
			'pagination' => '',
		);
	}

	ob_start();
	foreach ( $merged_cards as $card ) {
		if ( ! empty( $card['is_placeholder'] ) ) {
			$placeholder_text = isset( $card['text'] ) ? (string) $card['text'] : '';
			echo '<article class="bb-blog-card bb-blog-card--placeholder">';
			echo '<p class="bb-blog-card__placeholder-text">' . esc_html( $placeholder_text ) . '</p>';
			echo '</article>';
			continue;
		}
		get_template_part(
			'template-parts/blog/card',
			null,
			array(
				'post_id'             => (int) $card['post_id'],
				'excerpt'             => (string) $card['excerpt'],
				'reading_time_label'  => (string) $card['reading_time_label'],
				'fallback_media_name' => (string) $card['fallback_media_name'],
				'media_image'         => (string) $card['media_image'],
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
	$blog_page_id  = isset( $_POST['blog_page_id'] ) ? max( 0, absint( $_POST['blog_page_id'] ) ) : 0;
	$result        = brooklyn_beauty_get_blog_cards_markup( $category_slug, $paged, $base_url, $blog_page_id );

	wp_send_json_success(
		array(
			'cards'      => $result['cards'],
			'pagination' => $result['pagination'],
		)
	);
}
add_action( 'wp_ajax_bb_filter_blog_posts', 'brooklyn_beauty_ajax_filter_blog_posts' );
add_action( 'wp_ajax_nopriv_bb_filter_blog_posts', 'brooklyn_beauty_ajax_filter_blog_posts' );

