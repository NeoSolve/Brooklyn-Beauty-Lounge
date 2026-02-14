<?php
/**
 * Hero banner block
 *
 * @package Brooklyn_Beauty
 */
$hero_title   = get_theme_mod( 'bb_hero_title', __( 'be beautiful. be you.', 'brooklyn-beauty' ) );
$hero_subtitle = get_theme_mod( 'bb_hero_subtitle', __( 'Your place for beauty and relaxation', 'brooklyn-beauty' ) );
$hero_description = get_theme_mod( 'bb_hero_description', __( 'Welcome to Brooklyn Beauty Lounge, where personal expertise meets artistry. Our team of skilled stylists, colorists, nail artists, and estheticians create personalized beauty experiences developed just for you.', 'brooklyn-beauty' ) );
$hero_description_2 = get_theme_mod( 'bb_hero_description_2', __( 'From transformative hair services to flawless nails and skin care services, every detail is crafted to make you look and feel your best.', 'brooklyn-beauty' ) );
$hero_btn_text = get_theme_mod( 'bb_hero_btn', __( 'make an appointment', 'brooklyn-beauty' ) );
$hero_btn_url  = get_theme_mod( 'bb_hero_btn_url', '#book' );
$hero_image    = get_theme_mod( 'bb_hero_image', '' );
$hero_default_image = get_template_directory_uri() . '/assets/images/main-banner.jpg';
$hero_banner_image  = $hero_image ? $hero_image : $hero_default_image;
$hero_style         = ' style="background-image: url(' . esc_url( $hero_banner_image ) . ');"';
?>
<section class="bb-hero"<?php echo $hero_style; ?>>
	<div class="bb-hero__overlay" aria-hidden="true"></div>
	
	<div class="bb-hero__inner bb-container">
		<div class="bb-hero__content">
			<h1 class="bb-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
		<div class="bb-hero__body">
			<?php if ( $hero_description ) : ?>
				<p class="bb-hero__description"><?php echo esc_html( $hero_description ); ?></p>
			<?php endif; ?>
			<?php if ( $hero_description_2 ) : ?>
				<p class="bb-hero__description"><?php echo esc_html( $hero_description_2 ); ?></p>
			<?php endif; ?>
			<?php if ( $hero_btn_text ) : ?>
				<a href="<?php echo esc_url( $hero_btn_url ); ?>" class="btn btn--medium"><?php echo esc_html( $hero_btn_text ); ?></a>
			<?php endif; ?>
		</div>
		</div>
	</div>
</section>
