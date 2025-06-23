<?php

namespace TotalTheme\Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * SVG Sanitizer.
 */
class SVG_Sanitizer {

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Sanitize potentially dirty SVG.
	 */
	public function sanitize( $dirty ): string {
        $dirty = preg_replace( '/<\?(=|php)(.+?)\?>/i', '', (string) $dirty ); // strip php tags.
		
		return (string) \wp_kses( $dirty, $this->get_allowed_html() );
	}

	/**
	 * Returns array of allowed html.
	 */
	private function get_allowed_html(): array {
		$allowed_html = [
			'svg' => [
				'aria-hidden' => [],
				'aria-labelledby' => [],
				'class' => [],
				'height' => [],
				'width' => [],
				'viewbox' => [],
				'role' => [],
				'xmlns' => [],
				'preserveaspectratio' => [],
				'fill' => [],
				'focusable' => [],
			],
			'g' => [
				'fill' => [],
			],
			'title' => [
				'title' => [],
			],
			'path' => [
				'd' => [],
				'fill' => [],
			],
		];
        return apply_filters( 'totaltheme/helpers/svg_sanitizer/allowed_html', $allowed_html );
	}

}
