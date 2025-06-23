<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds a Post Type Editor Panel for defined Post Types.
 */
final class CPT_Settings {

	/**
	 * Check if options were saved or not.
	 */
	protected static $settings_saved = false;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init()  {
		self::$settings_saved = self::maybe_save_options(); // run immediately so changes are reflected on refresh.

		\add_action( 'admin_menu', [ self::class, 'on_admin_menu' ], 40 );
	}

	/**
	 * Runs on the "init" hook.
	 */
	private static function get_post_types(): array {
		$types = [];
		if ( \totaltheme_call_static( 'Portfolio\Post_Type', 'is_enabled' ) ) {
			$types[] = 'portfolio';
		}
		if ( \totaltheme_call_static( 'Testimonials\Post_Type', 'is_enabled' ) ) {
			$types[] = 'testimonials';
		}
		if ( \totaltheme_call_static( 'Staff\Post_Type', 'is_enabled' ) ) {
			$types[] = 'staff';
		}
		return (array) \apply_filters( 'wpex_post_type_editor_types', $types );
	}

	/**
	 * Returns current page post type.
	 */
	private static function get_post_type(): string {
		return ! empty( $_GET['post_type'] ) ? \sanitize_text_field( \wp_unslash( $_GET['post_type'] ) ) : '';
	}

	/**
	 * Runs on the "admin_menu" hook.
	 */
	public static function on_admin_menu(): void {
		foreach ( self::get_post_types() as $type ) {

			$post_type_obj = \get_post_type_object( $type );

			if ( ! \is_object( $post_type_obj ) ) {
				continue;
			}

			$hook_suffix = \add_submenu_page(
				"edit.php?post_type={$type}",
				\sprintf( \esc_html_x( 'Settings', 'Post Type Admin Settings Page Title', 'Total' ), \esc_html( $post_type_obj->labels->name ) ),
				\esc_html__( 'Settings', 'total' ),
				'edit_theme_options',
				"totaltheme-{$type}-settings",
				[ self::class, 'create_admin_page' ]
			);

			\add_action( "load-{$hook_suffix}", [ self::class, 'on_load' ] );
		}
	}

	/**
	 * On load.
	 */
	public static function on_load(): void {
		Theme_Panel::enable_admin_bar();

		if ( self::$settings_saved ) {
			Theme_Panel::set_admin_bar_updated_notice( 'success' );
		}

		self::enqueue_scripts();
	}

