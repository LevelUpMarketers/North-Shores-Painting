<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Returns array of choices for various element setting types.
 */
class Setting_Choices {

	/**
	 * Array of choices.
	 */
	public $choices = [];

	/**
	 * Array of arguments.
	 */
	protected $args = [];

	/**
	 * Builder we are grabbing settings for.
	 */
	protected $builder = '';

	/**
	 * Class Constructor.
	 */
	public function __construct( $choices, $args = [], $builder = '' ) {
		$this->args = $args;
		$this->builder = $builder;

		if ( \is_array( $choices ) ) {
			$this->choices = $choices;
		} elseif ( \method_exists( $this, "choices_{$choices}" ) ) {
			$method = "choices_{$choices}";
			$this->$method();
		}

		// Remove excluded choices from the array.
		if ( isset( $this->args['exclude_choices'] ) && \is_array( $this->args['exclude_choices'] ) ) {
			foreach ( $this->args['exclude_choices'] as $excluded ) {
				unset( $this->choices[ $excluded ] );
			}
		}
	}

	/**
	 * HTML tag choices.
	 */
	protected function choices_html_tag(): void {
		$this->choices = [
			''     => \esc_html__( 'Default', 'total-theme-core' ),
			'h1'   => 'h1',
			'h2'   => 'h2',
			'h3'   => 'h3',
			'h4'   => 'h4',
			'h5'   => 'h5',
			'div'  => 'div',
			'span' => 'span',
		];
	}

	/**
	 * Grid style choices.
	 */
	protected function choices_grid_style(): void {
		$this->choices = [
			'fit_columns' => \esc_html__( 'Fit Columns', 'total-theme-core' ),
			'masonry'     => \esc_html__( 'Masonry', 'total-theme-core' ),
		];
	}

	/**
	 * Grid column gap choices.
	 */
	protected function choices_grid_gap(): void {
		if ( \function_exists( 'wpex_column_gaps' ) ) {
			$this->choices = \wpex_column_gaps();
		}
	}

	/**
	 * Gap choices.
	 *
	 * @todo update when adding wpex_utl_gap() function.
	 */
	protected function choices_gap(): void {
		if ( \function_exists( 'wpex_column_gaps' ) ) {
			$this->choices = \wpex_column_gaps();
		}
	}

	/**
	 * Grid columns choices.
	 */
	protected function choices_grid_columns(): void {
		if ( \function_exists( 'wpex_grid_columns' ) ) {
			$this->choices = \wpex_grid_columns();
		}
	}

	/**
	 * Masonry layout mode choices.
	 */
	protected function choices_masonry_layout_mode(): void {
		$this->choices = [
			'masonry' => \esc_html__( 'Masonry', 'total-theme-core' ),
			'fitRows' => \esc_html__( 'Fit Rows', 'total-theme-core' ),
		];
	}

	/**
	 * Button size choices.
	 */
	protected function choices_button_size(): void {
		$this->choices = [
			''       => \esc_html__( 'Default', 'total-theme-core' ),
			'small'  => \esc_html__( 'Small', 'total-theme-core' ),
			'medium' => \esc_html__( 'Medium', 'total-theme-core' ),
			'large'  => \esc_html__( 'Large', 'total-theme-core' ),
		];
	}

	/**
	 * Button colors choices.
	 */
	protected function choices_button_colors(): void {
		if ( \function_exists( 'wpex_get_accent_colors' ) ) {
			$this->choices = [];
			foreach ( \wpex_get_accent_colors() as $key => $settings ) {
				if ( ! empty( $settings[ 'label' ] ) ) {
					$this->choices[ ( $key == 'default' ) ? '' : $key ] = $settings[ 'label' ];
				}
			}
		}
	}

	/**
	 * Button size choices.
	 */
	protected function choices_button_styles(): void {
		if ( \function_exists( 'wpex_button_styles' ) ) {
			$this->choices = \wpex_button_styles();
		}
	}

	/**
	 * Link target choices.
	 */
	protected function choices_link_target(): void {
		$this->choices = [
			'self'   => \esc_html__( 'Same tab', 'total-theme-core' ),
			'_blank' => \esc_html__( 'New tab', 'total-theme-core' )
		];
	}

