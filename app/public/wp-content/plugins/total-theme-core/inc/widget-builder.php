<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * Widget Builder.
 */
class WidgetBuilder extends \WP_Widget {

	/**
	 * Widget name.
	 */
	public $name = '';

	/**
	 * Widget id_base.
	 */
	public $id_base = '';

	/**
	 * Widget fields.
	 */
	private $fields = [];

	/**
	 * Return correct branding string.
	 */
	public function branding(): string {
		if ( \function_exists( 'wpex_get_theme_branding' ) ) {
			$branding = \wpex_get_theme_branding();
			return $branding ? "{$branding} - " : '';
		} else {
			return 'Total - ';
		}
	}

	/**
	 * Create Widget.
	 */
	public function create_widget( $args ): void {
		$this->name    = \wp_strip_all_tags( $args['name'] );
		$this->id_base = \wp_strip_all_tags( $args['id_base'] );
		$this->options = $args['options'] ?? '';
		$this->fields  = $args['fields'];

		$this->options = \apply_filters( $this->id_base . '_widget_options', $this->options );

		parent::__construct(
			$this->id_base,
			$this->name,
			$this->options
		);
	}

	/**
	 * Return default values.
	 */
	public function get_defaults(): array {
		if ( empty( $this->fields ) || ! \is_array( $this->fields ) ) {
			return [];
		}
		$defaults = [];
		foreach ( $this->fields as $field ) {
			if ( empty( $field['default'] ) && isset( $field['choices'] ) && \is_array( $field['choices'] ) ) {
				reset( $field['choices'] );
				$field['default'] = key( $field['choices'] );
			}
			$defaults[ $field['id'] ] = $field['default'] ?? '';
		}
		return $defaults;
	}

	/**
	 * Parse insance for live output.
	 */
	public function parse_instance( $instance ) {
		$defaults = $this->get_defaults();
		$instance = \wp_parse_args( $instance, $defaults );
		foreach ( $instance as $k => $v ) {
			if ( ! isset( $v ) && isset( $defaults[ $k ] ) ) {
				$instance[ $k ] = $defaults[ $k ];
			}
		}
		return $instance;
	}

	/**
	 * Output widget title.
	 */
	public function widget_title( $args, $instance ): void {
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . \apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		foreach ( $this->fields as $field ) {

			$field_id   = $field['id'];
			$field_type = $field['type'];
			$field_val  = $new_instance[$field_id] ?? null;
			$default    = $field['default'] ?? '';

			if ( 'notice' === $field_type ) {
				continue;
			}

			/* Field has value */
			if ( $field_val ) {

				switch ( $field_type ) {

					// Save checkboxes.
					case 'checkbox':
						$instance[ $field_id ] = (bool) true;
						break;

					// Save selects.
					case 'select':
						$array_to_check = [];
						if ( \is_array( $field['choices'] ) ) {
							$array_to_check = $field['choices'];
						} else {
							$method = 'choices_' . $field['choices'];
							if ( \method_exists( $this, $method ) ) {
								$array_to_check = $this->$method( $field );
							}
						}
						$instance[ $field_id ] = ( \array_key_exists( $field_val, $array_to_check ) ? $field_val : $default );
						break;

					// Save repeaters.
					case 'repeater':
						$fields = $field[ 'fields' ];
						$new_val = [];

						foreach ( $fields as $field_k => $field_v ) {
							$subfield_id       = $field_v['id'];
							$subfield_type     = $field_v['type'];
							$subfield_sanitize = $field_v['sanitize'] ?? $subfield_type;

							if ( empty( $field_val[ $subfield_id ] ) ) {
								$new_val = $new_instance[ $field_id ]; // Gutenberg block fix - because apparently the widget editor saves multiple times.
								break;
							}

							$field_vals = $field_val[ $subfield_id ];

							$count = 1;
							foreach ( $field_vals as $field_vals_k => $field_vals_v ) {
								if ( $count == \count( $field_vals ) ) {
									continue;
								}
								$count++;
								$new_val[ $field_vals_k ][ $subfield_id ] = $this->sanitize_field_value( $field_v, $field_vals_v );
							}

						}

						$instance[$field_id] = $new_val;

						break;

					// Save all other fiels.
					default:
						$instance[ $field_id ] = $this->sanitize_field_value( $field, $field_val );
						break;
				}

			}

			/* Field value is empty */
			else {

				if ( 'checkbox' === $field_type ) {
					$instance[ $field_id ] = (bool) false;
				} else {
					$instance[ $field_id ] = '';
				}

			}

		}
		return $instance;
	}

