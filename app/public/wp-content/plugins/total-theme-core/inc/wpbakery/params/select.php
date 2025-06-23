<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Select.
 */
final class Select {

	/**
	 * Returns true or false if the current select value exists as an option.
	 */
	protected static $value_exists;

	/**
	 * Renders the custom param field.
	 */
	public static function output( $settings = [], $value = '' ) {
		self::$value_exists = false; // must reset every time
		$type               = $settings['choices'] ?? $settings['param_name'] ?? []; // get the select choice type
		$value              = self::parse_value( $value, $type ); // !! must go before TotalThemeCore\Vcex\Setting_Choices !!

		if ( ( '' === $value || null === $value ) && isset( $settings['std'] ) ) {
			$value = $settings['std'];
		}

		if ( ! empty( $settings['choices_callback'] ) ) {
			$choices = is_callable( $settings['choices_callback'] ) ? call_user_func( $settings['choices_callback'] ) : [];
		} elseif ( \class_exists( '\TotalThemeCore\Vcex\Setting_Choices' ) ) {
			$choices = (new \TotalThemeCore\Vcex\Setting_Choices( $type, $settings ))->get_choices();
		}

		// If we don't have any choices return a text field with the value
		// This is important to prevent issues where the choices is empty but we had a value saved.
		if ( empty( $choices ) ) {
			return '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="' . \esc_attr( "wpb_vc_param_value wpb-textinput {$settings['param_name']} {$settings['type']}" ) . '" type="text" value="' . \esc_attr( $value ) . '">';
		}

		$select_class = "wpb_vc_param_value wpb-input wpb-select {$settings['param_name']} {$settings['type']}";

		if ( 'card_style' === $type || ( isset( $settings['chosen_select'] ) && true === $settings['chosen_select'] ) ) {
			$select_class .= ' vcex-chosen';
		}

		$output = '<select name="' . \esc_attr( $settings['param_name'] ) . '" class="' . \esc_attr( $select_class ) .'">';
			$options_out = self::render_options( $choices, $value );
			// If value isn't part of the choices we add it anyway to the select so it can be saved
			if ( $value && ! self::$value_exists ) {
				$option_name = $value;
				if ( isset( $settings['choices'] ) && 'template' === $settings['choices'] ) {
					$option_name = \get_the_title( $value ) ?: $value;
				}
				$options_out = '<option value="' . \esc_attr( $value )  . '" ' . \selected( $value, $value, false ) . '>' . \esc_html__( $option_name ) . '</option>' . $options_out;
			}
		$output .= $options_out;
		$output .= '</select>';

		if ( 'acf_repeater_templates' === $type && current_user_can( 'publish_wpex_templates' ) ) {
			$description = \sprintf(
				\esc_html__( '%sCreate new template &#8599;%s', 'total' ),
				'<a class="vcex-create-template-link" href="' . \admin_url( 'post-new.php?post_type=wpex_templates&wpex_template_type=acf_repeater' ) . '" target="_blank" rel="noopener noreferrer">',
				'</a>',
			);
			$description .= \sprintf(
				\esc_html__( '%sRefresh list%s', 'total' ),
				'<span class="hidden"> | <a href="#" data-vcex-refresh-choices="acf_repeater_templates" data-vcex-nonce="' . wp_create_nonce( 'vcex_params_ajax' ) . '">',
				'</a></span>'
			);
		}

		if ( ! empty( $description ) ) {
			$allowed_html = [
				'span' => [
					'class' => [],
				],
				'a' => [
					'href' => [],
					'rel' => [],
					'target' => [],
					'data-vcex-refresh-choices' => [],
					'data-vcex-nonce' => [],
					'class' => [],
				],
			];
			$output .= '<div class="vc_description vcex-param-description">' . \wp_kses( $description, $allowed_html ) . '</div>';
		}

		return $output;
	}

	/**
	 * Render select options.
	 */
	protected static function render_options( $choices = [], $selected = '' ): string {
		$html = '';
		foreach ( $choices as $choice_k => $choice_v ) {
			if ( is_array( $choice_v ) ) {
				$sub_choices = $choice_v['choices'] ?? $choice_v['options'] ?? [];
				if ( $sub_choices ) {
					$html .= '<optgroup label="' . esc_attr( $choice_v['label'] ?? '' ) . '">';
						$html .= self::render_options( $sub_choices, $selected );
					$html .= '</optgroup>';
				}
			} else {
				// @note we use a loose check to support numbers
				if ( $selected && ! self::$value_exists && $choice_k == $selected ) {
					self::$value_exists = true;
				}
				$html .= '<option value="' . \esc_attr( $choice_k )  . '" ' . \selected( $selected, $choice_k, false ) . '>' . \esc_attr( $choice_v ) . '</option>';
			}
		}
		return $html;
	}

	/**
	 * Parses the field value.
	 */
	protected static function parse_value( $value, $choices ) {
		if ( $value ) {
			switch ( $choices ) {
				case 'visibility':
					$value = \str_replace( '-portrait', '', $value );
					$value = \str_replace( '-landscape', '', $value );
					break;
			}
		}
		return $value;
	}

}
