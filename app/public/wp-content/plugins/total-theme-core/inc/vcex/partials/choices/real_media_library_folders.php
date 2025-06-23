<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wp_rml_root_childs' ) ) {
	return [];
}

if ( ! function_exists( 'vcex_real_media_library_folders_array' ) ) {
	function vcex_real_media_library_folders_array( bool $include_empty = true, array $rec_childs = [], array &$folders = [] ): array {
		$get_folders = $rec_childs ?: wp_rml_root_childs();
		if ( $get_folders ) {
			if ( defined( 'RML_VERSION' ) && version_compare( RML_VERSION, '2.8' ) <= 0 ) {
				foreach ( $get_folders as $parent_folder ) {
					$folders[$parent_folder->id] = $parent_folder->name;
					if ( ! empty( $parent_folder->children ) ) {
						vcex_real_media_library_folders_array( false, $parent_folder->children, $folders );
					}
				}
			} else {
				foreach ( $get_folders as $parent_folder ) {
					$folders[$parent_folder->getId()] = $parent_folder->getName();
					if ( is_callable( [ $parent_folder, 'getChildrens' ] ) ) {
						$childs = $parent_folder->getChildrens();
					} elseif ( is_callable( [ $parent_folder, 'getChildren' ] ) ) {
						$childs = $parent_folder->getChildren();
					}
					if ( ! empty( $childs ) ) {
						vcex_real_media_library_folders_array( false, $childs, $folders );
					}
				}
			}
		}
		return $folders;
	}
}

// Set default folder to the select option.
$folders[''] = esc_html__( '— Select —', 'total-theme-core' );

return (array) vcex_real_media_library_folders_array( true, [], $folders );