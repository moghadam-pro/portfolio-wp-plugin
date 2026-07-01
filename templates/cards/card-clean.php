<?php
/**
 * Clean portfolio card.
 *
 * Available variables: $post_id, $title, $permalink, $description,
 * $role, $category, $image_html, $year, $style.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="mpro-portfolio-card mpro-portfolio-card--clean">
	<a class="mpro-portfolio-card__link" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'mpro-portfolio' ), $title ) ); ?>">
		<div class="mpro-portfolio-card__media">
			<?php if ( $image_html ) : ?>
				<?php echo wp_kses_post( $image_html ); ?>
			<?php else : ?>
				<span class="mpro-portfolio-card__placeholder" aria-hidden="true"></span>
			<?php endif; ?>
		</div>
		<div class="mpro-portfolio-card__body">
			<div class="mpro-portfolio-card__eyebrow">
				<?php if ( $category ) : ?><span><?php echo esc_html( $category->name ); ?></span><?php endif; ?>
				<?php if ( $role ) : ?><span><?php echo esc_html( $role ); ?></span><?php endif; ?>
			</div>
			<h3 class="mpro-portfolio-card__title"><?php echo esc_html( $title ); ?></h3>
			<?php if ( $description ) : ?><p class="mpro-portfolio-card__description"><?php echo esc_html( wp_trim_words( $description, 26 ) ); ?></p><?php endif; ?>
			<span class="mpro-portfolio-card__action"><?php esc_html_e( 'View Project', 'mpro-portfolio' ); ?> <span aria-hidden="true">↗</span></span>
		</div>
	</a>
</article>
