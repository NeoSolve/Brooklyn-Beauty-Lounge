<?php
/**
 * Local business structured data helpers.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get configured local business price range.
 *
 * @return string
 */
function brooklyn_beauty_get_local_business_price_range() {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	return trim( (string) get_field( 'local_business_price_range', 'option' ) );
}

/**
 * Convert a human-readable time string to 24-hour format.
 *
 * @param string $time_string Raw time string.
 *
 * @return string
 */
function brooklyn_beauty_normalize_local_business_time( $time_string ) {
	$time_string = trim( (string) $time_string );
	if ( '' === $time_string ) {
		return '';
	}

	$timestamp = strtotime( $time_string );
	if ( false === $timestamp ) {
		return '';
	}

	return date( 'H:i', $timestamp );
}

/**
 * Build opening hours specification from legacy footer fields.
 *
 * @param string $days_text Legacy days text.
 * @param string $time_text Legacy time text.
 *
 * @return array<int, array<string, mixed>>
 */
function brooklyn_beauty_get_local_business_opening_hours_fallback( $days_text, $time_text ) {
	$days_text = trim( rtrim( (string) $days_text, ':' ) );
	$time_text = trim( (string) $time_text );

	if ( '' === $days_text || '' === $time_text ) {
		return array();
	}

	if ( ! preg_match( '/(.+?)\s*-\s*(.+)/', $time_text, $time_matches ) ) {
		return array();
	}

	$opens  = brooklyn_beauty_normalize_local_business_time( $time_matches[1] );
	$closes = brooklyn_beauty_normalize_local_business_time( $time_matches[2] );

	if ( '' === $opens || '' === $closes ) {
		return array();
	}

	$day_map = array(
		'monday'    => 'https://schema.org/Monday',
		'tuesday'   => 'https://schema.org/Tuesday',
		'wednesday' => 'https://schema.org/Wednesday',
		'thursday'  => 'https://schema.org/Thursday',
		'friday'    => 'https://schema.org/Friday',
		'saturday'  => 'https://schema.org/Saturday',
		'sunday'    => 'https://schema.org/Sunday',
	);
	$day_order = array_keys( $day_map );
	$days      = array();

	if ( preg_match( '/^(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\s*-\s*(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)$/i', $days_text, $day_range_matches ) ) {
		$start_day = strtolower( $day_range_matches[1] );
		$end_day   = strtolower( $day_range_matches[2] );
		$start_idx = array_search( $start_day, $day_order, true );
		$end_idx   = array_search( $end_day, $day_order, true );

		if ( false !== $start_idx && false !== $end_idx ) {
			if ( $start_idx <= $end_idx ) {
				$days = array_slice( $day_order, $start_idx, $end_idx - $start_idx + 1 );
			} else {
				$days = array_merge( array_slice( $day_order, $start_idx ), array_slice( $day_order, 0, $end_idx + 1 ) );
			}
		}
	} else {
		$raw_days = preg_split( '/\s*,\s*/', $days_text );
		$raw_days = is_array( $raw_days ) ? $raw_days : array();

		foreach ( $raw_days as $raw_day ) {
			$day_key = strtolower( trim( (string) $raw_day ) );
			if ( isset( $day_map[ $day_key ] ) ) {
				$days[] = $day_key;
			}
		}
	}

	$days = array_values( array_unique( $days ) );
	if ( empty( $days ) ) {
		return array();
	}

	$schema_days = array_map(
		static function ( $day_key ) use ( $day_map ) {
			return $day_map[ $day_key ];
		},
		$days
	);

	return array(
		array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => 1 === count( $schema_days ) ? $schema_days[0] : $schema_days,
			'opens'     => $opens,
			'closes'    => $closes,
		),
	);
}

/**
 * Build LocalBusiness / BeautySalon schema payload.
 *
 * @return array<string, mixed>
 */
