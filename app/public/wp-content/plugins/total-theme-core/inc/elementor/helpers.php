<?php

namespace TotalThemeCore\Elementor;

use Elementor\Plugin as Elementor;

\defined( 'ABSPATH' ) || exit;

/**
 * Elementor helper functions.
 */
class Helpers {

    /**
	 * Static-only class.
	 */
	private function __construct() {}

    /**
     * Check if currently editing the page using Elementor.
     */
    public static function is_edit_mode(): bool {
        if ( ! \class_exists( '\Elementor\Plugin' ) ) {
            return false;
        }

        // This method is not always reliable for some reason.
        if ( \is_object( Elementor::$instance->preview )
            && \is_callable( [ Elementor::$instance->preview, 'is_preview_mode' ] )
            && Elementor::$instance->preview->is_preview_mode()
        ) {
            return true;
        }

        // Additional check incase the previous fails.
        if ( \is_admin() && isset( $_POST['action'] ) && 'elementor_ajax' === $_POST['action'] ) {
            return true;
        }

        return false;
    }

    /**
     * Check if currently editing a specific post type with Elementor.
     */
    public static function is_cpt_in_frontend_mode( string $post_type = '' ): bool {
       return ( did_action( 'elementor/loaded' ) && isset( $_GET['elementor-preview'] ) && isset( $_GET[ $post_type ] ) );
    }

    /**
     * Returns Elementor color by ID.
     */
    public static function get_global_color( $id = 0 ) {
        static $colors = null;
        if ( null === $colors ) {
            $colors = [];
            if ( is_callable( 'Elementor\Plugin::instance' )
                && isset( Elementor::instance()->kits_manager )
                && is_callable( [ Elementor::instance()->kits_manager, 'get_active_kit_for_frontend' ] )
                && $kit = Elementor::instance()->kits_manager->get_active_kit_for_frontend()
            ) {
                if ( is_callable( [ $kit, 'get_settings_for_display' ] ) ) {
                    $system_colors = $kit->get_settings_for_display( 'system_colors' );
                    if ( is_array( $system_colors ) ) {
                        foreach ( $system_colors as $color ) {
                            if ( isset( $color['_id'] ) && isset( $color['color'] ) ) {
                                $colors[ $color['_id'] ] = "var(--e-global-color-{$color['_id']}, {$color['color']} )";
                            }
                        }
                    }
                    $custom_colors = $kit->get_settings_for_display( 'custom_colors' );
                    if ( is_array( $custom_colors ) ) {
                        foreach ( $custom_colors as $color ) {
                            if ( isset( $color['_id'] ) && isset( $color['color'] ) ) {
                                $colors[ $color['_id'] ] = "var(--e-global-color-{$color['_id']}, {$color['color']} )";
                            }
                        }
                    }
                }
            }
        }
        return $colors[ $id ] ?? '';
    }

}
