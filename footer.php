<?php
/**
 * Footer template
 *
 * @package Brooklyn_Beauty
 */
?>
</main><!-- #content -->

<footer class="bb-footer">
	<div class="bb-container bb-footer__inner">
		<div class="bb-footer__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<strong><?php bloginfo( 'name' ); ?></strong>
			<?php endif; ?>
		</div>
		<?php
		wp_nav_menu( array(
			'theme_location' => 'footer',
			'container'      => 'nav',
			'container_class' => 'bb-footer__nav',
			'menu_class'     => '',
			'depth'          => 1,
		) );
		?>
		<div class="bb-footer__copy">
			&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'brooklyn-beauty' ); ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
