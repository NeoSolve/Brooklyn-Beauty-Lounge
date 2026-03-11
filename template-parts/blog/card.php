<?php
/**
 * Blog card component.
 *
 * @package Brooklyn_Beauty
 */

$post_id            = isset( $args['post_id'] ) ? (int) $args['post_id'] : 0;
$card_excerpt       = isset( $args['excerpt'] ) ? (string) $args['excerpt'] : '';
$reading_time_label = isset( $args['reading_time_label'] ) ? (string) $args['reading_time_label'] : '';
$fallback_media     = isset( $args['fallback_media_name'] ) ? sanitize_html_class( (string) $args['fallback_media_name'] ) : 'pedicure';
$media_image        = isset( $args['media_image'] ) ? (string) $args['media_image'] : '';

if ( $post_id <= 0 ) {
	return;
}

$card_title = get_the_title( $post_id );
$card_link  = (string) get_permalink( $post_id );
$card_date  = (string) get_the_date( 'm/d/Y', $post_id );
?>
<article class="bb-blog-card">
	<a class="bb-blog-card__media bb-blog-card__media--<?php echo esc_attr( $fallback_media ); ?>" href="<?php echo esc_url( $card_link ); ?>" aria-label="<?php echo esc_attr( $card_title ); ?>"<?php echo '' !== $media_image ? ' style="background-image: url(' . esc_url( $media_image ) . ');"' : ''; ?>></a>
	<div class="bb-blog-card__content">
		<div class="bb-blog-card__meta">
			<span><?php echo esc_html( $card_date ); ?></span>
			<?php if ( '' !== trim( $reading_time_label ) ) : ?>
				<span><?php echo esc_html( $reading_time_label ); ?></span>
			<?php endif; ?>
		</div>

		<h3 class="bb-blog-card__title">
			<a href="<?php echo esc_url( $card_link ); ?>"><?php echo esc_html( $card_title ); ?></a>
		</h3>

		<?php if ( '' !== trim( $card_excerpt ) ) : ?>
			<p class="bb-blog-card__excerpt"><?php echo esc_html( $card_excerpt ); ?></p>
		<?php endif; ?>

		<a class="bb-blog-card__link" href="<?php echo esc_url( $card_link ); ?>">
			<?php esc_html_e( 'read more', 'brooklyn-beauty' ); ?>
		</a>
	</div>
</article>
