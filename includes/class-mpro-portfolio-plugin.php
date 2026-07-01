<?php
/**
 * Main plugin bootstrap.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var MPRO_Portfolio_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Return the singleton instance.
	 *
	 * @return MPRO_Portfolio_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register plugin services.
	 *
	 * @return void
	 */
	public function boot() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		MPRO_Portfolio_Content_Types::init();
		MPRO_Portfolio_Admin_Menu::init();
		MPRO_Portfolio_Settings::init();
		MPRO_Portfolio_Meta_Boxes::init();
		MPRO_Portfolio_Admin_List::init();
		MPRO_Portfolio_Renderer::init();
		MPRO_Portfolio_Shortcodes::init();
		MPRO_Portfolio_Templates::init();
		MPRO_Portfolio_Elementor::init();
		MPRO_Portfolio_Rank_Math::init();
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'mpro-portfolio', false, dirname( plugin_basename( MPRO_PORTFOLIO_FILE ) ) . '/languages' );
	}

	/**
	 * Activation callback.
	 *
	 * @return void
	 */
	public static function activate() {
		MPRO_Portfolio_Content_Types::register();
		MPRO_Portfolio_Settings::add_default_options();
		update_option( 'mpro_portfolio_version', MPRO_PORTFOLIO_VERSION );
		flush_rewrite_rules();
	}

	/**
	 * Deactivation callback.
	 *
	 * @return void
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
