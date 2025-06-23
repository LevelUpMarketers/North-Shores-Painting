<?php declare(strict_types=1);

namespace TotalTheme\Integration\WooCommerce\Single;

defined( 'ABSPATH' ) || exit;

/**
 * Adds a flex container around the single product gallery and content.
 */
class Flex_Container {

	/**
	 * Check if vertical align is enabled.
	 */
	public $vertical_align = null;

	/**
	 * Check if reversed layout is enabled.
	 */
	public $reverse_layout = null;

	/**
	 * Check if sticky gallery is enabled.
	 */
	public $sticky_gallery = null;

	/**
	 * Check if sticky summary is enabled.
	 */
	public $sticky_summary = null;

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Thumbnails.
	 */
	public static function instance() {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init_hooks();
		}

		return self::$instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	public function init_hooks(): void {
		\add_action( 'woocommerce_before_single_product_summary', [ $this, 'open_container' ], $this->open_priority() );
		\add_action( 'woocommerce_after_single_product_summary', [ $this, 'close_container' ], $this->close_priority() );
	}

	/**
	 * Check if the flex container is enabled.
	 */
	public function is_enabled(): bool {
		if ( null === $this->vertical_align ) {
			$this->vertical_align = \get_theme_mod( 'woo_single_product_vertical_align' );
			$this->reverse_layout = \get_theme_mod( 'woo_single_product_layout_reverse' );
			$this->sticky_gallery = \get_theme_mod( 'woo_single_product_sticky_gallery' );
			$this->sticky_summary = \get_theme_mod( 'woo_single_product_sticky_summary' );
		}
		return ( $this->vertical_align || $this->reverse_layout || $this->sticky_gallery || $this->sticky_summary );
	}

	/**
	 * Open container.
	 */
	public function open_container(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$bk         = $this->get_breakpoint();
		$class      = 'wpex-woocommerce-product-flex-container';
		$util_class = "wpex-{$bk}-flex wpex-{$bk}-flex-wrap wpex-{$bk}-justify-between";

		if ( wp_validate_boolean( $this->vertical_align ) ) {
			$util_class .=  " wpex-{$bk}-items-center";
		} else {
			$util_class .=  " wpex-{$bk}-items-start";
		}

		if ( wp_validate_boolean( $this->reverse_layout ) ) {
			$class .=  ' wpex-woocommerce-product-flex-container--reverse';
			$util_class .= " wpex-{$bk}-flex-row-reverse";
		}

		if ( wp_validate_boolean( $this->sticky_gallery ) ) {
			$class .= ' wpex-woocommerce-has-sticky-gallery';
		}

		if ( wp_validate_boolean( $this->sticky_summary ) ) {
			$class .= ' wpex-woocommerce-has-sticky-summary';
		}

		echo '<div class="' . esc_attr( "{$class} {$util_class}" ) . '">';
	}

	/**
	 * Close Container.
	 */
	public function close_container(): void {
		if ( $this->is_enabled() ) {
			echo '</div>';
		}
	}

	/**
	 * Container breakpoint.
	 */
	protected function get_breakpoint(): string {
		return (string) \apply_filters( 'totaltheme/integration/woocommerce/single/flex_container/breakpoint', 'md' );
	}

	/**
	 * Open Priority.
	 */
	protected function open_priority(): int {
		return (int) \apply_filters( 'totaltheme/integration/woocommerce/single/flex_container/open_priority', 0 );
	}

	/**
	 * Close Priority.
	 */
	protected function close_priority(): int {
		return (int) \apply_filters( 'totaltheme/integration/woocommerce/single/flex_container/close_priority', 0 );
	}

}
