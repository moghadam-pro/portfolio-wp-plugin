<?php
/**
 * Custom Post Type: Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_CPT {

    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_post_type' ] );
    }

    public static function register_post_type() {

        $labels = [
            'name'                  => 'Portfolio',
            'singular_name'         => 'Portfolio Item',
            'menu_name'             => 'Portfolio',
            'name_admin_bar'        => 'Portfolio Item',
            'add_new'               => 'Add Portfolio',
            'add_new_item'          => 'Add New Portfolio Item',
            'edit_item'             => 'Edit Portfolio Item',
            'new_item'              => 'New Portfolio Item',
            'view_item'             => 'View Portfolio Item',
            'view_items'            => 'View Portfolio',
            'search_items'          => 'Search Portfolio',
            'not_found'             => 'No portfolio items found.',
            'not_found_in_trash'    => 'No portfolio items found in Trash.',
            'all_items'             => 'All Portfolio',
            'archives'              => 'Portfolio Archives',
            'attributes'            => 'Portfolio Attributes',
            'featured_image'        => 'Cover Image',
            'set_featured_image'    => 'Set cover image',
            'remove_featured_image' => 'Remove cover image',
            'use_featured_image'    => 'Use as cover image',
        ];

        $args = [
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => false, // custom menu handles this
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => [ 'slug' => 'portfolio', 'with_front' => false ],
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-portfolio',
            'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author', 'custom-fields' ],
            'taxonomies'          => [ 'mpro_portfolio_category', 'mpro_portfolio_tag' ],
        ];

        register_post_type( MPRO_PF_POST_TYPE, $args );
    }
}
