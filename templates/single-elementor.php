<?php
/**
 * Elementor bridge for single Portfolio pages.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
while ( have_posts() ) : the_post();
	$style       = MPRO_Portfolio_Renderer::resolve_single_style( get_the_ID() );
	$template_id = 0 === strpos( $style, 'elementor:' ) ? absint( substr( $style, 10 ) ) : 0;
	$content     = $template_id ? MPRO_Portfolio_Renderer::render_elementor_template( $template_id, get_the_ID() ) : '';

	if ( $content ) {
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		the_content();
	}
endwhile;
get_footer();
