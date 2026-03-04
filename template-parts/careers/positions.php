<?php
/**
 * Careers positions block (vacancies list).
 *
 * @package Brooklyn_Beauty
 */

$page_id    = (int) get_queried_object_id();
$positions  = array();
$apply_text = __( 'apply now', 'brooklyn-beauty' );

if ( function_exists( 'get_field' ) ) {
	$raw = get_field( 'careers_positions', $page_id );
	if ( is_array( $raw ) ) {
		foreach ( $raw as $row ) {
			$title = isset( $row['title'] ) ? trim( (string) $row['title'] ) : '';
			if ( '' !== $title ) {
				$positions[] = array(
					'title'      => $title,
					'apply_link' => isset( $row['apply_link'] ) ? trim( (string) $row['apply_link'] ) : '',
				);
			}
		}
	}
}

$section_title   = __( 'positions', 'brooklyn-beauty' );
$section_decor   = __( 'apply', 'brooklyn-beauty' );

if ( empty( $positions ) ) {
	return;
}

?>
<section class="bb-careers-positions" aria-labelledby="bb-careers-positions-title">
	<div class="bb-container bb-careers-positions__inner">
		<header class="bb-careers-positions__header">
			<span class="bb-careers-positions__decor t-decor"><?php echo esc_html( $section_decor ); ?></span>
			<h2 class="bb-careers-positions__title" id="bb-careers-positions-title"><?php echo esc_html( $section_title ); ?></h2>
		</header>

		<div class="bb-careers-positions__grid">
			<ul class="bb-careers-positions__list">
				<?php
				foreach ( $positions as $index => $position ) :
					$num   = str_pad( (string) ( $index + 1 ), 3, '0', STR_PAD_LEFT );
					$link  = $position['apply_link'];
					$title = $position['title'];
					?>
					<li class="bb-careers-positions__item">
						<div class="bb-careers-positions__cell">
							<span class="bb-careers-positions__num" aria-hidden="true"><?php echo esc_html( $num ); ?></span>
							<span class="bb-careers-positions__job-title"><?php echo esc_html( $title ); ?></span>
						</div>
						<?php if ( '' !== $link ) : ?>
							<a class="bb-careers-positions__apply btn btn--small" href="<?php echo esc_url( $link ); ?>">
								<?php echo esc_html( $apply_text ); ?>
							</a>
						<?php else : ?>
							<span class="bb-careers-positions__apply bb-careers-positions__apply--disabled"><?php echo esc_html( $apply_text ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
