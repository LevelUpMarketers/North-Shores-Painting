<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Recommended Plugins.
 */
final class Recommended_Plugins {

	/**
	 * Excluded plugins.
	 */
	protected static $excluded_plugins = [];

	/**
	 * Init.
	 */
	public static function init() {
		if ( ! \get_theme_mod( 'recommend_plugins_enable', true ) ) {
			return;
		}

		$user_excluded_plugins = \get_theme_mod( 'excluded_plugins' );

		if ( \is_array( $user_excluded_plugins ) ) {
			self::$excluded_plugins = $user_excluded_plugins;
		}

		self::tgmpa_init();

		\add_filter( 'update_bulk_theme_complete_actions', [ self::class, '_add_theme_update_complete_actions' ] );
		\add_filter( 'update_theme_complete_actions', [ self::class, '_add_theme_update_complete_actions' ] );
		\add_filter( 'install_theme_complete_actions', [ self::class, '_add_theme_update_complete_actions' ] );
		\add_filter( 'tgmpa_table_data_item', [ self::class, '_filter_tgmpa_table_data_item' ] , 10, 2 );
		\add_filter( 'tgmpa_table_columns', [ self::class, '_filter_tgmpa_table_columns' ] );

		if ( empty( $_GET['tgmpa-update'] ) && empty( $_GET['tgmpa-install'] ) && ( ! self::$excluded_plugins
			|| ( ! \in_array( 'revslider', self::$excluded_plugins ) && ! \class_exists( '\RevSlider', false ) )
			|| ( ! \in_array( 'templatera', self::$excluded_plugins ) && ! \class_exists( '\VcTemplateManager', false ) )
		) ) {
			\add_action( 'admin_notices', [ self::class, '_on_admin_notices' ] );
		}
	}

	/**
	 * Returns list of recommended plugins.
	 */
	public static function get_list(): array {
		$plugins = [];

		// Required Plugins.
		$plugins['total-theme-core'] = [
			'name'             => 'Total Theme Core',
			'slug'             => 'total-theme-core',
			'version'          => \WPEX_THEME_CORE_PLUGIN_SUPPORTED_VERSION,
			'source'           => 'https://totalwptheme.s3.us-east-1.amazonaws.com/plugins/total-theme-core/version-2-3/total-theme-core.zip',
			'required'         => true,
			'force_activation' => false,
		];

		if ( ! \in_array( 'js_composer', self::$excluded_plugins, true ) || \WPEX_VC_ACTIVE ) {
			$plugins['js_composer'] = [
				'name'             => 'WPBakery Page Builder',
				'slug'             => 'js_composer',
				'version'          => \WPEX_VC_SUPPORTED_VERSION,
				'source'           => 'https://totalwptheme.s3.us-east-1.amazonaws.com/plugins/wpbakery/version-8-4-1/js_composer.zip',
				'required'         => false,
				'force_activation' => false,
			];
		}

		if ( ! \in_array( 'templatera', self::$excluded_plugins, true ) || \class_exists( '\VcTemplateManager', false ) ) {
			$plugins['templatera'] = [
				'name'             => 'Templatera',
				'slug'             => 'templatera',
				'source'           => 'https://totalwptheme.s3.us-east-1.amazonaws.com/plugins/templatera/version-2-3-0/templatera.zip',
				'version'          => '2.3.0',
				'required'         => false,
				'force_activation' => false,
			];
		}

		if ( ! \in_array( 'revslider', self::$excluded_plugins, true ) || \class_exists( '\RevSlider', false ) ) {
			$plugins['revslider'] = [
				'name'             => 'Slider Revolution',
				'slug'             => 'revslider',
				'version'          => '6.7.32',
				'source'           => 'https://totalwptheme.s3.us-east-1.amazonaws.com/plugins/revslider.zip',
				'required'         => false,
				'force_activation' => false,
			];
		}

		return (array) \apply_filters( 'wpex_recommended_plugins', $plugins );
	}

	/**
	 * Adds custom links to the theme update complete actions.
	 */
	public static function _add_theme_update_complete_actions( $actions ) {
		if ( \is_multisite() && \is_network_admin() ) {
			return $actions; // tgmpa not here.
		}
		if ( \is_array( $actions )
			&& \defined( '\TTC_VERSION' )
			&& \defined( '\WPEX_THEME_CORE_PLUGIN_SUPPORTED_VERSION' )
			&& \version_compare( TTC_VERSION, WPEX_THEME_CORE_PLUGIN_SUPPORTED_VERSION, '<' )
		) {
			\array_unshift( $actions, \sprintf(
				'<a href="%s" target="_parent" class="button button-primary">%s</a>',
				\self_admin_url( 'themes.php?page=install-required-plugins&plugin_status=update' ),
				\esc_html__( 'Update theme plugins', 'total' )
			) );
		}
		return $actions;
	}

