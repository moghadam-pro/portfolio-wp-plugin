<?php
/**
 * Display Styles
 *
 * Three built-in card styles for now (placeholders, intended to be refined
 * in future versions) plus support for registering Elementor-saved templates
 * as additional selectable styles.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_Styles {

    const OPTION_KEY = 'mpro_pf_style_settings';

    public static function init() {
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    public static function register_settings() {
        register_setting( 'mpro_pf_styles_group', self::OPTION_KEY, [
            'sanitize_callback' => [ __CLASS__, 'sanitize_settings' ],
        ] );
    }

    public static function sanitize_settings( $input ) {
        return [
            'default_card_style'    => sanitize_text_field( $input['default_card_style'] ?? 'style-1' ),
            'archive_card_style'    => sanitize_text_field( $input['archive_card_style'] ?? 'style-1' ),
            'single_template'       => sanitize_text_field( $input['single_template'] ?? 'default' ),
            'elementor_card_templates'  => array_map( 'absint', (array) ( $input['elementor_card_templates'] ?? [] ) ),
            'elementor_single_template' => absint( $input['elementor_single_template'] ?? 0 ),
        ];
    }

    public static function get_settings() {
        $defaults = [
            'default_card_style'        => 'style-1',
            'archive_card_style'        => 'style-1',
            'single_template'           => 'default',
            'elementor_card_templates'  => [],
            'elementor_single_template' => 0,
        ];
        return wp_parse_args( get_option( self::OPTION_KEY, [] ), $defaults );
    }

    /**
     * Built-in card styles.
     * These are intentionally simple baseline layouts — designed to be
     * refined or replaced in future plugin versions.
     */
    public static function get_card_styles() {
        $styles = [
            'style-1' => [
                'label'       => 'Style 1 — Minimal (image, title, category)',
                'description' => 'Cover image with title and category overlay. Clean and minimal.',
                'render'      => [ __CLASS__, 'render_card_style_1' ],
            ],
            'style-2' => [
                'label'       => 'Style 2 — Detailed (image, title, excerpt, tags)',
                'description' => 'Cover image, title, short description, and tag list below.',
                'render'      => [ __CLASS__, 'render_card_style_2' ],
            ],
            'style-3' => [
                'label'       => 'Style 3 — List row (horizontal layout)',
                'description' => 'Horizontal row layout — image on one side, details on the other. Good for dense archive listings.',
                'render'      => [ __CLASS__, 'render_card_style_3' ],
            ],
        ];

        // Append any Elementor-saved templates registered as card styles
        $settings = self::get_settings();
        if ( ! empty( $settings['elementor_card_templates'] ) ) {
            foreach ( $settings['elementor_card_templates'] as $template_id ) {
                $template_id = absint( $template_id );
                if ( ! $template_id || get_post_status( $template_id ) !== 'publish' ) continue;
                $styles[ 'elementor-' . $template_id ] = [
                    'label'       => 'Elementor: ' . get_the_title( $template_id ),
                    'description' => 'Custom Elementor template used as a card style.',
                    'render'      => null, // rendered via Elementor template engine
                    'elementor_template_id' => $template_id,
                ];
            }
        }

        return $styles;
    }

    /**
     * Single (case study) display templates.
     */
    public static function get_single_templates() {
        $templates = [
            'default' => [
                'label' => 'Default Single Template',
            ],
        ];

        $settings = self::get_settings();
        if ( ! empty( $settings['elementor_single_template'] ) ) {
            $template_id = absint( $settings['elementor_single_template'] );
            if ( $template_id && get_post_status( $template_id ) === 'publish' ) {
                $templates[ 'elementor-' . $template_id ] = [
                    'label' => 'Elementor: ' . get_the_title( $template_id ),
                ];
            }
        }

        return $templates;
    }

    /* ─────────────────────────────────────────
       Renderer dispatch
    ───────────────────────────────────────── */
    public static function render_card( $post_id, $style_key = null ) {
        $styles = self::get_card_styles();

        if ( ! $style_key ) {
            $style_key = MPRO_PF_Meta_Boxes::get_field( $post_id, 'card_style', self::get_settings()['default_card_style'] );
        }

        if ( ! isset( $styles[ $style_key ] ) ) {
            $style_key = 'style-1';
        }

        $style = $styles[ $style_key ];

        // Elementor template card
        if ( ! empty( $style['elementor_template_id'] ) ) {
            if ( class_exists( '\Elementor\Plugin' ) ) {
                echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $style['elementor_template_id'] );
                return;
            }
            // Elementor deactivated — fall back to the default built-in style
            call_user_func( $styles['style-1']['render'], $post_id );
            return;
        }

        if ( is_callable( $style['render'] ) ) {
            call_user_func( $style['render'], $post_id );
        }
    }

    public static function render_card_style_1( $post_id ) {
        $title    = get_the_title( $post_id );
        $link     = get_permalink( $post_id );
        $thumb    = get_the_post_thumbnail( $post_id, 'large', [ 'class' => 'mpro-pf-card-img' ] );
        $cats     = get_the_terms( $post_id, 'mpro_portfolio_category' );
        $cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
        ?>
        <a href="<?php echo esc_url( $link ); ?>" class="mpro-pf-card mpro-pf-card--style-1">
            <div class="mpro-pf-card-media"><?php echo $thumb; ?></div>
            <div class="mpro-pf-card-overlay">
                <?php if ( $cat_name ) : ?><span class="mpro-pf-card-cat"><?php echo esc_html( $cat_name ); ?></span><?php endif; ?>
                <h3 class="mpro-pf-card-title"><?php echo esc_html( $title ); ?></h3>
            </div>
        </a>
        <?php
    }

    public static function render_card_style_2( $post_id ) {
        $title     = get_the_title( $post_id );
        $link      = get_permalink( $post_id );
        $thumb     = get_the_post_thumbnail( $post_id, 'large', [ 'class' => 'mpro-pf-card-img' ] );
        $short_desc = MPRO_PF_Meta_Boxes::get_field( $post_id, 'short_description' );
        $tags      = get_the_terms( $post_id, 'mpro_portfolio_tag' );
        ?>
        <a href="<?php echo esc_url( $link ); ?>" class="mpro-pf-card mpro-pf-card--style-2">
            <div class="mpro-pf-card-media"><?php echo $thumb; ?></div>
            <div class="mpro-pf-card-body">
                <h3 class="mpro-pf-card-title"><?php echo esc_html( $title ); ?></h3>
                <?php if ( $short_desc ) : ?><p class="mpro-pf-card-desc"><?php echo esc_html( $short_desc ); ?></p><?php endif; ?>
                <?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
                <div class="mpro-pf-card-tags">
                    <?php foreach ( $tags as $tag ) : ?>
                        <span class="mpro-pf-tag"><?php echo esc_html( $tag->name ); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </a>
        <?php
    }

    public static function render_card_style_3( $post_id ) {
        $title      = get_the_title( $post_id );
        $link       = get_permalink( $post_id );
        $thumb      = get_the_post_thumbnail( $post_id, 'medium', [ 'class' => 'mpro-pf-card-img' ] );
        $short_desc = MPRO_PF_Meta_Boxes::get_field( $post_id, 'short_description' );
        $cats       = get_the_terms( $post_id, 'mpro_portfolio_category' );
        $cat_name   = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
        ?>
        <a href="<?php echo esc_url( $link ); ?>" class="mpro-pf-card mpro-pf-card--style-3">
            <div class="mpro-pf-card-media"><?php echo $thumb; ?></div>
            <div class="mpro-pf-card-body">
                <?php if ( $cat_name ) : ?><span class="mpro-pf-card-cat"><?php echo esc_html( $cat_name ); ?></span><?php endif; ?>
                <h3 class="mpro-pf-card-title"><?php echo esc_html( $title ); ?></h3>
                <?php if ( $short_desc ) : ?><p class="mpro-pf-card-desc"><?php echo esc_html( $short_desc ); ?></p><?php endif; ?>
            </div>
        </a>
        <?php
    }
}
