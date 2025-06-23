<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

\defined( 'ABSPATH' ) || exit;

/**
 * Customizer Heading Control.
 */

class Heading extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_heading';

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * The control template.
	 */
	public function content_template() {
		$admin_color = \sanitize_html_class( (string) \get_user_option( 'admin_color' ) );
		$admin_color = $admin_color ? " wpex-customizer-heading--{$admin_color}" : '';
		?>
		<# if ( data.label ) { #>
			<span class="wpex-customizer-heading<?php echo \esc_attr( $admin_color ); ?>">{{ data.label }}</span>
		<# } #>
	<?php }

}
