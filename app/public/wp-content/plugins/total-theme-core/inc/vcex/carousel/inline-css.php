<?php

namespace TotalThemeCore\Vcex\Carousel;

\defined( 'ABSPATH' ) || exit;

/**
 * Generates Inline CSS for carousels.
 */
final class Inline_CSS {

	/**
	 * Unique Classname.
	 */
	protected $classname = '';

	/**
	 * CSS.
	 */
	protected $css = '';

	/**
	 * Settings.
	 */
	protected $settings = [];

	/**
	 * Check if we fail to write complete CSS for the carousel.
	 */
	protected $fails = false;

	/**
	 * Renders CSS.
	 */
	public function __construct( $class = '', $settings = [] ) {
		$this->parse_settings( $settings );
		$this->classname    = $class;
		$this->css          = '';
	}

	/**
	 * Renders CSS.
	 */
	public function render() {
		if ( isset( $this->settings['items'] ) && 1 === $this->settings['items'] ) {
			return;
		}
		$center    = \wp_validate_boolean( $this->settings['center'] ?? false );
		$autoWidth = \wp_validate_boolean( $this->settings['autoWidth'] ?? false );
		if ( $center || $autoWidth ) {
			return;
		}
		$margin = $this->settings['margin'] ?? '';
		if ( ! empty( $margin ) || 0 === \absint( $margin ) ) {
			$this->css .= $this->var_css( '--wpex-carousel-gap', \absint( $margin ) . 'px' );
		}
		$this->css .= $this->generate_responsive_css();
		if ( $this->css && ! $this->fails ) {
			echo '<style class="vcex-carousel-preload-css">' . \wp_strip_all_tags( $this->css ) . '</style>';
		}
	}

	/**
	 * Parse settings.
	 */
	protected function parse_settings( $settings = [] ) {
		if ( \is_string( $settings ) ) {
			$this->settings = \json_decode( \htmlspecialchars_decode( $settings ), true );
			return;
		}
		if ( \is_array( $settings ) ) {
			$this->settings = $settings;
			return $this->settings;
		}
	}

	/**
	 * Generates var css.
	 */
	protected function var_css( $var = '', $value = '' ) {
		$safe_value = \esc_attr( $value );
		return ".{$this->classname}{{$var}:{$safe_value};}";
	}

	/**
	 * Generates CSS to hide slides.
	 */
	protected function hide_slides( $items = 0 ) {
		$items = absint( $items );
		if ( $items ) {
			$items = $items + 1;
			$css = ".{$this->classname}.wpex-carousel:not(.wpex-carousel--loaded) > *:not(:nth-child(1n+{$items})){display: flex !important;}";
			return $css;
		}
	}

	/**
	 * Generates responsive css.
	 *
	 * Important: We must check the breakpoints specifically because if any isn't defined the CSS must "fail".
	 */
	protected function generate_responsive_css() {
		$breakpoints = [ '0', '480', '768', '960' ]; // @todo loop through $this->settings['responsive'] instead and just fail whenever items isn't set?

		foreach ( $breakpoints as $breakpoint ) {
			if ( ! empty( $this->settings['responsive'][$breakpoint]['items'] )
				&& is_numeric( $this->settings['responsive'][$breakpoint]['items'] )
			) {
				$bk_css = '';
				$bk_css .= $this->var_css( '--wpex-carousel-columns', absint( $this->settings['responsive'][$breakpoint]['items'] ) );
				$bk_css .= $this->hide_slides( $this->settings['responsive'][$breakpoint]['items'] );
				if ( '0' === $breakpoint ) {
					$this->css .= $bk_css;
				} else {
					$this->css .= $this->media_query( absint( $breakpoint ), $bk_css );
				}
			} else {
				$this->fails = true;
				break;
			}
		}
	}

	/**
	 * Wrap in media query.
	 */
	protected function media_query( $breakpoint = '', $css = '' ) {
		if ( ! $css ) {
			return;
		}
		return "@media only screen and (min-width: {$breakpoint}px) {{$css}}";
	}

}
