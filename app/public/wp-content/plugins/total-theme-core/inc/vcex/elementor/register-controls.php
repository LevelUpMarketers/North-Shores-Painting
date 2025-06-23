<?php

namespace TotalThemeCore\Vcex\Elementor;

use Elementor\Controls_Manager as Elementor_Controls_Manager;

\defined( 'ABSPATH' ) || exit;

final class Register_Controls {

	/**
	 * Base widget class reference.
	 */
	private $widget = null;

	/**
	 * Shortcode params unparsed.
	 */
	private $params = [];

	/**
	 * Constructor.
	 */
	public function __construct( $widget, $params ) {
		$this->widget = $widget;
		$this->params = $params;

		$this->add_controls();
	}

	/**
	 * Add Controls to the $widget base.
	 */
	private function add_controls(): void {
		$sections = $this->generate_sections();

		if ( ! is_array( $sections ) ) {
			return;
		}

		foreach ( $sections as $section_k => $section_v ) {
			if ( empty( $section_v['settings'] ) ) {
				continue;
			}
		
			$this->widget->start_controls_section(
				"section_{$section_k}",
				[
					'label' => $section_v['label'],
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			foreach ( $section_v['settings'] as $setting => $setting_args ) {
				if ( isset( $setting_args['is_group'] ) && true === $setting_args['is_group'] ) {
					$group_settings = array_merge( [
						'name' => $setting,
					], $setting_args );
					unset( $group_settings['is_group'], $group_settings['group_type'], $group_settings['type'] );
					$this->widget->add_group_control( $setting_args['group_type'], $group_settings );
				} elseif ( ! empty( $setting_args['repeater'] ) ) {
					$repeater = new \Elementor\Repeater();
					foreach ( $setting_args['repeater'] as $repeater_control_id => $repeater_control_settings ) {
						$repeater->add_control( $repeater_control_id, $repeater_control_settings );
					}
					$this->widget->add_control( $setting, $setting_args );
				} elseif ( ! empty( $setting_args['responsive'] ) ) {
					$this->widget->add_responsive_control( $setting, $setting_args );
				} else {
					if ( isset( $setting_args['type'] ) && 'text' === $setting_args['type'] && ! in_array( $setting, [ 'content', 'heading', 'text' ], true ) ) {
						$setting_args['ai'] = [ 'active' => false ];
					}
					$this->widget->add_control( $setting, $setting_args );
				}
			}
		
			$this->widget->end_controls_section();
		}
		
	}

	/**
	 * Loops through all params to parse and group them.
	 */
	private function generate_sections() {
		$sections = [];
		foreach ( $this->params as $param_k => $param ) {
			if ( ! $this->is_elementor_param( $param ) ) {
				unset( $this->params[ $param_k ] );
				continue; // not an elementor param
			}

			// Parse param to make them compatible with elementor
			$param = $this->parse_param( $param );

			// Requirements
			if ( ! $param || 'hidden' === $param['type'] ) {
				continue;
			}

			// Get section key from group name
			$section_k = \sanitize_key( $param['group'] );
			
			// Add new section
			if ( ! isset( $sections[ $section_k ] ) ) {
				$sections[ $section_k ] = [
					'label'    => $param['group'],
					'settings' => []
				];
			}

			// Get param name
			$param_name = $param['name'];

			if ( ! $param_name ) {
				continue;
			}

			// Clean up param
			unset( $param['group'], $param['name'] );

			// Add param to section settings
			$sections[ $section_k ]['settings'][ $param_name ] = $param;
		}
		return $sections;
	}

	/**
	 * Parse param.
	 */
	private function parse_param( $param, $recursive_fields = [] ) {
		if ( ! empty( $param['elementor'] ) ) {
			foreach ( $param['elementor'] as $key => $val ) {
				if ( 'condition' === $key ) {
					$param['dependency'] = $val;
				} else {
					if ( 'conditions' === $key ) {
						unset( $param['dependency'] ); // conditions override dependency
					}
					$param[ $key ] = $val;
				}
			}
			unset( $param['elementor'] );
		}

		// Define main vars
		$dependency = $param['dependency'] ?? false;
		$default    = $param['default'] ?? $param['std'] ?? null;

		// If a default isn't set try grab it from the value which is what WPB uses
		// We must do this before our switch statement
		if ( ! $default && isset( $param['value'] ) ) {
			if ( \is_array( $param['value'] ) ) {
				$default = reset( $param['value'] );
			} elseif ( \is_string( $param['value'] ) ) {
				$default = $param['value'];
			}
		}

		// These params always get added
		$param['type']  = $param['type'] ?? 'text';
		$param['label'] = $param['heading'] ?? '';
		$param['name']  = $param['param_name'] ?? '';

		if ( ! $recursive_fields ) {
			$param['group'] = $param['group'] ?? \esc_html__( 'General', 'total-theme-core' );
		}

		// Modify param based on the param type
		switch ( $param['type'] ) {
			case 'colorpicker':
			case 'vcex_colorpicker':
				$param['type'] = 'color';
				break;
			case 'vcex_social_button_styles':
				$param['type'] = 'select';
				$param['default'] = 'default';
				$param['options'] = function_exists( 'wpex_social_button_styles' ) ? wpex_social_button_styles() : [];
				break;
			case 'vcex_select_callback_function':
			case 'vcex_custom_field':
				$param['type'] = 'text';
				break;
			case 'autocomplete':
				// @todo
				$param['type'] = 'select2';
				break;
			case 'vcex_media_select':
				$param['type'] = 'media';
				$media_type = $param['media_type'] ?? 'image';
				$param['media_types'] = is_array( $media_type ) ? $media_type : [ $media_type ];
				break;
			case 'attach_image':
				$param['type'] = 'media';
				$param['media_types'] = [ 'image', 'svg' ];
				break;
			case 'vcex_sorter':
				if ( empty( $param['choices'] ) ) {
					$param['type'] = 'textfield';
				} else {
					$param['type']     = 'select2';
					$param['multiple'] = true; // should always be multiple, no point in using a sorter if it's not - just use standard select instead
					$param['options']  = $param['choices'];
					unset( $param['choices'] ); // prevents first option from being selected by default

					// Make sure default is an array for the sorter type - this is important to prevent errors with the contains conditions check
					if ( $default ) {
						if ( \is_string( $default ) ) {
							$default = explode( ',', $default );
						}
					} else {
						$param['default'] = []; // need to set the param directly because [] won't return true later so it won't get added
					}
				}
				break;
			case 'posttypes':
				$param['type']        = 'select2';
				$param['label_block'] = true;
				$param['multiple']    = true;
				$param['options']     = $this->choices_get_post_types();
				break;
			case 'checkbox':
				if ( isset( $param['value'] ) && \is_array( $param['value'] ) ) {
					$param['type']     = 'select2';
					$param['multiple'] = true;
					$param['options']  = \array_flip( $param['value'] );
					if ( $default && \is_string( $default ) ) {
						$default = explode(',', $default );
					}
				} else {
					$param['type'] = 'switch';
				}
				break;
			case 'vcex_font_family_select':
				if ( $font_choices = $this->get_font_family_choices() ) {
					$param['type']    = 'select';
					$param['choices'] = $font_choices;
				} else {
					$param['type'] = 'text';
				}
				break;
			case 'select_group': // special type for Elementor
			case 'vcex_select':
			case 'vcex_select_buttons':
			case 'vcex_wpex_card_select':
				$grouped_options = 'select_group' === $param['type'];
				$param['type'] = 'select';
				if ( ! empty( $param['choices_callback'] ) ) {
					$param['choices'] = is_callable( $param['choices_callback'] ) ? call_user_func( $param['choices_callback'] ) : [];
					unset( $param['choices_callback'] );
				} else {
					$choices = $param['choices'] ?? $param['name'];
					if ( 'card_style' === $choices ) {
						$param['groups'] = $this->parse_options( 'card_style', $param );
						unset( $param['choices'] );
					} else {
						$param['choices'] = $choices;
					}
				}
				break;
			case 'vcex_text_align':
				$param['type'] = 'select';
				$param['choices'] = \totalthemecore_call_static( 'WPBakery\Params\Text_Align', 'get_choices', $param );
				break;
			case 'vcex_hover_animations':
			case 'vcex_button_colors':
			case 'vcex_button_styles':
			case 'vcex_image_sizes';
			case 'vcex_image_crop_locations':
			case 'vcex_image_filters':
			case 'vcex_image_hovers':
				$param['choices'] = \str_replace( 'vcex_', '', $param['type'] );
				$param['type']    = 'select'; // must go after choices!
				break;
			case 'vcex_preset_textfield':
				if ( 'icon_size' === $param['name'] ) {
					$param['type'] = 'text';
				}
				$param['choices'] = $param['choices'] ?? $param['name'];
				break;
			case 'exploded_textarea':
				$param['type'] = 'textarea';
				break;
			case 'vc_link':
				// @note Elementor is bugged and the url options display anyway.
				if ( empty( ['url_options'] ) ) {
					$param['options'] = false;
				}
				break;
			case 'vcex_trbl':
				$param['type'] = 'dimensions';
				// @note this causes elementor to add 0's by default to the fields.
				/*if ( empty( $param['default'] ) ) {
					$param['default'] = [
						'top'      => '',
						'right'    => '',
						'bottom'   => '',
						'left'     => '',
						'unit'     => 'px',
						'isLinked' => false,
					];
				}*/
				if ( empty( $param['size_units'] ) ) {
					$param['size_units'] = [ 'px', 'em', 'rem', '%', 'vh', 'custom' ];
				}
				break;
			case 'vcex_grid_columns':
				$param['type']       = 'select';
				$param['choices']    = 'grid_columns';
				$param['responsive'] = true;
				break;
			case 'vcex_notice':
				$param['type']            = 'raw_html';
				$param['raw']             = $param['text'];
				$param['content_classes'] = 'vcex-elementor-control-notice';
				break;
			case 'iconpicker':
			case 'vcex_select_icon':
				$param['type'] = 'icon';
				// @note this is a temp fix for some icon options that don't have a dependency.
				if ( ! in_array( $param['name'], [ 'icon_active', 'toggle_icon', 'toggle_icon_active' ], true ) ) {
					$dependency = false;
				}
				if ( ! empty( $param['choices'] ) ) {
					$param['choices'] = \array_combine( $param['choices'], $param['choices'] );
				}
				if ( $default ) {
					$default = [
						'value'   => "ticon-{$default}",
						'library' => 'ticon'
					];
				}
				break;
			case 'dropdown':
				$param['type'] = 'select';
				if ( $param['value'] && \is_array( $param['value'] ) ) {
					$param['options'] = \array_flip( $param['value'] );
				}
				break;
			case 'vcex_ofswitch':
				$param['type']         = 'switcher';
				$param['return_value'] = $param['vcex']['on'] ?? 'true';
				$param['label_on']     = \esc_html__( 'On', 'total-theme-core' );
				$param['label_off']    = \esc_html__( 'Off', 'total-theme-core' );
				if ( ! $default ) {
					$default = $param['vcex']['off'] ?? 'false'; // vcex_ofswitch switcher is always false by default
				}
				if ( $default && ( 'false' === $default || 'no' === $default ) ) {
					$default = ''; // if the default is "false" it causes issues with conditional checks in the editor
				}
				break;
			case 'param_group':
				if ( ! empty( $param['params'] ) ) { 
					$default         = ''; // for now we don't add any defaults.
					$param['type']   = 'repeater';
					$param['fields'] = [];
					foreach ( $param['params'] as $param_group_param ) {
						if ( $this->is_elementor_param( $param_group_param ) ) {
							$param['fields'][] = $this->parse_param( $param_group_param, $param['params'] );
						}
					}
					unset( $param['params'] );
				} else {
					return;
				}
				break;
			case 'typography':
				$param['is_group']   = true;
				$param['group_type'] = \Elementor\Group_Control_Typography::get_type();
				if ( isset( $param['selector'] ) ) {
					$param['selector'] = '{{WRAPPER}} ' . $param['selector'];
				} else {
					$param['selector'] = '{{WRAPPER}}';
				}
				$default = '';
				break;
			case 'vcex_subheading':
				$param['type'] = 'heading';
				if ( isset( $param['text'] ) ) {
					$param['label'] = $param['text'];
					$param['separator'] = 'before';
				}
				break;
		}

		// Parse dependency
		if ( $dependency ) {
			$condition = $this->parse_dependency( $dependency, $recursive_fields ?: $this->params );
			if ( $condition ) {
				$param['condition'] = $condition;
			}
		}

		// Parse choices
		if ( ! empty( $param['choices'] ) && empty( $param['options'] ) ) {
			$options = $this->parse_options( $param['choices'], $param );
			if ( $options ) {
				if ( isset( $grouped_options ) && true === $grouped_options ) {
					$param['groups'] = $options;
				} else {
					$param['options'] = $options;
				}
				if ( ! $default ) {
					$default = array_key_first( $options );
				}
			}
			unset( $param['choices'] );
		}

		// Add default
		if ( $default ) {
			$param['default'] = $default;
		}

		// Update param type for Elementor
		$param['type'] = $this->parse_param_type( $param['type'] );

		// Clean up final array to remove non elementor args
		unset(
			$param['editors'],
			$param['css'],
			$param['heading'],
			$param['param_name'],
			$param['admin_label'],
			$param['value'],
			$param['std'],
			$param['exclude_choices'],
			$param['dependency']
		);

		return $param;
	}

	/**
	 * Parses options before sending to elementor.
	 */
	private function parse_options( $options, $param ) {
		if ( ! \is_array( $options ) && \class_exists( 'TotalThemeCore\Vcex\Setting_Choices' ) ) {
			return (new \TotalThemeCore\Vcex\Setting_Choices( $options, $param, 'elementor' ))->get_choices();
		}
		return $options;
	}

	/**
	 * Converts dependency to conditional & removes dependencies for any param whos dependency doesn't exist
	 * in Elementor. Because if the og param doesn't exist Elementor won't ever display the setting.
	 */
	private function parse_dependency( $dependency, $params = [] ) {
		if ( ! \is_array( $dependency ) || empty( $dependency['element'] ) ) {
			return;
		}
		$condition        = [];
		$element          = $dependency['element'];
		$element_exists   = false;
		$target_type      = '';
		$parent_condition = false; // add parent condition if it exists
		foreach ( $params as $param_k => $param ) {
			if ( isset( $param['param_name'] ) && $param['param_name'] === $element ) {
				if ( $this->is_elementor_param( $param ) ) {
					$element_exists = true;
					$target_type = $param['type'] ?? 'text';
					if ( ! empty( $param['dependency'] ) ) {
						$parent_condition = $this->parse_dependency( $param['dependency'], $params );
					}
				}
				break;
			}
		}
		if ( ! $element_exists ) {
			return;
		}
		if ( $converted_dep = $this->convert_dependency_to_condition( $element, $target_type, $dependency ) ) {
			$condition = array_merge( $condition, $converted_dep );
		}
		if ( $parent_condition ) {
			$condition = array_merge( $condition, $parent_condition );
		}
		return $condition;
	}

	/**
	 * Converts dependency into a condition.
	 */
	private function convert_dependency_to_condition( $element = '', $target_type = '', $dependency = [] ) {
		$equality = '';
		if ( ! empty( $dependency['value'] ) ) {
			if ( 'false' === $dependency['value'] && 'vcex_ofswitch' === $target_type ) {
				$check = '';
			} else {
				$check = $dependency['value'];
			}
		} elseif ( ! empty( $dependency['is_empty'] ) ) {
			$check = '';
		} elseif ( ! empty( $dependency['not_empty'] ) ) {
			$equality = '!';
			$check = '';
		} elseif( ! empty( $dependency['value_not_equal_to'] ) ) {
			$equality = '!';
			$check = $dependency['value_not_equal_to'];
		}
		if ( ! isset( $check ) ) {
			return;
		}
		if ( 'attach_image' === $target_type || 'vcex_media_select' === $target_type || 'media' === $target_type ) {
			$equality = "[url]{$equality}";
		}
		return [ $element . $equality => $check ];
	}

	/**
	 * Parses the param type to return an Elementor compatible type.
	 */
	private function parse_param_type( $type ) {
		$controls = [
			'textarea'              => Elementor_Controls_Manager::TEXTAREA,
			'textarea_safe'         => Elementor_Controls_Manager::TEXTAREA,
			'select2'               => Elementor_Controls_Manager::SELECT2,
			'repeater'              => Elementor_Controls_Manager::REPEATER,
			'dimensions'            => Elementor_Controls_Manager::DIMENSIONS,
			'raw_html'              => Elementor_Controls_Manager::CODE,
			'textarea_raw_html'     => Elementor_Controls_Manager::CODE,
			'media'                 => Elementor_Controls_Manager::MEDIA,
			'attach_images'         => Elementor_Controls_Manager::GALLERY,
			'vc_link'               => Elementor_Controls_Manager::URL,
			'vcex_font_size'        => Elementor_Controls_Manager::TEXT,
			'textfield'             => Elementor_Controls_Manager::TEXT,
			'text'                  => Elementor_Controls_Manager::TEXT,
			'vcex_text'             => Elementor_Controls_Manager::TEXT,
			'textarea_html'         => Elementor_Controls_Manager::WYSIWYG,
			'color'                 => Elementor_Controls_Manager::COLOR,
			'vcex_preset_textfield' => Elementor_Controls_Manager::SELECT,
			'select'                => Elementor_Controls_Manager::SELECT,
			'switcher'              => Elementor_Controls_Manager::SWITCHER,
			'icon'                  => Elementor_Controls_Manager::ICONS,
			'heading'               => Elementor_Controls_Manager::HEADING,
		];
		return $controls[ $type ] ?? $type;
	}

	/**
	 * Returns post type options.
	 */
	private function choices_get_post_types() {
		$post_types_list = [];
		$post_types = \get_post_types( [
			'public' => true,
		] );
		if ( $post_types ) {
			foreach ( $post_types as $post_type ) {
				if ( ! in_array( $post_type, [ 'revision', 'nav_menu_item', 'attachment', 'elementor_library'  ], true ) ) {
					$post_types_list[ $post_type ] = \get_post_type_object( $post_type )->labels->name ?? $post_type;
				}
			}
		}
		return $post_types_list;
	}

	/**
	 * Returns font family choices.
	 */
	private function get_font_family_choices(): array {
		$fonts = [];
		if ( \function_exists( '\wpex_get_registered_fonts' ) ) {
			$user_fonts = (array) \wpex_get_registered_fonts();
			if ( $user_fonts ) {
				foreach ( $user_fonts as $font_name => $font_settings ) {
					$fonts[ $font_name ] = \ucfirst( $font_name );
				}
			}
		}
		if ( ! $fonts && \function_exists( '\wpex_standard_fonts' ) ) {
			$std_fonts = (array) \wpex_standard_fonts();
			foreach ( $std_fonts as $font_name ) {
				$fonts[ $font_name ] = \ucfirst( $font_name );
			}
		}
		if ( $fonts ) {
			$fonts = [ '' => \esc_html__( 'Default', 'total-theme-core' ) ] + $fonts;
		}
		return $fonts;
	}

	/**
	 * Check if a param is an elementor param.
	 */
	private function is_elementor_param( $param ): bool {
		return isset( $param['editors'] ) && \is_array( $param['editors'] ) && \in_array( 'elementor', $param['editors'], true );
	}

}