	/**
	 * Object Fit choices.
	 */
	protected function choices_object_fit(): void {
		$this->choices = [
			''           => \esc_html__( 'None', 'total-theme-core' ),
			'cover'      => \esc_html__( 'Cover', 'total-theme-core' ),
			'contain'    => \esc_html__( 'Contain', 'total-theme-core' ),
			'scale-down' => \esc_html__( 'Scale Down', 'total-theme-core' ),
			'fill'       => \esc_html__( 'Fill', 'total-theme-core' ),
		];
	}

	/**
	 * Object Position choices.
	 */
	protected function choices_object_position(): void {
		$this->choices = [
			''              => \esc_html__( 'Default', 'total-theme-core' ),
			'top'           => \esc_html__( 'Top', 'total-theme-core' ),
			'center'        => \esc_html__( 'Center', 'total-theme-core' ),
			'bottom'        => \esc_html__( 'Bottom', 'total-theme-core' ),
			'left-top'      => \esc_html__( 'Left Top', 'total-theme-core' ),
			'left'          => \esc_html__( 'Left Center', 'total-theme-core' ),
			'left-bottom'   => \esc_html__( 'Left Bottom ', 'total-theme-core' ),
			'right-top'     => \esc_html__( 'Right Top', 'total-theme-core' ),
			'right'         => \esc_html__( 'Right Center ', 'total-theme-core' ),
			'right-bottom'  => \esc_html__( 'Right Bottom', 'total-theme-core' ),
		];
	}

	/**
	 * Margin choices.
	 */
	protected function choices_margin(): void {
		if ( \function_exists( 'wpex_utl_margins' ) ) {
			$this->choices = \wpex_utl_margins();
		}
	}

	/**
	 * Bottom margin choices.
	 */
	protected function choices_bottom_margin(): void {
		$this->choices_margin();
	}

	/**
	 * Padding choices.
	 */
	protected function choices_padding(): void {
		if ( \function_exists( '\wpex_utl_paddings' ) ) {
			$this->choices = \wpex_utl_paddings();
		}
	}

	/**
	 * Padding-all choices.
	 */
	protected function choices_padding_all(): void {
		$this->choices_padding();
	}

	/**
	 * Opacity choices.
	 */
	protected function choices_opacity(): void {
		if ( \function_exists( '\wpex_utl_opacities' ) ) {
			$this->choices = (array) \wpex_utl_opacities();
		}
	}

	/**
	 * Breakpoint choices.
	 */
	protected function choices_breakpoint(): void {
		if ( \function_exists( '\wpex_utl_breakpoints' ) ) {
			$this->choices = (array) \wpex_utl_breakpoints();
			/*if ( ! empty( $this->args['std'] ) && array_key_exists( $this->args['std'], $this->choices ) ) {
				$this->choices[''] = \esc_html__( 'None', 'total-theme-core' );
			}*/
		}
	}

	/**
	 * Breakpoint hover animations.
	 */
	protected function choices_hover_animations(): void {
		if ( \function_exists( 'wpex_hover_css_animations' ) ) {
			$this->choices = \wpex_hover_css_animations();
		}
	}

	/**
	 * Transition duration choices.
	 */
	protected function choices_transition_duration(): void {
		$this->choices = [
			''       => \esc_html__( 'Default', 'total-theme-core' ),
			'0ms'    => '0ms',
			'75ms'   => '75ms',
			'100ms'  => '100ms',
			'150ms'  => '150ms',
			'200ms'  => '200ms',
			'300ms'  => '300ms',
			'500ms'  => '500ms',
			'700ms'  => '700ms',
			'1000ms' => '1000ms',
		];
	}

	/**
	 * Aspect Ratio choices.
	 */
	protected function choices_aspect_ratio(): void {
		$this->choices = [
			''     => \esc_html__( 'Default', 'total-theme-core' ),
			'1/1'  => \esc_html__( 'Square - 1:1', 'total-theme-core' ),
			'4/3'  => \esc_html__( 'Standard - 4:3', 'total-theme-core' ),
			'3/4'  => \esc_html__( 'Portrait - 3:4', 'total-theme-core' ),
			'3/2'  => \esc_html__( 'Classic - 3:2', 'total-theme-core' ),
			'2/3'  => \esc_html__( 'Classic Portrait - 2:3', 'total-theme-core' ),
			'16/9' => \esc_html__( 'Wide - 16:9', 'total-theme-core' ),
			'9/16' => \esc_html__( 'Tall - 9:16', 'total-theme-core' ),
		];
	}

