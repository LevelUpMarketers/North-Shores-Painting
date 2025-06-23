<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Total Theme Panel.
 */
class Theme_Panel {

	/**
	 * Check if the admin bar is enabled.
	 */
	protected static $admin_bar_enabled;

	/**
	 * Used to set the admin bar updated notice type (saved/failed/etc).
	 */
	protected static $admin_bar_updated_notice = false;

	/**
	 * Admin bar title tag.
	 */
	protected static $admin_bar_title_tag = 'h1';

	/**
	 * Class instance.
	 */
	private static $instance = null;

	/**
	 * Admin Hook Prefix.
	 */
	public static $hook_prefix;

	/**
	 * Create or retrieve the instance of Theme_Panel.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Private Constructor.
	 */
	private function __construct() {
		\add_action( 'admin_menu', [ $this, 'on_admin_menu' ], 0 );
		\add_action( 'admin_enqueue_scripts', [ $this, 'maybe_enqueue_scripts' ] );
		\add_action( 'admin_init', [ $this, 'register_settings' ] );
		\add_action( 'in_admin_header', [ $this, 'admin_bar' ] );
	}

	/**
	 * Return settings.
	 *
	 * Important: Settings must be registered on init so that the strings can be translated.
	 */
	protected function get_settings(): array {
		$total_theme_core_enabled = \class_exists( '\TotalThemeCore\Plugin', false );

		$settings = [];

		if ( $total_theme_core_enabled ) {
			$settings[] = [
				'id'          => 'demo_importer_enable',
				'label'       => \esc_html__( 'Demo Importer', 'total' ),
				'icon'        => 'download',
				'category'    => \esc_html__( 'Core', 'total' ),
				'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Demo Importer so you can import one of the Total live demos. Demos are to be used as a "starter" for a new site and are best imported on a fresh WordPress installation.', 'total' ),
			];
		}

		$settings[] = [
			'id'          => 'under_construction_enable',
			'label'       => \esc_html__( 'Under Construction', 'total' ),
			'icon'        => 'person-digging',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Under Construction where you can select a custom page and have your entire website redirect to this page for any visitor that is not logged in. Logged in users will be able to browse the site normally.', 'total' ),
		];

		$settings[] = [
			'id'          => 'recommend_plugins_enable',
			'label'       => \esc_html__( 'Bundled/Recommended Plugins', 'total' ),
			'icon'        => 'plug',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a notice that displays a list of bundled or recommended plugins for the theme. It also adds an admin screen under Appearance > Install Plugins for installing and updating bundled plugins. This feature also provides updates to the bundled plugins whenever there is a theme update which includes an updated version of a bundled plugin.', 'total' ),
		];

		$settings[] = [
			'id'          => 'dark_mode_enable',
			'disabled'    => true,
			'label'       => \esc_html__( 'Dark Mode', 'total' ),
			'icon'        => 'circle-half-stroke',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a "Dark Mode" tab under Customize > Global Styles to assign dark colors to your main site colors, the ability to assign a dark color variation to your color palette colors as well as the ability to enable a light/dark toggle button for your site.', 'total' ),
		];

		if ( $total_theme_core_enabled ) {
			$settings[] = [
				'id'          => 'wpex_templates_enable',
				'label'       => \esc_html__( 'Dynamic Templates', 'total' ),
				'icon'        => 'layer-group',
				'category'    => \esc_html__( 'Core', 'total' ),
				'description' => \esc_html__( 'Enables the Dynamic Templates dashboard.', 'total' ),
			];

			$settings[] = [
				'id'          => 'extend_visual_composer',
				'label'       => \esc_html__( 'Theme Elements', 'total' ),
				'icon'        => 'cubes-stacked',
				'category'    => 'Shortcodes/Blocks',
				'description' => \esc_html__( 'Enables over 60 custom builder blocks exclusive to the Total theme for WPBakery. These modules are located inside the Total Theme Core plugin. By default the modules will load regardless of the WPBakery plugin allowing you to use the blocks as standard shortcodes if you are using a different builder such as Elementor.', 'total' ),
			];
		}

		$settings[] = [
			'id'          => 'custom_css_enable',
			'label'       => \esc_html__( 'Custom CSS', 'total' ),
			'icon'        => 'css3',
			'category'    => \esc_html__( 'Developers', 'total' ),
			'description' => \esc_html__( 'Enables the Custom CSS admin panel for making CSS customizations via the backend. This function hooks into the core WordPress custom CSS functionality so any CSS added here will also be available in the Customizer.', 'total' ),
		];