	/**
	 * TGMPA init.
	 */
	public static function tgmpa_init() {
		if ( ! \class_exists( 'TGM_Plugin_Activation' ) ) {
			require_once \WPEX_INC_DIR . 'lib/tgmpa/class-tgm-plugin-activation.php';
		}
		\add_action( 'tgmpa_register', [ self::class, 'tmpa_register' ] );
	}

	/**
	 * TGMPA Register.
	 */
	public static function tmpa_register() {
		$plugins = self::get_list();

		$dismissable = true;

		if ( \WPEX_VC_ACTIVE ) {
			if ( \totaltheme_call_static( 'Integration\WPBakery\Helpers', 'is_theme_mode_enabled' ) ) {
				$dismissable = \totaltheme_call_static( 'Integration\WPBakery\Helpers', 'is_version_supported' );
			} else {
				unset( $plugins['js_composer'] );
			}
		}

		\tgmpa( $plugins, [
			'id'           => 'totaltheme',
			'domain'       => 'total',
			'menu'         => 'install-required-plugins',
			'has_notices'  => true,
			'is_automatic' => true, // auto activation on installation/updating.
			'dismissable'  => $dismissable,
		] );
	}

	/**
	 * Filters tgmpa_table_columns.
	 */
	public static function _filter_tgmpa_table_columns( $columns ) {
		if ( isset( $columns['type'] ) ) {
			unset( $columns['type'] );
		}
		return $columns;
	}

	/**
	 * Filters tgmpa_table_data_item.
	 */
	public static function _filter_tgmpa_table_data_item( $data, $plugin ) {
		if ( isset( $data['slug'] ) ) {
			if ( \in_array( $data['slug'], [ 'revslider', 'js_composer', 'templatera' ], true ) ) {
				$data['type'] = \esc_html__( 'Optional', 'total' );
				$data['source'] = \esc_html__( 'Bundled', 'total' );
			}
			$allowed_html = [
				'a' => [
					'href'   => [],
					'rel'    => [],
					'target' => [],
				],
			];
			switch ( $data['slug'] ) {
				case 'revslider':
					$data['plugin'] = '<strong>Slider Revolution</strong><br><small><span style="background:#C62828;color:#FFF;display:inline-block;border-radius:4px;margin-inline-end:5px;line-height:1.5;padding-inline:5px;vertical-align:baseline;">' . \esc_html__( 'Important', 'total' ) . '</span>' . \wp_kses( \sprintf( \__( 'Sliders are generally not recommended. Install this plugin only if absolutely necessary. We include it because it has always been bundled, but we believe the <a href="%s" target="_blank" rel="noopener noreferrer">free Depicter ↗</a> plugin (unaffiliated) might be a better option.', 'total' ), 'https://wordpress.org/plugins/depicter/' ), $allowed_html ) . '</small>';
					break;
				case 'templatera':
					$data['plugin'] = '<strong>Templatera</strong><br><small>' . \sprintf(
						\esc_html__( 'This plugin allows you to create reusable WPBakery templates that can be quickly inserted into your site. However, if you need to assign the same template to multiple sections across your site, it\'s better to use %sDynamic Templates%s for greater global control.', 'total' ),
						'<a href="' . \esc_url( \admin_url( 'edit.php?post_type=wpex_templates' ) ) . '">',
						'</a>'
					) . '</small>';
					break;
				case 'js_composer':
					$data['plugin'] = '<strong>WPBakery Page Builder</strong><br><small>' . \esc_html__( 'This is a bundled premium plugin for creating complex pages and templates. If you prefer using Elementor, Total is fully compatible with it—just keep this plugin disabled and activate Elementor instead.', 'total' );
					break;
				case 'total-theme-core':
					$data['source'] = \esc_html__( 'Bundled', 'total' );
					break;
			}
		}
		return $data;
	}

	/**
	 * Hooks into admin_notices.
	 */
	public static function _on_admin_notices() {
		if ( empty( $_GET['page'] ) || 'install-required-plugins' !== $_GET['page'] || ! \defined( 'WPEX_THEME_PANEL_SLUG' ) ) {
			return;
		}
		?>
		<div class="notice notice-info">
            <p><?php \printf(
				esc_html__( 'This theme includes the optional Slider Revolution, WPBakery Page Builder, and Templatera plugins.%1$sIf you prefer not to use them, you can disable them entirely in the %2$sTheme Panel%3$s under the Bundled Plugins section, so you won\'t receive prompts to install or update them.', 'total' ),
				'<br>',
				'<a href="' . \esc_url( \admin_url( 'admin.php?page=' . WPEX_THEME_PANEL_SLUG ) ) . '">',
				'</a>',
			); ?></p>
        </div>
		<?php
	}

}
