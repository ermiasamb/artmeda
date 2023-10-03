<?php
/**
 * UM Lock Down Core.
 *
 * @since   1.0.0
 * @package UM_Lock_Down
 */

/**
 * UM Lock Down Core.
 *
 * @since 1.0.0
 */
class UMLD_Core {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   UM_Lock_Down
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  UM_Lock_Down $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {		
		add_filter( 'um_account_tab_privacy_fields', array( $this, 'add_um_lock_down_fields' ), 100, 2 );
		add_filter( 'um_predefined_fields_hook', array( $this, 'set_account_fields' ), 12, 1 );
		add_filter( 'um_shortcode_args_filter', array( $this, 'custom_um_pre_args_setup' ), 99, 1 );
		add_action( 'um_before_profile_form_is_loaded', array( $this, 'add_paused_account_tmpl' ), 12, 1 );
		add_filter( 'um_prepare_user_query_args', array( $this, 'um_remove_paused_users_from_list' ), 100, 2 );
		add_filter( 'um_settings_structure', array( $this, 'um_settings_structure' ), 12, 1 );
	}

	/**
	 * Add setting to Ultimate Member setting panel
	 * 
	 * @param array $structure
	 * 
	 * @since 1.0.0
	 */
	public function um_settings_structure( $structure = array() ) {
		$structure['']['sections']['account']['fields'][] = array(
			'id'            => 'um_allow_pause_message',
			'type'          => 'checkbox',
			'label'         => __( 'Allow custom pause messages?','um-lock-down' ),
			'tooltip' 	    => __('Allow users to set a custom message when pausing an account.','um-lock-down'),
		);
		$structure['']['sections']['account']['fields'][] = array(
			'id'       		=> 'um_pause_account_page',
			'type'     		=> 'select',
			'label'    		=> __( 'Pause Account Template','um-lock-down' ),
			'options' 		=> UM()->query()->wp_pages(),
			'placeholder' 	=> __('Choose a page...','um-lock-down'),
			'tooltip' 	    => __('Add custom page content that will load when a user pauses their account.','um-lock-down'),
			'compiler' 		=> true,
			'size'          => 'small',
		);
		return $structure;
	}

	/**
	 * Hide paused accounts from the member directory.
	 * 
	 * @param array $query_args
	 * @param array $args
	 * 
	 * @since 1.0.0
	 */
	public function um_remove_paused_users_from_list( $query_args, $args ) {
		if ( ! UM()->roles()->um_user_can( 'can_view_all' ) ) {
			$query_args['meta_query'][] = array(
				"relation"	=> "OR",
				array(
					'key' => 'pause_account',
					'value' => '',
					'compare' => 'NOT EXISTS'
				),
				array(
					"relation"	=> "AND",
					array(
						'key' => 'pause_account',
						'value' => 'Yes',
						'compare' => 'NOT LIKE'
					),
					array(
						'key' => 'pause_account',
						'value' => 'Yes',
						'compare' => 'NOT LIKE'
					),
				),
			);
		}
		return $query_args;
	}

	/**
	 * Manipulate the UM args.
	 * 
	 * @param array $fields
	 * 
	 * @since 1.0.0
	 */
	public function add_paused_account_tmpl( $args = array() ) {
		if ( 
			( ! um_is_user_himself() || ! um_user( 'can_edit_everyone' ) ) && 
			isset( $args['template'] ) && 
			'paused_account' == $args['template'] 
		) {
			$user_id    = um_get_requested_user();
			$is_paused = um_filtered_value( 'pause_account' );
			error_log( __LINE__ . '  ' . $is_paused );
			if ( 'Yes' == $is_paused ) {
				// Get the paused template.
				$paused_template_id = UM()->options()->get( 'um_pause_account_page' );

				// Get the paused template.
				$allow_pause_message = UM()->options()->get( 'um_allow_pause_message' );

				$paused_msg = um_filtered_value( 'pause_account_message' );

				// Get the template content.
				if ( $paused_template_id ) {
					$template_content = get_post_field( 'post_content', $paused_template_id );
					if ( $template_content ) {
						if ( $paused_msg && $allow_pause_message ) {
							$content = str_replace( '{um_paused_message}', $paused_msg, $template_content );
							echo wp_kses_post( $content );
						} else {
							echo wp_kses_post( $template_content );
						}
					}
				} else {
					if ( $paused_msg && $allow_pause_message ) {
						echo wp_kses_post( $paused_msg );
					}
				}
			}
		}
	}

	/**
	 * Load paused_account via filter.
	 * 
	 * @param array $args
	 * 
	 * @since 1.0.0
	 */
	public function custom_um_pre_args_setup( $args = array() ) {
		if ( 
			( ! um_is_user_himself() || ! um_user( 'can_edit_everyone' ) ) && 
			isset( $args['mode'] ) && 
			'profile' == $args['mode'] 
		) {
			$user_id    = um_get_requested_user();
			um_fetch_user( $user_id );
			$is_paused = um_filtered_value( 'pause_account' );
			if ( 'Yes' == $is_paused ) {
				// Set template to paused.
				$args['template'] = 'paused_account';
			}
			um_reset_user();
		}
		return $args;
	}
	
	/**
	 * Link the pause account message fields.
	 * 
	 * @param array $fields
	 * @param array $shortcode_args
	 * 
	 * @since 1.0.0
	 */
	public function add_um_lock_down_fields( $args, $shortcode_args) {
		if ( $args ) {
			$args = explode( ',', $args );
			if ( is_array( $args ) ) {
				$args[] = 'pause_account';
				$args[] = 'pause_account_message';
			}
			$args = implode( ',', $args );
		}

		return $args;
	}

	/**
	 * Add Account fields to Ultimate Member
	 * 
	 * @param array $fields
	 * 
	 * @since 1.0.0
	 */
	public function set_account_fields( $fields = array() ) {
		$fields['pause_account'] = array(
			'title'        => __('Pause Account','um-lock-down'),
			'metakey'      => 'uml_pause_account',
			'type'         => 'radio',
			'label'        => __('Pause my account','um-lock-down'),
			'help'         => __('Here you can hide access to your profile','um-lock-down'),
			'required'     => 0,
			'public'       => 1,
			'editable'     => 1,
			'default'      => 'No',
			'options'      => array( 
				'No'  => __( 'No','um-lock-down' ),
				'Yes' => __( 'Yes','um-lock-down' ) 
			),
			'account_only' => true,
		);
		$fields['pause_account_message'] = array(
			'title'        => __('Pause Account Message','um-lock-down'),
			'metakey'      => 'uml_pause_account_message',
			'type'         => 'text',
			'label'        => __('Paused Account Message','um-lock-down'),
			'help'         => __('Here you can hide access to your profile','um-lock-down'),
			'required'     => 0,
			'public'       => 1,
			'editable'     => 1,
			'default'      => 'No',
			'options'      => array( 
				'No'  => __( 'No','um-lock-down' ),
				'Yes' => __( 'Yes','um-lock-down' ) 
			),
			'account_only' => true,
		);

		return $fields;
	}
}
