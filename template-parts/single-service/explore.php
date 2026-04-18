<?php
/**
 * Single service "Explore" block — sticky left column + scrollable right sections.
 *
 * @package Brooklyn_Beauty
 */

$service_id = isset( $args['service_id'] ) ? (int) $args['service_id'] : (int) get_the_ID();

$headline = __( 'types of hair services we offer', 'brooklyn-beauty' );
$label    = __( 'explore', 'brooklyn-beauty' );
$label_icon_url = '';

$sections = array(
	array(
		'number' => '001',
		'title' => __( 'haircuts', 'brooklyn-beauty' ),
		'intro' => __( 'Satisfied customers, beautiful looks, and healthy, well-groomed skin are what we always strive for, and we invite you to try it too.', 'brooklyn-beauty' ),
		'image' => '',
		'items' => array(
			array(
				'title'       => __( 'haircuts', 'brooklyn-beauty' ),
				'description' => __( 'A bang cut is the quickest way to refresh your look and style. Using advanced cutting techniques, we ensure your bangs fall naturally and are easy to style at home.', 'brooklyn-beauty' ),
			),
			array(
				'title'       => __( "children's haircut", 'brooklyn-beauty' ),
				'description' => __( "Cutting a child's bangs is necessary not only for beauty, but also for comfort and to preserve eyesight. Our stylists are patient and gentle with kids of all ages.", 'brooklyn-beauty' ),
			),
		),
	),
	array(
		'number' => '002',
		'title' => __( 'hair coloring services', 'brooklyn-beauty' ),
		'intro' => __( 'A new hair color can completely change your look. In recent years, complex coloring techniques have become very popular, and our specialists are ready to help you choose.', 'brooklyn-beauty' ),
		'image' => '',
		'items' => array(
			array(
				'title'       => __( 'single process', 'brooklyn-beauty' ),
				'description' => __( 'Uniform hair color from roots to ends to refresh your look and add healthy shine.', 'brooklyn-beauty' ),
			),
			array(
				'title'       => __( 'balayage / highlights', 'brooklyn-beauty' ),
				'description' => __( 'Dimensional color with smooth transitions and natural brightness for a modern effect.', 'brooklyn-beauty' ),
			),
		),
	),
);

if ( function_exists( 'get_field' ) && $service_id > 0 ) {
	$acf_headline = trim( (string) get_field( 'service_explore_headline', $service_id ) );
	if ( '' !== $acf_headline ) {
		$headline = $acf_headline;
	}

	$acf_label = trim( (string) get_field( 'service_explore_label', $service_id ) );
	if ( '' !== $acf_label ) {
		$label = $acf_label;
	}

	$acf_label_icon = get_field( 'service_explore_label_icon', $service_id );
	if ( is_numeric( $acf_label_icon ) ) {
		$label_icon_url = (string) wp_get_attachment_image_url( (int) $acf_label_icon, 'thumbnail' );
	} elseif ( is_array( $acf_label_icon ) && ! empty( $acf_label_icon['url'] ) ) {
		$label_icon_url = (string) $acf_label_icon['url'];
	}

	$acf_sections = get_field( 'service_explore_sections', $service_id );
	if ( is_array( $acf_sections ) && ! empty( $acf_sections ) ) {
		$normalized_sections = array();

		foreach ( $acf_sections as $section_index => $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}

			$section_title = isset( $section['section_title'] ) ? trim( (string) $section['section_title'] ) : '';
			$section_number = isset( $section['section_number'] ) ? trim( (string) $section['section_number'] ) : '';
			$section_intro = isset( $section['section_intro'] ) ? trim( (string) $section['section_intro'] ) : '';
			if ( '' === $section_intro && isset( $section['section_description'] ) ) {
				$section_intro = trim( (string) $section['section_description'] );
			}
			$section_image = '';

			if ( isset( $section['section_image'] ) ) {
				if ( is_numeric( $section['section_image'] ) ) {
					$section_image = (string) wp_get_attachment_image_url( (int) $section['section_image'], 'large' );
				} elseif ( is_array( $section['section_image'] ) && ! empty( $section['section_image']['url'] ) ) {
					$section_image = (string) $section['section_image']['url'];
				} elseif ( is_string( $section['section_image'] ) ) {
					$section_image = trim( $section['section_image'] );
				}
			}

			if ( '' === $section_number ) {
				$section_number = str_pad( (string) ( $section_index + 1 ), 3, '0', STR_PAD_LEFT );
			}

			$section_items = array();

			if ( isset( $section['items'] ) && is_array( $section['items'] ) ) {
				foreach ( $section['items'] as $item ) {
					if ( ! is_array( $item ) ) {
						continue;
					}

					$item_title = isset( $item['title'] ) ? trim( (string) $item['title'] ) : '';
					$item_text = isset( $item['text'] ) ? trim( (string) $item['text'] ) : '';

					if ( '' === $item_title && '' !== $item_text ) {
						$item_title = $item_text;
					}

					$item_description = isset( $item['description'] ) ? trim( (string) $item['description'] ) : '';

					if ( '' !== $item_title ) {
						$section_items[] = array(
							'title'       => $item_title,
							'description' => $item_description,
						);
					}
				}
			}

			if ( '' !== $section_title && ( '' !== $section_intro || ! empty( $section_items ) ) ) {
				$normalized_sections[] = array(
					'number' => $section_number,
					'title'  => $section_title,
					'intro'  => $section_intro,
					'image'  => $section_image,
					'items'  => $section_items,
				);
			}
		}

		if ( ! empty( $normalized_sections ) ) {
			$sections = $normalized_sections;
		}
	}
}
?>