	/**
	 * Return array of settings.
	 */
	private static function get_settings( $type ): array {
		return [
			'page' => [
				'label' => \esc_html__( 'Main Page', 'total' ),
				'type'  => 'wp_dropdown_pages',
				'description' => \esc_html__( 'Used for theme breadcrumbs when the auto archive is disabled.', 'total' ),
			],
			'admin_icon' => [
				'label' => \esc_html__( 'Admin Icon', 'total' ),
				'type'  => 'dashicon',
				'default' => [
					'staff' => 'businessman',
					'portfolio' => 'portfolio',
					'testimonials' => 'testimonial',
				],
			],
			'has_archive' => [
				'label' => \esc_html__( 'Enable Auto Archive?', 'total' ),
				'type'  => 'checkbox',
				'description' => \esc_html__( 'Disabled by default so you can create your archive page using a page builder.', 'total' ),
			],
			'archive_orderby' => [
				'label' => \esc_html__( 'Archive Orderby', 'total' ),
				'type'  => 'select',
				'choices' => [
					'' => \esc_html__( 'Default', 'total' ),
					'date' => \esc_html__( 'Date', 'total' ),
					'title' => \esc_html__( 'Title', 'total' ),
					'name' => \esc_html__( 'Name (post slug)', 'total' ),
					'modified' => \esc_html__( 'Modified', 'total' ),
					'author' => \esc_html__( 'Author', 'total' ),
					'parent' => \esc_html__( 'Parent', 'total' ),
					'ID' => \esc_html__( 'ID', 'total' ),
					'comment_count' => \esc_html__( 'Comment Count', 'total' ),
					'menu_order' => \esc_html__( 'Menu Order', 'total' ),
				],

			],
			'archive_order' => [
				'label' => \esc_html__( 'Archive Order', 'total' ),
				'type'  => 'select',
				'choices' => [
					'' => \esc_html__( 'Default', 'total' ),
					'DESC' => \esc_html__( 'Descending', 'total' ),
					'ASC' => \esc_html__( 'Ascending', 'total' ),
				],

			],
			'has_single' => [
				'label' => \esc_html__( 'Enable Single Post?', 'total' ),
				'type'  => 'checkbox',
				'default' => true,
			],
			'show_in_rest' => [
				'label' => \esc_html__( 'Show in Rest?', 'total' ),
				'type'  => 'checkbox',
				'default' => false,
				'description' => \esc_html__( 'Enables support for the Gutenberg Editor.', 'total' ),
			],
			'custom_sidebar' => [
				'label' => \esc_html__( 'Enable Custom Sidebar?', 'total' ),
				'type'  => 'checkbox',
				'default' => true,
			],
			'search' => [
				'label' => \esc_html__( 'Include in Search Results?', 'total' ),
				'type'  => 'checkbox',
				'default' => true,
			],
			'labels' => [
				'label' => \esc_html__( 'Post Type: Name', 'total' ),
				'description' => \sprintf(
					\esc_html__( 'If you are going to rename the post type you may want to instead consider disabling this post type via the %sTheme Panel%s and re-register your post type via our %sPost Types Unlimited Plugin%s for complete control.', 'total' ),
						'<a href="' . \esc_url( Theme_Panel::get_setting_link( "{$type}_enable" ) ) . '" target="_blank">',
						' &#8599;</a>',
						'<a href="https://wordpress.org/plugins/total/" target="_blank" rel="nofollow noopener noreferrer">',
						' &#8599;</a>'
				),
				'type'  => 'text',
			],
			'singular_name' => [
				'label' => \esc_html__( 'Post Type: Singular Name', 'total' ),
				'type'  => 'text',
			],
			'slug' => [
				'label' => \esc_html__( 'Post Type: Slug', 'total' ),
				'description' => \esc_html__( 'In WordPress, the slug plays a crucial role in preventing conflicts, so it\'s generally not recommended to remove it. However, you can enter "0" in this field if you wish to remove it.', 'total' ),
				'type'  => 'text',
			],
			'categories' => [
				'label' => \esc_html__( 'Enable Categories?', 'total' ),
				'type'  => 'checkbox',
				'default' => true,
			],
			'cat_labels' => [
				'label' => \esc_html__( 'Categories: Label', 'total' ),
				'type'  => 'text',
			],
			'cat_slug' => [
				'label' => \esc_html__( 'Categories: Slug', 'total' ),
				'type'  => 'text',
			],
			'tags' => [
				'label' => \esc_html__( 'Enable Tags?', 'total' ),
				'type' => 'checkbox',
				'default' => true,
				'exclusive' => [ 'portfolio', 'staff' ],
			],
			'tag_labels' => [
				'label' => \esc_html__( 'Tag: Label', 'total' ),
				'type'  => 'text',
				'conditional' => 'has_tags',
				'exclusive' => [ 'portfolio', 'staff' ],
			],
			'tag_slug' => [
				'label' => \esc_html__( 'Tag: Slug', 'total' ),
				'type'  => 'text',
				'conditional' => 'has_tags',
				'exclusive' => [ 'portfolio', 'staff' ]
			],
		];
	}

	/**
	 * Potentially save the panel options.
	 */
	private static function maybe_save_options(): bool {
		if ( ! empty( $_POST['totaltheme_cpt_settings'] ) ) {
			return self::save_options( $_POST['totaltheme_cpt_settings'] );
		}
		return false;
	}

