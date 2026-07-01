<?php
/**
 * Editorial portfolio card.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="mpro-portfolio-card mpro-portfolio-card--editorial">
	<a class="mpro-portfolio-card__link" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'mpro-portfolio' ), $title ) ); ?>">
		<div class="mpro-portfolio-card__editorial-head">
			<span><?php echo esc_html( $category ? $category->name : __( 'Portfolio', 'mpro-portfolio' ) ); ?></span>
			<span><?php echo esc_html( $year ); ?></span>
		</div>
		<div class="mpro-portfolio-card__media">
			<?php if ( $image_html ) : ?>
				<?php echo wp_kses_post( $image_html ); ?>
			<?php else : ?>
				<span class="mpro-portfolio-card__placeholder" aria-hidden="true"></span>
			<?php endif; ?>
		</div>
		<div class="mpro-portfolio-card__body">
			<div>
				<h3 class="mpro-portfolio-card__title"><?php echo esc_html( $title ); ?></h3>
				<?php if ( $description ) : ?><p class="mpro-portfolio-card__description"><?php echo esc_html( wp_trim_words( $description, 22 ) ); ?></p><?php endif; ?>
			</div>
			<span class="mpro-portfolio-card__editorial-arrow" aria-hidden="true">↗</span>
		</div>
		<?php if ( $role ) : ?><div class="mpro-portfolio-card__footer"><?php echo esc_html( $role ); ?></div><?php endif; ?>
	</a>
</article>
