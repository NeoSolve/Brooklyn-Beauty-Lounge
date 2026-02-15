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

/**
 * Theme setup
 */
function brooklyn_beauty_setup() {
	load_theme_textdomain( 'brooklyn-beauty', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'brooklyn_beauty_setup' );

/**
 * Register menus
 */
function brooklyn_beauty_menus() {
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'brooklyn-beauty' ),
		'footer'  => __( 'Footer Menu', 'brooklyn-beauty' ),
	) );
}
add_action( 'init', 'brooklyn_beauty_menus' );

/**
 * Register Services post type and categories taxonomy.
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
			'menu_icon'    => 'dashicons-cutlery',
			'has_archive'  => true,
			'rewrite'      => array(
				'slug' => 'services',
			),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

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
add_action( 'init', 'brooklyn_beauty_register_services_content' );

/**
 * Enqueue scripts and styles
 */
function brooklyn_beauty_assets() {
	$css_path              = get_template_directory() . '/assets/css/main.css';
	$home_css_path         = get_template_directory() . '/assets/css/home.css';
	$js_path               = get_template_directory() . '/assets/js/main.js';
	$services_tabs_js_path = get_template_directory() . '/assets/js/services-tabs.js';
	$our_work_js_path      = get_template_directory() . '/assets/js/our-work.js';

	wp_enqueue_style(
		'brooklyn-beauty-fonts',
		'https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Nothing+You+Could+Do&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'brooklyn-beauty-style', get_stylesheet_uri(), array(), BROOKLYN_BEAUTY_VERSION );

	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-main',
			get_template_directory_uri() . '/assets/css/main.css',
			array( 'brooklyn-beauty-fonts', 'brooklyn-beauty-style' ),
			(string) filemtime( $css_path )
		);
	}

	if ( is_front_page() && file_exists( $home_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-home',
			get_template_directory_uri() . '/assets/css/home.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $home_css_path )
		);
	}

	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'brooklyn-beauty-main',
			get_template_directory_uri() . '/assets/js/main.js',
			array(),
			(string) filemtime( $js_path ),
			true
		);

		if ( is_front_page() && file_exists( $services_tabs_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-services-tabs',
				get_template_directory_uri() . '/assets/js/services-tabs.js',
				array(),
				(string) filemtime( $services_tabs_js_path ),
				true
			);

			wp_localize_script(
				'brooklyn-beauty-services-tabs',
				'bbServicesAjax',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'bb_services_filter' ),
				)
			);
		}

		if ( is_front_page() && file_exists( $our_work_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-our-work',
				get_template_directory_uri() . '/assets/js/our-work.js',
				array( 'brooklyn-beauty-main' ),
				(string) filemtime( $our_work_js_path ),
				true
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'brooklyn_beauty_assets' );

/**
 * Disable Gutenberg block editors.
 */
add_filter( 'use_block_editor_for_post', '__return_false', 100 );
add_filter( 'use_widgets_block_editor', '__return_false', 100 );

/**
 * Register ACF options pages.
 */
function brooklyn_beauty_register_acf_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( array(
		'page_title' => __( 'Site Settings', 'brooklyn-beauty' ),
		'menu_title' => __( 'Site Settings', 'brooklyn-beauty' ),
		'menu_slug'  => 'brooklyn-beauty-site-settings',
		'capability' => 'edit_posts',
		'redirect'   => false,
		'position'   => 58,
	) );
}
add_action( 'acf/init', 'brooklyn_beauty_register_acf_options_pages' );

/**
 * Register ACF field groups.
 */
