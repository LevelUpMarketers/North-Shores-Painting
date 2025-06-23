<?php

namespace TotalTheme\Integration;

use Tribe__Events__Community__Main;
use Tribe__Events__Main;

\defined( 'ABSPATH' ) || exit;

/**
 * Configure the Tribe Events Plugin.
 */
final class Tribe_Events {

	/**
	 * Current plugin version.
	 */
	public $plugin_version;

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Tribe_Events.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 */
	public function define_constants() {
		\define( 'WPEX_TRIBE_EVENTS_DIR', WPEX_INC_DIR . 'integration/tribe-events/' );
		\define( 'WPEX_TRIBE_EVENTS_COMMUNITY_ACTIVE', \class_exists( 'Tribe__Events__Community__Main' ) );
	}

	/**
	 * Hook into actions and filters.
	 */
	public function init_hooks() {
		if ( \get_option( 'wpex_tribe_events_accent_color_set' ) ) {
			\delete_option( 'wpex_tribe_events_accent_color_set' ); // remove deprecated setting.
		}

		if ( is_customize_preview() ) {
			\add_filter( 'wpex_customizer_panels', array( $this, 'customizer_settings' ) );
		}

		\add_filter( 'wpex_register_sidebars_array', array( $this, 'register_events_sidebar' ), 10 );

		if ( $this->has_theme_styles() ) {
			if ( \wpex_is_request( 'frontend' ) ) {
				\add_action( 'wp_enqueue_scripts', array( $this, 'load_custom_stylesheet' ), 10 );
			}
		}

		if ( \wpex_is_request( 'admin' ) ) {
			\add_filter( 'wpex_main_metaboxes_post_types', array( $this, 'metaboxes' ), 10 );
		}

		if ( \wpex_is_request( 'frontend' ) ) {
			\add_filter( 'body_class', array( $this, 'body_class' ), 10 );
			\add_filter( 'wpex_current_post_id', array( $this, 'set_events_page_id' ), 10 );
			\add_filter( 'wpex_post_layout_class', array( $this, 'layouts' ), 10 );
			\add_filter( 'wpex_page_header_title_args', array( $this, 'page_header_title_args' ), 10 );
			\add_filter( 'totaltheme/page/header/subheading', array( $this, 'alter_page_header_subheading' ), 10 );
			\add_filter( 'wpex_get_sidebar', array( $this, 'display_events_sidebar' ), 10 );
			\add_filter( 'wpex_has_next_prev', array( $this, 'next_prev' ), 10, 2 );

			if ( \get_theme_mod( 'tribe_events_main_page' ) && false === \wpex_is_request( 'admin' ) ) {
				\add_filter( 'template_redirect', array( $this, 'redirect_events_page_to_events_archive' ) );
			}

			if ( WPEX_TRIBE_EVENTS_COMMUNITY_ACTIVE ) {
				\add_filter( 'get_edit_post_link', array( $this, 'get_edit_post_link' ), 40 );
			}

		}

	}

	/**
	 * Check if the theme should add custom theme styles.
	 */
	public function has_theme_styles() {
		return \get_theme_mod( 'tribe_events_total_styles', true );
	}

	/**
	 * Get plugin version.
	 */
	public function get_plugin_version() {
		if ( ! $this->plugin_version ) {
			if ( \class_exists( 'Tribe__Events__Main' ) && defined( 'Tribe__Events__Main::VERSION' ) ) {
				$this->plugin_version = Tribe__Events__Main::VERSION;
			}
		}
		return $this->plugin_version;
	}

	/**
	 * Filter body classes.
	 */
	public function body_class( $classes ) {
		if ( \get_theme_mod( 'tribe_events_page_header_details', true )
			&& \is_singular( 'tribe_events' )
			&& \totaltheme_call_static( 'Page\Header', 'is_enabled' )
		) {
			$classes[] = 'tribe-page-header-details';
		}
		return $classes;
	}

