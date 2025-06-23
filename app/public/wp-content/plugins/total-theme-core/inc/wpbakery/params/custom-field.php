<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Preset textfield.
 */
final class Custom_Field {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Choices to return.
	 */
	protected static $choices = '';

	/**
	 * Field output.
	 */
	public static function output( $settings, $value ): string {
		$is_preset     = false;
		self::$choices = $settings['choices'] ?? 'all';

		$html = '<div class="vcex-param-custom-field">';

			if ( $choices = self::get_choices() ) {
				$html .= '<select class="vcex-param-custom-field__select wpb-input wpb-select">';
				foreach ( $choices as $key => $val ) {
					if ( \is_array( $val ) ) {
						$html .= '<optgroup label="' . \esc_attr( $val['label'] ) . '">';
						foreach ( $val['options'] as $option_key => $option_val ) {
							if ( ! $is_preset && $value && $option_key === $value ) {
								$is_preset = true;
							}
							$html .= '<option value="' . \esc_attr( $option_key )  . '" ' . \selected( $value, $option_key, false ) . '>' . \esc_attr( $option_val ) . '</option>';
						}
						$html .= '</optgroup>';
					} else {
						$html .= '<option value="' . \esc_attr( $key )  . '" ' . \selected( $value, $key, false ) . '>' . \esc_attr( $val ) . '</option>';
					}
				}
				$html .= '</select>';
			}

			$field_type = ( ! $choices || $is_preset ) ? 'hidden' : 'text';

			$html .= '<input placeholder="' . \esc_html( 'Enter your custom field name or ACF field key here', 'total-theme-core' ) . '" name="' . \esc_attr( $settings['param_name'] ) . '" class="vcex-param-custom-field__input wpb_vc_param_value  ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '_field" type="' . \esc_attr( $field_type ) . '" value="' . \esc_attr( $value ) . '">';

		$html .= '</div>';

		return $html;
	}

	/**
	 * Returns choices for select.
	 */
	protected static function get_choices(): array {
		$allowed_choices = self::get_allowed_choices();

		$choices = [
			'' => \esc_html__( 'Custom', 'total-theme-core' ),
		];

		switch ( $allowed_choices ) {
			case 'link':
				$choices = \array_merge( $choices, self::get_theme_choices_link() );
				break;
			case 'percent':
				$choices = \array_merge( $choices, self::get_theme_choices_percent() );
				break;
			case 'image':
				$choices = \array_merge( $choices, self::get_theme_choices_image() );
				break;
			case 'video':
				$choices = \array_merge( $choices, self::get_theme_choices_video() );
				break;
			case 'post':
			case 'posts':
			case 'date':
			case 'gallery':
				// Nothing.
				break;
			default:
				$choices = \array_merge( $choices, self::get_theme_choices_all() );
				break;
		}

		if ( \function_exists( '\acf_get_field_groups' ) && \function_exists( '\acf_get_fields' ) ) {
			$acf_groups = (array) \acf_get_field_groups();
			foreach ( $acf_groups as $group ) {
				$group_title = $group['title'] ?? '';
				$group_id = $group['ID'] ?? $group['id'];
				$fields = (array) \acf_get_fields( $group_id );
				if ( $fields ) {
					$group_options = self::process_acf_fields( $fields );
					if ( $group_options ) {
						$group_title = $group['title'] ?? $group_id;
						$choices[ "acf_{$group_id}" ] = [
							'label'   => "ACF - {$group_title}",
							'options' => $group_options,
						];
					}
				}
			}
		}

		return (array) \apply_filters( 'vcex_custom_field_param_choices', $choices, $allowed_choices );
	}

	/**
	 * Loops through acf groups to return allowed fields.
	 */
	private static function process_acf_fields( $fields = [], $group_options = [], $repeater_field = '' ) {
		foreach ( $fields as $field ) {
			$field_type = $field['type'] ?? '';
			if ( 'repeater' === $field_type ) {
				$sub_fields = $field['sub_fields'] ?? [];
				$group_options = \array_merge( $group_options, self::process_acf_fields( $sub_fields, $group_options, $field ) );
			}
			if ( ! empty( $field['key'] ) && \in_array( $field_type, self::allowed_acf_types(), true ) ) {
				$label = $field['label'] ?? $field['key'];
				if ( $repeater_field ) {
					$repeater_field_label = $repeater_field['label'] ?? $repeater_field['key'];
					$label = "{$repeater_field_label} > {$label}";
				}
				$group_options[ $field['key'] ] = $label;
			}
		}
		return $group_options;
	}

