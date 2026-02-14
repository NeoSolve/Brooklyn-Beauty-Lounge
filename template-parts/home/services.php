<?php
/**
 * Services block
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_services_title', __( 'brooklyn beauty lounge services', 'brooklyn-beauty' ) );
$services      = array();
$fallback_services = array(
	array(
		'title'         => __( 'pedicure', 'brooklyn-beauty' ),
		'text'          => __( 'Relax and refresh your legs with our signature pedicure services. Enjoy flawless polish and soft, beautiful feet.', 'brooklyn-beauty' ),
		'category_slugs'=> array( 'nails' ),
		'media_class'   => 'bb-service-card__media--pedicure',
		'media_image'   => '',
		'visit_url'     => '#',
		'more_url'      => '#',
	),
	array(
		'title'         => __( 'eyebrows and lashes', 'brooklyn-beauty' ),
		'text'          => __( 'Define your gaze with precision brow shaping, tinting, lamination, and lash lifting or extensions.', 'brooklyn-beauty' ),
		'category_slugs'=> array( 'brows-lashes' ),
		'media_class'   => 'bb-service-card__media--brows',
		'media_image'   => '',
		'visit_url'     => '#',
		'more_url'      => '#',
	),
	array(
		'title'         => __( 'makeup services', 'brooklyn-beauty' ),
		'text'          => __( 'Having a night out, a photoshoot, or an event? Our talented makeup artists enhance your natural beauty with flawless application and long-lasting results.', 'brooklyn-beauty' ),
		'category_slugs'=> array( 'face' ),
		'media_class'   => 'bb-service-card__media--makeup',
		'media_image'   => '',
		'visit_url'     => '#',
		'more_url'      => '#',
	),
);

$service_categories_fallback = array(
	array(
		'slug'  => 'all-services',
		'label' => __( 'all services', 'brooklyn-beauty' ),
	),
	array(
		'slug'  => 'hair',
		'label' => __( 'hair', 'brooklyn-beauty' ),
	),
	array(
		'slug'  => 'nails',
		'label' => __( 'nails', 'brooklyn-beauty' ),
	),
	array(
		'slug'  => 'face',
		'label' => __( 'face', 'brooklyn-beauty' ),
	),
	array(
		'slug'  => 'brows-lashes',
		'label' => __( 'brows & lashes', 'brooklyn-beauty' ),
	),
	array(
		'slug'  => 'hair-removal',
		'label' => __( 'hair removal', 'brooklyn-beauty' ),
	),
	array(
		'slug'  => 'tanning-whitening',
		'label' => __( 'tanning & whitening', 'brooklyn-beauty' ),
	),
	array(
		'slug'  => 'other',
		'label' => __( 'other', 'brooklyn-beauty' ),
	),
);

$service_categories = array(
	array(
		'slug'  => 'all-services',
		'label' => __( 'all services', 'brooklyn-beauty' ),
	),
);

$selected_service_category_ids = array();

if ( function_exists( 'get_field' ) ) {
	$front_page_id = (int) get_option( 'page_on_front' );

	if ( $front_page_id <= 0 ) {
		$front_page_id = (int) get_queried_object_id();
	}

	$acf_selected_categories = get_field( 'services_tab_categories', $front_page_id );
	if ( is_array( $acf_selected_categories ) ) {
		$selected_service_category_ids = array_values( array_filter( array_map( 'intval', $acf_selected_categories ) ) );
	}
}

if ( ! empty( $selected_service_category_ids ) ) {
	$service_terms = get_terms(
		array(
			'taxonomy'   => 'service_category',
			'hide_empty' => false,
			'include'    => $selected_service_category_ids,
			'orderby'    => 'include',
		)
	);
} else {
	$service_terms = get_terms(
		array(
			'taxonomy'   => 'service_category',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
}

if ( ! is_wp_error( $service_terms ) && ! empty( $service_terms ) ) {
	foreach ( $service_terms as $service_term ) {
		if ( 'all-services' === $service_term->slug ) {
			continue;
		}

		$service_categories[] = array(
			'slug'  => $service_term->slug,
			'label' => $service_term->name,
		);
	}
} else {
	foreach ( $service_categories_fallback as $fallback_category ) {
		if ( 'all-services' === $fallback_category['slug'] ) {
			continue;
		}

		$service_categories[] = $fallback_category;
	}
}

$service_posts = get_posts(
	array(
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
	)
);

if ( ! empty( $service_posts ) ) {
	$fallback_media_classes = array(
		'bb-service-card__media--pedicure',
		'bb-service-card__media--brows',
		'bb-service-card__media--makeup',
	);

	foreach ( $service_posts as $index => $service_post ) {
		$service_term_slugs = wp_get_post_terms( $service_post->ID, 'service_category', array( 'fields' => 'slugs' ) );
		if ( is_wp_error( $service_term_slugs ) ) {
			$service_term_slugs = array();
		}

		$category_slugs = array_values( array_unique( $service_term_slugs ) );
		$service_text   = get_the_excerpt( $service_post );

		if ( '' === trim( $service_text ) ) {
			$service_text = wp_trim_words( wp_strip_all_tags( $service_post->post_content ), 26, '...' );
		}

		$services[] = array(
			'title'          => get_the_title( $service_post ),
			'text'           => $service_text,
			'category_slugs' => $category_slugs,
			'media_class'    => $fallback_media_classes[ $index % count( $fallback_media_classes ) ],
			'media_image'    => (string) get_the_post_thumbnail_url( $service_post, 'large' ),
			'visit_url'      => '#book',
			'more_url'       => (string) get_permalink( $service_post ),
		);
	}
} else {
	$services = $fallback_services;
}
?>
<section class="bb-services-section" id="services">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<div class="bb-services-section__heading">
				<h2 class="bb-services-section__title"><?php echo esc_html( $section_title ); ?></h2>
				<p class="bb-services-section__label"><?php esc_html_e( 'choose', 'brooklyn-beauty' ); ?></p>
			</div>
		<?php endif; ?>

		<ul class="bb-services-filters" aria-label="<?php esc_attr_e( 'Service categories', 'brooklyn-beauty' ); ?>">
			<?php foreach ( $service_categories as $index => $service_category ) : ?>
				<li class="bb-services-filters__item<?php echo 0 === $index ? ' is-active' : ''; ?>">
					<button type="button" class="bb-services-filters__button" data-filter="<?php echo esc_attr( $service_category['slug'] ); ?>" aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>">
						<?php echo esc_html( $service_category['label'] ); ?>
					</button>
				</li>
			<?php endforeach; ?>
			<span class="bb-services-filters__indicator" aria-hidden="true"></span>
		</ul>

		<div class="bb-services-cards">
			<?php foreach ( $services as $service ) : ?>
				<article class="bb-service-card" data-categories="<?php echo esc_attr( implode( ' ', $service['category_slugs'] ) ); ?>">
					<div class="bb-service-card__media <?php echo esc_attr( $service['media_class'] ); ?>"<?php echo '' !== $service['media_image'] ? ' style="background-image: url(' . esc_url( $service['media_image'] ) . ');"' : ''; ?> aria-hidden="true"></div>
					<div class="bb-service-card__content">
						<h3 class="bb-service-card__title"><?php echo esc_html( $service['title'] ); ?></h3>
						<p class="bb-service-card__text"><?php echo esc_html( $service['text'] ); ?></p>
						<div class="bb-service-card__actions">
							<a class="btn btn--medium bb-service-card__visit-btn" href="<?php echo esc_url( $service['visit_url'] ); ?>">
								<?php esc_html_e( 'book a visit', 'brooklyn-beauty' ); ?>
							</a>
							<a class="bb-service-card__more-link" href="<?php echo esc_url( $service['more_url'] ); ?>">
								<?php esc_html_e( 'learn more', 'brooklyn-beauty' ); ?>
								<span aria-hidden="true">&#8594;</span>
							</a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
