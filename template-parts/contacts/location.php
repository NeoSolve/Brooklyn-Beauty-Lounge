<?php
/**
 * Contacts location block.
 *
 * @package Brooklyn_Beauty
 */

$location_title = __( 'location', 'brooklyn-beauty' );
$address_label  = __( 'address', 'brooklyn-beauty' );
$address_text         = __( '2080 Coney Island Avenue', 'brooklyn-beauty' );
$address_text_line_2  = __( 'Brooklyn, NY 11223', 'brooklyn-beauty' );
$address_link         = 'https://maps.google.com/?q=2080+Coney+Island+Avenue+Brooklyn+NY+11223';
$hours_label    = __( 'hours', 'brooklyn-beauty' );
$hours_days     = __( 'Monday - Sunday:', 'brooklyn-beauty' );
$hours_time     = __( '8:30 AM - 8:30 PM', 'brooklyn-beauty' );
$price_range_label = __( 'price range', 'brooklyn-beauty' );
$price_range       = '';

if ( function_exists( 'get_field' ) ) {
	$acf_address_label = trim( (string) get_field( 'footer_address_label', 'option' ) );
	$acf_address_text         = trim( (string) get_field( 'footer_address_text', 'option' ) );
	$acf_address_text_line_2  = trim( (string) get_field( 'footer_address_text_line_2', 'option' ) );
	$acf_address_link         = trim( (string) get_field( 'footer_address_link', 'option' ) );
	$acf_hours_label   = trim( (string) get_field( 'footer_hours_label', 'option' ) );
	$acf_hours_days    = trim( (string) get_field( 'footer_hours_days', 'option' ) );
	$acf_hours_time    = trim( (string) get_field( 'footer_hours_time', 'option' ) );

	if ( '' !== $acf_address_label ) {
		$address_label = $acf_address_label;
	}

	if ( '' !== $acf_address_text ) {
		$address_text = $acf_address_text;
	}

	if ( '' !== $acf_address_text_line_2 ) {
		$address_text_line_2 = $acf_address_text_line_2;
	}

	if ( '' !== $acf_address_link ) {
		$address_link = $acf_address_link;
	}

	if ( '' !== $acf_hours_label ) {
		$hours_label = $acf_hours_label;
	}

	if ( '' !== $acf_hours_days ) {
		$hours_days = $acf_hours_days;
	}

	if ( '' !== $acf_hours_time ) {
		$hours_time = $acf_hours_time;
	}

	$acf_price_range = trim( (string) get_field( 'local_business_price_range', 'option' ) );
	if ( '' !== $acf_price_range ) {
		$price_range = $acf_price_range;
	}
}

$map_lat = 40.60646495007802;
$map_lng = -73.96208768734041;

if ( '' !== $address_link ) {
	if ( preg_match( '/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $address_link, $google_at_matches ) ) {
		$map_lat = (float) $google_at_matches[1];
		$map_lng = (float) $google_at_matches[2];
	} elseif ( preg_match( '/[?&]q=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $address_link, $google_q_matches ) ) {
		$map_lat = (float) $google_q_matches[1];
		$map_lng = (float) $google_q_matches[2];
	} elseif ( preg_match( '/[?&]ll=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $address_link, $google_ll_matches ) ) {
		$map_lat = (float) $google_ll_matches[1];
		$map_lng = (float) $google_ll_matches[2];
	} elseif ( preg_match( '/#map=\d+\/(-?\d+(?:\.\d+)?)\/(-?\d+(?:\.\d+)?)/', $address_link, $osm_hash_matches ) ) {
		$map_lat = (float) $osm_hash_matches[1];
		$map_lng = (float) $osm_hash_matches[2];
	}
}

$address_link_opens_in_new_tab = brooklyn_beauty_should_open_external_url_in_new_tab( $address_link );

?>

<section class="bb-contacts-location" aria-labelledby="bb-contacts-location-title">
	<div class="bb-container">
		<div class="bb-contacts-location__frame">
			<div class="bb-contacts-location__content">
				<div class="bb-contacts-location__info">
					<div class="bb-contacts-location__block">
						<?php if ( '' !== $address_label ) : ?>
							<p class="bb-contacts-location__label"><?php echo esc_html( $address_label ); ?></p>
						<?php endif; ?>
					<?php if ( '' !== $address_link ) : ?>
						<?php if ( '' !== $address_text ) : ?>
							<a class="bb-contacts-location__value bb-contacts-location__value--link" href="<?php echo esc_url( $address_link ); ?>"<?php if ( $address_link_opens_in_new_tab ) : ?> target="_blank" rel="nofollow noopener noreferrer"<?php endif; ?>>
								<?php echo esc_html( $address_text ); ?>
							</a>
						<?php endif; ?>
						<?php if ( '' !== $address_text_line_2 ) : ?>
							<a class="bb-contacts-location__value bb-contacts-location__value--link" href="<?php echo esc_url( $address_link ); ?>"<?php if ( $address_link_opens_in_new_tab ) : ?> target="_blank" rel="nofollow noopener noreferrer"<?php endif; ?>>
								<?php echo esc_html( $address_text_line_2 ); ?>
							</a>
						<?php endif; ?>
					<?php else : ?>
						<?php if ( '' !== $address_text ) : ?>
							<p class="bb-contacts-location__value"><?php echo esc_html( $address_text ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== $address_text_line_2 ) : ?>
							<p class="bb-contacts-location__value"><?php echo esc_html( $address_text_line_2 ); ?></p>
						<?php endif; ?>
					<?php endif; ?>
					</div>

				<div class="bb-contacts-location__block">
					<?php if ( '' !== $hours_label ) : ?>
						<p class="bb-contacts-location__label"><?php echo esc_html( $hours_label ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $hours_days ) : ?>
						<p class="bb-contacts-location__value"><?php echo esc_html( $hours_days ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $hours_time ) : ?>
						<p class="bb-contacts-location__value"><?php echo esc_html( $hours_time ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $price_range ) : ?>
					<div class="bb-contacts-location__block">
						<p class="bb-contacts-location__label"><?php echo esc_html( $price_range_label ); ?></p>
						<p class="bb-contacts-location__value"><?php echo esc_html( $price_range ); ?></p>
					</div>
				<?php endif; ?>
				</div>

				<h2 class="bb-contacts-location__title" id="bb-contacts-location-title"><?php echo esc_html( $location_title ); ?></h2>
			</div>

			<div class="bb-contacts-location__map-wrap">
			<div
				class="bb-contacts-location__map js-bb-contacts-map"
				data-lat="<?php echo esc_attr( (string) $map_lat ); ?>"
				data-lng="<?php echo esc_attr( (string) $map_lng ); ?>"
				data-zoom="17.5"
				data-directions-url="<?php echo esc_attr( '' !== $address_link ? $address_link : 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $map_lat . ',' . $map_lng ) ); ?>"
				role="img"
				aria-label="<?php esc_attr_e( 'Brooklyn Beauty Lounge location map', 'brooklyn-beauty' ); ?>"
			></div>
			</div>
		</div>
	</div>
</section>
