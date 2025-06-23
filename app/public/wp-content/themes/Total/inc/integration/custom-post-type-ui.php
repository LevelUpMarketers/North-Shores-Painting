<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Post Types UI Integration.
 */
final class Custom_Post_Type_UI {

	/**
	 * Array of assigned post types.
	 */
	private $types = [];

	/**
	 * Array of post type settings.
	 */
	private $wpex_cpt_settings = [];

	/**
	 * Array of custom theme fields.
	 */
	protected $total_fields = [];

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Custom_Post_Type_UI.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	private function __construct() {
		\define( 'WPEX_CPTUI_INTEGRATION', true );

		$cptui_post_types = \get_option( 'cptui_post_types' );

		// Add new settings for the Custom Post Types UI plugin.
		if ( \wpex_is_request( 'admin' ) ) {
			\add_action( 'cptui_post_type_after_fieldsets', [ $this, 'add_total_post_type_settings' ], 10, 1 );
			\add_action( 'cptui_pre_save_post_type', [ $this, 'pre_save_post_type' ], 10, 2 );
		}

		// There are custom types so lets do things.
		if ( ! empty( $cptui_post_types ) && \is_array( $cptui_post_types ) ) {

			// Save types in Class var.
			$this->types = $cptui_post_types;

			// Get settings.
			$wpex_cpt_settings = \get_option( 'wpex_cpt_settings' );

			// Make sure settings are not empty and are in fact an array.
			if ( ! empty( $wpex_cpt_settings ) && \is_array( $wpex_cpt_settings ) ) {

				// Set data to class var.
				$this->wpex_cpt_settings = $wpex_cpt_settings;

				// Admin filters.
				if ( \wpex_is_request( 'admin' ) ) {
					\add_filter( 'wpex_main_metaboxes_post_types', [ $this, 'metabox_types' ] );
					\add_filter( 'wpex_metabox_array', [ $this, 'enable_metabox_media' ] );
					\add_filter( 'wpex_gallery_metabox_post_types', [ $this, 'gallery_metabox' ] );
					\add_filter( 'wpex_image_sizes_tabs', [ $this, 'image_sizes_tabs' ], 10 );
				}

				// Frontend filters.
				if ( \wpex_is_request( 'frontend' ) ) {
					\add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
					\add_filter( 'wpex_post_layout_class', [ $this, 'layouts' ] );
					\add_filter( 'wpex_page_header_title_args', [ $this, 'page_header_title_args' ], 10 );
					\add_filter( 'wpex_get_sidebar', [ $this, 'get_sidebar' ] );
					\add_filter( 'wpex_meta_categories_taxonomy', [ $this, 'meta_tax' ] );
					\add_filter( 'wpex_get_grid_entry_columns', [ $this, 'entry_columns' ] );
					\add_filter( 'wpex_has_next_prev', [ $this, 'next_prev' ], 10, 2 );
					\add_filter( 'wpex_meta_blocks', [ $this, 'meta_blocks' ], 5, 2 );

					// Loop through types.
					foreach ( $this->wpex_cpt_settings as $type => $settings ) {
						if ( ! empty( $settings['entry_blocks'] ) ) {
							\add_filter( "wpex_{$type}_entry_blocks", [ $this, 'entry_blocks' ], 5, 2 );
						}
						if ( ! empty( $settings['single_blocks'] ) ) {
							\add_filter( "wpex_{$type}_single_blocks", [ $this, 'single_blocks' ], 5, 2 );
						}
					}

				} // End admin check.

				// Global filters.
				\add_filter( 'wpex_get_singular_template_id', [ $this, 'singular_template' ], 5, 2 );
				\add_filter( 'wpex_image_sizes', [ $this, 'add_image_sizes' ], 99 );
				\add_filter( 'wpex_register_sidebars_array', [ $this, 'register_sidebars' ] );

			}

		}

	}

