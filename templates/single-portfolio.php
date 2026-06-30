<?php
/**
 * Default Single Template — Portfolio Item
 *
 * Used when no Elementor single template is configured and no
 * theme override (single-mpro_portfolio.php) exists.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post();
    $post_id = get_the_ID();

    $short_desc     = MPRO_PF_Meta_Boxes::get_field( $post_id, 'short_description' );
    $client_name    = MPRO_PF_Meta_Boxes::get_field( $post_id, 'client_name' );
    $project_role   = MPRO_PF_Meta_Boxes::get_field( $post_id, 'project_role' );
    $project_url    = MPRO_PF_Meta_Boxes::get_field( $post_id, 'project_url' );
    $impl_date      = MPRO_PF_Meta_Boxes::get_field( $post_id, 'implementation_date' );
    $duration_value = MPRO_PF_Meta_Boxes::get_field( $post_id, 'duration_value' );
    $duration_unit  = MPRO_PF_Meta_Boxes::get_field( $post_id, 'duration_unit', 'days' );
    $tools          = get_post_meta( $post_id, '_mpro_pf_tools', true );
    $people         = get_post_meta( $post_id, '_mpro_pf_people', true );
    $meta_details   = get_post_meta( $post_id, '_mpro_pf_meta_details', true );
    $categories     = get_the_terms( $post_id, 'mpro_portfolio_category' );
    $tags           = get_the_terms( $post_id, 'mpro_portfolio_tag' );
    ?>

    <article <?php post_class( 'mpro-pf-single' ); ?>>

        <header class="mpro-pf-single-header">
            <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
            <div class="mpro-pf-single-cats">
                <?php foreach ( $categories as $cat ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="mpro-pf-cat-pill"><?php echo esc_html( $cat->name ); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h1 class="mpro-pf-single-title"><?php the_title(); ?></h1>

            <?php if ( $short_desc ) : ?>
                <p class="mpro-pf-single-summary"><?php echo esc_html( $short_desc ); ?></p>
            <?php endif; ?>
        </header>

        <?php if ( has_post_thumbnail() ) : ?>
        <div class="mpro-pf-single-cover">
            <?php the_post_thumbnail( 'full' ); ?>
        </div>
        <?php endif; ?>

        <div class="mpro-pf-single-layout">

            <div class="mpro-pf-single-content">
                <?php the_content(); ?>
            </div>

            <aside class="mpro-pf-single-sidebar">
                <div class="mpro-pf-detail-panel">
                    <h3>Project Details</h3>
                    <dl class="mpro-pf-detail-list">

                        <?php if ( $client_name ) : ?>
                        <div class="mpro-pf-detail-row">
                            <dt>Client</dt><dd><?php echo esc_html( $client_name ); ?></dd>
                        </div>
                        <?php endif; ?>

                        <?php if ( $project_role ) : ?>
                        <div class="mpro-pf-detail-row">
                            <dt>Role</dt><dd><?php echo esc_html( $project_role ); ?></dd>
                        </div>
                        <?php endif; ?>

                        <?php if ( $impl_date ) : ?>
                        <div class="mpro-pf-detail-row">
                            <dt>Implementation Date</dt>
                            <dd><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $impl_date ) ) ); ?></dd>
                        </div>
                        <?php endif; ?>

                        <?php if ( $duration_value ) : ?>
                        <div class="mpro-pf-detail-row">
                            <dt>Duration</dt><dd><?php echo esc_html( $duration_value . ' ' . $duration_unit ); ?></dd>
                        </div>
                        <?php endif; ?>

                        <div class="mpro-pf-detail-row">
                            <dt>Published</dt>
                            <dd><?php echo esc_html( get_the_date() ); ?></dd>
                        </div>

                        <?php if ( $project_url ) : ?>
                        <div class="mpro-pf-detail-row">
                            <dt>Live URL</dt>
                            <dd><a href="<?php echo esc_url( $project_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( preg_replace( '#^https?://#', '', $project_url ) ); ?></a></dd>
                        </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $meta_details ) && is_array( $meta_details ) ) : ?>
                            <?php foreach ( $meta_details as $detail ) : if ( empty( $detail['label'] ) ) continue; ?>
                            <div class="mpro-pf-detail-row">
                                <dt><?php echo esc_html( $detail['label'] ); ?></dt>
                                <dd><?php echo esc_html( $detail['value'] ); ?></dd>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </dl>
                </div>

                <?php if ( ! empty( $tools ) && is_array( $tools ) ) : ?>
                <div class="mpro-pf-detail-panel">
                    <h3>Tools Used</h3>
                    <ul class="mpro-pf-tools-list">
                        <?php foreach ( $tools as $tool ) : ?>
                            <li><?php echo esc_html( $tool ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $people ) && is_array( $people ) ) : ?>
                <div class="mpro-pf-detail-panel">
                    <h3>People Involved</h3>
                    <ul class="mpro-pf-people-list">
                        <?php foreach ( $people as $person ) : if ( empty( $person['name'] ) ) continue; ?>
                            <li>
                                <strong><?php echo esc_html( $person['name'] ); ?></strong>
                                <?php if ( ! empty( $person['role'] ) ) : ?>
                                    <span><?php echo esc_html( $person['role'] ); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
                <div class="mpro-pf-detail-panel">
                    <h3>Tags</h3>
                    <div class="mpro-pf-card-tags">
                        <?php foreach ( $tags as $tag ) : ?>
                            <a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="mpro-pf-tag"><?php echo esc_html( $tag->name ); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>

        </div>
    </article>

    <?php
endwhile;

get_footer();
