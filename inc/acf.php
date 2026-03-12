<?php
/**
 * ACF (Advanced Custom Fields) related logic.
 *
 * Registers ACF options page (Site Settings) and all local field groups:
 * Site Settings (header, footer, share), About Us, Reviews, Book Appointment,
 * Single Service Hero, Why Choose, Blocks Visibility, FAQ.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
				'key'           => 'field_brooklyn_beauty_single_service_hero_title',
				'label'         => __( 'Title', 'brooklyn-beauty' ),
				'name'          => 'service_hero_title',
				'type'          => 'textarea',
				'rows'          => 2,
				'new_lines'     => '',
				'instructions'  => __( 'Optional. If empty, the service title is used. HTML is allowed.', 'brooklyn-beauty' ),
			),
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
		'key'                   => 'group_brooklyn_beauty_single_service_visibility',
		'title'                 => __( 'Single Service Blocks Visibility', 'brooklyn-beauty' ),
		'fields'                => array(
			array(
				'key'           => 'field_brooklyn_beauty_single_service_show_hero',
				'label'         => __( 'Show Hero Block', 'brooklyn-beauty' ),
				'name'          => 'service_show_hero',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'brooklyn-beauty' ),
				'ui_off_text'   => __( 'Hide', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_show_why_choose',
				'label'         => __( 'Show Why Choose Block', 'brooklyn-beauty' ),
				'name'          => 'service_show_why_choose',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'brooklyn-beauty' ),
				'ui_off_text'   => __( 'Hide', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_show_reasons',
				'label'         => __( 'Show Reasons Block', 'brooklyn-beauty' ),
				'name'          => 'service_show_reasons',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'brooklyn-beauty' ),
				'ui_off_text'   => __( 'Hide', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_show_prices',
				'label'         => __( 'Show Prices Block', 'brooklyn-beauty' ),
				'name'          => 'service_show_prices',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'brooklyn-beauty' ),
				'ui_off_text'   => __( 'Hide', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_show_promotions',
				'label'         => __( 'Show Promotions Block', 'brooklyn-beauty' ),
				'name'          => 'service_show_promotions',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'brooklyn-beauty' ),
				'ui_off_text'   => __( 'Hide', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_show_explore',
				'label'         => __( 'Show Explore Block', 'brooklyn-beauty' ),
				'name'          => 'service_show_explore',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'brooklyn-beauty' ),
				'ui_off_text'   => __( 'Hide', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_show_advantages',
				'label'         => __( 'Show Advantages Block', 'brooklyn-beauty' ),
				'name'          => 'service_show_advantages',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'brooklyn-beauty' ),
				'ui_off_text'   => __( 'Hide', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_show_book_appointment',
				'label'         => __( 'Show Book Appointment Block', 'brooklyn-beauty' ),
				'name'          => 'service_show_book_appointment',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'brooklyn-beauty' ),
				'ui_off_text'   => __( 'Hide', 'brooklyn-beauty' ),
			),
			array(
				'key'           => 'field_brooklyn_beauty_single_service_show_faq',
				'label'         => __( 'Show FAQ Block', 'brooklyn-beauty' ),
				'name'          => 'service_show_faq',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'brooklyn-beauty' ),
				'ui_off_text'   => __( 'Hide', 'brooklyn-beauty' ),
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
		'position'              => 'side',
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
				'key'           => 'field_brooklyn_beauty_faq_background_image_mobile',
				'label'         => __( 'Background Image (Mobile)', 'brooklyn-beauty' ),
				'name'          => 'faq_background_image_mobile',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'instructions'  => __( 'Optional. Separate background image for the FAQ block on mobile. If empty, desktop left image or default is used.', 'brooklyn-beauty' ),
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

