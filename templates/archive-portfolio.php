<?php
/**
 * Default Archive Template — Portfolio
 *
 * Used when no theme override (archive-mpro_portfolio.php) exists.
 * Renders all published Portfolio items using the configured
 * archive card style.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$settings = MPRO_PF_Styles::get_settings();
$style    = $settings['archive_card_style'];
?>

<div class="mpro-pf-archive">
    <header class="mpro-pf-archive-header">
        <h1 class="mpro-pf-archive-title">Portfolio</h1>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="mpro-pf-grid mpro-pf-grid--cols-3">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="mpro-pf-grid-item">
                    <?php MPRO_PF_Styles::render_card( get_the_ID(), $style ); ?>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="mpro-pf-pagination">
            <?php
            the_posts_pagination( [
                'prev_text' => '&larr; Previous',
                'next_text' => 'Next &rarr;',
            ] );
            ?>
        </div>
    <?php else : ?>
        <p class="mpro-pf-empty">No portfolio items found.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
