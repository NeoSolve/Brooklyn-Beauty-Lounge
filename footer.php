<?php
/**
 * Footer template
 *
 * @package Brooklyn_Beauty
 */
?>
</main><!-- #content -->

<?php
$footer_address_label = __( 'address', 'brooklyn-beauty' );
$footer_address_text  = __( '2080 Coney Island Avenue', 'brooklyn-beauty' );
$footer_address_link  = 'https://maps.google.com/?q=2080+Coney+Island+Avenue+Brooklyn+NY+11223';
$footer_address_city  = __( 'Brooklyn, NY 11223', 'brooklyn-beauty' );

$footer_phone_label = __( 'phone', 'brooklyn-beauty' );
$footer_phone_text  = __( '(718) 419-3232', 'brooklyn-beauty' );
$footer_phone_link  = 'tel:+17184193232';

$footer_email_label = __( 'e-mail', 'brooklyn-beauty' );
$footer_email_text  = 'info@brooklynbeautylounge.com';
$footer_email_link  = 'mailto:info@brooklynbeautylounge.com';

$footer_hours_label = __( 'hours', 'brooklyn-beauty' );
$footer_hours_days  = __( 'Monday - Sunday:', 'brooklyn-beauty' );
$footer_hours_time  = __( '8:30 AM - 8:30 PM', 'brooklyn-beauty' );

$footer_button_text = __( 'book now', 'brooklyn-beauty' );
$footer_button_link = '#book';
$footer_tagline     = __( 'be fabulous with', 'brooklyn-beauty' );

$footer_social_items = array(
	array(
		'label' => 'Instagram',
		'text'  => 'IG',
		'link'  => '#',
	),
	array(
		'label' => 'Facebook',
		'text'  => 'f',
		'link'  => '#',
	),
	array(
		'label' => 'Google',
		'text'  => 'G',
		'link'  => '#',
	),
	array(
		'label' => 'TikTok',
		'text'  => 't',
		'link'  => '#',
	),
);

if ( function_exists( 'get_field' ) ) {
	$acf_button_text = trim( (string) get_field( 'header_button_text', 'option' ) );
	$acf_button_link = trim( (string) get_field( 'header_button_link', 'option' ) );
	$acf_social      = get_field( 'header_social_items', 'option' );

	if ( '' !== $acf_button_text ) {
		$footer_button_text = $acf_button_text;
	}

	if ( '' !== $acf_button_link ) {
		$footer_button_link = $acf_button_link;
	}

	if ( ! empty( $acf_social ) && is_array( $acf_social ) ) {
		$prepared_social = array();

		foreach ( $acf_social as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$icon_slug = isset( $item['icon'] ) ? sanitize_key( (string) $item['icon'] ) : '';
			$label     = $icon_slug ? ucfirst( $icon_slug ) : __( 'Social', 'brooklyn-beauty' );
			$text      = '';

			if ( 'instagram' === $icon_slug ) {
				$text = 'IG';
			} elseif ( 'facebook' === $icon_slug ) {
				$text = 'f';
			} elseif ( 'google' === $icon_slug ) {
				$text = 'G';
			} elseif ( 'tiktok' === $icon_slug ) {
				$text = 't';
			} else {
				$text = strtoupper( substr( $label, 0, 1 ) );
			}

			$prepared_social[] = array(
				'label' => $label,
				'text'  => $text,
				'link'  => isset( $item['link'] ) ? (string) $item['link'] : '#',
			);
		}

		if ( ! empty( $prepared_social ) ) {
			$footer_social_items = $prepared_social;
		}
	}
}
?>

