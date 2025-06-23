<?php

namespace TotalThemeCore\Widgets;

\defined( 'ABSPATH' ) || exit;

/**
 * bbPress Topic Info Widget.
 */
class Widget_bbPress_Topic_Info extends \WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		if ( \function_exists( '\wpex_get_theme_branding' ) ) {
			$branding = \wpex_get_theme_branding();
			if ( $branding ) {
				$branding = $branding . ' - ';
			}
		} else {
			$branding = 'Total - ';
		}

		parent::__construct(
			'wpex_bbpress_topic_info',
			'(bbPress) ' . $branding . \esc_html__( 'Topic Info', 'total-theme-core' ),
			array(
				'customize_selective_refresh' => true,
			)
		);

	}

	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		if ( ! \bbp_is_single_topic() ) {
			return;
		}

		$html = '';

		// Widget options
		$title = isset( $instance['title'] ) ? \apply_filters( 'widget_title', $instance['title'] ) : '';

		// Before widget hook
		echo \wp_kses_post( $args['before_widget'] );

			// Display widget title
			if ( $title ) :
				$html .= $args['before_title'];
					$html .= \esc_html( $title );
				$html .= $args['after_title'];
			endif;

			// Wrap classes
			$html .= '<ul class="wpex-bbpress-forum-info wpex-bordered-list">';

				// Parent.
				if ( $forum = \wp_get_post_parent_id( get_the_ID() ) ) {

					$parent_name = \get_the_title( $forum );

					$html .= '<li class="wpex-bbpress-forum-info__item forum-topic-count">';
						if ( \function_exists( '\totaltheme_get_icon' ) ) {
							$html .= '<span class="wpex-bbpress-forum-info__icon wpex-mr-10 wpex-text-3">' . \totaltheme_get_icon( 'folder-o' ) . '</span>';
						}
						$html .= \esc_html__( 'In', 'total-theme-core' ) .': ';
						$html .= '<a href="' . \esc_url( \get_permalink( $forum ) ) . '">' . \esc_html( $parent_name ) . '</a>';
					$html .= '</li>';
				}

				// Replies.
				$count = \bbp_show_lead_topic() ? \bbp_get_topic_reply_count() : \bbp_get_topic_post_count();
				$html .= '<li class="wpex-bbpress-forum-info__item forum-reply-count">';
					if ( \function_exists( '\totaltheme_get_icon' ) ) {
						$html .= '<span class="wpex-bbpress-forum-info__icon wpex-mr-10 wpex-text-3">' . \totaltheme_get_icon( 'comments-o' ) . '</span>';
					}
					$html .= \absint( $count ) . ' ' . \esc_html__( 'replies', 'total-theme-core' );
				$html .= '</li>';

				// Participants.
				$html .= '<li class="wpex-bbpress-forum-info__item forum-participants">';
					if ( \function_exists( '\totaltheme_get_icon' ) ) {
						$html .= '<span class="wpex-bbpress-forum-info__icon wpex-mr-10 wpex-text-3">' . \totaltheme_get_icon( 'user-o' ) . '</span>';
					}
					$html .= \esc_html__( 'Participants', 'total-theme-core' ) .': ' . \absint( \bbp_get_topic_voice_count() );
				$html .= '</li>';

				// Last Reply.
				$html .= '<li class="wpex-bbpress-forum-info__item last-user">';
					if ( \function_exists( '\totaltheme_get_icon' ) ) {
						$html .= '<span class="wpex-bbpress-forum-info__icon wpex-mr-10 wpex-text-3">' . \totaltheme_get_icon( 'commenting-o' ) . '</span>';
					}

					$html .= \esc_html__( 'Last reply from', 'total-theme-core' ) .': ';

					$last_post_id = \bbp_get_topic_last_active_id();
					$last_user_id = \bbp_get_topic_author_id( $last_post_id );
					$user         = \get_user_by( 'id', $last_user_id );
					$name         = $user->display_name;

					$html .= '<a href="' . \esc_url( bbp_get_user_profile_url( $last_user_id ) ) . '">' . \esc_html( $name ) . '</a>';

				$html .= '</li>';

				// Freshness
				$html .= '<li class="wpex-bbpress-forum-info__item forum-freshness-time">';
					if ( \function_exists( '\totaltheme_get_icon' ) ) {
						$html .= '<span class="wpex-bbpress-forum-info__icon wpex-mr-10 wpex-text-3">' . \totaltheme_get_icon( 'clock-o' ) . '</span>';
					}
					$html .= \esc_html__( 'Last activity', 'total-theme-core' ) .': ' . \bbp_get_topic_freshness_link();
				$html .= '</li>';

			// Close widget wrap
			$html .= '</ul>';

		// After widget hook
		$html .= \wp_kses_post( $args['after_widget'] );

		// Echo output
		echo $html;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @return array Updated safe values to be saved.
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
		$instance = \wp_parse_args( (array) $instance, [
			'title' => '',
		] ); ?>

		<p>
			<label for="<?php echo \esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php \esc_html_e( 'Title', 'total-theme-core' ); ?>:</label>
			<input class="widefat" id="<?php echo \esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo \esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo \esc_attr( $instance['title'] ); ?>">
		</p>

		<?php
	}
}
register_widget( 'TotalThemeCore\\Widgets\\Widget_bbPress_Topic_Info' );