	/**
	 * Heading styles.
	 */
	protected function choices_header_style(): void {
		if ( \function_exists( '\wpex_get_theme_heading_styles' ) ) {
			$this->choices = (array) \wpex_get_theme_heading_styles();
		}
	}

	/**
	 * Form styles.
	 */
	protected function choices_form_style(): void {
		if ( \function_exists( '\wpex_get_form_styles' ) ) {
			$this->choices = (array) \wpex_get_form_styles();
		}
	}

	/**
	 * Font size choices.
	 */
	protected function choices_font_size(): void {
		if ( \function_exists( 'wpex_utl_font_sizes' ) ) {
			$this->choices = \wpex_utl_font_sizes();
		}
	}

	/**
	 * Font weight choices.
	 */
	protected function choices_font_weight(): void {
		if ( \function_exists( 'wpex_font_weights' ) ) {
			$this->choices = \wpex_font_weights();
		}
	}

	/**
	 * Text decoration choices.
	 */
	protected function choices_text_decoration(): void {
		$this->choices = [
			''             => \esc_html__( 'Default', 'total-theme-core' ),
			'underline'    => \esc_html__( 'Underline', 'total-theme-core' ),
			'overline'     => \esc_html__( 'Overline','total-theme-core' ),
			'line-through' => \esc_html__( 'Line Through', 'total-theme-core' ),
		];
	}

	/**
	 * Text transform choices.
	 */
	protected function choices_text_transform(): void {
		$this->choices = [
			''           => \esc_html__( 'Default', 'total-theme-core' ),
			'none'       => \esc_html__( 'None', 'total-theme-core' ),
			'capitalize' => \esc_html__( 'Capitalize', 'total-theme-core' ),
			'uppercase'  => \esc_html__( 'Uppercase', 'total-theme-core' ),
			'lowercase'  => \esc_html__( 'Lowercase', 'total-theme-core' ),
		];
	}

	/**
	 * Text align choices.
	 */
	protected function choices_text_align(): void {
		$this->choices = [
			''       => \esc_html__( 'Default', 'total-theme-core' ),
			'left'   => \esc_html__( 'Left', 'total-theme-core' ),
			'center' => \esc_html__( 'Center','total-theme-core' ),
			'right'  => \esc_html__( 'Right', 'total-theme-core' ),
		];
	}

	/**
	 * Font style choices.
	 */
	protected function choices_font_style(): void {
		$this->choices = [
			''        => \esc_html__( 'Default', 'total-theme-core' ),
			'normal'  => \esc_html__( 'Normal', 'total-theme-core' ),
			'italic'  => \esc_html__( 'Italic', 'total-theme-core' ),
			'oblique' => \esc_html__( 'Oblique', 'total-theme-core' ),
		];
	}

	/**
	 * Shadow choices.
	 */
	protected function choices_shadow(): void {
		if ( \function_exists( 'wpex_utl_shadows' ) ) {
			$this->choices = \wpex_utl_shadows();
		}
	}

	/**
	 * Divider style choices.
	 */
	protected function choices_divider_style(): void {
		if ( \function_exists( '\wpex_utl_divider_styles' ) ) {
			$this->choices = (array) \wpex_utl_divider_styles();
		}
	}

	/**
	 * Border width choices.
	 */
	protected function choices_border_width(): void {
		if ( \function_exists( '\wpex_utl_border_widths' ) ) {
			$this->choices = (array) \wpex_utl_border_widths();
		}
	}

	/**
	 * Justify content choices.
	 */
	protected function choices_justify_content(): void {
		$this->choices = [
			''              => \esc_html__( 'Default', 'total-theme-core' ),
			'start'         => \esc_html__( 'Start', 'total-theme-core' ),
			'center'        => \esc_html__( 'Center', 'total-theme-core' ),
			'end'           => \esc_html__( 'End', 'total-theme-core' ),
			'space-between' => \esc_html__( 'Space Between', 'total-theme-core' ),
			'space-around'  => \esc_html__( 'Space Around', 'total-theme-core' ),
			'space-evenly'  => \esc_html__( 'Space Evenly', 'total-theme-core' ),
		];
	}

