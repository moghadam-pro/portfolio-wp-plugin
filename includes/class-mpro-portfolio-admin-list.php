<?php
/**
 * Portfolio post-list columns, filters, and sorting.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Admin_List {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'manage_' . MPRO_Portfolio_Content_Types::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . MPRO_Portfolio_Content_Types::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
		add_filter( 'manage_edit-' . MPRO_Portfolio_Content_Types::POST_TYPE . '_sortable_columns', array( __CLASS__, 'sortable_columns' ) );
		add_action( 'restrict_manage_posts', array( __CLASS__, 'filters' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'apply_query_changes' ) );
	}

	/**
	 * Define list-table columns.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public static function columns( $columns ) {
		return array(
			'cb'                           => isset( $columns['cb'] ) ? $columns['cb'] : '<input type="checkbox">',
			'mpro_portfolio_image'         => __( 'Image', 'mpro-portfolio' ),
			'title'                        => __( 'Title', 'mpro-portfolio' ),
			'mpro_portfolio_categories'    => __( 'Categories', 'mpro-portfolio' ),
			'mpro_portfolio_tags'          => __( 'Tags', 'mpro-portfolio' ),
			'mpro_portfolio_role'          => __( 'Role', 'mpro-portfolio' ),
			'mpro_portfolio_implementation' => __( 'Implementation', 'mpro-portfolio' ),
			'mpro_portfolio_style'         => __( 'Card Style', 'mpro-portfolio' ),
			'mpro_portfolio_featured'      => __( 'Featured', 'mpro-portfolio' ),
			'date'                         => isset( $columns['date'] ) ? $columns['date'] : __( 'Date', 'mpro-portfolio' ),
		);
	}

	/**
	 * Render custom list-table cells.
	 *
	 * @param string $column Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public static function render_column( $column, $post_id ) {
		switch ( $column ) {
			case 'mpro_portfolio_image':
				$image_id = MPRO_Portfolio_Renderer::get_primary_image_id( $post_id );
				if ( $image_id ) {
					echo wp_kses_post( wp_get_attachment_image( $image_id, array( 72, 54 ) ) );
				} else {
					echo '<span aria-hidden="true">—</span>';
				}
				break;

			case 'mpro_portfolio_categories':
				self::render_terms( $post_id, MPRO_Portfolio_Content_Types::TAX_CATEGORY );
				break;

			case 'mpro_portfolio_tags':
				self::render_terms( $post_id, MPRO_Portfolio_Content_Types::TAX_TAG );
				break;

			case 'mpro_portfolio_role':
				$value = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'role' );
				echo $value ? esc_html( $value ) : '<span aria-hidden="true">—</span>';
				break;

			case 'mpro_portfolio_implementation':
				$date = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'implementation_date' );
				echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '<span aria-hidden="true">—</span>';
				break;

			case 'mpro_portfolio_style':
				$style   = MPRO_Portfolio_Meta_Boxes::get( $post_id, 'card_style' );
				$options = MPRO_Portfolio_Settings::card_style_options();
				if ( ! $style ) {
					esc_html_e( 'Global Default', 'mpro-portfolio' );
				} else {
					$style = MPRO_Portfolio_Settings::normalize_card_style( $style );
					echo esc_html( isset( $options[ $style ] ) ? $options[ $style ] : $style );
				}
				break;

			case 'mpro_portfolio_featured':
				echo MPRO_Portfolio_Meta_Boxes::get( $post_id, 'featured', false ) ? '<span class="dashicons dashicons-star-filled" aria-label="' . esc_attr__( 'Featured', 'mpro-portfolio' ) . '"></span>' : '<span aria-hidden="true">—</span>';
				break;
		}
	}

	/**
	 * Register sortable columns.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public static function sortable_columns( $columns ) {
		$columns['mpro_portfolio_implementation'] = 'mpro_portfolio_implementation';
		$columns['mpro_portfolio_role']           = 'mpro_portfolio_role';
		return $columns;
	}

	/**
	 * Render list filters.
	 *
	 * @param string $post_type Current post type.
	 * @return void
	 */
	public static function filters( $post_type ) {
		if ( MPRO_Portfolio_Content_Types::POST_TYPE !== $post_type ) {
			return;
		}

		self::taxonomy_dropdown( MPRO_Portfolio_Content_Types::TAX_CATEGORY, 'mpro_portfolio_category_filter', __( 'All Categories', 'mpro-portfolio' ) );
		self::taxonomy_dropdown( MPRO_Portfolio_Content_Types::TAX_TAG, 'mpro_portfolio_tag_filter', __( 'All Tags', 'mpro-portfolio' ) );

		$current_style = isset( $_GET['mpro_portfolio_style_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['mpro_portfolio_style_filter'] ) ) : '';
		?>
		<select name="mpro_portfolio_style_filter">
			<option value=""><?php esc_html_e( 'All Card Styles', 'mpro-portfolio' ); ?></option>
			<option value="inherit" <?php selected( $current_style, 'inherit' ); ?>><?php esc_html_e( 'Global Default', 'mpro-portfolio' ); ?></option>
			<?php foreach ( MPRO_Portfolio_Settings::card_style_options() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_style, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
		$current_featured = isset( $_GET['mpro_portfolio_featured_filter'] ) ? sanitize_key( wp_unslash( $_GET['mpro_portfolio_featured_filter'] ) ) : '';
		?>
		<select name="mpro_portfolio_featured_filter">
			<option value=""><?php esc_html_e( 'All Portfolio Items', 'mpro-portfolio' ); ?></option>
			<option value="yes" <?php selected( $current_featured, 'yes' ); ?>><?php esc_html_e( 'Featured Only', 'mpro-portfolio' ); ?></option>
			<option value="no" <?php selected( $current_featured, 'no' ); ?>><?php esc_html_e( 'Not Featured', 'mpro-portfolio' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Apply admin filters and sorting.
	 *
	 * @param WP_Query $query Query object.
	 * @return void
	 */
	public static function apply_query_changes( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || MPRO_Portfolio_Content_Types::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}

		$orderby = $query->get( 'orderby' );
		if ( 'mpro_portfolio_implementation' === $orderby ) {
			$query->set( 'meta_key', '_mpro_portfolio_implementation_date' );
			$query->set( 'orderby', 'meta_value' );
		} elseif ( 'mpro_portfolio_role' === $orderby ) {
			$query->set( 'meta_key', '_mpro_portfolio_role' );
			$query->set( 'orderby', 'meta_value' );
		}

		$tax_query = array();
		foreach (
			array(
				'mpro_portfolio_category_filter' => MPRO_Portfolio_Content_Types::TAX_CATEGORY,
				'mpro_portfolio_tag_filter'      => MPRO_Portfolio_Content_Types::TAX_TAG,
			) as $parameter => $taxonomy
		) {
			if ( isset( $_GET[ $parameter ] ) && absint( $_GET[ $parameter ] ) ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => absint( $_GET[ $parameter ] ),
				);
			}
		}
		if ( $tax_query ) {
			$query->set( 'tax_query', $tax_query );
		}

		$meta_query = array();
		$style      = isset( $_GET['mpro_portfolio_style_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['mpro_portfolio_style_filter'] ) ) : '';
		if ( 'inherit' === $style ) {
			$meta_query[] = array(
				'key'     => '_mpro_portfolio_card_style',
				'compare' => 'NOT EXISTS',
			);
		} elseif ( $style ) {
			$meta_query[] = array(
				'key'   => '_mpro_portfolio_card_style',
				'value' => MPRO_Portfolio_Settings::normalize_card_style( $style ),
			);
		}

		$featured = isset( $_GET['mpro_portfolio_featured_filter'] ) ? sanitize_key( wp_unslash( $_GET['mpro_portfolio_featured_filter'] ) ) : '';
		if ( 'yes' === $featured ) {
			$meta_query[] = array( 'key' => '_mpro_portfolio_featured', 'value' => '1' );
		} elseif ( 'no' === $featured ) {
			$meta_query[] = array(
				'relation' => 'OR',
				array( 'key' => '_mpro_portfolio_featured', 'compare' => 'NOT EXISTS' ),
				array( 'key' => '_mpro_portfolio_featured', 'value' => '0' ),
			);
		}
		if ( $meta_query ) {
			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Render taxonomy links in a list cell.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $taxonomy Taxonomy name.
	 * @return void
	 */
	private static function render_terms( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( ! $terms || is_wp_error( $terms ) ) {
			echo '<span aria-hidden="true">—</span>';
			return;
		}

		$links = array();
		foreach ( $terms as $term ) {
			$url     = add_query_arg(
				array(
					'post_type' => MPRO_Portfolio_Content_Types::POST_TYPE,
					$taxonomy   => $term->slug,
				),
				admin_url( 'edit.php' )
			);
			$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $term->name ) . '</a>';
		}
		echo wp_kses_post( implode( ', ', $links ) );
	}

	/**
	 * Render a taxonomy dropdown.
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @param string $name Field name.
	 * @param string $label Default label.
	 * @return void
	 */
	private static function taxonomy_dropdown( $taxonomy, $name, $label ) {
		$current = isset( $_GET[ $name ] ) ? absint( $_GET[ $name ] ) : 0;
		wp_dropdown_categories(
			array(
				'show_option_all' => $label,
				'taxonomy'        => $taxonomy,
				'name'            => $name,
				'orderby'         => 'name',
				'selected'        => $current,
				'hide_empty'      => false,
				'hierarchical'    => is_taxonomy_hierarchical( $taxonomy ),
				'value_field'     => 'term_id',
			)
		);
	}
}
