<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Returns source value from vcex field.
 */
class Source_Value {

	/**
	 * Return value.
	 */
	public $value = '';

	/**
	 * Shortcode attributes.
	 */
	private $atts = '';

	/**
	 * Class Constructor.
	 */
	public function __construct( $source, $atts ) {
		if ( ! empty( $source ) && \method_exists( $this, $source ) ) {
			$this->atts = $atts;
			$this->$source();
		}
	}

	/**
	 * Site Name.
	 */
	private function site_name(): void {
		$this->value = \get_bloginfo( 'name' );
	}

	/**
	 * Post Title.
	 */
	private function post_title(): void {
		$this->value = \vcex_get_the_title();
	}

	/**
	 * Post Subheading.
	 */
	private function post_subheading(): void {
		$this->value = \get_post_meta( \vcex_get_the_ID(), 'wpex_post_subheading', true );
	}

	/**
	 * Post Date.
	 */
	private function post_date(): void {
		$this->value = \get_the_date( '', \vcex_get_the_ID() );
	}

	/**
	 * Post Modified Date.
	 */
	private function post_modified_date(): void {
		$this->value = \get_the_modified_date( '', \vcex_get_the_ID() );
	}

	/**
	 * Post Author.
	 */
	private function post_author(): void {
		$author = \get_the_author();
		if ( empty( $author ) ) {
			$post_tmp = \get_post( \vcex_get_the_ID() );
			if ( $user = \get_userdata( $post_tmp->post_author ) ) {
				$author = $user->data->display_name;
			}
		}
		$this->value = $author;
	}

	/**
	 * Term title.
	 */
	private function term_name(): void {
		$card = \vcex_card_instance();
		if ( ! empty( $card->term ) ) {
			$this->value = $card->term->name ?? '';
		} elseif ( \is_archive() ) {
			$this->value = \single_term_title( '', false );
		}
	}

	/**
	 * Archive title.
	 */
	private function archive_title(): void {
		$title = '';
		if ( \is_home() ) {
			$title = \get_the_title( \get_queried_object_id() );
		} else {
			\ob_start();
				\the_archive_title();
			$title = \ob_get_clean();
		}
		$this->value = $title;
	}

	/**
	 * Current User.
	 */
	private function current_user(): void {
		$this->value = \wp_get_current_user()->display_name;
	}

	/**
	 * Custom Field.
	 */
	private function custom_field(): void {
		$custom_field = ! empty( $this->atts['custom_field'] ) ? \sanitize_text_field( $this->atts['custom_field'] ) : null;
		if ( ! $custom_field || ! \is_string( $custom_field ) ) {
			return;
		}
		$this->value = vcex_get_meta_value( $custom_field );
		if ( ! $this->value && \vcex_is_template_edit_mode() ) {
			$this->value = \vcex_custom_field_placeholder( $custom_field );
		}
	}

	/**
	 * Callback function.
	 */
	private function callback_function(): void {
		if ( ! empty( $this->atts['callback_function'] )
			&& \vcex_validate_user_func( $this->atts['callback_function'] )
		) {
			$this->value = \call_user_func( $this->atts['callback_function'] );
		}
	}

	/**
	 * Return value.
	 */
	public function get_value() {
		return $this->value;
	}

}
