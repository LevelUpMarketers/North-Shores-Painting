<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Page Animations.
 */
final class Page_Animations {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		\add_filter( 'wpex_customizer_sections', [ self::class, 'customizer_settings' ] ); // @todo can we move to top of General tab?

		if ( self::is_enabled() || is_customize_preview() ) {
			\add_action( 'wp_enqueue_scripts', [ self::class, 'enqueue_scripts' ], 5 ); // load before theme scripts
			\add_action( 'wpex_outer_wrap_before', [ self::class, 'open_wrapper' ], 0 );
			\add_action( 'wpex_hook_main_before', [ self::class, 'open_wrapper' ] );
			\add_action( 'wpex_outer_wrap_after', [ self::class, 'close_wrapper' ], PHP_INT_MAX );
		}
	}

	/**
	 * Check if the functionality is enabled.
	 */
	public static function is_enabled(): bool {
		$check = self::get_in_animation() && ! \totaltheme_is_wpb_frontend_editor() && ! \totaltheme_is_elementor_editor();
		return (bool) \apply_filters( 'wpex_is_page_animations_enabled', $check );
	}

	/**
	 * Returns the in animation.
	 */
	public static function get_in_animation(): string {
		return (string) \apply_filters( 'wpex_page_animation_in', \get_theme_mod( 'page_animation_in' ) );
	}

	/**
	 * Returns the out animation.
	 */
	public static function get_out_animation(): string {
		return (string) \apply_filters( 'wpex_page_animation_out', \get_theme_mod( 'page_animation_out' ) );
	}

	/**
	 * Retrieves cached CSS or generates the responsive CSS.
	 */
	public static function enqueue_scripts(): void {
		if ( self::is_enabled() && $localize = self::localize() ) {
			\wp_enqueue_script(
				'wpex-page-animations',
				\totaltheme_get_js_file( 'frontend/page-animations' ),
				[],
				\WPEX_THEME_VERSION,
				true
			);

			\wp_localize_script(
				'wpex-page-animations',
				'wpex_page_animations_params',
				$localize
			);
		}
	}

	/**
	 * Localize script.
	 */
	public static function localize(): array {
		$visible_header = self::has_visible_header();

		$settings = [
			'inDuration'  => '600',
			'outDuration' => '400',
		//	'devMode'     => true,
		];

		// Animate In.
		$animate_in = self::get_in_animation();
		if ( $animate_in && \array_key_exists( $animate_in, self::in_transitions() ) ) {
			$settings['inClass'] = \esc_js( $animate_in );
		}

		// Animate out.
		$animate_out = self::get_out_animation();
		if ( $animate_out && \array_key_exists( $animate_out, self::out_transitions() ) ) {
			$settings['outClass'] = \esc_js( $animate_out );
		}

		// Custom Speed.
		$speed = ( $speed = \get_theme_mod( 'page_animation_speed' ) ) ? intval( $speed ) : '';
		if ( $speed || '0' == $speed ) {
			$settings['inDuration'] = \esc_js( $speed );
		}

		// New out speed setting.
		$speed = ( $speed = \get_theme_mod( 'page_animation_speed_out' ) ) ? intval( $speed ) : '';
		if ( $speed || '0' == $speed ) {
			$settings['outDuration'] = \esc_js( $speed );
		}

		$link_excludes = [
			// Link types.
			'[target="_blank"]',
			'[href^="#"]',
			'[href*="javascript"]',
			'[href*=".jpg"]',
			'[href*=".jpeg"]',
			'[href*=".gif"]',
			'[href*=".png"]',
			'[href*=".mov"]',
			'[href*=".swf"]',
			'[href*=".mp4"]',
			'[href*=".flv"]',
			'[href*=".avi"]',
			'[href*=".mp3"]',
			'[href^="mailto:"]',
			'[href*="?"]',
			'[href*="#localscroll"]',
			'[aria-controls]',
			'[data-ls_linkto]',
			'[role="button"]',
			'[data-vcex-type]',
			// Classes.
			'.wpex-lightbox',
			'.local-scroll-link',
			'.local-scroll',
			'.local-scroll a',
			'.sidr-class-local-scroll a',
			'.exclude-from-page-animation',
			'.wcmenucart',
			'.about_paypal',
			'.wpex-lightbox-gallery',
			'.wpb_single_image.wpex-lightbox a.vc_single_image-wrapper',
			'.wpex-dropdown-menu--onclick .menu-item-has-children > a',
			'#sidebar .widget_nav_menu .menu-item-has-children > a',
			'li.sidr-class-menu-item-has-children > a',
		//	'.mobile-toggle-nav-ul .menu-item-has-children > a',
			'.full-screen-overlay-nav-menu .menu-item-has-children > a',
		];

		$link_excludes = (array) \apply_filters( 'wpex_page_animations_excluded_links', $link_excludes );

		// Link Elements / The links that trigger the animation
		if ( $link_excludes ) {
			$link_excludes_sting = '';
			foreach ( $link_excludes as $exclude ) {
				$link_excludes_sting .= ":not($exclude)";
			}
			$settings['linkElement'] = 'a' . $link_excludes_sting;
		}

		$settings = \apply_filters( 'wpex_animsition_settings', $settings );

		return (array) $settings;
	}

	/**
	 * Open wrapper.
	 *
	 */
	public static function open_wrapper(): void {
		if ( ! self::is_enabled() ) {
			return;
		}

		$check = true;
		$current_filter = \current_filter();
		$visible_header = self::has_visible_header();
		switch ( $current_filter ) {
			case 'wpex_outer_wrap_before':
				if ( $visible_header ) {
					$check = false;
				}
				break;
			case 'wpex_hook_main_before':
				if ( ! $visible_header ) {
					$check = false;
				}
				break;
		}
		if ( $check ) { ?>
			<div class="wpex-page-animation-wrap">
				<style><?php self::inline_css(); ?></style>
				<?php self::render_loader(); ?>
				<div class="wpex-page-animation">
		<?php }
	}

	/**
	 * Close Wrapper.
	 *
	 */
	public static function close_wrapper(): void {
		if ( self::is_enabled() ) {
			echo '</div></div>';
		}
	}

	/**
	 * In Transitions.
	 *
	 */
	public static function in_transitions(): array {
		return [
			''                 => \esc_html__( 'None', 'total' ),
			'fade-in'          => \esc_html__( 'Fade In', 'total' ),
			'fade-in-up'       => \esc_html__( 'Fade In Up', 'total' ),
			'fade-in-up-sm'    => \esc_html__( 'Fade In Up Small', 'total' ),
			'fade-in-up-lg'    => \esc_html__( 'Fade In Up Large', 'total' ),
			'fade-in-down'     => \esc_html__( 'Fade In Down', 'total' ),
			'fade-in-down-sm'  => \esc_html__( 'Fade In Down Small', 'total' ),
			'fade-in-down-lg'  => \esc_html__( 'Fade In Down Large', 'total' ),
			'fade-in-left'     => \esc_html__( 'Fade In Left', 'total' ),
			'fade-in-left-sm'  => \esc_html__( 'Fade In Left Small', 'total' ),
			'fade-in-left-lg'  => \esc_html__( 'Fade In Left Large', 'total' ),
			'fade-in-right'    => \esc_html__( 'Fade In Right', 'total' ),
			'fade-in-right-sm' => \esc_html__( 'Fade In Right Small', 'total' ),
			'fade-in-right-lg' => \esc_html__( 'Fade In Right Large', 'total' ),
			'zoom-in'          => \esc_html__( 'Zoom In', 'total' ),
			'zoom-in-sm'       => \esc_html__( 'Zoom In Small', 'total' ),
			'zoom-in-lg'       => \esc_html__( 'Zoom In Large', 'total' ),
			'rotate-in'        => \esc_html__( 'Rotate In', 'total' ),
			'flip-in-x'        => \esc_html__( 'Flip In X', 'total' ),
			'flip-in-y'        => \esc_html__( 'Flip In Y', 'total' ),
		];
	}

	/**
	 * Out Transitions.
	 */
	public static function out_transitions(): array {
		return [
			''                  => \esc_html__( 'None', 'total' ),
			'fade-out'          => \esc_html__( 'Fade Out', 'total' ),
			'fade-out-up'       => \esc_html__( 'Fade Out Up', 'total' ),
			'fade-out-up-sm'    => \esc_html__( 'Fade Out Up Small', 'total' ),
			'fade-out-up-lg'    => \esc_html__( 'Fade Out Up Large', 'total' ),
			'fade-out-down'     => \esc_html__( 'Fade Out Down', 'total' ),
			'fade-out-down-sm'  => \esc_html__( 'Fade Out Down Small', 'total' ),
			'fade-out-down-lg'  => \esc_html__( 'Fade Out Down Large', 'total' ),
			'fade-out-left'     => \esc_html__( 'Fade Out Left', 'total' ),
			'fade-out-left-sm'  => \esc_html__( 'Fade Out Left Small', 'total' ),
			'fade-out-left-lg'  => \esc_html__( 'Fade Out Left Large', 'total' ),
			'fade-out-right'    => \esc_html__( 'Fade Out Right', 'total' ),
			'fade-out-right-sm' => \esc_html__( 'Fade Out Right Small', 'total' ),
			'fade-out-right-lg' => \esc_html__( 'Fade Out Right Large', 'total' ),
			'zoom-out'          => \esc_html__( 'Zoom Out', 'total' ),
			'zoom-out-sm'       => \esc_html__( 'Zoom Out Small', 'total' ),
			'zoom-out-lg'       => \esc_html__( 'Zoom Out Large', 'total' ),
			'rotate-out'        => \esc_html__( 'Rotate Out', 'total' ),
			'flip-out-x'        => \esc_html__( 'Flip Out X', 'total' ),
			'flip-out-y'        => \esc_html__( 'Flip Out Y', 'total' ),
		];
	}

	/**
	 * Adds customizer settings for the animations.
	 */
	public static function customizer_settings( $sections ) {
		$sections['wpex_page_animations'] = [
			'title' => \esc_html__( 'Page Animations (Site Loader)', 'total' ),
			'panel' => 'wpex_general',
			'description' => \esc_html__( 'This feature is disabled by default. Select an "In Animation" to enable. If you wish to enable but not animate select Fade In then set the In Speed to "0ms".', 'total' ),
			'settings' => [
				[
					'id' => 'page_animation_visible_header',
					'control' => [
						'label' => \esc_html__( 'Visible Header', 'total' ),
						'type' => 'totaltheme_toggle',
						'description' => \esc_html__( 'Enable this option to keep the header visible and only animate the site content. This functionality will not use AJAX to display the site inner content as that can break a lot of 3rd party plugins so the page is still re-loaded, the difference is that the header will be visible as the rest of the site loads. Disabled for any page using a transparent header.', 'total' ),
					],
				],
				[
					'id' => 'page_animation_in',
					'control' => [
						'label' => \esc_html__( 'In Animation', 'total' ),
						'type' => 'select',
						'choices' => self::in_transitions(),
					],
				],
				[
					'id' => 'page_animation_out',
					'control' => [
						'label' => \esc_html__( 'Out Animation', 'total' ),
						'type' => 'select',
						'choices' => self::out_transitions(),
					],
				],
				[
					'id' => 'page_animation_loading',
					'control' => [
						'label' => \esc_html__( 'Loading Text', 'total' ),
						'type' => 'text',
						'description' =>  \esc_html__( 'Replaces the loading icon.', 'total' ),
					],
				],
				[
					'id' => 'page_animation_speed',
					'control' => [
						'label' => \esc_html__( 'In Speed', 'total' ) . ' (ms)',
						'type' => 'text',
						'input_attrs' => [
							'placeholder' => '600ms',
						]
					],
				],
				[
					'id' => 'page_animation_speed_out',
					'control' => [
						'label' => \esc_html__( 'Out Speed', 'total' ) . ' (ms)',
						'type' => 'text',
						'input_attrs' => [
							'placeholder' => '400ms',
						]
					],
				],
				[
					'id' => 'page_animation_loader_speed',
					'transport' => 'postMessage',
					'control' => [
						'type' => 'text',
						'label' => \esc_html__( 'Loader Speed', 'total' ) . ' (ms)',
						'sanitize_callback' => 'absint',
						'input_attrs' => [
							'placeholder' => '1500ms',
						]
					],
					'inline_css' => [
						'target' => ':root',
						'alter' => '--wpex-page-animation-loader-speed',
						'sanitize' => 'ms',
					],
				],
				[
					'id' => 'page_animation_loader_size',
					'transport' => 'postMessage',
					'control' => [
						'type' => 'totaltheme_length_unit',
						'units' => [ 'px' ],
						'label' => \esc_html__( 'Loader Size', 'total' ),
						'placeholder' => '35',
					],
					'inline_css' => [
						'target' => ':root',
						'alter' => '--wpex-page-animation-loader-size',
						'sanitize' => 'px',
					],
				],
				[
					'id' => 'page_animation_loader_width',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Loader Width', 'total' ),
						'type' => 'totaltheme_length_unit',
						'units' => [ 'px' ],
						'placeholder' => '3',
					],
					'inline_css' => [
						'target' => ':root',
						'alter'  => '--wpex-page-animation-loader-width',
						'sanitize' => 'px',
					],
				],
				[
					'id' => 'page_animation_color',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Loader Color', 'total' ),
						'type' => 'totaltheme_color',
					],
					'inline_css' => [
						'target' => ':root',
						'alter'  => '--wpex-page-animation-loader-accent',
					],
				],
				[
					'id' => 'page_animation_loader_inner_color',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Loader Inner Color', 'total' ),
						'type' => 'totaltheme_color',
					],
					'inline_css' => [
						'target' => ':root',
						'alter'  => '--wpex-page-animation-loader-color',
					],
				],
			],
		];

		return $sections;
	}

	/**
	 * Check if the header should be visible.
	 *
	 */
	private static function has_visible_header() {
		$visible_header = \get_theme_mod( 'page_animation_visible_header' );
		if ( $visible_header && ! totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Render loader icon.
	 */
	private static function render_loader() {
		$custom_text = \wpex_get_translated_theme_mod( 'page_animation_loading' );
		if ( $custom_text ) {
			$loader = $custom_text;
		} else {
			$loader = '<span class="wpex-page-animation__loader"></span>';

			/**
			 * Filters the page animations loader html.
			 *
			 * @param string $loader
			 */
			$loader = (string) \apply_filters( 'wpex_page_animations_loader_html', $loader );
		}
		echo '<span class="wpex-page-animation__loading">' . $loader .'</span>';
	}

	/**
	 * Returns page animation CSS added inline to speed things up and prevent render blocking CSS.
	 */
	public static function inline_css() {
		echo '.wpex-page-animation-wrap::after{content:"";display:block;height:0;clear:both;visibility:hidden}.wpex-page-animation{position:relative;opacity:0;animation-fill-mode:both}.wpex-page-animation--complete,.wpex-page-animation--persisted{opacity:1}.wpex-page-animation__loading{position:fixed;top:50%;width:100%;height:100%;text-align:center;left:0;font-size:var(--wpex-text-3xl)}.wpex-page-animation__loading--hidden{opacity:0}.wpex-page-animation__loader,.wpex-page-animation__loader:after{width:var(--wpex-page-animation-loader-size, 40px);height:var(--wpex-page-animation-loader-size, 40px);position:fixed;top:50%;left:50%;margin-top:calc(-1 * (var(--wpex-page-animation-loader-size, 40px) / 2));margin-left:calc(-1 * (var(--wpex-page-animation-loader-size, 40px) / 2));border-radius:50%;z-index:2}.wpex-page-animation__loader{background-color:transparent;border-top:var(--wpex-page-animation-loader-width, 2px) solid var(--wpex-page-animation-loader-accent, var(--wpex-accent));border-right:var(--wpex-page-animation-loader-width, 2px) solid var(--wpex-page-animation-loader-accent, var(--wpex-accent));border-bottom:var(--wpex-page-animation-loader-width, 2px) solid var(--wpex-page-animation-loader-accent, var(--wpex-accent));border-left:var(--wpex-page-animation-loader-width, 2px) solid var(--wpex-page-animation-loader-color, var(--wpex-surface-3));transform:translateZ(0);animation-iteration-count:infinite;animation-timing-function:linear;animation-duration:var(--wpex-page-animation-loader-speed, 1.5s);animation-name:wpex-pa-loader-icon}@keyframes wpex-pa-loader-icon{0%{transform:rotate(0deg)}to{transform:rotate(1turn)}}#wrap .wpex-page-animation-wrap{position:relative}#wrap .wpex-page-animation__loading{position:absolute;top:calc(50vh - var(--wpex-header-height, 100px));height:auto}';
		echo self::get_animation_css( self::get_in_animation() );
		echo self::get_animation_css( self::get_out_animation() );
	}

	/**
	 * Returns animation specific CSS.
	 */
	public static function get_animation_css( $animation = '' ) {
		switch ( $animation ) {
			case 'fade-in':
				return '@keyframes wpex-pa-fade-in{0%{opacity:0}to{opacity:1}}.wpex-page-animation--fade-in{animation-name:wpex-pa-fade-in}';
				break;
			case 'fade-out':
				return '@keyframes wpex-pa-fade-out{0%{opacity:1}to{opacity:0}}.wpex-page-animation--fade-out{animation-name:wpex-pa-fade-out}';
				break;
			case 'fade-in-up':
				return '@keyframes wpex-pa-fade-in-up{0%{transform:translateY(500px);opacity:0}to{transform:translateY(0);opacity:1}}.wpex-page-animation--fade-in-up{animation-name:wpex-pa-fade-in-up}';
				break;
			case 'fade-out-up':
				return '@keyframes wpex-pa-fade-out-up{0%{transform:translateY(0);opacity:1}to{transform:translateY(-500px);opacity:0}}.wpex-page-animation--fade-out-up{animation-name:wpex-pa-fade-out-up}';
				break;
			case 'fade-in-up-sm':
				return '@keyframes wpex-pa-fade-in-up-sm{0%{transform:translateY(100px);opacity:0}to{transform:translateY(0);opacity:1}}.wpex-page-animation--fade-in-up-sm{animation-name:wpex-pa-fade-in-up-sm}';
				break;
			case 'fade-out-up-sm':
				return '@keyframes wpex-pa-fade-out-up-sm{0%{transform:translateY(0);opacity:1}to{transform:translateY(-100px);opacity:0}}.wpex-page-animation--fade-out-up-sm{animation-name:wpex-pa-fade-out-up-sm}';
				break;
			case 'fade-in-up-lg':
				return '@keyframes wpex-pa-fade-in-up-lg{0%{transform:translateY(1000px);opacity:0}to{transform:translateY(0);opacity:1}}.wpex-page-animation--fade-in-up-lg{animation-name:wpex-pa-fade-in-up-lg}';
				break;
			case 'fade-out-up-lg':
				return '@keyframes wpex-pa-fade-out-up-lg{0%{transform:translateY(0);opacity:1}to{transform:translateY(-1000px);opacity:0}}.wpex-page-animation--fade-out-up-lg{animation-name:wpex-pa-fade-out-up-lg}';
				break;
			case 'fade-in-down':
				return '@keyframes wpex-pa-fade-in-down{0%{transform:translateY(-500px);opacity:0}to{transform:translateY(0);opacity:1}}.wpex-page-animation--fade-in-down,.wpex-page-animation--fade-in-down-lg{animation-name:wpex-pa-fade-in-down}';
				break;
			case 'fade-out-down':
				return '@keyframes wpex-pa-fade-out-down{0%{transform:translateY(0);opacity:1}to{transform:translateY(500px);opacity:0}}.wpex-page-animation--fade-out-down{animation-name:wpex-pa-fade-out-down}';
				break;
			case 'fade-in-down-sm':
				return '@keyframes wpex-pa-fade-in-down-sm{0%{transform:translateY(-100px);opacity:0}to{transform:translateY(0);opacity:1}}.wpex-page-animation--fade-in-down-sm{animation-name:wpex-pa-fade-in-down-sm}';
				break;
			case 'fade-out-down-sm':
				return '@keyframes wpex-pa-fade-out-down-sm{0%{transform:translateY(0);opacity:1}to{transform:translateY(100px);opacity:0}}.wpex-page-animation--fade-out-down-sm{animation-name:wpex-pa-fade-out-down-sm}';
				break;
			case 'fade-out-down-lg':
				return '@keyframes wpex-pa-fade-out-down-lg{0%{transform:translateY(0);opacity:1}to{transform:translateY(1000px);opacity:0}}.wpex-page-animation--fade-out-down-lg{animation-name:wpex-pa-fade-out-down-lg}';
				break;
			case 'fade-in-left':
				return '@keyframes wpex-pa-fade-in-left{0%{transform:translateX(-500px);opacity:0}to{transform:translateX(0);opacity:1}}.wpex-page-animation--fade-in-left{animation-name:wpex-pa-fade-in-left}';
				break;
			case 'fade-out-left':
				return '@keyframes wpex-pa-fade-out-left{0%{transform:translateX(0);opacity:1}to{transform:translateX(-500px);opacity:0}}.wpex-page-animation--fade-out-left{animation-name:wpex-pa-fade-out-left}';
				break;
			case 'fade-in-left-sm':
				return '@keyframes wpex-pa-fade-in-left-sm{0%{transform:translateX(-100px);opacity:0}to{transform:translateX(0);opacity:1}}.wpex-page-animation--fade-in-left-sm{animation-name:wpex-pa-fade-in-left-sm}';
				break;
			case 'fade-out-left-sm':
				return '@keyframes wpex-pa-fade-out-left-sm{0%{transform:translateX(0);opacity:1}to{transform:translateX(-100px);opacity:0}}.wpex-page-animation--fade-out-left-sm{animation-name:wpex-pa-fade-out-left-sm}';
				break;
			case 'fade-in-left-lg':
				return '@keyframes wpex-pa-fade-in-left-lg{0%{transform:translateX(-1600px);opacity:0}to{transform:translateX(0);opacity:1}}.wpex-page-animation--fade-in-left-lg{animation-name:wpex-pa-fade-in-left-lg}';
				break;
			case 'fade-out-left-lg':
				return '@keyframes wpex-pa-fade-out-left-lg{0%{transform:translateX(0);opacity:1}to{transform:translateX(-1600px);opacity:0}}.wpex-page-animation--fade-out-left-lg{animation-name:wpex-pa-fade-out-left-lg}';
				break;
			case 'fade-in-right':
				return '@keyframes wpex-pa-fade-in-right{0%{transform:translateX(500px);opacity:0}to{transform:translateX(0);opacity:1}}.wpex-page-animation--fade-in-right{animation-name:wpex-pa-fade-in-right}';
				break;
			case 'fade-out-right':
				return '@keyframes wpex-pa-fade-out-right{0%{transform:translateX(0);opacity:1}to{transform:translateX(500px);opacity:0}}.wpex-page-animation--fade-out-right{animation-name:wpex-pa-fade-out-right}';
				break;
			case 'fade-in-right-sm':
				return '@keyframes wpex-pa-fade-in-right-sm{0%{transform:translateX(100px);opacity:0}to{transform:translateX(0);opacity:1}}.wpex-page-animation--fade-in-right-sm{animation-name:wpex-pa-fade-in-right-sm}';
				break;
			case 'fade-out-right-sm':
				return '@keyframes wpex-pa-fade-out-right-sm{0%{transform:translateX(0);opacity:1}to{transform:translateX(100px);opacity:0}}.wpex-page-animation--fade-out-right-sm{animation-name:wpex-pa-fade-out-right-sm}';
				break;
			case 'fade-in-right-lg':
				return '@keyframes wpex-pa-fade-in-right-lg{0%{transform:translateX(1500px);opacity:0}to{transform:translateX(0);opacity:1}}.wpex-page-animation--fade-in-right-lg{animation-name:wpex-pa-fade-in-right-lg}';
				break;
			case 'fade-out-right-lg':
				return '@keyframes wpex-pa-fade-out-right-lg{0%{transform:translateX(0);opacity:1}to{transform:translateX(1500px);opacity:0}}.wpex-page-animation--fade-out-right-lg{animation-name:wpex-pa-fade-out-right-lg}';
				break;
			case 'rotate-in':
				return '@keyframes wpex-pa-rotate-in{0%{transform:rotate(-45deg);transform-origin:center center;opacity:0}to{transform:rotate(0);transform-origin:center center;opacity:1}}.wpex-page-animation--rotate-in{animation-name:wpex-pa-rotate-in}';
				break;
			case 'rotate-out':
				return '@keyframes wpex-pa-rotate-out{0%{transform:rotate(0);transform-origin:center center;opacity:1}to{transform:rotate(45deg);transform-origin:center center;opacity:0}}.wpex-page-animation--rotate-out{animation-name:wpex-pa-rotate-out}';
				break;
			case 'flip-in-y':
				return '@keyframes wpex-pa-flip-in-y{0%{transform:perspective(550px) rotateY(90deg);opacity:0}100%{transform:perspective(550px) rotateY(0);opacity:1}}.wpex-page-animation--flip-in-y{animation-name:wpex-pa-flip-in-y;backface-visibility:visible}';
				break;
			case 'flip-in-x':
				return '@keyframes wpex-pa-flip-in-x{0%{transform:perspective(550px) rotateX(90deg);opacity:0}to{transform:perspective(550px) rotateX(0);opacity:1}}.wpex-page-animation--flip-in-x{animation-name:wpex-pa-flip-in-x;backface-visibility:visible}';
				break;
			case 'flip-out-y':
				return '@keyframes wpex-pa-flip-out-y{0%{transform:perspective(550px) rotateY(0);opacity:1}to{transform:perspective(550px) rotateY(90deg);opacity:0}}.wpex-page-animation--flip-out-y{animation-name:wpex-pa-flip-out-y;backface-visibility:visible}';
				break;
			case 'flip-out-x':
				return '@keyframes wpex-pa-flip-out-x{0%{transform:perspective(550px) rotateX(0);opacity:1}to{transform:perspective(550px) rotateX(90deg);opacity:0}}.wpex-page-animation--flip-out-x{animation-name:wpex-pa-flip-out-x;backface-visibility:visible}';
				break;
			case 'zoom-in':
				return '@keyframes wpex-pa-zoom-in{0%{transform:scale(0.7);opacity:0}to{opacity:1}}.wpex-page-animation--zoom-in{animation-name:wpex-pa-zoom-in}';
				break;
			case 'zoom-out':
				return '@keyframes wpex-pa-zoom-out{0%{transform:scale(1);opacity:1}50%{transform:scale(0.7)}50%,to{opacity:0}}.wpex-page-animation--zoom-out{animation-name:wpex-pa-zoom-out}';
				break;
			case 'zoom-in-sm':
				return '@keyframes wpex-pa-zoom-in-sm{0%{transform:scale(0.95);opacity:0}to{opacity:1}}.wpex-page-animation--zoom-in-sm{animation-name:wpex-pa-zoom-in-sm}';
				break;
			case 'zoom-out-sm':
				return '@keyframes wpex-pa-zoom-out-sm{0%{transform:scale(1);opacity:1}50%{transform:scale(0.95)}50%,to{opacity:0}}.wpex-page-animation--zoom-out-sm{animation-name:wpex-pa-zoom-out-sm}';
				break;
			case 'zoom-in-lg':
				return '@keyframes wpex-pa-zoom-in-lg{0%{transform:scale(0.4);opacity:0}to{opacity:1}}.wpex-page-animation--zoom-in-lg{animation-name:wpex-pa-zoom-in-lg}';
				break;
			case 'zoom-out-lg':
				return '@keyframes wpex-pa-zoom-out-lg{0%{transform:scale(1);opacity:1}50%{transform:scale(0.4)}50%,to{opacity:0}}.wpex-page-animation--zoom-out-lg{animation-name:wpex-pa-zoom-out-lg}';
				break;
		}
	}

}
