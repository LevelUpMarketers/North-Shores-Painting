<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * WP Nav Custom Fields: Hooks into "after_switch_theme".
 */
final class WP_Nav_Custom_Fields {

	/**
	 * Nonce Action.
	 */
	private const ADMIN_NONCE_ACTION = 'totaltheme_menu_items_meta';

	/**
	 * Nonce Name.
	 */
	private const ADMIN_NONCE_NAME = 'totaltheme_menu_items_meta_nonce';


	/**
	 * Static-only class.
	 */
	private function __construct() {}

    /**
	 * Init.
	 */
	public static function init(): void {
		if ( \is_admin() ) {
       		\add_action( 'wp_nav_menu_item_custom_fields', [ self::class, '_on_wp_nav_menu_item_custom_fields' ], 10, 4 );
			\add_action( 'wp_update_nav_menu_item', [ self::class, '_on_wp_update_nav_menu_item' ], 10, 2 );
			\add_filter( 'manage_nav-menus_columns', [ self::class, '_filter_manage_nav_menus_columns' ], 20 );
			\add_action( 'admin_head', [ self::class, '_add_help_tab' ] );
		}

		// We hook here so we can target all menus and not just the main menu.
		\add_filter( 'nav_menu_item_title', [ self::class, '_filter_nav_menu_item_title' ], 10, 4 );
    }

	/**
	 * Add Help tab to the menus panel.
	 */
	public static function _add_help_tab() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'nav-menus' === $screen->id ) {
			// Add help tab
			$screen->add_help_tab(
				[
					'id'      => 'totaltheme_megamenus',
					'title'   => \esc_html__( 'Mega Menus', 'total' ),
					'content' => '<p>' . \esc_html__( 'Follow these steps to create mega menus for the theme\'s main header menu or Horizontal Menu element.', 'total' ) . '</p><ul><li>' . \wp_kses( \__( 'Edit any top level menu item and use the <strong>Mega Menu Columns</strong> field to select how many columns you want for your mega menu.', 'total' ), [ 'strong' => [] ] ) . '</li><li>' . \wp_kses( \__( 'Under the parent menu item where you selected the columns add <strong>2nd-level items</strong>. Each 2nd level item will create a new column and it\'s text will be used as the <strong>column heading</strong>.', 'total' ), [ 'strong' => [] ] ) . '</li><li>' . \wp_kses( \__( 'Last, add <strong>3rd level items</strong>. These will display inside the columns under the 2nd level heading.', 'total' ), [ 'strong' => [] ] ) . '</li></ul><p>' .\wp_kses( \__( 'If you wish to make <strong>a column full-width</strong> add the classname "megamenu-col-full" to the 2nd level menu item\'s CSS classes field.', 'total' ), [ 'strong' => [] ] ) . '</p>',
				]
			);
		}
	}

	/**
	 * Hooks into wp_nav_menu_item_custom_fields.
	 */
	public static function _on_wp_nav_menu_item_custom_fields( $item_id, $item, $depth, $args ): void {
		\wp_nonce_field( self::ADMIN_NONCE_ACTION, self::ADMIN_NONCE_NAME );

		?>

		<?php if ( $depth === 0 ) { ?>
			<div class="description-wide field-totaltheme-mega_cols totaltheme-mega_cols" style="margin: 2px 0 5px;">
				<label for="menu-item-totaltheme-mega_cols-<?php echo \esc_attr( $item_id ); ?>"><?php \esc_html_e( 'Mega Menu Columns', 'total' ); ?></label>
				<select id="<?php echo \esc_attr( "menu_item_totaltheme_mega_cols[{$item_id}]" ); ?>" name="<?php echo \esc_attr( "menu_item_totaltheme_mega_cols[{$item_id}]" ); ?>">
					<?php $selected = (int) \get_post_meta( $item_id, '_menu_item_totaltheme_mega_cols', true ); ?>
					<option value=""><?php esc_html_e( '— Select —', 'total' ); ?></option>
					<option value="1" <?php \selected( $selected, 1 ); ?>>1</option>
					<option value="2" <?php \selected( $selected, 2 ); ?>>2</option>
					<option value="3" <?php \selected( $selected, 3 ); ?>>3</option>
					<option value="4" <?php \selected( $selected, 4 ); ?>>4</option>
					<option value="5" <?php \selected( $selected, 5 ); ?>>5</option>
					<option value="6" <?php \selected( $selected, 6 ); ?>>6</option>
				</select>
			</div>
		<?php } ?>

		<div class="description-wide field-totaltheme-icon totaltheme-icon" style="margin: 2px 0 5px;">
			<label for="menu-item-totaltheme-icon-<?php echo \esc_attr( $item_id ); ?>"><?php \esc_html_e( 'Menu Icon', 'total' ); ?></label>
			<br />
			<?php \totaltheme_call_static(
				'Helpers\Icon_Select',
				'render_form',
				[
					'selected'   => \get_post_meta( $item_id, '_menu_item_totaltheme_icon', true ),
					'input_name' => "menu_item_totaltheme_icon[{$item_id}]",
					'input_id'   => "menu-item-totaltheme-icon-{$item_id}"
				]
			); ?>
		</div>

		<?php
	}

	/**
	 * Hooks into wp_update_nav_menu_item.
	 */
	public static function _on_wp_update_nav_menu_item( $menu_id, $item_id ) {
		if ( ! isset( $_POST[ self::ADMIN_NONCE_NAME ] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST[ self::ADMIN_NONCE_NAME ] ) ), self::ADMIN_NONCE_ACTION )
		) {
			return $item_id;
		}

		$settings = [ 'mega_cols', 'icon' ];

		foreach ( $settings as $setting ) {
			if ( isset( $_POST[ "menu_item_totaltheme_{$setting}" ] ) ) {
				$value = $_POST[ "menu_item_totaltheme_{$setting}" ][ $item_id ] ?? null;
				if ( $value ) {
					\update_post_meta( $item_id, "_menu_item_totaltheme_{$setting}", \sanitize_text_field( \wp_unslash( $value ) ) );
				} else {
					\delete_post_meta( $item_id, "_menu_item_totaltheme_{$setting}" );
				}
			}
		}
	}

	/**
	 * Filters the _filter_manage_nav_menus_columns values.
	 */
	public static function _filter_manage_nav_menus_columns( array $columns ): array {
		$columns['totaltheme-icon'] = \esc_html__( 'Menu Icon', 'total' );
		return $columns;
	}

	/**
	 * Filters the _filter_nav_menu_item_title to insert icons into ANY menu.
	 */
	public static function _filter_nav_menu_item_title( $title, $item, $args, $depth ) {
		$item_id = $item->ID ?? 0;
		if ( $item_id && $icon = \get_post_meta( $item_id, '_menu_item_totaltheme_icon', true ) ) {
			$icon_class = 'menu-item-icon';
			if ( isset( $args->slug ) && 'main' === $args->slug && (int) $depth > 0 || 'six' === \totaltheme_call_static( 'Header\Core', 'style' ) ) {
				$icon_class .= ' wpex-icon--w';
			}
			$title = \totaltheme_get_icon( \sanitize_text_field( $icon ), $icon_class ) . $title;
		}
		return $title;
	}

}
