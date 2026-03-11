<?php
/**
 * Our Work block.
 *
 * @package Brooklyn_Beauty
 */
$section_title       = '';
$section_text        = '';
$section_label       = '';
$section_button_text = '';
$section_button_link = '';
$work_items          = array();

if ( function_exists( 'get_field' ) ) {
	$front_page_id = (int) get_option( 'page_on_front' );

	if ( $front_page_id <= 0 ) {
		$front_page_id = (int) get_queried_object_id();
	}

	$acf_section_title = (string) get_field( 'our_work_title', $front_page_id );
	if ( '' !== trim( $acf_section_title ) ) {
		$section_title = $acf_section_title;
	}

	$acf_section_text = (string) get_field( 'our_work_text', $front_page_id );
	if ( '' !== trim( $acf_section_text ) ) {
		$section_text = $acf_section_text;
	}

	$acf_section_label = (string) get_field( 'our_work_label', $front_page_id );
	if ( '' !== trim( $acf_section_label ) ) {
		$section_label = $acf_section_label;
	}

	$acf_button_text = (string) get_field( 'our_work_button_text', $front_page_id );
	if ( '' !== trim( $acf_button_text ) ) {
		$section_button_text = $acf_button_text;
	}

	$acf_button_link = (string) get_field( 'our_work_button_link', $front_page_id );
	if ( '' !== trim( $acf_button_link ) ) {
		$section_button_link = $acf_button_link;
	}

	$acf_work_items = get_field( 'our_work_items', $front_page_id );
	if ( is_array( $acf_work_items ) ) {
		foreach ( $acf_work_items as $acf_work_item ) {
			if ( ! is_array( $acf_work_item ) ) {
				continue;
			}

			$preview_id = 0;
			$video_url  = '';

			if ( isset( $acf_work_item['preview_image'] ) ) {
				$preview_field = $acf_work_item['preview_image'];

				if ( is_numeric( $preview_field ) ) {
					$preview_id = (int) $preview_field;
				} elseif ( is_array( $preview_field ) && ! empty( $preview_field['ID'] ) ) {
					$preview_id = (int) $preview_field['ID'];
				}
			}

			if ( isset( $acf_work_item['video_file'] ) ) {
				$video_field = $acf_work_item['video_file'];

				if ( is_numeric( $video_field ) ) {
					$video_url = (string) wp_get_attachment_url( (int) $video_field );
				} elseif ( is_array( $video_field ) && ! empty( $video_field['url'] ) ) {
					$video_url = (string) $video_field['url'];
				} elseif ( is_string( $video_field ) ) {
					$video_url = $video_field;
				}
			}

			if ( $preview_id <= 0 ) {
				continue;
			}

			$work_items[] = array(
				'preview_id' => $preview_id,
				'video_url'  => $video_url,
			);
		}
	}
}
?>
<?php $play_icon_url = get_template_directory_uri() . '/assets/images/Play Button Container.svg'; ?>
<section class="bb-our-work-section" id="our-work" data-play-icon-url="<?php echo esc_url( $play_icon_url ); ?>">
	<div class="bb-container">
		<div class="bb-our-work__header">
			<div class="bb-our-work__intro">
				<?php if ( $section_label ) : ?>
					<p class="bb-our-work__label-mobile"><?php echo esc_html( $section_label ); ?></p>
				<?php endif; ?>
				<?php if ( $section_title ) : ?>
					<h2 class="bb-our-work__title"><?php echo esc_html( $section_title ); ?></h2>
				<?php endif; ?>
				<?php if ( $section_text ) : ?>
					<p class="bb-our-work__text">
						<?php echo esc_html( $section_text ); ?>
					</p>
				<?php endif; ?>
				<?php if ( $section_button_text && $section_button_link ) : ?>
					<a class="btn btn--medium bb-our-work__button" href="<?php echo esc_url( $section_button_link ); ?>">
						<?php echo esc_html( $section_button_text ); ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="bb-our-work__controls" aria-label="<?php esc_attr_e( 'Our work slider controls', 'brooklyn-beauty' ); ?>">
				<button class="bb-our-work__arrow bb-our-work__arrow--prev" type="button" data-work-nav="prev" aria-label="<?php esc_attr_e( 'Previous work items', 'brooklyn-beauty' ); ?>"></button>
				<button class="bb-our-work__arrow bb-our-work__arrow--next" type="button" data-work-nav="next" aria-label="<?php esc_attr_e( 'Next work items', 'brooklyn-beauty' ); ?>"></button>
			</div>
		</div>

		<div class="bb-our-work__content">
			<?php if ( $section_label ) : ?>
				<p class="bb-our-work__label"><?php echo esc_html( $section_label ); ?></p>
			<?php endif; ?>
			<div class="bb-work" data-work-track>
				<?php
				$work_items_slice = ! empty( $work_items ) ? array_slice( $work_items, 0, 8 ) : array();
				if ( ! empty( $work_items_slice ) ) :
					$work_items_count = count( $work_items_slice );
					$work_index       = 0;
					foreach ( $work_items_slice as $work_item ) :
						$work_index++;
						$is_last = $work_index === $work_items_count;
						$img = wp_get_attachment_image(
							$work_item['preview_id'],
							'large',
							false,
							array(
								'class'   => 'bb-work__image',
								'loading' => 'lazy',
							)
						);
						if ( $img ) :
							?>
							<article
								class="bb-work__item<?php echo $work_item['video_url'] ? ' bb-work__item--has-video' : ''; ?><?php echo $is_last ? ' bb-work__item--last' : ''; ?>"
								<?php echo $work_item['video_url'] ? ' data-video-url="' . esc_url( $work_item['video_url'] ) . '"' : ''; ?>
							>
								<?php echo $img; ?>
								<?php if ( $work_item['video_url'] ) : ?>
									<button class="bb-work__play-btn" type="button" aria-label="<?php esc_attr_e( 'Play video', 'brooklyn-beauty' ); ?>">
										<img class="bb-work__play-icon" src="<?php echo esc_url( $play_icon_url ); ?>" width="80" height="80" alt="" aria-hidden="true" loading="lazy" decoding="async">
									</button>
								<?php endif; ?>
							</article>
							<?php
						endif;
					endforeach;
				endif;
				?>
			</div>
		</div>
	</div>
</section>
