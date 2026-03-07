<?php
/**
 * Custom taxonomies.
 *
 * Registers the Service Category taxonomy for the Service post type.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Service Category taxonomy.
 */
function brooklyn_beauty_register_service_category_taxonomy() {
	register_taxonomy(
		'service_category',
		array( 'service' ),
		array(
			'labels'            => array(
				'name'              => __( 'Service Categories', 'brooklyn-beauty' ),
				'singular_name'     => __( 'Service Category', 'brooklyn-beauty' ),
				'search_items'      => __( 'Search Service Categories', 'brooklyn-beauty' ),
				'all_items'         => __( 'All Service Categories', 'brooklyn-beauty' ),
				'parent_item'       => __( 'Parent Service Category', 'brooklyn-beauty' ),
				'parent_item_colon' => __( 'Parent Service Category:', 'brooklyn-beauty' ),
				'edit_item'         => __( 'Edit Service Category', 'brooklyn-beauty' ),
				'update_item'       => __( 'Update Service Category', 'brooklyn-beauty' ),
				'add_new_item'      => __( 'Add New Service Category', 'brooklyn-beauty' ),
				'new_item_name'     => __( 'New Service Category Name', 'brooklyn-beauty' ),
				'menu_name'         => __( 'Categories', 'brooklyn-beauty' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug' => 'service-category',
			),
		)
	);
}
add_action( 'init', 'brooklyn_beauty_register_service_category_taxonomy' );