	/**
	 * Helper function returns correct sanitization for the field type.
	 */
	private function sanitize_field_value( $field, $value ) {
		$sanitize = $field['sanitize'] ?? $field['type'];
		if ( 'text' === $sanitize || 'image' === $sanitize || 'media_upload' === $sanitize ) {
			$sanitize = 'text_field';
		}
		if ( 'url' === $sanitize ) {
			// fix because esc_url removes %20
			if ( \str_contains( $value, '@' ) ) {
				if ( \is_email( $value ) ) {
					return 'mailto:' . \wp_strip_all_tags( $value );
				} else {
					return \wp_strip_all_tags( $value );
				}
			} else {
				return \sanitize_url( $value );
			}
		} else {
			if ( \function_exists( 'wpex_sanitize_data' ) ) {
				return \wpex_sanitize_data( $value, $sanitize );
			} else {
				return \wp_strip_all_tags( $value );
			}
		}
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ): void {
		echo '<div class="wpex-widget-settings-form">';
			foreach ( $this->fields as $field ) {
				$id             = $field['id'];
				$field['class'] = 'widefat';
				$field['id']    = $this->get_field_id( $id );
				$field['name']  = $this->get_field_name( $id );
				if ( empty( $instance ) ) {
					$default = $field['std'] ?? ''; // new instance
					$default = $field['default'] ?? $default;
				} else {
					$default = $field['default'] ?? ''; // already saved instance
				}
				$field['value'] = $instance[$id] ?? $default;
				if ( 'social_profiles' === $id && ! $field['value'] && ! empty( $instance['social_services'] ) ) {
					$field['value'] = $this->migrate_social_services( $instance['social_services'] );
				}
				$this->add_field( $field );
			}
		echo '</div>';
	}

	/**
	 * Migrates the old social_services field to the new social_profiles field.
	 */
	protected function migrate_social_services( $services = [] ): array {
		$profiles = [];
		if ( \is_array( $services ) ) {
			foreach ( $services as $service => $settings ) {
				if ( empty( $settings['url'] ) ) {
					continue;
				}
				if ( 'vimeo-square' === $service ) {
					$service = 'vimeo';
				}
				$profiles[] = [
					'site' => $service,
					'url'  => $settings['url'],
				];
			}
		}
		return $profiles;
	}

	/**
	 * Adds a new field to the admin form.
	 */
	public function add_field( $field ): void {
		$type        = $field['type' ] ?? '';
		$method_name = "field_{$type}";
		$description = '';
		if ( \method_exists( $this, $method_name ) ) {
			if ( isset( $field['description'] ) && 'notice' !== $type ) {
				$description = '<small class="description" style="display:block;padding:6px 0 0;clear:both;">' . \wp_kses_post( $field['description'] ) . '</small>';
			}
			echo '<p>' . $this->$method_name( $field ) . $description . '</p>';
		}
	}

	/**
	 * Return field label for admin form.
	 */
	private function field_label( $field, $semicolon = true ): string {
		if ( empty( $field['repeater'] ) ) {
			$for = '  for="' . \esc_attr( $field['id'] ) . '"';
		} else {
			$for = '';
		}
		$output = "<label {$for}>";
			$output .= \esc_html( $field['label'] );
			if ( $semicolon ) {
				$output .= ':';
			}
		$output .= '</label>';
		return $output;
	}

	/**
	 * Return notice type field.
	 */
	private function field_notice( $field, $output = '' ): string {
		$output .= '<p class="wpex-widget-notice" style="font-size:12px;padding:20px;background:#eee;">';
			$output .= '<strong>Notice:</strong> ' . \esc_html( $field['description'] );
		$output .= '</p>';
		return $output;
	}

	/**
	 * Return text field for admin form.
	 */
	private function field_text( $field, $output = '' ): string {
		$output .= $this->field_label( $field );

		$output .= '<input type="text"';

		if ( isset( $field['class'] ) ) {
			$output .= ' class="' . \esc_attr( $field['class'] ) . '"';
		}

		$default = $field['default'] ?? '';
		$value   = $field['value'] ?? $default;

		if ( empty( $field['repeater'] ) ) {
			$output .= ' id="' . \esc_attr( $field['id'] ) . '" ';
		}

		$output .= 'name="' . \esc_attr( $field['name'] ) . '" value="' . \esc_attr( $value ) . '"';

		if ( ! empty( $field['placeholder'] ) ) {
			$output .= ' placeholder="' . \esc_attr( $field['placeholder'] ) . '" ';
		}

		if ( isset( $field['size'] ) ) {
			$output .= ' size="' . \esc_attr( $field['size'] ) . '" ';
		}

		$output .= '>';

		return $output;
	}

