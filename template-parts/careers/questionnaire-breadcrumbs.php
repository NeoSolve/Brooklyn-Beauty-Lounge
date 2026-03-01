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
		<nav class="bb-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'brooklyn-beauty' ); ?>">
			<a class="bb-breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Home', 'brooklyn-beauty' ); ?>
			</a>
			<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
			<span class="bb-breadcrumbs__current" aria-current="page"><?php echo esc_html( $page_title ); ?></span>
		</nav>
	</div>
</section>
