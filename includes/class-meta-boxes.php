<?php
/**
 * Meta Boxes — Portfolio Item Details
 *
 * Fields:
 * - Short description
 * - Meta details (client, project URL, etc. — repeatable key/value)
 * - Publish date is native (post date), implementation date is custom
 * - Project duration
 * - Tools used (repeatable)
 * - People involved (repeatable: name + role)
 * - Display style (linked to MPRO_PF_Styles)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MPRO_PF_Meta_Boxes {

    const NONCE_ACTION = 'mpro_pf_save_meta';
    const NONCE_NAME   = 'mpro_pf_meta_nonce';

    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'register' ] );
        add_action( 'save_post_' . MPRO_PF_POST_TYPE, [ __CLASS__, 'save' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
    }

    public static function enqueue_admin_assets( $hook ) {
        global $post_type;
        if ( $post_type !== MPRO_PF_POST_TYPE ) return;
        if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) return;

        wp_enqueue_style( 'mpro-pf-admin', MPRO_PF_URL . 'css/mpro-portfolio-admin.css', [], MPRO_PF_VERSION );
        wp_enqueue_script( 'mpro-pf-admin', MPRO_PF_URL . 'js/mpro-portfolio-admin.js', [ 'jquery' ], MPRO_PF_VERSION, true );
        wp_enqueue_media();
    }

    public static function register() {
        add_meta_box(
            'mpro_pf_summary',
            'Summary',
            [ __CLASS__, 'render_summary' ],
            MPRO_PF_POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'mpro_pf_project_details',
            'Project Details',
            [ __CLASS__, 'render_project_details' ],
            MPRO_PF_POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'mpro_pf_tools',
            'Tools Used',
            [ __CLASS__, 'render_tools' ],
            MPRO_PF_POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'mpro_pf_people',
            'People Involved',
            [ __CLASS__, 'render_people' ],
            MPRO_PF_POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'mpro_pf_meta_details',
            'Meta Details',
            [ __CLASS__, 'render_meta_details' ],
            MPRO_PF_POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'mpro_pf_display',
            'Display Style',
            [ __CLASS__, 'render_display_style' ],
            MPRO_PF_POST_TYPE,
            'side',
            'default'
        );
    }

    /* ─────────────────────────────────────────
       SUMMARY — short description
    ───────────────────────────────────────── */
    public static function render_summary( $post ) {
        wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
        $short_desc = get_post_meta( $post->ID, '_mpro_pf_short_description', true );
        ?>
        <p class="description">A brief summary shown on portfolio cards and archive listings. The full case-study content goes in the main editor below.</p>
        <textarea name="mpro_pf_short_description" rows="3" style="width:100%;"
            placeholder="One or two sentences describing this project..."><?php echo esc_textarea( $short_desc ); ?></textarea>
        <?php
    }

    /* ─────────────────────────────────────────
       PROJECT DETAILS — dates, duration, role
    ───────────────────────────────────────── */
    public static function render_project_details( $post ) {
        $implementation_date = get_post_meta( $post->ID, '_mpro_pf_implementation_date', true );
        $duration_value       = get_post_meta( $post->ID, '_mpro_pf_duration_value', true );
        $duration_unit        = get_post_meta( $post->ID, '_mpro_pf_duration_unit', true ) ?: 'days';
        $project_role         = get_post_meta( $post->ID, '_mpro_pf_project_role', true );
        $project_url          = get_post_meta( $post->ID, '_mpro_pf_project_url', true );
        $client_name          = get_post_meta( $post->ID, '_mpro_pf_client_name', true );
        ?>
        <table class="form-table mpro-pf-table">
            <tr>
                <th><label for="mpro_pf_implementation_date">Implementation Date</label></th>
                <td>
                    <input type="date" id="mpro_pf_implementation_date" name="mpro_pf_implementation_date"
                        value="<?php echo esc_attr( $implementation_date ); ?>">
                    <p class="description">The date this project was actually built or delivered (separate from the WordPress publish date).</p>
                </td>
            </tr>
            <tr>
                <th><label for="mpro_pf_duration_value">Project Duration</label></th>
                <td>
                    <input type="number" min="0" step="1" id="mpro_pf_duration_value" name="mpro_pf_duration_value"
                        value="<?php echo esc_attr( $duration_value ); ?>" style="width:90px;">
                    <select name="mpro_pf_duration_unit">
                        <option value="days"   <?php selected( $duration_unit, 'days' ); ?>>Days</option>
                        <option value="weeks"  <?php selected( $duration_unit, 'weeks' ); ?>>Weeks</option>
                        <option value="months" <?php selected( $duration_unit, 'months' ); ?>>Months</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="mpro_pf_project_role">Role in Project</label></th>
                <td>
                    <input type="text" id="mpro_pf_project_role" name="mpro_pf_project_role"
                        value="<?php echo esc_attr( $project_role ); ?>" class="regular-text"
                        placeholder="e.g. Full-stack Developer, Lead Designer">
                </td>
            </tr>
            <tr>
                <th><label for="mpro_pf_client_name">Client</label></th>
                <td>
                    <input type="text" id="mpro_pf_client_name" name="mpro_pf_client_name"
                        value="<?php echo esc_attr( $client_name ); ?>" class="regular-text"
                        placeholder="Client or company name">
                </td>
            </tr>
            <tr>
                <th><label for="mpro_pf_project_url">Project URL</label></th>
                <td>
                    <input type="url" id="mpro_pf_project_url" name="mpro_pf_project_url"
                        value="<?php echo esc_attr( $project_url ); ?>" class="regular-text"
                        placeholder="https://example.com">
                </td>
            </tr>
        </table>
        <?php
    }

    /* ─────────────────────────────────────────
       TOOLS USED — repeatable list
    ───────────────────────────────────────── */
    public static function render_tools( $post ) {
        $tools = get_post_meta( $post->ID, '_mpro_pf_tools', true );
        $tools = is_array( $tools ) ? $tools : [];
        if ( empty( $tools ) ) $tools = [ '' ];
        ?>
        <div id="mpro-pf-tools-wrap" class="mpro-pf-repeater">
            <div class="mpro-pf-repeater-rows">
                <?php foreach ( $tools as $tool ) : ?>
                <div class="mpro-pf-repeater-row">
                    <input type="text" name="mpro_pf_tools[]" value="<?php echo esc_attr( $tool ); ?>"
                        class="regular-text" placeholder="e.g. WordPress, Figma, PHP">
                    <button type="button" class="button mpro-pf-remove-row">Remove</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button mpro-pf-add-row" data-target="mpro-pf-tools-wrap" data-name="mpro_pf_tools[]" data-placeholder="e.g. WordPress, Figma, PHP">+ Add Tool</button>
        </div>
        <?php
    }

    /* ─────────────────────────────────────────
       PEOPLE INVOLVED — repeatable name + role
    ───────────────────────────────────────── */
    public static function render_people( $post ) {
        $people = get_post_meta( $post->ID, '_mpro_pf_people', true );
        $people = is_array( $people ) ? $people : [];
        if ( empty( $people ) ) $people = [ [ 'name' => '', 'role' => '' ] ];
        ?>
        <div id="mpro-pf-people-wrap" class="mpro-pf-repeater">
            <div class="mpro-pf-repeater-rows">
                <?php foreach ( $people as $person ) : ?>
                <div class="mpro-pf-repeater-row mpro-pf-people-row">
                    <input type="text" name="mpro_pf_people_name[]" value="<?php echo esc_attr( $person['name'] ?? '' ); ?>"
                        class="regular-text" placeholder="Name">
                    <input type="text" name="mpro_pf_people_role[]" value="<?php echo esc_attr( $person['role'] ?? '' ); ?>"
                        class="regular-text" placeholder="Role, e.g. Designer">
                    <button type="button" class="button mpro-pf-remove-row">Remove</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button mpro-pf-add-row" data-target="mpro-pf-people-wrap" data-people-row="1">+ Add Person</button>
        </div>
        <?php
    }

    /* ─────────────────────────────────────────
       META DETAILS — repeatable key/value pairs
    ───────────────────────────────────────── */
    public static function render_meta_details( $post ) {
        $details = get_post_meta( $post->ID, '_mpro_pf_meta_details', true );
        $details = is_array( $details ) ? $details : [];
        if ( empty( $details ) ) $details = [ [ 'label' => '', 'value' => '' ] ];
        ?>
        <p class="description">Custom key/value pairs displayed in the project details panel — e.g. "Industry: Real Estate", "Platform: WooCommerce".</p>
        <div id="mpro-pf-meta-wrap" class="mpro-pf-repeater">
            <div class="mpro-pf-repeater-rows">
                <?php foreach ( $details as $detail ) : ?>
                <div class="mpro-pf-repeater-row mpro-pf-meta-row">
                    <input type="text" name="mpro_pf_meta_label[]" value="<?php echo esc_attr( $detail['label'] ?? '' ); ?>"
                        class="regular-text" placeholder="Label, e.g. Industry">
                    <input type="text" name="mpro_pf_meta_value[]" value="<?php echo esc_attr( $detail['value'] ?? '' ); ?>"
                        class="regular-text" placeholder="Value, e.g. Real Estate">
                    <button type="button" class="button mpro-pf-remove-row">Remove</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button mpro-pf-add-row" data-target="mpro-pf-meta-wrap" data-meta-row="1">+ Add Detail</button>
        </div>
        <?php
    }

    /* ─────────────────────────────────────────
       DISPLAY STYLE — card style picker
    ───────────────────────────────────────── */
    public static function render_display_style( $post ) {
        $current = get_post_meta( $post->ID, '_mpro_pf_card_style', true ) ?: 'style-1';
        $styles  = MPRO_PF_Styles::get_card_styles();
        ?>
        <p class="description">Card style used when this item appears in a grid or featured section. Used unless overridden by the shortcode/widget settings.</p>
        <?php foreach ( $styles as $key => $style ) : ?>
            <label style="display:block;margin-bottom:8px;">
                <input type="radio" name="mpro_pf_card_style" value="<?php echo esc_attr( $key ); ?>" <?php checked( $current, $key ); ?>>
                <?php echo esc_html( $style['label'] ); ?>
            </label>
        <?php endforeach; ?>
        <?php
    }

    /* ─────────────────────────────────────────
       SAVE
    ───────────────────────────────────────── */
    public static function save( $post_id ) {

        if ( ! isset( $_POST[ self::NONCE_NAME ] ) || ! wp_verify_nonce( $_POST[ self::NONCE_NAME ], self::NONCE_ACTION ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        // Short description
        if ( isset( $_POST['mpro_pf_short_description'] ) ) {
            update_post_meta( $post_id, '_mpro_pf_short_description', sanitize_textarea_field( $_POST['mpro_pf_short_description'] ) );
        }

        // Project details
        $fields = [
            'mpro_pf_implementation_date' => '_mpro_pf_implementation_date',
            'mpro_pf_duration_value'      => '_mpro_pf_duration_value',
            'mpro_pf_duration_unit'       => '_mpro_pf_duration_unit',
            'mpro_pf_project_role'        => '_mpro_pf_project_role',
            'mpro_pf_client_name'         => '_mpro_pf_client_name',
        ];
        foreach ( $fields as $post_key => $meta_key ) {
            if ( isset( $_POST[ $post_key ] ) ) {
                update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
            }
        }

        if ( isset( $_POST['mpro_pf_project_url'] ) ) {
            update_post_meta( $post_id, '_mpro_pf_project_url', esc_url_raw( $_POST['mpro_pf_project_url'] ) );
        }

        // Tools (repeatable, flat array)
        if ( isset( $_POST['mpro_pf_tools'] ) && is_array( $_POST['mpro_pf_tools'] ) ) {
            $tools = array_filter( array_map( 'sanitize_text_field', $_POST['mpro_pf_tools'] ) );
            update_post_meta( $post_id, '_mpro_pf_tools', array_values( $tools ) );
        } else {
            update_post_meta( $post_id, '_mpro_pf_tools', [] );
        }

        // People (repeatable, name + role pairs)
        if ( isset( $_POST['mpro_pf_people_name'] ) && is_array( $_POST['mpro_pf_people_name'] ) ) {
            $names = array_map( 'sanitize_text_field', $_POST['mpro_pf_people_name'] );
            $roles = array_map( 'sanitize_text_field', $_POST['mpro_pf_people_role'] ?? [] );
            $people = [];
            foreach ( $names as $i => $name ) {
                if ( $name === '' && empty( $roles[ $i ] ) ) continue;
                $people[] = [ 'name' => $name, 'role' => $roles[ $i ] ?? '' ];
            }
            update_post_meta( $post_id, '_mpro_pf_people', $people );
        } else {
            update_post_meta( $post_id, '_mpro_pf_people', [] );
        }

        // Meta details (repeatable, label + value pairs)
        if ( isset( $_POST['mpro_pf_meta_label'] ) && is_array( $_POST['mpro_pf_meta_label'] ) ) {
            $labels = array_map( 'sanitize_text_field', $_POST['mpro_pf_meta_label'] );
            $values = array_map( 'sanitize_text_field', $_POST['mpro_pf_meta_value'] ?? [] );
            $details = [];
            foreach ( $labels as $i => $label ) {
                if ( $label === '' && empty( $values[ $i ] ) ) continue;
                $details[] = [ 'label' => $label, 'value' => $values[ $i ] ?? '' ];
            }
            update_post_meta( $post_id, '_mpro_pf_meta_details', $details );
        } else {
            update_post_meta( $post_id, '_mpro_pf_meta_details', [] );
        }

        // Card style
        if ( isset( $_POST['mpro_pf_card_style'] ) ) {
            update_post_meta( $post_id, '_mpro_pf_card_style', sanitize_text_field( $_POST['mpro_pf_card_style'] ) );
        }
    }

    /* ─────────────────────────────────────────
       Helper getters used by templates
    ───────────────────────────────────────── */
    public static function get_field( $post_id, $key, $default = '' ) {
        $value = get_post_meta( $post_id, '_mpro_pf_' . $key, true );
        return $value === '' ? $default : $value;
    }
}