	/**
	 * Return url field for admin form.
	 */
	private function field_url( $field, $output = '' ): string {
		$output .= $this->field_label( $field );

		$output .= '<input type="url"';

		if ( isset( $field['class'] ) ) {
			$output .= ' class="' . \esc_attr( $field['class'] ) . '"';
		}

		$default = $field['default'] ?? '';
		$value   = isset( $field['value'] ) ? \esc_url( $field['value'] ) : $default;

		if ( ! empty( $field['repeater'] ) ) {
			$id = '';
		} else {
			$id = ' id="' . \esc_attr( $field['id'] ) . '" ';
		}

		$output .= $id . 'name="' . \esc_attr( $field['name'] ) . '" value="' . \esc_attr( \esc_url( $value ) ) . '" placeholder="http://"';

		if ( isset( $field['size'] ) ) {
			$output .= ' size="' . \esc_attr( $field['size'] ) . '" ';
		}

		$output .= '>';

		return $output;
	}

	/**
	 * Return textarea field for admin form.
	 */
	private function field_textarea( $field, $output = '' ): string {
		$output .= $this->field_label( $field );

		$output .= '<textarea';

			if ( isset( $field['class'] ) ) {
				$output .= ' class="' . \esc_attr( $field['class'] ) . '"';
			}

			$output .= ' id="' . \esc_attr( $field['id'] ) . '" name="' . \esc_attr( $field['name'] ) . '"';

			$rows = $field['rows'] ?? 5;
			$output .= ' rows="' . \esc_attr( $rows ) . '"';

		$output .= '>';

		$default = $field['default'] ?? '';
		$value   = $field['value'] ?? $default;

		if ( $value ) {
			if ( isset( $field['sanitize'] ) && \function_exists( 'wpex_sanitize_data' ) ) {
				$output .= \wpex_sanitize_data( $value, $field['sanitize'] );
			} else {
				$output .= \wp_kses_post( $value );
			}
		}

		$output .= '</textarea>';

		return $output;
	}

	/**
	 * Return media upload field for admin form.
	 */
	private function field_media_upload( $field, $output = '' ): string {
		\wp_enqueue_media();

		$output .= $this->field_label( $field );

		$output .= '<input type="text"';

		if ( isset( $field['class'] ) ) {
			$output .= ' class="' . \esc_attr( $field['class'] ) . '"';
		}

		$default = $field['default'] ?? '';
		$value   = isset( $field['value'] ) ? \esc_attr( $field['value'] ) : $default;

		if ( ! empty( $field['repeater'] ) ) {
			$id = '';
		} else {
			$id = ' id="' . \esc_attr( $field['id'] ) . '" ';
		}

		$output .= $id . 'name="' . \esc_attr( $field['name'] ) . '" value="' . \esc_attr( $value ) . '"';

		if ( isset( $field['size'] ) ) {
			$output .= ' size="' . \esc_attr( $field['size'] ) . '" ';
		}

		$output .= '>';

		$output .= '<input style="margin-block-start:8px;" class="wpex-upload-button button button-secondary" type="button" value="' . \esc_html__( 'Upload/Select', 'total-theme-core' ) . '">';

		return $output;
	}

