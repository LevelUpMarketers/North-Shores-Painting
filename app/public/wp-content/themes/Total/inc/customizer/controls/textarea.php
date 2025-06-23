<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

/**
 * Customizer Textarea Control
 */
class Textarea extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'wpex_textarea';

	/**
	 * Custom textarea rows.
	 */
	public $rows = 10;

	/**
	 * Render Control Content.
	 */
	public function render_content() {
		$input_id       = "_customize-input-{$this->id}";
		$description_id = "_customize-description-{$this->id}";
		?>

		<?php if ( ! empty( $this->label ) ) : ?>
			<label for="<?php echo \esc_attr( $input_id ); ?>" class="customize-control-title"><?php echo \esc_html( $this->label ); ?></label>
		<?php endif; ?>

		<?php if ( ! empty( $this->description ) ) : ?>
			<span id="<?php echo \esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo \wp_kses_post( $this->description ); ?></span>
		<?php endif; ?>

		<textarea class="totaltheme-customize-textarea" rows="<?php echo \esc_attr( $this->rows ); ?>" <?php $this->link(); ?>></textarea>

		<?php
		switch ( $this->id ) {
			case 'top_bar_content':
				$this->topbar_toolbar();
				break;
			case 'header_flex_aside_content':
				$this->header_aside_toolbar();
				break;
			case 'footer_copyright_text';
				$this->copyright_toolbar();
				break;
		}

	}

	/**
	 * Header Aside quick insert toolbar.
	 */
	public function header_aside_toolbar() {
		?>
		<div class="totaltheme-customize-textarea-toolbar">
			<div class="totaltheme-customize-textarea-toolbar__buttons">
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '<a href="#" class="theme-button wpex-mm-w-100">' . \esc_html__( 'Button', 'total' ) . '</a>' ); ?>"><?php \esc_html_e( 'Button', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[header_search_icon]' ); ?>"><?php \esc_html_e( 'Search Icon', 'total' ); ?></button>
				<?php if ( \shortcode_exists( 'header_dark_mode_icon' ) ) { ?>
					<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[header_dark_mode_icon]' ); ?>"><?php \esc_html_e( 'Dark Mode Icon', 'total' ); ?></button>
				<?php } ?>
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[searchform]' ); ?>"><?php \esc_html_e( 'Search Form', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[vcex_social_links twitter="twitter.com" facebook="facebook.com" instagram="instagram.com" style="none" classes="wpex-inline-flex"]' ); ?>"><?php \esc_html_e( 'Social Links', 'total' ); ?></button>
				<?php if ( totaltheme_is_integration_active( 'woocommerce' ) ) { ?>
					<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[header_cart_icon]' ); ?>"><?php \esc_html_e( 'Cart Icon', 'total' ); ?></button>
				<?php } ?>
				<?php if ( \defined( 'WPEX_WPML_ACTIVE' ) && \WPEX_WPML_ACTIVE ) { ?>
					<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[wpml_language_selector_widget]' ); ?>"><?php \esc_html_e( 'Language Switcher', 'total' ); ?></button>
				<?php } ?>
				<?php if ( class_exists( 'Polylang' ) ) { ?>
					<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[polylang_switcher]' ); ?>"><?php \esc_html_e( 'Language Switcher', 'total' ); ?></button>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Top bar quick insert toolbar.
	 */
	public function topbar_toolbar() {
		?>
		<div class="totaltheme-customize-textarea-toolbar">
			<div class="totaltheme-customize-textarea-toolbar__buttons">
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[topbar_item icon="phone" text="1-800-987-654" link="tel:1-800-987-654"/]' ); ?>"><?php \esc_html_e( 'Phone Number', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[topbar_item icon="envelope" text="admin@totalwptheme.com" link="mailto:admin@totalwptheme.com"/]' ); ?>"><?php \esc_html_e( 'Email', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[topbar_item type="login" icon="user" icon_logged_in="sign-out" text="User Login" text_logged_in="Log Out" logout_text="Logout"/]' ); ?>"><?php \esc_html_e( 'Login Link', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[topbar_item icon="star" text="My Link" link="#"/]' ); ?>"><?php \esc_html_e( 'Link With Icon', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[topbar_item icon="star" text="My Text"/]' ); ?>"><?php \esc_html_e( 'Text With Icon', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '<a href="#" class="theme-button">' . \esc_html__( 'Button', 'total' ) . '</a>' ); ?>"><?php \esc_html_e( 'Button', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[topbar_item][searchform][/topbar_item]' ); ?>"><?php \esc_html_e( 'Search Form', 'total' ); ?></button>
				<?php if ( totaltheme_is_integration_active( 'woocommerce' ) ) { ?>
					<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[topbar_item][cart_link items="icon,count,total"][/topbar_item]' ); ?>"><?php \esc_html_e( 'Cart Link', 'total' ); ?></button>
				<?php } ?>
				<?php if ( \defined( 'WPEX_WPML_ACTIVE' ) && \WPEX_WPML_ACTIVE ) { ?>
					<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[topbar_item][wpml_language_selector_widget][/topbar_item]' ); ?>"><?php \esc_html_e( 'Language Switcher', 'total' ); ?></button>
				<?php } ?>
				<?php if ( class_exists( 'Polylang' ) ) { ?>
					<button type="button" class="button button-secondary" data-wpex-insert="<?php echo \esc_textarea( '[topbar_item][polylang_switcher][/topbar_item]' ); ?>"><?php \esc_html_e( 'Language Switcher', 'total' ); ?></button>
				<?php } ?>
			</div>
		</div>
	<?php
	}

	/**
	 * Copyright quick insert toolbar.
	 */
	public function copyright_toolbar() {
		?>

		<div class="totaltheme-customize-textarea-toolbar">
			<div class="totaltheme-customize-textarea-toolbar__buttons">
				<button type="button" class="button button-secondary" data-wpex-insert="[current_year]"><?php \esc_html_e( 'Current Year', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="[site_name]"><?php \esc_html_e( 'Site Name', 'total' ); ?></button>
				<button type="button" class="button button-secondary" data-wpex-insert="[site_url]"><?php \esc_html_e( 'Site URL', 'total' ); ?></button>
			</div>
		</div>
		<?php
	}

}
