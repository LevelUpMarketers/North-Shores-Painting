<?php

namespace TotalThemeCore\Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Modern Menu widget.
 */
class Widget_Modern_Menu extends \TotalThemeCore\WidgetBuilder {

	/**
	 * Widget args.
	 */
	private $args;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->args = [
			'id_base' => 'wpex_modern_menu',
			'name'    => $this->branding() . \esc_html__( 'Sidebar Menu', 'total-theme-core' ),
			'options' => [
				'customize_selective_refresh' => true,
			],
			'fields'  => [
				[
					'id'    => 'title',
					'label' => \esc_html__( 'Title', 'total-theme-core' ),
					'type'  => 'text',
				],
				[
					'id'      => 'nav_menu',
					'label'   => \esc_html__( 'Select Menu', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => 'menus',
				],
				[
					'id'          => 'aria_label',
					'label'       => \esc_html__( 'Aria Label', 'total-theme-core' ),
					'description' => esc_html__( 'Label for screen readers.', 'total-theme-core' ),
					'type'        => 'text',
				],
				[
					'id'      => 'style',
					'label'   => \esc_html__( 'Style', 'total-theme-core' ),
					'type'    => 'select',
					'choices' => [
						'bordered' => \esc_html__( 'Default', 'total-theme-core' ),
						'clean' => \esc_html__( 'Clean', 'total-theme-core' ),
						'plain' => \esc_html__( 'Plain', 'total-theme-core' ),
					],
				],
				[
					'id'      => 'active_highlight',
					'default' => true,
					'label'   => \esc_html__( 'Highlight Active Page', 'total-theme-core' ),
					'type'    => 'checkbox',
				],
				[
					'id'      => 'show_arrows',
					'default' => true,
					'label'   => \esc_html__( 'Show Side Arrows', 'total-theme-core' ),
					'description' => \esc_html__( 'If "Hide Dropdowns" is enabled, arrows will only appear on items that have dropdowns, not on all menu items.', 'total-theme-core' ),
					'type'    => 'checkbox',
				],
				[
					'id'      => 'show_descriptions',
					'default' => false,
					'label'   => \esc_html__( 'Show Descriptions', 'total-theme-core' ),
					'type'    => 'checkbox',
				],
				[
					'id'          => 'hide_dropdowns',
					'default'     => false,
					'label'       => \esc_html__( 'Hide Dropdowns', 'total-theme-core' ),
					'description' => \esc_html__( 'Dropdowns will open when the parent menu item is clicked. This option works best with the "Clean" or "Plain" style.', 'total-theme-core' ),
					'type'        => 'checkbox',
				],
				[
					'id'          => 'expand_active_dropdowns',
					'default'     => true,
					'label'       => \esc_html__( 'Expand Active Dropdowns', 'total-theme-core' ),
					'description' => \esc_html__( 'Automatically expand dropdowns that include the active page.', 'total-theme-core' ),
					'type'        => 'checkbox',
				],
				[
					'id'          => 'el_class',
					'label'       => \esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => \esc_html__( 'Add extra classes to the widget container.', 'total-theme-core' ),
					'type'        => 'text',
				],
			],
		];

		$this->create_widget( $this->args );
	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		$instance = $this->parse_instance( $instance );

		if ( empty( $instance['nav_menu'] ) ) {
			return;
		}

		echo \wp_kses_post( $args['before_widget'] );

		$this->widget_title( $args, $instance );

		$style = $instance['style'] ?? 'bordered';

		if ( ! $style || ! in_array( $style, [ 'bordered', 'clean', 'plain' ], true ) ) {
			$style = 'bordered'; // this is required
		}

		$menu_class = "modern-menu-widget modern-menu-widget--{$style} wpex-m-0";

		if ( 'bordered' === $style ) {
			$menu_class .= ' wpex-border wpex-border-solid wpex-border-main wpex-rounded-sm wpex-last-border-none wpex-overflow-hidden';
		}

		if ( ! empty( $instance['el_class'] ) ) {
			$menu_class .= ' ' . \esc_attr( $instance['el_class'] );
		}

		$aria_label = ! empty( $instance['aria_label'] ) ? \sanitize_text_field( $instance['aria_label'] ) : '';

		\wp_nav_menu( [
			'menu_class'           => $menu_class,
			'menu'                 => $instance['nav_menu'],
			'walker'               => new Widget_Modern_Menu_Walker,
			'widget_instance'      => $instance,
			'fallback_cb'          => '',
			'container'            => $aria_label ? 'nav' : 'div',
			'container_aria_label' => $aria_label,
		] );

		echo \wp_kses_post( $args['after_widget'] );
	}

}

