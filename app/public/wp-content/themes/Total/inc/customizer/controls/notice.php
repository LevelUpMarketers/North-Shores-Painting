<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Notice Control.
 */
class Notice extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_notice';

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * The control template.
	 */
	public function content_template() { ?>
		<# if ( data.description ) { #>
		<div class="totaltheme-customize-notice">{{{ data.description }}}</div>
		<# } #>
	<?php }

}
