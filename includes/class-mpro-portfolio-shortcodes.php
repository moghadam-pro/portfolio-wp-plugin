<?php
/**
 * Portfolio shortcodes.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Shortcodes {
	/**
	 * Register shortcodes.
	 *
	 * @return void
	 */
	public static function init() {
		add_shortcode( 'mpro_portfolio', array( __CLASS__, 'grid' ) );
		add_shortcode( 'mpro_portfolio_grid', array( __CLASS__, 'grid' ) );
		add_shortcode( 'mpro_portfolio_featured', array( __CLASS__, 'featured' ) );
		add_shortcode( 'mpro_portfolio_single', array( __CLASS__, 'single' ) );
	}

	/**
	 * Render a flexible portfolio grid.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function grid( $atts ) {
		$atts = shortcode_atts(
			array(
				'ids'            => '',
				'featured'       => 'false',
				'category'       => '',
				'tag'            => '',
				'style'          => '',
				'columns'        => 3,
				'columns_tablet' => 2,
				'columns_mobile' => 1,
				'count'          => 9,
				'posts_per_page' => '',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'pagination'     => 'false',
				'page_var'       => 'mpro_page',
			),
			$atts,
			'mpro_portfolio'
		);

		if ( '' !== $atts['posts_per_page'] ) {
			$atts['count'] = $atts['posts_per_page'];
		}

		return MPRO_Portfolio_Renderer::render_grid( $atts );
	}

	/**
	 * Render a manually curated set.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function featured( $atts ) {
		$atts = shortcode_atts(
			array(
				'ids'            => '',
				'style'          => '',
				'columns'        => 3,
				'columns_tablet' => 2,
				'columns_mobile' => 1,
			),
			$atts,
			'mpro_portfolio_featured'
		);

		if ( ! $atts['ids'] ) {
			$atts['featured'] = true;
			$atts['count']    = 6;
		}

		return MPRO_Portfolio_Renderer::render_grid( $atts );
	}

	/**
	 * Render one portfolio card.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function single( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'    => 0,
				'style' => '',
			),
			$atts,
			'mpro_portfolio_single'
		);

		return MPRO_Portfolio_Renderer::render_card( absint( $atts['id'] ), $atts['style'] );
	}
}