	/**
	 * Array of Total settings to add.
	 */
	public function wpex_settings() {

		// Store layouts
		$layout_choices = [];
		$layouts = wpex_get_post_layouts();
		foreach ( $layouts as $key => $val ) {
			$layout_choices[] = [
				'attr' => $key,
				'text' => $val,
			];
		}

		// Store grid columns
		$grid_columns_choices = [
			[
				'attr' => '',
				'text' => \esc_html__( 'Default', 'total' )
			]
		];
		$grid_columns = \wpex_grid_columns();
		foreach ( $grid_columns as $key => $val ) {
			$grid_columns_choices[] = [
				'attr' => $key,
				'text' => $val,
			];
		}

		// Entry blocks
		$entry_blocks = \apply_filters( 'wpex_cptui_entry_blocks', [
			'media'    => \esc_html__( 'Media', 'total' ),
			'title'    => \esc_html__( 'Title', 'total' ),
			'meta'     => \esc_html__( 'Meta', 'total' ),
			'content'  => \esc_html__( 'Content', 'total' ),
			'readmore' => \esc_html__( 'Readmore', 'total' ),
		] );

		// Single blocks
		$single_blocks = \apply_filters( 'wpex_cptui_single_blocks', [
			'media'       => \esc_html__( 'Media', 'total' ),
			'title'       => \esc_html__( 'Title', 'total' ),
			'meta'        => \esc_html__( 'Meta', 'total' ),
			'post-series' => \esc_html__( 'Post Series', 'total' ),
			'content'     => \esc_html__( 'Content', 'total' ),
			'page-links'  => \esc_html__( 'Page Links', 'total' ),
			'share'       => \esc_html__( 'Social Share', 'total' ),
			'comments'    => \esc_html__( 'Comments', 'total' ),
		] );

		// Meta blocks
		$meta_blocks = \apply_filters( 'wpex_cptui_meta_sections', [
			'date'       => \esc_html__( 'Date', 'total' ),
			'author'     => \esc_html__( 'Author', 'total' ),
			'categories' => \esc_html__( 'Categories', 'total' ),
			'comments'   => \esc_html__( 'Comments', 'total' ),
		] );

		// Return array
		return array(
			'add_to_total_array' => array(
				'label' => \esc_html__( 'Total Settings', 'total' ),
				'type' => 'select',
				'choices' => array(
					array( 'attr' => 'true', 'text' => \esc_attr__( 'True', 'total' ) ),
					array( 'attr' => 'false', 'text' => \esc_attr__( 'False', 'total' ) ),
				),
				'default' => 'true',
				'desc' => \esc_html__( 'Enable Total settings for this post type. Disabling this setting will also reset all the options.', 'total' ),
			),
			'enable_image_sizes' => array(
				'label' => \esc_html__( 'Image Sizes', 'total' ),
				'type' => 'select',
				'choices' => array(
					array( 'attr' => 'true', 'text' => \esc_attr__( 'True', 'total' ) ),
					array( 'attr' => 'false', 'text' => \esc_attr__( 'False', 'total' ) ),
				),
				'default' => 'true',
				'desc' => \esc_html__( 'Enable image size settings for this post type.', 'total' ),
			),
			'custom_sidebar' => array(
				'label' => \esc_html__( 'Custom Sidebar', 'total' ),
				'type' => 'textfield',
				'desc' => \esc_html__( 'Enter a name to create a custom sidebar for the post type archive, single posts and attached taxonomies.', 'total' ),
			),
			'post_metabox' => array(
				'label' => \esc_html__( 'Post Settings Metabox', 'total' ),
				'type' => 'select',
				'choices' => array(
					array( 'attr' => 'false', 'text' => \esc_attr__( 'False', 'total' ) ),
					array( 'attr' => 'true', 'text' => \esc_attr__( 'True', 'total' ) ),
				),
				'default' => 'false',
			),
			'gallery_metabox' => array(
				'label' => \esc_html__( 'Post Gallery', 'total' ),
				'type' => 'select',
				'choices' => array(
					array( 'attr' => 'false', 'text' => \esc_attr__( 'False', 'total' ) ),
					array( 'attr' => 'true', 'text' => \esc_attr__( 'True', 'total' ) ),
				),
				'default' => 'false',
			),
			'single_layout' => array(
				'label' => \esc_html__( 'Single Layout', 'total' ),
				'type' => 'select',
				'choices' => $layout_choices,
			),
			'single_page_header_title' => array(
				'label' => \esc_html__( 'Single Page Header title', 'total' ),
				'type' => 'textfield',
				'desc' => \esc_html__( 'If you are using a translation plugin you should use theme filters instead to alter this text. Please refer to the online documentation and snippets.', 'total' ) .'<br><br>'. \esc_html__( 'Use {{title}} to display the current post title.', 'total' ),
			),
			'next_prev' => array(
				'label' => \esc_html__( 'Next/Prev', 'total' ),
				'type' => 'select',
				'choices' => array(
					array( 'attr' => 'true', 'text' => \esc_attr__( 'True', 'total' ) ),
					array( 'attr' => 'false', 'text' => \esc_attr__( 'False', 'total' ) ),
				),
				'default' => 'true',
			),
			'main_page' => array(
				'label' => \esc_html__( 'Main Page ID', 'total' ),
				'type' => 'textfield',
				'desc' => esc_html( 'Used for breadcrumbs.', 'total' ),
			),
			'main_taxonomy' => array(
				'label' => \esc_html__( 'Main Taxonomy', 'total' ),
				'type' => 'taxonomy_select',
				'desc' => esc_html( 'Used for breadcrumbs and post meta categories.', 'total' ),
			),
			'archive_layout' => array(
				'label' => \esc_html__( 'Archive Layout', 'total' ),
				'type' => 'select',
				'choices' => $layout_choices,
			),
			'archive_grid_columns' => array(
				'label' => \esc_html__( 'Archive Grid Columns', 'total' ),
				'type' => 'textfield',
				'desc' => esc_html( 'Enter a number between 1 and 7. Default is 1.', 'total' ),
			),
			'archive_posts_per_page' => array(
				'label' => \esc_html__( 'Archive Posts Per Page', 'total' ),
				'type' => 'textfield',
				'desc' => esc_html( 'Will apply to the main post type archive and any built-in taxonomies associated with the post type.', 'total' ),
			),
			'archive_page_header_title' => array(
				'label' => \esc_html__( 'Archive Page Header Title', 'total' ),
				'type' => 'textfield',
				'desc' => \esc_html__( 'If you are using a translation plugin you should use theme filters instead to alter this text. Please refer to the online documentation and snippets.', 'total' ),
			),
			'entry_blocks' => array(
				'label' => \esc_html__( 'Entry Blocks', 'total' ),
				'type' => 'builder',
				'choices' => $entry_blocks,
			),
			'entry_meta_blocks' => array(
				'label' => \esc_html__( 'Entry Meta', 'total' ),
				'type' => 'builder',
				'choices' => $meta_blocks,
			),
			'single_template' => array(
				'label' => \esc_html__( 'Dynamic Singular Template ID', 'total' ),
				'type' => 'textfield',
				'default' => '',
				'desc' => \esc_html__( 'Create a dynamic template using Templatera to override the default content layout.', 'total' ),
			),
			'single_blocks' => array(
				'label' => \esc_html__( 'Single Blocks', 'total' ),
				'type' => 'builder',
				'choices' => $single_blocks,
			),
			'single_meta_blocks' => array(
				'label' => \esc_html__( 'Single Meta', 'total' ),
				'type' => 'builder',
				'builder_instance' => 'single',
				'choices' => $meta_blocks,
			),
		);
	}

