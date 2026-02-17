<?php
/**
 * Footer template
 *
 * @package Brooklyn_Beauty
 */
?>
</main><!-- #content -->

<?php
$footer_address_label = '';
$footer_address_text  = '';
$footer_address_link  = '';

$footer_phone_label = '';
$footer_phone_text  = '';
$footer_phone_link  = '';

$footer_email_label = '';
$footer_email_text  = '';
$footer_email_link  = '';

$footer_hours_label = '';
$footer_hours_days  = '';
$footer_hours_time  = '';

$footer_button_text = '';
$footer_button_link = '';
$footer_tagline     = '';
$footer_featured_label           = '';
$footer_press_title_image_uri    = '';
$footer_press_subtitle_image_uri = '';
$footer_press_title_alt          = '';
$footer_press_subtitle_alt       = '';

$footer_made_by_text      = '';
$footer_made_by_link_text = '';
$footer_made_by_link      = '';
$footer_terms_text        = '';
$footer_terms_link        = '';

$footer_social_items = array();
$footer_allowed_html = array(
	'br' => array(),
);

if ( function_exists( 'get_field' ) ) {
	$acf_footer_tagline = trim( (string) get_field( 'footer_tagline', 'option' ) );
	$acf_button_text    = trim( (string) get_field( 'footer_button_text', 'option' ) );
	$acf_button_link    = trim( (string) get_field( 'footer_button_link', 'option' ) );

	$footer_tagline = $acf_footer_tagline;
	$footer_button_text = $acf_button_text;
	$footer_button_link = $acf_button_link;

	$acf_address_label = trim( (string) get_field( 'footer_address_label', 'option' ) );
	$acf_address_text  = trim( (string) get_field( 'footer_address_text', 'option' ) );
	$acf_address_link  = trim( (string) get_field( 'footer_address_link', 'option' ) );
	$acf_phone_label   = trim( (string) get_field( 'footer_phone_label', 'option' ) );
	$acf_phone_text    = trim( (string) get_field( 'footer_phone_text', 'option' ) );
	$acf_phone_link    = trim( (string) get_field( 'footer_phone_link', 'option' ) );
	$acf_email_label   = trim( (string) get_field( 'footer_email_label', 'option' ) );
	$acf_email_text    = trim( (string) get_field( 'footer_email_text', 'option' ) );
	$acf_email_link    = trim( (string) get_field( 'footer_email_link', 'option' ) );
	$acf_hours_label   = trim( (string) get_field( 'footer_hours_label', 'option' ) );
	$acf_hours_days    = trim( (string) get_field( 'footer_hours_days', 'option' ) );
	$acf_hours_time    = trim( (string) get_field( 'footer_hours_time', 'option' ) );

	$footer_address_label = $acf_address_label;
	$footer_address_text  = $acf_address_text;
	$footer_address_link  = $acf_address_link;
	$footer_phone_label   = $acf_phone_label;
	$footer_phone_text    = $acf_phone_text;
	$footer_phone_link    = $acf_phone_link;
	$footer_email_label   = $acf_email_label;
	$footer_email_text    = $acf_email_text;

	if ( '' !== $acf_email_link ) {
		$footer_email_link = $acf_email_link;
	} elseif ( '' !== $acf_email_text && is_email( $acf_email_text ) ) {
		$footer_email_link = 'mailto:' . sanitize_email( $acf_email_text );
	}

	$footer_hours_label = $acf_hours_label;
	$footer_hours_days  = $acf_hours_days;
	$footer_hours_time  = $acf_hours_time;

	$acf_footer_social = get_field( 'footer_social_items', 'option' );

	if ( ! empty( $acf_footer_social ) && is_array( $acf_footer_social ) ) {
		$prepared_social = array();

		foreach ( $acf_footer_social as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$icon_image_id = isset( $item['icon_image'] ) ? (int) $item['icon_image'] : 0;
			$link          = isset( $item['link'] ) ? trim( (string) $item['link'] ) : '';

			if ( $icon_image_id <= 0 ) {
				continue;
			}

			$label = trim( (string) get_post_meta( $icon_image_id, '_wp_attachment_image_alt', true ) );
			if ( '' === $label ) {
				$label = __( 'Social', 'brooklyn-beauty' );
			}

			$prepared_social[] = array(
				'label'         => $label,
				'icon_image_id' => $icon_image_id,
				'link'          => '' !== $link ? $link : '#',
			);
		}

		if ( ! empty( $prepared_social ) ) {
			$footer_social_items = $prepared_social;
		}
	}

	$acf_featured_label = trim( (string) get_field( 'footer_featured_label', 'option' ) );
	$footer_featured_label = $acf_featured_label;

	$acf_title_image_id = (int) get_field( 'footer_press_title_image', 'option' );
	if ( $acf_title_image_id > 0 ) {
		$acf_title_image_uri = wp_get_attachment_image_url( $acf_title_image_id, 'full' );
		$acf_title_image_alt = trim( (string) get_post_meta( $acf_title_image_id, '_wp_attachment_image_alt', true ) );
		if ( $acf_title_image_uri ) {
			$footer_press_title_image_uri = $acf_title_image_uri;
		}
		$footer_press_title_alt = $acf_title_image_alt;
	}

	$acf_subtitle_image_id = (int) get_field( 'footer_press_subtitle_image', 'option' );
	if ( $acf_subtitle_image_id > 0 ) {
		$acf_subtitle_image_uri = wp_get_attachment_image_url( $acf_subtitle_image_id, 'full' );
		$acf_subtitle_image_alt = trim( (string) get_post_meta( $acf_subtitle_image_id, '_wp_attachment_image_alt', true ) );
		if ( $acf_subtitle_image_uri ) {
			$footer_press_subtitle_image_uri = $acf_subtitle_image_uri;
		}
		$footer_press_subtitle_alt = $acf_subtitle_image_alt;
	}

	$footer_made_by_text      = trim( (string) get_field( 'footer_made_by_text', 'option' ) );
	$footer_made_by_link_text = trim( (string) get_field( 'footer_made_by_link_text', 'option' ) );
	$footer_made_by_link      = trim( (string) get_field( 'footer_made_by_link', 'option' ) );
	$footer_terms_text        = trim( (string) get_field( 'footer_terms_text', 'option' ) );
	$footer_terms_link        = trim( (string) get_field( 'footer_terms_link', 'option' ) );
}
?>