		$settings[] = [
			'id'          => 'custom_actions_enable',
			'label'       => \esc_html__( 'Custom Actions', 'total' ),
			'icon'        => 'code',
			'category'    => \esc_html__( 'Developers', 'total' ),
			'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Custom Actions where you can insert HTML/JS to any of the theme’s core action hooks. It’s a great way to add global content anywhere on your site. PHP input is not supported for security reasons. For more advanced modifications it is adviced to use a child theme.', 'total' ),
		];

		if ( $total_theme_core_enabled ) {

			if ( class_exists( 'TotalTheme\Portfolio\Post_Type' ) ) {
				$settings[] = [
					'id'       => 'portfolio_enable',
					'label'    => totaltheme_call_static( 'Portfolio\Post_Type', 'get_name' ),
					'icon'     => $this->get_dashicon_class( totaltheme_call_static( 'Portfolio\Post_Type', 'get_menu_icon' ) ),
					'category' => \esc_html__( 'Post Types', 'total' ),
				];
			}

			if ( class_exists( 'TotalTheme\Staff\Post_Type' ) ) {
				$settings[] = [
					'id'       => 'staff_enable',
					'label'    => totaltheme_call_static( 'Staff\Post_Type', 'get_name' ),
					'icon'     => $this->get_dashicon_class( totaltheme_call_static( 'Staff\Post_Type', 'get_menu_icon' ) ),
					'category' => \esc_html__( 'Post Types', 'total' ),
				];
			}

			if ( class_exists( 'TotalTheme\Testimonials\Post_Type' ) ) {
				$settings[] = [
					'id'       => 'testimonials_enable',
					'label'    => totaltheme_call_static( 'Testimonials\Post_Type', 'get_name' ),
					'icon'     => $this->get_dashicon_class( totaltheme_call_static( 'Testimonials\Post_Type', 'get_menu_icon' ) ),
					'category' => \esc_html__( 'Post Types', 'total' ),
				];
			}

			$settings[] = [
				'id'          => 'post_series_enable',
				'label'       => \esc_html__( 'Post Series', 'total' ),
				'icon'        => 'list',
				'category'    => \esc_html__( 'Core', 'total' ),
				'description' => \esc_html__( 'Enables a new taxonomy named Post Series to your standard posts which allows you to "connect" different posts together as a series. For any post in a series, the front end will display links to all other posts in the series. This is commonly used for multipart tutorials.', 'total' ),
			];
		}

		$settings[] = [
			'id'          => 'header_builder_enable',
			'label'       => \esc_html__( 'Header Builder', 'total' ),
			'icon'        => 'window-maximize',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Header Builder where you can select any template to override the default theme header. This functionality is compatible with both WPBakery and Elementor if you want to create a page builder based header.', 'total' ),
		];

		$settings[] = [
			'id'          => 'footer_builder_enable',
			'label'       => \esc_html__( 'Footer Builder', 'total' ),
			'icon'        => 'window-maximize',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Footer Builder where you can select any template to override the default theme footer. This functionality is compatible with both WPBakery and Elementor if you want to create a page builder based header.', 'total' ),
		];

		$settings[] = [
			'id'          => 'custom_admin_login_enable',
			'label'       => \esc_html__( 'Custom WP Login Screen', 'total' ),
			'icon'        => 'sign-in',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Custom Login where you tweak the default WordPress login screen such as uploading a custom logo and changin the default background and form colors.', 'total' ),
		];

		$settings[] = [
			'id'          => 'custom_404_enable',
			'label'       => \esc_html__( 'Custom 404 Page', 'total' ),
			'icon'        => 'warning-circle',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Custom 404 where you can redirect your 404 pages or select a custom template for your 404 page.', 'total' ),
		];

		$settings[] = [
			'id'          => 'customizer_panel_enable',
			'label'       => \esc_html__( 'Customizer Manager', 'total' ),
			'icon'        => 'dashicons dashicons-admin-settings',
			'category'    => \esc_html__( 'Optimizations', 'total' ),
			'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Customizer Manager where you can select what theme tabs are displayed in the Customizer. For example if you do not plan on using the theme Top Bar or Toggle Bar functionaility you can hide the settings completely in the Customizer to slim things down and speed things up.', 'total' ),
		];

		if ( \totaltheme_is_integration_active( 'elementor' ) ) {

			if ( ! \defined( 'ELEMENTOR_PRO_VERSION' ) ) {
				$settings[] = [
					'id'          => 'elementor_remove_upsells',
					'label'       => \esc_html__( 'Remove Elementor Upsells', 'total' ),
					'icon'        => 'ban',
					'category'    => \esc_html__( 'Elementor', 'total' ),
					'description' => \esc_html__( 'When enabled, the theme will attempt to remove as many Elementor upsell prompts as possible, helping to declutter the admin area and eliminate unnecessary distractions.', 'total' ),
				];
			}

			$settings[] = [
				'id'          => 'elementor_container_compat',
				'label'       => \esc_html__( 'Elementor Container Compatibility', 'total' ),
				'icon'        => 'arrows-left-right-to-line',
				'category'    => \esc_html__( 'Elementor', 'total' ),
				'description' => \esc_html__( 'Enables compatibility between the theme\'s layout and Elementor. It removes the default left and right padding applied to containers in Elementor and sets the width and max width for the outermost containers to match the settings defined under Customize > Layout. This helps align your Elementor-created pages with the overall design of the site.', 'total' ),
			];
		} else {
			$settings[] = [
				'id'          => 'custom_wp_gallery_enable',
				'label'       => \esc_html__( 'Custom WordPress Gallery', 'total' ),
				'icon'        => 'images-o',
				'category'    => \esc_html__( 'Core', 'total' ),
				'description' => \esc_html__( 'Enables a custom output for the "legacy" WordPress gallery (non-gutenberg) that includes lightbox functionality and custom image cropping via the settings under Theme Panel > Image Sizes > Other. If you are using Elementor it is recommended that you disable this setting to prevent conflicts because the plugin also creates a custom output for galleries.', 'total' ),
			];
		}

		if ( $total_theme_core_enabled ) {
			$settings[] = [
				'id'          => 'widget_areas_enable',
				'label'       => \esc_html__( 'Custom Widget Areas', 'total' ),
				'icon'        => 'dashicons dashicons-welcome-widgets-menus',
				'category'    => \esc_html__( 'Widgets', 'total' ),
				'description' => \esc_html__( 'Enables a dashboard at Appearance > Widget Areas for creating and assigning custom widget areas.', 'total' ),
			];

			$settings[] = [
				'id'          => 'card_builder_enable',
				'label'       => \esc_html__( 'Card Builder', 'total' ),
				'icon'        => 'dashicons dashicons-images-alt',
				'category'    => \esc_html__( 'Post Cards', 'total' ),
				'description' => \esc_html__( 'Enables the Custom Cards panel which allows you to build your own custom post card designs.', 'total' ),
			];

			$settings[] = [
				'id'          => 'custom_widgets_enable',
				'label'       => \esc_html__( 'Theme Widgets', 'total' ),
				'icon'        => 'dashicons dashicons-list-view',
				'category'    => \esc_html__( 'Widgets', 'total' ),
				'description' => \esc_html__( 'Enables over 20 custom widgets exclusive to the Total theme.', 'total' ),
			];
		}

		$settings[] = [
			'id'          => 'widget_block_editor_enable',
			'label'       => \esc_html__( 'Widget Block Editor', 'total' ),
			'icon'        => 'dashicons dashicons-list-view',
			'category'    => \esc_html__( 'Widgets', 'total' ),
			'description' => \esc_html__( 'Enables the widget block editor in WordPress 5.8+.', 'total' ),
		];

		if ( $total_theme_core_enabled ) {
			$settings[] = [
				'id'          => 'term_thumbnails_enable',
				'label'       => \esc_html__( 'Taxonomy Thumbnails', 'total' ),
				'icon'        => 'image-o',
				'category'    => \esc_html__( 'Taxonomies (categories)', 'total' ),
				'description' => \esc_html__( 'Enables the ability to define a thumbnail for your categories and custom taxonomies. This thumbnail is used on the front-end as the page header background and is also used in various elements such as the Categories Grid and Categories Carousel modules.', 'total' ),
			];

			$settings[] = [
				'id'          => 'term_page_header_image_enable',
				'label'       => \esc_html__( 'Taxonomy Thumbnail Page Header', 'total' ),
				'icon'        => 'image-o',
				'category'    => \esc_html__( 'Taxonomies (categories)', 'total' ),
				'description' => \esc_html__( 'When enabled taxonomy terms that have thumbnails defined will display the page header background style using the thumbnail as the background.', 'total' ),
			];

			$settings[] = [
				'id'          => 'term_meta_enable',
				'label'       => \esc_html__( 'Taxonomy Theme Settings', 'total' ),
				'icon'        => 'dashicons dashicons-admin-settings',
				'category'    => \esc_html__( 'Taxonomies (categories)', 'total' ),
				'description' => \esc_html__( 'Enables extra options within the Theme Settings metabox when editing categories and other taxonomies (redirection, sidebar, card entry style, etc).', 'total' ),
			];

			$settings[] = [
				'id'          => 'term_colors_enable',
				'label'       => \esc_html__( 'Taxonomy Colors', 'total' ),
				'icon'        => 'eye-dropper',
				'category'    => \esc_html__( 'Taxonomies (categories)', 'total' ),
				'description' => \esc_html__( 'Enables the ability to set custom colors for your categories which may be used by various theme elements and card styles. This functionality is enabled for categories only but can be extended to custom taxonomies via a filter.', 'total' ),
			];

			if ( (bool) \apply_filters( 'wpex_metaboxes', true ) ) {
				$settings[] = [
					'id'          => 'gallery_metabox_enable',
					'label'       => \esc_html__( 'Image Gallery Metabox', 'total' ),
					'icon'        => 'images-o',
					'category'    => \esc_html__( 'Custom Fields', 'total' ),
					'description' => \esc_html__( 'Enables the "Image Gallery" metabox in the post editor which allows you do define gallery images for your pages, posts, portfolio items and custom post types.', 'total' ),
				];
				$settings[] = [
					'id'          => 'theme_settings_metabox_core_fields_enable', // @todo rename and disable by default.
					'label'       => \esc_html__( 'Theme Settings Metabox', 'total' ),
					'icon'        => 'gear',
					'category'    => \esc_html__( 'Custom Fields', 'total' ),
					'description' => \esc_html__( 'Enables the "Theme Settings" metabox in the post editor that allows you to alter various Customizer settings on a per post basis. For most, this feature isn\'t necessary and can be disabled to slim things down. Disabling this feature will only remove the non post type specific settings.', 'total' ),
				];
				$settings[] = [
					'id'          => 'metabox_media_fields_enable',
					'label'       => \esc_html__( 'Post Media Custom Fields', 'total' ),
					'icon'        => 'photo-film',
					'category'    => \esc_html__( 'Custom Fields', 'total' ),
					'description' => \esc_html__( 'Enables the Media tab for defining your post video, audio or other media.', 'total' ),
				];
			}

			if ( apply_filters( 'wpex_card_metabox', true ) ) {
				$settings[] = [
					'id'          => 'card_metabox_enable',
					'label'       => \esc_html__( 'Card Settings Metabox', 'total' ),
					'icon'        => 'dashicons dashicons-admin-generic',
					'category'    => \esc_html__( 'Post Cards', 'total' ),
					'description' => \esc_html__( 'Enables the Card Settings metabox in the post editor that allows you to alter various card settings on a per post basis. Disabling this feature will only remove the fields from the post editor, any previously set values will still be applied.', 'total' ),
				];
			}
		}

		$settings[] = [
			'id'          => 'editor_styles_enable',
			'label'       => \esc_html__( 'Editor Styles', 'total' ),
			'icon'        => 'dashicons dashicons-editor-paste-word',
			'category'    => \esc_html__( 'Editor', 'total' ),
			'description' => \esc_html__( 'Loads custom styles when using the WordPress editor so that your fonts and certain styles match the live site.', 'total' ),
		];

		$settings[] = [
			'id'          => 'editor_formats_enable',
			'label'       => \esc_html__( 'Editor Formats', 'total' ),
			'icon'        => 'dashicons dashicons-editor-paste-word',
			'category'    => \esc_html__( 'Editor', 'total' ),
			'description' => \esc_html__( 'Enables the "Formats" button with some default and custom styles that you can use when adding text via the WordPress editor.', 'total' ),
		];

		$settings[] = [
			'id'          => 'editor_shortcodes_enable',
			'label'       => \esc_html__( 'Editor Shortcodes', 'total' ),
			'icon'        => 'dashicons dashicons-editor-paste-word',
			'category'    => \esc_html__( 'Editor', 'total' ),
			'description' => \esc_html__( 'Enables a new "Shortcodes" button in the WordPress editor with some exclusive theme shortcodes including line break, icon, current year, searchform, button, divider and spacing. You can easily add new shortcodes to this dropdown via a child theme.', 'total' ),
		];

		$settings[] = [
			'id'          => 'remove_emoji_scripts_enable',
			'label'       => \esc_html__( 'Remove WP Emojis', 'total' ),
			'icon'        => 'face-smile-o',
			'category'    => \esc_html__( 'Optimizations', 'total' ),
			'description' => \esc_html__( 'By default WordPress adds scripts to the admin and frontend to render custom WP emoji icons. This is unnecessary bloat and thus disabled by default.', 'total' ),
		];

		$settings[] = [
			'id'          => 'image_sizes_enable',
			'label'       => \esc_html__( 'Image Sizes', 'total' ),
			'icon'        => 'panorama',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Image Sizes where you can define custom cropping for the images on your site.', 'total' ),
		];

		$settings[] = [
			'id'          => 'page_animations_enable',
			'label'       => \esc_html__( 'Page Animations', 'total' ),
			'icon'        => 'wand-magic-sparkles',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a new tab under Appearance > Customize > General Theme Options > Page Animations where you can enable a loading icon when people visit your website as well define load in and load out animations for your site.', 'total' ),
		];

		if ( $total_theme_core_enabled ) {
			$settings[] = [
				'id'          => 'color_palette_enable',
				'label'       => \esc_html__( 'Color Palette', 'total' ),
				'icon'        => 'palette',
				'category'    => \esc_html__( 'Core', 'total' ),
				'description' => \esc_html__( 'Enables the Color Palette admin panel for registering your site\'s color palette.', 'total' ),
			];

			$settings[] = [
				'id'          => 'font_manager_enable',
				'label'       => \esc_html__( 'Font Manager', 'total' ),
				'icon'        => 'dashicons dashicons-editor-spellcheck',
				'category'    => \esc_html__( 'Fonts', 'total' ),
				'description' => \esc_html__( 'Enables the Font Manager admin panel for registering fonts for use on the site.', 'total' ),
			];
		}

		$settings[] = [
			'id'          => 'typography_enable',
			'label'       => \esc_html__( 'Typography Options', 'total' ),
			'icon'        => 'font',
			'category'    => \esc_html__( 'Fonts', 'total' ),
			'description' => \esc_html__( 'Enables a new tab at Appearance > Customize > Typography where you can define custom fonts and font styles for various parts of the site including the main body, menu, sidebar, footer, callout, topbar, etc.', 'total' ),
		];

		$settings[] = [
			'id'          => 'edit_post_link_enable',
			'label'       => \esc_html__( 'Page Edit Links', 'total' ),
			'icon'        => 'pen-ruler',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables edit links at the bottom of your posts and pages so you can quickly access the backend or front-end editor while logged into your site.', 'total' ),
		];

		$settings[] = [
			'id'          => 'import_export_enable',
			'label'       => \esc_html__( 'Import/Export Panel', 'total' ),
			'icon'        => 'file-import',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables a new admin screen at Theme Panel > Import/Export where you can quickly export or import Customizer settings from one site to the other. This is useful when switching to a child theme or if you wanted to copy a backup of your settings before making changes in the Customizer (you can copy the settings and paste them into a text file on your computer).', 'total' ),
		];

		if ( WPEX_VC_ACTIVE ) {

			$settings[] = [
				'id'          => 'wpb_slim_mode_enable',
				'label'       =>  \esc_html__( 'WPBakery Slim Mode', 'total' ),
				'icon'        => 'wpbakery',
				'category'    => 'WPBakery Builder',
				'disabled'    => true,
				'description' => \esc_html__( 'Exclusive theme feature that removes redundant WPBakery elements and their CSS to greatly slim things down. Enabling this option will also hide elements intended for dynamic templates and custom cards from showing up when editing posts and pages. If enabling on an existing site you will need to double to ensure you are not using any of the elements removed by this function and if so, replace them.', 'total' ),
			];

			$settings[] = [
				'id'          => 'section_templates_enable',
				'label'       =>  \esc_html__( 'WPBakery Patterns', 'total' ),
				'icon'        => 'wpbakery',
				'category'    => 'WPBakery Builder',
				'description' => \esc_html__( 'Enables theme Patterns for WPBakery which you can access when inserting templates.', 'total' ),
			];

			$settings[] = [
				'id'          => 'wpb_param_desc_enabled',
				'label'       =>  \esc_html__( 'WPBakery Field Info Tooltips', 'total' ),
				'icon'        => 'wpbakery',
				'category'    => 'WPBakery Builder',
				'description' => \esc_html__( 'By default various WPBakery page builder editor fields include extra info about the field to help you use it. Previously, these displayed below the fields, but in WPBakery v7.7 they added an info icon next to the field heading instead. If you don\'t like looking at all the info icons and you understand how to use theme you can disable this feature to clean up the editor.', 'total' ),
			];

			$settings[] = [
				'id'          => 'wpb_optimize_js_enable',
				'label'       =>  \esc_html__( 'WPBakery Optimized JS', 'total' ),
				'icon'        => 'wpbakery',
				'category'    => 'WPBakery Builder',
				'description' => \esc_html__( 'By default WPBakery loads their main javascript file and the jQuery library whenever any builder element is present on the page. By enabling this option the theme will check to make sure the scripts are absolutely necessary and if not will remove them from the page prior to rendering to speed up your site.', 'total' ),
			];

			$settings[] = [
				'id'          => 'wpb_full_width_css_enable',
				'label'       =>  \esc_html__( 'WPBakery CSS Stretch Sections & Rows', 'total' ),
				'icon'        => 'wpbakery',
				'category'    => 'WPBakery Builder',
				'description' => \esc_html__( 'The theme will use CSS (where possible) for your Section and Row Stretch settings instead of the default javascript based WPBakery stretching.', 'total' ),
			];

			if ( \get_theme_mod( 'visual_composer_theme_mode', true ) && ! totaltheme_call_static( 'Integration\WPBakery\Slim_Mode', 'is_enabled' ) ) {
				$settings[] = [
					'id'          => 'wpbakery_design_options_enable',
					'label'       =>  \esc_html__( 'WPBakery Design Options', 'total' ),
					'icon'        => 'wpbakery',
					'category'    => 'WPBakery Builder',
					'disabled'    => true,
					'description' => \esc_html__( 'Enables the "Design Options" tab in the WPBakery plugin settings which is disabled by default to prevent theme conflicts and keep the site slimmer.', 'total' ),
				];
			}

			$settings[] = [
				'id'          => 'visual_composer_theme_mode',
				'label'       =>  \esc_html__( 'WPBakery Theme Mode', 'total' ),
				'icon'        => 'wpbakery',
				'category'    => 'WPBakery Builder',
				'description' => \esc_html__( 'Enables "Theme Mode" for the WPBakery page builder plugin. This disables the License Tab under the WPbakery admin settings and hides some unnecessary notices and about pages.', 'total' ),
			];

		}

		if ( \totaltheme_is_integration_active( 'woocommerce' ) ) {
			$settings[] = [
				'id'          => 'woocommerce_integration',
				'label'       =>  \esc_html__( 'WooCommerce Integration', 'total' ),
				'icon'        => 'woocommerce',
				'category'    => 'WooCommerce',
				'description' => \esc_html__( 'Enables theme modifications for the WooCommerce plugin including custom styling and modifications. Disable for testing purposes or to use WooCommerce in it\'s native/vanilla form.', 'total' ),
			];
		}

		$settings[] = [
			'id'          => 'thumbnail_format_icons',
			'label'       => \esc_html__( 'Thumbnail Post Format Icons', 'total' ),
			'icon'        => 'icons',
			'category'    => \esc_html__( 'Core', 'total' ),
			'disabled'    => true,
			'description' => \esc_html__( 'Enables the display of a format icon on the front-end. For example if you publish a blog post that is set as an "Image" post format, the theme will display a little image icon over the Thumbnail (featured image) on entries and in the related posts section.', 'total' ),
		];

		$settings[] = [
			'id'          => 'header_image_enable',
			'label'       => \esc_html__( 'WP Header Image', 'total' ),
			'disabled'    => true,
			'icon'        => 'dashicons dashicons-format-image',
			'category'    => \esc_html__( 'Core', 'total' ),
			'description' => \esc_html__( 'Enables the WordPress core header image function under Appearance > Customize > Header Image which simply lets you set a custom background image for your theme header.', 'total' ),
		];

		$settings[] = [
			'id'          => 'disable_gs',
			'disabled'    => true,
			'label'       => \esc_html__( 'Remove Google Fonts', 'total' ),
			'icon'        => 'google',
			'category'    => \esc_html__( 'Fonts', 'total' ),
			'description' => \esc_html__( 'Disables all Google font options from the Customizer Typography panel and Total builder modules. This feature is primarily for users in Countries where Google hosted fonts are not allowed such as China.', 'total' ),
		];

		$settings[] = [
			'id'          => 'classic_styles_enable',
			'disabled'    => true,
			'label'       => \esc_html__( 'Classic Styles', 'total' ),
			'icon'        => 'clock-rotate-left',
			'category'    => \esc_html__( 'Deprecated', 'total' ),
			'description' => \esc_html__( 'Enables the theme\'s original typography, layout styles and deprecated classes. This setting is for customers updating from versions prior to 6.0 so their site isn\'t forced into the new modern standards.', 'total' ),
		];

		$settings[] = [
			'id'          => 'favicons_enable',
			'disabled'    => true,
			'label'       => \esc_html__( 'Favicons', 'total' ),
			'icon'        => 'folder-o',
			'category'    => \esc_html__( 'Deprecated', 'total' ),
			'description' => \esc_html__( 'Enables the deprecated Favicons Panel. We recommend setting your site icon using the newer core setting at Settings > General > Site Icon.', 'total' ),
		];

		$settings = (array) \apply_filters_deprecated( 'wpex_addons', [ $settings ], 'Total v5.20' );

		return $settings;
	}

	/**
	 * Runs on the on_admin_menu hook.
	 */
	public function on_admin_menu() {
		$this->register_menu_page();
		$this->register_general_submenu_page();
	}

	/**
	 * Runs on the on_admin_menu hook.
	 */
	public function register_menu_page() {
		$icon = 'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgMjAwIDIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGZpbGw9IiNmZmYiIGZpbGwtcnVsZT0iZXZlbm9kZCI+PHBhdGggZD0ibTY4LjMgMjEuNSAzMy43LTE5LjUgNDIuNSAyNC41IDQyLjQgMjQuNXYzOXoiLz48cGF0aCBkPSJtMTcuMiAxMjAuN3YtMjAuNy00OWw2MC4zIDM0Ljl6Ii8+PHBhdGggZD0ibTE4Ni45IDE0OS00Mi40IDI0LjUtNDIuNSAyNC41LTQyLjQtMjQuNS0xNS44LTkuMiA4NC44LTQ5eiIvPjwvZz48L3N2Zz4=';

		self::$hook_prefix = \add_menu_page(
			\esc_html__( 'Theme Panel', 'total' ),
			'Theme Panel', // menu title - can't be translated because it's used for the $hook prefix
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG,
			'',
			$icon,
			null
		);
	}

	/**
	 * Registers a new submenu page.
	 */
	public function register_general_submenu_page() {
		$hook_suffix = \add_submenu_page(
			'wpex-general',
			\esc_html__( 'General', 'total' ),
			\esc_html__( 'General', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG,
			[ $this, 'render_admin_page' ]
		);

		\add_action( "load-{$hook_suffix}", [ $this, 'on_load' ] );
	}

	/**
	 * Enqueue scripts.
	 */
	public function maybe_enqueue_scripts( $hook ) {
		if ( \str_contains( $hook, \WPEX_THEME_PANEL_SLUG . '-' ) ) {
			\wp_enqueue_style( 'totaltheme-admin-pages' );
			\wp_enqueue_script( 'totaltheme-admin-pages' );
		} elseif ( self::$hook_prefix === $hook ) {
			$this->enqueue_scripts();
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		\wp_enqueue_style( 'totaltheme-admin-pages' );
		\wp_enqueue_script( 'totaltheme-admin-pages' );

		// Panel specific.
		\wp_enqueue_style( 'totaltheme-admin-panel' );
		\wp_enqueue_script( 'totaltheme-admin-panel' );
	}

	/**
	 * Runs on load.
	 */
	public function on_load() {
		self::$admin_bar_title_tag = 'div';

		$this->admin_help_tab();
	}

	/**
	 * Add admin help tab.
	 */
	public function admin_help_tab() {
		$screen = \get_current_screen();

		if ( ! $screen ) {
			return;
		}

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_panel',
				'title'   => \esc_html__( 'Overview', 'total' ),
				'content' => '<p>Thank you for choosing the Total WordPress theme to power your WordPress website. Created by AJ Clarke, owner of <a href="https://www.wpexplorer.com/" target="_blank">WPExplorer.com &#8599;</a>, this theme is primarily a WPBakery Page Builder based theme but has been developed to also work well with Elementor or Gutenberg.</p><p>If you have any questions or issues please have a look at the theme documentation or reach out for support.</p>',
			]
		);

		ob_start();
			?><p><strong><?php esc_html_e( 'Useful Links', 'total' ); ?></strong></p>
			<p><a href="https://totalwptheme.com/docs/" target="_blank"><?php esc_html_e( 'Documentation', 'total' ); ?> &#8599;</a></p>
			<p><a href="https://totalwptheme.com/docs/changelog" target="_blank"><?php esc_html_e( 'Changelog', 'total' ); ?> &#8599;</a></p>
			<p><a href="https://themeforest.net/item/total-responsive-multipurpose-wordpress-theme/6339019/comments?ref=WPExplorer" target="_blank"><?php esc_html_e( 'Support Forum', 'total' ); ?> &#8599;</a></p>
			<p><a href="https://wpexplorer-themes.com/support/" target="_blank"><?php esc_html_e( 'Submit Ticket', 'total' ); ?> &#8599;</a></p>
			<p><a href="https://www.youtube.com/channel/UCXbt8I70QMYmsmYygeZStAQ" target="_blank"><?php esc_html_e( 'YouTube Channel', 'total' ); ?> &#8599;</a></p>
			<?php
		$sidebar_content = ob_get_clean();

		$screen->set_help_sidebar( $sidebar_content );
	}

	/**
	 * Register a setting and its sanitization callback.
	 */
	public function register_settings() {
		\register_setting(
			'wpex_theme_panel',
			'wpex_theme_panel',
			[
				'sanitize_callback' => [ $this, 'save_options' ],
				'default' => null,
			]
		);
	}

	/**
	 * Saves the options.
	 */
	public function save_options( $options ) {
		if ( empty( $options )
			|| ! is_array( $options )
			|| ! isset( $_POST['totaltheme-admin-theme-panel-nonce'] )
			|| ! wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-admin-theme-panel-nonce'] ) ), 'totaltheme-admin-theme-panel' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		foreach ( $this->get_settings() as $setting ) {
			if ( empty( $setting['id'] ) ) {
				continue;
			}

			$id = $setting['id'];

			// No need to save items that are enabled by default unless they have been disabled.
			$default = ! isset( $setting['disabled'] );

			// If default is true.
			if ( $default ) {
				if ( ! isset( $options[ $id ] ) ) {
					\set_theme_mod( $id, 0 ); // Disable option that is enabled by default
				} else {
					\remove_theme_mod( $id ); // Make sure its not in the theme_mods since it's already enabled
				}
			}

			// If default is false.
			elseif ( ! $default ) {
				if ( isset( $options[ $id ] ) ) {
					\set_theme_mod( $id, 1 ); // Enable option that is disabled by default
				} else {
					\remove_theme_mod( $id ); // Remove theme mod because it's disabled by default
				}
			}

		} // end addon saves.

		// Save Branding.
		$value = $options['theme_branding'] ?? '';
		if ( empty( $value ) ) {
			\set_theme_mod( 'theme_branding', 'disabled' );
		} else {
			if ( 'Total' === $value ) {
				\remove_theme_mod( 'theme_branding' );
			} else {
				\set_theme_mod( 'theme_branding', \sanitize_text_field( $value ) );
			}
		}

		// Save bundled plugins.
		$bundled_plugins = array( 'js_composer', 'templatera', 'revslider' );
		if ( \array_key_exists( 'bundled_plugins', $options ) ) {
			$value = $options['bundled_plugins'] ?: '';
			if ( $value && is_array( $value ) ) {
				$excluded_plugins = [];
				foreach ( $bundled_plugins as $bundled_plugin ) {
					if ( ! array_key_exists( $bundled_plugin, $value ) ) {
						$excluded_plugins[] = $bundled_plugin;
					}
				}
			} else {
				$excluded_plugins = array( 'js_composer', 'templatera', 'revslider' );
			}
			if ( $excluded_plugins ) {
				\set_theme_mod( 'excluded_plugins', $excluded_plugins );
			} else {
				\remove_theme_mod( 'excluded_plugins' );
			}
		} elseif ( array_key_exists( 'bundled_plugins_hidden', $options ) ) {
			\set_theme_mod( 'excluded_plugins', $bundled_plugins );
		}

		// Save Google tracking.
		$value = $options['google_property_id'] ?? '';
		if ( ! empty( $value ) ) {
			\set_theme_mod( 'google_property_id', \sanitize_text_field( $value ) );
		} else {
			\remove_theme_mod( 'google_property_id' );
		}

		// Save contact form email.
		$contact_form_mail_to_address = $options['contact_form_mail_to_address'] ?? '';
		if ( ! empty( $contact_form_mail_to_address ) && \is_email( $contact_form_mail_to_address ) ) {
			\set_theme_mod( 'contact_form_mail_to_address', \sanitize_text_field( $contact_form_mail_to_address ) );
		} elseif ( \array_key_exists( 'contact_form_mail_to_address', $options ) ) {
			\remove_theme_mod( 'contact_form_mail_to_address' );
		}

		$contact_form_mail_to_address_meta_key = $options['contact_form_mail_to_address_meta_key'] ?? '';
		if ( ! empty( $contact_form_mail_to_address_meta_key ) ) {
			\set_theme_mod( 'contact_form_mail_to_address_meta_key', \sanitize_text_field( $contact_form_mail_to_address_meta_key ) );
		} elseif ( \array_key_exists( 'contact_form_mail_to_address_meta_key', $options ) ) {
			\remove_theme_mod( 'contact_form_mail_to_address_meta_key' );
		}

		// Save reCAPTCHA keys (these are stored in the options table).
		$recaptcha_keys = [];

		if ( ! empty( $options[ 'recaptcha_site_key'] ) ) {
			$recaptcha_keys['site_key'] = \sanitize_text_field( $options[ 'recaptcha_site_key'] );
		}

		if ( ! empty( $options[ 'recaptcha_secret_key'] ) ) {
			$recaptcha_keys['secret_key'] = \sanitize_text_field( $options[ 'recaptcha_secret_key'] );
		}

		if ( $recaptcha_keys ) {
			\update_option( 'wpex_recaptcha_keys', $recaptcha_keys, false );
		} else {
			\delete_option( 'wpex_recaptcha_keys' );
		}

		return null; //prevent options from saving.
	}

	/**
	 * Return theme panel tabs navigation.
	 */
	public function panel_tabs() {
		?>
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo \esc_url( \admin_url( 'admin.php?page=' . \WPEX_THEME_PANEL_SLUG ) ); ?>" class="nav-tab nav-tab-active"><span class="dashicons dashicons-admin-settings" aria-hidden="true"></span><?php \esc_html_e( 'Features', 'total' ); ?></a>
			<?php if ( totaltheme_call_static( 'Admin\License_Panel', 'is_enabled' ) ) { ?>
				<a href="<?php echo \esc_url( \admin_url( 'admin.php?page=wpex-panel-theme-license' ) ); ?>" class="nav-tab wpex-theme-license"><span class="dashicons dashicons-<?php echo \totaltheme_get_license() ? 'yes-alt' : 'warning'; ?>" aria-hidden="true"></span><?php \esc_html_e( 'License', 'total' ); ?></a>
			<?php } ?>
			<?php if ( \get_theme_mod( 'demo_importer_enable', true ) && \class_exists( '\TotalThemeCore\Plugin' ) ) { ?>
				<a href="<?php echo \esc_url( \admin_url( 'admin.php?page=wpex-panel-demo-importer' ) ); ?>" class="nav-tab"><span class="dashicons dashicons-download" aria-hidden="true"></span><?php \esc_html_e( 'Demo Import', 'total' ); ?></a>
			<?php } ?>
			<a href="<?php echo \esc_url( \admin_url( 'customize.php' ) ); ?>" class="nav-tab"><span class="dashicons dashicons-admin-appearance" aria-hidden="true"></span><?php \esc_html_e( 'Customize', 'total' ); ?></a>
		</h2>
		<?php
	}

	/**
	 * Renders the admin page.
	 */
	public function render_admin_page() {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		// remove possible bloat since we are saving options in theme_mods now.
		\delete_option( 'wpex_theme_panel' );

		$this->enqueue_scripts();

		?>

		<div class="wpex-theme-panel wpex-main-panel wpex-clr">

			<?php if ( \get_option( 'active_theme_license_dev' ) ) { ?>
				<p></p>
				<div class="wpex-notice wpex-warning">
					<p><?php \esc_html_e( 'Your site is currently active as a development environment.', 'total' ); ?></p>
				</div>
			<?php } ?>

			<div class="wrap wpex-theme-panel__wrap">

				<div class="wpex-theme-panel__about">

					<div class="wpex-theme-panel__about-text">

						<h1><?php \esc_html_e( 'Theme Options Panel', 'total' ); ?></h1>

						<p><?php echo \sprintf( \esc_html__( 'Here you can enable or disable various core features of the theme in order to keep the site optimized for your needs. Visit the %sCustomizer%s to access all theme settings.', 'total' ), '<a href="' . \esc_url( \admin_url( '/customize.php' ) ) . '">', '</a>' ); ?></p>

					</div><!-- .wpex-theme-panel__about-text -->

					<div class="wpex-theme-panel__badge">
						<div class="wpex-theme-panel__badge-svg"><?php echo \totaltheme_get_svg( 'totaltheme' ); ?></div>
						<div class="wpex-theme-panel__badge-version"><?php echo \esc_html__( 'Version', 'total' ) . ' <span class="wpex-version">' . \WPEX_THEME_VERSION . '</span>'; ?></div>
					</div>

				</div><!-- .wpex-theme-panel__about -->

				<?php $this->panel_tabs(); ?>

				<div id="wpex-theme-panel-content">

					<ul class="wpex-theme-panel__sublinks wpex-theme-panel__sort">
						<li><strong><?php \esc_html_e( 'Setting status:', 'total' ); ?></strong> &#124; </li>
						<li><a href="#" data-filter="status-all" class="wpex-theme-panel__sort-item"><?php \esc_html_e( 'All', 'total' ); ?></a> &#124; </li>
						<li><a href="#" data-filter="enabled" class="wpex-theme-panel__sort-item"><?php \esc_html_e( 'Enabled', 'total' ); ?></a> &#124; </li>
						<li><a href="#" data-filter="disabled" class="wpex-theme-panel__sort-item"><?php \esc_html_e( 'Disabled', 'total' ); ?></a></li>
					</ul>

					<div class="wpex-theme-panel__savenag">
						<p><?php echo \wp_kses_post( __( 'Don\'t forget to <a href="javascript:void(0)">save your changes</a>', 'total' ) ); ?></p>
					</div>

					<form method="post" action="options.php" class="wpex-theme-panel__form" autocomplete="off">
						<div class="wpex-theme-panel__form-grid">
							<div class="wpex-theme-panel__form-main"><?php $this->get_features(); ?></div>
							<div class="wpex-theme-panel__form-sidebar"><?php $this->get_widgets(); ?></div>
						</div>
						<?php \settings_fields( 'wpex_theme_panel' ); ?>
						<?php \wp_nonce_field( 'totaltheme-admin-theme-panel', 'totaltheme-admin-theme-panel-nonce' ); ?>
						<?php \submit_button(); ?>
					</form>

					</div>

			</div>

		</div>
	<?php
	}

	/**
	 * Display admin features.
	 */
	protected function get_features() {
		?>

		<div class="wpex-theme-panel__settings">

			<?php
			// Loop through settings.
			foreach ( $this->get_settings() as $setting ) :
				if ( empty( $setting['id'] ) ) {
					continue;
				}

				$id      = $setting['id'];
				$default = ! isset( $setting['disabled'] );
				$label   = $setting['label'] ?? '';
				$icon    = $setting['icon'] ?? '';

				// Get theme option.
				$theme_mod = \get_theme_mod( $id, $default );

				// Is enabled?
				$enabled = ! empty( $theme_mod ) ? true : false;

				// Get category and sanitize.
				$category = $setting['category'] ?? ' other';

				// Classes.
				$classes = 'wpex-theme-panel__setting';
				$status  = $enabled ? 'enabled' : 'disabled';
				?>

				<div id="wpex-theme-panel-<?php echo \esc_attr( $id ); ?>" class="<?php echo \esc_attr( $classes ); ?>" data-status="<?php echo \esc_attr( $status ); ?>" data-category="<?php echo \sanitize_key( $category ); ?>">

					<div class="wpex-theme-panel__setting-heading">
						<div class="wpex-theme-panel__setting-heading__inner">
							<input type="checkbox" id="wpex_theme_panel[<?php echo \esc_attr( $id ); ?>]" name="wpex_theme_panel[<?php echo \esc_attr( $id ); ?>]" value="<?php echo \esc_attr( $theme_mod ); ?>" <?php \checked( $theme_mod, true ); ?> class="wpex-checkbox">

							<label for="wpex_theme_panel[<?php echo \esc_attr( $id ); ?>]"><?php
								if ( $icon ) {
									if ( \str_starts_with( $icon, 'dashicons' ) ) {
										echo '<span class="'. \esc_attr( $icon ) . '"></span>';
									} else {
										echo \totaltheme_get_icon( $icon );
									}
								}
								echo '<span>' . \esc_html( $label ) . '</span>';
							?></label>
						</div>
						<?php if ( ! empty( $setting['description'] ) ) { ?>
							<button type="button" aria-expanded="false" class="wpex-theme-panel__setting-toggle" aria-label="<?php \esc_html_e( 'Show setting description', 'total' ); ?>" role="button"><span aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path></svg></span></button>
						<?php } ?>
					</div>

					<?php if ( ! empty( $setting['description'] ) ) { ?>
						<div class="wpex-theme-panel__setting-info"><?php
							echo \wp_kses_post( $setting['description'] );
						?></div>
					<?php } ?>

				</div>

			<?php endforeach; ?>

		</div>
	<?php }

	/**
	 * Display admin widgets.
	 */
	protected function get_widgets() {
		$this->sort_widget();
		$this->branding_widget();
		if ( \get_theme_mod( 'recommend_plugins_enable', true ) ) {
			$this->bundled_plugins_widget();
		}
		$this->tracking_widget();
		if ( \shortcode_exists( 'vcex_contact_form' ) ) {
			$this->contact_email();
		}
		$this->recaptcha_widget();
		if ( \current_user_can( 'switch_themes' ) ) {
			$this->system_status_widget();
		}
	}

	/**
	 * Branding widget.
	 */
	protected function branding_widget() { ?>
		<div class="wpex-theme-panel__widget">
			<div class="wpex-theme-panel__widget-heading wpex-theme-panel__widget-heading--hidden">
				<p><?php \esc_html_e( 'Branding', 'total' ); ?></p>
				<?php $this->widget_heading_toggle_button(); ?>
			</div>
			<div class="wpex-theme-panel__widget-inner">
				<p class="wpex-theme-panel__widget-desc"><?php \esc_html_e( 'Used in widgets and builder blocks. Leave empty to disable.', 'total' ); ?></p>
				<?php
				$value = \get_theme_mod( 'theme_branding', 'Total' );
				$branding_disabled = ( 'disabled' === $value );
				$value = ( $branding_disabled || empty( $value ) ) ? '' : \wp_strip_all_tags( $value );
				?>
				<label class="screen-reader-text" for="wpex_theme_panel[theme_branding]"><?php \esc_html_e( 'Branding', 'total' ); ?></label>
				<input id="wpex_theme_panel[theme_branding]" type="text" name="wpex_theme_panel[theme_branding]" value="<?php echo \esc_attr( $value ); ?>" placeholder="<?php echo \esc_attr_e( 'Disabled', 'total' ); ?>">
			</div>
		</div>
	<?php }

	/**
	 * Bundled Plugins
	 */
	protected function bundled_plugins_widget() { ?>
		<div class="wpex-theme-panel__widget">
			<div class="wpex-theme-panel__widget-heading wpex-theme-panel__widget-heading--hidden">
				<p><?php \esc_html_e( 'Bundled Plugins', 'total' ); ?></p>
				<?php $this->widget_heading_toggle_button(); ?>
			</div>
			<div class="wpex-theme-panel__widget-inner">
				<p class="wpex-theme-panel__widget-desc"><?php \esc_html_e( 'Select which plugins you will use with the theme. Disable any to prevent update/installation notifications.', 'total' ); ?></p>
				<?php
				$excluded_plugins = \get_theme_mod( 'excluded_plugins' );
				if ( ! is_array( $excluded_plugins ) ) {
					$excluded_plugins = [];
				}
				?>
				<fieldset>
					<legend class="screen-reader-text"><?php \esc_html_e( 'Allowed Plugins', 'total' ); ?></legend>
					<div>
						<input type="checkbox" id="bundled_plugins--js_composer" name="wpex_theme_panel[bundled_plugins][js_composer]" <?php \checked( \in_array( 'js_composer', $excluded_plugins ), false ); ?>>
						<label for="bundled_plugins--js_composer">WPBakery Page Builder</label>
					</div>
					<div>
						<input type="checkbox" id="bundled_plugins--templatera" name="wpex_theme_panel[bundled_plugins][templatera]" <?php \checked( \in_array( 'templatera', $excluded_plugins ), false ); ?>>
						<label for="bundled_plugins--templatera">Templatera</label>
					</div>
					<div>
						<input type="checkbox" id="bundled_plugins--revslider" name="wpex_theme_panel[bundled_plugins][revslider]" <?php \checked( \in_array( 'revslider', $excluded_plugins ), false ); ?>>
						<label for="bundled_plugins--revslider">Slider Revolution</label>
					</div>
				</fieldset>
				<input type="hidden" name="wpex_theme_panel[bundled_plugins_hidden]">
			</div>
		</div>
	<?php }


	/**
	 * Tracking widget.
	 */
	protected function tracking_widget() { ?>
		<div class="wpex-theme-panel__widget">
			<div class="wpex-theme-panel__widget-heading wpex-theme-panel__widget-heading--hidden">
				<p><?php \esc_html_e( 'Google Analytics ID', 'total' ); ?></p>
				<?php $this->widget_heading_toggle_button(); ?>
			</div>
			<div class="wpex-theme-panel__widget-inner">
				<p class="wpex-theme-panel__widget-desc"><?php \esc_html_e( 'Enter your Google analytics property ID so the theme can add the tracking code for you.', 'total' ); ?></p>
				<label for="wpex_theme_panel[google_property_id]" class="screen-reader-text" for="wpex_theme_panel[google_property_id]"><?php \esc_html_e( 'Google Analytics ID', 'total' ); ?></label>
				<input id="wpex_theme_panel[google_property_id]" type="text" name="wpex_theme_panel[google_property_id]" value="<?php echo \esc_attr( \get_theme_mod( 'google_property_id' ) ); ?>" placeholder="G-XXXXXXX" autocomplete="off">
				<small><?php \esc_html_e( 'Separate property ID\'s with a comma to use multiple.', 'total' ); ?></small>
			</div>
		</div>
	<?php }

	/**
	 * Contact Form.
	 */
	protected function contact_email() { ?>
		<div class="wpex-theme-panel__widget">
			<div class="wpex-theme-panel__widget-heading wpex-theme-panel__widget-heading--hidden">
				<p><?php \esc_html_e( 'Contact Form', 'total' ); ?></p>
				<?php $this->widget_heading_toggle_button(); ?>
			</div>
			<div class="wpex-theme-panel__widget-inner">
				<div class="wpex-theme-panel__widget-setting">
					<label class="wpex-theme-panel__widget-styled-label" for="wpex_theme_panel[contact_form_mail_to_address]"><?php esc_html_e( 'Sent to Email Address', 'total' ); ?></label>
					<p class="wpex-theme-panel__widget-desc"><?php \esc_html_e( 'Email address to use for the theme\'s built-in contact form element.', 'total' ); ?></p>
					<input id="wpex_theme_panel[contact_form_mail_to_address]" type="text" name="wpex_theme_panel[contact_form_mail_to_address]" value="<?php echo \esc_attr( \get_theme_mod( 'contact_form_mail_to_address' ) ); ?>" placeholder="<?php echo \esc_attr( \get_bloginfo( 'admin_email' ) ); ?>">
				</div>
				<div class="wpex-theme-panel__widget-setting">
					<label class="wpex-theme-panel__widget-styled-label" for="wpex_theme_panel[contact_form_mail_to_address_meta_key]"><?php esc_html_e( 'Send to Email Address Custom Field', 'total' ); ?></label>
					<p class="wpex-theme-panel__widget-desc"><?php \esc_html_e( 'Optional custom field name to use for the send to email address. If undefined, the email address above will be used as a fallback.', 'total' ); ?></p>
					<input id="wpex_theme_panel[contact_form_mail_to_address_meta_key]" type="text" name="wpex_theme_panel[contact_form_mail_to_address_meta_key]" value="<?php echo \esc_attr( \get_theme_mod( 'contact_form_mail_to_address_meta_key' ) ); ?>">
				</div>
			</div>
		</div>
	<?php }

	/**
	 * reCAPTCHA widget.
	 */
	protected function recaptcha_widget() {
		$site_key   = \wpex_get_recaptcha_keys( 'site' );
		$secret_key = \wpex_get_recaptcha_keys( 'secret' );
		?>

		<div class="wpex-theme-panel__widget">
			<div class="wpex-theme-panel__widget-heading wpex-theme-panel__widget-heading--hidden">
				<p><?php \esc_html_e( 'reCAPTCHA Keys', 'total' ); ?></p>
				<?php $this->widget_heading_toggle_button(); ?>
			</div>
			<div class="wpex-theme-panel__widget-inner">
				<p class="wpex-theme-panel__widget-desc"><?php \printf( \esc_html__( 'Enter your Google %sreCAPTCHA v3 keys%s for use with theme elements such as the Contact Form.', 'total' ), '<a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="nofollow noopener noreferrer">', ' &#8599;</a>' ); ?></p>
				<p><label class="screen-reader-text" for="wpex_theme_panel[recaptcha_site_key]">Google reCAPTCHA <?php echo \esc_attr__( 'Site Key', 'total' ); ?></label><input id="wpex_theme_panel[recaptcha_site_key]" type="text" name="wpex_theme_panel[recaptcha_site_key]" value="<?php echo \esc_attr( $site_key ); ?>" placeholder="<?php echo \esc_attr__( 'Site Key', 'total' ); ?>" autocomplete="off"></p>
				<p><label class="screen-reader-text" for="wpex_theme_panel[recaptcha_secret_key]">Google reCAPTCHA <?php echo \esc_attr__( 'Secret Key', 'total' ); ?></label><input id="wpex_theme_panel[recaptcha_secret_key]" type="text" name="wpex_theme_panel[recaptcha_secret_key]" value="<?php echo \esc_attr( $secret_key ); ?>" placeholder="<?php echo \esc_attr__( 'Secret Key', 'total' ); ?>" autocomplete="off"></p>
			</div>
		</div>
	<?php }

	/**
	 * Sort widget.
	 */
	protected function sort_widget() { ?>
		<div class="wpex-theme-panel__widget">
			<div class="wpex-theme-panel__widget-heading">
				<p><?php \esc_html_e( 'Sort Features', 'total' ); ?></p>
			</div>

			<div class="wpex-theme-panel__widget-inner"><?php
				$categories = \wp_list_pluck( $this->get_settings(), 'category' );
				$categories = \array_unique( $categories );
				\asort( $categories );
				?>

				<ul class="wpex-theme-panel__sort wpex-theme-panel__sort--cats">
					<li><a href="#" data-filter="all" class="wpex-theme-panel__sort-item<?php echo empty( $_GET['filter'] ) ? ' wpex-theme-panel__sort-item--active' : ''; ?>"><?php \esc_html_e( 'All', 'total' ); ?></a></li>
					<?php
					// Loop through cats.
					foreach ( $categories as $key => $category ) :
						$sanitize_category = \sanitize_key( $category ); ?>
						<li><a href="#" data-filter="<?php echo \esc_attr( $sanitize_category ); ?>" class="wpex-theme-panel__sort-item"><?php echo \wp_strip_all_tags( $category ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php }

	/**
	 * System Status widget.
	 */
	protected function system_status_widget() { ?>
		<div class="wpex-theme-panel__widget">

			<div class="wpex-theme-panel__widget-heading"><p><?php esc_html_e( 'System Status', 'total' ); ?></p></div>

			<div class="wpex-theme-panel__widget-inner wpex-system-status">

				<?php
				$mem_limit = \ini_get( 'memory_limit' );
				$mem_limit_bytes = \wp_convert_hr_to_bytes( $mem_limit );
				$enough = $mem_limit_bytes < 268435456 ? false : true;
				$val_class = $enough ? 'wpex-good' : 'wpex-bad'; ?>
				<div class="wpex-system-status__item">
					<?php \esc_html_e( 'Memory Limit', 'total' ); ?>
					<span class="wpex-system-status__val <?php echo \esc_html( $val_class ); ?>"><?php echo esc_html( $mem_limit ); ?></span>
					<span class="wpex-system-status__rec"><?php \esc_html_e( 'Recommended: 256M', 'total' ); ?></span>
				</div>

				<?php
				$max_execute = \ini_get( 'max_execution_time' );
				$enough = $max_execute < 300 ? false : true;
				$val_class = $enough ? 'wpex-good' : 'wpex-bad'; ?>
				<div class="wpex-system-status__item">
					<?php \esc_html_e( 'Max Execution Time', 'total' ); ?>
					<span class="wpex-system-status__val <?php echo \esc_html( $val_class ); ?>"><?php echo \esc_html( $max_execute ); ?></span>
					<br>
					<span class="wpex-system-status__rec"><?php \esc_html_e( 'Recommended: 300', 'total' ); ?></span>
				</div>

				<?php
				$post_max_size = \ini_get( 'post_max_size' );
				$post_max_size_byte = \wp_convert_hr_to_bytes( $post_max_size );
				$enough = $post_max_size_byte < 33554432 ? false : true;
				$val_class = $enough ? 'wpex-good' : 'wpex-bad'; ?>
				<div class="wpex-system-status__item">
					<?php \esc_html_e( 'Max Post Size', 'total' ); ?>
					<span class="wpex-system-status__val <?php echo \esc_html( $val_class ); ?>"><?php echo \esc_html( $post_max_size ); ?></span>
					<br>
					<span class="wpex-system-status__rec"><?php \esc_html_e( 'Recommended: 32M', 'total' ); ?></span>
				</div>

				<?php
				$input_vars = \ini_get( 'max_input_vars' );
				$enough = $input_vars < 1000 ? false : true;
				$val_class = $enough ? 'wpex-good' : 'wpex-bad'; ?>
				<div class="wpex-system-status__item wpex-system-status__item--last">
					<?php \esc_html_e( 'Max Input Vars', 'total' ); ?>
					<span class="wpex-system-status__val <?php echo \esc_html( $val_class ); ?>"><?php echo \esc_html( $input_vars ); ?></span>
					<br>
					<span class="wpex-system-status__rec"><?php \esc_html_e( 'Recommended: 1000', 'total' ); ?></span>
				</div>

				<div class="wpex-theme-panel__widget-footer"><a href="https://wordpress.org/about/requirements/" target="_blank"><?php \esc_html_e( 'WordPress requirements', 'total' ); ?> &#8599;</a></div>

			</div>

		</div>
	<?php }

	/**
	 * Renders widget heading toggle button.
	 */
	protected function widget_heading_toggle_button() {
		?>
		<button type="button" aria-expanded="false" class="wpex-theme-panel__widget-heading-toggle" aria-label="<?php \esc_html_e( 'Show setting description', 'total' ); ?>" role="button"><span aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path></svg></span></button>
		<?php
	}

	/**
	 * Returns link to a specific theme panel section.
	 */
	public static function get_dashicon_class( string $icon ): string {
		return str_starts_with( $icon, 'dashicons-' ) ? "dashicons {$icon}" : "dashicons dashicons-{$icon}";
	}

	/**
	 * Returns link to a specific theme panel section.
	 */
	public static function get_setting_link( $setting = '' ) {
		$panel_slug = \WPEX_THEME_PANEL_SLUG;
		return \esc_url( \admin_url( "admin.php?page={$panel_slug}&filter={$setting}" ) );
	}

	/**
	 * Enables the admin bar.
	 */
	public static function enable_admin_bar(): void {
		self::$admin_bar_enabled = true;
	}

	/**
	 * Show settings saved notice.
	 */
	public static function set_admin_bar_updated_notice( string $notice_type ): void {
		self::$admin_bar_updated_notice = $notice_type;
	}

	/**
	 * Check if the admin bar should display.
	 */
	protected function show_admin_bar(): bool {
		if ( ! \is_null( self::$admin_bar_enabled ) ) {
			return self::$admin_bar_enabled;
		}

		$check = false;

		global $current_screen;
		$page_id = $current_screen->id ?? '';

		if ( $page_id && \str_contains( $page_id, \WPEX_THEME_PANEL_SLUG ) ) {
			$check = true;
		} elseif ( ! $check && ! empty( $current_screen->base ) && 'edit' === $current_screen->base ) {
			$types = [
				'wpex_templates',
				'wpex_color_palette',
				'wpex_font',
				'wpex_card',
				'wpex_widget_area',
			];
			foreach ( $types as $type ) {
				if ( "edit-{$type}" === $page_id ) {
					$check = true;
					break;
				}
			}
		}

		self::$admin_bar_enabled = (bool) $check;

		return self::$admin_bar_enabled;
	}

	/**
	 * Checks if currently on a post type edit screen/
	 */
	protected function is_post_type_edit_screen(): bool {
		global $current_screen;
		return ( ! empty( $current_screen->base ) && 'edit' === $current_screen->base );
	}

	/**
	 * Returns title tag for admin bar.
	 */
	protected function get_admin_bar_title_tag(): string {
		$tag = self::$admin_bar_title_tag;

		if ( $this->is_post_type_edit_screen() ) {
			$tag = 'div';
		}

		return $tag;
	}

	/**
	 * Inserts the Total admin bar to the theme admin pages.
	 */
	public function admin_bar() {
		if ( ! $this->show_admin_bar() ) {
			return;
		}

		\wp_enqueue_style( 'totaltheme-admin-bar' );
		\wp_enqueue_style( 'totaltheme-admin-pages' );

		\ob_start();

		$branding    = \wpex_get_theme_branding();
		$brand       = ( 'diabled' !== $branding ) ? $branding : 'Total';
		$admin_color = (string) \get_user_option( 'admin_color' ) ?: 'default';
		$title_tag   = $this->get_admin_bar_title_tag();

		?>

			<div class="totaltheme-admin-bar totaltheme-admin-bar--<?php echo \esc_attr( $admin_color ); ?>">
				<div class="totaltheme-admin-bar__inner">
					<div class="totaltheme-admin-bar__branding">
						<div class="totaltheme-admin-bar__logo"><?php
							echo \totaltheme_get_svg( 'totaltheme', 18 );
						?></div>
						<<?php echo \tag_escape( $title_tag ); ?> class="totaltheme-admin-bar__title">
							<?php if ( $branding ) { ?>
								<?php echo \esc_html( $branding ); ?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" height="10" width="10"><g><path d="M0,0h24v24H0V0z" fill="none"/></g><g><polygon points="6.23,20.23 8,22 18,12 8,2 6.23,3.77 14.46,12"/></g></svg>
							<?php } ?>
						<?php echo \esc_html( \get_admin_page_title() ); ?></<?php echo \tag_escape( $title_tag ); ?>>
					</div>
					<?php
					// Show saved notice.
					if ( ( isset( $_GET['settings-updated'] ) && \wp_validate_boolean( $_GET['settings-updated'] ) )
						|| isset( $_POST['wpex_disabled_customizer_panels'] )
						|| 'success' === self::$admin_bar_updated_notice
					) { ?>
						<div class="totaltheme-admin-bar__alert totaltheme-admin-bar__alert--success"><span class="dashicons dashicons-yes-alt"></span><?php \esc_html_e( 'Settings Saved', 'total' ); ?></div>
					<?php } ?>
				</div>
			</div>

		<?php

		echo \ob_get_clean();
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