	/**
	 * Add Total settings to the CPTUI admin.
	 */
	public function add_total_post_type_settings( $ui ) {
		// Define empty current var.
		$current = '';

		// Get current tab.
		$tab = ( ! empty( $_GET ) && ! empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) ? 'edit' : 'new';

		// Get current type.
		if ( 'edit' === $tab ) {
			$post_type_deleted = \apply_filters( 'cptui_post_type_deleted', false );
			$selected_post_type = \cptui_get_current_post_type( $post_type_deleted );

			// Get current settings.
			if ( ! empty( $_POST['wpex_data'] ) && \is_array( $_POST['wpex_data'] ) ) {
				$current = [];
				$post_data = $_POST['wpex_data'];
				foreach ( $post_data as $key => $val ) {
					$key = \str_replace( 'wpex_', '', $key );
					$current[ $key ] = $val;
				}
			} elseif( $selected_post_type ) {
				$wpex_data = $this->wpex_cpt_settings;
				$wpex_data = $wpex_data[ $selected_post_type ] ?? '';
				$current = $wpex_data;
			}

			// Check if disabled.
			if ( isset( $current['add_to_total_array'] ) && 'false' == $current['add_to_total_array'] ) {
				$current = 'disabled';
			}

		}

		?>

		<div class="cptui-section postbox">

			<div class="postbox-header">

				<h2 class="hndle ui-sortable-handle">
					<span><?php \esc_html_e( 'Total Theme', 'total' ); ?></span>
				</h2>

				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text"><?php \esc_html_e( 'Toggle panel: Total Settings', 'total' ); ?></span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>

			</div>

			<div class="inside">
				<div class="notice notice-warning"><p><span class="dashicons dashicons-warning"></span> <strong><?php \esc_html_e( 'We highly recommend the Post Types Unlimited plugin instead, which was custom built for the Total theme and includes more options to customize your post types and taxonomies and is more efficient.', 'total' ); ?> <a href="https://wordpress.org/plugins/post-types-unlimited/"><?php \esc_html_e( 'Learn more', 'total' ); ?> &rarr;</a></strong></p></div>
				<div class="main">
					<table class="form-table cptui-table">

						<?php
						// Get settings to loop through.
						$settings = $this->wpex_settings();

						// Loop through settings.
						foreach ( $settings as $key => $val ) {

							// Restore select.
							$select = [];

							// Get new val.
							$new_val = $current[ $key ] ?? '';

							// Default value.
							$default = $val['default'] ?? '';

							// Check if enabled.
							if ( 'disabled' === $current ) {
								if ( 'add_to_total_array' !== $key ) {
									break;
								}
								$new_val = 'false';
							}

							// Text.
							if ( 'textfield' === $val['type'] ) {

								// Sanitize columns.
								if ( $new_val && 'archive_grid_columns' == $key ) {
									$new_val = \intval( $new_val );
									$new_val = $new_val == 0 ? '' : $new_val;
									$new_val = $new_val > 7 ? 7 : $new_val;
								}

								// Display input.
								echo $ui->get_text_input( array(
									'namearray' => 'wpex_data',
									'name'      => $key,
									'textvalue' => $new_val,
									'labeltext' => $val['label'],
									'helptext'  => $val['desc'] ?? '',
									'required'  => isset( $val['required'] ) ? true : false,
								) );

							}

							// Taxonomy select.
							elseif ( 'taxonomy_select' === $val['type'] ) {

								$select['options'] = array(
									array( 'attr' => '', 'text' => \esc_attr__( 'None', 'total' ) )
								);
								$add_taxes = \get_taxonomies( array( 'public' => true ), 'objects' );
								unset( $add_taxes['nav_menu'] ); unset( $add_taxes['post_format'] );
								foreach ( $add_taxes as $add_tax ) {
									$select['options'][] = array( 'attr' => $add_tax->name, 'text' => $add_tax->labels->name );
								}
								$select['selected'] = $new_val;

								echo $ui->get_select_input( array(
									'namearray'  => 'wpex_data',
									'name'       => $key,
									'labeltext'  => $val['label'],
									'aftertext'  => $val['desc'] ?? '',
									'selections' => $select,
								) );

							}

							// Selects.
							elseif ( 'select' === $val['type'] ) {

								$select['options']  = $val['choices'];
								$select['selected'] = $new_val ?: $default;

								echo $ui->get_select_input( array(
									'namearray'  => 'wpex_data',
									'name'       => $key,
									'labeltext'  => $val['label'],
									'aftertext'  => $val['desc'] ?? '',
									'selections' => $select,
								) );

							}

							// Entry builder.
							if ( 'builder' === $val['type'] ) {

								// Add to current settings.
								if ( ! empty( $_POST[ $key ] ) && \is_array( $_POST[ $key ] ) ) {
									$current[ $key ] = $_POST[ $key ];
								}

								// Add label.
								echo '<tr valign="top" class="wpex-builder-field">';

								echo $ui->get_th_start() . $val['label'];

								// Open Fieldset
								echo $ui->get_th_end() . $ui->get_td_start() . $ui->get_fieldset_start();

								// Loop through blocks.
								foreach ( $val['choices'] as $block => $label ) {

									// Check if enabled.
									$checked = true;
									if ( ! empty( $current[ $key ] ) ) {
										if ( \is_array( $current[ $key ] ) ) {
											if ( in_array( $block, $current[ $key ] ) ) {
												$checked = 'true';
											} else {
												$checked = 'false';
											}
										}
									}

									// Always true for new tab.
									if ( 'new' === $tab ) {
										$checked = 'true';
									}

									$field_id = 'wpex_' . $key . '[' . $block . ']';
									$field_name = 'wpex_data[' . $key .'][' . $block . ']';

									echo '<input type="checkbox" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $block ) . '" ' . checked( $checked, 'true', false ) . '>';

									echo '<label for="' . esc_attr( $field_id ) . '">' . esc_html( $label ) .'</label><br>';

								}

								// Close fieldset.
								$ui->get_fieldset_end();

							}

							// Close table.
							echo $ui->get_td_end();
							echo $ui->get_tr_end();

						} ?>

					</table>

				</div><!-- .main -->

			</div><!-- .inside -->

		</div><!-- .cptui-section -->

	<?php }

