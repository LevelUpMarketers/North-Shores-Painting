<?php

namespace TotalThemeCore\Meta;

defined( 'ABSPATH' ) || exit;

/**
 * Adds custom settings for post categories.
 */
final class Category_Settings {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Hook into actions and filters.
	 */
	public static function init(): void {
		if ( self::is_enabled() ) {
			\add_action( 'admin_init', [ self::class, 'admin_init' ] );
		}
	}

	/**
	 * Check if this functionality is enabled.
	 */
	public static function is_enabled(): bool {
		$check = \get_theme_mod( 'category_settings_enable' ) ?: \get_theme_mod( 'term_meta_enable', true );
		return (bool) \apply_filters( 'wpex_category_settings', $check );
	}

	/**
	 * Adds new category fields.
	 */
	public static function admin_init(): void {
		\add_action( 'wpex_category_form_fields_bottom', [ self::class, 'category_edit_form_fields' ] );
		\add_action( 'edited_category', [ self::class, 'edited_category' ] );
	}

	/**
	 * Adds new category fields.
	 */
	public static function category_edit_form_fields( $term ): void {
		if ( empty( $term ) || ! is_object( $term ) ) {
			return;
		}

		$term_id = $term->term_id ?? 0;

		if ( ! $term_id ) {
			return;
		}

		// Post layout.
		$layout = self::get_setting_value( $term_id, 'wpex_term_layout' ); ?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_layout]"><?php esc_html_e( 'Layout', 'total-theme-core' ); ?></label></th>
		<td>
			<select id="wpex_category_settings[wpex_term_layout]" name="wpex_category_settings[wpex_term_layout]">
				<option value="" <?php selected( $layout ) ?>><?php esc_html_e( 'Default', 'total-theme-core' ); ?></option>
				<option value="right-sidebar" <?php selected( $layout, 'right-sidebar', true ) ?>><?php esc_html_e( 'Right Sidebar', 'total-theme-core' ); ?></option>
				<option value="left-sidebar" <?php selected( $layout, 'left-sidebar', true ) ?>><?php esc_html_e( 'Left Sidebar', 'total-theme-core' ); ?></option>
				<option value="full-width" <?php selected( $layout, 'full-width', true ) ?>><?php esc_html_e( 'No Sidebar', 'total-theme-core' ); ?></option>
			</select>
		</td>
		</tr>

