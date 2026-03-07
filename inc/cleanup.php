<?php
/**
 * WordPress cleanup and removed features.
 *
 * Disables Gutenberg block editor for posts and widgets so the theme
 * uses classic editor and classic widgets.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disable Gutenberg block editors.
 */
add_filter( 'use_block_editor_for_post', '__return_false', 100 );
add_filter( 'use_widgets_block_editor', '__return_false', 100 );