	/**
	 * Returns all choices.
	 */
	public static function get_theme_choices_all(): array {
		$choices = [
			'main' => [
				'label' => \esc_attr( 'Theme Settings', 'total-theme-core' ),
				'options' => [
					'wpex_post_title' => \esc_html__( 'Custom Page Title', 'total-theme-core' ),
					'wpex_post_subheading' => \esc_html__( 'Page Subheading', 'total-theme-core' ),
					'wpex_callout_text' => \esc_html__( 'Callout Text', 'total-theme-core' ),
				],
			]
		];

		if ( \class_exists( 'TotalThemeCore\Cpt\Portfolio', false ) && \post_type_exists( 'portfolio' ) ) {
			$choices['portfolio'] = [
				'label' => \get_post_type_object( 'portfolio' )->labels->singular_name,
				'options' => [
					'wpex_portfolio_budget' => \esc_html__( 'Budget', 'total-theme-core' ),
					'wpex_portfolio_company' => \esc_html__( 'Company Name', 'total-theme-core' ),
					'wpex_portfolio_url' => \esc_html__( 'Company URL', 'total-theme-core' ),
				],
			];
		}

		if ( \class_exists( 'TotalThemeCore\Cpt\Staff', false ) && \post_type_exists( 'staff' ) ) {
			$choices['staff'] = [
				'label' => \get_post_type_object( 'staff' )->labels->singular_name,
				'options' => [
					'wpex_staff_position' => \esc_html__( 'Position', 'total-theme-core' ),
				],
			];
		}

		if ( \class_exists( 'TotalThemeCore\Cpt\Testimonials', false ) && \post_type_exists( 'testimonials' ) ) {
			$choices['testimonials'] = [
				'label' => \get_post_type_object( 'testimonials' )->labels->singular_name,
				'options' => [
					'wpex_testimonial_author' => \esc_html__( 'Author', 'total-theme-core' ),
					'wpex_testimonial_company' => \esc_html__( 'Company', 'total-theme-core' ),
					'wpex_post_rating' => \esc_html__( 'Rating', 'total-theme-core' ),
				],
			];
		}

		return $choices;
	}

	/**
	 * Returns text choices.
	 */
	protected static function get_theme_choices_text(): array {
		return self::get_theme_choices_all();
	}

	/**
	 * Returns percent choices.
	 */
	protected static function get_theme_choices_percent(): array {
		return [];
	}

	/**
	 * Returns image choices.
	 */
	protected static function get_theme_choices_image(): array {
		$choices = [
			'main' => [
				'label' => \esc_attr( 'Theme Settings', 'total-theme-core' ),
				'options' => [
					'wpex_secondary_thumbnail' => \esc_html__( 'Secondary Thumbnail', 'total-theme-core' ),
				],
			]
		];
		return $choices;
	}

	/**
	 * Returns video choices.
	 */
	protected static function get_theme_choices_video(): array {
		$choices = [
			'main' => [
				'label' => \esc_attr( 'Theme Settings', 'total-theme-core' ),
				'options' => [
					'wpex_post_oembed' => \esc_html__( 'oEmbed URL', 'total-theme-core' ),
					'wpex_post_self_hosted_media' => \esc_html__( 'Self Hosted Video', 'total-theme-core' ),
				],
			]
		];
		return $choices;
	}

	/**
	 * Returns link choices.
	 */
	protected static function get_theme_choices_link(): array {
		$choices = [
			'main' => [
				'label' => \esc_attr( 'Theme Settings', 'total-theme-core' ),
				'options' => [
					'wpex_post_link' => \esc_html__( 'Redirect', 'total-theme-core' ),
				],
			],
		];

		if ( \class_exists( 'TotalThemeCore\Cpt\Portfolio', false ) && \post_type_exists( 'portfolio' ) ) {
			$choices['portfolio'] = [
				'label' => \get_post_type_object( 'portfolio' )->labels->singular_name,
				'options' => [
					'wpex_portfolio_url' => \esc_html__( 'Company URL', 'total-theme-core' ),
				],
			];
		}

		if ( \class_exists( 'TotalThemeCore\Cpt\Testimonials', false ) && \post_type_exists( 'testimonials' ) ) {
			$choices['testimonials'] = [
				'label' => \get_post_type_object( 'testimonials' )->labels->singular_name,
				'options' => [
					'wpex_testimonial_url' => \esc_html__( 'Company URL', 'total-theme-core' ),
				],
			];
		}

		return $choices;
	}

	/**
	 * Returns Allowed Fields.
	 */
	protected static function get_allowed_choices() {
		$choices = self::$choices;
		if ( 's' === \substr( $choices, -1 ) ) {
			$choices = \substr( $choices, 0, -1 );
		}
		return $choices;
	}

	/**
	 * Returns Allowed ACF Fields.
	 */
	protected static function allowed_acf_types(): array {
		$types = [];
		$allowed_choices = self::get_allowed_choices();

		switch ( $allowed_choices ) {
			case 'posts':
			case 'post';
				$types = [ 'post_object', 'relationship' ];
				break;
			case 'image':
				$types = [ 'image' ];
				break;
			case 'video':
				$types = [ 'oembed', 'file' ];
				break;
			case 'gallery':
				$types = [ 'gallery' ];
				break;
			case 'number':
			case 'percent':
				$types = [ 'text', 'number' ];
				break;
			case 'text':
				$types = [
					'message',
					'text',
					'textarea',
					'wysiwyg',
					'image',
					'file',
					'oembed',
					'range',
					'email',
					'url',
					'link',
					'select',
					'button_group',
					'radio',
					'date_picker',
					'date_time_picker',
					'time_picker',
					'number',
				];
				break;
			case 'link':
				$types = [
					'link',
					'image',
					'file',
					'oembed',
					'email',
					'url',
				];
				break;
			case 'date':
				$types = [
					'date_picker',
					'date_time_picker',
				];
				break;
			default:
				$types = [
					'message',
					'textarea',
					'text',
					'select',
					'number',
					'link',
					'google_map',
					'wysiwyg',
					'file',
					'range',
					'email',
					'url',
					'link',
					'image',
					'oembed',
					'date_picker',
					'date_time_picker',
					'time_picker',
					'button_group',
					'radio',
				];
				break;
		}

		return $types;
	}

}
