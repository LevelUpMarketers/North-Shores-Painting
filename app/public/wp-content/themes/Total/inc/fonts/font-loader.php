<?php

namespace TotalTheme\Fonts;

\defined( 'ABSPATH' ) || exit;

/**
 * Font_Loader Class.
 */
class Font_Loader {

	/**
	 * Array of already loaded fonts.
	 */
	static $loaded_fonts = [];

	/**
	 * Font to load.
	 */
	public $font = '';

	/**
	 * Font type.
	 */
	public $type = '';

	/**
	 * Font args.
	 */
	public $font_args = '';

	/**
	 * Font URL.
	 */
	public $font_url = '';

	/**
	 * Main constructor.
	 */
	public function __construct( $font, $type = '', $args = [] ) {
		$this->font      = $font;
		$this->type      = $type;
		$this->font_args = $args;

		if ( $this->font ) {
			if ( \array_key_exists( $this->font, self::$loaded_fonts ) ) {
				$this->font_url = self::$loaded_fonts[$this->font];
			} else {
				$this->load_font();
				if ( ! empty( $this->font_url ) ) {
					self::$loaded_fonts[$this->font] = $this->font_url;
				}
			}
		}
	}

	/**
	 * Load the font.
	 */
	public function load_font() {
		if ( empty( $this->font_args ) ) {
			$registered_fonts = \wpex_get_registered_fonts();
			if ( isset( $registered_fonts[ $this->font ] ) ) {
				$this->type = 'registered';
				$this->font_args = $registered_fonts[ $this->font ];
			}
		}

		$this->type = $this->type ?: 'google'; // Google is the fallback font type.

		if ( 'registered' === $this->type ) {
			$this->load_custom_font();
		} else {
			$this->load_theme_font();
		}
	}

	/**
	 * Load a theme font.
	 */
	public function load_theme_font() {
		if ( 'google' === $this->type ) {

			$gfonts = \wpex_google_fonts_array();

			if ( empty( $gfonts ) || ! \is_array( $gfonts ) ) {
				return;
			}

			if ( 'Sansita One' === $this->font ) {
				$font = 'Sansita'; // renamed font.
			}

			if ( \in_array( $this->font, $gfonts, true ) ) {
				$this->enqueue_google_font( $this->font );
			}
		}
	}

	/**
	 * Load a custom font.
	 */
	public function load_custom_font() {
		$font = $this->font;
		$args = $this->font_args;
		$type = ! empty( $args['type'] ) ? $args['type'] : '';

		if ( $type ) {
			$method = "enqueue_{$type}_font";
			if ( \method_exists( $this, $method ) ) {
				return $this->$method( $font, $args );
			}
		}
	}

	/**
	 * Enqueue google font.
	 */
	public function enqueue_google_font( $font, $args = [] ) {
		if ( ! \wpex_has_google_services_support() ) {
			return;
		}

		// Define default Google font args.
		$default_args = [
			'weights' => [
				'100',
				'200',
				'300',
				'400',
				'500',
				'600',
				'700',
				'800',
				'900',
			],
			'italic'  => true,
			'subset'  => \get_theme_mod( 'google_font_subsets', [ 'latin' ] ),
			'display' => (string) \get_theme_mod( 'google_font_display', 'swap' ),
		];

		// Parse args and extract.
		\extract( \wp_parse_args( $args, $default_args ) );

		// Check allowed font weights.
		$weights = \apply_filters( 'wpex_google_font_enqueue_weights', $weights, $font );
		$weights = \is_array( $weights ) ? $weights : \explode( ',', $weights );

		// Check if we should get italic fonts.
		$italic = \apply_filters( 'wpex_google_font_enqueue_italics', $italic, $font );

		// Check the subsets to load.
		$subset = \apply_filters( 'wpex_google_font_enqueue_subsets', $subset, $font );
		$subset = \is_array( $subset ) ? $subset : \explode( ',', $subset );

		// Check font display type.
		$display = (string) \apply_filters( 'wpex_google_font_enqueue_display', $display, $font );

		// Define Google Font URL.
		$url = \wpex_get_google_fonts_url() . '/css2?family=' . \str_replace( ' ', '%20', $this->sanitize_google_font_name( $font ) );

		// Font with variables.
		if ( ! empty( $weights ) ) {
			$weights_count = count( $weights );
			
			if ( 1 === $weights_count && 400 === $weights[0] ) {
				$url .= $italic ? 'ital@0;1' : '@0;1';
			} else {
				$url .= $italic ? ':ital,wght@' : ':wght@';

				$weight_axes = [];
				$weight_axes_sep = ';';

				if ( $italic ) {
					foreach ( $weights as $weight ) {
						$weight_axes[] = "0,{$weight}";
					}
					foreach ( $weights as $weight ) {
						$weight_axes[] = "1,{$weight}";
					}
				} else {
					foreach ( $weights as $weight ) {
						$weight_axes[] = $weight;
					}
				}

				$url .= \implode( $weight_axes_sep, $weight_axes );
			}

		}

		// Add font display.
		if ( $display && in_array( $display, $this->get_display_choices(), true ) ) {
			$url .= "&display={$display}";
		}

		// Add subsets.
		if ( $subset && $subset_sanitized = array_filter( $subset, [ $this, 'sanitize_subset_array' ] ) ) {
			$subset_string = implode( ', ', $subset_sanitized );
			$url .= "&subset={$subset_string}";
		}

		// Update $font_url var.
		$this->font_url = \esc_url( $url );

		// Enqueue the font.
		\wp_enqueue_style(
			'wpex-google-font-' . $this->get_font_handle( $font ),
			$this->font_url,
			[],
			null // important
		);
	}

	/**
	 * Enqueue adobe font.
	 */
	public function enqueue_adobe_font( $font, $args = [] ) {
		if ( empty( $args['project_id'] ) ) {
			return;
		}

		$project_id_safe = \sanitize_text_field( $args['project_id'] );
		$this->font_url  = \esc_url( "https://use.typekit.net/{$project_id_safe}.css" );

		\wp_enqueue_style(
			"typekit-{$project_id_safe}",
			$this->font_url,
			[],
			null
		);
	}

	/**
	 * Return font handle for enqueue.
	 */
	public function get_font_handle( string $font ): string {
		return \str_replace( ' ', '-', \strtolower( \trim( $font ) ) );
	}

	/**
	 * Sanitize Google Font Name
	 */
	public function sanitize_google_font_name( string $font ): string {
		return \str_replace( ' ', '+', \trim( $font ) );
	}

	/**
	 * Enqueues all registered fonts.
	 */
	public static function enqueue_all_registered_fonts(): void {
		foreach ( \wpex_get_registered_fonts() as $font_name => $font_args ) {
			new self( $font_name, 'registered', $font_args );
		}
	}

	/**
	 * Returns display choices.
	 */
	private function get_display_choices(): array {
		return [
			'auto',
			'block',
			'swap',
			'fallback',
			'optional',
		];
	}

	/**
	 * Returns subset choices.
	 */
	private function get_subset_choices(): array {
		return [
			'latin',
			'latin-ext',
			'cyrillic',
			'cyrillic-ext',
			'greek',
			'greek-ext',
			'vietnamese',
		];
	}

	/**
	 * Sanitize 
	 */
	private function sanitize_subset_array( $value ): bool {
		return in_array( $value, $this->get_subset_choices(), true );
	}

}
