<?php

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Template Shortcode.
 */
if ( ! class_exists( 'VCEX_WooCommerce_Template_Shortcode' ) ) {

	class VCEX_WooCommerce_Template_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_woocommerce_template';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Get shortcode title.
		 */
		public static function get_title(): string {
			return esc_html__( 'Woo Template Part', 'total-theme-core' );
		}

		/**
		 * Get shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Returns WooCommerce template parts', 'total-theme-core' );
		}

		/**
		 * Custom VC map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			$settings = [
				'category'         => [ 'WooCommerce' ],
				'admin_enqueue_js' => 'woocommerce-template',
				'js_view'          => 'vcexWooTemplateView',
			];
			if ( $branding = \vcex_shortcodes_branding() ) {
				$settings['category'][] = $branding;
			}
			return $settings;
		}

		/**
		 * Shortcode output => Get template file and display shortcode.
		 */
		public static function output( $atts, $content = null, $shortcode_tag = '' ): ?string {
			if ( ! vcex_maybe_display_shortcode( self::TAG, $atts ) || ! function_exists( 'wc_get_product' ) ) {
				return null;
			}

			$atts = vcex_shortcode_atts( self::TAG, $atts, get_class() );
			$template_choices = self::get_template_choices();

			if ( empty( $atts['part'] ) || ! in_array( $atts['part'], $template_choices ) ) {
				return null;
			}

			global $product;
			global $post;

			$html = '';
			$is_edit_mode = vcex_is_template_edit_mode();
			$unique_classname = vcex_element_unique_classname();

			$class = [
				'vcex-wc-template-part',
				'vcex-wc-template-part--' . self::parse_part_class( $atts['part'] ),
				'vcex-module',
				'woocommerce', // styles sometimes target this class
			];

			if ( 'loop/orderby' === $atts['part'] ) {
				$class[] = 'wpex-w-fit';
			}

			if ( ! empty( $atts['css_animation'] ) ) {
				$class[] = vcex_get_css_animation( $atts['css_animation'] );
			}

			if ( ! empty( $atts['text_align'] ) ) {
				if ( 'loop/orderby' === $atts['part'] ) {
					$class[] = vcex_parse_align_class( $atts['text_align'] );
				}
				$class[] = 'wpex-text-' . sanitize_html_class( $atts['text_align'] ); // !!! important!!!
			}

			if ( ! empty( $atts['css'] ) ) {
				$class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
			}

			if ( ! empty( $atts['el_class'] ) ) {
				$class[] = vcex_get_extra_class( $atts['el_class'] );
			}

			if ( ! empty( $atts['bottom_margin'] ) ) {
				$class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
			}

			$shortcode_style = vcex_inline_style( [
				'animation_delay'    => $atts['animation_delay'] ?? null,
				'animation_duration' => $atts['animation_duration'] ?? null,
			] );

			$typography_css = vcex_inline_style( [
				'color'                   => $atts['color'] ?? null,
				'--wpex-link-color'       => $atts['color'] ?? null,
				'--wpex-hover-link-color' => $atts['color'] ?? null,
				'--wpex-woo-price-color'  => $atts['color'] ?? null,
				'font_family'             => $atts['font_family'] ?? null,
				'font_size'               => $atts['font_size'] ?? null,
				'letter_spacing'          => $atts['letter_spacing'] ?? null,
				'font_weight'             => $atts['font_weight'] ?? null,
				'line_height'             => $atts['line_height'] ?? null,
				'text_transform'          => $atts['text_transform'] ?? null,
				'text_align'              => $atts['text_align'] ?? null,
			], false );

			$responsive_css = vcex_element_responsive_css( [
				'font_size' => $atts['font_size'] ?? null,
			], 'vcex-wc-template-part.'. $unique_classname . '> *' );

			if ( $typography_css || $responsive_css ) {
				$class[] = $unique_classname;
				$html .= '<style>';
					if ( $typography_css ) {
						$html .= '.vcex-wc-template-part.' . $unique_classname .' > *:not(.star-rating) {' . wp_strip_all_tags( $typography_css ) . '}'; // note: can't use esc_attr because it can break font families.
					}
					if ( $responsive_css ) {
						$html .= $responsive_css;
					}
				$html  .= '</style>';
			}

			$html .= '<div class="' . esc_attr( implode( ' ', array_filter( $class ) ) ) . '"' . $shortcode_style . '>';
				$product_data = null;

				// Catalog parts don't require $product.
				if ( in_array( $atts['part'], [ 'loop/orderby', 'loop/result-count' ], true ) ) {
					$template_html = self::get_template_part( $atts['part'], null, $is_edit_mode );
					if ( $template_html ) {
						$html .= $template_html;
					} elseif ( $is_edit_mode ) {
						$html .= array_flip( $template_choices )[ $atts['part'] ];
					}
				}
				// Singular and Entry parts require $product.
				else {

					if ( $is_edit_mode ) {
						$products = get_posts( [
							'numberposts' => 1,
							'post_type'   => 'product',
							'fields'      => 'ids',
						] );
						if ( isset( $products[0] ) ) {
							$temp_post    = $post;
							$post         = get_post( $products[0] );
							$product_data = wc_get_product( $products[0] );
						}
					} else {
						$product_data = get_post();
					}

					if ( $product_data ) {
						$product = is_object( $product_data ) && in_array( $product_data->post_type, [ 'product', 'product_variation' ], true ) ? wc_setup_product_data( $post ) : false;
						if ( $product ) {
							$template_html = self::get_template_part( $atts['part'], $product, $is_edit_mode );
							if ( $template_html ) {
								$html .= $template_html;
							} elseif ( $is_edit_mode ) {
								$html .= array_flip( $template_choices )[ $atts['part'] ];
							}
							if ( $is_edit_mode && ! empty( $temp_post ) ) {
								$post = $temp_post;
							}
						}
						wc_setup_product_data( $post );
					}
				}
			$html .= '</div>';
			return $html;
		}

		/**
		 * Returns template part.
		 */
		protected static function get_template_part( $part, $product, $is_edit_mode ) {
			if ( $is_edit_mode ) {
				switch ( $part ) {
					case 'single-product/tabs/description':
					case 'single-product/tabs/tabs':
						return '<div class="wpex-surface-3 wpex-text-2 wpex-p-15 wpex-text-center">' . esc_html__( 'This element is disabled in the frontend editor to prevent an endless loop.', 'total-theme-core' ) .'</div>';
						break;
					case 'single-product/product-image':
						if ( function_exists( 'wpex_get_placeholder_image' ) ) {
							return wpex_get_placeholder_image();
						}
						break;
				}
			}
			ob_start();
			switch ( $part ) {
				// Catalog
				case 'loop/result-count':
					if ( function_exists( 'woocommerce_result_count' ) ) {
						woocommerce_result_count();
					}
					break;
				case 'loop/orderby':
					if ( function_exists( 'woocommerce_catalog_ordering' ) ) {
						woocommerce_catalog_ordering();
					}
					break;
				// Entry
				case 'loop/title':
					if ( function_exists( 'woocommerce_template_loop_product_title' ) ) {
						woocommerce_template_loop_product_title();
					}
					break;
				case 'loop/thumbnail':
					if ( function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
						woocommerce_template_loop_product_thumbnail();
					}
					break;
				case 'loop/add-to-cart':
					if ( function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
						woocommerce_template_loop_add_to_cart();
					}
					break;
				// Single
				case 'single-product/product-attributes':
					if ( function_exists( 'wc_display_product_attributes' ) ) {
						wc_display_product_attributes( $product );
					}
					break;
				case 'single-product/stock':
					if ( function_exists( 'wc_get_stock_html' ) ) {
						echo wc_get_stock_html( $product );
					}
					break;
				case 'single-product/add-to-cart':
					if ( function_exists( 'woocommerce_template_single_add_to_cart' ) ) {
						woocommerce_template_single_add_to_cart();
					}
					break;
				case 'single-product/related':
					if ( function_exists( 'wpex_woocommerce_output_related_products' ) ) {
						wpex_woocommerce_output_related_products();
					} elseif ( function_exists( 'woocommerce_output_related_products' ) ) {
						woocommerce_output_related_products();
					}
					break;
				case 'single-product/up-sells':
					if ( function_exists( 'wpex_woocommerce_upsell_display' ) ) {
						wpex_woocommerce_upsell_display();
					} elseif ( function_exists( 'woocommerce_upsell_display' ) ) {
						woocommerce_upsell_display();
					}
					break;
				case 'single-product/reviews':
					if ( function_exists( 'comments_template' ) ) {
						comments_template();
					}
					break;
				default:
					if ( function_exists( 'wc_get_template' ) ) {
						wc_get_template( "{$part}.php" );
					}
					break;
			}
			return ob_get_clean();
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Template', 'total-theme-core' ),
					'param_name' => 'part',
					'value' => self::get_template_choices(),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				// Typography
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'text_transform',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				// CSS
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
			);
		}

		/**
		 * Returns classname based on part.
		 */
		protected static function parse_part_class( $part ) {
			if ( 'single-product/tabs/tabs' === $part ) {
				return 'single-product__tabs';
			}
			return str_replace( '/', '__', $part );
		}

		/**
		 * Returns array of allowed template choices.
		 */
		protected static function get_template_choices() {
			return [
				esc_html__( '— Select —', 'total-theme-core' ) => '',
				// Entry
				esc_html__( 'Product Entry: Title', 'total-theme-core' ) => 'loop/title',
				esc_html__( 'Product Entry: Thumbnail', 'total-theme-core' ) => 'loop/thumbnail',
				esc_html__( 'Product Entry: Thumbnail with Secondary Hover', 'total-theme-core' ) => 'loop/thumbnail/image-swap',
				esc_html__( 'Product Entry: Price', 'total-theme-core' ) => 'loop/price',
				esc_html__( 'Product Entry: Rating', 'total-theme-core' ) => 'loop/rating',
				esc_html__( 'Product Entry: Sale Flash', 'total-theme-core' ) => 'loop/sale-flash',
				esc_html__( 'Product Entry: Add to Cart', 'total-theme-core' ) => 'loop/add-to-cart',
				// Single
				esc_html__( 'Single Product: Image', 'total-theme-core' ) => 'single-product/product-image',
				esc_html__( 'Single Product: Title', 'total-theme-core' ) => 'single-product/title',
				esc_html__( 'Single Product: Sale Flash', 'total-theme-core' ) => 'single-product/sale-flash',
				esc_html__( 'Single Product: Price', 'total-theme-core' ) => 'single-product/price',
				esc_html__( 'Single Product: Stock', 'total-theme-core' ) => 'single-product/stock',
				esc_html__( 'Single Product: Rating', 'total-theme-core' ) => 'single-product/rating',
				esc_html__( 'Single Product: Short Description', 'total-theme-core' ) => 'single-product/short-description',
				esc_html__( 'Single Product: Add to Cart', 'total-theme-core' ) => 'single-product/add-to-cart',
				esc_html__( 'Single Product: Meta', 'total-theme-core' ) => 'single-product/meta',
				esc_html__( 'Single Product: Attributes', 'total-theme-core' ) => 'single-product/product-attributes',
				esc_html__( 'Single Product: Tabs', 'total-theme-core' ) => 'single-product/tabs/tabs',
				esc_html__( 'Single Product: Tab Description', 'total-theme-core' ) => 'single-product/tabs/description',
				esc_html__( 'Single Product: Tab Additional Information', 'total-theme-core' ) => 'single-product/tabs/additional-information',
				esc_html__( 'Single Product: Reviews', 'total-theme-core' ) => 'single-product/reviews',
				esc_html__( 'Single Product: Related', 'total-theme-core' ) => 'single-product/related',
				esc_html__( 'Single Product: Up-Sells', 'total-theme-core' ) => 'single-product/up-sells',
				// Catalog
				esc_html__( 'Catalog: Sort By Dropdown', 'total-theme-core' ) => 'loop/orderby',
				esc_html__( 'Catalog: Result Count', 'total-theme-core' ) => 'loop/result-count',
			];
		}

	}

}

new VCEX_WooCommerce_Template_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_WooCommerce_Template' ) ) {
	class WPBakeryShortCode_Vcex_WooCommerce_Template extends WPBakeryShortCode {
		
		/**
		 * Modify the title HTML.
		 */
		public function outputTitle( $title ) {
			$icon = $this->settings( 'icon' );
			return '<h4 class="wpb_element_title"><i class="vc_general vc_element-icon' . ( ! empty( $icon ) ? ' ' . $icon : '' ) . '" aria-hidden="true"></i><span class="vcex-heading-text">' . esc_html__( 'WooCommerce Template Part', 'total-theme-core' ) . '<span></span></span></h4><span class="vc_admin_label">' . VCEX_WooCommerce_Template_Shortcode::get_title() . '</label></span>';
		}
	}
}