	/**
	 * Save Settings.
	 *
	 * @param array  $post_types Array of final post type data to save.
	 * @param string $name       Post type slug for post type being saved.
	 */
	public function pre_save_post_type( $post_types, $name ) {
		$post_data = $_POST['wpex_data'] ?? [];

		if ( ! $post_data ) {
			return $post_types; // no need to save anything.
		}

		$type = $name;
		$wpex_data = \get_option( 'wpex_cpt_settings' ) ?: [];

		// Get settings to loop through.
		$settings = $this->wpex_settings();

		// Loop through settings to save them.
		foreach ( $settings as $key => $val ) {

			// First check if disabled.
			if ( 'add_to_total_array' === $key ) {
				if ( isset( $post_data[ $key ] ) && 'false' == $post_data[ $key ] ) {
					unset( $wpex_data[ $type ] );
					break;
				}
			}

			// Get new val for builder field type (uses $data var seperate from post_data ).
			if ( 'builder' === $val['type'] ) {
				if ( ! empty( $post_data[ $key ] ) && \is_array( $post_data[ $key ] ) ) {
					$new_val = \array_map( 'sanitize_text_field', $post_data[ $key ] );
				} else {
					$new_val = \array_flip( \array_map( 'sanitize_text_field', $val['choices'] ) );
				}
			}

			// Val for other modules.
			else {
				$new_val = ! empty( $post_data[ $key ] ) ? \sanitize_text_field( $post_data[ $key ] ) : '';
			}

			// Get val.
			$default = $val['default'] ?? '';

			// Sanitize columns.
			if ( $new_val && 'archive_grid_columns' === $key ) {
				$new_val_escaped = \intval( $new_val );
				$new_val = $new_val_escaped > 7 ? 7 : $new_val_escaped;
			}

			// Sanitize Posts per page.
			if ( $new_val && 'archive_posts_per_page' === $key ) {
				$new_val = \intval( $new_val );
			}

			// Save new value (already escaped/sanitized).
			if ( $new_val ) {
				$wpex_data[ $type ][ $key ] = $new_val;
			} elseif ( $default ) {
				$wpex_data[ $type ][ $key ] = $default; // Defaults that can't be removed
			} else {
				unset( $wpex_data[ $type ][ $key ] );
			}

		}

		$new_result = \update_option( 'wpex_cpt_settings', $wpex_data );

		return $post_types;
	}

