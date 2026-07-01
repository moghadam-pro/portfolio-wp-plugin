<?php
/**
 * Elementor integration.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Elementor {
	/**
	 * Whether Elementor hooks have been attached.
	 *
	 * @var bool
	 */
	private static $registered = false;

	/**
	 * Register Elementor lifecycle hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'elementor/loaded', array( __CLASS__, 'register_hooks' ) );
		if ( did_action( 'elementor/loaded' ) ) {
			self::register_hooks();
		}
	}

	/**
	 * Attach widget and category hooks once.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		if ( self::$registered ) {
			return;
		}
		self::$registered = true;

		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
	}

	/**
	 * Register the MPRO widget category.
	 *
	 * @param object $elements_manager Elementor elements manager.
	 * @return void
	 */
	public static function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'mpro-portfolio',
			array(
				'title' => __( 'MPRO Portfolio', 'mpro-portfolio' ),
				'icon'  => 'eicon-gallery-grid',
			)
		);
	}

	/**
	 * Register custom Elementor widgets.
	 *
	 * @param object $widgets_manager Elementor widgets manager.
	 * @return void
	 */
	public static function register_widgets( $widgets_manager ) {
		require_once MPRO_PORTFOLIO_DIR . 'includes/elementor/class-mpro-portfolio-grid-widget.php';
		$widgets_manager->register( new MPRO_Portfolio_Grid_Widget() );
	}
}
