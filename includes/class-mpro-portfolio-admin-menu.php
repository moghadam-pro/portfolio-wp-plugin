<?php
/**
 * Portfolio admin menu.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Admin_Menu {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 9 );
		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', array( __CLASS__, 'position_menu' ), 999 );
		add_filter( 'plugin_action_links_' . plugin_basename( MPRO_PORTFOLIO_FILE ), array( __CLASS__, 'plugin_action_links' ) );
	}

	/**
	 * Register one top-level menu and exactly five submenus.
	 *
	 * @return void
	 */
	public static function register_menu() {
		$parent = 'edit.php?post_type=' . MPRO_Portfolio_Content_Types::POST_TYPE;

		add_menu_page(
			__( 'Portfolio', 'mpro-portfolio' ),
			__( 'Portfolio', 'mpro-portfolio' ),
			'edit_posts',
			$parent,
			'',
			'dashicons-portfolio',
			4
		);

		add_submenu_page(
			$parent,
			__( 'All Portfolio', 'mpro-portfolio' ),
			__( 'All Portfolio', 'mpro-portfolio' ),
			'edit_posts',
			$parent
		);

		add_submenu_page(
			$parent,
			__( 'Add Portfolio', 'mpro-portfolio' ),
			__( 'Add Portfolio', 'mpro-portfolio' ),
			'edit_posts',
			'post-new.php?post_type=' . MPRO_Portfolio_Content_Types::POST_TYPE
		);

		add_submenu_page(
			$parent,
			__( 'Tags', 'mpro-portfolio' ),
			__( 'Tags', 'mpro-portfolio' ),
			'manage_categories',
			'edit-tags.php?taxonomy=' . MPRO_Portfolio_Content_Types::TAX_TAG . '&post_type=' . MPRO_Portfolio_Content_Types::POST_TYPE
		);

		add_submenu_page(
			$parent,
			__( 'Categories', 'mpro-portfolio' ),
			__( 'Categories', 'mpro-portfolio' ),
			'manage_categories',
			'edit-tags.php?taxonomy=' . MPRO_Portfolio_Content_Types::TAX_CATEGORY . '&post_type=' . MPRO_Portfolio_Content_Types::POST_TYPE
		);

		add_submenu_page(
			$parent,
			__( 'Portfolio Styles', 'mpro-portfolio' ),
			__( 'Styles', 'mpro-portfolio' ),
			'manage_options',
			'mpro-portfolio-styles',
			array( 'MPRO_Portfolio_Settings', 'render_page' )
		);
	}

	/**
	 * Place Portfolio in the fourth visible slot and before another MPRO menu.
	 *
	 * @param array $menu_order Current menu order.
	 * @return array
	 */
	public static function position_menu( $menu_order ) {
		if ( ! is_array( $menu_order ) ) {
			return $menu_order;
		}

		$target = 'edit.php?post_type=' . MPRO_Portfolio_Content_Types::POST_TYPE;
		$index  = array_search( $target, $menu_order, true );

		if ( false === $index ) {
			return $menu_order;
		}

		unset( $menu_order[ $index ] );
		$menu_order = array_values( $menu_order );
		$insert_at  = min( 3, count( $menu_order ) );

		foreach ( $menu_order as $position => $slug ) {
			if ( false !== stripos( (string) $slug, 'mpro' ) ) {
				$insert_at = min( $insert_at, $position );
				break;
			}
		}

		array_splice( $menu_order, $insert_at, 0, array( $target ) );
		return $menu_order;
	}

	/**
	 * Add a Styles shortcut on the Plugins screen.
	 *
	 * @param array $links Existing links.
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$url = admin_url( 'edit.php?post_type=' . MPRO_Portfolio_Content_Types::POST_TYPE . '&page=mpro-portfolio-styles' );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Styles', 'mpro-portfolio' ) . '</a>' );
		return $links;
	}
}
