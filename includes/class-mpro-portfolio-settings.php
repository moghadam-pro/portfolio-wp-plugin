<?php
/**
 * Portfolio display settings.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Settings {
	const OPTION = 'mpro_portfolio_settings';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/**
	 * Return default settings.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'default_card_style'       => 'clean',
			'archive_card_style'       => 'clean',
			'default_single_style'     => 'builtin',
			'archive_columns'          => 3,
			'elementor_card_templates' => array(),
		);
	}

	/**
	 * Add defaults without overwriting existing settings.
	 *
	 * @return void
	 */
	public static function add_default_options() {
		if ( false === get_option( self::OPTION, false ) ) {
			add_option( self::OPTION, self::defaults() );
		}
	}

	/**
	 * Register the option.
	 *
	 * @return void
	 */
	public static function register_settings() {
		register_setting(
			'mpro_portfolio_styles_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	/**
	 * Return saved settings with defaults.
	 *
	 * @return array
	 */
	public static function get() {
		return wp_parse_args( get_option( self::OPTION, array() ), self::defaults() );
	}

	/**
	 * Sanitize the settings form.
	 *
	 * @param array $input Raw settings.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$input    = is_array( $input ) ? $input : array();
		$defaults = self::defaults();
		$output   = $defaults;

		$template_ids = isset( $input['elementor_card_templates'] ) && is_array( $input['elementor_card_templates'] )
			? array_values( array_unique( array_filter( array_map( 'absint', $input['elementor_card_templates'] ) ) ) )
			: array();

		$available_templates = self::get_elementor_templates();
		$template_ids        = array_values(
			array_filter(
				$template_ids,
				static function ( $template_id ) use ( $available_templates ) {
					return isset( $available_templates[ $template_id ] );
				}
			)
		);

		$output['elementor_card_templates'] = $template_ids;
		$output['archive_columns']          = isset( $input['archive_columns'] ) ? max( 1, min( 4, absint( $input['archive_columns'] ) ) ) : 3;

		$allowed_cards = array_keys( self::builtin_card_styles() );
		foreach ( $template_ids as $template_id ) {
			$allowed_cards[] = 'elementor:' . $template_id;
		}

		$default_card = isset( $input['default_card_style'] ) ? self::normalize_card_style( $input['default_card_style'] ) : $defaults['default_card_style'];
		$archive_card = isset( $input['archive_card_style'] ) ? self::normalize_card_style( $input['archive_card_style'] ) : $defaults['archive_card_style'];

		$output['default_card_style'] = in_array( $default_card, $allowed_cards, true ) ? $default_card : $defaults['default_card_style'];
		$output['archive_card_style'] = in_array( $archive_card, $allowed_cards, true ) ? $archive_card : $defaults['archive_card_style'];

		$single_style = isset( $input['default_single_style'] ) ? sanitize_text_field( wp_unslash( $input['default_single_style'] ) ) : 'builtin';
		if ( 'builtin' !== $single_style && 0 !== strpos( $single_style, 'elementor:' ) ) {
			$single_style = 'builtin';
		}
		if ( 0 === strpos( $single_style, 'elementor:' ) ) {
			$template_id = absint( substr( $single_style, 10 ) );
			if ( ! isset( $available_templates[ $template_id ] ) ) {
				$single_style = 'builtin';
			}
		}
		$output['default_single_style'] = $single_style;

		return $output;
	}

	/**
	 * Return built-in card styles.
	 *
	 * @return array
	 */
	public static function builtin_card_styles() {
		return array(
			'clean' => array(
				'label'       => __( 'Clean', 'mpro-portfolio' ),
				'description' => __( 'A balanced image-first card for general portfolio grids.', 'mpro-portfolio' ),
			),
			'editorial' => array(
				'label'       => __( 'Editorial', 'mpro-portfolio' ),
				'description' => __( 'A typography-led layout with a strong case-study presentation.', 'mpro-portfolio' ),
			),
			'immersive' => array(
				'label'       => __( 'Immersive', 'mpro-portfolio' ),
				'description' => __( 'A full-image card with layered content and a cinematic overlay.', 'mpro-portfolio' ),
			),
		);
	}

	/**
	 * Normalize style names used by previous plugin builds.
	 *
	 * @param string $style Style identifier.
	 * @return string
	 */
	public static function normalize_card_style( $style ) {
		$style = sanitize_text_field( (string) $style );
		$map   = array(
			'style-1' => 'clean',
			'style-2' => 'editorial',
			'style-3' => 'immersive',
		);

		return isset( $map[ $style ] ) ? $map[ $style ] : $style;
	}

	/**
	 * Return published Elementor templates.
	 *
	 * @return array
	 */
	public static function get_elementor_templates() {
		if ( ! post_type_exists( 'elementor_library' ) ) {
			return array();
		}

		$posts = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$templates = array();
		foreach ( $posts as $template ) {
			$type  = get_post_meta( $template->ID, '_elementor_template_type', true );
			$label = $template->post_title ? $template->post_title : sprintf( __( 'Template #%d', 'mpro-portfolio' ), $template->ID );
			if ( $type ) {
				$label .= ' — ' . ucwords( str_replace( '-', ' ', $type ) );
			}
			$templates[ $template->ID ] = $label;
		}

		return $templates;
	}

	/**
	 * Return card styles available for frontend rendering.
	 *
	 * @return array
	 */
	public static function card_style_options() {
		$options  = array();
		$settings = self::get();
		$builtins = self::builtin_card_styles();

		foreach ( $builtins as $key => $style ) {
			$options[ $key ] = $style['label'];
		}

		$templates = self::get_elementor_templates();
		foreach ( $settings['elementor_card_templates'] as $template_id ) {
			if ( isset( $templates[ $template_id ] ) ) {
				$options[ 'elementor:' . $template_id ] = sprintf( __( 'Elementor: %s', 'mpro-portfolio' ), $templates[ $template_id ] );
			}
		}

		return $options;
	}

	/**
	 * Return single-template options.
	 *
	 * @return array
	 */
	public static function single_style_options() {
		$options   = array( 'builtin' => __( 'Built-in Single Template', 'mpro-portfolio' ) );
		$templates = self::get_elementor_templates();

		foreach ( $templates as $template_id => $label ) {
			$options[ 'elementor:' . $template_id ] = sprintf( __( 'Elementor: %s', 'mpro-portfolio' ), $label );
		}

		return $options;
	}

	/**
	 * Validate a card style for storage or rendering.
	 *
	 * @param string $style Style identifier.
	 * @param bool   $allow_inherit Whether an empty value is valid.
	 * @return string
	 */
	public static function validate_card_style( $style, $allow_inherit = false ) {
		$style = self::normalize_card_style( $style );
		if ( $allow_inherit && '' === $style ) {
			return '';
		}

		$options = self::card_style_options();
		return isset( $options[ $style ] ) ? $style : 'clean';
	}

	/**
	 * Validate a single style for storage or rendering.
	 *
	 * @param string $style Style identifier.
	 * @param bool   $allow_inherit Whether an empty value is valid.
	 * @return string
	 */
	public static function validate_single_style( $style, $allow_inherit = false ) {
		$style = sanitize_text_field( (string) $style );
		if ( $allow_inherit && '' === $style ) {
			return '';
		}

		$options = self::single_style_options();
		return isset( $options[ $style ] ) ? $style : 'builtin';
	}

	/**
	 * Enqueue admin CSS on the Styles page.
	 *
	 * @param string $hook Current admin hook.
	 * @return void
	 */
	public static function enqueue_assets( $hook ) {
		if ( ! isset( $_GET['page'] ) || 'mpro-portfolio-styles' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
			return;
		}

		wp_enqueue_style( 'mpro-portfolio-admin', MPRO_PORTFOLIO_URL . 'assets/css/admin.css', array(), MPRO_PORTFOLIO_VERSION );
	}

	/**
	 * Render the Styles page.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings     = self::get();
		$builtins     = self::builtin_card_styles();
		$templates    = self::get_elementor_templates();
		$card_options = array();

		foreach ( $builtins as $key => $style ) {
			$card_options[ $key ] = $style['label'];
		}
		foreach ( $templates as $template_id => $label ) {
			$card_options[ 'elementor:' . $template_id ] = sprintf( __( 'Elementor: %s', 'mpro-portfolio' ), $label );
		}
		?>
		<div class="wrap mpro-portfolio-settings">
			<h1><?php esc_html_e( 'Portfolio Styles', 'mpro-portfolio' ); ?></h1>
			<p><?php esc_html_e( 'Choose built-in styles or explicitly enable saved Elementor templates for portfolio cards and single pages.', 'mpro-portfolio' ); ?></p>

			<form method="post" action="options.php">
				<?php settings_fields( 'mpro_portfolio_styles_group' ); ?>

				<h2><?php esc_html_e( 'Built-in Card Styles', 'mpro-portfolio' ); ?></h2>
				<div class="mpro-style-previews">
					<?php foreach ( $builtins as $key => $style ) : ?>
						<div class="mpro-style-preview mpro-style-preview--<?php echo esc_attr( $key ); ?>">
							<div class="mpro-style-preview__media"></div>
							<div class="mpro-style-preview__content">
								<strong><?php echo esc_html( $style['label'] ); ?></strong>
								<p><?php echo esc_html( $style['description'] ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="mpro-default-card-style"><?php esc_html_e( 'Default Card Style', 'mpro-portfolio' ); ?></label></th>
						<td>
							<select id="mpro-default-card-style" name="<?php echo esc_attr( self::OPTION ); ?>[default_card_style]">
								<?php foreach ( $card_options as $value => $label ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['default_card_style'], $value ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="description"><?php esc_html_e( 'Used when a card does not request a specific style.', 'mpro-portfolio' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mpro-archive-card-style"><?php esc_html_e( 'Archive Card Style', 'mpro-portfolio' ); ?></label></th>
						<td>
							<select id="mpro-archive-card-style" name="<?php echo esc_attr( self::OPTION ); ?>[archive_card_style]">
								<?php foreach ( $card_options as $value => $label ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['archive_card_style'], $value ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mpro-archive-columns"><?php esc_html_e( 'Archive Columns', 'mpro-portfolio' ); ?></label></th>
						<td>
							<select id="mpro-archive-columns" name="<?php echo esc_attr( self::OPTION ); ?>[archive_columns]">
								<?php for ( $columns = 1; $columns <= 4; $columns++ ) : ?>
									<option value="<?php echo esc_attr( $columns ); ?>" <?php selected( $settings['archive_columns'], $columns ); ?>><?php echo esc_html( $columns ); ?></option>
								<?php endfor; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mpro-default-single-style"><?php esc_html_e( 'Default Single Template', 'mpro-portfolio' ); ?></label></th>
						<td>
							<select id="mpro-default-single-style" name="<?php echo esc_attr( self::OPTION ); ?>[default_single_style]">
								<?php foreach ( self::single_style_options() as $value => $label ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['default_single_style'], $value ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Elementor Card Templates', 'mpro-portfolio' ); ?></h2>
				<?php if ( empty( $templates ) ) : ?>
					<p><?php esc_html_e( 'No published Elementor saved templates were found. Built-in styles remain fully available.', 'mpro-portfolio' ); ?></p>
				<?php else : ?>
					<p><?php esc_html_e( 'Only checked templates become selectable as card styles. This avoids exposing unrelated Elementor templates.', 'mpro-portfolio' ); ?></p>
					<div class="mpro-template-checklist">
						<?php foreach ( $templates as $template_id => $label ) : ?>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION ); ?>[elementor_card_templates][]" value="<?php echo esc_attr( $template_id ); ?>" <?php checked( in_array( $template_id, $settings['elementor_card_templates'], true ) ); ?>>
								<?php echo esc_html( $label ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php submit_button( __( 'Save Styles', 'mpro-portfolio' ) ); ?>
			</form>

			<hr>
			<h2><?php esc_html_e( 'Usage', 'mpro-portfolio' ); ?></h2>
			<p><code>[mpro_portfolio ids="12,45,67" style="editorial" columns="3"]</code></p>
			<p><code>[mpro_portfolio featured="true" count="6" style="immersive"]</code></p>
			<p><code>&lt;?php mpro_portfolio_the_card( get_the_ID(), 'clean' ); ?&gt;</code></p>
		</div>
		<?php
	}
}
