<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

class Term_Cards extends \Wpex_Term_Cards_Shortcode {

	/**
	 * Output.
	 */
	protected $output = '';

	/**
	 * Unique element classname.
	 */
	protected $unique_classname = '';

	/**
	 * Associative array of shortcode attributes.
	 */
	protected $atts = [];

	/**
	 * Constructor.
	 */
	public function __construct( $atts = [] ) {
		if ( \vcex_maybe_display_shortcode( self::TAG, $atts )
			&& \function_exists( 'vcex_build_wp_query' )
			&& \function_exists( 'wpex_get_card' )
		) {
			$this->render_cards( $atts );
		}
	}

	/**
	 * Render the cards.
	 */
	protected function render_cards( $atts ) {
		// Parse shortcode atts (need to do this because this element has it's own output() method).
		$this->atts = \vcex_shortcode_atts( self::TAG, $atts, \get_parent_class( $this ) );

		// Core vars
		$inner_output       = '';
		$running_count      = 0;
		$display_type       = $this->get_display_type();
		$grid_style         = $this->get_grid_style();
		$grid_columns       = $this->get_grid_columns();
		$grid_gap_class     = $this->get_grid_gap_class();
		$grid_is_responsive = \vcex_validate_att_boolean( 'grid_columns_responsive', $this->atts, true );

		// We can remove $atts from memory now
		unset( $atts );

		// Query posts
		$terms = vcex_get_terms( $this->atts, 'wpex_term_cards' );

		// Bail completely
		if ( ! $terms || ! is_array( $terms ) ) {
			$this->output = $this->no_terms_found_message();
			return;
		}

		// Define card args
		$card_args = $this->get_card_args();

		// Output inline CSS.
		$this->inline_style();

		/*-------------------------------------*/
		/* [ Inner Output Starts Here ]
		/*-------------------------------------*/
		$inner_output .= $this->get_heading();

		$inner_class = [
			'wpex-term-cards-inner',
		];

		$inner_output .= '<div class="' . \esc_attr( \implode( ' ', $inner_class ) ) . '">';

		/*-------------------------------------*/
		/* [ Entries start here ]
		/*-------------------------------------*/

		// Define items wrap class.
		$items_wrap_class = [
			'wpex-term-cards-loop',
		];

		// Define item tags.
		$items_wrap_tag = 'div';
		$card_tag = 'div';

		switch ( $display_type ) :
			case 'carousel':

				\vcex_enqueue_carousel_scripts();

				// All carousels need a unique classname.
				if ( empty( $this->unique_classname ) ) {
					$this->unique_classname = \vcex_element_unique_classname();
				}

				// Get carousel settings.
				$carousel_settings = \vcex_get_carousel_settings( $this->atts, self::TAG, false );
				$carousel_css = \vcex_get_carousel_inline_css( $this->unique_classname . ' .wpex-posts-card-carousel', $carousel_settings );

				$items_data['data-wpex-carousel'] = \vcex_carousel_settings_to_json( $carousel_settings );
				$items_wrap_class[] = 'wpex-posts-card-carousel';
				$items_wrap_class[] = 'wpex-carousel';

				if ( ! empty( $this->atts['carousel_bleed'] ) && \in_array( $this->atts['carousel_bleed'], [ 'end', 'start-end' ], true ) ) {
					$items_wrap_class[] = "wpex-carousel--bleed-{$this->atts['carousel_bleed']}";
				}

				if ( isset( $this->atts['items'] ) && 1 === (int) $this->atts['items'] ) {
					$items_wrap_class[] = 'wpex-carousel--single';
				}

				if ( \totalthemecore_call_static( 'Vcex\Carousel\Core', 'use_owl_classnames' ) ) {
					$items_wrap_class[] = 'owl-carousel';
				}

				if ( $carousel_css ) {
					$items_wrap_class[] = 'wpex-carousel--render-onload';
				}

				// Flex carousel.
				if ( empty( $this->atts['auto_height'] ) || 'false' === $this->atts['auto_height'] ) {
					$items_wrap_class[] = 'wpex-carousel--flex';
				}

				// No margins.
				if ( isset( $this->atts['items_margin'] )
					&& '' !== $this->atts['items_margin']
					&& 0 === \absint( $this->atts['items_margin'] )
				) {
					$items_wrap_class[] = 'wpex-carousel--no-margins';
				} elseif ( ! vcex_validate_att_boolean( 'center', $this->atts, false )
					&& ( empty( $this->atts['out_animation'] ) || ( 'fadeOut' !== $this->atts['out_animation'] ) )
				) {
					$items_wrap_class[] = 'wpex-carousel--offset-fix';
				}

				// Arrow style.
				$arrows_style = ! empty( $this->atts['arrows_style'] ) ? \sanitize_text_field( $this->atts['arrows_style'] ) : 'default';
				$items_wrap_class[] = "arrwstyle-{$arrows_style}";

				// Arrow position.
				$arrow_position = ! empty( $this->atts['arrows_position'] ) ? \sanitize_text_field( $this->atts['arrows_position'] ) : 'default';
				$items_wrap_class[] = "arrwpos-{$arrow_position}";
				break;
			case 'list':
				$items_wrap_class[] = 'wpex-term-cards-list';
				$items_wrap_class[] = 'wpex-grid wpex-grid-cols-1'; // @note needs the grid class to prevent blowout.
				if ( $grid_gap_class ) {
					$items_wrap_class[] = $grid_gap_class;
				}
				if ( \vcex_validate_att_boolean( 'alternate_flex_direction', $this->atts ) ) {
					$items_wrap_class[] = 'wpex-term-cards-list--alternate-flex-direction';
				}
				if ( \vcex_validate_att_boolean( 'list_divider_remove_last', $this->atts ) ) {
					$items_wrap_class[] = 'wpex-last-divider-none';
				}
				break;
			case 'ol_list':
				$items_wrap_class[] = 'wpex-term-cards-ol_list wpex-m-0 wpex-p-0 wpex-list-inside';
				$items_wrap_tag = 'ol';
				$card_tag = 'li';
				break;
			case 'ul_list':
				$items_wrap_tag = 'ul';
				$card_tag = 'li';
				$items_wrap_class[] = 'wpex-term-cards-ul_list wpex-m-0 wpex-p-0 wpex-list-inside';
				break;
			case 'flex_wrap':
				$items_wrap_class[] = 'wpex-term-cards-flex_wrap wpex-flex wpex-flex-wrap';
				if ( ! empty( $this->atts['flex_justify'] ) ) {
					$items_wrap_class[] = \vcex_parse_justify_content_class( $this->atts['flex_justify'] );
				}
				if ( $grid_gap_class ) {
					$items_wrap_class[] = $grid_gap_class;
				}
				break;
			case 'flex':
				$items_wrap_class[] = 'wpex-term-cards-flex wpex-flex';
				$flex_bk = ! empty( $this->atts['flex_breakpoint'] ) ? \sanitize_html_class( $this->atts['flex_breakpoint'] ) : '';
				if ( $flex_bk && 'false' !== $flex_bk ) {
					$items_wrap_class[] = 'wpex-flex-col';
					$items_wrap_class[] = "wpex-{$flex_bk}-flex-row";
				}
				if ( ! empty( $this->atts['flex_justify'] ) ) {
					$items_wrap_class[] = \vcex_parse_justify_content_class( $this->atts['flex_justify'], $flex_bk );
					if ( $flex_bk && 'false' !== $flex_bk ) {
						$items_wrap_class[] = \vcex_parse_align_items_class( $this->atts['flex_justify'] );
						$items_wrap_class[] = \vcex_parse_align_items_class( 'stretch', $flex_bk );
					}
				}
				$items_wrap_class[] = 'wpex-overflow-x-auto';
				if ( $grid_gap_class ) {
					$items_wrap_class[] = $grid_gap_class;
				}
				if ( \vcex_validate_att_boolean( 'hide_scrollbar', $this->atts ) ) {
					$items_wrap_class[] = 'wpex-hide-scrollbar';
				}
				$snap_type = ! empty( $this->atts['flex_scroll_snap_type'] ) ? \sanitize_text_field( $this->atts['flex_scroll_snap_type'] ) : 'proximity';
				if ( 'proximity' === $snap_type || 'mandatory' === $snap_type ) {
					$has_scroll_snap = true;
					$items_wrap_class[] = 'wpex-snap-x';
					$items_wrap_class[] = "wpex-snap-{$snap_type}";
				}
				break;
			case 'grid':
			default:
				if ( 'css_grid' === $grid_style ) {
					$items_wrap_class[] = 'wpex-term-cards-grid wpex-grid';
					if ( $grid_is_responsive && ! empty( $this->atts['grid_columns_responsive_settings'] ) ) {
						$r_grid_columns = \vcex_parse_multi_attribute( $this->atts['grid_columns_responsive_settings'] );
						if ( $r_grid_columns && is_array( $r_grid_columns ) ) {
							$r_grid_columns['d'] = $grid_columns;
							$grid_columns = $r_grid_columns;
						}
					}
					if ( $grid_is_responsive && \function_exists( 'wpex_grid_columns_class' ) ) {
						$items_wrap_class[] = \wpex_grid_columns_class( $grid_columns );
					} else {
						$items_wrap_class[] = 'wpex-grid-cols-' . \sanitize_html_class( $grid_columns );
					}
				} else {
					$items_wrap_class[] = 'wpex-term-cards-grid';
					$items_wrap_class[] = 'wpex-row';
					$items_wrap_class[] = 'wpex-clr';
				}

				if ( 'masonry' === $grid_style ) {
					$items_wrap_class[] = 'wpex-masonry-grid';
					if ( \function_exists( 'wpex_enqueue_masonry_scripts' ) ) {
						\wpex_enqueue_masonry_scripts(); // uses theme masonry scripts.
					}
				}

				if ( $grid_gap_class ) {
					$items_wrap_class[] = $grid_gap_class;
				}
				break;
		endswitch; // end display_type switch

		// Opens items wrap (wpex-term-cards-loop)
		if ( isset( $carousel_css ) ) {
			$inner_output .= $carousel_css; // add here so it can be removed by the JS
		}
		$items_wrap_tag_safe = tag_escape( $items_wrap_tag );
		$inner_output .= '<' . $items_wrap_tag_safe . ' class="' . \esc_attr( \implode( ' ', $items_wrap_class ) ) . '"';

			// Add grid data attributes.
			if ( ! empty( $items_data ) ) {
				foreach ( $items_data as $key => $value ) {
					$inner_output .= ' ' . $key ."='" . \esc_attr( $value ) . "'";
				}
			}

			// Inner Items CSS
			$grid_css_args = [];
			if ( ! $grid_gap_class ) {
				switch ( $display_type ) {
					case 'grid':
					case 'flex':
					case 'flex_wrap':
						if ( ! empty( $this->atts['grid_spacing'] ) ) {
							$grid_spacing = \sanitize_text_field( $this->atts['grid_spacing'] );
							if ( $grid_spacing ) {
								if ( \is_numeric( $grid_spacing ) ) {
									$grid_spacing = "{$grid_spacing}px";
								}
								if ( 'css_grid' === $grid_style
									|| 'flex' === $display_type
									|| 'flex_wrap' === $display_type
								) {
									$grid_css_args['gap'] = $grid_spacing;
								} else {
									$grid_css_args['--wpex-row-gap'] = $grid_spacing;
								}
							}
						}
						break;
				}
			}
			if ( $grid_css_args ) {
				$inner_output .= \vcex_inline_style( $grid_css_args );
			}

			$inner_output .= '>';

			// Add first divider if enabled
			if ( 'list' === $display_type && ! \vcex_validate_att_boolean( 'list_divider_remove_first', $this->atts, true ) ) {
				$inner_output .= $this->list_divider( $this->atts );
			}

			// The Loop
			foreach ( $terms as $term ) :
				$card_args['term'] = $term;

				$running_count++;
				\set_query_var( 'wpex_loop_running_count', \absint( $running_count ) );

				$item_class = [
					'wpex-term-cards-entry',
				];

				switch ( $display_type ) :
					case 'ol_list':
					case 'ul_list':
						if ( isset( $card_args['style' ] )
							&& in_array( $card_args['style'], [ 'title_1', 'link' ] )
						) {
							$item_class[] = 'wpex-card-title';
						}
						break;
					case 'carousel':
						$item_class[] = 'wpex-carousel-slide';
						break;
					case 'list':
						if ( \vcex_validate_att_boolean( 'alternate_flex_direction', $this->atts ) ) {
							$even_odd = ( 0 === $running_count % 2 ) ? 'even' : 'odd';
							$item_class[] = 'wpex-term-cards-entry--' . sanitize_html_class( $even_odd );
						}
						break;
					case 'grid':
					case 'flex':
					case 'flex_wrap':
					default:

						// Horizontal scroll.
						if ( 'flex' === $display_type ) {
							$item_class[] = 'wpex-flex';
							$item_class[] = 'wpex-flex-col';
							$item_class[] = 'wpex-max-w-100';
							if ( ! empty( $this->atts['flex_basis'] )
								|| ! vcex_validate_att_boolean( 'flex_shrink', $this->atts, true )
							) {
								$item_class[] = 'wpex-flex-shrink-0';
							} else {
								$item_class[] = 'wpex-flex-grow';
							}
							if ( isset( $has_scroll_snap ) && true === $has_scroll_snap ) {
								$item_class[] = 'wpex-snap-start';
							}
						}

						// Flex Container.
						elseif ( 'flex_wrap' === $display_type ) {
							$item_class[] = 'wpex-flex';
							$item_class[] = 'wpex-flex-col';
						}

						// Grids
						else {
							if ( 'masonry' === $grid_style ) {
								$item_class[] = 'wpex-masonry-col';

								if ( $grid_is_responsive ) {
									$item_class[] = 'col';
								} else {
									$item_class[] = 'nr-col';
								}

								if ( $grid_columns ) {
									$item_class[] = 'span_1_of_' . \sanitize_html_class( $grid_columns );
								}

								if ( $grid_is_responsive ) {
									$rs = \vcex_parse_multi_attribute( $this->atts['grid_columns_responsive_settings'] );
									foreach ( $rs as $key => $val ) {
										if ( $val ) {
											$item_class[] = 'span_1_of_' . \sanitize_html_class( $val ) . '_' . \sanitize_html_class( $key );
										}
									}
								}
							} else {
								$item_class[] = 'wpex-flex';
								$item_class[] = 'wpex-flex-col';
								$item_class[] = 'wpex-flex-grow';
							}
						}
						break;
				endswitch;

				// Add standard wp classes.
				$item_class[] = 'term-' . \sanitize_html_class( $term->term_id );
				$item_class = (array) \apply_filters( 'wpex_term_cards_entry_class', $item_class, $this->atts );
				$item_class_string = \implode( ' ', \array_unique( \array_filter( $item_class ) ) );

				// Begin entry output.
				$card_html = \wpex_get_card( $card_args );

				if ( $card_html ) {
					$card_tag_safe = \tag_escape( $card_tag );
					$inner_output .= '<' . $card_tag_safe .' class="' . esc_attr( $item_class_string ) . '">' . $card_html . '</' . $card_tag_safe . '>';
				}

				// List Divider.
				if ( 'list' === $display_type && ! empty( $this->atts['list_divider'] ) ) {
					$inner_output .= $this->list_divider( $this->atts );
				}

			endforeach;

		// Remove running count.
		\set_query_var( 'wpex_loop_running_count', null );

		// Close wpex-term-cards-loop element
		$inner_output .= '</' . $items_wrap_tag_safe . '>';

		// Close post cards inner
		$inner_output .= '</div>';

		/*-------------------------------------*/
		/* [ Put inner_output inside wrap. ]
		/*-------------------------------------*/
		$this->output .= '<div';

		if ( ! empty( $this->atts['unique_id'] ) ) {
			$this->output .= ' id="' . \esc_attr( $this->atts['unique_id'] ) . '"';
		}

		$this->output .= ' class="' . \esc_attr( \implode( ' ', $this->get_wrap_classes() ) ) . '">' . $inner_output . '</div>';
	}

