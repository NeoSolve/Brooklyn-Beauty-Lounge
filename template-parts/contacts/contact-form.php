<?php
/**
 * Contacts page form block.
 *
 * @package Brooklyn_Beauty
 */
?>
<section class="bb-contacts-form" id="contact-form">
	<div class="bb-container">
		<div class="bb-contacts-form__layout">
			<span class="bb-contacts-form__decor bb-contacts-form__decor--left" aria-hidden="true"><?php esc_html_e( "you're", 'brooklyn-beauty' ); ?></span>
			<div class="bb-contacts-form__inner">
				<h2 class="bb-contacts-form__title"><?php esc_html_e( 'contact us', 'brooklyn-beauty' ); ?></h2>
				<?php echo do_shortcode( '[contact-form-7 id="4530bca" title="Form on the contact page"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<span class="bb-contacts-form__decor bb-contacts-form__decor--right" aria-hidden="true"><?php esc_html_e( 'welcome', 'brooklyn-beauty' ); ?></span>
		</div>
	</div>
</section>
