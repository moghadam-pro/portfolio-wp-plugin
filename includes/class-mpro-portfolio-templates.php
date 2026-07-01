<?php
/**
 * Single and archive template routing.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Templates {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'template_include' ), 99 );
	}

	/**
	 * Select plugin templates with theme override support.
	 *
	 * @param string $template Current template.
	 * @return string
	 */
	public static function template_include( $template ) {
		if ( is_singular( MPRO_Portfolio_Content_Types::POST_TYPE ) ) {
			$post_id = get_queried_object_id();
			$style   = MPRO_Portfolio_Renderer::resolve_single_style( $post_id );

			if ( 0 === strpos( $style, 'elementor:' ) ) {
				return MPRO_PORTFOLIO_DIR . 'templates/single-elementor.php';
			}

			$theme_template = locate_template(
				array(
					'mpro-portfolio/single-mpro_portfolio.php',
					'single-mpro_portfolio.php',
				)
			);
			return $theme_template ? $theme_template : MPRO_PORTFOLIO_DIR . 'templates/single-mpro_portfolio.php';
		}

		if ( is_post_type_archive( MPRO_Portfolio_Content_Types::POST_TYPE ) || is_tax( array( MPRO_Portfolio_Content_Types::TAX_CATEGORY, MPRO_Portfolio_Content_Types::TAX_TAG ) ) ) {
			$candidates = array();
			if ( is_tax( MPRO_Portfolio_Content_Types::TAX_CATEGORY ) ) {
				$candidates[] = 'mpro-portfolio/taxonomy-mpro_portfolio_category.php';
				$candidates[] = 'taxonomy-mpro_portfolio_category.php';
			}
			if ( is_tax( MPRO_Portfolio_Content_Types::TAX_TAG ) ) {
				$candidates[] = 'mpro-portfolio/taxonomy-mpro_portfolio_tag.php';
				$candidates[] = 'taxonomy-mpro_portfolio_tag.php';
			}
			$candidates[] = 'mpro-portfolio/archive-mpro_portfolio.php';
			$candidates[] = 'archive-mpro_portfolio.php';

			$theme_template = locate_template( $candidates );
			return $theme_template ? $theme_template : MPRO_PORTFOLIO_DIR . 'templates/archive-mpro_portfolio.php';
		}

		return $template;
	}
}