<footer class="bb-footer">
	<div class="bb-container bb-footer__inner">
		<div class="bb-footer__top">
			<div class="bb-footer__left">
				<div class="bb-footer__brand">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
					<?php endif; ?>
				</div>

				<div class="bb-footer__contacts">
					<div class="bb-footer__contact-block">
						<div class="bb-footer__label"><?php echo esc_html( $footer_address_label ); ?></div>
						<a class="bb-footer__contact-link" href="<?php echo esc_url( $footer_address_link ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $footer_address_text ); ?>
						</a>
						<a class="bb-footer__contact-link" href="<?php echo esc_url( $footer_address_link ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $footer_address_city ); ?>
						</a>
					</div>

					<div class="bb-footer__contact-block">
						<div class="bb-footer__label"><?php echo esc_html( $footer_phone_label ); ?></div>
						<a class="bb-footer__contact-link" href="<?php echo esc_url( $footer_phone_link ); ?>">
							<?php echo esc_html( $footer_phone_text ); ?>
						</a>
					</div>
				</div>

				<div class="bb-footer__social" aria-label="<?php esc_attr_e( 'Social links', 'brooklyn-beauty' ); ?>">
					<?php foreach ( $footer_social_items as $social_item ) : ?>
						<a
							class="bb-footer__social-link"
							href="<?php echo esc_url( isset( $social_item['link'] ) ? $social_item['link'] : '#' ); ?>"
							aria-label="<?php echo esc_attr( isset( $social_item['label'] ) ? $social_item['label'] : __( 'Social', 'brooklyn-beauty' ) ); ?>"
						>
							<?php echo esc_html( isset( $social_item['text'] ) ? $social_item['text'] : 'S' ); ?>
						</a>
					<?php endforeach; ?>
				</div>

				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => 'nav',
					'container_class' => 'bb-footer__nav',
					'menu_class'     => 'bb-footer__menu',
					'depth'          => 1,
					'fallback_cb'    => static function() {
						?>
						<nav class="bb-footer__nav" aria-label="<?php esc_attr_e( 'Footer menu', 'brooklyn-beauty' ); ?>">
							<ul class="bb-footer__menu">
								<li><a href="#services"><?php esc_html_e( 'Services', 'brooklyn-beauty' ); ?></a></li>
								<li><a href="#promotions"><?php esc_html_e( 'Promotions', 'brooklyn-beauty' ); ?></a></li>
								<li><a href="#blog"><?php esc_html_e( 'Blog', 'brooklyn-beauty' ); ?></a></li>
								<li><a href="#careers"><?php esc_html_e( 'Careers', 'brooklyn-beauty' ); ?></a></li>
								<li><a href="#contacts"><?php esc_html_e( 'Contacts', 'brooklyn-beauty' ); ?></a></li>
							</ul>
						</nav>
						<?php
					},
				) );
				?>
			</div>

			<div class="bb-footer__right">
				<div class="bb-footer__cta">
					<div class="bb-footer__tagline"><?php echo esc_html( $footer_tagline ); ?></div>
					<div class="bb-footer__name"><?php bloginfo( 'name' ); ?></div>
					<a class="btn btn--small bb-footer__button" href="<?php echo esc_url( $footer_button_link ); ?>">
						<?php echo esc_html( $footer_button_text ); ?>
					</a>
				</div>

				<div class="bb-footer__contact-block bb-footer__contact-block--right">
					<div class="bb-footer__label"><?php echo esc_html( $footer_email_label ); ?></div>
					<a class="bb-footer__contact-link" href="<?php echo esc_url( $footer_email_link ); ?>">
						<?php echo esc_html( $footer_email_text ); ?>
					</a>
				</div>

				<div class="bb-footer__contact-block bb-footer__contact-block--right">
					<div class="bb-footer__label"><?php echo esc_html( $footer_hours_label ); ?></div>
					<div class="bb-footer__contact-text"><?php echo esc_html( $footer_hours_days ); ?></div>
					<div class="bb-footer__contact-text"><?php echo esc_html( $footer_hours_time ); ?></div>
				</div>

				<div class="bb-footer__featured">
					<div class="bb-footer__label bb-footer__label--featured"><?php esc_html_e( 'featured in', 'brooklyn-beauty' ); ?></div>
					<div class="bb-footer__press-title">NEW YORK<br>FASHION<br>WEEK</div>
					<div class="bb-footer__press-subtitle">TimeOut<br>New York</div>
				</div>
			</div>
		</div>

		<div class="bb-footer__bottom">
			<div class="bb-footer__made-by">
				<?php esc_html_e( 'who made this website:', 'brooklyn-beauty' ); ?>
				<a href="#" class="bb-footer__made-by-link">KSANTY WEB</a>
			</div>
			<div class="bb-footer__copy">
				<?php bloginfo( 'name' ); ?> &copy;<?php echo esc_html( date( 'Y' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'brooklyn-beauty' ); ?>
			</div>
			<div class="bb-footer__terms">
				<a href="#"><?php esc_html_e( 'Terms and Services', 'brooklyn-beauty' ); ?></a>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
