<?php

namespace TotalTheme;

use TotalTheme\Theme_Builder\Render_Template;
use TotalTheme\Theme_Builder\Location_Template;

\defined( 'ABSPATH' ) || exit;

/**
 * Theme Builder.
 */
class Theme_Builder {

	/**
	 * Array of locations that have assigned templates and we've already checked/rendered them.
	 */
	protected static $assigned_locations = [];

	/**
	 * Parsed locations.
	 */
	protected static $did_locations = [];

	/**
	 * Location currently being shown.
	 */
	protected static $current_location = null;

	/**
	 * Stores the ID of the Template currently being rendered.
	 */
	protected static $current_template_id = 0;

	/**
	 * Stores the builder type of the Template currently being rendered.
	 */
	protected static $current_template_builder = '';

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Main Theme_Builder Instance.
	 */
	public static function instance() {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Returns the current location being displayed.
	 */
	public function get_current_location(): ?string {
		return self::$current_location;
	}

	/**
	 * Returns the current location being displayed.
	 */
	public function did_location( $location ): bool {
		return \in_array( $location, self::$did_locations, true );
	}

	/**
	 * Assigns a location to the list of assigned locations.
	 */
	public function add_location_to_assigned_list( $location ): void {
		if ( ! \in_array( $location, self::$assigned_locations ) ) {
			self::$assigned_locations[] = $location;
		}
	}

	/**
	 * Checks if a location has a defined template.
	 */
	public function location_has_template( $location ): bool {
		if ( \in_array( $location, self::$assigned_locations ) ) {
			return true; // prevent extra checks if we've already grabbed the template.
		}
		
		return (bool) self::do_location( $location, false );
	}

	/**
	 * Do location.
	 *
	 * @todo move header/footer builder functions here if possible.
	 */
	public function do_location( $location, $render = true ): bool {
		$has_template = false;

		if ( $render ) {
			self::$current_location = $location;
		}
		
		$location_action_hook = "totaltheme/theme_builder/do_{$location}";

		// Check custom actions.
		if ( \has_action( $location_action_hook ) ) {
			\ob_start();
				\do_action( $location_action_hook );
			$location_action = \ob_get_clean();
			if ( $location_action ) {
				$has_template = true;
				if ( $render ) {
					echo $location_action;
				}
			}
		}

		// Check elementor templates.
		if ( ! $has_template && \function_exists( 'elementor_theme_do_location' ) ) {
			if ( $render ) {
				$has_template = \elementor_theme_do_location( $location );
			} else {
				// @todo Add elementor location check?
			}
		}

		// Check theme templates.
		if ( ! $has_template ) {
			self::$current_template_id = $this->get_location_template_id( $location );
			if ( ! empty( self::$current_template_id ) && \class_exists( '\TotalTheme\Theme_Builder\Render_Template' ) ) {
				$render_template = new Render_Template( self::$current_template_id, $location );
				if ( $render ) {
					$has_template = $render_template->render();
				} else {
					$has_template = (bool) $render_template->get_template_content();
				}
			}
		}

		// Add location to assigned list if it has a template.
		if ( $has_template ) {
			self::add_location_to_assigned_list( $location );
		}

		// Update class vars after render.
		if ( $render ) {
			if ( ! \in_array( $location, self::$did_locations ) ) {
				self::$did_locations[] = $location;
			}
			self::$current_location = null;
		}

		return (bool) $has_template;
	}

	/**
	 * Returns the template ID for given location.
	 */
	public function get_location_template_id( string $location ): int {
		$template_id = 0;

		if ( \class_exists( '\TotalTheme\Theme_Builder\Location_Template' ) ) {
			$template_id = (new Location_Template( $location ))->template;
		}

		return (int) \apply_filters( 'totaltheme/theme_builder/location_template_id', $template_id, $location );
	}

	/**
	 * Returns template post types.
	 */
	public function get_template_post_types(): array {
		$types = [
			'wpex_templates'    => \esc_html__( 'Dynamic Templates', 'total' ),
			'elementor_library' => \esc_html__( 'Elementor Library', 'total' ),
		];
		if ( ! \get_theme_mod( 'wpex_templates_enable', true ) ) {
			$types['templatera'] = 'Templatera';
		}
		$types = \array_filter( $types, 'post_type_exists', \ARRAY_FILTER_USE_KEY );
		// @todo filter should be theme_builder not theme-builder.
		return (array) \apply_filters( 'totaltheme/theme-builder/template_post_types', $types );
	}

	/**
	 * Returns an array of template choices for use with select fields.
	 *
	 * @param string|array $template_type The template type(s) to return.
	 * @param bool $multidimensional Return a multidimensional array or not.
	 */
	public function get_template_choices( $template_type = 'all', bool $multidimensional = true ): array {
		$types = $this->get_template_post_types();

		if ( ! $types ) {
			return [];
		}

		$multidimensional = ( $multidimensional && \count( $types ) > 1 );

		$choices = [];

		foreach ( $types as $type => $val ) {

			$args = [
				'posts_per_page' => 100,
				'post_type'      => $type,
				'fields'         => 'ids',
				'orderby'        => 'name',
				'order'          => 'ASC',
			];

			if ( 'wpex_templates' === $type && 'all' !== $template_type ) {
				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'   => 'wpex_template_type',
						'value' => $template_type,
					],
					[
						'key'     => 'wpex_template_type',
						'compare' => 'NOT EXISTS'
					],
				];
			}

			$get_templates = new \WP_Query( $args );

			if ( ! empty( $get_templates->posts ) ) {
				foreach ( $get_templates->posts as $template ) {
					$template = \absint( $template );
					$template_name = \sanitize_text_field( \get_the_title( $template ) );
					if ( $multidimensional ) {
						if ( ! isset( $choices[ $type ] ) ) {
							$choices[ $type ] = [
								'label'   => $types[ $type ] ?? $type,
								'choices' => [],
							];
						}
						$choices[ $type ]['choices'][ $template ] = $template_name;
					} else {
						$choices[ $template ] = $template_name;
					}
				}
			}

		}

