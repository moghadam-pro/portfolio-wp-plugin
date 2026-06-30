<?php
/**
 * Admin Columns — All Portfolio list table
 *
 * Adds Cover, Category, Tags, Role, Duration columns to the
 * default list-table view so it matches the "list of posts" feel.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_Admin_Columns {

    public static function init() {
        add_filter( 'manage_' . MPRO_PF_POST_TYPE . '_posts_columns', [ __CLASS__, 'columns' ] );
        add_action( 'manage_' . MPRO_PF_POST_TYPE . '_posts_custom_column', [ __CLASS__, 'render_column' ], 10, 2 );
        add_filter( 'manage_edit-' . MPRO_PF_POST_TYPE . '_sortable_columns', [ __CLASS__, 'sortable_columns' ] );
    }

    public static function columns( $columns ) {
        $new = [];
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( $key === 'title' ) {
                $new['mpro_pf_cover'] = 'Cover';
            }
        }
        // Reorder: cb, cover, title, then the rest
        $ordered = [
            'cb'              => $new['cb'] ?? '',
            'mpro_pf_cover'   => 'Cover',
            'title'           => $new['title'],
            'mpro_pf_category' => 'Category',
            'mpro_pf_tags'    => 'Tags',
            'mpro_pf_role'    => 'Role',
            'mpro_pf_duration' => 'Duration',
            'date'            => $new['date'] ?? 'Date',
        ];
        return $ordered;
    }

    public static function render_column( $column, $post_id ) {
        switch ( $column ) {
            case 'mpro_pf_cover':
                if ( has_post_thumbnail( $post_id ) ) {
                    echo get_the_post_thumbnail( $post_id, [ 60, 60 ], [ 'style' => 'object-fit:cover;border-radius:4px;' ] );
                } else {
                    echo '<span style="color:#ccc;">—</span>';
                }
                break;

            case 'mpro_pf_category':
                $terms = get_the_terms( $post_id, 'mpro_portfolio_category' );
                echo $terms && ! is_wp_error( $terms )
                    ? esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) )
                    : '—';
                break;

            case 'mpro_pf_tags':
                $terms = get_the_terms( $post_id, 'mpro_portfolio_tag' );
                echo $terms && ! is_wp_error( $terms )
                    ? esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) )
                    : '—';
                break;

            case 'mpro_pf_role':
                $role = MPRO_PF_Meta_Boxes::get_field( $post_id, 'project_role' );
                echo $role ? esc_html( $role ) : '—';
                break;

            case 'mpro_pf_duration':
                $value = MPRO_PF_Meta_Boxes::get_field( $post_id, 'duration_value' );
                $unit  = MPRO_PF_Meta_Boxes::get_field( $post_id, 'duration_unit', 'days' );
                echo $value ? esc_html( $value . ' ' . $unit ) : '—';
                break;
        }
    }

    public static function sortable_columns( $columns ) {
        $columns['mpro_pf_role'] = 'mpro_pf_role';
        return $columns;
    }
}
