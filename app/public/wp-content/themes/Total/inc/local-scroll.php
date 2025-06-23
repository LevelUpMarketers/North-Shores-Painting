<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Local Scroll.
 */
class Local_Scroll {

	/**
	 * Holds list of enabled features.
	 */
	protected $enabled_features = null;

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of our class.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
			static::$instance->init_hooks();
			static::$instance->get_enabled_features();
		}
		return static::$instance;
	}

	/**
	 * Register hooks.
	 */
	public function init_hooks(): void {
		if ( $this->is_feature_enabled( 'scroll_to_hash' ) ) {
			\add_filter( 'get_comments_link', [ $this, 'modify_link_to_comments' ], 10, 2 );
			\add_filter( 'respond_link', [ $this, 'modify_link_to_comments' ], 10, 2 );
		}
		if ( $this->is_feature_enabled( 'easing' ) ) {
			\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_easing' ], 5 );
		}
	}

	/**
	 * Returns list of local scroll features.
	 */
	private function get_enabled_features(): array {
		if ( null === $this->enabled_features ) {
			$this->enabled_features = [];
			if ( \get_theme_mod( 'local_scroll_find_links', true ) ) {
				$this->enabled_features[] = 'find_links';
			}
			if ( $this->is_scroll_to_hash_enabled() ) {
				$this->enabled_features[] = 'scroll_to_hash';
			}
			if ( $this->is_highlight_enabled() ) {
				$this->enabled_features[] = 'highlight';
			}
			if ( $this->is_update_hash_enabled() ) {
				$this->enabled_features[] = 'update_hash';
			}
			if ( $this->is_easing_enabled() ) {
				$this->enabled_features[] = 'easing';
			}
		}
		return $this->enabled_features;
	}

	/**
	 * Checks if a certain feature is enabled.
	 */
	public function is_feature_enabled( $feature = '' ): bool {
		return \in_array( $feature, $this->get_enabled_features(), true );
	}

	/**
	 * Checks if scroll to hash is user enabled.
	 */
	private function is_scroll_to_hash_enabled(): bool {
		$check = \wp_validate_boolean( \get_theme_mod( 'scroll_to_hash', true ) );
		return (bool) \apply_filters( 'wpex_has_local_scroll_on_load', $check );
	}

	/**
	 * Checks if local scroll highlighting is user enabled.
	 */
	private function is_highlight_enabled(): bool {
		$check = \wp_validate_boolean( \get_theme_mod( 'local_scroll_highlight', true ) );
		return (bool) \apply_filters( 'wpex_has_local_scroll_menu_highlight', $check );
	}

	/**
	 * Checks if scroll to hash is user enabled.
	 */
	private function is_update_hash_enabled(): bool {
		$check = \wp_validate_boolean( \get_theme_mod( 'local_scroll_update_hash', true ) );
		return (bool) \apply_filters( 'wpex_has_local_scroll_hash_update', $check );
	}

	/**
	 * Checks if easing is enabled.
	 */
	private function is_easing_enabled(): bool {
		return (bool) \wp_validate_boolean( \get_theme_mod( 'scroll_to_easing', false ) );
	}

	/**
	 * Returns time to wait when scrolling to a local section on load.
	 */
	public function get_onload_timeout_time(): int {
		$timeout = \get_theme_mod( 'scroll_to_hash_timeout' ) ?: 500;
		$timeout = (int) \apply_filters( 'wpex_local_scroll_on_load_timeout', $timeout );
		return absint( $timeout );
	}

	/**
	 * Returns local scroll targets.
	 */
	public function get_trigger_targets(): string {
		$targets = 'li.local-scroll a, a.local-scroll, .local-scroll-link, .local-scroll-link > a,.sidr-class-local-scroll-link,li.sidr-class-local-scroll > span > a,li.sidr-class-local-scroll > a';
		return (string) \apply_filters( 'wpex_local_scroll_targets', $targets );
	}

	/**
	 * Returns local scroll speed function.
	 */
	public function get_scroll_to_duration(): int {
		$speed = \get_theme_mod( 'local_scroll_speed' );
		$speed = ( $speed || '0' === $speed ) ? absint( $speed ) : 1000;
		$speed = \apply_filters( 'wpex_local_scroll_speed', $speed );
		return \absint( $speed );
	}

	/**
	 * Returns local scroll behavior.
	 */
	public function get_scroll_to_behavior(): string {
		$behavior = (string) \get_theme_mod( 'local_scroll_behaviour' );
		if ( ! in_array( $behavior, [ 'smooth', 'instant', 'auto' ], true ) ) {
			$behavior = 'smooth';
		}
		return (string) \apply_filters( 'wpex_local_scroll_behavior', $behavior );
	}

	/**
	 * Returns easing function.
	 */
	public function get_easing_function(): string {
		return (string) \apply_filters( 'wpex_local_scroll_easing', 'easeInOutExpo' );
	}

	/**
	 * Returns l10n for wp_localize_script.
	 */
	public function get_l10n(): array {
		$l10n = [
			'scrollToHash'          => $this->is_feature_enabled( 'scroll_to_hash' ),
			'localScrollFindLinks'  => $this->is_feature_enabled( 'find_links' ),
			'localScrollHighlight'  => $this->is_feature_enabled( 'highlight' ),
			'localScrollUpdateHash' => $this->is_feature_enabled( 'update_hash' ),
			'scrollToHashTimeout'   => $this->get_onload_timeout_time(),
			'localScrollTargets'    => $this->get_trigger_targets(),
			'scrollToBehavior'      => $this->get_scroll_to_behavior(),
		];
		if ( $this->is_feature_enabled( 'easing' ) ) {
			$l10n['localScrollEasing'] = $this->get_easing_function();
			$l10n['localScrollSpeed']  = $this->get_scroll_to_duration();
		}
		if ( $offset = \get_theme_mod( 'local_scroll_offset' ) ) {
			$l10n['localScrollExtraOffset'] = \floatval( $offset );
		}
		return $l10n;
	}

	/**
	 * Modifies the comments and respond links.
	 */
	public function modify_link_to_comments( $comments_link, $post ) {
		if ( ! is_singular() || ( isset( $post->ID ) && $post->ID !== get_the_ID() ) || \totaltheme_is_card() ) {
			$permalink = \esc_url( \get_permalink( $post ) );
			$comments_link = "{$permalink}#_comments";
		}
		return $comments_link;
	}

	/**
	 * Enqueue easing scripts.
	 */
	public function enqueue_easing() {
		\wp_enqueue_script(
			'easing',
			\totaltheme_get_js_file( 'vendor/jquery.easing' ),
			[ 'jquery' ],
			'1.3.2',
			true
		);
	}

}
