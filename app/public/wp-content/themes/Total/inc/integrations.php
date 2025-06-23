<?php declare(strict_types=1);

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * 3rd Party Integrations.
 */
class Integrations {

	/**
	 * Store list of active integrations.
	 */
	protected static $active_integrations = [];


	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns array of integrations.
	 */
	private static function get_integrations(): array {
		return [
			'gutenberg' => [
				'class'     => 'Gutenberg',
				'condition' => true,
			],
			'wpbakery' => [
				'class'     => 'WPBakery',
				'condition' => \WPEX_VC_ACTIVE,
			],
			'templatera' => [
				'class'     => 'Templatera',
				'condition' => \class_exists( '\VcTemplateManager', false ),
			],
			'woocommerce' => [
				'class'     => 'WooCommerce',
				'condition' => \class_exists( '\WooCommerce', false ),
			],
			'elementor' => [
				'class'     => 'Elementor',
				'condition' => \did_action( 'elementor/loaded' ),
			],
			'post_types_unlimited' => [
				'class'     => 'Post_Types_Unlimited',
				'condition' => class_exists( 'Post_Types_Unlimited', false ),
			],
			'just_events' => [
				'class'     => 'Just_Events',
				'condition' => \class_exists( '\Just_Events\Plugin', false ),
			],
			'yoastseo' => [
				'class'     => 'Yoast_SEO',
				'condition' => \defined( '\WPSEO_VERSION' ),
			],
			'tribe_events' => [
				'class'     => 'Tribe_Events',
				'condition' => \class_exists( '\Tribe__Events__Main', false ),
			],
			'w3_total_cache' => [
				'class'     => 'W3_Total_cache',
				'condition' => \defined( '\W3TC' ),
			],
			'wpml' => [
				'class'     => 'WPML',
				'condition' => \WPEX_WPML_ACTIVE,
			],
			'polylang' => [
				'class'     => 'Polylang',
				'condition' => \WPEX_POLYLANG_ACTIVE,
			],
			'bbpress' => [
				'class'     => 'bbPress',
				'condition' => \class_exists( '\bbPress', false ),
			],
			'buddypress' => [
				'class'     => 'BuddyPress',
				'condition' => \function_exists( '\buddypress' ),
			],
			'contactform7' => [
				'class'     => 'Contact_Form_7',
				'condition' => \defined( '\WPCF7_VERSION' ),
			],
			'gravityforms' => [
				'class'     => 'Gravity_Forms',
				'condition' => \class_exists( '\RGForms', false ),
			],
			'jetpack' => [
				'class'     => 'Jetpack',
				'condition' => \class_exists( '\Jetpack', false ),
			],
			'learndash' => [
				'class'     => 'Learn_Dash',
				'condition' => \defined( '\LEARNDASH_VERSION' ),
			],
			'sensei' => [
				'class'     => 'Sensei',
				'condition' => \function_exists( 'Sensei' ),
			],
			'lifterlms'  => [
				'class'     => 'LifterLMS',
				'condition' => \function_exists( 'llms' ),
			],
			'cptui' => [
				'class'     => 'Custom_Post_Type_UI',
				'condition' => \function_exists( '\cptui_init' ),
			],
			'massive_addons' => [
				'class'     => 'Massive_Addons_For_WPBakery',
				'condition' => \defined( '\MPC_MASSIVE_VERSION' ),
			],
			'tablepress' => [
				'class'     => 'TablePress',
				'condition' => \class_exists( '\TablePress', false ),
			],
			'revslider' => [
				'class'     => 'Revslider',
				'condition' => \class_exists( '\RevSlider', false ),
			],
			'relevanssi' => [
				'class'     => 'Relevanssi',
				'condition' => \function_exists( '\relevanssi_init' ),
			],
			'easy_notification_bar' => [
				'class'     => 'Easy_Notification_Bar',
				'condition' => \class_exists( '\Easy_Notification_Bar', false ),
			],
			'wpcode' => [
				'class'     => 'WPCode',
				'condition' =>  \class_exists( '\WPCode', false ),
			],
		];
	}

	/**
	 * Init.
	 */
	public static function init(): void {
		foreach ( self::get_integrations() as $vendor => $args ) {
			if ( true === (bool) $args['condition'] && self::is_integration_enabled( $vendor ) ) {
				totaltheme_init_class( 'Integration\\' . $args['class'] );
				self::$active_integrations[] = $vendor;
			}
		}
	}

	/**
	 * Check if a current integration is enabled.
	 */
	public static function is_integration_enabled( string $integration ): bool {
		return (bool) \apply_filters( "totaltheme/integration/{$integration}/is_enabled", true );
	}

	/**
	 * Check if a current integration is active.
	 */
	public static function is_integration_active( string $integration ): bool {
		return \in_array( $integration, self::$active_integrations );
	}

}
