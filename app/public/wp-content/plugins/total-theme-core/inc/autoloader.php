<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

final class Autoloader {

	/**
	 * Register our autoloader.
	 */
	public static function run(): void {
		\spl_autoload_register( [ self::class, 'autoload' ] );
	}

	/**
	 * Function registered as an autoloader which loads class files.
	 */
	private static function autoload( $class ): void {
		if ( 'WPEX_Meta_Factory' === $class ) {
			require_once TTC_PLUGIN_DIR_PATH . 'inc/lib/wpex-meta-factory/class-wpex-meta-factory.php';
		} elseif ( 0 === \strpos( $class, __NAMESPACE__ ) ) {
			$path = self::get_class_path( $class );
			if ( $path && \is_readable( $path ) ) {
				require $path;
			}
		}
	}

	/**
	 * Get the absolute path to a class file.
	 *
	 * @param string $class The classname.
	 * @return string|null $path The path to the class file.
	 */
	private static function get_class_path( string $class ): ?string {

		// Remove namespace.
		$class = \str_replace( __NAMESPACE__ . '\\', '', $class );

		// Lowercase.
		$class = \strtolower( $class );

		// Convert underscores to dashes.
		$class = \str_replace( '_', '-', $class );

		// Fix classnames with incorrect naming convention.
		$class = self::parse_class_filename( $class );

		// Return early if parsing returns null.
		if ( ! $class ) {
			return null;
		}

		// Convert backslash to correct directory separator.
		$class = \str_replace( '\\', DIRECTORY_SEPARATOR, $class ) . '.php';

		// Return final class path.
		$plugin_dir = \untrailingslashit( \TTC_PLUGIN_DIR_PATH );
		return "{$plugin_dir}/inc/{$class}";
	}

	/**
	 * Parses the class filename to fix classnames with incorrect naming convention.
	 *
	 * @param string $class The classname.
	 * @return string $parsed_class The parsed classname.
	 */
	private static function parse_class_filename( string $class ): string {
		if ( 'widgetbuilder' === $class ) {
			$class = 'widget-builder';
		}
		return $class;
	}

}

Autoloader::run();