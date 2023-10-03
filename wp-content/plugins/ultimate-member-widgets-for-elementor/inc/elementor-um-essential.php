<?php
/**
 * Declare plugin dependency.
 */
if ( ! function_exists( 'user_elementor_um_dependency' ) ) {
	function user_elementor_um_dependency() {

    $notice = sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Elementor installation link */
        __( '%1$s requires %2$s to be installed and activated to function properly. %3$s', 'um-elementor' ),
        '<strong>' . __( 'Ultimate Member - Elementor', 'um-elementor' ) . '</strong>',
        '<strong>' . __( 'Elementor', 'um-elementor' ) . '</strong>',
        '<a href="' . esc_url( admin_url( 'plugin-install.php?s=Elementor&tab=search&type=term' ) ) . '">' . __( 'Please click on this link and install Elementor', 'um-elementor' ) . '</a>'
    );

    printf( '<div class="notice notice-warning is-dismissible"><p style="padding: 13px 0">%1$s</p></div>', $notice );

	}
}

/**
 * Output PHP notice, if the PHP version is below 5.4
 */
if ( ! function_exists( 'user_elementor_um_fail_php' ) ) {
	function user_elementor_um_fail_php() {
		$message      = esc_html__( 'Ultimate Member - Elementor requires PHP version 5.4+, the plugin is currently NOT ACTIVE.', 'um-addons-elementor' );
		$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
		echo wp_kses_post( $html_message );
	}
}


/**
 * Load plugin text domain.
 */
if ( ! function_exists( 'user_elementor_um_textdomain' ) ) {
	function user_elementor_um_textdomain() {
		load_plugin_textdomain( 'um-addons-elementor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/**
 * Create a category for Ultimate Member elements.
 */
if ( ! function_exists( 'user_elementor_um_category' ) ) {
	function user_elementor_um_category() {
		\Elementor\Plugin::instance()->elements_manager->add_category( 'um-addons-elementor', [
			'title' => __( 'Ultimate Member', 'um-elementor' ),
			'icon'  => 'font',
		], 1 );
	}
}

/**
 * Load the Ultimate Member elements.
 */
if ( ! function_exists( 'user_elementor_um_modules' ) ) {
	function user_elementor_um_modules() {
		if ( class_exists( 'UM' ) ) {
			require_once UM_USER_ELEMENTOR_PATH . 'modules/um-user-list.php';
            require_once UM_USER_ELEMENTOR_PATH . 'modules/um-user-list-flipbox.php';
		}
	}
}

/**
 * Load the Ultimate Member element CSS & js.
 */
if ( ! function_exists( 'user_elementor_um_scripts' ) ) {
	function user_elementor_um_scripts() {
		wp_enqueue_style( 'um-ele-styles', UM_USER_ELEMENTOR_URL . 'assets/css/ep-elements.css' );
	}
}

/**
 * Get all user roles and their capabilities.
 *
 * @return array
 */
function user_elementor_um_get_user_roles() {
    // Get the global WP_Roles instance.
    global $wp_roles;

    // If the WP_Roles instance is not set, create it.
    if ( ! isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles();
    }

    // Get all user role names and their capabilities in one go.
    $all_roles = $wp_roles->roles;

    // Create an empty array to store the capable roles.
    $available_roles_capable = [];

    // Iterate over the roles and extract the role key and name.
    foreach ( $all_roles as $role_key => $role_data ) {
        $available_roles_capable[ $role_key ] = $role_data['name'];
    }

    // Return the array of capable roles.
    return $available_roles_capable;
}



/**
 * Get all user meta keys that have a field type.
 *
 * @return array
 */
function user_elementor_um_get_user_meta_keys(): array
{
    // Get all users.
    $users = get_users();

    // List of user meta keys with field type.
    $userMetaKeys = [];

    foreach ($users as $user) {
        $metaKeys = array_keys(get_user_meta($user->ID));

        foreach ($metaKeys as $key) {
            // Check if the meta key is not excluded.
            if (substr($key, 0, 1) !== '_') {
                $fieldType = UM()->Fields()->get_field_type($key); // Get field type for key

                if (!empty($fieldType)) {
                    $userMetaKeys[] = $key;
                }
            }
        }
    }

    // Return the unique list of user meta keys.
    return array_unique($userMetaKeys);
}


/**
 * Get a combined array of unique user meta keys.
 *
 * @return array
 */
function user_elementor_um_get_user_meta_keys_combine()
{
    $array_data = user_elementor_um_get_user_meta_keys();
    $clean_array = array_unique($array_data);
    return array_combine($clean_array, $clean_array);
}

function user_elementor_dismissible_notice() {
    if ( ! get_user_meta( get_current_user_id(), 'user_elementor_notice_dismissed', false ) ) {
        ?>
        <div class="notice notice-info is-dismissible um-elemetor-pro">
            <h4><?php _e( 'Upgrade to Ultimate Member Widgets for Elementor PRO', 'um-elementor' ); ?></h4>

                <p><?php _e( '<strong>Get Access to</strong>', 'um-elementor' ) ?></p>

                <ul>
                    <li><?php _e( '5 Premium Widgets', 'um-elementor' ) ?></li>
                    <li><?php _e( 'Repeated Custom User Fields', 'um-elementor' ) ?></li>
                    <li><?php _e( 'Display any field types', 'um-elementor' ) ?></li>
                </ul>
                <p>
                    <a href="https://userelements.com/ultimate-member-elementor/" class="button button-primary" target="_blank">
                        <?php _e( 'Get 30% OFF', 'um-elementor' ) ?>
                    </a>
                </p>


            <button type="button" class="notice-dismiss" onclick="dismissNotice();">
                <span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'my-plugin-textdomain' ); ?></span>
            </button>
        </div>
        <script>
            function dismissNotice() {
                jQuery.post( ajaxurl, {
                    action: 'dismiss_my_notice',
                    _wpnonce: '<?php echo wp_create_nonce( 'dismiss_my_notice' ); ?>'
                } );
            }
        </script>
        <style type="text/css">
            .um-elemetor-pro{background-color:#e5d5ff;border-left-color:#7b61ff}.um-elemetor-pro .button-primary{background-color:#7b61ff!important;border:none!important}.um-elemetor-pro ul{list-style:disc;margin-left:1rem}

        </style>
        <?php
    }
}

function user_elementor_dismiss_notice_callback() {
    if ( check_ajax_referer( 'dismiss_my_notice', '_wpnonce' ) ) {
        update_user_meta( get_current_user_id(), 'user_elementor_notice_dismissed', true );
    }
    wp_die();
}

add_action( 'wp_ajax_dismiss_my_notice', 'user_elementor_dismiss_notice_callback' );
add_action( 'admin_notices', 'user_elementor_dismissible_notice' );
