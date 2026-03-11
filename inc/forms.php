<?php
/**
 * Contact Form 7 customizations.
 *
 * Phone field validation (strict mask +1-234-567-8901) for specific form IDs.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validate contact form phone fields by strict mask.
 *
 * @param WPCF7_Validation $result Validation result.
 * @param WPCF7_FormTag    $tag    Current field tag.
 *
 * @return WPCF7_Validation
 */
function brooklyn_beauty_validate_phone_mask_for_contact_forms( $result, $tag ) {
	$contact_form = class_exists( 'WPCF7_ContactForm' ) ? WPCF7_ContactForm::get_current() : null;
	$form_id      = $contact_form ? (string) $contact_form->id() : '';
	$target_forms = array( '7e69551', '4530bca' );
	if ( '' === $form_id || ! in_array( $form_id, $target_forms, true ) ) {
		return $result;
	}

	$field_name = is_object( $tag ) && isset( $tag->name ) ? (string) $tag->name : '';
	if ( '' === $field_name ) {
		return $result;
	}

	$submission = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;
	$posted_data = $submission ? (array) $submission->get_posted_data() : array();
	$field_value = isset( $posted_data[ $field_name ] ) ? trim( (string) $posted_data[ $field_name ] ) : '';

	if ( '' === $field_value ) {
		return $result;
	}

	if ( ! preg_match( '/^\+1-\d{3}-\d{3}-\d{4}$/', $field_value ) ) {
		$result->invalidate( $tag, __( 'Please use phone format +1-234-567-8901.', 'brooklyn-beauty' ) );
	}

	return $result;
}
add_filter( 'wpcf7_validate_tel', 'brooklyn_beauty_validate_phone_mask_for_contact_forms', 20, 2 );
add_filter( 'wpcf7_validate_tel*', 'brooklyn_beauty_validate_phone_mask_for_contact_forms', 20, 2 );