	/**
	 * Return repeater field for admin form.
	 */
	private function field_repeater( $field, $output = '' ): ?string {
		if ( empty( $field['fields'] ) ) {
			return null;
		}

		$fields  = $field['fields'];
		$default = $field['default'] ?? '';
		$value   = $field['value'] ?? $default;

		if ( empty( $fields ) || ! \is_array( $fields ) ) {
			return null;
		}

		$output .= '<h4>' . $this->field_label( $field ) . '</h4>';

		$output .= '<ul id="' . \esc_attr( $field[ 'id' ] ) . '" class="wpex-repeater-field">';

			// Show saved fields
			if ( ! empty( $value ) && \is_array( $value ) ) {
				for ( $k = 0; $k < \count( $value ); $k++ ) {
					$output .= '<li><a href="#" role="button" class="wpex-rpf-remove" aria-label="' . \esc_attr__( 'remove item', 'total-theme-core' ) . '"><span aria-hidden="true" class="dashicons dashicons-no-alt"></span></a>';
						foreach ( $fields as $subfield ) {
							$subfield['repeater'] = true;
							$subfield['name'] = $field[ 'name' ] . '[' . $subfield[ 'id' ] . '][]'; // same name for each
							$subfield['value'] = $value[ $k ][ $subfield[ 'id' ] ] ?? '';
							$method = 'field_' . $subfield['type'];
							if ( \method_exists( $this, $method ) ) {
								$output .= '<p>' . $this->$method( $subfield ) . '</p>';
							}
						}
					$output .= '</li>';
				}
			}

		$output .= '</ul>';

		// Add button.
		$output .= '<p><a href="#" class="wpex-rpf-add button">' . \esc_html__( 'Add New', 'total-theme-core' ) . '</a></p>';

		// Add the cloner item.
		$output .= '<div class="wpex-rpf-clone"><a href="#" role="button" class="wpex-rpf-remove" aria-label="' . \esc_attr__( 'remove item', 'total-theme-core' ) . '"><span aria-hidden="true" class="dashicons dashicons-no-alt"></span></a>';
			foreach ( $fields as $subfield ) {
				$subfield['repeater'] = true;
				$subfield['name']   = $field['name'] . '[' . $subfield[ 'id' ] . '][]'; // same name for each
				$subfield['value']  = '';
				$method = 'field_' . $subfield['type'];
				if ( \method_exists( $this, $method ) ) {
					$output .= '<p>' . $this->$method( $subfield ) . '</p>';
				}
			}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Return select field for admin form.
	 */
	private function field_select( $field, $output = '' ): ?string {
		if ( empty( $field['choices'] ) ) {
			return null;
		}

		$choices = $field['choices'];

		if ( ! \is_array( $choices ) ) {
			$method = "choices_{$choices}";
			if ( \method_exists( $this, $method ) ) {
				$choices = $this->$method( $field );
			}
		}

		if ( empty( $choices ) || ! \is_array( $choices ) ) {
			return null;
		}

		$output .= $this->field_label( $field );

		$output .= '<select';
			if ( isset( $field['class'] ) ) {
				$output .= ' class="' . \esc_attr( $field['class'] ) . '"';
			}
			$output .= ' id="' . \esc_attr( $field['id'] ) . '" name="' . \esc_attr( $field['name'] ) . '"';
		$output .= '>';

		$default = $field['default'] ?? '';
		$value   = $field['value'] ?? $default;

		foreach ( $choices as $id => $label ) {
			$output .= '<option value="' . \esc_attr( $id ) . '" ' . \selected( $value, $id, false ) . '>' .  \esc_html( $label ) . '</option>';
		}

		$output .= '</select>';

		return $output;
	}

	/**
	 * Return select templates field.
	 */
	private function field_select_template( $field, $output = '' ): string {
		if ( \function_exists( '\totaltheme_call_non_static' ) ) {
			$output .= $this->field_label( $field );
			\add_filter( 'totaltheme/theme-builder/template_post_types', [ $this, 'filter_template_post_types' ] );
			$output .= \totaltheme_call_non_static( 'Theme_Builder', 'template_select', [
				'id'            => $field['id'],
				'name'          => $field['name'],
				'class'         => $field['class'] ?? '',
				'selected'      => $field['value'] ?? $field['default'] ?? '',
				'template_type' => 'part',
				'echo'          => false,
			] );
			\remove_filter( 'totaltheme/theme-builder/template_post_types', [ $this, 'filter_template_post_types' ] );
		} else {
			return $this->field_text( $field, $output );
		}

		return $output;
	}

	/**
	 * Filters the template post types to allow templatera as an option.
	 */
	public function filter_template_post_types( array $post_types ): array {
		$post_types['templatera'] = 'Templatera';
		return $post_types;
	}

	/**
	 * Return checkbox field for admin form.
	 */
	private function field_checkbox( $field, $output = '' ): string {
		$output .= '<div class="wpex-checkbox-wrap">';

			$output .= '<input type="checkbox"';

				if ( isset( $field['class'] ) ) {
					$output .= ' class="' . \esc_attr( $field['class'] ) . '"';
				}

				$default = $field['default'] ?? 'off';
				$value   = $field['value'] ?? $default;

				$output .= ' id="' . \esc_attr( $field['id'] ) . '" name="' . \esc_attr( $field['name'] ) . '"';

				$output .= ' ' . \checked( (bool) $value, true, false );

			$output .= '>';

			$output .= $this->field_label( $field, false );

		$output .= '</div>';

		return $output;
	}

	/**
	 * Return toggle field for admin form.
	 */
	private function field_toggle( $field, $output = '' ): string {
		$default = $field['default'] ?? 'off';
		$value   = $field['value'] ?? $default;

		$checked_class = $value ? ' wpex-widget-toggle--checked' : '';

		$output .= '<div class="wpex-widget-toggle' . $checked_class . '">';

			$output .= $this->field_label( $field, false );

			$output .= '<span class="wpex-widget-toggle__btn">';

				$output .= '<input type="checkbox"';

					if ( isset( $field['class'] ) ) {
						$output .= ' class="' . \esc_attr( $field['class'] ) . '"';
					}

					$output .= ' id="' . \esc_attr( $field['id'] ) . '" name="' . \esc_attr( $field['name'] ) . '"';

					$output .= ' ' . \checked( (bool) $value, true, false );

				$output .= '>';

				$output .= '<span class="wpex-widget-toggle__track"></span>';
				$output .= '<span class="wpex-widget-toggle__thumb"></span>';

			$output .= '</span>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Return number field for admin form.
	 */
	private function field_number( $field, $output = '' ): string {
		$output .= $this->field_label( $field );

		$output .= '<input type="number"';

		if ( isset( $field['class'] ) ) {
			$output .= ' class="' . \esc_attr( $field['class'] ) . '"';
		}

		$default = $field['default'] ?? '';
		$value   = isset( $field['value'] ) ? \floatval( $field['value'] ) : $default;
		$min     = $field['min'] ?? '';
		$max     = $field['max'] ?? '';
		$step    = $field['step'] ?? '';

		$output .= ' id="' . \esc_attr( $field['id'] ) . '" name="' . \esc_attr( $field['name'] ) . '" value="' . \esc_attr( $value ) . '"';

		$output .= ' min="' . \esc_attr( $min ) . '" ';
		$output .= ' max="' . \esc_attr( $max ) . '" ';
		$output .= ' step="' . \esc_attr( $step ) . '" ';

		$output .= '>';

		return $output;
	}

	/**
	 * Return post_types choices for admin form.
	 */
	private function choices_post_types(): array {
		if ( \function_exists( 'wpex_get_post_types' ) ) {
			return \wpex_get_post_types( 'wpex_recent_posts_thumb_widget', [ 'attachment' ] );
		}

		$types = [];
		$get_types = \get_post_types( [
			'public'   => true,
		], 'objects', 'and' );
		foreach ( $get_types as $key => $val ) {
			$types[$key] = $val->labels->name;
		}

		return $types;
	}

	/**
	 * Return taxonomies choices for admin form.
	 */
	private function choices_taxonomies(): array {
		$taxonomies = [
			'' => \esc_html( '— Select —', 'total-theme-core' ),
		];
		$get_taxonomies = \get_taxonomies( [
			'public' => true,
		], 'objects' );
		foreach ( $get_taxonomies as $get_taxonomy ) {
			$taxonomies[ $get_taxonomy->name ] = \ucfirst( $get_taxonomy->labels->singular_name );
		}
		return $taxonomies;
	}

	/**
	 * Return query_orderby choices for admin form.
	 */
	private function choices_query_orderby(): array {
		return [
			'date'          => \esc_html__( 'Date', 'total-theme-core' ),
			'title'         => \esc_html__( 'Title', 'total-theme-core' ),
			'modified'      => \esc_html__( 'Modified', 'total-theme-core' ),
			'author'        => \esc_html__( 'Author', 'total-theme-core' ),
			'rand'          => \esc_html__( 'Random', 'total-theme-core' ),
			'comment_count' => \esc_html__( 'Comment Count', 'total-theme-core' ),
		];
	}

	/**
	 * Return query_order choices for admin form.
	 */
	private function choices_query_order(): array {
		return [
			'desc' => \esc_html__( 'Descending', 'total-theme-core' ),
			'asc'  => \esc_html__( 'Ascending', 'total-theme-core' ),
		];
	}

	/**
	 * Return aspect_ratio choices for admin form.
	 */
	private function choices_aspect_ratio(): array {
		return \function_exists( 'totaltheme_get_aspect_ratio_choices' ) ? \totaltheme_get_aspect_ratio_choices() : [];
	}

	/**
	 * Return border_radius choices for admin form.
	 */
	private function choices_border_radius(): array {
		return \function_exists( 'wpex_utl_border_radius' ) ? \wpex_utl_border_radius() : [];
	}

	/**
	 * Return margin options for admin form.
	 */
	private function choices_margin(): array {
		return \function_exists( 'wpex_utl_margins' ) ? \wpex_utl_margins() : [];
	}

	/**
	 * Return categories choices for admin form.
	 */
	private function choices_categories() {
		$choices = [
			'' => \esc_html( '— Select —', 'total-theme-core' ),
		];
		$terms = \get_terms( 'category' );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$choices[ $term->term_id ] = $term->name;
			}
		}
		return $choices;
	}