	/**
	 * Enable metabox for types.
	 */
	public function metabox_types( $types ) {
		if ( $this->wpex_cpt_settings && \is_array( $this->wpex_cpt_settings ) ) {
			foreach ( $this->wpex_cpt_settings as $type => $settings ) {
				if ( ! empty( $settings['post_metabox'] ) && 'true' == $settings['post_metabox'] ) {
					$types[ $type ] = $type;
				}
			}
		}
		return $types;
	}

	/**
	 * Enable media box for types.
	 */
	public function enable_metabox_media( $array ) {
		if ( $this->wpex_cpt_settings && \is_array( $this->wpex_cpt_settings ) ) {
			foreach ( $this->wpex_cpt_settings as $type => $settings ) {
				if ( ! empty( $settings['post_metabox'] ) && 'true' == $settings['post_metabox'] ) {
					$array['media']['post_type'][] = $type;
				}
			}
		}
		return $array;
	}

	/**
	 * Register gallery metabox for your custom types.
	 */
	public function gallery_metabox( $types ) {
		if ( $this->wpex_cpt_settings && \is_array( $this->wpex_cpt_settings ) ) {
			foreach ( $this->wpex_cpt_settings as $type => $settings ) {
				if ( ! empty( $settings['gallery_metabox'] ) && 'true' == $settings['gallery_metabox'] ) {
					$types[ $type ] = $type;
				}
			}
		}
		return $types;
	}

