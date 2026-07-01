<?php
/**
 * Public template helper functions.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return one portfolio card.
 *
 * @param int    $post_id Portfolio ID.
 * @param string $style Card style, empty, or "item".
 * @return string
 */
function mpro_portfolio_get_card( $post_id = 0, $style = '' ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	return MPRO_Portfolio_Renderer::render_card( $post_id, $style );
}

/**
 * Print one portfolio card.
 *
 * @param int    $post_id Portfolio ID.
 * @param string $style Card style, empty, or "item".
 * @return void
 */
function mpro_portfolio_the_card( $post_id = 0, $style = '' ) {
	echo mpro_portfolio_get_card( $post_id, $style ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Return a portfolio grid.
 *
 * @param array $args Grid arguments.
 * @return string
 */
function mpro_portfolio_get_grid( $args = array() ) {
	return MPRO_Portfolio_Renderer::render_grid( $args );
}

/**
 * Print a portfolio grid.
 *
 * @param array $args Grid arguments.
 * @return void
 */
function mpro_portfolio_the_grid( $args = array() ) {
	echo mpro_portfolio_get_grid( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
