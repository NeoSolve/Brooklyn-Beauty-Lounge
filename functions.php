<?php
/**
 * Brooklyn Beauty Lounge theme functions and definitions
 *
 * @package Brooklyn_Beauty
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BROOKLYN_BEAUTY_VERSION', '1.0' );
define( 'BROOKLYN_BEAUTY_PROMOTIONS_OPTIONS_ID', 'brooklyn_beauty_promotions' );
define( 'BROOKLYN_BEAUTY_REDIRECTS_OPTIONS_ID', 'brooklyn_beauty_redirects' );

require_once get_template_directory() . '/inc/services.php';
require_once get_template_directory() . '/inc/blog.php';
require_once get_template_directory() . '/inc/popups.php';
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/taxonomies.php';
require_once get_template_directory() . '/inc/ajax.php';
require_once get_template_directory() . '/inc/acf.php';
require_once get_template_directory() . '/inc/redirects.php';
require_once get_template_directory() . '/inc/forms.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/local-business.php';
require_once get_template_directory() . '/inc/robots.php';
require_once get_template_directory() . '/inc/sitemap.php';
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/cleanup.php';