	/**
	 * Adds new image size tabs.
	 */
	public function image_sizes_tabs( $tabs ) {
		foreach ( $this->types as $type => $args ) {
			$settings = $this->wpex_cpt_settings[ $type ] ?? [];
			if ( ! empty( $settings['enable_image_sizes'] ) && 'true' == $settings['enable_image_sizes'] ) {
				$obj = \get_post_type_object( $type );
				if ( $obj && \is_object( $obj ) ) {
					$name = $obj->labels->name;
				} else {
					$name = $type;
				}
				$tabs[ $type ] = $name;
			}
		}
		return $tabs;
	}

	/**
	 * Filter singular template id.
	 *
	 * @since 4.3
	 */
	public function singular_template( $template_id, $type ) {
		if ( $type && \array_key_exists( $type, $this->wpex_cpt_settings ) ) {
			$settings = $this->wpex_cpt_settings[ $type ];
			if ( ! empty( $settings['single_template'] ) ) {
				$template_id = $settings['single_template'];
			}
		}
		return $template_id;
	}

	/**
	 * Add image size option tabs for post types.
	 */
	public function add_image_sizes( $sizes ) {
		if ( $this->wpex_cpt_settings && \is_array( $this->wpex_cpt_settings ) ) {
			foreach ( $this->wpex_cpt_settings as $type => $settings ) {
				if ( ! empty( $settings['enable_image_sizes'] ) && 'true' == $settings['enable_image_sizes'] ) {
					$sizes[ "{$type}_archive" ] = [
						'label'   => \esc_html__( 'Archive', 'total' ),
						'width'   => "{$type}_archive_width",
						'height'  => "{$type}_archive_height",
						'crop'    => "{$type}_archive_image_crop",
						'section' => $type,
					];
					$sizes[ "{$type}_single" ] = [
						'label'   => \esc_html__( 'Post', 'total' ),
						'width'   => "{$type}_post_width",
						'height'  => "{$type}_post_height",
						'crop'    => "{$type}_post_image_crop",
						'section' => $type,
					];
				}
			}
		}
		return $sizes;
	}

	/**
	 * Custom Layouts.
	 */
	public function pre_get_posts( $query ) {
		if ( \is_admin() || ! \is_object( $query ) || ! $query->is_main_query() ) {
			return;
		}

		// Post type archive posts per page.
		if ( $query->is_post_type_archive() ) {
			$type = $query->query['post_type'];
			if ( $type && \array_key_exists( $type, $this->wpex_cpt_settings ) ) {
				$settings = $this->wpex_cpt_settings[ $type ];
				if ( ! empty( $settings['archive_posts_per_page'] ) ) {
					$query->set( 'posts_per_page', $settings['archive_posts_per_page'] );
				}
			}
		}

		// Taxonomy query.
		if ( $query->is_tax() ) {
			$types = $this->types;
			foreach ( $this->wpex_cpt_settings as $type => $settings ) {
				$taxonomies = ! empty( $types[ $type ]['taxonomies'] ) ? $types[ $type ]['taxonomies'] : '';
				if ( $taxonomies ) {
					foreach ( $taxonomies as $tax ) {
						if ( $query->is_tax( $tax ) && ! empty( $settings['archive_posts_per_page'] ) ) {
							$query->set( 'posts_per_page', $settings['archive_posts_per_page'] );
						}
					}
				}
			}
		}
	}

