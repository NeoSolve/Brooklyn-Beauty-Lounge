<?php
/**
 * Sitemap.xml generation and sync.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get sitemap.xml path in WordPress root.
 *
 * @return string
 */
function brooklyn_beauty_get_sitemap_path() {
	return trailingslashit( ABSPATH ) . 'sitemap.xml';
}

/**
 * Escape XML text.
 *
 * @param string $value Raw XML value.
 *
 * @return string
 */
function brooklyn_beauty_escape_xml( $value ) {
	return htmlspecialchars( (string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8' );
}

/**
 * Get latest modified GMT date for a post type.
 *
 * @param string $post_type Post type slug.
 *
 * @return string
 */
function brooklyn_beauty_get_latest_modified_gmt_for_post_type( $post_type ) {
	$posts = get_posts(
		array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( empty( $posts ) ) {
		return '';
	}

	$post_modified_gmt = get_post_field( 'post_modified_gmt', (int) $posts[0] );
	if ( is_string( $post_modified_gmt ) && '' !== trim( $post_modified_gmt ) ) {
		return $post_modified_gmt;
	}

	return '';
}

/**
 * Normalize datetime string to W3C format.
 *
 * @param string $datetime GMT datetime.
 *
 * @return string
 */
function brooklyn_beauty_get_sitemap_lastmod( $datetime ) {
	$datetime = trim( (string) $datetime );
	if ( '' === $datetime || '0000-00-00 00:00:00' === $datetime ) {
		return gmdate( 'c' );
	}

	$timestamp = strtotime( $datetime . ' GMT' );
	if ( false === $timestamp ) {
		return gmdate( 'c' );
	}

	return gmdate( 'c', $timestamp );
}

/**
 * Collect sitemap entries for public URLs.
 *
 * @return array<int, array<string, string>>
 */
function brooklyn_beauty_get_sitemap_entries() {
	$entries = array();

	$add_entry = static function ( $url, $lastmod = '' ) use ( &$entries ) {
		$url = trim( (string) $url );
		if ( '' === $url ) {
			return;
		}

		$entries[ $url ] = array(
			'loc'     => $url,
			'lastmod' => brooklyn_beauty_get_sitemap_lastmod( $lastmod ),
		);
	};

	$page_on_front = (int) get_option( 'page_on_front' );
	if ( $page_on_front > 0 ) {
		$add_entry(
			home_url( '/' ),
			(string) get_post_field( 'post_modified_gmt', $page_on_front )
		);
	} else {
		$latest_page_modified    = brooklyn_beauty_get_latest_modified_gmt_for_post_type( 'page' );
		$latest_post_modified    = brooklyn_beauty_get_latest_modified_gmt_for_post_type( 'post' );
		$latest_service_modified = post_type_exists( 'service' ) ? brooklyn_beauty_get_latest_modified_gmt_for_post_type( 'service' ) : '';
		$latest_modified         = max( array_filter( array( $latest_page_modified, $latest_post_modified, $latest_service_modified ) ) );

		$add_entry( home_url( '/' ), is_string( $latest_modified ) ? $latest_modified : '' );
	}

	if ( post_type_exists( 'service' ) ) {
		$service_archive_url = get_post_type_archive_link( 'service' );
		if ( is_string( $service_archive_url ) && '' !== $service_archive_url ) {
			$add_entry( $service_archive_url, brooklyn_beauty_get_latest_modified_gmt_for_post_type( 'service' ) );
		}
	}

	$post_types = get_post_types(
		array(
			'public' => true,
		),
		'names'
	);

	unset( $post_types['attachment'] );

	foreach ( $post_types as $post_type ) {
		$post_ids = get_posts(
			array(
				'post_type'              => $post_type,
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'orderby'                => 'menu_order title',
				'order'                  => 'ASC',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		foreach ( $post_ids as $post_id ) {
			$post_url = get_permalink( (int) $post_id );
			if ( ! is_string( $post_url ) || '' === $post_url ) {
				continue;
			}

			$add_entry(
				$post_url,
				(string) get_post_field( 'post_modified_gmt', (int) $post_id )
			);
		}
	}

	return array_values( $entries );
}

/**
 * Build sitemap.xml content.
 *
 * @return string
 */
function brooklyn_beauty_get_sitemap_xml_content() {
	$entries = brooklyn_beauty_get_sitemap_entries();
	$xml     = array(
		'<?xml version="1.0" encoding="UTF-8"?>',
		'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
	);

	foreach ( $entries as $entry ) {
		$xml[] = '  <url>';
		$xml[] = '    <loc>' . brooklyn_beauty_escape_xml( $entry['loc'] ) . '</loc>';
		$xml[] = '    <lastmod>' . brooklyn_beauty_escape_xml( $entry['lastmod'] ) . '</lastmod>';
		$xml[] = '  </url>';
	}

	$xml[] = '</urlset>';

	return implode( "\n", $xml ) . "\n";
}

/**
 * Write sitemap.xml file to WordPress root.
 *
 * @return bool
 */
function brooklyn_beauty_write_sitemap_file() {
	$sitemap_path    = brooklyn_beauty_get_sitemap_path();
	$sitemap_content = brooklyn_beauty_get_sitemap_xml_content();

	$result = @file_put_contents( $sitemap_path, $sitemap_content, LOCK_EX ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

	return false !== $result;
}

/**
 * Sync sitemap.xml file.
 *
 * @return void
 */
function brooklyn_beauty_sync_sitemap_file() {
	$did_write = brooklyn_beauty_write_sitemap_file();

	if ( ! $did_write && is_admin() ) {
		set_transient( 'brooklyn_beauty_sitemap_write_error', 1, MINUTE_IN_SECONDS );
	} else {
		delete_transient( 'brooklyn_beauty_sitemap_write_error' );
	}
}

/**
 * Sync sitemap on post save.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 *
 * @return void
 */
function brooklyn_beauty_sync_sitemap_on_save_post( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return;
	}

	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$post_type_object = get_post_type_object( $post->post_type );
	if ( ! $post_type_object || ! $post_type_object->public ) {
		return;
	}

	brooklyn_beauty_sync_sitemap_file();
}
add_action( 'save_post', 'brooklyn_beauty_sync_sitemap_on_save_post', 20, 2 );

/**
 * Sync sitemap before deleting a public post.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function brooklyn_beauty_sync_sitemap_on_delete_post( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$post_type_object = get_post_type_object( $post->post_type );
	if ( ! $post_type_object || ! $post_type_object->public ) {
		return;
	}

	brooklyn_beauty_sync_sitemap_file();
}
add_action( 'before_delete_post', 'brooklyn_beauty_sync_sitemap_on_delete_post', 20 );
add_action( 'trashed_post', 'brooklyn_beauty_sync_sitemap_on_delete_post', 20 );
add_action( 'untrashed_post', 'brooklyn_beauty_sync_sitemap_on_delete_post', 20 );

/**
 * Sync sitemap when a public post changes publication status.
 *
 * @param string  $new_status New status.
 * @param string  $old_status Old status.
 * @param WP_Post $post       Post object.
 *
 * @return void
 */
function brooklyn_beauty_sync_sitemap_on_status_transition( $new_status, $old_status, $post ) {
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$post_type_object = get_post_type_object( $post->post_type );
	if ( ! $post_type_object || ! $post_type_object->public ) {
		return;
	}

	if ( $new_status === $old_status ) {
		return;
	}

	brooklyn_beauty_sync_sitemap_file();
}
add_action( 'transition_post_status', 'brooklyn_beauty_sync_sitemap_on_status_transition', 20, 3 );

/**
 * Sync sitemap when key reading settings change.
 *
 * @param mixed  $old_value Old option value.
 * @param mixed  $value     New option value.
 * @param string $option    Option name.
 *
 * @return void
 */
function brooklyn_beauty_sync_sitemap_on_option_update( $old_value, $value, $option ) {
	unset( $old_value, $value );

	if ( ! in_array( $option, array( 'page_on_front', 'page_for_posts', 'show_on_front' ), true ) ) {
		return;
	}

	brooklyn_beauty_sync_sitemap_file();
}
add_action( 'updated_option', 'brooklyn_beauty_sync_sitemap_on_option_update', 20, 3 );

/**
 * Ensure sitemap.xml exists in WordPress root.
 *
 * @return void
 */
function brooklyn_beauty_ensure_sitemap_file_exists() {
	$sitemap_path = brooklyn_beauty_get_sitemap_path();

	if ( file_exists( $sitemap_path ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_exists
		return;
	}

	brooklyn_beauty_sync_sitemap_file();
}
add_action( 'after_switch_theme', 'brooklyn_beauty_ensure_sitemap_file_exists' );
add_action( 'admin_init', 'brooklyn_beauty_ensure_sitemap_file_exists' );

/**
 * Render sitemap.xml dynamically at the standard path.
 *
 * @return void
 */
function brooklyn_beauty_render_sitemap_xml() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string) $_SERVER['REQUEST_URI'] ) : '';
	$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );

	if ( ! is_string( $request_path ) ) {
		return;
	}

	$request_path = trim( $request_path, '/' );

	if ( 'sitemap.xml' !== $request_path ) {
		return;
	}

	status_header( 200 );
	nocache_headers();
	header( 'Content-Type: application/xml; charset=UTF-8' );

	echo brooklyn_beauty_get_sitemap_xml_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'template_redirect', 'brooklyn_beauty_render_sitemap_xml', 0 );

/**
 * Show admin notice when sitemap.xml could not be written.
 *
 * @return void
 */
function brooklyn_beauty_sitemap_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! get_transient( 'brooklyn_beauty_sitemap_write_error' ) ) {
		return;
	}
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Unable to write sitemap.xml to the WordPress root directory. The dynamic sitemap is still available at /sitemap.xml, but please check file permissions if you need a physical file.', 'brooklyn-beauty' ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'brooklyn_beauty_sitemap_admin_notice' );
