<?php
/**
 * Elementor Integration
 *
 * Allows Elementor-saved templates (Theme Builder / Saved Templates)
 * to be selected as additional card styles or as the single item template,
 * via the Display Styles settings screen.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_Elementor {

    public static function init() {
        add_action( 'elementor/elements/categories_registered', [ __CLASS__, 'register_category' ] );
    }

    public static function is_elementor_active() {
        return did_action( 'elementor/loaded' );
    }

    public static function register_category( $elements_manager ) {
        $elements_manager->add_category( 'mpro-portfolio', [
            'title' => 'MPRO Portfolio',
            'icon'  => 'fa fa-plug',
        ] );
    }

    /**
     * Returns all Elementor "saved templates" the admin can pick from,
     * used in the Display Styles settings screen.
     */
    public static function get_available_templates() {
        if ( ! self::is_elementor_active() ) return [];

        $templates = get_posts( [
            'post_type'      => 'elementor_library',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ] );

        $list = [];
        foreach ( $templates as $template ) {
            $list[ $template->ID ] = $template->post_title;
        }
        return $list;
    }
}
