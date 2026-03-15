<?php
/**
 * Contact Form 7 customizations.
 *
 * Phone field validation (strict 10-digit mask) for specific form IDs.
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

	$digits_only = preg_replace( '/\D/', '', $field_value );

	if ( 11 === strlen( $digits_only ) && '1' === $digits_only[0] ) {
		$digits_only = substr( $digits_only, 1 );
	}

	if ( strlen( $digits_only ) !== 10 ) {
		$result->invalidate( $tag, __( 'Please enter a 10-digit phone number.', 'brooklyn-beauty' ) );
	}

	return $result;
}
add_filter( 'wpcf7_validate_tel', 'brooklyn_beauty_validate_phone_mask_for_contact_forms', 20, 2 );
add_filter( 'wpcf7_validate_tel*', 'brooklyn_beauty_validate_phone_mask_for_contact_forms', 20, 2 );

/**
 * Validate email fields for contact forms.
 *
 * @param WPCF7_Validation $result Validation result.
 * @param WPCF7_FormTag    $tag    Current field tag.
 *
 * @return WPCF7_Validation
 */
function brooklyn_beauty_validate_email_for_contact_forms( $result, $tag ) {
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

	$submission   = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;
	$posted_data  = $submission ? (array) $submission->get_posted_data() : array();
	$field_value  = isset( $posted_data[ $field_name ] ) ? trim( (string) $posted_data[ $field_name ] ) : '';

	if ( '' === $field_value ) {
		return $result;
	}

	if ( ! is_email( $field_value ) ) {
		$result->invalidate( $tag, __( 'Please enter a valid email address.', 'brooklyn-beauty' ) );
	}

	return $result;
}
add_filter( 'wpcf7_validate_email', 'brooklyn_beauty_validate_email_for_contact_forms', 20, 2 );
add_filter( 'wpcf7_validate_email*', 'brooklyn_beauty_validate_email_for_contact_forms', 20, 2 );
