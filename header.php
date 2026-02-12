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
		<div class="bb-header__meta">
			<a class="bb-header__meta-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Brooklyn, New York', 'brooklyn-beauty' ); ?></a>
			<a class="bb-header__meta-link" href="tel:+17184193232"><?php esc_html_e( '(718) 419-3232', 'brooklyn-beauty' ); ?></a>
		</div>
		<nav class="bb-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'brooklyn-beauty' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'     => false,
				'menu_class'    => 'bb-header__menu',
				'fallback_cb'   => function() {
					echo '<ul><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'brooklyn-beauty' ) . '</a></li></ul>';
				},
				'items_wrap'    => '<ul class="%2$s">%3$s</ul>',
			) );
			?>
		</nav>
		<div class="bb-header__social" aria-label="<?php esc_attr_e( 'Social links', 'brooklyn-beauty' ); ?>">
			<a class="bb-header__social-link" href="#" aria-label="<?php esc_attr_e( 'Instagram', 'brooklyn-beauty' ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<rect x="3.5" y="3.5" width="17" height="17" rx="5"></rect>
					<circle cx="12" cy="12" r="4.1"></circle>
					<circle cx="17.4" cy="6.7" r="1.1"></circle>
				</svg>
			</a>
			<a class="bb-header__social-link" href="#" aria-label="<?php esc_attr_e( 'Facebook', 'brooklyn-beauty' ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path d="M14.2 8.3h2V5h-2.5c-2.8 0-4.4 1.7-4.4 4.7v2H7v3.2h2.3V19h3.4v-4.1h2.8l.4-3.2h-3.2V9.9c0-1 .4-1.6 1.5-1.6z"></path>
				</svg>
			</a>
			<a class="bb-header__social-link" href="#" aria-label="<?php esc_attr_e( 'Google', 'brooklyn-beauty' ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path d="M20.2 12.3c0-.6-.1-1-.2-1.5H12v2.8h4.6c-.2 1.2-.9 2.1-1.9 2.8v2.3h3.1c1.8-1.7 2.8-4.2 2.4-6.4z"></path>
					<path d="M12 21c2.3 0 4.2-.8 5.6-2.1l-3.1-2.3c-.9.6-1.9.9-3 .9-2.3 0-4.2-1.5-4.9-3.6H3.3v2.4C4.8 19 8.1 21 12 21z"></path>
					<path d="M6.6 13.9a5.3 5.3 0 0 1 0-3.8V7.7H3.3A9 9 0 0 0 3.3 16l3.3-2.1z"></path>
					<path d="M12 6.5c1.2 0 2.4.4 3.3 1.3l2.5-2.5A8.7 8.7 0 0 0 3.3 7.7l3.3 2.4c.7-2.1 2.6-3.6 5.4-3.6z"></path>
				</svg>
			</a>
			<a class="bb-header__social-link" href="#" aria-label="<?php esc_attr_e( 'TikTok', 'brooklyn-beauty' ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path d="M16.9 4h-3v9.2a2.6 2.6 0 1 1-2.6-2.7c.3 0 .6 0 .9.1V7.7l-.9-.1A5.6 5.6 0 1 0 16.9 13V9.5a6 6 0 0 0 3.8 1.4V8a3.3 3.3 0 0 1-3.8-4z"></path>
				</svg>
			</a>
		</div>
		<a class="btn btn--small" href="#book"><?php esc_html_e( 'book now', 'brooklyn-beauty' ); ?></a>
	</div>
</header>

<main id="content" class="bb-main">