	/**
	 * Align items choices.
	 */
	protected function choices_align_items(): void {
		$this->choices = [
			''        => \esc_html__( 'Default', 'total-theme-core' ),
			'stretch' => \esc_html__( 'Stretch', 'total-theme-core' ),
			'center'  => \esc_html__( 'Center', 'total-theme-core' ),
			'start'   => \esc_html__( 'Start', 'total-theme-core' ),
			'end'     => \esc_html__( 'End', 'total-theme-core' ),
		];
	}

	/**
	 * Justify items choices.
	 */
	protected function choices_justify_items(): void {
		$this->choices_align_items();
	}

	/**
	 * Line height choices.
	 */
	protected function choices_line_height(): void {
		if ( \function_exists( 'wpex_utl_line_height' ) ) {
			$this->choices = \wpex_utl_line_height();
		}
	}

	/**
	 * Letter spacing choices.
	 */
	protected function choices_letter_spacing(): void {
		if ( \function_exists( 'wpex_utl_letter_spacing' ) ) {
			$this->choices = \wpex_utl_letter_spacing();
		}
	}

	/**
	 * Border style choices.
	 */
	protected function choices_border_style(): void {
		$this->choices = [
			''       => \esc_html__( 'Default', 'total-theme-core' ),
			'solid'  => \esc_html__( 'Solid', 'total-theme-core' ),
			'dashed' => \esc_html__( 'Dashed', 'total-theme-core' ),
			'dotted' => \esc_html__( 'Dotted', 'total-theme-core' ),
		];
	}

	/**
	 * Content style choices.
	 */
	protected function choices_content_style(): void {
		$this->choices = (array) apply_filters( 'vcex_entry_content_styles', [
			'none'     => \esc_html__( 'None', 'total-theme-core' ),
			'boxed'    => \esc_html__( 'Boxed', 'total-theme-core' ),
			'bordered' => \esc_html__( 'Bordered', 'total-theme-core' ),
		] );
	}

	/**
	 * Border radius choices.
	 */
	protected function choices_border_radius(): void {
		if ( \function_exists( '\wpex_utl_border_radius' ) ) {
			$this->choices = \wpex_utl_border_radius( $this->args['supports_blobs'] ?? false );
		}
	}

	/**
	 * Visibility choices.
	 */
	protected function choices_visibility(): void {
		if ( \function_exists( 'totaltheme_get_visibility_choices' ) ) {
			$this->choices = \totaltheme_get_visibility_choices( 'elementor' !== $this->builder );
		}
	}

	/**
	 * Typography style choices.
	 */
	protected function choices_typography_style(): void {
		$this->choices = (array) [
			''        => \esc_html__( 'Default', 'total-theme-core' ),
			'wpex-h1' => 'h1',
			'wpex-h2' => 'h2',
			'wpex-h3' => 'h3',
			'wpex-h4' => 'h4',
		];
	}

	/**
	 * Image crop location choices.
	 */
	protected function choices_overlay_style(): void {
		if ( \function_exists( 'totaltheme_call_static' ) ) {
			$this->choices = (array) totaltheme_call_static( 'Overlays', 'get_style_choices' );
		}
	}

	/**
	 * Image filter choices.
	 */
	protected function choices_image_filter(): void {
		if ( \function_exists( 'wpex_image_filters' ) ) {
			$this->choices = \wpex_image_filters();
		}
	}

	/**
	 * Img filter choices.
	 */
	protected function choices_img_filter(): void {
		$this->choices_image_filter();
	}

	/**
	 * Image hover choices.
	 */
	protected function choices_image_hover(): void {
		if ( \function_exists( 'wpex_image_hovers' ) ) {
			$this->choices = \wpex_image_hovers();
		}
	}

	/**
	 * Img hover choices.
	 */
	protected function choices_img_hover_style(): void {
		$this->choices_image_hover();
	}

	/**
	 * Image crop location choices.
	 */
	protected function choices_image_crop_locations(): void {
		if ( \function_exists( 'wpex_image_crop_locations' ) ) {
			$this->choices = \wpex_image_crop_locations();
		}
	}

