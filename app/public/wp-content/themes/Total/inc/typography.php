<?php

namespace TotalTheme;

use TotalTheme\Customizer\Controls\Top_Right_Bottom_Left as Control_Top_Right_Bottom_Left;
use TotalTheme\Customizer\Controls\Length_Unit as Control_Length_Unit;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds all Typography options to the Customizer and outputs the custom CSS for them.
 */
final class Typography {

	/**
	 * Holds an array of supported fonts.
	 */
	private $supported_fonts = null;

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of our class.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Main constructor.
	 */
	private function __construct() {
		if ( ! \get_theme_mod( 'typography_enable', true ) ) {
			return;
		}

		// Register customizer settings.
		if ( \wpex_has_customizer_panel( 'typography' ) ) {
			\add_action( 'customize_register',[ $this, 'register' ], 40 );
		}

		// Front-end actions.
		if ( \wpex_is_request( 'frontend' ) ) {

			// Enqueue Google Fonts.
			if ( \wpex_has_google_services_support() ) {
				if ( get_theme_mod( 'google_fonts_in_footer' ) ) {
					\add_action( 'wp_footer', [ $this, 'frontend_enqueue_google_fonts' ] );
				} else {
					\add_action( 'wp_enqueue_scripts', [ $this, 'frontend_enqueue_google_fonts' ] );
				}
			}

		}

		// CSS output for typography settings.
		if ( is_customize_preview() && \wpex_has_customizer_panel( 'typography' ) ) {
			\add_action( 'wp_enqueue_scripts', [ $this, 'customize_enqueue_registered_fonts' ] );
			\add_action( 'customize_preview_init', [ $this, 'customize_preview_js' ] );
			\add_action( 'customize_controls_enqueue_scripts', [ $this, 'customize_controls_js' ] );
			\add_action( 'wp_head', [ $this, 'customize_css' ], 999 );
		} else {
			\add_filter( 'wpex_head_css', [ $this, 'frontend_css' ], 99 );
		}

	}

