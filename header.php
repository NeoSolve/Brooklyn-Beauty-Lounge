<?php
/**
 * Header template
 *
 * @package Brooklyn_Beauty
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="bb-header">
	<div class="bb-container bb-header__inner">
		<div class="bb-header__logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</div>
		<nav class="bb-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'brooklyn-beauty' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'     => false,
				'menu_class'    => '',
				'fallback_cb'   => function() {
					echo '<ul><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'brooklyn-beauty' ) . '</a></li></ul>';
				},
				'items_wrap'    => '<ul>%3$s</ul>',
			) );
			?>
		</nav>
	</div>
</header>

<main id="content" class="bb-main">
