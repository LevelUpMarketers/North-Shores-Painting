<?php declare(strict_types=1);

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Returns a description for a param type.
 */
class Param_Description {

	/**
	 * Return description.
	 */
	public function get( string $type ): string {
		if ( \method_exists( $this, $type ) ) {
			return (string) $this->$type();
		}
		return '';
	}

	/**
	 * Card select
	 */
	protected function card_select() {
		$desc = \esc_html__( 'Select your card style. Note: Not all settings are used for every card style', 'total-theme-core' ) . '<br>' . \sprintf( \esc_html__( '%sPreview card styles%s', 'total-theme-core' ), '<a href="https://totalwptheme.com/cards/" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' );
		if ( \post_type_exists( 'wpex_card' ) ) {
			$desc .= ' | ' . \sprintf( \esc_html__( '%sCreate Card%s', 'total-theme-core' ), '<a href="' . \esc_url( \admin_url( 'edit.php?post_type=' . 'wpex_card' ) ) . '" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' );
		}
		return $desc;
	}

	/**
	 * Advanced Query.
	 */
	protected function advanced_query() {
		return \sprintf( \esc_html__( 'Build a query according to the WordPress Codex in string format or enter a custom callback function name that will return an array of query arguments. %sview docs%s', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/advanced-query-setting/" target="_blank" rel="noopener noreferrer">', '&#8599;</a>' );
	}

	/**
	 * Header Style.
	 */
	protected function header_style() {
		return \sprintf( \esc_html__( 'Select your custom heading style. You can select your global style in %sthe Customizer%s.', 'total-theme-core' ), '<a href="' . \esc_url( \admin_url( '/customize.php?autofocus[section]=wpex_theme_heading' ) ) . '" target="_blank" rel="noopener noreferrer">', '&#8599;</a>' );
	}

	/**
	 * Unique ID.
	 */
	protected function unique_id() {
		return \sprintf( \esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %sw3c specification%s).', 'total-theme-core' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' );
	}

	/**
	 * Extra classname.
	 */
	protected function el_class() {
		return \sprintf( \esc_html__( 'Enter a classname to target this element with custom CSS or multiple classnames separated by an empty space. You may use theme %sCSS framework%s classes, however some may not work if they are being overwritten by the element.', 'total-theme-core' ), '<a href="https://totalwptheme.com/css-framework/" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' );
	}

	/**
	 * Text HTML
	 */
	protected function text_html() {
		return \esc_html__( 'Allowed:', 'total-theme-core' ) . ' ' . \esc_html__( 'text', 'total-theme-core' ) . ', ' . \esc_html__( 'shortcodes', 'total-theme-core' ) . ', ' . $this->allowed_dynamic_vars() . ', HTML';
	}

	/**
	 * Text.
	 */
	protected function text() {
		return \esc_html__( 'Allowed:', 'total-theme-core' ) . ' ' . \esc_html__( 'text', 'total-theme-core' ) . ', ' . \esc_html__( 'shortcodes', 'total-theme-core' ) . ', ' . $this->allowed_dynamic_vars();
	}

	/**
	 * Link.
	 */
	protected function link() {
		return \esc_html__( 'Enter your custom link url, lightbox url or local/toggle element ID (including a # at the front).', 'total-theme-core' ) . '<br>' . $this->text();
	}

	/**
	 * PX.
	 */
	protected function px() {
		return \esc_html__( 'Allowed:', 'total-theme-core' ) . ' px';
	}

	/**
	 * Border Radius.
	 */
	protected function border_radius() {
		$description = \esc_html__( 'Allowed:', 'total-theme-core' ) . ' px, rem, %';
		$description .= '<br>';
		$description .= \sprintf(
			\esc_html__( 'Shorthand allowed: %s', 'total-theme-core' ),
			'top right bottom left'
		);
		return $description;
	}

	/**
	 * Width.
	 */
	protected function width() {
		$description = \sprintf(
			\esc_html__( 'Allowed units: %s', 'total-theme-core' ),
			'px, em, rem, vw, vmin, vmax'
		) . '<br>';
		$description .= \sprintf(
			\esc_html__( 'Allowed Keywords: %s', 'total-theme-core' ),
			'max-content, min-content, fit-content'
		) . '<br>';
		$description .= \sprintf(
			\esc_html__( 'Allowed CSS functions: %s', 'total-theme-core' ),
			'calc(), clamp(), min(), max()'
		);
		return $description;
	}

	/**
	 * Height.
	 */
	protected function height() {
		$description = \sprintf(
			\esc_html__( 'Allowed units: %s', 'total-theme-core' ),
			'px, em, rem, vh, vmin, vmax'
		) . '<br>';
		$description .= \sprintf(
			\esc_html__( 'Allowed Keywords: %s', 'total-theme-core' ),
			'max-content, min-content, fit-content'
		) . '<br>';
		$description .= \sprintf(
			\esc_html__( 'Allowed CSS functions: %s', 'total-theme-core' ),
			'calc(), clamp(), min(), max()'
		);
		return $description;
	}

	/**
	 * Border.
	 */
	protected function border() {
		return esc_html__( 'Please use the shorthand format: width style color. Enter a single value (such as 4px) to modify the border width only.', 'total-theme-core' );
	}

	/**
	 * Border Width.
	 */
	protected function border_width() {
		$description = \sprintf(
			\esc_html__( 'Allowed units: %s', 'total-theme-core' ),
			'px, rem, em, thin, medium, thick'
		);
		$description .= '<br>';
		$description .= \sprintf(
			\esc_html__( 'Shorthand allowed: %s', 'total-theme-core' ),
			'top right bottom left'
		);
		return $description;
	}

	/**
	 * Padding.
	 */
	protected function padding() {
		return \sprintf(
			\esc_html__( 'Allowed: %s', 'total-theme-core' ),
			'px, rem, em, vmin, vmax, vw, vh, %'
		);
	}

	/**
	 * Margin.
	 */
	protected function margin() {
		return \sprintf(
			\esc_html__( 'Allowed: %s', 'total-theme-core' ),
			'px, rem, em, vw, vmin, vmax, calc, var'
		);
	}

	/**
	 * Margin Shorthand.
	 */
	protected function margin_shorthand() {
		return \esc_html__( 'Please use the following format: top right bottom left.', 'total-theme-core' );
	}

	/**
	 * Line Height.
	 */
	protected function line_height() {
		return \sprintf(
			esc_html_x( 'Allowed: text, number, %s', 'Allowed values for the Line Height wpbakery field used in Total elements the variable is a list of allowed CSS units.', 'total-theme-core' ),
			'px, %'
		);
	}

	/**
	 * Letter Spacing.
	 */
	protected function letter_spacing() {
		return \sprintf(
			\esc_html__( 'Allowed: %s', 'total-theme-core' ),
			'px, rem, vmin, vmax'
		);
	}

	/**
	 * Opacity.
	 */
	protected function opacity() {
		return \esc_html__( 'Enter a decimal or percentage value.', 'total-theme-core' );
	}

	/**
	 * Icon Size.
	 */
	protected function icon_size() {
		return \sprintf(
			\esc_html__( 'Allowed: %s', 'total-theme-core' ),
			'px, rem, vw, vmin, vmax'
		);
	}

	/**
	 * Gap.
	 */
	protected function gap() {
		return \sprintf(
			\esc_html__( 'Allowed: %s', 'total-theme-core' ),
			'px, rem, em, vw, vmin, vmax, calc, var'
		);
	}

	/**
	 * Milliseconds.
	 */
	protected function ms() {
		return \esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' );
	}

	/**
	 * Font Size.
	 */
	protected function font_size() {
		return \sprintf(
			\esc_html__( 'Allowed: %s', 'total-theme-core' ),
			'px, em, rem, vw, vmin, vmax'
		);
	}

	/**
	 * Allowed Dynamic Vars.
	 */
	protected function allowed_dynamic_vars() {
		return '<a href="https://totalwptheme.com/docs/dynamic-variables/" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'dynamic variables', 'total-theme-core' ) . ' &#8599;</a>';
	}

}