	/**
	 * Return intermediate_image_sizes choices for admin form.
	 */
	private function choices_intermediate_image_sizes( $field ): array {
		if ( isset( $field['exclude_custom'] ) ) {
			$sizes = [
				'' => \esc_html__( 'Default', 'total-theme-core' ),
			];
		} else {
			$sizes =[
				'wpex-custom' => \esc_html__( 'Custom', 'total-theme-core' ),
			];
		}
		$get_sizes = \array_keys( $this->get_intermediate_sizes() );
		$sizes = $sizes + \array_combine( $get_sizes, $get_sizes );
		return $sizes;
	}

	/**
	 * Return intermediate_image_sizes choices for admin form.
	 */
	private function get_intermediate_sizes(): array {
		return \function_exists( 'wpex_get_thumbnail_sizes' ) ? \wpex_get_thumbnail_sizes() : [];
	}

	/**
	 * Return image_crop_locations choices for admin form.
	 */
	private function choices_image_crop_locations(): array {
		return \function_exists( 'wpex_image_crop_locations' ) ? \wpex_image_crop_locations() : [];
	}

	/**
	 * Return image_hovers choices for admin form.
	 */
	private function choices_image_hovers(): array {
		return \function_exists( 'wpex_image_hovers' ) ? \wpex_image_hovers() : [];
	}

