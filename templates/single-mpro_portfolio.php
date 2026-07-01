<?php
/**
 * Default single Portfolio template.
 *
 * Themes can override it with mpro-portfolio/single-mpro_portfolio.php.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
MPRO_Portfolio_Renderer::enqueue_assets();

while ( have_posts() ) : the_post();
	$post_id         = get_the_ID();
	$short_desc      = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'short_description' );
	$client          = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'client' );
	$project_url     = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'project_url' );
	$role            = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'role' );
	$implementation  = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'implementation_date' );
	$duration        = MPRO_Portfolio_Meta_Boxes::get_duration_label( $post_id );
	$tools           = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'tools', array() );
	$collaborators   = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'collaborators', array() );
	$meta_details    = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'meta_details', array() );
	$categories      = get_the_terms( $post_id, MPRO_Portfolio_Content_Types::TAX_CATEGORY );
	$tags            = get_the_terms( $post_id, MPRO_Portfolio_Content_Types::TAX_TAG );
	$cover_id        = absint( MPRO_Portfolio_Meta_Boxes::get( $post_id, 'cover_id', 0 ) );
	$hero_image_id   = $cover_id ? $cover_id : absint( get_post_thumbnail_id( $post_id ) );
	?>
	<main <?php post_class( 'mpro-portfolio-single' ); ?>>
		<header class="mpro-portfolio-single__header">
			<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
				<div class="mpro-portfolio-single__taxonomies">
					<?php foreach ( $categories as $category ) : ?>
						<a href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<h1 class="mpro-portfolio-single__title"><?php the_title(); ?></h1>
			<?php if ( $short_desc ) : ?><p class="mpro-portfolio-single__summary"><?php echo esc_html( $short_desc ); ?></p><?php endif; ?>
		</header>

		<?php if ( $hero_image_id ) : ?>
			<figure class="mpro-portfolio-single__cover"><?php echo wp_kses_post( wp_get_attachment_image( $hero_image_id, 'full' ) ); ?></figure>
		<?php endif; ?>

		<div class="mpro-portfolio-single__layout">
			<div class="mpro-portfolio-single__content"><?php the_content(); ?></div>
			<aside class="mpro-portfolio-single__sidebar">
				<section class="mpro-portfolio-detail-panel">
					<h2><?php esc_html_e( 'Project Details', 'mpro-portfolio' ); ?></h2>
					<dl>
						<?php if ( $client ) : ?><div><dt><?php esc_html_e( 'Client', 'mpro-portfolio' ); ?></dt><dd><?php echo esc_html( $client ); ?></dd></div><?php endif; ?>
						<?php if ( $role ) : ?><div><dt><?php esc_html_e( 'Role', 'mpro-portfolio' ); ?></dt><dd><?php echo esc_html( $role ); ?></dd></div><?php endif; ?>
						<?php if ( $implementation ) : ?><div><dt><?php esc_html_e( 'Implementation', 'mpro-portfolio' ); ?></dt><dd><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $implementation ) ) ); ?></dd></div><?php endif; ?>
						<?php if ( $duration ) : ?><div><dt><?php esc_html_e( 'Duration', 'mpro-portfolio' ); ?></dt><dd><?php echo esc_html( $duration ); ?></dd></div><?php endif; ?>
						<div><dt><?php esc_html_e( 'Published', 'mpro-portfolio' ); ?></dt><dd><?php echo esc_html( get_the_date() ); ?></dd></div>
						<?php if ( $project_url ) : ?><div><dt><?php esc_html_e( 'Project URL', 'mpro-portfolio' ); ?></dt><dd><a href="<?php echo esc_url( $project_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Visit Project', 'mpro-portfolio' ); ?></a></dd></div><?php endif; ?>
						<?php if ( is_array( $meta_details ) ) : foreach ( $meta_details as $detail ) : ?>
							<?php if ( ! empty( $detail['label'] ) || ! empty( $detail['value'] ) ) : ?><div><dt><?php echo esc_html( isset( $detail['label'] ) ? $detail['label'] : '' ); ?></dt><dd><?php echo esc_html( isset( $detail['value'] ) ? $detail['value'] : '' ); ?></dd></div><?php endif; ?>
						<?php endforeach; endif; ?>
					</dl>
				</section>

				<?php if ( is_array( $tools ) && $tools ) : ?>
					<section class="mpro-portfolio-detail-panel">
						<h2><?php esc_html_e( 'Tools Used', 'mpro-portfolio' ); ?></h2>
						<ul class="mpro-portfolio-chip-list"><?php foreach ( $tools as $tool ) : ?><li><?php echo esc_html( $tool ); ?></li><?php endforeach; ?></ul>
					</section>
				<?php endif; ?>

				<?php if ( is_array( $collaborators ) && $collaborators ) : ?>
					<section class="mpro-portfolio-detail-panel">
						<h2><?php esc_html_e( 'People Involved', 'mpro-portfolio' ); ?></h2>
						<ul class="mpro-portfolio-people-list">
							<?php foreach ( $collaborators as $person ) : if ( empty( $person['name'] ) && empty( $person['role'] ) ) { continue; } ?>
								<li>
									<?php if ( ! empty( $person['url'] ) ) : ?><a href="<?php echo esc_url( $person['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( isset( $person['name'] ) ? $person['name'] : '' ); ?></a><?php else : ?><strong><?php echo esc_html( isset( $person['name'] ) ? $person['name'] : '' ); ?></strong><?php endif; ?>
									<?php if ( ! empty( $person['role'] ) ) : ?><span><?php echo esc_html( $person['role'] ); ?></span><?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
					<section class="mpro-portfolio-detail-panel">
						<h2><?php esc_html_e( 'Tags', 'mpro-portfolio' ); ?></h2>
						<div class="mpro-portfolio-chip-list"><?php foreach ( $tags as $tag ) : ?><a href="<?php echo esc_url( get_term_link( $tag ) ); ?>"><?php echo esc_html( $tag->name ); ?></a><?php endforeach; ?></div>
					</section>
				<?php endif; ?>
			</aside>
		</div>
	</main>
	<?php
endwhile;

get_footer();
