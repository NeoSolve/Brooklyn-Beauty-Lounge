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

require_once get_template_directory() . '/inc/services.php';
require_once get_template_directory() . '/inc/blog.php';

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
	$services_page_css_path = get_template_directory() . '/assets/css/services-page.css';
	$single_service_css_path = get_template_directory() . '/assets/css/single-service.css';
	$single_service_prices_css_path = get_template_directory() . '/assets/css/single-service/prices.css';
	$single_service_why_choose_css_path = get_template_directory() . '/assets/css/single-service/why-choose.css';
	$single_service_reasons_css_path    = get_template_directory() . '/assets/css/single-service/reasons.css';
	$single_service_advantages_css_path = get_template_directory() . '/assets/css/single-service/advantages.css';
	$single_service_explore_css_path    = get_template_directory() . '/assets/css/single-service/explore.css';
	$book_appointment_css_path          = get_template_directory() . '/assets/css/components/book-appointment.css';
	$services_archive_css_path = get_template_directory() . '/assets/css/services-archive.css';
	$contacts_page_css_path = get_template_directory() . '/assets/css/contacts-page.css';
	$blog_page_css_path = get_template_directory() . '/assets/css/blog-page.css';
	$single_post_css_path = get_template_directory() . '/assets/css/single-post.css';
	$page_404_css_path = get_template_directory() . '/assets/css/404.css';
	$js_path               = get_template_directory() . '/assets/js/main.js';
	$services_page_js_path = get_template_directory() . '/assets/js/services-page/main.js';
	$single_service_reasons_js_path = get_template_directory() . '/assets/js/single-service/reasons.js';
	$services_tabs_js_path = get_template_directory() . '/assets/js/services-tabs.js';
	$our_work_js_path      = get_template_directory() . '/assets/js/our-work.js';
	$why_us_js_path        = get_template_directory() . '/assets/js/why-us.js';
	$video_tour_js_path    = get_template_directory() . '/assets/js/video-tour.js';
	$about_us_block_js_path = get_template_directory() . '/assets/js/about-us-block.js';
	$promotions_js_path    = get_template_directory() . '/assets/js/promotions.js';
	$reviews_js_path       = get_template_directory() . '/assets/js/reviews.js';
	$book_appointment_js_path = get_template_directory() . '/assets/js/book-appointment.js';
	$faq_js_path           = get_template_directory() . '/assets/js/faq.js';
	$hero_video_js_path    = get_template_directory() . '/assets/js/hero-video.js';
	$footer_js_path        = get_template_directory() . '/assets/js/footer.js';
	$contacts_map_js_path  = get_template_directory() . '/assets/js/contacts-map.js';
	$contacts_form_js_path = get_template_directory() . '/assets/js/contacts-form.js';
	$single_post_js_path   = get_template_directory() . '/assets/js/single-post.js';
	$other_articles_js_path = get_template_directory() . '/assets/js/other-articles.js';

	wp_enqueue_style(
		'brooklyn-beauty-fonts',
		'https://fonts.googleapis.com/css2?family=Geist:wght@100;200;300;400;500;600;700&family=Nothing+You+Could+Do&display=swap',
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

	if ( is_page_template( 'page-services.php' ) && file_exists( $services_page_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-services-page',
			get_template_directory_uri() . '/assets/css/services-page.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $services_page_css_path )
		);
	}

	if ( is_singular( 'service' ) && file_exists( $single_service_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-single-service',
			get_template_directory_uri() . '/assets/css/single-service.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $single_service_css_path )
		);
	}

	if ( is_singular( 'service' ) && file_exists( $single_service_why_choose_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-single-service-why-choose',
			get_template_directory_uri() . '/assets/css/single-service/why-choose.css',
			array( 'brooklyn-beauty-single-service' ),
			(string) filemtime( $single_service_why_choose_css_path )
		);
	}

	if ( is_singular( 'service' ) && file_exists( $single_service_prices_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-single-service-prices',
			get_template_directory_uri() . '/assets/css/single-service/prices.css',
			array( 'brooklyn-beauty-single-service' ),
			(string) filemtime( $single_service_prices_css_path )
		);
	}

	if ( is_singular( 'service' ) && file_exists( $single_service_reasons_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-single-service-reasons',
			get_template_directory_uri() . '/assets/css/single-service/reasons.css',
			array( 'brooklyn-beauty-single-service' ),
			(string) filemtime( $single_service_reasons_css_path )
		);
	}

	if ( is_singular( 'service' ) && file_exists( $single_service_advantages_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-single-service-advantages',
			get_template_directory_uri() . '/assets/css/single-service/advantages.css',
			array( 'brooklyn-beauty-single-service' ),
			(string) filemtime( $single_service_advantages_css_path )
		);
	}

	if ( is_singular( 'service' ) && file_exists( $single_service_explore_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-single-service-explore',
			get_template_directory_uri() . '/assets/css/single-service/explore.css',
			array( 'brooklyn-beauty-single-service' ),
			(string) filemtime( $single_service_explore_css_path )
		);
	}

	if ( ( is_front_page() || is_singular( 'service' ) || is_singular( 'post' ) ) && file_exists( $book_appointment_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-book-appointment',
			get_template_directory_uri() . '/assets/css/components/book-appointment.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $book_appointment_css_path )
		);
	}

	if ( is_post_type_archive( 'service' ) && file_exists( $services_archive_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-services-archive',
			get_template_directory_uri() . '/assets/css/services-archive.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $services_archive_css_path )
		);
	}

	if ( is_page_template( 'page-contacts.php' ) && file_exists( $contacts_page_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-contacts-page',
			get_template_directory_uri() . '/assets/css/contacts-page.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $contacts_page_css_path )
		);

		wp_enqueue_style(
			'brooklyn-beauty-maplibre',
			'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css',
			array(),
			'4.7.1'
		);
	}

	if ( is_page_template( 'page-blog.php' ) && file_exists( $blog_page_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-blog-page',
			get_template_directory_uri() . '/assets/css/blog-page.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $blog_page_css_path )
		);
	}

	if ( is_singular( 'post' ) && file_exists( $single_post_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-single-post',
			get_template_directory_uri() . '/assets/css/single-post.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $single_post_css_path )
		);
	}

	if ( is_404() && file_exists( $page_404_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-404',
			get_template_directory_uri() . '/assets/css/404.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $page_404_css_path )
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

		if ( file_exists( $footer_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-footer',
				get_template_directory_uri() . '/assets/js/footer.js',
				array( 'brooklyn-beauty-main' ),
				(string) filemtime( $footer_js_path ),
				true
			);
		}

		if ( is_page_template( 'page-services.php' ) && file_exists( $services_page_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-services-page',
				get_template_directory_uri() . '/assets/js/services-page/main.js',
				array( 'brooklyn-beauty-main' ),
				(string) filemtime( $services_page_js_path ),
				true
			);
		}

		if ( is_singular( 'service' ) && file_exists( $single_service_reasons_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-single-service-reasons',
				get_template_directory_uri() . '/assets/js/single-service/reasons.js',
				array( 'brooklyn-beauty-main' ),
				(string) filemtime( $single_service_reasons_js_path ),
				true
			);
		}

		if ( ( is_front_page() || is_page_template( 'page-services.php' ) ) && file_exists( $services_tabs_js_path ) ) {
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

		if ( is_front_page() && file_exists( $why_us_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-why-us',
				get_template_directory_uri() . '/assets/js/why-us.js',
				array(),
				(string) filemtime( $why_us_js_path ),
				true
			);
		}

		if ( is_front_page() && file_exists( $video_tour_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-video-tour',
				get_template_directory_uri() . '/assets/js/video-tour.js',
				array(),
				(string) filemtime( $video_tour_js_path ),
				true
			);
		}

		if ( is_front_page() && file_exists( $about_us_block_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-about-us-block',
				get_template_directory_uri() . '/assets/js/about-us-block.js',
				array(),
				(string) filemtime( $about_us_block_js_path ),
				true
			);
		}

		if ( ( is_front_page() || is_singular( 'service' ) ) && file_exists( $promotions_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-promotions',
				get_template_directory_uri() . '/assets/js/promotions.js',
				array(),
				(string) filemtime( $promotions_js_path ),
				true
			);
		}

		if ( is_front_page() && file_exists( $reviews_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-reviews',
				get_template_directory_uri() . '/assets/js/reviews.js',
				array(),
				(string) filemtime( $reviews_js_path ),
				true
			);
		}

		if ( ( is_front_page() || is_singular( 'service' ) || is_singular( 'post' ) ) && file_exists( $book_appointment_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-book-appointment',
				get_template_directory_uri() . '/assets/js/book-appointment.js',
				array(),
				(string) filemtime( $book_appointment_js_path ),
				true
			);
		}

		if ( ( is_front_page() || is_singular( 'service' ) || is_singular( 'post' ) ) && file_exists( $faq_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-faq',
				get_template_directory_uri() . '/assets/js/faq.js',
				array(),
				(string) filemtime( $faq_js_path ),
				true
			);
		}

		if ( is_front_page() && file_exists( $hero_video_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-hero-video',
				get_template_directory_uri() . '/assets/js/hero-video.js',
				array(),
				(string) filemtime( $hero_video_js_path ),
				true
			);
		}

		if ( is_page_template( 'page-contacts.php' ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-maplibre',
				'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js',
				array(),
				'4.7.1',
				true
			);

			if ( file_exists( $contacts_map_js_path ) ) {
				wp_enqueue_script(
					'brooklyn-beauty-contacts-map',
					get_template_directory_uri() . '/assets/js/contacts-map.js',
					array( 'brooklyn-beauty-maplibre' ),
					(string) filemtime( $contacts_map_js_path ),
					true
				);
			}

			if ( file_exists( $contacts_form_js_path ) ) {
				wp_enqueue_script(
					'brooklyn-beauty-contacts-form',
					get_template_directory_uri() . '/assets/js/contacts-form.js',
					array(),
					(string) filemtime( $contacts_form_js_path ),
					true
				);
			}
		}

		if ( is_singular( 'post' ) && file_exists( $single_post_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-single-post',
				get_template_directory_uri() . '/assets/js/single-post.js',
				array( 'brooklyn-beauty-main' ),
				(string) filemtime( $single_post_js_path ),
				true
			);
		}

		if ( is_singular( 'post' ) && file_exists( $other_articles_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-other-articles',
				get_template_directory_uri() . '/assets/js/other-articles.js',
				array(),
				(string) filemtime( $other_articles_js_path ),
				true
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'brooklyn_beauty_assets' );

/**
 * Enqueue admin scripts for ACF editing UX.
 *
 * @param string $hook_suffix Current admin page hook suffix.
 *
 * @return void
 */
function brooklyn_beauty_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$acf_repeater_duplicate_js_path = get_template_directory() . '/assets/js/admin/acf-repeater-duplicate.js';

	if ( file_exists( $acf_repeater_duplicate_js_path ) ) {
		wp_enqueue_script(
			'brooklyn-beauty-acf-repeater-duplicate',
			get_template_directory_uri() . '/assets/js/admin/acf-repeater-duplicate.js',
			array( 'jquery', 'acf-input' ),
			(string) filemtime( $acf_repeater_duplicate_js_path ),
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'brooklyn_beauty_admin_assets' );

/**
 * Disable Gutenberg block editors.
 */
add_filter( 'use_block_editor_for_post', '__return_false', 100 );
add_filter( 'use_widgets_block_editor', '__return_false', 100 );

/**
 * Output Open Graph meta tags for single posts.
 */
function brooklyn_beauty_og_meta_tags() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post_id   = (int) get_the_ID();
	$post      = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	$og_title   = wp_strip_all_tags( (string) get_the_title( $post_id ) );
	$og_url     = (string) get_permalink( $post_id );
	$og_type    = 'article';

	$og_description = '';
	if ( function_exists( 'get_field' ) ) {
		$acf_desc = trim( (string) get_field( 'meta_description', $post_id ) );
		if ( '' !== $acf_desc ) {
			$og_description = $acf_desc;
		}
	}
	if ( '' === $og_description && defined( 'WPSEO_VERSION' ) ) {
		$yoast_desc = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		if ( is_string( $yoast_desc ) && '' !== trim( $yoast_desc ) ) {
			$og_description = $yoast_desc;
		}
	}
	if ( '' === $og_description ) {
		$og_description = trim( (string) get_the_excerpt( $post_id ) );
	}
	if ( '' === $og_description ) {
		$og_description = wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 30, '...' );
	}

	$og_image = '';
	if ( function_exists( 'get_field' ) ) {
		$acf_image = get_field( 'single_post_hero_image', $post_id );
		if ( is_numeric( $acf_image ) ) {
			$og_image = (string) wp_get_attachment_image_url( (int) $acf_image, 'large' );
		} elseif ( is_array( $acf_image ) && ! empty( $acf_image['url'] ) ) {
			$og_image = (string) $acf_image['url'];
		}
	}
	if ( '' === $og_image && has_post_thumbnail( $post_id ) ) {
		$og_image = (string) get_the_post_thumbnail_url( $post_id, 'large' );
	}

	?>
	<meta property="og:title" content="<?php echo esc_attr( $og_title ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $og_type ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $og_url ); ?>">
	<?php if ( '' !== $og_description ) : ?>
		<meta property="og:description" content="<?php echo esc_attr( $og_description ); ?>">
	<?php endif; ?>
	<?php if ( '' !== $og_image ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $og_image ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'brooklyn_beauty_og_meta_tags', 5 );

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
		'title'                 => __( 'Site Settings', 'brooklyn-beauty' ),
		'fields'                => array(
			array(
				'key'       => 'field_brooklyn_beauty_site_settings_header_tab',
				'label'     => __( 'Header', 'brooklyn-beauty' ),
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
				'endpoint'  => 0,
			),
			array(
				'key'           => 'field_brooklyn_beauty_header_logo_alt',
				'label'         => __( 'Header Logo (Solid Background)', 'brooklyn-beauty' ),
				'name'          => 'header_logo_alt',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'instructions'  => __( 'Shown when header has a solid background (outside hero blur state).', 'brooklyn-beauty' ),
			),
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
			array(
				'key'       => 'field_brooklyn_beauty_site_settings_footer_tab',
				'label'     => __( 'Footer', 'brooklyn-beauty' ),
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
				'endpoint'  => 0,
			),
			array(
				'key'        => 'field_brooklyn_beauty_footer_cta_accordion',
				'label'      => __( 'Footer CTA', 'brooklyn-beauty' ),
				'name'       => '',
				'type'       => 'accordion',
				'open'       => 1,
				'multi_expand' => 1,
				'endpoint'   => 0,
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_tagline',
				'label' => __( 'Footer Tagline', 'brooklyn-beauty' ),
				'name'  => 'footer_tagline',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '100',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_button_text',
				'label' => __( 'Footer Button Text', 'brooklyn-beauty' ),
				'name'  => 'footer_button_text',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '50',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_button_link',
				'label' => __( 'Footer Button Link', 'brooklyn-beauty' ),
				'name'  => 'footer_button_link',
				'type'  => 'url',
				'wrapper' => array(
					'width' => '50',
				),
			),
			array(
				'key'        => 'field_brooklyn_beauty_footer_contacts_accordion',
				'label'      => __( 'Footer Contacts', 'brooklyn-beauty' ),
				'name'       => '',
				'type'       => 'accordion',
				'open'       => 0,
				'multi_expand' => 1,
				'endpoint'   => 0,
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_address_label',
				'label' => __( 'Footer Address Label', 'brooklyn-beauty' ),
				'name'  => 'footer_address_label',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_address_text',
				'label' => __( 'Footer Address Line 1', 'brooklyn-beauty' ),
				'name'  => 'footer_address_text',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '17',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_address_text_line_2',
				'label' => __( 'Footer Address Line 2', 'brooklyn-beauty' ),
				'name'  => 'footer_address_text_line_2',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '17',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_address_link',
				'label' => __( 'Footer Address Link', 'brooklyn-beauty' ),
				'name'  => 'footer_address_link',
				'type'  => 'url',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_phone_label',
				'label' => __( 'Footer Phone Label', 'brooklyn-beauty' ),
				'name'  => 'footer_phone_label',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_phone_text',
				'label' => __( 'Footer Phone Text', 'brooklyn-beauty' ),
				'name'  => 'footer_phone_text',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '34',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_phone_link',
				'label' => __( 'Footer Phone Link', 'brooklyn-beauty' ),
				'name'  => 'footer_phone_link',
				'type'  => 'url',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_email_label',
				'label' => __( 'Footer Email Label', 'brooklyn-beauty' ),
				'name'  => 'footer_email_label',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_email_text',
				'label' => __( 'Footer Email Text', 'brooklyn-beauty' ),
				'name'  => 'footer_email_text',
				'type'  => 'email',
				'wrapper' => array(
					'width' => '34',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_email_link',
				'label' => __( 'Footer Email Link', 'brooklyn-beauty' ),
				'name'  => 'footer_email_link',
				'type'  => 'url',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_hours_label',
				'label' => __( 'Footer Hours Label', 'brooklyn-beauty' ),
				'name'  => 'footer_hours_label',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_hours_days',
				'label' => __( 'Footer Hours Days', 'brooklyn-beauty' ),
				'name'  => 'footer_hours_days',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '34',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_hours_time',
				'label' => __( 'Footer Hours Time', 'brooklyn-beauty' ),
				'name'  => 'footer_hours_time',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'        => 'field_brooklyn_beauty_footer_social_accordion',
				'label'      => __( 'Footer Social', 'brooklyn-beauty' ),
				'name'       => '',
				'type'       => 'accordion',
				'open'       => 0,
				'multi_expand' => 1,
				'endpoint'   => 0,
			),
			array(
				'key'          => 'field_brooklyn_beauty_footer_social_items',
				'label'        => __( 'Footer Social Items', 'brooklyn-beauty' ),
				'name'         => 'footer_social_items',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => __( 'Add Social Item', 'brooklyn-beauty' ),
				'sub_fields'   => array(
					array(
						'key'           => 'field_brooklyn_beauty_footer_social_icon_image',
						'label'         => __( 'Icon Image', 'brooklyn-beauty' ),
						'name'          => 'icon_image',
						'type'          => 'image',
						'return_format' => 'id',
						'preview_size'  => 'thumbnail',
						'library'       => 'all',
						'wrapper'       => array(
							'width' => '50',
						),
					),
					array(
						'key'   => 'field_brooklyn_beauty_footer_social_link',
						'label' => __( 'Link', 'brooklyn-beauty' ),
						'name'  => 'link',
						'type'  => 'url',
						'wrapper' => array(
							'width' => '50',
						),
					),
				),
			),
			array(
				'key'        => 'field_brooklyn_beauty_footer_featured_accordion',
				'label'      => __( 'Footer Featured', 'brooklyn-beauty' ),
				'name'       => '',
				'type'       => 'accordion',
				'open'       => 0,
				'multi_expand' => 1,
				'endpoint'   => 0,
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_featured_label',
				'label' => __( 'Footer Featured Label', 'brooklyn-beauty' ),
				'name'  => 'footer_featured_label',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '100',
				),
			),
			array(
				'key'           => 'field_brooklyn_beauty_footer_press_title_image',
				'label'         => __( 'Footer Press Title Image', 'brooklyn-beauty' ),
				'name'          => 'footer_press_title_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'wrapper'       => array(
					'width' => '50',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_press_title_link',
				'label' => __( 'Footer Press Title Link', 'brooklyn-beauty' ),
				'name'  => 'footer_press_title_link',
				'type'  => 'url',
				'wrapper' => array(
					'width' => '50',
				),
			),
			array(
				'key'           => 'field_brooklyn_beauty_footer_press_subtitle_image',
				'label'         => __( 'Footer Press Subtitle Image', 'brooklyn-beauty' ),
				'name'          => 'footer_press_subtitle_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'wrapper'       => array(
					'width' => '50',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_press_subtitle_link',
				'label' => __( 'Footer Press Subtitle Link', 'brooklyn-beauty' ),
				'name'  => 'footer_press_subtitle_link',
				'type'  => 'url',
				'wrapper' => array(
					'width' => '50',
				),
			),
			array(
				'key'        => 'field_brooklyn_beauty_footer_bottom_accordion',
				'label'      => __( 'Footer Bottom', 'brooklyn-beauty' ),
				'name'       => '',
				'type'       => 'accordion',
				'open'       => 0,
				'multi_expand' => 1,
				'endpoint'   => 0,
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_made_by_text',
				'label' => __( 'Footer Made By Text', 'brooklyn-beauty' ),
				'name'  => 'footer_made_by_text',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_made_by_link_text',
				'label' => __( 'Footer Made By Link Text', 'brooklyn-beauty' ),
				'name'  => 'footer_made_by_link_text',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '34',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_made_by_link',
				'label' => __( 'Footer Made By Link', 'brooklyn-beauty' ),
				'name'  => 'footer_made_by_link',
				'type'  => 'url',
				'wrapper' => array(
					'width' => '33',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_terms_text',
				'label' => __( 'Footer Terms Text', 'brooklyn-beauty' ),
				'name'  => 'footer_terms_text',
				'type'  => 'text',
				'wrapper' => array(
					'width' => '50',
				),
			),
			array(
				'key'   => 'field_brooklyn_beauty_footer_terms_link',
				'label' => __( 'Footer Terms Link', 'brooklyn-beauty' ),
				'name'  => 'footer_terms_link',
				'type'  => 'url',
				'wrapper' => array(
					'width' => '50',
				),
			),
			array(
				'key'       => 'field_brooklyn_beauty_site_settings_share_tab',
				'label'     => __( 'Share Article', 'brooklyn-beauty' ),
				'name'      => '',
				'type'      => 'tab',
				'placement' => 'top',
				'endpoint'  => 0,
			),
			array(
				'key'           => 'field_brooklyn_beauty_share_article_label',
				'label'         => __( 'Share Label', 'brooklyn-beauty' ),
				'name'          => 'share_article_label',
				'type'          => 'text',
				'default_value' => __( 'Share Article to:', 'brooklyn-beauty' ),
			),
			array(
				'key'          => 'field_brooklyn_beauty_share_article_items',
				'label'        => __( 'Share Items', 'brooklyn-beauty' ),
				'name'         => 'share_article_items',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => __( 'Add Share Item', 'brooklyn-beauty' ),
				'sub_fields'   => array(
					array(
						'key'           => 'field_brooklyn_beauty_share_article_item_type',
						'label'         => __( 'Type', 'brooklyn-beauty' ),
						'name'          => 'type',
						'type'          => 'select',
						'choices'       => array(
							'copy'     => __( 'Copy Link', 'brooklyn-beauty' ),
							'facebook' => __( 'Facebook', 'brooklyn-beauty' ),
							'x'        => __( 'X (Twitter)', 'brooklyn-beauty' ),
							'whatsapp' => __( 'WhatsApp', 'brooklyn-beauty' ),
							'custom'   => __( 'Custom URL', 'brooklyn-beauty' ),
						),
						'default_value' => 'copy',
						'ui'            => 1,
						'wrapper'       => array(
							'width' => '25',
						),
					),
					array(
						'key'           => 'field_brooklyn_beauty_share_article_item_icon',
						'label'         => __( 'Icon Image', 'brooklyn-beauty' ),
						'name'          => 'icon_image',
						'type'          => 'image',
						'return_format' => 'id',
						'preview_size'  => 'thumbnail',
						'library'       => 'all',
						'wrapper'       => array(
							'width' => '25',
						),
					),
					array(
						'key'           => 'field_brooklyn_beauty_share_article_item_aria_label',
						'label'         => __( 'Accessible Label', 'brooklyn-beauty' ),
						'name'          => 'aria_label',
						'type'          => 'text',
						'instructions'  => __( 'Optional. If empty, generated from selected type.', 'brooklyn-beauty' ),
						'wrapper'       => array(
							'width' => '25',
						),
					),
					array(
						'key'           => 'field_brooklyn_beauty_share_article_item_custom_url',
						'label'         => __( 'Custom URL', 'brooklyn-beauty' ),
						'name'          => 'custom_url',
						'type'          => 'url',
						'instructions'  => __( 'Used only when type is Custom URL. Supports placeholders: {url}, {title}.', 'brooklyn-beauty' ),
						'wrapper'       => array(
							'width' => '25',
						),
					),
				),
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

	acf_add_local_field_group( array(
		'key'                   => 'group_brooklyn_beauty_about_us_block',
		'title'                 => __( 'About Us Block', 'brooklyn-beauty' ),
		'fields'                => array(
			array(
				'key'   => 'field_brooklyn_beauty_about_us_title',
				'label' => __( 'Section Title', 'brooklyn-beauty' ),
				'name'  => 'about_us_title',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_brooklyn_beauty_about_us_left_text',
				'label' => __( 'Left Text (Top)', 'brooklyn-beauty' ),
				'name'  => 'about_us_left_text',
				'type'  => 'textarea',
				'rows'  => 4,
			),
			array(
				'key'   => 'field_brooklyn_beauty_about_us_left_bottom_text',
				'label' => __( 'Left Text (Bottom)', 'brooklyn-beauty' ),
				'name'  => 'about_us_left_bottom_text',
				'type'  => 'textarea',
				'rows'  => 4,
			),
			array(
				'key'   => 'field_brooklyn_beauty_about_us_right_text',
				'label' => __( 'Right Text', 'brooklyn-beauty' ),
				'name'  => 'about_us_right_text',
				'type'  => 'textarea',
				'rows'  => 4,
			),
			array(
				'key'          => 'field_brooklyn_beauty_about_us_slides',
				'label'        => __( 'Quote Slides', 'brooklyn-beauty' ),
				'name'         => 'about_us_slides',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => __( 'Add Slide', 'brooklyn-beauty' ),
				'sub_fields'   => array(
					array(
						'key'           => 'field_brooklyn_beauty_about_us_slide_portrait',
						'label'         => __( 'Portrait', 'brooklyn-beauty' ),
						'name'          => 'portrait',
						'type'          => 'image',
						'return_format' => 'id',
						'preview_size'  => 'medium',
						'library'       => 'all',
					),
					array(
						'key'   => 'field_brooklyn_beauty_about_us_slide_name',
						'label' => __( 'Name', 'brooklyn-beauty' ),
						'name'  => 'name',
						'type'  => 'text',
					),
					array(
						'key'          => 'field_brooklyn_beauty_about_us_slide_quote',
						'label'        => __( 'Quote (one line per row)', 'brooklyn-beauty' ),
						'name'         => 'quote',
						'type'         => 'textarea',
						'rows'         => 4,
						'new_lines'    => '',
						'instructions' => __( 'Each new line becomes a separate quote row.', 'brooklyn-beauty' ),
					),
					array(
						'key'   => 'field_brooklyn_beauty_about_us_slide_highlighted_line',
						'label' => __( 'Highlighted Line', 'brooklyn-beauty' ),
						'name'  => 'highlighted_line',
						'type'  => 'text',
					),
				),
			),
			array(
				'key'           => 'field_brooklyn_beauty_about_us_gallery',
				'label'         => __( 'Right Gallery Images', 'brooklyn-beauty' ),
				'name'          => 'about_us_gallery',
				'type'          => 'gallery',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'min'           => 0,
				'max'           => 0,
				'insert'        => 'append',
			),
			array(
				'key'           => 'field_brooklyn_beauty_about_us_main_image',
				'label'         => __( 'Right Image Fallback', 'brooklyn-beauty' ),
				'name'          => 'about_us_main_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'instructions'  => __( 'Used only if gallery is empty.', 'brooklyn-beauty' ),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'front_page',
				),
			),
		),
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'                   => 'group_brooklyn_beauty_reviews_block',
		'title'                 => __( 'Reviews Block', 'brooklyn-beauty' ),
		'fields'                => array(
			array(
				'key'   => 'field_brooklyn_beauty_reviews_title',
				'label' => __( 'Section Title', 'brooklyn-beauty' ),
				'name'  => 'reviews_title',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_brooklyn_beauty_reviews_text',
				'label' => __( 'Section Description', 'brooklyn-beauty' ),
				'name'  => 'reviews_text',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_brooklyn_beauty_reviews_label',
				'label' => __( 'Left Label', 'brooklyn-beauty' ),
				'name'  => 'reviews_label',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'          => 'field_brooklyn_beauty_reviews_items',
				'label'        => __( 'Reviews Items', 'brooklyn-beauty' ),
				'name'         => 'reviews_items',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => __( 'Add Review', 'brooklyn-beauty' ),
				'sub_fields'   => array(
					array(
						'key'   => 'field_brooklyn_beauty_reviews_item_title',
						'label' => __( 'Title', 'brooklyn-beauty' ),
						'name'  => 'title',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_brooklyn_beauty_reviews_item_text',
						'label' => __( 'Text', 'brooklyn-beauty' ),
						'name'  => 'text',
						'type'  => 'textarea',
						'rows'  => 6,
					),
					array(
						'key'           => 'field_brooklyn_beauty_reviews_item_rating',
						'label'         => __( 'Rating (Stars)', 'brooklyn-beauty' ),
						'name'          => 'rating',
						'type'          => 'number',
						'default_value' => 5,
						'min'           => 1,
						'max'           => 5,
						'step'          => 1,
					),
					array(
						'key'   => 'field_brooklyn_beauty_reviews_item_author',
						'label' => __( 'Author', 'brooklyn-beauty' ),
						'name'  => 'author',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_brooklyn_beauty_reviews_item_time',
						'label' => __( 'Date Text', 'brooklyn-beauty' ),
						'name'  => 'time',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_brooklyn_beauty_reviews_item_link',
						'label' => __( 'Read More Link', 'brooklyn-beauty' ),
						'name'  => 'link',
						'type'  => 'url',
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'front_page',
				),
			),
		),
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'                   => 'group_brooklyn_beauty_book_appointment_block',
		'title'                 => __( 'Book Appointment Block', 'brooklyn-beauty' ),
		'fields'                => array(
			array(
				'key'   => 'field_brooklyn_beauty_book_appointment_title',
				'label' => __( 'Section Title', 'brooklyn-beauty' ),
				'name'  => 'book_appointment_title',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_brooklyn_beauty_book_appointment_text_top',
				'label' => __( 'Top Description', 'brooklyn-beauty' ),
				'name'  => 'book_appointment_text_top',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_brooklyn_beauty_book_appointment_text_bottom',
				'label' => __( 'Bottom Description', 'brooklyn-beauty' ),
				'name'  => 'book_appointment_text_bottom',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_brooklyn_beauty_book_appointment_button_text',
				'label' => __( 'Button Text', 'brooklyn-beauty' ),
				'name'  => 'book_appointment_button_text',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_brooklyn_beauty_book_appointment_button_link',
				'label' => __( 'Button Link', 'brooklyn-beauty' ),
				'name'  => 'book_appointment_button_link',
				'type'  => 'text',
				'instructions' => __( 'Use a URL or section id (for example: #services).', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_book_appointment_content_image',
				'label'         => __( 'Left Column Content Image', 'brooklyn-beauty' ),
				'name'          => 'book_appointment_content_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'library'       => 'all',
			),
			array(
				'key'           => 'field_brooklyn_beauty_book_appointment_main_image',
				'label'         => __( 'Right Column Main Image', 'brooklyn-beauty' ),
				'name'          => 'book_appointment_main_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'large',
				'library'       => 'all',
			),
			array(
				'key'           => 'field_brooklyn_beauty_book_appointment_fallback_media_image',
				'label'         => __( 'Right Column Fallback Image', 'brooklyn-beauty' ),
				'name'          => 'book_appointment_fallback_media_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'large',
				'library'       => 'all',
				'instructions'  => __( 'Used when the main right image is empty.', 'brooklyn-beauty' ),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'front_page',
				),
			),
		),
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'                   => 'group_brooklyn_beauty_single_service_hero',
		'title'                 => __( 'Single Service Hero', 'brooklyn-beauty' ),
		'fields'                => array(
			array(
				'key'           => 'field_brooklyn_beauty_single_service_hero_image',
				'label'         => __( 'Hero Image Override', 'brooklyn-beauty' ),
				'name'          => 'service_hero_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'large',
				'library'       => 'all',
				'instructions'  => __( 'Optional. If empty, featured image is used.', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_card_image',
				'label'         => __( 'Services Card Image', 'brooklyn-beauty' ),
				'name'          => 'service_card_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'large',
				'library'       => 'all',
				'instructions'  => __( 'Image for service card in the homepage services block. If empty, featured image is used.', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_hero_tagline',
				'label'         => __( 'Tagline', 'brooklyn-beauty' ),
				'name'          => 'service_hero_tagline',
				'type'          => 'textarea',
				'default_value' => __( 'be fabulous with brooklyn beauty lounge!', 'brooklyn-beauty' ),
				'rows'          => 2,
				'new_lines'     => '',
				'instructions'  => __( 'HTML is allowed (for example: <br> for a line break).', 'brooklyn-beauty' ),
			),
			array(
				'key'   => 'field_brooklyn_beauty_single_service_hero_intro_left',
				'label' => __( 'Left Intro Text', 'brooklyn-beauty' ),
				'name'  => 'service_hero_intro_left',
				'type'  => 'textarea',
				'rows'  => 4,
				'instructions' => __( 'Left text column in hero. If empty, generated from content.', 'brooklyn-beauty' ),
			),
			array(
				'key'   => 'field_brooklyn_beauty_single_service_hero_intro_right',
				'label' => __( 'Right Intro Text', 'brooklyn-beauty' ),
				'name'  => 'service_hero_intro_right',
				'type'  => 'textarea',
				'rows'  => 4,
				'instructions' => __( 'Right text column in hero. If empty, generated from content.', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_hero_button_text',
				'label'         => __( 'Button Text', 'brooklyn-beauty' ),
				'name'          => 'service_hero_button_text',
				'type'          => 'text',
				'default_value' => __( 'book now', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_hero_button_link',
				'label'         => __( 'Button Link', 'brooklyn-beauty' ),
				'name'          => 'service_hero_button_link',
				'type'          => 'text',
				'default_value' => '/#book',
				'instructions'  => __( 'Use URL or section id, e.g. /#book or #book.', 'brooklyn-beauty' ),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'service',
				),
			),
		),
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'                   => 'group_brooklyn_beauty_single_service_why_choose',
		'title'                 => __( 'Single Service Why Choose', 'brooklyn-beauty' ),
		'fields'                => array(
			array(
				'key'           => 'field_brooklyn_beauty_single_service_why_choose_title',
				'label'         => __( 'Section Title', 'brooklyn-beauty' ),
				'name'          => 'service_why_choose_title',
				'type'          => 'textarea',
				'rows'          => 2,
				'new_lines'     => '',
				'default_value' => __( 'why to choose brooklyn beauty lounge for this service?', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_why_choose_label',
				'label'         => __( 'Decorative Label', 'brooklyn-beauty' ),
				'name'          => 'service_why_choose_label',
				'type'          => 'text',
				'default_value' => __( 'what sets us apart', 'brooklyn-beauty' ),
			),
			array(
				'key'          => 'field_brooklyn_beauty_single_service_why_choose_items',
				'label'        => __( 'Cards', 'brooklyn-beauty' ),
				'name'         => 'service_why_choose_items',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => __( 'Add Card', 'brooklyn-beauty' ),
				'min'          => 0,
				'max'          => 6,
				'sub_fields'   => array(
					array(
						'key'           => 'field_brooklyn_beauty_single_service_why_choose_item_number',
						'label'         => __( 'Number', 'brooklyn-beauty' ),
						'name'          => 'number',
						'type'          => 'text',
						'default_value' => '',
						'instructions'  => __( 'Optional. Example: 001', 'brooklyn-beauty' ),
						'wrapper'       => array(
							'width' => '20',
						),
					),
					array(
						'key'     => 'field_brooklyn_beauty_single_service_why_choose_item_title',
						'label'   => __( 'Title', 'brooklyn-beauty' ),
						'name'    => 'title',
						'type'    => 'text',
						'wrapper' => array(
							'width' => '40',
						),
					),
					array(
						'key'     => 'field_brooklyn_beauty_single_service_why_choose_item_text',
						'label'   => __( 'Text', 'brooklyn-beauty' ),
						'name'    => 'text',
						'type'    => 'textarea',
						'rows'    => 4,
						'wrapper' => array(
							'width' => '40',
						),
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'service',
				),
			),
		),
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'                   => 'group_brooklyn_beauty_faq_block',
		'title'                 => __( 'FAQ Block', 'brooklyn-beauty' ),
		'fields'                => array(
			array(
				'key'           => 'field_brooklyn_beauty_faq_left_image',
				'label'         => __( 'Left Image', 'brooklyn-beauty' ),
				'name'          => 'faq_left_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
			),
			array(
				'key'           => 'field_brooklyn_beauty_faq_badge_image',
				'label'         => __( 'Center Badge Image', 'brooklyn-beauty' ),
				'name'          => 'faq_badge_image',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'instructions'  => __( 'Optional. If empty, text badge will be shown.', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_faq_badge_subtitle',
				'label'         => __( 'Center Badge Subtitle', 'brooklyn-beauty' ),
				'name'          => 'faq_badge_subtitle',
				'type'          => 'text',
				'default_value' => __( 'curious?', 'brooklyn-beauty' ),
			),
			array(
				'key'          => 'field_brooklyn_beauty_faq_items',
				'label'        => __( 'FAQ Items', 'brooklyn-beauty' ),
				'name'         => 'faq_items',
				'type'         => 'repeater',
				'layout'       => 'row',
				'button_label' => __( 'Add FAQ Item', 'brooklyn-beauty' ),
				'sub_fields'   => array(
					array(
						'key'   => 'field_brooklyn_beauty_faq_item_question',
						'label' => __( 'Question', 'brooklyn-beauty' ),
						'name'  => 'question',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_brooklyn_beauty_faq_item_answer',
						'label' => __( 'Answer', 'brooklyn-beauty' ),
						'name'  => 'answer',
						'type'  => 'textarea',
						'rows'  => 5,
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'front_page',
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
