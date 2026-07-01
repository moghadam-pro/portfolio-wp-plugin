<?php
/**
 * Frontend portfolio rendering.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Renderer {
	/**
	 * Register frontend assets.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
		add_action( 'elementor/frontend/after_register_styles', array( __CLASS__, 'register_assets' ) );
	}

	/**
	 * Register the public stylesheet.
	 *
	 * @return void
	 */
	public static function register_assets() {
		wp_register_style( 'mpro-portfolio', MPRO_PORTFOLIO_URL . 'assets/css/portfolio.css', array(), MPRO_PORTFOLIO_VERSION );
	}

	/**
	 * Enqueue public assets.
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		if ( ! wp_style_is( 'mpro-portfolio', 'registered' ) ) {
			self::register_assets();
		}
		wp_enqueue_style( 'mpro-portfolio' );
	}

	/**
	 * Return the preferred card image ID.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	public static function get_primary_image_id( $post_id ) {
		$cover_id = absint( MPRO_Portfolio_Meta_Boxes::get( $post_id, 'cover_id', 0 ) );
		return $cover_id ? $cover_id : absint( get_post_thumbnail_id( $post_id ) );
	}

	/**
	 * Resolve a card style.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $requested Explicit style or "item".
	 * @return string
	 */
	public static function resolve_card_style( $post_id, $requested = '' ) {
		$settings = MPRO_Portfolio_Settings::get();
		if ( 'item' === $requested || '' === $requested ) {
			$item_style = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'card_style', '' );
			if ( $item_style ) {
				return MPRO_Portfolio_Settings::validate_card_style( $item_style );
			}
			if ( 'item' === $requested ) {
				return MPRO_Portfolio_Settings::validate_card_style( $settings['default_card_style'] );
			}
		}

		if ( $requested ) {
			return MPRO_Portfolio_Settings::validate_card_style( $requested );
		}

		return MPRO_Portfolio_Settings::validate_card_style( $settings['default_card_style'] );
	}

	/**
	 * Resolve the single template style.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public static function resolve_single_style( $post_id ) {
		$item_style = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'single_style', '' );
		if ( $item_style ) {
			return MPRO_Portfolio_Settings::validate_single_style( $item_style );
		}
		$settings = MPRO_Portfolio_Settings::get();
		return MPRO_Portfolio_Settings::validate_single_style( $settings['default_single_style'] );
	}

	/**
	 * Render a reusable portfolio card.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $requested_style Explicit style, empty, or "item".
	 * @return string
	 */
	public static function render_card( $post_id, $requested_style = '' ) {
		$post = get_post( $post_id );
		if ( ! $post || MPRO_Portfolio_Content_Types::POST_TYPE !== $post->post_type ) {
			return '';
		}
		if ( 'publish' !== $post->post_status && ! current_user_can( 'edit_post', $post_id ) ) {
			return '';
		}

		self::enqueue_assets();
		$style = self::resolve_card_style( $post_id, $requested_style );
		if ( 0 === strpos( $style, 'elementor:' ) ) {
			return self::render_elementor_template( absint( substr( $style, 10 ) ), $post_id );
		}

		$template = self::locate_card_template( $style );
		if ( ! $template ) {
			return '';
		}

		$context = self::card_context( $post_id, $style );
		ob_start();
		extract( $context, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		include $template;
		return ob_get_clean();
	}

	/**
	 * Render a portfolio grid.
	 *
	 * @param array $args Query and layout arguments.
	 * @return string
	 */
	public static function render_grid( $args = array() ) {
		$defaults = array(
			'ids'            => array(),
			'featured'       => false,
			'category'       => array(),
			'tag'            => array(),
			'style'          => '',
			'columns'        => 3,
			'columns_tablet' => 2,
			'columns_mobile' => 1,
			'count'          => 9,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'pagination'     => false,
			'page_var'       => 'mpro_page',
			'empty_message'  => __( 'No portfolio items found.', 'mpro-portfolio' ),
		);
		$args = wp_parse_args( $args, $defaults );

		$ids = self::normalize_ids( $args['ids'] );
		$page_var = sanitize_key( $args['page_var'] );
		$paged = isset( $_GET[ $page_var ] ) ? max( 1, absint( $_GET[ $page_var ] ) ) : 1;

		$count = (int) $args['count'];
		$posts_per_page = -1 === $count ? -1 : max( 1, min( 100, absint( $count ) ) );

		$query_args = array(
			'post_type'           => MPRO_Portfolio_Content_Types::POST_TYPE,
			'post_status'         => 'publish',
			'posts_per_page'      => $posts_per_page,
			'order'               => 'ASC' === strtoupper( (string) $args['order'] ) ? 'ASC' : 'DESC',
			'orderby'             => self::normalize_orderby( $args['orderby'] ),
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => ! self::to_bool( $args['pagination'] ) || ! empty( $ids ),
		);

		if ( 'implementation_date' === $args['orderby'] ) {
			$query_args['meta_key'] = '_mpro_portfolio_implementation_date';
			$query_args['orderby']  = 'meta_value';
		}

		if ( $ids ) {
			$query_args['post__in']       = $ids;
			$query_args['orderby']        = 'post__in';
			$query_args['posts_per_page'] = count( $ids );
			$query_args['paged']          = 1;
		}

		$tax_query = array();
		$categories = self::normalize_terms( $args['category'] );
		$tags       = self::normalize_terms( $args['tag'] );
		if ( $categories ) {
			$tax_query[] = array(
				'taxonomy' => MPRO_Portfolio_Content_Types::TAX_CATEGORY,
				'field'    => self::terms_are_ids( $categories ) ? 'term_id' : 'slug',
				'terms'    => $categories,
			);
		}
		if ( $tags ) {
			$tax_query[] = array(
				'taxonomy' => MPRO_Portfolio_Content_Types::TAX_TAG,
				'field'    => self::terms_are_ids( $tags ) ? 'term_id' : 'slug',
				'terms'    => $tags,
			);
		}
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		if ( $tax_query ) {
			$query_args['tax_query'] = $tax_query;
		}

		if ( self::to_bool( $args['featured'] ) ) {
			$query_args['meta_query'] = array(
				array(
					'key'   => '_mpro_portfolio_featured',
					'value' => '1',
				),
			);
		}

		$query = new WP_Query( $query_args );
		self::enqueue_assets();
		$desktop = max( 1, min( 6, absint( $args['columns'] ) ) );
		$tablet  = max( 1, min( 4, absint( $args['columns_tablet'] ) ) );
		$mobile  = max( 1, min( 2, absint( $args['columns_mobile'] ) ) );

		ob_start();
		?>
		<div class="mpro-portfolio-grid" style="--mpro-columns:<?php echo esc_attr( $desktop ); ?>;--mpro-columns-tablet:<?php echo esc_attr( $tablet ); ?>;--mpro-columns-mobile:<?php echo esc_attr( $mobile ); ?>;">
			<?php if ( $query->have_posts() ) : ?>
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<?php echo self::render_card( get_the_ID(), $args['style'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p class="mpro-portfolio-empty"><?php echo esc_html( $args['empty_message'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		if ( self::to_bool( $args['pagination'] ) && ! $ids && $query->max_num_pages > 1 ) {
			$placeholder = 999999999;
			$base = str_replace( (string) $placeholder, '%#%', add_query_arg( $page_var, $placeholder ) );
			$links = paginate_links(
				array(
					'base'      => $base,
					'format'    => '',
					'current'   => $paged,
					'total'     => $query->max_num_pages,
					'type'      => 'list',
					'prev_text' => __( 'Previous', 'mpro-portfolio' ),
					'next_text' => __( 'Next', 'mpro-portfolio' ),
				)
			);
			if ( $links ) {
				echo '<nav class="mpro-portfolio-pagination" aria-label="' . esc_attr__( 'Portfolio pagination', 'mpro-portfolio' ) . '">' . wp_kses_post( $links ) . '</nav>';
			}
		}
		wp_reset_postdata();
		return ob_get_clean();
	}

	/**
	 * Render an Elementor saved template with the requested post context.
	 *
	 * @param int $template_id Elementor template ID.
	 * @param int $post_id Portfolio post ID.
	 * @return string
	 */
	public static function render_elementor_template( $template_id, $post_id ) {
		if ( ! $template_id || ! class_exists( '\\Elementor\\Plugin' ) ) {
			return '';
		}

		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$previous_post       = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		$GLOBALS['post']     = $post;
		setup_postdata( $post );
		$content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );

		if ( $previous_post instanceof WP_Post ) {
			$GLOBALS['post'] = $previous_post;
			setup_postdata( $previous_post );
		} else {
			wp_reset_postdata();
		}

		return $content ? '<div class="mpro-portfolio-elementor-template">' . $content . '</div>' : '';
	}

	/**
	 * Build template context for a card.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $style Style name.
	 * @return array
	 */
	private static function card_context( $post_id, $style ) {
		$categories = get_the_terms( $post_id, MPRO_Portfolio_Content_Types::TAX_CATEGORY );
		$category   = ( $categories && ! is_wp_error( $categories ) ) ? reset( $categories ) : null;
		$image_id   = self::get_primary_image_id( $post_id );
		$description = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'short_description' );
		if ( ! $description ) {
			$description = get_the_excerpt( $post_id );
		}

		$date = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'implementation_date' );
		$year = $date ? date_i18n( 'Y', strtotime( $date ) ) : get_the_date( 'Y', $post_id );

		return array(
			'post_id'     => $post_id,
			'style'       => $style,
			'title'       => get_the_title( $post_id ),
			'permalink'   => get_permalink( $post_id ),
			'description' => $description,
			'role'        => MPRO_Portfolio_Meta_Boxes::get( $post_id, 'role' ),
			'category'    => $category,
			'image_html'  => $image_id ? wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'mpro-portfolio-card__image', 'loading' => 'lazy' ) ) : '',
			'year'        => $year,
		);
	}

	/**
	 * Locate a card template in the theme or plugin.
	 *
	 * @param string $style Style name.
	 * @return string
	 */
	private static function locate_card_template( $style ) {
		$filename = 'card-' . sanitize_file_name( $style ) . '.php';
		$theme    = locate_template( array( 'mpro-portfolio/cards/' . $filename ) );
		if ( $theme ) {
			return $theme;
		}

		$plugin = MPRO_PORTFOLIO_DIR . 'templates/cards/' . $filename;
		return file_exists( $plugin ) ? $plugin : '';
	}

	/**
	 * Normalize IDs from arrays or comma-separated strings.
	 *
	 * @param mixed $value Raw IDs.
	 * @return array
	 */
	private static function normalize_ids( $value ) {
		$value = is_array( $value ) ? $value : explode( ',', (string) $value );
		return array_values( array_unique( array_filter( array_map( 'absint', $value ) ) ) );
	}

	/**
	 * Normalize term IDs or slugs.
	 *
	 * @param mixed $value Raw terms.
	 * @return array
	 */
	private static function normalize_terms( $value ) {
		$value = is_array( $value ) ? $value : explode( ',', (string) $value );
		$output = array();
		foreach ( $value as $term ) {
			if ( '' === (string) $term ) {
				continue;
			}
			$output[] = is_numeric( $term ) ? absint( $term ) : sanitize_title( $term );
		}
		return array_values( array_unique( $output, SORT_REGULAR ) );
	}

	/**
	 * Determine whether all terms are numeric IDs.
	 *
	 * @param array $terms Terms.
	 * @return bool
	 */
	private static function terms_are_ids( $terms ) {
		foreach ( $terms as $term ) {
			if ( ! is_int( $term ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Normalize supported orderby values.
	 *
	 * @param string $orderby Raw value.
	 * @return string
	 */
	private static function normalize_orderby( $orderby ) {
		$allowed = array( 'date', 'title', 'menu_order', 'rand', 'modified', 'ID', 'post__in' );
		return in_array( $orderby, $allowed, true ) ? $orderby : 'date';
	}

	/**
	 * Convert common values to boolean.
	 *
	 * @param mixed $value Raw value.
	 * @return bool
	 */
	private static function to_bool( $value ) {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}
}
