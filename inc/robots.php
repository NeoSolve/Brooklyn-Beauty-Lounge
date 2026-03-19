<?php
/**
 * Robots.txt management.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get robots.txt path in WordPress root.
 *
 * @return string
 */
function brooklyn_beauty_get_robots_txt_path() {
	return trailingslashit( ABSPATH ) . 'robots.txt';
}

/**
 * Get default robots.txt contents.
 *
 * @return string
 */
function brooklyn_beauty_get_default_robots_txt_content() {
	$lines = array(
		'User-agent: *',
		'Disallow: /wp-admin/',
		'Allow: /wp-admin/admin-ajax.php',
	);

	$sitemap_url = trim( (string) home_url( '/sitemap.xml' ) );
	if ( '' !== $sitemap_url ) {
		$lines[] = '';
		$lines[] = 'Sitemap: ' . $sitemap_url;
	}

	return implode( "\n", $lines ) . "\n";
}

/**
 * Get configured robots.txt contents.
 *
 * @return string
 */
function brooklyn_beauty_get_robots_txt_content() {
	$content = '';

	if ( function_exists( 'get_field' ) ) {
		$content = (string) get_field( 'robots_txt_content', 'option' );
	}

	$content = str_replace( array( "\r\n", "\r" ), "\n", $content );
	$content = trim( $content );

	if ( '' === $content ) {
		$content = trim( brooklyn_beauty_get_default_robots_txt_content() );
	}

	return $content . "\n";
}

/**
 * Write robots.txt file to WordPress root.
 *
 * @return bool
 */
function brooklyn_beauty_write_robots_txt_file() {
	$robots_path    = brooklyn_beauty_get_robots_txt_path();
	$robots_content = brooklyn_beauty_get_robots_txt_content();

	$result = @file_put_contents( $robots_path, $robots_content, LOCK_EX ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

	return false !== $result;
}

/**
 * Sync robots.txt after saving site settings.
 *
 * @param string|int $post_id Saved object ID.
 *
 * @return void
 */
function brooklyn_beauty_sync_robots_txt_on_acf_save( $post_id ) {
	if ( 'options' !== $post_id && 'option' !== $post_id ) {
		return;
	}

	$did_write = brooklyn_beauty_write_robots_txt_file();

	if ( ! $did_write && is_admin() ) {
		set_transient( 'brooklyn_beauty_robots_txt_write_error', 1, MINUTE_IN_SECONDS );
	} else {
		delete_transient( 'brooklyn_beauty_robots_txt_write_error' );
	}
}
add_action( 'acf/save_post', 'brooklyn_beauty_sync_robots_txt_on_acf_save', 20 );

/**
 * Ensure robots.txt exists in WordPress root.
 *
 * @return void
 */
function brooklyn_beauty_ensure_robots_txt_file_exists() {
	$robots_path = brooklyn_beauty_get_robots_txt_path();

	if ( file_exists( $robots_path ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_exists
		return;
	}

	brooklyn_beauty_write_robots_txt_file();
}
add_action( 'after_switch_theme', 'brooklyn_beauty_ensure_robots_txt_file_exists' );
add_action( 'admin_init', 'brooklyn_beauty_ensure_robots_txt_file_exists' );

/**
 * Fallback dynamic robots.txt output from configured content.
 *
 * @param string $output Existing robots content.
 *
 * @return string
 */
function brooklyn_beauty_filter_robots_txt( $output ) {
	$configured_output = brooklyn_beauty_get_robots_txt_content();

	return '' !== trim( $configured_output ) ? $configured_output : $output;
}
add_filter( 'robots_txt', 'brooklyn_beauty_filter_robots_txt', 10, 1 );

/**
 * Show admin notice when robots.txt could not be written.
 *
 * @return void
 */
function brooklyn_beauty_robots_txt_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! get_transient( 'brooklyn_beauty_robots_txt_write_error' ) ) {
		return;
	}
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Unable to write robots.txt to the WordPress root directory. Please check file permissions.', 'brooklyn-beauty' ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'brooklyn_beauty_robots_txt_admin_notice' );
