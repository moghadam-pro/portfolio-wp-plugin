<?php
/**
 * Template Loader
 *
 * Routes single Portfolio item requests to either the plugin's
 * default single template or a selected Elementor template.
 * Routes archive requests to a default archive template that
 * uses the configured archive card style.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_Templates {

    public static function init() {
        add_filter( 'single_template', [ __CLASS__, 'load_single_template' ] );
        add_filter( 'archive_template', [ __CLASS__, 'load_archive_template' ] );
    }

    public static function load_single_template( $template ) {
        if ( ! is_singular( MPRO_PF_POST_TYPE ) ) return $template;

        $settings = MPRO_PF_Styles::get_settings();
        $template_id = absint( $settings['elementor_single_template'] );

        // If an Elementor single template is configured, let Elementor handle rendering
        // by short-circuiting to our bridge template, which calls Elementor's content method.
        if ( $template_id && class_exists( '\Elementor\Plugin' ) ) {
            $bridge = MPRO_PF_PATH . 'templates/single-elementor-bridge.php';
            if ( file_exists( $bridge ) ) return $bridge;
        }

        // Otherwise use the plugin's own default single template if present,
        // unless the active theme already provides one (theme override wins).
        if ( locate_template( [ 'single-' . MPRO_PF_POST_TYPE . '.php' ] ) ) {
            return $template;
        }

        $default = MPRO_PF_PATH . 'templates/single-portfolio.php';
        if ( file_exists( $default ) ) return $default;

        return $template;
    }

    public static function load_archive_template( $template ) {
        if ( ! is_post_type_archive( MPRO_PF_POST_TYPE ) ) return $template;

        if ( locate_template( [ 'archive-' . MPRO_PF_POST_TYPE . '.php' ] ) ) {
            return $template;
        }

        $default = MPRO_PF_PATH . 'templates/archive-portfolio.php';
        if ( file_exists( $default ) ) return $default;

        return $template;
    }
}
