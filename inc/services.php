<?php
/**
 * Services block helpers and AJAX handlers.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get configured placeholder cards for services block.
 *
 * @param string $category_slug Service category slug.
 *
 * @return array<int, array<string, mixed>>
 */
function brooklyn_beauty_get_service_placeholder_cards( $category_slug = 'all-services' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id <= 0 ) {
		$front_page_id = (int) get_queried_object_id();
	}

	if ( $front_page_id <= 0 ) {
		return array();
	}

	$placeholder_rows = get_field( 'services_placeholder_cards', $front_page_id );
	if ( ! is_array( $placeholder_rows ) || empty( $placeholder_rows ) ) {
		return array();
	}

	$category_slug     = sanitize_title( (string) $category_slug );
	$is_all_categories = '' === $category_slug || 'all-services' === $category_slug;
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
				'category_slugs' => array( 'all-services' ),
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

		$placeholder_term = get_term( $placeholder_category_id, 'service_category' );
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
			'category_slugs' => array( $placeholder_term->slug ),
			'is_placeholder' => true,
		);
	}

	return $placeholder_cards;
}

/**
 * Get booking URL for a service card.
 *
 * Uses Single Service Hero button link when configured.
 *
 * @param int $service_id Service post ID.
 *
 * @return string
 */
function brooklyn_beauty_get_service_card_visit_url( $service_id ) {
	$service_id = (int) $service_id;
	if ( $service_id <= 0 ) {
		return '#book';
	}

	if ( function_exists( 'get_field' ) ) {
		$hero_button_link = trim( (string) get_field( 'service_hero_button_link', $service_id ) );
		if ( '' !== $hero_button_link ) {
			return $hero_button_link;
		}
	}

	return '#book';
}

/**
 * Merge service cards with placeholder cards by configured position.
 *
 * Position is 1-based and calculated against service cards order.
 *
 * @param array<int, array<string, mixed>> $service_cards     Service cards.
 * @param array<int, array<string, mixed>> $placeholder_cards Placeholder cards.
 *
 * @return array<int, array<string, mixed>>
 */
