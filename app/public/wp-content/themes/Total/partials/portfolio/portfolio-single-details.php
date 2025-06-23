<?php

/**
 * Portfolio single details.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$post_id = get_the_ID();
$company = get_post_meta( $post_id, 'wpex_portfolio_company', true );
$url     = get_post_meta( $post_id, 'wpex_portfolio_url', true );
$budget  = get_post_meta( $post_id, 'wpex_portfolio_budget', true );

if ( ! $company && ! $url && ! $budget ) {
	return;
}

$just_button = false;

if ( $url && ! $company && ! $budget ) {
	$just_button = true;
}

?>

<div class="portfolio-single-details wpex-mb-40"><?php

	$link_attrs = array(
		'href' => $url,
	);

	if ( $just_button ) {
		$link_attrs['class'] = 'theme-button';
	}

	/**
	 * Filters the single portfolio details company link attributes.
	 *
	 * @param array $attributes
	 */
	$link_attrs = (array) apply_filters( 'wpex_portfolio_single_details_link_attrs', $link_attrs );

	// Only the URL is set
	if ( $just_button ) {
		echo wpex_parse_html( 'a', $link_attrs, esc_html__( 'Visit the Site', 'total' ) );
	} else {

		if ( $budget ) { ?>

			<div class="portfolio-single-details__item">
				<strong><?php esc_html_e( 'Budget:', 'total' ); ?></strong>
				<?php echo esc_html( $budget ); ?>
			</div>

		<?php }

		if ( $company ) { ?>
			<div class="portfolio-single-details__item">
				<strong><?php esc_html_e( 'Company:', 'total' ); ?></strong>
				<?php if ( $url ) {
					echo wpex_parse_html( 'a', $link_attrs, esc_html( $company ) );
				} else {
					echo esc_html( $company );
				} ?>
			</div>
		<?php }
	}

?></div>