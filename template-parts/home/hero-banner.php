<?php
/**
 * Hero banner block
 *
 * @package Brooklyn_Beauty
 */
$hero_title   = get_theme_mod( 'bb_hero_title', __( 'Brooklyn Beauty Lounge', 'brooklyn-beauty' ) );
$hero_subtitle = get_theme_mod( 'bb_hero_subtitle', __( 'Your place for beauty and relaxation', 'brooklyn-beauty' ) );
$hero_btn_text = get_theme_mod( 'bb_hero_btn', __( 'Book an appointment', 'brooklyn-beauty' ) );
$hero_btn_url  = get_theme_mod( 'bb_hero_btn_url', '#book' );
$hero_image    = get_theme_mod( 'bb_hero_image', '' );
$hero_style    = $hero_image ? ' style="background-image: url(' . esc_url( $hero_image ) . ');"' : '';
?>
<section class="bb-hero"<?php echo $hero_style; ?>>
	<div class="bb-hero__overlay" aria-hidden="true"></div>
	<div class="bb-hero__content">
		<h1 class="bb-hero__title t-h1-alt"><?php echo esc_html( $hero_title ); ?></h1>
		<?php if ( $hero_subtitle ) : ?>
			<p class="bb-hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
		<?php endif; ?>
		<?php if ( $hero_btn_text ) : ?>
			<a href="<?php echo esc_url( $hero_btn_url ); ?>" class="bb-hero__cta"><?php echo esc_html( $hero_btn_text ); ?></a>
		<?php endif; ?>
	</div>
</section>