function brooklyn_beauty_register_acf_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'                   => 'group_brooklyn_beauty_header_settings',
		'title'                 => __( 'Header Settings', 'brooklyn-beauty' ),
		'fields'                => array(
			array(
				'key'          => 'field_brooklyn_beauty_header_meta_items',
				'label'        => __( 'Header Meta Items', 'brooklyn-beauty' ),
				'name'         => 'header_meta_items',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => __( 'Add Meta Item', 'brooklyn-beauty' ),
				'sub_fields'   => array(
					array(
						'key'   => 'field_brooklyn_beauty_header_meta_label',
						'label' => __( 'Label', 'brooklyn-beauty' ),
						'name'  => 'label',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_brooklyn_beauty_header_meta_link',
						'label' => __( 'Link', 'brooklyn-beauty' ),
						'name'  => 'link',
						'type'  => 'url',
					),
				),
			),
			array(
				'key'          => 'field_brooklyn_beauty_header_social_items',
				'label'        => __( 'Header Social Items', 'brooklyn-beauty' ),
				'name'         => 'header_social_items',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => __( 'Add Social Item', 'brooklyn-beauty' ),
				'sub_fields'   => array(
				array(
					'key'           => 'field_brooklyn_beauty_header_social_icon_image',
					'label'         => __( 'Icon Image', 'brooklyn-beauty' ),
					'name'          => 'icon_image',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'thumbnail',
					'library'       => 'all',
				),
				array(
					'key'           => 'field_brooklyn_beauty_header_social_icon_image_dark',
					'label'         => __( 'Icon Image (Dark)', 'brooklyn-beauty' ),
					'name'          => 'icon_image_dark',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'thumbnail',
					'library'       => 'all',
					'instructions'  => __( 'Dark version of the icon, used when header text is dark.', 'brooklyn-beauty' ),
				),
				array(
					'key'   => 'field_brooklyn_beauty_header_social_link',
						'label' => __( 'Link', 'brooklyn-beauty' ),
						'name'  => 'link',
						'type'  => 'url',
					),
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_header_button_text',
				'label' => __( 'Header Button Text', 'brooklyn-beauty' ),
				'name'  => 'header_button_text',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_brooklyn_beauty_header_button_link',
				'label' => __( 'Header Button Link', 'brooklyn-beauty' ),
				'name'  => 'header_button_link',
				'type'  => 'url',
			),
			array(
				'key'           => 'field_brooklyn_beauty_hero_banner_video',
				'label'         => __( 'Hero Banner Video', 'brooklyn-beauty' ),
				'name'          => 'hero_banner_video',
				'type'          => 'file',
				'return_format' => 'id',
				'library'       => 'all',
				'mime_types'    => 'mp4,webm,m4v,mov,ogg',
				'instructions'  => __( 'Upload a video file for the homepage hero banner.', 'brooklyn-beauty' ),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'brooklyn-beauty-site-settings',
				),
			),
		),
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

}
add_action( 'acf/init', 'brooklyn_beauty_register_acf_field_groups' );

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
		'posts_per_page' => 9,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
	);

	$category_slug = sanitize_title( (string) $category_slug );

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

	if ( empty( $service_posts ) ) {
		return '<div class="bb-services-cards__empty">' . esc_html__( 'No services found in this category.', 'brooklyn-beauty' ) . '</div>';
	}

	$fallback_media_classes = array(
		'bb-service-card__media--pedicure',
		'bb-service-card__media--brows',
		'bb-service-card__media--makeup',
	);

	ob_start();
	foreach ( $service_posts as $index => $service_post ) {
		$media_class = $fallback_media_classes[ $index % count( $fallback_media_classes ) ];
		$media_image = (string) get_the_post_thumbnail_url( $service_post, 'large' );
		$service_text = get_the_excerpt( $service_post );

		if ( '' === trim( $service_text ) ) {
			$service_text = wp_trim_words( wp_strip_all_tags( $service_post->post_content ), 26, '...' );
		}
		?>
		<article class="bb-service-card">
			<div class="bb-service-card__media <?php echo esc_attr( $media_class ); ?>"<?php echo '' !== $media_image ? ' style="background-image: url(' . esc_url( $media_image ) . ');"' : ''; ?> aria-hidden="true"></div>
			<div class="bb-service-card__content">
				<h3 class="bb-service-card__title"><?php echo esc_html( get_the_title( $service_post ) ); ?></h3>
				<p class="bb-service-card__text"><?php echo esc_html( $service_text ); ?></p>
				<div class="bb-service-card__actions">
					<a class="btn btn--medium bb-service-card__visit-btn" href="#book">
						<?php esc_html_e( 'book a visit', 'brooklyn-beauty' ); ?>
					</a>
					<a class="bb-service-card__more-link" href="<?php echo esc_url( (string) get_permalink( $service_post ) ); ?>">
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
