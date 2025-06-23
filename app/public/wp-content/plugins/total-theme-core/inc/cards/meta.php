<?php

namespace TotalThemeCore\Cards;

\defined( 'ABSPATH' ) || exit;

/**
 * Register meta options for theme cards.
 */
final class Meta {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Hook into actions and filters.
	 */
	public static function init(): void {
		if ( \is_admin() ) {
			\add_action( 'admin_init', [ self::class, 'on_admin_init' ] );
		}
	}

	/**
	 * Initialize.
	 */
	public static function on_admin_init(): void {
		if ( self::is_enabled() && \class_exists( '\WPEX_Meta_Factory' ) ) {
			new \WPEX_Meta_Factory( self::card_metabox() );
		}
	}

	/**
	 * Check if enabled.
	 */
	public static function is_enabled(): bool {
		return (bool) \apply_filters( 'wpex_has_card_metabox', true );
	}

	/**
	 * Card metabox settings.
	 */
	protected static function card_metabox(): array {
		return [
			'id'       => 'card',
			'title'    => \esc_html__( 'Card Settings', 'total-theme-core' ),
			'screen'   => (array) \apply_filters( 'wpex_card_metabox_post_types', [ 'post' => 'post' ] ),
			'context'  => 'normal',
			'priority' => 'default',
			'fields'   => [ self::class, 'get_fields' ]
		];
	}

	/**
	 * Return metabox fields.
	 */
	public static function get_fields(): array {
		return (array) \apply_filters( 'wpex_card_metabox_fields', [
			[
				'name' => \esc_html__( 'Link Target', 'total-theme-core' ),
				'id' => 'wpex_card_link_target',
				'type' => 'button_group',
				'choices' => [
					'' => \esc_html__( 'Default', 'total-theme-core' ),
					'_blank' => \esc_html__( 'New Tab', 'total-theme-core' ),
				],
			],
			[
				'name' => \esc_html__( 'Link URL', 'total-theme-core' ),
				'id' => 'wpex_card_url',
				'type' => 'text',
			],
			[
				'name' => \esc_html__( 'Thumbnail', 'total-theme-core' ),
				'id' => 'wpex_card_thumbnail',
				'type' => 'upload',
				'media_type' => 'image',
				'return' => 'id',
				'desc' => \esc_html__( 'Select a custom thumbnail to override the featured image.', 'total-theme-core' ),
			],
			[
			 	'name' => \esc_html__( 'Icon', 'total-theme-core' ),
				'id'   => 'wpex_card_icon',
				'type' => 'icon_select',
				'desc' => \esc_html__( 'Enter your custom Font Icon classname or click the button to select from the available theme icons.', 'total-theme-core' ),
			],
		] );
	}

	/**
	 * Icon choices.
	 */
	protected static function choices_icons(): void {
		\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
	}

}
