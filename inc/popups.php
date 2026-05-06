<?php
/**
 * Promo popup custom post type and rendering.
 *
 * @package Brooklyn_Beauty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default popup description text.
 *
 * @return string
 */
function brooklyn_beauty_get_default_popup_description_text() {
	return 'Limited time offer valid Oct 21 - Nov 21. Includes full balayage and blowout with newest stylist. Pre-Book now and use any time before Nov 21.';
}

/**
 * Register Promo Popups post type.
 *
 * @return void
 */
function brooklyn_beauty_register_popup_post_type() {
	register_post_type(
		'bb_popup',
		array(
			'labels'              => array(
				'name'               => __( 'Promo Popups', 'brooklyn-beauty' ),
				'singular_name'      => __( 'Promo Popup', 'brooklyn-beauty' ),
				'menu_name'          => __( 'Promo Popups', 'brooklyn-beauty' ),
				'add_new'            => __( 'Add New', 'brooklyn-beauty' ),
				'add_new_item'       => __( 'Add New Popup', 'brooklyn-beauty' ),
				'edit_item'          => __( 'Edit Popup', 'brooklyn-beauty' ),
				'new_item'           => __( 'New Popup', 'brooklyn-beauty' ),
				'view_item'          => __( 'View Popup', 'brooklyn-beauty' ),
				'search_items'       => __( 'Search Popups', 'brooklyn-beauty' ),
				'not_found'          => __( 'No popups found.', 'brooklyn-beauty' ),
				'not_found_in_trash' => __( 'No popups found in Trash.', 'brooklyn-beauty' ),
				'all_items'          => __( 'All Popups', 'brooklyn-beauty' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'menu_icon'           => 'dashicons-images-alt2',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'brooklyn_beauty_register_popup_post_type' );

/**
 * Register popup settings metabox.
 *
 * @return void
 */
function brooklyn_beauty_register_popup_metabox() {
	add_meta_box(
		'brooklyn-beauty-popup-settings',
		__( 'Popup Settings', 'brooklyn-beauty' ),
		'brooklyn_beauty_render_popup_metabox',
		'bb_popup',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_bb_popup', 'brooklyn_beauty_register_popup_metabox' );

/**
 * Render popup settings metabox.
 *
 * @param WP_Post $post Current popup post.
 *
 * @return void
 */
function brooklyn_beauty_render_popup_metabox( $post ) {
	$is_active          = (bool) get_post_meta( $post->ID, '_bb_popup_is_active', true );
	$start_date         = (string) get_post_meta( $post->ID, '_bb_popup_start_date', true );
	$end_date           = (string) get_post_meta( $post->ID, '_bb_popup_end_date', true );
	$desktop_image_id   = (int) get_post_meta( $post->ID, '_bb_popup_desktop_image_id', true );
	$mobile_image_id    = (int) get_post_meta( $post->ID, '_bb_popup_mobile_image_id', true );
	$description_text   = (string) get_post_meta( $post->ID, '_bb_popup_description_text', true );
	$button_text        = (string) get_post_meta( $post->ID, '_bb_popup_button_text', true );
	$button_url         = (string) get_post_meta( $post->ID, '_bb_popup_button_url', true );
	$show_once          = (bool) get_post_meta( $post->ID, '_bb_popup_show_once', true );
	$delay_seconds      = (int) get_post_meta( $post->ID, '_bb_popup_delay_seconds', true );
	$next_delay_seconds = (int) get_post_meta( $post->ID, '_bb_popup_next_delay_seconds', true );
	$sequence_order     = (int) $post->menu_order;
	$desktop_preview    = '';
	$mobile_preview     = '';

	if ( $desktop_image_id <= 0 ) {
		$desktop_image_id = (int) get_post_meta( $post->ID, 'bb_popup_desktop_image_id', true );
	}

	if ( $mobile_image_id <= 0 ) {
		$mobile_image_id = (int) get_post_meta( $post->ID, 'bb_popup_mobile_image_id', true );
	}

	if ( $desktop_image_id > 0 ) {
		$desktop_preview = (string) wp_get_attachment_image_url( $desktop_image_id, 'medium' );
	}

	if ( $mobile_image_id > 0 ) {
		$mobile_preview = (string) wp_get_attachment_image_url( $mobile_image_id, 'medium' );
	}

	if ( '' === trim( $description_text ) ) {
		$description_text = brooklyn_beauty_get_default_popup_description_text();
	}

	wp_nonce_field( 'brooklyn_beauty_popup_settings', 'brooklyn_beauty_popup_nonce' );
	?>
	<p>
		<label>
			<input type="checkbox" name="bb_popup_is_active" value="1" <?php checked( $is_active ); ?> />
			<?php esc_html_e( 'Enable this popup on the website', 'brooklyn-beauty' ); ?>
		</label>
	</p>

	<p>
		<label for="bb-popup-start-date"><strong><?php esc_html_e( 'Start date', 'brooklyn-beauty' ); ?></strong></label><br />
		<input id="bb-popup-start-date" type="date" name="bb_popup_start_date" value="<?php echo esc_attr( $start_date ); ?>" />
	</p>

	<p>
		<label for="bb-popup-end-date"><strong><?php esc_html_e( 'End date', 'brooklyn-beauty' ); ?></strong></label><br />
		<input id="bb-popup-end-date" type="date" name="bb_popup_end_date" value="<?php echo esc_attr( $end_date ); ?>" />
	</p>

	<p>
		<label for="bb-popup-delay-seconds"><strong><?php esc_html_e( 'Display delay (seconds)', 'brooklyn-beauty' ); ?></strong></label><br />
		<input id="bb-popup-delay-seconds" type="number" min="0" step="1" name="bb_popup_delay_seconds" value="<?php echo esc_attr( (string) $delay_seconds ); ?>" />
	</p>

	<p>
		<label for="bb-popup-next-delay-seconds"><strong><?php esc_html_e( 'Delay before next popup after closing this one (seconds)', 'brooklyn-beauty' ); ?></strong></label><br />
		<input id="bb-popup-next-delay-seconds" type="number" min="0" step="1" name="bb_popup_next_delay_seconds" value="<?php echo esc_attr( (string) $next_delay_seconds ); ?>" />
	</p>

	<p>
		<label>
			<input type="checkbox" name="bb_popup_show_once" value="1" <?php checked( $show_once ); ?> />
			<?php esc_html_e( 'Show only once per visitor (localStorage)', 'brooklyn-beauty' ); ?>
		</label>
	</p>

	<p><?php esc_html_e( 'Display sequence uses popup order (menu_order).', 'brooklyn-beauty' ); ?></p>

	<p>
		<label for="bb-popup-sequence-order"><strong><?php esc_html_e( 'Sequence order', 'brooklyn-beauty' ); ?></strong></label><br />
		<input id="bb-popup-sequence-order" type="number" min="0" step="1" name="bb_popup_sequence_order" value="<?php echo esc_attr( (string) $sequence_order ); ?>" />
		<br />
		<small><?php esc_html_e( 'Lower number appears first in popup sequence.', 'brooklyn-beauty' ); ?></small>
	</p>

	<hr />

	<p>
		<label for="bb-popup-desktop-image-id"><strong><?php esc_html_e( 'Popup Image (Desktop)', 'brooklyn-beauty' ); ?></strong></label><br />
		<input id="bb-popup-desktop-image-id" type="hidden" name="bb_popup_desktop_image_id" value="<?php echo esc_attr( (string) $desktop_image_id ); ?>" />
		<button type="button" class="button" id="bb-popup-desktop-upload"><?php esc_html_e( 'Select image', 'brooklyn-beauty' ); ?></button>
		<button type="button" class="button" id="bb-popup-desktop-remove"><?php esc_html_e( 'Remove', 'brooklyn-beauty' ); ?></button>
	</p>

	<div id="bb-popup-desktop-preview-wrap" style="<?php echo '' === $desktop_preview ? 'display:none;' : ''; ?>">
		<img id="bb-popup-desktop-preview" src="<?php echo esc_url( $desktop_preview ); ?>" alt="" style="max-width: 220px; height: auto;" />
	</div>

	<p>
		<label for="bb-popup-mobile-image-id"><strong><?php esc_html_e( 'Popup Image (Mobile)', 'brooklyn-beauty' ); ?></strong></label><br />
		<input id="bb-popup-mobile-image-id" type="hidden" name="bb_popup_mobile_image_id" value="<?php echo esc_attr( (string) $mobile_image_id ); ?>" />
		<button type="button" class="button" id="bb-popup-mobile-upload"><?php esc_html_e( 'Select image', 'brooklyn-beauty' ); ?></button>
		<button type="button" class="button" id="bb-popup-mobile-remove"><?php esc_html_e( 'Remove', 'brooklyn-beauty' ); ?></button>
	</p>

	<div id="bb-popup-mobile-preview-wrap" style="<?php echo '' === $mobile_preview ? 'display:none;' : ''; ?>">
		<img id="bb-popup-mobile-preview" src="<?php echo esc_url( $mobile_preview ); ?>" alt="" style="max-width: 220px; height: auto;" />
	</div>

	<hr />

	<p>
		<label for="bb-popup-description-text"><strong><?php esc_html_e( 'Description text (above button)', 'brooklyn-beauty' ); ?></strong></label><br />
		<textarea id="bb-popup-description-text" class="widefat" rows="4" name="bb_popup_description_text"><?php echo esc_textarea( $description_text ); ?></textarea>
	</p>

	<p>
		<label for="bb-popup-button-text"><strong><?php esc_html_e( 'Button text', 'brooklyn-beauty' ); ?></strong></label><br />
		<input id="bb-popup-button-text" type="text" class="widefat" name="bb_popup_button_text" value="<?php echo esc_attr( $button_text ); ?>" placeholder="<?php esc_attr_e( 'book now', 'brooklyn-beauty' ); ?>" />
	</p>

	<p>
		<label for="bb-popup-button-url"><strong><?php esc_html_e( 'Button URL', 'brooklyn-beauty' ); ?></strong></label><br />
		<input id="bb-popup-button-url" type="url" class="widefat" name="bb_popup_button_url" value="<?php echo esc_attr( $button_url ); ?>" placeholder="https://example.com" />
	</p>

	<p><?php esc_html_e( 'Title is taken from popup title field.', 'brooklyn-beauty' ); ?></p>
	<?php
}

/**
 * Save popup settings metabox values.
 *
 * @param int $post_id Current post id.
 *
 * @return void
 */
function brooklyn_beauty_save_popup_metabox( $post_id ) {
	if ( ! isset( $_POST['brooklyn_beauty_popup_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['brooklyn_beauty_popup_nonce'] ) ), 'brooklyn_beauty_popup_settings' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$is_active = isset( $_POST['bb_popup_is_active'] ) ? '1' : '';
	update_post_meta( $post_id, '_bb_popup_is_active', $is_active );

	$show_once = isset( $_POST['bb_popup_show_once'] ) ? '1' : '';
	update_post_meta( $post_id, '_bb_popup_show_once', $show_once );

	$start_date = isset( $_POST['bb_popup_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_popup_start_date'] ) ) : '';
	$end_date   = isset( $_POST['bb_popup_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_popup_end_date'] ) ) : '';
	$start_date = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $start_date ) ? $start_date : '';
	$end_date   = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $end_date ) ? $end_date : '';

	update_post_meta( $post_id, '_bb_popup_start_date', $start_date );
	update_post_meta( $post_id, '_bb_popup_end_date', $end_date );

	$desktop_image_id = isset( $_POST['bb_popup_desktop_image_id'] ) ? (int) $_POST['bb_popup_desktop_image_id'] : 0;
	$mobile_image_id  = isset( $_POST['bb_popup_mobile_image_id'] ) ? (int) $_POST['bb_popup_mobile_image_id'] : 0;
	update_post_meta( $post_id, '_bb_popup_desktop_image_id', $desktop_image_id > 0 ? $desktop_image_id : '' );
	update_post_meta( $post_id, '_bb_popup_mobile_image_id', $mobile_image_id > 0 ? $mobile_image_id : '' );
	update_post_meta( $post_id, 'bb_popup_desktop_image_id', $desktop_image_id > 0 ? $desktop_image_id : '' );
	update_post_meta( $post_id, 'bb_popup_mobile_image_id', $mobile_image_id > 0 ? $mobile_image_id : '' );

	$description_text = isset( $_POST['bb_popup_description_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bb_popup_description_text'] ) ) : '';
	update_post_meta( $post_id, '_bb_popup_description_text', $description_text );

	$button_text = isset( $_POST['bb_popup_button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_popup_button_text'] ) ) : '';
	$button_url  = isset( $_POST['bb_popup_button_url'] ) ? esc_url_raw( wp_unslash( $_POST['bb_popup_button_url'] ) ) : '';
	update_post_meta( $post_id, '_bb_popup_button_text', $button_text );
	update_post_meta( $post_id, '_bb_popup_button_url', $button_url );

	$delay_seconds = isset( $_POST['bb_popup_delay_seconds'] ) ? (int) $_POST['bb_popup_delay_seconds'] : 0;
	if ( $delay_seconds < 0 ) {
		$delay_seconds = 0;
	}
	update_post_meta( $post_id, '_bb_popup_delay_seconds', $delay_seconds );

	$next_delay_seconds = isset( $_POST['bb_popup_next_delay_seconds'] ) ? (int) $_POST['bb_popup_next_delay_seconds'] : 0;
	if ( $next_delay_seconds < 0 ) {
		$next_delay_seconds = 0;
	}
	update_post_meta( $post_id, '_bb_popup_next_delay_seconds', $next_delay_seconds );

	$sequence_order = isset( $_POST['bb_popup_sequence_order'] ) ? (int) $_POST['bb_popup_sequence_order'] : 0;
	if ( $sequence_order < 0 ) {
		$sequence_order = 0;
	}

	if ( $sequence_order !== (int) get_post_field( 'menu_order', $post_id ) ) {
		remove_action( 'save_post_bb_popup', 'brooklyn_beauty_save_popup_metabox' );
		wp_update_post(
			array(
				'ID'         => $post_id,
				'menu_order' => $sequence_order,
			)
		);
		add_action( 'save_post_bb_popup', 'brooklyn_beauty_save_popup_metabox' );
	}
}
add_action( 'save_post_bb_popup', 'brooklyn_beauty_save_popup_metabox' );

/**
 * Enqueue popup admin media uploader behavior.
 *
 * @param string $hook_suffix Current admin page hook.
 *
 * @return void
 */
function brooklyn_beauty_popup_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'bb_popup' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_script( 'jquery' );
	wp_enqueue_media();
	wp_add_inline_script(
		'jquery',
		"jQuery(function ($) {
			function initImageSelector(config) {
				var mediaFrame;
				var idInput = $(config.idInputSelector);
				var uploadButton = $(config.uploadButtonSelector);
				var removeButton = $(config.removeButtonSelector);
				var previewWrap = $(config.previewWrapSelector);
				var preview = $(config.previewSelector);

				function hidePreview() {
					idInput.val('');
					preview.attr('src', '');
					previewWrap.hide();
				}

				uploadButton.on('click', function (event) {
					event.preventDefault();

					if (mediaFrame) {
						mediaFrame.open();
						return;
					}

					mediaFrame = wp.media({
						title: 'Select popup image',
						button: {
							text: 'Use image'
						},
						multiple: false
					});

					mediaFrame.on('select', function () {
						var attachment = mediaFrame.state().get('selection').first().toJSON();
						var previewUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

						idInput.val(attachment.id);
						preview.attr('src', previewUrl);
						previewWrap.show();
					});

					mediaFrame.open();
				});

				removeButton.on('click', function (event) {
					event.preventDefault();
					hidePreview();
				});
			}

			initImageSelector({
				idInputSelector: '#bb-popup-desktop-image-id',
				uploadButtonSelector: '#bb-popup-desktop-upload',
				removeButtonSelector: '#bb-popup-desktop-remove',
				previewWrapSelector: '#bb-popup-desktop-preview-wrap',
				previewSelector: '#bb-popup-desktop-preview'
			});

			initImageSelector({
				idInputSelector: '#bb-popup-mobile-image-id',
				uploadButtonSelector: '#bb-popup-mobile-upload',
				removeButtonSelector: '#bb-popup-mobile-remove',
				previewWrapSelector: '#bb-popup-mobile-preview-wrap',
				previewSelector: '#bb-popup-mobile-preview'
			});
		});"
	);
}
add_action( 'admin_enqueue_scripts', 'brooklyn_beauty_popup_admin_assets' );