	/**
	 * Image size choices.
	 */
	protected function choices_image_sizes(): void {
		$sizes = [
			'wpex_custom' => \esc_html__( 'Custom Size', 'total-theme-core' ),
		];
		if ( \function_exists( 'get_intermediate_image_sizes' ) ) {
			$get_sizes = \get_intermediate_image_sizes();
			\array_unshift( $get_sizes, 'full' );
			$get_sizes = \array_combine( $get_sizes, $get_sizes );
			$sizes = \array_merge( $sizes, $get_sizes );
		}
		$this->choices = $sizes;
	}

	/**
	 * Slider animation choices.
	 */
	protected function choices_slider_animation(): void {
		$this->choices = [
			'fade_slides' => \esc_html__( 'Fade', 'total-theme-core' ),
			'slide'       => \esc_html__( 'Slide', 'total-theme-core' ),
		];
	}

	/*
	 * Mix Blend Mode.
	 */
	protected function choices_mix_blend_mode(): void {
		$this->choices = [
			''             => \esc_html__( 'Normal', 'total-theme-core' ),
			'multiply'     => \esc_html__( 'Multiply', 'total-theme-core' ),
			'screen'       => \esc_html__( 'Screen', 'total-theme-core' ),
			'overlay'      => \esc_html__( 'Overlay', 'total-theme-core' ),
			'darken'       => \esc_html__( 'Darken', 'total-theme-core' ),
			'lighten'      => \esc_html__( 'Lighten', 'total-theme-core' ),
			'color-dodge'  => \esc_html__( 'Color Doge', 'total-theme-core' ),
			'color-burn'   => \esc_html__( 'Color Burn', 'total-theme-core' ),
			'hard-light'   => \esc_html__( 'Hard Light', 'total-theme-core' ),
			'soft-light'   => \esc_html__( 'Soft Light', 'total-theme-core' ),
			'difference'   => \esc_html__( 'Difference', 'total-theme-core' ),
			'exclusion'    => \esc_html__( 'Exclusion', 'total-theme-core' ),
			'hue'          => \esc_html__( 'Hue', 'total-theme-core' ),
			'saturation'   => \esc_html__( 'Saturation', 'total-theme-core' ),
			'color'        => \esc_html__( 'Color', 'total-theme-core' ),
			'luminosity'   => \esc_html__( 'Luminosity', 'total-theme-core' ),
			'plus-lighter' => \esc_html__( 'Plus Lighter', 'total-theme-core' ),
		];
	}

	/**
	 * Onclick choices.
	 */
	protected function choices_onclick(): void {
		$choices = [
			'custom_link'           => \esc_html__( 'Custom Link', 'total-theme-core' ),
			'internal_link'         => \esc_html__( 'Internal Page', 'total-theme-core' ),
			'home'                  => \esc_html__( 'Homepage', 'total-theme-core' ),
			'post_permalink'        => \esc_html__( 'Current Post', 'total-theme-core' ),
			'current_url'           => \esc_html__( 'Current URL', 'total-theme-core' ),
			'post_author'           => \esc_html__( 'Post Author', 'total-theme-core' ),
			'local_scroll'          => \esc_html__( 'Scroll to Section', 'total-theme-core' ),
			'toggle_element'        => \esc_html__( 'Toggle Element', 'total-theme-core' ),
			'custom_field'          => \esc_html__( 'Custom Field', 'total-theme-core' ),
			'callback_function'     => \esc_html__( 'Callback Function', 'total-theme-core' ),
			'popup'                 => \esc_html__( 'Inline Content or iFrame Popup', 'total-theme-core' ),
			'lightbox_image'        => \esc_html__( 'Image lightbox', 'total-theme-core' ),
			'lightbox_gallery'      => \esc_html__( 'Image Gallery Lightbox', 'total-theme-core' ),
			'lightbox_post_gallery' => \esc_html__( 'Post Image Gallery Lightbox', 'total-theme-core' ),
			'lightbox_video'        => \esc_html__( 'Video Lightbox', 'total-theme-core' ),
			'lightbox_post_video'   => \esc_html__( 'Post Video Lightbox', 'total-theme-core' ),
			'go_back'               => \esc_html__( 'Back Link', 'total-theme-core' ),
			'search_toggle'         => \esc_html__( 'Open Search', 'total-theme-core' ),
		];

		if ( \class_exists( '\WooCommerce', false ) ) {
			$choices['cart_toggle'] = \esc_html__( 'Open Cart', 'total-theme-core' );
		}

		if ( \function_exists( '\totaltheme_call_static' ) && \totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
			$choices['dark_mode_toggle'] = \esc_html__( 'Dark Mode Switch', 'total-theme-core' );
		}

		if ( \class_exists( '\WooCommerce', false ) ) {
			$choices['cart_toggle'] = \esc_html__( 'Open Cart', 'total-theme-core' );
		}

		if ( \class_exists( 'Just_Events\Plugin', false ) ) {
			$choices['just_event_link'] = \esc_html__( 'Just Event Link', 'total-theme-core' );
		}

		$this->choices = (array) $choices;
	}

