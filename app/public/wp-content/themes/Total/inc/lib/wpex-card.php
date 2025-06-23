<?php

defined( 'ABSPATH' ) || exit;

/**
 * WPEX_Card Class.
 *
 * @copyright WPExplorer.com
 * @license All Rights Reserved. This is proprietary code. Do not copy, share or redistribute without permission.
 */
if ( ! class_exists( 'WPEX_Card' ) ) {

	#[\AllowDynamicProperties]
	class WPEX_Card {

		/**
		 * The card arguments.
		 */
		public $args = [];

		/**
		 * The card style.
		 */
		public $style = 'blog_1';

		/**
		 * Post ID for dynamic cards.
		 */
		public $post_id = null;

		/**
		 * Term object for term based cards.
		 */
		public $term = null;

		/**
		 * Unique card ID.
		 */
		public $unique_id = null;

		/**
		 * Current element args.
		 */
		public $el_args = [];

		/**
		 * Holds array of all card styles to prevent extra lookups.
		 */
		private static $card_styles = null;

		/**
		 * Class instance - returns the current card object.
		 */
		public static $instance;

		/**
		 * Stores all card instances - for threaded cards.
		 */
		private static $instances = [];

		/**
		 * Create or retrieve the class instance.
		 */
		public static function instance() {
			return static::$instance;
		}

		/**
		 * Initialization.
		 */
		public function __construct( $args ) {
			if ( ! is_array( $args ) || empty( $args ) ) {
				return;
			}

			$this->args = $args;

			foreach ( get_object_vars( $this ) as $key => $value ) {
				if ( isset( $this->args[ $key ] ) ) {
					$this->$key = $this->args[ $key ];
				}
			}

			/**
			 * Filters the card style.
			 * 
			 * @param string $style
			 * @param array $this Current WPEX_Card object.
			 */
			$this->style = \apply_filters( 'wpex_card_style', $this->style, $this );

			// Store this instance so we can access it globally.
			self::$instance = $this;
			self::$instances[] = $this;
		}

		/**
		 * Render card.
		 */
		final public function render() {
			$template = $this->locate_template();

			if ( ! empty( $template ) ) {

				$template_content = require $template;

				if ( $template_content ) {

					$card_style_safe = sanitize_html_class( $this->style );
					$this_class = "wpex-card";

					if ( $this->is_featured() ) {
						$this_class .= ' wpex-card-featured';
					}

					$this_class .= " wpex-card-{$card_style_safe}";

					if ( ! empty( $this->args['el_class'] ) && is_string( $this->args['el_class'] ) ) {
						$this_class .= " {$this->args['el_class']}";
					}

					if ( $this->is_template() ) {
						$meta_el_class = (string) $this->get_template_meta( 'el_class' );
						if ( $meta_el_class ) {
							$this_class .= " {$meta_el_class}";
						}
					}

					if ( function_exists( 'vcex_get_css_animation' )
						&& ! empty( $this->args['css_animation'] )
						&& 'none' !== $this->args['css_animation'] ) {
						$css_animation = (string) vcex_get_css_animation( $this->args['css_animation'] );
						if ( $css_animation ) {
							$this_class .= ' ' . trim( $css_animation );
						}
					}

					$card_html = '<div class="'. esc_attr( trim( $this_class ) ) . '">';
						$card_html .= $template_content;
						switch ( $this->get_var( 'link_type' ) ) {
							case 'modal':
								$card_html .= $this->get_modal( $this );
								break;
							case 'dialog':
								$card_html .= $this->get_dialog( $this );
								break;
						}
					$card_html .= '</div>';

				}

			}

			array_pop( self::$instances );
			self::$instance = ! empty( self::$instances ) ? end( self::$instances ) : null;

			return $card_html ?? '';
		}

		/**
		 * Locate card template.
		 */
		final protected function locate_template() {
			$style = trim( (string) $this->style );

			if ( ! $style ) {
				return;
			}

			// Get card styles
			self::get_card_styles();

			// These cards define a custom template path
			if ( ! empty( self::$card_styles[ $style ]['template'] ) ) {
				$template = self::$card_styles[ $style ]['template'];
				if ( ! file_exists( $template ) ) {
					$template = ''; // prevent errors with non existing templates
				}
			} else {
				// Custom cards created via wp admin
				// @important we can't check if the template is included in the self::$card_styles array
				// because of conlicts with WPML where the template may not be in the array
				if ( $this->is_template() ) {
					$template = WPEX_THEME_DIR . '/cards/template.php';
				}
				// Default theme cards or custom cards defined in the child theme
				elseif ( array_key_exists( $style, self::$card_styles ) ) {
					if ( str_contains( $style, '_' ) ) {
						$category = strstr( $style, '_', true );
					} else {
						$category = $style;
					}
					$path = "cards/{$category}/{$style}.php";
					$template = locate_template( $path, false );
					// Template not found, lets try and grab it from the root directory.
					if ( ! $template ) {
						$template = locate_template( "cards/{$style}.php", false );
					}
				}
			}

			/**
			 * Filters the card template.
			 *
			 * @param string $template
			 * @param array $this Current WPEX_Card object.
			 */
			$template = apply_filters( 'wpex_card_template', $template, $this );

			return (string) $template;
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Card Modals ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Returns the card modal id.
		 */
		protected function get_modal_id() {
			return 'wpex-card-modal-' . absint( $this->get_var( 'unique_id' ) );
		}

		/**
		 * Return card modal.
		 */
		protected function get_modal() {
			$output = '<div id="' . esc_attr( $this->get_modal_id() ) . '" class="wpex-card-modal wpex-hidden wpex-shadow-lg wpex-rounded-sm">';

				// Modal title.
				$title = $this->get_modal_title();

				if ( $title ) {
					$output .= '<div class="wpex-card-modal-title wpex-p-20 wpex-border-b wpex-border-solid wpex-border-gray-200 wpex-text-xl wpex-text-black wpex-text-center wpex-font-bold">';
						$output .= esc_html( $title );
					$output .= '</div>';
				}

				// Modal content.
				$content = $this->get_modal_content();

				if ( $content ) {
					if ( \WPEX_VC_ACTIVE && $wpb_style = totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' ) ) {
						if ( ! empty( $this->args['modal_template'] ) ) {
							$output .= $wpb_style->get_style( $this->args['modal_template'], true );
						}
						$output .= $wpb_style->get_style( $this->post_id );
					}
					$output .= '<div class="wpex-card-modal-body wpex-py-40 wpex-px-20 wpex-last-mb-0 wpex-clr">';
						$output .= $content;
					$output .= '</div>';
				}

				$output .= '<div class="wpex-card-modal-footer wpex-p-20 wpex-text-right wpex-border-t wpex-border-solid wpex-border-gray-200">';

					$output .= '<button href="javascript:;" data-fancybox-close class="theme-button wpex-rounded-full">';
						$output .= esc_html__( 'Close', 'total' );
					$output .= '</button>';

				$output .= '</div>';

			$output .= '</div>';

			return $output;
		}

		/**
		 * Return card dialog.
		 */
		protected function get_dialog() {
			$output = '<dialog id="' . esc_attr( $this->get_modal_id() ) . '" class="wpex-modal wpex-shadow-lg wpex-rounded wpex-p-0"><div class="wpex-modal__inner">';

				// Dialog header.
				$output .= '<div class="wpex-modal__header wpex-flex wpex-items-center wpex-gap-20 wpex-p-20 wpex-border-b wpex-border-solid wpex-border-main">';
					if ( $title = $this->get_modal_title() ) {
						$output .= '<div class="wpex-modal__title wpex-heading wpex-text-xl">' . esc_html( $title ) . '</div>';
					}
					$output .= '<button class="wpex-modal__close wpex-close-modal wpex-unstyled-button wpex-flex wpex-items-center wpex-justify-center wpex-ml-auto" aria-label="' . esc_attr__( 'close modal', 'total' ) . '">' . totaltheme_get_svg( 'material-close', 24 ) . '</button>';
				$output .= '</div>';

				// Modal content.
				$content = $this->get_modal_content();

				if ( $content ) {
					if ( \WPEX_VC_ACTIVE && $wpb_style = totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' ) ) {
						if ( ! empty( $this->args['modal_template'] ) ) {
							$output .= $wpb_style->get_style( $this->args['modal_template'], true );
						}
						$output .= $wpb_style->get_style( $this->post_id );
					}
					$output .= '<div class="wpex-modal__body wpex-p-20 wpex-last-mb-0 wpex-clr">';
						$output .= $content;
					$output .= '</div>';
				}

			$output .= '</div></dialog>';

			return $output;
		}

		/**
		 * Return card modal.
		 */
		protected function get_modal_settings() {
			$settings = array(
				'type'       => 'inline',
			//	'buttons'    => 'false',
				'small-btn'  => 'true',
				'auto-focus' => 'false',
				'touch'      => 'false',
			);

			/**
			 * Filters the card modal settings
			 *
			 * @param array $settings
			 * @param object $this Current WPEX_Card object.
			 */
			$this->modal_settings = (array) apply_filters( 'wpex_card_modal_settings', $settings, $this );

			return $this->modal_settings;
		}

		/**
		 * Return card modal content.
		 */
		protected function get_modal_content() {
			$content = '';

			if ( ! empty( $this->args['modal_template'] ) && empty( $this->args['modal_content'] ) ) {
				$temp_post = get_post( $this->args['modal_template'] );
				if ( $temp_post ) {
					$template_type = totaltheme_get_post_builder_type( $this->args['modal_template'] );
					if ( 'elementor' === $template_type ) {
						$this->args['modal_content'] = wpex_get_elementor_content_for_display( $this->args['modal_template'] );
					} else {
						$this->args['modal_content'] = $temp_post->post_content;
					}
				}
			}

			if ( ! empty( $this->args['modal_content'] ) ) {
				$content = $this->args['modal_content'];
			} elseif ( $this->post_id ) {
				$post = get_post( $this->post_id );
				if ( ! empty( $post ) && ! is_wp_error( $post ) ) {
					if ( function_exists( 'wpex_get_current_post_id' ) ) {
						$current_post = wpex_get_current_post_id();
					} else {
						$current_post = get_queried_object_id();
					}
					// !!! Important check to prevent infinite loops !!!!
					if ( $current_post !== $this->post_id ) {
						$content = $post->post_content;
					}
				}
			} elseif ( ! empty( $this->term->description ) ) {
				$content = $this->term->description;
			}

			/**
			 * Filters the card modal content.
			 *
			 * @param string $content
			 * @param object $this Current WPEX_Card object.
			 */
			$content = (string) apply_filters( 'wpex_card_modal_content', $content, $this );

			// Parse content AFTER filters so that users can pass on content to the filter that contains shortcodes
			if ( $content && ( empty( $template_type ) || 'elementor' !== $template_type ) ) {
				if ( function_exists( 'totaltheme_render_content' ) ) {
					$content = totaltheme_render_content( $content );
				} else {
					$content = do_shortcode( wp_kses_post( $content ) );
				}
			}

			return $content;
		}

		/**
		 * Return card modal title.
		 */
		protected function get_modal_title() {
			$title = '';

			if ( array_key_exists( 'modal_title', $this->args )
				&& ( false === $this->args['modal_title'] || 'false' === $this->args['modal_title'] )
			) {
				$title = '';
			} elseif ( ! empty( $this->args['modal_title'] )
				&& is_string( $this->args['modal_title'] )
				&& 'true' !== $this->args['modal_title']
			) {
				$title = $this->args['modal_title'];
			} elseif ( $this->post_id ) {
				$post = get_post( $this->post_id );
				if ( ! empty( $post ) && ! is_wp_error( $post ) ) {
					$title = get_the_title( $this->post_id );
				}
			} elseif ( ! empty( $this->term->name ) ) {
				$title = $this->term->name;
			}

			/**
			 * Filters the card modal title
			 *
			 * @param string $title
			 * @param object $this Current WPEX_Card object.
			 */
			$title = apply_filters( 'wpex_card_modal_title', $title, $this );

			return $title;
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Links ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Check if a card has a link.
		 */
		public function has_link(): bool {
			return (bool) $this->get_var( 'url' );
		}

		/**
		 * Get card url.
		 */
		public function get_url() {
			$link = '';

			$type = $this->get_var( 'link_type' );

			if ( 'none' === $type ) {
				return;
			}

			if ( ! empty( $this->args['url'] ) ) {
				$url = $this->args['url'];
			} elseif ( ! in_array( $type, [ 'dialog', 'modal', 'post', 'term' ] )
				&& $this->is_template()
				&& $custom_field = $this->get_template_meta( 'link_custom_field' )
			) {
				if ( $this->term ) {
					$url = get_term_meta( $this->term->term_id, $custom_field, true );
				} else {
					$url = get_post_meta( $this->post_id, $custom_field, true );
				}
				if ( is_array( $url ) ) {
					$url = $url['url'] ?? $url['href'] ?? $url[0];
				}
			} else {

				$url = $this->get_card_meta( 'url' );

				if ( empty( $url ) ) {
					switch ( $type ) {
						case 'dialog':
							$url = '#';
							break;
						case 'modal':
							$url = '#' . $this->get_modal_id();
							break;
						case 'lightbox':
							$url = $this->get_var( 'lightbox_url' );
							break;
						case 'post': // legacy
						case 'term': // legacy
						default:
							// @note we don't use is_post_publicly_viewable because we still want
							// to link to posts that may be privatized.
							if ( $this->post_id ) {
								if ( is_post_type_viewable( $this->get_post_type() ) ) {
									if ( function_exists( 'wpex_get_permalink' ) ) {
										$url = wpex_get_permalink( $this->post_id );
									} else {
										$url = get_permalink( $this->post_id );
									}
								}
							} elseif ( $this->term ) {
								if ( is_term_publicly_viewable( $this->term ) ) {
									$url = get_term_link( $this->term );
								}
							}
							break;
					}
				}

			}

			/**
			 * Filters the card URL.
			 * 
			 * @param string $url The URL.
			 * @param object $this Current WPEX_Card object.
			 */
			$url = (string) apply_filters( 'wpex_card_url', $url, $this );

			if ( $url ) {
				$url = esc_url( $url );
			}

			$this->url = $url;

			return $this->url;
		}

		/**
		 * Get card link type.
		 */
		public function get_link_type() {
			$type = '';

			// Check custom template first as this is the "default" value
			if ( $this->is_template() ) {
				$meta_type = $this->get_template_meta( 'link_type' );
				if ( $meta_type ) {
					$type = $meta_type;
				}
			}

			// Card specific link type should override the default
			if ( ! empty( $this->args['link_type'] ) ) {
				$type = $this->args['link_type'];
			}

			// Last we check the post meta which should override the template and card args
			if ( $this->post_id ) {
				$meta_type = get_post_meta( $this->post_id, 'wpex_card_link_type', true );
				if ( $meta_type ) {
					$type = $meta_type;
				}
			}

			/**
			 * Filters the card link type.
			 * 
			 * @param string $type Link type.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->link_type = (string) apply_filters( 'wpex_card_link_type', $type, $this );

			return $this->link_type;
		}

		/**
		 * Get card link target.
		 */
		public function get_link_target() {
			$target = '_self';

			if ( ! empty( $this->args['link_target'] ) ) {
				$target = $this->args['link_target'];
			} elseif ( $this->post_id ) {
				$target = get_post_meta( $this->post_id, 'wpex_card_link_target', true ) ?: $target;
			}

			/**
			 * Filters the card link target.
			 *
			 * @param string $target The link target.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->link_target = (string) apply_filters( 'wpex_card_link_target', $target, $this );

			return $this->link_target;
		}

		/**
		 * Get card link title.
		 */
		public function get_link_title() {
			$link_title = '';

			if ( ! empty( $this->args['link_title'] ) ) {
				$link_title = $this->args['link_title'];
			}

			/**
			 * Filters the card link title.
			 *
			 * @param string $title The link title.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->link_title = (string) apply_filters( 'wpex_card_link_title', $link_title, $this );

			return $this->link_title;
		}

		/**
		 * Get card link rel attribute.
		 */
		public function get_link_rel() {
			$rel = '';

			if ( ! empty( $this->args['link_rel'] ) ) {
				$rel = $this->args['link_rel'];
			} elseif( $this->post_id ) {
				$rel = get_post_meta( $this->post_id, 'wpex_card_link_rel', true );
			}

			/**
			 * Filters the card link rel.
			 *
			 * @param string $rel The link rel.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->link_rel = (string) apply_filters( 'wpex_card_link_rel', $rel, $this );

			return $this->link_rel;
		}

		/**
		 * Get custom card link data attributes.
		 */
		public function get_link_data() {
			$data = [];

			if ( ! empty( $this->args['link_data'] ) ) {
				$data = $this->args['link_data'];
				if ( function_exists( 'wp_parse_list' ) ) {
					$data = wp_parse_list( $data );
				}
			}

			/**
			 * Filters the card link data.
			 *
			 * @param array $data The link data.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->link_data = (array) apply_filters( 'wpex_card_link_data', $data, $this );

			return $this->link_data;
		}

		/**
		 * Get link attributes.
		 */
		public function get_link_attributes( $attributes = [] ) {
			$defaults_attributes = [
				'href' => $this->get_var( 'url' ),
			];

			$attrs = wp_parse_args( $attributes, $defaults_attributes );

			$type   = $this->get_var( 'link_type' );
			$target = $this->get_var( 'link_target' );
			$title  = $this->get_var( 'link_title' );
			$rel    = $this->get_var( 'link_rel' );
			$data   = $this->get_var( 'link_data' );

			$class = [];

			switch ( $type ) {
				case 'local':
					$class[] = 'local-scroll-link';
					break;
				case 'dialog':
					$attrs['role'] = 'button';
					$attrs['aria-controls'] = $this->get_modal_id();
					$attrs['aria-expanded'] = 'false';
					$class[] = 'wpex-open-modal';
					break;
				case 'modal':
				case 'lightbox':
					$lightbox_type = $this->get_var( 'lightbox_type' );
					$class[] = $this->get_var( 'lightbox_class' );
					$lightbox_data = $this->get_var( 'lightbox_data' );
					if ( ! empty( $lightbox_data ) && is_array( $lightbox_data ) ) {
						foreach ( $lightbox_data as $data_key => $data_val ) {
							$data[ $data_key ] = $data_val;
						}
					}
					$attrs['role'] = 'button';
					$this->enqueue_lightbox();
					break;
				default:
					break;
			}

			switch ( $target ) {
				case 'blank':
				case '_blank':
					$attrs['target'] = '_blank';
					$targeted_rel = apply_filters( 'wpex_targeted_link_rel', 'noopener noreferrer', $attrs['href'] );
					if ( $rel ) {
						$attrs['rel'] = $rel . ' ' . $targeted_rel;
					} else {
						$attrs['rel'] = $targeted_rel;
					}
					break;
				default:
					if ( $rel ) {
						$attrs['rel'] = $rel;
					}
					break;
			}

			if ( $data && is_array( $data ) ) {

				foreach ( $data as $datak => $datav ) {
					$attrs['data-' . $datak] = $datav;
				}

			}

			if ( $class ) {
				$attrs['class'] = implode( ' ', $class );
			}

			if ( $title ) {
				$attrs['title'] = esc_attr( $title );
			}

			/**
			 * Filters the card link attributes.
			 *
			 * @param array $attributes The link attributes.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->link_attributes = (array) apply_filters( 'wpex_card_link_attributes', $attrs, $this );

			return $this->link_attributes;
		}

		/**
		 * Get card link open tag.
		 */
		public function get_link_open( $args = [] ) {
			if ( ! $this->has_link() ) {
				return;
			}

			$default_args = [
				'class'      => '',
				'attributes' => [],
			];

			$args = wp_parse_args( $args, $default_args );

			if ( ! empty( $args['attributes'] ) ) {
				$attrs = $this->get_link_attributes( $args['attributes'] );
			} else {
				$attrs = $this->get_var( 'link_attributes' );
			}

			if ( $args['class'] ) {
				if ( empty( $attrs['class'] ) ) {
					$attrs['class'] = $args['class'];
				} else {
					$attrs['class'] .= ' ' . trim( (string) $args['class'] );
				}
			}

			$attrs = array_map( 'esc_attr', $attrs ); // escape attribute values.

			$html = '<a';

				foreach ( $attrs as $name => $value ) {
					if ( ! empty( $value ) ) {
						$html .= ' ' . $name . '="' . $value . '"'; // note $value already escaped
					} elseif ( 'download' === $name ) {
						$html .= ' ' . $name;
					}
				}

			$html .= '>';

			$this->link_open = $html;

			return $this->link_open;
		}

		/**
		 * Get card link close tag.
		 */
		public function get_link_close() {
			if ( $this->has_link() ) {
				return '</a>';
			}
		}

		/**
		 * Check if it's possible to add a link around the whole card.
		 */
		public function has_link_wrap(): bool {
			return ( $this->has_link() && ! $this->has_thumbnail_overlay() && 'video' !== $this->get_var( 'media_type' ) );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Lightbox ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card lightbox url.
		 */
		public function get_lightbox_url() {
			$lightbox_url = '';

			if ( isset( $this->args['lightbox_url'] ) ) {
				$lightbox_url = $this->args['lightbox_url'];
			} elseif ( isset( $this->args['url'] ) ) {
				$lightbox_url = $this->args['url'];
			} else {
				if ( $this->post_id || $this->term ) {
					$lightbox_type = $this->get_var( 'lightbox_type' );
					switch ( $lightbox_type ) {
						case 'video':
							$lightbox_url = $this->get_var( 'lightbox_video' );
							break;
						case 'gallery':
							$lightbox_url = '#';
							break;
						case 'thumbnail':
						default:
							$thumbnail_id = $this->get_var( 'thumbnail_id' );
							if ( $thumbnail_id ) {
								$lightbox_url = wpex_get_lightbox_image( $thumbnail_id );
							}
							break;
					}
				} else {
					$lightbox_url = $this->get_var( 'thumbnail_url' );
				}
			}

			/**
			 * Filters the card lightbox url.
			 *
			 * @param string $lightbox_url The lightbox url.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->lightbox_url = (string) apply_filters( 'wpex_card_lightbox_url', $lightbox_url, $this );

			return $this->lightbox_url;
		}

		/**
		 * Get card lightbox type.
		 */
		public function get_lightbox_type() {
			$lightbox_type = 'thumbnail';

			if ( isset( $this->args['lightbox_type'] ) ) {
				$lightbox_type = $this->args['lightbox_type'];
			} elseif ( $this->post_id ) {

				$link_type = $this->get_var( 'link_type' );

				switch ( $link_type ) {
					case 'modal':
						if ( $this->get_card_meta( 'url' ) ) {
							$lightbox_type = 'iframe';
						} else {
							$lightbox_type = 'modal';
						}
						break;
					default:
						$video_check = true;
						if ( 'post' === $this->get_post_type() && 'video' !== $this->get_post_format() ) {
							$video_check = false;
						}
						if ( $video_check && $this->get_var( 'lightbox_video' ) ) {
							$lightbox_type = 'video';
						} elseif ( wpex_has_post_gallery( $this->post_id ) ) {
							$lightbox_type = 'gallery';
						}
						break;
				}

			}

			/**
			 * Filters the card lightbox type.
			 *
			 * @param string $lightbox_type The lightbox type.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->lightbox_type = (string) apply_filters( 'wpex_card_lightbox_type', $lightbox_type, $this );

			return $this->lightbox_type;
		}

		/**
		 * Get lightbox video.
		 */
		public function get_lightbox_video() {
			$video = wpex_get_post_video_oembed_url( $this->post_id );

			/**
			 * Filters the card lightbox video.
			 *
			 * @param string $video The lightbox video.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->lightbox_video = (string) apply_filters( 'wpex_card_lightbox_video', $video, $this );

			return $this->lightbox_video;
		}

		/**
		 * Get lightbox class.
		 */
		public function get_lightbox_class() {
			$lightbox_class = '';

			$lightbox_type = $this->get_var( 'lightbox_type' );

			switch ( $lightbox_type ) {
				case 'gallery':
					$lightbox_class = 'wpex-lightbox-gallery';
					break;
				case 'modal':
				case 'iframe':
				default:
					$lightbox_class = 'wpex-lightbox';
					break;
			}

			/**
			 * Filters the card lightbox class.
			 *
			 * @param string $lightbox_class The lightbox class.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->lightbox_class = (string) apply_filters( 'wpex_card_lightbox_class', $lightbox_class, $this );

			return $this->lightbox_class;
		}

		/**
		 * Get lightbox data.
		 */
		public function get_lightbox_data() {
			$lightbox_data = [];

			$lightbox_type = $this->get_var( 'lightbox_type' );

			switch ( $lightbox_type ) {
				case 'modal':
					$modal_settings = $this->get_var( 'modal_settings' );
					foreach ( $modal_settings as $msk => $msv ) {
						$lightbox_data[ $msk ] = $msv;
					}
					break;
				case 'gallery':
					$lightbox_data['gallery'] = $this->get_var( 'lightbox_gallery_data' );
					break;
				case 'iframe':
					$lightbox_data['type'] = 'iframe';
					break;
				default:
					// No data needed here.
					break;
			}

			/**
			 * Filters the card lightbox data attributes.
			 *
			 * @param array $lightbox_data The lightbox data attributes.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->lightbox_data = (array) apply_filters( 'wpex_card_lightbox_data', $lightbox_data, $this );

			return $this->lightbox_data;
		}

		/**
		 * Get lightbox gallery data.
		 */
		public function get_lightbox_gallery_data() {
			$this->lightbox_gallery_data = wpex_parse_inline_lightbox_gallery( wpex_get_gallery_ids( $this->post_id ) );
			return $this->lightbox_gallery_data;
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Element ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card element.
		 */
		public function get_element( $args = [] ) {
			$default_args = [
				'name'                => '',
				'class'               => '',
				'link'                => false,
				'link_class'          => '',
				'link_rel'            => '', // used for custom links only
				'link_target'         => '', // used for custom links only
				'content'             => '',
				'screen_reader_text'  => '',
				'sanitize_content'    => true,
				'html_tag'            => 'div',
				'before'              => '',
				'after'               => '',
				'icon'                => '',
				'icon_class'          => '',
				'icon_bidi'           => false,
				'svg'                 => '',
				'prefix'              => '',
				'suffix'              => '',
				'css'                 => '',
				'data'                => '',
				'overlay'             => false,
				'attributes'          => [],
				'link_attributes'     => [],
				'strip_overlay_links' => false,
			];

			$args = wp_parse_args( $args, $default_args );

			$this->el_args = $args;

			$content = $this->parse_element_content( $args['name'] );

			if ( empty( $content ) && '0' !== $content ) {
				return;
			}

			$class = $this->parse_element_class();

			$content_out = '';

			if ( true === $args['link'] ) {

				$content_out .= $this->get_link_open( [
					'class'      => $args['link_class'],
					'attributes' => $args['link_attributes'] ?? null,
				] );

			} elseif ( ! empty( $args['link'] ) ) {

				$link_attrs = [
					'href'   => esc_url( $args['link'] ),
					'class'  => esc_attr( $args['link_class'] ),
					'rel'    => esc_attr( $args['link_rel'] ),
					'target' => esc_attr( $args['link_target'] ),
				];

				$link_attrs = wp_parse_args( $args['link_attributes'], $link_attrs );

				$content_out .= '<a ' . wpex_parse_attrs( $link_attrs ) . '>';

			}

			if ( ! empty( $args['icon'] ) && is_string( $args['icon'] ) ) {
				$icon_class = $args['icon_class'] ?? '';
				if ( str_contains( $args['icon'], 'ticon' ) ) {
					$icon_class_array = explode( ' ', str_replace( 'ticon ', '', $args['icon'] ) );
					if ( $icon_class_array ) {
						foreach ( $icon_class_array as $icon_class_array_v ) {
							if ( empty( $icon_name ) && str_starts_with( $icon_class_array_v, 'ticon-' ) ) {
								$icon_name = str_replace( 'ticon-', '', $icon_class_array_v );
							} else {
								$icon_class .= " {$icon_class_array_v}";
							}
						}
						$icon_class = trim( $icon_class );
					}
				}
				if ( $icon_html = totaltheme_get_icon( $icon_name ?? $args['icon'], $icon_class, '', $args['icon_bidi'] ) ) {
					$content_out .= $icon_html;
				} else {
					$content_out .= '<span class="' . esc_attr( trim( "{$args['icon']} {$icon_class}" ) ) . '" aria-hidden="true"></span>';
				}
			}

			if ( ! empty( $args['svg'] ) ) {
				$content_out .= $args['svg'];
			}

			if ( $content && ! empty( $args['screen_reader_text'] ) ) {
				$content = $content . '<span class="screen-reader-text">' . esc_html( $args['screen_reader_text'] ) . '</span>';
			}

			$content_out .= $args['prefix'] . $content . $args['suffix'];

			$content_out .= $this->get_overlay( $args['overlay'], 'inside_link', $args['strip_overlay_links'] );

			if ( true === $args['link'] ) {
				$content_out .= $this->get_link_close();
			} elseif ( ! empty( $args['link'] ) ) {
				$content_out .= '</a>';
			}

			$content_out .= $this->get_overlay( $args['overlay'], 'outside_link', $args['strip_overlay_links'] );

			if ( ! empty( $content_out ) ) {

				$el_css = $this->parse_element_css( $args );

				$output = $args['before'];

					$attributes = [
						'class' => $class,
						'style' => $el_css,
					];

					$attributes = wp_parse_args( $args['attributes'], $attributes );

					$output .= wpex_parse_html( $this->parse_element_html_tag(), $attributes, $content_out );

				$output .= $args['after'];

				$this->el_args = null;

				return $output;

			}

			$this->el_args = null;
		}

		/**
		 * Get card empty element..4
		 */
		public function get_empty_element( $args = [] ) {
			$args = wp_parse_args( $args, [
				'html_tag' => 'div',
				'class'    => '',
				'css'      => '',
			] );

			return wpex_parse_html( $args['html_tag'], [
				'class' => $args['class'],
				'style' => $args['css'],
			], '' );
		}

		/**
		 * Get current element name.
		 */
		protected function get_element_name() {
			if ( ! empty( $this->el_args['name'] ) ) {
				return $this->el_args['name'];
			}
			return '';
		}

		/**
		 * Parse element html tag.
		 */
		protected function parse_element_html_tag() {
			$html_tag = 'div';

			if ( ! empty( $this->el_args['html_tag'] ) ) {
				$html_tag = tag_escape( $this->el_args['html_tag'] );
			}

			if ( 'title' === $this->get_element_name() ) {
				$html_tag = $this->get_var( 'title_tag' );
			}

			return $html_tag;
		}

		/**
		 * Parse element class.
		 */
		protected function parse_element_class( $args = [] ) {
			if ( ! empty( $args ) ) {
				$this->el_args = $args;
			}

			$args = $this->el_args;

			$element_name = $this->get_element_name();

			$class = [];

			if ( $element_name ) {
				$class[] = "wpex-card-{$element_name}";
			} else {
				$class[] = 'wpex-card-element';
			}

			switch ( $element_name ) {
				case 'excerpt':
					$class[] = 'wpex-last-mb-0';
					break;
				case 'icon':
					// @todo create new utility class for icon size!
					if ( ! empty( $args['size'] ) ) {
						$class[] = 'wpex-icon-' . sanitize_html_class( $args['size'] );
					}
					break;
				case 'thumbnail':
					$class[] = 'wpex-relative';
					if ( $this->get_var( 'thumbnail_hover' ) ) {
						$class[] = wpex_image_hover_classes( $this->get_var( 'thumbnail_hover' ) );
					}
					if ( $this->get_var( 'thumbnail_filter' ) ) {
						$class[] = wpex_image_filter_class( $this->get_var( 'thumbnail_filter' ) );
					}
					break;
			}

			if ( ! empty( $args['class'] ) ) {
				$class_vals = wp_parse_list( $args['class'] );
				if ( ! empty( $class_vals ) && is_array( $class_vals ) ) {
					foreach ( $class_vals as $val ) {
						$class[] = $val;
					}
				}
			}

			if ( $args['overlay'] && 'none' !== $args['overlay'] ) {
				$overlay_class = (string) totaltheme_call_static(
					'Overlays',
					'get_parent_class',
					(string) $args['overlay']
				);
				if ( $overlay_class ) {
					$class[] = trim( (string) $overlay_class );
				}
			}

			if ( $element_name && ! empty( $this->args[$element_name . '_class'] ) ) {
				$custom_class = $this->args[$element_name . '_class'];
				if ( is_array( $custom_class ) ) {
					foreach ( $custom_class as $val ) {
						$class[] = $val;
					}
				} else {
					$class[] = $custom_class;
				}
			}

			if ( ! empty( $this->args['media_el_class'] )
				&& ( 'thumbnail' === $element_name || 'video' === $element_name ) // can't target "media" as it can cause duplicate issues.
			) {
				$class[] = $this->args['media_el_class'];
			}

			if ( $element_name && ! empty( $this->args[ "{$element_name}_font_size" ] ) ) {
				$custom_font_size = $this->args[ "{$element_name}_font_size" ];
				if ( $custom_font_size ) {
					$class = $this->modify_element_font_size( $custom_font_size, $class );
				}
			}

			if ( ! empty( $this->args['media_width'] )
				&& ( 'media' === $element_name || 'thumbnail' === $element_name )
			) {
				$media_width = $this->args['media_width'];
				if ( $media_width && 'custom' !== $media_width ) {
					$class = $this->modify_element_width( $media_width, $class );
				}
			}

			$class = array_map( 'esc_attr', $class );

			return $class;
		}

		/**
		 * Parse element content.
		 */
		protected function parse_element_content( $element = '' ) {
			$content = $this->el_args['content'] ?? '';

			if ( $content && $this->el_args['sanitize_content'] ) {
				if ( isset( $this->el_args['name'] ) && in_array( $this->el_args['name'], [ 'excerpt' ], true ) ) {
					if ( function_exists( 'totaltheme_render_content' ) ) {
						$content = totaltheme_render_content( $content );
					} else {
						$content = do_shortcode( wp_kses_post( $content ) );
					}
				} else {
					$content = do_shortcode( wp_kses_post( $content ) );
					$content = totaltheme_replace_vars( $content );
				}
			}

			return $content;
		}

		/**
		 * Parse element css.
		 */
		protected function parse_element_css() {
			$css = '';
			$args = $this->el_args;

			if ( ! empty( $args['css'] ) ) {
				$css = $args['css'];
			}

			if ( ! empty( $args['name'] ) && ! empty( $this->args[ "{$args['name']}_css" ] ) ) {
				$custom_css = $this->args[ "{$args['name']}_css" ];
				if ( is_array( $custom_css ) ) {
					$custom_css = implode( ' ', $custom_css );
				}
				$css .= ' ' . $custom_css;
			}

			if ( $css && is_string( $css ) ) {
				$css = ' style="' . esc_attr( trim( $css ) ) . '"';
			}

			return $css;
		}

		/**
		 * Modifies an element font size.
		 *
		 * @todo deprecate
		 */
		protected function modify_element_font_size( $custom_font_size = '', $classes = [] ) {
			if ( empty( $classes ) ) {
				return $classes;
			}

			$custom_font_size = wpex_sanitize_utl_font_size( $custom_font_size );

			if ( ! $custom_font_size ) {
				return $classes;
			}

			$get_font_sizes = wpex_utl_font_sizes();
			$font_sizes = [];
			$custom_size_added = false;

			if ( $get_font_sizes ) {
				foreach ( $get_font_sizes as $key => $value ) {
					if ( $key ) {
						$font_sizes[$key] = "wpex-text-{$key}";
					}
				}
				foreach ( $classes as $key => $val ) {
					if ( in_array( $val, $font_sizes ) ) {
						$classes[$key] = $custom_font_size;
						$custom_size_added = true;
						break; // no need to check multiple, each element should only have 1 font size defined.
					}
				}
				if ( ! $custom_size_added ) {
					$classes[] = $custom_font_size;
				}
			}

			return $classes;
		}

		/**
		 * Modifies an element width.
		 *
		 * @todo update to use preg_match
		 */
		protected function modify_element_width( $custom_width = '', $classes = [] ) {
			if ( empty( $classes ) || empty( $custom_width ) ) {
				return $classes;
			}

			$custom_width_safe = absint( $custom_width );

			if ( $custom_width_safe ) {

				$widths = wpex_utl_percent_widths();

				if ( $widths ) {

					$breakpoint = $this->get_breakpoint();

					// Loop through element classes
					foreach ( $classes as $key => $val ) {
						if ( 'wpex-w-100' === $val ) {
							continue; // don't modify elements intended to be 100% wide
						}

						// Alter width classes
						$class = str_replace( 'wpex-w-', '', $val );
						if ( ! empty( $class ) && array_key_exists( $class, $widths ) ) {
							$classes[ $key ] = "wpex-w-{$custom_width_safe}";
							continue;
						}

						// Alter responsive width classes
						if ( $breakpoint ) {
							$bk_class = str_replace( 'wpex-' . $breakpoint . '-w-', '', $val );
							if ( ! empty( $bk_class ) && array_key_exists( $bk_class, $widths ) ) {
								$classes[ $key ] = "wpex-{$breakpoint}-w-{$custom_width_safe}";
							}
						}

					}

				}

			}

			return $classes;
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Media ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card media.
		 */
		public function get_media( $args = [] ) {
			$default_args = [
				'class'          => '',
				'before'         => '',
				'after'          => '',
				'link'           => true,
				'overlay'        => true, // overlays can be enabled to check for overlay style or a string.
				'class'          => '',
				'css'            => '',
				'image_class'    => '',
				'image_size'     => '',
				'thumbnail_args' => [],
			];

			/**
			 * Filters the card media element arguments.
			 *
			 * @param array $args The arguments.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_media_args', wp_parse_args( $args, $default_args ), $this );

			if ( ! empty( $args['image_class'] ) ) {
				$args['thumbnail_args']['image_class'] = $args['image_class'];
			}

			if ( ! empty( $args['image_size'] ) ) {
				$args['thumbnail_args']['size'] = $args['image_size'];
			}

			if ( false === $args['link'] ) {
				$args['thumbnail_args']['link'] = false;
			}

			if ( false === $args['overlay'] ) {
				$args['thumbnail_args']['overlay'] = false;
			}

			if ( isset( $args['strip_overlay_links'] ) && true === $args['strip_overlay_links'] ) {
				$args['thumbnail_args']['strip_overlay_links'] = true;
			}

			$media_type = $this->get_var( 'media_type' );

			switch ( $media_type ) {
				case 'video':
					$media = $this->get_video();
					break;
				case 'audio':
					$media = $this->get_audio();
					break;
				case 'gallery':
					$media = $this->get_gallery_slider( [
						'thumbnail_args' => $args['thumbnail_args'],
					] );
					break;
				case 'thumbnail':
					$media = $this->get_thumbnail( $args['thumbnail_args'] );
					break;
				default:
					$media = '';
					break;
			}

			/**
			 * Filters the card media html.
			 *
			 * @param string $media The card media html.
			 * @param object $this Current WPEX_Card object.
			 * @param array $args The media element arguments.
			 */
			$media = (string) apply_filters( 'wpex_card_media', $media, $this, $args );

			if ( empty( $media ) ) {
				return;
			}

			$args['name'] = 'media';
			$class = $this->parse_element_class( $args );
			$el_css = $this->parse_element_css( $args );

			// Important - this is a wrapper element, don't use $this->get_element
			$output = $args['before'];

				$output .= wpex_parse_html( 'div', [
					'class' => $class,
					'style' => $el_css,
				], $media );

			$output .= $args['after'];

			return $output;
		}

		/**
		 * Card allowed media types.
		 */
		public function get_allowed_media() {
			if ( array_key_exists( 'allowed_media', $this->args ) ) {
				$allowed_media = (array) $this->args['allowed_media'];
			} else {
				$allowed_media = [ 'thumbnail' ];
			}

			if ( ! empty( $this->args['display_video'] ) && true === $this->args['display_video'] ) {
				$allowed_media[] = 'video';
			}
			if ( ! empty( $this->args['display_audio'] ) && true === $this->args['display_audio'] ) {
				$allowed_media[] = 'audio';
			}

			if ( ! empty( $this->args['display_gallery'] ) && true === $this->args['display_gallery'] ) {
				$allowed_media[] = 'gallery';
			}

			if ( $this->post_id && post_password_required( $this->post_id ) ) {
				$allowed_media = array_combine( $allowed_media, $allowed_media );
				unset( $allowed_media['video'] );
				unset( $allowed_media['audio'] );
			}

			/**
			 * Filters the card allowed media types.
			 *
			 * @param array $allowed_media The media types.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->allowed_media = (array) apply_filters( 'wpex_card_allowed_media', $allowed_media, $this );

			return array_filter( $this->allowed_media );
		}

		/**
		 * Card media type.
		 */
		public function get_media_type() {
			$type = '';

			$allowed_media = $this->get_var( 'allowed_media' );

			if ( $this->post_id ) {
				if ( in_array( 'video', $allowed_media ) && $this->get_var( 'post_video' ) ) {
					$type = 'video';
				} elseif ( in_array( 'audio', $allowed_media ) && wpex_has_post_audio( $this->post_id ) ) {
					$type = 'audio';
				} elseif ( in_array( 'gallery', $allowed_media ) && wpex_has_post_gallery( $this->post_id ) ) {
					$type = 'gallery';
				} elseif ( in_array( 'thumbnail', $allowed_media ) ) {
					$type = 'thumbnail'; // don't bother with the has_post_thumbnail check.
				}
			} elseif ( $this->term ) {
				$type = 'thumbnail';
			}

			/**
			 * Filters the card media type
			 *
			 * @param string $type The media type.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->media_type = (string) apply_filters( 'wpex_card_media_type', $type, $this );

			return $this->media_type;
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Thumbnail ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card thumbnail ID.
		 */
		public function get_thumbnail_id() {
			$attachment = '';

			if ( ! empty( $this->args['thumbnail_id'] ) ) {
				$attachment = $this->args['thumbnail_id'];
			} elseif ( $this->post_id ) {
				$attachment = absint( $this->get_card_meta( 'thumbnail' ) );
				if ( empty( $attachment ) ) {
					if ( 'attachment' === $this->get_post_type() ) {
						$attachment = $this->post_id;
					} else {
						$attachment = get_post_thumbnail_id( $this->post_id );
					}
				}
			} elseif( $this->term ) {
				$attachment = wpex_get_term_thumbnail_id( $this->term );
			}

			/**
			 * Filters the card thumbnail id.
			 *
			 * @param string|int $attachment The thumbnail attachment id.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->thumbnail_id = apply_filters( 'wpex_card_thumbnail_id', $attachment, $this );

			return $this->thumbnail_id;
		}

		/**
		 * Get card thumbnail url.
		 */
		public function get_thumbnail_url( $size = '' ) {
			$attachment = $this->get_var( 'thumbnail_id' );

			if ( ! $attachment ) {
				return false;
			}

			$thumbnail_url = '';

			if ( ! $size ) {
				$size = $this->get_var( 'thumbnail_size', $size );
			}

			$thumbnail_args = array(
				'attachment' => $attachment,
			);

			if ( is_array( $size ) ) {
				$thumbnail_args['width']  = $size[0] ?? '';
				$thumbnail_args['height'] = $size[1] ?? '';
				$thumbnail_args['crop']   = $size[2] ?? '';
			} else {
				$thumbnail_args['size'] = $size;
			}

			if ( function_exists( 'wpex_get_post_thumbnail_url' ) ) {
				$thumbnail_url = wpex_get_post_thumbnail_url( $thumbnail_args );
			} else {
				$thumbnail_url = wp_get_attachment_url( $attachment );
			}

			/**
			 * Filters the card thumbnail url.
			 *
			 * @param string $thumbnail_url The thumbnail url.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->thumbnail_url = (string) apply_filters( 'wpex_card_thumbnail_url', $thumbnail_url, $this );

			return $this->thumbnail_url;
		}

		/**
		 * Get card thumbnail.
		 */
		public function get_thumbnail( $args = [] ) {
			$allowed_media = $this->get_var( 'allowed_media' );

			if ( ! is_array( $allowed_media ) || ! in_array( 'thumbnail', $allowed_media ) ) {
				return;
			}

			$default_args = [
				'overlay'             => true,
				'link'                => true,
				'class'               => '',
				'image_class'         => '',
				'size'                => 'full',
				'strip_overlay_links' => false,
			];

			$args = (array) apply_filters( 'wpex_card_thumbnail_args', wp_parse_args( $args, $default_args ), $this );

			$size = $this->get_var( 'thumbnail_size', $args['size'] );
			$alt  = $this->get_thumbnail_alt();

			$attachment = $this->get_var( 'thumbnail_id' );

			if ( empty( $attachment ) ) {
				return;
			}

			$image_class = 'wpex-align-middle';

			if ( ! empty( $args['image_class'] ) && is_string( $args['image_class'] ) ) {
				$image_class .= ' ' . trim( $args['image_class'] );
			}

			if ( function_exists( 'wpex_get_post_thumbnail' ) ) {
				$thumbnail_args = [
					'attachment' => $attachment,
					'alt'        => $alt,
					'class'      => $image_class,
				];

				if ( isset( $args['lazy'] ) || isset( $this->args['thumbnail_lazy'] ) ) {
					$thumbnail_args['lazy'] = $args['lazy'] ?? $this->args['thumbnail_lazy'];
				}

				if ( is_array( $size ) ) {
					$thumbnail_args['width']  = $size[0] ?? '';
					$thumbnail_args['height'] = $size[1] ?? '';
					$thumbnail_args['crop']   = $size[2] ?? '';
				} else {
					$thumbnail_args['size']   = $size;
				}

				$thumbnail = wpex_get_post_thumbnail( $thumbnail_args );
			} else {
				$thumbnail = wp_get_attachment_image( $attachment, $size, array(
					'alt'   => $alt,
					'class' => $image_class,
				) );
			}

			/**
			 * Filters the card thumbnail html.
			 *
			 * @param string $thumbnail
			 * @param object $this WPEX_Card object
			 * @param array $args
			 */
			$thumbnail = apply_filters( 'wpex_card_thumbnail', $thumbnail, $this, $args );

			if ( empty( $thumbnail ) ) {
				return;
			}

			if ( $this->post_id
				&& function_exists( 'wpex_get_entry_media_after' )
				&& 'post' === $this->get_post_type()
			) {
				$thumbnail .= wpex_get_entry_media_after( 'card' );
			}

			$args['name']             = 'thumbnail';
			$args['content']          = $thumbnail;
			$args['sanitize_content'] = false;

			if ( ! empty( $args['overlay'] ) ) {
				if ( is_string( $args['overlay'] ) ) {
					$args['overlay'] = $args['overlay'];
				} else {
					$args['overlay'] = $this->get_var( 'thumbnail_overlay_style' );
				}
			}

			return $this->get_element( $args );
		}

		/**
		 * Get card thumbnail alt.
		 */
		public function get_thumbnail_alt() {
			if ( isset( $this->thumbnail_alt ) ) {
				return $this->thumbnail_alt;
			}
			$attachment = $this->get_var( 'thumbnail_id' );
			if ( ! $attachment ) {
				return;
			}
			$alt = (string) get_post_meta( $attachment, '_wp_attachment_image_alt', true );
			if ( ! $alt ) {
				if ( ! empty( $this->args['title'] ) ) {
					$alt = (string) $this->args['title'];
				} elseif ( $this->post_id ) {
					$alt = (string) get_the_title( $this->post_id );
				} elseif ( ! empty( $this->term->name ) ) {
					$alt = $this->term->name;
				}
			}
			if ( $alt ) {
				return sanitize_text_field( $alt );
			}
		}

		/**
		 * Get card thumbnail size.
		 */
		public function get_thumbnail_size( $default_size = '' ) {
			$size = '';

			if ( isset( $this->args['thumbnail_size'] ) ) {
				$size = $this->args['thumbnail_size'];
			} else {
				$size = $default_size;
			}

			$this->thumbnail_size = apply_filters( 'wpex_card_thumbnail_size', $size, $this );

			return $this->thumbnail_size;

		}

		/**
		 * Get card thumbnail hover.
		 */
		public function get_thumbnail_hover() {
			$hover = '';

			if ( isset( $this->args['thumbnail_hover'] ) ) {
				$hover = (string) $this->args['thumbnail_hover'];
			}

			/**
			 * Filters the thumbnail hover style.
			 *
			 * @param string $hover The hover style name.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->thumbnail_hover = (string) apply_filters( 'wpex_card_thumbnail_hover', $hover, $this );

			return $this->thumbnail_hover;
		}

		/**
		 * Get card thumbnail filter.
		 */
		public function get_thumbnail_filter() {
			$filter = '';

			if ( isset( $this->args['thumbnail_filter'] ) ) {
				$filter = $this->args['thumbnail_filter'];
			}

			/**
			 * Filters the thumbnail filter style.
			 *
			 * @param string $filter The filter style name.
			 * @param object $this Current WPEX_Card object.
			 */
			$this->thumbnail_filter = apply_filters( 'wpex_card_thumbnail_size', $filter, $this );

			return $this->thumbnail_filter;
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Overlay ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card thumbnail overlay.
		 */
		public function get_thumbnail_overlay_style(): string {
			$style = '';

			if ( ! empty( $this->args['thumbnail_overlay_style'] ) ) {
				$style = $this->args['thumbnail_overlay_style'];
			} elseif ( $this->post_id ) {
				$style = $this->get_card_meta( 'thumbnail_overlay_style', true );
			}

			/**
			 * Filters the thumbnail overlay style.
			 *
			 * @param string $style The overlay style name.
			 * @param object $this Current WPEX_Card object.
			 */
			$style = (string) apply_filters( 'wpex_card_thumbnail_overlay_style', $style, $this );

			if ( 'none' === $style ) {
				$style = '';
			}

			$this->thumbnail_overlay_style = $style;

			return $this->thumbnail_overlay_style;
		}

		/**
		 * Check if a card has an overlay.
		 */
		public function has_thumbnail_overlay(): bool {
			$overlay = $this->get_var( 'thumbnail_overlay_style' );
			return ( $overlay && 'none' !== $overlay && function_exists( 'wpex_overlay' ) );
		}

		/**
		 * Get card overlay.
		 */
		public function get_overlay( $style = '', $position = 'inside', $strip_links = false ) {
			if ( empty( $style ) || ! function_exists( 'wpex_overlay' ) ) {
				return;
			}

			$args = [];

			if ( ! empty( $this->args['thumbnail_overlay_button_text'] ) ) {
				$args['overlay_button_text'] = $this->args['thumbnail_overlay_button_text'];
			}

			$link_type = $this->get_var( 'link_type' );

			switch ( $link_type ) {
				case 'modal':
				case 'lightbox':
					$args['lightbox_class'] = $this->get_var( 'lightbox_class' );
					$args['lightbox_link']  = $this->get_var( 'url' );
					$lightbox_data = $this->get_var( 'lightbox_data' );
					if ( ! empty( $lightbox_data ) && is_array( $lightbox_data ) ) {
						$args['lightbox_data'] = '';
						foreach ( $lightbox_data as $data_key => $data_val ) {
							$args['lightbox_data'] .= 'data-' . sanitize_key( $data_key ) . '="' . esc_attr( $data_val ) . '" ';
						}
						$args['lightbox_data'] = trim( $args['lightbox_data'] );
					}
					break;
				default:
					if ( 'dialog' !== $link_type ) {
						$args['post_permalink'] = $this->get_var( 'url' );
					}
					if ( ! empty( $this->args['link_target'] ) ) {
						$args['link_target'] = $this->args['link_target'];
					}
					break;
			}

			if ( $this->term ) {
				$args['post_title'] = $this->term->name ?? '';
				$args['taxonomy'] = '';
				if ( empty( $args['overlay_button_text'] ) ) {
					$args['overlay_button_text'] = esc_html__( 'View Archive', 'total' );
				}
			}

			ob_start();
				totaltheme_render_overlay( $position, $style, $args );
			$html = ob_get_clean();

			if ( $strip_links ) {
				$html = str_replace( '<a', '<span', $html );
				$html = str_replace( '</a>', '</span>', $html );
			}

			return $html;
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Gallery Slider ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card gallery slider.
		 */
		public function get_gallery_slider( $args = [] ) {
			if ( ! $this->post_id || ! function_exists( 'wpex_get_post_media_gallery_slider' ) ) {
				return;
			}

			$default_args = [
				'before'         => '',
				'after'          => '',
				'thumbnail_args' => [],
				'slider_args'    => [
					'lightbox'       => false,
					'captions'       => false,
					'slider_data'    => [
						'thumbnails' => 'false',
						'buttons'    => 'true',
						'fade'       => 'true',
					],
				],
			];

			$args = wp_parse_args( $args, $default_args );

			extract( $args );

			if ( empty( $thumbnail_args['size'] ) ) {

				$thumb_size = $this->thumbnail_size ?? 'full';

				if ( is_array( $thumb_size ) ) {
					$thumbnail_args['width']  = $thumb_size[0] ?? '';
					$thumbnail_args['height'] = $thumb_size[1] ?? '';
					$thumbnail_args['crop']   = $thumb_size[2] ?? '';
				} else {
					$thumbnail_args['size']   = $thumb_size;
				}

			}

			$slider_args['thumbnail_args'] = $thumbnail_args;

			$slider = wpex_get_post_media_gallery_slider( $this->post_id, $slider_args );

			/**
			 * Filters the card gallery slider html.
			 *
			 * @param string $html The slider html.
			 * @param object $this Current WPEX_Card object.
			 */
			$slider = (string) apply_filters( 'wpex_card_gallery_slider', $slider, $this, $args );

			if ( $slider ) {
				return $before . $slider . $after;
			}
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Video ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card video.
		 */
		public function get_video( $args = [] ) {
			$default_args = [
				'class' => '',
			];

			$args = wp_parse_args( $args, $default_args );

			$video = $this->get_post_video_html();

			/**
			 * Filters the card video html.
			 *
			 * @param string $html The video html.
			 * @param object $this Current WPEX_Card object.
			 */
			$video = (string) apply_filters( 'wpex_card_video', $video, $this, $args );

			if ( empty( $video ) ) {
				return;
			}

			$args['name']             = 'video';
			$args['content']          = $video;
			$args['sanitize_content'] = false;
			$args['link']             = false;

			return $this->get_element( $args );
		}

		/**
		 * Get post video.
		 */
		public function get_post_video() {
			if ( ! $this->post_id ) {
				return;
			}

			$this->post_video = wpex_get_post_video( $this->post_id );

			return $this->post_video;
		}

		/**
		 * Get post video html.
		 */
		public function get_post_video_html(): string {
			return ( $video = $this->get_var( 'post_video' ) ) ? (string) wpex_get_post_video_html( $video ) : '';
		}

		/**
		 * Check if a card has a video.
		 */
		public function has_video(): bool {
			return (bool) $this->get_var( 'post_video' );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Audio ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card audio.
		 */
		public function get_audio( $args = [] ) {
			$default_args = array(
				'class' => '',
			);

			$args = wp_parse_args( $args, $default_args );

			if ( $this->post_id ) {
				$audio = wpex_get_post_audio( $this->post_id );
				if ( $audio ) {
					$audio = wpex_get_post_audio_html( $audio );
				}
			}

			/**
			 * Filters the card audio html.
			 *
			 * @param string $html The audio html.
			 * @param object $this Current WPEX_Card object.
			 */
			$audio = (string) apply_filters( 'wpex_card_audio', $audio, $this, $args );

			if ( empty( $audio ) ) {
				return;
			}

			$args['name']             = 'audio';
			$args['content']          = $audio;
			$args['sanitize_content'] = false;
			$args['link']             = false;

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Title ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Returns the title text.
		 */
		public function get_the_title( $args = [] ) {
			$title = '';

			if ( ! empty( $this->args['title'] ) ) {
				$title = $this->args['title'];
			} elseif ( ! empty( $args['content'] ) ) {
				$title = $args['content'];
			} elseif ( $this->post_id ) {
				$title = get_the_title( $this->post_id );
			} elseif ( ! empty( $this->term->name ) ) {
				$title = $this->term->name;
			}

			/**
			 * Filters the card title.
			 *
			 * @param string $title The title.
			 * @param object $this Current WPEX_Card object.
			 */
			$title = (string) apply_filters( 'wpex_card_title', $title, $this, $args );

			return $title;
		}

		/**
		 * Returns the card title element.
		 */
		public function get_title( $args = [] ) {
			$default_args = [
				'content'    => '',
				'link'       => true,
				'class'      => '',
				'show_count' => false,
			];

			/**
			 * Filters the card title element arguments.
			 *
			 * @param array $args The arguments.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_title_args', wp_parse_args( $args, $default_args ), $this );

			$title = $this->get_the_title( $args );

			if ( empty( $title ) ) {
				return;
			}

			if ( true === $args['show_count'] ) {
				$running_count = $this->get_var( 'running_count' );
				$title = "{$running_count}. {$title}";
			}

			$args['name']    = 'title';
			$args['content'] = $title;

			return $this->get_element( $args );
		}

		/**
		 * Get card title tag.
		 */
		public function get_title_tag() {
			if ( ! empty( $this->args['title_tag'] ) ) {
				$tag = $this->args['title_tag'];
			} else {
				$tag = ( 'related' === wpex_get_loop_instance() ) ? 'h4' : 'h2';
			}

			/**
			 * Filters the card title html tag.
			 *
			 * @param string $tag The title tag.
			 * @param object $this Current WPEX_Card object.
			 */
			$tag = (string) apply_filters( 'wpex_card_title_tag', $tag, $this );

			$this->title_tag = tag_escape( $tag );

			return $this->title_tag;
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Subheading ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Returns the title text.
		 */
		public function get_the_subheading( array $args ): string {
			$subheading = '';

			if ( ! empty( $this->args['subheading'] ) ) {
				$subheading = $this->args['subheading'];
			} elseif ( ! empty( $args['content'] ) ) {
				$subheading = $args['content'];
			} elseif ( $this->post_id ) {
				$post_type = $this->get_post_type();

				switch ( $post_type ) {
					case 'staff':
						$subheading = wpex_get_staff_member_position( $this->post_id );
						break;
					case 'portfolio':
						$subheading = get_post_meta( $this->post_id, 'wpex_portfolio_company', true );
						break;
				}
				if ( $meta = get_post_meta( $this->post_id, 'wpex_post_subheading', true ) ) {
					$subheading = $meta;
				}
				if ( ! $subheading ) {
					$subheading = get_post_meta( $this->post_id, 'subheading', true );
				}
			}

			/**
			 * Filters the card sub heading text.
			 *
			 * @param string $subheading The subheading.
			 * @param object $this Current WPEX_Card object.
			 */
			$subheading = (string) apply_filters( 'wpex_card_subheading', $subheading, $this, $args );

			return $subheading;
		}

		/**
		 * Returns the card subheading.
		 */
		public function get_subheading( $args = [] ) {
			$default_args = [
				'content'    => '',
				'class'      => '',
			];

			/**
			 * Filters the card sub heading args.
			 *
			 * @param array $args The subheading args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_subheading_args', wp_parse_args( $args, $default_args ), $this );

			$subheading = $this->get_the_subheading( $args );

			if ( empty( $subheading ) ) {
				return;
			}

			$args['name']    = 'subheading';
			$args['content'] = $subheading;

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Excerpt ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Returns the default excerpt length.
		 */
		protected function get_default_excerpt_length(): int {
			return $this->is_featured() ? 40 : 20;
		}

		/**
		 * Get card excerpt.
		 */
		public function get_the_excerpt( $args = [] ) {
			$excerpt = '';

			if ( isset( $this->args['excerpt_length'] ) ) {
				$excerpt_length = $this->args['excerpt_length'];
			} else {
				$excerpt_length = $args['length'] ?? $this->get_default_excerpt_length();
			}

			$excerpt = '';

			if ( $excerpt_length && '0' !== $excerpt_length ) {
				if ( ! empty( $this->args['excerpt'] ) ) {
					$excerpt = $this->args['excerpt'];
				} elseif ( $this->post_id ) {
					$excerpt = get_post_meta( $this->post_id, 'wpex_card_excerpt', true );
					if ( empty( $excerpt ) ) {
						if ( function_exists( 'totaltheme_get_post_excerpt' ) ) {
							$excerpt_args = apply_filters( 'wpex_card_excerpt_args', [
								'post_id' => $this->post_id,
								'length'  => $excerpt_length,
							], $this );
							$excerpt = totaltheme_get_post_excerpt( $excerpt_args );
						} else {
							$excerpt = get_the_excerpt( $this->post_id );
						}
					}
				} elseif ( ! empty( $this->term->description ) ) {
					$excerpt = $this->term->description;
				}
			}

			/**
			 * Filters the card excerpt.
			 *
			 * @param string $excerpt The excerpt.
			 * @param object $this Current WPEX_Card object.
			 */
			$excerpt = (string) apply_filters( 'wpex_card_excerpt', $excerpt, $this, $args );

			return $excerpt;
		}

		/**
		 * Get card excerpt.
		 */
		public function get_excerpt( $args = [] ) {
			$default_args = [
				'link'   => false, // disabled by default but it's possible to add.
				'class'  => '',
				'length' => $this->get_default_excerpt_length(),
			];

			/**
			 * Filters the card excerpt args.
			 *
			 * @param array $args The excerpt args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_excerpt_args', wp_parse_args( $args, $default_args ), $this );

			$excerpt = $this->get_the_excerpt( $args );

			if ( empty( $excerpt ) ) {
				return;
			}

			if ( isset( $this->args['excerpt_length'] ) ) {
				$args['length'] = $this->args['excerpt_length'];
			}

			$args['name'] = 'excerpt';
			$args['content'] = $excerpt;

			if ( '-1' === $args['length'] || '9999' === $args['length'] ) {
				$args['sanitize_content'] = false; // important because the content has already been sanitized and if we do it again things get stripped out such as <style> tags and we are also running do_shortcode twice.
			}

			unset( $args['length'] );

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ More Link ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card more link.
		 */
		public function get_more_link( $args = [] ) {
			if ( isset( $this->args['excerpt_length'] ) && '-1' == $this->args['excerpt_length'] ) {
				return;
			}

			$custom_more_link = apply_filters( 'wpex_card_more_link_url', null, $this );

			if ( ! $this->has_link() && ! $custom_more_link ) {
				return;
			}

			$default_args = [
				'class'      => '',
				'text'       => esc_html__( 'Read more', 'total' ),
				'link'       => true,
				'link_class' => '',
			];

			/**
			 * Filters the card more link args.
			 *
			 * @param array $args The more link args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_more_link_args', wp_parse_args( $args, $default_args ), $this );

			if ( isset( $this->args['more_link_text'] ) ) {
				$args['text'] = $this->args['more_link_text'];
			}

			$more_link_text = (string) apply_filters( 'wpex_card_more_link_text', $args['text'], $this, $args );

			if ( ! empty( $more_link_text ) && '0' !== $more_link_text ) {

				$args['name'] = 'more-link';

				if ( $custom_more_link ) {
					$args['link'] = $custom_more_link;
				}

				$args['content'] = $more_link_text;

				if ( ! empty( $this->args['title'] ) ) {
					$escaped_post_title = esc_attr( $this->args['title'] );
				} else {
					$escaped_post_title = the_title_attribute( array(
						'echo' => false,
						'post' => get_post( $this->post_id ),
					) );
				}

				if ( ! empty( $this->args['more_link_aria_label'] ) ) {
					$aria_label = $this->args['more_link_aria_label'];
				} else {
					$aria_label = sprintf( esc_attr_x( '%s about %s', '*read more text* about *post name* aria label', 'total' ), esc_attr( $more_link_text ), $escaped_post_title );
					// @todo deprecate.
					$aria_label = (string) apply_filters( 'wpex_aria_label', $aria_label, 'more_link' );
				}
				$aria_label = (string) apply_filters( 'wpex_card_more_link_aria_label', $aria_label, $this, $args );
				if ( $aria_label ) {
					$args['link_attributes'] = array(
						'aria-label' => strip_shortcodes( $aria_label ),
					);
				}

				return $this->get_element( $args );
			}
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Date ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card date.
		 */
		public function get_date( $args = [] ) {
			$default_args = [
				'link'   => false,
				'type'   => 'published',
				'format' => '',
			];

			/**
			 * Filters the card date args.
			 *
			 * @param array $args The date args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_date_args', wp_parse_args( $args, $default_args ), $this );

			$date = '';

			if ( isset( $this->args['date'] ) ) {
				$date = $this->args['date'];
			} elseif ( $this->post_id ) {
				$format = $this->args['date_format'] ?? $args['format'];

				if ( 'null' === $format ) {
					return; // allows the date to be disabled via the Post Cards format field.
				}

				switch ( $this->get_post_type() ) {
					case 'tribe_events':
						if ( function_exists( 'tribe_get_start_date' ) ) {
							$date = tribe_get_start_date( $this->post_id, false, $format );
						}
						break;
					case 'just_event':
						if ( str_starts_with( $this->get_var( 'thumbnail_overlay_style' ), 'just-events-date' ) ) {
							if ( function_exists( 'Just_Events\get_event_formatted_time' ) ) {
								$date = Just_Events\get_event_formatted_time( $this->post_id );
							}		
						} else {
							if ( function_exists( 'Just_Events\get_event_formatted_date' ) ) {
								$je_args = [
									'start_end' => $args['start_end'] ?? 'both',
									'show_time' => $args['show_time'] ?? false,
									'format'    => $format,
								];
								if ( isset( $args['separator'] ) ) {
									$je_args['separator'] = $args['separator'];
								}
								$date = Just_Events\get_event_formatted_date( $this->post_id, $je_args );
							}
						}
						break;
				}

				if ( ! $date ) {
					switch ( $args['type'] ) {
						case 'time_ago':
							$date = human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' '. esc_html__( 'ago', 'total' );
							break;
						case 'modified':
							$date = get_the_modified_date( $format, $this->post_id );
							break;
						default:
							$date = get_the_date( $format, $this->post_id );
							break;
					}
				}
			}

			/**
			 * Filters the card date.
			 *
			 * @param string $date The date.
			 * @param object $this Current WPEX_Card object.
			 */
			$date = (string) apply_filters( 'wpex_card_date', $date, $this, $args );

			if ( empty( $date ) ) {
				return;
			}

			$args['name'] = 'date';
			$args['content'] = $date;

			unset( $args['type'] );
			unset( $args['format'] );

			return $this->get_element( $args );
		}

		/**
		 * Get card time.
		 */
		public function get_time( $args = [] ) {
			$default_args = array(
				'link'   => false,
				'type'   => 'published',
				'format' => '',
			);

			/**
			 * Filters the card time args.
			 *
			 * @param array $args The time args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_time_args', wp_parse_args( $args, $default_args ), $this );

			$time = '';

			if ( isset( $this->args['time'] ) ) {
				$time = $this->args['time'];
			} elseif ( $this->post_id ) {
				$format = $this->args['date_format'] ?? $args['format'];
				switch ( $args['type'] ) {
					case 'modified':
						$time = get_the_modified_time( $format, $this->post_id );
						break;
					default:
						$time = get_the_time( $format, $this->post_id );
						break;
				}
				if ( 'tribe_events' === $this->get_post_type() ) {
					$time = '';
				}
			}

			/**
			 * Filters the card time.
			 *
			 * @param string $time The time.
			 * @param object $this Current WPEX_Card object.
			 */
			$time = (string) apply_filters( 'wpex_card_time', $time, $this, $args );

			if ( empty( $time ) ) {
				return;
			}

			$args['name']    = 'time';
			$args['content'] = $time;

			unset( $args['type'] );
			unset( $args['format'] );

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Read Time ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card read time.
		 */
		public function get_estimated_read_time( $args = [] ) {
			if ( $this->post_id ) {
				$args['content'] = totaltheme_get_post_estimated_read_time( $this->post_id, [], $this );
			} elseif ( ! empty( $this->term->description ) ) {
				$args['content'] = totaltheme_get_estimatd_read_time( $this->term->description, [], $this );
			} else {
				return;
			}

			if ( ! $args['content'] ) {
				return;
			}

			$args['name'] = 'estimated-read-time';

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Author ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card author.
		 */
		public function get_author( $args = [] ) {
			$default_args = array(
				'link'       => true,
				'link_class' => '',
				'class'      => '',
			);

			/**
			 * Filters the card author args.
			 *
			 * @param array $args The author args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_author_args', wp_parse_args( $args, $default_args ), $this );

			$author = '';

			if ( isset( $this->args['author'] ) ) {
				$author = $this->args['author'];
			} elseif ( $this->post_id ) {

				$post = get_post( $this->post_id );

				$authordata = get_userdata( $post->post_author );

				$the_author = apply_filters( 'the_author', is_object( $authordata ) ? $authordata->display_name : null );

				if ( $the_author ) {

					if ( true === $args['link'] ) {
						$author_posts_url = get_author_posts_url( $post->post_author );
					}

					if ( ! empty( $author_posts_url ) ) {

						$link_attrs = [
							'href'  => esc_url( $author_posts_url ),
							'class' => $args['link_class'],
						];

						$author .= '<a ' . wpex_parse_attrs( $link_attrs ) . '>';
					}

					$author .= esc_html( ucwords( $the_author ) );

					if ( ! empty( $author_posts_url ) ) {
						$author .= '</a>';
					}

				}

			}

			/**
			 * Filters the card author.
			 *
			 * @param string $author The author name.
			 * @param object $this Current WPEX_Card object.
			 */
			$author = (string) apply_filters( 'wpex_card_author', $author, $this, $args );

			if ( empty( $author ) ) {
				return;
			}

			$args['name']             = 'author';
			$args['content']          = $author;
			$args['sanitize_content'] = false;
			$args['link']             = false; // important!!

			unset( $args['link_class'] );

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Terms ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card terms list.
		 */
		public function get_terms_list( $args = [] ) {
			$default_args = [
				'class'                     => '',
				'term_class'                => '',
				'term_prefix'               => '',
				'separator'                 => ' ',
				'taxonomy'                  => '',
				'link'                      => true,
				'has_term_color'            => false,
				'has_term_background_color' => false,
			];

			/**
			 * Filters the card terms list args.
			 *
			 * @param array $args The terms list args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_terms_list_args', wp_parse_args( $args, $default_args ), $this );

			if ( empty( $args['taxonomy'] ) ) {
				if ( $this->term ) {
					$args['taxonomy'] = $this->term->taxonomy ?? '';
				} else {
					if ( isset( $this->post_id ) && function_exists( 'wpex_get_post_primary_taxonomy' ) ) {
						$args['taxonomy'] = wpex_get_post_primary_taxonomy( $this->post_id );
					} else {
						$args['taxonomy'] = 'category';
					}
				}
			}

			$terms = $this->get_terms( $args );

			if ( empty( $terms ) ) {
				return false;
			}

			$items    = [];
			$has_link = wpex_validate_boolean( $args['link'] );

			foreach ( $terms as $term ) {

				$term_class = $args['term_class'];

				if ( $args['has_term_color'] && $term_color_clasname = totaltheme_get_term_color_classname( $term ) ) {
					$term_class .= " {$term_color_clasname}";
				}

				if ( $args['has_term_background_color'] && $term_color_background_classname = totaltheme_get_term_color_background_classname( $term ) ) {
					$term_class .= " {$term_color_background_classname}";
				}

				if ( $has_link && is_taxonomy_viewable( $args['taxonomy'] ) ) {
					$term_link = get_term_link( $term );
					if ( is_wp_error( $term_link ) ) {
						unset( $term_link );
					}
				}

				if ( $has_link && ! empty( $term_link ) ) {

					$item = '<a href="' . esc_url( $term_link ) . '"';
						if ( $term_class ) {
							$item .= ' class="' . esc_attr( trim( $term_class ) ) . '"';
						}
					$item .= '>';
					if ( $args['term_prefix'] ) {
						$item .= wp_kses_post( $args['term_prefix'] );
					}
					$item .= esc_html( $term->name ) . '</a>';
					$items[] = $item;

				} else {
					$item = '<span';
						if ( $term_class ) {
							// Fix for cards that show links but they have been disabled.
							$term_class = str_replace( 'wpex-underline', '', $term_class );
							$term_class = str_replace( 'wpex-hover-underline', '', $term_class );
							$item .= ' class="' . esc_attr( trim( $term_class ) ) . '"';
						}
						$item .= '>';
						if ( $args['term_prefix'] ) {
							$item .= wp_kses_post( $args['term_prefix'] );
						}
						$item .= esc_html( $term->name ) . '</span>';
					$items[] = $item;
				}

			}

			/**
			 * Filters the card terms list html.
			 *
			 * @param string $terms_list The terms list html.
			 * @param object $this Current WPEX_Card object.
			 */
			$terms_list = (string) apply_filters( 'wpex_card_terms_list', join( $args['separator'], $items ), $this, $args );

			if ( empty( $terms_list ) ) {
				return;
			}

			$args['name']             = 'terms-list';
			$args['content']          = $terms_list;
			$args['sanitize_content'] = false;
			$args['link']             = false; // important!!!

			unset( $args['term_class'] );
			unset( $args['separator'] );
			unset( $args['taxonomy'] );

			return $this->get_element( $args );
		}

		/**
		 * Get card terms.
		 */
		public function get_terms( $args = [] ) {
			$default_args = [
				'taxonomy' => '',
			];

			$args = wp_parse_args( $args, $default_args );

			$terms = [];

			if ( $this->post_id ) {
				if ( empty( $args['taxonomy'] ) ) {
					if ( function_exists( 'wpex_get_post_primary_taxonomy' ) ) {
						$args['taxonomy'] = wpex_get_post_primary_taxonomy( $this->post_id );
					} else {
						$args['taxonomy'] = 'category';
					}
				}
				$get_terms = get_the_terms( $this->post_id, $args['taxonomy'] );
			} elseif ( ! empty( $this->term->parent ) ) {
				if ( empty( $args['taxonomy'] ) ) {
					$args['taxonomy'] = $this->term->taxonomy ?? '';
				}
				$get_terms = get_terms( [
					'taxonomy' => $args['taxonomy'],
					'include'  => $this->term->parent,
				] );
			}

			if ( ! empty( $get_terms ) && ! is_wp_error( $get_terms ) ) {
				$terms = $get_terms;
			}

			/**
			 * Filters the card terms.
			 *
			 * @param array $terms The terms list.
			 * @param object $this Current WPEX_Card object.
			 */
			return (array) apply_filters( 'wpex_card_terms', $terms, $this );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Primary Term ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card primary term.
		 */
		public function get_primary_term( $args = [] ) {
			$default_args = [
				'link'                      => true,
				'class'                     => '',
				'term_class'                => '',
				'has_term_color'            => false,
				'has_term_background_color' => false,
				'taxonomy'                  => '',
			];

			/**
			 * Filters the card primary term args.
			 *
			 * @param array $args The args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_primary_term_args', wp_parse_args( $args, $default_args ), $this );

			$primary_term = '';

			if ( isset( $this->args['primary_term'] ) ) {
				$terms = $this->args['primary_term'];
			} else {
				if ( $this->post_id ) {
					$primary_term = totaltheme_get_post_primary_term( $this->post_id, $args['taxonomy'] );
				} elseif ( ! empty( $this->term->parent ) ) {
					$primary_term = get_term( $this->term->parent, $this->term->taxonomy );
				} else {
					$primary_term = false;
				}

				$primary_term = apply_filters( 'wpex_card_primary_term', $primary_term, $this, $args );

				if ( is_object( $primary_term ) && is_a( $primary_term, 'WP_Term' ) ) {

					if ( $args['has_term_color'] && $term_color_clasname = totaltheme_get_term_color_classname( $primary_term ) ) {
						$args['term_class'] .= " {$term_color_clasname}";
					}
	
					if ( $args['has_term_background_color'] && $term_color_background_classname = totaltheme_get_term_color_background_classname( $primary_term ) ) {
						$args['term_class'] .= " {$term_color_background_classname}";
					}

					if ( $args['link'] ) {

						$link_attrs = [
							'href'  => esc_url( get_term_link( $primary_term ) ),
							'class' => $args['term_class'],
						];

						$primary_term_out = '<a ' . wpex_parse_attrs( $link_attrs) . '>' . esc_html( $primary_term->name ) . '</a>';

					} else {

						if ( $args['term_class'] ) {
							$primary_term_out = '<span class="' . esc_attr( trim( $args['term_class'] ) ) . '">' . esc_html( $primary_term->name ) . '</span>';
						} else {
							$primary_term_out = esc_html( $primary_term->name );
						}

					}

				}

			}

			if ( empty( $primary_term_out ) ) {
				return;
			}

			$args['name']             = 'primary-term';
			$args['content']          = $primary_term_out;
			$args['sanitize_content'] = false;
			$args['link']             = false; // important!!

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Icon ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Returns the icon.
		 */
		public function get_the_icon( $args = [], $extra_class = '', $size = '', $bidi = false ) {
			$icon = $args['icon'] ?? '';

			if ( isset( $this->args['icon'] ) ) {
				$icon = $this->args['icon'];
			} elseif ( $this->post_id ) {
				$meta_icon = $this->get_card_meta( 'icon', false );
				if ( ! empty( $meta_icon ) ) {
					$icon = $meta_icon;
				}
			}

			if ( ! empty( $icon ) ) {
				$icon = totaltheme_get_icon( $icon, $extra_class, $size, $bidi );
			}
			
			/**
			 * Filters the card icon html.
			 *
			 * @param string $icon
			 * @param object $card
			 * @param array $args
			 */
			$icon = apply_filters( 'wpex_card_icon', $icon, $this, $args );

			return (string) $icon;
		}

		/**
		 * Get card icon.
		 */
		public function get_icon( $args = [] ) {
			$default_args = [
				'class' => '',
				'icon'  => '',
				'size'  => '',
				'bidi'  => false,
			];

			/**
			 * Filters the card icon args.
			 *
			 * @param array $args The args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_icon_args', wp_parse_args( $args, $default_args ), $this );

			$icon = $this->get_the_icon( $args, '', '', $args['bidi'] );

			if ( empty( $icon ) ) {
				return;
			}

			$args['name']             = 'icon';
			$args['content']          = $icon;
			$args['sanitize_content'] = false;

			unset( $args['icon'] ); // !!! important !!!
			unset( $args['bidi'] );

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Comments ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card comment count.
		 */
		public function get_comment_count( $args = [] ) {
			$default_args = [
				'link'        => true,
				'link_class'  => '',
				'number_only' => false,
				'show_empty'  => false,
			];

			/**
			 * Filters the card comment count arguments.
			 * 
			 * @param array $args The arguments.
			 * @param object $card The card object.
			 */
			$args = (array) apply_filters( 'wpex_card_comment_count_args', wp_parse_args( $args, $default_args ), $this );

			$comment_count = '';

			if ( $this->post_id && comments_open( $this->post_id ) ) {

				$link_class = $args['link_class'] ?? '';

				if ( is_array( $link_class ) ) {
					$link_class = implode( ' ', $link_class );
				}

				if ( $args['link'] ) {
					ob_start();
						if ( $args['number_only'] ) {
							comments_popup_link( '0', '1', '%', $link_class, false );
						} else {
							comments_popup_link( false, false, false, $link_class, false );
						}
					$comment_count = ob_get_clean();
				} else {
					$comment_count = get_comments_number( $this->post_id );
				}

			}

			$comment_count = (string) apply_filters( 'wpex_card_comment_count', $comment_count, $this, $args );

			if ( empty( $comment_count ) && false === $args['show_empty'] ) {
				return;
			}

			$args['name']             = 'comment-count';
			$args['content']          = $comment_count;
			$args['sanitize_content'] = false;
			$args['link']             = false; // important!!

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Avatar ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card avatar.
		 */
		public function get_avatar( $args = [] ) {
			$default_args = array(
				'link'        => true,
				'size'        => '',
				'class'       => '',
				'image_class' => '',
			);

			/**
			 * Filters the card avatar args.
			 *
			 * @param array $args The args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_avatar_args', wp_parse_args( $args, $default_args ), $this );

			$avatar = '';


			if ( isset( $this->args['avatar'] ) ) {
				$avatar = $this->args['avatar'];
			} elseif ( $this->post_id ) {

				$post = get_post( $this->post_id );

				if ( true === $args['link'] ) {
					$author_posts_url = get_author_posts_url( $post->post_author );
				}

				if ( ! empty( $author_posts_url ) ) {
					$avatar .= '<a href="' . esc_url( $author_posts_url ) . '">';
				}

				$avatar .= get_avatar( $post->post_author, $args['size'], '', '', array(
					'class' => $args['image_class'],
				) );

				if ( ! empty( $author_posts_url ) ) {
					$avatar .= '</a>';
				}

			}

			/**
			 * Filters the card avatar html.
			 *
			 * @param string $avatar The avatar html.
			 * @param object $this Current WPEX_Card object.
			 * @param array $args The args passed to get_avatar()
			 */
			$avatar = (string) apply_filters( 'wpex_card_avatar', $avatar, $this, $args );

			if ( empty( $avatar ) ) {
				return;
			}

			$args['name']             = 'avatar';
			$args['content']          = $avatar;
			$args['sanitize_content'] = false;
			$args['link']             = false; // important!!

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Count ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card number.
		 */
		public function get_number( $args = [] ) {
			$default_args = array(
				'link'         => false,
				'class'        => '',
				'number'       => '',
				'prepend_zero' => false,
			);

			/**
			 * Filters the card number element args.
			 *
			 * @param array $args The args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_number_args', wp_parse_args( $args, $default_args ), $this );

			$number = $args['number'];

			if ( isset( $this->args['number'] ) ) {
				$number = $this->args['number'];
			} elseif ( ! empty( $this->post_id ) ) {
				$number = $this->get_card_meta( 'number', true );
			}

			if ( empty( $number ) ) {
				$number = $this->get_var( 'running_count' );
			}

			if ( true === wpex_validate_boolean( $args['prepend_zero'] ) ) {
				$number = sprintf( '%02d', $number );
			}

			/**
			 * Filters the card number display.
			 *
			 * @param string $number The number display.
			 * @param object $this Current WPEX_Card object.
			 */
			$number = (string) apply_filters( 'wpex_card_number', $number, $this, $args );

			if ( empty( $number ) ) {
				return;
			}

			$args['name']    = 'number';
			$args['content'] = $number;
			unset( $args['number'] );

			return $this->get_element( $args );
		}

		/**
		 * Get card count.
		 */
		public function get_running_count() {
			$running_count = get_query_var( 'wpex_loop_running_count' );

			// Applies running_count fix with paginated pages.
			// @todo apply to all is_main_query() queries and check using global $wp_query
			if ( is_search() && is_paged() && in_the_loop() ) {
				$paged = absint( get_query_var( 'paged' ) );
				if ( $paged > 1 ) {
					$posts_per_page = absint( get_query_var( 'posts_per_page' ) );
					if ( $posts_per_page ) {
						$running_count = absint( $running_count ) + $posts_per_page * ( $paged - 1 );
					}
				}
			}

			/**
			 * Filters the card running count.
			 *
			 * @param int $count The running count.
			 * @param object $this Current WPEX_Card object.
			 */
			$running_count = (int) apply_filters( 'wpex_card_running_count', $running_count, $this );

			$this->running_count = absint( $running_count );

			return $this->running_count;
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Rating ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get card rating.
		 */
		public function get_rating() {
			$rating = '';

			if ( ! empty( $this->args['rating'] ) ) {
				$rating = $this->args['rating'];
			} elseif ( $this->post_id ) {
				$rating = get_post_meta( $this->post_id, 'wpex_post_rating', true );
				if ( empty( $rating ) ) {
					$rating = $this->get_card_meta( 'rating', true );
				}
			}

			if ( empty( $rating ) && function_exists( 'wc_get_product' ) && 'product' === $this->get_post_type() ) {
				$product = wc_get_product( $this->post_id );
				if ( $product ) {
					$rating = $product->get_average_rating();
				}
			}

			/**
			 * Filters the card rating value.
			 *
			 * @param int $rating The rating.
			 * @param object $this Current WPEX_Card object.
			 */
			$rating = (int) apply_filters( 'wpex_card_rating', $rating, $this );

			$this->rating = floatval( $rating );

			return $this->rating;
		}

		/**
		 * Get card rating.
		 */
		public function get_star_rating( $args = [] ) {
			$default_args = [
				'link'   => false,
				'class'  => '',
			];

			/**
			 * Filters the card star rating element args.
			 *
			 * @param array $args The args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_star_rating_args', wp_parse_args( $args, $default_args ), $this );

			$star_rating = '';

			$rating = $this->get_rating();

			if ( $rating ) {
				$star_rating = wpex_get_star_rating( $rating );
			}

			/**
			 * Filters the card star rating element display.
			 *
			 * @param string $star_rating The star rating html.
			 * @param object $this Current WPEX_Card object.
			 */
			$star_rating = (string) apply_filters( 'wpex_card_star_rating', $star_rating, $this, $args );

			if ( empty( $star_rating ) ) {
				return;
			}

			$args['name']             = 'star-rating';
			$args['content']          = $star_rating;
			$args['sanitize_content'] = false; // already sanitized

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Products ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Check if a product card is onsale.
		 */
		public function is_on_sale() {
			$check = false;

			if ( isset( $this->args['is_on_sale'] ) ) {
				$check = $this->args['is_on_sale'];
			} elseif ( $this->post_id ) {
				$post_type = get_post_type( $this->post_id );
				switch ( $post_type ) {
					case 'product':
						if ( class_exists( 'WooCommerce' ) ) {
							if ( function_exists( 'wc_get_product' ) ) {
								$product = wc_get_product( $this->post_id );
								if ( $product && $product->is_on_sale() ) {
									$check = true;
								}
							}
						}
						break;
				}
			}

			/**
			 * Filters whether the currently displayed post is on sale.
			 *
			 * @param bool $check Is it on sale?
			 * @param object $this Current WPEX_Card object.
			 */
			return (bool) apply_filters( 'wpex_card_is_on_sale', $check, $this );
		}

		/**
		 * Get card product price.
		 */
		public function get_price( $args = [] ) {
			$default_args = array(
				'link'  => false,
				'class' => '',
			);

			/**
			 * Filters the card price element arguments.
			 *
			 * @param array $args The args.
			 * @param object $this Current WPEX_Card object.
			 */
			$args = (array) apply_filters( 'wpex_card_price_args', wp_parse_args( $args, $default_args ), $this );

			$price = '';

			if ( ! empty( $this->args['price'] ) ) {
				$price = $this->args['price'];
			} elseif ( $this->post_id ) {

				$price = $this->get_card_meta( 'price', true );

				if ( empty( $price ) ) {

					$post_type = get_post_type( $this->post_id );

					switch ( $post_type ) {
						case 'product':
							if ( class_exists( 'WooCommerce' ) ) {
								if ( function_exists( 'wc_get_product' ) ) {
									$product = wc_get_product( $this->post_id );
									if ( $product ) {
										$price = $product->get_price_html();
									}
								}
							}
							break;
						case 'download':
							if ( class_exists( 'Easy_Digital_Downloads' ) ) {
								if ( edd_is_free_download() ) {
									$price = esc_html__( 'Free', 'total' );
								} else {
									$price = edd_price( $this->post_id, false );
								}
							}
							break;
					}

				}

			}

			/**
			 * Filters the card price html.
			 *
			 * @param string $price The price html.
			 * @param object $this Current WPEX_Card object.
			 */
			$price = (string) apply_filters( 'wpex_card_price', $price, $this, $args );

			if ( empty( $price ) ) {
				return;
			}

			$args['name']             = 'price';
			$args['content']          = $price;
			$args['sanitize_content'] = false; // already sanitized

			return $this->get_element( $args );
		}

		/**
		 * Get card sale flash.
		 */
		public function get_sale_flash( $args = [] ) {
			if ( ! $this->is_on_sale() ) {
				return;
			}

			$default_args = [
				'link'  => false,
				'class' => '',
				'text'  => esc_html( 'Sale', 'total' ),
			];

			/**
			 * Filters the card sale flash args.
			 * 
			 * @param array $args The args.
			 * @param object $card The card object.
			 */
			$args = (array) apply_filters( 'wpex_card_sale_flash_args', wp_parse_args( $args, $default_args ), $this );

			/**
			 * Filters the card sale flash html.
			 *
			 * @param string $text The sale flash text.
			 * @param object $this Current WPEX_Card object.
			 */
			$sale_flash = (string) apply_filters( 'wpex_card_sale_flash', $args['text'] , $this, $args );

			if ( empty( $sale_flash ) ) {
				return;
			}

			$args['name']    = 'sale-flash';
			$args['content'] = $sale_flash;

			unset( $args['text'] );

			return $this->get_element( $args );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Helpers ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Get unique_id.
		 */
		protected function get_unique_id() {
			if ( $this->post_id ) {
				$this->unique_id = $this->post_id;
			} elseif ( ! empty( $this->term->term_id ) ) {
				$this->unique_id = $this->term->term_id;
			} else {
				$this->unique_id = uniqid();
			}
			return $this->unique_id;
		}

		/**
		 * Check if the current card is a template.
		 */
		public function is_template(): bool {
			return str_starts_with( $this->style, 'template_' );
		}

		/**
		 * Get template ID.
		 */
		public function get_template_id(): int {
			$template_id = 0;

			if ( ! empty( $this->args['template_id'] ) ) {
				$template_id = absint( $this->args['template_id'] );
			} else {
				$template_id = absint( str_replace( 'template_', '', $this->style ) );
			}

			$template_id = wpex_parse_obj_id( $template_id, 'wpex_card' );
			$template_id = apply_filters_deprecated( 'wpex_get_template_id', [ $template_id, $this ], '5.10.1', 'wpex_card_template_id' );

			/**
			 * Filters the card template ID when displaying template (WPBakery/Gutenberg/Elementor) based cards.
			 *
			 * @param string $template_id
			 * @param object $this Current WPEX_Card object.
			 */
			$template_id = apply_filters( 'wpex_card_template_id', $template_id, $this );

			$this->template_id = $template_id;

			return (int) $this->template_id;
		}

		/**
		 * Check if card is featured.
		 */
		public function is_featured(): bool {
			return ( isset( $this->args['featured'] ) && true === $this->args['featured'] );
		}

		/**
		 * Check if card is even.
		 */
		public function is_even(): bool {
			return ( 0 === $this->get_var( 'running_count' ) % 2 );
		}

		/**
		 * Enqueue lightbox scripts.
		 */
		public function enqueue_lightbox(): void {
			if ( function_exists( 'wpex_enqueue_lightbox_scripts' ) ) {
				wpex_enqueue_lightbox_scripts();
			}
		}

		/**
		 * Get card meta.
		 */
		public function get_card_meta( $key = '', $check_common = false ) {
			if ( $this->post_id && $key ) {
				$meta = get_post_meta( $this->post_id, "wpex_card_{$key}", true );
				if ( empty( $meta ) && $check_common ) {
					$meta = get_post_meta( $this->post_id, $key, true ); // check common named meta.
				}
				return $meta;
			}
		}

		/**
		 * Get template meta.
		 */
		protected function get_template_meta( string $key ) {
			return get_post_meta( $this->get_var( 'template_id' ), $key, true );
		}

		/**
		 * Get card breakpoint.
		 */
		public function get_breakpoint() {
			$breakpoint = 'md';

			if ( ! empty( $this->args['breakpoint'] ) && is_string( $this->args['breakpoint'] ) ) {
				$breakpoint = sanitize_text_field( $this->args['breakpoint'] );
			}

			/**
			 * Filters the card breakpoint.
			 *
			 * @param string $breakpoint
			 * @param object $this Current WPEX_Card object.
			 */
			$breakpoint = (string) apply_filters( 'wpex_card_breakpoint', $breakpoint, $this );

			if ( 'false' === $breakpoint || false === $breakpoint ) {
				return '';
			}

			return esc_attr( $breakpoint );
		}

		/**
		 * Get card post type.
		 */
		public function get_post_type() {
			if ( $this->post_id ) {
				return get_post_type( $this->post_id );
			}
		}

		/**
		 * Get card post format.
		 */
		public function get_post_format() {
			if ( $this->post_id ) {

				/**
				 * Filters the card post format.
				 *
				 * @param string|boolean $format
				 * @param object $this WPEX_Card
				 */
				$format = apply_filters( 'wpex_card_post_format', get_post_format( $this->post_id ), $this );

				return $format;
			}
		}

		/**
		 * Get card object variable.
		 */
		public function get_var( $var, $args = '' ) {
			if ( isset( $this->$var ) ) {
				return $this->$var;
			}

			$method_name = "get_{$var}";

			if ( method_exists( $this, $method_name ) ) {
				if ( $args ) {
					return $this->$method_name( $args );
				}
				return $this->$method_name();
			}
		}

		/**
		 * Get card custom field.
		 */
		public function get_custom_field( $args = [] ) {
			if ( empty( $args['key'] ) || ! $this->post_id ) {
				return;
			}

			$args['content'] = get_post_meta( $this->post_id, $args['key'], true );

			unset( $args['key'] );

			return $this->get_element( $args );
		}

		/**
		 * Check if card flex direction is reversed.
		 */
		public function has_flex_direction_reverse() {
			$check = false;

			if ( ! empty( $this->args['alternate_flex_direction'] )
				&& wpex_validate_boolean( $this->args['alternate_flex_direction'] )
				&& $this->is_even()
			) {
				$check = true;
			}

			return $check;
		}

		/**
		 * Check if a product card is onsale.
		 */
		public function is_event(): bool {
			return in_array( $this->get_post_type(), [ 'just_event', 'tribe_events' ] );
		}

		/*-------------------------------------------------------------------------------*/
		/* [ Public Static Methods ]
		/*-------------------------------------------------------------------------------*/

		/**
		 * Returns link types.
		 */
		public static function get_link_types() {
			$link_types = [
				''         => esc_html__( 'Go to Item', 'total' ),
			//	'post'     => esc_html__( 'Link to Post', 'total' ), // @deprecated in 6.1 as it's the default no need to save.
				'lightbox' => esc_html__( 'Lightbox', 'total' ),
				'dialog'   => esc_html__( 'Modal Dialog (Browser Modal)', 'total' ),
				'modal'    => esc_html__( 'Modal Popup (Lightbox Script)', 'total' ),
				'none'     => esc_html__( 'None', 'total' ),
			];

			/**
			 * Filters the card link types that are allowed.
			 * 
			 * @param array $types The link types.
			 * @param object $card The card object.
			 */
			return (array) apply_filters( 'wpex_card_link_types', $link_types );
		}

		/**
		 * Define and return array of all card styles.
		 */
		public static function get_card_styles(): array {
			if ( null === self::$card_styles ) {
				self::$card_styles = (array) apply_filters( 'wpex_card_styles', self::get_default_card_styles() );
			}
			return self::$card_styles;
		}

		/**
		 * Returns default card styles.
		 */
		public static function get_default_card_styles(): array {
			$enabled = (bool) apply_filters( 'wpex_has_default_card_styles', true );

			if ( ! $enabled ) {
				return [];
			}

			$styles = [
				'button' => [
					'name' => esc_html__( 'Button', 'total' ),
				],
				'link' => [
					'name' => esc_html__( 'Simple Link', 'total' ),
				],
			];
		
			$card_categories = [
				'title' => [
					'name' => esc_html__( 'Title', 'total' ),
				],
				'post-content' => [
					'name' => esc_html__( 'Post Content', 'total' ),
				],
				'image' => [
					'name' => esc_html__( 'Image', 'total' ),
				],
				'image_cover' => [
					'name' => esc_html__( 'Image Cover', 'total' ),
				],
				'video' => [
					'name' => esc_html__( 'Video', 'total' ),
				],
				'toggle' => [
					'name' => esc_html__( 'Toggle', 'total' ),
					'count' => 2,
				],
				'simple' => [
					'name' => esc_html__( 'Simple', 'total' ),
					'count' => 9,
				],
				'news' => [
					'name' => esc_html__( 'News', 'total' ),
					'count' => 6,
				],
				'blog' => [
					'name' => esc_html__( 'Blog', 'total' ),
					'count' => 22,
				],
				'blog-list' => [
					'name' => esc_html__( 'Blog List', 'total' ),
					'count' => 22,
				],
				'magazine' => [
					'name' => esc_html__( 'Magazine', 'total' ),
					'count' => 2,
				],
				'magazine-list' => [
					'name' => esc_html__( 'Magazine List', 'total' ),
					'count' => 2,
				],
				'numbered-list' => [
					'name' => esc_html__( 'Numbered List', 'total' ),
					'count' => 6,
				],
				'overlay' => [
					'name' => esc_html__( 'Overlay', 'total' ),
					'count' => 14,
				],
				'testimonial' => [
					'name' => esc_html__( 'Testimonial', 'total' ),
					'count' => 9,
				],
				'staff' => [
					'name' => esc_html__( 'Staff', 'total' ),
					'count' => 8,
				],
				'portfolio' => [
					'name' => esc_html__( 'Portfolio', 'total' ),
					'count' => 6,
				],
				'product' => [
					'name' => esc_html__( 'Product', 'total' ),
					'count' => 4,
				],
				'search' => [
					'name' => esc_html__( 'Search', 'total' ),
					'count' => 6,
				],
				'icon-box' => [
					'name' => esc_html__( 'Icon Box', 'total' ),
					'count' => 6,
				],
			];
		
			if ( totaltheme_is_integration_active( 'just_events' ) ) {
				$card_categories['just-events'] = [
					'name' => esc_html__( 'Just Events', 'total' ),
					'count' => 2,
				];
			}
		
			foreach ( $card_categories as $key => $val ) {
				if ( 'image_cover' === $key ) {
					$styles[ $key ] = [
						'name' => $val['name'],
					];
				} else {
					$count = $val['count'] ?? 1;
					$x = 1;
					while ( $x <= $count ) {
						if ( 1 === $count ) {
							$name = $val['name'];
						} else {
							$name = "{$val['name']} {$x}";
						}
						$styles["{$key}_{$x}"] = [
							'name' => $name,
						];
						$x++;
					}
				}
			}

			if ( totaltheme_is_integration_active( 'woocommerce' ) ) {
				$styles['woocommerce'] = [
					'name' => 'WooCommerce',
				];
			}

			return $styles;
		}

		/**
		 * Returns array of groupd card style choices for use with select fields.
		 * 
		 * @note we use "options" and not "choices" in the naming convention to support Elementor.
		 */
		public static function get_grouped_card_style_options(): array {
			$options = [
				'' => esc_html__( 'None', 'total' ),
			];
			foreach ( self::get_card_styles() as $key => $value ) {
				if ( ! empty( $value['name'] ) ) {
					$group_name = $value['group'] ?? esc_html__( 'Default Cards', 'total' );
					$group_key  = sanitize_key( $group_name );
					if ( ! isset( $options[ $group_key ] ) ) {
						$options[ $group_key ] = [
							'label'   => $group_name,
							'options' => [],
						];
					}
					$options[ $group_key ]['options'][ $key ] = $value['name'];
				}
			}
			return $options;
		}

		/**
		 * Returns array of all card style options for use with simple select fields that don't support option groups.
		 */
		public static function get_card_style_options(): array {
			$options = [
				'' => esc_html__( 'None', 'total' ),
			];
			foreach ( self::get_card_styles() as $key => $value ) {
				if ( ! empty( $value['name'] ) ) {
					$options[ $key ] = $value['name'];
				}
			}
			return $options;
		}

	}

}