	/**
	 * Adds inline style to output.
	 */
	protected function inline_style() {
		$css = '';

		if ( \class_exists( '\TotalThemeCore\Vcex\Shortcode_CSS' ) ) {
			$shortcode_css = new Shortcode_CSS( \get_parent_class( $this ), $this->atts );

			// Carousel animation speed.
			if ( 'carousel' === $this->get_display_type()
				&& ! empty( $this->atts['animation_speed'] )
				&& ! empty( $this->atts['out_animation'] )
				&& isset( $this->atts['items'] )
				&& 1 === (int) $this->atts['items']
			) {
				$shortcode_css->add_extra_css( [
					'selector' => '{{WRAPPER}}',
					'property' => '--wpex-carousel-animation-duration',
					'val'      => absint( $this->atts['animation_speed'] ) . 'ms',
				] );
			}


			$shortcode_style = $shortcode_css->render_style( false );
			if ( $shortcode_style && ! empty( $shortcode_css->unique_classname ) ) {
				$css .= $shortcode_style;
				$unique_classname = $shortcode_css->unique_classname;
			}
		}

		// Flex basis needs to be calculated differently.
		$css_xtra = '';
		if ( in_array( $this->get_display_type(), [ 'flex', 'flex_wrap' ], true ) && ! empty( $this->atts['flex_basis'] ) ) {
			$flex_bk = $this->get_breakpoint_px( $this->atts['flex_breakpoint'] ?? null );
			$flex_basis = '{{class}} .wpex-term-cards-entry{flex-basis:' . $this->parse_flex_basis( $this->atts['flex_basis'] ) . '}';
			if ( $flex_bk ) {
				$css_xtra .= "@media only screen and (min-width: {$flex_bk}) { {$flex_basis} }";
			} else {
				$css_xtra .= $flex_basis;
			}
		}

		if ( $css_xtra ) {
			$unique_classname = $unique_classname ?? \vcex_element_unique_classname();
			$css .= \str_replace( '{{class}}', '.' . $unique_classname, $css_xtra );
		}

		if ( $css ) {
			$this->unique_classname = $unique_classname;
			$this->output .= "<style>{$css}</style>";
		}
	}