	/**
	 * Orderby choices.
	 */
	protected function choices_orderby(): void {
		$choices = [
			''               => \esc_html__( 'Default', 'total-theme-core' ),
			'date'           => \esc_html__( 'Date', 'total-theme-core' ),
			'title'          => \esc_html__( 'Title', 'total-theme-core' ),
			'name'           => \esc_html__( 'Name (post slug)', 'total-theme-core' ),
			'modified'       => \esc_html__( 'Modified', 'total-theme-core' ),
			'author'         => \esc_html__( 'Author', 'total-theme-core' ),
			'rand'           => \esc_html__( 'Random', 'total-theme-core' ),
			'parent'         => \esc_html__( 'Parent', 'total-theme-core' ),
			'type'           => \esc_html__( 'Type', 'total-theme-core' ),
			'ID'             => \esc_html__( 'ID', 'total-theme-core' ),
			'relevance'      => \esc_html__( 'Relevance', 'total-theme-core' ),
			'comment_count'  => \esc_html__( 'Comment Count', 'total-theme-core' ),
			'menu_order'     => \esc_html__( 'Menu Order', 'total-theme-core' ),
			'meta_value'     => \esc_html__( 'Meta Key Value', 'total-theme-core' ),
			'meta_value_num' => \esc_html__( 'Meta Key Value Num', 'total-theme-core' ),
		];

		if ( \class_exists( 'WooCommerce', false ) ) {
			$choices['woo_price']        = \esc_html__( 'WooCommerce - Price', 'total-theme-core' );
			$choices['woo_best_selling'] = \esc_html__( 'WooCommerce - Sales', 'total-theme-core' );
			$choices['woo_top_rated']    = \esc_html__( 'WooCommerce - Average Rating', 'total-theme-core' );
		}

		 // @todo rename filter
		$this->choices = (array) \apply_filters( 'vcex_orderby', $choices );
	}

	/**
	 * Template choices.
	 */
	protected function choices_template(): void {
		$this->choices = [
			'' => \esc_html( '— Select —', 'total-theme-core' ),
		];
		if ( \function_exists( 'totaltheme_call_non_static' ) ) {
			$template_type = $this->args['template_type'] ?? [ 'part', 'single' ];
			$templates = totaltheme_call_non_static( 'Theme_Builder', 'get_template_choices', $template_type, false );
			if ( $templates ) {
				$this->choices = $this->choices + $templates; // can't use array_merge because we need to keep keys.
			}
		}
	}

	/**
	 * Card choices.
	 */
	protected function choices_card_style(): void {
		if ( \is_callable( 'WPEX_Card::get_grouped_card_style_options' ) ) {
			$this->choices = \WPEX_Card::get_grouped_card_style_options();
		}
	}

	/**
	 * Menu choices.
	 */
	protected function choices_menu(): void {
		$this->choices = [
			'' => \esc_html__( '— Select —', 'total-theme-core' ),
		];
		$menus = get_terms( 'nav_menu', [
			'hide_empty' => true,
		] );
		if ( \is_array( $menus ) && ! is_wp_error( $menus ) ) {
			foreach ( $menus as $menu ) {
				$this->choices[ $menu->term_id ] = \esc_attr( $menu->name );
			}
		}
	}

	/**
	 * ACF Repeater fields.
	 */
	protected function choices_acf_repeater_fields(): void {
		$this->choices = require TTC_PLUGIN_DIR_PATH . 'inc/vcex/partials/choices/acf_repeater_fields.php';
	}

