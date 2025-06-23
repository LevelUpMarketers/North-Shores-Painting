<?php

namespace TotalTheme\Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * Registers a new widget area with WP.
 */
class Register_Widget_Area {

	/**
	 * Constructor.
	 */
	public function __construct( $location = '', $args = [] ) {
		$method = "register_{$location}_widget_area";

		if ( \method_exists( $this, $method) ) {
			$this->$method( $args );
		}
	}

	/**
	 * Registers a new sidebar widget area.
	 */
	protected function register_sidebar_widget_area( $args = [] ) {
		if ( ! class_exists( 'TotalTheme\Sidebars\Primary' ) ) {
			return;
		}

		$widget_class = 'sidebar-box widget %2$s wpex-mb-30 wpex-clr';
		$widget_class = \apply_filters( 'wpex_sidebar_widget_class', $widget_class );
		$widget_title_args = totaltheme_call_static( 'Sidebars\Primary', 'widget_title_args' );

		$default_args = [
			'before_widget' => '<div id="%1$s" class="' . \esc_attr( $widget_class ) . '">',
			'after_widget'  => '</div>',
			'before_title'  => $widget_title_args['before'] ?? '',
			'after_title'   => $widget_title_args['after'] ?? '',
		];

		$args = \wp_parse_args( $args, $default_args );

		if ( empty( $args['id'] ) ) {
			return;
		}

		\register_sidebar( $args );
	}

	/**
	 * Registers a new footer widget area.
	 */
	protected function register_footer_widget_area( $args = [] ) {
		$widget_class = 'footer-widget widget wpex-pb-40 wpex-clr %2$s';
		$widget_class = \apply_filters( 'wpex_footer_widget_class', $widget_class );
		$widget_title_args = totaltheme_call_static( 'Footer\Widgets', 'widget_title_args' );

		$default_args = [
			'before_widget' => '<div id="%1$s" class="' . \esc_attr( $widget_class ) . '">',
			'after_widget'  => '</div>',
			'before_title'  => $widget_title_args['before'] ?? '',
			'after_title'   => $widget_title_args['after'] ?? '',
		];

		$args = \wp_parse_args( $args, $default_args );

		if ( empty( $args['id'] ) ) {
			return;
		}

		\register_sidebar( $args );
	}

}