	/**
	 * Parses the flex basis and returns correct value.
	 */
	protected function parse_flex_basis( $basis = '' ) {
		if ( ! empty( $this->atts['grid_spacing'] ) ) {
			$gap = \sanitize_text_field( $this->atts['grid_spacing'] );
		} else {
			$gap = $this->get_default_grid_gap();
		}
		return vcex_get_flex_basis( $basis, $gap );
	}

	/**
	 * Returns a breakpoint in pixels based on selected option.
	 */
	protected function get_breakpoint_px( $breakpoint = '' ) {
		if ( $breakpoint ) {
			$breakpoints = [
				'xl' => '1280px',
				'lg' => '1024px',
				'md' => '768px',
				'sm' => '640px',
			];
			return $breakpoints[ $breakpoint ] ?? null;
		}
	}

	/**
	 * Return array of wrap classes.
	 */
	protected function get_wrap_classes() {
		$classes = [
			'wpex-term-cards',
			'wpex-term-cards-' . \sanitize_html_class( $this->get_card_style() ),
		];

		if ( ! empty( $this->atts['bottom_margin'] ) ) {
			$classes[] = \vcex_parse_margin_class( $this->atts['bottom_margin'], 'bottom' );
		}

		if ( ! empty( $this->atts['el_class'] ) ) {
			$classes[] = \vcex_get_extra_class( $this->atts['el_class'] );
		}

		if ( ! empty( $this->atts['css_animation'] ) && \vcex_validate_att_boolean( 'css_animation_sequential', $this->atts ) ) {
			$classes[] = 'wpb-animate-in-sequence';
		}

		if ( ! empty( $this->unique_classname ) ) {
			$classes[] = $this->unique_classname;
		}

		$classes[] = 'wpex-relative';

		return $classes;
	}

