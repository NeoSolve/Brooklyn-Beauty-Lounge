<?php
/**
 * 404 template
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chandelier_rel_path = '/assets/images/404-image.png';
$chandelier_path     = get_theme_file_path( $chandelier_rel_path );
$chandelier_url      = file_exists( $chandelier_path ) ? get_theme_file_uri( $chandelier_rel_path ) : '';

get_header();
?>

<section class="bb-404 section" aria-labelledby="bb-404-title">
	<div class="bb-container bb-404__inner">
		<div class="bb-404__visual" aria-hidden="true">
			<div class="bb-404__code">404</div>
			<?php if ( '' !== $chandelier_url ) : ?>
				<img class="bb-404__chandelier" src="<?php echo esc_url( $chandelier_url ); ?>" alt="" loading="eager" decoding="async">
			<?php endif; ?>
		</div>

		<div class="bb-404__content">
			<h1 id="bb-404-title" class="bb-404__title">
				<?php esc_html_e( "oops, we can't find this page", 'brooklyn-beauty' ); ?>
				<span class="bb-404__sorry"><?php esc_html_e( 'sorry :(', 'brooklyn-beauty' ); ?></span>
			</h1>

			<a class="btn btn--small bb-404__button" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'return to homepage', 'brooklyn-beauty' ); ?>
			</a>
		</div>
	</div>
</section>

<?php
get_footer();
