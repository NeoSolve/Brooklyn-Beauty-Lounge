<?php
/**
 * Single service template.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$service_id          = (int) get_the_ID();
	$service_title       = get_the_title();
	$service_excerpt     = get_the_excerpt();
	$service_archive_url = get_post_type_archive_link( 'service' );
	$service_categories  = get_the_terms( $service_id, 'service_category' );
	$hero_image_url      = (string) get_the_post_thumbnail_url( $service_id, 'large' );
	$hero_image_alt      = $service_title;
	$content             = get_the_content();
	$hero_tagline        = __( 'be fabulous with brooklyn beauty lounge!', 'brooklyn-beauty' );
	$hero_button_text    = __( 'book now', 'brooklyn-beauty' );
	$hero_button_link    = home_url( '/#book' );
	$hero_intro_left     = '';
	$hero_intro_right    = '';

	if ( '' === trim( $service_excerpt ) ) {
		$service_excerpt = wp_trim_words( wp_strip_all_tags( $content ), 28, '...' );
	}

	if ( has_post_thumbnail( $service_id ) ) {
		$thumb_alt = get_post_meta( (int) get_post_thumbnail_id( $service_id ), '_wp_attachment_image_alt', true );
		if ( is_string( $thumb_alt ) && '' !== trim( $thumb_alt ) ) {
			$hero_image_alt = $thumb_alt;
		}
	}

	if ( function_exists( 'get_field' ) ) {
		$acf_hero_tagline = trim( (string) get_field( 'service_hero_tagline', $service_id ) );
		if ( '' !== $acf_hero_tagline ) {
			$hero_tagline = $acf_hero_tagline;
		}

		$acf_hero_button_text = trim( (string) get_field( 'service_hero_button_text', $service_id ) );
		if ( '' !== $acf_hero_button_text ) {
			$hero_button_text = $acf_hero_button_text;
		}

		$acf_hero_button_link = trim( (string) get_field( 'service_hero_button_link', $service_id ) );
		if ( '' !== $acf_hero_button_link ) {
			$hero_button_link = $acf_hero_button_link;
		}

		$acf_hero_intro_left = trim( (string) get_field( 'service_hero_intro_left', $service_id ) );
		if ( '' !== $acf_hero_intro_left ) {
			$hero_intro_left = $acf_hero_intro_left;
		}

		$acf_hero_intro_right = trim( (string) get_field( 'service_hero_intro_right', $service_id ) );
		if ( '' !== $acf_hero_intro_right ) {
			$hero_intro_right = $acf_hero_intro_right;
		}

		$acf_hero_image = get_field( 'service_hero_image', $service_id );
		if ( is_numeric( $acf_hero_image ) ) {
			$acf_hero_image_id = (int) $acf_hero_image;
			$acf_image_url     = (string) wp_get_attachment_image_url( $acf_hero_image_id, 'large' );
			if ( '' !== $acf_image_url ) {
				$hero_image_url = $acf_image_url;
				$acf_alt_text   = get_post_meta( $acf_hero_image_id, '_wp_attachment_image_alt', true );
				if ( is_string( $acf_alt_text ) && '' !== trim( $acf_alt_text ) ) {
					$hero_image_alt = $acf_alt_text;
				}
			}
		} elseif ( is_array( $acf_hero_image ) ) {
			if ( ! empty( $acf_hero_image['url'] ) ) {
				$hero_image_url = (string) $acf_hero_image['url'];
			}
			if ( ! empty( $acf_hero_image['alt'] ) && is_string( $acf_hero_image['alt'] ) ) {
				$hero_image_alt = $acf_hero_image['alt'];
			}
		} elseif ( is_string( $acf_hero_image ) && '' !== trim( $acf_hero_image ) ) {
			$hero_image_url = $acf_hero_image;
		}
	}

	if ( '' === $hero_intro_left && '' === $hero_intro_right ) {
		$content_paragraphs = preg_split( '/\R{2,}/', trim( (string) wp_strip_all_tags( $content ) ) );
		$content_paragraphs = array_values(
			array_filter(
				array_map(
					static function( $paragraph ) {
						$normalized = trim( (string) preg_replace( '/\s+/', ' ', (string) $paragraph ) );
						return '' !== $normalized ? $normalized : '';
					},
					is_array( $content_paragraphs ) ? $content_paragraphs : array()
				)
			)
		);

		if ( count( $content_paragraphs ) >= 2 ) {
			$hero_intro_left  = $content_paragraphs[0];
			$hero_intro_right = $content_paragraphs[1];
		} elseif ( count( $content_paragraphs ) === 1 ) {
			$single_paragraph_words = preg_split( '/\s+/', $content_paragraphs[0] );
			$split_index            = (int) ceil( count( $single_paragraph_words ) / 2 );
			$hero_intro_left        = trim( implode( ' ', array_slice( $single_paragraph_words, 0, $split_index ) ) );
			$hero_intro_right       = trim( implode( ' ', array_slice( $single_paragraph_words, $split_index ) ) );
		} else {
			$hero_intro_left  = $service_excerpt;
			$hero_intro_right = '';
		}
	}

	$related_ids = array();
	$related_query_args = array(
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post__not_in'   => array( $service_id ),
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
	);

	if ( ! is_wp_error( $service_categories ) && ! empty( $service_categories ) ) {
		$related_ids = array_values(
			array_filter(
				array_map(
					static function( $term ) {
						return $term instanceof WP_Term ? (int) $term->term_id : 0;
					},
					$service_categories
				)
			)
		);
	}

	if ( ! empty( $related_ids ) ) {
		$related_query_args['tax_query'] = array(
			array(
				'taxonomy' => 'service_category',
				'field'    => 'term_id',
				'terms'    => $related_ids,
			),
		);
	}

	$related_services = get_posts( $related_query_args );
	?>
	<section class="bb-single-service-hero">
		<div class="bb-container">
			<div class="bb-single-service-hero__top">
				<nav class="bb-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'brooklyn-beauty' ); ?>">
					<a class="bb-breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php esc_html_e( 'Home', 'brooklyn-beauty' ); ?>
					</a>
					<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
					<?php if ( is_string( $service_archive_url ) && '' !== $service_archive_url ) : ?>
						<a class="bb-breadcrumbs__link" href="<?php echo esc_url( $service_archive_url ); ?>">
							<?php esc_html_e( 'Services', 'brooklyn-beauty' ); ?>
						</a>
						<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
					<?php endif; ?>
					<span class="bb-breadcrumbs__current" aria-current="page"><?php echo esc_html( $service_title ); ?></span>
				</nav>
				<p class="bb-single-service-hero__tagline t-decor"><?php echo wp_kses_post( $hero_tagline ); ?></p>
			</div>

			<div class="bb-single-service-hero__grid">
				<div class="bb-single-service-hero__content">
					<h1 class="bb-single-service-hero__title"><?php echo esc_html( $service_title ); ?></h1>
					<a class="btn btn--medium bb-single-service-hero__cta" href="<?php echo esc_url( $hero_button_link ); ?>">
						<?php echo esc_html( $hero_button_text ); ?>
					</a>

					<?php if ( '' !== trim( $hero_intro_left . $hero_intro_right ) ) : ?>
						<div class="bb-single-service-hero__intro">
							<?php if ( '' !== trim( $hero_intro_left ) ) : ?>
								<p class="bb-single-service-hero__intro-text"><?php echo esc_html( $hero_intro_left ); ?></p>
							<?php endif; ?>
							<?php if ( '' !== trim( $hero_intro_right ) ) : ?>
								<p class="bb-single-service-hero__intro-text"><?php echo esc_html( $hero_intro_right ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="bb-single-service-hero__media">
					<?php if ( '' !== $hero_image_url ) : ?>
						<img src="<?php echo esc_url( $hero_image_url ); ?>" alt="<?php echo esc_attr( $hero_image_alt ); ?>" loading="lazy" decoding="async">
					<?php else : ?>
						<div class="bb-single-service-hero__fallback" aria-hidden="true"></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<?php
	get_template_part(
		'template-parts/single-service/reasons',
		null,
		array( 'service_id' => $service_id )
	);
	get_template_part(
		'template-parts/single-service/why-choose',
		null,
		array(
			'service_id'    => $service_id,
			'service_title' => $service_title,
		)
	);
	?>

	<?php if ( ! empty( $related_services ) ) : ?>
		<section class="bb-single-service-related">
			<div class="bb-container">
				<h2 class="bb-single-service-related__title"><?php esc_html_e( 'Related services', 'brooklyn-beauty' ); ?></h2>
				<div class="bb-single-service-related__grid">
					<?php foreach ( $related_services as $related_service ) : ?>
						<?php
						$related_excerpt = get_the_excerpt( $related_service );
						if ( '' === trim( $related_excerpt ) ) {
							$related_excerpt = wp_trim_words( wp_strip_all_tags( $related_service->post_content ), 20, '...' );
						}
						$related_image = (string) get_the_post_thumbnail_url( $related_service, 'large' );
						?>
						<article class="bb-single-service-related__card">
							<div class="bb-single-service-related__card-media"<?php echo '' !== $related_image ? ' style="background-image: url(' . esc_url( $related_image ) . ');"' : ''; ?> aria-hidden="true"></div>
							<div class="bb-single-service-related__card-content">
								<h3 class="bb-single-service-related__card-title"><?php echo esc_html( get_the_title( $related_service ) ); ?></h3>
								<p class="bb-single-service-related__card-text"><?php echo esc_html( $related_excerpt ); ?></p>
								<a class="bb-single-service-related__card-link" href="<?php echo esc_url( (string) get_permalink( $related_service ) ); ?>">
									<?php esc_html_e( 'learn more', 'brooklyn-beauty' ); ?>
								</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
?>
