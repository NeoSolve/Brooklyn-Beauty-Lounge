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
$post_url      = (string) get_permalink( $post_id );
$post_title    = wp_strip_all_tags( (string) get_the_title( $post_id ) );
$share_label   = __( 'Share Article to:', 'brooklyn-beauty' );
$share_items   = array();

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

	$acf_share_label = trim( (string) get_field( 'share_article_label', 'option' ) );
	if ( '' !== $acf_share_label ) {
		$share_label = $acf_share_label;
	}

	$acf_share_items = get_field( 'share_article_items', 'option' );
	if ( is_array( $acf_share_items ) ) {
		foreach ( $acf_share_items as $acf_share_item ) {
			$share_type  = isset( $acf_share_item['type'] ) ? sanitize_key( (string) $acf_share_item['type'] ) : '';
			$icon_image  = isset( $acf_share_item['icon_image'] ) ? (int) $acf_share_item['icon_image'] : 0;
			$custom_url  = isset( $acf_share_item['custom_url'] ) ? trim( (string) $acf_share_item['custom_url'] ) : '';
			$aria_label  = isset( $acf_share_item['aria_label'] ) ? trim( (string) $acf_share_item['aria_label'] ) : '';
			$share_url   = '';
			$is_copy     = false;
			$is_external = true;

			switch ( $share_type ) {
				case 'copy':
					$share_url   = $post_url;
					$is_copy     = true;
					$is_external = false;
					break;
				case 'facebook':
					$share_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $post_url );
					break;
				case 'x':
					$share_url = 'https://twitter.com/intent/tweet?url=' . rawurlencode( $post_url ) . '&text=' . rawurlencode( $post_title );
					break;
				case 'whatsapp':
					$share_url = 'https://api.whatsapp.com/send?text=' . rawurlencode( trim( $post_title . ' ' . $post_url ) );
					break;
				case 'custom':
					if ( '' !== $custom_url ) {
						$share_url = str_replace(
							array( '{url}', '{title}' ),
							array( rawurlencode( $post_url ), rawurlencode( $post_title ) ),
							$custom_url
						);
					}
					break;
			}

			if ( '' === $share_type || '' === $share_url || $icon_image <= 0 ) {
				continue;
			}

			if ( '' === $aria_label ) {
				$labels = array(
					'copy'     => __( 'Copy link', 'brooklyn-beauty' ),
					'facebook' => __( 'Share on Facebook', 'brooklyn-beauty' ),
					'x'        => __( 'Share on X', 'brooklyn-beauty' ),
					'whatsapp' => __( 'Share on WhatsApp', 'brooklyn-beauty' ),
					'custom'   => __( 'Share article', 'brooklyn-beauty' ),
				);
				$aria_label = isset( $labels[ $share_type ] ) ? $labels[ $share_type ] : __( 'Share article', 'brooklyn-beauty' );
			}

			$share_items[] = array(
				'url'         => $share_url,
				'icon_image'  => $icon_image,
				'aria_label'  => $aria_label,
				'is_copy'     => $is_copy,
				'is_external' => $is_external,
			);
		}
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
$has_share       = ! empty( $share_items );
$has_sidebar     = $has_toc || $has_share;
$layout_classes  = array( 'bb-blog-single-content__layout' );

if ( ! $has_author_note ) {
	$layout_classes[] = 'bb-blog-single-content__layout--no-note';
}

if ( ! $has_sidebar ) {
	$layout_classes[] = 'bb-blog-single-content__layout--no-sidebar';
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

			<?php if ( $has_sidebar ) : ?>
				<div class="bb-blog-single-content__sidebar">
					<?php if ( $has_toc ) : ?>
						<aside class="bb-blog-single-content__toc" aria-label="<?php esc_attr_e( 'Table of contents', 'brooklyn-beauty' ); ?>">
							<div class="bb-blog-single-content__toc-progress" aria-hidden="true">
								<span class="bb-blog-single-content__toc-progress-fill"></span>
							</div>
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

					<?php if ( $has_share ) : ?>
						<aside class="bb-blog-single-content__share" aria-label="<?php esc_attr_e( 'Share article', 'brooklyn-beauty' ); ?>">
							<p class="bb-blog-single-content__share-label"><?php echo esc_html( $share_label ); ?></p>
							<ul class="bb-blog-single-content__share-items">
								<?php foreach ( $share_items as $share_item ) : ?>
									<li class="bb-blog-single-content__share-item">
										<a
											class="bb-blog-single-content__share-link"
											href="<?php echo esc_url( $share_item['url'] ); ?>"
											aria-label="<?php echo esc_attr( $share_item['aria_label'] ); ?>"
											<?php if ( $share_item['is_external'] ) : ?>
												target="_blank" rel="nofollow noopener noreferrer"
											<?php endif; ?>
											<?php if ( $share_item['is_copy'] ) : ?>
												data-share-action="copy"
												data-share-url="<?php echo esc_attr( $share_item['url'] ); ?>"
											<?php endif; ?>
										>
											<?php
											echo wp_get_attachment_image(
												(int) $share_item['icon_image'],
												'thumbnail',
												false,
												array(
													'class'    => 'bb-blog-single-content__share-icon',
													'alt'      => '',
													'loading'  => 'lazy',
													'decoding' => 'async',
												)
											); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</aside>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
