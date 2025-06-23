<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Integration.
 */
final class WPBakery {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( \is_admin() ) {
			\add_action( 'wp_ajax_vcex_wpb_backend_view', [ self::class, '_backend_view_ajax_callback' ] );
			\add_action( 'vc_page_settings_build', [ self::class, '_enqueue_icons_css'] );
			\add_action( 'vc_backend_editor_enqueue_js_css', [ self::class, '_enqueue_icons_css' ] );
			\add_action( 'vc_frontend_editor_enqueue_js_css', [ self::class, '_enqueue_icons_css' ] );

			if ( ! \totalthemecore_call_static( 'WPBakery\Helpers', 'is_slim_mode_enabled' ) ) {
				\add_action( 'vc_before_init_backend_editor', [ self::class, '_modify_shortcode_weights' ] );
				\add_action( 'vc_before_init_frontend_editor', [ self::class, '_modify_shortcode_weights' ] );
			}
		}

		if ( \totalthemecore_call_static( 'WPBakery\Helpers', 'is_frontend_edit_mode' ) ) {
			\add_action( 'vc_load_iframe_jscss', [ self::class, '_editor_iframe_scripts' ], 1000 );
		}
	}

	/**
	 * Modify wpbakery shortcode weights to move them to the bottom.
	 */
	public static function _modify_shortcode_weights() {
		$shortcodes = [
			'vc_empty_space',
			'vc_single_image',
			'vc_icon',
			'vc_video',
			'vc_gmaps',
			'vc_raw_html',
			'vc_raw_js',
			'vc_pie',
			'vc_round_chart',
			'vc_line_chart',
			'vc_tta_tabs',
			'vc_tta_tour',
			'vc_tta_toggle',
			'vc_tta_pageable',
			'vc_tta_accordion',
			'vc_separator',
			'vc_custom_heading',
			'vc_zigzag',
			'vc_text_separator',
			'vc_message',
			'vc_facebook',
			'vc_tweetmeme',
			'vc_googleplus',
			'vc_pinterest',
			'vc_toggle',
			'vc_btn',
			'vc_cta',
			'vc_flickr',
			'vc_progress_bar',
			'vc_basic_grid',
			'vc_media_grid',
			'vc_masonry_grid',
			'vc_masonry_media_grid',
			'vc_hoverbox',
			'vc_pricing_table',
			'vc_widget_sidebar',
		];

		if ( \class_exists( 'Disable_Elements_For_WPBakery_Page_Builder' ) ) {
			$disabled_elements = \get_option( 'wpex_wpb_disabled_elements' );
			if ( \is_array( $disabled_elements ) ) {
				$shortcodes = \array_diff( $shortcodes, $disabled_elements );
			}
		}
		foreach ( $shortcodes as $shortcode ) {
			\vc_map_update( $shortcode, [
				'weight' => '-1',
			] );
		}
	}

	/**
	 * Backend view ajax callback.
	 */
	public static function _backend_view_ajax_callback(): void {
		if ( \function_exists( 'vc_user_access' ) ) {
			\vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();
		} else {
			\wp_die();
		}

		$view = (string) \vc_post_param( 'view' );

		if ( $view ) {
			$method = "_backend_view_{$view}";
			if ( \method_exists( self::class, $method ) ) {
				self::$method();
			}
		}
		
		\wp_die();
	}

	/**
	 * Enqueue icons CSS.
	 */
	public static function _enqueue_icons_css(): void {
		\wp_enqueue_style(
			'totalthemecore-admin-wpbakery-icons',
			\totalthemecore_get_css_file( 'admin/wpbakery/icons' ),
			[],
			TTC_VERSION
		);
	}

	/**
	 * Editor Scripts.
	 */
	public static function _editor_iframe_scripts(): void {
		self::_enqueue_icons_css();

		\wp_enqueue_script(
			'totalthemecore-vcex-vc_reload',
			\totalthemecore_get_js_file( 'admin/wpbakery/vc_reload' ),
			[ 'jquery' ],
			TTC_VERSION,
			true
		);
	}

	/**
	 * Backend view > acf_repeater.
	 */
	public static function _backend_view_acf_repeater() {
		$template_id = (int) \vc_post_param( 'template_id' );
		if ( $template_id
			&& \current_user_can( 'edit_post', $template_id )
			&& $post = \get_post( $template_id )
		) {
			$links = [];
			if ( \function_exists( 'get_field_object' ) && $field = \vc_post_param( 'field' ) ) {
				$field_object = \get_field_object( $field );
				if ( $field_object && ! empty( $field_object['parent'] ) && $field_edit_link = get_edit_post_link( $field_object['parent'] ) ) {
					$links[] = '<a href="' . esc_url( $field_edit_link ) . '" target="_blank">' . \esc_html__( 'Edit field', 'total-theme-core' ) . ' &nearr;</a>';
				}
			}
			if ( $link = \get_edit_post_link( $post ) ) {
				$links[] = '<a href="' . \esc_url( $link ) . '" target="_blank">' . \esc_html__( 'Edit template', 'total-theme-core' ) . ' &nearr;</a>';
			}
			$data = [
				'name'  => \esc_html( $post->post_title ?? '' ),
				'links' => \implode( ' | ', $links ),
			];
		}
		\wp_send_json( $data ?? [] );
	}

	/**
	 * Backend view > menu.
	 */
	public static function _backend_view_menu() {
		$menu_id = (int) \vc_post_param( 'menu_id' );
		if ( ! $menu_id ) {
			$menu_id = \get_nav_menu_locations()['main_menu'] ?? 0;
		}
		if ( $menu_id && $menu = \wp_get_nav_menu_object( $menu_id ) ) {
			$data = [
				'menu' => \esc_html( $menu->name ),
				'link' => ( \current_user_can( 'edit_term', $menu_id, 'nav_menu' ) ) ? '<a href="' . \esc_url( \admin_url( "nav-menus.php?menu={$menu_id}" ) ) . '" target="_blank">' . \esc_html__( 'Edit this Menu', 'total-theme-core' ) . ' &nearr;</a>' : '',
			];
		}
		\wp_send_json( $data ?? [] );
	}

	/**
	 * Backend view > custom_field.
	 */
	public static function _backend_view_custom_field() {
		$field_name = \vc_post_param( 'field_name' );
		if ( $field_name ) {
			if ( \str_starts_with( $field_name, 'field_' ) ) {
				if ( \function_exists( 'get_field_object' ) ) {
					$field_obj = \get_field_object( $field_name );
					if ( \is_array( $field_obj ) && ! empty( $field_obj['label'] ) ) {
						$field_name = $field_obj['label'];
					}
				}
			} else {
				$theme_fields = (array) \totalthemecore_call_static( 'WPBakery\Params\Custom_Field', 'get_theme_choices_all' );
				if ( $theme_fields ) {
					$field_name = \array_merge( ...\array_column( $theme_fields, 'options' ) )[ $field_name ] ?? $field_name;
				}
			}
			echo \sanitize_text_field( $field_name );
		}
	}

	/**
	 * Backend view > icon.
	 */
	public static function _backend_view_icon() {
		$icon = (string) \vc_post_param( 'icon' );
		if ( $icon ) {
			if ( 'ticons' === vcex_get_icon_type_from_class( $icon ) ) {
				echo \vcex_get_theme_icon_html( str_replace( 'fa fa-', '', $icon ) );
			} else {
				echo '<span class="' . esc_attr( $icon ) . '" aria-hidden="true"></span>';
			}
		}
	}

	/**
	 * Backend view > image.
	 */
	public static function _backend_view_image() {
		$post_id = (int) \vc_post_param( 'post_id' );
		$source  = \vc_post_param( 'image_source', 'media_library' );
		$img     = '';

		switch ( $source ) {
			case 'media_library':
			case 'featured':
				if ( 'featured' === $source ) {
					if ( $post_id && \has_post_thumbnail( $post_id ) && 'templatera' !== \get_post_type( $post_id ) ) {
						$img = \wp_get_attachment_image_url( \get_post_thumbnail_id( $post_id ), 'thumbnail' );
					}
				}
				break;
			case 'author_avatar';
				$img = \get_avatar_url( \get_post_field( 'post_author', $post_id ), [
					'size' => get_option( 'thumbnail_size_w' )
				] );
				break;
			case 'user_avatar';
				$img = \get_avatar_url( \wp_get_current_user(), [
					'size' => \get_option( 'thumbnail_size_w' )
				] );
				break;
			case 'external';
				if ( $external_image = \vc_post_param( 'external_image' ) ) {
					$img = $external_image;
				}
				break;
			case 'custom_field';
				if ( $cf_key = \vc_post_param( 'custom_field_image' ) ) {
					$img = \vcex_get_meta_value_attachment_id( $cf_key, $post_id );
					if ( \is_numeric( $img ) ) {
						$img = \wp_get_attachment_image_url( $img, 'thumbnail' );
					}
				}
				break;
			case 'secondary_thumbnail':
				$secondary_thumbnail = \get_post_meta( $post_id, 'wpex_secondary_thumbnail', true );
				if ( $secondary_thumbnail ) {
					if ( \is_numeric( $secondary_thumbnail ) ) {
						$img = \wp_get_attachment_image_url( $secondary_thumbnail, 'thumbnail' );
					} elseif ( \is_string( $secondary_thumbnail ) ) {
						$img = $secondary_thumbnail;
					}
				}
				break;
		}
		if ( ! $img && $image_id = \vc_post_param( 'image_id' ) ) {
			$img_id = \preg_replace( '/[^\d]/', '', \intval( $image_id ) );
			if ( $img_id ) {
				$img = \wp_get_attachment_image_url( $img_id, 'thumbnail' );
			}
		}
		if ( $img ) {
			echo \esc_url( $img );
		}
	}

	/**
	 * Backend view > image_before_after.
	 */
	public static function _backend_view_image_before_after() {
		$images = [];
		$post_id = (int) \vc_post_param( 'post_id' );
		$source = \vc_post_param( 'source' );
		$before_image = $after_image = '';

		switch ( $source ) {
			case 'featured':
				$before_image = \get_post_thumbnail_id( $post_id );
				if ( \function_exists( 'totaltheme_get_post_secondary_thumbnail_id' ) ) {
					$after_image = \totaltheme_get_post_secondary_thumbnail_id( $post_id );
				}
				break;
			case 'custom_field':
				$before_image = \vcex_get_meta_value_attachment_id( \vc_post_param( 'beforeImageCf' ), $post_id );
				$after_image  = \vcex_get_meta_value_attachment_id( \vc_post_param( 'afterImageCf' ), $post_id );
				break;
			case 'media_library';
			default:
				$before_image = \vc_post_param( 'beforeImage' );
				$after_image  = \vc_post_param( 'afterImage' );
				break;
		}

		if ( $before_image ) {
			$before_image = \esc_url( \wp_get_attachment_image_url( $before_image, 'thumbnail' ) );
			if ( $before_image ) {
				$images[] = $before_image;
			}
		}

		if ( $after_image ) {
			$after_image = \esc_url( \wp_get_attachment_image_url( $after_image, 'thumbnail' ) );
			if ( $after_image ) {
				$images[] = $after_image;
			}
		}

		\wp_send_json( $images );
	}

	/**
	 * Backend view > image_gallery.
	 */
	public static function _backend_view_image_gallery() {
		$post_id      = (int) \vc_post_param( 'post_id' );
		$image_ids    = \vc_post_param( 'image_ids' );
		$post_gallery = \vc_post_param( 'post_gallery' );
		$custom_field = \vc_post_param( 'custom_field' );
		$return       = [];
		$images       = '';

		if ( $image_ids ) {
			$images = $image_ids;
		}

		if ( $custom_field && $post_id ) {
			if ( \function_exists( 'get_field_object' ) && \str_starts_with( $custom_field, 'field_' ) ) {
				$field_obj = \get_field_object( $custom_field );
				if ( ! empty( $field_obj['type'] )
					&& 'gallery' === $field_obj['type']
					&& ! empty( $field_obj['name'] )
				) {
					$custom_field = $field_obj['name'];
				}
			}
			$custom_images = \get_post_meta( $post_id, $custom_field, true );
			if ( $custom_images ) {
				$images = $custom_images;
			}
		}

		if ( 'true' === $post_gallery && $post_id && \function_exists( 'wpex_get_gallery_ids' ) ) {
			$gallery_images = \wpex_get_gallery_ids( $post_id );
			if ( $gallery_images ) {
				$images = $gallery_images;
			}
		}

		if ( $images ) {
			if ( \is_string( $images ) ) {
				$images = \explode( ',', $images );
			}
			if ( \is_array( $images ) ) {
				$images = array_slice( $images, 0, 100 ); // show 100 images max.
				foreach ( $images as $image_id ) {
					$return[] = \esc_url( \wp_get_attachment_image_url( $image_id, 'thumbnail' ) );
				}
			}
		}
		\wp_send_json( $return );
	}

}
