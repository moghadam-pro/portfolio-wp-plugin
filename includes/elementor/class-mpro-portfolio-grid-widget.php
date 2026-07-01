<?php
/**
 * Elementor Portfolio Grid widget.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPRO_Portfolio_Grid_Widget extends \Elementor\Widget_Base {
	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mpro_portfolio_grid';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Portfolio Grid', 'mpro-portfolio' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'mpro-portfolio' );
	}

	/**
	 * Style dependencies.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'mpro-portfolio' );
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array( 'label' => __( 'Portfolio Query', 'mpro-portfolio' ) )
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'mpro-portfolio' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'latest',
				'options' => array(
					'latest'   => __( 'Latest Portfolio', 'mpro-portfolio' ),
					'manual'   => __( 'Manual Selection', 'mpro-portfolio' ),
					'featured' => __( 'Featured Portfolio', 'mpro-portfolio' ),
				),
			)
		);

		$this->add_control(
			'portfolio_ids',
			array(
				'label'       => __( 'Select Portfolio Items', 'mpro-portfolio' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->portfolio_options(),
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'categories',
			array(
				'label'       => __( 'Categories', 'mpro-portfolio' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->term_options( MPRO_Portfolio_Content_Types::TAX_CATEGORY ),
				'condition'   => array( 'source!' => 'manual' ),
			)
		);

		$this->add_control(
			'tags',
			array(
				'label'       => __( 'Tags', 'mpro-portfolio' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->term_options( MPRO_Portfolio_Content_Types::TAX_TAG ),
				'condition'   => array( 'source!' => 'manual' ),
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => __( 'Number of Items', 'mpro-portfolio' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 100,
				'condition' => array( 'source!' => 'manual' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order By', 'mpro-portfolio' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'                => __( 'Publish Date', 'mpro-portfolio' ),
					'implementation_date' => __( 'Implementation Date', 'mpro-portfolio' ),
					'title'               => __( 'Title', 'mpro-portfolio' ),
					'menu_order'          => __( 'Menu Order', 'mpro-portfolio' ),
					'rand'                => __( 'Random', 'mpro-portfolio' ),
				),
				'condition' => array( 'source!' => 'manual' ),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'mpro-portfolio' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array( 'DESC' => __( 'Descending', 'mpro-portfolio' ), 'ASC' => __( 'Ascending', 'mpro-portfolio' ) ),
				'condition' => array( 'source!' => 'manual' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_section',
			array( 'label' => __( 'Layout', 'mpro-portfolio' ) )
		);

		$this->add_control(
			'card_style',
			array(
				'label'   => __( 'Card Style', 'mpro-portfolio' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array( '' => __( 'Use Item or Global Default', 'mpro-portfolio' ) ) + MPRO_Portfolio_Settings::card_style_options(),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'   => __( 'Columns', 'mpro-portfolio' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
			)
		);

		$this->add_control(
			'pagination',
			array(
				'label'        => __( 'Pagination', 'mpro-portfolio' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'source!' => 'manual' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$source   = isset( $settings['source'] ) ? $settings['source'] : 'latest';

		$args = array(
			'ids'            => 'manual' === $source ? (array) $settings['portfolio_ids'] : array(),
			'featured'       => 'featured' === $source,
			'category'       => isset( $settings['categories'] ) ? (array) $settings['categories'] : array(),
			'tag'            => isset( $settings['tags'] ) ? (array) $settings['tags'] : array(),
			'style'          => isset( $settings['card_style'] ) ? $settings['card_style'] : '',
			'columns'        => isset( $settings['columns'] ) ? $settings['columns'] : 3,
			'columns_tablet' => isset( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : 2,
			'columns_mobile' => isset( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : 1,
			'count'          => isset( $settings['count'] ) ? $settings['count'] : 6,
			'orderby'        => isset( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'order'          => isset( $settings['order'] ) ? $settings['order'] : 'DESC',
			'pagination'     => isset( $settings['pagination'] ) && 'yes' === $settings['pagination'],
			'page_var'       => 'mpro_page_' . sanitize_key( $this->get_id() ),
		);

		echo MPRO_Portfolio_Renderer::render_grid( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Return Portfolio post options.
	 *
	 * @return array
	 */
	private function portfolio_options() {
		$posts = get_posts(
			array(
				'post_type'      => MPRO_Portfolio_Content_Types::POST_TYPE,
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		$options = array();
		foreach ( $posts as $post ) {
			$options[ $post->ID ] = $post->post_title ? $post->post_title : sprintf( __( 'Portfolio #%d', 'mpro-portfolio' ), $post->ID );
		}
		return $options;
	}

	/**
	 * Return taxonomy term options.
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @return array
	 */
	private function term_options( $taxonomy ) {
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
		$options = array();
		if ( is_wp_error( $terms ) ) {
			return $options;
		}
		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}
		return $options;
	}
}
