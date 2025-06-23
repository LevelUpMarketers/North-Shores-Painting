<?php

use TotalTheme\Header\Menu\Search;

/**
 * Header Search Overlay.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Header\Menu\Search' ) ) {
	return;
}

$legacy_typo = totaltheme_has_classic_styles();

?>

<div id="wpex-searchform-overlay" class="header-searchform-wrap wpex-fs-overlay wpex-fixed wpex-inset-0 wpex-z-modal wpex-duration-400 wpex-text-white wpex-invisible wpex-opacity-0">
	<button class="wpex-fs-overlay__close wpex-close wpex-unstyled-button wpex-block wpex-fixed wpex-top-0 wpex-right-0 wpex-mr-20 wpex-mt-20 <?php echo $legacy_typo ? 'wpex-text-5xl' : 'wpex-text-base'; ?>" aria-label="<?php esc_html_e( 'Close search', 'total' ); ?>"><?php
		echo totaltheme_get_icon(
			'material-close',
			'wpex-close__icon wpex-flex',
			$legacy_typo ? 'sm' : 'xl'
		);
	?></button>
	<div class="wpex-fs-overlay__inner wpex-inner wpex-scale wpex-relative wpex-top-50 wpex-max-w-100 wpex-mx-auto wpex-px-20<?php echo totaltheme_has_classic_styles() ? '' : ' wpex-text-2xl'; ?>">
		<?php wpex_hook_header_search_overlay_top(); ?>
		<div class="wpex-fs-overlay__title wpex-title wpex-hidden wpex-mb-15"><?php esc_html_e( 'Search', 'total' ); ?></div>
		<?php echo Search::get_form( [
			'style'        => 'overlay',
			'submit_text'  => '',
			'form_class'   => 'wpex-relative',
			'input_class'  => 'wpex-unstyled-input wpex-relative wpex-flex wpex-w-100 wpex-outline-0 wpex-font-light wpex-text-left wpex-leading-normal wpex-py-15 wpex-pl-20 wpex-pr-50 wpex-leading-none',
			'submit_class' => 'wpex-unstyled-button wpex-absolute wpex-top-50 wpex-right-0 wpex-mr-25 -wpex-translate-y-50',
			'autocomplete' => 'off',
		] ); ?>
		<?php wpex_hook_header_search_overlay_bottom(); ?>
	</div>
</div>