	/**
	 * ACF Repeater templates.
	 */
	protected function choices_acf_repeater_templates(): void {
		$this->choices = require TTC_PLUGIN_DIR_PATH . 'inc/vcex/partials/choices/acf_repeater_templates.php';
	}

	/**
	 * Real Media Library Choices.
	 */
	protected function choices_real_media_library_folders(): void {
		$this->choices = require TTC_PLUGIN_DIR_PATH . 'inc/vcex/partials/choices/real_media_library_folders.php';
	}

	/**
	 * Time Zone Choices.
	 */
	protected function choices_moment_js_timezones(): void {
		$this->choices = require TTC_PLUGIN_DIR_PATH . 'inc/vcex/partials/choices/moment_js_timezones.php';
	}

	/**
	 * Icon Size Choices.
	 */
	protected function choices_icon_size(): void {
		$this->choices = [
			''       => \esc_html__( 'Default', 'total-theme-core' ),
			'xs'     => \esc_html__( 'Extra Small', 'total-theme-core' ),
			'sm'     => \esc_html__( 'Small', 'total-theme-core' ),
			'normal' => \esc_html__( 'Normal', 'total-theme-core' ),
			'md'     => \esc_html__( 'Medium', 'total-theme-core' ),
			'lg'     => \esc_html__( 'Large', 'total-theme-core' ),
			'xl'     => \esc_html__( 'Extra Large', 'total-theme-core' ),
		];
	}

	/**
	 * Carousel arrow positions.
	 */
	protected function choices_carousel_arrow_positions(): void {
		$this->choices = (array) apply_filters( 'wpex_carousel_arrow_positions', [
			'default'   => \esc_html__( 'Default', 'total-theme-core' ),
			'abs'       => \esc_html__( 'Absolute (Left/Right)', 'total-theme-core' ),
			'top-right' => \esc_html__( 'Top Right', 'total-theme-core' ),
			'left'      => \esc_html__( 'Bottom Left', 'total-theme-core' ),
			'center'    => \esc_html__( 'Bottom Center', 'total-theme-core' ),
			'right'     => \esc_html__( 'Bottom Right', 'total-theme-core' ),
		] );
	}

	/**
	 * Carousel arrow styles.
	 */
	protected function choices_carousel_arrow_styles(): void {
		$this->choices = (array) apply_filters( 'wpex_carousel_arrow_styles', [
			''             => \esc_html__( 'Default', 'total-theme-core' ),
			'round-white'  => \esc_html__( 'Rounded White', 'total-theme-core' ),
			'round-black'  => \esc_html__( 'Rounded Black', 'total-theme-core' ),
			'round-accent' => \esc_html__( 'Rounded Accent', 'total-theme-core' ),
			'slim'         => \esc_html__( 'Slim', 'total-theme-core' ),
			'min'          => \esc_html__( 'Minimal', 'total-theme-core' ),
			'border'       => \esc_html__( 'Border', 'total-theme-core' ),
			'circle'       => \esc_html__( 'Circle Arrow', 'total-theme-core' ),
		] );
	}

	/**
	 * Return taxonomy choices.
	 */
	protected function choices_taxonomy(): void {
		$choices = [
			'' => \esc_html( '— Select —', 'total-theme-core' ),
		];
		$taxonomies = \get_taxonomies( [
			'public' => true,
		], 'objects' );
		if ( $taxonomies && ! is_wp_error( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$choices[ $taxonomy->name ] = \ucfirst( $taxonomy->labels->name ) . ' (' . $taxonomy->name . ')';
			}
			\ksort( $choices );
			$this->choices = $choices;
		}
	}

	/**
	 * Callback function choices.
	 */
	protected function choices_callback_functions(): void {
		$this->choices = [
			'' => \esc_html__( '— Select —', 'total-theme-core' ),
		];
		if ( \defined( 'VCEX_CALLBACK_FUNCTION_WHITELIST' )
			&& \is_array( \VCEX_CALLBACK_FUNCTION_WHITELIST )
		) {
			$this->choices = \array_merge(
				$this->choices,
				\array_combine( \VCEX_CALLBACK_FUNCTION_WHITELIST, \VCEX_CALLBACK_FUNCTION_WHITELIST )
			);
		}
	}

	/**
	 * Return choices.
	 */
	public function get_choices(): array {
		return $this->choices;
	}

}
