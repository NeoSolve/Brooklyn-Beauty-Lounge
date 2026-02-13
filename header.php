<?php
/**
 * Header template
 *
 * @package Brooklyn_Beauty
 */

$header_meta_items = array(
	array(
		'label' => __( 'Brooklyn, New York', 'brooklyn-beauty' ),
		'link'  => home_url( '/' ),
	),
	array(
		'label' => __( '(718) 419-3232', 'brooklyn-beauty' ),
		'link'  => 'tel:+17184193232',
	),
);

$header_social_items = array(
	array(
		'icon' => 'instagram',
		'link' => '#',
	),
	array(
		'icon' => 'facebook',
		'link' => '#',
	),
	array(
		'icon' => 'google',
		'link' => '#',
	),
	array(
		'icon' => 'tiktok',
		'link' => '#',
	),
);

$header_button_text = __( 'book now', 'brooklyn-beauty' );
$header_button_link = '#book';

if ( function_exists( 'get_field' ) ) {
	$acf_meta_items = get_field( 'header_meta_items', 'option' );
	if ( ! empty( $acf_meta_items ) && is_array( $acf_meta_items ) ) {
		$header_meta_items = array_values(
			array_filter(
				$acf_meta_items,
				static function( $item ) {
					return is_array( $item ) && ! empty( $item['label'] );
				}
			)
		);
	}

	$acf_social_items = get_field( 'header_social_items', 'option' );
	if ( ! empty( $acf_social_items ) && is_array( $acf_social_items ) ) {
		$header_social_items = array_values(
			array_filter(
				$acf_social_items,
				static function( $item ) {
					$has_image = ! empty( $item['icon_image'] );
					$has_icon  = ! empty( $item['icon'] );

					return is_array( $item ) && ( $has_image || $has_icon );
				}
			)
		);
	}

	$acf_button_text = get_field( 'header_button_text', 'option' );
	if ( ! empty( $acf_button_text ) ) {
		$header_button_text = $acf_button_text;
	}

	$acf_button_link = get_field( 'header_button_link', 'option' );
	if ( ! empty( $acf_button_link ) ) {
		$header_button_link = $acf_button_link;
	}
}
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
		<div class="bb-header__block bb-header__block--left">
			<div class="bb-header__logo">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
				<?php endif; ?>
			</div>
			<div class="bb-header__meta">
				<?php foreach ( $header_meta_items as $meta_item ) : ?>
					<?php
					$meta_label = isset( $meta_item['label'] ) ? $meta_item['label'] : '';
					$meta_link  = isset( $meta_item['link'] ) && $meta_item['link'] ? $meta_item['link'] : '#';
					?>
					<?php if ( $meta_label ) : ?>
						<a class="bb-header__meta-link" href="<?php echo esc_url( $meta_link ); ?>"><?php echo esc_html( $meta_label ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="bb-header__block bb-header__block--center">
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
		</div>
		<div class="bb-header__block bb-header__block--right">
			<div class="bb-header__social" aria-label="<?php esc_attr_e( 'Social links', 'brooklyn-beauty' ); ?>">
				<?php foreach ( $header_social_items as $social_item ) : ?>
				<?php
					$icon_slug    = isset( $social_item['icon'] ) ? sanitize_key( $social_item['icon'] ) : '';
					$social_url   = isset( $social_item['link'] ) ? $social_item['link'] : '';
					$social_label = $icon_slug ? ucfirst( $icon_slug ) : __( 'Social icon', 'brooklyn-beauty' );

					// --- Light (default) icon ---
					$icon_image   = isset( $social_item['icon_image'] ) ? $social_item['icon_image'] : '';
					$icon_src     = '';
					$icon_alt     = $social_label;

					if ( is_numeric( $icon_image ) ) {
						$icon_src = wp_get_attachment_image_url( (int) $icon_image, 'full' );
						$alt_text = get_post_meta( (int) $icon_image, '_wp_attachment_image_alt', true );
						if ( is_string( $alt_text ) && '' !== $alt_text ) {
							$icon_alt = $alt_text;
						}
					} elseif ( is_array( $icon_image ) ) {
						$icon_src = isset( $icon_image['url'] ) ? $icon_image['url'] : '';
						if ( ! empty( $icon_image['alt'] ) && is_string( $icon_image['alt'] ) ) {
							$icon_alt = $icon_image['alt'];
						}
					} elseif ( is_string( $icon_image ) ) {
						$icon_src = $icon_image;
					}

					$icon_img_markup = '';
					if ( $icon_src ) {
						$icon_img_markup = sprintf(
							'<img class="bb-header__social-icon bb-header__social-icon--light" src="%1$s" alt="%2$s" loading="lazy" decoding="async">',
							esc_url( $icon_src ),
							esc_attr( $icon_alt )
						);
					}

					// --- Dark icon ---
					$icon_image_dark = isset( $social_item['icon_image_dark'] ) ? $social_item['icon_image_dark'] : '';
					$icon_src_dark   = '';
					$icon_alt_dark   = $social_label;

					if ( is_numeric( $icon_image_dark ) ) {
						$icon_src_dark = wp_get_attachment_image_url( (int) $icon_image_dark, 'full' );
						$alt_text_dark = get_post_meta( (int) $icon_image_dark, '_wp_attachment_image_alt', true );
						if ( is_string( $alt_text_dark ) && '' !== $alt_text_dark ) {
							$icon_alt_dark = $alt_text_dark;
						}
					} elseif ( is_array( $icon_image_dark ) ) {
						$icon_src_dark = isset( $icon_image_dark['url'] ) ? $icon_image_dark['url'] : '';
						if ( ! empty( $icon_image_dark['alt'] ) && is_string( $icon_image_dark['alt'] ) ) {
							$icon_alt_dark = $icon_image_dark['alt'];
						}
					} elseif ( is_string( $icon_image_dark ) ) {
						$icon_src_dark = $icon_image_dark;
					}

					$icon_img_dark_markup = '';
					if ( $icon_src_dark ) {
						$icon_img_dark_markup = sprintf(
							'<img class="bb-header__social-icon bb-header__social-icon--dark" src="%1$s" alt="%2$s" loading="lazy" decoding="async">',
							esc_url( $icon_src_dark ),
							esc_attr( $icon_alt_dark )
						);
					}

					$icon_svg    = function_exists( 'brooklyn_beauty_get_header_social_icon_svg' ) ? brooklyn_beauty_get_header_social_icon_svg( $icon_slug ) : '';
					$icon_markup = $icon_img_markup ? $icon_img_markup . $icon_img_dark_markup : $icon_svg;
					?>
					<?php if ( $icon_markup ) : ?>
						<?php if ( $social_url ) : ?>
							<a class="bb-header__social-link" href="<?php echo esc_url( $social_url ); ?>" aria-label="<?php echo esc_attr( $social_label ); ?>">
								<?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php else : ?>
							<span class="bb-header__social-link" aria-label="<?php echo esc_attr( $social_label ); ?>">
								<?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<a class="btn btn--small" href="<?php echo esc_url( $header_button_link ); ?>"><?php echo esc_html( $header_button_text ); ?></a>
		</div>
	</div>
</header>

<main id="content" class="bb-main">