	/**
	 * Return card args based on shortcode atts.
	 */
	protected function get_card_args() {
		$args = [
			'style' => $this->get_card_style(),
		];

		$params = [
			'template_id',
			'display_type',
			'link_type',
			'modal_title',
			'modal_template',
			'link_target',
			'link_rel',
			'title_tag',
			'css_animation',
			'media_width',
			'media_breakpoint',
			'thumbnail_overlay_style',
			'thumbnail_overlay_button_text',
			'thumbnail_hover',
			'thumbnail_filter',
			'alternate_flex_direction',
		];

		foreach ( $params as $param ) {
			if ( ! empty( $this->atts[ $param ] ) ) {
				$args[ $param ] = $this->atts[ $param ];
			}
		}

		if ( empty( $this->atts['thumbnail_size'] ) || 'wpex_custom' === $this->atts['thumbnail_size'] ) {
			$args['thumbnail_size'] = [
				$this->atts['thumbnail_width'],
				$this->atts['thumbnail_height'],
				$this->atts['thumbnail_crop'],
			];
		} else {
			$args['thumbnail_size'] = $this->atts['thumbnail_size'];
		}

		if ( ! empty( $this->atts['media_breakpoint'] ) ) {
			$args['breakpoint'] = $this->atts['media_breakpoint'];
		}

		if ( ! empty( $this->atts['media_el_class'] ) ) {
			$args['media_el_class'] = \vcex_get_extra_class( $this->atts['media_el_class'] );
		}

		if ( ! empty( $this->atts['card_el_class'] ) ) {
			$args['el_class'] = \vcex_get_extra_class( $this->atts['card_el_class'] );
		}

		if ( 'carousel' === $this->get_display_type() ) {
			$args['thumbnail_lazy'] = false;
		}

		return $args;
	}

