<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Lightbox.
 */
class Lightbox {

	/**
	 * Register fancybox css.
	 */
	public static function register_css(): void {
		\wp_register_style(
			'fancybox',
			\totaltheme_get_css_file( 'vendor/jquery.fancybox' ),
			[],
			'3.5.7'
		);
		\wp_register_style(
			'wpex-fancybox',
			\totaltheme_get_css_file( 'frontend/fancybox' ),
			[ 'fancybox' ],
			WPEX_THEME_VERSION
		);
	}

	/**
	 * Register fancybox script.
	 */
	public static function register_js(): void {
		\wp_register_script(
			'fancybox',
			\totaltheme_get_js_file( 'vendor/jquery.fancybox' ),
			[ 'jquery' ],
			'3.5.7',
			true
		);

		\wp_register_script(
			'wpex-fancybox',
			\totaltheme_get_js_file( 'frontend/fancybox' ),
			[ 'jquery', 'fancybox' ],
			\WPEX_THEME_VERSION,
			true
		);

		\wp_localize_script(
			'wpex-fancybox',
			'wpex_fancybox_params',
			self::get_l10n()
		);

		if ( \get_theme_mod( 'lightbox_auto', false ) ) {
			self::register_auto_lightbox();
		}
	}

	/**
	 * Register auto lightbox.
	 */
	public static function register_auto_lightbox(): void {
		\wp_register_script(
			'wpex-auto-lightbox',
			\totaltheme_get_js_file( 'frontend/auto-lightbox' ),
			[ 'jquery', 'fancybox' ],
			\WPEX_THEME_VERSION,
			true
		);

		$auto_lightbox_targets = '.wpb_text_column a > img, body.no-composer .entry a > img';

		$auto_lightbox_targets = \apply_filters( 'wpex_auto_lightbox_targets', $auto_lightbox_targets );

		\wp_localize_script(
			'wpex-auto-lightbox',
			'wpex_autolightbox_params',
			[
				'targets' => $auto_lightbox_targets,
			]
		);
	}

	/**
	 * Enqueue Global Scripts.
	 */
	public static function enqueue_global_scripts(): void {
		if ( self::maybe_enqueue_scripts_globally() ) {
			\wpex_enqueue_lightbox_scripts();
		}

		if ( \get_theme_mod( 'lightbox_auto', false ) ) {
			\wp_enqueue_script( 'wpex-auto-lightbox' );
		}
	}

	/**
	 * Check if scripts should load globally.
	 */
	protected static function maybe_enqueue_scripts_globally(): bool {
		if ( \get_theme_mod( 'lightbox_auto', false ) ) {
			return true;
		}
		$check = \get_theme_mod( 'lightbox_load_style_globally', false );
		return (bool) \apply_filters( 'wpex_load_ilightbox_globally', $check );
	}

	/**
	 * Enqueue scripts.
	 */
	public static function enqueue_scripts(): void {
		self::enqueue_fancybox();
		\do_action( 'wpex_enqueue_lightbox_scripts' );
	}

	/**
	 * Enqueue fancybox.
	 */
	public static function enqueue_fancybox( $initialize = true ): void {
		\wp_enqueue_style( 'fancybox' );
		\wp_enqueue_style( 'wpex-fancybox' );
		\wp_enqueue_script( 'fancybox' );

		$skin = \get_theme_mod( 'lightbox_skin' );

		if ( 'light' === $skin ) {
			\wp_enqueue_style(
				'wpex-fancybox-light',
				\totaltheme_get_css_file( 'frontend/fancybox-skins/light' ),
				[ 'fancybox' ],
				'1.0'
			);
		}

		if ( $initialize ) {
			\wp_enqueue_script( 'wpex-fancybox' );
		}
	}

	/**
	 * Returns l10n.
	 */
	protected static function get_l10n(): array {
		$animationDuration = \absint( \get_theme_mod( 'lightbox_animation_duration', 366 ) );

		$l10n = [
			'animationEffect' => 0 === $animationDuration ? '0' : 'fade', // 0, zoom, fade, zoom-in-out
			'zoomOpacity' => 'auto', // If opacity is "auto", then opacity will be changed if image and thumbnail have different aspect ratios
			'animationDuration' => $animationDuration,
			'transitionEffect' => \esc_js( \get_theme_mod( 'lightbox_transition_effect' ) ?: 'fade' ),
			'transitionDuration' => \absint( \get_theme_mod( 'lightbox_transition_duration' ) ?: 366 ),
			'gutter' => \absint( 50 ),
			'loop' => \wp_validate_boolean( \get_theme_mod( 'lightbox_loop', false ) ),
			'arrows' => \wp_validate_boolean( \get_theme_mod( 'lightbox_arrows', true ) ),
			'infobar' => \wp_validate_boolean( true ),
			'smallBtn' => 'auto',
			'backFocus' => false, // the theme handles this.
			'closeExisting' => true, // prevent multiple instance stacking
			//'preventCaptionOverlap' => true, // causes jumpiness on first item
			'buttons' => [
				'zoom',
				'slideShow',
				'close',
				//'share',
			],
			'slideShow' => [
				'autoStart' => \wp_validate_boolean( \get_theme_mod( 'lightbox_slideshow_autostart', false ) ),
				'speed' => \absint( \get_theme_mod( 'lightbox_slideshow_speed', 3000 ) ),
			],
			'lang' => 'en',
			'i18n' => [
				'en' => [
					'CLOSE' => \esc_html__( 'Close', 'total' ),
					'NEXT' => \esc_html__( 'Next', 'total' ),
					'PREV' => \esc_html__( 'Previous', 'total' ),
					'ERROR' => \esc_html__( 'The requested content cannot be loaded. Please try again later.', 'total' ),
					'PLAY_START' => \esc_html__( 'Start slideshow', 'total' ),
					'PLAY_STOP' => \esc_html__( 'Pause slideshow', 'total' ),
					'FULL_SCREEN' => \esc_html__( 'Full screen', 'total' ),
					'THUMBS' => \esc_html__( 'Thumbnails', 'total' ),
					'DOWNLOAD' => \esc_html__( 'Download', 'total' ),
					'SHARE' => \esc_html__( 'Share', 'total' ),
					'ZOOM' => \esc_html__( 'Zoom', 'total' ),
					'DIALOG_ARIA' => \esc_html__( 'You can close this modal content with the ESC key', 'total' ),
				],
			],
		];

		if ( \wp_validate_boolean( \get_theme_mod( 'lightbox_thumbnails', true ) ) ) {
			$l10n['buttons'][] = 'thumbs';
			$l10n['thumbs'] = [
				'autoStart' => \wp_validate_boolean( \get_theme_mod( 'lightbox_thumbnails_auto_start', false ) ),
				'hideOnClose' => \wp_validate_boolean( true ),
				'axis' => 'y',
			];
		}

		if ( \wp_validate_boolean( \get_theme_mod( 'lightbox_fullscreen', false ) ) ) {
			$l10n['buttons'][] = 'fullScreen';
		}

		$l10n = \apply_filters( 'wpex_get_lightbox_settings', $l10n ); // @deprecated
		$l10n = \apply_filters( 'wpex_lightbox_settings', $l10n ); // @deprecated

		return (array) \apply_filters( 'totaltheme/lightbox/l10n', $l10n );
	}

}
