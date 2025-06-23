<?php

namespace TotalThemeCore\WPBakery\Autocomplete;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery AutoComplete => User Roles.
 */
final class User_Roles {

	public static function callback( $search_string ) {
		$roles = [];
		$get_roles = \get_editable_roles();
		if ( $get_roles ) {
			foreach ( $get_roles as $role_name => $role_info ) {
				if ( false !== \stripos( $role_name, $search_string ) ) {
					$roles[] = [
						'label' => $role_name,
						'value' => $role_name,
					];
				}
			}
		}
		return $roles;
	}

	public static function render( $data ) {
		return [
			'label' => $data['value'],
			'value' => $data['value'],
		];
	}

}
