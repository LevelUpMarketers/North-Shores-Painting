<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer HR Control.
 */
class Hr extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_hr';

	/**
	 * The control template.
	 */
	public function content_template() {
		echo '<hr>';
	}

}