register_widget( 'TotalThemeCore\Widgets\Widget_Modern_Menu' );

/**
 * Custom Walker_Nav_Menu for the core menu widget.
 */
class Widget_Modern_Menu_Walker extends \Walker_Nav_Menu {

	/**
	 * The URL to the privacy policy page.
	 */
	protected $privacy_policy_url = '';

	/**
	 * Constructor.
	 */
	public function __construct( $privacy_policy_url = '' ) {
		$this->privacy_policy_url = get_privacy_policy_url();
	}

	/**
	 * Starts the list before the elements are added.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {// Define widget vars
		$widget_style = $args->widget_instance['style'] ?? 'bordered';

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );

		// Default class.
		$classes = array( 'sub-menu' );

		// WIDGET CLASSES START
		if ( 'bordered' === $widget_style ) {
			$classes[] = 'wpex-border-t';
			$classes[] = 'wpex-border-solid';
			$classes[] = 'wpex-border-main';
			$classes[] = 'wpex-last-border-none';
		} else {
			$classes[] = 'wpex-m-0';
		}
		// WIDGET CLASSES END

		/**
		 * Filters the CSS class(es) applied to a menu list element.
		 */
		$class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );

		$atts          = array();
		$atts['class'] = ! empty( $class_names ) ? $class_names : '';

		/**
		 * Filters the HTML attributes applied to a menu list element.
		 */
		$atts       = apply_filters( 'nav_menu_submenu_attributes', $atts, $args, $depth );
		$attributes = $this->build_atts( $atts );

		$output .= "{$n}{$indent}<ul{$attributes}>{$n}";
	}

	/**
	 * Ends the list of after the elements are added.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
		if ( 0 === $depth && isset( $args->widget_instance['hide_dropdowns'] ) && true === $args->widget_instance['hide_dropdowns'] ) {
			$output .= '</details>';
		}
	}

	/**
	 * Starts the element output.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		// Define widget vars
		$widget_style = $args->widget_instance['style'] ?? 'bordered';

		// Restores the more descriptive, specific name for use within this method.
		$menu_item = $data_object;

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes   = empty( $menu_item->classes ) ? array() : (array) $menu_item->classes;
		$classes[] = 'menu-item-' . $menu_item->ID;

		/**
		 * Filters the arguments for a single nav menu item.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $menu_item, $depth );

		// WIDGET LI CLASSES START
		if ( 'bordered' === $widget_style ) {
			$classes[] = 'wpex-border-b';
			$classes[] = 'wpex-border-solid';
			$classes[] = 'wpex-border-main';
		}
		// WIDGET LI CLASSES END

		/**
		 * Filters the CSS classes applied to a menu item's list item element.
		 */
		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $menu_item, $args, $depth ) );

		/**
		 * Filters the ID attribute applied to a menu item's list item element.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $menu_item->ID, $menu_item, $args, $depth );

		$li_atts          = array();
		$li_atts['id']    = ! empty( $id ) ? $id : '';
		$li_atts['class'] = ! empty( $class_names ) ? $class_names : '';

		/**
		 * Filters the HTML attributes applied to a menu's list item element.
		 */
		$li_atts       = apply_filters( 'nav_menu_item_attributes', $li_atts, $menu_item, $args, $depth );
		$li_attributes = $this->build_atts( $li_atts );

		$output .= $indent . '<li' . $li_attributes . '>';

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );

		// Save filtered value before filtering again.
		$the_title_filtered = $title;

		/**
		 * Filters a menu item's title.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $menu_item, $args, $depth );

		$atts           = array();
		$atts['target'] = ! empty( $menu_item->target ) ? $menu_item->target : '';
		$atts['rel']    = ! empty( $menu_item->xfn ) ? $menu_item->xfn : '';

		if ( ! empty( $menu_item->url ) ) {
			if ( $this->privacy_policy_url === $menu_item->url ) {
				$atts['rel'] = empty( $atts['rel'] ) ? 'privacy-policy' : $atts['rel'] . ' privacy-policy';
			}

			$atts['href'] = $menu_item->url;
		} else {
			$atts['href'] = '';
		}

		$atts['aria-current'] = $menu_item->current ? 'page' : '';

		// Add title attribute only if it does not match the link text (before or after filtering).
		if ( ! empty( $menu_item->attr_title )
			&& trim( strtolower( $menu_item->attr_title ) ) !== trim( strtolower( $menu_item->title ) )
			&& trim( strtolower( $menu_item->attr_title ) ) !== trim( strtolower( $the_title_filtered ) )
			&& trim( strtolower( $menu_item->attr_title ) ) !== trim( strtolower( $title ) )
		) {
			$atts['title'] = $menu_item->attr_title;
		} else {
			$atts['title'] = '';
		}

		// WIDGET MODIFY LINK ATTRIBUTES START
		$atts = $this->_modify_nav_menu_link_attributes( $atts, $menu_item, $args, $depth );
		// WIDGET MODIFY LINK ATTTRIBUTES END

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 */
		$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $menu_item, $args, $depth );
		$attributes = $this->build_atts( $atts );

		$item_output  = $args->before;

		// WIDGET DETAILS ELEMENT START

		// Check if details element should be added
		$hide_dropdowns = $args->widget_instance['hide_dropdowns'] ?? false;
		$has_details = ! empty( $this->has_children ) && $hide_dropdowns;

		if ( $has_details ) {
			$summary_class = 'wpex-list-none'; // removes arrow marker
			if ( isset( $atts['class'] ) ) {
				if ( is_array( $atts['class'] ) ) {
					$summary_class .= ' ' . implode( ' ', $atts['class'] );
				} elseif ( is_string( $atts['class'] ) ) {
					$summary_class .= ' ' . $atts['class'];
				}
			}
			if ( ! isset( $args->widget_instance['expand_active_dropdowns'] ) || true == $args->widget_instance['expand_active_dropdowns'] ) {
				$details_open = ( ! empty( $class_names ) && str_contains( $class_names, 'current-menu-ancestor' ) ) ? ' open' : ''; 
			} else {
				$details_open = '';
			}
			$item_output .= '<details class="wpex-m-0"' . $details_open . '><summary class="' . \esc_attr( $summary_class ) . '">';
		}
		// WIDGET DETAILS ELEMENT END

		if ( ! $has_details ) {
			$item_output .= '<a' . $attributes . '>';
		}

		// WIDGET EXTRA EL CONTENT START
		if ( ! empty( $menu_item->description )
			&& isset( $args->widget_instance['show_descriptions'] )
			&& true === $args->widget_instance['show_descriptions']
		) {
			$desc_class = 'modern-menu-widget__link-description wpex-block wpex-font-normal';
			if ( 'plain' === $widget_style || 'clean' === $widget_style ) {
				$desc_class .= ' wpex-text-2';
			}
			$title = '<span class="modern-menu-widget__link-text"><span class="modern-menu-widget__link-heading wpex-block wpex-bold">' . $title . '</span><span class="' . \esc_attr( $desc_class ) . '">' . \esc_html( $menu_item->description ) . '</span></span>';
		} else {
			$title = '<span class="modern-menu-widget__link-text">' . $title . '</span>';
		}

		// Insert side arrow
		$show_arrows = ! isset( $args->widget_instance['show_arrows'] ) || true === $args->widget_instance['show_arrows'];

		if ( $hide_dropdowns && ! $has_details ) {
			$show_arrows = false;
		}

		if ( $show_arrows && \function_exists( '\totaltheme_get_icon' ) ) {

			if ( $has_details ) {
				$link_arrow_icon = ( 'bordered' === $widget_style ) ? 'material-arrow-forward-ios' : 'bootstrap-chevron-right';
			} else {
				$link_arrow_icon = ( 'bordered' === $widget_style ) ? 'material-arrow-back-ios' : 'bootstrap-chevron-left';
			}

			$link_arrow = (string) \apply_filters( 'wpex_modern_menu_widget_link_icon', $link_arrow_icon );

			if ( $link_arrow ) {
				$icon_class = 'modern-menu-widget__link-icon';
				$icon_position = 'before_title';

				if ( ! empty( $args->widget_instance['arrow_position'] ) ) {
					$icon_position = $args->widget_instance['arrow_position'];
				} else {
					if ( $has_details
						|| 'right-sidebar' !== $this->get_page_layout()
						|| ( is_rtl() && 'right-sidebar' === $this->get_page_layout() )
					) {
						$icon_position = 'after_title';
					}
				}

				if ( 'after_title' === $icon_position ) {
					if ( 'plain' !== $widget_style ) {
						$icon_class .= ' wpex-ml-auto';
					}
					if ( 'bordered' !== $widget_style ) {
						$icon_class .= ' -wpex-mr-5'; // move icon a bit over since it has some inner white space in the svg
					}
					if ( ! $has_details && ! is_rtl() ) {
						$icon_class .= ' wpex-rotate-180';
					}
					$title = $title . \totaltheme_get_icon( $link_arrow, $icon_class );
				} else {
					$title = \totaltheme_get_icon( $link_arrow, $icon_class ) . $title;
				}

			}
		}
		// WIDGET EXTRA EL CONTENT END

		$item_output .= $args->link_before . $title . $args->link_after;

		if ( $has_details ) {
			$item_output .= '</summary>';
		} else {
			$item_output .= '</a>';
		}

		$item_output .= $args->after;

		/**
		 * Filters a menu item's starting output.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		if ( 0 === $depth
			&& ! empty( $this->has_children )
			&& isset( $args->widget_instance['hide_dropdowns'] )
			&& true === $args->widget_instance['hide_dropdowns']
		) {
			$output .= '</details>';
		}
		$output .= '</li>';
	}

	/**
	 * Returns down arrow.
	 */
	private function _get_down_arrow( $args = [] ) {
		// @todo
	}

	/**
	 * Modify the menu link attributes.
	 */
	public function _modify_nav_menu_link_attributes( $atts, $menu_item, $args, $depth ) {
		$style = $args->widget_instance['style'] ?? 'bordered';

		$class = [
			( 'plain' === $style ) ? 'wpex-inline-flex' : 'wpex-flex',
			'wpex-gap-10',
			'wpex-items-center',
			'wpex-relative',
			'wpex-no-underline',
		];

		if ( 'bordered' === $style ) {
			$class[] = 'wpex-text-3';
			$class[] = 'wpex-transition-colors';
		} elseif ( 'clean' === $style ) {
			$class[] = 'wpex-text-current';
			$class[] = 'wpex-py-10';
			if ( 0 === $depth ) {
				$class[] = 'wpex-px-20';
			} else {
				if ( 1 === $depth ) {
					$class[] = 'wpex-pl-40 wpex-pr-20';
				} elseif ( 2 === $depth ) {
					$class[] = 'wpex-pl-50 wpex-pr-20';
				} else {
					$class[] = 'wpex-pl-60 wpex-pr-20';
				}
			}
		} elseif( 'plain' === $style ) {
			$class[] = 'wpex-text-current';
			$class[] = 'wpex-py-5';
			if ( 1 === $depth ) {
				$class[] = 'wpex-ml-15';
			} elseif ( 2 === $depth ) {
				$class[] = 'wpex-ml-30';
			} elseif ( $depth > 0 ) {
				$class[] = 'wpex-ml-60';
			}
		}

		if ( isset( $menu_item->current )
			&& (bool) $menu_item->current
			&& isset( $args->widget_instance['active_highlight'] )
			&& true === $args->widget_instance['active_highlight']
		) {
			if ( 'bordered' === $style ) {
				$class[] = 'wpex-bg-accent';
				$class[] = 'wpex-on-accent';
			//	$clas[]  = '-wpex-mx-1'; // removed because it causes the item to shift and it's not really needed.
			} elseif ( 'clean' === $style || 'plain' === $style ) {
				if ( empty( $this->has_children ) ) {
					$class[] = 'wpex-text-accent';
					$class[] = 'wpex-bold';
					$class[] = 'wpex-pointer-events-none';
				} else {
					// make sure it has default hovers
					$class[] = 'wpex-hover-surface-2';
				}
			}
		} else {
			if ( 'bordered' === $style || 'clean' === $style ) {
				$class[] = 'wpex-hover-surface-2';
			}
			if ( 'bordered' === $style ) {
				$class[] = 'wpex-hover-text-3';
			}
		}

		$class_string = implode( ' ', $class );

		if ( isset( $atts['class'] ) && is_scalar( $atts['class'] ) ) {
			$atts['class'] .= " {$class_string}";
		} else {
			$atts['class'] = $class_string;
		}

		return $atts;
	}

	/**
	 * Helper function returns page layout.
	 */
	private function get_page_layout() {
		return \function_exists( 'wpex_content_area_layout' ) ? \wpex_content_area_layout() : 'right-sidebar';
	}

}