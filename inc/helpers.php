<?php
/**
 * Utility and helper functions.
 *
 * URL helpers (external link detection, rel merge), nav menu and content
 * link attributes for external links, header social SVG icons, and admin
 * post/page clone action.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether URL points to an external host.
 *
 * @param string $url URL to inspect.
 *
 * @return bool
 */
function brooklyn_beauty_is_external_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url || '#' === $url || 0 === strpos( $url, '#' ) ) {
		return false;
	}

	$url_host = wp_parse_url( $url, PHP_URL_HOST );

	if ( empty( $url_host ) ) {
		return false;
	}

	$site_host = wp_parse_url( home_url(), PHP_URL_HOST );

	if ( empty( $site_host ) ) {
		return true;
	}

	$url_host  = strtolower( (string) $url_host );
	$site_host = strtolower( (string) $site_host );

	if ( 0 === strpos( $url_host, 'www.' ) ) {
		$url_host = substr( $url_host, 4 );
	}

	if ( 0 === strpos( $site_host, 'www.' ) ) {
		$site_host = substr( $site_host, 4 );
	}

	if ( $url_host === $site_host ) {
		return false;
	}

	return ! str_ends_with( $url_host, '.' . $site_host );
}

/**
 * Determine whether an external URL should open in a new tab.
 *
 * Keeps selected booking providers in the current tab.
 *
 * @param string $url URL to inspect.
 *
 * @return bool
 */
function brooklyn_beauty_should_open_external_url_in_new_tab( $url ) {
	if ( ! brooklyn_beauty_is_external_url( $url ) ) {
		return false;
	}

	$url_host = wp_parse_url( (string) $url, PHP_URL_HOST );
	$url_host = strtolower( (string) $url_host );

	if ( 0 === strpos( $url_host, 'www.' ) ) {
		$url_host = substr( $url_host, 4 );
	}

	if ( 'fresha.com' === $url_host || str_ends_with( $url_host, '.fresha.com' ) ) {
		return false;
	}

	return true;
}

/**
 * Merge rel tokens without duplicates.
 *
 * @param string $rel     Existing rel value.
 * @param array  $tokens  Required rel tokens.
 *
 * @return string
 */
function brooklyn_beauty_merge_rel_tokens( $rel, $tokens ) {
	$rel_tokens = preg_split( '/\s+/', strtolower( trim( (string) $rel ) ) );
	$rel_tokens = is_array( $rel_tokens ) ? array_filter( $rel_tokens ) : array();

	foreach ( $tokens as $token ) {
		$token = strtolower( trim( (string) $token ) );
		if ( '' !== $token && ! in_array( $token, $rel_tokens, true ) ) {
			$rel_tokens[] = $token;
		}
	}

	return implode( ' ', $rel_tokens );
}

/**
 * Add required attributes to external menu links.
 *
 * @param array $atts Current menu link attributes.
 *
 * @return array
 */
function brooklyn_beauty_nav_menu_external_link_attributes( $atts ) {
	$href = isset( $atts['href'] ) ? (string) $atts['href'] : '';

	if ( brooklyn_beauty_is_external_url( $href ) && ! brooklyn_beauty_should_open_external_url_in_new_tab( $href ) ) {
		unset( $atts['target'] );

		return $atts;
	}

	if ( ! brooklyn_beauty_should_open_external_url_in_new_tab( $href ) ) {
		return $atts;
	}

	$atts['target'] = '_blank';
	$atts['rel']    = brooklyn_beauty_merge_rel_tokens( isset( $atts['rel'] ) ? $atts['rel'] : '', array( 'nofollow', 'noopener', 'noreferrer' ) );

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'brooklyn_beauty_nav_menu_external_link_attributes', 10, 1 );

/**
 * Add required attributes to external links in post content.
 *
 * @param string $content Rendered post content.
 *
 * @return string
 */