	/**
	 * Get supported media.
	 */
	protected function get_allowed_media() {
		if ( ! $this->is_elementor_widget() && \array_key_exists( 'allowed_media', $this->atts ) ) {
			if ( $this->atts['allowed_media'] ) {
				if ( \is_string( $this->atts['allowed_media'] ) ) {
					$this->atts['allowed_media'] = \wp_parse_list( $this->atts['allowed_media'] );
				}
				foreach ( $this->atts['allowed_media'] as $k => $v ) {
					if ( ! \in_array( $v, [ 'thumbnail', 'video' ] ) ) {
						unset( $this->atts['allowed_media'][ $k ] );
					}
				}
			}
			return $this->atts['allowed_media'];
		}
	}

	/**
	 * Returns the card style.
	 */
	protected function get_card_style(): string {
		return (string) $this->atts['card_style'];
	}

	/**
	 * List Divider.
	 */
	protected function list_divider() {
		$divider_class = [
			'wpex-card-list-divider',
			'wpex-divider',
			'wpex-divider-' . \sanitize_html_class( $this->atts['list_divider'] ),
		];

		$divider_class[] = 'wpex-my-0'; // remove default margin since we want to use gaps.

		if ( ! empty( $this->atts['list_divider_size'] ) ) {
			$divider_size = \absint( $this->atts['list_divider_size'] );
			if ( 1 === $divider_size ) {
				$divider_class[] = 'wpex-border-b';
			} else {
				$divider_class[] = "wpex-border-b-{$divider_size}";
			}
		}

		return '<div class="' . \esc_attr( \implode( ' ', $divider_class ) ) . '"></div>';
	}