	/**
	 * Array of Typography settings to add to the customizer.
	 */
	public function get_settings(): array {
		$header_is_custom = \totaltheme_call_static( 'Header\Core', 'is_custom' );
		$footer_is_custom = \totaltheme_call_static( 'Footer\Core', 'is_custom' );

		$settings = [];

		$settings['body'] = [
			'label'  => \esc_html__( 'Body', 'total' ),
			'target' => 'body',
		];

		if ( ! $header_is_custom ) {
			$settings['logo'] = [
				'label' => \esc_html__( 'Logo', 'total' ),
				'target' => '#site-logo .site-logo-text',
				'exclude' => [ 'color' ],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_image_logo',
			];
		}

		$settings['button'] = [
			'label' => \esc_html__( 'Buttons', 'total' ),
			'target' => ':root',
			'exclude' => [ 'color', 'margin' ],
			'css_vars' => [
				'text-transform' => '--wpex-btn-text-transform',
				'letter-spacing' => '--wpex-btn-letter-spacing',
				'font-family' => '--wpex-btn-font-family',
				'font-style' => '--wpex-btn-font-style',
				'font-weight' => '--wpex-btn-font-weight',
				'line-height' => '--wpex-btn-line-height',
				'font-size' => '--wpex-btn-font-size',
			],
		];

		$settings['toggle_bar'] = [
			'label' => \esc_html__( 'Toggle Bar', 'total' ),
			'target' => '#toggle-bar-wrap',
			'exclude' => [ 'color' ],
			'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_toggle_bar',
			'condition' => 'wpex_has_togglebar',
		];

		// @todo this be renamed to topbar?
		$settings['top_menu'] = [
			'label' => \esc_html__( 'Top Bar', 'total' ),
			'target' => '#top-bar-content',
			'exclude' => [ 'color' ],
			'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_top_bar',
			'condition' => 'TotalTheme\Topbar\Core::is_enabled',
		];

		if ( ! $header_is_custom ) {
			$settings['header_aside'] = [
				'label' => \esc_html__( 'Header Aside', 'total' ),
				'target' => '.header-aside-content',
			];

			$settings['menu'] = [
				'label' => \esc_html__( 'Main Menu', 'total' ),
				'target' => '.main-navigation-ul .link-inner', // @todo Should we add : #current-shop-items-dropdown, #searchform-dropdown input[type="search"] ??
				'exclude' => [ 'color', 'line-height' ], // Can't include color causes issues with menu styling settings
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_menu_typography',
				'condition' => 'wpex_hasnt_dev_style_header',
			];

			$settings['menu_dropdown'] = [
				'label' => \esc_html__( 'Main Menu: Dropdowns', 'total' ),
				'target' => '.main-navigation-ul .sub-menu .link-inner',
				'exclude' => [ 'color' ],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_menu_typography',
				'condition' => 'wpex_hasnt_dev_style_header',
			];

			$settings['mobile_menu'] = [
				'label' => \esc_html__( 'Mobile Menu', 'total' ),
				'target' => '.wpex-mobile-menu, #sidr-main',
				'exclude' => [ 'color' ],
			];
		}

		$settings['page_title'] = [
			'label' => \esc_html__( 'Page Header Title', 'total' ),
			'description' => \esc_html__( 'Important: These settings will only affect the Globally set page header style in order to prevent conflicts when using a custom style on a per-page basis.', 'total' ),
			'target' => '.page-header .page-header-title',
			'exclude' => [ 'color' ],
			'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
			'condition' => 'TotalTheme\Page\Header::is_enabled',
		];

		$settings['page_subheading'] = [
			'label' => \esc_html__( 'Page Title Subheading', 'total' ),
			'target' => '.page-header .page-subheading',
			'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
			'condition' => 'TotalTheme\Page\Header::is_enabled',
		];

		$settings['blog_entry_title'] = [
			'label' => \esc_html__( 'Blog Entry Title', 'total' ),
			// @todo can we optimize this?
			'target' => '.blog-entry-title.entry-title, .blog-entry-title.entry-title a, .blog-entry-title.entry-title a:hover',
		];

		$settings['blog_entry_meta'] = [
			'label' => \esc_html__( 'Blog Entry Meta', 'total' ),
			'target' => '.blog-entry .meta',
		];

		$settings['blog_entry_content'] = [
			'label' => \esc_html__( 'Blog Entry Excerpt', 'total' ),
			'target' => '.blog-entry-excerpt',
		];

		$settings['blog_post_title'] = [
			'label' => \esc_html__( 'Blog Post Title', 'total' ),
			'target' => 'body.single-post .single-post-title',
		];

		$settings['blog_post_meta'] = [
			'label' => \esc_html__( 'Blog Post Meta', 'total' ),
			'target' => '.single-post .meta',
		];

		$settings['breadcrumbs'] = [
			'label' => \esc_html__( 'Breadcrumbs', 'total' ),
			'target' => '.site-breadcrumbs',
			'exclude' => [ 'color', 'line-height' ],
			'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_breadcrumbs',
			'condition' => 'wpex_has_breadcrumbs',
		];

		$settings['blockquote'] = [
			'label' => \esc_html__( 'Blockquote', 'total' ),
			'target' => 'blockquote',
			'exclude' => [ 'color' ],
		];

		$settings['sidebar'] = [
			'label' => \esc_html__( 'Sidebar', 'total' ),
			'target' => '#sidebar',
			'exclude' => [ 'color' ],
			'condition' => 'wpex_has_sidebar',
		];

		$settings['sidebar_widget_title'] = [
			'label' => \esc_html__( 'Sidebar Widget Heading', 'total' ),
			'target' => '.sidebar-box .widget-title',
			'margin' => true,
			'exclude' => [ 'color' ],
		];

		$settings['headings'] = [
			'label' => \esc_html__( 'Headings', 'total' ),
			'target' => ':root', // important so it doesn't break the wpex-dark-surface class.
			'exclude' => [ 'font-size' ],
			'css_vars' => [
				'color' => '--wpex-heading-color',
				'text-transform' => '--wpex-heading-text-transform',
				'letter-spacing' => '--wpex-heading-letter-spacing',
				'font-family' => '--wpex-heading-font-family',
				'font-style' => '--wpex-heading-font-style',
				'font-weight' => '--wpex-heading-font-weight',
				'line-height' => '--wpex-heading-line-height',
			],
		];

		$settings['theme_heading'] = [
			'label' => \esc_html__( 'Theme Heading', 'total' ),
			'target' => '.theme-heading',
			'margin' => true,
		];

		if ( WPEX_VC_ACTIVE ) {
			$settings['vcex_heading'] = [
				'label'  => \esc_html__( 'Heading Element', 'total' ),
				// @important - we must target the .vcex-heading.wpex-heading element to work with the Customizer's
				// Custom Heading Typography setting but still add the default styles to the element.
				'target' => '.vcex-heading',
				'margin' => \totaltheme_has_classic_styles(),
			];
		}

		$settings['entry_h1'] = [
			'label' => \esc_html__( 'H1', 'total' ),
			'target' => 'h1,.wpex-h1',
			'margin' => true,
			'description' => \esc_html__( 'Will target headings in your post content.', 'total' ),
			// @important headings can't target CSS vars.
		];

		$settings['entry_h2'] = [
			'label' => 'H2',
			'target' => 'h2,.wpex-h2',
			'margin' => true,
			'description' => \esc_html__( 'Will target headings in your post content.', 'total' ),
			// @important headings can't target CSS vars.
		];

		$settings['entry_h3'] = [
			'label' => 'H3',
			'target' => 'h3,.wpex-h3',
			'margin' => true,
			'description' => \esc_html__( 'Will target headings in your post content.', 'total' ),
			// @important headings can't target CSS vars.
		];

		$settings['entry_h4'] = [
			'label' => 'H4',
			'target' => 'h4,.wpex-h4',
			'margin' => true,
			'description' => \esc_html__( 'Will target headings in your post content.', 'total' ),
			// @important headings can't target CSS vars.
		];

		$settings['post_content'] = [
			'label' => \esc_html__( 'Post Content', 'total' ),
			'target' => '.single-blog-content, .vcex-post-content-c, .wpb_text_column, body.no-composer .single-content, .woocommerce-Tabs-panel--description',
		];

		if ( ! $footer_is_custom || get_theme_mod( 'footer_builder_footer_widgets' ) ) {
			$settings['footer_widgets'] = [
				'label' => \esc_html__( 'Footer Widgets', 'total' ),
				'target' => '#footer-widgets',
				'exclude' => [ 'color' ],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_footer_widgets',
				'condition' => 'TotalTheme\Footer\Widgets::is_enabled',
			];

			$settings['footer_widget_title'] = [
				'label' => \esc_html__( 'Footer Widget Heading', 'total' ),
				'target' => '.footer-widget .widget-title',
				'exclude' => [ 'color' ],
				'margin' => true,
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_footer_widgets',
				'condition' => 'TotalTheme\Footer\Widgets::is_enabled',
			];
		}

		$settings['callout'] = [
			'label' => \esc_html__( 'Footer Callout', 'total' ),
			'target' => '.footer-callout-content',
			'exclude' => [ 'color' ],
			'condition' => 'TotalTheme\Footer\Callout::is_enabled',
		];

		if ( ! $footer_is_custom || get_theme_mod( 'footer_builder_footer_bottom' ) ) {
			$settings['copyright'] = [
				'label' => \esc_html__( 'Footer Bottom Text', 'total' ),
				'target' => '#copyright',
				'exclude' => [ 'color' ],
				'condition' => 'TotalTheme\Footer\Bottom\Core::is_enabled',
			];
			$settings['footer_menu'] = [
				'label' => \esc_html__( 'Footer Bottom Menu', 'total' ),
				'target' => '#footer-bottom-menu',
				'exclude' => [ 'color' ],
				'condition' => 'TotalTheme\Footer\Bottom\Core::is_enabled',
			];
		}

		return (array) \apply_filters( 'wpex_typography_settings', $settings );
	}

