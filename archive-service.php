<?php
/**
 * Services archive template.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$archive_title       = post_type_archive_title( '', false );
$archive_description = get_the_archive_description();
?>

<section class="bb-services-archive-hero">
	<div class="bb-container">
		<nav class="bb-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'brooklyn-beauty' ); ?>">
			<a class="bb-breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Home', 'brooklyn-beauty' ); ?>
			</a>
			<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
			<span class="bb-breadcrumbs__current" aria-current="page"><?php echo esc_html( $archive_title ); ?></span>
		</nav>

		<h1 class="bb-services-archive-hero__title"><?php echo esc_html( $archive_title ); ?></h1>
		<?php if ( '' !== trim( wp_strip_all_tags( $archive_description ) ) ) : ?>
			<div class="bb-services-archive-hero__description"><?php echo wp_kses_post( $archive_description ); ?></div>
		<?php endif; ?>
	</div>
</section>

<section class="bb-services-archive-content">
	<div class="bb-container">
		<?php if ( have_posts() ) : ?>
			<div class="bb-services-archive-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$card_excerpt = get_the_excerpt();
					if ( '' === trim( $card_excerpt ) ) {
						$card_excerpt = wp_trim_words( wp_strip_all_tags( get_the_content() ), 24, '...' );
					}
					$card_image = (string) get_the_post_thumbnail_url( get_the_ID(), 'large' );
					?>
					<article class="bb-services-archive-card">
						<div class="bb-services-archive-card__media"<?php echo '' !== $card_image ? ' style="background-image: url(' . esc_url( $card_image ) . ');"' : ''; ?> aria-hidden="true"></div>
						<div class="bb-services-archive-card__content">
							<h2 class="bb-services-archive-card__title"><?php the_title(); ?></h2>
							<p class="bb-services-archive-card__text"><?php echo esc_html( $card_excerpt ); ?></p>
							<a class="bb-services-archive-card__link" href="<?php the_permalink(); ?>">
								<?php esc_html_e( 'learn more', 'brooklyn-beauty' ); ?>
							</a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No services found.', 'brooklyn-beauty' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
?>