	/**
	 * Load custom CSS file for tweaks.
	 */
	public function load_custom_stylesheet() {
		if ( $this->has_theme_styles() ) {
			\wp_enqueue_style(
				'wpex-the-events-calendar', // legacy name
				\totaltheme_get_css_file( 'frontend/tribe-events' )
			);
		}
	}

	/**
	 * Set page id for main events page.
	 */
	public function set_events_page_id( $id ) {
		if ( \is_post_type_archive( 'tribe_events' ) && $page_id = \wpex_get_tribe_events_main_page_id() ) {
			return $page_id;
		}
		return $id;
	}

	/**
	 * Alter the post layouts for all events.
	 */
	public function layouts( $class ) {
		if ( self::is_event_page() ) {
			if ( \is_singular( 'tribe_events' ) ) {
				$class = \get_theme_mod( 'tribe_events_single_layout', 'full-width' );
			} else {
				$class = \get_theme_mod( 'tribe_events_archive_layout', 'full-width' );
			}
		}
		if ( \function_exists( 'tribe_is_community_edit_event_page' )
			&& \function_exists( 'tribe_is_community_my_events_page' )
		) {
			if ( \tribe_is_community_edit_event_page() || \tribe_is_community_my_events_page() ) {
				$class = \get_theme_mod( 'tribe_events_community_my_events_layout', 'full-width' );
			}
		}
		return $class;
	}

	/**
	 * Add the Page Settings metabox to the events calendar.
	 */
	public function metaboxes( $types ) {
		$types['tribe_events'] = 'tribe_events';
		return $types;
	}

	/**
	 * Alter the main page header title text for tribe events.
	 */
	public function page_header_title_args( $args ) {
		if ( ! self::is_event_page() ) {
			return $args;
		}

		if ( \tribe_is_event_category() ) {
			$main_page = \wpex_get_tribe_events_main_page_id();
			$args['string'] = $main_page ? \get_the_title( $main_page ) : \esc_html__( 'Events Calendar', 'total' );
		} elseif ( \tribe_is_month() ) {
			$post_id = \wpex_get_current_post_id();
			$args['string'] = $post_id ? \get_the_title( $post_id ) : \esc_html__( 'Events Calendar', 'total' );
		} elseif ( \tribe_is_event() && ! \tribe_is_day() && ! \is_single() ) {
			$args['string'] = \esc_html__( 'Events List', 'total' );
		} elseif ( \tribe_is_day() ) {
			$args['string'] = \esc_html__( 'Single Day Events', 'total' );
		} elseif ( \is_singular( 'tribe_events' ) ) {
			if ( \get_theme_mod( 'tribe_events_page_header_details', true ) ) {
				$args['html_tag'] = 'h1';
				$args['string']   = \single_post_title( '', false );
			} else {
				$args['string'] = \get_post_type_object( 'tribe_events' )->labels->name;
			}
		}

		return $args;
	}

	/**
	 * Alter the post subheading for events.
	 */
	public function alter_page_header_subheading( $subheading ) {
		if ( is_singular( 'tribe_events' ) && \get_theme_mod( 'tribe_events_page_header_details', true ) ) {
			$subheading = '<div class="page-subheading-extra wpex-mt-5">';
				$schedule = \tribe_events_event_schedule_details( \wpex_get_current_post_id() );
				if ( $schedule ) {
					$subheading .= '<div class="schedule">';
						if ( 'font' === \totaltheme_call_static( 'Theme_Icons', 'get_format' ) ) {
							$subheading .= '[ticon icon="calendar-o" class="wpex-mr-10"]'; // use shortcode to prevent issues with SVG's and wp_kses_post.
						}
						$subheading .= $schedule;
					$subheading .= '</div>';
				}
			$cost = \tribe_get_cost( null, true );
			if ( $cost ) {
				$subheading .= '<div class="cost">';
					if ( 'font' === \totaltheme_call_static( 'Theme_Icons', 'get_format' ) ) {
						$subheading .= '[ticon icon="money" class="wpex-mr-10"]';
					} else {
						$subheading .= '<span class="wpex-bold">' . \esc_html__( 'Event Cost: ', 'total' ) . '</span>';
					}
					$subheading .= \wp_kses_post( $cost );
				$subheading .= '</div>';
			}
			$subheading .= '</div>';
		}
		return $subheading;
	}