		<?php
		// Card Style.
		if ( ! get_theme_mod( 'blog_entry_card_style' ) ) {
			$style = self::get_setting_value( $term_id, 'wpex_term_style' ); ?>
			<tr class="form-field">
			<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_style]"><?php esc_html_e( 'Style', 'total-theme-core' ); ?></label></th>
			<td>
				<select id="wpex_category_settings[wpex_term_style]" name="wpex_category_settings[wpex_term_style]">
					<option value="" <?php selected( $style, '', true ); ?>><?php esc_html_e( 'Default', 'total-theme-core' ); ?></option>
					<option value="large-image" <?php selected( $style, 'large-image', true ); ?>><?php esc_html_e( 'Large Image', 'total-theme-core' ); ?></option>
					<option value="thumbnail" <?php selected( $style, 'thumbnail', true ); ?>><?php esc_html_e( 'Left Thumbnail', 'total-theme-core' ); ?></option>
					<option value="grid" <?php selected( $style, 'grid', true ); ?>><?php esc_html_e( 'Grid', 'total-theme-core' ); ?></option>
				</select>
			</td>
			</tr>
		<?php } ?>

		<?php
		// Grid columns
		$grid_cols = self::get_setting_value( $term_id, 'wpex_term_grid_cols' ); ?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_grid_cols]"><?php esc_html_e( 'Grid Columns', 'total-theme-core' ); ?></label></th>
		<td>
			<select id="wpex_category_settings[wpex_term_grid_cols]" name="wpex_category_settings[wpex_term_grid_cols]">
				<option value=""  <?php selected( $grid_cols, '', true ); ?>><?php esc_html_e( 'Default', 'total-theme-core' ); ?></option>
				<option value="1" <?php selected( $grid_cols, 1, true ) ?>>1</option>
				<option value="2" <?php selected( $grid_cols, 2, true ) ?>>2</option>
				<option value="3" <?php selected( $grid_cols, 3, true ) ?>>3</option>
				<option value="4" <?php selected( $grid_cols, 4, true ) ?>>4</option>
				<option value="5" <?php selected( $grid_cols, 5, true ) ?>>5</option>
				<option value="6" <?php selected( $grid_cols, 6, true ) ?>>6</option>
			</select>
		</td>
		</tr>

		<?php
		// Grid Style.
		$grid_style = self::get_setting_value( $term_id, 'wpex_term_grid_style' ); ?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_grid_style]"><?php esc_html_e( 'Grid Style', 'total-theme-core' ); ?></label></th>
		<td>
			<select id="wpex_category_settings[wpex_term_grid_style]" name="wpex_category_settings[wpex_term_grid_style]">
				<option value="" <?php selected( $grid_style, '', true ) ?>><?php esc_html_e( 'Default', 'total-theme-core' ); ?></option>
				<option value="fit-rows" <?php selected( $grid_style, 'fit-rows', true ) ?>><?php esc_html_e( 'Fit Rows', 'total-theme-core' ); ?></option>
				<option value="masonry" <?php selected( $grid_style, 'masonry', true ) ?>><?php esc_html_e( 'Masonry', 'total-theme-core' ); ?></option>
			</select>
		</td>
		</tr>

		<?php
		// Grid Gap.
		if ( function_exists( 'wpex_column_gaps' ) ) {
			$gap = self::get_setting_value( $term_id, 'wpex_term_grid_gap' ); ?>
			<tr class="form-field">
			<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_grid_gap]"><?php esc_html_e( 'Grid Gap', 'total-theme-core' ); ?></label></th>
			<td>
				<select id="wpex_category_settings[wpex_term_grid_gap]" name="wpex_category_settings[wpex_term_grid_gap]">
					<?php $gaps = wpex_column_gaps();
					foreach ( $gaps as $gapk => $gapv ) { ?>
						<option value="<?php echo esc_attr( $gapk ); ?>" <?php selected( $gap, $gapk, true ) ?>><?php echo esc_html( $gapv ); ?></option>
					<?php } ?>
				</select>
			</td>
			</tr>
		<?php } ?>

		<?php
		// Pagination Type.
		$pagination = self::get_setting_value( $term_id, 'wpex_term_pagination' ); ?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_pagination]"><?php esc_html_e( 'Pagination', 'total-theme-core' ); ?></label></th>
		<td>
			<select id="wpex_category_settings[wpex_term_pagination]" name="wpex_category_settings[wpex_term_pagination]">
				<option value="" <?php selected( $pagination, '', true ) ?>><?php esc_html_e( 'Default', 'total-theme-core' ); ?></option>
				<option value="standard" <?php selected( $pagination, 'standard', true ) ?>><?php esc_html_e( 'Standard', 'total-theme-core' ); ?></option>
				<option value="load_more" <?php selected( $pagination, 'load_more', true ) ?>><?php esc_html_e( 'Load More', 'total-theme-core' ); ?></option>
				<option value="infinite_scroll" <?php selected( $pagination, 'infinite_scroll', true ) ?>><?php esc_html_e( 'Inifinite Scroll', 'total-theme-core' ); ?></option>
				<option value="next_prev" <?php selected( $pagination, 'next_prev', true ) ?>><?php esc_html_e( 'Next/Previous', 'total-theme-core' ); ?></option>
			</select>
		</td>
		</tr>

		<?php
		// Excerpt length.
		$excerpt_length = self::get_setting_value( $term_id, 'wpex_term_excerpt_length' );
		?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_excerpt_length]"><?php esc_html_e( 'Excerpt Length', 'total-theme-core' ); ?></label></th>
			<td>
			<input id="wpex_category_settings[wpex_term_excerpt_length]" type="number" name="wpex_category_settings[wpex_term_excerpt_length]" value="<?php echo esc_attr( $excerpt_length ); ?>">
			</td>
		</tr>

		<?php
		// Posts per page.
		$posts_per_page = self::get_setting_value( $term_id, 'wpex_term_posts_per_page' ); ?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_posts_per_page]"><?php esc_html_e( 'Posts Per Page', 'total-theme-core' ); ?></label></th>
			<td>
			<input id="wpex_category_settings[wpex_term_posts_per_page]" type="number" name="wpex_category_settings[wpex_term_posts_per_page]" value="<?php echo esc_attr( $posts_per_page ); ?>">
			</td>
		</tr>

		<?php
		// Image Width.
		$thumb_width = self::get_setting_value( $term_id, 'wpex_term_image_width' ); ?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_image_width]"><?php esc_html_e( 'Image Width', 'total-theme-core' ); ?></label></th>
			<td>
			<input id="wpex_category_settings[wpex_term_image_width]" type="number" name="wpex_category_settings[wpex_term_image_width]" value="<?php echo esc_attr( $thumb_width ); ?>">
			</td>
		</tr>

		<?php
		// Image Height.
		$thumb_height = self::get_setting_value( $term_id, 'wpex_term_image_height' ); ?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="wpex_category_settings[wpex_term_image_height]"><?php esc_html_e( 'Image Height', 'total-theme-core' ); ?></label></th>
			<td>
			<input id="wpex_category_settings[wpex_term_image_height]" type="number" name="wpex_category_settings[wpex_term_image_height]" value="<?php echo esc_attr( $thumb_height ); ?>">
			</td>
		</tr>

	<?php  }

	/**
	 * Get category setting value.
	 */
	protected static function get_setting_value( $term_id, $key ) {
		$value = \get_term_meta( $term_id, $key, true );
		if ( null === $value || ( \is_string( $value ) && '' === \trim( $value ) ) ) {
			$option = \get_option( 'category_' . \sanitize_key( $term_id ) );
			if ( $option ) {
				$value = $option[ $key ] ?? '';
			}
		}
		return $value;
	}

	/**
	 * Saves new category fields.
	 */
	public static function edited_category( $term_id ): void {
		if ( ! \array_key_exists( 'wpex_term_meta_nonce', $_POST )
			|| ! \array_key_exists( 'wpex_category_settings', $_POST )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['wpex_term_meta_nonce'] ) ), 'wpex_term_meta_nonce' )
		) {
			return;
		}

		$post_data = $_POST['wpex_category_settings'];
		$settings = array_keys( $post_data );

		foreach ( $settings as $setting_id ) {

			/**
			 * Skip any option that isn't in $post_data.
			 * this way we aren't deleting meta that could be temporarily hidden.
			 */
			if ( ! array_key_exists( $setting_id, $post_data ) ) {
				continue;
			}

			$value = $post_data[$setting_id];

			if ( $value || '0' === $value ) {
				update_term_meta( $term_id, $setting_id, sanitize_text_field( $value ) );
			} else {
				delete_term_meta( $term_id, $setting_id );
			}

		}

		self::maybe_delete_old_option( $term_id );
	}

	/**
	 * Maybe deletes the old term option.
	 */
	protected static function maybe_delete_old_option( $term_id ): void {
		$safe_key      = sanitize_key( $term_id );
		$option_key    = "category_{$safe_key}";
		$old_term_meta = get_option( $option_key );
		if ( $old_term_meta ) {
			delete_option( $option_key );
		}
	}

}