		if ( ! $multidimensional && $choices ) {
			asort( $choices );
		}

		return $choices;
	}

	/**
	 * Returns a template select field.
	 */
	public function template_select( array $args = [] ) {
		$defaults = [
			'echo'              => 1,
			'selected'          => '',
			'name'              => 'template_id',
			'id'                => 'wpex-template-select',
			'class'             => '',
			'describedby'       => '',
			'show_option_none'  => 1,
			'option_none_label' => \esc_html__( 'Default', 'total' ),
			'option_none_value' => '',
			'template_type'     => 'all',
			'multidimensional'  => 1,
		];

		$parsed_args = wp_parse_args( $args, $defaults );

		$choices = $this->get_template_choices( $parsed_args['template_type'], $parsed_args['multidimensional'] );

		if ( ! $choices && ! $parsed_args['show_option_none'] ) {
			return;
		}

		$selected = $parsed_args['selected'];

		if ( $selected && ! \get_post_status( $selected ) ) {
			$selected = $parsed_args['option_none_value'];
		}

		$html = '<select';
			if ( $parsed_args['id'] ) {
				$html .= ' id="' . esc_attr( $parsed_args['id'] ) . '"';
			}
			if ( $parsed_args['class'] ) {
				$html .= ' class="' . esc_attr( $parsed_args['class'] ) . '"';
			}
			if ( $parsed_args['name'] ) {
				$html .= ' name="' . esc_attr( $parsed_args['name'] ) . '"';
			}
			if ( $parsed_args['describedby'] ) {
				$html .= ' aria-describedby="' . esc_attr( $parsed_args['describedby'] ) . '"';
			}
		$html .= '>';

		if ( $parsed_args['show_option_none'] ) {
			$html .= '<option value="' . esc_attr( $parsed_args['option_none_value'] ) . '">' . esc_html( $parsed_args['option_none_label'] ) . '</option>';
		}

		$value_option_exists = false;

		$options = '';

		foreach ( $choices as $choice_k => $choice_v ) {
			if ( \is_array( $choice_v ) && isset( $choice_v['choices'] ) ) {
				$options .= '<optgroup label="' . \esc_attr( $choice_v['label'] ) . '">';
				foreach ( $choice_v['choices'] as $subchoice_k => $subchoice_v ) {
					if ( ! $value_option_exists && ( \strval( $selected ) === \strval( $subchoice_k ) ) ) {
						$value_option_exists = true;
					}
					$options .= '<option value="' . \esc_attr( $subchoice_k ) . '"' . \selected( $selected, $subchoice_k, false ) . '>' . \esc_html( $subchoice_v ) . '</option>';
				}
				$options .= '</optgroup>';
			} else {
				if ( ! $value_option_exists && ( \strval( $selected ) === \strval( $choice_k ) ) ) {
					$value_option_exists = true;
				}
				$options .= '<option value="' . \esc_attr( $choice_k ) . '"' . \selected( $selected, $choice_k, false ) . '>' . \esc_html( $choice_v ) . '</option>';
			}
		}

		if ( $selected && $selected !== $parsed_args['option_none_value'] && ! $value_option_exists ) {
			$options = '<option value="' . \esc_attr( $selected ) . '" selected="selected">' . \esc_html( \get_the_title( $selected ) ) . '</option>' . $options;
		}

		$html .= $options;
		$options = '';

		$html .= '</select>';

		if ( $parsed_args['echo'] ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Returns the post type name given the template id it has been assigned to.
	 */
	public function get_post_type_from_template_id( int $template_id, string $template_type = 'single' ) {
		static $data = [];
		if ( isset( $data[ $template_id ] ) ) {
			return $data[ $template_id ];
		}
		if ( \totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
			$ptu_post = new \WP_Query( [
				'posts_per_page' => 1,
				'post_type'      => 'ptu',
				'fields'         => 'ids',
				'meta_key'       => '_ptu_total_singular_template_id',
				'meta_value'     => \strval( $template_id ),
			] );
			if ( ! empty( $ptu_post->posts[0] ) ) {
				$ptu_meta_type = \get_post_meta( $ptu_post->posts[0], '_ptu_name', true );
				if ( $ptu_meta_type && \post_type_exists( $ptu_meta_type ) ) {
					return $ptu_meta_type;
				}
			}
		}
		$mods = ( $mods = \get_theme_mods() ) ? \array_filter( $mods, 'is_numeric' ) : [];
		if ( $mods && \in_array( $template_id, $mods ) ) {
			$mods = \array_keys( $mods, $template_id );
			if ( $mods ) {
				$mod_name = ( 'single' === $template_type ) ? '_singular_template' : '_archive_template_id';
				foreach ( $mods as $mod ) {
					if ( \str_ends_with( $mod, $mod_name ) ) {
						$post_type = \str_replace( $mod_name, '', $mod );
						break;
					}
				}
				if ( isset( $post_type ) && ( 'blog' === $post_type || 'post_series' === $post_type ) ) {
					$post_type = 'post';
				}
			}
		}
		if ( ( isset( $post_type ) && \post_type_exists( $post_type ) ) ) {
			$data[ $template_id ] = $post_type;
			return $post_type;
		}
	}

}
