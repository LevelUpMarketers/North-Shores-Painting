<?php

namespace TotalThemeCore\Meta;

\defined( 'ABSPATH' ) || exit;

/**
 * Main Metabox.
 */
class Main_Metabox {

	/**
	 * Static Class.
	 */
	public function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		if ( \is_admin() ) {
			\add_action( 'current_screen', [ self::class, 'register_metabox' ] );
		}
	}

	/**
	 * Register metabox.
	 */
	public static function register_metabox() {
		if ( ! \class_exists( '\WPEX_Meta_Factory', true ) ) {
			return;
		}

		$screen = self::get_post_types();
		$current_screen = \get_current_screen();
		
		if ( empty( $current_screen->id ) || ! in_array( $current_screen->id, $screen, true ) ) {
			return; // bail if the current admin screen is not a post type that needs the metabox.
		}

		$post_type = $current_screen->post_type ?? '';
		$groups = self::get_field_groups( $post_type );

		if ( ! $groups ) {
			return;
		}
		
		$fields     = [];
		$tabs       = [];
		$active_tab = '';

		// Converts old meta fields to work with the new WPEX_Meta_Factory array.
		foreach ( $groups as $group_id => $group ) {
			if ( empty( $group['settings'] ) || ! \is_array( $group['settings'] ) ) {
				continue;
			}
			$group_post_type = isset( $group['post_type'] ) ? (array) $group['post_type'] : [];
			if ( $group_post_type ) {
				if ( ! \in_array( $post_type, $group_post_type, true ) ) {
					continue;
				} else {
					$active_tab = $group_id;
				}
			}
			if ( ! isset( $tabs[ $group_id ] ) ) {
				$tabs[ $group_id ] = $group['title'] ?? $group_id;
			}
			foreach ( $group['settings'] as $setting_id => $setting ) {
				$setting['tab'] = $group_id;
				$setting['name'] = $setting['title'];
				
				if ( ! isset( $setting['id'] ) ) {
					$setting['id'] = $setting_id;
				}

				if ( isset( $setting['options_callback'] ) ) {
					$setting['choices'] = $setting['options_callback'];
					unset( $setting['options_callback'] );
				}

				if ( isset( $setting['options'] ) ) {
					$setting['choices'] = $setting['options'];
				}

				switch ( $setting['type'] ) {
					case 'media':
					case 'image':
						$setting['type']       = 'upload';
						$setting['return']     = $setting['return'] ?? 'id';
						$setting['preview']    = $setting['preview'] ?? true;
						$setting['media_type'] = $setting['media_type'] ?? 'image';
						break;
					case 'link':
						$setting['type'] = 'url';
						break;
					case 'color':
						$setting['alpha'] = true;
						break;
					case 'editor':
						$setting['type'] = 'wp_editor';
						break;
					case 'code':
					case 'text_html':
						$setting['type'] = 'html';
						$setting['rows'] = 1;
						break;
					case 'code':
						$setting['type'] = 'html';
						break;
				}

				unset( $setting['title'] );

				$fields[] = $setting;
			}
		}

		$metabox_title = \esc_html__( 'Theme Settings', 'total-theme-core' );

		if ( 1 === \count( $tabs ) ) {
			$post_type_obj = \get_post_type_object( $post_type );
			$label = $post_type_obj->labels->singular_name ?? '';
			$metabox_title = \sprintf( \esc_html__( '%s Settings', 'total-theme-core' ), $label );
		}

		$metabox_args = [
			'id'         => 'general',
			'context'    => 'normal',
			'priority'   => 'high',
			'title'      => $metabox_title,
			'screen'     => $screen,
			'fields'     => $fields,
			'tabs'       => $tabs,
			'active_tab' => $active_tab,
			'scripts'    => [
				[
					'totalthemecore-admin-main-metabox',
					\totalthemecore_get_js_file( 'admin/main-metabox' ),
					[ 'jquery' ],
					\TTC_VERSION,
					true
				],
			]
		];

		new \WPEX_Meta_Factory( $metabox_args );
	}

	/**
	 * Returns metabox post types.
	 */
	private static function get_post_types(): array {
		$post_types = [
			'post'    => 'post',
			'page'    => 'page',
			'product' => 'product',
		];

		if ( \defined( '\TYPES_VERSION' ) ) {
			$toolkit_types = (array) \get_option( 'wpcf-custom-types' );
			if ( $toolkit_types ) {
				foreach ( $toolkit_types as $type => $params ) {
					if ( ! empty( $params['public'] ) ) {
						$post_types[ $type ] = $type;
					}
				}
			}
		}

		$post_types = \apply_filters( 'wpex_main_metaboxes_post_types', $post_types );
		return (array) \apply_filters( 'totalthemecore/meta/main_metabox/post_types', $post_types );
	}

	/**
	 * Settings Array.
	 */
	private static function get_field_groups( $post_type ): array {
		$tabs = [];
		$is_viewable = \is_post_type_viewable( $post_type );

		if ( self::has_core_fields( $post_type ) ) {
			if ( $is_viewable ) {
				$tabs['main']       = self::get_main_fields();
				$tabs['header']     = self::get_header_fields();
				$tabs['title']      = self::get_title_fields();
				$tabs['slider']     = self::get_slider_fields();
				$tabs['background'] = self::get_background_fields();
				$tabs['footer']     = self::get_footer_fields();
				$tabs['callout']    = self::get_callout_fields();
			} else {
				$tabs['main'] = self::get_main_fields_non_singular();
			}
		}

		$tabs['media'] = self::get_media_fields( $post_type );

		return (array) \apply_filters( 'wpex_metabox_array', $tabs, $post_type );
	}

	/**
	 * Returns the main tab fields.
	 */
	private static function get_main_fields(): array {
		$fields = [];

		$fields['post_link'] = [
			'title'       => \esc_html__( 'Redirect', 'total-theme-core' ),
			'id'          => 'wpex_post_link',
			'type'        => 'url',
			'description' => \esc_html__( 'Enter a URL to redirect this post or page.', 'total-theme-core' ),
		];

		$fields['main_layout'] = [
			'title'       => \esc_html__( 'Site Layout', 'total-theme-core' ),
			'type'        => 'select',
			'id'          => 'wpex_main_layout',
			'description' => \esc_html__( 'This option should only be used in very specific cases since there is a global setting available in the Customizer.', 'total-theme-core' ),
			'choices'     => 'wpex_get_site_layouts',
		];

		$fields['post_layout'] = [
			'title'       => \esc_html__( 'Content Layout', 'total-theme-core' ),
			'type'        => 'select',
			'id'          => 'wpex_post_layout',
			'description' => \esc_html__( 'Select your custom layout for this page or post content.', 'total-theme-core' ),
			'choices'     => 'wpex_get_post_layouts',
		];

		$fields['singular_template'] = [
			'title'       => \esc_html__( 'Dynamic Template', 'total-theme-core' ),
			'type'        => 'select_template',
			'id'          => 'wpex_singular_template',
			'description' => \esc_html__( 'Select a dynamic template to override this page.', 'total-theme-core' ),
		];

		$fields['sidebar'] = [
			'title'       => \esc_html__( 'Sidebar', 'total-theme-core' ),
			'type'        => 'select',
			'id'          => 'sidebar',
			'description' => \esc_html__( 'Select your a custom sidebar for this page or post.', 'total-theme-core' ),
			'choices'     => [ self::class, 'get_widget_areas' ],
		];

		$fields['disable_toggle_bar'] = [
			'title'       => \esc_html__( 'Toggle Bar', 'total-theme-core' ),
			'id'          => 'wpex_disable_toggle_bar',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Default', 'total-theme-core' ),
				'enable' => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['disable_top_bar'] = [
			'title'       => \esc_html__( 'Top Bar', 'total-theme-core' ),
			'id'          => 'wpex_disable_top_bar',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Default', 'total-theme-core' ),
				'enable' => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['disable_breadcrumbs'] = [
			'title'       => \esc_html__( 'Breadcrumbs', 'total-theme-core' ),
			'id'          => 'wpex_disable_breadcrumbs',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Default', 'total-theme-core' ),
				'enable' => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['disable_social'] = [
			'title'       => \esc_html__( 'Social Share', 'total-theme-core' ),
			'id'          => 'wpex_disable_social',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Default', 'total-theme-core' ),
				'enable' => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['secondary_thumbnail'] = [
			'title'       => \esc_html__( 'Secondary Image', 'total-theme-core' ),
			'id'          => 'wpex_secondary_thumbnail',
			'type'        => 'image',
			'description' => \esc_html__( 'Used for the secondary Image Swap overlay style.', 'total-theme-core' ),
		];

		return [
			'title'    => \esc_html__( 'Main', 'total-theme-core' ),
			'settings' => $fields,
		];
	}

	/**
	 * Returns the main fields non-singular.
	 * 
	 * This fields are added when is_post_type_viewable() returns false.
	 */
	private static function get_main_fields_non_singular(): array {
		$fields = [];

		$fields['secondary_thumbnail'] = [
			'title'       => \esc_html__( 'Secondary Image', 'total-theme-core' ),
			'id'          => 'wpex_secondary_thumbnail',
			'type'        => 'image',
			'description' => \esc_html__( 'Used for the secondary Image Swap overlay style.', 'total-theme-core' ),
		];

		return [
			'title'    => \esc_html__( 'Main', 'total-theme-core' ),
			'settings' => $fields,
		];
	}

	/**
	 * Returns the header tab fields.
	 */
	private static function get_header_fields(): array {
		$fields           = [];
		$is_dev_header    = ( 'dev' === get_theme_mod( 'header_style' ) );
		$is_header_custom = function_exists( 'totaltheme_call_static' ) && totaltheme_call_static( 'Header\Core', 'is_custom' );
			
		$fields['disable_header'] = [
			'title'       => \esc_html__( 'Header', 'total-theme-core' ),
			'id'          => 'wpex_disable_header',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => array(
				''       => \esc_html__( 'Default', 'total-theme-core' ),
				'enable' => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			),
		];

		if ( ! $is_dev_header ) {
			$fields['header_style'] = [
				'title'       => \esc_html__( 'Header Style', 'total-theme-core' ),
				'id'          => 'wpex_header_style',
				'type'        => 'select',
				'description' => \esc_html__( 'Override default header style.', 'total-theme-core' ),
				'choices'     => [ self::class, 'get_header_styles' ],
			];
		}

		$fields['sticky_header'] = [
			'title'       => \esc_html__( 'Sticky Header', 'total-theme-core' ),
			'id'          => 'wpex_sticky_header',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => array(
				''        => \esc_html__( 'Default', 'total-theme-core' ),
				'enable'  => \esc_html__( 'Enable', 'total-theme-core' ),
				'disable' => \esc_html__( 'Disable', 'total-theme-core' ),
			),
		];

		$fields['logo_scroll_top'] = [
			'title'       => \esc_html__( 'Scroll Up When Clicking Logo', 'total-theme-core' ),
			'id'          => 'wpex_logo_scroll_top',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => array(
				''        => \esc_html__( 'Default', 'total-theme-core' ),
				'enable'  => \esc_html__( 'Enable', 'total-theme-core' ),
				'disable' => \esc_html__( 'Disable', 'total-theme-core' ),
			),
		];

		$fields['header_menu'] = [
			'title'       => \esc_html__( 'Menu', 'total-theme-core' ),
			'type'        => 'button_group',
			'id'          => 'wpex_header_menu',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => array(
				''    => \esc_html__( 'Default', 'total-theme-core' ),
				'on'  => \esc_html__( 'Enable', 'total-theme-core' ),
				'off' => \esc_html__( 'Disable', 'total-theme-core' ),
			),
		];

		if ( ! $is_header_custom ) {
			$fields['custom_menu'] = [
				'title'       => \esc_html__( 'Custom Menu', 'total-theme-core' ),
				'type'        => 'select',
				'id'          => 'wpex_custom_menu',
				'description' => \esc_html__( 'Select a custom menu for this page or post.', 'total-theme-core' ),
				'choices'     => [ self::class, 'get_menus' ],
			];
		}

		$fields['overlay_header'] = [
			'title'       => \esc_html__( 'Transparent Header', 'total-theme-core' ),
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'id'          => 'wpex_overlay_header',
			'type'        => 'button_group',
			'choices'     => array(
				''    => \esc_html__( 'Default', 'total-theme-core' ),
				'on'  => \esc_html__( 'Enable', 'total-theme-core' ),
				'off' => \esc_html__( 'Disable', 'total-theme-core' ),
			),
		];

		if ( ! $is_dev_header && ! $is_header_custom ) {
			$fields['overlay_header_style'] = [
				'title'       => \esc_html__( 'Transparent Header Style', 'total-theme-core' ),
				'type'        => 'select',
				'id'          => 'wpex_overlay_header_style',
				'description' => \esc_html__( 'Select your overlay header style', 'total-theme-core' ),
				'choices'     => 'TotalTheme\Header\Overlay::style_choices',
				'default' => '',
			];
		}

		$fields['overlay_header_background'] = [
			'title'       => \esc_html__( 'Transparent Header Background', 'total-theme-core' ),
			'id'          => 'wpex_overlay_header_background',
			'description' => \esc_html__( 'Select a color to enable a background for your header (optional)', 'total-theme-core' ),
			'type'        => 'color',
		];

		if ( ! $is_dev_header && ! $is_header_custom ) {
			$fields['overlay_header_dropdown_style'] = [
				'title'       => \esc_html__( 'Transparent Header Dropdown Style', 'total-theme-core' ),
				'type'        => 'select',
				'id'          => 'wpex_overlay_header_dropdown_style',
				'description' => \esc_html__( 'Select your overlay header style', 'total-theme-core' ),
				'choices'     => 'wpex_get_menu_dropdown_styles',
				//'default' => 'black', // @deprecated 1.0.4
			];
		}

		if ( ! $is_dev_header && ! $is_header_custom ) {
			$fields['overlay_header_font_size'] = [
				'title'       => \esc_html__( 'Transparent Header Menu Font Size', 'total-theme-core' ),
				'id'          => 'wpex_overlay_header_font_size',
				'description' => \esc_html__( 'Custom font size for your transparent header.', 'total-theme-core' ),
				'type'        => 'text',
			];
		}
		
		if ( ! $is_header_custom ) {
			$fields['overlay_header_logo'] = [
				'title'       => \esc_html__( 'Transparent Header Logo', 'total-theme-core'),
				'id'          => 'wpex_overlay_header_logo',
				'type'        => 'image',
				'description' => \esc_html__( 'Select a custom logo (optional) for the overlay header.', 'total-theme-core' ),
			];
		}

		if ( ! $is_header_custom ) {
			$fields['overlay_header_logo_retina'] = [
				'title'       => \esc_html__( 'Transparent Header Logo: Retina', 'total-theme-core'),
				'id'          => 'wpex_overlay_header_logo_retina',
				'type'        => 'image',
				'description' => \esc_html__( 'Retina version for the overlay header custom logo.', 'total-theme-core' ),
			];
		}

		return [
			'title'    => \esc_html__( 'Header', 'total-theme-core' ),
			'settings' => $fields,
		];
	}

	/**
	 * Returns the title tab fields.
	 */
	private static function get_title_fields(): array {
		$fields = [];

		$fields['disable_title'] = [
			'title'       => \esc_html__( 'Title', 'total-theme-core' ),
			'id'          => 'wpex_disable_title',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Default', 'total-theme-core' ),
				'enable' => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['post_title'] = [
			'title'       => \esc_html__( 'Custom Title', 'total-theme-core' ),
			'id'          => 'wpex_post_title',
			'type'        => 'html',
			'rows'        => 1,
			'description' => \esc_html__( 'Alter the main title display.', 'total-theme-core' ),
		];

		$fields['disable_header_margin'] = [
			'title'       => \esc_html__( 'Title Margin', 'total-theme-core' ),
			'id'          => 'wpex_disable_header_margin',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => array(
				''       => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			),
		];

		$fields['post_subheading'] = [
			'title'       => \esc_html__( 'Subheading', 'total-theme-core' ),
			'type'        => 'html',
			'rows'        => 1,
			'id'          => 'wpex_post_subheading',
			'description' => \esc_html__( 'Enter your page subheading. Shortcodes & HTML is allowed.', 'total-theme-core' ),
		];

		$fields['post_title_style'] = [
			'title'       => \esc_html__( 'Title Style', 'total-theme-core' ),
			'type'        => 'select',
			'id'          => 'wpex_post_title_style',
			'description' => \esc_html__( 'Select a custom title style for this page or post.', 'total-theme-core' ),
			'choices'     => [ self::class, 'get_title_styles' ],
		];

		$fields['post_title_background_color'] = [
			'title'       => \esc_html__( 'Background Color', 'total-theme-core' ),
			'description' => \esc_html__( 'Select a color.', 'total-theme-core' ),
			'id'          => 'wpex_post_title_background_color',
			'type'        => 'color',
		];

		$fields['post_title_background_redux'] = [
			'title'       => \esc_html__( 'Background Image', 'total-theme-core'),
			'id'          => 'wpex_post_title_background_redux', //@todo remove _redux
			'type'        => 'image',
			'description' => \esc_html__( 'Select a custom header image for your main title.', 'total-theme-core' ),
		];

		$fields['post_title_height'] = [
			'title'       => \esc_html__( 'Background Height', 'total-theme-core' ),
			'type'        => 'text',
			'id'          => 'wpex_post_title_height',
			'description' => \esc_html__( 'Select your custom height for your title background.', 'total-theme-core' ),
		];

		$fields['post_title_background_style'] = [
			'title'       => \esc_html__( 'Background Style', 'total-theme-core' ),
			'type'        => 'select',
			'id'          => 'wpex_post_title_background_image_style',
			'description' => \esc_html__( 'Select the style.', 'total-theme-core' ),
			'choices'     => 'wpex_get_bg_img_styles',
		];

		$fields['post_title_background_position'] = [
			'title'       => \esc_html__( 'Background Position', 'total-theme-core' ),
			'type'        => 'text',
			'id'          => 'wpex_post_title_background_position', // @todo rename to post_title_background_image_position
			'description' => \esc_html__( 'Enter a custom position for your background image.', 'total-theme-core' ),
		];

		$fields['post_title_background_overlay'] = [
			'title'       => \esc_html__( 'Background Overlay', 'total-theme-core' ),
			'type'        => 'select',
			'id'          => 'wpex_post_title_background_overlay',
			'description' => \esc_html__( 'Select an overlay for the title background.', 'total-theme-core' ),
			'choices'     => [
				''         => \esc_html__( 'None', 'total-theme-core' ),
				'dark'     => \esc_html__( 'Dark', 'total-theme-core' ),
				'dotted'   => \esc_html__( 'Dotted', 'total-theme-core' ),
				'dashed'   => \esc_html__( 'Diagonal Lines', 'total-theme-core' ),
				'bg_color' => \esc_html__( 'Background Color', 'total-theme-core' ),
			],
		];

		$fields['post_title_background_overlay_opacity'] = [
			'id'          => 'wpex_post_title_background_overlay_opacity',
			'type'        => 'number',
			'title'       => \esc_html__( 'Background Overlay Opacity', 'total-theme-core' ),
			'description' => \esc_html__( 'Enter a custom opacity for your title background overlay.', 'total-theme-core' ),
			'default'     => '',
			'hidden'      => true,
			'step'        => 0.01,
			'min'         => 0,
			'max'         => 1,
		];

		return [
			'title'    => \esc_html__( 'Title', 'total-theme-core' ),
			'settings' => $fields,
		];
	}

	/**
	 * Returns the backgrounds tab fields.
	 */
	private static function get_background_fields(): array {
		$fields = [];

		$fields['page_background_color'] = [
			'title'       => \esc_html__( 'Background Color', 'total-theme-core' ),
			'description' => \esc_html__( 'Select a color.', 'total-theme-core' ),
			'id'          => 'wpex_page_background_color',
			'type'        => 'color',
		];

		// @todo remove _redux
		$fields['page_background_image_redux'] = [
			'title'       => \esc_html__( 'Background Image', 'total-theme-core' ),
			'id'          => 'wpex_page_background_image_redux',
			'description' => \esc_html__( 'Select an image.', 'total-theme-core' ),
			'type'        => 'image',
		];

		$fields['page_background_image_style'] = [
			'title'       => \esc_html__( 'Background Style', 'total-theme-core' ),
			'type'        => 'select',
			'id'          => 'wpex_page_background_image_style',
			'description' => \esc_html__( 'Select the style.', 'total-theme-core' ),
			'choices'     => 'wpex_get_bg_img_styles',
		];

		return [
			'title'    => \esc_html__( 'Background', 'total-theme-core' ),
			'settings' => $fields,
		];
	}

	/**
	 * Returns the footer tab fields.
	 */
	private static function get_footer_fields(): array {
		$fields = [];

		$fields['disable_footer'] = [
			'title'       => \esc_html__( 'Footer', 'total-theme-core' ),
			'id'          => 'wpex_disable_footer',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Includes the footer callout, widgets and footer bottom areas.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Default', 'total-theme-core' ),
				'enable' => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['disable_footer_widgets'] = [
			'title'       => \esc_html__( 'Footer Widgets', 'total-theme-core' ),
			'id'          => 'wpex_disable_footer_widgets',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => [
				''   => \esc_html__( 'Default', 'total-theme-core' ),
				'on' => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['footer_reveal'] = [
			'title'       => \esc_html__( 'Footer Reveal', 'total-theme-core' ),
			'description' => \esc_html__( 'The footer will be placed in a fixed postion and display on scroll.', 'total-theme-core' ),
			'id'          => 'wpex_footer_reveal',
			'type'        => 'button_group',
			'choices'     => [
				''    => \esc_html__( 'Default', 'total-theme-core' ),
				'on'  => \esc_html__( 'Enable', 'total-theme-core' ),
				'off' => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['footer_bottom'] = [
			'title'       => \esc_html__( 'Footer Bottom', 'total-theme-core' ),
			'description' => \esc_html__( 'Enable the footer bottom area (copyright section).', 'total-theme-core' ),
			'id'          => 'wpex_footer_bottom',
			'type'        => 'button_group',
			'choices'     => [
				''    => \esc_html__( 'Default', 'total-theme-core' ),
				'on'  => \esc_html__( 'Enable', 'total-theme-core' ),
				'off' => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		return [
			'title'    => \esc_html__( 'Footer', 'total-theme-core' ),
			'settings' => $fields,
		];
	}

	/**
	 * Returns the callout tab fields.
	 */
	private static function get_callout_fields(): array {
		$fields = [];

		$fields['disable_footer_callout'] = [
			'title'       => \esc_html__( 'Callout', 'total-theme-core' ),
			'id'          => 'wpex_disable_footer_callout',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable this element on this page or post.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Default', 'total-theme-core' ),
				'enable' => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['callout_link'] = [
			'title'       => \esc_html__( 'Callout Link', 'total-theme-core' ),
			'id'          => 'wpex_callout_link',
			'type'        => 'link',
			'description' => \esc_html__( 'Override the default callout link.', 'total-theme-core' ),
		];

		$fields['callout_link_txt'] = [
			'title'       => \esc_html__( 'Callout Link Text', 'total-theme-core' ),
			'id'          => 'wpex_callout_link_txt',
			'type'        => 'text',
			'description' => \esc_html__( 'Override the default callout link text.', 'total-theme-core' ),
		];

		$fields['callout_text'] = [
			'title'       => \esc_html__( 'Callout Text', 'total-theme-core' ),
			'id'          => 'wpex_callout_text',
			'type'        => 'html',
			'rows'        => '5',
			'description' => \esc_html__( 'Override the default callout text.', 'total-theme-core' ),
		];

		return [
			'title'    => \esc_html__( 'Callout', 'total-theme-core' ),
			'settings' => $fields,
		];
	}

	/**
	 * Returns the slider tab fields.
	 */
	private static function get_slider_fields(): array {
		$fields = [];

		$fields['post_slider_shortcode'] = [
			'title'       => \esc_html__( 'Slider Shortcode', 'total-theme-core' ),
			'type'        => 'code',
			'rows' => 1,
			'id'          => 'wpex_post_slider_shortcode',
			'description' => \esc_html__( 'Enter a slider shortcode here to display a slider at the top of the page.', 'total-theme-core' ),
		];

		$fields['post_slider_shortcode_position'] = [
			'title'       => \esc_html__( 'Slider Position', 'total-theme-core' ),
			'type'        => 'select',
			'id'          => 'wpex_post_slider_shortcode_position',
			'description' => \esc_html__( 'Select the position for the slider shortcode.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Default', 'total-theme-core' ),
				'below_title' => \esc_html__( 'Below Title', 'total-theme-core' ),
				'above_title' => \esc_html__( 'Above Title', 'total-theme-core' ),
				'above_menu' => \esc_html__( 'Above Menu (Header 2 or 3)', 'total-theme-core' ),
				'above_header' => \esc_html__( 'Above Header', 'total-theme-core' ),
				'above_topbar' => \esc_html__( 'Above Top Bar', 'total-theme-core' ),
			],
		];

		$fields['post_slider_bottom_margin'] = [
			'title'       => \esc_html__( 'Slider Bottom Margin', 'total-theme-core' ),
			'description' => \esc_html__( 'Enter a bottom margin for your slider in pixels.', 'total-theme-core' ),
			'id'          => 'wpex_post_slider_bottom_margin',
			'type'        => 'text',
		];

		$fields['contain_post_slider'] = [
			'title'       => \esc_html__( 'Contain Slider?', 'total-theme-core' ),
			'id'          => 'wpex_contain_post_slider',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Adds the container wrapper around the slider to center it with the rest of the content.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Disable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Enable', 'total-theme-core' ),
			],
		];

		$fields['disable_post_slider_mobile'] = [
			'title'       => \esc_html__( 'Slider On Mobile', 'total-theme-core' ),
			'id'          => 'wpex_disable_post_slider_mobile',
			'type'        => 'button_group',
			'description' => \esc_html__( 'Enable or disable slider display for mobile devices.', 'total-theme-core' ),
			'choices'     => [
				''       => \esc_html__( 'Enable', 'total-theme-core' ),
				'on'     => \esc_html__( 'Disable', 'total-theme-core' ),
			],
		];

		$fields['post_slider_mobile_alt'] = [
			'title'       => \esc_html__( 'Slider Mobile Alternative', 'total-theme-core' ),
			'id'          => 'wpex_post_slider_mobile_alt',
			'description' => \esc_html__( 'Select an image.', 'total-theme-core' ),
			'type'        => 'image',
		];

		$fields['post_slider_mobile_alt_url'] = [
			'title'       => \esc_html__( 'Slider Mobile Alternative URL', 'total-theme-core' ),
			'id'          => 'wpex_post_slider_mobile_alt_url',
			'description' => \esc_html__( 'URL for the mobile slider alternative.', 'total-theme-core' ),
			'type'        => 'text',
		];

		$fields['post_slider_mobile_alt_url_target'] = [
			'title'       => \esc_html__( 'Slider Mobile Alternative URL Target', 'total-theme-core' ),
			'id'          => 'wpex_post_slider_mobile_alt_url_target',
			'description' => \esc_html__( 'Select your link target window.', 'total-theme-core' ),
			'type'        => 'button_group',
			'choices'     => [
				''       => \esc_html__( 'Same Tab', 'total-theme-core' ),
				'blank' => \esc_html__( 'New Tab', 'total-theme-core' ),
			],
		];

		return [
			'title'    => \esc_html__( 'Slider', 'total-theme-core' ),
			'settings' => $fields,
		];
	}

	/**
	 * Returns the media fields.
	 */
	private static function get_media_fields( $post_type ): array {
		$fields = [];
		$is_viewable = \is_post_type_viewable( $post_type );

		if ( self::has_media_fields( $post_type ) ) {

			if ( $is_viewable ) {

				if ( ! self::post_type_has_dynamic_template( $post_type ) ) {
					$fields['post_media_position'] = [
						'title'       => \esc_html__( 'Media Display', 'total-theme-core' ),
						'id'          => 'wpex_post_media_position',
						'type'        => 'button_group',
						'description' => \esc_html__( 'Select your preferred position for your post\'s media (featured image or video).', 'total-theme-core' ),
						'choices'     => [
							''       => \esc_html__( 'Default', 'total-theme-core' ),
							'above'  => \esc_html__( 'Full Width', 'total-theme-core' ),
							'hidden' => \esc_html__( 'Hidden', 'total-theme-core' ),
						],
					];
				}

			}
			
			$fields['post_oembed'] = [
				'title'       => \esc_html__( 'oEmbed URL', 'total-theme-core' ),
				'description' => \esc_html__( 'Enter a URL that is compatible with WP\'s built-in oEmbed feature.', 'total-theme-core' ),
				'id'          => 'wpex_post_oembed',
				'type'        => 'text',
			];

			$fields['post_self_hosted_media'] = [
				'title'       => \esc_html__( 'Self Hosted Video or Audio', 'total-theme-core' ),
				'description' => \esc_html__( 'Select a self hosted video or audio.', 'total-theme-core' ),
				'id'          => 'wpex_post_self_hosted_media',
				'type'        => 'media',
				'media_type'  => 'video,audio',
				'migrate'     => 'wpex_post_self_hosted_shortcode_redux',
			];

			$fields['post_video_embed'] = [
				'title'       => \esc_html__( 'Embed Code', 'total-theme-core' ),
				'description' => \esc_html__( 'Insert your embed/iframe code.', 'total-theme-core' ),
				'id'          => 'wpex_post_video_embed',
				'type'        => 'iframe',
				'rows'        => 4,
			];

			if ( ! self::has_core_fields( $post_type ) ) {
				$fields['secondary_thumbnail'] = [
					'title'       => \esc_html__( 'Secondary Image', 'total-theme-core' ),
					'id'          => 'wpex_secondary_thumbnail',
					'type'        => 'image',
					'description' => \esc_html__( 'Used for the secondary Image Swap overlay style.', 'total-theme-core' ),
				];
			}
		}

		// We need to define things this way to prevent issues with older methods of including the post type
		// by hooking into "wpex_metabox_array" to modify the post types the media tab was available for.
		$post_types = [
			'post',
		];

		if ( $fields && 'post' !== $post_type ) {
			$post_types[] = $post_type;
		}
		
		return [
			'title'     => \esc_html__( 'Media', 'total-theme-core' ),
			'settings'  => $fields,
			'post_type' => $post_types,
		];
	}

	/**
	 * Checks if the core fields are enabled.
	 */
	private static function has_core_fields( $post_type ): bool {
		$check = \wp_validate_boolean( \get_theme_mod( 'theme_settings_metabox_core_fields_enable', true ) );
		return (bool) \apply_filters( 'totalthemecore/meta/main_metabox/has_core_fields', $check, $post_type );
	}

	/**
	 * Check if the media tab should be enabled.
	 */
	private static function has_media_fields( $post_type ): bool {
		if ( 'post' === $post_type ) {
			$check = \wp_validate_boolean( \get_theme_mod( 'metabox_media_fields_enable', true ) );
		} else {
			$check = false;
			// Fix for when updating to version 5.18 when we introduced this option.
			// @todo deprecate and force people to use new totalthemecore/meta/main_metabox/has_media_fields filter.
			$filter_array = \apply_filters( 'wpex_metabox_array', [], null );
			if ( isset( $filter_array['media']['post_type'] )
				&& \is_array( $filter_array['media']['post_type'] )
				&& \in_array( $post_type, $filter_array['media']['post_type'], true )
			) {
				$check = true;
			}
		}
		return (bool) \apply_filters( 'totalthemecore/meta/main_metabox/has_media_fields', $check, $post_type );
	}

	/**
	 * Helper function to check if a post_type has a dynamic template.
	 */
	private static function post_type_has_dynamic_template( $post_type ): bool {
		if ( \function_exists( '\totaltheme_call_static' )
			&& $template_id = (int) \totaltheme_call_static( 'Theme_Builder\Post_Template', 'get_template_id', $post_type )
		) {
			$post = \get_post( $template_id );
			if ( $post && 'publish' === \get_post_status( $post ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get header_styles.
	 */
	public static function get_header_styles(): array {
		$header_styles = [
			'' => \esc_html__( 'Default', 'total-theme-core' ),
		];
		if ( \is_callable( 'TotalTheme\Header\Core::style_choices' ) ) {
			$header_styles = $header_styles + \TotalTheme\Header\Core::style_choices();
		}
		return $header_styles;
	}

	/**
	 * Get menus.
	 */
	public static function get_menus(): array {
		$menus = [
			\esc_html__( 'Default', 'total-theme-core' )
		];
		$get_menus = \get_terms( 'nav_menu', [ 'hide_empty' => true ] );
		foreach ( $get_menus as $menu) {
			$menus[ $menu->term_id ] = $menu->name;
		}
		return $menus;
	}

	/**
	 * Get title styles.
	 */
	public static function get_title_styles(): array {
		return \apply_filters( 'wpex_title_styles', [
			''                 => \esc_html__( 'Default', 'total-theme-core' ),
			'centered'         => \esc_html__( 'Centered', 'total-theme-core' ),
			'centered-minimal' => \esc_html__( 'Centered Minimal', 'total-theme-core' ),
			'background-image' => \esc_html__( 'Background Image', 'total-theme-core' ),
			'solid-color'      => \esc_html__( 'Solid Color & White Text', 'total-theme-core' ),
		] );
	}

	/**
	 * Get widget areas.
	 */
	public static function get_widget_areas(): array {
		$widget_areas = [
			\esc_html__( 'Default', 'total-theme-core' )
		];
		if ( \function_exists( '\wpex_get_widget_areas' ) ) {
			$widget_areas = $widget_areas + \wpex_get_widget_areas();
		}
		return $widget_areas;
	}

}