/**
 * Return all active popup posts in display sequence.
 *
 * @return array<int, WP_Post>
 */
function brooklyn_beauty_get_active_popup_posts() {
	static $cached_popups = array();
	static $is_loaded    = false;

	if ( $is_loaded ) {
		return $cached_popups;
	}

	$is_loaded = true;
	$today     = (string) current_time( 'Y-m-d' );
	$candidates = get_posts(
		array(
			'post_type'      => 'bb_popup',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'meta_query'     => array(
				array(
					'key'     => '_bb_popup_is_active',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);

	foreach ( $candidates as $candidate ) {
		$start_date = (string) get_post_meta( $candidate->ID, '_bb_popup_start_date', true );
		$end_date   = (string) get_post_meta( $candidate->ID, '_bb_popup_end_date', true );

		if ( '' !== $start_date && $today < $start_date ) {
			continue;
		}

		if ( '' !== $end_date && $today > $end_date ) {
			continue;
		}

		$cached_popups[] = $candidate;
	}

	return $cached_popups;
}

/**
 * Backward compatible accessor for first active popup.
 *
 * @return WP_Post|null
 */
function brooklyn_beauty_get_active_popup_post() {
	$active_popups = brooklyn_beauty_get_active_popup_posts();
	return ! empty( $active_popups ) ? $active_popups[0] : null;
}

/**
 * Render active popups markup in footer.
 *
 * @return void
 */
function brooklyn_beauty_render_active_popups() {
	if ( is_admin() ) {
		return;
	}

	$popup_posts = brooklyn_beauty_get_active_popup_posts();
	if ( empty( $popup_posts ) ) {
		return;
	}

	foreach ( $popup_posts as $popup_index => $popup_post ) {
		$popup_id            = (int) $popup_post->ID;
		$title               = (string) get_the_title( $popup_post );
		$description_text    = (string) get_post_meta( $popup_id, '_bb_popup_description_text', true );
		$desktop_image_id    = (int) get_post_meta( $popup_id, '_bb_popup_desktop_image_id', true );
		$desktop_image_url   = $desktop_image_id > 0 ? (string) wp_get_attachment_image_url( $desktop_image_id, 'full' ) : '';
		$mobile_image_id     = (int) get_post_meta( $popup_id, '_bb_popup_mobile_image_id', true );
		$mobile_image_url    = $mobile_image_id > 0 ? (string) wp_get_attachment_image_url( $mobile_image_id, 'full' ) : '';
		$button_text         = (string) get_post_meta( $popup_id, '_bb_popup_button_text', true );
		$button_url          = (string) get_post_meta( $popup_id, '_bb_popup_button_url', true );
		$show_once           = (bool) get_post_meta( $popup_id, '_bb_popup_show_once', true );
		$delay_seconds       = (int) get_post_meta( $popup_id, '_bb_popup_delay_seconds', true );
		$next_delay_seconds  = (int) get_post_meta( $popup_id, '_bb_popup_next_delay_seconds', true );
		$delay_ms            = max( 0, $delay_seconds ) * 1000;
		$next_delay_ms       = max( 0, $next_delay_seconds ) * 1000;

		if ( '' === $button_text ) {
			$button_text = __( 'book now', 'brooklyn-beauty' );
		}

		if ( '' === $button_url ) {
			$button_url = '#book';
		}

		if ( '' === $desktop_image_url ) {
			$desktop_image_id  = (int) get_post_meta( $popup_id, 'bb_popup_desktop_image_id', true );
			$desktop_image_url = $desktop_image_id > 0 ? (string) wp_get_attachment_image_url( $desktop_image_id, 'full' ) : '';
		}

		if ( '' === $mobile_image_url ) {
			$mobile_image_id  = (int) get_post_meta( $popup_id, 'bb_popup_mobile_image_id', true );
			$mobile_image_url = $mobile_image_id > 0 ? (string) wp_get_attachment_image_url( $mobile_image_id, 'full' ) : '';
		}

		if ( '' === $desktop_image_url ) {
			$desktop_image_url = (string) get_the_post_thumbnail_url( $popup_post, 'full' );
		}

		if ( '' === $mobile_image_url ) {
			$mobile_image_url = $desktop_image_url;
		}

		if ( '' === trim( $description_text ) ) {
			$description_text = brooklyn_beauty_get_default_popup_description_text();
		}

		$description_html = wpautop( esc_html( $description_text ) );
		$title_id         = 'bb-promo-popup-title-' . $popup_id;
		?>
		<div
			class="bb-promo-popup"
			data-bb-promo-popup
			data-popup-id="<?php echo esc_attr( (string) $popup_id ); ?>"
			data-sequence-index="<?php echo esc_attr( (string) $popup_index ); ?>"
			data-initial-delay-ms="<?php echo esc_attr( (string) $delay_ms ); ?>"
			data-next-delay-ms="<?php echo esc_attr( (string) $next_delay_ms ); ?>"
			data-show-once="<?php echo $show_once ? '1' : '0'; ?>"
			hidden
		>
			<div class="bb-promo-popup__backdrop" data-bb-popup-close></div>
			<div class="bb-promo-popup__dialog" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
				<div class="bb-promo-popup__header">
					<?php if ( '' !== trim( $title ) ) : ?>
						<h2 id="<?php echo esc_attr( $title_id ); ?>" class="bb-promo-popup__title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<button type="button" class="bb-promo-popup__close" data-bb-popup-close aria-label="<?php esc_attr_e( 'Close popup', 'brooklyn-beauty' ); ?>">&#10005;</button>
				</div>

				<div class="bb-promo-popup__content">
					<div class="bb-promo-popup__media-row">
						<div class="bb-promo-popup__image">
							<?php if ( '' !== $desktop_image_url ) : ?>
								<picture>
									<?php if ( '' !== $mobile_image_url ) : ?>
										<source media="(max-width: 860px)" srcset="<?php echo esc_url( $mobile_image_url ); ?>" />
									<?php endif; ?>
									<img src="<?php echo esc_url( $desktop_image_url ); ?>" alt="" loading="eager" />
								</picture>
							<?php endif; ?>
						</div>

						<?php if ( '' !== trim( $description_text ) ) : ?>
							<div class="bb-promo-popup__text"><?php echo wp_kses_post( $description_html ); ?></div>
						<?php endif; ?>
					</div>

					<a class="bb-promo-popup__button" href="<?php echo esc_url( $button_url ); ?>">
						<?php echo esc_html( $button_text ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}
}
add_action( 'wp_footer', 'brooklyn_beauty_render_active_popups', 5 );
