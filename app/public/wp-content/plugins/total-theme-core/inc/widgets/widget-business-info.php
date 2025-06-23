<?php

namespace TotalThemeCore\Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Business Info widget.
 */
class Widget_Business_Info extends \TotalThemeCore\WidgetBuilder {

	/**
	 * Widget args.
	 */
	private $args;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->args = [
			'id_base' => 'wpex_info_widget',
			'name'    => $this->branding() . \esc_html__( 'Business Info', 'total-theme-core' ),
			'options' => [
				'customize_selective_refresh' => true,
			],
			'fields'  => [
				[
					'id'    => 'title',
					'label' => \esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				],
				[
					'id'    => 'address',
					'label' => \esc_html__( 'Address', 'total-theme-core' ),
					'type'  => 'textarea',
				],
				[
					'id'    => 'phone_number',
					'label' => \esc_html__( 'Phone Number', 'total-theme-core' ),
					'type'  => 'text',
				],
				[
					'id'    => 'phone_number_mobile',
					'label' => \esc_html__( 'Mobile Phone Number', 'total-theme-core' ),
					'type'  => 'text',
				],
				[
					'id'    => 'phone_number_tel_link',
					'label' => \esc_html__( 'Add "tel" link to the phone number?', 'total-theme-core' ),
					'type'  => 'checkbox',
				],
				[
					'id'    => 'fax_number',
					'label' => \esc_html__( 'Fax Number', 'total-theme-core' ),
					'type'  => 'text',
				],
				[
					'id'    => 'email',
					'label' => \esc_html__( 'Email', 'total-theme-core' ),
					'type'  => 'text',
				],
				[
					'id'          => 'email_label',
					'label'       => \esc_html__( 'Email Label', 'total-theme-core' ),
					'type'        => 'text',
					'description' => \esc_html__( 'Will display your email by default if this field is empty.', 'total-theme-core' ),
				],
				[
					'id'      => 'has_icons',
					'default' => true,
					'label'   => \esc_html__( 'Show icons?', 'total-theme-core' ),
					'type'    => 'checkbox',
				],
				[
					'id'      => 'item_bottom_margin',
					'label'   => \esc_html__( 'Bottom margin between items.', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'margin',
				],
			],
		];

		$this->create_widget( $this->args );
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		extract( $this->parse_instance( $instance ) );

		echo \wp_kses_post( $args['before_widget'] );

		$this->widget_title( $args, $instance );

		// Define item bottom margin class.
		$bm_class = 'wpex-mb-10';

		if ( $item_bottom_margin
			&& \function_exists( '\wpex_utl_margins' )
			&& \array_key_exists( $item_bottom_margin, \wpex_utl_margins() )
		) {
			$bm_class = 'wpex-mb-' . sanitize_html_class( absint( $item_bottom_margin ) );
		}

		$has_icons = isset( $has_icons ) && \wp_validate_boolean( $has_icons ) && \function_exists( '\totaltheme_get_icon' );

		// Define widget output.
		$output = '';

		$output .= '<ul class="wpex-info-widget wpex-last-mb-0">';

		// Address.
		if ( $address ) {
			$output .= '<li class="wpex-info-widget-address wpex-flex ' . $bm_class . '">';
				if ( $has_icons ) {
					$output .= '<div class="wpex-info-widget-icon wpex-mr-10">' . \totaltheme_get_icon( 'map-marker', 'wpex-icon--w' ) . '</div>';
				}
				$output .= '<div class="wpex-info-widget-data wpex-flex-grow wpex-last-mb-0">' . wpautop( wp_kses_post( $address ) ) . '</div>';
			$output .= '</li>';
		}

		// Phone number.
		if ( $phone_number ) {
			$output .= '<li class="wpex-info-widget-phone wpex-flex ' . $bm_class . '">';
				if ( $has_icons ) {
					$output .= '<div class="wpex-info-widget-icon wpex-mr-10">' . \totaltheme_get_icon( 'phone', 'wpex-icon--w' ) . '</div>';
				}
				$output .= '<div class="wpex-info-widget-data wpex-flex-grow">';
					if ( \wp_validate_boolean( $phone_number_tel_link ) ) {
						$output .= '<a href="tel:' . \sanitize_text_field( $phone_number ) . '">' . \esc_html( $phone_number ) . '</a>';
					} else {
						$output .= \esc_html( $phone_number );
					}
				$output .= '</div>';
			$output .= '</li>';
		}

		// Phone number mobile.
		if ( $phone_number_mobile ) {
			$output .= '<li class="wpex-info-widget-phone-mobile wpex-flex ' . $bm_class . '">';
				if ( $has_icons ) {
					$output .= '<div class="wpex-info-widget-icon wpex-mr-10">' . \totaltheme_get_icon( 'mobile', 'wpex-icon--w' ) . '</div>';
				}
				$output .= '<div class="wpex-info-widget-data wpex-flex-grow">';
					if ( true == wp_validate_boolean( $phone_number_tel_link ) ) {
						$output .= '<a href="tel:' . sanitize_text_field( $phone_number_mobile ) . '">' . \esc_html( $phone_number_mobile ) . '</a>';
					} else {
						$output .= \esc_html( $phone_number_mobile );
					}
				$output .= '</div>';
			$output .= '</li>';
		}

		// Fax number.
		if ( $fax_number ) {
			$output .= '<li class="wpex-info-widget-fax wpex-flex ' . $bm_class . '">';
				if ( $has_icons ) {
					$output .= '<div class="wpex-info-widget-icon wpex-mr-10">' . \totaltheme_get_icon( 'fax', 'wpex-icon--w' ) . '</div>';
				}
				$output .= '<div class="wpex-info-widget-data wpex-flex-grow">' . \esc_html( $fax_number ) . '</div>';
			$output .= '</li>';
		}

		// Email.
		if ( $email ) {

			// Sanitize email.
			$sanitize_email = \sanitize_email( $email );
			$is_email       = \is_email( $sanitize_email );

			// Spam protect email address.
			$protected_email = $is_email ? \antispambot( $sanitize_email ) : $sanitize_email;

			// Sanitize & fallback for email label.
			$email_label = ( ! $email_label && $is_email ) ? $protected_email : $email_label;

			// Email output.
			$output .= '<li class="wpex-info-widget-email wpex-flex ' . $bm_class . '">';

				if ( $has_icons ) {
					$output .= '<div class="wpex-info-widget-icon wpex-mr-10">' . \totaltheme_get_icon( 'envelope', 'wpex-icon--w' ) . '</div>';
				}

				$output .= '<div class="wpex-info-widget-data wpex-flex-grow">';

					if ( $is_email ) {
						$output .= '<a href="mailto:' . $protected_email . '">' . \esc_html( $email_label ) . '</a>';
					} else {
						$parse_email_url = parse_url( $email );

						if ( ! empty( $parse_email_url['scheme'] ) ) {
							$output .= '<a href="' . esc_url( $email ) . '">' . \esc_html( $email_label ) . '</a>';
						} else {
							$output .= \esc_html( $email_label );
						}
					}

				$output .= '</div>';

			$output .= '</li>';

		}

		$output .= '</ul>';

		echo $output;

		echo wp_kses_post( $args['after_widget'] );
	}

}

register_widget( 'TotalThemeCore\Widgets\Widget_Business_Info' );
