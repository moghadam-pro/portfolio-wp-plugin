<?php
/**
 * Admin Menu
 *
 * Registers the Portfolio top-level menu at sidebar position 4,
 * placed before the MPRO menu (which is registered at position 4
 * by other MPRO plugins — Portfolio uses 3.9 so it sorts just above).
 *
 * Submenus:
 * - All Portfolio   (post list table)
 * - Add Portfolio   (new post screen)
 * - Tags            (taxonomy term list)
 * - Categories      (taxonomy term list)
 * - Styles          (custom settings screen)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_Admin_Menu {

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    public static function register_menu() {

        // Top-level menu — positioned just before MPRO (which sits at 4)
        add_menu_page(
            'Portfolio',
            'Portfolio',
            'edit_posts',
            'edit.php?post_type=' . MPRO_PF_POST_TYPE,
            '',
            'dashicons-portfolio',
            3.9
        );

        // All Portfolio (default list table — relabel the auto-generated one)
        add_submenu_page(
            'edit.php?post_type=' . MPRO_PF_POST_TYPE,
            'All Portfolio',
            'All Portfolio',
            'edit_posts',
            'edit.php?post_type=' . MPRO_PF_POST_TYPE
        );

        // Add Portfolio
        add_submenu_page(
            'edit.php?post_type=' . MPRO_PF_POST_TYPE,
            'Add Portfolio',
            'Add Portfolio',
            'edit_posts',
            'post-new.php?post_type=' . MPRO_PF_POST_TYPE
        );

        // Categories
        add_submenu_page(
            'edit.php?post_type=' . MPRO_PF_POST_TYPE,
            'Categories',
            'Categories',
            'manage_categories',
            'edit-tags.php?taxonomy=mpro_portfolio_category&post_type=' . MPRO_PF_POST_TYPE
        );

        // Tags
        add_submenu_page(
            'edit.php?post_type=' . MPRO_PF_POST_TYPE,
            'Tags',
            'Tags',
            'manage_categories',
            'edit-tags.php?taxonomy=mpro_portfolio_tag&post_type=' . MPRO_PF_POST_TYPE
        );

        // Styles (custom settings page)
        add_submenu_page(
            'edit.php?post_type=' . MPRO_PF_POST_TYPE,
            'Display Styles',
            'Styles',
            'manage_options',
            'mpro-portfolio-styles',
            [ __CLASS__, 'render_styles_page' ]
        );
    }

    public static function register_settings() {
        // Settings are registered in MPRO_PF_Styles::register_settings()
    }

    public static function render_styles_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;

        $settings       = MPRO_PF_Styles::get_settings();
        $card_styles    = MPRO_PF_Styles::get_card_styles();
        $elementor_tpls = MPRO_PF_Elementor::get_available_templates();
        $elementor_active = MPRO_PF_Elementor::is_elementor_active();
        ?>
        <div class="wrap" id="mpro-pf-styles-wrap">
            <h1>Portfolio — Display Styles</h1>
            <p class="description">Choose the default card style used in archives and grids, and the single-item template used on individual Portfolio pages. Each Portfolio item can also override the card style individually from its edit screen.</p>

            <?php if ( isset( $_GET['settings-updated'] ) ) : ?>
            <div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'mpro_pf_styles_group' ); ?>

                <h2>Built-in Card Styles</h2>
                <table class="form-table" role="presentation">
                    <?php foreach ( $card_styles as $key => $style ) : if ( strpos( $key, 'elementor-' ) === 0 ) continue; ?>
                    <tr>
                        <th scope="row"><?php echo esc_html( $style['label'] ); ?></th>
                        <td><p class="description"><?php echo esc_html( $style['description'] ); ?></p></td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">Default Card Style</th>
                        <td>
                            <select name="mpro_pf_style_settings[default_card_style]">
                                <?php foreach ( $card_styles as $key => $style ) : ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['default_card_style'], $key ); ?>>
                                    <?php echo esc_html( $style['label'] ); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Used for new Portfolio items unless overridden per-item.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Archive Page Card Style</th>
                        <td>
                            <select name="mpro_pf_style_settings[archive_card_style]">
                                <?php foreach ( $card_styles as $key => $style ) : ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['archive_card_style'], $key ); ?>>
                                    <?php echo esc_html( $style['label'] ); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Card style used on the main Portfolio archive page (<code><?php echo esc_html( home_url( '/portfolio/' ) ); ?></code>).</p>
                        </td>
                    </tr>
                </table>

                <hr>
                <h2>Elementor Templates</h2>
                <?php if ( ! $elementor_active ) : ?>
                    <p class="description">Elementor is not active. Activate Elementor to use saved templates as card or single-item styles.</p>
                <?php else : ?>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">Card Style Templates</th>
                            <td>
                                <?php if ( empty( $elementor_tpls ) ) : ?>
                                    <p class="description">No saved Elementor templates found. Create one in Templates → Saved Templates, then it will appear here.</p>
                                <?php else : ?>
                                    <?php foreach ( $elementor_tpls as $tpl_id => $tpl_title ) : ?>
                                    <label style="display:block;margin-bottom:6px;">
                                        <input type="checkbox" name="mpro_pf_style_settings[elementor_card_templates][]"
                                            value="<?php echo esc_attr( $tpl_id ); ?>"
                                            <?php checked( in_array( $tpl_id, $settings['elementor_card_templates'] ) ); ?>>
                                        <?php echo esc_html( $tpl_title ); ?>
                                    </label>
                                    <?php endforeach; ?>
                                    <p class="description">Checked templates will appear as selectable card styles for individual Portfolio items and in shortcode <code>style</code> parameters (use <code>elementor-{template_id}</code>).</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Single Item Template</th>
                            <td>
                                <select name="mpro_pf_style_settings[elementor_single_template]">
                                    <option value="0">— Use default single template —</option>
                                    <?php foreach ( $elementor_tpls as $tpl_id => $tpl_title ) : ?>
                                    <option value="<?php echo esc_attr( $tpl_id ); ?>" <?php selected( $settings['elementor_single_template'], $tpl_id ); ?>>
                                        <?php echo esc_html( $tpl_title ); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description">If set, this Elementor template renders for all single Portfolio item pages instead of the plugin's default template.</p>
                            </td>
                        </tr>
                    </table>
                <?php endif; ?>

                <?php submit_button( 'Save Styles' ); ?>
            </form>

            <hr>
            <h2>Shortcodes</h2>
            <table class="widefat striped" style="max-width:900px;">
                <thead>
                    <tr><th>Shortcode</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>[mpro_portfolio_grid count="12" category="" tag="" style="" columns="3"]</code></td>
                        <td>Archive-style grid. Queries Portfolio items, optionally filtered by category/tag slug.</td>
                    </tr>
                    <tr>
                        <td><code>[mpro_portfolio_featured ids="12,45,67" style="" columns="3"]</code></td>
                        <td>Manually curated set of items — ideal for homepage "Selected Work" sections.</td>
                    </tr>
                    <tr>
                        <td><code>[mpro_portfolio_single id="123" style=""]</code></td>
                        <td>Embed a single Portfolio item's card anywhere on the site.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}