	/**
	 * Get display type.
	 */
	protected function get_display_type() {
		return ! empty( $this->atts['display_type'] ) ? \sanitize_text_field( $this->atts['display_type'] ) : 'grid';
	}

	/**
	 * Get grid style.
	 */
	protected function get_grid_style() {
		$allowed = [
			'css_grid',
			'masonry',
			'fit_rows',
		];
		if ( ! empty( $this->atts['grid_style'] ) && \in_array( $this->atts['grid_style'], $allowed ) ) {
			return $this->atts['grid_style'];
		}
		return 'fit_rows';
	}

	/**
	 * Get grid style.
	 */
	protected function get_grid_columns() {
		return ! empty( $this->atts['grid_columns'] ) ? \absint( $this->atts['grid_columns'] ) : 3;
	}

	/**
	 * Get grid gap class.
	 */
	protected function get_grid_gap_class() {
		$display_type = $this->get_display_type();
		if ( 'carousel' === $display_type ) {
			return;
		}
		$gap        = '';
		$grid_style = $this->get_grid_style();
		switch ( $display_type ) {
			case 'list':
				$gap = ! empty( $this->atts['list_spacing'] ) ? \sanitize_text_field( $this->atts['list_spacing'] ) : $this->get_default_list_gap();
				break;
			case 'grid':
			case 'flex':
			case 'flex_wrap':
				$default = '';
				// css_grid needs a default gap.
				if ( 'css_grid' === $grid_style || 'flex' === $display_type || 'flex_wrap' === $display_type ) {
					$default = $this->get_default_grid_gap();
				}
				$gap = ! empty( $this->atts['grid_spacing'] ) ? \sanitize_text_field( $this->atts['grid_spacing'] ) : $default;
				break;
		}
		if ( $gap ) {
			if ( 'list' === $display_type
				|| 'flex' === $display_type
				|| 'flex_wrap' === $display_type
				|| ( 'grid' === $display_type && 'css_grid' === $grid_style )
			) {
				$use_utl_class = true;
			} else {
				$use_utl_class = false;
			}
			if ( 'none' === $gap ) {
				if ( $use_utl_class ) {
					return 'wpex-gap-0';
				} else {
					return 'gap-none';
				}
			}
			if ( \function_exists( 'wpex_column_gaps' ) ) {
				$gap_parsed = \str_replace( 'px', '', $gap );
				if ( \array_key_exists( $gap_parsed, (array) \wpex_column_gaps() ) ) {
					if ( $use_utl_class ) {
						return "wpex-gap-{$gap_parsed}";
					} else {
						return "gap-{$gap_parsed}";
					}
				}
			}
		}
	}

