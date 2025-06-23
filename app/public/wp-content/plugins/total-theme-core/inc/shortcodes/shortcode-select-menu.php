<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Select_menu {

	public function __construct() {
		if ( ! shortcode_exists( 'select_menu' ) ) {
			add_shortcode( 'select_menu', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = null ) {
		$atts = shortcode_atts( [
			'menu'          => null,
			'custom_select' => true,
		], $atts );

		if ( empty( $atts['menu'] ) ) {
			return;
		}

		$menu = wp_get_nav_menu_object( $atts['menu'] );

		if ( ! $menu ) {
			return;
		}

		$has_custom_select = wp_validate_boolean( $atts['custom_select'] );

		ob_start();

		$menu_items = wp_get_nav_menu_items( $menu->term_id );

		$escaped_menu_id = esc_attr( 'select-menu-' . sanitize_html_class( $menu->term_id ) ); ?>

		<?php if ( $has_custom_select ) {
			if ( is_callable( [ 'TotalTheme\Forms\Select_Wrap', 'open' ] ) ) {
				\TotalTheme\Forms\Select_Wrap::open();
			} else {
				$has_custom_select = false;
			}
		} ?>

		<select id="<?php echo $escaped_menu_id; ?>" class="wpex-select-menu-shortcode" onchange="if (this.value) window.location.href=this.value"><?php

			// Make sure we have menu items
			if ( $menu_items && is_array( $menu_items ) ) {

				foreach ( $menu_items as $menu_item ) : ?>

					<option value="<?php echo esc_url( $menu_item->url ); ?>"><?php echo esc_attr( $menu_item->title ); ?></option>

				<?php endforeach;
			}

		?></select>

		<?php if ( $has_custom_select ) {
			if ( is_callable( [ 'TotalTheme\Forms\Select_Wrap', 'arrow' ] ) ) {
				\TotalTheme\Forms\Select_Wrap::arrow();
			}
			echo '</div>'; // close select wrap.
		} ?>

		<?php return ob_get_clean();
	}

}
