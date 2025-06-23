<?php
namespace TotalThemeCore\Widgets;

\defined( 'ABSPATH' ) || exit;

/**
 * bbPress Forum Info Widget.
 */
class Widget_bbPress_Forum_Info extends \WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		$branding = '';

		if ( \function_exists( '\wpex_get_theme_branding' ) ) {
			$branding = \wpex_get_theme_branding();
			if ( $branding ) {
				$branding = $branding . ' - ';
			}
		} else {
			$branding = 'Total - ';
		}

		parent::__construct(
			'wpex_bbpress_forum_info',
			'(bbPress) ' . $branding . \esc_html__( 'Forum Info', 'total-theme-core' ),
			[
				'customize_selective_refresh' => true,
			]
		);

	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		if ( ! \function_exists( 'bbp_is_single_forum' ) || ! \bbp_is_single_forum() ) {
			return;
		}

		$html = '';

		// Widget options.
		$title = isset( $instance['title'] ) ? \apply_filters( 'widget_title', $instance['title'] ) : '';

		// Before widget hook.
		echo \wp_kses_post( $args[ 'before_widget' ] );

			// Display widget title.
			if ( $title ) {
				$html .= $args['before_title'];
					$html .= \esc_html( $title );
				$html .= $args['after_title'];
			}

			// Wrap classes.
			$html .= '<ul class="wpex-bbpress-forum-info wpex-bordered-list">';

				// Topics.
				$html .= '<li class="wpex-bbpress-forum-info__item topic-count">';
					if ( \function_exists( '\totaltheme_get_icon' ) ) {
						$html .= '<span class="wpex-bbpress-forum-info__icon wpex-mr-10 wpex-text-3">' . \totaltheme_get_icon( 'folder-o' ) . '</span>';
					}
					$html .= \bbp_get_forum_topic_count() . ' ' . \esc_html__( 'topics', 'total-theme-core' );
				$html .= '</li>';

				// Replies.
				$html .= '<li class="wpex-bbpress-forum-info__item reply-count">';
					if ( \function_exists( '\totaltheme_get_icon' ) ) {
						$html .= '<span class="wpex-bbpress-forum-info__icon wpex-mr-10 wpex-text-3">' . \totaltheme_get_icon( 'comments-o' ) . '</span>';
					}
					$html .= \bbp_get_forum_post_count() . ' ' . \esc_html__( 'replies', 'total-theme-core' );
				$html .= '</li>';

				// Freshness.
				$html .= '<li class="wpex-bbpress-forum-info__item forum-freshness-time">';
					if ( \function_exists( '\totaltheme_get_icon' ) ) {
						$html .= '<span class="wpex-bbpress-forum-info__icon wpex-mr-10 wpex-text-3">' . \totaltheme_get_icon( 'clock-o' ) . '</span>';
					}
					$html .= \esc_html__( 'Last activity', 'total-theme-core' ) .': '. \bbp_get_forum_freshness_link();
				$html .= '</li>';

			// Close widget wrap.
			$html .= '</ul>';

		// After widget hook.
		$html .= wp_kses_post( $args[ 'after_widget' ] );

		echo $html;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = \wp_strip_all_tags( \sanitize_text_field( $new_instance['title'] ) );
		return $instance;
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, [
			'title' => '',
		] );
		
		?>

		<p>
			<label for="<?php echo \esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php \esc_html_e( 'Title', 'total-theme-core' ); ?>:</label>
			<input class="widefat" id="<?php echo \esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo \esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo \esc_attr( $instance['title'] ); ?>">
		</p>

		<?php
	}
}
register_widget( 'TotalThemeCore\Widgets\Widget_bbPress_Forum_Info' );