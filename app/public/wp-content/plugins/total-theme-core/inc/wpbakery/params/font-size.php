<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param > Font Size.
 */
final class Font_Size {

	/**
	 * Param output.
	 */
	public static function output( $settings, $value ) {
		$ogvalue            = $value;
		$show_media_queries = ! isset( $settings['responsive'] ) || true === $settings['responsive'];
		$val_is_responsive  = false;

		if ( $show_media_queries ) {
			$medias = [
				'd'  => [
					'label' => \esc_html__( 'Desktop', 'total-theme-core' ),
					'icon'  => 'dashicons dashicons-desktop',
				],
				'tl' => [
					'label' => \esc_html__( 'Tablet Landscape', 'total-theme-core' ),
					'icon'  => 'dashicons dashicons-tablet',
				],
				'tp' => [
					'label' => \esc_html__( 'Tablet Portrait', 'total-theme-core' ),
					'icon'  => 'dashicons dashicons-tablet',
				],
				'pl' => [
					'label' => \esc_html__( 'Phone Landscape', 'total-theme-core' ),
					'icon'  => 'dashicons dashicons-smartphone',
				],
				'pp' => [
					'label' => \esc_html__( 'Phone Portrait', 'total-theme-core' ),
					'icon'  => 'dashicons dashicons-smartphone',
				],
			];

			if ( $value && ! \str_contains( $value, ':' ) ) {
				$value = "d:{$value}";
			}

			$defaults = [];

			foreach ( $medias as $key => $val ) {
				$defaults[ $key ] = '';
			}

			$field_values = [];
			$params_pairs = ( $value && \is_string( $value ) ) ? \explode( '|', $value ) : '';
			if ( ! empty( $params_pairs ) ) {
				if ( \count( $params_pairs ) > 1 ) {
					$val_is_responsive = true;
				}
				foreach ( $params_pairs as $pair ) {
					$param = preg_split( '/\:/', $pair );
					if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
						$field_values[ $param[0] ] = \rawurldecode( $param[1] );
					}
				}
			}

		}

		$fields_class = 'vcex-param-font-size';

		if ( ! $val_is_responsive ) {
			$fields_class .= ' vcex-param-font-size--single';
		}

		if ( $show_media_queries ) {

			$html = '<div class="' . esc_attr( $fields_class ) . '">';

				$html .= '<div class="vcex-param-font-size__list">';
						
					foreach ( $medias as $key => $val ) {
						$field_name = "{$settings['param_name']}[{$key}]";
						$icon_class = $val['icon'];
						$hidden_class = '';
						$html .= '<div class="vcex-param-font-size__item">';
							if ( 'pl' === $key || 'tl' === $key ) {
								$icon_class .= ' dashicons--flip';
							}
							$html .= '<div class="vcex-param-font-size__icon"><span class="' . \esc_attr( $icon_class ) . '"></span></div>';
							$html .= '<label for="' . \esc_attr( $field_name ) .'" class="screen-reader-text">' . \esc_html( $medias[ $key ]['label'] ) . '</label>';
							$html .= '<input id="' . \esc_attr( $field_name ) .'" data-vcex-device="' . \esc_attr( $key ) . '" value="' . \esc_attr( $field_values[ $key ] ?? '' ) . '" type="text" placeholder="' . \esc_attr( ( 'd' === $key && isset( $settings['placeholder'] ) ) ? $settings['placeholder'] : '' ) . '">';
						$html .= '</div>';
					}

				$html .= '</div>';
				
				$html .= '<button type="button" title="' . \esc_attr__( 'Enter responsive inputs.', 'total-theme-core' ) . '" class="vcex-param-font-size__toggle" aria-pressed="' . \esc_attr( $val_is_responsive ? 'true' : 'false' ) . '"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false" fill="currentColor"><path d="M14.5 13.8c-1.1 0-2.1.7-2.4 1.8H4V17h8.1c.3 1 1.3 1.8 2.4 1.8s2.1-.7 2.4-1.8H20v-1.5h-3.1c-.3-1-1.3-1.7-2.4-1.7zM11.9 7c-.3-1-1.3-1.8-2.4-1.8S7.4 6 7.1 7H4v1.5h3.1c.3 1 1.3 1.8 2.4 1.8s2.1-.7 2.4-1.8H20V7h-8.1z"></path></svg></button>';

				$html .= '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value ' . \esc_attr( "{$settings['param_name']} {$settings['type']}_field" ) . '" type="hidden" value="' . \esc_attr( $ogvalue ) . '">';

			$html .= '</div>';

		} else {
			$html = '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput vcex_text ' . \esc_attr( "{$settings['param_name']} {$settings['type']}_field" ) . '" type="text" value="' . \esc_attr( $ogvalue ) . '">';
		}

		$html .= '<div class="vc_description vc_clearfix">';
			if ( \wp_validate_boolean( \get_theme_mod( 'wpb_param_desc_enabled', true ) ) ) {
				$html .= \sprintf(
					\esc_html__( 'Allowed units: %s', 'total-theme-core' ),
					'px, em, rem, vw, vmin, vmax.'
				);
				$html .= '<br>';
				$html .= \sprintf(
					\esc_html__( 'Allowed CSS functions: %s', 'total-theme-core' ),
					'calc(), clamp(), min(), max()'
				);
				$html .= '<br>';
			}
			if ( \function_exists( 'wpex_utl_font_sizes' ) ) {
				$vars = [];
				foreach( \array_keys( \wpex_utl_font_sizes() ) as $size ) {
					if ( $size ) {
						$vars[] = '<a href="#">' . \esc_html( $size ) . '</a>';
					}
				}
				$html .= \sprintf(
					\esc_html__( 'Allowed variables: %s', 'total-theme-core' ),
					\implode( ', ', $vars )
				);
			}
		$html .= '</div>';

		return $html;
	}

}