<footer class="bb-footer">
	<div class="bb-container bb-footer__inner">
		<div class="bb-footer__top">
			<div class="bb-footer__row bb-footer__row--main">
				<div class="bb-footer__row-left">
					<div class="bb-footer__brand">
						<?php if ( has_custom_logo() ) : ?>
							<?php the_custom_logo(); ?>
						<?php else : ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
						<?php endif; ?>
					</div>
				</div>

				<div class="bb-footer__row-right">
					<div class="bb-footer__cta">
						<?php if ( '' !== $footer_tagline ) : ?>
							<div class="bb-footer__tagline"><?php echo wp_kses( $footer_tagline, $footer_allowed_html ); ?></div>
						<?php endif; ?>
						<?php if ( '' !== $footer_button_text && '' !== $footer_button_link ) : ?>
							<a class="btn btn--small bb-footer__button" href="<?php echo esc_url( $footer_button_link ); ?>">
								<?php echo wp_kses( $footer_button_text, $footer_allowed_html ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="bb-footer__row bb-footer__row--contacts">
				<div class="bb-footer__row-left">
					<div class="bb-footer__contacts">
						<?php if ( '' !== $footer_address_text ) : ?>
							<div class="bb-footer__contact-block">
								<?php if ( '' !== $footer_address_label ) : ?>
									<div class="bb-footer__label"><?php echo wp_kses( $footer_address_label, $footer_allowed_html ); ?></div>
								<?php endif; ?>
								<?php if ( '' !== $footer_address_link ) : ?>
									<a class="bb-footer__contact-link" href="<?php echo esc_url( $footer_address_link ); ?>" target="_blank" rel="noopener noreferrer">
										<?php echo wp_kses( $footer_address_text, $footer_allowed_html ); ?>
									</a>
								<?php else : ?>
									<div class="bb-footer__contact-text"><?php echo wp_kses( $footer_address_text, $footer_allowed_html ); ?></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( '' !== $footer_phone_text ) : ?>
							<div class="bb-footer__contact-block">
								<?php if ( '' !== $footer_phone_label ) : ?>
									<div class="bb-footer__label"><?php echo wp_kses( $footer_phone_label, $footer_allowed_html ); ?></div>
								<?php endif; ?>
								<?php if ( '' !== $footer_phone_link ) : ?>
									<a class="bb-footer__contact-link" href="<?php echo esc_url( $footer_phone_link ); ?>">
										<?php echo wp_kses( $footer_phone_text, $footer_allowed_html ); ?>
									</a>
								<?php else : ?>
									<div class="bb-footer__contact-text"><?php echo wp_kses( $footer_phone_text, $footer_allowed_html ); ?></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="bb-footer__row-right">
					<?php if ( '' !== $footer_email_text ) : ?>
						<div class="bb-footer__contact-block bb-footer__contact-block--right">
							<?php if ( '' !== $footer_email_label ) : ?>
								<div class="bb-footer__label"><?php echo wp_kses( $footer_email_label, $footer_allowed_html ); ?></div>
							<?php endif; ?>
							<?php if ( '' !== $footer_email_link ) : ?>
								<a class="bb-footer__contact-link" href="<?php echo esc_url( $footer_email_link ); ?>">
									<?php echo wp_kses( $footer_email_text, $footer_allowed_html ); ?>
								</a>
							<?php else : ?>
								<div class="bb-footer__contact-text"><?php echo wp_kses( $footer_email_text, $footer_allowed_html ); ?></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( '' !== $footer_hours_days || '' !== $footer_hours_time ) : ?>
						<div class="bb-footer__contact-block bb-footer__contact-block--right">
							<?php if ( '' !== $footer_hours_label ) : ?>
								<div class="bb-footer__label"><?php echo wp_kses( $footer_hours_label, $footer_allowed_html ); ?></div>
							<?php endif; ?>
							<?php if ( '' !== $footer_hours_days ) : ?>
								<div class="bb-footer__contact-text"><?php echo wp_kses( $footer_hours_days, $footer_allowed_html ); ?></div>
							<?php endif; ?>
							<?php if ( '' !== $footer_hours_time ) : ?>
								<div class="bb-footer__contact-text"><?php echo wp_kses( $footer_hours_time, $footer_allowed_html ); ?></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="bb-footer__row bb-footer__row--bottom-content">
				<div class="bb-footer__row-left">
					<div class="bb-footer__social" aria-label="<?php esc_attr_e( 'Social links', 'brooklyn-beauty' ); ?>">
						<?php foreach ( $footer_social_items as $social_item ) : ?>
							<?php
							$social_icon_image_id = isset( $social_item['icon_image_id'] ) ? (int) $social_item['icon_image_id'] : 0;
							if ( $social_icon_image_id <= 0 ) {
								continue;
							}
							?>
							<a
								class="bb-footer__social-link"
								href="<?php echo esc_url( isset( $social_item['link'] ) ? $social_item['link'] : '#' ); ?>"
								aria-label="<?php echo esc_attr( isset( $social_item['label'] ) ? $social_item['label'] : __( 'Social', 'brooklyn-beauty' ) ); ?>"
							>
								<?php echo wp_get_attachment_image( $social_icon_image_id, 'full', false, array( 'class' => 'bb-footer__social-icon', 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
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

				<div class="bb-footer__row-right">
					<div class="bb-footer__featured">
						<?php if ( '' !== $footer_featured_label ) : ?>
							<div class="bb-footer__label bb-footer__label--featured"><?php echo wp_kses( $footer_featured_label, $footer_allowed_html ); ?></div>
						<?php endif; ?>
						<?php if ( '' !== $footer_press_title_image_uri ) : ?>
							<div class="bb-footer__press-image bb-footer__press-image--top">
								<img src="<?php echo esc_url( $footer_press_title_image_uri ); ?>" alt="<?php echo esc_attr( $footer_press_title_alt ); ?>" loading="lazy">
							</div>
						<?php endif; ?>
						<?php if ( '' !== $footer_press_subtitle_image_uri ) : ?>
							<div class="bb-footer__press-image bb-footer__press-image--bottom">
								<img src="<?php echo esc_url( $footer_press_subtitle_image_uri ); ?>" alt="<?php echo esc_attr( $footer_press_subtitle_alt ); ?>" loading="lazy">
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="bb-footer__bottom">
			<div class="bb-footer__made-by">
				<?php if ( '' !== $footer_made_by_text ) : ?>
					<?php echo wp_kses( $footer_made_by_text, $footer_allowed_html ); ?>
				<?php endif; ?>
				<?php if ( '' !== $footer_made_by_link_text && '' !== $footer_made_by_link ) : ?>
					<a href="<?php echo esc_url( $footer_made_by_link ); ?>" class="bb-footer__made-by-link"><?php echo wp_kses( $footer_made_by_link_text, $footer_allowed_html ); ?></a>
				<?php endif; ?>
			</div>
			<div class="bb-footer__copy">
				<?php bloginfo( 'name' ); ?> &copy;<?php echo esc_html( date( 'Y' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'brooklyn-beauty' ); ?>
			</div>
			<div class="bb-footer__terms">
				<?php if ( '' !== $footer_terms_text && '' !== $footer_terms_link ) : ?>
					<a href="<?php echo esc_url( $footer_terms_link ); ?>"><?php echo wp_kses( $footer_terms_text, $footer_allowed_html ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
