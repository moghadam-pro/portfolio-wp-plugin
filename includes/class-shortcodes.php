<?php
/**
 * Shortcodes
 *
 * [mpro_portfolio_grid]    — archive-style grid, queries all/filtered items
 * [mpro_portfolio_featured] — manually selected items (for homepage sections)
 * [mpro_portfolio_single]   — embed a single portfolio item's card anywhere
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_Shortcodes {

    public static function init() {
        add_shortcode( 'mpro_portfolio_grid', [ __CLASS__, 'render_grid' ] );
        add_shortcode( 'mpro_portfolio_featured', [ __CLASS__, 'render_featured' ] );
        add_shortcode( 'mpro_portfolio_single', [ __CLASS__, 'render_single_card' ] );
    }

    /**
     * [mpro_portfolio_grid count="12" category="" tag="" style="style-1" columns="3"]
     */
    public static function render_grid( $atts ) {
        $a = shortcode_atts( [
            'count'    => -1,
            'category' => '',
            'tag'      => '',
            'style'    => '',
            'columns'  => 3,
            'orderby'  => 'date',
            'order'    => 'DESC',
        ], $atts, 'mpro_portfolio_grid' );

        $query_args = [
            'post_type'      => MPRO_PF_POST_TYPE,
            'posts_per_page' => (int) $a['count'],
            'orderby'        => sanitize_text_field( $a['orderby'] ),
            'order'          => sanitize_text_field( $a['order'] ),
        ];

        $tax_query = [];
        if ( $a['category'] ) {
            $tax_query[] = [
                'taxonomy' => 'mpro_portfolio_category',
                'field'    => 'slug',
                'terms'    => array_map( 'trim', explode( ',', $a['category'] ) ),
            ];
        }
        if ( $a['tag'] ) {
            $tax_query[] = [
                'taxonomy' => 'mpro_portfolio_tag',
                'field'    => 'slug',
                'terms'    => array_map( 'trim', explode( ',', $a['tag'] ) ),
            ];
        }
        if ( $tax_query ) $query_args['tax_query'] = $tax_query;

        $query = new WP_Query( $query_args );
        $columns = max( 1, min( 6, (int) $a['columns'] ) );

        ob_start();
        if ( $query->have_posts() ) :
            ?>
            <div class="mpro-pf-grid mpro-pf-grid--cols-<?php echo esc_attr( $columns ); ?>">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="mpro-pf-grid-item">
                        <?php MPRO_PF_Styles::render_card( get_the_ID(), $a['style'] ?: null ); ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        else :
            echo '<p class="mpro-pf-empty">No portfolio items found.</p>';
        endif;

        return ob_get_clean();
    }

    /**
     * [mpro_portfolio_featured ids="12,45,67" style="style-2" columns="3"]
     * For manually curated homepage sections.
     */
    public static function render_featured( $atts ) {
        $a = shortcode_atts( [
            'ids'     => '',
            'style'   => '',
            'columns' => 3,
        ], $atts, 'mpro_portfolio_featured' );

        $ids = array_filter( array_map( 'absint', explode( ',', $a['ids'] ) ) );
        if ( empty( $ids ) ) return '<p class="mpro-pf-empty">No portfolio items selected.</p>';

        $columns = max( 1, min( 6, (int) $a['columns'] ) );

        $query = new WP_Query( [
            'post_type'      => MPRO_PF_POST_TYPE,
            'post__in'       => $ids,
            'orderby'        => 'post__in',
            'posts_per_page' => count( $ids ),
        ] );

        ob_start();
        if ( $query->have_posts() ) :
            ?>
            <div class="mpro-pf-grid mpro-pf-grid--cols-<?php echo esc_attr( $columns ); ?> mpro-pf-grid--featured">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="mpro-pf-grid-item">
                        <?php MPRO_PF_Styles::render_card( get_the_ID(), $a['style'] ?: null ); ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        endif;

        return ob_get_clean();
    }

    /**
     * [mpro_portfolio_single id="123" style="style-1"]
     * Embed one item's card anywhere.
     */
    public static function render_single_card( $atts ) {
        $a = shortcode_atts( [
            'id'    => 0,
            'style' => '',
        ], $atts, 'mpro_portfolio_single' );

        $id = absint( $a['id'] );
        if ( ! $id || get_post_type( $id ) !== MPRO_PF_POST_TYPE ) {
            return '<p class="mpro-pf-empty">Invalid portfolio item ID.</p>';
        }

        ob_start();
        echo '<div class="mpro-pf-single-embed">';
        MPRO_PF_Styles::render_card( $id, $a['style'] ?: null );
        echo '</div>';
        return ob_get_clean();
    }
}