	/**
	 * Loads scripts for live previews.
	 */
	public function customize_preview_js(): void {
		\wp_enqueue_script(
			'totaltheme-customize-typography',
			\totaltheme_get_js_file( 'customize/typography' ),
			[ 'customize-preview' ],
			\WPEX_THEME_VERSION,
			true
		);

		\wp_localize_script(
			'totaltheme-customize-typography',
			'totaltheme_customize_typography_vars',
			[
				'stdFonts'          => \wpex_standard_fonts(),
				'userFonts'         => \array_keys( \wpex_get_registered_fonts() ),
				'customFonts'       => \wpex_add_custom_fonts(),
				'googleFontsUrl'    => \wpex_get_google_fonts_url(),
				'googleFontsSuffix' => '100i,200i,300i,400i,500i,600i,700i,800i,100,200,300,400,500,600,700,800',
				'sytemUIFontStack'  => \wpex_get_system_ui_font_stack(),
				'settings'          => $this->get_settings(),
				'properties'        => [
					'font-family',
					'font-weight',
					'font-style',
					'font-size',
					'color',
					'line-height',
					'letter-spacing',
					'text-transform',
					'margin',
				],
			]
		);
	}

	/**
	 * Loads scripts for custom controls.
	 */
	public function customize_controls_js(): void {
		\wp_enqueue_script(
			'totaltheme-customize-typography-quicklinks',
			\totaltheme_get_js_file( 'customize/typography-quick-links' ),
			[ 'customize-controls' ],
			\WPEX_THEME_VERSION
		);
		\wp_localize_script(
			'totaltheme-customize-typography-quicklinks',
			'totaltheme_customize_typography_quicklinks_vars',
			[
				'linkText' => \esc_html__( 'edit font', 'total' ),
			]
		);
	}

