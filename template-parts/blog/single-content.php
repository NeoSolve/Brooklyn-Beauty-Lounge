<?php
/**
 * Single post content section.
 *
 * @package Brooklyn_Beauty
 */

$post_id = (int) get_the_ID();

if ( $post_id <= 0 ) {
	return;
}

$author_id     = (int) get_post_field( 'post_author', $post_id );
$author_name   = (string) get_the_author_meta( 'display_name', $author_id );
$author_role   = __( 'Author', 'brooklyn-beauty' );
$author_avatar = get_avatar( $author_id, 88, '', $author_name, array( 'class' => 'bb-blog-single-content__author-avatar' ) );
$author_note   = '';

if ( function_exists( 'get_field' ) ) {
	$acf_author_name = trim( (string) get_field( 'single_post_hero_author_name', $post_id ) );
	if ( '' !== $acf_author_name ) {
		$author_name = $acf_author_name;
	}

	$acf_author_role = trim( (string) get_field( 'single_post_hero_author_role', $post_id ) );
	if ( '' !== $acf_author_role ) {
		$author_role = $acf_author_role;
	}

	$acf_author_avatar = get_field( 'single_post_hero_author_avatar', $post_id );
	if ( is_numeric( $acf_author_avatar ) ) {
		$acf_author_avatar_id = (int) $acf_author_avatar;
		$acf_avatar_markup    = wp_get_attachment_image(
			$acf_author_avatar_id,
			'thumbnail',
			false,
			array(
				'class'    => 'bb-blog-single-content__author-avatar',
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);

		if ( is_string( $acf_avatar_markup ) && '' !== trim( $acf_avatar_markup ) ) {
			$author_avatar = $acf_avatar_markup;
		}
	}

	$acf_author_note = trim( (string) get_field( 'single_post_author_note', $post_id ) );
	if ( '' !== $acf_author_note ) {
		$author_note = $acf_author_note;
	}
}

$raw_post_content = (string) get_post_field( 'post_content', $post_id );
$post_content     = apply_filters( 'the_content', $raw_post_content );
$toc_items        = array();
$slug_occurrences = array();

$post_content = preg_replace_callback(
	'/<h2\b([^>]*)>(.*?)<\/h2>/is',
	static function ( $matches ) use ( &$toc_items, &$slug_occurrences ) {
		$attributes   = isset( $matches[1] ) ? (string) $matches[1] : '';
		$heading_html = isset( $matches[2] ) ? (string) $matches[2] : '';
		$heading_text = trim( wp_strip_all_tags( $heading_html ) );

		if ( '' === $heading_text ) {
			return (string) $matches[0];
		}

		$section_id = '';
		if ( preg_match( '/\sid=(["\'])(.*?)\1/i', $attributes, $id_match ) ) {
			$section_id = trim( (string) $id_match[2] );
		}

		if ( '' === $section_id ) {
			$base_id = sanitize_title( $heading_text );
			if ( '' === $base_id ) {
				$base_id = 'section';
			}

			if ( ! isset( $slug_occurrences[ $base_id ] ) ) {
				$slug_occurrences[ $base_id ] = 0;
			}
			$slug_occurrences[ $base_id ]++;

			$section_id = $base_id;
			if ( $slug_occurrences[ $base_id ] > 1 ) {
				$section_id .= '-' . $slug_occurrences[ $base_id ];
			}

			$attributes = rtrim( $attributes ) . ' id="' . esc_attr( $section_id ) . '"';
		}

		$toc_items[] = array(
			'id'    => $section_id,
			'title' => $heading_text,
		);

		return '<h2' . $attributes . '>' . $heading_html . '</h2>';
	},
	$post_content
);

$has_author_note = '' !== trim( $author_note );
$has_toc         = ! empty( $toc_items );
$layout_classes  = array( 'bb-blog-single-content__layout' );

if ( ! $has_author_note ) {
	$layout_classes[] = 'bb-blog-single-content__layout--no-note';
}

if ( ! $has_toc ) {
	$layout_classes[] = 'bb-blog-single-content__layout--no-toc';
}
?>
<section class="bb-blog-single-content" aria-label="<?php esc_attr_e( 'Article content', 'brooklyn-beauty' ); ?>">
	<div class="bb-container">
		<div class="<?php echo esc_attr( implode( ' ', $layout_classes ) ); ?>">
			<?php if ( $has_author_note ) : ?>
				<aside class="bb-blog-single-content__author-note" aria-label="<?php esc_attr_e( 'Author note', 'brooklyn-beauty' ); ?>">
					<p class="bb-blog-single-content__author-note-text"><?php echo esc_html( $author_note ); ?></p>
					<div class="bb-blog-single-content__author">
						<?php if ( '' !== trim( (string) $author_avatar ) ) : ?>
							<?php echo $author_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
						<div class="bb-blog-single-content__author-meta">
							<p class="bb-blog-single-content__author-name"><?php echo esc_html( $author_name ); ?></p>
							<p class="bb-blog-single-content__author-role"><?php echo esc_html( $author_role ); ?></p>
						</div>
					</div>
				</aside>
			<?php endif; ?>

			<article class="bb-blog-single-content__article">
				<?php echo $post_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</article>

			<?php if ( $has_toc ) : ?>
				<aside class="bb-blog-single-content__toc" aria-label="<?php esc_attr_e( 'Table of contents', 'brooklyn-beauty' ); ?>">
					<p class="bb-blog-single-content__toc-title"><?php esc_html_e( 'Content', 'brooklyn-beauty' ); ?></p>
					<ul class="bb-blog-single-content__toc-list">
						<?php foreach ( $toc_items as $index => $toc_item ) : ?>
							<li class="bb-blog-single-content__toc-item<?php echo 0 === $index ? ' is-active' : ''; ?>">
								<a class="bb-blog-single-content__toc-link<?php echo 0 === $index ? ' is-active' : ''; ?>" href="#<?php echo esc_attr( $toc_item['id'] ); ?>">
									<?php echo esc_html( $toc_item['title'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
