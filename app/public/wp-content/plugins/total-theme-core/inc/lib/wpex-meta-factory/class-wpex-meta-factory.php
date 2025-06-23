<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPEX_Meta_Factory', false ) ) {

	/**
	 * Meta Factory.
	 */
	class WPEX_Meta_Factory {

		/**
		 * Check if class has initialized.
		 */
		protected static $initialized = false;

		/**
		 * Check if core scripts are already enqueued.
		 */
		protected static $scripts_enqueued = false;

		/**
		 * Version.
		 */
		public $version = '2.1';

		/**
		 * Default metabox settings.
		 */
		protected $defaults = [
			'id'       => '',
			'title'    => '',
			'screen'   => [ 'post' ],
			'context'  => 'normal',
			'priority' => 'default',
			'classes'  => [],
			'fields'   => [],
		];

		/**
		 * Array of custom metabox settings.
		 */
		protected $metabox = [];

		/**
		 * Field prefix.
		 */
		protected $prefix = 'wpex_mf_';

		/**
		 * Register this class with the WordPress API.
		 */
		public function __construct( $metabox ) {
			$this->metabox = wp_parse_args( $metabox, $this->defaults );

			if ( empty( $this->metabox['screen'] ) || empty( $this->metabox['fields'] ) ) {
				return;
			}

			if ( is_string( $this->metabox['screen'] ) ) {
				$this->metabox['screen'] = [ $this->metabox['screen'] ];
			}

			add_action( 'load-post.php', [ $this, 'add_save_actions' ] );
			add_action( 'load-post-new.php', [ $this, 'add_save_actions' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'maybe_enqueue_scripts' ] );
			add_filter( 'vcex_custom_field_param_choices', [ $this, '_filter_vcex_custom_field_param_choices' ], 10, 2 );

			if ( ! self::$initialized ) {
				add_action( "wp_ajax_{$this->prefix}field_preview_ajax", [ $this, 'field_preview_ajax' ] );
				self::$initialized = true;
			}
		}

		/**
		 * Add metabox and save post actions.
		 */
		public function add_save_actions() {
			foreach ( $this->metabox['screen'] as $post_type ) {
				add_action( "add_meta_boxes_{$post_type}", [ $this, 'add_meta_box' ] );
				add_action( "save_post_{$post_type}", [ $this, 'save_meta_data' ] );
			}
		}

		/**
		 * Maybe enqueue class scripts.
		 */
		public function maybe_enqueue_scripts( $hook_suffix ) {
			if ( ! empty( $this->metabox['screen'] )
				&& in_array( $hook_suffix, [ 'post.php', 'post-new.php' ] )
			) {
				$screen = get_current_screen();
				if ( is_object( $screen ) && ! empty( $screen->post_type )
					&& in_array( $screen->post_type, $this->metabox['screen'] )
				) {
					$this->load_scripts();
				}
			}
		}

		/**
		 * The function responsible for creating the actual meta boxes.
		 */
		public function add_meta_box() {
			$box_id = "wpex-mf-metabox--{$this->metabox['id']}";

			add_meta_box(
				$box_id,
				$this->metabox['title'],
				[ $this, 'display_meta_box' ],
				$this->metabox['screen'],
				$this->metabox['context'],
				$this->metabox['priority']
			);

			if ( ! empty( $this->metabox['classes'] ) && is_array( $this->metabox['screen'] ) ) {
				foreach ( $this->metabox['screen'] as $screen_id ) {
					add_filter( "postbox_classes_{$screen_id}_{$box_id}", [ $this, 'postbox_classes' ] );
				}
			}
		}

		/**
		 * Add custom classes to the metabox.
		 */
		public function postbox_classes( $classes ) {
			if ( is_array( $this->metabox['classes'] ) ) {
				foreach ( $this->metabox['classes'] as $class ) {
					array_push( $classes, $class );
				}
			}
			return $classes;
		}

		/**
		 * Enqueue scripts and styles needed for the metaboxes.
		 */
		public function load_scripts() {
			$this->enqueue_metabox_scripts();
			$this->enqueue_metabox_styles();

			// Load icon selector always. We could loop through fields to see if it's needed.
			// But the files are very small.
			if ( ! self::$scripts_enqueued && function_exists( 'totaltheme_call_static' ) ) {
				totaltheme_call_static( 'Helpers\Icon_Select', 'enqueue_scripts' );
			}

			self::$scripts_enqueued = true;
		}

		/**
		 * Renders the content of the meta box.
		 */
		public function display_meta_box( $post ) {
			wp_nonce_field(
				"wpex_metabox_factory_{$this->metabox['id']}",
				"wpex_meta_factory_nonce_{$this->metabox['id']}"
			);

			$this->load_scripts();

			$fields = $this->get_metabox_fields();

			if ( ! $fields ) {
				return;
			}

			$tabs = $this->metabox['tabs'] ?? [];

			if ( count( $tabs ) > 1 ) {
				$tabs_fields = [];
				foreach ( $fields as $field ) {
					if ( isset( $field['tab'] ) ) {
						$tabs_fields[ $field['tab'] ][] = $field;
					} else {
						$tabs_fields['other'][] = $field;
					}
				}
				if ( isset( $tabs_fields['other'] ) ) {
					$tabs['other'] = esc_html( 'Other', 'total-theme-core' );
				}
				unset( $fields );
			}
			?>

			<div class="wpex-mf-metabox<?php echo isset( $tabs_fields ) ? ' wpex-mf-metabox--has-tabs' : ''; ?>"><?php
				// Render Tabs.
				if ( isset( $tabs_fields ) ) {
					$active_tab = ! empty( $this->metabox['active_tab'] ) ? $this->metabox['active_tab'] : array_key_first( $tabs );
					echo '<ul class="wpex-mf-metabox__tabs wp-tab-bar">';
					foreach ( $tabs as $tab_id => $tab_name ) {
						if ( $active_tab === $tab_id ) {
							$selected = 'true';
							$active_tab_class = ' wp-tab-active';
							$has_selected = true;
						} else {
							$selected = 'false';
							$active_tab_class = '';
						}
						echo '<li class="wpex-mf-metabox__tab' . $active_tab_class . '"><a href="#" role="button" aria-selected="' . $selected . '" aria-controls="wpex-mf-metabox__tab--' . esc_attr( $tab_id ) . '">' . esc_html( $tab_name ) . '</a></li>';
					}
					echo '</ul>';
				} ?>
				<?php if ( isset( $tabs_fields ) ) {
					foreach ( $tabs_fields as $tab_id => $tab_fields ) {
						$hidden_class = ( $active_tab !== $tab_id ) ? ' hidden' : '';
						echo '<div id="wpex-mf-metabox__tab--' . esc_attr( $tab_id ) . '" class="wp-tab-panel' . $hidden_class . '">';
						$this->render_section_table( $tab_fields, $post );
						echo '</div>';
					}
				} else {
					$this->render_section_table( $fields, $post );
				}
			?></div>
		<?php }

		/**
		 * Renders a metabox section.
		 */
		protected function render_section_table( $fields, $post ): void {
			?>
			<table class="form-table">
			<?php
			// Loop through sections and store meta output.
			foreach ( $fields as $key => $field ) {

				// Field defaults.
				$defaults = [
					'name'     => '',
					'id'       => '',
					'type'     => '',
					'desc'     => '',
					'desc_tip' => '',
					'default'  => '',
				];

				// Parse and extract.
				$field = wp_parse_args( $field, $defaults );

				// Notice field.
				if ( isset( $field['type'] ) && 'notice' === $field['type'] ) { ?>
					<tr<?php echo $this->get_conditional_data( $field ); ?>><?php echo wp_kses_post( $field['content'] ); ?></tr>
				<?php }
				
				// Standard field.
				else {
					
					$custom_field_keys = get_post_custom_keys();
					
					if ( is_array( $custom_field_keys ) && in_array( $field['id'], $custom_field_keys ) ) {
						$value = get_post_meta( $post->ID, $field['id'], true );
					} else {
						$value = $field['default'] ?? '';
					}

					if ( isset( $field['migrate'] ) && ! $value ) {
						$value = get_post_meta( $post->ID, $field['migrate'], true );
					}

					$this->render_field( $field, $value );
				}

			} // end foreach ?>
			</table>
			<?php
		}

		/**
		 * Returns the field type.
		 */
		protected function get_field_type( array $field ): string {
			return $field['type'] ?? 'text';
		}

		/**
		 * Renders a field.
		 */
		protected function render_field( $field, $value = '' ) {
			$field_id   = $field['id'];
			$field_name = $field['name'];
			$field_type = $this->get_field_type( $field );
			$field_desc = $field['description'] ?? $field['desc'] ?? '';
			$desc_tip   = $this->field_has_desc_tip( $field );

			if ( isset( $field['index'] ) ) {
				$field_id_parsed = str_replace( '][', '-', $field_id );
				$field_id_parsed = str_replace( ']', '', $field_id_parsed );
				$field_id_parsed = str_replace( '[', '-', $field_id_parsed );
			} else {
				$field_id_parsed = $field_id;
			}

			$tr_id = 'wpex-mf-tr--' . esc_attr( $field_id_parsed );

			if ( $field_desc ) {
				$desc_class = 'wpex-mf-desc';
				if ( $desc_tip ) {
					$desc_class .= ' wpex-mf-desc--has-tip';
				}
				$field_desc_html = '<p id="wpex-mf-desc--' . esc_attr( $field_id_parsed ) . '" class="' . esc_attr( $desc_class ) . '"';
					if ( $desc_tip ) {
						$field_desc_html .= ' hidden';
					}
				$field_desc_html .= '>' . wp_kses_post( $field_desc ) . '</p>';
			}
			?>

			<tr id="<?php echo esc_attr( $tr_id ); ?>" class="wpex-mf-tr wpex-mf-field--<?php echo esc_attr( $field['type'] ?? 'text' ); ?>"<?php echo $this->get_conditional_data( $field ); ?>>

				<?php if ( $field_name ) {
					?>

					<th>

						<div class="wpex-mf-label__wrap">
							
							<span class="wpex-mf-label"><?php

							if ( isset( $field['icon'] ) && function_exists( 'totaltheme_get_icon' ) ) {
								echo totaltheme_get_icon( $field['icon'], 'wpex-mf-label__icon' );
							}
							
							switch ( $field['type'] ) {
								case 'multi_select':
								case 'group':
									echo esc_html( wp_strip_all_tags( $field_name ) );
									break;
								default:
									?>
									<label class="wpex-mf-label" for="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>"><?php echo esc_html( wp_strip_all_tags( $field_name ) ); ?></label>
									<?php
									break;

							} ?>

							</span>

							<?php if ( $desc_tip ) { ?>
								<button type="button" class="wpex-mf-help-button" aria-expanded="false" aria-controls="wpex-mf-desc--<?php echo esc_attr( $field_id_parsed ); ?>"><span class="dashicons dashicons-editor-help"aria-hidden="true"></span><span class="screen-reader-text"><?php echo sprintf( esc_html_x( 'Help on: %s', 'Help on: *custom field name* screen reader text.', 'total-theme-core' ), $field_name ); ?></span></button>
							<?php } ?>

						</div>

						<?php if ( $field_desc ) {
							// @codingStandardsIgnoreLine
							echo $field_desc_html;
						} ?>

					</th>

					<?php
				} ?>

				<?php
				// Output field type.
				$method = "field_{$field_type}";

				if ( method_exists( $this, $method ) ) {

					$td_colspan = empty( $field_name ) ? '2' : '';
					?>
					<td colspan="<?php echo esc_attr( $td_colspan ); ?>"><?php

						// Render field type.
						$this->$method( $field, $value );

						// After field hook.
						if ( ! empty( $field['after_hook'] ) ) {
							echo '<div class="wpex-mf-after-hook">' . wp_kses_post( $field['after_hook'] ) . '</div>';
						}
						
						// Field description.
						if ( empty( $field_name ) && $field_desc ) {
							// @codingStandardsIgnoreLine
							echo $field_desc_html;
						}

					?></td>
					<?php
				}
				?>

			</tr>

		<?php
		}

		/**
		 * Render a group field type.
		 */
		protected function field_group( $field, $value ) {
			if ( empty( $field['fields'] ) ) {
				return;
			}

			$group_button = $field['group_button'] ?? esc_html__( 'Add New', 'total-theme-core' );
			$group_sort   = $field['group_sort'] ?? true;

			if ( $group_sort ) {
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
			?>
			<div class="wpex-mf-group-set"<?php echo $group_sort ? ' data-wpex-mf-sortable="1"' : ''; ?>><?php
				if ( empty( $value ) ) {
					$this->field_group_set( $field, $value, 0 );
				} elseif ( is_array( $value ) ) {
					$groups = $value;
					$groups_count = count( $groups );
					$index = 0;
					foreach ( $groups as $group_k => $group_v ) {
						$this->field_group_set( $field, $value, $index );
						$index++;
					}
				}
			?></div>
			<template class="wpex-mf-group-template"><?php $this->field_group_set( $field, '', 'TEMPLATE' ); ?></template>
			<button type="button" class="wpex-mf-add-group button-primary">&#65291; <?php echo esc_html( $group_button ); ?></button>
			<?php
		}

		/**
		 * Render singular field group
		 */
		protected function field_group_set( $field, $value, $index ) {
			$index       = 'TEMPLATE' === $index ? $index : absint( $index );
			$group_title = $field['group_title'] ?? esc_html__( 'Entry', 'total-theme-core' );
			?>
			<div class="wpex-mf-group">
				<div class="wpex-mf-group-header">
					<div class="wpex-mf-group-header-title"><?php esc_html_e( $group_title );?>&nbsp;<span class="wpex-mf-group-set-index"><?php echo esc_html( 'TEMPLATE' === $index ? 0 : $index + 1 ); ?></span></div>
					<div class="wpex-mf-group-header-actions">
						<button type="button" class="dashicons-before dashicons-trash wpex-mf-remove-group"><span class="screen-reader-text"><?php esc_html_e( 'Remove Group Set', 'total-theme-core' ); ?></span></button>
					</div>
				</div>
				<div class="wpex-mf-group-fields">
					<table>
						<?php foreach ( $field['fields'] as $field_k => $field_v ) {
							if ( 'TEMPLATE' === $index ) {
								$field_value = $field_v['default'] ?? '';
								$field_v['id'] = $field['id'] . '[0][' . $field_v['id'] . ']';
								$field_v['index'] = 0;
							} else {
								$field_value = $value[ $index ][ $field_v['id'] ] ?? $field_v['default'] ?? '';
								$field_v['id'] = $field['id'] . '[' . $index . '][' . $field_v['id'] . ']';
								$field_v['index'] = $index;
							}
							$this->render_field( $field_v, $field_value );
						} ?>
					</table>
				</div>
			</div>
			<?php
		}

		/**
		 * Render a text field type.
		 */
		protected function field_text( $field, $value ) {
			$required    = isset( $field['required'] ) ? ' required' : '';
			$maxlength   = isset( $field['maxlength'] ) ? ' maxlength="' . esc_attr( floatval( $field['maxlength'] ) ) . '"' : '';
			$placeholder = ! empty( $field['placeholder'] ) ? ' placeholder="' . esc_attr( esc_attr( $field['placeholder'] ) ) . '"' : '';
			?>
				<input class="wpex-mf-metabox__input wpex-mf-metabox__input--wide" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" <?php echo $required . $maxlength . $placeholder; ?>>
			<?php
		}

		/**
		 * Render a date field type.
		 */
		protected function field_date( $field, $value ) {
			$value = strtotime( $value );

			if ( $value ) {
				$value = date( 'Y-m-d', $value );
			}

			$required = isset( $field['required'] ) ? ' required' : '';
			$min      = isset( $field['min'] ) ? ' min="' . esc_attr( $field['min'] ) . '"' : '';
			$max      = isset( $field['max'] ) ? ' max="' . esc_attr( $field['max'] ) . '"' : '';
			?>

				<input class="wpex-mf-metabox__input wpex-mf-metabox__input--wide" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" type="date" value="<?php echo esc_attr( $value ); ?>" <?php echo $required . $min . $max; ?>>
			<?php
		}

		/**
		 * Render a URL field type.
		 */
		protected function field_url( $field, $value ) {
			$required    = isset( $field['required'] ) ? ' required' : '';
			$maxlength   = isset( $field['maxlength'] ) ? ' maxlength="' . floatval( $field['maxlength'] ) . '"' : '';
			$placeholder = ! empty( $field['placeholder'] ) ? ' placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '';
			?>
				<input class="wpex-mf-metabox__input wpex-mf-metabox__input--wide" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" type="url" value="<?php echo esc_attr( $value ); ?>" <?php echo $required . $maxlength . $placeholder; ?>>
			<?php
		}

		/**
		 * Render a number field type.
		 */
		protected function field_number( $field, $value ) {
			$step_safe = ! empty( $field['step'] ) ? ' step="' . esc_attr( $field['step'] ) . '"' : '';
			$min_safe  = isset( $field['min'] ) ? ' min="' . esc_attr( $field['min'] ) . '"' : '';
			$max_safe  = isset( $field['max'] ) ? ' max="' . esc_attr( $field['max'] ) . '"' : '';
			?>
				<input class="wpex-mf-metabox__input wpex-mf-metabox__input--wide" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" type="number" value="<?php echo esc_attr( $value ); ?>"<?php echo $step_safe . $min_safe . $max_safe; ?>>
			<?php
		}

		/**
		 * Render a textare field type.
		 */
		protected function field_textarea( $field, $value ) {
			$rows = $field['rows'] ?? 4;
			?>
			<textarea class="wpex-mf-metabox__input wpex-mf-metabox__input--wide" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" rows="<?php echo absint( $rows ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>"><?php echo wp_kses_post( $value ); ?></textarea>
			<?php
		}

		/**
		 * Render an iFrame field type.
		 */
		protected function field_iframe( $field, $value ) {
			$rows = $field['rows'] ?? 4;
			?>
			<textarea class="wpex-mf-metabox__input wpex-mf-metabox__input--wide" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" rows="<?php echo absint( $rows ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>"><?php echo wp_kses( $value, $this->get_iframe_allowed_tags() ); ?></textarea>
			<?php
		}

		/**
		 * Render an HTML field.
		 */
		protected function field_html( $field, $value ) {
			$rows = $field['rows'] ?? 4;
			?>
			<textarea class="wpex-mf-metabox__input wpex-mf-metabox__input--wide wpex-mf-metabox__input--noresize" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" rows="<?php echo absint( $rows ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>"><?php echo wp_kses_post( $value ); ?></textarea>
			<?php
		}

		/**
		 * Render a wp_editor field type.
		 */
		protected function field_wp_editor( $field, $value ) {
			wp_editor(
				wp_kses_post( $value ),
				$this->parse_field_id( $field['id'] ),
				[
					'textarea_name' => esc_attr( $this->add_prefix( $field['id'] ) ),
					'textarea_rows' => $field['rows'] ?? 4,
					'media_buttons' => wp_validate_boolean( $field['media_buttons'] ?? false ),
					'quicktags'     => wp_validate_boolean( $field['quicktags'] ?? false ),
					'teeny'         => $field['teeny'] ?? false,
				]
			);
		}

		/**
		 * Render a checkbox field type.
		 */
		protected function field_checkbox( $field, $value ) {
			$value = $value ? true : false;
			?>
				<input class="wpex-mf-metabox__input wpex-mf-metabox__input--wide" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" type="checkbox" <?php checked( $value, true, true ); ?>>
			<?php
		}

		/**
		 * Render a select field type.
		 */
		protected function field_select( $field, $value ) {
			$choices      = $field['choices'] ?? $field['options'] ?? [];
			$autocomplete = ! empty( $field['autocomplete'] ) ? $field['autocomplete'] : []; // @todo
			if ( is_callable( $choices ) ) {
				$choices = call_user_func( $choices );
			}
			if ( empty( $choices ) || ! is_array( $choices ) ) {
				return;
			}
			?>
			<select class="wpex-mf-metabox__input wpex-mf-metabox__input--wide" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>"><?php $this->render_select_options( $choices, $value ); ?></select>
			<?php
		}

		/**
		 * Helper function used to render select options.
		 */
		protected function render_select_options( $choices = [], $selected = '' ): void {
			foreach ( $choices as $choice_k => $choice_v ) {
				if ( is_array( $choice_v ) ) {
					$sub_choices = $choice_v['choices'] ?? $choice_v['options'] ?? [];
					if ( $sub_choices ) { ?>
						<?php if ( ! empty( $choice_v['label'] ) ) { ?>
							<optgroup label="<?php echo esc_attr( $choice_v['label'] ); ?>">
								<?php $this->render_select_options( $sub_choices, $selected ); ?>
							</optgroup>
						<?php } else { ?>
							<?php $this->render_select_options( $sub_choices, $selected ); ?>
						<?php } ?>
					<?php }
				} else { ?>
					<option value="<?php echo esc_attr( $choice_k ); ?>" <?php selected( $selected, $choice_k, true ); ?>><?php echo esc_html( $choice_v ); ?></option>
				<?php
				}
			}
		}

		/**
		 * Render a button group field.
		 */
		protected function field_button_group( $field, $value ) {
			$choices = $field['choices'] ?? $field['options'] ?? [];

			if ( is_callable( $choices ) ) {
				$choices = call_user_func( $choices );
			}

			if ( empty( $choices ) || ! is_array( $choices ) ) {
				return;
			}
			?>
			<div class="wpex-mf-metabox__button-group">
				<?php foreach ( $choices as $choice_v => $name ) {
					$button_class = $choice_v ? 'wpex-mf-metabox__button--' . sanitize_html_class( sanitize_key( $name ) ) : '';
					?>
					<button class="<?php echo esc_attr( trim( $button_class ) ); ?>"<?php echo $choice_v ? 'value="' . esc_attr( $choice_v ) . '"' : ''; ?><?php echo ( $value === $choice_v ) ? ' aria-pressed="true"' : ''; ?>><?php echo esc_html( $name ); ?></button>
				<?php } ?>
			</div>
			<input class="wpex-mf-metabox__input" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" type="hidden" value="<?php echo esc_attr( $value ); ?>">
			<?php
		}

		/**
		 * Render a select field type.
		 */
		protected function field_select_template( $field, $value ) {
			if ( function_exists( 'totaltheme_call_non_static' ) ) {
				totaltheme_call_non_static( 'Theme_Builder', 'template_select', [
					'id'            => $this->parse_field_id( $field['id'] ),
					'class'         => 'wpex-mf-metabox__input wpex-mf-metabox__input--wide',
					'name'          => $this->get_field_name( $field ),
					'selected'      => $value,
					'template_type' => 'single',
					'echo'          => true,
				] );
			} else {
				$this->field_text( $field, $value );
			}
		}

		/**
		 * Render a color field type.
		 */
		protected function field_color( $field, $value ) {
			if ( function_exists( 'totaltheme_component' ) ) {
				totaltheme_component( 'color', [
					'id'                 => $this->parse_field_id( $field['id'] ),
					'input_name'         => $this->get_field_name( $field ),
					'value'              => $value,
					'allow_global'       => $field['allow_global'] ?? true,
					'exclude'            => 'extra,theme',
					'dropdown_placement' => 'right',
				] );
			} else {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				?>
					<input class="wpex-mf-metabox__input wpex-mf-colorpicker" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>">
				<?php
			}
		}

		/**
		 * Render an icon select field type.
		 */
		protected function field_icon_select( $field, $value ) {
			if ( function_exists( 'totaltheme_call_static' ) ) {
				totaltheme_call_static( 'Helpers\Icon_Select', 'render_form', [
					'selected'   => $value,
					'choices'    => [],
					'input_name' => $this->add_prefix( $field['id'] ),
				] );
			}
		}

		/**
		 * Render a multi_select field type.
		 */
		protected function field_multi_select( $field, $value ) {
			$value = is_array( $value ) ? $value : [];
			$choices = $field['choices'] ?? [];
			if ( empty( $choices ) ) {
				return;
			}
			?>
				<fieldset>
					<?php foreach ( $choices as $choice_v => $name ) {
						$field_id = $field['id'] . '_' . $choice_v;
						?>
						<input class="wpex-mf-metabox__input" id="<?php echo $this->parse_field_id( $field_id ); ?>" type="checkbox" name="<?php echo esc_attr( $this->add_prefix( $field['id'] ) ); ?>[]" value="<?php echo esc_attr( $choice_v ); ?>" <?php checked( in_array( $choice_v, $value ), true, true ); ?>>
						<label for="<?php echo $this->parse_field_id( $field_id ); ?>"><?php echo esc_html( $name ); ?></label>
						<br>
					<?php } ?>
				</fieldset>
			<?php
		}

		/**
		 * Render an upload field type.
		 */
		protected function field_upload( $field, $value ) {
			wp_enqueue_media();

			// Old Redux cleanup.
			if ( is_array( $value ) ) {
				$value = $value['url'] ?? '';
			}

			$required    = isset( $field['required'] ) ? ' required' : '';
			$placeholder = ! empty( $field['placeholder'] ) ? ' placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '';
			$return      = ! empty( $field['return'] ) ? $field['return'] : 'url';
			$media_type  = $field['media_type'] ?? 'all';
			$show_input  = 'url' == $return || isset( $field['show_input'] );

			if ( is_array( $media_type ) ) {
				$media_type = implode( ',', $media_type );
			}

			// ID based upload (displays preview).
			if ( 'id' === $return || ( isset( $field['preview'] ) && true === $field['preview'] ) ) {
				$preview_visible = (bool) $value;
				if ( $value ) {
					$attachment_id = $value;
					if ( ! is_numeric( $attachment_id ) ) {
						$attachment_id = attachment_url_to_postid( $attachment_id );
					}
					if ( ! $attachment_id || ! get_post_mime_type( $attachment_id ) ) {
						$preview_visible = false;
						$show_input = true;
					}
				}

				?>

				<div class="wpex-mf-upload-preview<?php echo ! $preview_visible ? ' wpex-mf-upload-preview--empty' : ''; ?>">
					<div class="wpex-mf-upload-preview__content<?php echo ! $preview_visible ? ' hidden' : ''; ?>"><?php $this->upload_field_attachment_preview( $value ); ?></div>
					<a href="#" class="wpex-mf-upload-remove" role="button"><svg fill="currentColor" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg><span class="screen-reader-text"><?php esc_html_e( 'remove selected icon', 'total-theme-core' ); ?></span></a>
					<div class="wpex-mf-upload-preview__loader"><svg viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg"><circle cx="18" cy="18" r="18" fill="#a2a2a2" fill-opacity=".5"/><circle cx="18" cy="8" r="4" fill="#fff"><animateTransform attributeName="transform" dur="1100ms" from="0 18 18" repeatCount="indefinite" to="360 18 18" type="rotate"/></circle></svg></div>
				</div>

				<div class="wpex-mf-upload-actions">
					<input class="wpex-mf-metabox__input wpex-mf-metabox__input--wide" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" type="<?php echo $show_input ? 'text' : 'hidden'; ?>" value="<?php echo esc_attr( $value ); ?>" data-selection="<?php echo esc_attr( $return ); ?>" <?php echo $required . $placeholder; ?>>
					<button class="wpex-mf-upload button-secondary" type="button" data-wpex-mf-media-type="<?php echo esc_attr( $media_type ); ?>"><?php
						switch ( $media_type ) {
							case 'image':
								esc_html_e( 'Select Image', 'total-theme-core' );
								break;
							case 'video':
								esc_html_e( 'Select Video', 'total-theme-core' );
								break;
							case 'audio':
								esc_html_e( 'Select Audio', 'total-theme-core' );
								break;
							case 'font':
								esc_html_e( 'Select Font', 'total-theme-core' );
								break;
							case 'all':
							default:
								esc_html_e( 'Select File', 'total-theme-core' );
								break;
						}
					?></button>
				</div>

				<?php

			}

			// Standard upload
			else { ?>
				<input class="wpex-mf-metabox__input" id="<?php echo esc_attr( $this->parse_field_id( $field['id'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" data-selection="<?php echo esc_attr( $return ); ?>" <?php echo $required . $placeholder; ?>>
				<button class="wpex-mf-upload button-secondary" type="button" data-wpex-mf-media-type="<?php echo esc_attr( $media_type ); ?>"><?php esc_html_e( 'Upload', 'total-theme-core' ); ?></button>
			<?php

			}
		}

		/**
		 * Save metabox data.
		 */
		public function save_meta_data( $post_id ) {
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Check if our nonce is set.
			if ( ! isset( $_POST["wpex_meta_factory_nonce_{$this->metabox['id']}"] ) ) {
				return;
			}

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST["wpex_meta_factory_nonce_{$this->metabox['id']}" ] ) ),
				"wpex_metabox_factory_{$this->metabox['id']}" )
			) {
				return;
			}

			// Check the user's permissions.
			if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}

			/* OK, it's safe for us to save the data now. Now we can loop through fields */
			foreach ( $this->get_metabox_fields() as $field ) {
				if ( isset( $field['dont_save'] ) || 'notice' === $field['type'] ) {
					continue;
				}

				$value               = '';
				$field_id            = $field['id'];
				$prefixed_field_id   = $this->prefix . $field_id;
				$new_value           = $_POST[ $prefixed_field_id ] ?? '';

				if ( $field['type'] === 'group' ) {
					$group_fields = $field['fields'];
					if ( ! empty( $new_value ) && is_array( $new_value ) ) {
						$new_value_sanitized = [];
						// Loop through each group
						foreach ( $new_value as $new_value_k => $new_value_v ) {
							if ( empty( $new_value_v ) || ! is_array( $new_value_v ) ) {
								continue;
							}
							// Loop through each item in each group to sanitize the data
							foreach ( $new_value_v as $new_group_value_k => $new_group_value_v ) {
								$new_value_field = [];
								foreach ( $group_fields as $group_field ) {
									if ( $new_group_value_k === $group_field['id'] ) {
										$new_value_field = $group_field;
										break;
									}
								}
								$new_value_sanitized[ $new_value_k ][ $new_group_value_k ] = $this->sanitize_value_for_db( $new_group_value_v, $new_value_field );
							}
						}
						update_post_meta( $post_id, $field_id, $new_value_sanitized );
					} else {
						delete_post_meta( $post_id, $field_id );
					}
				} else {

					// Migrate fields.
					if ( isset( $field['migrate'] ) && get_post_meta( $post_id, $field['migrate'] ) ) {
						delete_post_meta( $post_id, $field['migrate'] );
					}

					// Make sure field exists and if so validate the data
					if ( $new_value ) {

						// Sanitize field before inserting into the database
						$new_val_escaped = $this->sanitize_value_for_db( $new_value, $field );

						// Update meta if value exists
						if ( $new_val_escaped ) {
							update_post_meta( $post_id, $field_id, $new_val_escaped );
						}

						// Delete if value is empty
						else {
							delete_post_meta( $post_id, $field_id );
						}

					} else {

						if ( 'checkbox' === $field['type'] && ! empty( $field['default'] ) ) {
							update_post_meta( $post_id, $field_id, 0 );
						} else {
							delete_post_meta( $post_id, $field_id );
						}

					}

				}

			}

		}

		/**
		 * Returns the field id.
		 */
		protected function parse_field_id( $id ): string {
			$id = str_replace( '][', '-', $id );
			$id = str_replace( '[', '-', $id );
			$id = str_replace( ']', '', $id );
			return 'wpex-mf-field--' . esc_attr( $id );
		}

		/**
		 * Returns the field name.
		 */
		protected function get_field_name( $field ): string {
			return $this->add_prefix( $field['id'] );
		}

		/**
		 * Sanitize input values before inserting into the database.
		 */
		protected function sanitize_value_for_db( $input, $field ) {
			switch ( $this->get_field_type( $field ) ) {
				case 'date':
					if ( $timestamp = strtotime( $input ) ) {
						return sanitize_text_field( date( 'Ymd', $timestamp ) ); // save date using same format as ACF.
					}
					break;
				case 'url':
					return sanitize_url( $input );
					break;
				case 'number':
					return (float) sanitize_text_field( $input );
					break;
				case 'textarea':
					return sanitize_textarea_field( $input );
					break;
				case 'html':
					return wp_kses_post( wp_check_invalid_utf8( trim( $input ) ) );
					break;
				case 'iframe':
					return wp_kses( wp_check_invalid_utf8( trim( $input ) ), $this->get_iframe_allowed_tags() );
					break;
				case 'wp_editor':
					return wp_kses_post( trim( str_replace( '<br data-mce-bogus="1">', '', wp_check_invalid_utf8( $input ) ) ) );
					break;
				case 'checkbox':
					return isset( $input ) ? 1 : 0;
					break;
				case 'button_group':
				case 'select':
					$choices = $field['choices'] ?? [];
					if ( is_callable( $choices ) ) {
						$choices = call_user_func( $choices );
					}
					if ( in_array( $input, $choices, true ) || array_key_exists( $input, $choices ) ) {
						return sanitize_text_field( $input );
					}
					break;
				case 'multi_select':
					if ( ! is_array( $input ) ) {
						return $field['default'] ?? [];
					}
					$checks = true;
					foreach ( $input as $v ) {
						if ( ! in_array( $v, $field['choices'], true ) && ! array_key_exists( $v, $field['choices'] ) ) {
							$checks = false;
							break;
						}
					}
					return $checks ? array_map( 'sanitize_text_field', $input ) : [];
					break;
				case 'upload':
					$return = ! empty( $field['return'] ) ? $field['return'] : 'url';
					switch ( $return ) {
						case 'url':
							return esc_url_raw( $input );
							break;
						default:
							// @important we don't use (int) to prevent issues with old metabox.
							return sanitize_textarea_field( $input );
							break;
					}
					break;
				default:
					if ( isset( $field['allow_html'] ) && true === $field['allow_html'] ) {
						return wp_kses_post( wp_check_invalid_utf8( trim( $input ) ) );
					} else {
						return sanitize_text_field( $input );
					}
					break;
			}
		}

		/**
		 * Upload field preview.
		 */
		protected function upload_field_attachment_preview( $attachment_id = '' ): void {
			if ( ! $attachment_id ) {
				return;
			}

			if ( ! is_numeric( $attachment_id ) ) {
				$attachment_id = attachment_url_to_postid( $attachment_id );
			}

			if ( ! $attachment_id ) {
				return;
			}

			$mime_type = get_post_mime_type( $attachment_id );
			
			if ( ! $mime_type ) {
				return;
			}

			$is_image = str_starts_with( $mime_type, 'image/' );

			echo '<div class="wpex-mf-upload-preview__icon">';

			if ( $is_image ) {
				echo wp_get_attachment_image( $attachment_id, [ 50, 9999 ] );
			} elseif ( str_starts_with( $mime_type, 'video/' ) ) {
				echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM48 368v32c0 8.8 7.2 16 16 16H96c8.8 0 16-7.2 16-16V368c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V368c0-8.8-7.2-16-16-16H416zM48 240v32c0 8.8 7.2 16 16 16H96c8.8 0 16-7.2 16-16V240c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V240c0-8.8-7.2-16-16-16H416zM48 112v32c0 8.8 7.2 16 16 16H96c8.8 0 16-7.2 16-16V112c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16zM416 96c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V112c0-8.8-7.2-16-16-16H416zM160 128v64c0 17.7 14.3 32 32 32H320c17.7 0 32-14.3 32-32V128c0-17.7-14.3-32-32-32H192c-17.7 0-32 14.3-32 32zm32 160c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H320c17.7 0 32-14.3 32-32V320c0-17.7-14.3-32-32-32H192z"/></svg>';
			} elseif ( str_starts_with( $mime_type, 'audio/' ) ) {
				echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M499.1 6.3c8.1 6 12.9 15.6 12.9 25.7v72V368c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6V147L192 223.8V432c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6V200 128c0-14.1 9.3-26.6 22.8-30.7l320-96c9.7-2.9 20.2-1.1 28.3 5z"/></svg>';
			} elseif ( str_contains( $mime_type, 'font' ) ) {
				echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M254 52.8C249.3 40.3 237.3 32 224 32s-25.3 8.3-30 20.8L57.8 416H32c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32h-1.8l18-48H303.8l18 48H320c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H390.2L254 52.8zM279.8 304H168.2L224 155.1 279.8 304z"/></svg>';
			}

			echo '</div>';

			$file      = get_attached_file( $attachment_id );
			$file_name = esc_html( wp_basename( $file ) );
			$meta      = wp_get_attachment_metadata( $attachment_id );

			echo '<div class="wpex-mf-upload-preview__meta">';

				// Display file name.
				echo '<strong>' . esc_html__( 'File', 'total-theme-core' ) . '</strong>: ';

				if ( $edit_link = get_edit_post_link( $attachment_id ) ) {
					echo '<a href="' . esc_url( $edit_link ) . '" target="_blank" title="' . esc_attr__( 'Edit File in New Tab', 'total-theme-core' ) . '">' . esc_html( $file_name ) . ' &#8599;</a>';
				} else {
					echo esc_html( $file_name );
				}

				// Display alt.
				if ( $is_image && $alt = get_post_meta( $attachment_id , '_wp_attachment_image_alt', true ) ) {
					echo '<br><strong>' . esc_html__( 'Alternative Text' ) . '</strong>: ' . esc_html( $alt );
				}

				// Display file size.
				$file_size = $meta['filesize'] ?? '';

				if ( ! $file_size && file_exists( $file ) ) {
					$file_size = filesize( $file );
				}

				if ( $file_size ) {
					echo '<br><strong>' . esc_html__( 'Size' ) . '</strong>: ' . size_format( $file_size );
				}

			echo '</div>';
		}

		/**
		 * Grabs attachment preview via AJAX.
		 */
		public function field_preview_ajax() {
			check_ajax_referer( "{$this->prefix}ajax_nonce", 'nonce' );

			if ( ! empty( $_POST['field_value'] ) ) {
				echo $this->upload_field_attachment_preview( sanitize_text_field( $_POST['field_value'] ) );
			}

			wp_die();
		}

		/**
		 * Enqueues metabox scripts.
		 */
		protected function enqueue_metabox_scripts() {
			if ( ! self::$scripts_enqueued ) {
				if ( wp_script_is( 'totaltheme-components', 'registered' ) ) {
					wp_enqueue_script( 'totaltheme-components' );
					wp_enqueue_style( 'totaltheme-components' );
				}

				wp_enqueue_script(
					'totalthemecore-admin-meta-factory',
					totalthemecore_get_js_file( 'admin/meta-factory' ),
					[ 'jquery' ],
					$this->version,
					true
				);
				wp_localize_script(
					'totalthemecore-admin-meta-factory',
					'totalthemecore_admin_meta_factory_params',
					[
						'delete_group_confirm' => esc_html__( 'Please click ok to confirm.', 'total-theme-core' ),
						'ajax_nonce'           => esc_js( wp_create_nonce( $this->prefix . 'ajax_nonce' ) ),
					]
				);
			}

			$custom_scripts = $this->metabox['scripts'] ?? [];

			if ( is_callable( $custom_scripts ) ) {
				$custom_scripts = call_user_func( $custom_scripts );
			}

			if ( is_array( $custom_scripts ) ) {
				foreach ( $custom_scripts as $script_args ) {
					call_user_func_array( 'wp_enqueue_script', $script_args );
				}
			}
		}

		/**
		 * Enqueues metabox styles.
		 */
		protected function enqueue_metabox_styles(): void {
			if ( ! self::$scripts_enqueued ) {
				wp_enqueue_style(
					'totalthemecore-admin-meta-factory',
					totalthemecore_get_css_file( 'admin/meta-factory' ),
					[ 'wp-components' ],
					$this->version
				);
			}

			if ( isset( $this->metabox['styles'] ) ) {
				foreach ( $this->metabox['styles'] as $args ) {
					call_user_func_array( 'wp_enqueue_style', $args );
				}
			}
		}

		/**
		 * Adds prefix to string.
		 */
		protected function add_prefix( string $string = '' ): string {
			return $this->prefix . $string;
		}

		/**
		 * Checks if the current field has a desc tip.
		 */
		protected function field_has_desc_tip( $field = [] ): bool {
			return ( ! empty( $field['desc_tip'] ) && true === $field['desc_tip'] );
		}

		/**
		 * Returns the metabox fields.
		 */
		protected function get_metabox_fields(): array {
			$fields = $this->metabox['fields'] ?? [];
			if ( is_callable( $fields ) ) {
				$fields = call_user_func( $fields );
			}
			return (array) $fields;
		}

		/**
		 * Returns allowed html tags for iFrame fields.
		 */
		protected function get_iframe_allowed_tags(): array {
			return [
				'iframe' => [
					'src'             => [],
					'height'          => [],
					'width'           => [],
					'frameborder'     => [],
					'allowfullscreen' => [],
					'allow'           => [],
				],
			];
		}

		/**
		 * Filters WPB custom field param choices.
		 */
		public function _filter_vcex_custom_field_param_choices( $choices = [], $allowed = '' ) {
			$fields = $this->get_metabox_fields();
			if ( is_array( $fields ) ) {
				$public_fields = [];
				foreach ( $fields as $field ) {
					if ( isset( $field['public'] ) && true === $field['public'] ) {
						if ( $this->is_field_type_allowed( $this->get_field_type( $field ), $allowed ) ) {
							$public_fields[ $field['id'] ] = $field['name'];
						}
					}
				}
				if ( $public_fields ) {
					$choices[ $this->metabox['title'] ] = [
						'label' => $this->metabox['title'],
						'options' => $public_fields,
					];
				}
			}
			return $choices;
		}

		/**
		 * Helper function checks if a specific field type is allowed.
		 */
		private function is_field_type_allowed( $type, $allowed ): bool {
			$allowed_types = [
				'percent' => [ 'number', 'text' ],
				'image'   => [ 'upload', 'image' ],
				'video'   => [ 'upload', 'video', 'iframe' ],
				'link'    => [ 'url' ],
				'text'    => [ 'text', 'textarea', 'wp_editor', 'select', 'button_group' ],
				'color'   => [ 'color' ],
				'icon'    => [ 'icon_select' ],
			];
			return ! isset( $allowed_types[ $allowed ] ) || in_array( $type, $allowed_types[ $allowed ], true );
		}

		/**
		 * Helper function adds conditinal data to the table rows.
		 */
		private function get_conditional_data( $field ): string {
			if ( empty( $field['conditional_logic'] ) || ! is_array( $field['conditional_logic'] ) ) {
				return '';
			}
			return ' data-wpex-mf-condition="' . esc_attr( \wp_json_encode( $field['conditional_logic'] ) ) . '"';
		}

	}

}
