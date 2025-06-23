<?php

namespace TotalThemeCore\Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Facebook Page widget.
 */
class Widget_Facebook extends \TotalThemeCore\WidgetBuilder {

	/**
	 * Widget args.
	 */
	private $args;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->args = array(
			'id_base' => 'wpex_facebook_page_widget',
			'name'    => $this->branding() . esc_html__( 'Facebook Page', 'total-theme-core' ),
			'options' => array(
				'customize_selective_refresh' => true,
			),
			'fields'  => array(
				array(
					'id'    => 'title',
					'label' => esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				),
				array(
					'id'    => 'facebook_url',
					'label' => esc_html__( 'Facebook Page URL', 'total-theme-core' ),
					'type'  => 'text',
					'std'   => ''
				),
				array(
					'id'      => 'language',
					'label'   => esc_html__( 'Language Locale', 'total-theme-core' ),
					'type'    => 'text',
					'default' => 'en_US'
				),
				array(
					'id'      => 'tabs',
					'label'   => esc_html__( 'Tabs', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => array(
						''                => esc_html__( '— None —', 'total-theme-core' ),
						'timeline'        => esc_html__( 'Timeline', 'total-theme-core' ),
						'events'          => esc_html__( 'Events', 'total-theme-core' ),
						'timeline,events' => esc_html__( 'Timeline & Events', 'total-theme-core' ),
					),
				),
				array(
					'id'    => 'small_header',
					'label' => esc_html__( 'Use small header', 'total-theme-core' ),
					'type'  => 'checkbox',
				),
				array(
					'id'    => 'hide_cover',
					'label' => esc_html__( 'Hide Cover Photo', 'total-theme-core' ),
					'type'  => 'checkbox',
				),
				array(
					'id'      => 'show_facepile',
					'label'   => esc_html__( 'Show Faces', 'total-theme-core' ),
					'type'    => 'checkbox',
					'default' => 'on',
				),
				array(
					'id'    => 'lazy_load',
					'label' => esc_html__( 'Lazy Load', 'total-theme-core' ),
					'type'  => 'checkbox',
				),
				array(
					'id'          => 'disable_javascript_sdk',
					'label'       => esc_html__( 'Disable JavaScript SDK', 'total-theme-core' ),
					'type'        => 'checkbox',
					'description' => esc_html__( 'Check this box to remove the call for the Facebook sdk.js file. This is useful to prevent duplicate calls if another plugin or code on your site is already loading it.', 'total-theme-core' ),
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
		extract( $this->parse_instance( $instance ) );

		echo wp_kses_post( $args['before_widget'] );

		$this->widget_title( $args, $instance );

		if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {

			esc_html_e( 'Facebook widget does not display in the Customizer because it can slow things down.', 'total-theme-core' );

		} elseif ( $facebook_url ) {

			$language = ! empty( $language ) ? sanitize_text_field( $language ) : 'en_US';

			$attrs = [
				'class'                      => 'fb-page wpex-overflow-hidden wpex-align-top',
				'data-href'                  => esc_url( do_shortcode( (string) $facebook_url ) ),
				'data-small-header'          => esc_attr( (string) $small_header ),
				'data-adapt-container-width' => 'true',
				'data-hide-cover'            => esc_attr( (string) $hide_cover ),
				'data-show-facepile'         => esc_attr( (string) $show_facepile ),
				'data-width'                 => 500,
				'data-lazy'                  => esc_attr( (string) $lazy_load ),
			];

			if ( $tabs ) {
				$attrs['data-tabs'] = $tabs;
			}

			?>

			<div<?php

				foreach ( $attrs as $name => $value ) {
					echo ' ' . $name . '=' . '"' . esc_attr( $value ) . '"';
				}

			?>></div>

			<?php if ( empty( $disable_javascript_sdk ) ) { ?>
				<div id="fb-root"></div>
				<script async defer crossorigin="anonymous" src="https://connect.facebook.net/<?php echo esc_html( $language ); ?>/sdk.js#xfbml=1&version=v17.0" nonce="VPHq5L0q"></script>
			<?php } ?>

		<?php }

		echo wp_kses_post( $args['after_widget'] );
	}

}

register_widget( 'TotalThemeCore\\Widgets\\Widget_Facebook' );
