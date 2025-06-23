<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Add image sizes and image size settings panel.
 */
class Image_Sizes {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		if ( ! \get_theme_mod( 'image_sizes_enable', true ) ) {
			return;	// disabled.
		}

		// Define and add image sizes
		// Can't run earlier cause it comflicts with WooCommerce updated in v 4.5.5 from priority 1 to 40.
		\add_action( 'init', [ self::class, 'add_sizes' ], 40 );

		// Prevent images from cropping when on the fly is enabled.
		if ( \get_theme_mod( 'image_resizing', true ) ) {
			\add_filter( 'intermediate_image_sizes_advanced', [ self::class, 'filter_intermediate_image_sizes_advanced' ] );
		}

		// Admin only functions.
		if ( \wpex_is_request( 'admin' ) && \apply_filters( 'wpex_image_sizes_panel', true ) ) {
			\add_action( 'admin_menu', [ self::class, 'add_admin_submenu_page' ], 10 );
		}

		// Disable WP responsive images.
		if ( self::disable_responsive_images_check() ) {
			\add_filter( 'wp_calculate_image_srcset', '__return_false', PHP_INT_MAX );
		}
	}

	/**
	 * Return array of image sizes used by the theme.
	 */
	public static function get_sizes(): array {
		return (array) \apply_filters( 'wpex_image_sizes', [
			'lightbox'    => [
				'label'   => \esc_html__( 'Lightbox Images', 'total' ),
				'section' => 'other',
			],
			'search_results' => [
				'label'   => \esc_html__( 'Search', 'total' ),
				'section' => 'other',
			],
			'blog_entry'  => [
				'label'   => \esc_html__( 'Blog Entry', 'total' ),
				'section' => 'blog',
			],
			'blog_post'   => [
				'label'   => \esc_html__( 'Blog Post', 'total' ),
				'section' => 'blog',
			],
			'blog_post_full' => [
				'label'   => \esc_html__( 'Blog Post: (Media Position Full-Width Above Content)', 'total' ),
				'section' => 'blog',
			],
			'blog_related' => [
				'label'   => \esc_html__( 'Blog Post: Related', 'total' ),
				'section' => 'blog',
			],
		] );
	}

	/**
	 * Filter the image sizes automatically generated when uploading an image.
	 */
	public static function filter_intermediate_image_sizes_advanced( $sizes ) {
		foreach ( \array_keys( self::get_sizes() ) as $size ) {
			unset( $sizes[ $size ] );
		}
		return $sizes;
	}

	/**
	 * Returns args for a specific image size.
	 */
	private static function get_image_size_args( $size, $args ): array {
		return [
			'width'  => (int) \get_theme_mod( $args['width'] ?? "{$size}_image_width", $args['defaults']['width'] ?? 9999 ),
			'height' => (int) \get_theme_mod( $args['height'] ?? "{$size}_image_height", $args['defaults']['height'] ?? 9999 ),
			'crop'   => \get_theme_mod( $args['crop'] ?? "{$size}_image_crop", $defaults['crop'] ?? true ),
		];
	}

	/**
	 * Register image sizes in WordPress.
	 */
	public static function add_sizes(): void {
		foreach ( self::get_sizes() as $size => $args ) {
			\extract( self::get_image_size_args( $size, $args ) );

			if ( ! $crop && false !== $crop ) {
				$crop = true; // Always crop images center-center as this was always the theme default.
			}

			// Set crop to false depending on height value.
			if ( ! $height || ! $width || 'soft-crop' === $crop || $height >= 9999 || $width >= 9999 ) {
				$crop = false;
			}

			if ( $crop && \is_string( $crop ) ) {
				$crop = \explode( '-', $crop );
			}

			if ( $width || $height ) {
				\add_image_size( $size, $width, $height, $crop );
			}
		}
	}

	/**
	 * Add sub menu page.
	 */
	public static function add_admin_submenu_page(): void {
		$hook_suffix = \add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_html__( 'Image Sizes', 'total' ),
			\esc_html__( 'Image Sizes', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG . '-image-sizes',
			[ self::class, 'render_admin_page' ]
		);

		\add_action( "load-{$hook_suffix}", [ self::class, 'admin_help_tab' ] );
	}

	/**
	 * Add admin help tab.
	 */
	public static function admin_help_tab(): void {
		$screen = \get_current_screen();

		if ( ! $screen ) {
			return;
		}

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_image_sizes',
				'title'   => \esc_html__( 'Overview', 'total' ),
				'content' => '<p>' . \esc_html__( 'Define the exact cropping for all the featured images on your site. Leave the width and height empty to display the full image. Set any height to "9999" or empty to disable cropping and simply resize the image to the corresponding width. All image sizes defined below will be added to the list of WordPress image sizes.', 'total' ) . '</p>'
			]
		);

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_image_sizes_retina',
				'title'   => \esc_html__( 'Retina', 'total' ),
				'content' => '<p>' . \esc_html__( 'When you enable the Retina settting the theme will try and generate a second image that is twice as big for any defined image size to display on retina devices. For example, if you set your "Blog Entry" image size to 600x400, the theme will try and generate a second image that is 1200x800 and save it on the server. If you want to limit server memory usage we recommend keeping this setting disabled and remove any custom image sizes and use the Aspect Ratio field instead.', 'total' ) . '</p>'
			]
		);

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_image_sizes_crop',
				'title'   => \esc_html__( 'Cropping Images', 'total' ),
				'content' => '<p>' . \esc_html__( 'For any image size you can enable the "Crop Image" checkbox to enter a custom width, height and crop location. For most websites we do not recommend custom cropping images. We recommend not uploading massive images on your site and using the Aspect Ratio settings so that your images display nicely. This way you aren\'nt bloating your server with cropped images and it allows you to optimize your images prior to upload. ', 'total' ) . '</p>'
			]
		);

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_image_sizes_aspect_ratio',
				'title'   => \esc_html__( 'Aspect Ratio', 'total' ),
				'content' => '<p>' . \esc_html__( 'When setting a custom aspect ratio for your images it will be done using CSS only. So if you want to prevent the theme from creating and saving cropped images on your server you can leave the width, height and crop fields empty and simply select an aspect ratio.', 'total' ) . '</p>'
			]
		);

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_image_sizes_dynamic',
				'title'   => \esc_html__( 'Dynamic Resizing', 'total' ),
				'content' => '<p>' . \esc_html__( 'When this setting is enabled the theme will crop your images as requested. Whenever an image is displayed on your site that is part of the sizes listed below, the theme will generate the image based on your defined width, height and crop settings and save it to your uploads folder for usage the next time it\'s requested.', 'total' ) . '<p><p>' . \esc_html__( 'If you disable this feature, the theme will not crop your images, instead WordPress will crop every image size whenever any new image is uploaded via the Media Library. If you have defined any custom width, height or crop values we recommend keeping Dynamic Resizing enabled to prevent unnecessary image cropping on your site. If you haven\'t defined any custom sizes, you can disable Dynamic Resizing since the theme will register the images with a large width and height of 9999 prevent to any cropping on upload.', 'total' ) . '</p><p>' . \esc_html__( 'See the "Aspect Ratio" tab for setting image ratios without any cropping!', 'total' ) .'</p>',
			]
		);
	}

	/**
	 * Save admin options.
	 */
	private static function save_options( $options ): void {
		if ( ! \is_array( $options )
			|| ! isset( $_POST['totaltheme-image-sizes-admin-nonce'] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-image-sizes-admin-nonce'] ) ), 'totaltheme-image-sizes-admin' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		// Save checkboxes.
		$checkboxes = [
			'image_resizing'               => true,
			'post_thumbnail_lazy_loading'  => true,
			'retina'                       => false,
			'disable_wp_responsive_images' => false,
			'woo_dynamic_image_resizing'   => false,
		];

		foreach ( $checkboxes as $theme_mod_name => $theme_mod_default ) {
			if ( isset( $options[ $theme_mod_name ] ) ) {
				if ( $theme_mod_default ) {
					\remove_theme_mod( $theme_mod_name );
				} else {
					\set_theme_mod( $theme_mod_name, 1 );
				}
			} else {
				if ( $theme_mod_default ) {
					\set_theme_mod( $theme_mod_name, 0 );
				} else {
					\remove_theme_mod( $theme_mod_name );
				}
			}
		}

		// Standard options.
		foreach ( $options as $key => $value ) {
			if ( array_key_exists( $key, $checkboxes ) ) {
				continue; // checkboxes already done.
			}
			if ( ! empty( $value ) ) {
				\set_theme_mod( $key, \sanitize_text_field( \wp_unslash( $value ) ) );
			} else {
				\remove_theme_mod( $key );
			}
		}

	}

	/**
	 * Settings page output.
	 */
	public static function render_admin_page(): void {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		if ( isset( $_POST['totaltheme_image_sizes'] ) ) {
			self::save_options( $_POST['totaltheme_image_sizes'] );
		}

		\wp_enqueue_style( 'totaltheme-admin-pages' );
		\wp_enqueue_script( 'totaltheme-admin-pages' );

		\wp_enqueue_script(
			'totaltheme-admin-panel-image-sizes',
			\totaltheme_get_js_file( 'admin/panel-image-sizes' ),
			[],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
				'in_footer' => false
			]
		);

		\wp_enqueue_style(
			'totaltheme-admin-panel-image-sizes',
			\totaltheme_get_css_file( 'admin/panel-image-sizes' ),
			[],
			\WPEX_THEME_VERSION
		);

		// Remove deprecated option.
		\delete_option( 'wpex_image_sizes' );

		?>

		<div class="wrap totaltheme-panel-image-sizes">

			<h2 class="nav-tab-wrapper wpex-panel-js-tabs" style="margin-block-start:20px;">
				<?php
				// Image sizes tabs
				$tabs = \apply_filters( 'wpex_image_sizes_tabs', [
					'general' => \esc_html__( 'General', 'total' ),
					'blog'    => \esc_html__( 'Blog', 'total' ),
				] );

				// Add 'other' tab after filter so it's always at end
				$tabs['other'] = \esc_html__( 'Other', 'total' );

				// Loop through tabs and display them
				$count = 0;
				foreach ( $tabs as $key => $val ) {
					$count++;
					$classes = 'nav-tab';
					if ( 1 === $count ) {
						$classes .=' nav-tab-active';
					}
					echo '<a href="#' . \esc_attr( $key ) . '" class="' . \esc_attr( $classes ) . '">' . \esc_html( $val ) . '</a>';
				} ?>

			</h2>

			<form method="post" action="">

				<table class="form-table wpex-image-sizes-admin-table">

					<tr valign="top" class="wpex-tab-content wpex-general">
						<th scope="row"><?php \esc_html_e( 'Dynamic Resizing', 'total' ); ?></th>
						<td>
							<label>
								<?php $dynamic_resizing = \get_theme_mod( 'image_resizing', true ); ?>
								<input id="wpex_image_resizing" type="checkbox" name="totaltheme_image_sizes[image_resizing]" <?php \checked( $dynamic_resizing ); ?>> <?php \esc_html_e( 'Enable on-the-fly dynamic image resizing for featured images displayed by the theme.', 'total' ); ?>
							</label>
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-general<?php echo $dynamic_resizing ? '' : ' hidden'; ?>">
						<th scope="row"><?php \esc_html_e( 'Lazy Loading', 'total' ); ?></th>
						<td>
							<label>
								<input id="wpex_lazy_loading" type="checkbox" name="totaltheme_image_sizes[post_thumbnail_lazy_loading]" <?php \checked( \get_theme_mod( 'post_thumbnail_lazy_loading', true ), true ); ?>> <?php \esc_html_e( 'Enables native browser lazy loading for featured images displayed by the theme.', 'total' ); ?>
							</label>
						</td>
					</tr>

					<tr valign="top" class="wpex-tab-content wpex-general<?php echo $dynamic_resizing ? '' : ' hidden'; ?>">
						<th scope="row"><?php \esc_html_e( 'Retina', 'total' ); ?></th>
						<td>
							<label>
								<input id="wpex_retina" type="checkbox" name="totaltheme_image_sizes[retina]" <?php \checked( \get_theme_mod( 'retina' ), true ); ?>> <?php \esc_html_e( 'Enable retina support for images generated by the theme.', 'total' ); ?>
							</label>
						</td>
					</tr>

					<?php
					// Disable srcset
					$mod = \wp_validate_boolean( \get_theme_mod( 'disable_wp_responsive_images', false ) ); ?>
					<tr valign="top" class="wpex-tab-content wpex-general">
						<th scope="row"><?php \esc_html_e( 'Disable WordPress srcset Images', 'total' ); ?></th>
						<td>
							<label>
								<input id="wpex-disable-srcset" type="checkbox" name="totaltheme_image_sizes[disable_wp_responsive_images]" <?php \checked( $mod, true ); ?>> <?php \esc_html_e( 'Disables the WordPress "wp_calculate_image_srcset" functionality which may add srcset attributes to post thumbnails.', 'total' ); ?>
							</label>
						</td>
					</tr>

					<?php
					// WooCommerce Image Sizing
					if ( totaltheme_is_integration_active( 'woocommerce' )
						&& totaltheme_call_static( 'Integration\WooCommerce', 'is_advanced_mode' )
					) {

						$mod = \wp_validate_boolean( \get_theme_mod( 'woo_dynamic_image_resizing', false ) ); ?>

						<tr valign="top" class="wpex-tab-content wpex-general<?php echo $dynamic_resizing ? '' : ' hidden'; ?>">
							<th scope="row"><?php \esc_html_e( 'Use WooCommerce Native Image Sizing?', 'total' ); ?></th>
							<td>
								<label>
									<input id="wpex_woo_support" type="checkbox" name="totaltheme_image_sizes[woo_dynamic_image_resizing]" <?php \checked( $mod, true ); ?>> <?php \esc_html_e( 'By default the Total theme makes use of it\'s own image resizing functions for WooCommerce, if you rather use the native WooCommerce image sizing functions you can do so by enabling this setting.', 'total' ); ?>
								</label>
							</td>
						</tr>

					<?php } ?>

					<?php
					// Loop through image sizes to add form fields.
					foreach ( self::get_sizes() as $size => $args ) :
						$label = ! empty( $args['label'] ) ? $args['label'] : $size;
						$section = $args['section'] ?? 'other';
						$width_mod = $args['width'] ?? "{$size}_image_width";
						$width_value = \get_theme_mod( $width_mod );
						$height_mod = $args['height'] ?? "{$size}_image_height";
						$height_value = \get_theme_mod( $height_mod );
						$crop_mod = $args['crop'] ?? "{$size}_image_crop";
						$aspect_ratio_mod = $args['aspect_ratio'] ?? "{$size}_image_aspect_ratio";
						$fit_mod = $args['fit'] ?? "{$size}_image_fit";
						$position_mod = $args['position'] ?? "{$size}_image_position";
						$has_crop = ( $width_value || $height_value ) && ( 0 !== (int) $width_value && 0 !== (int) $height_value );
						?>

						<tr valign="top" class="totaltheme-panel-image-sizes__size-tr wpex-tab-content wpex-<?php echo \esc_attr( $section ); ?>">
							<th scope="row"><?php echo \esc_html( $label ); ?><br><small><?php printf( \esc_html__( 'ID: %s', 'total' ), \esc_html( $size ) ); ?></small></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php printf( \esc_html__( 'Image Size Settings for: %s', 'total' ), \esc_html( $label ) ); ?></legend>

									<div class="totaltheme-field-stack">
										<label for="<?php echo \esc_attr( "totaltheme-field--enable_crop-{$size}" ); ?>"><?php \esc_html_e( 'Crop?', 'total' ); ?></label>
										<input id="<?php echo \esc_attr( "totaltheme-field--enable_crop-{$size}" ); ?>" class="totaltheme-crop-image-checkbox" type="checkbox" <?php \checked( $has_crop, 1, true ); ?>>
									</div>
									
									<div class="totaltheme-crop-settings<?php echo ! $has_crop ? ' hidden' : ''; ?>">
										<div class="totaltheme-field-stack">
											<label for="totaltheme-field--<?php echo \esc_attr( $width_mod ); ?>"><?php \esc_html_e( 'Crop Width', 'total' ); ?></label>
											<input id="totaltheme-field--<?php echo \esc_attr( $width_mod ); ?>" name="totaltheme_image_sizes[<?php echo \esc_attr( $width_mod ); ?>]" type="number" step="1" min="0" value="<?php echo \esc_attr( $width_value ); ?>" class="small-text">
										</div>

										<div class="totaltheme-field-stack">
											<label for="totaltheme-field--<?php echo \esc_attr( $height_mod ); ?>"><?php \esc_html_e( 'Crop Height', 'total' ); ?></label>
											<input id="totaltheme-field--<?php echo \esc_attr( $height_mod ); ?>" name="totaltheme_image_sizes[<?php echo \esc_attr( $height_mod ); ?>]" type="number" step="1" min="0" value="<?php echo \esc_attr( $height_value ); ?>" class="small-text">
										</div>
										
										<div class="totaltheme-field-stack">
											<label for="totaltheme-field--<?php echo \esc_attr( $crop_mod ); ?>"><?php \esc_html_e( 'Crop Location', 'total' ); ?></label>
											<select id="totaltheme-field--<?php echo \esc_attr( $crop_mod ); ?>" name="totaltheme_image_sizes[<?php echo \esc_attr( $crop_mod ); ?>]">
												<?php
												$crop_value = \get_theme_mod( $crop_mod );
												foreach ( \wpex_image_crop_locations() as $key => $label ) { ?>
													<option value="<?php echo \esc_attr( $key ); ?>" <?php \selected( $key, $crop_value, true ); ?>><?php echo \wp_strip_all_tags( $label ); ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									
									<?php if ( 'lightbox' !== $size ) { ?>
										<div class="totaltheme-field-stack totaltheme-aspect-ratio-field-wrap">
											<label for="totaltheme-field--<?php echo \esc_attr( $aspect_ratio_mod ); ?>"><?php \esc_html_e( 'Aspect Ratio', 'total' ); ?></label>
											<select id="totaltheme-field--<?php echo \esc_attr( $aspect_ratio_mod ); ?>" name="totaltheme_image_sizes[<?php echo \esc_attr( $aspect_ratio_mod ); ?>]">
												<?php
												$aspect_ratio_value = \get_theme_mod( $aspect_ratio_mod );
												foreach ( \totaltheme_get_aspect_ratio_choices() as $key => $label ) { ?>
													<option value="<?php echo \esc_attr( $key ); ?>" <?php \selected( $key, $aspect_ratio_value, true ); ?>><?php echo \wp_strip_all_tags( $label ); ?></option>
												<?php } ?>
											</select>
										</div>

										<div class="totaltheme-field-stack totaltheme-image-fit-field-wrap<?php echo ! $aspect_ratio_value ? ' hidden' : ''; ?>">
											<label for="totaltheme-field--<?php echo \esc_attr( $fit_mod ); ?>"><?php \esc_html_e( 'Image Fit', 'total' ); ?></label>
											<?php $fit_value = \get_theme_mod( $fit_mod ) ?: ''; ?>
											<select id="totaltheme-field--<?php echo \esc_attr( $fit_mod ); ?>" name="totaltheme_image_sizes[<?php echo \esc_attr( $fit_mod ); ?>]">
												<option value="" <?php \selected( '', $fit_value, true ); ?>><?php \esc_html_e( 'Cover', 'total' ); ?></option>
												<option value="contain" <?php \selected( 'contain', $fit_value, true ); ?>><?php \esc_html_e( 'Contain', 'total' ); ?></option>
												<option value="fill" <?php \selected( 'fill', $fit_value, true ); ?>><?php \esc_html_e( 'Fill', 'total' ); ?></option>
												<option value="scale-down" <?php \selected( 'scale-down', $fit_value, true ); ?>><?php \esc_html_e( 'Scale Down', 'total' ); ?></option>
												<option value="none" <?php \selected( 'none', $fit_value, true ); ?>><?php \esc_html_e( 'None', 'total' ); ?></option>
											</select>
										</div>

										<div class="totaltheme-field-stack totaltheme-image-position-field-wrap<?php echo ! $aspect_ratio_value ? ' hidden' : ''; ?>">
											<label for="totaltheme-field--<?php echo \esc_attr( $position_mod ); ?>"><?php \esc_html_e( 'Image Position', 'total' ); ?></label>
											<?php $position_value = \get_theme_mod( $position_mod ) ?: ''; ?>
											<select id="totaltheme-field--<?php echo \esc_attr( $position_mod ); ?>" name="totaltheme_image_sizes[<?php echo \esc_attr( $position_mod ); ?>]">
												<?php
												$position_choices = [
													''              => \esc_html__( 'Default', 'total' ),
													'top'           => \esc_html__( 'Top', 'total' ),
													'center'        => \esc_html__( 'Center', 'total' ),
													'bottom'        => \esc_html__( 'Bottom', 'total' ),
													'left-top'      => \esc_html__( 'Left Top', 'total' ),
													'left'          => \esc_html__( 'Left Center', 'total' ),
													'left-bottom'   => \esc_html__( 'Left Bottom ', 'total' ),
													'right-top'     => \esc_html__( 'Right Top', 'total' ),
													'right'         => \esc_html__( 'Right Center ', 'total' ),
													'right-bottom'  => \esc_html__( 'Right Bottom', 'total' ),
												];
												foreach ( $position_choices as $k => $v ) {
													echo '<option value="' . \esc_attr( $k ) . '" ' . \selected( $k, $position_value, false ) . '>' . \esc_html( $v ) . '</option>';
												}
												?>
											</select>
										</div>
									<?php } ?>

								</fieldset>
							</td>
						</tr>

					<?php endforeach; ?>

				</table>

				<?php
				// Add form nonce and display submit button.
				\wp_nonce_field( 'totaltheme-image-sizes-admin', 'totaltheme-image-sizes-admin-nonce' );
				
				\submit_button();
				?>
			</form>

		</div>
	<?php
	}

	/**
	 * Check if WP responsive images should be disabled.
	 */
	protected static function disable_responsive_images_check(): bool {
		return (bool) \apply_filters(
			'wpex_disable_wp_responsive_images',
			\get_theme_mod( 'disable_wp_responsive_images', false )
		);
	}

}
