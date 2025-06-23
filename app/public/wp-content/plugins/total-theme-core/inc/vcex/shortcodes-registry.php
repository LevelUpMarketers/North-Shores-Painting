<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Vcex Shortcodes Registry.
 */
final class Shortcodes_Registry {

	/**
	 * Shortcode class path.
	 */
	private const SHORTCODE_CLASS_PATH = \TTC_PLUGIN_DIR_PATH . 'inc/vcex/shortcodes/';

	/**
	 * Registered shortcodes, as `$key => $shortcode_tag` pairs.
	 */
	private $registered_shortcodes = [];

	/**
	 * Container for the main instance of the class.
	 */
	private static $instance = null;

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 */
	public static function instance() {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
		//	add_action( 'wp_body_open', [ self::$instance, 'run_test' ] );
		}

		return self::$instance;
	}

	/**
	 * Retrieves list of shortcodes in the queue to be registered.
	 */
	public static function get_queue() {
		$shortcodes = [

			// Standard shortcodes.
			'flex_container',
			'grid_container',
			'spacing',
			'divider',
			'heading',
			'button',
			'multi_buttons',
			'toggle_group',
			'toggle',
			'alert',
			'animated_text',
			'wpex_post_cards',
			'wpex_term_cards',
			'blog_grid',
			'blog_carousel',
			'breadcrumbs',
			'bullets',
			'list_item',
			'contact_form',
			'callout',
			'countdown',
			'column_side_border',
			'custom_field',
			'divider_dots',
			'divider_multicolor',
			'form_shortcode',
			'icon_box',
			'feature_box',
			'teaser',
			'icon',
			'video',
			'image',
			'image_banner',
			'image_before_after',
			'image_carousel',
			'image_flexslider',
			'image_galleryslider',
			'image_grid',
			'image_swap',
			'leader',
			'login_form',
			'milestone',
			'horizontal_menu',
			'off_canvas_menu',
			'navbar',
			'newsletter_form',
			'portfolio_carousel',
			'portfolio_grid',
			'post_type_grid',
			'post_type_carousel',
			'post_type_slider',
			'post_type_archive',
			'pricing',
			'recent_news',
			'searchbar',
			'shortcode',
			'skillbar',
			'social_links',
			'staff_carousel',
			'staff_grid',
			'staff_social',
			'star_rating',
			'steps',
			'terms_carousel',
			'terms_grid',
			'testimonials_carousel',
			'testimonials_grid',
			'testimonials_slider',
			'users_grid',

			// Dynamic post modules.
			'page_title',
			'post_comments',
			'post_content',
			'post_excerpt',
			'post_media',
			'post_meta',
			'post_next_prev',
			'post_series',
			'post_terms',
			'author_bio',
			'social_share',

			// Dynamic archive modules.
			'term_description',

			// Custom Grid items.
			'grid_item-post_excerpt',
			'grid_item-post_meta',
			'grid_item-post_terms',
			'grid_item-post_video',

			// Other
			'widget_modern_menu',
		];

		if ( \class_exists( '\acf_pro' ) ) {
			$shortcodes[] = 'acf_repeater';
		}

		if ( \function_exists( 'totaltheme_call_static' ) && \totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
			$shortcodes[] = 'dark_mode_toggle';
		}

		if ( \class_exists( 'Just_Events\Plugin', false ) ) {
			$shortcodes[] = 'just_events_date';
			$shortcodes[] = 'just_events_time';
		}

		if ( \class_exists( 'WooCommerce', false ) ) {
			$shortcodes[] = 'cart_link';
			$shortcodes[] = 'woocommerce_carousel';
			$shortcodes[] = 'woocommerce_loop_carousel';
			$shortcodes[] = 'woocommerce_content';
			$shortcodes[] = 'woocommerce_template';
			$shortcodes[] = 'woocommerce_notices';
		}

		if ( \class_exists( 'Tribe__Events__Main', false ) ) {
			$shortcodes[] = 'tribe_event_data';
		}

		if ( \class_exists( 'Disable_Elements_For_WPBakery_Page_Builder', false ) ) {
			$shortcodes = self::parse_disabled_shortcodes( $shortcodes );
		}

		$shortcodes = \apply_filters( 'vcex_builder_modules', $shortcodes ); // @deprecated 1.2.8

		return (array) \apply_filters( 'vcex_shortcodes_list', $shortcodes );
	}

	/**
	 * Registeres all shortcodes.
	 */
	public function register_all() {
		$shortcodes = $this->get_queue();
		if ( ! empty( $shortcodes ) ) {
			foreach ( $shortcodes as $key => $val ) {
				$file = '';
				if ( \is_array( $val ) ) {
					$condition = $val['condition'] ?? true;
					if ( $condition ) {
						if ( isset( $val['file'] ) ) {
							$file = $val['file'];
						} else {
							$file = self::SHORTCODE_CLASS_PATH . $key . '.php';
						}
					}
				} else {
					$file = self::SHORTCODE_CLASS_PATH . $val . '.php';
				}
				if ( $file && \file_exists( $file ) ) {
					require_once $file;
					$this->registered_shortcodes[] = $val;
				}
			}
		}
	}

	/**
	 * Retrieves all registered shortcodes.
	 */
	public function get_all_registered() {
		return $this->registered_shortcodes;
	}

	/**
	 * Removes disabled shortcodes.
	 */
	protected static function parse_disabled_shortcodes( array $shortcodes ): array {
		if ( is_admin() && isset( $_GET['page'] ) && 'vc-disable-elements' === $_GET['page'] ) {
			return $shortcodes;
		}
		$disabled_elements = \get_option( 'wpex_wpb_disabled_elements' );
		if ( $disabled_elements && \is_array( $disabled_elements ) ) {
			foreach ( $disabled_elements as $disabled_element ) {
				if ( \str_starts_with( $disabled_element, 'vcex_' ) ) {
					$disabled_element = \str_replace( 'vcex_', '', $disabled_element );
					$key = array_search( $disabled_element, $shortcodes );
					if ( false !== $key ) {
						unset( $shortcodes[ $key ] );
					}
				}
			}
		}
		return $shortcodes;
	}

	/**
	 * Inserts all shortcodes on the page to ensure there are no errors.
	 */
	public function run_test() {
		foreach ( $this->get_all_registered() as $shortcode ) {
			if ( ! \str_starts_with( $shortcode, 'wpex_' ) ) {
				$shortcode = "vcex_{$shortcode}";
			}
			print_r( do_shortcode( "<div class='container wpex-mb-50'>[{$shortcode}]</div>" ) );
		}
	}

}
