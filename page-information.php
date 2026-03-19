<?php
/**
 * Template Name: Information Page
 *
 * Simple informational page template with breadcrumbs and text content.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="bb-info-page section" aria-label="<?php esc_attr_e( 'Information page', 'brooklyn-beauty' ); ?>">
	<div class="bb-container">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<?php
			get_template_part(
				'template-parts/components/breadcrumbs',
				null,
				array(
					'class_name' => 'bb-info-page__breadcrumbs',
					'items'      => array(
						array(
							'label' => __( 'Home', 'brooklyn-beauty' ),
							'url'   => home_url( '/' ),
						),
						array( 'label' => get_the_title() ),
					),
				)
			);
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'bb-info-page__article' ); ?>>
				<h1 class="bb-info-page__title"><?php the_title(); ?></h1>
				<div class="bb-info-page__content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
		?>
	</div>
</section>

<?php
get_footer();
