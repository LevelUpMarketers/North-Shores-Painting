<?php declare(strict_types=1);

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Easy Notifcation Bar Integration.
 */
final class Easy_Notification_bar {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Gravity_Forms.
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
		\add_filter( 'easy_notification_bar_wrap_class', [ $this, 'filter_wrap_class' ] );
		\add_action( 'totaltheme/inline_css', [ $this, 'add_inline_css' ] );
	}

	/**
	 * Modify the wrap class.
	 */
	public function filter_wrap_class( array $class ): array {
		if ( \is_array( $class ) && \in_array( 'easy-notification-bar--sticky', $class ) ) {
			$class[] = 'wpex-ls-offset';
			$class[] = 'wpex-sticky-el-offset';
		}
		return $class;
	}

	/**
	 * Modify the wrap class.
	 */
	public function add_inline_css(): void {
		if ( \wp_validate_boolean( \get_theme_mod( 'site_frame_border' ) ) ) {
			?>
			.easy-notification-bar--sticky {
				top: var(--wpex-site-frame-border-size);
			}
			<?php
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
