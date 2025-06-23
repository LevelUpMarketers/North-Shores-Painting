<?php

namespace TotalTheme\Customizer\Controls;

use WP_Customize_Control;

/**
 * Customizer Social_Profiles Control.
 */
class Social_Profiles extends WP_Customize_Control {

	/**
	 * The control type.
	 */
	public $type = 'totaltheme_social_profiles';

	/**
	 * Enque scripts.
	 */
	public function enqueue() {
		\wp_enqueue_script( 'jquery-ui-core' );
		\wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Render Control Content.
	 */
	public function render_content() {
		$input_id       = "_customize-input-{$this->id}";
		$description_id = "_customize-description-{$this->id}";
		$value          = $this->value();
		$social_options = (array) \totaltheme_call_static( 'Topbar\Social', 'get_profile_options' );

		if ( ! \is_array( $value ) ) {
			$value = \json_decode( $value, true );
		}

		?>

		<?php if ( ! empty( $this->label ) ) : ?>
			<label for="<?php echo \esc_attr( $input_id ); ?>" class="customize-control-title"><?php echo \esc_html( $this->label ); ?></label>
		<?php endif; ?>

		<?php if ( ! empty( $this->description ) ) : ?>
			<span id="<?php echo \esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo \esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<div class="totaltheme-customize-social-profiles">

			<div class="totaltheme-customize-social-profiles__list">
				<?php if ( $value && \is_array( $value ) ) {
					foreach ( $value as $site => $url ) {
						if ( ! $url || ! array_key_exists( $site, $social_options ) ) {
							continue;
						}
						echo '<div class="totaltheme-customize-social-profiles__item">';
							$label = $social_options[ $site ]['name'] ?? $social_options[ $site ]['label'];
							echo '<label><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg><span>' . \esc_html( $label ) . '</span></label>';
							echo '<div>';
								echo '<input value="' . \esc_attr( $url ) . '" type="text" data-wpex-key="' . \esc_attr( $site ) . '">';
								echo '<button class="totaltheme-customize-social-profiles__delete-item button-secondary"><span class="dashicons dashicons-trash"></span></button>';
							echo '</div>';
						echo '</div>';
					}
				} ?>
			</div>

			<hr>

			<?php
			// Template
			echo '<div class="totaltheme-customize-social-profiles__template">';
				echo '<label><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg><span></span></label>';
				echo '<div>';
					echo '<input type="text" value="" data-wpex-key="">';
					echo '<button class="totaltheme-customize-social-profiles__delete-item button-secondary"><span class="dashicons dashicons-trash"></span></button>';
				echo '</div>';
			echo '</div>';

			// Select
			echo '<select class="totaltheme-customize-social-profiles__select">';
				echo '<option>' . \esc_html__( 'Select a site', 'total' ) . '</option>';
				foreach ( $social_options as $key => $args ) {
					echo '<option value="' . \esc_attr( $key ) . '">' . \esc_html( $args['name'] ?? $args['label'] ) . '</option>';
				}
			echo '</select>';

			// Add new button.
			echo '<button type="button" class="totaltheme-customize-social-profiles__add-item button button-primary"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="currentColor"><path d="M2 12C2 6.44444 6.44444 2 12 2C17.5556 2 22 6.44444 22 12C22 17.5556 17.5556 22 12 22C6.44444 22 2 17.5556 2 12ZM13 11V7H11V11H7V13H11V17H13V13H17V11H13Z"></path></svg> ' . \esc_html__( 'Add New', 'total' ) . '</button>';

			?>

		</div>

	<?php }

}
