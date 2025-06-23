<?php

namespace TotalThemeCore\Meta;

defined( 'ABSPATH' ) || exit;

/**
 * Adds new fields for the media items.
 */
final class Attachment_Settings {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Hook into actions and filters.
	 */
	public static function init() {
		add_filter( 'attachment_fields_to_edit', [ self::class, 'edit_fields' ], null, 2 );
		add_filter( 'attachment_fields_to_save', [ self::class, 'save_fields' ], null , 2 );
	}

	/**
	 * Adds new edit attachment fields.
	 */
	public static function edit_fields( $form_fields, $post ) {
		$form_fields['wpex_video_url'] = [
			'label'	=> esc_html__( 'Video URL', 'total-theme-core' ),
			'input'	=> 'text',
			'value'	=> get_post_meta( $post->ID, '_video_url', true ),
		];
	   return $form_fields;
	}

	/**
	 * Save new attachment fields.
	 */
	public static function save_fields( $post, $attachment ) {
		if ( isset( $attachment['wpex_video_url'] ) ) {
			update_post_meta( $post['ID'], '_video_url', sanitize_text_field( $attachment['wpex_video_url'] ) );
		}
		return $post;
	}

}
