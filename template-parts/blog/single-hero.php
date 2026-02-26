<?php
/**
 * Single post hero section.
 *
 * @package Brooklyn_Beauty
 */

$post_id    = (int) get_the_ID();
$post_title = get_the_title( $post_id );

if ( $post_id <= 0 ) {
	return;
}

$author_id      = (int) get_post_field( 'post_author', $post_id );
$author_name    = (string) get_the_author_meta( 'display_name', $author_id );
$author_avatar  = get_avatar( $author_id, 88, '', $author_name, array( 'class' => 'bb-blog-single-hero__author-avatar' ) );
$author_role    = __( 'Author', 'brooklyn-beauty' );
$publish_date   = (string) get_the_date( 'd.m.Y', $post_id );
$featured_image = (string) get_the_post_thumbnail_url( $post_id, 'full' );
$image_alt      = $post_title;
$hero_title     = $post_title;

if ( has_post_thumbnail( $post_id ) ) {
	$image_alt_meta = get_post_meta( (int) get_post_thumbnail_id( $post_id ), '_wp_attachment_image_alt', true );
	if ( is_string( $image_alt_meta ) && '' !== trim( $image_alt_meta ) ) {
		$image_alt = $image_alt_meta;
	}
}

$blog_page_url = '';
$posts_page_id = (int) get_option( 'page_for_posts' );
if ( $posts_page_id > 0 ) {
	$blog_page_url = (string) get_permalink( $posts_page_id );
}
if ( '' === $blog_page_url ) {
	$blog_page_url = home_url( '/blog/' );
}

$post_excerpt = trim( (string) get_the_excerpt( $post_id ) );
$post_content = (string) get_post_field( 'post_content', $post_id );

$content_paragraphs = preg_split( '/\R{2,}/', trim( (string) wp_strip_all_tags( $post_content ) ) );
$content_paragraphs = array_values(
	array_filter(
		array_map(
			static function ( $paragraph ) {
				return trim( (string) preg_replace( '/\s+/', ' ', (string) $paragraph ) );
			},
			is_array( $content_paragraphs ) ? $content_paragraphs : array()
		)
	)
);

$intro_left  = $post_excerpt;
$intro_right = '';

if ( '' === $intro_left ) {
	$intro_left = isset( $content_paragraphs[0] ) ? (string) $content_paragraphs[0] : '';
}

if ( isset( $content_paragraphs[1] ) ) {
	$intro_right = (string) $content_paragraphs[1];
} elseif ( '' === $intro_left && isset( $content_paragraphs[0] ) ) {
	$intro_right = (string) $content_paragraphs[0];
}

if ( '' === $intro_right && '' !== $intro_left ) {
	$words = preg_split( '/\s+/', $intro_left );
	if ( is_array( $words ) && count( $words ) > 16 ) {
		$split_index = (int) ceil( count( $words ) / 2 );
		$intro_left  = trim( implode( ' ', array_slice( $words, 0, $split_index ) ) );
		$intro_right = trim( implode( ' ', array_slice( $words, $split_index ) ) );
	}
}

$categories = get_the_category( $post_id );

