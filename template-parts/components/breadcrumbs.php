<?php
/**
 * Reusable breadcrumbs partial.
 *
 * Accepts an array of breadcrumb items via $args['items'].
 * Each item: [ 'label' => string, 'url' => string|null ]
 * The last item is rendered as the current page (no link).
 *
 * @package Brooklyn_Beauty
 */

$items = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();

if ( empty( $items ) ) {
	return;
}

$last_index = count( $items ) - 1;
?>
<nav class="bb-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'brooklyn-beauty' ); ?>"
	itemscope itemtype="https://schema.org/BreadcrumbList">
	<?php foreach ( $items as $index => $item ) : ?>
		<?php $is_last = $index === $last_index; ?>
		<?php $position = $index + 1; ?>

		<?php if ( $is_last ) : ?>
			<span class="bb-breadcrumbs__current" aria-current="page"
				itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<span itemprop="name"><?php echo esc_html( $item['label'] ); ?></span>
				<meta itemprop="position" content="<?php echo esc_attr( $position ); ?>">
			</span>
		<?php else : ?>
			<a class="bb-breadcrumbs__link" href="<?php echo esc_url( $item['url'] ); ?>"
				itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<span itemprop="name"><?php echo esc_html( $item['label'] ); ?></span>
				<meta itemprop="item" content="<?php echo esc_url( $item['url'] ); ?>">
				<meta itemprop="position" content="<?php echo esc_attr( $position ); ?>">
			</a>
			<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
		<?php endif; ?>
	<?php endforeach; ?>
</nav>
