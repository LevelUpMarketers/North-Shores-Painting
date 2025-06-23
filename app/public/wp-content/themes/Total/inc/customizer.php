<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * WP Customizer Support.
 */
class Customizer {

	/**
	 * Customizer sections.
	 */
	public $sections = null;

	/**
	 * CSS settings.
	 */
	protected static $css_settings = null;

	/**
	 * Is postMessage enabled.
	 */
	protected $enable_postMessage = true;

	/**
	 * Check if currently in customizer preview mode.
	 */
	protected $is_customize_preview = null;

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of our class.
	 */
	public static function instance() {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->initialize();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Initialize
	 */
	public function initialize(): void {
		\define( 'WPEX_CUSTOMIZER_DIR', WPEX_INC_DIR . 'customizer/' );
		$this->init_hooks();
		$this->load_customizer_manager();
	}

	/**
	 * Hook into actions and filters.
	 */
	public function init_hooks(): void {
		if ( $this->is_customize_preview() || \wpex_is_request( 'frontend' ) ) {
			\add_action( 'wp_loaded', [ $this, 'add_to_customizer' ], 1 );
			\add_action( 'init', [ $this, 'inline_css' ] );
		}
		if ( $this->is_customize_preview() || ( \is_admin() && \wp_doing_ajax() ) ) {
			\add_action( 'wp_ajax_totaltheme_customize_create_template', [ $this, 'ajax_create_template' ] );
		}
		if ( $this->is_customize_preview() ) {
			if ( \class_exists( 'TotalTheme\Helpers\Icon_Select', true ) ) {
				\add_action( 'customize_controls_print_footer_scripts', [ 'TotalTheme\Helpers\Icon_Select', 'render_modal' ] );
			}
		}
	}

	/**
	 * Include Customizer Manager.
	 */
	public function load_customizer_manager(): void {
		if ( \get_theme_mod( 'customizer_panel_enable', true ) && \is_admin() ) {
			// @note can't use totaltheme_init_class() because it's a child class that extends this class which
			// has an instance()
			if ( class_exists( 'TotalTheme\Customizer\Manager' ) ) {
				new Customizer\Manager;
			}
		}
	}

	/**
	 * Check if currently in customize mode.
	 */
	protected function is_customize_preview(): bool {
		if ( null === $this->is_customize_preview ) {
			$this->is_customize_preview = (bool) \is_customize_preview();
		}
		return $this->is_customize_preview;
	}

	/**
	 * Define panels.
	 */
	public function panels(): array {
		$panels = [];

		$panels['global_styles'] = [
			'title' => \esc_html__( 'Global Styles', 'total' ),
			'icon'  => '\f100',
		];

		$panels['general'] = [
			'title' => \esc_html__( 'General Theme Options', 'total' ),
		];

		$panels['layout'] = [
			'title' => \esc_html__( 'Layout', 'total' ),
			'icon' => '\f535',
		];

		if ( \get_theme_mod( 'typography_enable', true ) ) {
			$panels['typography'] = [
				'title' => \esc_html__( 'Typography', 'total' ),
				'icon'  => '\f216',
			];
		}

		$panels['togglebar'] = [
			'title'      => \esc_html__( 'Toggle Bar', 'total' ),
			'is_section' => true,
			'icon'       => '\f132',
		];

		$panels['topbar'] = [
			'title' => \esc_html__( 'Top Bar', 'total' ),
			'icon'  => '\f157',
		];

		$panels['header'] = [
			'title' => \esc_html__( 'Header', 'total' ),
			'icon'  => '\f175',
		];

		$panels['sidebar'] = [
			'title'      => \esc_html__( 'Sidebar', 'total' ),
			'is_section' => true,
			'icon'       => '\f135',
		];

		$panels['blog'] = [
			'title' => \esc_html__( 'Blog', 'total' ),
			'icon'  => '\f109',
		];

		if ( \totaltheme_call_static( 'Portfolio\Post_Type', 'is_enabled' ) ) {
			$panels['portfolio'] = [
				'title' => \totaltheme_call_static( 'Portfolio\Post_Type', 'get_name' ),
				'icon'  => '\\' . $this->_get_dashicon_unicode( \totaltheme_call_static( 'Portfolio\Post_Type', 'get_menu_icon' ) ),
			];
		}

		if ( \totaltheme_call_static( 'Staff\Post_Type', 'is_enabled' ) ) {
			$panels['staff'] = [
				'title' => \totaltheme_call_static( 'Staff\Post_Type', 'get_name' ),
				'icon'  => '\\' . $this->_get_dashicon_unicode( \totaltheme_call_static( 'Staff\Post_Type', 'get_menu_icon' ) ),
			];
		}

		if ( \totaltheme_call_static( 'Testimonials\Post_Type', 'is_enabled' ) ) {
			$panels['testimonials'] = [
				'title' => \totaltheme_call_static( 'Testimonials\Post_Type', 'get_name' ),
				'icon'  => '\\' . $this->_get_dashicon_unicode( \totaltheme_call_static( 'Testimonials\Post_Type', 'get_menu_icon' ) ),
			];
		}

		$panels['callout'] = [
			'title' => \esc_html__( 'Callout', 'total' ),
			'icon'  => '\f488',
		];

		if ( totaltheme_call_static( 'Footer\Core', 'is_custom' ) && ! get_theme_mod( 'footer_builder_footer_widgets', false ) ) {
			$panels['footer_widgets'] = [
				'title'      => \esc_html__( 'Footer', 'total' ),
				'is_section' => true,
				'file_name'  => 'footer_builder',
				'icon'       => '\f209',
			];
		} else {
			$panels['footer_widgets'] = [
				'title'      => \esc_html__( 'Footer Widgets', 'total' ),
				'is_section' => true,
				'icon'       => '\f209',
			];
		}

		$panels['footer_bottom'] = [
			'title'      => \esc_html__( 'Footer Bottom', 'total' ),
			'is_section' => true,
			'icon'       => '\f209',
		];

		return (array) \apply_filters( 'wpex_customizer_panels', $panels );
	}

	/**
	 * Returns array of enabled panels.
	 */
	public function enabled_panels(): array {
		$panels = $this->panels();
		$disabled_panels = (array) \get_option( 'wpex_disabled_customizer_panels', [] );
		foreach ( $panels as $key => $val ) {
			if ( \in_array( $key, $disabled_panels ) ) {
				unset( $panels[ $key ] );
			}
		}
		return $panels ?: [];
	}

	/**
	 * Check if customizer section is enabled.
	 */
	public function is_section_enabled( array $section, string $section_id ): bool {
		$section_panel = ! empty( $section[ 'panel' ] ) ? $section[ 'panel' ] : $section_id;
		if ( $section_panel ) {
			$enabled_panels = $this->enabled_panels();
			$section_panel = \str_replace( 'wpex_', '', $section_panel );
			if ( empty( $enabled_panels[ $section_panel ] ) ) {
				return false;
			}
		}
		return true; // all enabled by default
	}

	/**
	 * Initialize customizer settings.
	 */
	public function add_to_customizer(): void {
		\add_action( 'customize_controls_enqueue_scripts', [ $this, 'customize_controls_enqueue_scripts' ] );
		\add_action( 'customize_controls_print_styles', [ $this, 'customize_controls_print_styles' ] );
		\add_action( 'customize_register', [ $this, 'register_control_types' ] );
		\add_action( 'customize_register', [ $this, 'remove_core_sections' ], 11 );
		\add_action( 'customize_register', [ $this, 'add_customizer_panels_sections' ], 40 );
		\add_action( 'customize_preview_init', [ $this, 'customize_preview_init' ] );
	}

	/**
	 * Inline css.
	 */
	public function inline_css(): void {
		if ( $this->is_customize_preview() && $this->enable_postMessage ) {
			\add_action( 'wp_head', [ $this, 'live_preview_styles' ], 99999 );
		} else {
			\add_filter( 'wpex_head_css', [ $this, 'head_css' ], 999 );
		}
	}

	/**
	 * Adds custom controls.
	 */
	public function customize_controls_enqueue_scripts(): void {
		// Theme custom properties.
		\wp_enqueue_style( 'wpex-custom-properties' );

		// Chosen
		\wp_enqueue_style( 'wpex-chosen' );
		\wp_enqueue_script( 'wpex-chosen' );

		// Controls JS
		\wp_enqueue_script(
			'totaltheme-customize-controls',
			\totaltheme_get_js_file( 'customize/controls' ),
			[ 'customize-controls', 'wpex-chosen', 'jquery' ],
			WPEX_THEME_VERSION
		);

		\wp_localize_script(
			'totaltheme-customize-controls',
			'totaltheme_customize_controls_vars',
			$this->get_controls_l10n(),
		);

		\wp_enqueue_script(
			'totaltheme-customize-control-display',
			\totaltheme_get_js_file( 'customize/control-display' ),
			[ 'customize-controls' ],
			WPEX_THEME_VERSION
		);

		\wp_localize_script(
			'totaltheme-customize-control-display',
			'totaltheme_customize_control_display_vars',
			$this->get_control_visibility_settings()
		);

		// Customizer CSS
		\wp_enqueue_style(
			'totaltheme-customize-controls',
			\totaltheme_get_css_file( 'customize/controls' ),
			[],
			WPEX_THEME_VERSION
		);
	}

	/**
	 * Registered custom controls that are eligible to be rendered via JS and created dynamically.
	 */
	public function register_control_types( $wp_customize ): void {
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Hr' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Heading' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Notice' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Icon' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Blocks' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Button_Color_Select' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Font_Family' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Responsive_Field' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Card_Select' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Top_Right_Bottom_Left' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Multi_Select' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Toggle' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Length_Unit' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Visibility_Select' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Font_Size' );
		$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Color' );
	//	$wp_customize->register_control_type( '\TotalTheme\Customizer\Controls\Template_Select' ); // @todo
	}

	/**
	 * Adds CSS for customizer custom controls.
	 */
	public function customize_controls_print_styles(): void {
		// Get icon color based on the admin color.
		$admin_colors = [
			'light'      => '#04a4cc',
			'blue'       => '#0073aa',
			'coffee'     => '#c7a589',
			'ectoplasm'  => '#a3b745',
			'midnight'   => '#e14d43',
			'ocean'      => '#9ebaa0',
			'sunrise'    => '#dd823b',
			'modern'     => '#3858e9',
		];
		$icon_color = $admin_colors[ \get_user_option( 'admin_color' ) ] ?? '#2271b1';
		?>
		 <style id="wpex-customizer-controls-css">
			:root {
				--totaltheme-customizer-icon-color: <?php echo esc_attr( $icon_color ) ?>;
			}
			/* Icons */
			<?php
			// Dynamically add icons.
			foreach ( $this->panels() as $panel_id => $panel ) {
				$panel_id = $this->parse_panel_id( $panel_id );
				if ( ! empty( $panel['icon'] ) ) {
					$prefix = isset( $panel['is_section'] ) ? 'section' : 'panel';
					if ( \str_starts_with( $panel['icon'], "data:" ) ) {
						$css = '--totaltheme-customizer-icon-content:"";--totaltheme-customizer-icon-url:url("' . $panel['icon'] . '");';
					} else {
						$css = '--totaltheme-customizer-icon-content:"' . $panel['icon'] . '";';
					}
					echo wp_strip_all_tags( '#accordion-' . $prefix . '-' . $panel_id . '{' . $css . '}' );
				}
			} ?>
		 </style>

	<?php }

	/**
	 * Removes core modules.
	 */
	public function remove_core_sections( $wp_customize ): void {
		// Remove core sections.
		$wp_customize->remove_section( 'colors' );
		$wp_customize->remove_section( 'background_image' );

		// Remove core controls.
		$wp_customize->remove_control( 'blogdescription' );
		$wp_customize->remove_control( 'header_textcolor' );
		$wp_customize->remove_control( 'background_color' );
		$wp_customize->remove_control( 'background_image' );

		// Remove default settings.
		$wp_customize->remove_setting( 'background_color' );
		$wp_customize->remove_setting( 'background_image' );
	}

	/**
	 * Get customizer sections.
	 */
	public function get_sections(): array {
		if ( \is_array( $this->sections ) ) {
			return $this->sections;
		}
	
		$panels = $this->panels();

		if ( ! $panels ) {
			return [];
		}

		$this->sections = [];

		// Loop through panels.
		foreach ( $panels as $panel_id => $panel ) {

			// These have their own sections outside the main class scope.
			if ( 'typography' === $panel_id ) {
				continue;
			}

			// Continue if condition isn't me.
			if ( isset( $panel['condition'] ) && ! call_user_func( $panel['condition'] ) ) {
				continue;
			}

			// Section file location.
			if ( ! empty( $panel['settings'] ) ) {
				$file = $panel['settings'];
			} else {
				$file_name = $panel['file_name'] ?? $panel_id;
				$file = WPEX_CUSTOMIZER_DIR . "settings/{$file_name}.php";
			}

			// Include file and update sections var.
			if ( \is_string( $file ) && file_exists( $file ) ) {
				require_once $file;
			} elseif ( !empty( $panel['sections_callback'] ) && \is_callable( $panel['sections_callback'] ) ) {
				$sections = (array) \call_user_func( $panel['sections_callback'] );
				$is_section = isset( $panel['is_section'] );
				foreach ( $sections as $section_id => $section ) {
					if ( $is_section ) {
						$this->sections[ $panel_id ] = $section;
					} else {
						$section['panel'] = $panel_id;
						$this->sections[ "{$section_id}_{$section_id}" ] = $section;
					}
				}
			}

		}

		// Loop through sections and set keys equal to ID for easier child theming.
		// Also remove anything that is only needed in the customizer to free up memory.
		$parsed_sections = [];
		if ( $this->sections && \is_array( $this->sections ) ) {
			foreach ( $this->sections as $key => $val ) {
				$new_settings = [];
				if ( ! $this->is_customize_preview() ) {
					unset( $val['title'], $val['panel'], $val['description'] );
				}
				foreach ( $val['settings'] as $skey => $sval ) {
					if ( $this->is_customize_preview() ) {
						$new_settings[ $sval['id'] ] = $sval;
					} else {
						if ( empty( $sval['inline_css'] ) ) {
							continue; // only inline_css needed for frontend.
						}
						if ( isset( $sval['control']['type'] ) ) {
							$sval['type'] = $sval['control']['type'];
						}
						unset( $sval['transport'], $sval['control'], $sval['control_display'], $sval['description'] );
						$new_settings[ $sval['id'] ] = $sval;
					}
				}
				if ( $new_settings ) {
					$val['settings'] = $new_settings;
					$parsed_sections[ $key ] = $val;
				}
			}
		}

		$this->sections = (array) \apply_filters( 'wpex_customizer_sections', $parsed_sections );

		return $this->sections;
	}

	/**
	 * Registers new controls.
	 *
	 * Removes default customizer sections and settings
	 * Adds new customizer sections, settings & controls
	 */
	public function add_customizer_panels_sections( $wp_customize ): void {
		$all_panels     = $this->panels();
		$enabled_panels = $this->enabled_panels();

		if ( ! $enabled_panels ) {
			return;
		}

		$priority = 140;

		foreach ( $all_panels as $id => $val ) {

			$priority++;

			// Continue if panel is disabled or it's the typography panel.
			if ( ! isset( $enabled_panels[ $id ] ) || 'typography' === $id ) {
				continue;
			}

			// Continue if condition isn't met.
			if ( isset( $val['condition'] ) && ! $val['condition'] ) {
				continue;
			}

			// Get panel title.
			$title = $val['title'] ?? $val;

			// Check if panel is a section.
			$is_section = isset( $val['is_section'] );

			// Add section.
			if ( $is_section ) {
				$wp_customize->add_section( $this->parse_panel_id( $id ), [
					'priority' => $priority,
					'title'    => $title,
				] );
			}

			// Add Panel.
			else {
				$wp_customize->add_panel( $this->parse_panel_id( $id ), [
					'priority' => $priority,
					'title'    => $title,
				] );
			}

		}

		// Create the new customizer sections.
		$this->create_sections( $wp_customize );
	}

	/**
	 * Creates the Sections and controls for the customizer.
	 */
	public function create_sections( $wp_customize ): void {
		$this->sections = $this->get_sections();

		if ( ! $this->sections ) {
			return;
		}

		$enabled_panels = $this->enabled_panels();

		// Loop through sections and add create the customizer sections, settings & controls.
		foreach ( $this->sections as $section_id => $section ) {

			// Check if section panel is enabled.
			if ( ! $this->is_section_enabled( $section, $section_id ) ) {
				continue;
			}

			// Get section settings.
			$settings = ! empty( $section['settings'] ) ? $section['settings'] : null;

			// Return if no settings are found.
			if ( ! $settings ) {
				return;
			}

			// Add customizer section.
			if ( isset( $section['panel'] ) ) {
				$wp_customize->add_section( $section_id, [
					'title'       => $section['title'],
					'panel'       => $section['panel'],
					'description' => $section['description'] ?? '',
				] );
			}

			// Add settings+controls.
			foreach ( $settings as $setting ) {

				if ( empty( $setting['id'] ) ) {
					continue;
				}

				// Get vals.
				$id           = $setting['id'];
				$transport    = $setting['transport'] ?? 'refresh';
				$default      = $setting['default'] ?? '';
				$control_type = $setting['control']['type'] ?? 'text';

				// Check partial refresh.
				if ( 'partialRefresh' === $transport ) {
					$transport = isset( $wp_customize->selective_refresh ) ? 'postMessage' : 'refresh';
				}

				// Set transport to refresh if postMessage is disabled.
				if ( ! $this->enable_postMessage || 'wpex_heading' === $control_type ) {
					$transport = 'refresh';
				}

				// Add values to control.
				$setting['control']['settings'] = $setting['id'];
				$setting['control']['section'] = $section_id;

				// Get global setting description.
				if ( empty( $setting['control']['description'] ) ) {
					$control_description = $this->get_control_description( $setting );
					if ( $control_description ) {
						$setting['control']['description'] = $control_description;
					}
				}

				// Control object.
				if ( ! empty( $setting['control']['object'] ) ) {
					$control_obj = $setting['control']['object'];
				} else {
					$control_obj = $this->get_control_object( $control_type );
				}

				// Add sanitize callbacks.
				if ( ! empty( $setting['sanitize_callback'] ) ) {
					$sanitize_callback = $setting['sanitize_callback'];
				} else {
					$sanitize_callback = $this->get_sanitize_callback( $control_type );
				}

				// Add setting.
				$wp_customize->add_setting( $id, [
					'type'              => 'theme_mod',
					'transport'         => $transport,
					'default'           => $default,
					'sanitize_callback' => $sanitize_callback,
				] );

				if ( isset( $setting['control'] ) ) {

					// Add choices here so we don't have to store them in memory on the frontend.
					if ( isset( $setting['control']['choices'] ) ) {
						$setting['control']['choices'] = $this->parse_control_choices( $setting['control']['choices'] );
					}

					$wp_customize->add_control( new $control_obj( $wp_customize, $id, $setting['control'] ) );
				}
			}

		}

		// Load partial refresh functions.
		if ( isset( $wp_customize->selective_refresh ) ) {
			require_once WPEX_CUSTOMIZER_DIR . 'partial-refresh.php';
		}
	}

	/**
	 * Returns object name for for given control type.
	 */
	public function get_control_object( $control_type ): string {
		$control_classes = [
			'image'                        => 'WP_Customize_Image_Control',
			'media'                        => 'WP_Customize_Media_Control',
			'color'                        => 'WP_Customize_Color_Control',
			'totaltheme_color'             => 'TotalTheme\Customizer\Controls\Color',
			'totaltheme_button_color'      => 'TotalTheme\Customizer\Controls\Button_Color_Select',
			'totaltheme_hr'                => 'TotalTheme\Customizer\Controls\Hr',
			'totaltheme_blocks'            => 'TotalTheme\Customizer\Controls\Blocks',
			'totaltheme_font_size'         => 'TotalTheme\Customizer\Controls\Font_Size',
			'totaltheme_font_family'       => 'TotalTheme\Customizer\Controls\Font_Family',
			'totaltheme_responsive_input'  => 'TotalTheme\Customizer\Controls\Responsive_Field',
			'totaltheme_notice'            => 'TotalTheme\Customizer\Controls\Notice',
			'totaltheme_card_select'       => 'TotalTheme\Customizer\Controls\Card_Select',
			'totaltheme_template_select'   => 'TotalTheme\Customizer\Controls\Template_Select',
			'totaltheme_trbl'              => 'TotalTheme\Customizer\Controls\Top_Right_Bottom_Left',
			'totaltheme_heading'           => 'TotalTheme\Customizer\Controls\Heading',
			'totaltheme_length_unit'       => 'TotalTheme\Customizer\Controls\Length_Unit',
			'totaltheme_svg_select'        => 'TotalTheme\Customizer\Controls\SVG_Select',
			'totaltheme_toggle'            => 'TotalTheme\Customizer\Controls\Toggle',
			'totaltheme_icon'              => 'TotalTheme\Customizer\Controls\Icon',
			'totaltheme_social_profiles'   => 'TotalTheme\Customizer\Controls\Social_Profiles',
			'wpex-dropdown-pages'          => 'TotalTheme\Customizer\Controls\Dropdown_Pages',
			'wpex_textarea'                => 'TotalTheme\Customizer\Controls\Textarea',
			'wpex-columns'                 => 'TotalTheme\Customizer\Controls\Grid_Columns',
			'wpex-grid-columns'            => 'TotalTheme\Customizer\Controls\Grid_Columns',
			'multi-select'                 => 'TotalTheme\Customizer\Controls\Multi_Select',
			'totaltheme_multi_select'      => 'TotalTheme\Customizer\Controls\Multi_Select',
			'totaltheme_visibility_select' => 'TotalTheme\Customizer\Controls\Visibility_Select',
		];
		return $control_classes[ $control_type ] ?? 'WP_Customize_Control';		
	}

	/**
	 * Returns control description when not defined.
	 */
	protected function get_control_description( $control ) {
		$control_type = $control['type'] ?? 'text';
		switch ( $control_type ) {
			case 'totaltheme_template_select':
				return \esc_html__( 'Create a dynamic template to override the default content layout.', 'total' );
			break;
		}
	}

	/**
	 * Returns sanitize_callback for given control type.
	 */
	public function get_sanitize_callback( $control_type ): string {
		$callbacks = [
			'totaltheme_color'             => 'TotalTheme\Customizer\Sanitize_Callbacks::color',
			'totaltheme_length_unit'       => 'TotalTheme\Customizer\Sanitize_Callbacks::length_unit',
			'totaltheme_template_select'   => 'TotalTheme\Customizer\Sanitize_Callbacks::template_id',
			'checkbox'                     => 'TotalTheme\Customizer\Sanitize_Callbacks::checkbox',
			'totaltheme_toggle'            => 'TotalTheme\Customizer\Sanitize_Callbacks::checkbox',
			'select'                       => 'TotalTheme\Customizer\Sanitize_Callbacks::select',
			'wpex-columns'                 => 'TotalTheme\Customizer\Sanitize_Callbacks::grid_columns',
			'totaltheme_visibility_select' => 'TotalTheme\Customizer\Sanitize_Callbacks::visibility',
			'totaltheme_social_profiles'   => 'TotalTheme\Customizer\Sanitize_Callbacks::social_profiles',
			'image'                        => 'sanitize_url',
			'wpex-dropdown-pages'          => 'absint',
			'media'                        => 'absint',
			'color'                        => 'sanitize_hex_color',
			'textarea'                     => 'wp_kses_post',
			'wpex_textarea'                => 'wp_kses_post',
			'text'                         => 'sanitize_text_field',
			'totaltheme_card_select'       => 'sanitize_text_field',
			'multiple-select'              => 'sanitize_text_field',
		];
		return $callbacks[ $control_type ] ?? 'sanitize_text_field';
	}

	/**
	 * Loads js file for customizer preview.
	 */
	public function customize_preview_init(): void {
		if ( ! $this->enable_postMessage ) {
			return;
		}

		\wp_enqueue_script(
			'totaltheme-customize-preview',
			totaltheme_get_js_file( 'customize/preview' ),
			[ 'customize-preview' ],
			WPEX_THEME_VERSION,
			true
		);

		wp_localize_script(
			'totaltheme-customize-preview',
			'totaltheme_customize_preview_vars',
			[
				'inline_css'   => $this->get_inline_css_settings(),
				'theme_colors' => \array_keys( \totaltheme_get_color_palette( 'theme' ) ),
			]
		);
	}

	/**
	 * Loops through all settings and returns visibility settings.
	 */
	protected function get_control_visibility_settings() {
		$this->sections = $this->get_sections();

		if ( ! $this->sections ) {
			return;
		}
	
		$control_visibility = [];
		$settings = array_column( $this->sections, 'settings' );

		if ( ! $settings ) {
			return [];
		}

		$settings = array_merge( ...$settings ); // combine the array of arrays into a single array.

		foreach ( $settings as $setting ) {
			if ( isset( $setting['control_display'] ) ) {
				$control_visibility[ $setting['id'] ] = $this->parse_control_display( $setting['control_display'] );
			}
		}

		return $control_visibility;
	}

	/**
	 * Loops through all settings and returns array of online inline_css settings.
	 */
	public function get_inline_css_settings(): array {
		$this->sections = $this->get_sections();

		if ( ! $this->sections ) {
			return [];
		}

		if ( \is_null( self::$css_settings ) ) {
			self::$css_settings = [];
			$settings = array_column( $this->sections, 'settings' );
			if ( $settings ) {
				$settings = (array) array_merge( ...$settings ); // combine the array of arrays into a single array.
				foreach ( $settings as $setting ) {
					if ( ! isset( $setting['inline_css'] ) ) {
						continue;
					}
					if ( isset( $setting['inline_css']['condition'] ) ) {
						$condition = $setting['inline_css']['condition'];
						if ( \is_callable( $condition ) ) {
							$condition = \call_user_func( $condition );
						}
						if ( false === $condition ) {
							continue;
						}
					}
					$setting_type = $setting['control']['type'] ?? $setting['type'] ?? '';
					if ( 'totaltheme_color' === $setting_type ) {
						$setting['inline_css']['sanitize'] = 'color';
					}
					self::$css_settings[ $setting['id'] ] = $setting['inline_css'];
					if ( isset( $setting['default'] ) ) {
						self::$css_settings[ $setting['id'] ]['default'] = $setting['default'];
					}
				}
			}
		}

		return self::$css_settings;
	}

	/**
	 * Generates inline CSS for styling options.
	 */
	public function loop_through_inline_css( $return = 'css' ) {
		$settings = $this->get_inline_css_settings();

		if ( ! $settings ) {
			return;
		}

		$elements_to_alter = [];
		$preview_styles    = [];
		$add_css           = '';

		// Combine and add media queries last for front-end CSS (not needed for live preview).
		$media_queries = [
			'(min-width: 960px)'                        => null,
			'(min-width: 960px) and (max-width:1280px)' => null,
			'(min-width: 768px) and (max-width:959px)'  => null,
			'(max-width: 767px)'                        => null,
			'(min-width: 480px) and (max-width:767px)'  => null,
		];

		foreach ( $settings as $key => $inline_css ) {

			// Store setting ID.
			$setting_id = $key;

			// Get theme mod value.
			$default = $inline_css['default'] ?? null;

			if ( \str_contains( $setting_id, '[' ) ) {
				$parts = \explode( '[', \rtrim( $setting_id, ']' ) );
				if ( isset( $parts[0] ) && isset( $parts[1] ) ) {
					$theme_mod = \get_theme_mod( $parts[0], [] )[ $parts[1] ] ?? $default;
				} else {
					$theme_mod = null;
				}
			} else {
				$theme_mod = \get_theme_mod( $setting_id, $default );
			}

			// Checkboxes.
			if ( isset( $inline_css['value'] ) && \wp_validate_boolean( $theme_mod ) ) {
				$theme_mod = $inline_css['value'];
			}

			// Get css params.
			$sanitize       = $inline_css['sanitize'] ?? false;
			$media_query    = $inline_css['media_query'] ?? false;
			$selector       = $inline_css['target'] ?? '';
			$property       = $inline_css['alter'] ?? '';
			$important      = isset( $inline_css['important'] ) ? '!important' : false;
			$multi_prop_val = []; // @todo move loop into it's own function so we can use recursive instead.

			// If alter is set to "display" and type equals 'checkbox' then set correct value.
			if ( 'display' === $property && 'checkbox' === $sanitize ) {
				$theme_mod = $theme_mod ? '' : 'none';
			}

			// Theme mod can't be empty (prevent 0 inputs).
			if ( ! $theme_mod ) {
				continue;
			}

			// Add to preview_styles array.
			if ( 'preview_styles' === $return ) {
				$preview_styles[ $setting_id ] = '';
			}

			// Target and alter vars are required, if they are empty continue onto the next setting.
			if ( ! $selector || ! $property ) {
				continue;
			}

			// Sanitize theme mod.
			$theme_mod = $sanitize ? \wpex_sanitize_data( $theme_mod, $sanitize ) : \sanitize_text_field( $theme_mod );

			// Value is empty after sanitization.
			if ( '' === $theme_mod || null === $theme_mod ) {
				continue;
			}

			// Multi target element (trbl) - currently only supported for padding.
			if ( 'padding' === $property
				&& \str_contains( $theme_mod, ':' )
				&& $multi_prop_val = \totaltheme_parse_css_multi_property( $theme_mod, $property )
			) {
				$property = \array_keys( $multi_prop_val );
			}

			// Set to array if not.
			$selector = \is_array( $selector ) ? $selector : [ $selector ];
			$selector = \array_filter( $selector ); // remove empty targets (some targets maybe conditionally added).

			// Loop through items.
			foreach ( $selector as $element ) {

				// Add each element to the elements to alter to prevent undefined indexes.
				if ( 'css' === $return && ! $media_query && ! isset( $elements_to_alter[ $element ] ) ) {
					$elements_to_alter[ $element ] = '';
				}

				// Return CSS or js.
				if ( \is_array( $property ) ) {

					// Loop through elements to alter.
					foreach ( $property as $property_val ) {

						// Modify theme_mod for multi properties.
						if ( $multi_prop_val ) {
							$theme_mod = $multi_prop_val[ $property_val ] ?? null;
							if ( ! $theme_mod ) {
								continue;
							}
						}

						// Define el css output.
						$el_css = "{$property_val}:{$theme_mod}{$important};";

						// Inline CSS.
						if ( 'css' === $return ) {
							if ( $media_query ) {
								$media_queries[ $media_query ][ $element ][] = $el_css;
							} else {
								$elements_to_alter[ $element ] .= $el_css;
							}
						}

						// Live preview styles.
						elseif ( 'preview_styles' === $return ) {
							if ( $media_query ) {
								$preview_styles[ $setting_id ] .= "@media only screen and {$media_query}{{$element}{{$el_css};}}";
							} else {
								$preview_styles[ $setting_id ] .= "{$element}{{$el_css};}";
							}
						}
					}
				}

				// Single element to alter.
				else {

					// Add url to background-image params.
					if ( 'background-image' === $property ) {
						$safe_bg = \esc_url( $theme_mod );
						$theme_mod = "url({$safe_bg})";
					}

					// Define el css output.
					$el_css = "{$property}:{$theme_mod}{$important};";

					// Inline CSS.
					if ( 'css' === $return ) {
						if ( $media_query ) {
							$media_queries[ $media_query ][ $element ][] = $el_css;
						} else {
							$elements_to_alter[ $element ] .= $el_css;
						}
					}

					// Live preview styles.
					elseif ( 'preview_styles' === $return ) {
						if ( $media_query ) {
							$preview_styles[ $setting_id ] .= "@media only screen and {$media_query}{{$element}{{$el_css}}}";
						} else {
							$preview_styles[ $setting_id ] .= "{$element}{{$el_css};}";
						}
					}

				}

			}

		} // End settings loop.

		if ( 'css' === $return ) {

			if ( $elements_to_alter && \is_array( $elements_to_alter ) ) {
				foreach ( $elements_to_alter as $element => $attributes ) {
					if ( \is_string( $attributes ) && $attributes = \trim( $attributes ) ) {
						$add_css .= "{$element}{{$attributes}}";
					}
				}
			}

			if ( $media_queries && \is_array( $media_queries ) ) {
				foreach ( $media_queries as $media_query => $elements ) {
					if ( \is_array( $elements ) && $elements ) {
						$add_css .= "@media only screen and {$media_query}{";
						foreach ( $elements as $element => $attributes ) {
							if ( $attributes && \is_array( $attributes ) ) {
								$attributes_string = \implode( '', $attributes );
								if ( $attributes_string ) {
									$add_css .= "{$element}{{$attributes_string}}";
								}
							}
						}
						$add_css .= '}';
					}
				}
			}

			return $add_css;
		}

		if ( 'preview_styles' === $return ) {
			return $preview_styles;
		}

	}

	/**
	 * Returns CSS to output to wp_head.
	 */
	public function head_css( $output ) {
		if ( $inline_css = $this->loop_through_inline_css( 'css' ) ) {
			$output .= "/*CUSTOMIZER STYLING*/{$inline_css}";
		}
		$this->sections = null; // clear up memory on the front-end.
		return $output;
	}

	/**
	 * Returns CSS to output to wp_head.
	 */
	public function live_preview_styles() {
		$live_preview_styles = $this->loop_through_inline_css( 'preview_styles' );
		if ( $live_preview_styles ) {
			foreach ( $live_preview_styles as $key => $val ) {
				if ( ! empty( $val ) ) {
					echo '<style id="wpex-customizer-' . \sanitize_html_class( $key ) . '">' . $val . '</style>';
				}
			}
		}
	}

	/**
	 * Parses the control_display so we dynamically add values rather then storing in memory.
	 */
	protected function parse_control_display( $display ) {
		if ( isset( $display['value'] ) ) {
			$display['value'] = $this->parse_control_display_value( $display['value'] );
		}
		return $display;
	}

	/**
	 * Parses the control_display value key.
	 */
	protected function parse_control_display_value( $value ) {
		switch ( $value ) {
			case 'header_has_aside':
				$value = totaltheme_call_static( 'Header\Aside', 'supported_header_styles' );
				break;
		}
		return $value;
	}

	/**
	 * Parses control choices.
	 *
	 * @todo Register custom controls for values used multiple times.
	 */
	protected function parse_control_choices( $choices ) {
		if ( \is_array( $choices ) ) {
			return $choices;
		}
		switch ( $choices ) {
			case 'post_types':
				$choices = \wpex_get_post_types( 'customizer_settings', [ 'attachment' ] );
				break;
			case 'opacity':
				$choices = \wpex_utl_opacities();
				break;
			case 'margin':
				$choices = \wpex_utl_margins();
				break;
			case 'padding':
				$choices = \wpex_utl_paddings();
				break;
			case 'shadow':
				$choices = \wpex_utl_shadows();
				break;
			case 'border_radius':
				$choices = \wpex_utl_border_radius();
				break;
			case 'column_gap':
				$choices = \wpex_column_gaps();
				break;
			case 'breakpoint':
				$choices = \wpex_utl_breakpoints();
				break;
			case 'font_size':
				$choices = \wpex_utl_font_sizes();
				break;
			case 'icon_size':
				$choices = [
					'' => esc_html( 'Default', 'total' ),
					'2xs' =>\esc_html__( '2x Small', 'total' ),
					'xs'  => \esc_html__( 'x Small (Default)', 'total' ),
					'sm'  => \esc_html__( 'Small', 'total' ),
					'lg'  => \esc_html__( 'Large', 'total' ),
					'xl'  => \esc_html__( 'x Large', 'total' ),
					'2xl'  => \esc_html__( '2x Large', 'total' ),
				];
				break;
			case 'bg_style':
				$choices = \wpex_get_bg_img_styles();
				break;
			case 'post_layout':
				$choices = \wpex_get_post_layouts();
				break;
			case 'social_styles':
				$choices = $this->choices_social_styles();
				break;
			case 'blog_taxonomies':
				$choices = $this->choices_taxonomies( 'post' );
				break;
			case 'portfolio_taxonomies':
				$choices = $this->choices_taxonomies( 'portfolio' );
				break;
			case 'staff_taxonomies':
				$choices = $this->choices_taxonomies( 'staff' );
				break;
			case 'overlay':
				$choices = (array) totaltheme_call_static( 'Overlays', 'get_style_choices' );
				break;
			case 'font_weight':
				$choices = [
					''    => \esc_html__( 'Default', 'total' ),
					'100' => '100',
					'200' => '200',
					'300' => '300',
					'400' => '400',
					'500' => '500',
					'600' => '600',
					'700' => '700',
					'900' => '900',
				];
				break;
			case 'html_tag':
				$choices = [
					'div' => 'div',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
				];
				break;
			default:
				if ( \is_string( $choices ) && \is_callable( $choices ) ) {
					$choices = \call_user_func( $choices );
				}
				break;
		}
		return $choices;
	}

	/**
	 * Returns array of taxonomies associated with the blog.
	 */
	protected function choices_taxonomies( $postype ) {
		$taxonomies = [
			'null' => \esc_html__( 'Anything', 'total' ),
		];
		$get_taxonomies = \get_object_taxonomies( $postype );
		if ( $get_taxonomies ) {
			foreach ( $get_taxonomies as $tax ) {
				$taxonomies[ $tax ] = \get_taxonomy( $tax )->labels->name;
			}
		}
		return $taxonomies;
	}

	/**
	 * Returns array of social styles.
	 */
	protected function choices_social_styles() {
		$social_styles = [
			'colored-icons' => \esc_html__( 'Colored Image Icons (Legacy)', 'total' ),
		];
		return \array_merge( \wpex_social_button_styles(), $social_styles );
	}

	/**
	 * Create template ajax.
	 */
	public function ajax_create_template() {
		if ( empty( $_POST['post_title'] )
			|| ! class_exists( 'TotalTheme\Helpers\Add_Template' )
			|| ! \current_user_can( 'publish_pages' )
			|| ! \current_user_can( 'edit_theme_options' )
			|| ! \post_type_exists( 'wpex_templates' )
		) {
			\wp_die();
		}

		\check_ajax_referer( 'totaltheme_customize_nonce', 'nonce' );

		$result = [
			'success' => 0
		];

		$title = \sanitize_text_field( \wp_unslash( $_POST['post_title'] ) );
		$type  = ! empty( $_POST['type'] ) ? \sanitize_text_field( \wp_unslash( $_POST['type'] ) ) : '';

		$template_id = (new Helpers\Add_Template($title, $type))->template_id;

		if ( $template_id ) {
			$result['success'] = 1;
			$result['template_id'] = \absint( $template_id );
		}

		echo \wp_json_encode( $result );

		\wp_die();
	}

	/**
	 * Returns l10n for the controls scripts.
	 */
	private function get_controls_l10n(): array {
		$l10n = [
			'deleteConfirm'  => \esc_html__( 'Are you sure you want to delete this?', 'total' ),
			'duplicate'      => \esc_html__( 'This item has already been added', 'total' ),
			'nonce'          => \wp_create_nonce( 'totaltheme_customize_nonce' ),
			'adminColor'     => \esc_js( \get_user_option( 'admin_color' ) ),
			'themeColors'    => \totaltheme_get_color_palette( 'theme' ),
			'backLinkText'   => \esc_html__( 'back to setting', 'total' ),
		];
		$allowed_html = [
			'a' => [
				'href'   => [],
				'rel'    => [],
				'target' => [],
			],
		];
		if ( totaltheme_call_static( 'Header\Core', 'is_custom' ) ) {
			$l10n['headerBuilderNotice'] = \wp_kses( \sprintf(
				\__( 'Your site is using the <a href="%s" target="_blank" rel="noopener noreferrer">Header Builder &#8599;</a>', 'total' ),
				\esc_url( \admin_url( 'admin.php?page=wpex-panel-header-builder' )
			) ), $allowed_html );
		}
		return $l10n;
	}

	/**
	 * Parse panel ID.
	 */
	protected function parse_panel_id( string $panel_id ): string {
		if ( ! \str_starts_with( $panel_id, 'wpex_' ) && ! \str_starts_with( $panel_id, 'totaltheme_' ) ) {
			$panel_id = "wpex_{$panel_id}";
		}
		return $panel_id;
	}

	/**
	 * Returns dashicon unicode.
	 */
	protected function _get_dashicon_unicode( string $dashicon ): string {
		return wpex_get_dashicons_array()[ str_replace( 'dashicons-', '', $dashicon ) ] ?? '';
	}

	/**
	 * Get all sections.
	 */
	public function add_sections(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 5.16' );
	}

	/**
	 * Parses a multi property theme mod (used for padding).
	 */
	protected function parse_css_multi_property(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 5.16' );
	}

}
