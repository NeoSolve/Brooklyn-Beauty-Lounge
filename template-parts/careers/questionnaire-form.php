<?php
/**
 * Career questionnaire form block.
 *
 * @package Brooklyn_Beauty
 */
?>
<section class="bb-contacts-form bb-career-questionnaire-form" id="career-questionnaire-form">
	<div class="bb-container">
		<div class="bb-contacts-form__layout bb-contacts-form__layout--career-questionnaire">
			<div class="bb-contacts-form__inner">
				<?php echo do_shortcode( '[contact-form-7 id="7e69551" title="Career questionnaire"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<span class="bb-contacts-form__decor bb-contacts-form__decor--right" aria-hidden="true"><?php esc_html_e( 'welcome', 'brooklyn-beauty' ); ?></span>
		</div>
	</div>
</section>
