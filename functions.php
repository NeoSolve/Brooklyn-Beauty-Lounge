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
 * Enqueue scripts and styles
 */
function brooklyn_beauty_assets() {
	$css_path      = get_template_directory() . '/assets/css/main.css';
	$home_css_path = get_template_directory() . '/assets/css/home.css';
	$js_path       = get_template_directory() . '/assets/js/main.js';

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
