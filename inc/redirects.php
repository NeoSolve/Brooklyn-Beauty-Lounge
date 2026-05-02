<?php
/**
 * Canonical host (www/non-www alignment with WP “Site Address”) + ACF path redirects (`site_301_redirects`).
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize a URL path for redirect matching (leading slash, no trailing slash except root).
 *
 * @param string $path Raw path.
 *
 * @return string
 */
function brooklyn_beauty_normalize_redirect_path_for_match( $path ) {
	$path = rawurldecode( trim( wp_unslash( (string) $path ) ) );
	$path = str_replace( '\\', '/', $path );
	$path = preg_replace( '#//+#', '/', $path );
	$path = '/' . ltrim( $path, '/' );

	if ( '/' !== $path ) {
		$path = untrailingslashit( $path );
	}

	return ( '' !== $path ) ? $path : '/';
}

/**
 * Resolve a destination string to an absolute URL for wp_redirect().
 *
 * @param string $raw From ACF ("https://…" or "/path").
 *
 * @return string Empty if invalid.
 */
function brooklyn_beauty_redirect_resolve_target_url( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}

	if ( preg_match( '#^\s*https?://#i', $raw ) ) {
		return esc_url_raw( $raw );
	}

	if ( strlen( $raw ) >= 2 && '//' === substr( $raw, 0, 2 ) ) {
		return '';
	}

	$internal = '/' . ltrim( $raw, '/' );

	return esc_url_raw( home_url( $internal ) );
}

/**
 * One-time copy of redirect rules when they lived under generic Site Settings options (`option`).
 */
function brooklyn_beauty_maybe_migrate_redirects_options_context() {
	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	if ( 'yes' === get_option( 'brooklyn_beauty_redirects_ctx_migrated', '' ) ) {
		return;
	}

	$legacy  = get_field( 'site_301_redirects', 'option' );
	$current = get_field( 'site_301_redirects', BROOKLYN_BEAUTY_REDIRECTS_OPTIONS_ID );

	$has_legacy  = is_array( $legacy ) && ! empty( $legacy );
	$has_current = is_array( $current ) && ! empty( $current );

	if ( $has_legacy && ! $has_current ) {
		update_field( 'site_301_redirects', $legacy, BROOKLYN_BEAUTY_REDIRECTS_OPTIONS_ID );
	}

	update_option( 'brooklyn_beauty_redirects_ctx_migrated', 'yes', true );
}
add_action( 'acf/init', 'brooklyn_beauty_maybe_migrate_redirects_options_context', 25 );

/**
 * 301 redirect to the canonical site host from WP “Site Address” (e.g. www → без www если так задан home_url).
 */
function brooklyn_beauty_redirect_canonical_http_host() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return;
	}

	if ( is_customize_preview() ) {
		return;
	}

	if ( wp_installing() ) {
		return;
	}

	$home = wp_parse_url( home_url( '/' ) );
	if ( ! is_array( $home ) || empty( $home['host'] ) ) {
		return;
	}

	$canonical = strtolower( (string) $home['host'] );
	$canonical = preg_replace( '#:\d+$#', '', $canonical );
	if ( '' === $canonical ) {
		return;
	}

	if ( empty( $_SERVER['HTTP_HOST'] ) || ! is_string( $_SERVER['HTTP_HOST'] ) ) {
		return;
	}

	$requested_raw = strtolower( preg_replace( '#:\d+$#', '', trim( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) );
	$requested     = preg_replace( '/^\.+|\.+$/', '', $requested_raw );
	if ( '' === $requested ) {
		return;
	}

	if ( $requested === $canonical ) {
		return;
	}

	$scheme = is_ssl() ? 'https' : 'http';

	$request_uri = '/';
	if ( isset( $_SERVER['REQUEST_URI'] ) && is_string( $_SERVER['REQUEST_URI'] ) ) {
		$request_uri = wp_unslash( $_SERVER['REQUEST_URI'] );
	}
	if ( '' === $request_uri || '/' !== substr( $request_uri, 0, 1 ) ) {
		$request_uri = '/';
	}

	$destination = $scheme . '://' . $canonical . $request_uri;
	wp_safe_redirect( esc_url_raw( $destination ), 301 );
	exit;
}
add_action( 'template_redirect', 'brooklyn_beauty_redirect_canonical_http_host', -1 );

/**
 * Apply the first matching 301 redirect rule.
 */
function brooklyn_beauty_template_redirect_301() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	if ( is_customize_preview() ) {
		return;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$rules = get_field( 'site_301_redirects', BROOKLYN_BEAUTY_REDIRECTS_OPTIONS_ID );
	if ( ! is_array( $rules ) || array() === $rules ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$req_parts   = wp_parse_url( $request_uri );
	$req_path    = isset( $req_parts['path'] ) ? $req_parts['path'] : '/';
	$current     = brooklyn_beauty_normalize_redirect_path_for_match( $req_path );

	foreach ( $rules as $rule ) {
		if ( ! is_array( $rule ) ) {
			continue;
		}

		$from = isset( $rule['redirect_from_path'] ) ? (string) $rule['redirect_from_path'] : '';
		$to   = isset( $rule['redirect_to_url'] ) ? (string) $rule['redirect_to_url'] : '';
		if ( '' === $from || '' === $to ) {
			continue;
		}

		$from_norm = brooklyn_beauty_normalize_redirect_path_for_match( $from );
		if ( $from_norm !== $current ) {
			continue;
		}

		$target = brooklyn_beauty_redirect_resolve_target_url( $to );
		if ( '' === $target ) {
			continue;
		}

		$target_path = (string) wp_parse_url( $target, PHP_URL_PATH );
		if ( '' !== $target_path && brooklyn_beauty_normalize_redirect_path_for_match( $target_path ) === $current ) {
			continue;
		}

		wp_redirect( $target, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'brooklyn_beauty_template_redirect_301', 0 );
