<?php
/**
 * Blog posts block in services-style layout with tabs.
 *
 * @package Brooklyn_Beauty
 */

$blog_categories = array(
	array(
		'slug'  => 'all-posts',
		'label' => __( 'all', 'brooklyn-beauty' ),
	),
);

$page_id        = (int) get_queried_object_id();
$selected_category_ids = array();

if ( function_exists( 'get_field' ) && $page_id > 0 ) {
	$acf_selected_categories = get_field( 'blog_tab_categories', $page_id );
	if ( is_array( $acf_selected_categories ) ) {
		$selected_category_ids = array_values( array_filter( array_map( 'intval', $acf_selected_categories ) ) );
	}
}

if ( ! empty( $selected_category_ids ) ) {
	$category_terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
			'include'    => $selected_category_ids,
			'orderby'    => 'include',
		)
	);
} else {
	$category_terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
}

if ( ! is_wp_error( $category_terms ) && ! empty( $category_terms ) ) {
	foreach ( $category_terms as $category_term ) {
		$blog_categories[] = array(
			'slug'  => $category_term->slug,
			'label' => $category_term->name,
		);
	}
}

$current_page_url = get_permalink( $page_id );

$active_tab = isset( $_GET['category'] ) ? sanitize_title( wp_unslash( (string) $_GET['category'] ) ) : 'all-posts';

$valid_slugs = array_column( $blog_categories, 'slug' );
if ( ! in_array( $active_tab, $valid_slugs, true ) ) {
	$active_tab = 'all-posts';
}

$current_paged = max(
	1,
	(int) get_query_var( 'paged' ),
	(int) get_query_var( 'page' ),
	isset( $_GET['paged'] ) ? absint( wp_unslash( $_GET['paged'] ) ) : 0
);

$pagination_base_url = 'all-posts' === $active_tab
	? $current_page_url
	: add_query_arg( 'category', $active_tab, $current_page_url );

$blog_result = function_exists( 'brooklyn_beauty_get_blog_cards_markup' )
	? brooklyn_beauty_get_blog_cards_markup( $active_tab, $current_paged, $pagination_base_url )
	: array( 'cards' => '', 'pagination' => '' );
$blog_cards_html    = isset( $blog_result['cards'] ) ? $blog_result['cards'] : '';
$blog_pagination_html = isset( $blog_result['pagination'] ) ? $blog_result['pagination'] : '';
?>
<section class="bb-blog-posts-section" id="blog-posts" data-blog-page-url="<?php echo esc_url( $current_page_url ); ?>">
	<div class="bb-container">
		<ul class="bb-blog-filters" aria-label="<?php esc_attr_e( 'Blog categories', 'brooklyn-beauty' ); ?>">
			<?php foreach ( $blog_categories as $blog_category ) :
				$is_active = $blog_category['slug'] === $active_tab;
				$tab_url   = 'all-posts' === $blog_category['slug']
					? $current_page_url
					: add_query_arg( 'category', $blog_category['slug'], $current_page_url );
			?>
				<li class="bb-blog-filters__item<?php echo $is_active ? ' is-active' : ''; ?>">
					<a class="bb-blog-filters__button" href="<?php echo esc_url( $tab_url ); ?>" data-category="<?php echo esc_attr( $blog_category['slug'] ); ?>" aria-current="<?php echo $is_active ? 'true' : 'false'; ?>">
						<?php echo esc_html( $blog_category['label'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
			<span class="bb-blog-filters__indicator" aria-hidden="true"></span>
		</ul>

		<div class="bb-blog-cards" aria-live="polite" aria-busy="false">
			<?php echo wp_kses_post( $blog_cards_html ); ?>
		</div>

		<nav class="bb-blog-posts__pagination" aria-label="<?php esc_attr_e( 'Blog pages', 'brooklyn-beauty' ); ?>">
			<?php echo wp_kses_post( $blog_pagination_html ); ?>
		</nav>
	</div>
</section>
