<?php
/**
 * Theme setup, support, and menus.
 *
 * Handles after_setup_theme (text domain, title-tag, thumbnails, html5, custom logo,
 * selective refresh, responsive embeds, align-wide) and nav menu registration.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
