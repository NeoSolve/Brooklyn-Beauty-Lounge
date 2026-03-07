<?php
/**
 * Script and style enqueue.
 *
 * Front-end and admin asset registration: main CSS/JS, conditional
 * page-specific and post-type-specific styles and scripts, and ACF
 * repeater duplicate admin script.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	$careers_page_css_path = get_template_directory() . '/assets/css/careers-page.css';
	$single_post_css_path = get_template_directory() . '/assets/css/single-post.css';
	$page_404_css_path = get_template_directory() . '/assets/css/404.css';
	$information_page_css_path = get_template_directory() . '/assets/css/information-page.css';
	$js_path               = get_template_directory() . '/assets/js/main.js';
	$services_page_js_path = get_template_directory() . '/assets/js/services-page/main.js';
	$single_service_reasons_js_path = get_template_directory() . '/assets/js/single-service/reasons.js';
	$single_service_explore_js_path = get_template_directory() . '/assets/js/single-service/explore.js';
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

	if ( ( is_page_template( 'page-careers.php' ) || is_page_template( 'page-career-questionnaire.php' ) ) && file_exists( $careers_page_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-careers-page',
			get_template_directory_uri() . '/assets/css/careers-page.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $careers_page_css_path )
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

	if ( is_page_template( 'page-information.php' ) && file_exists( $information_page_css_path ) ) {
		wp_enqueue_style(
			'brooklyn-beauty-information-page',
			get_template_directory_uri() . '/assets/css/information-page.css',
			array( 'brooklyn-beauty-main' ),
			(string) filemtime( $information_page_css_path )
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

		if ( is_singular( 'service' ) && file_exists( $single_service_explore_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-single-service-explore',
				get_template_directory_uri() . '/assets/js/single-service/explore.js',
				array( 'brooklyn-beauty-main' ),
				(string) filemtime( $single_service_explore_js_path ),
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

		if ( is_page_template( 'page-career-questionnaire.php' ) && file_exists( $contacts_form_js_path ) ) {
			wp_enqueue_script(
				'brooklyn-beauty-contacts-form',
				get_template_directory_uri() . '/assets/js/contacts-form.js',
				array(),
				(string) filemtime( $contacts_form_js_path ),
				true
			);
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
