<?php
/**
 * Rank Math integration using documented public hooks.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Rank_Math {
	/**
	 * Register Rank Math hooks.
	 *
	 * Public custom post types are discovered by Rank Math automatically.
	 * These hooks add defaults, icons, and useful portfolio variables without
	 * overriding the site owner's current Rank Math settings.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'rank_math/post_type_icons', array( __CLASS__, 'post_type_icons' ) );
		add_filter( 'rank_math/taxonomy_icons', array( __CLASS__, 'taxonomy_icons' ) );
		add_filter( 'rank_math/settings/snippet/type', array( __CLASS__, 'default_schema_type' ), 10, 2 );
		add_filter( 'rank_math/settings/defaults/titles', array( __CLASS__, 'title_defaults' ) );
		add_filter( 'rank_math/settings/defaults/sitemap', array( __CLASS__, 'sitemap_defaults' ) );
		add_action( 'rank_math/vars/register_extra_replacements', array( __CLASS__, 'register_variables' ) );
	}

	/**
	 * Add the Portfolio icon to Rank Math settings.
	 *
	 * @param array $icons Existing icons.
	 * @return array
	 */
	public static function post_type_icons( $icons ) {
		$icons[ MPRO_Portfolio_Content_Types::POST_TYPE ] = 'dashicons dashicons-portfolio';
		return $icons;
	}

	/**
	 * Add taxonomy icons to Rank Math settings.
	 *
	 * @param array $icons Existing icons.
	 * @return array
	 */
	public static function taxonomy_icons( $icons ) {
		$icons[ MPRO_Portfolio_Content_Types::TAX_CATEGORY ] = 'dashicons dashicons-category';
		$icons[ MPRO_Portfolio_Content_Types::TAX_TAG ]      = 'dashicons dashicons-tag';
		return $icons;
	}

	/**
	 * Suggest Article schema for Portfolio while remaining user-overridable.
	 *
	 * @param string $type Current schema type.
	 * @param string $post_type Post type.
	 * @return string
	 */
	public static function default_schema_type( $type, $post_type ) {
		return MPRO_Portfolio_Content_Types::POST_TYPE === $post_type ? 'article' : $type;
	}

	/**
	 * Provide defaults only when Rank Math initializes its title settings.
	 *
	 * @param array $settings Default settings.
	 * @return array
	 */
	public static function title_defaults( $settings ) {
		$post_type = MPRO_Portfolio_Content_Types::POST_TYPE;
		$settings[ 'pt_' . $post_type . '_title' ]       = '%title% %sep% %sitename%';
		$settings[ 'pt_' . $post_type . '_description' ] = '%portfolio_short_description%';
		$settings[ 'pt_' . $post_type . '_robots' ]      = array( 'index' );
		return $settings;
	}

	/**
	 * Provide sitemap defaults only when Rank Math initializes settings.
	 *
	 * @param array $settings Default settings.
	 * @return array
	 */
	public static function sitemap_defaults( $settings ) {
		$settings[ 'pt_' . MPRO_Portfolio_Content_Types::POST_TYPE . '_sitemap' ] = 'on';
		return $settings;
	}

	/**
	 * Register portfolio variables for titles, descriptions, and schema fields.
	 *
	 * @return void
	 */
	public static function register_variables() {
		if ( ! function_exists( 'rank_math_register_var_replacement' ) ) {
			return;
		}

		$variables = array(
			'portfolio_short_description' => array(
				'name'        => __( 'Portfolio Short Description', 'mpro-portfolio' ),
				'description' => __( 'The short description stored for the current portfolio item.', 'mpro-portfolio' ),
				'callback'    => array( __CLASS__, 'variable_short_description' ),
			),
			'portfolio_role' => array(
				'name'        => __( 'Portfolio Role', 'mpro-portfolio' ),
				'description' => __( 'The role stored for the current portfolio item.', 'mpro-portfolio' ),
				'callback'    => array( __CLASS__, 'variable_role' ),
			),
			'portfolio_duration' => array(
				'name'        => __( 'Portfolio Duration', 'mpro-portfolio' ),
				'description' => __( 'The formatted project duration.', 'mpro-portfolio' ),
				'callback'    => array( __CLASS__, 'variable_duration' ),
			),
			'portfolio_tools' => array(
				'name'        => __( 'Portfolio Tools', 'mpro-portfolio' ),
				'description' => __( 'A comma-separated list of project tools.', 'mpro-portfolio' ),
				'callback'    => array( __CLASS__, 'variable_tools' ),
			),
			'portfolio_client' => array(
				'name'        => __( 'Portfolio Client', 'mpro-portfolio' ),
				'description' => __( 'The client or company stored for the project.', 'mpro-portfolio' ),
				'callback'    => array( __CLASS__, 'variable_client' ),
			),
		);

		foreach ( $variables as $slug => $variable ) {
			rank_math_register_var_replacement(
				$slug,
				array(
					'name'        => $variable['name'],
					'description' => $variable['description'],
					'variable'    => $slug,
					'example'     => call_user_func( $variable['callback'] ),
				),
				$variable['callback']
			);
		}
	}

	/**
	 * Return the current portfolio ID.
	 *
	 * @return int
	 */
	private static function current_post_id() {
		return absint( get_queried_object_id() ? get_queried_object_id() : get_the_ID() );
	}

	/** @return string */
	public static function variable_short_description() {
		return (string) MPRO_Portfolio_Meta_Boxes::get( self::current_post_id(), 'short_description' );
	}

	/** @return string */
	public static function variable_role() {
		return (string) MPRO_Portfolio_Meta_Boxes::get( self::current_post_id(), 'role' );
	}

	/** @return string */
	public static function variable_duration() {
		return MPRO_Portfolio_Meta_Boxes::get_duration_label( self::current_post_id() );
	}

	/** @return string */
	public static function variable_tools() {
		$tools = MPRO_Portfolio_Meta_Boxes::get( self::current_post_id(), 'tools', array() );
		return is_array( $tools ) ? implode( ', ', $tools ) : '';
	}

	/** @return string */
	public static function variable_client() {
		return (string) MPRO_Portfolio_Meta_Boxes::get( self::current_post_id(), 'client' );
	}
}
