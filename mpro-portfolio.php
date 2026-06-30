<?php
/**
 * Plugin Name: MPRO Portfolio
 * Plugin URI:  https://moghadam.pro
 * Description: A complete portfolio / case-study management system with custom card styles, Elementor template support, and Rank Math SEO integration. Part of the MPRO suite.
 * Version:     1.0.1
 * Author:      Sayid Moghadam
 * Author URI:  https://moghadam.pro
 * License:     GPL-2.0+
 * Text Domain: mpro-portfolio
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'MPRO_PF_VERSION', '1.0.1' );
define( 'MPRO_PF_PATH', plugin_dir_path( __FILE__ ) );
define( 'MPRO_PF_URL',  plugin_dir_url( __FILE__ ) );
define( 'MPRO_PF_POST_TYPE', 'mpro_portfolio' );

/* ---------------------------------------------------------------
   INCLUDES
--------------------------------------------------------------- */
require_once MPRO_PF_PATH . 'includes/class-cpt.php';
require_once MPRO_PF_PATH . 'includes/class-taxonomies.php';
require_once MPRO_PF_PATH . 'includes/class-meta-boxes.php';
require_once MPRO_PF_PATH . 'includes/class-styles.php';
require_once MPRO_PF_PATH . 'includes/class-shortcodes.php';
require_once MPRO_PF_PATH . 'includes/class-elementor.php';
require_once MPRO_PF_PATH . 'includes/class-seo.php';
require_once MPRO_PF_PATH . 'includes/class-admin-menu.php';
require_once MPRO_PF_PATH . 'includes/class-admin-columns.php';
require_once MPRO_PF_PATH . 'includes/class-templates.php';

/* ---------------------------------------------------------------
   BOOT
--------------------------------------------------------------- */
function mpro_pf_init() {
    MPRO_PF_CPT::init();
    MPRO_PF_Taxonomies::init();
    MPRO_PF_Meta_Boxes::init();
    MPRO_PF_Styles::init();
    MPRO_PF_Shortcodes::init();
    MPRO_PF_Elementor::init();
    MPRO_PF_SEO::init();
    MPRO_PF_Admin_Menu::init();
    MPRO_PF_Admin_Columns::init();
    MPRO_PF_Templates::init();
}
add_action( 'plugins_loaded', 'mpro_pf_init' );

/* ---------------------------------------------------------------
   FRONTEND ASSETS
--------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', 'mpro_pf_enqueue_assets' );
function mpro_pf_enqueue_assets() {
    if ( ! is_singular( MPRO_PF_POST_TYPE )
        && ! is_post_type_archive( MPRO_PF_POST_TYPE )
        && ! is_tax( 'mpro_portfolio_category' )
        && ! is_tax( 'mpro_portfolio_tag' )
        && ! mpro_pf_page_has_shortcode() ) {
        return;
    }

    wp_enqueue_style(
        'mpro-portfolio',
        MPRO_PF_URL . 'css/mpro-portfolio.css',
        [],
        MPRO_PF_VERSION
    );

    wp_enqueue_script(
        'mpro-portfolio',
        MPRO_PF_URL . 'js/mpro-portfolio.js',
        [],
        MPRO_PF_VERSION,
        true
    );
}

function mpro_pf_page_has_shortcode() {
    global $post;
    if ( ! $post instanceof WP_Post ) return false;
    return has_shortcode( $post->post_content, 'mpro_portfolio_grid' )
        || has_shortcode( $post->post_content, 'mpro_portfolio_featured' )
        || has_shortcode( $post->post_content, 'mpro_portfolio_single' );
}

/* ---------------------------------------------------------------
   ACTIVATION / DEACTIVATION
--------------------------------------------------------------- */
register_activation_hook( __FILE__, 'mpro_pf_activate' );
function mpro_pf_activate() {
    require_once MPRO_PF_PATH . 'includes/class-cpt.php';
    require_once MPRO_PF_PATH . 'includes/class-taxonomies.php';
    MPRO_PF_CPT::register_post_type();
    MPRO_PF_Taxonomies::register_taxonomies();
    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'mpro_pf_deactivate' );
function mpro_pf_deactivate() {
    flush_rewrite_rules();
}
