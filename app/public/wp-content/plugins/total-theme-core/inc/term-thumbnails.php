<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds thumbnail options to taxonomies.
 */
final class Term_Thumbnails {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		$is_admin = \is_admin();

		if ( $is_admin ) {
			\add_action( 'admin_init', [ self::class, 'on_admin_init' ] );
		}

		if ( ( ! $is_admin || \wp_doing_ajax() ) && self::page_header_image_enabled() ) {
			\add_filter( 'totaltheme/page/header/style', [ self::class, 'filter_page_header_style' ] );
			\add_filter( 'wpex_page_header_background_image', [ self::class, 'filter_page_header_bg' ] );
		}
	}

	/**
	 * Returns an array of supported taxonomies for the thumbnail option.
	 */
	public static function supported_taxonomies(): array {
		$taxonomies = (array) get_taxonomies( [
			'public' => true,
		], 'names' );

		if ( \function_exists( 'wpex_get_ptu_tax_mod' ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$ptu_check = \wpex_get_ptu_tax_mod( $taxonomy, 'term_thumbnails', true );
				if ( isset( $ptu_check ) && ! \wp_validate_boolean( $ptu_check ) ) {
					unset( $taxonomies[ $taxonomy ] );
				}
			}
		}

		/**
		 * Filters the supported taxonomies for the term color meta option (wpex_color).
		 *
		 * @param array $taxonomies
		 */
		$taxonomies = \apply_filters( 'totalthemecore/term_thumbnails/supported_taxonomies', $taxonomies );

		/*** deprecated ***/
		$taxonomies = \apply_filters( 'wpex_thumbnail_taxonomies', $taxonomies );

		return (array) $taxonomies;
	}

	/**
	 * Get things started in the backend to add/save the settings.
	 */
	public static function on_admin_init(): void {
		foreach ( self::supported_taxonomies() as $taxonomy ) {
			if ( self::page_header_image_enabled() || 'product_cat' !== $taxonomy ) {
				\add_action( "wpex_{$taxonomy}_form_fields_top", [ self::class, 'on_wpex_form_fields_top' ] );
			}
			if ( 'product_cat' !== $taxonomy ) {
				\add_action( "{$taxonomy}_add_form_fields", [ self::class, 'add_form_fields'], 10 );
				\add_filter( "manage_edit-{$taxonomy}_columns", [ self::class, 'admin_columns' ] );
				\add_filter( "manage_{$taxonomy}_custom_column", [ self::class, 'admin_column' ], 10, 3 );
			}
			\add_action( "created_{$taxonomy}", [ self::class, 'on_term_created' ] );
			\add_action( "edit_{$taxonomy}", [ self::class, 'on_term_edit' ] );
		}
	}

	/**
	 * Add Thumbnail field to add form fields.
	 */
	public static function add_form_fields( $taxonomy ): void {
		\wp_nonce_field( 'wpex_term_thumbnail_meta_nonce', 'wpex_term_thumbnail_meta_nonce' );

		self::enqueue_admin_scripts();
		?>
		<div class="form-field">
			<label for="term-thumbnail"><?php \esc_html_e( 'Image', 'total-theme-core' ); ?></label>
			<div>
				<input type="hidden" id="wpex_term_thumbnail" name="wpex_term_thumbnail">
				<button id="wpex-add-term-thumbnail" class="button-secondary"><?php \esc_attr_e( 'Select', 'total-theme-core' ); ?></button>
				<button id="wpex-term-thumbnail-remove" class="button-secondary" style="display:none;"><?php \esc_html_e( 'Remove', 'total-theme-core' ); ?></button>
				<div id="wpex-term-thumbnail-preview" data-image-size="80"></div>
			</div>
			<div class="clear"></div>
		</div>
	<?php
	}

	/**
	 * Add Thumbnail field to edit form fields.
	 */
	public static function on_wpex_form_fields_top( $term ): void {
		if ( empty( $term ) || ! \is_object( $term ) ) {
			return;
		}

		$taxonomy = $term->taxonomy ?? '';
		$term_id  = $term->term_id ?? 0;

		if ( ! $term_id || ! in_array( $taxonomy, self::supported_taxonomies() ) ) {
			return;
		}

		// Options not needed for Woo.
		if ( 'product_cat' !== $taxonomy ) :

			// Get thumbnail.
			$thumbnail_id  = self::get_term_thumbnail_id( $term_id, false );

			if ( $thumbnail_id ) {
				$thumbnail_src = \wp_get_attachment_image_src( $thumbnail_id, 'thumbnail', false );
				$thumbnail_url = $thumbnail_src[0] ?? '';
			}

			self::enqueue_admin_scripts();
			?>

			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="wpex-add-term-thumbnail"><?php \esc_html_e( 'Image', 'total-theme-core' ); ?></label>
				</th>
				<td>
					<input type="hidden" id="wpex_term_thumbnail" name="wpex_term_thumbnail" value="<?php echo esc_attr( $thumbnail_id ); ?>">
					<button id="wpex-add-term-thumbnail" class="button-secondary"><?php \esc_attr_e( 'Select', 'total-theme-core' ); ?></button>
					<button id="wpex-term-thumbnail-remove" class="button-secondary"<?php if ( ! $thumbnail_id ) echo ' style="display:none;"'; ?>><?php \esc_html_e( 'Remove', 'total-theme-core' ); ?></button>
					<div id="wpex-term-thumbnail-preview" data-image-size="80">
						<?php if ( ! empty( $thumbnail_url ) ) { ?>
							<img class="wpex-term-thumbnail-img" src="<?php echo \esc_url( $thumbnail_url ); ?>" width="80" height="80" style="margin-block-start:10px;">
						<?php } ?>
					</div>
				</td>
			</tr>

		<?php endif; ?>

		<?php if ( self::page_header_image_enabled() ) :
			$page_header_bg = self::get_term_meta( $term_id, 'page_header_bg', true );
			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="wpex_term_page_header_image"><?php \esc_html_e( 'Page Header Image', 'total-theme-core' ); ?></label></th>
				<td>
					<select id="wpex_term_page_header_image" name="wpex_term_page_header_image" class="postform">
						<option value="" <?php \selected( $page_header_bg, '' ); ?>><?php \esc_html_e( 'Default', 'total-theme-core' ); ?></option>
						<option value="false" <?php \selected( $page_header_bg, 'false' ); ?>><?php \esc_html_e( 'No', 'total-theme-core' ); ?></option>
						<option value="true" <?php \selected( $page_header_bg, 'true' ); ?>><?php \esc_html_e( 'Yes', 'total-theme-core' ); ?></option>
					</select>
					<?php
					/*
					if ( \current_user_can( 'edit_theme_options' ) && \is_callable( 'TotalTheme\Admin\Theme_Panel::get_setting_link' ) ) { ?>
						<p class="description"><?php echo sprintf(
							esc_html_x( 'You can disable this globally via the %1$sTheme Panel%2$s.', '1: Open link to the theme panel 2: Close Link', 'total-theme-core' ),
							'<a href="' . \TotalTheme\Admin\Theme_Panel::get_setting_link( 'term_page_header_image_enable' ) . '" target="_blank">',
							'</a>'
						); ?></p>
					<?php }
					*/ ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php
	}

	/**
	 * Enqueue Admin scripts for uploading/selecting thumbnails.
	 */
	private static function enqueue_admin_scripts(): void {
		\wp_enqueue_media();
		\wp_enqueue_script(
			'totalthemecore-admin-term-thumbnails',
			\totalthemecore_get_js_file( 'admin/term-thumbnails' ),
			[ 'jquery' ],
			TTC_VERSION,
			true
		);
	}

	/**
	 * Saves term data in database.
	 */
	private static function add_term_data( $term_id, $meta_key, $meta_value, $prev_value = '' ): void {
		\update_term_meta( $term_id, $meta_key, \sanitize_text_field( $meta_value ), $prev_value );
	}

	/**
	 * Delete term data from database.
	 */
	private static function remove_term_data( $term_id, $key ): void {
		if ( ! empty( $term_id ) && ! empty( $key ) ) {
			\delete_term_meta( $term_id, $key );
		}
	}

	/**
	 * Delete term data from database.
	 */
	private static function remove_deprecated_term_data( $term_id, $key ): void {
		if ( empty( $term_id ) || empty( $key ) ) {
			return;
		}

		// Get deprecated data.
		$term_data = \get_option( 'wpex_term_data' );

		// Add to options.
		if ( isset( $term_data[ $term_id ][ $key ] ) ) {
			unset( $term_data[ $term_id ][ $key ] );
		}

		\update_option( 'wpex_term_data', $term_data );
	}

	/**
	 * Update thumbnail value.
	 */
	private static function update_thumbnail( $term_id, $thumbnail_id ): void {
		$safe_thumbnail_id = \absint( $thumbnail_id );

		if ( ! empty( $safe_thumbnail_id ) ) {
			self::add_term_data( $term_id, 'thumbnail_id', $safe_thumbnail_id );
		} else {
			self::remove_term_data( $term_id, 'thumbnail_id' );
		}

		self::remove_deprecated_term_data( $term_id, 'thumbnail' ); // Remove old data.
	}

	/**
	 * Update page header image option.
	 */
	private static function update_page_header_img( $term_id, $value ): void {
		if ( \is_bool( $value ) ) {
			$value = $value ? 'true' : 'false';
		}

		if ( isset( $value ) && '' !== $value ) {
			self::add_term_data( $term_id, 'page_header_bg', $value );
		} else {
			self::remove_term_data( $term_id, 'page_header_bg' );
		}

		self::remove_deprecated_term_data( $term_id, 'page_header_bg' );
	}

	/**
	 * Runs when a new term is created.
	 */
	public static function on_term_created( $term_id ): void {
		if ( \array_key_exists( 'wpex_term_thumbnail_meta_nonce', $_POST )
			&& \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['wpex_term_thumbnail_meta_nonce'] ) ), 'wpex_term_thumbnail_meta_nonce' )
		) {
			self::save_forms( $term_id );
		}
	}

	/**
	 * Runs when a new term is edited.
	 */
	public static function on_term_edit( $term_id ): void {
		if ( \array_key_exists( 'wpex_term_meta_nonce', $_POST )
			&& \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['wpex_term_meta_nonce'] ) ), 'wpex_term_meta_nonce' )
		) {
			self::save_forms( $term_id );
		}
	}

	/**
	 * Save Forms.
	 */
	private static function save_forms( $term_id ): void {
		if ( \array_key_exists( 'wpex_term_thumbnail', $_POST ) ) {
			self::update_thumbnail( $term_id, $_POST['wpex_term_thumbnail' ] );
		}
		if ( \array_key_exists( 'wpex_term_page_header_image', $_POST ) ) {
			self::update_page_header_img( $term_id, $_POST['wpex_term_page_header_image' ] );
		}
	}

	/**
	 * Thumbnail column added to category admin.
	 */
	public static function admin_columns( $columns ) {
		$columns['wpex-term-thumbnail-col'] = \esc_attr__( 'Image', 'total-theme-core' );
		return $columns;
	}

	/**
	 * Thumbnail column value added to category admin.
	 */
	public static function admin_column( $columns, $column, $id ) {
		if ( 'wpex-term-thumbnail-col' === $column ) {
			$thumbnail_id = self::get_term_thumbnail_id( $id, false );
			if ( $thumbnail_id ) {
				$thumbnail = \wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
			}
			if ( ! empty( $thumbnail ) ) {
				$columns .= '<img loading="lazy" src="' . \esc_url( $thumbnail[0] ) . '" class="wp-post-image" height="40" width="40" style="object-fit:cover;border:1px solid rgba(0,0,0,.07);">';
			} else {
				$columns .= '&#8212;';
			}
		}
		return $columns;
	}

	/**
	 * Get term meta with fallback.
	 */
	private static function get_term_meta( $term_id = null, $key = '', $single = true ) {
		if ( ! $term_id ) {
			$term_id = \get_queried_object()->term_id;
		}

		$value = '';

		if ( $term_id ) {
			$value = \get_term_meta( $term_id, $key, $single );
			if ( isset( $value ) ) {
				return $value;
			}
			$term_data = \get_option( 'wpex_term_data' );
			$term_data = $term_data[ $term_id ] ?? '';
			if ( $term_data && ! empty( $term_data[ $key ] ) ) {
				return $term_data[ $key ];
			}
		}

		return $value;
	}

	/**
	 * Check if the term page header should have a background image.
	 */
	public static function filter_page_header_style( $style ) {
		if ( 'hidden' !== $style
			&& self::is_tax_archive()
			&& self::term_page_header_image_enabled()
			&& self::get_term_thumbnail_id()
		) {
			$style = 'background-image';
		}
		return $style;
	}

	/**
	 * Filters the page header background image.
	 */
	public static function filter_page_header_bg( $image ) {
		if ( self::is_tax_archive() && self::term_page_header_image_enabled() ) {
			$term_thumbnail = self::get_term_thumbnail_id();
			if ( $term_thumbnail ) {
				$image_url = \wp_get_attachment_image_url( $term_thumbnail, 'full' );
				if ( $image_url ) {
					$image = $image_url;
				}
			}
		}
		return $image;
	}

	/**
	 * Retrieve term thumbnail for admin panel.
	 */
	public static function get_term_thumbnail_id( $term_id = null, $apply_filters = true ) {
		$thumbnail_id = '';
		$term_id      = $term_id ?: \get_queried_object_id();
		if ( $term_id ) {
			if ( \is_object( $term_id ) && \is_a( $term_id, 'WP_Term' ) ) {
				$term_id = $term_id->term_id;
			}
			$thumbnail_id = \get_term_meta( $term_id, 'thumbnail_id', true );
			// Check old options.
			if ( empty( $thumbnail_id ) ) {
				$term_data = \get_option( 'wpex_term_data' );
				$term_data = $term_data[ $term_id ] ?? '';
				if ( $term_data && ! empty( $term_data[ 'thumbnail' ] ) ) {
					return $term_data[ 'thumbnail' ];
				}
			}
		}

		/**
		 * Filters the term thumbnail id.
		 *
		 * @param int $thumbnail_id
		 * @param int $term_id
		 */
		if ( $apply_filters ) {
			$thumbnail_id = \apply_filters( 'wpex_get_term_thumbnail_id', $thumbnail_id );
		}

		return $thumbnail_id;
	}

	/**
	 * Checks that the functionality is enabled.
	 */
	public static function page_header_image_enabled(): bool {
		return (bool) \apply_filters( 'wpex_enable_term_page_header_image', \get_theme_mod( 'term_page_header_image_enable', true ) );
	}

	/**
	 * Check if on a tax archive.
	 */
	private static function is_tax_archive(): bool {
		return ( ! \is_search() && ( \is_tax() || \is_category() || \is_tag() ) );
	}

	/**
	 * Check if the term page header image is enabled.
	 */
	private static function term_page_header_image_enabled(): bool {
		return ( function_exists( 'wpex_term_page_header_image_enabled' )
			&& \wpex_term_page_header_image_enabled()
			&& in_array( self::get_current_taxonomy(), self::supported_taxonomies() )
		);
	}

	/**
	 * Returns the current taxonomy name.
	 */
	private static function get_current_taxonomy(): string {
		$taxonomy = \get_query_var( 'taxonomy' );
		if ( ! $taxonomy ) {
			if ( \is_category() ) {
				$taxonomy = 'category';
			} elseif ( \is_tag() ) {
				$taxonomy = 'post_tag';
			}
		}
		return $taxonomy;
	}

}
