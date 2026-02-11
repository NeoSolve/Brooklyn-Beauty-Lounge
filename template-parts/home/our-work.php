<?php
/**
 * Our Work block (gallery placeholder)
 *
 * @package Brooklyn_Beauty
 */
$section_title = get_theme_mod( 'bb_work_title', __( 'Our Work', 'brooklyn-beauty' ) );
$gallery_ids = get_theme_mod( 'bb_work_gallery', array() );
if ( ! is_array( $gallery_ids ) ) {
	$gallery_ids = array();
}
?>
<section class="bb-section" id="our-work">
	<div class="bb-container">
		<?php if ( $section_title ) : ?>
			<h2 class="bb-section__title"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>
		<div class="bb-work">
			<?php
			if ( ! empty( $gallery_ids ) ) :
				foreach ( array_slice( $gallery_ids, 0, 6 ) as $img_id ) :
					$img = wp_get_attachment_image( $img_id, 'medium_large' );
					if ( $img ) :
						?>
						<div class="bb-work__item"><?php echo $img; ?></div>
						<?php
					endif;
				endforeach;
			else :
				for ( $i = 1; $i <= 6; $i++ ) :
					?>
					<div class="bb-work__item"></div>
					<?php
				endfor;
			endif;
			?>
		</div>
	</div>
</section>