if ( function_exists( 'get_field' ) ) {
	$acf_hero_title = trim( (string) get_field( 'single_post_hero_title', $post_id ) );
	if ( '' !== $acf_hero_title ) {
		$hero_title = $acf_hero_title;
	}

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
				'class'   => 'bb-blog-single-hero__author-avatar',
				'loading' => 'lazy',
				'decoding' => 'async',
			)
		);

		if ( is_string( $acf_avatar_markup ) && '' !== trim( $acf_avatar_markup ) ) {
			$author_avatar = $acf_avatar_markup;
		}
	}

	$acf_date = trim( (string) get_field( 'single_post_hero_date', $post_id ) );
	if ( '' !== $acf_date ) {
		$publish_date = $acf_date;
	}

	$acf_intro_left = trim( (string) get_field( 'single_post_hero_intro_left', $post_id ) );
	if ( '' !== $acf_intro_left ) {
		$intro_left = $acf_intro_left;
	}

	$acf_intro_right = trim( (string) get_field( 'single_post_hero_intro_right', $post_id ) );
	if ( '' !== $acf_intro_right ) {
		$intro_right = $acf_intro_right;
	}

	$acf_hero_image = get_field( 'single_post_hero_image', $post_id );
	if ( is_numeric( $acf_hero_image ) ) {
		$acf_hero_image_id = (int) $acf_hero_image;
		$acf_image_url     = (string) wp_get_attachment_image_url( $acf_hero_image_id, 'full' );
		if ( '' !== $acf_image_url ) {
			$featured_image = $acf_image_url;
			$acf_alt_text   = get_post_meta( $acf_hero_image_id, '_wp_attachment_image_alt', true );
			if ( is_string( $acf_alt_text ) && '' !== trim( $acf_alt_text ) ) {
				$image_alt = $acf_alt_text;
			}
		}
	} elseif ( is_array( $acf_hero_image ) ) {
		if ( ! empty( $acf_hero_image['url'] ) ) {
			$featured_image = (string) $acf_hero_image['url'];
		}
		if ( ! empty( $acf_hero_image['alt'] ) && is_string( $acf_hero_image['alt'] ) ) {
			$image_alt = $acf_hero_image['alt'];
		}
	} elseif ( is_string( $acf_hero_image ) && '' !== trim( $acf_hero_image ) ) {
		$featured_image = $acf_hero_image;
	}
}
?>

<section class="bb-blog-single-hero" aria-labelledby="bb-blog-single-title">
	<div class="bb-container">
		<div class="bb-blog-single-hero__top">
			<nav class="bb-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'brooklyn-beauty' ); ?>">
				<a class="bb-breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Home', 'brooklyn-beauty' ); ?>
				</a>
				<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
				<a class="bb-breadcrumbs__link" href="<?php echo esc_url( $blog_page_url ); ?>">
					<?php esc_html_e( 'Blog', 'brooklyn-beauty' ); ?>
				</a>
				<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
				<span class="bb-breadcrumbs__current" aria-current="page"><?php echo esc_html( $hero_title ); ?></span>
			</nav>

			<?php if ( ! empty( $categories ) ) : ?>
				<ul class="bb-blog-single-hero__tags" aria-label="<?php esc_attr_e( 'Post categories', 'brooklyn-beauty' ); ?>">
					<?php foreach ( $categories as $category ) : ?>
						<li class="bb-blog-single-hero__tag"><?php echo esc_html( $category->name ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<h1 class="bb-blog-single-hero__title" id="bb-blog-single-title"><?php echo esc_html( $hero_title ); ?></h1>

		<div class="bb-blog-single-hero__intro-grid">
			<div class="bb-blog-single-hero__meta">
				<div class="bb-blog-single-hero__author">
					<?php if ( '' !== trim( (string) $author_avatar ) ) : ?>
						<?php echo $author_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
					<div class="bb-blog-single-hero__author-text">
						<p class="bb-blog-single-hero__author-name"><?php echo esc_html( $author_name ); ?></p>
						<p class="bb-blog-single-hero__author-role"><?php echo esc_html( $author_role ); ?></p>
					</div>
				</div>

				<?php if ( '' !== trim( $publish_date ) ) : ?>
					<p class="bb-blog-single-hero__date"><?php echo esc_html( $publish_date ); ?></p>
				<?php endif; ?>
			</div>

			<div class="bb-blog-single-hero__intro-texts">
				<?php if ( '' !== trim( $intro_left ) ) : ?>
					<p><?php echo esc_html( $intro_left ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== trim( $intro_right ) ) : ?>
					<p><?php echo esc_html( $intro_right ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="bb-blog-single-hero__media">
			<?php if ( '' !== $featured_image ) : ?>
				<img src="<?php echo esc_url( $featured_image ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" decoding="async">
			<?php else : ?>
				<div class="bb-blog-single-hero__media-fallback" aria-hidden="true"></div>
			<?php endif; ?>
		</div>
	</div>
</section>
