<?php

namespace TotalThemeCore\Widgets;

\defined( 'ABSPATH' ) || exit;

/**
 * Minimal Newsletter widget.
 */
class Widget_Simple_Newsletter extends \TotalThemeCore\WidgetBuilder {

	/**
	 * Widget args.
	 */
	private $args;

	/**
	 * Register widget with WordPress.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->args = array(
			'id_base' => 'wpex_newsletter',
			'name'    => $this->branding() . \esc_html__( 'Newsletter Form v2', 'total-theme-core' ),
			'options' => array(
				'customize_selective_refresh' => true,
				'description' => \esc_html__( 'Single line newsletter form.', 'total-theme-core' ),
			),
			'fields'  => array(
				array(
					'id'    => 'title',
					'label' => \esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'    => 'description',
					'label' => \esc_html__( 'Description', 'total-theme-core' ),
					'type'  => 'textarea',
				),
				array(
					'id'          => 'form_action',
					'label'       => \esc_html__( 'Form Action URL', 'total-theme-core' ),
					'type'        => 'text',
					'description' => '<a href="https://totalwptheme.com/docs/mailchimp-form-action-url/" target="_blank">' . \esc_html__( 'Learn more', 'total-theme-core' ) . '&rarr;</a>',
				),
				array(
					'id'      => 'placeholder_text',
					'label'   => \esc_html__( 'Email Placeholder Text', 'total-theme-core' ),
					'type'    => 'text',
					'default' => \esc_html__( 'Your email address', 'total-theme-core' ),
				),
				array(
					'id'          => 'input_name',
					'label'       => \esc_html__( 'Email Input Attribute', 'total-theme-core' ),
					'type'        => 'text',
					'default'     =>'EMAIL',
					'description' => \esc_html__( 'Used for the input name attribute value.', 'total-theme-core' ),
				),
				array(
					'id'      => 'button_text',
					'label'   => \esc_html__( 'Button Text', 'total-theme-core' ),
					'type'    => 'text',
					'default' => \esc_html__( 'Sign Up', 'total-theme-core' ),
				),
				array(
					'id'      => 'space_between',
					'label'   => \esc_html__( 'Spacing Between Input & Button', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => \function_exists( 'wpex_utl_margins' ) ? \wpex_utl_margins() : array(),
				),
				// Hidden Fields.
				array(
					'id'      => 'hidden_fields',
					'label'   => \esc_html__( 'Hidden Fields', 'total-theme-core' ),
					'type'   => 'repeater',
					'fields' => [
						[
							'id'    => 'name',
							'label' => \esc_html__( 'Name', 'total-theme-core' ),
							'type'  => 'text',
						],
						[
							'id'    => 'value',
							'label' => \esc_html__( 'Value', 'total-theme-core' ),
							'type'  => 'text',
						],
					],
				),
			),
		);

		$this->create_widget( $this->args );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 * @since 1.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		\extract( $this->parse_instance( $instance ) );

		// Before widget hook.
		echo \wp_kses_post( $args['before_widget'] );

		// Display widget title.
		$this->widget_title( $args, $instance );

		// Define html var.
		$html = '';

		// Display the description.
		if ( ! empty( $description ) && \is_string( $description ) ) {
			$html .= '<div class="wpex-newsletter-widget-description wpex-text-sm wpex-mb-15 wpex-last-mb-0">';
				$html .= \wp_kses_post( \trim( $description ) );
			$html .= '</div>';
		}

		// Sanitize args.
		$input_name = ! empty( $input_name ) ? $input_name : 'EMAIL';

		/**
		 * Filters the newsletter widget action url.
		 *
		 * @param string $form_action
		 * @param array $instance | widget instance
		 */
		$form_action = (string) \apply_filters( 'totalthemecore/widgets/newsletter/form_action', $form_action, $instance );

		/*** deprecated ***/
		$form_action = \apply_filters( 'wpex_newsletter_widget_action_url', $form_action, $instance );

		// Begin output.
		$html .= '<form action="'. \esc_attr( $form_action ) .'" method="post" class="wpex-simple-newsletter wpex-flex wpex-w-100 wpex-justify-center validate">';

			$label_class = 'wpex-flex-grow';

			if ( ! empty( $space_between ) ) {
				$label_class .= ' wpex-mr-' . \sanitize_html_class( \absint( $space_between ) );
			}
			
			$input_unique_id = isset( $args['widget_id'] ) ? $args['widget_id'] : uniqid( 'wpex_newsletter-' );
			$input_id = "{$input_unique_id}-input";

			// Email field.
			$html .= '<label for="' . \esc_attr( $input_id ) . '" class="screen-reader-text">' . \esc_html( $placeholder_text ) . '</label>';
			$html .= '<input id="' . \esc_attr( $input_id ) . '" type="email" name="' . \esc_attr( $input_name ) . '" placeholder="' . \esc_attr( $placeholder_text ) . '" autocomplete="off" class="wpex-simple-newsletter-input wpex-border wpex-border-solid wpex-border-surface-4 wpex-outline-0 wpex-p-10 wpex-w-100 wpex-surface-1 wpex-text-2 wpex-p-10 wpex-unstyled-input wpex-leading-normal" required>';

			// Hidden fields.
			$hidden_fields = $hidden_fields ?? [];

			/**
			 * Filters the hidden fields.
			 */
			$hidden_fields = (array) \apply_filters( 'totalthemecore/widgets/newsletter/hidden_fields', $hidden_fields, $instance );

			if ( $hidden_fields && \is_array( $hidden_fields ) ) {
				foreach ( $hidden_fields as $hidden_field ) {
					if ( isset( $hidden_field['name'] ) && isset( $hidden_field['value'] ) ) {
						$html .= '<input type="hidden" name="' . \esc_attr(  $hidden_field['name'] ) . '" value="' . \esc_attr( $hidden_field['value'] ) . '">';
					}
				}
			}

			/*** deprecated ***/
			$html .= \apply_filters( 'wpex_newsletter_widget_form_extras', null );

			// Submit button.
			if ( empty( $button_text ) ) {
				$button_text = \esc_html__( 'Sign Up', 'total-theme-core' );
			}

			$html .= '<button type="submit" value="" name="subscribe" class="wpex-simple-newsletter-button wpex-flex-shrink-0 wpex-uppercase wpex-font-semibold wpex-tracking-wide wpex-py-10 wpex-px-15 wpex-text-xs wpex-truncate theme-button wpex-rounded-0 wpex-leading-normal">' . \do_shortcode( \sanitize_text_field( $button_text ) ) . '</button>';

		$html .= '</form>';

		echo $html;

		echo \wp_kses_post( $args['after_widget'] );
	}

}

register_widget( 'TotalThemeCore\Widgets\Widget_Simple_Newsletter' );

