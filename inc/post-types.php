<?php
/**
 * Custom post types and post permalink/rewrite logic.
 *
 * Registers the Service post type and blog post rewrite rules so that
 * single posts use the /blog/post-slug URL format.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Services post type.
 */
function brooklyn_beauty_register_services_content() {
	register_post_type(
		'service',
		array(
			'labels'       => array(
				'name'               => __( 'Services', 'brooklyn-beauty' ),
				'singular_name'      => __( 'Service', 'brooklyn-beauty' ),
				'menu_name'          => __( 'Services', 'brooklyn-beauty' ),
				'add_new'            => __( 'Add New', 'brooklyn-beauty' ),
				'add_new_item'       => __( 'Add New Service', 'brooklyn-beauty' ),
				'edit_item'          => __( 'Edit Service', 'brooklyn-beauty' ),
				'new_item'           => __( 'New Service', 'brooklyn-beauty' ),
				'view_item'          => __( 'View Service', 'brooklyn-beauty' ),
				'search_items'       => __( 'Search Services', 'brooklyn-beauty' ),
				'not_found'          => __( 'No services found.', 'brooklyn-beauty' ),
				'not_found_in_trash' => __( 'No services found in Trash.', 'brooklyn-beauty' ),
				'all_items'          => __( 'All Services', 'brooklyn-beauty' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-store',
			// Keep single services under /services/<slug>, but free /services for the static page.
			'has_archive'  => 'services-archive',
			'rewrite'      => array(
				'slug' => 'services',
			),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'brooklyn_beauty_register_services_content' );

/**
 * Add rewrite rule for single blog posts: /blog/post-slug.
 *
 * @return void
 */
function brooklyn_beauty_add_blog_post_rewrite_rule() {
	add_rewrite_rule( '^blog/([^/]+)/?$', 'index.php?post_type=post&name=$matches[1]', 'top' );
}
add_action( 'init', 'brooklyn_beauty_add_blog_post_rewrite_rule' );

/**
 * Use /blog/%postname% permalink format for default posts.
 *
 * @param string  $post_link Generated post permalink.
 * @param WP_Post $post      Post object.
 * @param bool    $leavename Whether to keep %postname% placeholder.
 *
 * @return string
 */
function brooklyn_beauty_blog_post_link( $post_link, $post, $leavename ) {
	if ( ! $post instanceof WP_Post || 'post' !== $post->post_type ) {
		return $post_link;
	}

	$post_slug = $leavename ? '%postname%' : $post->post_name;

	if ( '' === $post_slug ) {
		$post_slug = sanitize_title( (string) $post->post_title );
	}

	return home_url( user_trailingslashit( 'blog/' . $post_slug ) );
}
add_filter( 'post_link', 'brooklyn_beauty_blog_post_link', 10, 3 );

/**
 * Flush rewrite rules once on theme activation.
 *
 * @return void
 */
function brooklyn_beauty_flush_rewrite_rules_on_switch() {
	brooklyn_beauty_add_blog_post_rewrite_rule();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'brooklyn_beauty_flush_rewrite_rules_on_switch' );

/**
 * Flush rewrite rules once after deploying rewrite changes.
 *
 * @return void
 */
function brooklyn_beauty_maybe_flush_rewrite_rules() {
	$rewrite_version_option = 'brooklyn_beauty_rewrite_version';
	$current_version        = '2';
	$stored_version         = (string) get_option( $rewrite_version_option, '' );

	if ( $stored_version === $current_version ) {
		return;
	}

	brooklyn_beauty_add_blog_post_rewrite_rule();
	flush_rewrite_rules( false );
	update_option( $rewrite_version_option, $current_version );
}
add_action( 'init', 'brooklyn_beauty_maybe_flush_rewrite_rules', 20 );
