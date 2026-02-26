<?php
/**
 * Other articles block for single post page.
 *
 * @package Brooklyn_Beauty
 */

$current_post_id = (int) get_the_ID();
if ( $current_post_id <= 0 ) {
	return;
}

$section_title = __( 'read this next:', 'brooklyn-beauty' );

$query_args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'post__not_in'   => array( $current_post_id ),
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$categories = get_the_category( $current_post_id );
if ( ! empty( $categories ) ) {
	$category_ids = array_map(
		static function ( $cat ) {
			return (int) $cat->term_id;
		},
		$categories
	);
	$query_args['category__in'] = $category_ids;
	$query_args['orderby']      = array(
		'date' => 'DESC',
	);
}

$other_posts_query = new WP_Query( $query_args );

if ( ! $other_posts_query->have_posts() ) {
	wp_reset_postdata();
	return;
}

$fallback_media_classes = array( 'pedicure', 'brows', 'makeup' );
?>
<section class="bb-other-articles" id="other-articles" data-other-articles>
	<div class="bb-container">
		<div class="bb-other-articles__header">
			<p class="bb-other-articles__label" aria-hidden="true"><?php esc_html_e( 'look', 'brooklyn-beauty' ); ?></p>
			<div class="bb-other-articles__header-right">
				<h2 class="bb-other-articles__title"><?php echo esc_html( $section_title ); ?></h2>
				<div class="bb-other-articles__controls" aria-label="<?php esc_attr_e( 'Other articles carousel controls', 'brooklyn-beauty' ); ?>">
					<button class="bb-other-articles__arrow bb-other-articles__arrow--prev" type="button" data-other-articles-nav="prev" aria-label="<?php esc_attr_e( 'Previous articles', 'brooklyn-beauty' ); ?>"></button>
					<button class="bb-other-articles__arrow bb-other-articles__arrow--next" type="button" data-other-articles-nav="next" aria-label="<?php esc_attr_e( 'Next articles', 'brooklyn-beauty' ); ?>"></button>
				</div>
			</div>
		</div>

		<div class="bb-other-articles__track" data-other-articles-track>
			<?php
			$index = 0;
			foreach ( $other_posts_query->posts as $blog_post ) :
				$card_excerpt = get_the_excerpt( $blog_post );
				if ( '' === trim( $card_excerpt ) ) {
					$card_excerpt = wp_trim_words( wp_strip_all_tags( $blog_post->post_content ), 26, '...' );
				}

				$card_image          = (string) get_the_post_thumbnail_url( $blog_post, 'large' );
				$fallback_media_name  = $fallback_media_classes[ $index % count( $fallback_media_classes ) ];
				$reading_time_label  = brooklyn_beauty_get_post_reading_time_label( (int) $blog_post->ID );

				get_template_part(
					'template-parts/blog/card',
					null,
					array(
						'post_id'             => (int) $blog_post->ID,
						'excerpt'             => $card_excerpt,
						'reading_time_label'  => $reading_time_label,
						'fallback_media_name' => $fallback_media_name,
						'media_image'         => $card_image,
					)
				);
				++$index;
			endforeach;
			?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
