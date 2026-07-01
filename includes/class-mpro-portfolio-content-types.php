<?php
/**
 * Portfolio post type and taxonomy registration.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Content_Types {
	const POST_TYPE    = 'mpro_portfolio';
	const TAX_CATEGORY = 'mpro_portfolio_category';
	const TAX_TAG      = 'mpro_portfolio_tag';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ), 5 );
	}

	/**
	 * Register the post type and taxonomies.
	 *
	 * The admin menu is intentionally disabled here. A single custom menu
	 * owner prevents WordPress from creating duplicate taxonomy submenus.
	 *
	 * @return void
	 */
	public static function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'             => self::post_type_labels(),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'show_in_admin_bar'  => true,
				'show_in_nav_menus'  => true,
				'show_in_rest'       => true,
				'has_archive'        => 'portfolio',
				'rewrite'            => array(
					'slug'       => 'portfolio',
					'with_front' => false,
				),
				'query_var'          => true,
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'hierarchical'       => false,
				'menu_icon'          => 'dashicons-portfolio',
				'supports'           => array(
					'title',
					'editor',
					'thumbnail',
					'excerpt',
					'revisions',
					'author',
					'comments',
					'trackbacks',
					'custom-fields',
					'page-attributes',
				),
				'taxonomies'         => array( self::TAX_CATEGORY, self::TAX_TAG ),
				'delete_with_user'   => false,
			)
		);

		register_taxonomy(
			self::TAX_TAG,
			array( self::POST_TYPE ),
			array(
				'labels'            => self::tag_labels(),
				'public'            => true,
				'publicly_queryable' => true,
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => false,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array(
					'slug'       => 'portfolio-tag',
					'with_front' => false,
				),
			)
		);

		register_taxonomy(
			self::TAX_CATEGORY,
			array( self::POST_TYPE ),
			array(
				'labels'            => self::category_labels(),
				'public'            => true,
				'publicly_queryable' => true,
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => false,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array(
					'slug'       => 'portfolio-category',
					'with_front' => false,
				),
			)
		);
	}

	/**
	 * Return post type labels.
	 *
	 * @return array
	 */
	private static function post_type_labels() {
		return array(
			'name'                  => __( 'Portfolio', 'mpro-portfolio' ),
			'singular_name'         => __( 'Portfolio Item', 'mpro-portfolio' ),
			'menu_name'             => __( 'Portfolio', 'mpro-portfolio' ),
			'name_admin_bar'        => __( 'Portfolio Item', 'mpro-portfolio' ),
			'add_new'               => __( 'Add Portfolio', 'mpro-portfolio' ),
			'add_new_item'          => __( 'Add New Portfolio', 'mpro-portfolio' ),
			'new_item'              => __( 'New Portfolio', 'mpro-portfolio' ),
			'edit_item'             => __( 'Edit Portfolio', 'mpro-portfolio' ),
			'view_item'             => __( 'View Portfolio', 'mpro-portfolio' ),
			'view_items'            => __( 'View Portfolio', 'mpro-portfolio' ),
			'all_items'             => __( 'All Portfolio', 'mpro-portfolio' ),
			'search_items'          => __( 'Search Portfolio', 'mpro-portfolio' ),
			'not_found'             => __( 'No portfolio items found.', 'mpro-portfolio' ),
			'not_found_in_trash'    => __( 'No portfolio items found in Trash.', 'mpro-portfolio' ),
			'featured_image'        => __( 'Featured Image', 'mpro-portfolio' ),
			'set_featured_image'    => __( 'Set featured image', 'mpro-portfolio' ),
			'remove_featured_image' => __( 'Remove featured image', 'mpro-portfolio' ),
			'use_featured_image'    => __( 'Use as featured image', 'mpro-portfolio' ),
			'archives'              => __( 'Portfolio Archives', 'mpro-portfolio' ),
			'attributes'            => __( 'Portfolio Attributes', 'mpro-portfolio' ),
			'insert_into_item'      => __( 'Insert into portfolio', 'mpro-portfolio' ),
			'uploaded_to_this_item' => __( 'Uploaded to this portfolio', 'mpro-portfolio' ),
			'filter_items_list'     => __( 'Filter portfolio list', 'mpro-portfolio' ),
			'items_list_navigation' => __( 'Portfolio list navigation', 'mpro-portfolio' ),
			'items_list'            => __( 'Portfolio list', 'mpro-portfolio' ),
		);
	}

	/**
	 * Return category labels.
	 *
	 * @return array
	 */
	private static function category_labels() {
		return array(
			'name'              => __( 'Categories', 'mpro-portfolio' ),
			'singular_name'     => __( 'Category', 'mpro-portfolio' ),
			'menu_name'         => __( 'Categories', 'mpro-portfolio' ),
			'search_items'      => __( 'Search Categories', 'mpro-portfolio' ),
			'all_items'         => __( 'All Categories', 'mpro-portfolio' ),
			'parent_item'       => __( 'Parent Category', 'mpro-portfolio' ),
			'parent_item_colon' => __( 'Parent Category:', 'mpro-portfolio' ),
			'edit_item'         => __( 'Edit Category', 'mpro-portfolio' ),
			'update_item'       => __( 'Update Category', 'mpro-portfolio' ),
			'add_new_item'      => __( 'Add New Category', 'mpro-portfolio' ),
			'new_item_name'     => __( 'New Category Name', 'mpro-portfolio' ),
		);
	}

	/**
	 * Return tag labels.
	 *
	 * @return array
	 */
	private static function tag_labels() {
		return array(
			'name'                       => __( 'Tags', 'mpro-portfolio' ),
			'singular_name'              => __( 'Tag', 'mpro-portfolio' ),
			'menu_name'                  => __( 'Tags', 'mpro-portfolio' ),
			'search_items'               => __( 'Search Tags', 'mpro-portfolio' ),
			'popular_items'              => __( 'Popular Tags', 'mpro-portfolio' ),
			'all_items'                  => __( 'All Tags', 'mpro-portfolio' ),
			'edit_item'                  => __( 'Edit Tag', 'mpro-portfolio' ),
			'update_item'                => __( 'Update Tag', 'mpro-portfolio' ),
			'add_new_item'               => __( 'Add New Tag', 'mpro-portfolio' ),
			'new_item_name'              => __( 'New Tag Name', 'mpro-portfolio' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'mpro-portfolio' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'mpro-portfolio' ),
			'choose_from_most_used'      => __( 'Choose from the most used tags', 'mpro-portfolio' ),
		);
	}
}
