<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * bbPress Integration.
 */
final class bbPress {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of bbPress.
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
		\add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );

		if ( \get_theme_mod( 'bbpress_custom_sidebar', true ) ) {
			\add_filter( 'wpex_register_sidebars_array', [ $this, 'register_sidebar' ] );
		}

		\add_filter( 'wpex_get_sidebar', [ $this, 'display_sidebar' ] );
		\add_filter( 'wpex_single_blocks', [ $this, 'page_blocks' ], 99, 2 );
		\add_filter( 'wpex_has_next_prev', [ $this, 'next_prev' ], 99 );
		\add_filter( 'wpex_post_layout_class', [ $this, 'layouts' ] );
		\add_filter( 'wpex_title', [ $this, 'title' ] );
		\add_filter( 'post_class', [ $this, 'post_class' ] );
		\add_filter( 'wpex_breadcrumbs_trail', [ $this, 'breadcrumbs' ] );
		\add_filter( 'wpex_customizer_panels', [ $this, 'add_customizer_panel' ] );
	}

	/**
	 * Load custom CSS for bbPress.
	 *
	 * Must load globally because of Widgets.
	 */
	public function scripts() {
		\wp_enqueue_style(
			'wpex-bbpress',
			\totaltheme_get_css_file( 'frontend/bbpress' ),
			[ 'bbp-default' ],
			\WPEX_THEME_VERSION
		);
	}

	/**
	 * Registers a bbpress_sidebar widget area.
	 */
	public function register_sidebar( $sidebars ) {
		$sidebars['bbpress_sidebar'] = \esc_html__( 'bbPress Sidebar', 'total' );
		return $sidebars;
	}

	/**
	 * Alter main sidebar to display bbpress_sidebar sidebar.
	 */
	public function display_sidebar( $sidebar ) {
		if ( \function_exists( 'is_bbpress' )
			&& \is_bbpress()
			&& \get_theme_mod( 'bbpress_custom_sidebar', true )
		) {
			$sidebar = 'bbpress_sidebar';
		}
		return $sidebar;
	}

	/**
	 * Alter page blocks to remove elements that aren't needed for bbPress.
	 */
	public function page_blocks( $blocks, $type ) {
		if ( \function_exists( 'is_bbpress' ) && \is_bbpress() ) {
			return array( 'content' ); // Only content needed
		}
		return $blocks;
	}

	/**
	 * Disable next/prev for bbPress.
	 */
	public function next_prev( $check ) {
		if ( \function_exists( 'is_bbpress' ) && \is_bbpress() ) {
			return false;
		}
		return $check;
	}

	/**
	 * Set layouts.
	 */
	public function layouts( $layout ) {
		if ( \bbp_is_forum_archive() ) {
			$layout = \get_theme_mod( 'bbpress_forums_layout' );
		}

		if ( \bbp_is_single_forum() ) {
			$layout = \get_theme_mod( 'bbpress_single_forum_layout' );
		}

		if ( \bbp_is_topic_archive() ) {
			$layout = \get_theme_mod( 'bbpress_topics_layout' );
		}

		if ( \bbp_is_single_topic()
			|| \bbp_is_topic_edit()
			|| \bbp_is_topic_merge()
			|| \bbp_is_topic_split()
		) {
			$layout = \get_theme_mod( 'bbpress_single_topic_layout' );
		}

		if ( \bbp_is_single_user() ) {
			$layout = \get_theme_mod( 'bbpress_user_layout', 'full-width' );
		}

		return $layout;
	}

	/**
	 * Fix page header title.
	 */
	public function title( $title ) {
		if ( \bbp_is_single_forum()
			|| \bbp_is_single_topic()
			|| \bbp_is_topic_edit()
			|| \bbp_is_topic_merge()
			|| \bbp_is_topic_split()
		) {
			$title = \get_the_title();
		}
		return $title;
	}

	/**
	 * Add custom post classes.
	 */
	public function post_class( $classes ) {
		if ( 'forum' == \get_post_type() ) {
			$count = \bbp_show_lead_topic() ? \bbp_get_forum_reply_count() : \bbp_get_forum_post_count();
			$count = ( 0 == $count ) ? 'no' : $count;
			$classes[] = "{$count}-replies";
		}
		return $classes;
	}

	/**
	 * Fix Breadcrumbs trail.
	 */
	public function breadcrumbs( $trail ) {
		if ( ! \class_exists( '\WPEX_Breadcrumbs' ) ) {
			return $trail;
		}

		// Set correct archive for single topic
		if ( \bbp_is_single_topic() || \bbp_is_single_user() ) {
			$obj = \get_post_type_object( 'forum' );
			if ( $obj && $forums_link = \get_post_type_archive_link( 'forum' ) ) {
				$trail['post_type_archive'] = \WPEX_Breadcrumbs::get_crumb_html( $obj->labels->name, $forums_link, 'trail-forums' );
			}
			/*
			@deprecated in 4.6 - already included in main crumbs
			if ( bbp_is_single_topic() ) {
				$forum = wp_get_post_parent_id( get_the_ID() );
				if ( $forum ) {
					$text = get_the_title( $forum );
					$link = get_permalink( $forum );
					$trail['pre_trail_end'] = \WPEX_Breadcrumbs::get_crumb_html( $text, $link, 'trail-forums' );
				}
			}*/
		}

		// Set correct end_trail for user
		if ( \bbp_is_single_user() ) {
			$trail['trail_end'] = \get_the_title();
		}

		// Search results
		if ( \bbp_is_search_results() ) {
			$obj = \get_post_type_object( 'forum' );
			if ( $obj && $forums_link = \get_post_type_archive_link( 'forum' ) ) {
				$trail['post_type_archive'] = \WPEX_Breadcrumbs::get_crumb_html( $obj->labels->name, $forums_link, 'trail-forums' );
			}
			$trail['trail_end'] = \esc_html__( 'Search Results', 'total' );
		}

		return $trail;
	}

	/**
	 * Adds new Customizer Panel for bbPress.
	 */
	public function add_customizer_panel( $panels ) {
		$panels['bbpress'] = [
			'title'      => \esc_html__( 'bbPress', 'total' ),
			'is_section' => true,
			'icon'       => '\f449',
			'settings'   => WPEX_INC_DIR . 'integration/bbpress/customizer-settings.php',
		];
		return $panels;
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
