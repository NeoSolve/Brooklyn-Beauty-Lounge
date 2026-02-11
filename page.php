<?php
/**
 * Default page template
 *
 * @package Brooklyn_Beauty
 */

get_header();
?>

<main class="bb-container bb-section">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h1 class="bb-section__title"><?php the_title(); ?></h1>
			<div class="bb-page-content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