	/**
	 * Register a new events sidebar area.
	 */
	public function register_events_sidebar( $sidebars ) {
		$sidebars['tribe_events_sidebar'] = \esc_html__( 'Events Sidebar', 'total' );
		return $sidebars;
	}

	/**
	 * Alter main sidebar to display events sidebar.
	 */
	public function display_events_sidebar( $sidebar ) {
		if ( self::is_event_page() && \is_active_sidebar( 'tribe_events_sidebar' ) ) {
			$sidebar = 'tribe_events_sidebar';
		}
		return $sidebar;
	}

	/**
	 * Disables the next/previous links for tribe events because they already have some.
	 */
	public function next_prev( $return, $post_type ) {
		if ( 'tribe_events' === $post_type ) {
			return false;
		}
		return $return;
	}

	/**
	 * Adds Customizer settings for Tribe Events.
	 */
	public function customizer_settings( $panels ) {
		$branding = ( $branding = \wpex_get_theme_branding() ) ? ' (' . $branding . ')' : '';
		$panels['tribe_events'] = [
			'title'      => \esc_html__( 'Events Calendar', 'total' ) . $branding,
			'is_section' => true,
			'settings'   => WPEX_TRIBE_EVENTS_DIR . 'customizer-settings.php',
			'icon'       => '\f145',
		];
		return $panels;
	}

	/**
	 * Redirects.
	 *
	 * @todo Can we remove this now?
	 */
	public function redirect_events_page_to_events_archive() {
		if ( $page_id = \get_theme_mod( 'tribe_events_main_page' ) ) {

			// Redirect on page as long as it's not posts page to prevent endless loop
			if ( \is_page( $page_id ) && $page_id != \get_option( 'page_for_posts' ) ) {
				$redirect = \get_post_type_archive_link( 'tribe_events' ) ?: \home_url( '/' );
				\wp_redirect( \esc_url( $redirect ), 301 );
				exit();
			}

		}
	}

	/**
	 * Edit post link.
	 */
	public function get_edit_post_link( $url ) {
		if ( \is_singular( 'tribe_events' ) && \class_exists( 'Tribe__Events__Community__Main' ) ) {
			$url = \esc_url( Tribe__Events__Community__Main::instance()->getUrl( 'edit', \get_the_ID(), null, Tribe__Events__Main::POSTTYPE ) );
		}
		return $url;
	}

	/**
	 * Check if we are currently on a tribe page.
	 */
	public static function is_event_page() {
		if ( \is_search() || \wpex_is_blog_query() || \is_singular( 'post' ) ) {
			return false; // fixes some bugs with the plugin.
		}

		if ( \is_singular( 'tribe_events' )
			|| ( \function_exists( 'tribe_is_event' ) && \tribe_is_event() )
			|| ( \function_exists( 'tribe_is_list_view' ) && \tribe_is_list_view() )
		) {
			return true;
		}

		if ( \function_exists( 'tec_is_view' ) ) {
			return \tec_is_view();
		} else {
			return \tribe_is_view(); // deprecated in 6.7.0
		}

		if ( \is_archive() ) {
			if ( \is_post_type_archive( 'tribe_events' )
				|| ( \function_exists( 'tribe_is_month' ) && \tribe_is_month() )
				|| ( \function_exists( 'tribe_is_day' ) && \tribe_is_day() )
				|| ( \function_exists( 'tribe_is_event_category' ) && \tribe_is_event_category() )
			) {
				return true;
			}
		}
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
