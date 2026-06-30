<?php
/**
 * Single Elementor Bridge Template
 *
 * Used when a single Portfolio item template has been selected
 * in Display Styles → Single Item Template. Renders the chosen
 * Elementor template using Elementor's own rendering pipeline
 * (so Elementor Theme Builder dynamic tags work as expected).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();

    $settings    = MPRO_PF_Styles::get_settings();
    $template_id = absint( $settings['elementor_single_template'] );

    if ( $template_id && class_exists( '\Elementor\Plugin' ) ) {
        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );
    } else {
        // Fallback if template was removed after being selected
        the_content();
    }

endwhile;

get_footer();
