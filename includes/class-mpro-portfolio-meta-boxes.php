<?php
/**
 * Portfolio metadata and editor panels.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPRO_Portfolio_Meta_Boxes {
	const NONCE_ACTION = 'mpro_portfolio_save_meta';
	const NONCE_NAME   = 'mpro_portfolio_meta_nonce';

	/**
	 * Canonical metadata keys.
	 *
	 * @var array
	 */
	private static $keys = array(
		'short_description'   => '_mpro_portfolio_short_description',
		'cover_id'            => '_mpro_portfolio_cover_id',
		'client'              => '_mpro_portfolio_client',
		'project_url'         => '_mpro_portfolio_project_url',
		'implementation_date' => '_mpro_portfolio_implementation_date',
		'duration_value'      => '_mpro_portfolio_duration_value',
		'duration_unit'       => '_mpro_portfolio_duration_unit',
		'role'                => '_mpro_portfolio_role',
		'tools'               => '_mpro_portfolio_tools',
		'collaborators'       => '_mpro_portfolio_collaborators',
		'meta_details'        => '_mpro_portfolio_meta_details',
		'featured'            => '_mpro_portfolio_featured',
		'card_style'          => '_mpro_portfolio_card_style',
		'single_style'        => '_mpro_portfolio_single_style',
	);

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_meta' ), 10 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_boxes' ) );
		add_action( 'save_post_' . MPRO_Portfolio_Content_Types::POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/**
	 * Register post metadata for REST and editor integrations.
	 *
	 * @return void
	 */
	public static function register_meta() {
		$auth = static function () {
			return current_user_can( 'edit_posts' );
		};

		$string_fields = array( 'short_description', 'client', 'project_url', 'implementation_date', 'duration_value', 'duration_unit', 'role', 'card_style', 'single_style' );
		foreach ( $string_fields as $field ) {
			register_post_meta(
				MPRO_Portfolio_Content_Types::POST_TYPE,
				self::$keys[ $field ],
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'auth_callback'     => $auth,
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
		}

		register_post_meta(
			MPRO_Portfolio_Content_Types::POST_TYPE,
			self::$keys['cover_id'],
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'auth_callback'     => $auth,
				'sanitize_callback' => 'absint',
			)
		);

		register_post_meta(
			MPRO_Portfolio_Content_Types::POST_TYPE,
			self::$keys['featured'],
			array(
				'type'              => 'boolean',
				'single'            => true,
				'show_in_rest'      => true,
				'auth_callback'     => $auth,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);

		register_post_meta(
			MPRO_Portfolio_Content_Types::POST_TYPE,
			self::$keys['tools'],
			array(
				'type'          => 'array',
				'single'        => true,
				'auth_callback'     => $auth,
				'sanitize_callback' => array( __CLASS__, 'sanitize_tools_meta' ),
				'show_in_rest'      => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array( 'type' => 'string' ),
					),
				),
			)
		);

		foreach ( array( 'collaborators', 'meta_details' ) as $field ) {
			$properties = 'collaborators' === $field
				? array(
					'name' => array( 'type' => 'string' ),
					'role' => array( 'type' => 'string' ),
					'url'  => array( 'type' => 'string' ),
				)
				: array(
					'label' => array( 'type' => 'string' ),
					'value' => array( 'type' => 'string' ),
				);

			register_post_meta(
				MPRO_Portfolio_Content_Types::POST_TYPE,
				self::$keys[ $field ],
				array(
					'type'              => 'array',
					'single'            => true,
					'auth_callback'     => $auth,
					'sanitize_callback' => 'collaborators' === $field ? array( __CLASS__, 'sanitize_collaborators_meta' ) : array( __CLASS__, 'sanitize_meta_details_meta' ),
					'show_in_rest'      => array(
						'schema' => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => $properties,
							),
						),
					),
				)
			);
		}
	}

	/**
	 * Sanitize tools submitted through the REST API.
	 *
	 * @param mixed $value Raw value.
	 * @return array
	 */
	public static function sanitize_tools_meta( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$output = array();
		foreach ( $value as $tool ) {
			$tool = sanitize_text_field( $tool );
			if ( '' !== $tool ) {
				$output[] = $tool;
			}
		}
		return array_values( array_unique( $output ) );
	}

	/**
	 * Sanitize collaborators submitted through the REST API.
	 *
	 * @param mixed $value Raw value.
	 * @return array
	 */
	public static function sanitize_collaborators_meta( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$output = array();
		foreach ( $value as $person ) {
			if ( ! is_array( $person ) ) {
				continue;
			}
			$name = isset( $person['name'] ) ? sanitize_text_field( $person['name'] ) : '';
			$role = isset( $person['role'] ) ? sanitize_text_field( $person['role'] ) : '';
			$url  = isset( $person['url'] ) ? esc_url_raw( $person['url'] ) : '';
			if ( $name || $role || $url ) {
				$output[] = compact( 'name', 'role', 'url' );
			}
		}
		return $output;
	}

	/**
	 * Sanitize custom details submitted through the REST API.
	 *
	 * @param mixed $value Raw value.
	 * @return array
	 */
	public static function sanitize_meta_details_meta( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$output = array();
		foreach ( $value as $detail ) {
			if ( ! is_array( $detail ) ) {
				continue;
			}
			$label = isset( $detail['label'] ) ? sanitize_text_field( $detail['label'] ) : '';
			$item  = isset( $detail['value'] ) ? sanitize_text_field( $detail['value'] ) : '';
			if ( $label || $item ) {
				$output[] = array( 'label' => $label, 'value' => $item );
			}
		}
		return $output;
	}

	/**
	 * Register editor panels.
	 *
	 * @return void
	 */
	public static function register_boxes() {
		$post_type = MPRO_Portfolio_Content_Types::POST_TYPE;

		add_meta_box( 'mpro_portfolio_overview', __( 'Portfolio Overview', 'mpro-portfolio' ), array( __CLASS__, 'render_overview' ), $post_type, 'normal', 'high' );
		add_meta_box( 'mpro_portfolio_details', __( 'Project Details', 'mpro-portfolio' ), array( __CLASS__, 'render_details' ), $post_type, 'normal', 'high' );
		add_meta_box( 'mpro_portfolio_tools', __( 'Tools Used', 'mpro-portfolio' ), array( __CLASS__, 'render_tools' ), $post_type, 'normal', 'default' );
		add_meta_box( 'mpro_portfolio_collaborators', __( 'People Involved', 'mpro-portfolio' ), array( __CLASS__, 'render_collaborators' ), $post_type, 'normal', 'default' );
		add_meta_box( 'mpro_portfolio_meta_details', __( 'Additional Meta Details', 'mpro-portfolio' ), array( __CLASS__, 'render_meta_details' ), $post_type, 'normal', 'default' );
		add_meta_box( 'mpro_portfolio_display', __( 'Display Options', 'mpro-portfolio' ), array( __CLASS__, 'render_display' ), $post_type, 'side', 'default' );
	}

	/**
	 * Enqueue editor assets.
	 *
	 * @param string $hook Current admin hook.
	 * @return void
	 */
	public static function enqueue_assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || MPRO_Portfolio_Content_Types::POST_TYPE !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'mpro-portfolio-admin', MPRO_PORTFOLIO_URL . 'assets/css/admin.css', array(), MPRO_PORTFOLIO_VERSION );
		wp_enqueue_script( 'mpro-portfolio-admin', MPRO_PORTFOLIO_URL . 'assets/js/admin.js', array( 'jquery' ), MPRO_PORTFOLIO_VERSION, true );
		wp_localize_script(
			'mpro-portfolio-admin',
			'mproPortfolioAdmin',
			array(
				'chooseImage' => __( 'Choose Cover Image', 'mpro-portfolio' ),
				'useImage'    => __( 'Use This Image', 'mpro-portfolio' ),
			)
		);
	}

	/**
	 * Render overview fields.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public static function render_overview( $post ) {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
		$description = self::get( $post->ID, 'short_description' );
		$cover_id    = absint( self::get( $post->ID, 'cover_id', 0 ) );
		$cover_html  = $cover_id ? wp_get_attachment_image( $cover_id, 'medium', false, array( 'class' => 'mpro-cover-preview__image' ) ) : '';
		?>
		<p>
			<label for="mpro_portfolio_short_description"><strong><?php esc_html_e( 'Short Description', 'mpro-portfolio' ); ?></strong></label>
		</p>
		<textarea id="mpro_portfolio_short_description" name="mpro_portfolio_short_description" rows="4" class="widefat" placeholder="<?php esc_attr_e( 'A concise summary used on cards and archive pages.', 'mpro-portfolio' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>

		<div class="mpro-cover-field">
			<p><strong><?php esc_html_e( 'Cover Image', 'mpro-portfolio' ); ?></strong></p>
			<p class="description"><?php esc_html_e( 'The cover is separate from the native WordPress Featured Image. Cards use the cover first and fall back to the Featured Image.', 'mpro-portfolio' ); ?></p>
			<input type="hidden" id="mpro_portfolio_cover_id" name="mpro_portfolio_cover_id" value="<?php echo esc_attr( $cover_id ); ?>">
			<div class="mpro-cover-preview"><?php echo wp_kses_post( $cover_html ); ?></div>
			<p>
				<button type="button" class="button mpro-select-cover"><?php esc_html_e( 'Select Cover Image', 'mpro-portfolio' ); ?></button>
				<button type="button" class="button-link-delete mpro-remove-cover" <?php echo $cover_id ? '' : 'hidden'; ?>><?php esc_html_e( 'Remove Cover', 'mpro-portfolio' ); ?></button>
			</p>
		</div>
		<?php
	}

	/**
	 * Render structured project details.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public static function render_details( $post ) {
		$client              = self::get( $post->ID, 'client' );
		$project_url         = self::get( $post->ID, 'project_url' );
		$implementation_date = self::get( $post->ID, 'implementation_date' );
		$duration_value      = self::get( $post->ID, 'duration_value' );
		$duration_unit       = self::get( $post->ID, 'duration_unit', 'weeks' );
		$role                = self::get( $post->ID, 'role' );
		?>
		<table class="form-table mpro-meta-table" role="presentation">
			<tr>
				<th><label for="mpro_portfolio_client"><?php esc_html_e( 'Client or Company', 'mpro-portfolio' ); ?></label></th>
				<td><input type="text" id="mpro_portfolio_client" name="mpro_portfolio_client" class="regular-text" value="<?php echo esc_attr( $client ); ?>"></td>
			</tr>
			<tr>
				<th><label for="mpro_portfolio_project_url"><?php esc_html_e( 'Project URL', 'mpro-portfolio' ); ?></label></th>
				<td><input type="url" id="mpro_portfolio_project_url" name="mpro_portfolio_project_url" class="regular-text" value="<?php echo esc_attr( $project_url ); ?>" placeholder="https://"></td>
			</tr>
			<tr>
				<th><label for="mpro_portfolio_implementation_date"><?php esc_html_e( 'Implementation Date', 'mpro-portfolio' ); ?></label></th>
				<td>
					<input type="date" id="mpro_portfolio_implementation_date" name="mpro_portfolio_implementation_date" value="<?php echo esc_attr( $implementation_date ); ?>">
					<p class="description"><?php esc_html_e( 'The WordPress publish date remains available in the Publish panel.', 'mpro-portfolio' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="mpro_portfolio_duration_value"><?php esc_html_e( 'Project Duration', 'mpro-portfolio' ); ?></label></th>
				<td>
					<input type="number" min="0" step="0.5" id="mpro_portfolio_duration_value" name="mpro_portfolio_duration_value" value="<?php echo esc_attr( $duration_value ); ?>" class="small-text">
					<select name="mpro_portfolio_duration_unit">
						<?php foreach ( array( 'days' => __( 'Days', 'mpro-portfolio' ), 'weeks' => __( 'Weeks', 'mpro-portfolio' ), 'months' => __( 'Months', 'mpro-portfolio' ), 'years' => __( 'Years', 'mpro-portfolio' ) ) as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $duration_unit, $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="mpro_portfolio_role"><?php esc_html_e( 'Role in Project', 'mpro-portfolio' ); ?></label></th>
				<td><input type="text" id="mpro_portfolio_role" name="mpro_portfolio_role" class="regular-text" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'Senior Product Designer', 'mpro-portfolio' ); ?>"></td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render repeatable tools.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public static function render_tools( $post ) {
		$tools = self::get( $post->ID, 'tools', array() );
		$tools = is_array( $tools ) ? $tools : array();
		self::render_repeater_start( 'tools' );
		foreach ( $tools as $tool ) {
			self::render_tool_row( $tool );
		}
		self::render_tool_row( '', true );
		self::render_repeater_end( __( 'Add Tool', 'mpro-portfolio' ), 'tool' );
	}

	/**
	 * Render repeatable collaborators.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public static function render_collaborators( $post ) {
		$people = self::get( $post->ID, 'collaborators', array() );
		$people = is_array( $people ) ? $people : array();
		self::render_repeater_start( 'collaborators' );
		foreach ( $people as $person ) {
			self::render_collaborator_row( $person );
		}
		self::render_collaborator_row( array(), true );
		self::render_repeater_end( __( 'Add Person', 'mpro-portfolio' ), 'collaborator' );
	}

	/**
	 * Render repeatable meta details.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public static function render_meta_details( $post ) {
		$details = self::get( $post->ID, 'meta_details', array() );
		$details = is_array( $details ) ? $details : array();
		self::render_repeater_start( 'meta-details' );
		foreach ( $details as $detail ) {
			self::render_meta_detail_row( $detail );
		}
		self::render_meta_detail_row( array(), true );
		self::render_repeater_end( __( 'Add Detail', 'mpro-portfolio' ), 'meta-detail' );
	}

	/**
	 * Render display options.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public static function render_display( $post ) {
		$featured    = (bool) self::get( $post->ID, 'featured', false );
		$card_style  = self::get( $post->ID, 'card_style', '' );
		$single_style = self::get( $post->ID, 'single_style', '' );
		?>
		<p>
			<label>
				<input type="checkbox" name="mpro_portfolio_featured" value="1" <?php checked( $featured ); ?>>
				<?php esc_html_e( 'Featured Portfolio Item', 'mpro-portfolio' ); ?>
			</label>
		</p>
		<p>
			<label for="mpro_portfolio_card_style"><strong><?php esc_html_e( 'Card Style', 'mpro-portfolio' ); ?></strong></label>
			<select id="mpro_portfolio_card_style" name="mpro_portfolio_card_style" class="widefat">
				<option value=""><?php esc_html_e( 'Inherit Global Default', 'mpro-portfolio' ); ?></option>
				<?php foreach ( MPRO_Portfolio_Settings::card_style_options() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( MPRO_Portfolio_Settings::normalize_card_style( $card_style ), $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="mpro_portfolio_single_style"><strong><?php esc_html_e( 'Single Template', 'mpro-portfolio' ); ?></strong></label>
			<select id="mpro_portfolio_single_style" name="mpro_portfolio_single_style" class="widefat">
				<option value=""><?php esc_html_e( 'Inherit Global Default', 'mpro-portfolio' ); ?></option>
				<?php foreach ( MPRO_Portfolio_Settings::single_style_options() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $single_style, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Save portfolio metadata.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) || MPRO_Portfolio_Content_Types::POST_TYPE !== $post->post_type ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		self::save_text( $post_id, 'short_description', isset( $_POST['mpro_portfolio_short_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mpro_portfolio_short_description'] ) ) : '' );
		self::save_text( $post_id, 'cover_id', isset( $_POST['mpro_portfolio_cover_id'] ) ? absint( $_POST['mpro_portfolio_cover_id'] ) : 0 );
		self::save_text( $post_id, 'client', isset( $_POST['mpro_portfolio_client'] ) ? sanitize_text_field( wp_unslash( $_POST['mpro_portfolio_client'] ) ) : '' );
		self::save_text( $post_id, 'project_url', isset( $_POST['mpro_portfolio_project_url'] ) ? esc_url_raw( wp_unslash( $_POST['mpro_portfolio_project_url'] ) ) : '' );

		$date = isset( $_POST['mpro_portfolio_implementation_date'] ) ? sanitize_text_field( wp_unslash( $_POST['mpro_portfolio_implementation_date'] ) ) : '';
		if ( $date && ! self::valid_date( $date ) ) {
			$date = '';
		}
		self::save_text( $post_id, 'implementation_date', $date );

		$duration = isset( $_POST['mpro_portfolio_duration_value'] ) ? (float) wp_unslash( $_POST['mpro_portfolio_duration_value'] ) : 0;
		self::save_text( $post_id, 'duration_value', $duration > 0 ? rtrim( rtrim( number_format( $duration, 2, '.', '' ), '0' ), '.' ) : '' );
		$units = array( 'days', 'weeks', 'months', 'years' );
		$unit  = isset( $_POST['mpro_portfolio_duration_unit'] ) ? sanitize_key( wp_unslash( $_POST['mpro_portfolio_duration_unit'] ) ) : 'weeks';
		self::save_text( $post_id, 'duration_unit', in_array( $unit, $units, true ) ? $unit : 'weeks' );
		self::save_text( $post_id, 'role', isset( $_POST['mpro_portfolio_role'] ) ? sanitize_text_field( wp_unslash( $_POST['mpro_portfolio_role'] ) ) : '' );

		$tools = array();
		if ( isset( $_POST['mpro_portfolio_tools'] ) && is_array( $_POST['mpro_portfolio_tools'] ) ) {
			foreach ( wp_unslash( $_POST['mpro_portfolio_tools'] ) as $tool ) {
				$tool = sanitize_text_field( $tool );
				if ( '' !== $tool ) {
					$tools[] = $tool;
				}
			}
		}
		self::save_array( $post_id, 'tools', array_values( array_unique( $tools ) ) );

		$collaborators = array();
		if ( isset( $_POST['mpro_portfolio_collaborators'] ) && is_array( $_POST['mpro_portfolio_collaborators'] ) ) {
			foreach ( wp_unslash( $_POST['mpro_portfolio_collaborators'] ) as $person ) {
				if ( ! is_array( $person ) ) {
					continue;
				}
				$name = isset( $person['name'] ) ? sanitize_text_field( $person['name'] ) : '';
				$role = isset( $person['role'] ) ? sanitize_text_field( $person['role'] ) : '';
				$url  = isset( $person['url'] ) ? esc_url_raw( $person['url'] ) : '';
				if ( $name || $role || $url ) {
					$collaborators[] = compact( 'name', 'role', 'url' );
				}
			}
		}
		self::save_array( $post_id, 'collaborators', $collaborators );

		$details = array();
		if ( isset( $_POST['mpro_portfolio_meta_details'] ) && is_array( $_POST['mpro_portfolio_meta_details'] ) ) {
			foreach ( wp_unslash( $_POST['mpro_portfolio_meta_details'] ) as $detail ) {
				if ( ! is_array( $detail ) ) {
					continue;
				}
				$label = isset( $detail['label'] ) ? sanitize_text_field( $detail['label'] ) : '';
				$value = isset( $detail['value'] ) ? sanitize_text_field( $detail['value'] ) : '';
				if ( $label || $value ) {
					$details[] = compact( 'label', 'value' );
				}
			}
		}
		self::save_array( $post_id, 'meta_details', $details );

		update_post_meta( $post_id, self::$keys['featured'], isset( $_POST['mpro_portfolio_featured'] ) ? 1 : 0 );
		$card_style = isset( $_POST['mpro_portfolio_card_style'] ) ? MPRO_Portfolio_Settings::validate_card_style( wp_unslash( $_POST['mpro_portfolio_card_style'] ), true ) : '';
		$single_style = isset( $_POST['mpro_portfolio_single_style'] ) ? MPRO_Portfolio_Settings::validate_single_style( wp_unslash( $_POST['mpro_portfolio_single_style'] ), true ) : '';
		self::save_text( $post_id, 'card_style', $card_style );
		self::save_text( $post_id, 'single_style', $single_style );
	}

	/**
	 * Get a metadata value with compatibility fallbacks.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $field Field name.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public static function get( $post_id, $field, $default = '' ) {
		if ( ! isset( self::$keys[ $field ] ) ) {
			return $default;
		}

		$value = get_post_meta( $post_id, self::$keys[ $field ], true );
		if ( '' !== $value && array() !== $value ) {
			return $value;
		}

		$legacy = array(
			'short_description'   => '_mpro_pf_short_description',
			'client'              => '_mpro_pf_client_name',
			'project_url'         => '_mpro_pf_project_url',
			'implementation_date' => '_mpro_pf_implementation_date',
			'duration_value'      => '_mpro_pf_duration_value',
			'duration_unit'       => '_mpro_pf_duration_unit',
			'role'                => '_mpro_pf_project_role',
			'tools'               => '_mpro_pf_tools',
			'collaborators'       => '_mpro_pf_people',
			'meta_details'        => '_mpro_pf_meta_details',
			'card_style'          => '_mpro_pf_card_style',
		);

		if ( isset( $legacy[ $field ] ) ) {
			$value = get_post_meta( $post_id, $legacy[ $field ], true );
			if ( '' !== $value && array() !== $value ) {
				if ( 'card_style' === $field ) {
					return MPRO_Portfolio_Settings::normalize_card_style( $value );
				}
				return $value;
			}
		}

		if ( in_array( $field, array( 'duration_value', 'duration_unit' ), true ) ) {
			$legacy_duration = trim( (string) get_post_meta( $post_id, '_mpro_portfolio_duration', true ) );
			if ( $legacy_duration ) {
				if ( 'duration_value' === $field && preg_match( '/([0-9]+(?:[.,][0-9]+)?)/', $legacy_duration, $matches ) ) {
					return str_replace( ',', '.', $matches[1] );
				}

				if ( 'duration_unit' === $field ) {
					$normalized = strtolower( $legacy_duration );
					foreach ( array( 'days' => array( 'day', 'days' ), 'weeks' => array( 'week', 'weeks' ), 'months' => array( 'month', 'months' ), 'years' => array( 'year', 'years' ) ) as $unit => $needles ) {
						foreach ( $needles as $needle ) {
							if ( false !== strpos( $normalized, $needle ) ) {
								return $unit;
							}
						}
					}
				}
			}
		}

		return $default;
	}

	/**
	 * Return a display-ready duration.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public static function get_duration_label( $post_id ) {
		$value = self::get( $post_id, 'duration_value' );
		$unit  = self::get( $post_id, 'duration_unit', '' );

		if ( ! $value ) {
			return '';
		}
		if ( ! $unit || ! is_numeric( $value ) ) {
			return (string) $value;
		}

		return trim( $value . ' ' . $unit );
	}

	/**
	 * Save or delete a scalar metadata value.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $field Field name.
	 * @param mixed  $value Value.
	 * @return void
	 */
	private static function save_text( $post_id, $field, $value ) {
		$key = self::$keys[ $field ];
		if ( '' === $value || 0 === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Save or delete an array metadata value.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $field Field name.
	 * @param array  $value Value.
	 * @return void
	 */
	private static function save_array( $post_id, $field, $value ) {
		$key = self::$keys[ $field ];
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Validate an ISO date.
	 *
	 * @param string $date Date string.
	 * @return bool
	 */
	private static function valid_date( $date ) {
		$parts = explode( '-', $date );
		return 3 === count( $parts ) && checkdate( absint( $parts[1] ), absint( $parts[2] ), absint( $parts[0] ) );
	}

	/**
	 * Render a repeater wrapper.
	 *
	 * @param string $type Repeater type.
	 * @return void
	 */
	private static function render_repeater_start( $type ) {
		echo '<div class="mpro-repeater" data-repeater="' . esc_attr( $type ) . '"><div class="mpro-repeater__rows">';
	}

	/**
	 * Finish a repeater wrapper.
	 *
	 * @param string $label Button label.
	 * @param string $template Template type.
	 * @return void
	 */
	private static function render_repeater_end( $label, $template ) {
		echo '</div><button type="button" class="button mpro-repeater__add" data-template="' . esc_attr( $template ) . '">' . esc_html( $label ) . '</button></div>';
	}

	/**
	 * Render a tool row.
	 *
	 * @param string $tool Tool name.
	 * @param bool   $template Whether this is a hidden template.
	 * @return void
	 */
	private static function render_tool_row( $tool, $template = false ) {
		$class = $template ? ' mpro-repeater__template' : '';
		?>
		<div class="mpro-repeater__row<?php echo esc_attr( $class ); ?>" <?php echo $template ? 'hidden' : ''; ?>>
			<input type="text" name="mpro_portfolio_tools[]" value="<?php echo esc_attr( $tool ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Figma', 'mpro-portfolio' ); ?>">
			<button type="button" class="button-link-delete mpro-repeater__remove"><?php esc_html_e( 'Remove', 'mpro-portfolio' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Render a collaborator row.
	 *
	 * @param array $person Person data.
	 * @param bool  $template Whether this is a hidden template.
	 * @return void
	 */
	private static function render_collaborator_row( $person, $template = false ) {
		$name  = isset( $person['name'] ) ? $person['name'] : '';
		$role  = isset( $person['role'] ) ? $person['role'] : '';
		$url   = isset( $person['url'] ) ? $person['url'] : '';
		$index = $template ? '__INDEX__' : uniqid( 'person_', false );
		$class = $template ? ' mpro-repeater__template' : '';
		?>
		<div class="mpro-repeater__row mpro-repeater__row--columns<?php echo esc_attr( $class ); ?>" <?php echo $template ? 'hidden' : ''; ?>>
			<input type="text" name="mpro_portfolio_collaborators[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Name', 'mpro-portfolio' ); ?>">
			<input type="text" name="mpro_portfolio_collaborators[<?php echo esc_attr( $index ); ?>][role]" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'Role', 'mpro-portfolio' ); ?>">
			<input type="url" name="mpro_portfolio_collaborators[<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $url ); ?>" placeholder="https://">
			<button type="button" class="button-link-delete mpro-repeater__remove"><?php esc_html_e( 'Remove', 'mpro-portfolio' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Render a metadata row.
	 *
	 * @param array $detail Detail data.
	 * @param bool  $template Whether this is a hidden template.
	 * @return void
	 */
	private static function render_meta_detail_row( $detail, $template = false ) {
		$label = isset( $detail['label'] ) ? $detail['label'] : '';
		$value = isset( $detail['value'] ) ? $detail['value'] : '';
		$index = $template ? '__INDEX__' : uniqid( 'detail_', false );
		$class = $template ? ' mpro-repeater__template' : '';
		?>
		<div class="mpro-repeater__row mpro-repeater__row--details<?php echo esc_attr( $class ); ?>" <?php echo $template ? 'hidden' : ''; ?>>
			<input type="text" name="mpro_portfolio_meta_details[<?php echo esc_attr( $index ); ?>][label]" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'Label', 'mpro-portfolio' ); ?>">
			<input type="text" name="mpro_portfolio_meta_details[<?php echo esc_attr( $index ); ?>][value]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'Value', 'mpro-portfolio' ); ?>">
			<button type="button" class="button-link-delete mpro-repeater__remove"><?php esc_html_e( 'Remove', 'mpro-portfolio' ); ?></button>
		</div>
		<?php
	}
}