	/**
	 * Custom Layouts.
	 */
	public function layouts( $layout ) {

		// Single layout.
		if ( \is_singular() ) {
			$type = \get_post_type();
			if ( $type && \array_key_exists( $type, $this->wpex_cpt_settings ) ) {
				$settings = $this->wpex_cpt_settings[ $type ];
				if ( ! empty( $settings['single_layout'] ) ) {
					$layout = $settings['single_layout'];
				}
			}
		}

		// Post Type Archives.
		elseif ( \is_post_type_archive() ) {
			$type = \get_post_type();
			if ( $type && \array_key_exists( $type, $this->wpex_cpt_settings ) ) {
				$settings = $this->wpex_cpt_settings[ $type ];
				if ( ! empty( $settings['archive_layout'] ) ) {
					$layout = $settings['archive_layout'];
				}
			}
		}

		// Taxonomies.
		elseif ( \is_tax() ) {
			$types = $this->types;
			foreach ( $this->wpex_cpt_settings as $type => $settings ) {
				if ( ! empty( $types[ $type ] ) && $type == \get_post_type() ) {
					$taxonomies = ! empty( $types[ $type ]['taxonomies'] ) ? $types[ $type ]['taxonomies'] : '';
					if ( $taxonomies ) {
						foreach ( $taxonomies as $tax ) {
							if ( is_tax( $tax ) && ! empty( $settings['archive_layout'] ) ) {
								$layout = $settings['archive_layout'];
							}
						}
					}
				}
			}
		}

		return $layout;
	}

	/**
	 * Page Header Title Args.
	 */
	public function page_header_title_args( $args ) {
		$post_type = \get_post_type();

		if ( \is_singular() ) {
			if ( $post_type && \array_key_exists( $post_type, $this->wpex_cpt_settings ) ) {
				$settings = $this->wpex_cpt_settings[ $post_type ];
				if ( ! empty( $settings['single_page_header_title'] ) ) {
					$title = $settings['single_page_header_title'];
					$title = ( '{{title}}' == $title ) ? \get_the_title() : $title;
					$args['string'] = $title;
				}
			}
		} elseif ( \is_post_type_archive() ) {
			if ( $post_type && \array_key_exists( $post_type, $this->wpex_cpt_settings ) ) {
				$settings = $this->wpex_cpt_settings[ $post_type ];
				if ( ! empty( $settings['archive_page_header_title'] ) ) {
					$args['string'] = $settings['archive_page_header_title'];
				}
			}
		}

		return $args;
	}

	/**
	 * Register sidebars.
	 */
	public function register_sidebars( $sidebars ) {
		if ( $this->wpex_cpt_settings && \is_array( $this->wpex_cpt_settings ) ) {
			foreach ( $this->wpex_cpt_settings as $type => $settings ) {
				if ( ! empty( $settings['custom_sidebar'] ) ) {
					$id = $this->sanitize_sidebar_id( $settings['custom_sidebar'] );
					$sidebars[ $id ] = $settings['custom_sidebar'];
				}
			}
		}

		return $sidebars;
	}

	/**
	 * Get sidebar.
	 */
	public function get_sidebar( $sidebar ) {

		if ( ( \is_singular() && ! \is_page() && ! \is_attachment() ) || \is_post_type_archive() || \is_tax() ) {

			// Save types.
			$types = $this->types;

			// Loop through types and add sidebars.
			if ( $this->wpex_cpt_settings && \is_array( $this->wpex_cpt_settings ) ) {
				foreach ( $this->wpex_cpt_settings as $type => $settings ) {

					// Get custom sidebar.
					$custom_sidebar = ! empty( $settings['custom_sidebar'] ) ? $this->sanitize_sidebar_id( $settings['custom_sidebar'] ) : '';

					// If sidebar not empty lets set it.
					if ( $custom_sidebar ) {

						// Set for singular.
						if ( \is_singular( $type ) ) {
							$sidebar = $custom_sidebar;
						}

						// Set for post type.
						elseif ( \is_post_type_archive( $type ) ) {
							$sidebar = $custom_sidebar;
						}

						// Set for taxes - we need to loop through taxonomies set for type.
						elseif ( \is_tax() && ! empty( $types[ $type ] ) && $type == get_post_type() ) {
							$taxonomies = ! empty( $types[ $type ]['taxonomies'] ) ? $types[ $type ]['taxonomies'] : '';
							if ( $taxonomies ) {
								foreach ( $taxonomies as $tax ) {
									if ( \is_tax( $tax ) ) {
										$sidebar = $custom_sidebar;
									}
								}
							}
						}

					}

				}

			}
		}

		return $sidebar;
	}

