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
			<nav class="bb-breadcrumbs bb-info-page__breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'brooklyn-beauty' ); ?>">
				<a class="bb-breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Home', 'brooklyn-beauty' ); ?>
				</a>
				<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
				<span class="bb-breadcrumbs__current" aria-current="page"><?php the_title(); ?></span>
			</nav>

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
