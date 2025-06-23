<?php

defined( 'ABSPATH' ) || exit;

/**
 * Call a static class method.
 */
function totalthemecore_call_static( string $class_name, string $method, ...$params ) {
	if ( ! str_starts_with( $class_name, 'TotalThemeCore' ) ) {
		$class_name = 'TotalThemeCore\\' . $class_name;
	}
	if ( class_exists( $class_name ) ) {
		if ( 'init' === $method && ! method_exists( $class_name, 'init' ) ) {
			$method = 'instance';
		}
		if ( 'instance' === $method && ! method_exists( $class_name, 'instance' ) ) {
			return new $class_name( ...$params );
		}
		if ( is_callable( $class_name, $method ) ) {
			return $class_name::$method( ...$params );
		}
	}
}

/**
 * Call a non static class method.
 */
function totalthemecore_call_non_static( string $class_name, string $method, ...$params ) {
	$instance = totalthemecore_call_static( $class_name, 'instance' );
	if ( $instance && method_exists( $instance, $method ) ) {
		return $instance->$method( ...$params );
	}
}

/**
 * Initialize and return class.
 */
function totalthemecore_init_class( string $class_name, ...$args ) {
	return totalthemecore_call_static( $class_name, 'init', ...$args );
}

/**
 * Return class instance.
 */
function totalthemecore_get_instance_of( string $class_name ) {
	return totalthemecore_call_static( $class_name, 'instance' );
}

/**
 * Fallback for str_contains().
 */
if ( ! function_exists( 'str_contains' ) ) {
    function str_contains( string $haystack, string $needle ): bool {
        return '' === $needle || false !== strpos( $haystack, $needle );
    }
}

/**
 * Fallback for str_starts_with().
 */
if ( ! function_exists( 'str_starts_with' ) ) {
	function str_starts_with( string $haystack, string $needle ): bool {
		return ( 0 === strpos( $haystack, $needle ) );
	}
}

/**
 * Fallback for str_ends_with().
 */
if ( ! function_exists( 'str_ends_with' ) ) {
	function str_ends_with( string $haystack, string $needle ): bool {
		if ( '' === $haystack && '' !== $needle ) {
			return false;
		}
		$len = strlen( $needle );
		return 0 === substr_compare( $haystack, $needle, -$len, $len );
	}
}

/**
 * Validate boolean.
 */
if ( ! function_exists( 'ttc_validate_boolean' ) ) {
	function ttc_validate_boolean( $var ): bool {
		if ( is_bool( $var ) ) {
			return $var;
	    }
		if ( is_string( $var ) && ( 'false' === strtolower( $var ) || 'off' === strtolower( $var ) ) ) {
			return false;
		}
		return (bool) $var;
	}
}

/**
 * Sanitize data.
 */
if ( ! function_exists( 'ttc_sanitize_data' ) ) {
	function ttc_sanitize_data( $data = '', $type = '' ) {
		return function_exists( 'wpex_sanitize_data' ) ? wpex_sanitize_data( $data, $type ) : wp_strip_all_tags( $data );
	}
}

/**
 * Get CSS file.
 */
function totalthemecore_get_css_file( string $file ): string {
	return TTC_PLUGIN_DIR_URL . "assets/css/{$file}.min.css";
}

/**
 * Get JS file.
 */
function totalthemecore_get_js_file( string $file ): string {
	return TTC_PLUGIN_DIR_URL . "assets/js/{$file}.min.js";
}
