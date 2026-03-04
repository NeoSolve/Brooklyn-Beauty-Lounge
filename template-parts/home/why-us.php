<?php
/**
 * Why us block
 *
 * @package Brooklyn_Beauty
 */
$section_title = '';
$intro_text    = '';
$stats         = array_fill(
	0,
	4,
	array(
		'value' => '',
		'label' => '',
	)
);
$items         = array_fill(
	0,
	4,
	array(
		'title' => '',
		'text'  => '',
	)
);

if ( function_exists( 'get_field' ) ) {
	$front_page_id = (int) get_option( 'page_on_front' );
	$field_post_id = $front_page_id > 0 ? $front_page_id : get_queried_object_id();

	$section_title = trim( (string) get_field( 'why_us_title', $field_post_id ) );

	$intro_text = trim( (string) get_field( 'why_us_intro', $field_post_id ) );

	$acf_stats = get_field( 'why_us_stats', $field_post_id );
	if ( is_array( $acf_stats ) ) {
		for ( $index = 0; $index < 4; $index++ ) {
			if ( ! isset( $acf_stats[ $index ] ) || ! is_array( $acf_stats[ $index ] ) ) {
				continue;
			}

			$stat_value = isset( $acf_stats[ $index ]['value'] ) ? trim( (string) $acf_stats[ $index ]['value'] ) : '';
			$stat_label = isset( $acf_stats[ $index ]['label'] ) ? trim( (string) $acf_stats[ $index ]['label'] ) : '';

			if ( '' !== $stat_value ) {
				$stats[ $index ]['value'] = $stat_value;
			}

			if ( '' !== $stat_label ) {
				$stats[ $index ]['label'] = $stat_label;
			}
		}
	}

	$acf_cards = get_field( 'why_us_cards', $field_post_id );
	if ( is_array( $acf_cards ) ) {
		for ( $index = 0; $index < 4; $index++ ) {
			if ( ! isset( $acf_cards[ $index ] ) || ! is_array( $acf_cards[ $index ] ) ) {
				continue;
			}

			$card_title = isset( $acf_cards[ $index ]['title'] ) ? trim( (string) $acf_cards[ $index ]['title'] ) : '';
			$card_text  = isset( $acf_cards[ $index ]['text'] ) ? trim( (string) $acf_cards[ $index ]['text'] ) : '';

			$items[ $index ]['title'] = $card_title;
			$items[ $index ]['text']  = $card_text;
		}
	}
}
?>
<section class="bb-section bb-section--alt bb-why-us-section" id="why-us">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-why-us__heading"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<div class="bb-why-us" data-why-us>
			<?php if ( $intro_text ) : ?>
				<p class="bb-why-us__intro"><?php echo esc_html( $intro_text ); ?></p>
			<?php endif; ?>
			<div class="bb-why-us__stat bb-why-us__stat--left-top" data-why-us-item>
				<p class="bb-why-us__value"><?php echo esc_html( $stats[0]['value'] ); ?></p>
				<p class="bb-why-us__label"><?php echo esc_html( $stats[0]['label'] ); ?></p>
			</div>
			<div class="bb-why-us__card bb-why-us__card--top-left" data-why-us-item>
				<h3 class="bb-why-us__card-title"><?php echo esc_html( $items[0]['title'] ); ?></h3>
				<p class="bb-why-us__card-text"><?php echo esc_html( $items[0]['text'] ); ?></p>
			</div>
			<div class="bb-why-us__center-stats" data-why-us-item>
				<div class="bb-why-us__stat bb-why-us__stat--center-top">
					<p class="bb-why-us__value"><?php echo esc_html( $stats[2]['value'] ); ?></p>
					<p class="bb-why-us__label"><?php echo esc_html( $stats[2]['label'] ); ?></p>
				</div>
				<div class="bb-why-us__stat bb-why-us__stat--center-bottom">
					<p class="bb-why-us__value"><?php echo esc_html( $stats[3]['value'] ); ?></p>
					<p class="bb-why-us__label"><?php echo esc_html( $stats[3]['label'] ); ?></p>
				</div>
			</div>
			<div class="bb-why-us__stat bb-why-us__stat--right-top" data-why-us-item>
				<p class="bb-why-us__value"><?php echo esc_html( $stats[1]['value'] ); ?></p>
				<p class="bb-why-us__label"><?php echo esc_html( $stats[1]['label'] ); ?></p>
			</div>
			<div class="bb-why-us__card bb-why-us__card--top-right" data-why-us-item>
				<h3 class="bb-why-us__card-title"><?php echo esc_html( $items[1]['title'] ); ?></h3>
				<p class="bb-why-us__card-text"><?php echo esc_html( $items[1]['text'] ); ?></p>
			</div>
			<div class="bb-why-us__bottom-cards" data-why-us-item>
				<div class="bb-why-us__card bb-why-us__card--bottom-left">
					<h3 class="bb-why-us__card-title"><?php echo esc_html( $items[2]['title'] ); ?></h3>
					<p class="bb-why-us__card-text"><?php echo esc_html( $items[2]['text'] ); ?></p>
				</div>
				<div class="bb-why-us__card bb-why-us__card--bottom-right">
					<h3 class="bb-why-us__card-title"><?php echo esc_html( $items[3]['title'] ); ?></h3>
					<p class="bb-why-us__card-text"><?php echo esc_html( $items[3]['text'] ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>
