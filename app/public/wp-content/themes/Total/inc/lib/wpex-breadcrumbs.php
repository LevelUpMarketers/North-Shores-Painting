<?php

defined( 'ABSPATH' ) || exit;

/**
 * WPEX_Breadcrumbs class.
 */
if ( ! class_exists( 'WPEX_Breadcrumbs' ) ) {

	class WPEX_Breadcrumbs {

		/**
		 * Breadcrumbs html output.
		 */
		public $output = '';

		/**
		 * Breadcrumbs args.
		 */
		protected static $args = null;

		/**
		 * Check if we are displaying custom crumbs.
		 */
		protected $has_custom_crumbs = false;

		/**
		 * Main constructor.
		 */
		public function __construct( $args = [] ) {
			self::$args = self::get_args( $args );

			// Generate trail.
			$gcrumbs = $this->generate_crumbs();

			if ( $gcrumbs ) {
				$has_container = wpex_has_breadcrumbs_container();
				$aria_label    = wpex_get_aria_label( 'breadcrumbs' );

				if ( $aria_label ) {
					$aria_label = ' aria-label="' . esc_attr( $aria_label ) .'"';
				}

				$this->output .= '<nav class="' . esc_attr( self::wrap_classes() ) . '"' . $aria_label . '>';

					if ( $has_container ) {
						$this->output .= '<div class="container">';
					}

					if ( $this->has_custom_crumbs() ) {
						$this->output .= '<span class="breadcrumb-trail">' . $gcrumbs . '</span>';
					} else {
						$this->output .= '<span class="breadcrumb-trail">' . $gcrumbs . '</span>';
					}

					if ( $has_container ) {
						$this->output .= '</div>';
					}

				$this->output .= '</nav>';

			}
		}

		/**
		 * Outputs the generated breadcrumbs.
		 *
		 * @deprecated 3.6.0 // Will be removed at some point since it's not needed
		 */
		public function display( $echo = true ) {
			return $this->output;
		}

		/**
		 * Check if custom breadcrumbs are being displayed instead of the theme crumbs.
		 */
		public function has_custom_crumbs() {
			return apply_filters( 'wpex_has_custom_breadcrumbs_trail', $this->has_custom_crumbs );
		}

		/**
		 * Returns custom breadcrumbs for 3rd party integration.
		 */
		public function get_custom_crumbs(): string {
			$custom_crumbs = '';

			if ( ! $custom_crumbs && function_exists( 'rank_math_get_breadcrumbs' ) ) {
				$custom_crumbs = rank_math_get_breadcrumbs();
			}

			if ( ! $custom_crumbs && function_exists( 'yoast_breadcrumb' ) ) {
				$custom_crumbs = yoast_breadcrumb( '', '', false );
			}

			if ( $custom_crumbs ) {
				$this->has_custom_crumbs = true;
			}

			return (string) apply_filters( 'wpex_custom_breadcrumbs_trail', $custom_crumbs );
		}

		/**
		 * Returns breadcrumbs arguments.
		 */
		public static function get_args( $args = [] ) {
			$home_text = (string) wpex_get_translated_theme_mod( 'breadcrumbs_home_title' );
			$home_text = $home_text ?: esc_html__( 'Home', 'total' );

			if ( $separator = (string) get_theme_mod( 'breadcrumbs_separator' ) ) {
				$separator = do_shortcode( sanitize_text_field( $separator ) );
			}

			$args = wp_parse_args( $args, [
				'home_text'        => $home_text,
				'home_link'        => (string) home_url( '/' ),
				'separator'        => $separator ?: '&raquo',
				'front_page'       => false,
				'cpt_find_parents' => false,
				'show_parents'     => wp_validate_boolean( get_theme_mod( 'breadcrumbs_show_parents', true ) ),
				'show_trail_end'   => wp_validate_boolean( get_theme_mod( 'breadcrumbs_show_trail_end', false ) ),
				'first_term_only'  => wp_validate_boolean( get_theme_mod( 'breadcrumbs_first_cat_only', true ) ),
			] );

			return (array) apply_filters( 'wpex_breadcrumbs_args', $args );
		}

		/**
		 * Returns specific breadcrumbs argument.
		 */
		public static function get_arg( $arg = '' ) {
			if ( array_key_exists( $arg, self::$args ) ) {
				return self::$args[$arg];
			}
			return self::get_args()[$arg] ?? null;
		}

		/**
		 * Generates the breadcrumbs and updates the $trail var.
		 *
		 * @todo separate into different methods to keep things cleaner.
		 */
		public function generate_crumbs() {
			$custom_crumbs = $this->get_custom_crumbs();

			if ( $this->has_custom_crumbs() ) {
				return $custom_crumbs;
			}

			// Globals.
			global $wp_query, $wp_rewrite;

			// Define main variables.
			$breadcrumb = '';

			/*-----------------------------------------------------------------------------------*/
			/*  - Homepage link
			/*  - Note: can't use get_crumb_html() because text must support shortcodes + html
			/*-----------------------------------------------------------------------------------*/
			$trail['trail_start'] = '<span class="trail-begin"><a href="'. esc_url( self::$args['home_link'] ) .'" rel="home"><span>' . do_shortcode( wp_kses_post( self::$args['home_text'] ) ) . '</span></a></span>';

			/*-----------------------------------------------------------------------------------*/
			/*  - Front Page
			/*-----------------------------------------------------------------------------------*/
			if ( is_front_page() && false === self::$args['front_page'] ) {
				$trail = false;
			}

			/*-----------------------------------------------------------------------------------*/
			/*  - Homepage or posts page
			/*-----------------------------------------------------------------------------------*/
			elseif ( is_home() ) {
				$home_page = get_page( $wp_query->get_queried_object_id() );
				if ( is_object( $home_page ) ) {
					$trail = array_merge( $trail, self::get_post_parents( $home_page->post_parent, '' ) );
					if ( self::$args['show_trail_end'] ) {
						$trail['trail_end'] = get_the_title( $home_page->ID );
					}
				} else {
					$trail = false;
				}
			}

			/*-----------------------------------------------------------------------------------*/
			/*  - Singular: Page, Post, Attachment...etc
			/*-----------------------------------------------------------------------------------*/
			elseif ( $wp_query->is_singular ) {

				// Get singular vars.
				$post                       = $wp_query->get_queried_object();
				$post_id                    = absint( $wp_query->get_queried_object_id() );
				$post_type                  = $post->post_type;
				$post_type_obj              = get_post_type_object( $post_type );
				$parent                     = ( true === self::$args['show_parents'] ) ? $post->post_parent : '';
				$trail['post_type_archive'] = ''; // Add empty post type trail for custom types.

				// If parent is the same as front-page set to empty.
				if ( $parent == get_option( 'page_on_front' ) ) {
					$parent = '';
				}

				// Get Post types primary page.
				switch ( $post_type ) {

					case 'page':

						// Woo pages
						if ( totaltheme_is_integration_active( 'woocommerce' ) ) {

							// Add shop page to cart.
							if ( is_cart() || is_checkout() ) {

								// Get shop data.
								$shop_data  = self::get_shop_data();
								$shop_url   = $shop_data['url'];
								$shop_title = $shop_data['title'];

								// Add shop link.
								if ( $shop_url && $shop_title ) {
									$trail['shop'] = self::get_crumb_html( $shop_title, $shop_url, 'trail-shop' );
								}

							}

							// Add cart to checkout.
							if ( apply_filters( 'wpex_breadcrumbs_checkout_cart', false )
								&& is_checkout()
								&& $cart_id = wpex_parse_obj_id( wc_get_page_id( 'cart' ), 'page' )
							) {
								$trail['cart'] = self::get_crumb_html( get_the_title( $cart_id ), get_permalink( $cart_id ), 'trail-cart' );
							}

						}

						// Add page parents.
						if ( $parent ) {
							$trail = array_merge( $trail, self::get_post_parents( $parent ) );
						}

						break;

					case 'post';

						// Main Blog URL.
						if ( $page_id = wpex_parse_obj_id( get_theme_mod( 'blog_page' ), 'page' ) ) {
							if ( true === self::$args['show_parents'] && $parents = self::get_post_parents( $page_id ) ) {
								$trail = array_merge( $trail, $parents );
							} else {
								$trail['blog'] = self::get_crumb_html( get_the_title( $page_id ), get_permalink( $page_id ), 'trail-blog-url' );
							}
						}

						// Add URL based on posts page.
						elseif ( $page_for_posts = get_option( 'page_for_posts' ) ) {
							$trail['blog'] = self::get_crumb_html( get_the_title( $page_for_posts ), get_permalink( $page_for_posts ), 'trail-blog-url' );
						}

						// Categories.
						if ( $terms = self::get_post_terms( 'category' ) ) {
							$trail['categories'] = '<span class="trail-post-categories">' . $terms . '</span>';
						}

						break;

					case 'tribe_events';

						if ( function_exists( 'tribe_get_events_link' ) ) {
							if ( $page_id = wpex_parse_obj_id( get_theme_mod( 'tribe_events_main_page' ), 'page' ) ) {
								$title = get_the_title( $page_id );
								$link  = get_permalink( $page_id );
							} else {
								$title = esc_html__( 'All Events', 'total' );
								$link  = tribe_get_events_link();
							}
							$trail['tribe_events'] = self::get_crumb_html( $title, $link, 'trail-all-events' );
						}

						break;

					case 'just_event':
						$events_page = \get_option( 'just_events' )['totaltheme_events_page'] ?? '';
						if ( $events_page && $page_id = wpex_parse_obj_id( $events_page, 'page' ) ) {
							if ( true === self::$args['show_parents'] && $parents = self::get_post_parents( $page_id ) ) {
								$trail = array_merge( $trail, $parents );
							} else {
								$trail['post_type_archive'] = self::get_crumb_html( get_the_title( $page_id ), get_permalink( $page_id ) );
							}
						} elseif ( ! empty( $post_type_obj->has_archive ) ) {
							$trail['post_type_archive'] = self::get_crumb_html( $post_type_obj->labels->name, get_post_type_archive_link( $post_type ), 'trail-type-archive' );
						}
						$just_event_main_tax = wpex_get_post_type_cat_tax( 'just_event' );
						if ( $just_event_main_tax && $terms = self::get_post_terms( $just_event_main_tax ) ) {
							$trail['categories'] = '<span class="trail-post-categories">' . $terms . '</span>';
						}
						break;

					case 'product':

						// Get shop data.
						$shop_data  = self::get_shop_data();
						$shop_id    = $shop_data['id'];
						$shop_url   = $shop_data['url'];
						$shop_title = $shop_data['title'];

						// Add shop page to product post.
						if ( $shop_url && $shop_title && $shop_id != get_option( 'page_on_front' ) ) {
							$trail['shop'] = self::get_crumb_html( $shop_title, $shop_url, 'trail-shop' );
						}

						// Add categories to product post.
						if ( $terms = self::get_post_terms( 'product_cat' ) ) {
							$trail['categories'] = '<span class="trail-post-categories">' . $terms . '</span>';
						}

						// Add cart to product post.
						if ( apply_filters( 'wpex_breadcrumbs_single_product_cart', false )
							&& $page_id = wpex_parse_obj_id( wc_get_page_id( 'cart' ) )
						) {
							$trail['cart'] = self::get_crumb_html( get_the_title( $page_id ), get_permalink( $page_id ), 'trail-cart' );
						}

						break;

					default:

						// Check PTU settings which override the default post_type_archive.
						if ( $ptu_main_page = wpex_get_ptu_type_mod( $post_type, 'main_page' ) ) {
							$ptu_main_page = wpex_parse_obj_id( $ptu_main_page );
							if ( 'publish' === get_post_status( $ptu_main_page ) ) {
								if ( true === self::$args['show_parents'] && $parents = self::get_post_parents( $ptu_main_page ) ) {
									unset( $trail['post_type_archive'] );
									$trail = array_merge( $trail, $parents );
								} else {
									$trail['post_type_archive'] = self::get_crumb_html( get_the_title( $ptu_main_page ), get_permalink( $ptu_main_page ), 'trail-main-page' );
								}
							}
						}

						// Add post type archive or main page
						if ( empty( $trail['post_type_archive'] ) ) {
							if ( ! empty( $post_type_obj->has_archive ) && ! is_singular( 'product' ) ) {
								$trail['post_type_archive'] = self::get_crumb_html( $post_type_obj->labels->name, get_post_type_archive_link( $post_type ), 'trail-type-archive' );
							} elseif ( $main_page = apply_filters( 'wpex_breadcrumbs_cpt_main_page_id', 0 ) ) {
								$main_page = wpex_parse_obj_id( $main_page, 'page' );
								if ( 'publish' === get_post_status( $main_page ) ) {
									if ( true === self::$args['show_parents'] && $parents = self::get_post_parents( $main_page ) ) {
										unset( $trail['post_type_archive'] );
										$trail = array_merge( $trail, $parents );
									} else {
										$trail[ $post_type ] = self::get_crumb_html( get_the_title( $main_page ), get_permalink( $main_page ), "trail-{$post_type}-url" );
									}
								}
							}
							if ( empty( $trail['post_type_archive'] ) && self::$args['cpt_find_parents'] ) {
								$trail = array_merge( $trail, self::cpt_find_parents( $post_type_obj ) );
							}
						}

						// CPTUI parent page.
						if ( empty( $trail['categories'] ) && defined( 'WPEX_CPTUI_INTEGRATION' ) ) {
							$cptui_main_page = TotalTheme\Integration\Custom_Post_Type_UI::get_setting_val( $post_type, 'main_page' );
							if ( $cptui_main_page ) {
								$cptui_main_page = wpex_parse_obj_id( $cptui_main_page );
								if ( get_post_status( $cptui_main_page ) ) {
									$trail['post_type_archive'] = self::get_crumb_html( get_the_title( $cptui_main_page ), get_permalink( $cptui_main_page ) );
								}
							}
						}

						// Add post type parent posts.
						if ( $parent ) {
							$trail = array_merge( $trail, self::get_post_parents( $parent ) );
						}

						// CPTUI terms.
						if ( empty( $trail['categories'] ) && defined( 'WPEX_CPTUI_INTEGRATION' ) ) {
							$cptui_tax = TotalTheme\Integration\Custom_Post_Type_UI::get_setting_val( $post_type, 'main_taxonomy' );
							if ( $cptui_tax && taxonomy_exists( $cptui_tax ) ) {
								if ( $terms = self::get_post_terms( $cptui_tax ) ) {
									$trail['categories'] = '<span class="trail-cptui-terms">' . $terms . '</span>';
								}
							}
						}

						// Check cpt main tax.
						if ( empty( $trail['categories'] ) && $cpt_main_tax = wpex_get_post_type_cat_tax( $post_type ) ) {
							if ( $terms = self::get_post_terms( $cpt_main_tax ) ) {
								$trail['categories'] = '<span class="trail-categories">' . $terms . '</span>';
							}
						}

						// Add empty category to array for addition of taxonomies via filters.
						if ( empty( $trail['categories'] ) ) {
							$trail['categories'] = '';
						}
						break;

				} // End $post_type switch.

				// End trail with post title.
				if ( self::$args['show_trail_end'] && $post_title = get_the_title( $post_id ) ) {
					$trim_title = get_theme_mod( 'breadcrumbs_title_trim' );
					$show_title = true;
					if ( isset( $trim_title ) && '0' === $trim_title ) {
						$show_title = false;
					}
					if ( $show_title ) {
						if ( $trim_title ) {
							$post_title = wp_trim_words( $post_title, $trim_title );
						}
						$trail['trail_end'] = $post_title;
					}
				}

			}

			/*-----------------------------------------------------------------------------------*/
			/*  - ALL Archives
			/*-----------------------------------------------------------------------------------*/
			elseif ( is_archive() ) {

				/*-----------------------------------------------------------------------------------*/
				/*  - Post Type Archive
				/*-----------------------------------------------------------------------------------*/
				if ( is_post_type_archive() ) {

					// Shop Archive.
					if ( function_exists( 'is_shop' ) && is_shop() ) {

						if ( apply_filters( 'wpex_breadcrumbs_shop_cart', false ) ) {
							global $woocommerce;
							if ( sizeof( $woocommerce->cart->cart_contents ) > 0 ) {
								if ( $cart_id = totaltheme_wc_get_page_id( 'cart' ) ) {
									$trail['cart'] = self::get_crumb_html( get_the_title( $cart_id ), get_permalink( $cart_id ), 'trail-cart' );
								}
							}
						}

						// Store shop data.
						$shop_data = self::get_shop_data();

						// Add shop page title to trail end.
						if ( self::$args['show_trail_end'] ) {
							$trail['trail_end'] = ! empty( $shop_data['title'] ) ? $shop_data['title'] : post_type_archive_title( '', false );
						}

					}

					// Topics Post Type Archive.
					elseif ( is_post_type_archive( 'topic' ) ) {
						if ( is_object( $forum_obj = get_post_type_object( 'forum' ) ) ) {
							if ( $forums_link = get_post_type_archive_link( 'forum' ) ) {
								$trail['forum'] = self::get_crumb_html( $forum_obj->labels->name, $forums_link, 'trail-forum' );
							}
							if ( self::$args['show_trail_end'] ) {
								$trail['trail_end'] = $forum_obj->labels->name;
							}
						}

					// All other post type archives.
					} else {
						$post_type = get_query_var( 'post_type' );

						// Check PTU settings in case the selected main page is not the archive it can still be added to the trail
						if ( $ptu_main_page = wpex_get_ptu_type_mod( $post_type, 'main_page' ) ) {
							$ptu_main_page = wpex_parse_obj_id( $ptu_main_page );
							if ( get_post_status( $ptu_main_page ) && true === self::$args['show_parents'] && $parents = self::get_post_parents( $ptu_main_page ) ) {
								unset( $trail['post_type_archive'] );
								$trail = array_merge( $trail, $parents );
							}
						}

						// Add post type name to trail end
						if ( self::$args['show_trail_end'] ) {
							$trail['trail_end'] = get_post_type_object( $post_type )->labels->name ?? '';
						}

					}
				}

				/*-----------------------------------------------------------------------------------*/
				/*  - Taxonomy Archive
				/*-----------------------------------------------------------------------------------*/
				elseif ( ! is_search() && ( is_tax() || is_category() || is_tag() ) ) {

					// Display main blog page on post archives.
					if ( is_category() || is_tag() ) {
						$blog_page = wpex_parse_obj_id( get_theme_mod( 'blog_page' ), 'page' );
						if ( $blog_page ) {
							if ( true === self::$args['show_parents'] && $parents = self::get_post_parents( $blog_page ) ) {
								$trail = array_merge( $trail, $parents );
							} else {
								$trail['blog'] = self::get_crumb_html( get_the_title( $blog_page ), get_permalink( $blog_page ), 'trail-blog-url' );
							}
						} elseif ( $page_for_posts = get_option( 'page_for_posts' ) ) {
							$trail['blog'] = self::get_crumb_html( get_the_title( $page_for_posts ), get_permalink( $page_for_posts ), 'trail-blog-url' );
						}
					}

					// Woo Product Tax
					elseif ( wpex_is_woo_tax() ) {

						// Get shop data.
						$shop_data  = self::get_shop_data();
						$shop_url   = $shop_data['url'];
						$shop_title = $shop_data['title'];

						// Add shop page to product post.
						if ( $shop_url && $shop_title ) {
							$trail['shop'] = self::get_crumb_html( $shop_title, $shop_url, 'trail-shop' );
						}

					}

					// For all other taxonomies get post type archive URL or locate a page.
					else {

						if ( $ptu_main_page = wpex_get_ptu_tax_mod( get_query_var( 'taxonomy' ), 'main_page' ) ) {
							$ptu_main_page = wpex_parse_obj_id( $ptu_main_page );
							if ( str_starts_with( $ptu_main_page, 'pt_archive_' ) ) {
								$pt_check_cpt = str_replace( 'pt_archive_', '', $ptu_main_page );
								$pt_archive_link = get_post_type_archive_link( $pt_check_cpt );
								if ( $pt_archive_link ) {
									$pt_obj = get_post_type_object( $pt_check_cpt );
									if ( is_object( $pt_obj ) && ! empty( $pt_obj->labels->name ) ) {
										$trail['post_type_archive'] = self::get_crumb_html( $pt_obj->labels->name, $pt_archive_link );
									}
								}
							} else {
								if ( true === self::$args['show_parents'] && $parents = self::get_post_parents( $ptu_main_page ) ) {
									unset( $trail['post_type_archive'] );
									$trail = array_merge( $trail, $parents );
								} else {
									$trail['post_type_archive'] = self::get_crumb_html( get_the_title( $ptu_main_page ), get_permalink( $ptu_main_page ) );
								}
							}
						}

						if ( empty( $trail['post_type_archive'] ) ) {
							$post_type     = get_post_type();
							$post_type_obj = get_post_type_object( $post_type );
							if ( ! empty( $post_type_obj->has_archive ) ) {
								$trail['post_type_archive'] = self::get_crumb_html( $post_type_obj->labels->name, get_post_type_archive_link( $post_type ), 'trail-type-archive' );
							} elseif ( $main_page = apply_filters( 'wpex_breadcrumbs_cpt_main_page_id', 0 ) ) {
								$main_page = wpex_parse_obj_id( $main_page, 'page' );
								if ( 'publish' === get_post_status( $main_page ) ) {
									if ( true === self::$args['show_parents'] && $parents = self::get_post_parents( $main_page ) ) {
										$trail = array_merge( $trail, $parents );
									} else {
										$trail[ $post_type ] = self::get_crumb_html( get_the_title( $main_page ), get_permalink( $main_page ), "trail-{$post_type}-url" );
									}
								}
							}
							if ( empty( $trail['post_type_archive'] ) && self::$args['cpt_find_parents'] ) {
								$trail = array_merge( $trail, self::cpt_find_parents( $post_type_obj ) );
							}
						}

					}

					// Add term parents
					$term = $wp_query->get_queried_object();
					if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent ) {
						$trail = array_merge( $trail, $this->get_term_parents( $term ) );
					}

					// Add term name to trail end
					if ( self::$args['show_trail_end'] && isset( $term->name ) ) {
						$trail['trail_end'] = $term->name;
					}

				}

				/*-----------------------------------------------------------------------------------*/
				/*  - Author Archive
				/*-----------------------------------------------------------------------------------*/
				elseif ( is_author() ) {

					// Add main blog
					if ( $page_id = wpex_parse_obj_id( get_theme_mod( 'blog_page' ), 'page' ) ) {
						if ( true === self::$args['show_parents'] && $parents = self::get_post_parents( $page_id ) ) {
							$trail = array_merge( $trail, $parents );
						} else {
							$trail['blog'] = self::get_crumb_html( get_the_title( $page_id ), get_permalink( $page_id ), 'trail-blog-url' );
						}
					}

					// Add the author display name to end
					if ( self::$args['show_trail_end'] ) {
						$trail['trail_end'] = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
					}
				}

				/*-----------------------------------------------------------------------------------*/
				/*  - Time Archive
				/*-----------------------------------------------------------------------------------*/
				elseif ( is_time() ) {
					if ( self::$args['show_trail_end'] ) {
						if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) ) {
							$trail['trail_end'] = get_the_time( 'g:i a' );
						} elseif ( get_query_var( 'minute' ) ) {
							$trail['trail_end'] = sprintf( esc_html__( 'Minute %1$s', 'total' ), get_the_time( 'i' ) );
						} elseif ( get_query_var( 'hour' ) ) {
							$trail['trail_end'] = get_the_time( 'g a' );
						}
					}
				}

				/*-----------------------------------------------------------------------------------*/
				/*  - Date Archive
				/*-----------------------------------------------------------------------------------*/
				elseif ( is_date() ) {

					// Add main blog.
					if ( $page_id = wpex_parse_obj_id( get_theme_mod( 'blog_page' ), 'page' ) ) {
						if ( true === self::$args['show_parents'] && $parents = self::get_post_parents( $page_id ) ) {
							$trail = array_merge( $trail, $parents );
						} else {
							$trail['blog'] = self::get_crumb_html( get_the_title( $page_id ), get_permalink( $page_id ), 'trail-blog-url' );
						}
					} elseif ( $page_for_posts = get_option( 'page_for_posts' ) ) {
						$trail['blog'] = self::get_crumb_html( get_the_title( $page_for_posts ), get_permalink( $page_for_posts ), 'trail-blog-url' );
					}

					// If $front is set check for parents.
					if ( $wp_rewrite->front ) {
						$trail = array_merge( $trail, self::get_post_parents( '', $wp_rewrite->front ) );
					}

					// Day archive.
					if ( is_day() ) {

						// Link to year archive.
						$title = date_i18n( 'Y', strtotime( get_the_time( 'd.m.Y' ) ) );
						$link  = get_year_link( get_the_time( 'Y' ) );
						$trail['year'] = self::get_crumb_html( $title, $link, 'trail-year' );

						// Link to month archive.
						$title = date_i18n( 'F', strtotime( get_the_time( 'd.m.Y' ) ) );
						$link  = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
						$trail['month'] = self::get_crumb_html( $title, $link, 'trail-month' );

						// Add time to end.
						if ( self::$args['show_trail_end'] ) {
							$trail['trail_end'] = sprintf( esc_html__( 'Daily Archives: %s', 'total' ), get_the_date() );
						}

					}

					// Week archive.
					if ( get_query_var( 'w' ) ) {

						// Link to year archive.
						$title = date_i18n( 'Y', strtotime( get_the_time( 'd.m.Y' ) ) );
						$link  = get_year_link( get_the_time( 'Y' ) );
						$trail['year'] = self::get_crumb_html( $title, $link, 'trail-year' );

						// Add week to end.
						if ( self::$args['show_trail_end'] ) {
							$trail['trail_end'] = sprintf( esc_html__( 'Week %1$s', 'total' ), get_the_time( 'W' ) );
						}

					}

					// Month archive.
					if ( is_month() ) {

						// Link to year archive.
						$title = date_i18n( 'Y', strtotime( get_the_time( 'd.m.Y' ) ) );
						$link  = get_year_link( get_the_time( 'Y' ) );
						$trail['year'] = self::get_crumb_html( $title, $link, 'trail-year' );

						// Add month to end.
						if ( self::$args['show_trail_end'] ) {
							$trail['trail_end'] = esc_html( sprintf( esc_html__( 'Monthly Archives: %s', 'total' ), get_the_date( 'F Y' ) ) );
						}

					}

					// Year archive.
					if ( is_year() ) {
						if ( self::$args['show_trail_end'] ) {
							$trail['trail_end'] = esc_html( sprintf( esc_html__( 'Yearly Archives: %s', 'total' ), get_the_date( 'Y' ) ) );
						}
					}

				}
			}

			/*-----------------------------------------------------------------------------------*/
			/*  - Search
			/*-----------------------------------------------------------------------------------*/
			elseif ( is_search() ) {
				if ( self::$args['show_trail_end'] ) {
					$trail['trail_end'] = esc_html__( 'Search Results', 'total' );
				}
			}

			/*-----------------------------------------------------------------------------------*/
			/*  - 404
			/*-----------------------------------------------------------------------------------*/
			elseif ( is_404() ) {
				if ( self::$args['show_trail_end'] ) {
					$trail['trail_end'] = esc_html__( '404 Error Page', 'total' );
				}
			}

			/*-----------------------------------------------------------------------------------*/
			/*  - Tribe Calendar Month
			/*-----------------------------------------------------------------------------------*/
			elseif ( function_exists( 'tribe_is_month' ) && tribe_is_month() ) {
				if ( self::$args['show_trail_end'] ) {
					$trail['trail_end'] = esc_html__( 'Events Calendar', 'total' );
				}
			}

			/*-----------------------------------------------------------------------------------*/
			/*  - Create and return the breadcrumbs
			/*-----------------------------------------------------------------------------------*/

			// Add a before trail_end empty item for easier addition of items before the title.
			if ( isset( $trail['trail_end'] ) ) {
				$trail_end = $trail['trail_end'];
				unset( $trail['trail_end'] );
				$trail['pre_trail_end'] = '';
				$trail['trail_end'] = $trail_end;
			} else {
				$trail['pre_trail_end'] = ''; // allows adding pre_trail_end even if trail end doesn't exist.
			}

			$trail = (array) apply_filters( 'wpex_breadcrumbs_trail', $trail, self::$args );

			$trail = array_filter( $trail ); // Remove dups.

			if ( ! $trail ) {
				return;
			}

			// Add to trail.
			if ( isset( $trail['trail_end'] ) ) {
				if ( ! self::$args['show_trail_end'] ) {
					unset( $trail['trail_end'] );
				} else {
					$trail_end_allowed_tags = apply_filters( 'wpex_breadcrumbs_trail_end_allowed_html_tags', [
						'abbr' => [],
					] );
					$trail['trail_end'] = '<span class="trail-end">' . wp_kses( $trail['trail_end'], $trail_end_allowed_tags ) . '</span>';
				}
			}

			// Count all trail items.
			$all_count = count( $trail );

			if ( 0 === $all_count || ( false === self::$args['show_trail_end'] && 1 === $all_count ) ) {
				return '';
			}

			// Loop through items and convert into a single string.
			$count = 0;
			foreach ( $trail as $key => $val ) {
				$count++;
				$breadcrumb .= $val;
				if ( $all_count !== $count ) {
					$breadcrumb .= '<span class="sep sep-' . esc_attr( $count ) . '"> ' . self::$args['separator'] . ' </span>';
				}
			}

			return $breadcrumb;
		} // End generate_crumbs

		/**
		 * Generate single crumb html.
		 */
		public static function get_crumb_html( $label, $link, $class = '', $rel = '' ) {
			if ( ! $link ) {
				return; // Link required.
			}
			$class = $class ? ' class="' . esc_attr( $class ) . '"': '';
			$rel   = $rel ? ' rel="' . esc_attr( $rel ) . '"': '';
			return '<span ' . $class . $rel . '><a href="' . esc_url( $link ) . '"><span>' . wp_strip_all_tags( $label ) . '</span></a></span>';
		}

		/**
		 * Returns item scope.
		 *
		 * @deprecated 5.10.1
		 */
		public static function get_item_sd_markup() {
			return '';
		}

		/**
		 * Returns thing scope.
		 *
		 * @deprecated 5.10.1
		 */
		public static function get_link_sd_markup() {
			return '';
		}

		/**
		 * Display terms.
		 */
		public static function get_post_terms( $taxonomy = '' ) {
			if ( ! get_theme_mod( 'breadcrumbs_show_terms', true ) ) {
				return null;
			}

			// Make sure taxonomy exists.
			if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
				return null;
			}

			// Make sure taxonomy is public.
			$taxonomy_obj = get_taxonomy( $taxonomy );

			if ( ! is_wp_error( $taxonomy_obj )
				&& is_object( $taxonomy_obj )
				&& false === $taxonomy_obj->publicly_queryable
			) {
				return null;
			}

			$terms = apply_filters( 'wpex_breadcrumbs_terms', null, $taxonomy );

			// Return terms if filtered.
			if ( $terms ) {
				return $terms;
			}

			// Get terms.
			$list_terms = [];

			$args = apply_filters( 'wpex_breadcrumbs_wp_get_post_terms_args', [] );

			$terms = get_the_terms( get_the_ID(), $taxonomy, $args );

			// Return if no terms are found.
			if ( ! $terms || is_wp_error( $terms ) ) {
				return;
			}

			// Check if it should display all terms or only first term.
			$show_all_terms = ! self::get_arg( 'first_term_only' );
			$show_all_terms = (bool) apply_filters( 'wpex_breadcrumbs_terms_all', $show_all_terms );

			// Loop through terms.
			if ( $show_all_terms ) {
				foreach ( $terms as $term ) {
					$translated_term = wpex_parse_obj_id( $term->term_id, 'term', $taxonomy );
					if ( $term->term_id != $translated_term ) {
						$term = get_term( $translated_term, $taxonomy ); // wpml fix
					}
					$list_terms[] = self::get_crumb_html( $term->name, get_term_link( $term->term_id, $taxonomy ), 'term-' . $term->term_id );
				}
			}

			// Return first term only.
			else {

				if ( $primary_term = totaltheme_get_post_primary_term( get_the_ID(), $taxonomy, false ) ) {
					$term = $primary_term;
				} else {
					$term = $terms[0];
				}

				$term_link = get_term_link( $term->term_id, $taxonomy );

				$term_class = 'term-' . sanitize_html_class( $term->term_id );

				$list_terms[] = self::get_crumb_html( $term->name, $term_link, $term_class );

			}

			// Sanitize terms.
			$terms = ! empty( $list_terms ) ? implode( wpex_inline_list_sep( 'breadcrumbs' ), $list_terms ) : '';

			return $terms;
		}

		/**
		 * Tries to locate a custom post type parent page based on the path.
		 */
		public static function cpt_find_parents( $post_type_obj = '' ) {
			if ( ! $post_type_obj ) {
				return $trail;
			}

			global $wp_query, $wp_rewrite;
			$trail = [];
			$path  = '';

			// Add $front to the path.
			if ( $post_type_obj->rewrite['with_front'] && $wp_rewrite->front ) {
				$path .= trailingslashit( $wp_rewrite->front );
			}

			// Add slug to $path.
			if ( ! empty( $post_type_obj->rewrite['slug'] ) ) {
				$path .= $post_type_obj->rewrite['slug'];
			}

			// If we can't find a path then return trail.
			if ( ! $path ) {
				return $trail;
			}

			// Get parent post by the path.
			$parent_page = get_page_by_path( $path );

			// Try to get by title with single word.
			if ( empty( $parent_page ) ) {
				$parent_page = self::get_page_by_title( $path );
			}

			// Try again based on title with multiple words.
			if ( empty( $parent_page ) ) {
				$parent_page = self::get_page_by_title( str_replace( [ '-', '_' ], ' ', $path ) );
			}

			// Parent is found so lets return the ID.
			if ( ! empty( $parent_page ) ) {
				$post_id = $parent_page->ID ?? 0;
			}

			if ( $parent_page ) {
				$trail[] = self::get_crumb_html( get_the_title( $post_id ), get_permalink( $post_id ), 'trail-parent' );
			}

			return $trail;
		}

		/**
		 * Searches for post parents and adds them to the trail.
		 */
		public static function get_post_parents( $post_id = '' ) {
			$trail = [];

			// Return empty array if the post id and path are both empty.
			if ( empty( $post_id ) ) {
				return $trail;
			}

			// Define empty parents array.
			$parents = [];

			// Loop through and add parents to parents array.
			while ( $post_id ) {

				// Get the post by ID.
				$post = get_post( $post_id );

				// Add the post link to the array.
				$parents[] = self::get_crumb_html( get_the_title( $post_id ), get_permalink( $post_id ), 'trail-parent' );

				// Set the parent post's parent to the post ID.
				$post_id = $post ? $post->post_parent : '';

			}

			// If parent pages are found reverse order so they are correct.
			if ( $parents ) {
				$trail = array_reverse( $parents );
			}

			return $trail;
		}

		/**
		 * Searches for term parents and adds them to the trail.
		 */
		private function get_term_parents( $term = '' ) {
			$trail = [];

			if ( empty( $term->taxonomy ) ) {
				return $trail;
			}

			$parents  = [];
			$taxonomy = $term->taxonomy;

			if ( is_taxonomy_hierarchical( $taxonomy ) && $term->parent != 0 ) {

				// While there is a parent ID, add the parent term link to the $parents array.
				$count = 0;
				while ( $term->parent != 0 ) {
					$count ++;

					// Get term
					$term = get_term( $term->parent, $taxonomy );

					// Add the formatted term link to the array of parent terms.
					$parents['parent_term_'. $count ] = self::get_crumb_html( $term->name, get_term_link( $term, $taxonomy ), 'trail-parent-term' );

				}

				// If we have parent terms, reverse the array to put them in the proper order for the trail.
				if ( ! empty( $parents ) ) {
					$trail = array_reverse( $parents );
				}

			}

			return $trail;
		}

		/**
		 * Get the parent category if only one term exists for the post.
		 */
		public static function get_singular_first_cat_parents( $taxonomy = '' ) {
			$trail = [];

			if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
				return null;
			}

			$terms = get_the_terms( get_the_ID(), $taxonomy );

			if ( $terms && isset( $terms[0] ) and 1 == count( $terms ) ) {
				$term = get_term( $terms[0] );
				$trail = self::get_term_parents( $term );
			}

			return $trail;
		}

		/**
		 * Gets Woo Shop data.
		 */
		public static function get_shop_data( $return = '' ) {
			$data = [
				'id'    => '',
				'url'   => '',
				'title' => esc_html__( 'Shop', 'total' ),
			];

			$id = (int) totaltheme_wc_get_page_id( 'shop' );

			if ( $id ) {
				$data['id']    = $id;
				$data['url']   = get_permalink( $id );
				$data['title'] = get_the_title( $id );
			}

			$data['title'] = (string) apply_filters( 'wpex_breadcrumbs_shop_title', $data['title'] );

			return $data;
		}

		/**
		 * Returns breadcrumbs classes.
		 *
		 * @todo convert into function wpex_breadcrumbs_class? (keep this method as legacy incase anyone used it)
		 */
		public static function wrap_classes() {
			$classes = [
				'site-breadcrumbs',
			];

			// Position class.
			$classes[] = 'position-' . sanitize_html_class( wpex_breadcrumbs_position() );

			// Visibility.
			if ( $visibility = get_theme_mod( 'breadcrumbs_visibility' ) ) {
				$classes[] = totaltheme_get_visibility_class( $visibility );
			}

			// Utility Classes start here.
			$classes[] = 'wpex-text-4';
			$classes[] = 'wpex-text-sm';

			// Vertical Padding.
			if ( $padding_y = get_theme_mod( 'breadcrumbs_py' ) ) {
				$classes[] = 'wpex-py-' . sanitize_html_class( absint( $padding_y) );
			} elseif ( wpex_has_breadcrumbs_container() ) {
				$classes[] = 'wpex-py-15';
			}

			// Top margin.
			if ( $margin_t = get_theme_mod( 'breadcrumbs_mt' ) ) {
				$classes[] = 'wpex-mt-' . sanitize_html_class( absint( $margin_t ) );
			}

			// Bottom margin.
			if ( $margin_b = get_theme_mod( 'breadcrumbs_mb' ) ) {
				$classes[] = 'wpex-mb-' . sanitize_html_class( absint( $margin_b ) );
			}

			$classes = (array) apply_filters( 'wpex_breadcrumbs_classes', $classes );

			return implode( ' ', array_unique( $classes ) );
		}

		/**
		 * Return a page based on a given title.
		 */
		protected static function get_page_by_title( $title = '' ) {
			$query = new WP_Query(
				[
					'post_type'              => 'page',
					'title'                  => sanitize_text_field( $title ),
					'post_status'            => 'all',
					'posts_per_page'         => 1,
					'no_found_rows'          => true,
					'ignore_sticky_posts'    => true,
					'update_post_term_cache' => false,
					'update_post_meta_cache' => false,
					'orderby'                => 'post_date ID',
					'order'                  => 'ASC',
				]
			);
			if ( ! empty( $query->post ) ) {
				return $query->post;
			}
		}

	}
}
