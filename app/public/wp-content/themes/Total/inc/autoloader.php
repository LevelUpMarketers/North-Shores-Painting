<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Register theme autoloader.
 */
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
	private static function autoload( string $class ): void {
		if ( 'WPEX_Card' === $class ) {
			require_once \WPEX_INC_DIR . 'lib/wpex-card.php';
		} elseif ( 'WPEX_Breadcrumbs' === $class ) {
			require_once \WPEX_INC_DIR . 'lib/wpex-breadcrumbs.php';
		} elseif ( self::str_starts_with( $class, __NAMESPACE__ )
			&& ! self::str_starts_with( $class, 'TotalThemeCore' )
			&& ! self::str_starts_with( $class, 'TotalTheme\Demo_Importer' )
		) {
			$path = self::get_class_path( $class );
			if ( $path && \is_readable( $path ) ) {
				require $path;
			}
		}
	}

	/**
	 * Get the absolute path to a class file.
	 */
	private static function get_class_path( string $class ): ?string {

		// Remove namespace.
		$class = \str_replace( __NAMESPACE__ . '\\', '', $class );

		// Lowercase.
		$class = \strtolower( $class );

		// Convert underscores to dashes.
		$class = \str_replace( '_', '-', $class );

		// Return early if parsing returns null.
		if ( ! $class ) {
			return null;
		}

		// Convert backslash to correct directory separator.
		$class_name = \str_replace( '\\', DIRECTORY_SEPARATOR, $class );
		$class_file = "{$class_name}.php";

		// Return final class path.
		$theme_dir = \untrailingslashit( \WPEX_THEME_DIR );
		return "{$theme_dir}/inc/{$class_file}";
	}

	/**
	 * Polyfill for the PHP 8.0 str_starts_with function.
	 *
	 * @param string $haystack The string to check.
	 * @param string $needed The string to search form.
	 *
	 * @return bool $result Returns true if the needled was found in the haystay false otherwise.
	 */
	private static function str_starts_with( string $haystack, string $needle ): bool {
		if ( \function_exists( 'str_starts_with' ) ) {
			return \str_starts_with( $haystack, $needle );
		}
		return ( 0 === \strpos( $haystack, $needle ) );
	}

}

Autoloader::run();
