<?php
namespace TotalTheme\Forms;

\defined( 'ABSPATH' ) || exit;

/**
 * Used for the select wrapper.
 */
class Select_Wrap {

	/**
	 * Returns select wrap open element.
	 */
	public static function open(): void {
		echo '<div class="wpex-select-wrap">';
	}

	/**
	 * Returns the custom select arrow.
	 */
	public static function arrow(): void {
		echo '<div class="wpex-select-arrow">' . self::get_arrow_icon_html() . '</div>';
	}

	/**
	 * Returns arrow theme icon name.
	 */
	public static function get_arrow_ticon(): string {
		$icon = 'material-arrow-down-ios';
		return (string) \apply_filters( 'wpex_select_wrap_arrow_ticon', $icon );
	}

	/**
	 * Returns select arrow html.
	 */
	public static function get_arrow_icon_html(): string {
		$html = \totaltheme_get_icon( self::get_arrow_ticon(), 'wpex-select-arrow__icon wpex-icon--sm wpex-flex' );
		return (string) \apply_filters( 'wpex_select_wrap_arrow_html', $html );
	}

}