function brooklyn_beauty_get_local_business_schema_data() {
	$name            = wp_strip_all_tags( (string) get_bloginfo( 'name' ) );
	$street_address  = '';
	$address_locality = '';
	$address_region  = '';
	$postal_code     = '';
	$address_country = '';
	$telephone       = '';
	$price_range     = function_exists( 'brooklyn_beauty_get_local_business_price_range' ) ? brooklyn_beauty_get_local_business_price_range() : '';
	$opening_hours   = array();

	if ( function_exists( 'get_field' ) ) {
		$acf_name             = trim( (string) get_field( 'local_business_name', 'option' ) );
		$street_address       = trim( (string) get_field( 'local_business_street_address', 'option' ) );
		$address_locality     = trim( (string) get_field( 'local_business_address_locality', 'option' ) );
		$address_region       = trim( (string) get_field( 'local_business_address_region', 'option' ) );
		$postal_code          = trim( (string) get_field( 'local_business_postal_code', 'option' ) );
		$address_country      = trim( (string) get_field( 'local_business_address_country', 'option' ) );
		$telephone            = trim( (string) get_field( 'local_business_telephone', 'option' ) );
		$acf_opening_hours    = get_field( 'local_business_opening_hours', 'option' );
		$footer_address_text  = trim( (string) get_field( 'footer_address_text', 'option' ) );
		$footer_address_line_2 = trim( (string) get_field( 'footer_address_text_line_2', 'option' ) );
		$footer_phone_text    = trim( (string) get_field( 'footer_phone_text', 'option' ) );
		$footer_hours_days    = trim( (string) get_field( 'footer_hours_days', 'option' ) );
		$footer_hours_time    = trim( (string) get_field( 'footer_hours_time', 'option' ) );

		if ( '' !== $acf_name ) {
			$name = $acf_name;
		}

		if ( '' === $street_address && '' !== $footer_address_text ) {
			$street_address = $footer_address_text;
		}

		if ( '' === $telephone && '' !== $footer_phone_text ) {
			$telephone = $footer_phone_text;
		}

		if ( ( '' === $address_locality || '' === $address_region || '' === $postal_code ) && '' !== $footer_address_line_2 ) {
			if ( preg_match( '/^\s*([^,]+),\s*([A-Za-z]{2})\s+([A-Za-z0-9-]+)\s*$/', $footer_address_line_2, $address_line_2_matches ) ) {
				if ( '' === $address_locality ) {
					$address_locality = trim( (string) $address_line_2_matches[1] );
				}
				if ( '' === $address_region ) {
					$address_region = trim( (string) $address_line_2_matches[2] );
				}
				if ( '' === $postal_code ) {
					$postal_code = trim( (string) $address_line_2_matches[3] );
				}
			}
		}

		if ( is_array( $acf_opening_hours ) ) {
			$day_map = array(
				'monday'    => 'https://schema.org/Monday',
				'tuesday'   => 'https://schema.org/Tuesday',
				'wednesday' => 'https://schema.org/Wednesday',
				'thursday'  => 'https://schema.org/Thursday',
				'friday'    => 'https://schema.org/Friday',
				'saturday'  => 'https://schema.org/Saturday',
				'sunday'    => 'https://schema.org/Sunday',
			);

			foreach ( $acf_opening_hours as $opening_hours_row ) {
				if ( ! is_array( $opening_hours_row ) ) {
					continue;
				}

				$row_days = isset( $opening_hours_row['days'] ) && is_array( $opening_hours_row['days'] ) ? $opening_hours_row['days'] : array();
				$row_days = array_values(
					array_filter(
						array_map(
							static function ( $day ) use ( $day_map ) {
								$day_key = strtolower( trim( (string) $day ) );
								return isset( $day_map[ $day_key ] ) ? $day_map[ $day_key ] : '';
							},
							$row_days
						)
					)
				);

				$row_opens  = isset( $opening_hours_row['opens'] ) ? trim( (string) $opening_hours_row['opens'] ) : '';
				$row_closes = isset( $opening_hours_row['closes'] ) ? trim( (string) $opening_hours_row['closes'] ) : '';

				if ( empty( $row_days ) || '' === $row_opens || '' === $row_closes ) {
					continue;
				}

				$opening_hours_row_schema = array(
					'@type'  => 'OpeningHoursSpecification',
					'opens'  => $row_opens,
					'closes' => $row_closes,
				);

				$opening_hours_row_schema['dayOfWeek'] = 1 === count( $row_days ) ? $row_days[0] : $row_days;

				$opening_hours[] = $opening_hours_row_schema;
			}
		}

		if ( empty( $opening_hours ) ) {
			$opening_hours = brooklyn_beauty_get_local_business_opening_hours_fallback( $footer_hours_days, $footer_hours_time );
		}
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'BeautySalon',
		'name'     => $name,
	);

	if (
		'' !== $street_address ||
		'' !== $address_locality ||
		'' !== $address_region ||
		'' !== $postal_code ||
		'' !== $address_country
	) {
		$schema['address'] = array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $street_address,
				'addressLocality' => $address_locality,
				'addressRegion'   => $address_region,
				'postalCode'      => $postal_code,
				'addressCountry'  => $address_country,
			),
			static function ( $value ) {
				return '' !== $value;
			}
		);
	}

	if ( '' !== $telephone ) {
		$schema['telephone'] = $telephone;
	}

	if ( ! empty( $opening_hours ) ) {
		$schema['openingHoursSpecification'] = $opening_hours;
	}

	if ( '' !== $price_range ) {
		$schema['priceRange'] = $price_range;
	}

	return $schema;
}

/**
 * Output LocalBusiness schema.
 *
 * @return void
 */
function brooklyn_beauty_render_local_business_schema() {
	$schema = brooklyn_beauty_get_local_business_schema_data();

	if ( empty( $schema['name'] ) ) {
		return;
	}
	?>
	<script type="application/ld+json">
		<?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
	</script>
	<?php
}
add_action( 'wp_footer', 'brooklyn_beauty_render_local_business_schema', 20 );