	/**
	 * Register typography options to the Customizer.
	 */
	public function register( $wp_customize ) {
		if ( ! \class_exists( '\TotalTheme\Customizer', false ) ) {
			return;
		}

		// Get Settings.
		$settings = $this->get_settings();

		// Return if settings are empty. This check is needed due to the filter added above.
		if ( empty( $settings ) ) {
			return;
		}

		// Add General Panel.
		$wp_customize->add_panel( 'wpex_typography', [
			'priority' => 144,
			'capability' => 'edit_theme_options',
			'title' => \esc_html__( 'Typography', 'total' ),
		] );

		// Add General Tab with font smoothing.
		$wp_customize->add_section( 'wpex_typography_general' , [
			'title' => \esc_html__( 'General', 'total' ),
			'priority' => 1,
			'panel' => 'wpex_typography',
		] );

		// Font Smoothing.
		$wp_customize->add_setting( 'enable_font_smoothing', [
			'type' => 'theme_mod',
			'transport' => 'postMessage',
			'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::checkbox',
		] );
		$wp_customize->add_control( new Customizer\Controls\Toggle( $wp_customize, 'enable_font_smoothing', [
			'label' => \esc_html__( 'Font Smoothing', 'total' ),
			'section' => 'wpex_typography_general',
			'settings' => 'enable_font_smoothing',
			'type' => 'totaltheme_toggle',
			'description' => \esc_html__( 'Enable font-smoothing site wide. This makes fonts look a little "skinner". ', 'total' ),
		] ) );

		// Load fonts in footer.
		$wp_customize->add_setting( 'google_fonts_in_footer', [
			'type' => 'theme_mod',
			'transport' => 'postMessage',
			'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::checkbox',
		] );

		$wp_customize->add_control( new Customizer\Controls\Toggle( $wp_customize, 'google_fonts_in_footer', [
			'label' => \esc_html__( 'Load Fonts Last', 'total' ),
			'section' => 'wpex_typography_general',
			'settings' => 'google_fonts_in_footer',
			'type' => 'totaltheme_toggle',
			'description' => \esc_html__( 'Enable to load fonts after your body tag. This can help with render blocking scripts but also means your text will swap fonts so it may not render nicely.', 'total' ),
		] ) );

		// Bold Font Weight.
		$wp_customize->add_setting( 'bold_font_weight', [
			'type' => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
		] );

		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'bold_font_weight', [
			'label' => \esc_html__( 'Bold Font Weight', 'total' ),
			'section' => 'wpex_typography_general',
			'settings' => 'bold_font_weight',
			'type' => 'select',
			'choices' => $this->choices_font_weights(),
			'description' => \esc_html__( 'Controls the defaul font weight for bold elements in the theme.', 'total' ),
		] ) );

		// Google Font settings.
		if ( wpex_has_google_services_support() ) {

			// Load custom font 1.
			$wp_customize->add_setting( 'load_custom_google_font_1', [
				'type' => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
			] );

			$wp_customize->add_control( new Customizer\Controls\Font_Family( $wp_customize, 'load_custom_google_font_1', [
				'label' => \esc_html__( 'Load Custom Font', 'total' ),
				'section' => 'wpex_typography_general',
				'settings' => 'load_custom_google_font_1',
				'type' => 'totaltheme_font_family',
				'description' => \esc_html__( 'Allows you to load a custom font site wide for use with custom CSS. ', 'total' ),
			] ) );

			// Google font display option.
			$wp_customize->add_setting( 'google_font_display', [
				'type' => 'theme_mod',
				'transport' => 'postMessage',
				'default' => 'swap',
				'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::select',
			] );

			$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'google_font_display', [
				'label' => \esc_html__( 'Google Font Display Type', 'total' ),
				'section' => 'wpex_typography_general',
				'settings' => 'google_font_display',
				'type' => 'select',
				'choices' => [
					'' => \esc_html__( 'None', 'total' ),
					'auto' => 'auto',
					'block' => 'block',
					'swap' => 'swap',
					'fallback' => 'fallback',
					'optional' => 'optional',
				],
				'description' => '<a href="https://developer.chrome.com/blog/font-display/#which-font-display-is-right-for-you" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'Learn More', 'total' ) . ' &#8599;</a>'
			] ) );

			// Select subsets.
			$wp_customize->add_setting( 'google_font_subsets', [
				'type' => 'theme_mod',
				'default' => 'latin',
				'sanitize_callback' => 'sanitize_text_field',
			] );

			$wp_customize->add_control( new Customizer\Controls\Multi_Select( $wp_customize, 'google_font_subsets', [
				'label'    => \esc_html__( 'Font Subsets', 'total' ),
				'section'  => 'wpex_typography_general',
				'settings' => 'google_font_subsets',
				'choices'  => [
					'latin'        => 'latin',
					'latin-ext'    => 'latin-ext',
					'cyrillic'     => 'cyrillic',
					'cyrillic-ext' => 'cyrillic-ext',
					'greek'        => 'greek',
					'greek-ext'    => 'greek-ext',
					'vietnamese'   => 'vietnamese',
				],
			] ) );
		}

		// Loop through settings.
		foreach ( $settings as $element => $array ) {

			$label = $array['label'] ?? null;

			if ( ! $label ) {
				continue; // label is required.
			}

			$active_callback = $array['active_callback'] ?? null;
			$description     = $array['description'] ?? '';
			$transport       = $array['transport'] ?? 'postMessage';

			// Get attributes.
			if ( ! empty ( $array['attributes'] ) ) {
				$attributes = $array['attributes'];
			} else {
				$attributes = [
					'font-family',
					'font-weight',
					'font-style',
					'text-transform',
					'font-size',
					'line-height',
					'letter-spacing',
					'color',
				];
			}

			// Margin must be enabled seperately.
			if ( isset( $array['margin'] ) && true === $array['margin'] ) {
				$attributes[] = 'margin';
			}

			// Set keys equal to vals.
			$attributes = \array_combine( $attributes, $attributes );

			// Exclude attributes for specific options.
			if ( ! empty( $array['exclude'] ) && is_array( $array['exclude'] ) ) {
				foreach ( $array['exclude'] as $key => $val ) {
					unset( $attributes[ $val ] );
				}
			}

			// Define Section.
			$wp_customize->add_section( "wpex_typography_{$element}" , [
				'title'       => $label,
				'panel'       => 'wpex_typography',
				'description' => $description,
			] );

			// Font Family.
			if ( \in_array( 'font-family', $attributes ) ) {
				$wp_customize->add_setting( "{$element}_typography[font-family]", [
					'type'              => 'theme_mod',
					'transport'         => $transport,
					'sanitize_callback' => 'sanitize_text_field',
				] );

				$control_args = [
						'type'            => 'totaltheme_font_family',
						'label'           => \esc_html__( 'Font Family', 'total' ),
						'section'         => "wpex_typography_{$element}",
						'settings'        =>  "{$element}_typography[font-family]",
						'active_callback' => $active_callback,
				];

				if ( \class_exists( 'WPEX_Font_Manager', false ) ) {
					$control_args['description'] = sprintf( \esc_html__( 'You can use the %sFont Manager%s to register additional Google, Adobe or custom fonts.', 'total' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=wpex_font' ) ) . '" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' );
				}

				$wp_customize->add_control( new Customizer\Controls\Font_Family(
					$wp_customize,
					"{$element}_typography[font-family]",
					$control_args
				) );
			}

			// Font Weight.
			if ( \in_array( 'font-weight', $attributes ) ) {
				$wp_customize->add_setting( "{$element}_typography[font-weight]", [
					'type'              => 'theme_mod',
					'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::select',
					'transport'         => $transport,
				] );
				$wp_customize->add_control( "{$element}_typography[font-weight]", [
					'label'           => \esc_html__( 'Font Weight', 'total' ),
					'section'         => "wpex_typography_{$element}",
					'settings'        => "{$element}_typography[font-weight]",
					'type'            => 'select',
					'active_callback' => $active_callback,
					'choices'         => $this->choices_font_weights(),
					'description'     => \esc_html__( 'Note: Not all Fonts support every font weight style. ', 'total' ),
				] );
			}

			// Font Style.
			if ( \in_array( 'font-style', $attributes ) ) {
				$wp_customize->add_setting( "{$element}_typography[font-style]", [
					'type'              => 'theme_mod',
					'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::select',
					'transport'         => $transport,
				] );
				$wp_customize->add_control( "{$element}_typography[font-style]", [
					'label'           => \esc_html__( 'Font Style', 'total' ),
					'section'         => "wpex_typography_{$element}",
					'settings'        => "{$element}_typography[font-style]",
					'type'            => 'select',
					'active_callback' => $active_callback,
					'choices'         => $this->choices_font_style(),
				] );
			}

			// Text-Transform.
			if ( \in_array( 'text-transform', $attributes ) ) {
				$wp_customize->add_setting( "{$element}_typography[text-transform]", [
					'type'              => 'theme_mod',
					'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::select',
					'transport'         => $transport,
				] );
				$wp_customize->add_control( "{$element}_typography[text-transform]", [
					'label'           => \esc_html__( 'Text Transform', 'total' ),
					'section'         => "wpex_typography_{$element}",
					'settings'        => "{$element}_typography[text-transform]",
					'type'            => 'select',
					'active_callback' => $active_callback,
					'choices'         => $this->choices_text_transform(),
				] );
			}

			// Font Size.
			if ( \in_array( 'font-size', $attributes ) ) {
				$wp_customize->add_setting( "{$element}_typography[font-size]", [
					'type'              => 'theme_mod',
					'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::font_size',
					'transport'         => $transport,
				] );
				$wp_customize->add_control( new Customizer\Controls\Font_Size( $wp_customize, "{$element}_typography[font-size]", [
					'label'           => \esc_html__( 'Font Size', 'total' ),
					'section'         => "wpex_typography_{$element}",
					'settings'        => "{$element}_typography[font-size]",
					'type'            => 'totaltheme_font_size',
					'active_callback' => $active_callback,
				] ) );
			}

			// Font Color.
			if ( \in_array( 'color', $attributes ) ) {
				switch ( $element ) {
					case 'body':
						$color_description = \sprintf(
							\esc_html__( 'By default the body color uses the "Text 2" color defined in your color scheme where we recommend modifying it for consistency. (%sedit color scheme%s)', 'total' ), '<span class="totaltheme-customize-focus-link" data-wpex-section="wpex_color_scheme" data-wpex-control="body_typography_color">',
							'</span>'
						);
						break;
					case 'headings':
						$color_description = \sprintf(
							\esc_html__( 'By default the headings color uses the "Text 1" color defined in your color scheme where we recommend modifying it for consistency. (%sedit color scheme%s)', 'total' ), '<span class="totaltheme-customize-focus-link" data-wpex-section="wpex_color_scheme" data-wpex-control="headings_typography_color">',
							'</span>'
						);
						break;
					default:
					$color_description = '';
						break;
				}
				$wp_customize->add_setting( "{$element}_typography[color]", [
					'type'              => 'theme_mod',
					'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::color',
					'transport'         => $transport,
				] );
				$wp_customize->add_control( new Customizer\Controls\Color( $wp_customize, "{$element}_typography_color", [
					'label'           => \esc_html__( 'Color', 'total' ),
					'section'         => "wpex_typography_{$element}",
					'settings'        => "{$element}_typography[color]",
					'active_callback' => $active_callback,
					'description'     => $color_description,
				] ) );
			}

			// Line Height.
			if ( \in_array( 'line-height', $attributes ) ) {
				$wp_customize->add_setting( "{$element}_typography[line-height]", [
					'type'              => 'theme_mod',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport,
				] );
				$wp_customize->add_control( new Control_Length_Unit( $wp_customize, "{$element}_typography[line-height]", [
						'label'           => \esc_html__( 'Line Height', 'total' ),
						'section'         => "wpex_typography_{$element}",
						'settings'        => "{$element}_typography[line-height]",
						'type'            => 'totaltheme_length_unit',
						'default_unit'    => 'int',
						'units'           => [ 'int', 'px', 'em', 'rem', '%', 'vmin', 'vmax', 'var', 'func' ],
						'active_callback' => $active_callback,
				] ) );
			}

			// Letter Spacing.
			if ( \in_array( 'letter-spacing', $attributes ) ) {
				$wp_customize->add_setting( "{$element}_typography[letter-spacing]", [
					'type'              => 'theme_mod',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport,
				] );
				$wp_customize->add_control( new Control_Length_Unit( $wp_customize, "{$element}_typography_letter_spacing", [
					'label'           => \esc_html__( 'Letter Spacing', 'total' ),
					'section'         => "wpex_typography_{$element}",
					'settings'        => "{$element}_typography[letter-spacing]",
					'type'            => 'totaltheme_length_unit',
					'units'           => [ 'px', 'em', 'rem', 'vmin', 'vmax', 'var', 'func' ],
					'active_callback' => $active_callback,
				] ) );
			}

			// Margin.
			if ( \in_array( 'margin', $attributes ) ) {
				$wp_customize->add_setting( "{$element}_typography[margin]", [
					'type'              => 'theme_mod',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport,
				] );
				$wp_customize->add_control( new Control_Top_Right_Bottom_Left( $wp_customize, "{$element}_typography[margin]",
					[
						'label'           => \esc_html__( 'Margin', 'total' ),
						'section'         => "wpex_typography_{$element}",
						'settings'        =>  "{$element}_typography[margin]",
						'type'            => 'totaltheme_trbl',
						'active_callback' => $active_callback,
				] ) );
			}

		}

	}

	/**
	 * Loop through settings.
	 *
	 * @todo combine CSS targeting the same selector (such as :root).
	 */
	public function loop( $return = 'css' ) {
		$end_css              = '';
		$css                  = '';
		$tablet_landscape_css = '';
		$tablet_portrait_css  = '';
		$phone_landscape_css  = '';
		$phone_portrait_css   = '';
		$preview_styles       = [];

		// Loop through settings that need typography styling applied to them.
		foreach ( $this->get_settings() as $element => $array ) {
			if ( empty( $array['target'] ) ) {
				continue;
			}

			$get_mod  = $this->get_setting_val( $element );

			if ( ! $get_mod ) {
				continue;
			}

			// Check conditional when running CSS loop.
			// Prevents CSS from being added to the page if not needed.
			if ( 'css' === $return && isset( $array['condition'] ) && ! self::check_condition( $array['condition'] ) ) {
				continue;
			}

			// Attributes to loop through.
			if ( ! empty( $array['attributes'] ) ) {
				$attributes = $array['attributes'];
			} else {
				$attributes = [
					'font-family',
					'font-weight',
					'font-style',
					'font-size',
					'color',
					'line-height',
					'letter-spacing',
					'text-transform',
				];

				// Allow for margin on this setting if enabled.
				if ( isset( $array['margin'] ) && true === $array['margin'] ) {
					$attributes[] = 'margin';
				}

			}

			// Set attributes keys equal to vals.
			$attributes = \array_combine( $attributes, $attributes );

			// Exclude attributes.
			if ( ! empty( $array['exclude'] ) ) {
				foreach ( $array['exclude'] as $k => $v ) {
					unset( $attributes[ $v ] );
				}
			}

			// Define some vars before looping through attributes.
			$selector               = $array['target'];
			$css_vars               = $array['css_vars'] ?? [];
			$desktop_props          = '';
			$tablet_landscape_props = '';
			$tablet_portrait_props  = '';
			$phone_landscape_props  = '';
			$phone_portrait_props   = '';

			// Loop through attributes.
			foreach ( $attributes as $attribute ) {

				// Define attribute value.
				$val = $get_mod[ $attribute ] ?? null;

				if ( 'font-family' === $attribute && ! $this->is_font_supported( $val ) ) {
					$val = null;
				}

				// Val needed.
				if ( ! $val ) {
					continue;
				}

				// Sanitize property.
				$property = $css_vars[ $attribute ] ?? $attribute;

				// Font Sizes have responsive settings so we need to treat them differently.
				if ( 'font-size' === $attribute && \is_array( $val ) ) {

					$fontsize_pstyle = '';

					$responsive_bkpoints = [
						'd'  => '',
						'tl' => '',
						'tp' => '',
						'pl' => '',
						'pp' => '',
					];

					foreach ( $val as $k => $v ) {
						$val[ $k ] = $this->parse_attribute_val( $v, 'font-size' );
					}

					$val = \array_filter( $val );

					if ( ! $val ) {
						continue;
					}

					foreach ( $responsive_bkpoints as $bk_id => $bk_val ) {

						if ( ! empty( $val[ $bk_id ] ) ) {

							$bk_val = "{$property}:{$val[ $bk_id ]};";

							switch ( $bk_id ) {
								case 'd':
									if ( 'css' === $return ) {
										$desktop_props .= $bk_val;
									}
									$fontsize_pstyle .= "{$selector}{{$bk_val}}";
									break;
								case 'tl':
									if ( 'css' === $return ) {
										$tablet_landscape_props .= $bk_val;
									}
									$fontsize_pstyle .= "@media(max-width:1024px){{$selector}{{$bk_val};}}";
									break;
								case 'tp':
									if ( 'css' === $return ) {
										$tablet_portrait_props .= $bk_val;
									}
									$fontsize_pstyle .= "@media(max-width:959px){{$selector}{{$bk_val};}}";
									break;
								case 'pl':
									if ( 'css' === $return ) {
										$phone_landscape_props .= $bk_val;
									}
									$fontsize_pstyle .= "@media(max-width:767px){{$selector}{{$bk_val};}}";
									break;
								case 'pp':
									if ( 'css' === $return ) {
										$phone_portrait_props .= $bk_val;
									}
									$fontsize_pstyle .= "@media(max-width:479px){{$selector}{{$bk_val};}}";
									break;
							} // end switch

							if ( 'preview_styles' === $return ) {
								$preview_styles["wpex-customizer-{$element}-font-size"] = $fontsize_pstyle;
							}

						}

					}

				}

				// All other settings that aren't font sizes.
				else {

					// Parse the attribute value.
					$val = $this->parse_attribute_val( $val, $attribute );

					// No value for this setting.
					if ( ! $val ) {
						continue;
					}

					// Add to inline CSS.
					if ( 'margin' === $attribute && \str_contains( $val, ':' ) ) {
						$multi_prop_val = \totaltheme_parse_css_multi_property( $val, $attribute );
						if ( $multi_prop_val ) {
							$preview_css = '';
							foreach ( $multi_prop_val as $prop => $val ) {
								$property = $css_vars[ $prop ] ?? $prop;
								if ( 'css' === $return ) {
									$desktop_props .= "{$property}:{$val};";
								} elseif ( 'preview_styles' === $return ) {
									$preview_css .= "{$property}:{$val};";
								}
							}
						}
					} else {
						if ( 'css' === $return ) {
							$desktop_props .= "{$property}:{$val};";
						} elseif ( 'preview_styles' === $return ) {
							$preview_css = "{$property}:{$val};";
						}
					}

					// Customizer styles need to be added for each attribute.
					if ( 'preview_styles' === $return && ! empty( $preview_css ) ) {
						$preview_styles["wpex-customizer-{$element}-{$attribute}"] = "{$selector}{{$preview_css}}";
					}

				}

			} // End foreach attributes.

			// Add frontend responsive CSS.
			if ( $desktop_props ) {
				$css .= "{$selector}{{$desktop_props}}";
			}

			if ( $tablet_landscape_props ) {
				$tablet_landscape_css .= "{$selector}{{$tablet_landscape_props}}";
			}

			if ( $tablet_portrait_props ) {
				$tablet_portrait_css .=  "{$selector}{{$tablet_portrait_props}}";
			}

			if ( $phone_landscape_props ) {
				$phone_landscape_css .= "{$selector}{{$phone_landscape_props}}";
			}

			if ( $phone_portrait_props ) {
				$phone_portrait_css .= "{$selector}{{$phone_portrait_props}}";
			}

		} // End foreach settings.

		// Combine all settings CSS.
		if ( $css ) {
			$end_css .= $css;
		}

		// Combine all media query CSS for output.
		if ( $tablet_landscape_css ) {
			$end_css .= '@media(max-width:1024px){' . \esc_attr( $tablet_landscape_css ) . '}';
		}

		if ( $tablet_portrait_css ) {
			$end_css .= '@media(max-width:959px){' . \esc_attr( $tablet_portrait_css ) . '}';
		}

		if ( $phone_landscape_css ) {
			$end_css .= '@media(max-width:767px){' . \esc_attr( $phone_landscape_css ) . '}';
		}

		if ( $phone_portrait_css ) {
			$end_css .= '@media(max-width:479px){' . \esc_attr( $phone_portrait_css ) . '}';
		}

		// Add comment for typography css.
		if ( $css ) {
			$end_css = "/*TYPOGRAPHY*/{$end_css}";
		}

		switch ( $return ) {
			case 'css':
				return $end_css;
				break;
			case 'preview_styles':
				return $preview_styles;
				break;
		}

	}

	/**
	 * Outputs the typography custom CSS.
	 */
	public function frontend_css( $output ) {
		$typography_css = $this->loop( 'css' );
		if ( $typography_css ) {
			$output .= $typography_css;
		}
		return $output;
	}

	/**
	 * Returns correct CSS to output to wp_head.
	 */
	public function customize_css() {
		$styles = $this->loop( 'preview_styles' );

		if ( ! $styles || ! is_array( $styles ) ) {
			return;
		}

		foreach ( $styles as $style_key => $style_css ) {
			if ( empty( $style_css ) ) {
				continue;
			}
			echo '<style id="' . \esc_attr( $style_key ) . '">' . $style_css . '</style>';
		}
	}

	/**
	 * Loads Google fonts via wp_enqueue_style.
	 */
	public function frontend_enqueue_google_fonts() {
		$gfonts = [];

		if ( $custom_font = \get_theme_mod( 'load_custom_google_font_1' ) ) {
			$gfonts[] = $custom_font;
		}

		foreach ( $this->get_settings() as $setting_k => $setting_args ) {
			$mod_val = $this->get_setting_val( $setting_k, $setting_args );
			if ( empty( $mod_val['font-family'] )
				|| ! $this->setting_supports_css_property( 'font-family', $setting_args )
			) {
				continue;
			}
			$gfonts[] = $mod_val['font-family'];
		}

		if ( $gfonts ) {
			$gfonts = \array_unique( $gfonts );
			foreach ( $gfonts as $gfont ) {
				\wpex_enqueue_google_font( $gfont );
			}
		}
	}

	/**
	 * Return text transform choices.
	 */
	public function choices_text_transform() {
		return [
			''           => \esc_html__( 'Default', 'total' ),
			'capitalize' => \esc_html__( 'Capitalize', 'total' ),
			'lowercase'  => \esc_html__( 'Lowercase', 'total' ),
			'uppercase'  => \esc_html__( 'Uppercase', 'total' ),
			'none'       => \esc_html__( 'None', 'total' ),
		];
	}

	/**
	 * Return font style choices.
	 */
	public function choices_font_style() {
		return [
			''       => \esc_html__( 'Default', 'total' ),
			'normal' => \esc_html__( 'Normal', 'total' ),
			'italic' => \esc_html__( 'Italic', 'total' ),
		];
	}

	/**
	 * Return font weight choices.
	 */
	public function choices_font_weights() {
		return [
			''    => \esc_html__( 'Default', 'total' ),
			'100' => \esc_html__( 'Extra Light: 100', 'total' ),
			'200' => \esc_html__( 'Light: 200', 'total' ),
			'300' => \esc_html__( 'Book: 300', 'total' ),
			'400' => \esc_html__( 'Normal: 400', 'total' ),
			'500' => \esc_html__( 'Medium: 500', 'total' ),
			'600' => \esc_html__( 'Semibold: 600', 'total' ),
			'700' => \esc_html__( 'Bold: 700', 'total' ),
			'800' => \esc_html__( 'Extra Bold: 800', 'total' ),
			'900' => \esc_html__( 'Black: 900', 'total' ),
		];
	}

	/**
	 * Loads all registered fonts in the Customizer for use with Typography options.
	 */
	public function customize_enqueue_registered_fonts() {
		$user_fonts = (array) \wpex_get_registered_fonts();

		if ( empty( $user_fonts ) ) {
			return;
		}

		foreach ( $user_fonts as $user_font => $user_font_args ) {
			if ( 'other' === $user_font_args['type'] ) {
				continue;
			}
			\wpex_enqueue_font( $user_font, 'registered', $user_font_args );
		}
	}

	/**
	 * Return setting value.
	 */
	protected function get_setting_val( $setting = '' ) {
		return \get_theme_mod( "{$setting}_typography" );
	}

	/**
	 * Checks if a setting supports a specific CSS property.
	 */
	protected function setting_supports_css_property( $property = '', $setting_args = [] ): bool {
		return ( isset( $setting_args['attributes'][ $property ] ) || empty( $setting_args['exclude'] ) || ! \in_array( $property, $setting_args['exclude'] ) );
	}

	/**
	 * Parses an attribute value.
	 */
	protected function parse_attribute_val( $val = '', $attribute = '' ) {
		if ( $val && \is_string( $val ) ) {
			$val = \str_replace( '"', '', $val ); // remove any extra quotes (fix for older redux settings).
			return wpex_sanitize_data( $val, $attribute );
		}
	}

	/**
	 * Returns a list of supported fonts.
	 */
	protected function get_supported_fonts() {
		if ( null !== $this->supported_fonts ) {
			return $this->supported_fonts;
		}

		/**
		 * Support the disable Google font services function.
		 *
		 * If Google services is disabled and the user hasn't
		 * registered any fonts set the supported fonts to standard fonts
		 * plus any custom fonts added via legacy methods.
		 */
		if ( ! \wpex_has_google_services_support() && ! \wpex_has_registered_fonts() ) {
			$this->supported_fonts = \array_merge( \wpex_standard_fonts(), \wpex_add_custom_fonts() );
		} else {
			$this->supported_fonts = [];
		}

		return $this->supported_fonts;
	}

	/**
	 * Checks if a given font is supported.
	 *
	 * The font is supported if the supported_fonts array is empty or it exists in the array.
	 */
	protected function is_font_supported( $font ): bool {
		$supported_fonts = $this->get_supported_fonts();
		return ( ! $supported_fonts || \in_array( $font, $supported_fonts ) );
	}

	/**
	 * Checks setting condition.
	 */
	protected function check_condition( $condition ): bool {
		if ( \is_callable( $condition ) ) {
			return (bool) \call_user_func( $condition );
		}
		return (bool) $condition;
	}

	/**
	 * Parses a multi property theme mod.
	 */
	protected function parse_css_multi_property() {
		\_deprecated_function( __METHOD__, 'Total Theme 5.16' );
	}

}
