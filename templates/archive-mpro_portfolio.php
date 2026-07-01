<?php
/**
 * Default Portfolio archive and taxonomy template.
 *
 * This template uses the WordPress main query. Themes can override it with:
 * mpro-portfolio/archive-mpro_portfolio.php
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
MPRO_Portfolio_Renderer::enqueue_assets();
$settings = MPRO_Portfolio_Settings::get();
$title    = is_tax() ? single_term_title( '', false ) : post_type_archive_title( '', false );
$description = is_tax() ? term_description() : '';
?>
<main class="mpro-portfolio-archive">
	<header class="mpro-portfolio-archive__header">
		<h1 class="mpro-portfolio-archive__title"><?php echo esc_html( $title ? $title : __( 'Portfolio', 'mpro-portfolio' ) ); ?></h1>
		<?php if ( $description ) : ?><div class="mpro-portfolio-archive__description"><?php echo wp_kses_post( $description ); ?></div><?php endif; ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="mpro-portfolio-grid" style="--mpro-columns:<?php echo esc_attr( $settings['archive_columns'] ); ?>;--mpro-columns-tablet:2;--mpro-columns-mobile:1;">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php echo MPRO_Portfolio_Renderer::render_card( get_the_ID(), $settings['archive_card_style'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endwhile; ?>
		</div>

		<div class="mpro-portfolio-pagination">
			<?php
			the_posts_pagination(
				array(
					'prev_text' => __( 'Previous', 'mpro-portfolio' ),
					'next_text' => __( 'Next', 'mpro-portfolio' ),
				)
			);
			?>
		</div>
	<?php else : ?>
		<p class="mpro-portfolio-empty"><?php esc_html_e( 'No portfolio items found.', 'mpro-portfolio' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