function brooklyn_beauty_merge_service_cards_with_placeholders( array $service_cards, array $placeholder_cards ) {
	if ( empty( $placeholder_cards ) ) {
		return $service_cards;
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

	$merged_cards      = $service_cards;
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
 * Get ordered service category slugs used in homepage tabs.
 *
 * @return array<int, string>
 */
function brooklyn_beauty_get_home_services_tab_category_slugs() {
	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id <= 0 ) {
		$front_page_id = (int) get_queried_object_id();
	}

	$selected_service_category_ids = array();
	if ( function_exists( 'get_field' ) && $front_page_id > 0 ) {
		$acf_selected_categories = get_field( 'services_tab_categories', $front_page_id );
		if ( is_array( $acf_selected_categories ) ) {
			$selected_service_category_ids = array_values( array_filter( array_map( 'intval', $acf_selected_categories ) ) );
		}
	}

	if ( ! empty( $selected_service_category_ids ) ) {
		$service_terms = get_terms(
			array(
				'taxonomy'   => 'service_category',
				'hide_empty' => false,
				'include'    => $selected_service_category_ids,
				'orderby'    => 'include',
			)
		);
	} else {
		$service_terms = get_terms(
			array(
				'taxonomy'   => 'service_category',
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
	}

	if ( is_wp_error( $service_terms ) || empty( $service_terms ) ) {
		return array();
	}

	$ordered_slugs = array();
	foreach ( $service_terms as $service_term ) {
		if ( 'all-services' === $service_term->slug ) {
			continue;
		}

		$ordered_slugs[] = $service_term->slug;
	}

	return $ordered_slugs;
}

/**
 * Sort service posts by homepage tab category order.
 *
 * @param array<int, WP_Post> $service_posts Service posts.
 *
 * @return array<int, WP_Post>
 */
function brooklyn_beauty_sort_service_posts_by_home_tab_order( array $service_posts ) {
	if ( empty( $service_posts ) ) {
		return $service_posts;
	}

	$ordered_category_slugs = brooklyn_beauty_get_home_services_tab_category_slugs();
	if ( empty( $ordered_category_slugs ) ) {
		return $service_posts;
	}

	$category_order_map = array_flip( $ordered_category_slugs );
	$sortable_items     = array();

	foreach ( $service_posts as $index => $service_post ) {
		$post_category_slugs = wp_get_post_terms( $service_post->ID, 'service_category', array( 'fields' => 'slugs' ) );
		if ( is_wp_error( $post_category_slugs ) ) {
			$post_category_slugs = array();
		}

		$lowest_category_order = PHP_INT_MAX;
		foreach ( $post_category_slugs as $post_category_slug ) {
			if ( isset( $category_order_map[ $post_category_slug ] ) ) {
				$lowest_category_order = min( $lowest_category_order, (int) $category_order_map[ $post_category_slug ] );
			}
		}

		$sortable_items[] = array(
			'post'           => $service_post,
			'category_order' => $lowest_category_order,
			'original_index' => $index,
		);
	}

	usort(
		$sortable_items,
		static function ( $left, $right ) {
			if ( $left['category_order'] === $right['category_order'] ) {
				return $left['original_index'] <=> $right['original_index'];
			}

			return $left['category_order'] <=> $right['category_order'];
		}
	);

	$sorted_posts = array();
	foreach ( $sortable_items as $sortable_item ) {
		$sorted_posts[] = $sortable_item['post'];
	}

	return $sorted_posts;
}

/**
 * Get service positions map by category for homepage tabs.
 *
 * @return array<string, array<int, int>>
 */
function brooklyn_beauty_get_home_services_category_positions_map() {
	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id <= 0 ) {
		$front_page_id = (int) get_queried_object_id();
	}

	if ( ! function_exists( 'get_field' ) || $front_page_id <= 0 ) {
		return array();
	}

	$rows = get_field( 'services_category_order_map', $front_page_id );
	if ( ! is_array( $rows ) || empty( $rows ) ) {
		return array();
	}

	$positions_map = array();

	foreach ( $rows as $row ) {
		$raw_category = isset( $row['category'] ) ? $row['category'] : 0;
		if ( is_array( $raw_category ) ) {
			$raw_category = reset( $raw_category );
		}
		$category_id = (int) $raw_category;
		if ( $category_id <= 0 ) {
			continue;
		}

		$category_term = get_term( $category_id, 'service_category' );
		if ( ! $category_term instanceof WP_Term || is_wp_error( $category_term ) ) {
			continue;
		}

		$category_slug = (string) $category_term->slug;
		if ( '' === $category_slug || 'all-services' === $category_slug ) {
			continue;
		}

		$raw_service = isset( $row['service'] ) ? $row['service'] : 0;
		if ( $raw_service instanceof WP_Post ) {
			$service_id = (int) $raw_service->ID;
		} elseif ( is_array( $raw_service ) ) {
			$service_id = (int) reset( $raw_service );
		} else {
			$service_id = (int) $raw_service;
		}
		if ( $service_id <= 0 ) {
			continue;
		}

		$position = isset( $row['position'] ) ? (int) $row['position'] : 0;
		if ( $position <= 0 ) {
			continue;
		}

		if ( ! isset( $positions_map[ $category_slug ] ) ) {
			$positions_map[ $category_slug ] = array();
		}

		if ( ! isset( $positions_map[ $category_slug ][ $service_id ] ) ) {
			$positions_map[ $category_slug ][ $service_id ] = $position;
		}
	}

	return $positions_map;
}

/**
 * Sort service posts by manual positions in selected category.
 *
 * Posts with configured position are shown first (ascending), while
 * unconfigured posts keep default order and appear after positioned ones.
 *
 * @param array<int, WP_Post> $service_posts Service posts.
 * @param string              $category_slug Category slug.
 *
 * @return array<int, WP_Post>
 */
function brooklyn_beauty_sort_service_posts_by_category_position( array $service_posts, $category_slug ) {
	if ( empty( $service_posts ) ) {
		return $service_posts;
	}

	$category_slug = sanitize_title( (string) $category_slug );
	if ( '' === $category_slug || 'all-services' === $category_slug ) {
		return $service_posts;
	}

	$positions_map = brooklyn_beauty_get_home_services_category_positions_map();
	if ( empty( $positions_map[ $category_slug ] ) || ! is_array( $positions_map[ $category_slug ] ) ) {
		return $service_posts;
	}

	$category_positions = $positions_map[ $category_slug ];
	$sortable_items     = array();

	foreach ( $service_posts as $index => $service_post ) {
		$post_id  = (int) $service_post->ID;
		$position = isset( $category_positions[ $post_id ] ) ? (int) $category_positions[ $post_id ] : PHP_INT_MAX;

		$sortable_items[] = array(
			'post'           => $service_post,
			'position'       => $position,
			'has_position'   => isset( $category_positions[ $post_id ] ),
			'original_index' => $index,
		);
	}

	usort(
		$sortable_items,
		static function ( $left, $right ) {
			if ( $left['has_position'] !== $right['has_position'] ) {
				return $left['has_position'] ? -1 : 1;
			}

			if ( $left['position'] === $right['position'] ) {
				return $left['original_index'] <=> $right['original_index'];
			}

			return $left['position'] <=> $right['position'];
		}
	);

	$sorted_posts = array();
	foreach ( $sortable_items as $sortable_item ) {
		$sorted_posts[] = $sortable_item['post'];
	}

	return $sorted_posts;
}

/**
 * Get service positions map for all-services tab.
 *
 * @return array<int, int>
 */
function brooklyn_beauty_get_home_services_all_positions_map() {
	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id <= 0 ) {
		$front_page_id = (int) get_queried_object_id();
	}

	if ( ! function_exists( 'get_field' ) || $front_page_id <= 0 ) {
		return array();
	}

	$rows = get_field( 'services_all_order_map', $front_page_id );
	if ( ! is_array( $rows ) || empty( $rows ) ) {
		return array();
	}

	$positions_map = array();

	foreach ( $rows as $row ) {
		$raw_service = isset( $row['service'] ) ? $row['service'] : 0;
		if ( $raw_service instanceof WP_Post ) {
			$service_id = (int) $raw_service->ID;
		} elseif ( is_array( $raw_service ) ) {
			$service_id = (int) reset( $raw_service );
		} else {
			$service_id = (int) $raw_service;
		}
		if ( $service_id <= 0 ) {
			continue;
		}

		$position = isset( $row['position'] ) ? (int) $row['position'] : 0;
		if ( $position <= 0 ) {
			continue;
		}

		if ( ! isset( $positions_map[ $service_id ] ) ) {
			$positions_map[ $service_id ] = $position;
		}
	}

	return $positions_map;
}

/**
 * Sort service posts by manual positions for all-services tab.
 *
 * @param array<int, WP_Post> $service_posts Service posts.
 *
 * @return array<int, WP_Post>
 */
function brooklyn_beauty_sort_service_posts_by_all_services_position( array $service_posts ) {
	if ( empty( $service_posts ) ) {
		return $service_posts;
	}

	$positions_map = brooklyn_beauty_get_home_services_all_positions_map();
	if ( empty( $positions_map ) ) {
		return brooklyn_beauty_sort_service_posts_by_home_tab_order( $service_posts );
	}

	$sortable_items = array();

	foreach ( $service_posts as $index => $service_post ) {
		$post_id  = (int) $service_post->ID;
		$position = isset( $positions_map[ $post_id ] ) ? (int) $positions_map[ $post_id ] : PHP_INT_MAX;

		$sortable_items[] = array(
			'post'           => $service_post,
			'position'       => $position,
			'has_position'   => isset( $positions_map[ $post_id ] ),
			'original_index' => $index,
		);
	}

	usort(
		$sortable_items,
		static function ( $left, $right ) {
			if ( $left['has_position'] !== $right['has_position'] ) {
				return $left['has_position'] ? -1 : 1;
			}

			if ( $left['position'] === $right['position'] ) {
				return $left['original_index'] <=> $right['original_index'];
			}

			return $left['position'] <=> $right['position'];
		}
	);

	$sorted_posts = array();
	foreach ( $sortable_items as $sortable_item ) {
		$sorted_posts[] = $sortable_item['post'];
	}

	return $sorted_posts;
}

/**
 * Build services cards markup for selected category.
 *
 * @param string $category_slug Service category slug.
 *
 * @return string
 */
function brooklyn_beauty_get_services_cards_markup( $category_slug = 'all-services' ) {
	$query_args = array(
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
	);

	$category_slug     = sanitize_title( (string) $category_slug );
	$placeholder_cards = brooklyn_beauty_get_service_placeholder_cards( $category_slug );

	if ( '' !== $category_slug && 'all-services' !== $category_slug ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'service_category',
				'field'    => 'slug',
				'terms'    => $category_slug,
			),
		);
	}

	$service_posts = get_posts( $query_args );
	if ( 'all-services' === $category_slug ) {
		$service_posts = brooklyn_beauty_sort_service_posts_by_all_services_position( $service_posts );
	} else {
		$service_posts = brooklyn_beauty_sort_service_posts_by_category_position( $service_posts, $category_slug );
	}

	if ( empty( $service_posts ) && empty( $placeholder_cards ) ) {
		return '<div class="bb-services-cards__empty">' . esc_html__( 'No services found in this category.', 'brooklyn-beauty' ) . '</div>';
	}

	$fallback_media_classes = array(
		'bb-service-card__media--pedicure',
		'bb-service-card__media--brows',
		'bb-service-card__media--makeup',
	);

	$services = array();
	foreach ( $service_posts as $index => $service_post ) {
		$media_class            = $fallback_media_classes[ $index % count( $fallback_media_classes ) ];
		$service_card_image_id  = function_exists( 'get_field' ) ? (int) get_field( 'service_card_image', $service_post->ID ) : 0;
		$media_image            = '';
		if ( $service_card_image_id > 0 ) {
			$media_image = (string) wp_get_attachment_image_url( $service_card_image_id, 'large' );
		}
		if ( '' === $media_image ) {
			$media_image = (string) get_the_post_thumbnail_url( $service_post, 'large' );
		}
		$service_text = get_the_excerpt( $service_post );

		if ( '' === trim( $service_text ) ) {
			$service_text = wp_trim_words( wp_strip_all_tags( $service_post->post_content ), 26, '...' );
		}

		$services[] = array(
			'title'          => get_the_title( $service_post ),
			'text'           => $service_text,
			'category_slugs' => array(),
			'media_class'    => $media_class,
			'media_image'    => $media_image,
			'visit_url'      => brooklyn_beauty_get_service_card_visit_url( $service_post->ID ),
			'more_url'       => (string) get_permalink( $service_post ),
			'is_placeholder' => false,
		);
	}

	$service_cards = brooklyn_beauty_merge_service_cards_with_placeholders( $services, $placeholder_cards );

	ob_start();
	foreach ( $service_cards as $service_card ) {
		if ( ! empty( $service_card['is_placeholder'] ) ) {
			?>
			<article class="bb-service-card bb-service-card--placeholder">
				<p class="bb-service-card__placeholder-text"><?php echo esc_html( (string) $service_card['text'] ); ?></p>
			</article>
			<?php
			continue;
		}
		?>
		<article class="bb-service-card">
			<div class="bb-service-card__media <?php echo esc_attr( (string) $service_card['media_class'] ); ?>"<?php echo '' !== (string) $service_card['media_image'] ? ' style="background-image: url(' . esc_url( (string) $service_card['media_image'] ) . ');"' : ''; ?> aria-hidden="true"></div>
			<div class="bb-service-card__content">
				<h3 class="bb-service-card__title"><?php echo esc_html( (string) $service_card['title'] ); ?></h3>
				<p class="bb-service-card__text"><?php echo esc_html( (string) $service_card['text'] ); ?></p>
				<div class="bb-service-card__actions">
					<a class="btn btn--medium bb-service-card__visit-btn" href="<?php echo esc_url( (string) $service_card['visit_url'] ); ?>">
						<?php esc_html_e( 'book a visit', 'brooklyn-beauty' ); ?>
					</a>
					<a class="bb-service-card__more-link" href="<?php echo esc_url( (string) $service_card['more_url'] ); ?>">
						<?php esc_html_e( 'learn more', 'brooklyn-beauty' ); ?>
					</a>
				</div>
			</div>
		</article>
		<?php
	}

	return (string) ob_get_clean();
}

/**
 * AJAX callback: return services cards by category.
 *
 * @return void
 */
function brooklyn_beauty_ajax_filter_services() {
	check_ajax_referer( 'bb_services_filter', 'nonce' );

	$category_slug = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( (string) $_POST['category'] ) ) : 'all-services';
	$cards_html    = brooklyn_beauty_get_services_cards_markup( $category_slug );

	wp_send_json_success(
		array(
			'html' => $cards_html,
		)
	);
}
add_action( 'wp_ajax_bb_filter_services', 'brooklyn_beauty_ajax_filter_services' );
add_action( 'wp_ajax_nopriv_bb_filter_services', 'brooklyn_beauty_ajax_filter_services' );