	/**
	 * Return image_filters choices for admin form.
	 */
	private function choices_image_filters(): array {
		return \function_exists( 'wpex_image_filters' ) ? \wpex_image_filters() : [];
	}

	/**
	 * Return menus choices for admin form.
	 */
	private function choices_menus(): array {
		$menus = [];

		$get_menus = \get_terms( 'nav_menu', [
			'hide_empty' => false,
		] );

		if ( ! empty( $get_menus ) ) {
			foreach ( $get_menus as $menu ) {
				$menus[$menu->term_id] = $menu->name;
			}
		}

		return $menus;
	}

	/**
	 * Return posts choices for admin form.
	 *
	 * @todo remove option as it doesn't seem to be in use anywhere.
	 */
	private function choices_posts( $field ): array {
		$posts = [];

		$ids = new \WP_Query( [
			'post_type'      => sanitize_text_field( $field['post_type'] ),
			'posts_per_page' => 500,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		] );

		if ( $ids->have_posts() ) {
			foreach ( $ids->posts as $post_id ) {
				$posts[ $post_id ] = \get_post_field( 'post_title', $post_id, 'raw' );
			}
		}

		return $posts;
	}

	/**
	 * Return grid_columns choices for admin form.
	 */
	private function choices_grid_columns(): array {
		return \function_exists( 'wpex_grid_columns' ) ? \wpex_grid_columns() : [];
	}

	/**
	 * Return grid_gaps choices for admin form.
	 */
	private function choices_grid_gaps(): array {
		return \function_exists( 'wpex_column_gaps' ) ? \wpex_column_gaps() : [];
	}

	/**
	 * Return link_target choices for admin form.
	 */
	private function choices_link_target(): array {
		return [
			'_self' => \esc_html__( 'Current window', 'total-theme-core' ),
			'_blank' => \esc_html__( 'New window', 'total-theme-core' ),
		];
	}

	/**
	 * Return utl_font_size choices for admin form.
	 */
	private function choices_utl_font_size(): array {
		return \function_exists( 'wpex_utl_font_sizes' ) ? \wpex_utl_font_sizes() : [];
	}

	/**
	 * Checks if currently in legacy preview mode.
	 */
	protected function is_legacy_preview_view_mode(): bool {
		return ( \defined( 'REST_REQUEST' ) && \REST_REQUEST );
	}

}
