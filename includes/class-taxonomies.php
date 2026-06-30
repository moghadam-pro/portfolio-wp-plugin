<?php
/**
 * Taxonomies: Portfolio Category, Portfolio Tag
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_Taxonomies {

    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_taxonomies' ] );
    }

    public static function register_taxonomies() {

        // Categories — hierarchical, like post categories
        register_taxonomy( 'mpro_portfolio_category', MPRO_PF_POST_TYPE, [
            'labels' => [
                'name'              => 'Categories',
                'singular_name'     => 'Category',
                'menu_name'         => 'Categories',
                'search_items'      => 'Search Categories',
                'all_items'         => 'All Categories',
                'parent_item'       => 'Parent Category',
                'parent_item_colon' => 'Parent Category:',
                'edit_item'         => 'Edit Category',
                'update_item'       => 'Update Category',
                'add_new_item'      => 'Add New Category',
                'new_item_name'     => 'New Category Name',
            ],
            'hierarchical'       => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'show_admin_column'  => true,
            'show_in_rest'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'portfolio-category', 'with_front' => false ],
        ] );

        // Tags — non-hierarchical, like post tags
        register_taxonomy( 'mpro_portfolio_tag', MPRO_PF_POST_TYPE, [
            'labels' => [
                'name'              => 'Tags',
                'singular_name'     => 'Tag',
                'menu_name'         => 'Tags',
                'search_items'      => 'Search Tags',
                'all_items'         => 'All Tags',
                'edit_item'         => 'Edit Tag',
                'update_item'       => 'Update Tag',
                'add_new_item'      => 'Add New Tag',
                'new_item_name'     => 'New Tag Name',
            ],
            'hierarchical'       => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'show_admin_column'  => true,
            'show_in_rest'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'portfolio-tag', 'with_front' => false ],
        ] );
    }
}
