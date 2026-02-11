<?php
/**
 * Main template fallback
 *
 * @package Brooklyn_Beauty
 */

get_header();
?>

<main class="bb-container bb-section">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
			<?php
		endwhile;
	else :
		?>
		<p><?php esc_html_e( 'Nothing found.', 'brooklyn-beauty' ); ?></p>
		<?php
	endif;
	?>
</main>

<?php
get_footer();