	/**
	 * Returns the query type.
	 */
	protected function get_query_type() {
		return $this->atts['query_type'] ?? '';
	}

	/**
	 * Check if displaying within an elementor widget.
	 */
	protected function is_elementor_widget() {
		return \vcex_validate_att_boolean( 'is_elementor_widget', $this->atts );
	}

	/**
	 * Returns default list gap.
	 */
	protected function get_default_list_gap(): string {
		return '15px';
	}

	/**
	 * Returns theme default gap.
	 */
	protected function get_default_grid_gap(): string {
		return vcex_has_classic_styles() ? '20px' : '25px';
	}

	/**
	 * Returns the no terms found mesage.
	 */
	protected function no_terms_found_message() {
		$message = '';
		$check   = false;

		if ( ! empty( $this->atts['no_terms_found_message'] ) ) {
			$check = true;
			$message = $this->atts['no_terms_found_message'];
		} elseif ( \vcex_vc_is_inline() ) {
			$check = true;
			$message = \esc_html__( 'Nothing found.', 'total-theme-core' );
		}

		$check = (bool) \apply_filters( 'vcex_has_no_terms_found_message', $check, $this->atts );

		if ( ! $check ) {
			return;
		}

		$message = (string) \apply_filters( 'vcex_no_terms_found_message', $message, $this->atts );

		if ( $message ) {
			return '<div class="vcex-no-posts-found">' . vcex_parse_text_safe( $message ) . '</div>';
		}
	}

	/**
	 * Get heading.
	 */
	private function get_heading(): string {
		$heading = ! empty( $this->atts['heading'] ) ? \vcex_parse_text_safe( $this->atts['heading'] ) : '';

		if ( ! $heading || ! function_exists( 'wpex_get_heading' ) ) {
			return '';
		}

		if ( ! empty( $this->atts['heading_el_class'] ) ) {
			$class[] = \vcex_get_extra_class( $this->atts['heading_el_class'] );
		}

		$html = \wpex_get_heading( [
			'tag'     => ! empty( $this->atts['heading_tag'] ) ? $this->atts['heading_tag'] : 'h2',
			'style'   => ! empty( $this->atts['heading_style'] ) ? $this->atts['heading_style'] : '',
			'align'   => ! empty( $this->atts['heading_align'] ) ? $this->atts['heading_align'] : '',
			'classes' => [ 'wpex-term-cards-heading' ],
			'content' => $heading,
		] );

		return $html;
	}

	/**
	 * Outputs the html.
	 */
	public function render() {
		echo $this->output; // @codingStandardsIgnoreLine
	}

	/**
	 * Returns the html.
	 */
	public function get_output() {
		return $this->output;
	}

	/**
	 * Returns the query.
	 */
	public function get_query() {
		return $terms;
	}

	/**
	 * Returns the parsed atts.
	 */
	public function get_atts() {
		return $this->atts;
	}

}
