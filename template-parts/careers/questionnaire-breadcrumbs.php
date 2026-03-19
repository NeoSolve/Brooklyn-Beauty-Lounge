<?php
/**
 * Career questionnaire breadcrumbs.
 *
 * @package Brooklyn_Beauty
 */

$page_id    = (int) get_queried_object_id();
$page_title = $page_id > 0 ? get_the_title( $page_id ) : __( 'Career questionnaire', 'brooklyn-beauty' );
?>
<section class="bb-career-questionnaire-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'brooklyn-beauty' ); ?>">
	<div class="bb-container">
		<?php
		get_template_part(
			'template-parts/components/breadcrumbs',
			null,
			array(
				'items' => array(
					array(
						'label' => __( 'Home', 'brooklyn-beauty' ),
						'url'   => home_url( '/' ),
					),
					array( 'label' => $page_title ),
				),
			)
		);
		?>
	</div>
</section>