function brooklyn_beauty_content_external_link_attributes( $content ) {
	if ( ! is_string( $content ) || '' === $content || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$processor = new WP_HTML_Tag_Processor( $content );

	while ( $processor->next_tag( array( 'tag_name' => 'a' ) ) ) {
		$href = (string) $processor->get_attribute( 'href' );

		if ( brooklyn_beauty_is_external_url( $href ) && ! brooklyn_beauty_should_open_external_url_in_new_tab( $href ) ) {
			$processor->remove_attribute( 'target' );
			continue;
		}

		if ( ! brooklyn_beauty_should_open_external_url_in_new_tab( $href ) ) {
			continue;
		}

		$processor->set_attribute( 'target', '_blank' );
		$processor->set_attribute(
			'rel',
			brooklyn_beauty_merge_rel_tokens(
				(string) $processor->get_attribute( 'rel' ),
				array( 'nofollow', 'noopener', 'noreferrer' )
			)
		);
	}

	return $processor->get_updated_html();
}
add_filter( 'the_content', 'brooklyn_beauty_content_external_link_attributes', 20, 1 );

/**
 * Get inline SVG icon markup for header social links.
 *
 * @param string $icon Icon slug.
 *
 * @return string
 */
function brooklyn_beauty_get_header_social_icon_svg( $icon ) {
	$icons = array(
		'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3.5" y="3.5" width="17" height="17" rx="5"></rect><circle cx="12" cy="12" r="4.1"></circle><circle cx="17.4" cy="6.7" r="1.1"></circle></svg>',
		'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14.2 8.3h2V5h-2.5c-2.8 0-4.4 1.7-4.4 4.7v2H7v3.2h2.3V19h3.4v-4.1h2.8l.4-3.2h-3.2V9.9c0-1 .4-1.6 1.5-1.6z"></path></svg>',
		'google'    => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.2 12.3c0-.6-.1-1-.2-1.5H12v2.8h4.6c-.2 1.2-.9 2.1-1.9 2.8v2.3h3.1c1.8-1.7 2.8-4.2 2.4-6.4z"></path><path d="M12 21c2.3 0 4.2-.8 5.6-2.1l-3.1-2.3c-.9.6-1.9.9-3 .9-2.3 0-4.2-1.5-4.9-3.6H3.3v2.4C4.8 19 8.1 21 12 21z"></path><path d="M6.6 13.9a5.3 5.3 0 0 1 0-3.8V7.7H3.3A9 9 0 0 0 3.3 16l3.3-2.1z"></path><path d="M12 6.5c1.2 0 2.4.4 3.3 1.3l2.5-2.5A8.7 8.7 0 0 0 3.3 7.7l3.3 2.4c.7-2.1 2.6-3.6 5.4-3.6z"></path></svg>',
		'tiktok'    => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M16.9 4h-3v9.2a2.6 2.6 0 1 1-2.6-2.7c.3 0 .6 0 .9.1V7.7l-.9-.1A5.6 5.6 0 1 0 16.9 13V9.5a6 6 0 0 0 3.8 1.4V8a3.3 3.3 0 0 1-3.8-4z"></path></svg>',
	);

	return isset( $icons[ $icon ] ) ? $icons[ $icon ] : '';
}

/**
 * Add clone action link for admin post rows.
 *
 * @param array   $actions Existing row actions.
 * @param WP_Post $post    Current post.
 *
 * @return array
 */
function brooklyn_beauty_add_clone_post_link( $actions, $post ) {
	if ( ! $post instanceof WP_Post ) {
		return $actions;
	}

	$post_type_object = get_post_type_object( $post->post_type );
	if ( ! $post_type_object || ! current_user_can( 'edit_posts' ) ) {
		return $actions;
	}

	if ( 'attachment' === $post->post_type || 'revision' === $post->post_type ) {
		return $actions;
	}

	$clone_url = wp_nonce_url(
		admin_url( 'admin.php?action=brooklyn_beauty_clone_post&post=' . $post->ID ),
		'brooklyn_beauty_clone_post_' . $post->ID
	);

	$actions['brooklyn_beauty_clone'] = '<a href="' . esc_url( $clone_url ) . '">' . esc_html__( 'Clone', 'brooklyn-beauty' ) . '</a>';

	return $actions;
}
add_filter( 'post_row_actions', 'brooklyn_beauty_add_clone_post_link', 10, 2 );
add_filter( 'page_row_actions', 'brooklyn_beauty_add_clone_post_link', 10, 2 );

/**
 * Clone an existing post and redirect to edit screen.
 *
 * @return void
 */
function brooklyn_beauty_clone_post_action() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You are not allowed to clone posts.', 'brooklyn-beauty' ) );
	}

	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	if ( $post_id <= 0 ) {
		wp_die( esc_html__( 'Invalid post ID.', 'brooklyn-beauty' ) );
	}

	check_admin_referer( 'brooklyn_beauty_clone_post_' . $post_id );

	$original_post = get_post( $post_id );
	if ( ! $original_post ) {
		wp_die( esc_html__( 'Post not found.', 'brooklyn-beauty' ) );
	}

	$new_post_id = wp_insert_post(
		array(
			'post_type'      => $original_post->post_type,
			'post_title'     => $original_post->post_title . ' (Copy)',
			'post_content'   => $original_post->post_content,
			'post_excerpt'   => $original_post->post_excerpt,
			'post_status'    => 'draft',
			'post_author'    => get_current_user_id(),
			'menu_order'     => (int) $original_post->menu_order,
			'comment_status' => $original_post->comment_status,
			'ping_status'    => $original_post->ping_status,
			'post_parent'    => (int) $original_post->post_parent,
		),
		true
	);

	if ( is_wp_error( $new_post_id ) || ! $new_post_id ) {
		wp_die( esc_html__( 'Unable to clone post.', 'brooklyn-beauty' ) );
	}

	$taxonomies = get_object_taxonomies( $original_post->post_type );
	foreach ( $taxonomies as $taxonomy ) {
		$term_ids = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
		if ( ! is_wp_error( $term_ids ) ) {
			wp_set_object_terms( $new_post_id, $term_ids, $taxonomy );
		}
	}

	$post_meta = get_post_meta( $post_id );
	foreach ( $post_meta as $meta_key => $meta_values ) {
		if ( '_edit_lock' === $meta_key || '_edit_last' === $meta_key ) {
			continue;
		}

		foreach ( $meta_values as $meta_value ) {
			add_post_meta( $new_post_id, $meta_key, maybe_unserialize( $meta_value ) );
		}
	}

	wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
	exit;
}
add_action( 'admin_action_brooklyn_beauty_clone_post', 'brooklyn_beauty_clone_post_action' );
