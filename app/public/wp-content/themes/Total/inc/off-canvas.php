<?php declare(strict_types=1);

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Off_Canvas Class.
 */
final class Off_Canvas {

	/**
	 * Args.
	 */
	protected $args = [];

	/**
	 * Content.
	 */
	protected $content = '';

	/**
	 * Footer.
	 */
	protected $footer = '';

	/**
	 * Default args.
	 */
	protected $defaults = [
		'class'                  => '',
		'width'                   => '',
		'title'                   => '',
		'logo'                    => '',
		'surface'                 => '',
		'on_open_focus'           => '',
		'close_button_aria_label' => '',
		'visibility'              => '',
		'close_button_icon_size'  => 'xl',
		'id'                      => 'wpex-off-canvas',
		'padding'                 => '30',
		'placement'               => 'left',
		'close_button_icon'       => 'material-close',
		'transition_duration'     => '350',
		'under_header'            => false,
		'contain'                 => false,
		'top_border'              => true,
		'bottom_border'           => false,
		'close_button'            => true,
		'auto_insert'             => true,
		'backdrop'                => true,
		'backdrop_blur'           => false,
		'inner_scroll'            => false,
		'fixed_footer'            => true,
	];

	/**
	 * Constructor.
	 */
	public function __construct( array $args, string $content, string $footer = '' ) {
		$this->args    = \wp_parse_args( $args, $this->defaults );
		$this->content = $content;
		$this->footer  = $footer;

		$this->enqueue_scripts();

		if ( $this->args['auto_insert'] ) {
			\add_action( 'wp_footer', [ $this, 'renderer' ] );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	private function enqueue_scripts(): void {
		\wp_enqueue_script( 'wpex-off-canvas' );
	}

	/**
	 * Renderer.
	 */
	public function renderer(): void {
		echo $this->render();
	}

	/**
	 * Render HTML.
	 */
	public function render(): ?string {
		if ( ! $this->content ) {
			return null;
		}

		$wrap_class = [];
		$placement  = $this->args['placement'];

		if ( ! empty( $this->args['class'] ) ) {
			$wrap_class[] = \sanitize_text_field( \is_array( $this->args['class'] ) ? implode( ' ', $this->args['class'] ) : $this->args['class'] );
		}

		$wrap_class[] = 'wpex-off-canvas';
		$wrap_class[] = 'wpex-z-off-canvas';

		if ( $this->args['surface'] ) {
			$wrap_class[] = \sanitize_html_class( "wpex-surface-{$this->args['surface']}" );
		}

		if ( $this->args['class'] ) {
			$wrap_class[] = \esc_attr( $this->args['class'] );
		}

		if ( $this->args['inner_scroll'] ) {
			$wrap_class[] = 'wpex-off-canvas--innerscroll';
		}

		if ( $this->args['under_header'] ) {
			$wrap_class[] = 'wpex-off-canvas--under-header';
		}

		$wrap_class[] = 'wpex-overscroll-contain';
		$wrap_class[] = 'wpex-hide-scrollbar';
		$wrap_class[] = 'wpex-fixed';
		$wrap_class[] = "wpex-{$placement}-0";
		$wrap_class[] = 'wpex-overflow-auto';
		$wrap_class[] = 'wpex-surface-1';

		$wrap_class[] = 'wpex-invisible';
		$wrap_class[] = 'wpex-ease-in-out';
		$wrap_class[] = 'wpex-duration-' . \absint( $this->args['transition_duration'] );
		$wrap_class[] = 'left' === $placement ? '-wpex-translate-x-100' : 'wpex-translate-x-100';

		if ( $this->args['visibility'] ) {
			$wrap_class[] = \totaltheme_get_visibility_class( $this->args['visibility'] );
		}

		$html = '<div id="' . \esc_attr( $this->args['id'] ) .'" class="' . \esc_attr( \implode( ' ', $wrap_class ) ) . '" data-wpex-off-canvas-speed="' . \esc_attr( \absint( $this->args['transition_duration'] ) ) . '"';
			if ( $this->args['visibility'] ) {
				$html .= ' data-wpex-off-canvas-visibility="' . \esc_attr( $this->args['visibility'] ) . '"';
			}
			if ( $this->args['on_open_focus'] ) {
				$html .= ' data-wpex-off-canvas-open-focus="' . \esc_attr( $this->args['on_open_focus'] ) . '"';
			}
			if ( $this->args['backdrop'] ) {
				$html .= ' data-wpex-off-canvas-backdrop="true"';
				if ( $this->args['backdrop_blur'] ) {
					$html .= ' data-wpex-off-canvas-backdrop-blur="true"';
				}
			} else {
				$html .= ' data-wpex-off-canvas-backdrop="false"';
			}
			if ( $this->args['width'] ) {
				if ( \is_numeric( $this->args['width'] ) ) {
					$this->args['width'] = "{$this->args['width']}px";
				}
				$html .= ' style="--wpex-off-canvas-width:' . \esc_attr( $this->args['width'] ) . '"';
			}	
		$html .= '>';
			$inner_class = 'wpex-off-canvas__inner wpex-h-100 wpex-flex wpex-flex-col';
			if ( $this->args['contain'] ) {
				$inner_class .= ' container';
			}
			$html .= '<div class="' . \esc_attr( $inner_class ) . '">';
				$html .= $this->get_top_html();
				$html .= $this->get_mid_html();
				$html .= $this->get_footer_html();
			$html .= '</div>';
		$html .= '</div>';

		// Free up memory.
		$this->args = [];
		$this->content = null;
		$this->footer = null;

		return $html;
	}

	/**
	 * Returns the top html.
	 */
	protected function get_top_html(): string {
		if ( ! $this->args['close_button'] && ! $this->args['title'] && ! $this->args['logo'] ) {
			return '';
		}

		$class = [
			'wpex-off-canvas__header',
			'wpex-flex',
			'wpex-items-center',
			'wpex-gap-20',
		];

		if ( ! $this->args['contain'] && $padding_safe = \absint( $this->args['padding'] ) ) {
			$class[] = "wpex-px-{$padding_safe}";
		}

		$has_title = $this->args['title'] || $this->args['logo'];

		if ( $this->args['top_border'] && $has_title ) {
			$class[] = 'wpex-border-b wpex-border-solid wpex-border-surface-3';
		} else {
			
		}

		if ( $has_title ) {
			$class[] = 'wpex-py-20';
		} else {
			$class[] = 'wpex-pt-20 wpex-px-20';
		}

		$html = '<div class="' . \esc_attr( \implode( ' ', $class ) ) . '">';
			if ( $this->args['title'] || $this->args['logo'] ) {
				$html .= '<div class="wpex-off-canvas__header-left">';
					if ( $this->args['logo'] ) {
						$html .= '<div class="wpex-off-canvas__logo">' . \wp_get_attachment_image( $this->args['logo'], 'full', false, [
							'class' => 'wpex-align-bottom',
						] ) . '</div>';
					} else {
						$html .= '<div class="wpex-off-canvas__title wpex-heading wpex-leading-none wpex-text-xl">' . \esc_html( $this->args['title'] ) . '</div>';
					}
				$html .= '</div>';
			}
			if ( $this->args['close_button'] ) {
				$html .= $this->get_close_button();
			}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Returns the mid html.
	 */
	protected function get_mid_html(): string {
		$content_class = 'wpex-off-canvas__content wpex-hide-scrollbar wpex-w-100 wpex-max-w-100 wpex-mx-auto wpex-flex wpex-flex-col';
		if ( ! $this->args['contain'] && $padding_safe = \absint( $this->args['padding'] )) {
			$content_class .= " wpex-p-{$padding_safe}";
		}
		if ( $this->args['fixed_footer'] ) {
			$content_class .= ' wpex-flex-grow';
		}
		return '<div class="' . \esc_attr( $content_class ) . '">' . $this->content . '</div>';
	}

	/**
	 * Returns the footer html.
	 */
	protected function get_footer_html(): string {
		if ( ! $this->footer ) {
			return '';
		}

		$class = [ 'wpex-off-canvas__footer' ];

		if ( $this->args['fixed_footer'] ) {
			$class[] = 'wpex-mt-auto';
		}

		$padding_safe = \absint( $this->args['padding'] );
		
		if ( $padding_safe ) {
			if ( $this->args['contain'] ) {
				$class[] = "wpex-py-{$padding_safe}";
			} else {
				$class[] = "wpex-p-{$padding_safe}";
			}
		}

		if ( $this->args['bottom_border'] ) {
			$class[] = 'wpex-border-t';
			$class[] = 'wpex-border-solid';
			$class[] = 'wpex-border-surface-3';
		}

		if ( \is_numeric( $this->footer ) && $footer_post = \get_post( $this->footer ) ) {
			if ( 'publish' === \get_post_status( $footer_post ) ) {
				$template_type = \totaltheme_get_post_builder_type( $footer_post->ID );
				if ( 'elementor' === $template_type ) {
					$post_content = \wpex_get_elementor_content_for_display( $footer_post->ID );
				} else {
					$post_content = \totaltheme_render_content( $footer_post->post_content );
				}
				$this->footer = $post_content ?: null;
			}
		}

		return $this->footer ? '<div class="' . \esc_attr( \implode( ' ', $class ) ) . '">' . $this->footer . '</div>' : '';
	}

	/**
	 * Returns the close button.
	 */
	protected function get_close_button(): string {
		$icon_size = $this->args['close_button_icon_size'];
		$aria_label = $this->args['close_button_aria_label'] ?: \esc_attr( 'Close', 'Total' );
		return '<div class="wpex-off-canvas__header-right wpex-ml-auto"><button class="wpex-off-canvas__close wpex-unstyled-button wpex-flex wpex-text-2 wpex-hover-text-1" aria-label="' . \esc_attr( $aria_label ) . '">' . \totaltheme_get_icon( $this->args['close_button_icon'], 'wpex-flex', $icon_size ) . '</button></div>';
	}

}
