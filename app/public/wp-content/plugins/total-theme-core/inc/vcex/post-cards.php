<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

class Post_Cards extends \Wpex_Post_Cards_Shortcode {

	/**
	 * Output.
	 */
	protected $output = '';

	/**
	 * Current query.
	 */
	public $query = null;

	/**
	 * Unique element classname.
	 */
	protected $unique_classname = '';

	/**
	 * Associative array of shortcode attributes.
	 */
	protected $atts = [];

	/**
	 * Associative array of attributes used for ajax queries.
	 */
	protected $ajax_atts = [];

	/**
	 * Pagination type.
	 */
	protected $pagination_type = null;

	/**
	 * Checks if ajax is being used.
	 */
	protected $has_ajax = false;

	/**
	 * Stores ajax action to prevent extra checks.
	 */
	protected $ajax_action = null;

	/**
	 * Class instance.
	 */
	public static $instance = null;

	/**
	 * Stores all instances.
	 */
	private static $instances = [];

	/**
	 * Returns the current class object.
	 */
	public static function instance() {
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct( $atts = [] ) {
		if ( \vcex_maybe_display_shortcode( self::TAG, $atts )
			&& \function_exists( 'vcex_build_wp_query' )
			&& \function_exists( 'wpex_get_card' )
		) {
			// Create new instance
			self::$instance = $this;
			self::$instances[] = $this;

			// Display cards
			$this->render_cards( $atts );

			// Reset instance
			\array_pop( self::$instances ); // remove last instance (aka this instance)
			self::$instance = ! empty( self::$instances ) ? end( self::$instances ) : null;
		}
	}

	/**
	 * Render the cards.
	 */
	protected function render_cards( $atts ) {

		// Define shortcode output.
		$inner_output = '';

		// Store ajax atts before parsing.
		if ( ! empty( $atts ) ) {
			$this->ajax_atts = $atts;
		}

		// Check if currently loading items via load more.
		$is_doing_loadmore = $this->is_doing_ajax( 'load_more' );

		// Store some original atts before parsing.
		$og_entry_count = ( $is_doing_loadmore && ! empty( $atts['entry_count'] ) ) ? absint( $atts['entry_count'] ) : null;
		$running_count  = ( $is_doing_loadmore && ! empty( $atts['running_count'] ) ) ? absint( $atts['running_count'] ) : 0;

		// Parse shortcode atts (need to do this because this element has it's own output() method).
		$this->atts = \vcex_shortcode_atts( self::TAG, $atts, \get_parent_class( $this ) );

		// Parse card style.
		$this->parse_card_style();

		// Modify atts for custom queries.
		$this->maybe_modify_atts();

		// Core vars.
		$display_type       = $this->get_display_type();
		$grid_style         = $this->get_grid_style();
		$grid_columns       = $this->get_grid_columns();
		$grid_gap_class     = $this->get_grid_gap_class();
		$pagination_type    = $this->get_pagination_type();
		$grid_is_responsive = \vcex_validate_att_boolean( 'grid_columns_responsive', $this->atts, true );

		// Featured card vars.
		$has_featured_card      = $this->has_featured_card();
		$featured_card_location = $this->get_featured_card_location();
		$is_featured_card_top   = $this->is_featured_card_top();
		$fc_bk                  = ! empty( $this->atts['featured_breakpoint'] ) ? $this->atts['featured_breakpoint'] : 'sm';

		// Get paged value from ajax atts.
		$this->atts['paged'] = ! empty( $this->ajax_atts['paged'] ) ? $this->ajax_atts['paged'] : null;

		// We can remove $atts from memory now.
		unset( $atts );

		// Parse featured card ID.
		if ( \vcex_validate_att_boolean( 'featured_card', $this->atts ) ) {
			$this->atts['featured_post_id'] = $this->get_featured_post_id();
			$this->ajax_atts['featured_post_id'] = $this->atts['featured_post_id'];
		}

		// Set current entry count.
		$entry_count = $og_entry_count ?? 0;

		// Define card args.
		$card_args = $this->get_card_args();

		// Query posts.
		$this->query = \vcex_build_wp_query( $this->atts, self::TAG );

		// Bail completely!
		if ( ! $this->query->have_posts() && empty( $this->atts['featured_post_id'] ) && ! $this->is_doing_ajax( 'filter' ) ) {
			$this->output = $this->no_posts_found_message();
			return;
		}

		// Output inline CSS.
		$this->inline_style();

		/*-------------------------------------*/
		/* [ Inner Output Starts Here ]
		/*-------------------------------------*/
		$inner_output .= $this->get_heading();

		$inner_class = [
			'wpex-post-cards-inner',
		];

		// Add flex styles to post cards inner.
		if ( $has_featured_card ) {
			if ( ! $is_featured_card_top ) {
				$inner_class[] = "wpex-{$fc_bk}-flex";
			}
			if ( 'right' === $featured_card_location ) {
				$inner_class[] = "wpex-{$fc_bk}-flex-row-reverse";
			}
		}

		$inner_output .= '<div class="' . \esc_attr( \implode( ' ', $inner_class ) ) . '">';

		/*-------------------------------------*/
		/* [ Featured Card ]
		/*-------------------------------------*/
		if ( $has_featured_card ) {
			$aside_flex = ( \vcex_validate_att_boolean( 'aside_flex', $this->atts ) && 'masonry' !== $grid_style );
			$fc_width   = \apply_filters( 'wpex_post_cards_featured_width', 50 );
			$fc_width   = ! empty( $this->atts['featured_width'] ) ? $this->atts['featured_width'] : $fc_width;

			$featured_card_classes = [
				'wpex-post-cards-featured',
			];

			// Featured card flex classes.
			if ( ! $is_featured_card_top ) {
				if ( $aside_flex ) {
					$featured_card_classes[] = 'wpex-flex'; // make the featured card expand.
				}
				$fc_width_safe = \trim( \absint( $fc_width ) );
				$featured_card_classes[] = "wpex-{$fc_bk}-w-{$fc_width_safe}";
				$featured_card_classes[] = "wpex-{$fc_bk}-flex-shrink-0";
			}

			// Featured card bottom margin.
			if ( empty( $this->atts['featured_divider'] ) || ! $is_featured_card_top ) {
				$fc_margin = $this->atts['featured_margin'] ? \absint( $this->atts['featured_margin'] ) : 30;
				$featured_card_classes[] = "wpex-mb-{$fc_margin}";
			}

			// Featured card side margin.
			switch ( $featured_card_location ) {
				case 'left':
					$featured_card_classes[] = "wpex-{$fc_bk}-mb-0";
					$featured_card_classes[] = "wpex-{$fc_bk}-mr-{$fc_margin}";
					break;
				case 'right':
					$featured_card_classes[] = "wpex-{$fc_bk}-mb-0";
					$featured_card_classes[] = "wpex-{$fc_bk}-ml-{$fc_margin}";
					break;
			}

			if ( 'woocommerce' === $this->get_featured_card_style() ) {
				$featured_card_classes[] = 'products';
			}

			// Display featured card.
			$inner_output .= '<div class="' . \esc_attr( \implode( ' ', $featured_card_classes ) ) . '">';

				if ( ! empty( $this->atts['featured_post_id'] ) ) {
					$featured_post_id = $this->atts['featured_post_id'];
					global $post;
					$post = \get_post( $this->atts['featured_post_id'] );
					$inner_output .= \wpex_get_card( $this->get_featured_card_args( $featured_post_id ) );
					$this->query->reset_postdata();
				} else {
					$count=0;
					while ( $this->query->have_posts() ) :
						$count++;
						if ( 2 === $count ) {
							break;
						}
						$this->query->the_post();
						$featured_post_id = \get_the_ID();
						$inner_output .= \wpex_get_card( $this->get_featured_card_args( $featured_post_id ) );
					endwhile;
					$this->query->reset_postdata();
					$this->query->rewind_posts();
				}

			$inner_output .= '</div>';

			if ( ! empty( $this->atts['featured_divider'] ) && $is_featured_card_top ) {
				$inner_output .= $this->featured_divider();
			}

		}

		/*-------------------------------------*/
		/* [ Entries start here ]
		/*-------------------------------------*/
		if ( $this->query->have_posts() && $this->get_card_style()  ) {

			if ( $has_featured_card && ! $is_featured_card_top ) {
				$aside_class = "wpex-post-cards-aside wpex-min-w-0 wpex-{$fc_bk }-flex-grow";
				if ( isset( $aside_flex ) && true === $aside_flex ) {
					$aside_class .= ' wpex-flex';
				}
				$inner_output .= '<div class="' . $aside_class . '">';
			}

			// Before loop hook.
			if ( \has_action( 'wpex_hook_post_cards_loop_before' ) ) {
				\ob_start();
					\do_action( 'wpex_hook_post_cards_loop_before', $this->atts, $this->query );
				$inner_output .= \ob_get_clean();
			}

			// Define items wrap class.
			$items_wrap_class = [
				'wpex-post-cards-loop',
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
					$items_wrap_class[] = 'wpex-post-cards-list';
					$items_wrap_class[] = 'wpex-grid wpex-grid-cols-1'; // @note needs the grid class to prevent blowout.
					if ( $grid_gap_class ) {
						$items_wrap_class[] = $grid_gap_class;
					}
					if ( \vcex_validate_att_boolean( 'alternate_flex_direction', $this->atts ) ) {
						$items_wrap_class[] = 'wpex-post-cards-list--alternate-flex-direction';
					}
					if ( \vcex_validate_att_boolean( 'list_divider_remove_last', $this->atts ) ) {
						$items_wrap_class[] = 'wpex-last-divider-none';
					}
					break;
				case 'ol_list':
					$items_wrap_class[] = 'wpex-post-cards-ol_list wpex-m-0 wpex-p-0 wpex-list-inside';
					$items_wrap_tag = 'ol';
					$card_tag = 'li';
					break;
				case 'ul_list':
					$items_wrap_tag = 'ul';
					$card_tag = 'li';
					$items_wrap_class[] = 'wpex-post-cards-ul_list wpex-m-0 wpex-p-0 wpex-list-inside';
					break;
				case 'flex_wrap':
					$items_wrap_class[] = 'wpex-post-cards-flex_wrap wpex-flex wpex-flex-wrap';
					if ( ! empty( $this->atts['flex_justify'] ) ) {
						$items_wrap_class[] = \vcex_parse_justify_content_class( $this->atts['flex_justify'] );
					}
					if ( $grid_gap_class ) {
						$items_wrap_class[] = $grid_gap_class;
					}
					break;
				case 'flex':
					$items_wrap_class[] = 'wpex-post-cards-flex wpex-flex';
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
						$items_wrap_class[] = 'wpex-post-cards-grid wpex-grid';
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
						$items_wrap_class[] = 'wpex-post-cards-grid';
						$items_wrap_class[] = 'wpex-row';
						$items_wrap_class[] = 'wpex-clr';
					}

					if ( 'masonry' === $grid_style ) {
						$items_wrap_class[] = 'wpex-masonry-grid';
						if ( \function_exists( 'wpex_enqueue_masonry_scripts' ) ) {
							\wpex_enqueue_masonry_scripts(); // uses theme masonry scripts.
						}
						if ( 'masonry' === $grid_style && ! vcex_validate_att_boolean( 'masonry_horizontal_order', $this->atts, true ) ) {
							$items_data['data-horizontal-order'] = 'false';
						}
					}

					if ( $grid_gap_class ) {
						$items_wrap_class[] = $grid_gap_class;
					}

					break;
			endswitch; // end display_type switch

			if ( 'woocommerce' === $this->get_card_style() ) {
				$items_wrap_class[] = 'products';
			}

			// Opens items wrap (wpex-post-cards-loop)
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

				// Loop top hook.
				if ( \has_action( 'wpex_hook_post_cards_loop_top' ) ) {
					\ob_start();
						\do_action( 'wpex_hook_post_cards_loop_top', $this->atts, $this->query );
					$inner_output .= \ob_get_clean();
				}

				// Add first divider if enabled.
				if ( 'list' === $display_type
					&& ! \vcex_validate_att_boolean( 'list_divider_remove_first', $this->atts, true )
					&& ! $is_doing_loadmore
				) {
					$inner_output .= $this->list_divider( $this->atts );
				}

				// The Loop
				while ( $this->query->have_posts() ) :

					// Setup global post data.
					$this->query->the_post();

					// Set main entry vars.
					$post_id = \get_the_ID();
					$post_type = \get_post_type( $post_id );
					$card_args['post_id'] = $post_id;

					if ( ! empty( $featured_post_id )
						&& empty( $this->atts['featured_post_id'] )
						&& $post_id === $featured_post_id
					) {
						continue;
					}

					$entry_count++;

					$running_count++;
					\set_query_var( 'wpex_loop_running_count', \absint( $running_count ) );

					$item_class = [
						'wpex-post-cards-entry',
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
								$item_class[] = 'wpex-post-cards-entry--' . sanitize_html_class( $even_odd );
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

							// Modern CSS grids.
							elseif ( 'css_grid' === $grid_style ) {
								$item_class[] = 'wpex-flex';
								$item_class[] = 'wpex-flex-col';
								$item_class[] = 'wpex-flex-grow';
							}

							// Old school grids.
							else {
								if ( $grid_is_responsive ) {
									$item_class[] = 'col';
								} else {
									$item_class[] = 'nr-col';
								}

								$item_class[] = 'col-' . \sanitize_html_class( $entry_count );

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
							}

							if ( 'masonry' === $grid_style ) {
								$item_class[] = 'wpex-masonry-col';
							}

							break;

					endswitch;

					// Add standard wp classes.
					$item_class[] = 'post-' . \sanitize_html_class( $post_id );
					$item_class[] = 'type-' . \sanitize_html_class( $post_type );

					// Add term classes.
					if ( \function_exists( 'vcex_get_post_term_classes' ) ) {
						$terms = \vcex_get_post_term_classes();
						if ( $terms && \is_array( $terms ) ) {
							foreach ( $terms as $term_name ) {
								$item_class[] = $term_name;
							}
						}
					}

					$item_class = (array) \apply_filters( 'wpex_post_cards_entry_class', $item_class, $this->atts );
					$item_class_string = \implode( ' ', \array_unique( $item_class ) );

					// Before entry hook.
					if ( \has_action( 'wpex_hook_post_cards_entry_before' ) ) {
						\ob_start();
							\do_action( 'wpex_hook_post_cards_entry_before', $running_count, $item_class_string, $this->atts, $this->query );
						$inner_output .= \ob_get_clean();
					}

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

					// After entry hook.
					if ( \has_action( 'wpex_hook_post_cards_entry_after' ) ) {
						\ob_start();
							\do_action( 'wpex_hook_post_cards_entry_after', $running_count, $item_class_string, $this->atts, $this->query );
						$entry_after = \ob_get_clean();
						if ( $entry_after ) {
							$inner_output .= $entry_after;
							if ( 'list' === $display_type && ! empty( $this->atts['list_divider'] ) ) {
								$inner_output .= $this->list_divider( $this->atts );
							}
						}
					}

					// Reset entry count.
					if ( 'grid' === $display_type
						&& 'fit_rows' === $grid_style
						&& $entry_count === $grid_columns
					) {
						$entry_count = 0;
					}

				endwhile;

			// Update ajax vars after loop (needs to run always for load more to work, not just on loadmore)
			$this->ajax_atts['entry_count'] = $entry_count;
			$this->ajax_atts['running_count'] = $running_count;

			// Reset post data.
			$this->reset_postdata();

			// Remove running count.
			\set_query_var( 'wpex_loop_running_count', null );

			// Loop bottom hook.
			if ( \has_action( 'wpex_hook_post_cards_loop_bottom' ) ) {
				\ob_start();
				\do_action( 'wpex_hook_post_cards_loop_bottom', $this->atts, $this->query );
				$inner_output .= \ob_get_clean();
			}

			// Close element that holds main (not featured) posts - wpex-post-cards-loop
			$inner_output .= '</' . $items_wrap_tag_safe . '>';

			// After loop hook.
			if ( \has_action( 'wpex_hook_post_cards_loop_after' ) ) {
				\ob_start();
					\do_action( 'wpex_hook_post_cards_loop_after', $this->atts, $this->query );
				$inner_output .= \ob_get_clean();
			}

			// Pagination.
			if ( 'numbered' !== $pagination_type && 'numbered_ajax' !== $pagination_type ) {
				$pagination_added = true;
				$inner_output .= $this->get_pagination();
			}

			// Close featured aside wrap.
			if ( $has_featured_card && ! $is_featured_card_top ) {
				$inner_output .= '</div>';
			}

		} else {

			if ( $this->is_doing_ajax( 'filter' ) ) {
				$inner_output .= $this->no_posts_found_message();
			}

		} // end has posts check

		// Close post cards inner.
		$inner_output .= '</div>';

		// Outer pagination.
		if ( ! isset( $pagination_added ) ) {
			$inner_output .= $this->get_pagination();
		}

		// Ajax Loader (must run after get_pagination)
		if ( ! empty( $this->atts['unique_id'] ) || true === $this->has_ajax ) {
			$inner_output .= totalthemecore_call_non_static( 'Vcex\Ajax', 'get_ajax_loader' );
		}

		/*-------------------------------------*/
		/* [ Put inner_output inside wrap. ]
		/*-------------------------------------*/
		$this->output .= '<div';

			// Wrap id attribute.
			if ( ! empty( $this->atts['unique_id'] ) ) {
				$this->output .= ' id="' . \esc_attr( $this->atts['unique_id'] ) . '"';
			}

			// Wrap class attribute.
			$this->output .= ' class="' . \esc_attr( \implode( ' ', $this->get_wrap_classes( $has_featured_card ) ) ) . '"';

			// Wrap data attributes.
			if ( ! empty( $this->atts['unique_id'] ) || true === $this->has_ajax ) {
				$this->output .= ' data-vcex-class="' . \get_parent_class( $this ) . '"';
				$this->output .= ' data-vcex-atts="' . $this->get_json_data() . '"';
				$this->output .= ' data-vcex-max-pages="' . \esc_attr( $this->query->max_num_pages ?? 0 )  .'"';
				$this->output .= ' data-vcex-current-page="' . \esc_attr( \get_query_var( 'paged' ) ?: 1 ) . '"';
			}

			if ( 'numbered_ajax' === $pagination_type ) {
				$this->output .= ' data-vcex-pagination="numbered_ajax"';
			}

			$this->output .= '>';

			$this->output .= $inner_output;

		$this->output .= '</div>'; // close wrap
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
			$flex_basis = '{{class}} .wpex-post-cards-entry{flex-basis:' . $this->parse_flex_basis( $this->atts['flex_basis'] ) . '}';
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
	 * Modifies atts.
	 */
	protected function maybe_modify_atts() {
		$query_type = $this->get_query_type();
		if ( \in_array( $query_type, [ 'post_gallery', 'attachments' ] ) ) {
			if ( ! $this->is_doing_ajax() ) {
				switch ( $query_type ) {
					case 'post_gallery':
						if ( \function_exists( 'wpex_get_gallery_ids' ) ) {
							$gallery_ids = \wpex_get_gallery_ids( get_the_ID() );
						}
						break;
					case 'attachments':
						if ( ! empty( $this->atts['attachments'] ) ) {
							if ( \is_string( $this->atts['attachments'] ) ) {
								$gallery_ids = \explode( ',', $this->atts['attachments'] );
							}
							if ( \is_array( $this->atts['attachments'] ) ) {
								if ( $this->is_elementor_widget() ) {
									$gallery_ids = \array_column( $this->atts['attachments'], 'id' );
								}
							}
						}
						break;
				}
				$this->atts['query_type'] = 'custom';
				$this->atts['custom_query_args'] = [
					'post_type'      => 'attachment',
					'post_status'    => 'any',
					'post__in'       => $gallery_ids ?? [ 0 ],
					'orderby'        => 'post__in',
					'posts_per_page' => ! empty( $this->atts['posts_per_page'] ) ? \sanitize_text_field( $this->atts['posts_per_page'] ) : '12',
				];
				// Important we need to reset ajax atts.
				$this->ajax_atts['query_type'] = $this->atts['query_type'];
				$this->ajax_atts['custom_query_args'] = $this->atts['custom_query_args'];
			}
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
	protected function get_wrap_classes( $has_featured_card = false ) {
		$classes = [
			'wpex-post-cards',
			'wpex-post-cards-' . \sanitize_html_class( $this->get_card_style() ),
		];

		if ( $has_featured_card ) {
			$classes[] = 'wpex-post-cards-has-featured'; // @todo rename to use BEM
		}

		if ( 'woocommerce' === $this->get_card_style() ) {
			$classes[] = 'woocommerce';
		}

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
	 * Return json data used for ajax functions.
	 */
	protected function get_json_data() {
		if ( $this->is_auto_query() ) {
			if ( empty( $this->ajax_atts['query_vars'] ) ) {
				$this->ajax_atts['query_vars'] = \wp_json_encode( $this->query->query_vars );
			}
			$this->ajax_atts['query_vars'] = $this->ajax_atts['query_vars'];
		}
		if ( $this->is_threaded() ) {
			$this->ajax_atts['post_id'] = get_the_ID(); // used for inner dynamic cards
		}
		unset( $this->ajax_atts['ajax_action'] ); // not needed anymore
		unset( $this->ajax_atts['ajax_filter'] ); // not needed anymore
		return \esc_attr( \wp_json_encode( $this->ajax_atts, false ) );
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
			'date_format',
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

		if ( isset( $this->atts['more_link_text'] ) && '' !== $this->atts['more_link_text'] ) {
			$args['more_link_text'] = $this->atts['more_link_text']; // allows "0" for disabling.
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

		if ( isset( $this->atts['excerpt_length'] ) && '' !== $this->atts['excerpt_length'] ) {
			$args['excerpt_length'] = $this->atts['excerpt_length'];
		}

		if ( $allowed_media = $this->get_allowed_media() ) {
			$args['allowed_media'] = $allowed_media;
		}

		if ( 'carousel' === $this->get_display_type() ) {
			$args['thumbnail_lazy'] = false;
		}

		return $args;
	}

	/**
	 * Check if loadmore is enabled.
	 */
	protected function has_loadmore(): bool {
		return ( ( ! empty( $this->atts['pagination'] ) && \in_array( $this->atts['pagination'], [ 'loadmore', 'infinite_scroll' ], true ) ) || \vcex_validate_att_boolean( 'pagination_loadmore', $this->atts ) );
	}

	/**
	 * Check if featured card is enabled.
	 */
	protected function has_featured_card(): bool {
		if ( $this->is_doing_ajax( 'load_more' ) ) {
			return false;
		}

		// Check if the featured card is enabled.
		$check = \vcex_validate_att_boolean( 'featured_card', $this->atts );

		// Do not show featured card on paginated pages.
		if ( ( ! \vcex_validate_att_boolean( 'featured_show_on_paged', $this->atts ) || $this->has_loadmore() ) && \is_paged() ) {
			$check = false;
		}

		return (bool) \apply_filters( 'wpex_post_cards_has_featured_card', $check, $this->atts );
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
	 * Check if the featured card should use a custom query.
	 */
	protected function has_featured_card_custom_query(): bool {
		return ( $this->has_loadmore() || ! \vcex_validate_att_boolean( 'featured_show_on_paged', $this->atts ) );
	}

	/**
	 * Get featured card ID.
	 */
	protected function get_featured_post_id() {
		$featured_post_id = 0;
		if ( ! empty( $this->atts['featured_post_id'] )
			&& ! $this->is_doing_ajax( [ 'pagination', 'filter' ] )
		) {
			$post_id = $this->atts['featured_post_id'];
			$post = \get_post( $post_id );
			if ( $post && 'publish' === $post->post_status ) {
				$featured_post_id = \wpex_parse_obj_id( $post_id, \get_post_type( $post ) );
			}
		} elseif ( $this->has_featured_card_custom_query() ) {
			$query_args = $this->atts;
			$query_args['posts_per_page'] = 1;
			$query_posts = \vcex_build_wp_query( $query_args );
			if ( $query_posts->have_posts() && ! empty( $query_posts->posts[0] ) ) {
				$featured_post_id = $query_posts->posts[0]->ID ?? 0;
			}
		}

		return (int) \apply_filters( 'wpex_post_cards_featured_post_id', $featured_post_id, $this->atts );
	}

	/**
	 * Parses the card style att value.
	 */
	protected function parse_card_style(): void {
		if ( ! $this->is_doing_ajax()
			&& $this->is_auto_query()
			&& function_exists( 'totaltheme_get_term_card_style' )
			&& ( is_tax() || is_category() || is_tag() )
			&& $term_card_style = totaltheme_get_term_card_style( get_queried_object() )
		) {
			$this->atts['card_style'] = $term_card_style;
			$this->ajax_atts['card_style'] = $term_card_style;
		}
	}

	/**
	 * Returns the card style.
	 */
	protected function get_card_style(): string {
		return (string) $this->atts['card_style'];
	}

	/**
	 * Returns the featured card style.
	 */
	protected function get_featured_card_style(): string {
		return ! empty( $this->atts['featured_style'] ) ? sanitize_text_field( (string) $this->atts['featured_style'] ) : $this->get_card_style();
	}

	/**
	 * Featured card args.
	 *
	 * @todo can we optimize this to use a loop instead?
	 */
	protected function get_featured_card_args( $post_id ): array {
		$args = [
			'post_id'  => $post_id,
			'style'    => $this->get_featured_card_style(),
			'featured' => true,
		];

		$params = [
			'date_format',
			'thumbnail_overlay_style',
			'thumbnail_overlay_button_text',
			'thumbnail_hover',
			'thumbnail_filter',
			'media_el_class',
			'modal_title',
			'modal_template',
			'link_type',
			'link_target',
			'link_rel',
		];

		foreach ( $params as $param ) {
			if ( ! empty( $this->atts[ $param ] ) ) {
				$args[ $param ] = $this->atts[ $param ];
			}
		}

		if ( ! empty( $this->atts['featured_template_id'] ) ) {
			$args['template_id'] = $this->atts['featured_template_id'];
		}

		if ( ! empty( $this->atts['featured_title_tag'] ) ) {
			$args['title_tag'] = $this->atts['featured_title_tag'];
		}

		if ( empty( $this->atts['featured_thumbnail_size'] ) || 'wpex_custom' === $this->atts['featured_thumbnail_size'] ) {
			$args['thumbnail_size'] = [
				$this->atts['featured_thumbnail_width'],
				$this->atts['featured_thumbnail_height'],
				$this->atts['featured_thumbnail_crop'],
			];
		} else {
			$args['thumbnail_size'] = $this->atts['featured_thumbnail_size'];
		}

		if ( isset( $this->atts['featured_more_link_text'] ) && '' !== $this->atts['featured_more_link_text'] ) {
			$args['more_link_text'] = $this->atts['featured_more_link_text']; // allows "0" for disabling.
		}

		if ( isset( $this->atts['featured_excerpt_length'] ) && '' !== $this->atts['featured_excerpt_length'] ) {
			$args['excerpt_length'] = $this->atts['featured_excerpt_length'];
		}

		if ( ! empty( $this->atts['featured_el_class'] ) ) {
			$args['el_class'] = $this->atts['featured_el_class'];
		}

		if ( ! empty( $this->atts['featured_media_width'] ) ) {
			$args['media_width'] = $this->atts['featured_media_width'];
		}

		if ( ! empty( $this->atts['featured_media_breakpoint'] ) ) {
			$args['breakpoint'] = $this->atts['featured_media_breakpoint'];
		} elseif ( ! empty( $this->atts['media_breakpoint'] ) ) {
			$args['breakpoint'] = $this->atts['media_breakpoint'];
		}

		if ( $allowed_media = $this->get_allowed_media() ) {
			$args['allowed_media'] = $allowed_media;
		}

		return (array) \apply_filters( 'wpex_post_cards_featured_card_args', $args, $this->atts );
	}

	/**
	 * Get featured card location.
	 */
	protected function get_featured_card_location() {
		return $this->atts['featured_location'] ?? 'top';
	}

	/**
	 * Check if featured card is enabled.
	 */
	protected function is_featured_card_top() {
		return ! in_array( $this->get_featured_card_location(), [ 'left', 'right' ], true );
	}

	/**
	 * Featured Card Divider
	 */
	protected function featured_divider() {
		$divider_class = [
			'wpex-post-cards-featured-card-divider',
			'wpex-divider',
			'wpex-divider-' . \sanitize_html_class( $this->atts['featured_divider'] ),
		];

		if ( ! empty( $this->atts['featured_divider_size'] ) ) {
			$divider_size = \absint( $this->atts['featured_divider_size'] );
			if ( 1 === $divider_size ) {
				$divider_class[] = 'wpex-border-b';
			} else {
				$divider_class[] = "wpex-border-b-{$divider_size}";
			}
		}

		$spacing = ! empty( $this->atts['featured_divider_margin'] ) ? $this->atts['featured_divider_margin'] : 15;

		if ( ! empty( $this->atts['featured_margin'] ) ) {
			$divider_class[] = \vcex_parse_margin_class( $spacing, 'top' );
			$divider_class[] = \vcex_parse_margin_class( $this->atts['featured_margin'], 'bottom' );
		} else {
			$divider_class[] = \vcex_parse_margin_class( $spacing, 'block' );
		}

		return '<div class="' . \esc_attr( \implode( ' ', $divider_class ) ) . '"></div>';
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
		return ! empty( $this->atts['display_type'] ) ? $this->atts['display_type'] : 'grid';
	}

	/**
	 * Get pagination type.
	 */
	protected function get_pagination_type() {
		if ( ! is_null( $this->pagination_type ) ) {
			return $this->pagination_type;
		}

		if ( in_array( $this->get_query_type(), [ 'term_card', 'custom_field' ], true ) ) {
			return ''; // pagination not allowed for these query types
		}

		$pagination_type = $this->atts['pagination'] ?? '';
		$is_auto_query   = $this->is_auto_query();

		$allowed_choices = [
			'loadmore',
			'numbered',
			'numbered_ajax',
			'infinite_scroll',
		];

		// Allowed pagination styles
		if ( ! in_array( $pagination_type, $allowed_choices, true ) ) {
			$pagination_type = '';
		}

		// Enable pagination for auto and custom queries
		if ( ! $pagination_type && ( $is_auto_query || ( \vcex_validate_att_boolean( 'custom_query', $this->atts ) && ! empty( $this->query->query['pagination'] ) ) ) ) {
			$pagination_type = 'numbered';
		}

		// relevanssi fix
		if ( $pagination_type && $is_auto_query && \function_exists( 'relevanssi_do_query' ) && \is_search() ) {
			$pagination_type = 'numbered';
		}

		$this->pagination_type = $pagination_type;

		return $this->pagination_type;
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
		return ! empty( $this->atts['grid_columns'] ) ? \absint( $this->atts['grid_columns'] ) : 1;
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
	 * Returns pagination output.
	 */
	protected function get_pagination() {
		$max_pages = absint( $this->query->max_num_pages ?? 1 );
		if ( ! $max_pages || 1 === $max_pages ) {
			return;
		}
		$html = '';
		$display_type = $this->get_display_type();
		if ( 'grid' === $display_type
			|| 'list' === $display_type
			|| 'flex' === $display_type
			|| 'flex_wrap' === $display_type
			|| 'ul_list' === $display_type
			|| 'ol_list' === $display_type
		) {
			$pagination_type = $this->get_pagination_type();
			switch ( $pagination_type ) {
				case 'loadmore';
				case 'infinite_scroll';
					$this->has_ajax = true;
					totalthemecore_call_non_static( 'Vcex\Ajax', 'enqueue_scripts', \get_parent_class( $this ), $this->atts );
					$infinite_scroll = ( 'infinite_scroll' === $pagination_type ) ? true : false;
					$html .= \totalthemecore_call_non_static( 'Vcex\Ajax', 'get_loadmore_button', [
						'shortcode_tag'   => self::TAG,
						'shortcode_atts'  => $this->ajax_atts,
						'query'           => $this->query,
						'infinite_scroll' => $infinite_scroll,
						'loadmore_text'   => ! empty( $this->atts['loadmore_text'] ) ? $this->atts['loadmore_text'] : '',
					] );
					break;
				case 'numbered_ajax':
					$this->has_ajax = true;
					\totalthemecore_call_non_static( 'Vcex\Ajax', 'enqueue_scripts', \get_parent_class( $this ), $this->atts );
					$html .= \vcex_pagination( $this->query, false );
					break;
				case 'numbered';
					$html .= \vcex_pagination( $this->query, false );
					break;
			}
		}
		if ( $html ) {
			$html = '<div class="wpex-post-cards-pagination wpex-mt-30 wpex-first-mt-0">' . $html .'</div>';
		}
		return $html;
	}

	/**
	 * Returns the query type.
	 */
	protected function get_query_type() {
		return $this->atts['query_type'] ?? '';
	}

	/**
	 * Check if we are showing an auto query.
	 */
	protected function is_auto_query() {
		if ( 'auto' === $this->get_query_type() ) {
			return true;
		} else {
			return \vcex_validate_att_boolean( 'auto_query', $this->atts );
		}
	}

	/**
	 * Check if displaying within an elementor widget.
	 */
	protected function is_elementor_widget() {
		return \vcex_validate_att_boolean( 'is_elementor_widget', $this->atts );
	}

	/**
	 * Get current AJAX action.
	 */
	protected function get_ajax_action() {
		if ( null === $this->ajax_action ) {
			$this->ajax_action = false;
			$action = $this->ajax_atts['ajax_action'] ?? null;
			if ( $action && ! empty( $_REQUEST['action'] ) && Ajax::ACTION === $_REQUEST['action'] ) {
				$this->ajax_action = $action;
			}
		}
		return $this->ajax_action;
	}

	/**
	 * Check if we are currently doing a specific ajax action.
	 */
	protected function is_doing_ajax( $action = 'any' ) {
		if ( 'any' === $action ) {
			return (bool) $this->get_ajax_action();
		}
		if ( ! \is_array( $action ) ) {
			$action = [ $action ];
		}
		if ( \in_array( $this->get_ajax_action(), $action ) ) {
			return true;
		}
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
	 * Check if we are currently doing a specific ajax action.
	 */
	protected function no_posts_found_message() {
		if ( $this->is_doing_ajax( 'load_more' ) ) {
			return;
		}

		$message = '';
		$check   = false;

		if ( ! empty( $this->atts['no_posts_found_message'] ) ) {
			$check = true;
			$message = $this->atts['no_posts_found_message'];
		} elseif ( \vcex_vc_is_inline() || $this->is_auto_query() || $this->is_doing_ajax( 'filter' ) ) {
			$check = true;
			$message = \esc_html__( 'Nothing found.', 'total-theme-core' );
		}

		$check = (bool) \apply_filters( 'vcex_has_no_posts_found_message', $check, $this->atts );

		if ( ! $check ) {
			return;
		}

		$message = (string) \apply_filters( 'vcex_no_posts_found_message', $message, $this->atts );

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
			'classes' => [ 'wpex-post-cards-heading' ],
			'content' => $heading,
		] );

		return $html;
	}

	/**
	 * Restore the original query.
	 */
	public function reset_postdata() {
		if ( $parent_instance = $this->get_parent_instance() ) {
			$parent_instance->query->reset_postdata();
		} else {
			\wp_reset_postdata();
		}
	}

	/**
	 * Get the parent instance.
	 */
	private function get_parent_instance() {
		$instances_count = \count( self::$instances );
		if ( $instances_count > 1 ) {
			return self::$instances[ $instances_count - 2 ];
		}
	}

	/**
	 * Check if we are dealing with a threaded card.
	 */
	public static function is_threaded(): bool {
		return \count( self::$instances ) > 1;
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
		return $this->query;
	}

	/**
	 * Returns the parsed atts.
	 */
	public function get_atts() {
		return $this->atts;
	}

}