<section class="bb-service-explore" aria-labelledby="bb-service-explore-heading">
	<div class="bb-container">
		<div class="bb-service-explore__grid">
			<div class="bb-service-explore__left">
				<div class="bb-service-explore__left-inner">
					<div class="bb-service-explore__label-wrap">
						<p class="bb-service-explore__label t-decor"><?php echo esc_html( $label ); ?></p>
						<?php if ( '' !== $label_icon_url ) : ?>
							<img class="bb-service-explore__label-icon" src="<?php echo esc_url( $label_icon_url ); ?>" alt="" loading="lazy" decoding="async">
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="bb-service-explore__content">
				<h2 id="bb-service-explore-heading" class="bb-service-explore__headline">
					<?php echo wp_kses( $headline, array( 'br' => array() ) ); ?>
				</h2>

				<div class="bb-service-explore__right" role="region" aria-label="<?php esc_attr_e( 'Service sections', 'brooklyn-beauty' ); ?>">
					<?php foreach ( $sections as $section_index => $section ) : ?>
						<?php $list_id = 'bb-service-explore-list-' . ( $section_index + 1 ); ?>
						<section class="bb-service-explore__section">
							<div class="bb-service-explore__section-head">
								<div class="bb-service-explore__section-copy">
									<div class="bb-service-explore__section-title-wrap">
										<h3 class="bb-service-explore__section-title">
											<?php echo wp_kses( $section['title'], array( 'br' => array() ) ); ?>
										</h3><span class="bb-service-explore__section-number bb-service-explore__section-number--mobile"><?php echo esc_html( $section['number'] ); ?></span>
										<span class="bb-service-explore__section-number bb-service-explore__section-number--desktop" aria-hidden="true"><?php echo esc_html( $section['number'] ); ?></span>
									</div>
									<?php if ( '' !== trim( (string) $section['intro'] ) ) : ?>
										<p class="bb-service-explore__section-intro"><?php echo esc_html( $section['intro'] ); ?></p>
									<?php endif; ?>

									<?php if ( ! empty( $section['items'] ) ) : ?>
										<ul id="<?php echo esc_attr( $list_id ); ?>" class="bb-service-explore__list">
											<?php foreach ( $section['items'] as $item_data ) : ?>
												<li class="bb-service-explore__list-item">
													<h4 class="bb-service-explore__item-title"><?php echo esc_html( $item_data['title'] ); ?></h4>
													<?php if ( '' !== trim( (string) $item_data['description'] ) ) : ?>
														<div class="bb-service-explore__item-description"><?php echo wp_kses_post( $item_data['description'] ); ?></div>
													<?php endif; ?>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>

								<div class="bb-service-explore__section-media">
									<?php if ( '' !== $section['image'] ) : ?>
										<img src="<?php echo esc_url( $section['image'] ); ?>" alt="<?php echo esc_attr( $section['title'] ); ?>" loading="lazy" decoding="async">
									<?php else : ?>
										<div class="bb-service-explore__section-media-placeholder" aria-hidden="true"></div>
									<?php endif; ?>
								</div>

								<?php if ( ! empty( $section['items'] ) ) : ?>
									<button
										class="bb-service-explore__toggle"
										type="button"
										aria-expanded="true"
										aria-controls="<?php echo esc_attr( $list_id ); ?>"
										data-show-more="<?php echo esc_attr__( 'show more', 'brooklyn-beauty' ); ?>"
										data-show-less="<?php echo esc_attr__( 'show less', 'brooklyn-beauty' ); ?>"
									>
										<span class="bb-service-explore__toggle-label"><?php esc_html_e( 'show less', 'brooklyn-beauty' ); ?></span>
									</button>
								<?php endif; ?>
							</div>

						</section>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
