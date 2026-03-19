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

$items      = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
$class_name = isset( $args['class_name'] ) && is_string( $args['class_name'] ) ? trim( $args['class_name'] ) : '';

if ( empty( $items ) ) {
	return;
}

$last_index   = count( $items ) - 1;
$nav_classes  = 'bb-breadcrumbs';
$current_url  = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
$schema_items = array();

if ( '' !== $class_name ) {
	$nav_classes .= ' ' . $class_name;
}

foreach ( $items as $index => $item ) {
	$label = isset( $item['label'] ) ? trim( (string) $item['label'] ) : '';
	if ( '' === $label ) {
		continue;
	}

	$item_url = isset( $item['url'] ) ? trim( (string) $item['url'] ) : '';
	if ( '' === $item_url && $index === $last_index ) {
		$item_url = $current_url;
	}

	$schema_list_item = array(
		'@type'    => 'ListItem',
		'position' => $index + 1,
		'name'     => $label,
	);

	if ( '' !== $item_url ) {
		$schema_list_item['item'] = esc_url_raw( $item_url );
	}

	$schema_items[] = $schema_list_item;
}
?>
<nav class="<?php echo esc_attr( $nav_classes ); ?>" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'brooklyn-beauty' ); ?>">
	<?php foreach ( $items as $index => $item ) : ?>
		<?php $is_last = $index === $last_index; ?>

		<?php if ( $is_last ) : ?>
			<span class="bb-breadcrumbs__current" aria-current="page"><?php echo esc_html( $item['label'] ); ?></span>
		<?php else : ?>
			<a class="bb-breadcrumbs__link" href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<span class="bb-breadcrumbs__separator" aria-hidden="true"></span>
		<?php endif; ?>
	<?php endforeach; ?>
</nav>
<?php if ( ! empty( $schema_items ) ) : ?>
	<script type="application/ld+json">
		<?php
		echo wp_json_encode(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $schema_items,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		);
		?>
	</script>
<?php endif; ?>
