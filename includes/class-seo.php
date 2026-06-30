<?php
/**
 * Rank Math SEO Integration
 *
 * Ensures the Portfolio post type and its taxonomies are properly
 * registered with Rank Math SEO PRO so SEO meta boxes, sitemaps,
 * breadcrumbs, and schema all work out of the box.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_SEO {

    public static function init() {
        add_filter( 'rank_math/sitemap/post_types', [ __CLASS__, 'add_to_sitemap' ] );
        add_filter( 'rank_math/researches/skip_post_types', [ __CLASS__, 'allow_seo_analysis' ] );
        add_filter( 'rank_math/schema/post_types', [ __CLASS__, 'add_schema_support' ] );
        add_filter( 'rank_math/sitemap/exclude_terms', [ __CLASS__, 'include_taxonomies_in_sitemap' ], 10, 1 );
        add_action( 'init', [ __CLASS__, 'register_schema_type' ], 20 );
    }

    /**
     * Make sure Portfolio items are included in the XML sitemap.
     */
    public static function add_to_sitemap( $post_types ) {
        $post_types[ MPRO_PF_POST_TYPE ] = MPRO_PF_POST_TYPE;
        return $post_types;
    }

    /**
     * Make sure Rank Math's content analysis (SEO score, readability)
     * runs on Portfolio items.
     */
    public static function allow_seo_analysis( $post_types ) {
        return array_diff( $post_types, [ MPRO_PF_POST_TYPE ] );
    }

    /**
     * Allow Schema markup (Article / CreativeWork) to be assigned
     * to Portfolio items via the Rank Math meta box.
     */
    public static function add_schema_support( $post_types ) {
        $post_types[] = MPRO_PF_POST_TYPE;
        return $post_types;
    }

    public static function include_taxonomies_in_sitemap( $excluded ) {
        // Ensure portfolio taxonomies are not excluded from the sitemap
        $excluded = array_diff( $excluded, [ 'mpro_portfolio_category', 'mpro_portfolio_tag' ] );
        return $excluded;
    }

    /**
     * Suggest CreativeWork schema as default for this post type if
     * Rank Math is active and no schema is already set.
     */
    public static function register_schema_type() {
        if ( ! class_exists( 'RankMath' ) && ! defined( 'RANK_MATH_VERSION' ) ) return;
        // Rank Math reads default schema per post type from its own settings UI;
        // this plugin only ensures Portfolio is selectable there via the filters above.
    }
}