	/**
	 * Taxonomy to use for post type meta.
	 */
	public function meta_tax( $taxonomy ) {
		if ( $type = \get_post_type() ) {
			$settings = $this->wpex_cpt_settings;
			$settings = ! empty( $settings[ $type ] ) ? $settings[ $type ] : '';
			if ( $settings && ! empty( $settings['main_taxonomy'] ) ) {
				$taxonomy = $settings['main_taxonomy'];
			}
		}

		return $taxonomy;
	}

	/**
	 * Entry classes.
	 */
	public function entry_columns( $columns ) {
		if ( $type = \get_post_type() ) {
			$settings = $this->wpex_cpt_settings;
			$settings = ! empty( $settings[ $type ] ) ? $settings[ $type ] : '';
			if ( $settings && ! empty( $settings['archive_grid_columns'] ) ) {
				$columns = $settings['archive_grid_columns'];
			}
		}
		return $columns;
	}

	/**
	 * Next Prev links.
	 */
	public function next_prev( $display, $post_type ) {
		if ( $post_type ) {

			// Get settings.
			$settings = $this->wpex_cpt_settings;
			$settings = ! empty( $settings[ $post_type ] ) ? $settings[ $post_type ] : '';

			// Check main taxonomy.
			if ( $settings && ! empty( $settings['next_prev'] ) ) {
				if ( 'false' == $settings['next_prev'] ) {
					$display = false;
				} elseif ( 'true' == $settings['next_prev'] ) {
					$display = true;
				}
			}

		}

		return $display;
	}

	/**
	 * Filter entry blocks.
	 */
	public function entry_blocks( $blocks, $type ) {
		if ( $type ) {
			$settings = $this->wpex_cpt_settings;
			$settings = ! empty( $settings[ $type ] ) ? $settings[ $type ] : '';
			if ( $settings && ! empty( $settings['entry_blocks'] ) ) {
				$blocks = $settings['entry_blocks'];
			}
		}

		return $blocks;
	}

	/**
	 * Filter single blocks.
	 */
	public function single_blocks( $blocks, $type ) {
		if ( $type ) {
			$settings = $this->wpex_cpt_settings;
			$settings = ! empty( $settings[ $type ] ) ? $settings[ $type ] : '';
			if ( $settings && ! empty( $settings['single_blocks'] ) ) {
				$blocks = $settings['single_blocks'];
			}
		}
		return $blocks;
	}

	/**
	 * Filter meta blocks.
	 */
	public function meta_blocks( $blocks, $type ) {
		if ( $type ) {

			// Get settings.
			$settings = $this->wpex_cpt_settings;
			$settings = ! empty( $settings[ $type ] ) ? $settings[ $type ] : '';

			// No settigns, return blocks.
			if ( ! $settings ) {
				return $blocks;
			}

			// Return entry meta blocks.
			if ( ! empty( $settings['entry_meta_blocks'] ) ) {
				if ( ! \is_singular() || ( \is_singular() && ! \is_main_query() ) ) {
					$blocks = $settings['entry_meta_blocks'];
				}
			}

			// Return single meta blocks.
			if ( ! empty( $settings['single_meta_blocks'] )
				&& \is_singular()
				&& \is_main_query()
			) {
				$blocks = $settings['single_meta_blocks'];
			}

		}

		return $blocks;
	}

	/**
	 * Sanitize sidebar ID.
	 */
	public function sanitize_sidebar_id( $id ) {
		$id = \wp_strip_all_tags( $id );
		$id = \str_replace( ' ', '_', $id );
		$id = \strtolower( $id );
		return $id;
	}

	/**
	 * Sanitize sidebar ID.
	 */
	public static function get_setting_val( $post_type = '', $setting = '' ) {
		$settings = \get_option( 'wpex_cpt_settings' );
		if ( $settings && \is_array( $settings ) ) {
			return $settings[ $post_type ][ $setting ] ?? null;
		}
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