	/**
	 * Save settings.
	 */
	private static function save_options( $options ): bool {
		if ( empty( $options['post_type'] )
			|| ! isset( $_POST['totaltheme-admin-cpt-settings-nonce'] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-admin-cpt-settings-nonce'] ) ), 'totaltheme-admin-cpt-settings' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return false;
		}

		$post_type = $options['post_type'];
		$settings  = self::get_settings( $post_type );

		foreach ( $settings as $setting_name => $setting_args ) {
			if ( isset( $setting_args['exclusive'] ) && ! \in_array( $post_type, $setting_args['exclusive'] ) ) {
				continue;
			}

			$mod_name     = "{$post_type}_{$setting_name}";
			$setting_type = $setting_args['type'];
			$default      = self::get_default( $setting_args );
			$value        = isset( $options[ $mod_name ] ) ? $options[ $mod_name ] : '';

			switch ( $setting_type ) {
				case 'checkbox':
					if ( $default ) {
						if ( $value ) {
							\remove_theme_mod( $mod_name );
						} else {
							\set_theme_mod( $mod_name, false );
						}
					} else {
						if ( $value ) {
							\set_theme_mod( $mod_name, true );
						} else {
							\remove_theme_mod( $mod_name );
						}
					}
					break;
				case 'select':
					if ( ! empty( $value )
						&& isset( $setting_args['choices'] )
						&& array_key_exists( $value, $setting_args['choices'] )
					) {
						\set_theme_mod( $mod_name, \sanitize_text_field( $value ) );
					} else {
						\remove_theme_mod( $mod_name );
					}
					break;
				default:
					if ( $value || ( '0' === $value && 'slug' === $setting_name ) ) {
						if ( '0' === $value ) {
							$value_safe = 0;
						} else {
							$value_safe = \sanitize_text_field( $value );
						}
						\set_theme_mod( $mod_name, $value_safe );
					} else {
						\remove_theme_mod( $mod_name );
					}
					break;
			}

		}

		// @todo should only run if any slugs have changed is it possible to check?
		\flush_rewrite_rules();

		return true;
	}

	/**
	 * Output for the actual Staff Editor admin page.
	 */
	public static function create_admin_page(): void {
		$post_type = self::get_post_type();

		if ( ! $post_type || ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		?>

		<div class="wrap">

			<form method="post">

				<table class="form-table"><?php

					$settings = self::get_settings( $post_type );

					foreach ( $settings as $field_id => $field ) :

						if ( isset( $field['exclusive'] ) && ! in_array( $post_type, $field['exclusive'] ) ) {
							continue;
						}

						$method = "field_{$field['type']}";

						if ( \method_exists( self::class, $method ) ) {

							$mod_name         = "{$post_type}_{$field_id}";
							$field['default'] = self::get_default( $field );
							$field['id']      = "totaltheme_cpt_settings[{$mod_name}]";
							$mod_v            = \get_theme_mod( $mod_name, $field['default'] );

							if ( 'checkbox' === $field['type'] ) {
								$field['value'] = ( $mod_v && 'off' !== $mod_v ) ? true : false;
							} else {
								$field['value'] = $mod_v;
							}

							?>

							<tr valign="top">

								<th scope="row"><label for="<?php echo \esc_attr( $field['id'] ); ?>"><?php echo \esc_html( $field['label'] ); ?></label></th>

								<td>
									<?php self::$method( $field ); ?>
									<?php if ( ! empty( $field['description'] ) ) { ?>
										<?php if ( 'checkbox' !== $field['type'] ) { ?>
											<p style="max-width:650px"><span class="description"><?php
												echo \wp_kses_post( $field['description'] );
											?></span></p>
										<?php } else { ?>
											<span class="description" style="margin-left: 5px;"><?php
												echo \wp_kses_post( $field['description'] );
											?></span>
										<?php } ?>

									<?php } ?>
								</td>

							</tr>

						<?php } ?>

					<?php endforeach; ?>

				</table>

				<input name="totaltheme_cpt_settings[post_type]" type="hidden" value="<?php echo esc_attr( $post_type ); ?>">

				<?php \wp_nonce_field( 'totaltheme-admin-cpt-settings', 'totaltheme-admin-cpt-settings-nonce' ); ?>

				<?php \submit_button(); ?>

			</form>

		</div>
	<?php }

	/**
	 * Return wp_dropdown_pages field.
	 */
	private static function field_wp_dropdown_pages( $field ): void {
		\wp_dropdown_pages( [
			'echo'             => true,
			'selected'         => $field['value'],
			'name'             => $field['id'],
			'id'               => $field['id'],
			'class'            => 'wpex-chosen',
			'show_option_none' => \esc_html__( 'None', 'total' ),
		] );
	}

	/**
	 * Return select field.
	 */
	private static function field_select( $field ): void {
		if ( empty( $field['choices'] ) ) {
			return;
		}

		?>

		<select id="<?php echo \esc_attr( $field['id'] ); ?>" name="<?php echo \esc_attr( $field['id'] ); ?>"><?php
			foreach ( $field['choices'] as $ck => $cv ) { ?>
				<option value="<?php echo \esc_attr( $ck ); ?>" <?php \selected( $field['value'], $ck, true ) ?>><?php
					echo \esc_html( $cv );
				?></option>
			<?php }
		?></select>
	<?php }

	/**
	 * Return text field.
	 */
	private static function field_text( $field ): void {
		$attributes = [
			'type'  => 'text',
			'id'    => $field['id'],
			'name'  => $field['id'],
			'value' => $field['value'],
		];

		if ( isset( $field['size'] ) ) {
			$attributes['size'] = \absint( $field['size'] );
		}

		// @todo add placeholders witht he default values.

		echo '<input';
	        foreach ( $attributes as $name => $value ) {
	            echo ' ' . \sanitize_key( $name ) . '="' . \esc_attr( $value ) . '"';
	        }
	    echo '>';
	}

	/**
	 * Return checkbox field.
	 */
	private static function field_checkbox( $field ): void {
		$attributes = [
			'type'  => 'checkbox',
			'id'    => $field['id'],
			'name'  => $field['id'],
		];

		if ( isset( $field['class'] ) ) {
			$attributes['class'] = $field['class'];
		}

		echo '<span class="totaltheme-admin-checkbox"><input';
			foreach ( $attributes as $name => $value ) {
	            echo ' ' . $name . '="' . \esc_attr( $value ) . '"';
	        }
			\checked( $field['value'], true, true );
		echo '><span class="totaltheme-admin-checkbox__track"></span>
			<span class="totaltheme-admin-checkbox__thumb"></span>
		</span>';
	}

	/**
	 * Return dashicon field.
	 *
	 * @param array $field Current field to get dashicon for.
	 *
	 * @return string
	 */
	private static function field_dashicon( $field ): void {
		$value = $field['value'] ?? '';
		?>
		<div class="totaltheme-dashicon-select">
			<input type="text" name="<?php echo \esc_attr( $field['id'] ); ?>" id="<?php echo \esc_attr( $field['id'] ); ?>" value="<?php echo \esc_attr( $value ) ; ?>">
			<button class="totaltheme-dashicon-select__button button-secondary" type="button"><?php echo \esc_html__( 'Select Icon', 'total' ); ?></button>
			<div class="totaltheme-dashicon-preview"><?php if ( $value ) { ?>
				<span class="dashicons dashicons-<?php echo \esc_attr( $value ); ?>" aria-hidden="true"></span>
			<?php } ?></div>
			<div class="totaltheme-dashicon-select-modal components-modal__screen-overlay" style="display:none">
				<div class="components-modal__frame is-full-screen" tabindex="-1">
					<div class="components-modal__content">
						<div class="components-modal__header">
							<div class="components-search-control__input-wrapper">
								<input class="totaltheme-dashicon-select-modal__search components-search-control__input" type="search" placeholder="<?php echo esc_html__( 'Search for an icon', 'total' ); ?>"><div class="components-search-control__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13.5 6C10.5 6 8 8.5 8 11.5c0 1.1.3 2.1.9 3l-3.4 3 1 1.1 3.4-2.9c1 .9 2.2 1.4 3.6 1.4 3 0 5.5-2.5 5.5-5.5C19 8.5 16.5 6 13.5 6zm0 9.5c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"></path></svg></div>
							</div>
							<button class="totaltheme-dashicon-select-modal__close components-button has-icon" aria-label="<?php echo \esc_html__( 'Close dialog', 'total' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg></button>
						</div>
						<div class="totaltheme-dashicon-select-modal__icons"></div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Get default value.
	 */
	private static function get_default( $setting_args ) {
		if ( ! empty( $setting_args['default'] ) ) {
			$post_type = self::get_post_type();
			if ( is_array( $setting_args['default'] ) && isset( $setting_args['default'][ $post_type ] ) ) {
				return $setting_args['default'][ $post_type ];
			}
			return $setting_args['default'];
		}
	}

	/**
	 * Enqueue scripts for the Post Type Editor Panel.
	 */
	private static function enqueue_scripts(): void {
		\wp_enqueue_style( 'wpex-chosen' );
		\wp_enqueue_script( 'wpex-chosen' );
		\wp_enqueue_script( 'wpex-chosen-icon' );

		\wp_enqueue_style( 'totaltheme-admin-pages' );
		\wp_enqueue_script( 'totaltheme-admin-pages' );

		\wp_enqueue_style(
			'totaltheme-module-dashicon-select',
			\totaltheme_get_css_file( 'module/dashicon-select' ),
			[ 'wp-components' ],
			WPEX_THEME_VERSION
		);

		\wp_enqueue_script(
			'totaltheme-module-dashicon-select',
			\totaltheme_get_js_file( 'module/dashicon-select' ),
			[ 'jquery' ],
			WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);
	}

}
